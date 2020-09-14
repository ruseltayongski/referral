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
use Illuminate\Support\Facades\Redirect;

class UserCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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


        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $data = Login::select(
            'users.id as id',
            'users.fname as fname',
            'users.lname as lname',
            'users.mname as mname',
            'users.contact',
            'facility.name as facility',
            'facility.abbr as abbr',
            'department.description as department',
            'login.login as login',
            'login.status as status'
        );

        $data = $data->where(function($q) {
            $q->where('login.status','login')
                ->orwhere('login.status','login_off');
        });

        $data = $data->join('users','users.id','=','login.userId')
                ->join('facility','facility.id','=','users.facility_id')
                ->leftJoin('department','department.id','=','users.department_id');

        $data = $data
                ->where('users.level','doctor')
                ->whereBetween('login.login',[$start,$end])
                ->where('login.logout','0000-00-00 00:00:00')
                ->orderBy('login.id','desc')
                ->get();

        $date_start = Carbon::now()->startOfDay();
        $date_end = Carbon::now()->endOfDay();
        $hospitals = \DB::connection('mysql')->select("call online_facility_view('$date_start','$date_end')");

        return view('doctor.list',[
            'title' => 'Online Doctors',
            'data' => $data,
            'hospitals' => $hospitals
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
//                User::where('id',$user->id)
//                    ->update([
//                        'login_status' => 'logout'
//                    ]);
//                $logout = date('Y-m-d H:i:s');
//                $logoutId = Login::where('userId',$user->id)
//                    ->orderBy('id','desc')
//                    ->first()
//                    ->id;
//
//                Login::where('id',$logoutId)
//                    ->update([
//                        'logout' => $logout
//                    ]);

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

                if($checkLastLogin > 0 ){
                    Login::where('id',$checkLastLogin)
                        ->update([
                            'logout' => $last_login
                        ]);

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
        if(!$login){
            return false;
        }

        return $login->id;
    }

    public function setLogoutTime(Request $request){
        $user = Session::get('auth');
        $input_time_logout = date('Y-m-d H:i:s',strtotime($request->input_time_logout));
        Login::where("userId",$user->id)->orderBy("id","desc")->first()->update([
            "logout" => $input_time_logout
        ]);
        Session::put('logout_time',true);
        return Redirect::back();
    }

}
