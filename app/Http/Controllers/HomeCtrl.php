<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Session::get('auth');
        if($user->user_priv==5){

        }else if($user->user_priv==6){

        }else{
            Session::flush();
            return redirect('/');
        }
    }
}
