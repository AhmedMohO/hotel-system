<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFloorRequest;
use App\Http\Requests\UpdateFloorRequest;
use App\Models\Floor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FloorController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user = $request->user();

    //     $floorsQuery = Floor::with('creator')
    //         ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
    //             ->orWhere('number', 'like', "%{$request->search}%"))
    //         ->when($request->sort && $request->direction, fn($q) =>
    //             $q->orderBy($request->sort, $request->direction))
    //         ->latest();

    //     $floors = $floorsQuery->paginate(10)->withQueryString();

    //     return Inertia::render('Dashboard/Floors/index', [
    //         'floors'  => $floors,
    //         'filters' => $request->only(['search', 'sort', 'direction']),
    //     ]);
    // }

    public function index(Request $request)
{
    $floors = Floor::with('creator')
        ->when($request->input('filter.name'), fn($q, $search) =>
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('number', 'like', "%{$search}%")
        )
        ->when($request->sort, function($q) use ($request) {
            $sort = $request->sort;
            $dir = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $col = ltrim($sort, '-');
            $q->orderBy($col, $dir);
        }, fn($q) => $q->latest())
        ->paginate($request->input('per_page', 10))
        ->withQueryString();

    return Inertia::render('Dashboard/Floors/index', [
        'floors'  => $floors,
        'filters' => $request->only(['filter', 'sort', 'per_page']),
    ]);
}
    public function create()
    {
        return Inertia::render('Dashboard/Floors/create');
    }

    public function store(StoreFloorRequest $request)
    {
        Floor::create([
            'name'       => $request->name,
            'number'     => $this->generateFloorNumber(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('floors.index')->with('success', 'Floor created successfully.');
    }

    public function edit(Floor $floor)
    {
        return Inertia::render('Dashboard/Floors/edit', [
            'floor' => $floor,
        ]);
    }

    public function update(UpdateFloorRequest $request, Floor $floor)
    {
        // Ensure only the creator (or admin) can update
        $this->authorizeCreatorOrAdmin($request->user(), $floor->created_by);

        $floor->update(['name' => $request->name]);

        return redirect()->route('floors.index')->with('success', 'Floor updated successfully.');
    }

    public function destroy(Request $request, Floor $floor)
    {
        $this->authorizeCreatorOrAdmin($request->user(), $floor->created_by);

        if ($floor->rooms()->exists()) {
            return back()->with('error', 'Cannot delete a floor that has rooms.');
        }

        $floor->delete();

        return redirect()->route('floors.index')->with('success', 'Floor deleted successfully.');
    }

    private function generateFloorNumber(): string
    {
        do {
            $number = (string) random_int(1000, 99999);
        } while (Floor::where('number', $number)->exists());

        return $number;
    }

    private function authorizeCreatorOrAdmin($user, int $createdBy): void
    {
        if (!$user->hasRole('admin') && $user->id !== $createdBy) {
            abort(403, 'You can only modify floors you created.');
        }
    }
}