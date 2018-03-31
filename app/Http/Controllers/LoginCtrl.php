<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginCtrl extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function validateLogin(Request $req)
    {
        $login = User::where('username',$req->username)
            ->first();
        if($login){

            if($login->status==='inactive'){
                return 'inactive';
            }else{
                if(Hash::check($req->password,$login->password))
                {
                    Session::put('auth',$login);
                    if($login->level=='doctor'){
                        return 'doctor';
                    }else if($login->level=='chief'){
                        return 'chief';
                    }else{
                        Session::forget('auth');
                        return 'denied';
                    }
                }
                else
                {
                    return 'error';
                }
            }
        }else{
            return 'error';
        }
    }
}
