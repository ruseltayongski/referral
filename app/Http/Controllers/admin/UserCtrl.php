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
        //$this->middleware('admin');
        //$this->middleware('doctor');
    }

    public function index(Request $request)
    {
        $keyword = $request->search;
        $data = new User();
        if($keyword){
            $data = $data
                ->where(function($q) use($keyword){
                $q->where('fname','like',"%$keyword%")
                    ->orwhere('mname','like',"%$keyword%")
                    ->orwhere('lname','like',"%$keyword%")
                    ->orwhere('username','like',"%$keyword%")
                    ->orwhere(\DB::raw('concat(fname," ",lname)'),'like',"$keyword")
                    ->orwhere(\DB::raw('concat(lname," ",fname)'),'like',"$keyword");
            });
        }

        if($request->facility_filter)
            $data = $data->where("facility_id",$request->facility_filter);

        $data = $data
                ->where(function($q){
                    $q->where("level",'support')
                        ->orWhere("level","opcen")
                        ->orWhere("level","bed_tracker");
                    })
                ->orderBy('lname','asc')
                ->paginate(20);

        $facility = Facility::orderBy('name','asc')->get();

        return view('admin.users',[
            'title' => 'List of Support User',
            'data' => $data,
            'facility' => $facility,
            'search' => $keyword,
            'facility_filter' => $request->facility_filter
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

        $user->facility_id = $req->facility_id;
        $province = Facility::find($req->facility_id)->province;

        $user->level = $req->level;
        $user->province = $province;

        Session::put('auth',$user);
        Session::put('admin',true);

        return redirect($user->level);
    }

    public function info($user_id)
    {
        $user = User::find($user_id);
        $facility = Facility::get();
        return view('admin.users_body',[
            "user" => $user,
            "facility" => $facility,
            "user_id" => $user_id
        ]);
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
        $facility = Facility::find($req->facility_id);
        $data = array(
            'fname' => $req->fname,
            'mname' => $req->mname,
            'lname' => $req->lname,
            'level' => $req->level,
            'facility_id' => $req->facility_id,
            'status' => 'active',
            'contact' => $req->contact,
            'email' => $req->email,
            'designation' => $req->designation,
            'username' => $req->username,
            'password' => bcrypt($req->password),
            'muncity' => $facility->muncity,
            'province' => $facility->province
        );
        if($req->user_id == "no_id"){
            Session::put("manage_user","Successfully added new account");
            User::create($data);
        }
        else{
            Session::put("manage_user","Successfully updated account");
            User::find($req->user_id)->update($data);
        }
    }

}
