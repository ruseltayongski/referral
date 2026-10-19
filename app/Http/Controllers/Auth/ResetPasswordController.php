<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Save the new password without auto-logging the user in
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();

        event(new PasswordReset($user));
    }

    // After a successful reset, send the user to the login page
    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Your password has been reset. You can now log in.']);
    }
}