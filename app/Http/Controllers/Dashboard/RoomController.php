<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['floor', 'creator'])
            ->when($request->search, fn($q) => $q->where('number', 'like', "%{$request->search}%"))
            ->when($request->sort && $request->direction, fn($q) =>
                $q->orderBy($request->sort, $request->direction))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Convert price from cents to dollars for display
        $rooms->getCollection()->transform(function ($room) {
            $room->price_dollars = $room->price / 100;
            return $room;
        });

        return Inertia::render('Dashboard/Rooms/index', [
            'rooms'   => $rooms,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Rooms/create', [
            'floors' => Floor::select('id', 'name', 'number')->get(),
        ]);
    }

    public function store(StoreRoomRequest $request)
    {
        Room::create([
            'number'     => $request->number,
            'floor_id'   => $request->floor_id,
            'capacity'   => $request->capacity,
            'price'      => (int) ($request->price * 100), // dollars → cents
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        return Inertia::render('Dashboard/Rooms/edit', [
            'room'   => array_merge($room->toArray(), ['price_dollars' => $room->price / 100]),
            'floors' => Floor::select('id', 'name', 'number')->get(),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $this->authorizeCreatorOrAdmin($request->user(), $room->created_by);

        $room->update([
            'number'   => $request->number,
            'floor_id' => $request->floor_id,
            'capacity' => $request->capacity,
            'price'    => (int) ($request->price * 100),
        ]);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Request $request, Room $room)
    {
        $this->authorizeCreatorOrAdmin($request->user(), $room->created_by);

        if ($room->reservations()->exists()) {
            return back()->with('error', 'Cannot delete a room that has reservations.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }

    private function authorizeCreatorOrAdmin($user, int $createdBy): void
    {
        if (!$user->hasRole('admin') && $user->id !== $createdBy) {
            abort(403, 'You can only modify rooms you created.');
        }
    }
}