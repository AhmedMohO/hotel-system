<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Reservation;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * GET /dashboard/receptionist/clients
     * "Manage Clients" — unapproved clients only.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['filter', 'per_page', 'page', 'sort']);

        $query = Client::whereNull('approved_at')
            ->select(['id', 'name', 'email', 'mobile', 'country', 'gender', 'avatar_image', 'created_at'])
            ->latest();

        if ($search = $request->input('filter.name')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 10);
        $clients = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Receptionist/Clients/Index', [
            'clients' => $clients,
            'filters' => $filters,
        ]);
    }

    /**
     * POST /dashboard/receptionist/clients/{client}/approve
     */
    public function approve(Client $client): RedirectResponse
    {
        abort_if($client->approved_at, 422, 'Client is already approved.');

        $client->update([
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $client->notify(new ClientApprovedNotification());

        return back()->with('success', "{$client->name} has been approved successfully.");
    }

    /**
     * GET /dashboard/receptionist/clients/my-approved
     * "My Approved Clients" — clients approved by this receptionist.
     */
    public function myApproved(Request $request): Response
    {
        $filters = $request->only(['filter', 'per_page', 'page', 'sort']);

        $query = Client::where('approved_by', Auth::id())
            ->whereNotNull('approved_at')
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
            'backHref' => '/dashboard/receptionist/clients',
        ]);
    }

    /**
     * GET /dashboard/receptionist/clients/reservations
     * "Clients Reservations" — reservations of this receptionist's approved clients.
     */
    public function reservations(Request $request): Response
    {
        $reservations = Reservation::whereHas('client', function ($q) {
                $q->where('approved_by', Auth::id());
            })
            ->with([
                'client:id,name',
                'room:id,number',
            ])
            ->select(['id', 'client_id', 'room_id', 'accompany_number', 'paid_price', 'created_at'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Receptionist/Clients/Reservations', [
            'reservations' => $reservations,
        ]);
    }
}