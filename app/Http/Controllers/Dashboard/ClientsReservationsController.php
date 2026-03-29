<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ClientsReservationsController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) data_get($request->input('filter'), 'name', '');
        $perPage = $request->filled('per_page') ? $request->integer('per_page') : 10;
        $perPage = max(1, min($perPage, 100));

        $sort = (string) $request->input('sort', '-created_at');
        $descending = str_starts_with($sort, '-');
        $sortField = ltrim($sort, '-');
        $direction = $descending ? 'desc' : 'asc';

        $reservations = Reservation::query()
            ->where('approved_by', $request->user()->id)
            ->with(['client:id,name', 'room:id,number'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('accompany_number', 'like', "%{$search}%")
                        ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('room', fn($q) => $q->where('number', 'like', "%{$search}%"));
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
            default:
                $reservations->latest();
                break;
        }

        $reservations = $reservations
            ->paginate($perPage)
            ->withQueryString();

        $reservations->through(function (Reservation $reservation) {
            return [
                'id' => $reservation->id,
                'client' => [
                    'name' => $reservation->client?->name,
                ],
                'room' => [
                    'number' => $reservation->room?->number,
                ],
                'accompany_number' => $reservation->accompany_number,
                'check_in' => $reservation->check_in,
                'check_out' => $reservation->check_out,
                'paid_price' => ((int) $reservation->paid_price) / 100,
            ];
        });

        return Inertia::render('Dashboard/ClientsReservations/index', [
            'reservations' => $reservations,
            'filters' => $request->all(['filter', 'sort', 'per_page']),
        ]);
    }
}
