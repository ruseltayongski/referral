<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    use VerifiesEmails;

    // ✅ Must be a property in Laravel 5.7, not a method
    protected $redirectTo = '/telemedicine/dashboard';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('resend');
    }

    // ✅ Show notice page, redirect if already verified
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectTo)
            : view('auth.verify');
    }

    // ✅ Override to show success page instead of redirecting
    public function verified(Request $request)
    {
        return view('auth.verified'); // ← you want a success page
    }
}