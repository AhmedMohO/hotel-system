<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('Dashboard/index');
    }

    public function statistics()
    {
        $totalReservations = Reservation::count();
        $totalRevenue = Reservation::sum('paid_price'); // Already in cents
        $activeClients = Client::count();
        $totalRooms = Room::count();
        
        // Get unique rooms occupied from reservations
        $roomsOccupied = Reservation::distinct('room_id')->count('room_id');

        return response()->json([
            'totalReservations' => $totalReservations,
            'totalRevenue' => $totalRevenue,
            'activeClients' => $activeClients,
            'roomsOccupied' => $roomsOccupied,
            'totalRooms' => $totalRooms,
        ]);
    }

    public function genderDistribution(Request $request)
    {
        $year = $request->query('year', now()->year);

        $totals = Reservation::whereYear('reservations.created_at', $year)
            ->join('clients', 'reservations.client_id', '=', 'clients.id')
            ->selectRaw("SUM(CASE WHEN LOWER(clients.gender) = 'male' THEN 1 ELSE 0 END) as male_total")
            ->selectRaw("SUM(CASE WHEN LOWER(clients.gender) = 'female' THEN 1 ELSE 0 END) as female_total")
            ->first();

        $maleTotal = (int) ($totals->male_total ?? 0);
        $femaleTotal = (int) ($totals->female_total ?? 0);

        return response()->json([
            'labels' => ['Male', 'Female'],
            'datasets' => [
                [
                    'label' => "Reservations by Gender ($year)",
                    'data' => [$maleTotal, $femaleTotal],
                    'backgroundColor' => ['#3b82f6', '#ec4899'],
                    'borderColor' => ['#1e40af', '#be123c'],
                    'borderWidth' => 2,
                ],
            ],
        ]);
    }

    public function reservationsRevenueMonthly(Request $request)
    {
        $year = $request->query('year', now()->year);

        $data = Reservation::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(paid_price) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => (int) $item->month,
                    'total' => $item->total / 100,
                ];
            });

        $months = collect(range(1, 12))->map(function ($month) use ($data) {
            $found = $data->firstWhere('month', $month);
            return [
                'month' => $month,
                'total' => $found ? $found['total'] : 0,
            ];
        });

        return response()->json([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => "Monthly Revenue ($year)",
                    'data' => $months->pluck('total'),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
        ]);
    }

    public function reservationsByCountry(Request $request)
    {
        $year = $request->query('year', now()->year);

        $data = Reservation::whereYear('reservations.created_at', $year)
            ->join('clients', 'reservations.client_id', '=', 'clients.id')
            ->selectRaw('clients.country, COUNT(*) as total')
            ->groupBy('clients.country')
            ->orderByDesc('total')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'country' => $item->country,
                    'total' => (int) $item->total,
                ];
            });

        $colors = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
            '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#6366f1',
            '#14b8a6', '#84cc16', '#0ea5e9', '#f43f5e', '#a78bfa',
            '#22d3ee', '#fbbf24', '#fb923c', '#fca5a5', '#fecaca',
        ];

        return response()->json([
            'labels' => $data->pluck('country'),
            'datasets' => [
                [
                    'label' => "Reservations by Country ($year)",
                    'data' => $data->pluck('total'),
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
        ]);
    }

    public function topReservationClients(Request $request)
    {
        $year = $request->query('year', now()->year);

        $data = Reservation::whereYear('reservations.created_at', $year)
            ->join('clients', 'reservations.client_id', '=', 'clients.id')
            ->selectRaw('clients.name, COUNT(*) as total')
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'total' => (int) $item->total,
                ];
            });

        $colors = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
            '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#6366f1',
        ];

        return response()->json([
            'labels' => $data->pluck('name'),
            'datasets' => [
                [
                    'label' => "Top Clients by Reservations ($year)",
                    'data' => $data->pluck('total'),
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
        ]);
    }
}
