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
use App\Profile;
use App\Province;
use App\Seen;
use App\Tracking;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Login;
use Illuminate\Support\Facades\Session;

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

    public function telemedicineToPatient(Request $req){
        if(!$province = Province::where("province_code",$req->province)->first()->id)
            return 'Invalid Province Code';

        if(!$muncity = Muncity::where("muncity_code",$req->muncity)->first()->id)
            return 'Invalid Municipality Code';

        if(!$barangay = Barangay::where("barangay_code",$req->barangay)->first()->id)
            return 'Invalid Barangay Code';

        $unique = array(
            $req->fname,
            $req->mname,
            $req->lname,
            date('Ymd',strtotime($req->dob)),
            $barangay
        );
        $unique = implode($unique);

        if(!$patient = Profile::where("unique_id",$unique)->first())
            $patient = new Profile();

        $patient->unique_id = $unique;
        $patient->phicID = ($req->phicID) ? $req->phicID: '';
        $patient->fname = $req->fname;
        $patient->mname = $req->mname;
        $patient->lname = $req->lname;
        $patient->dob = $req->dob;
        $patient->sex = $req->sex;
        $patient->province_id = $province;
        $patient->muncity_id = $muncity;
        $patient->barangay_id = $barangay;
        $patient->dengvaxia = "telemedicine";
        $patient->save();

        if($patient->income == "1")
            $patient->income = "Less than 7,890";
        elseif($patient->income == "2")
            $patient->income = "Between 7,890 to 15,780";
        elseif($patient->income == "3")
            $patient->income = "Between 15,780 to 31,560";
        elseif($patient->income == "4")
            $patient->income = "Between 31,560 to 78,900";
        elseif($patient->income == "5")
            $patient->income = "Between 78,900 to 118,350";
        elseif($patient->income == "6")
            $patient->income = "Between 118,350 to 157,800";
        elseif($patient->income == "7")
            $patient->income = "At least 157,800";


        if($patient->unmet == "1")
            $patient->unmet = "Women of Reproductive Age who wants to limit/space but no access to Family Planning Method.";
        elseif($patient->unmet == "2")
            $patient->unmet = "Couples and individuals who are fecund and sexually active and report not wanting any more children or wanting to delay the next pregnancy but are not using any Family Planning Method.";
        elseif($patient->unmet == "3")
            $patient->unmet = "Currently using Family Planning Method but in inappropriate way thus leading to pregnancy.";

        if($patient->water == "1")
            $patient->water = "Farthest user is not more than 250m from point source";
        elseif($patient->water == "2")
            $patient->water = "Farthest user is not more than 25m from communal faucet";
        elseif($patient->water == "3")
            $patient->water = "It has service connection from system.";

        if($patient->toilet == "non")
            $patient->toilet = "Farthest user is not more than 250m from point source";
        elseif($patient->toilet == "comm")
            $patient->toilet = "Farthest user is not more than 25m from communal faucet";
        elseif($patient->toilet == "indi")
            $patient->toilet = "It has service connection from system.";

        if($patient->education == "non")
            $patient->education = "No Education";
        elseif($patient->education == "elem")
            $patient->education = "Elementary Level";
        elseif($patient->education == "elem_grad")
            $patient->education = "Elementary Graduate";
        elseif($patient->education == "high")
            $patient->education = "High School Level";
        elseif($patient->education == "high_grad")
            $patient->education = "High School Graduate";
        elseif($patient->education == "college")
            $patient->education = "College Level";
        elseif($patient->education == "college_grad")
            $patient->education = "College Graduate";
        elseif($patient->education == "vocational")
            $patient->education = "Vocational Course";
        elseif($patient->education == "master")
            $patient->education = "Masteral Degree";

        $patient->province_id = $req->province;
        $patient->muncity_id = $req->muncity;
        $patient->barangay_id = $req->barangay;
        $patient->dengvaxia = "no";
        return $patient;
    }

    public function apiReferPatient(Request $req)
    {
        if(!$province = Province::where("province_code","like","$req->province%")->first())
            return 'Invalid Province Code';

        if(!$muncity = Muncity::where("muncity_code",$req->muncity)->first())
            return 'Invalid Municipality Code';

        if(!$barangay = Barangay::where("barangay_code",$req->barangay)->first())
            return 'Invalid Barangay Code';

        if(!$referring_facility = Facility::where("facility_code",$req->referring_facility)->first())
            return 'Invalid Referring Facility';

        if(!$referred_facility = Facility::where("facility_code",$req->referred_facility)->first())
            return 'Invalid Referred Facility';


        $unique = array(
            $req->fname,
            $req->mname,
            $req->lname,
            date('Ymd',strtotime($req->dob)),
            $barangay->id
        );
        $unique = implode($unique);


        if(!$patient = Patients::where("unique_id",$unique)->first())
            $patient = new Patients();

        $patient->unique_id = $unique;
        $patient->phic_id = $req->phic_id;
        $patient->fname = $req->fname;
        $patient->mname = $req->mname;
        $patient->lname = $req->lname;
        $patient->contact = $req->contact;
        $patient->dob = $req->dob;
        $patient->sex = $req->sex;
        $patient->civil_status = $req->civil_status;
        $patient->muncity = $muncity->id;
        $patient->province = $province->id;
        $patient->brgy = $barangay->id;
        $patient->save();


        //referring doctor
        if(!$referring_doctor = User::where("fname",$req->referring_md_fname)->where("mname",$req->referring_md_mname)->where("lname",$req->referring_md_lname)->first())
            $referring_doctor = new User();

        $referring_doctor->fname = $req->referring_md_fname;
        $referring_doctor->mname = $req->referring_md_mname;
        $referring_doctor->lname = $req->referring_md_lname;
        $referring_doctor->level = 'doctor';
        $referring_doctor->facility_id = $referring_facility->id;
        $referring_doctor->status = 'active';
        $referring_doctor->contact = $req->referring_md_contact;
        $referring_doctor->email = "n/a";
        $referring_doctor->designation = "doctor";
        $referring_doctor->department_id = $req->department_id;
        $referring_doctor->username = "$req->referring_md_fname$req->referring_md_mname$req->referring_md_lname";
        $referring_doctor->password = bcrypt('123');
        $referring_doctor->muncity = $muncity->id;
        $referring_doctor->province = $province->id;
        $referring_doctor->save();


        if(!$referred_doctor = User::where("fname",$req->referred_md_fname)->where("mname",$req->referred_md_mname)->where("lname",$req->referred_md_lname)->first())
            $referred_doctor = new User();

        $referred_doctor->fname = $req->referred_md_fname;
        $referred_doctor->mname = $req->referred_md_mname;
        $referred_doctor->lname = $req->referred_md_lname;
        $referred_doctor->level = 'doctor';
        $referred_doctor->facility_id = $referred_facility->id;
        $referred_doctor->status = 'active';
        $referred_doctor->contact = $req->referred_md_contact;
        $referred_doctor->email = "n/a";
        $referred_doctor->designation = "doctor";
        $referred_doctor->department_id = $req->department_id;
        $referred_doctor->username = "$req->referred_md_fname$req->referred_md_mname$req->referred_md_lname";
        $referred_doctor->password = bcrypt('123');
        $referred_doctor->muncity = $muncity->id;
        $referred_doctor->province = $province->id;
        $referred_doctor->save();

        //refer patient
        $unique_id = "$patient->id-$referring_facility->id-".date('ymdH');
        $match = array(
            'unique_id' => $unique_id
        );

        $data = array(
            'referring_facility' => $referring_facility->id,
            'referred_to' => $referred_facility->id,
            'department_id' => $req->department_id,
            'time_referred' => date('Y-m-d H:i:s'),
            'time_transferred' => '',
            'patient_id' => $patient->id,
            'case_summary' => $req->case_summary,
            'reco_summary' => $req->reco_summary,
            'diagnosis' => $req->diagnosis,
            //'icd_code' => $req->icd_code,
            'reason' => $req->reason,
            'referring_md' => $referring_doctor->id,
            'referred_md' => ($referred_doctor->id) ? $referred_doctor->id: 0,
        );

        $form = PatientForm::updateOrCreate($match,$data);

        $user_code = str_pad($referring_facility->id,3,0,STR_PAD_LEFT);
        $code = date('ymd').'-'.$user_code.'-'.date('His');


        PatientForm::where('unique_id',$unique_id)
            ->update([
                'code' => $code
            ]);
        $type = 'normal';
        $this->addTracking($code,$patient->id,$referring_doctor,$referred_doctor,$req,$type,$form->id,'refer');

        return "success";
    }

    function addTracking($code,$patient_id,$referring_doctor,$referred_doctor,$req,$type,$form_id,$status='')
    {
        $match = array(
            'code' => $code
        );
        $track = array(
            'patient_id' => $patient_id,
            'date_referred' => date('Y-m-d H:i:s'),
            'referred_from' => $referring_doctor->facility_id,
            'referred_to' => $referred_doctor->facility_id,
            'department_id' => $referring_doctor->department_id,
            'referring_md' =>  $referring_doctor->id,
            'action_md' => '',
            'type' => $type,
            'form_id' => $form_id,
            'remarks' => ($req->reason) ? $req->reason: '',
            'status' => ($status=='walkin') ? 'accepted' : 'referred',
            'walkin' => 'no',
            'source' => $req->source
        );

        Tracking::updateOrCreate($match,$track);

        $activity = array(
            'code' => $code,
            'patient_id' => $patient_id,
            'date_referred' => date('Y-m-d H:i:s'),
            'date_seen' => '',
            'referred_from' => $referred_doctor->id,
            'referred_to' => $referring_doctor->facility_id,
            'department_id' => $req->department_id,
            'referring_md' => $referring_doctor->id,
            'action_md' => '',
            'remarks' => ($req->reason) ? $req->reason: '',
            'status' => 'referred'
        );

        Activity::create($activity);

        $this->sendNotification($code,$referred_doctor->facility_id);
    }

    public function sendNotification($code,$referred_facility){
        /**
         * Server Key
         **/
        $SERVER_KEY = "AAAA4yHdicc:APA91bHLB-9vT2V6v3k6EjEXPIJ_OC70Lmd63ftlM3X3fEa1CgLYmCxoYLSIq4f0IULHDtG062jQ2cQ2Uy5hszVtSobwKc59dTZOzlNRV3NdjuIsNcax0UkKSjWwFKhN9VlO7V-rtuad";


        $url = 'https://fcm.googleapis.com/fcm/send';
        /**
         * Give title,body and other param in notification
         **/
        $messageAndroidIos                   = array();
        $messageAndroidIos['code']          = $code;
        $messageAndroidIos['referred_facility']          = $referred_facility;

        $login_token = Login::whereNotNull("token")->where("login","like","%".date('Y-m-d')."%")->where("logout","0000-00-00 00:00:00")->pluck('token')->toArray();
        /**
         * `registration_ids` send token in array for multiple devices
         **/

        $fields = array(
            'registration_ids'  =>  $login_token, // Put your token in Array.
            'data'              =>  $messageAndroidIos,
            'notification'      =>  $messageAndroidIos,
        );

        $headers = array(
            'Authorization: key='.$SERVER_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL,$url);
        curl_setopt( $ch,CURLOPT_POST,true);
        curl_setopt( $ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt( $ch,CURLOPT_POSTFIELDS,json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

    }

    public function referralAppend($code){
        $tracking = Tracking::select(
            'tracking.*',
            DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
            DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            'patients.sex',
            'facility.name as facility_name',
            DB::raw('CONCAT(
                        if(users.level="doctor","Dr. ",""),
                    users.fname," ",users.mname," ",users.lname) as referring_md'),
            DB::raw('CONCAT(action.fname," ",action.mname," ",action.lname) as action_md')
        )
            ->join('patients','patients.id','=','tracking.patient_id')
            ->join('facility','facility.id','=','tracking.referred_from')
            ->leftJoin('users','users.id','=','tracking.referring_md')
            ->leftJoin('users as action','action.id','=','tracking.action_md')
            ->where('code',$code)
            ->first();

        return view('doctor.referral_append',[
            "tracking" => $tracking
        ]);
    }


}
