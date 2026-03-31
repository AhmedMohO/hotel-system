<?php

use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\ReceptionistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Client\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Client\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Client\Auth\EmailVerificationController;
use App\Http\Controllers\Client\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Client\Auth\NewPasswordController;
use App\Http\Controllers\Client\Auth\PasswordResetLinkController;
use App\Http\Controllers\Client\Auth\RegisteredClientController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\ReservationController;
use App\Http\Controllers\Receptionist\ClientController as ReceptionistClientController;
use App\Http\Controllers\Dashboard\ClientsReservationsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['role:admin|manager'])->get('dashboard', fn () => Inertia::render('Dashboard/index'))->name('dashboard');

    Route::prefix('dashboard')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::resource('managers', ManagerController::class)
                ->only(['index', 'store', 'update', 'destroy', 'show']);
        });

        Route::middleware('role:receptionist|admin|manager')->group(function () {
            Route::resource('clients-reservations', ClientsReservationsController::class)
                ->only(['index']);
        });

        Route::middleware('role:admin|manager')->group(function () {
            Route::resource('receptionists', ReceptionistController::class)
                ->only(['index', 'store', 'update', 'destroy', 'show']);
            Route::patch('receptionists/{receptionist}/ban', [ReceptionistController::class, 'ban'])
                ->name('receptionists.ban');
            Route::patch('receptionists/{receptionist}/unban', [ReceptionistController::class, 'unban'])
                ->name('receptionists.unban');
        });


        Route::middleware(['auth', 'role:admin|manager'])->group(function () {
            Route::resource('floors', \App\Http\Controllers\Dashboard\FloorController::class)
                ->except(['show']);
            Route::resource('rooms', \App\Http\Controllers\Dashboard\RoomController::class)
                ->except(['show']);
        });
    });


     // ── Admin & Manager: Full Client CRUD ─────────────────────────────────
        // Route: /dashboard/clients
        Route::middleware('role:admin|manager')
            ->prefix('dashboard/clients')
            ->name('dashboard.clients.')
            ->group(function () {
                Route::get('/', [ClientsController::class, 'index'])->name('index');
                Route::get('pending', [ClientsController::class, 'pending'])->name('pending');
                Route::get('my-approved', [ClientsController::class, 'myApproved'])->name('my-approved');
                Route::get('export/excel', [ClientsController::class, 'export'])->name('export');
                Route::post('{client}/approve', [ClientsController::class, 'approve'])->name('approve');
                Route::patch('{client}/unapprove', [ClientsController::class, 'unapprove'])->name('unapprove');
                Route::get('create', [ClientsController::class, 'create'])->name('create');
                Route::post('/', [ClientsController::class, 'store'])->name('store');
                Route::get('{client}/edit', [ClientsController::class, 'edit'])->name('edit');
                Route::put('{client}', [ClientsController::class, 'update'])->name('update');
                Route::delete('{client}', [ClientsController::class, 'destroy'])->name('destroy');
                // wildcard last
                Route::get('{client}', [ClientsController::class, 'show'])->name('show');
            });

        // ── Receptionist: Limited Client Access ───────────────────────────────
        // Route: /dashboard/receptionist/clients
        Route::middleware('role:receptionist')
            ->prefix('dashboard/receptionist/clients')
            ->name('receptionist.clients.')
            ->group(function () {
                // "Manage Clients" → unapproved clients list
                Route::get('/', [ReceptionistClientController::class, 'index'])
                    ->name('index');

                // Approve a client
                Route::post('{client}/approve', [ReceptionistClientController::class, 'approve'])
                    ->name('approve');

                // "My Approved Clients" tab/page
                Route::get('my-approved', [ReceptionistClientController::class, 'myApproved'])
                    ->name('my-approved');
            });

});



Route::prefix('client')
    ->name('client.')
    ->group(function () {

        Route::middleware('guest:client')->group(function () {
            Route::get('register', [RegisteredClientController::class, 'create'])
                ->name('register');
            Route::post('register', [RegisteredClientController::class, 'store'])
                ->name('register.store');

            Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
            Route::post('login', [AuthenticatedSessionController::class, 'store'])
                ->name('login.store');

            Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');
            Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

            Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');
            Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.update');
        });

        // Auth only
        Route::middleware('auth:client')->group(function () {
            Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

            Route::get('verify-email', [EmailVerificationController::class, 'notice'])
                ->name('verification.notice');
            Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');
            Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

            Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');
            Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
                ->name('password.confirm.store');

            // Client pages
            Route::get('dashboard', [ClientDashboardController::class, 'index'])
                ->name('dashboard');
            Route::get('profile', [ProfileController::class, 'edit'])
                ->name('profile.edit');
            Route::patch('profile', [ProfileController::class, 'update'])
                ->name('profile.update');
            Route::get('reservations', [ReservationController::class, 'index'])
                ->name('reservations.index');
            Route::get('reservations/rooms/{room}', [ReservationController::class, 'show'])
                ->name('reservations.show');
            Route::post('reservations/rooms/{room}/payment-intent', [ReservationController::class, 'createPaymentIntent'])
                ->name('reservations.payment-intent');
            Route::post('reservations/rooms/{room}', [ReservationController::class, 'store'])
                ->name('reservations.store');
            Route::get('my-reservations', [ReservationController::class, 'myReservations'])
                ->name('reservations.my');
    });
});


Route::middleware(['auth', 'verified'])->group(function () {
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
