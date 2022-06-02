<?php

namespace App\Http\Controllers\doctor;

use App\Activity;
use App\Baby;
use App\Department;
use App\Events\NewReferral;
use App\Events\SocketReco;
use App\Facility;
use App\Feedback;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DeviceTokenCtrl;
use App\Http\Controllers\ParamCtrl;
use App\Icd;
use App\Issue;
use App\Monitoring;
use App\PatientForm;
use App\Patients;
use App\PregnantForm;
use App\ReasonForReferral;
use App\Seen;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class ReferralCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('doctor');
    }

    public function index(Request $request)
    {
        ParamCtrl::lastLogin();
        $keyword = '';
        $dept = '';
        $fac = '';
        $option = '';
        $start = Carbon::now()->startOfYear()->format('m/d/Y');
        $end = Carbon::now()->endOfDay()->format('m/d/Y');
        $user = Session::get('auth');

        $data = Tracking::select(
                    'tracking.*',
                    DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
                    DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS patient_age"),
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
                ->where('referred_to',$user->facility_id);

        if($request->search)
        {
            $keyword = $request->search;
            $data = $data->where(function($q) use ($keyword){
                $q->where('patients.lname',"$keyword")
                    ->orwhere('patients.fname',"$keyword")
                    ->orwhere('tracking.code',"$keyword");
            });
        }

        if($request->department_filter)
        {
            $dept = $request->department_filter;
            $data = $data->where('tracking.department_id',$dept);
        }

        if($request->facility_filter)
        {
            $fac = $request->facility_filter;
            $data = $data->where('tracking.referred_from',$fac);
        }

        if($request->date_range)
        {
            $date = $request->date_range;
            $range = explode('-',str_replace(' ', '', $date));
            $start = $range[0];
            $end = $range[1];
        }

        $start_date = Carbon::parse($start)->startOfDay();
        $end_date = Carbon::parse($end)->endOfDay();

        if($request->option_filter)
        {
            $option = $request->option_filter;
            if($option=='referred'){
                $data = $data->where(function($q){
                    $q->where('tracking.status','referred')
                        ->orwhere('tracking.status','seen');
                });
            }elseif($option=='accepted'){
                $data = $data->where(function($q){
                    $q->where('tracking.status','accepted');
                });
            }
        }else{
            $data = $data->where(function($q){
                $q->where('tracking.status','referred')
                    ->orwhere('tracking.status','seen')
                    ->orwhere('tracking.status','accepted')
                    ->orwhere('tracking.status','redirected')
                    ->orwhere('tracking.status','rejected')
                    ->orwhere('tracking.status','arrived')
                    ->orwhere('tracking.status','admitted')
                    ->orwhere('tracking.status','discharged')
                    ->orwhere('tracking.status','transferred')
                    ->orwhere('tracking.status','archived')
                    ->orwhere('tracking.status','cancelled');
            });
        }
        $data = $data->whereBetween('tracking.date_referred',[$start_date,$end_date]);

        $data = $data
                ->orderBy("tracking.date_referred","desc")
                ->paginate(15);

        return view('doctor.referral',[
            'title' => 'Incoming Patients',
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'keyword' => $keyword,
            'department' => $dept,
            'facility' => $fac,
            'option' => $option
        ]);
    }

    static function countReferral()
    {
        $user = Session::get('auth');
        $count = Tracking::where('referred_to',$user->facility_id)
            ->where(function($q){
                $q->where('status','referred')
                    ->orwhere('status','seen')
                    ->orWhere('status','transferred');
            })
            ->where(DB::raw("TIMESTAMPDIFF(MINUTE,date_referred,now())"),"<=",4320)
            ->count();
        return $count;
    }

    function seen($track_id) {

        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');
        Tracking::where('id',$track_id)
            ->update([
                'date_seen' => $date,
                'status' => 'seen'
            ]);
        $code = Tracking::find($track_id)->code;
        $activity = Activity::where('code',$code)
            ->where(function($q){
                $q->where('status','referred')
                    ->orwhere('status','redirected')
                    ->orwhere('status','transferred');
            })
            ->where('referred_to',$user->facility_id)
            ->orderBy('id','desc')
            ->first();
        $activity->update([
                'date_seen' => $date
            ]);
        return $activity->id;
    }

    public static function normalForm($id,$referral_status,$form_type) {
        $track = Tracking::select('code', 'status', 'referred_from as referring_fac_id')->where('id', $id)->first();
        $icd = Icd::select('icd10.code', 'icd10.description')
                    ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
                    ->where('icd.code',$track->code)->get();
        $path = (PatientForm::select('file_path')->where('code', $track->code)->first())->file_path;
        $file_name = basename($path);

        $reason = ReasonForReferral::select("reason_referral.reason","reason_referral.id")
                ->join('patient_form', 'patient_form.reason_referral', 'reason_referral.id')
                ->where('patient_form.code', $track->code)->first();

        $form = self::normalFormData($id);
        return view("doctor.referral_body_normal",[
            "form" => $form['form'],
            "id" => $id,
            "patient_age" => $form['age'],
            "age_type" => $form['ageType'],
            "reason" => $reason,
            "icd" => $icd,
            "file_path" => $path,
            "file_name" => $file_name,
            "referral_status" => $referral_status,
            "cur_status" => $track->status,
            "referring_fac_id" => $track->referring_fac_id,
            "form_type" => $form_type
        ]);
    }

    public static function normalFormData($id) {
        $form = PatientForm::select(
            DB::raw("'$id' as tracking_id"),
            'patient_form.code as code',
            DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
            'patients.dob as dob',
            'patients.sex as patient_sex',
            'patients.civil_status as patient_status',
            'patients.phic_status',
            'patients.phic_id',
            'patient_form.covid_number',
            'patient_form.refer_clinical_status',
            'patient_form.refer_sur_category',
            'patient_form.case_summary',
            'patient_form.reco_summary',
            'patient_form.other_reason_referral',
            'patient_form.diagnosis',
            'patient_form.other_diagnoses',
            DB::raw("if(
                patients.brgy,
                concat(patients.region,', ',province.description,', ',muncity.description,', ',barangay.description),
                concat(patients.region,', ',patients.province_others,', ',patients.muncity_others,', ',patients.brgy_others)
            ) as patient_address"),
            DB::raw("concat(p.description,', ',m.description,', ',b.description) as referring_address"),
            DB::raw("concat(pp.description,', ',mm.description,', ',pp.description) as referred_address"),
            'facility.name as referring_name',
            'ff.name as referred_name',
            'ff.id as referred_fac_id',
            DB::raw("DATE_FORMAT(patient_form.time_referred,'%M %d, %Y %h:%i %p') as time_referred"),
            DB::raw("DATE_FORMAT(patient_form.time_transferred,'%M %d, %Y %h:%i %p') as time_transferred"),
            DB::raw('CONCAT(
                        if(
                            users.level="doctor",
                            "Dr. ",
                            ""
                        )
                ,users.fname," ",users.mname," ",users.lname) as md_referring'),
            'users.id as md_referring_id',
            DB::raw('CONCAT("Dr. ",u.fname," ",u.mname," ",u.lname," / ",u.contact) as md_referred'),
            'u.id as md_referred_id',
            'facility.contact as referring_contact',
            'ff.contact as referred_contact',
            'users.contact as referring_md_contact',
            'department.description as department',
            'department.id as department_id',
            DB::raw("DATE_FORMAT(act.date_referred,'%M %e, %Y %r') as date_referred")
        )
            ->join('patients','patients.id','=','patient_form.patient_id')
            ->join('tracking','tracking.form_id','=','patient_form.id')
            ->join('facility','facility.id','=','tracking.referred_from')
            ->join('facility as ff','ff.id','=','tracking.referred_to')
            ->leftJoin('users','users.id','=','patient_form.referring_md')
            ->leftJoin('users as u','u.id','=','patient_form.referred_md')
            ->leftJoin('barangay','barangay.id','=','patients.brgy')
            ->leftJoin('muncity','muncity.id','=','patients.muncity')
            ->leftJoin('province','province.id','=','patients.province')
            ->leftJoin('barangay as b','b.id','=','facility.brgy')
            ->leftJoin('muncity as m','m.id','=','facility.muncity')
            ->leftJoin('province as p','p.id','=','facility.province')
            ->leftJoin('barangay as bb','bb.id','=','ff.brgy')
            ->leftJoin('muncity as mm','mm.id','=','ff.muncity')
            ->leftJoin('province as pp','pp.id','=','ff.province')
            ->leftJoin('department','department.id','=','patient_form.department_id')
            ->leftJoin('activity as act','act.code','=','tracking.code')
            ->where('tracking.id',$id)
            ->where(function($query) {
                $query->where('act.status', 'referred')
                    ->orWhere('act.status', 'redirected')
                    ->orWhere('act.status', 'transferred');
            })
            ->orderBy('act.date_referred','desc')
            ->first();
        $age =  ParamCtrl::getAge($form['dob']);
        $ageType = "y";
        if($age == 0){
            $age = ParamCtrl::getMonths($form['dob']);
            $ageType = "m";
        }
        return [
            "form" => $form,
            "age" => $age,
            "ageType" => $ageType
        ];
    }

    public static function pregnantForm($id,$referral_status) {
        $track = Tracking::select('code', 'status', 'referred_from as referring_fac_id')->where('id', $id)->first();
        $icd = Icd::select('icd10.code', 'icd10.description')
                    ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
                    ->where('icd.code',$track->code)->get();
        $path = (PregnantForm::select('file_path')->where('code', $track->code)->first())->file_path;
        $file_name = basename($path);

        $reason = ReasonForReferral::select("reason_referral.id", "reason_referral.reason")
                ->join('pregnant_form', 'pregnant_form.reason_referral', 'reason_referral.id')
                ->where('pregnant_form.code', $track->code)->first();

        return view('doctor.referral_body_pregnant',[
            "form" => self::pregnantFormData($id),
            "id" => $id,
            "reason" => $reason,
            "icd" => $icd,
            "file_path" => $path,
            "file_name" => $file_name,
            "referral_status" => $referral_status,
            "cur_status" => $track->status,
            "referring_fac_id" => $track->referring_fac_id,
            "form_type" => "pregnant"
        ]);
    }

    public static function pregnantFormData($id) {
        $form = PregnantForm::select(
            DB::raw("'$id' as tracking_id"),
            'pregnant_form.patient_baby_id',
            'pregnant_form.code',
            'pregnant_form.record_no',
            'pregnant_form.other_reason_referral',
            'pregnant_form.notes_diagnoses',
            'pregnant_form.other_diagnoses',
            DB::raw("DATE_FORMAT(pregnant_form.referred_date,'%M %d, %Y %h:%i %p') as referred_date"),
            DB::raw("DATE_FORMAT(pregnant_form.arrival_date,'%M %d, %Y %h:%i %p') as arrival_date"),
            DB::raw('CONCAT(
                if(users.level="doctor","Dr. ","")
            ,users.fname," ",users.mname," ",users.lname) as md_referring'),
            'users.id as md_referring_id',
            'facility.name as referring_facility',
            'b.description as facility_brgy',
            'm.description as facility_muncity',
            'p.description as facility_province',
            'ff.name as referred_facility',
            'ff.id as referred_facility_id',
            'bb.description as ff_brgy',
            'mm.description as ff_muncity',
            'pp.description as ff_province',
            'pregnant_form.health_worker',
            DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as woman_name'),
            DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS woman_age"),
            'patients.sex',
            DB::raw("if(
                patients.brgy,
                concat(patients.region,', ',province.description,', ',muncity.description,', ',barangay.description),
                concat(patients.region,', ',patients.province_others,', ',patients.muncity_others,', ',patients.brgy_others)
            ) as patient_address"),
            'pregnant_form.woman_reason',
            'pregnant_form.woman_major_findings',
            'pregnant_form.woman_before_treatment',
            DB::raw("DATE_FORMAT(pregnant_form.woman_before_given_time,'%M %d, %Y %h:%i %p') as woman_before_given_time"),
            'pregnant_form.woman_during_transport',
            DB::raw("DATE_FORMAT(pregnant_form.woman_transport_given_time,'%M %d, %Y %h:%i %p') as woman_transport_given_time"),
            'pregnant_form.woman_information_given',
            'facility.contact as referring_contact',
            'ff.contact as referred_contact',
            'users.contact as referring_md_contact',
            'department.description as department',
            'department.id as department_id',
            'pregnant_form.covid_number',
            'pregnant_form.refer_clinical_status',
            'pregnant_form.refer_sur_category',
            'pregnant_form.dis_clinical_status',
            'pregnant_form.dis_sur_category',
            'patients.id as mother_id'

        )
        ->leftJoin('patients','patients.id','=','pregnant_form.patient_woman_id')
        ->leftJoin('tracking','tracking.form_id','=','pregnant_form.id')
        ->leftJoin('facility','facility.id','=','tracking.referred_from')
        ->leftJoin('facility as ff','ff.id','=','tracking.referred_to')
        ->leftJoin('users','users.id','=','pregnant_form.referred_by')
        ->leftJoin('barangay','barangay.id','=','patients.brgy')
        ->leftJoin('muncity','muncity.id','=','patients.muncity')
        ->leftJoin('province','province.id','=','patients.province')
        ->leftJoin('barangay as b','b.id','=','facility.brgy')
        ->leftJoin('muncity as m','m.id','=','facility.muncity')
        ->leftJoin('province as p','p.id','=','facility.province')
        ->leftJoin('barangay as bb','bb.id','=','ff.brgy')
        ->leftJoin('muncity as mm','mm.id','=','ff.muncity')
        ->leftJoin('province as pp','pp.id','=','ff.province')
        ->leftJoin('department','department.id','=','pregnant_form.department_id')
        ->where('tracking.id',$id)
        ->first();

        $baby = array();
        if(isset($form->patient_baby_id) && $form->patient_baby_id > 0)
        {
            $baby = PregnantForm::select(
                        DB::raw('CONCAT(baby.fname," ",baby.mname," ",baby.lname) as baby_name'),
                        'pregnant_form.patient_baby_id as baby_id',
                        'baby.fname as baby_fname',
                        'baby.mname as baby_mname',
                        'baby.lname as baby_lname',
                        DB::raw("DATE_FORMAT(bb.birth_date,'%M %d, %Y %h:%i %p') as baby_dob"),
                        'bb.weight',
                        'bb.gestational_age',
                        'pregnant_form.baby_reason',
                        'pregnant_form.baby_major_findings',
                        DB::raw("DATE_FORMAT(pregnant_form.baby_last_feed,'%M %d, %Y %h:%i %p') as baby_last_feed"),
                        'pregnant_form.baby_before_treatment',
                        DB::raw("DATE_FORMAT(pregnant_form.baby_before_given_time,'%M %d, %Y %h:%i %p') as baby_before_given_time"),
                        'pregnant_form.baby_during_transport',
                        DB::raw("DATE_FORMAT(pregnant_form.baby_transport_given_time,'%M %d, %Y %h:%i %p') as baby_transport_given_time"),
                        'pregnant_form.baby_information_given'
                    )
                    ->join('baby as bb','bb.baby_id','=','pregnant_form.patient_baby_id')
                    ->join('patients as baby','baby.id','=','pregnant_form.patient_baby_id')
                    ->join('tracking','tracking.form_id','=','pregnant_form.id')
                    ->where('tracking.id',$id)
                    ->first();

            if($baby['baby_dob'] === null) {
                $dob_ = Patients::select(DB::raw("DATE_FORMAT(patients.dob,'%M %d, %Y %h:%i %p') as baby_dob"))
                    ->join('pregnant_form as pregnant','pregnant.patient_baby_id','=','patients.id')
                    ->join('tracking','tracking.code','=','pregnant.code')
                    ->where('tracking.id',$id)
                    ->first();
                $baby['baby_dob'] = $dob_->baby_dob;
            }
        }

        return array(
            'pregnant' => $form,
            'baby' => $baby
        );
    }

    public function referred2()
    {
        ParamCtrl::lastLogin();
        $user = Session::get('auth');
        $data = Tracking::select(
            'tracking.*',
            DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
            DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            DB::raw('CONCAT(users.fname," ",users.mname," ",users.lname) as referring_md'),
            'patients.sex',
            'facility.name as facility_name',
            'facility.id as facility_id',
            'patients.id as patient_id'
        )
            ->join('patients','patients.id','=','tracking.patient_id')
            ->join('facility','facility.id','=','tracking.referred_to')
            ->leftJoin('users','users.id','=','tracking.referring_md')
            ->where('referred_from',$user->facility_id)
            ->where(function($q){
                $q->where('tracking.status','referred')
                    ->orwhere('tracking.status','seen')
                    ->orwhere('tracking.status','seen')
                    ->orwhere('tracking.status','accepted')
                    ->orwhere('tracking.status','transferred')
                    ->orwhere('tracking.status','discharged')
                    ->orwhere('tracking.status','cancelled')
                    ->orwhere('tracking.status','rejected');
            })
            ->orderBy('date_referred','desc')
            ->paginate(15);

        return view('doctor.referred',[
            'title' => 'Referred Patients',
            'data' => $data
        ]);
    }

    public function referred(Request $request)
    {
        $user = Session::get('auth');
        ParamCtrl::lastLogin();
        $search = $request->search;
        $option_filter = $request->option_filter;
        $date = $request->date_range;
        $facility_filter = $request->facility_filter;
        $department_filter = $request->department_filter;

        $start = Carbon::now()->startOfYear()->format('m/d/Y');
        $end = Carbon::now()->endOfDay()->format('m/d/Y');

        if($request->referredCode){
            ParamCtrl::lastLogin();
            $data = Tracking::select(
                'tracking.*',
                DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
                DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
                DB::raw('CONCAT(users.fname," ",users.mname," ",users.lname) as referring_md'),
                'patients.sex',
                'facility.name as facility_name',
                'facility.id as facility_id',
                'patients.id as patient_id',
                'patients.contact',
                'users.level as user_level'
            )
                ->join('patients','patients.id','=','tracking.patient_id')
                ->join('facility','facility.id','=','tracking.referred_to')
                ->leftJoin('users','users.id','=','tracking.referring_md')
                ->where('tracking.code',$request->referredCode)
                ->orderBy('date_referred','desc')
                ->paginate(10);

        } else {
            /*$data = Tracking::select(
                'tracking.*',
                DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
                DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
                DB::raw('COALESCE(CONCAT(users.fname," ",users.mname," ",users.lname),"WALK IN") as referring_md'),
                'patients.sex',
                'facility.name as facility_name',
                'facility.id as facility_id',
                'patients.id as patient_id',
                'patients.contact',
                'users.level as user_level'
            )
                ->join('patients','patients.id','=','tracking.patient_id')
                ->join('facility','facility.id','=','tracking.referred_to')
                ->leftJoin('users','users.id','=','tracking.referring_md')
                ->where('referred_from',$user->facility_id)
                ->where(function($q){
                    $q->where('tracking.status','referred')
                        ->orwhere('tracking.status','seen')
                        ->orwhere('tracking.status','accepted')
                        ->orwhere('tracking.status','arrived')
                        ->orwhere('tracking.status','admitted')
                        ->orwhere('tracking.status','transferred')
                        ->orwhere('tracking.status','discharged')
                        ->orwhere('tracking.status','cancelled')
                        ->orwhere('tracking.status','archived')
                        ->orwhere('tracking.status','rejected')
                        ->orWhere('tracking.status','redirected');
                });

            if($search){
                $data = $data->where(function($q) use ($search){
                    $q->where('patients.fname','like',"%$search%")
                        ->orwhere('patients.mname','like',"%$search%")
                        ->orwhere('patients.lname','like',"%$search%")
                        ->orwhere('tracking.code','like',"%$search%");
                });
            }

            if($option_filter)
                $data = $data->where('tracking.status',$option_filter);
            if($facility_filter)
                $data = $data->where('tracking.referred_to',$facility_filter);
            if($department_filter)
                $data = $data->where('tracking.department_id',$department_filter);

            if($date) {
                $range = explode('-',str_replace(' ', '', $date));
                $start = $range[0];
                $end = $range[1];
            }

            $start_date = Carbon::parse($start)->startOfDay();
            $end_date = Carbon::parse($end)->endOfDay();

            $data = $data->whereBetween('tracking.date_referred',[$start_date,$end_date])
                ->orderBy('date_referred','desc')
                ->paginate(10);*/

            $data = Activity::select(
                'tracking.*',
                DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
                DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
                DB::raw('COALESCE(CONCAT(users.fname," ",users.mname," ",users.lname),"WALK IN") as referring_md'),
                'patients.sex',
                'facility.name as facility_name',
                'facility.id as facility_id',
                'patients.id as patient_id',
                'patients.contact',
                'users.level as user_level',
                'activity.referring_md as wew'
            )
                ->leftJoin('patients','patients.id','=','activity.patient_id')
                ->leftJoin('facility','facility.id','=','activity.referred_to')
                ->leftJoin('tracking','tracking.code','=','activity.code')
                ->leftJoin('users','users.id','=',DB::raw("if(activity.referring_md,activity.referring_md,activity.action_md)"))
                ->where('activity.referred_from',$user->facility_id);

            if($search){
                $data = $data->where(function($q) use ($search){
                    $q->where('patients.fname','like',"%$search%")
                        ->orwhere('patients.mname','like',"%$search%")
                        ->orwhere('patients.lname','like',"%$search%")
                        ->orwhere('activity.code','like',"%$search%");
                });
            }

            if($option_filter)
                $data = $data->where('activity.status',$option_filter);
            if($facility_filter)
                $data = $data->where('activity.referred_to',$facility_filter);
            if($department_filter)
                $data = $data->where('activity.department_id',$department_filter);

            if($date) {
                $range = explode('-',str_replace(' ', '', $date));
                $start = $range[0];
                $end = $range[1];
            }

            $start_date = Carbon::parse($start)->startOfDay();
            $end_date = Carbon::parse($end)->endOfDay();

            $data = $data->whereBetween('activity.created_at',[$start_date,$end_date])
                ->where(function($q){
                    $q->where('tracking.status','referred')
                        ->orwhere('tracking.status','redirected')
                        ->orwhere('tracking.status','transferred');
                })
                ->orderBy('activity.id','desc')
                ->groupBy("activity.code")
                ->paginate(10);
        }

        return view('doctor.referred2',[
            'title' => 'Referred Patients',
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'referredCode' => $request->referredCode,
            'search' => $search,
            'option_filter' => $option_filter,
            'facility_filter' => $facility_filter,
            'department_filter' => $department_filter,
            'user' => $user
        ]);
    }

    public function trackReferral(Request $request)
    {
        $code = $request->referredCode;

        ParamCtrl::lastLogin();
        $data = Tracking::select(
            'tracking.*',
            DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
            DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            DB::raw('CONCAT(users.fname," ",users.mname," ",users.lname) as referring_md'),
            'patients.sex',
            'facility.name as facility_name',
            'facility.id as facility_id',
            'patients.id as patient_id',
            'patients.contact'
        )
            ->join('patients','patients.id','=','tracking.patient_id')
            ->join('facility','facility.id','=','tracking.referred_to')
            ->leftJoin('users','users.id','=','tracking.referring_md')
            ->where('tracking.code',$code)
            ->orderBy('date_referred','desc')
            ->paginate(10);

        return view('doctor.tracking',[
            'title' => 'Track Patients',
            'data' => $data,
            'code' => $code
        ]);
    }

    public function searchTrackReferral(Request $req)
    {
        Session::put('referredCode',$req->keyword);
        return redirect('doctor/referred');
    }

    static function step_v2($status){
        $step = 0;
        if($status == 'referred')
            $step = 1;
        elseif($status == 'seen')
            $step = 2;
        elseif($status == 'accepted')
            $step = 3;
        elseif($status == 'arrived')
            $step = 4;
        elseif($status == 'admitted')
            $step = 5;
        elseif($status == 'discharged')
            $step = 6;
        elseif($status == 'transferred')
            $step = 6;
        elseif($status == 'cancelled')
            $step = 0;
        elseif($status == 'archived')
            $step = 4.5;

        return $step;
    }

    static function step($code)
    {
        $step = 0;
        $seen = Tracking::where('code',$code)
                    ->where('date_seen','<>','')
                    ->first();
        if(self::hasStatus('referred',$code))
            $step = 1;
        if($seen)
            $step = 2;
        if(self::hasStatus('accepted',$code))
            $step = 3;
        if(self::hasStatus('arrived',$code))
            $step = 4;
        if(self::hasStatus('admitted',$code))
            $step = 5;
        if(self::hasStatus('discharged',$code))
            $step = 6;
        if(self::hasStatus('cancelled',$code))
            $step = 0;
        if(self::hasStatus('archived',$code))
            $step = 4.5;

        return $step;
    }

    static function hasStatus($status,$code)
    {
        $check = Activity::where('code',$code)
                ->where('status',$status)
                ->first();
        if($check)
            return true;
        return false;
    }

    public function reject(Request $req, $track_id)
    {
        $user = Session::get('auth');
        $track = Tracking::find($track_id);

        if($track->status=='accepted' || $track->status=='rejected')
            return 'denied';

        Tracking::where('id',$track_id)
            ->update([
                'status' => 'rejected',
                'action_md' => $user->id
            ]);
        $track = Tracking::find($track_id);
        $act = Activity::where('code',$track->code)
            ->orderBy('id','desc')
            ->first();
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => date('Y-m-d H:i:s'),
            'referred_from' => $act->referred_from,
            'referred_to' => $user->facility_id,
            'action_md' => $user->id,
            'remarks' => $req->remarks,
            'status' => $track->status
        );
        $act = Activity::create($data);

        return $act->id;
    }

    public function accept(Request $req,$track_id)
    {
        $user = Session::get('auth');

        $track = Tracking::find($track_id);
        if($track->status=='accepted' || $track->status=='rejected') {
            Session::put('incoming_denied',true);
            return 'denied';
        } // trap if already accepted or rejected

        Tracking::where('id',$track_id)
            ->update([
                'status' => 'accepted',
                'action_md' => $user->id,
                'date_accepted' => date('Y-m-d H:i:s')
            ]);
        $track = Tracking::find($track_id);

        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => date('Y-m-d H:i:s'),
            'referred_from' => $track->referred_from,
            'referred_to' => $user->facility_id,
            'department_id' => $track->department_id,
            'referring_md' => $track->referring_md,
            'action_md' => $user->id,
            'remarks' => isset($req->remarks) ? $req->remarks : "",
            'status' => $track->status
        );

        Activity::create($data);

        /*$doc = User::find($user->id);
        $name = ucwords(mb_strtolower($doc->fname))." ".ucwords(mb_strtolower($doc->lname));
        $hosp = Facility::find($user->facility_id)->name;
        $msg = "Referral code $track->code was accepted by Dr. $name of $hosp.";
        DeviceTokenCtrl::send('Referral Accepted',$msg,$track->referred_from);*/

        return $track_id;
    }

    public function call($activity_id)
    {
        $user = Session::get('auth');

        $md_name = "Dr. $user->fname $user->mname $user->lname";
        $activity = Activity::find($activity_id);
        $action_md = User::find($activity->action_md);
        $action_md = "Dr. $action_md->fname $action_md->mname $action_md->lname";
        $facility_name = Facility::find($activity->referred_from)->name;

        $remarks = "$md_name called $facility_name";
        Activity::where('id',$activity_id)
            ->update([
                'remarks' => $remarks
            ]);
    }

    public function calling($track_id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');
        $track = Tracking::find($track_id);
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $track->referred_from,
            'referred_to' => $track->referred_to,
            'action_md' => $user->id,
            'remarks' => 'N/A',
            'status' => 'calling'
        );
        $activity = Activity::create($data);

        $doc = User::find($user->id);
        $name = ucwords(mb_strtolower($doc->fname))." ".ucwords(mb_strtolower($doc->lname));
        $hosp = Facility::find($track->referred_to)->name;
        $msg = "Dr. $name of $hosp is requesting a call regarding on $track->code. Please contact this number $doc->contact";
        //DeviceTokenCtrl::send('Requesting a Call',$msg,$track->referred_from);

        return array(
            'date' => date('M d, Y h:i A',strtotime($date)),
            'activity_id' => $activity->id
        );
    }

    public function arrive(Request $req, $track_id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');
        $track = Tracking::find($track_id);
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $track->referred_to,
            'referred_to' => 0,
            'action_md' => $user->id,
            'remarks' => $req->remarks,
            'status' => 'arrived'
        );
        Activity::create($data);

        Tracking::where("id",$track_id)
                ->update([
                    'date_arrived' => $date,
                    'status' => 'arrived'
                ]);
        PregnantForm::where('id',$track->form_id)
                ->update([
                    'arrival_date' => $date
                ]);

        $hosp = Facility::find($user->facility_id)->name;
        $msg = "$track->code arrived at $hosp.";
        DeviceTokenCtrl::send('Arrived',$msg,$track->referred_from);

        return date('M d, Y h:i A',strtotime($date));
    }

    public function archive(Request $req, $track_id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');
        $track = Tracking::find($track_id);
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $track->referred_to,
            'referred_to' => 0,
            'action_md' => $user->id,
            'remarks' => $req->remarks,
            'status' => 'archived'
        );
        Activity::create($data);

        Tracking::where("id",$track_id)
                ->update([
                    'date_arrived' => $date,
                    'status' => 'archived'
                ]);
        PregnantForm::where('id',$track->form_id)
                ->update([
                    'arrival_date' => $date
                ]);

        return date('M d, Y h:i A',strtotime($date));
    }

    public function admit(Request $req, $track_id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s',strtotime($req->date_time));
        $track = Tracking::find($track_id);
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $track->referred_to,
            'referred_to' => 0,
            'action_md' => $user->id,
            'remarks' => 'admitted',
            'status' => 'admitted'
        );
        Activity::create($data);

        Tracking::where('id',$track_id)
            ->update([
                'status' => 'admitted'
            ]);

        $hosp = Facility::find($user->facility_id)->name;
        $msg = "$track->code admitted at $hosp.";
        DeviceTokenCtrl::send('Admitted',$msg,$track->referred_from);

        return date('M d, Y h:i A',strtotime($date));
    }

    public function discharge(Request $req, $track_id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s',strtotime($req->date_time));
        $track = Tracking::find($track_id);
        $track->update([
            'status' => 'discharged'
        ]);

        $patient_form = PatientForm::where('code',$track->code)->first();
        if($patient_form){
            $patient_form->update([
                'dis_clinical_status' => $req->clinical_status,
                'dis_sur_category' => $req->sur_category
            ]);
        }

        $pregnant_form = PregnantForm::where('code',$track->code)->first();
        if($pregnant_form){
            $pregnant_form->update([
                'dis_clinical_status' => $req->clinical_status,
                'dis_sur_category' => $req->sur_category
            ]);
        }

        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $track->referred_to,
            'referred_to' => 0,
            'action_md' => $user->id,
            'remarks' => $req->remarks,
            'status' => 'discharged'
        );
        Activity::create($data);

//        foreach($req->icd_ids as $i) { /* FOR WHEN ICD IS TRANSFERRED TO "DISCHARGE" */
//            $icd = new Icd();
//            $icd->code = $track->code;
//            $icd->icd_id = $i;
//            $icd->save();
//        }

        $hosp = Facility::find($user->facility_id)->name;
        $msg = "$track->code discharged from $hosp.";
        DeviceTokenCtrl::send('Discharged',$msg,$track->referred_from);
        return date('M d, Y h:i A',strtotime($date));
    }

    public function transfer(Request $req, $track_id) {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');

        $track = Tracking::where('id',$track_id)->first();
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $track->referred_to,
            'referred_to' => $req->facility,
            'department_id' => $req->department,
            'action_md' => $user->id,
            'remarks' => $req->remarks,
            'status' => "transferred"
        );
        $activity = Activity::create($data);

        $new_data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'date_arrived' => '',
            'date_seen' => '',
            'department_id' => $req->department,
            'referred_from' => $track->referred_to,
            'referred_to' => $req->facility,
            'remarks' => $track->remarks,
            'referring_md' => $user->id,
            'status' => 'transferred',
            'type' => $track->type,
            'form_id' => $track->form_id
        );
        $track->update($new_data);


        $patient = Patients::select(
                        DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
                        'patients.sex'
                    )
                    ->where('id',$track->patient_id)
                    ->first();
        $user_md = User::find($user->id);


        $form_type = '#normalFormModal';
        if($track->type=='pregnant'){
            $form_type = '#pregnantFormModal';
        }

        /*$hosp = Facility::find($user->facility_id)->name;
        $hospTo = Facility::find($req->facility)->name;

        $msg = "$track->code transferred to $hospTo from $hosp.";
        DeviceTokenCtrl::send('Transferred',$msg,$track->referred_from);*/

        return array(
            'date' => date('M d, Y h:i A',strtotime($date)),
            'age' => $patient->age,
            'sex' => $patient->sex,
            'action_md' => "$user_md->fname $user_md->mname $user_md->lname",
            'form_type' => $form_type,
            'track_id' => $track->id,
            'activity_id' => $activity->id,
            'referred_facility' => $track->referred_to
        );
    }

    public function redirect(Request $req)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');

        $track = Tracking::where("code",$req->code)->first();
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $user->facility_id,
            'referred_to' => $req->facility,
            'referring_md' => $user->id,
            'remarks' => '',
            'status' => 'redirected'
        );

        Activity::create($data);
        $track->update([
            'date_referred' => $date,
            'department_id' => $req->department,
            'date_arrived' => '',
            'date_seen' => '',
            'referred_from' => $user->facility_id,
            'referred_to' => $req->facility,
            'remarks' => '',
            'referring_md' => $user->id,
            'status' => 'redirected'
        ]);

        $patient = Patients::find($track->patient_id);
        $count_activity = Activity::where("code",$req->code)
            ->where(function($query){
                $query->where("status","referred")
                    ->orWhere("status","redirected")
                    ->orWhere("status","transferred");
            })
            ->groupBy("code")
            ->count();
        $tracking = Tracking::where("code",$req->code)->first();
        $count_seen = Seen::where('tracking_id',$tracking->id)->count();
        $count_reco = Feedback::where("code",$req->code)->count();
        $new_referral = [
            "patient_name" => ucfirst($patient->fname).' '.ucfirst($patient->lname),
            "referring_md" => ucfirst($user->fname).' '.ucfirst($user->lname),
            "referring_name" => Facility::find($user->facility_id)->name,
            "referred_name" => Facility::find($req->facility)->name,
            "referred_to" => (int)$req->facility,
            "referred_department" => Department::find($req->department)->description,
            "referred_from" => $user->facility_id,
            "form_type" => $track->type,
            "tracking_id" => $track->id,
            "referred_date" => date('M d, Y h:i A'),
            "patient_sex" => $patient->sex,
            "age" => ParamCtrl::getAge($patient->dob),
            "patient_code" => $req->code,
            "status" => "redirected",
            "count_activity" => $count_activity,
            "count_seen" => $count_seen,
            "count_reco" => $count_reco
        ];
        broadcast(new NewReferral($new_referral)); //websockets notification for new referral
        return Redirect::back();

        /*$patient = Patients::select(
            DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            'patients.sex',
            DB::raw('CONCAT(fname," ",mname," ",lname) as patient_name')
        )
            ->where('id',$track->patient_id)
            ->first();

        $form_type = '#normalFormModal';
        if($track->type=='pregnant')
            $form_type = '#pregnantFormModal';

        if($user->level == 'doctor')
            $referring_md = "Dr. ".$user->fname.' '.$user->lname;
        else
            $referring_md = $user->fname.' '.$user->lname;

        return array (
            'code' => $track->code,
            'date' => date('M d, Y h:i A',strtotime($date)),
            'patient_name' => $patient->patient_name,
            'age' => $patient->age,
            'sex' => $patient->sex,
            'form_type' => $form_type,
            'track_id' => $track->id,
            'activity_id' => $activity->id,
            'referred_from' => $user->facility_id,
            'referring_md' => $referring_md,
            'referring_facility' => Facility::find($user->facility_id)->name,
            'department_id' => $req->department,
            'department_name' => Department::find($req->department)->description
        );*/
    }

    public function seenBy($track_id,$code)
    {
        $user = Session::get('auth');
        $data = array(
            'tracking_id' => $track_id,
            'code' => $code,
            'facility_id' => $user->facility_id,
            'user_md' => $user->id
        );
        Tracking::find($track_id)->update([
           "date_seen" => date("Y-m-d H:i:s")
        ]);

        Seen::create($data);
    }

    public function seenByList($track_id)
    {
        $data = Seen::select(
                    DB::raw('CONCAT(users.fname," ",users.mname," ",users.lname) as user_md'),
                    DB::raw("DATE_FORMAT(seen.created_at,'%M %d, %Y %h:%i %p') as date_seen"),
                    'users.contact'
                )
                ->join('users','users.id','=','seen.user_md')
                ->where('seen.tracking_id',$track_id)
                ->orderBy('seen.created_at','desc')
                ->get();
        return $data;
    }

    public function callerByList($track_id)
    {
        $data = \App\Tracking::select(
                    \DB::raw("concat('Dr. ',users.fname,' ',users.mname,' ',users.lname) as user_md"),
                    DB::raw("DATE_FORMAT(activity.created_at,'%M %d, %Y %h:%i %p') as date_call"),
                    "users.contact"
            )
            ->Join("activity","activity.code","=","tracking.code")
            ->Join("users","users.id","=","activity.action_md")
            ->where('tracking.id',$track_id)
            ->where("activity.status","=","calling")
            ->get();

        return $data;
    }

    static function checkForCancellation($code)
    {
        $check = Activity::where('code',$code)
                ->where(function($q) {
                    $q->where('status','arrived')
                        ->orwhere('status','admitted')
                        ->orwhere('status','discharged')
                        ->orwhere('status','cancelled')
                        ->orwhere('status','archived')
                        ->orwhere('status','transferred');
                })
                ->first();
        if($check)
            return true;
        return false;
    }

    public function cancelReferral(Request $req, $id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');
        $track = Tracking::find($id);
        $activity = Activity::where("code",$track->code)->where(function($q){
            $q->where("status","referred")
              ->orWhere("status","redirected")
              ->orWhere("status","transferred");
        })
        ->orderBy("id","desc")
        ->first();
        $data = array(
            'code' => $activity->code,
            'patient_id' => $activity->patient_id,
            'date_referred' => $date,
            'referred_from' => $activity->referred_from,
            'referred_to' => $activity->referred_to,
            'action_md' => $user->id,
            'remarks' => $req->reason,
            'status' => 'cancelled'
        );
        Activity::create($data);

        Tracking::where('id',$id)
            ->update([
                'status' => 'cancelled'
            ]);

        return redirect()->back();
    }

    public function transferReferral(Request $req, $tracking_id){
        $mode_transportation = $req->mode_transportation;
        $other_transportation = $req->other_transportation;
        if($mode_transportation == "5"){
            $mode_transportation .= "-".$other_transportation;
        }

        $user = Session::get('auth');

        $track = Tracking::find($tracking_id);
        if($track->status=='travel')
        {
            return 'denied';
        }

        $track->update([
            "date_transferred" => date('Y-m-d H:i:s'),
            "mode_transportation" => $mode_transportation
        ]);

        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => date('Y-m-d H:i:s'),
            'referred_from' => $track->referred_from,
            'referred_to' => $user->facility_id,
            'department_id' => $track->department_id,
            'referring_md' => $track->referring_md,
            'action_md' => $user->id,
            'remarks' => $mode_transportation,
            'status' => 'travel'
        );

        Activity::create($data);

        return redirect()->back()->with('transferReferral','Successfully Transfer!');
    }

    public function feedback($code){
        $data = Feedback::select(
                    'feedback.id as id',
                    'feedback.sender as sender',
                    'feedback.message',
                    'users.fname as fname',
                    'users.lname as lname',
                    'facility.name as facility',
                    'facility.abbr as abbr',
                    'feedback.created_at as date'
                )
                ->leftJoin('users','users.id','=','feedback.sender')
                ->leftJoin('facility','facility.id','=','users.facility_id')
                ->where('code',$code)
                ->orderBy("id","asc")
                ->get();

        return view('doctor.feedback',[
            'data' => $data
        ]);
    }

    public function loadFeedback($code)
    {
        $id = Session::get('last_scroll_id');
        $data = Feedback::select(
            'feedback.id as id',
            'feedback.sender as sender',
            'feedback.message',
            'users.fname as fname',
            'users.lname as lname',
            'facility.name as facility',
            'facility.abbr as abbr',
            'feedback.created_at as date'
        )
            ->leftJoin('users','users.id','=','feedback.sender')
            ->leftJoin('facility','facility.id','=','users.facility_id')
            ->where('code',$code)
            ->where('feedback.id','<',$id)
            ->latest('feedback.id')
            ->take(5)
            ->get();

        if(count($data)==0)
            return 0;

        return view('doctor.feedback',[
            'data' => $data->reverse()
        ]);
    }

    public function replyFeedback($id)
    {
        $user = Session::get('auth');
        $position = '';
        $icon = 'receiver.png';
        $pull = 'right';

        $data = Feedback::select(
            'feedback.sender as sender',
            'feedback.message',
            'users.fname as fname',
            'users.lname as lname',
            'facility.name as facility',
            'facility.abbr as abbr',
            'feedback.created_at as date'
        )
            ->leftJoin('users','users.id','=','feedback.sender')
            ->leftJoin('facility','facility.id','=','users.facility_id')
            ->where('feedback.id',$id)
            ->first();

        if($user->id==$data->sender){
            $position = 'right';
            $icon = 'sender.png';
            $pull = 'left';
        }

        $fullname = ucwords(mb_strtolower($data->fname))." ".ucwords(mb_strtolower($data->lname));
        $picture = url('resources/img/'.$icon);

        $content = '
            <div class="direct-chat-msg '.$position.'">
                    <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-'.$position.'">'.$fullname.'</span>
                    <span class="direct-chat-timestamp pull-'.$pull.'">'.date('d M h:i a',strtotime($data->date)).'</span>
                    </div>

                    <img class="direct-chat-img" title="'.$data->facility.'" src="'.$picture.'" alt="'.$data->facility.'">
                    <div class="direct-chat-text">
                        '.$data->message.'
                    </div>

                </div>
        ';

        return $content;
    }

    public function saveFeedback(Request $req)
    {
        $user = Session::get('auth');
        $data = array(
            'code'=> $req->code,
            'sender'=> $user->id,
            'receiver'=> 0,
            'message'=> $req->message,
        );

        Feedback::create($data);

        //reco websocket
        $reco_json = ParamCtrl::feedbackContent($req->code,$user->id,$req->message);
        broadcast(new SocketReco($reco_json));
        //end reco websocket

        $doc = User::find($user->id);
        $name = ucwords(mb_strtolower($doc->fname))." ".ucwords(mb_strtolower($doc->lname));
        return view('doctor.feedback_append',[
            "name" => $name,
            "facility" => Facility::find($user->facility_id)->name,
            "message" => $req->message
        ]);
    }

    public function receiverFeedback($user_id,$msg)
    {
        $user = User::find($user_id);
        $name = ucwords(mb_strtolower($user->fname))." ".ucwords(mb_strtolower($user->lname));
        return view('doctor.feedback_append',[
            "name" => $name,
            "message" => $msg
        ]);
    }

    public function notificationFeedback($code,$user_id)
    {
        $user = Session::get('auth');
        $facility_id = $user->facility_id;
        $dr = User::find($user_id);
        $check = Tracking::where(function($q) use ($facility_id){
                    $q->where('referred_from',$facility_id)
                        ->orwhere('referred_to',$facility_id);
                })
                ->where('code',$code)
                ->first();
        if($check){
            $data['check'] = 1;
            $data['info'] = array(
                'fname' => ucwords(mb_strtolower($dr->fname)),
                'lname' => ucwords(mb_strtolower($dr->lname))
            );
            return $data;
        }
        return 0;
    }

    public static function editInfo($id,$form_type,$referral_status)
    {
        $track = Tracking::select('code')->where('id', $id)->first();
        $icd = Icd::select('icd10.code', 'icd10.description', 'icd.icd_id as id')
            ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
            ->where('icd.code',$track->code)->get();

        if($form_type == 'normal') {
            $path = (PatientForm::select('file_path')->where('code', $track->code)->first())->file_path;
            $file_name = basename($path);
            $reason = ReasonForReferral::select('patient_form.reason_referral as id', 'reason_referral.reason as reason')
                ->join('patient_form', 'patient_form.reason_referral', 'reason_referral.id')
                ->where('patient_form.code', $track->code)->first();
            $form = self::normalFormData($id);
            return view("doctor.edit_referral_normal", [
                "form" => $form['form'],
                "id" => $id,
                "patient_age" => $form['age'],
                "age_type" => $form['ageType'],
                "reason" => $reason,
                "icd" => $icd,
                "file_path" => $path,
                "file_name" => $file_name,
                "form_type" => $form_type,
                "referral_status" => $referral_status
            ]);
        }else if($form_type == 'pregnant') {
            $path = (PregnantForm::select('file_path')->where('code', $track->code)->first())->file_path;
            $file_name = basename($path);
            $reason = ReasonForReferral::select('pregnant_form.reason_referral as id', 'reason_referral.reason as reason')
                ->join('pregnant_form', 'pregnant_form.reason_referral', 'reason_referral.id')
                ->where('pregnant_form.code', $track->code)->first();

            return view("doctor.edit_referral_pregnant", [
                "form" => self::pregnantFormData($id),
                "id" => $id,
                "reason" => $reason,
                "icd" => $icd,
                "file_path" => $path,
                "file_name" => $file_name,
                "referral_status" => $referral_status,
                "form_type" => "$form_type"
            ]);
        }
    }

    public static function editForm(Request $req)
    {
        $id = $req->id;

        $tracking = Tracking::where('id', $id)->first();
        $track = $tracking->code;

        $tracking->referred_to = $req->referred_to;
        $tracking->department_id = $req->department_id;
        $tracking->save();

        $activity = Activity::where('code',$track)->first();
        $activity->referred_to = $req->referred_to;
        $activity->department_id = $req->department_id;
        $activity->save();

        $form_type = $req->form_type;
        $user = Session::get('auth');
        $dob = date('y-m-d h-i-s', strtotime($req->baby_dob));
        $data = '';

        $data_update = $req->all();

        if($form_type === 'normal') {
            $data = PatientForm::where('code', $track)->first();

            if($req->notes_diag_cleared == "true")
                $data->update(['diagnosis' => NULL]);

            unset($data_update['notes_diag_cleared']);

            $tracking->action_md = $req->referred_md;
            $tracking->save();

            $activity->action_md = $req->referred_md;
            $activity->save();
        }
        else if($form_type === 'pregnant') {
            $data = PregnantForm::where('code', $track)->first();
            $baby_id = $req->baby_id;
            $match = Patients::where('id', $baby_id)->first();

            $baby = array(
                "fname" => $req->baby_fname,
                "mname" => $req->baby_mname,
                "lname" => $req->baby_lname,
                "dob" => $dob
            );
            if(isset($match))
                $match->update($baby);
            else
                $baby_id = PatientCtrl::storeBabyAsPatient($baby,$req->mother_id);

            $match = Baby::where("baby_id", $baby_id)->first();

            if(isset($match)) {
                $match->weight = ($req->baby_weight) ? $req->baby_weight : '';
                $match->gestational_age = ($req->baby_gestational_age) ? $req->baby_gestational_age : '';
                $match->birth_date = $dob;
                $match->save();
            } else {
                $b = new Baby();
                $b->baby_id = $baby_id;
                $b->mother_id = $req->mother_id;
                $b->weight = ($req->baby_weight) ? $req->baby_weight : '';
                $b->gestational_age = ($req->baby_gestational_age) ? $req->baby_gestational_age : '';
                $b->birth_date = $dob;
                $b->save();
            }


            unset($data_update['baby_fname']);
            unset($data_update['baby_mname']);
            unset($data_update['baby_lname']);
            unset($data_update['baby_dob']);
            unset($data_update['baby_weight']);
            unset($data_update['baby_gestational_age']);
            unset($data_update['baby_id']);
            unset($data_update['mother_id']);

            if($req->notes_diag_cleared == "true")
                $data->update(['notes_diagnoses' => NULL]);

            unset($data_update['notes_diag_cleared']);

            $data_update['patient_baby_id'] = $baby_id;

            $data_update['woman_before_given_time'] = ($req->woman_before_given_time) ? date('y-m-d h-i-s', strtotime($req->woman_before_given_time)) : '';
            $data_update['woman_transport_given_time'] = ($req->woman_transport_given_time) ? date('y-m-d h-i-s', strtotime($req->woman_transport_given_time)) : '';
            $data_update['baby_last_feed'] = ($req->baby_last_feed) ? date('y-m-d h-i-s', strtotime($req->baby_last_feed)) : '';
            $data_update['baby_before_given_time'] = ($req->baby_before_given_time) ? date('y-m-d h-i-s', strtotime($req->baby_before_given_time)) : '';
            $data_update['baby_transport_given_time'] = ($req->baby_transport_given_time) ? date('y-m-d h-i-s', strtotime($req->baby_transport_given_time)) : '';
        }

        if($req->other_diag_cleared == "true") {
            $data->update(['other_diagnoses' => NULL]);
        }
        unset($data_update['other_diag_cleared']);

        if($req->icd_cleared === 'true')
            Icd::where('code', $track)->delete();
        unset($data_update['icd_cleared']);

        foreach($req->icd_ids as $i) {
            $value = Icd::where('code', $track)->where('icd_id', $i)->first();
            if(!isset($value)) {
                $icd = new Icd();
                $icd->code = $track;
                $icd->icd_id = $i;
                $icd->save();
            }
        }
        unset($data_update['icd_ids']);

        if($req->file_cleared == "true") {
            $data->update([
                'file_path' => null
            ]);
        }
        unset($data_update['file_cleared']);

        if($_FILES["file_upload"]["name"]) {
            $req->username = $user->username;
            $file = $_FILES['file_upload']['name'];

            ApiController::fileUpload($req);
            $data->update([
                'file_path' => ApiController::fileUploadUrl().$req->username."/".$file
            ]);
        }

        unset($data_update['file_upload']);

        unset($data_update['id']);
        unset($data_update['referral_status']);
        unset($data_update['form_type']);

        $data->update($data_update);
        Session::put('referral_update_save',true);
        Session::put('message','Successfully updated referral form!');
        return redirect()->back();
    }

    public static function undoCancel(Request $req)
    {
        $id = $req->undo_cancel_id;

        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');
        $track = Tracking::find($id);
        $activity = Activity::where("code",$track->code)->orderBy("id","desc")->skip(1)->first();

        Tracking::where('id',$id)
            ->update([
                'status' => $activity->status
            ]);
        Activity::where("code",$track->code)->orderBy("id","desc")->first()->delete();

        return redirect()->back();
    }
}
