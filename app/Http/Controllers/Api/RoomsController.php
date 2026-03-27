<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoomsController extends Controller
{
    public function index(): JsonResponse
    {
        $rooms = Room::query()
            ->with('floor:id,name,number')
            ->orderBy('number')
            ->get(['id', 'number', 'floor_id', 'capacity', 'price', 'created_by', 'created_at', 'updated_at']);

        return response()->json($rooms);
    }
}
