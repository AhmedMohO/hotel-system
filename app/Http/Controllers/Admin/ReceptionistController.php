<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceptionistRequest;
use App\Http\Requests\UpdateReceptionistRequest;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ReceptionistController extends Controller
{
    public function __construct(private readonly AvatarService $avatarService) {}

    public function index(Request $request)
    {
        /** @var User $authUser */
        $authUser = $request->user();

        $query = QueryBuilder::for(User::role('receptionist')->with('creator:id,name'))
            ->allowedSorts(
                AllowedSort::field('name'),
                AllowedSort::field('email'),
                AllowedSort::field('created_at'),
            )
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
            );

        // Managers only see their own receptionists; Admins see all
        if ($authUser->hasRole('manager')) {
            $query->where('created_by', $authUser->id);
        }

        $receptionists = $query
            ->paginate($request->filled('per_page') ? $request->per_page : 10)
            ->withQueryString()
            ->through(fn (User $r) => [
                'id' => $r->id,
                'name' => $r->name,
                'email' => $r->email,
                'national_id' => $r->national_id,
                'avatar_image' => $r->avatar_image,
                'created_at' => $r->created_at,
                'created_by_name' => $r->creator?->name,
                'banned_at' => $r->isBanned() ? now()->toDateTimeString() : null,
            ]);

        return Inertia::render('Dashboard/Receptionists/index', [
            'receptionists' => $receptionists,
            'filters' => $request->all(['filter', 'sort', 'per_page']),
        ]);
    }

    public function show(Request $request, User $receptionist)
    {
        /** @var User $authUser */
        $authUser = $request->user();

        // Managers can only view their own receptionists
        if ($authUser->hasRole('manager') && $receptionist->created_by !== $authUser->id) {
            abort(403, 'Unauthorized action.');
        }

        $receptionist->load('creator:id,name');

        return Inertia::render('Dashboard/Receptionists/Show', [
            'receptionist' => [
                'id' => $receptionist->id,
                'name' => $receptionist->name,
                'email' => $receptionist->email,
                'national_id' => $receptionist->national_id,
                'avatar_image' => $receptionist->avatar_image,
                'created_at' => $receptionist->created_at->toDateTimeString(),
                'created_by' => $receptionist->creator ? [
                    'id' => $receptionist->creator->id,
                    'name' => $receptionist->creator->name,
                ] : null,
                'banned_at' => $receptionist->isBanned() ? $receptionist->banned_at->toDateTimeString() : null,
            ]
        ]);
    }

    public function store(StoreReceptionistRequest $request)
    {
        /** @var User $authUser */
        $authUser = $request->user();

        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['avatar_image'] = $this->avatarService->handle($request->file('avatar_image'));
        $validated['created_by'] = $authUser->id;

        $receptionist = User::create($validated);
        $receptionist->assignRole('receptionist');

        return redirect()->route('receptionists.index')->with('success', 'Receptionist created successfully.');
    }

    public function update(UpdateReceptionistRequest $request, User $receptionist)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['avatar_image'] = $this->avatarService->handle(
            $request->file('avatar_image'),
            $receptionist->avatar_image,
            $request->boolean('remove_avatar')
        );

        $receptionist->update($validated);

        return redirect()->route('receptionists.index')->with('success', 'Receptionist updated successfully.');
    }

    public function destroy(User $receptionist)
    {
        if ($receptionist->canBeHardDeleted()) {
            $receptionist->forceDelete();
            $message = 'Receptionist permanently deleted because they had no linked records.';
        } else {
            $receptionist->delete();
            $message = 'Receptionist soft-deleted because they have related records in the system.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function ban(User $receptionist)
    {
        $receptionist->ban();

        return redirect()->back()->with('success', 'Receptionist banned.');
    }

    public function unban(User $receptionist)
    {
        $receptionist->unban();

        return redirect()->back()->with('success', 'Receptionist unbanned.');
    }
}
