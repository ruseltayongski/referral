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
        if($user->level=='doctor'){
            return redirect('/doctor');
        }else if($user->level=='support'){
            return redirect('/support');
        }else if($user->level=='chief'){

        }else{
            Session::flush();
            return redirect('/');
        }
    }
}
