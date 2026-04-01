<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ClientsReservationsController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->renderByStatus(
            request: $request,
            status: Reservation::STATUS_APPROVED,
            view: 'Dashboard/ClientsReservations/index',
        );
    }

    public function pending(Request $request): Response
    {
        return $this->renderByStatus(
            request: $request,
            status: Reservation::STATUS_PENDING,
            view: 'Dashboard/ClientsReservations/pending',
        );
    }

    public function approve(Reservation $reservation, Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('receptionist')) {
            $reservation->loadMissing('client:id,approved_by');

            if ((int) $reservation->client?->approved_by !== (int) $user->id) {
                return back()->with('error', 'You can only approve reservations for clients approved by you.');
            }
        }

        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return back()->with('error', 'This reservation is already processed.');
        }

        $reservation->update([
            'status' => Reservation::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Reservation approved successfully.');
    }

    private function renderByStatus(Request $request, string $status, string $view): Response
    {
        $user = $request->user();
        $canSeeApprovedBy = $user->hasRole('admin') || $user->hasRole('manager');
        $search = (string) data_get($request->input('filter'), 'name', '');
        $perPage = $request->filled('per_page') ? $request->integer('per_page') : 10;
        $perPage = max(1, min($perPage, 100));

        $sort = (string) $request->input('sort', '-created_at');
        $descending = str_starts_with($sort, '-');
        $sortField = ltrim($sort, '-');
        $direction = $descending ? 'desc' : 'asc';

        $reservations = Reservation::query()
            ->where('status', $status)
            ->with(['client:id,name,avatar_image', 'room:id,number', 'approver:id,name'])
            ->when($user->hasRole('receptionist'), function ($query) use ($user) {
                $query->whereHas('client', fn($clientQuery) => $clientQuery->where('approved_by', $user->id));
            })
            ->when($search !== '', function ($query) use ($search, $canSeeApprovedBy) {
                $query->where(function ($inner) use ($search, $canSeeApprovedBy) {
                    $inner->where('accompany_number', 'like', "%{$search}%")
                        ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('room', fn($q) => $q->where('number', 'like', "%{$search}%"));

                    if ($canSeeApprovedBy) {
                        $inner->orWhereHas('approver', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }
                });
            });

        switch ($sortField) {
            case 'accompany_number':
            case 'paid_price':
            case 'check_in':
            case 'check_out':
            case 'created_at':
                $reservations->orderBy($sortField, $direction);
                break;
            case 'client_name':
                $reservations->orderBy(
                    DB::table('clients')
                        ->select('name')
                        ->whereColumn('clients.id', 'reservations.client_id')
                        ->limit(1),
                    $direction,
                );
                break;
            case 'room_number':
                $reservations->orderBy(
                    DB::table('rooms')
                        ->select('number')
                        ->whereColumn('rooms.id', 'reservations.room_id')
                        ->limit(1),
                    $direction,
                );
                break;
            case 'status':
                $reservations->orderBy('status', $direction);
                break;
            default:
                $reservations->latest();
                break;
        }

        $reservations = $reservations
            ->paginate($perPage)
            ->withQueryString();

        $reservations->through(function (Reservation $reservation) use ($canSeeApprovedBy) {
            return [
                'id' => $reservation->id,
                'client' => [
                    'name' => $reservation->client?->name,
                    'avatar_image' => $reservation->client?->avatar_image,
                ],
                'room' => [
                    'number' => $reservation->room?->number,
                ],
                'accompany_number' => $reservation->accompany_number,
                'check_in' => $reservation->check_in,
                'check_out' => $reservation->check_out,
                'paid_price' => ((int) $reservation->paid_price) / 100,
                'status' => $reservation->status,
                'approved_by' => $canSeeApprovedBy ? $reservation->approved_by : null,
                'approved_by_name' => $canSeeApprovedBy ? $reservation->approver?->name : null,
            ];
        });

        return Inertia::render($view, [
            'reservations' => $reservations,
            'filters' => $request->all(['filter', 'sort', 'per_page']),
            'canSeeApprovedBy' => $canSeeApprovedBy,
        ]);
    }
}
