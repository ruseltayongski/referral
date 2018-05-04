<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class UserCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {

    }

    public function loginAs()
    {
        return view('admin.loginAs',[
            'title' => 'Login As'
        ]);
    }

    public function assignLogin(Request $req)
    {
        $user = Session::get('auth');

        print_r($_POST);
        $user->facility_id = $req->facility_id;
        $user->level = $req->level;
        Session::put('auth',$user);
        Session::put('admin',true);

        return redirect('/');
    }
}
