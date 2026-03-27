<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $availableRooms = Room::whereDoesntHave('reservations')
            ->with('floor')
            ->paginate(10);

        return Inertia::render('Client/Dashboard', [
            'rooms' => $availableRooms,
        ]);
    }

}
