<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManagerRequest;
use App\Http\Requests\UpdateManagerRequest;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ManagerController extends Controller
{
    public function __construct(private readonly AvatarService $avatarService) {}

    public function index(Request $request)
    {
        $managers = QueryBuilder::for(User::role('manager'))
            ->allowedSorts(
                AllowedSort::field('name'),
                AllowedSort::field('email'),
                AllowedSort::field('created_at'),
            )
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
            )
            ->paginate($request->filled('per_page') ? $request->per_page : 10)
            ->withQueryString();

        return Inertia::render('Dashboard/Managers/index', [
            'managers' => $managers,
            'filters' => $request->all(['filter', 'sort', 'per_page']),
        ]);
    }

    public function show(int $manager)
    {
        $manager = User::withTrashed()->findOrFail($manager);

        return Inertia::render('Dashboard/Managers/Show', [
            'manager' => [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'national_id' => $manager->national_id,
                'avatar_image' => $manager->avatar_image,
                'created_at' => $manager->created_at->toDateTimeString(),
                'deleted_at' => $manager->deleted_at?->toDateTimeString(),
            ]
        ]);
    }

    public function store(StoreManagerRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['avatar_image'] = $this->avatarService->handle($request->file('avatar_image'));

        $manager = User::create($validated);
        $manager->assignRole('manager');

        return redirect()->route('managers.index')->with('success', 'Manager created successfully.');
    }

    public function update(UpdateManagerRequest $request, User $manager)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['avatar_image'] = $this->avatarService->handle(
            $request->file('avatar_image'),
            $manager->avatar_image,
            $request->boolean('remove_avatar')
        );

        $manager->update($validated);

        return redirect()->route('managers.index')->with('success', 'Manager updated successfully.');
    }

    public function destroy(User $manager)
    {
        if ($manager->canBeHardDeleted()) {
            $manager->forceDelete();
            $message = 'Manager permanently deleted because they had no linked records.';
        } else {
            $manager->delete();
            $message = 'Manager soft-deleted because they have related records in the system.';
        }

        return redirect()->back()->with('success', $message);
    }
}
