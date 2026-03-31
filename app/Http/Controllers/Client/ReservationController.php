<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationPaymentIntentRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\AvailableRoomResource;
use App\Http\Resources\MyReservationResource;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ReservationAvailabilityService;
use App\Services\ReservationPricingService;
use App\Services\StripeReservationPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Stripe\Exception\ApiErrorException;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationAvailabilityService $availabilityService,
        private readonly ReservationPricingService $pricingService,
        private readonly StripeReservationPaymentService $stripePaymentService,
    ) {
    }

    public function index(Request $request): Response
    {
        $filters = [
            'check_in' => $request->query('check_in'),
            'check_out' => $request->query('check_out'),
            'accompany_number' => $request->query('accompany_number'),
        ];

        $rooms = [];

        if ($request->filled(['check_in', 'check_out', 'accompany_number'])) {
            $validated = Validator::make($request->all(), [
                'check_in' => ['required', 'date', 'after_or_equal:today'],
                'check_out' => ['required', 'date', 'after:check_in'],
                'accompany_number' => ['required', 'integer', 'min:1'],
            ])->validate();

            $availableRooms = $this->availabilityService->availableRooms(
                $validated['check_in'],
                $validated['check_out'],
                (int) $validated['accompany_number'],
            );

            $rooms = AvailableRoomResource::collection($availableRooms)->resolve();
        }

        return Inertia::render('Client/Reservations/Index', [
            'rooms' => $rooms,
            'filters' => $filters,
        ]);
    }

    public function show(Room $room, Request $request): Response
    {
        return Inertia::render('Client/Reservations/Show', [
            'room' => [
                'id' => $room->id,
                'number' => $room->number,
                'capacity' => $room->capacity,
                'price' => (int) $room->price,
                'price_formatted' => number_format(((int) $room->price) / 100, 2),
            ],
            'defaults' => [
                'check_in' => $request->query('check_in'),
                'check_out' => $request->query('check_out'),
                'accompany_number' => $request->query('accompany_number', 1),
            ],
            'stripe' => [
                'publishable_key' => $this->stripePaymentService->publishableKey(),
                'currency' => $this->stripePaymentService->currency(),
            ],
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function createPaymentIntent(CreateReservationPaymentIntentRequest $request, Room $room): JsonResponse
    {
        $payload = $request->validated();

        if ((int) $payload['accompany_number'] > (int) $room->capacity) {
            throw ValidationException::withMessages([
                'accompany_number' => 'Accompany number exceeds room capacity.',
            ]);
        }

        if (! $this->availabilityService->roomIsAvailable($room->id, $payload['check_in'], $payload['check_out'])) {
            throw ValidationException::withMessages([
                'check_in' => 'This room is no longer available for the selected dates.',
            ]);
        }

        $paidPrice = $this->pricingService->calculatePaidPriceCents((int) $room->price, $payload['check_in'], $payload['check_out']);

        if ($paidPrice <= 0) {
            throw ValidationException::withMessages([
                'check_out' => 'Reservation period must be at least one night.',
            ]);
        }

        $client = $request->user('client');

        $paymentIntent = $this->stripePaymentService->createPaymentIntent(
            $paidPrice,
            $this->stripePaymentService->currency(),
            [
                'room_id' => $room->id,
                'client_id' => $client->id,
                'check_in' => $payload['check_in'],
                'check_out' => $payload['check_out'],
                'accompany_number' => (int) $payload['accompany_number'],
            ],
        );

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paidPrice,
            'currency' => $this->stripePaymentService->currency(),
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function store(StoreReservationRequest $request, Room $room): RedirectResponse
    {
        $payload = $request->validated();

        $client = $request->user('client');

        try {
            DB::transaction(function () use ($room, $payload, $client): void {
                $room = Room::query()->whereKey($room->id)->lockForUpdate()->firstOrFail();

                if ((int) $payload['accompany_number'] > (int) $room->capacity) {
                    throw ValidationException::withMessages([
                        'accompany_number' => 'Accompany number exceeds room capacity.',
                    ]);
                }

                if (! $this->availabilityService->roomIsAvailable($room->id, $payload['check_in'], $payload['check_out'])) {
                    throw ValidationException::withMessages([
                        'check_in' => 'This room is no longer available for the selected dates.',
                    ]);
                }

                $paidPrice = $this->pricingService->calculatePaidPriceCents(
                    (int) $room->price,
                    $payload['check_in'],
                    $payload['check_out'],
                );

                $this->stripePaymentService->assertSuccessfulPaymentIntent(
                    $payload['payment_intent_id'],
                    $paidPrice,
                    $this->stripePaymentService->currency(),
                    [
                        'room_id' => $room->id,
                        'client_id' => $client->id,
                        'check_in' => $payload['check_in'],
                        'check_out' => $payload['check_out'],
                        'accompany_number' => (int) $payload['accompany_number'],
                    ],
                );

                Reservation::query()->create([
                    'client_id' => $client->id,
                    'room_id' => $room->id,
                    'check_in' => $payload['check_in'],
                    'check_out' => $payload['check_out'],
                    'accompany_number' => (int) $payload['accompany_number'],
                    'paid_price' => $paidPrice,
                    'approved_by' => null,
                    'status' => Reservation::STATUS_PENDING,
                ]);

                $this->stripePaymentService->markPaymentIntentAsUsed($payload['payment_intent_id']);
            });
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages([
                'payment_intent_id' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('client.reservations.my')->with('success', 'Reservation created successfully.');
    }

    public function myReservations(Request $request): Response|JsonResponse
    {
        $client = $request->user('client');

        $reservations = Reservation::query()
            ->with(['room:id,number', 'approver:id,name'])
            ->where('client_id', $client->id)
            ->orderByDesc('check_in')
            ->get();

        if ($request->wantsJson()) {
            return response()->json(MyReservationResource::collection($reservations));
        }

        return Inertia::render('Client/Reservations/MyReservations', [
            'reservations' => MyReservationResource::collection($reservations)->resolve(),
        ]);
    }
}
