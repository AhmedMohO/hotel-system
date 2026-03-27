<?php

use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\ReceptionistController;
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


        // Floors & Rooms — Dev 2
Route::middleware(['auth', 'role:admin|manager'])->group(function () {
    Route::resource('floors', \App\Http\Controllers\Dashboard\FloorController::class)
        ->except(['show']);
    Route::resource('rooms', \App\Http\Controllers\Dashboard\RoomController::class)
        ->except(['show']);
});
    });

    Route::inertia('dashboard/manage-clients', 'Dashboard/ManageClients/index')
        ->name('dashboard.manage-clients');
});


require __DIR__.'/settings.php';
