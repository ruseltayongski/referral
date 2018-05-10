<?php

namespace App\Http\Controllers\support;

use App\Department;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class UserCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('support');
    }

    public function search(Request $req)
    {
        $data = array(
            'keyword' => $req->keyword
        );
        Session::put('searchKeyword',$data);
        return self::index();
    }

    public function index()
    {
        $search = Session::get('searchKeyword');
        $user = Session::get('auth');
        $data = User::where('facility_id',$user->facility_id)
                ->where('level','doctor');
        if($search){
            $keyword = $search['keyword'];
            $data = $data->where(function($q) use($keyword){
                $q->where('fname','like',"%$keyword%")
                    ->orwhere('mname','like',"%$keyword%")
                    ->orwhere('lname','like',"%$keyword%")
                    ->orwhere(DB::raw('concat(fname," ",lname)'),'like',"$keyword")
                    ->orwhere(DB::raw('concat(lname," ",fname)'),'like',"$keyword");
            });
        }

        $data = $data
                ->orderBy('fname','asc')
                ->paginate(15);
        $departments = Department::get();

        return view('support.users',[
            'title' => 'Manage Users',
            'data' => $data,
            'departments' => $departments
        ]);
    }

    public function create()
    {
        $departments = Department::get();
        return view('support.addUser',[
            'title' => 'Add User',
            'departments' => $departments
        ]);
    }

    public function store(Request $req)
    {
        $user = Session::get('auth');
        $match = array(
            'fname' => $req->fname,
            'mname' => $req->mname,
            'lname' => $req->lname
        );
        $data = array(
            'level' => 'doctor',
            'facility_id' => $user->facility_id,
            'status' => 'active',
            'contact' => $req->contact,
            'email' => $req->email,
            'designation' => $req->designation,
            'department_id' => $req->department_id,
            'username' => $req->username,
            'password' => bcrypt($req->password),
            'muncity' => $user->muncity,
            'province' => $user->province
        );
        User::updateOrCreate($match,$data);
        return 'added';
    }

    public function add(Request $req)
    {
        $user = Session::get('auth');
        $match = array(
            'fname' => $req->fname,
            'mname' => ($req->mname) ? $req->mname: '',
            'lname' => $req->lname
        );
        $email = 'N/A';
        if($req->email)
        {
            $email = $req->email;
        }
        $data = array(
            'level' => 'doctor',
            'facility_id' => $user->facility_id,
            'status' => 'active',
            'contact' => $req->contact,
            'email' => $email,
            'designation' => $req->designation,
            'department_id' => $req->department_id,
            'username' => $req->username,
            'password' => bcrypt($req->password),
            'muncity' => $user->muncity,
            'province' => $user->province
        );
        User::updateOrCreate($match,$data);

        return redirect()->back()->with('status','added');
    }

    public function update(Request $req)
    {
        $data = array(
            'fname' => $req->fname,
            'mname' => $req->mname,
            'lname' => $req->lname,
            'level' => 'doctor',
            'contact' => $req->contact,
            'email' => ($req->email) ? $req->email: 'N/A',
            'designation' => $req->designation,
            'department_id' => $req->department_id,
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

    public function info($user_id)
    {
        return User::find($user_id);
    }
}
