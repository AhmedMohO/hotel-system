<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ReservationAvailabilityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientDashboardController extends Controller
{
    public function __construct(private readonly ReservationAvailabilityService $availabilityService)
    {
    }

    public function index(Request $request)
    {
        $checkIn = $request->query('check_in', now()->toDateString());
        $checkOut = $request->query('check_out', now()->addDay()->toDateString());
        $accompanyNumber = max(1, (int) $request->query('accompany_number', 1));

        $availableRooms = $this->availabilityService
            ->availableRoomsQuery($checkIn, $checkOut, $accompanyNumber)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Client/Dashboard', [
            'rooms' => $availableRooms,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'accompanyNumber' => $accompanyNumber,
        ]);
    }
}
