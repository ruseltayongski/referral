<?php

namespace App\Http\Controllers\doctor;

use App\Http\Controllers\ParamCtrl;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    public function searchDoctor(Request $req)
    {
        $data = array(
            'keyword' => $req->keyword,
            'facility_id' => $req->facility_id
        );
        Session::put('search_doctor',$data);
        return self::index();
    }

    public function index()
    {
        ParamCtrl::lastLogin();
        $search = Session::get('search_doctor');

        $data = User::select(
                'users.*',
                'facility.name as facility',
                'department.description as department'
        );

        $data = $data->where(function($q) {
            $q->where('login_status','login')
                ->orwhere('login_status','login_off');
        });

        $data = $data->join('facility','facility.id','=','users.facility_id')
                ->leftJoin('department','department.id','=','users.department_id');

        if($search['keyword'])
        {
            $keyword = $search['keyword'];
            $data = $data->where(function($q) use ($keyword){
                $q->where('users.lname',"$keyword")
                    ->orwhere(DB::raw('concat(users.fname," ",users.lname)'),"$keyword");
            });
        }

        if($search['facility_id'])
        {
            $facility_id = $search['facility_id'];
            $data = $data->where('users.facility_id',$facility_id);
        }

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $data = $data
                ->where('users.level','doctor')
                ->whereBetween('users.last_login',[$start,$end])
                ->orderBy('users.facility_id','asc')
                ->orderBy('users.fname','asc')
                ->paginate(15);

        return view('doctor.list',[
            'title' => 'Online Doctors',
            'data' => $data
        ]);
    }
}
