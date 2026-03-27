<?php

use App\Http\Controllers\Api\RoomsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/rooms', [RoomsController::class, 'index']);
