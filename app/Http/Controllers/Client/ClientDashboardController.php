<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $checkIn  = $request->query('check_in',  now()->toDateString());
        $checkOut = $request->query('check_out', now()->addDay()->toDateString());

        $availableRooms = Room::whereDoesntHave('reservations', function ($query) use ($checkIn, $checkOut) {
            $query->where('check_in',  '<', $checkOut)
                ->where('check_out', '>',  $checkIn);
        })->with('floor')->paginate(10);

        return Inertia::render('Client/Dashboard', [
            'rooms'    => $availableRooms,
            'checkIn'  => $checkIn,
            'checkOut' => $checkOut,
        ]);
    }


}
