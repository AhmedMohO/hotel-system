<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: EnsureClientIsApproved
 *
 * Apply this to any reservation route to block unapproved clients.
 *
 * Usage in routes/web.php:
 *   Route::middleware(['auth:client', 'client.approved'])->group(function () {
 *       Route::resource('reservations', ReservationController::class);
 *   });
 *
 * Register in bootstrap/app.php (Laravel 11+):
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias(['client.approved' => EnsureClientIsApproved::class]);
 *   })
 */
class EnsureClientIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->user(); // Assumes client guard is active

        if (! $client || ! $client->approved_at) {
            abort(403, 'Your account is pending approval. Please wait for a receptionist to approve your account.');
        }

        return $next($request);
    }
}
