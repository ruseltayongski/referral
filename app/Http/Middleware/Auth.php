<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (URL::hasValidSignature($request)) {
            return $next($request);
        }

        $user = Session::get('auth');
        if(!$user){
            return redirect()->guest('/login');
        }

        if(Session::get('force_password_change') && !$request->is('security/change-password*')){
            return redirect('/security/change-password');
        }

        $date_now = date("Y-m-d");
        $check_login_now = \App\Login::where("userId",$user->id)->where("login","like","%$date_now%")->first();
        if(!$check_login_now){
            return redirect('/login_expire');
        }

        //return response()->json($user, 401);
        return $next($request);
    }
}
