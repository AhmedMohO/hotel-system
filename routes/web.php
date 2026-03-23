<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard/index')->name('dashboard');


    Route::inertia('dashboard/manage-managers', 'Dashboard/ManageManagers/index')->name('dashboard.manage-managers');


    Route::inertia('dashboard/manage-receptionists', 'Dashboard/ManageReceptionists/index')->name('dashboard.manage-receptionists');


    Route::inertia('dashboard/manage-clients', 'Dashboard/ManageClients/index')->name('dashboard.manage-clients');

});

require __DIR__.'/settings.php';
