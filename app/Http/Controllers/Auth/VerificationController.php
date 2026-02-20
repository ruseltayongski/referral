<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('verify', 'resend');
        $this->middleware('throttle:6,1')->only('resend');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectTo)
            : view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        try {
            // Get user by ID
            $userId = $request->route('id');
            $hash = $request->route('hash');
            
            Log::info('Email verification attempt', [
                'user_id' => $userId,
                'hash_received' => $hash
            ]);
            
            $user = User::findOrFail($userId);
            
            // Calculate expected hash
            $expectedHash = sha1($user->getEmailForVerification());
            
            Log::info('Hash comparison', [
                'email' => $user->getEmailForVerification(),
                'hash_expected' => $expectedHash,
                'hash_received' => $hash,
                'match' => hash_equals((string) $hash, $expectedHash)
            ]);

            // Verify the hash matches the user's email
            if (! hash_equals((string) $hash, $expectedHash)) {
                Log::warning('Hash mismatch for user', ['user_id' => $userId]);
                return redirect('/login')
                    ->with('error', 'Invalid or expired verification link. Please request a new one.');
            }

            // Check if already verified
            if ($user->hasVerifiedEmail()) {
                Log::info('User already verified', ['user_id' => $userId]);
                return redirect('/login')->with('message', 'Your email is already verified. Please log in.');
            }

            // Mark as verified and fire event
            $result = $user->markEmailAsVerified();
            
            Log::info('Email marked as verified', [
                'user_id' => $userId,
                'save_result' => $result,
                'email_verified_at' => $user->email_verified_at
            ]);
            
            if ($result) {
                event(new Verified($user));
            }

            // Show success page
            return view('auth.verified');
        } catch (\Exception $e) {
            Log::error('Email verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')
                ->with('error', 'An error occurred during verification. Please try again.');
        }
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        // Check if user is already verified
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo);
        }

        // Send verification notification
        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}