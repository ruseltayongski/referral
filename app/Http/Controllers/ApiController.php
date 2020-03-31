<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Baby;
use App\Barangay;
use App\Department;
use App\Facility;
use App\Feedback;
use App\Icd10;
use App\Issue;
use App\ModeTransportation;
use App\Muncity;
use App\PatientForm;
use App\Patients;
use App\PregnantForm;
use App\Province;
use App\Seen;
use App\Tracking;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Login;

class ApiController extends Controller
{
    public function api(Request $req)
    {
        if($req->r==='login')
            return self::login($req);
        elseif($req->r=='getContactList')
            return User::select(\DB::raw("concat(users.fname,' ',users.lname) as name"),'department.description as department','facility.name as hospital')
                ->leftJoin('facility','facility.id','=','users.facility_id')
                ->leftJoin('department','department.id','=','users.department_id')
                ->get();
        else
            return 'Error API';
    }

    public function login(Request $req)
    {
        $user = $req->user;
        $pass = $req->pass;

        $login = User::
              select('users.*','facility.name as hospital','department.description as department')
            ->leftJoin('facility','facility.id','=','users.facility_id')
            ->leftJoin('department','department.id','=','users.department_id')
            ->where('username','=',$user)
            ->first();
        if($login){
            if($login->status==='inactive'){
                return 'inactive';
            }else{
                if(Hash::check($pass,$login->password))
                {
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
                    return array(
                        'name' => $login->fname.' '.$login->lname,
                        'department' => $login->department,
                        'hospital' => $login->hospital,
                        'facility_id' => $login->facility_id,
                        'level' => $login->level,
                        'status' => 'success'
                    );
                }
                else
                {
                    return array(
                        'status' => 'denied'
                    );
                }
            }
        }else{
            return array(
                'status' => 'denied'
            );
        }
    }

    public function checkLastLogin($id)
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

    public function getActivity($offset,$limit,Request $request){
        if($request->count)
            return Activity::count();

        return Activity::skip($offset)->take($limit)->get();
    }
    public function getBaby(Request $request){
        if($request->count)
            return Baby::count();

        return Baby::get();
    }
    public function getBarangay(Request $request){
        if($request->count)
            return Barangay::count();

        return Barangay::get();
    }
    public function getDepartment(Request $request){
        if($request->count)
            return Department::count();

        return Department::get();
    }
    public function getFacility(Request $request){
        if($request->count)
            return Facility::count();

        return Facility::get();
    }
    public function getFeedback(Request $request){
        if($request->count)
            return Feedback::count();

        return Feedback::get();
    }
    public function getIcd10(Request $request){
        if($request->count)
            return Icd10::count();

        return Icd10::get();
    }
    public function getIssue(Request $request){
        if($request->count)
            return Issue::count();

        return Issue::get();
    }
    public function getLogin($offset,$limit,Request $request){
        if($request->count)
            return Login::count();

        return Login::skip($offset)->take($limit)->get();
    }
    public function getModeTransportation(Request $request){
        if($request->count)
            return ModeTransportation::count();

        return ModeTransportation::get();
    }
    public function getMuncity(Request $request){
        if($request->count)
            return Muncity::count();

        return Muncity::get();
    }
    public function getPatientForm($offset,$limit,Request $request){
        if($request->count)
            return PatientForm::count();

        return PatientForm::skip($offset)->take($limit)->get();
    }
    public function getPatients($offset,$limit,Request $request){
        if($request->count)
            return Patients::count();

        return Patients::skip($offset)->take($limit)->get();
    }
    public function getPregnantForm($offset,$limit,Request $request){
        if($request->count)
            return PregnantForm::count();

        return PregnantForm::skip($offset)->take($limit)->get();
    }
    public function getProvince(Request $request){
        if($request->count)
            return Province::count();

        return Province::get();
    }
    public function getSeen(Request $request){
        if($request->count)
            return Seen::count();

        return Seen::get();
    }
    public function getTracking($offset,$limit,Request $request){
        if($request->count)
            return Tracking::count();

        return Tracking::skip($offset)->take($limit)->get();
    }
    public function getUsers(Request $request){
        if($request->count)
            return User::count();

        return User::get();
    }


}
