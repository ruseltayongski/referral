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
        if (URL::hasValidSignature($request)) {
            return $next($request);  // controller builds the guest user itself
        }

        // Path 3: No session, no valid signature
        return redirect('/login')
            ->with('error', 'Please log in or use a valid appointment link.');
    }
}