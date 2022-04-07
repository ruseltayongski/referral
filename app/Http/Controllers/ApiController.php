<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Baby;
use App\Barangay;
use App\BedTracker;
use App\Department;
use App\Facility;
use App\Feedback;
use App\Icd;
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
    public function api(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        if($request->date_range){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Activity::select("created_at")->orderBy("created_at","asc")->first()->created_at;
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        if($request->request_type=='facility'){
            return $this->getFacilities($request);
        }
        if($request->request_type == 'incoming' || $request->request_type == 'outgoing') {
            if($request->top){
                if($request->top == "denied"){
                    $top = "denied";
                    $request->top = "rejected";
                }
                else
                    $top = $request->top;

                $top_referred = $this->topReferral($request,$date_start,$date_end);
                $data = [];
                foreach($top_referred as $referred){
                    $data[] = [
                        "province" => Province::find($request->province)->description ? Province::find($request->province)->description  : "ALL",
                        "facility_name" => $referred->facility_name,
                        "request_type" => $request->request_type,
                        "date_range" => date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)),
                        "data" => [
                            $top => $referred->count
                        ]
                    ];
                }
                return $data;
            }

            $incoming = $this->apiReport($request,$date_start,$date_end);
            $data = [];

            foreach($incoming as $inc){
                $data[] = [
                    "province" => $inc->province,
                    "facility_id" => $inc->facility_id,
                    "facility_name" => $inc->name,
                    "hospital_type" => $inc->hospital_type,
                    "date_range" => date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)),
                    "data" => [
                        "referred" => $inc->referred,
                        "redirected" => $inc->redirected,
                        "transferred" => $inc->transferred,
                        "accepted" => $inc->accepted,
                        "denied" => $inc->denied,
                        "cancelled" => $inc->cancelled,
                        "seen_only" => $inc->seen_only,
                        "request_call" => $inc->request_call,
                        "not_seen" => $inc->not_seen,
                        "redirected_spam" => $inc->redirected_spam
                    ]
                ];

            }

            return $data;
        }

        elseif($request->request_type=="bed"){
            $beds = $this->apiBedAvailability($request);
            $data = [];
            foreach($beds as $bed){

                $encoded_by = BedTracker::
                select("bed_tracker.id","users.fname","users.mname","users.lname","bed_tracker.created_at")
                    ->leftJoin("users","users.id","=","bed_tracker.encoded_by")
                    ->where("bed_tracker.facility_id","=",$bed->id)
                    ->where("users.level","!=","opcen")
                    ->orderBy("bed_tracker.id","desc")
                    ->first();
                $created_at = $encoded_by->created_at;
                $encoded_by = ucfirst($encoded_by->fname).' '.strtoupper($encoded_by->mname[0]).'. '.ucfirst($encoded_by->lname);

                $data[] = [
                    "province" => Province::find($bed->province)->description,
                    "facility_name" => $bed->facility_name,
                    "hospital_type" => $bed->hospital_type,
                    "encoded_by" => $encoded_by,
                    "created_at" => $created_at,
                    "data" => [
                        [
                            "UnusedCovid" => $bed->UnusedCovid,
                            "UsedCovid" => $bed->UsedCovid,
                            "UnusedNoncovid" => $bed->UnusedNoncovid,
                            "UsedNoncovid" => $bed->UsedNoncovid
                        ]
                    ]
                ];
            }
            return $data;
        }
        else{
            return 'Error API';
        }
    }

    public function apiGetReport(Request $request){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        if($request->date_range){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Activity::select("created_at")->orderBy("created_at","asc")->first()->created_at;
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        if($request->request_type=='incoming' || $request->request_type=='outgoing'){
            if($request->top){
                if($request->top == "denied"){
                    $top = "denied";
                    $request->top = "rejected";
                }
                else
                    $top = $request->top;

                return $this->dataResponse($request,$date_start,$date_end,$top);
            }
        }
        elseif($request->request_type=="bed"){
            $beds = $this->apiReportBedAvailability($request);
            $data = [];
            foreach($beds as $bed){

                $encoded_by = BedTracker::
                select("bed_tracker.id","users.fname","users.mname","users.lname","bed_tracker.created_at")
                    ->leftJoin("users","users.id","=","bed_tracker.encoded_by")
                    ->where("bed_tracker.facility_id","=",$bed->id)
                    ->where("users.level","!=","opcen")
                    ->orderBy("bed_tracker.id","desc")
                    ->first();
                $created_at = $encoded_by->created_at;
                $encoded_by = ucfirst($encoded_by->fname).' '.strtoupper($encoded_by->mname[0]).'. '.ucfirst($encoded_by->lname);

                $data[] = [
                    "province" => Province::find($bed->province)->description,
                    "facility_name" => $bed->facility_name,
                    "hospital_type" => $bed->hospital_type,
                    "encoded_by" => $encoded_by,
                    "created_at" => $created_at,
                    "data" => [
                        [
                            "UnusedCovid" => $bed->UnusedCovid,
                            "UsedCovid" => $bed->UsedCovid,
                            "UnusedNoncovid" => $bed->UnusedNoncovid,
                            "UsedNoncovid" => $bed->UsedNoncovid
                        ]
                    ]
                ];
            }
            return $data;
        }
        else{
            return 'Error API';
        }
    }

    public function dataResponse($request,$date_start,$date_end,$top){
        $response = $this->reportReferral($request,$date_start,$date_end);
        $data = [];
        foreach($response as $row){
            $data[] = [
                "province" => Province::find($request->province)->description ? Province::find($request->province)->description  : "ALL",
                "facility_name" => $row->facility_name,
                "request_type" => $request->request_type,
                "date_range" => date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)),
                "data" => [
                    $top => $row->count
                ]
            ];
        }
        return $data;
    }

    public function individualList(Request $request){
        if(!$request->facility_id)
            return "NO FACILITY FILTERED";
        elseif(!$request->request_type)
            return "NO REQUEST TYPE FILTERED";

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        if($request->status == 'denied')
            $request->status = 'rejected';

        if($request->date_range){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Activity::select("created_at")->orderBy("created_at","asc")->first()->created_at;
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        $data = \DB::connection('mysql')->select("call statistics_report_individual('$request->request_type','$request->facility_id','$date_start','$date_end','$request->status')");
        return $data;

        $data = Activity::select(
                "activity.code",
                DB::raw("concat(capitalize_name(pat.fname),' ',capitalize_name(pat.mname),'. ',capitalize_name(pat.lname)) as patient_name"),
                DB::raw("YEAR(CURRENT_TIMESTAMP) - YEAR(pat.dob) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(pat.dob, 5)) as age"),
                "fac_from.name as referring_facility",
                "fac_to.name as referred_facility",
                DB::raw("if(tra.type = 'normal',patient_f.diagnosis,pregnant_f.woman_major_findings) as diagnosis"),
                DB::raw("if(tra.type = 'normal',patient_f.reason,pregnant_f.woman_reason) as reason")
            )
            ->whereBetween("activity.date_referred",[$date_start,$date_end]);

        if($request->request_type == 'incoming')
            $data = $data->where("activity.referred_to",$request->facility_id);
        elseif($request->request_type == 'outgoing')
            $data = $data->where("activity.referred_from",$request->facility_id);
        else
            return 'ERROR API';

        $data = $data->leftJoin('tracking as tra','tra.code','=','activity.code')
                    ->leftJoin('patients as pat','pat.id','=','activity.patient_id')
                    ->leftJoin('patient_form as patient_f','patient_f.code','=','activity.code')
                    ->leftJoin('pregnant_form as pregnant_f','pregnant_f.code','=','activity.code')
                    ->leftJoin('facility as fac_from','fac_from.id','=','activity.referred_from')
                    ->leftJoin('facility as fac_to','fac_to.id','=','activity.referred_to')
        ;

        if($request->status == 'cancelled')
            $data = $data->groupBy("activity.code");

        if($request->status == 'seen_only'){
            $data = $data->join("seen as see","see.code","=","activity.code")
                        ->leftJoin("activity as act1",function($join){
                            $join->on("activity.code","=",'act1.code');
                            $join->on("activity.id",'<','act1.id');
                            $join->on('act1.referred_to','=','activity.referred_to');
                        })
                    ->where(function($query){
                        $query->where('activity.status','=','referred');
                        $query->orWhere('activity.status','=','redirected');
                        $query->orWhere('activity.status','=','transferred');
                    })
                    ->whereNull("act1.id")
                    ->groupBy('activity.code');
        }
        else
            $data = $data->where("activity.status",$request->status);

        $data = $data->get();

        return $data;
    }

    public function apiReport(Request $request,$date_start,$date_end){
        if($request->request_type == "incoming")
            return \DB::connection('mysql')->select("call statistics_report_incoming('$date_start','$date_end','$request->province')");

        return \DB::connection('mysql')->select("call statistics_report_outgoing('$date_start','$date_end','$request->province')");
    }

    public function apiBedAvailability(Request $request){
        $facility = Facility::
        select(
            "id",
            "province",
            "name as facility_name",
            "hospital_type",
            DB::raw("COALESCE(emergency_room_covid_vacant,0) + COALESCE(icu_covid_vacant,0) + COALESCE(beds_covid_vacant,0) + COALESCE(isolation_covid_vacant,0) AS UnusedCovid"),
            DB::raw("COALESCE(emergency_room_covid_occupied,0) + COALESCE(icu_covid_occupied,0) + COALESCE(beds_covid_occupied,0) + COALESCE(isolation_covid_occupied,0) AS UsedCovid"),
            DB::raw("COALESCE(emergency_room_non_vacant,0) + COALESCE(icu_non_vacant,0) + COALESCE(beds_non_vacant,0) + COALESCE(isolation_non_vacant,0) AS UnusedNoncovid"),
            DB::raw("COALESCE(emergency_room_non_occupied,0) + COALESCE(icu_non_occupied,0) + COALESCE(beds_non_occupied,0) + COALESCE(isolation_non_occupied,0) AS UsedNoncovid")
        )
        ->where(function($q){
            $q->where("hospital_type","government")->orWhere("hospital_type","private");
        })
            ->where("referral_used","yes");

        if($request->top == "most_bed")
            $facility->orderBy(DB::raw("COALESCE(emergency_room_covid_vacant,0) + COALESCE(icu_covid_vacant,0) + COALESCE(beds_covid_vacant,0) + COALESCE(isolation_covid_vacant,0) + COALESCE(emergency_room_covid_occupied,0) + COALESCE(icu_covid_occupied,0) + COALESCE(beds_covid_occupied,0) + COALESCE(isolation_covid_occupied,0)"),"desc")->limit(10);
        elseif($request->top == "least_bed"){
            $facility->orderBy(DB::raw("COALESCE(emergency_room_covid_vacant,0) + COALESCE(icu_covid_vacant,0) + COALESCE(beds_covid_vacant,0) + COALESCE(isolation_covid_vacant,0) + COALESCE(emergency_room_covid_occupied,0) + COALESCE(icu_covid_occupied,0) + COALESCE(beds_covid_occupied,0) + COALESCE(isolation_covid_occupied,0)"),"asc")->limit(10);
        }

        if($request->province)
            $facility = $facility->where("province",$request->province);

        $facility = $facility->get();

        return $facility;
    }

    public function apiReportBedAvailability(Request $request){
        $facility = Facility::
        select(
            "id",
            "province",
            "name as facility_name",
            "hospital_type",
            DB::raw("COALESCE(emergency_room_covid_vacant,0) + COALESCE(icu_covid_vacant,0) + COALESCE(beds_covid_vacant,0) + COALESCE(isolation_covid_vacant,0) AS UnusedCovid"),
            DB::raw("COALESCE(emergency_room_covid_occupied,0) + COALESCE(icu_covid_occupied,0) + COALESCE(beds_covid_occupied,0) + COALESCE(isolation_covid_occupied,0) AS UsedCovid"),
            DB::raw("COALESCE(emergency_room_non_vacant,0) + COALESCE(icu_non_vacant,0) + COALESCE(beds_non_vacant,0) + COALESCE(isolation_non_vacant,0) AS UnusedNoncovid"),
            DB::raw("COALESCE(emergency_room_non_occupied,0) + COALESCE(icu_non_occupied,0) + COALESCE(beds_non_occupied,0) + COALESCE(isolation_non_occupied,0) AS UsedNoncovid")
        )
            ->where(function($q){
                $q->where("hospital_type","government")->orWhere("hospital_type","private");
            })
            ->where("referral_used","yes");

        if($request->top == "most_bed")
            $facility->orderBy(DB::raw("COALESCE(emergency_room_covid_vacant,0) + COALESCE(icu_covid_vacant,0) + COALESCE(beds_covid_vacant,0) + COALESCE(isolation_covid_vacant,0) + COALESCE(emergency_room_covid_occupied,0) + COALESCE(icu_covid_occupied,0) + COALESCE(beds_covid_occupied,0) + COALESCE(isolation_covid_occupied,0)"),"desc");
        elseif($request->top == "least_bed"){
            $facility->orderBy(DB::raw("COALESCE(emergency_room_covid_vacant,0) + COALESCE(icu_covid_vacant,0) + COALESCE(beds_covid_vacant,0) + COALESCE(isolation_covid_vacant,0) + COALESCE(emergency_room_covid_occupied,0) + COALESCE(icu_covid_occupied,0) + COALESCE(beds_covid_occupied,0) + COALESCE(isolation_covid_occupied,0)"),"asc");
        }

        if($request->province)
            $facility = $facility->where("province",$request->province);

        $facility = $facility->get();

        return $facility;
    }

    public function getFacilities(Request $request){
        $facility = Facility::select("facility.id","facility.name as facility_name")->where("referral_used","yes")->orderBy("facility.name","asc");
        if($request->province)
            $facility = $facility->where("province",$request->province);

        $facility = $facility->get();
        return $facility;
    }

    public function topReferral($request,$date_start,$date_end){
        $data = \DB::connection('mysql')->select("call top_referral('$date_start','$date_end','$request->province','$request->top','$request->request_type')");
        return $data;
    }

    public function reportReferral($request,$date_start,$date_end){
        $data = \DB::connection('mysql')->select("call report_referral('$date_start','$date_end','$request->province','$request->top','$request->request_type')");
        return $data;
    }

    public function apiGetReferralList(Request $request){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        if($request->date_range){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Carbon::now()->startOfMonth()->format('Y-m-d').' 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        if(!$request->referring_facility && !$request->referred_facility)
            return "error API";

        $data = $data = \DB::connection('mysql')->select("call referral_list('$request->referring_facility','$request->referred_facility','$date_start','$date_end')");
        return $data;
    }

    public function apiGetReferralTrack(Request $request){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $data = $data = \DB::connection('mysql')->select("call referral_track('$request->code')");
        return $data;
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

    public static function fileUploadUrl(){
        return 'https://fileupload.user.edgecloudph.com/';
    }

    public static function fileUpload(Request $request) {
        $filePath = $_FILES['file_upload']['tmp_name'];
        $type=$_FILES['file_upload']['type'];
        $fileName = $_FILES['file_upload']['name'];

        $data = array(
            'file_upload' => curl_file_create($filePath, $type, $fileName),
            'username' => $request->username
        );
        $url = self::fileUploadUrl().'file_upload.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
        curl_close($ch);
    }

    public function apiReferPatient(Request $req)
    {

        if(!$province = Province::where("province_code","like","$req->province%")->first())
            return array(
                'code' => 227,
                'message' => 'Invalid Province Code. Province not found.'
            );

        if(!$muncity = Muncity::where("muncity_code",$req->muncity)->first())
            return array(
                'code' => 228,
                'message' => 'Invalid Municipality Code. Municipality not found.'
            );

        if(!$barangay = Barangay::where("barangay_code",$req->barangay)->first())
            return array(
                'code' => 229,
                'message' => 'Invalid Barangay Code. Barangay not found.'
            );

        if(!$referring_facility = Facility::where("facility_code",$req->referring_facility)->first())
            return array(
                'code' => 230,
                'message' => 'Invalid Referring Facility. Facility not found.'
            );

        if(!$referred_facility = Facility::where("facility_code",$req->referred_facility)->first())
            return array(
                'code' => 231,
                'message' => 'Invalid Referred Facility. Facility not found.'
            );


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
        $unique_id = "$patient->id-$referring_facility->id-".date('ymdHis');
        $user_code = str_pad($referring_facility->id,3,0,STR_PAD_LEFT);
        $code = date('ymd').'-'.$user_code.'-'.date('His');

        $data = array(
            'unique_id' => $unique_id,
            'code' => $code,
            'referring_facility' => $referring_facility->id,
            'referred_to' => $referred_facility->id,
            'department_id' => $req->department_id,
            'time_referred' => date('Y-m-d H:i:s'),
            'time_transferred' => '',
            'patient_id' => $patient->id,
            'case_summary' => $req->case_summary,
            'reco_summary' => $req->reco_summary,
            'diagnosis' => $req->diagnosis,
            'reason' => $req->reason,
            'referring_md' => $referring_doctor->id,
            'referred_md' => ($referred_doctor->id) ? $referred_doctor->id: 0,
            'covid_number' => $req->covid_number,
            'refer_clinical_status' => $req->clinical_status,
            'refer_sur_category' => $req->sur_category,
            'reason_referral' => $req->reason_referral1,
            'other_reason_referral' => $req->other_reason_referral,
            'other_diagnoses' => $req->other_diagnosis,
        );

        $form = PatientForm::create($data);

        if($_FILES["file_upload"]["name"]) {
            $req->username = $referring_doctor->username;
            $file = $_FILES['file_upload']['name'];

            self::fileUpload($req);
            $form->file_path = self::fileUploadUrl().$req->username."/".$file;
        }

        $form->save();
        foreach($req->icd_ids as $i) {
            if($icd10 = Icd10::where("code",$i)->first()) {
                $icd = new Icd();
                $icd->code = $form->code;
                $icd->icd_id = $icd10->id;
                $icd->save();
            }
        }
        $type = 'normal';
        $this->addTracking($code,$patient->id,$referring_doctor,$referred_doctor,$req,$type,$form->id,'refer');

        return array(
            'code' => http_response_code(),
            'message' => "Successfully referred patient!"
        );
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
