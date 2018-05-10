<?php

namespace App\Http\Controllers\admin;

use App\Facility;
use App\User;
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
        $data = User::where('level','support')
            ->orderBy('lname','asc')
            ->paginate(10);
        $facility = Facility::orderBy('name','asc')->get();
        return view('admin.users',[
            'title' => 'List of Support User',
            'data' => $data,
            'facility' => $facility
        ]);
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

    public function info($user_id)
    {
        return User::find($user_id);
    }
    public function check($string)
    {
        $user = Session::get('auth');
        $check = User::where('username',$string)
            ->where('id','!=',$user->id)
            ->first();
        if($check){
            return '1';
        }
        return '0';
    }

    public function store(Request $req)
    {
        $user = Session::get('auth');
        $match = array(
            'fname' => $req->fname,
            'mname' => $req->mname,
            'lname' => $req->lname
        );
        $facility = Facility::find($req->facility_id);
        $data = array(
            'level' => 'support',
            'facility_id' => $req->facility_id,
            'status' => 'active',
            'contact' => $req->contact,
            'email' => $req->email,
            'designation' => $req->designation,
            'username' => $req->username,
            'password' => bcrypt($req->password),
            'muncity' => $facility->muncity,
            'province' => $user->province
        );
        User::updateOrCreate($match,$data);
        return 'added';
    }

    public function update(Request $req)
    {
        $data = array(
            'fname' => $req->fname,
            'mname' => $req->mname,
            'lname' => $req->lname,
            'level' => 'support',
            'contact' => $req->contact,
            'email' => $req->email,
            'designation' => $req->designation,
            'facility_id' => $req->facility_id,
            'username' => $req->username,
            'status' => $req->status
        );

        if ($req->password)
        {
            $data['password'] = bcrypt($req->password);
        }


        User::where('id',$req->user_id)
            ->update($data);
        return 'updated';
    }

    public function checkUpdate($string,$user_id)
    {
        $user = Session::get('auth');
        $check = User::where('username',$string)
            ->where('id','!=',$user->id)
            ->where('id','!=',$user_id)
            ->first();
        if($check){
            return '1';
        }
        return '0';
    }
}
