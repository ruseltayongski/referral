<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

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
        $user = Session::get('auth');
        $date_now = date("Y-m-d");
        $check_login_now = \App\Login::where("userId",$user->id)->where("login","like","%$date_now%")->first();
        if(!$user){
            return redirect()->guest('/login');
        }
        else if(!$check_login_now){
            return redirect('/login_expire');
        }
        return $next($request);
    }
}
