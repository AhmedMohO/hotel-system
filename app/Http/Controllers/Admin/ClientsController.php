<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;

class ClientsController extends Controller
{
    /**
     * GET /dashboard/clients
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['filter', 'per_page', 'page', 'sort']);

        $query = Client::with('approvedBy:id,name')
            ->select(['id', 'name', 'email', 'avatar_image', 'country', 'gender', 'approved_at', 'approved_by', 'created_at'])
            ->latest();

        if ($search = $request->input('filter.name')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 10);
        $clients = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Dashboard/ManageClients/index', [
            'clients' => $clients,
            'filters' => $filters,
        ]);
    }

    /**
     * GET /dashboard/clients/pending
     */
    public function pending(Request $request): Response
    {
        $filters = $request->only(['filter', 'per_page', 'page', 'sort']);

        $query = Client::whereNull('approved_at')
            ->select(['id', 'name', 'email', 'avatar_image', 'country', 'gender', 'mobile', 'created_at'])
            ->latest();

        if ($search = $request->input('filter.name')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 10);
        $clients = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Dashboard/ManageClients/Pending', [
            'clients' => $clients,
            'filters' => $filters,
        ]);
    }

    /**
     * GET /dashboard/clients/my-approved
     * For admin/manager: all approved clients.
     */
    public function myApproved(Request $request): Response
    {
        $filters = $request->only(['filter', 'per_page', 'page', 'sort']);

        $query = Client::whereNotNull('approved_at')
            ->select(['id', 'name', 'email', 'mobile', 'country', 'gender', 'avatar_image', 'approved_at'])
            ->latest('approved_at');

        if ($search = $request->input('filter.name')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 10);
        $clients = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Receptionist/Clients/MyApproved', [
            'clients'  => $clients,
            'filters'  => $filters,
            'backHref' => '/dashboard/clients',
        ]);
    }

    /**
     * GET /dashboard/clients/create
     */
    public function create(): Response
    {
        return Inertia::render('Dashboard/ManageClients/Create');
    }

    /**
     * POST /dashboard/clients
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:clients,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'mobile'   => ['required', 'string', 'max:20'],
            'country'  => ['required', 'string', 'max:100'],
            'gender'   => ['required', 'in:Male,Female'],
        ]);

        $client = Client::create([
            ...$data,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $client->notify(new ClientApprovedNotification);

        return redirect()->route('dashboard.clients.index')
            ->with('success', "{$client->name} has been added successfully.");
    }

    /**
     * POST /dashboard/clients/{client}/approve
     */
    public function approve(Client $client): RedirectResponse
    {
        abort_if($client->approved_at, 422, 'Client is already approved.');

        $client->update([
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $client->notify(new ClientApprovedNotification);

        return back()->with('success', "{$client->name} has been approved successfully.");
    }

    /**
     * PATCH /dashboard/clients/{client}/unapprove
     */
    public function unapprove(Client $client): RedirectResponse
    {
        abort_unless($client->approved_at, 422, 'Client is not approved.');

        $client->update([
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', "{$client->name} has been unapproved.");
    }

    /**
     * GET /dashboard/clients/{client}
     */
    public function show(Client $client): Response
    {
        $client->load('approvedBy:id,name');

        return Inertia::render('Dashboard/ManageClients/Show', [
            'client' => $client,
        ]);
    }

    /**
     * GET /dashboard/clients/{client}/edit
     */
    public function edit(Client $client): Response
    {
        return Inertia::render('Dashboard/ManageClients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * PUT /dashboard/clients/{client}
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', Rule::unique('clients', 'email')->ignore($client->id)],
            'mobile'  => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'gender'  => ['required', 'in:Male,Female'],
        ]);

        $client->update($data);

        return redirect()->route('dashboard.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * DELETE /dashboard/clients/{client}
     * Soft-deletes the client (sets deleted_at).
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('dashboard.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * GET /dashboard/clients/export/excel
     */
    public function export()
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }
}