<?php

namespace App\Http\Controllers\doctor;

use App\Http\Controllers\ParamCtrl;
use App\Login;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                ->orderBy('users.last_login','desc')
                ->paginate(15);

        return view('doctor.list',[
            'title' => 'Online Doctors',
            'data' => $data
        ]);
    }

    public function changeLogin(Request $req)
    {
        $login = User::find($req->loginId);

        if($login->status==='inactive'){
            return 'inactive';
        }else{
            if(Hash::check($req->loginPassword,$login->password))
            {
                $user = Session::get('auth');
                Session::flush();
                User::where('id',$user->id)
                    ->update([
                        'login_status' => 'logout'
                    ]);
                $logout = date('Y-m-d H:i:s');
                $logoutId = Login::where('userId',$user->id)
                    ->orderBy('id','desc')
                    ->first()
                    ->id;

                Login::where('id',$logoutId)
                    ->update([
                        'logout' => $logout
                    ]);

                Session::put('auth',$login);
                $last_login = date('Y-m-d H:i:s');
                User::where('id',$login->id)
                    ->update([
                        'last_login' => $last_login,
                        'login_status' => 'login'
                    ]);
                $checkLastLogin = self::checkLastLogin($login->id);

                if(!$checkLastLogin){
                    $l = new Login();
                    $l->userId = $login->id;
                    $l->login = $last_login;
                    $l->status = 'login';
                    $l->save();
                }
                return redirect('doctor');
            }
            else
            {
                return redirect('doctor?error=1');
            }
        }
    }

    function checkLastLogin($id)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();
        $login = Login::where('userId',$id)
            ->whereBetween('login',[$start,$end])
            ->orderBy('id','desc')
            ->first();
        if($login && (!$login->logout>=$start && $login->logout<=$end)){
            return true;
        }
        return false;
    }
}
