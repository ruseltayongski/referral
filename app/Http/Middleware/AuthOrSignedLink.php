<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;

class AuthOrSignedLink
{
    public function handle($request, Closure $next)
    {
        // Path 1: Active session with auth data
        if (Session::has('auth')) {
            return $next($request);
        }

        // Path 2: Guest with a valid signed link
        // hasValidSignature() checks both signature validity AND expiration
        if (URL::hasValidSignature($request)) {
            return $next($request);  // controller builds the guest user itself
        }

        // Path 3: No valid authentication - reject access
        // Expired or missing signatures must not be allowed
        if (! $request->hasValidSignature()) {
        $message = 'Expired or invalid access link';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return response()->view('errors.link_expired', ['message' => $message], 403);
        }
    }
}