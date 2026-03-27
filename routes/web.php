<?php

use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\ReceptionistController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard/index')->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::resource('managers', ManagerController::class)
                ->only(['index', 'store', 'update', 'destroy', 'show']);
        });

        Route::middleware('role:admin|manager')->group(function () {
            Route::resource('receptionists', ReceptionistController::class)
                ->only(['index', 'store', 'update', 'destroy', 'show']);
            Route::patch('receptionists/{receptionist}/ban', [ReceptionistController::class, 'ban'])
                ->name('receptionists.ban');
            Route::patch('receptionists/{receptionist}/unban', [ReceptionistController::class, 'unban'])
                ->name('receptionists.unban');
        });
    });

    Route::inertia('dashboard/manage-clients', 'Dashboard/ManageClients/index')
        ->name('dashboard.manage-clients');

    Route::middleware('role:admin|manager')->prefix('dashboard/api')->group(function () {
        Route::get('statistics', [DashboardController::class, 'statistics'])
            ->name('dashboard.api.statistics');
        Route::get('gender-distribution', [DashboardController::class, 'genderDistribution'])
            ->name('dashboard.api.gender-distribution');
        Route::get('reservations-revenue-monthly', [DashboardController::class, 'reservationsRevenueMonthly'])
            ->name('dashboard.api.reservations-revenue-monthly');
        Route::get('reservations-by-country', [DashboardController::class, 'reservationsByCountry'])
            ->name('dashboard.api.reservations-by-country');
        Route::get('top-reservation-clients', [DashboardController::class, 'topReservationClients'])
            ->name('dashboard.api.top-reservation-clients');
    });
});

require __DIR__.'/settings.php';
