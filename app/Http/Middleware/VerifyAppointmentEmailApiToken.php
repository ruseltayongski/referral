<?php

namespace App\Http\Middleware;

use Closure;

class VerifyAppointmentEmailApiToken
{
    public function handle($request, Closure $next)
    {
        $configuredToken = config('services.appointment_email_api_token');

        if (!is_string($configuredToken) || $configuredToken === '') {
            return response()->json(array(
                'message' => 'API authentication is not configured.',
            ), 503);
        }

        $providedToken = $request->bearerToken();

        if (!is_string($providedToken) || !hash_equals($configuredToken, $providedToken)) {
            return response()->json(array(
                'message' => 'Unauthenticated.',
            ), 401);
        }

        return $next($request);
    }
}