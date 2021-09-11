<?php

namespace App\Http\Controllers\doctor;

use App\Activity;
use App\Facility;
use App\Feedback;
use App\Http\Controllers\DeviceTokenCtrl;
use App\Http\Controllers\ParamCtrl;
use App\Issue;
use App\Monitoring;
use App\PatientForm;
use App\Patients;
use App\PregnantForm;
use App\Seen;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
        $end = Carbon::now()->endOfMonth()->format('m/d/Y');
        $user = Session::get('auth');
        $data = Tracking::select(
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
                //->orderBy(DB::raw("IF( ((tracking.status='referred' or tracking.status='seen' or tracking.status = 'transferred') && tracking.department_id = '$user->department_id' ), now(), tracking.date_referred )"),"desc")
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

    function seen($track_id)
    {

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

    static function normalForm($id)
    {
        $form = PatientForm::select(
                    DB::raw("'$id' as tracking_id"),
                    'patient_form.code as code',
                    DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
                    DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
                    'patients.sex',
                    'patients.civil_status',
                    'patients.phic_status',
                    'patients.phic_id',
                    'patient_form.covid_number',
                    'patient_form.refer_clinical_status',
                    'patient_form.refer_sur_category',
                    'patient_form.case_summary',
                    'patient_form.reco_summary',
                    'patient_form.diagnosis',
                    'patient_form.reason',
                    'barangay.description as patient_brgy',
                    'muncity.description as patient_muncity',
                    'province.description as patient_province',
                    'b.description as facility_brgy',
                    'm.description as facility_muncity',
                    'p.description as facility_province',
                    'bb.description as ff_brgy',
                    'mm.description as ff_muncity',
                    'pp.description as ff_province',
                    'facility.name as referring_name',
                    'ff.name as referred_name',
                    DB::raw("DATE_FORMAT(patient_form.time_referred,'%M %d, %Y %h:%i %p') as time_referred"),
                    DB::raw("DATE_FORMAT(patient_form.time_transferred,'%M %d, %Y %h:%i %p') as time_transferred"),
                    DB::raw('CONCAT(
                            if(users.level="doctor","Dr. ","")
                    ,users.fname," ",users.mname," ",users.lname) as md_referring'),
                    DB::raw('CONCAT("Dr. ",u.fname," ",u.mname," ",u.lname) as md_referred'),
                    'facility.contact as referring_contact',
                    'ff.contact as referred_contact',
                    'users.contact as referring_md_contact',
                    'department.description as department'
                )
                ->join('patients','patients.id','=','patient_form.patient_id')
                ->join('tracking','tracking.form_id','=','patient_form.id')
                ->join('facility','facility.id','=','tracking.referred_from')
                ->join('facility as ff','ff.id','=','tracking.referred_to')
                ->leftJoin('users','users.id','=','patient_form.referring_md')
                ->leftJoin('users as u','u.id','=','patient_form.referred_md')
                ->leftJoin('barangay','barangay.id','=','patients.brgy')
                ->leftJoin('muncity','muncity.id','=','patients.muncity')
                ->join('province','province.id','=','patients.province')
                ->leftJoin('barangay as b','b.id','=','facility.brgy')
                ->leftJoin('muncity as m','m.id','=','facility.muncity')
                ->leftJoin('province as p','p.id','=','facility.province')
                ->leftJoin('barangay as bb','bb.id','=','ff.brgy')
                ->leftJoin('muncity as mm','mm.id','=','ff.muncity')
                ->leftJoin('province as pp','pp.id','=','ff.province')
                ->leftJoin('department','department.id','=','patient_form.department_id')
                ->where('tracking.id',$id)
                ->first();

        return $form;
    }

    static function pregnantForm($id)
    {
        $form = PregnantForm::select(
                DB::raw("'$id' as tracking_id"),
                'pregnant_form.patient_baby_id',
                'pregnant_form.code',
                'pregnant_form.record_no',
                DB::raw("DATE_FORMAT(pregnant_form.referred_date,'%M %d, %Y %h:%i %p') as referred_date"),
                DB::raw("DATE_FORMAT(pregnant_form.arrival_date,'%M %d, %Y %h:%i %p') as arrival_date"),
                DB::raw('CONCAT(
                    if(users.level="doctor","Dr. ","")
                ,users.fname," ",users.mname," ",users.lname) as md_referring'),
                'facility.name as referring_facility',
                'b.description as facility_brgy',
                'm.description as facility_muncity',
                'p.description as facility_province',
                'ff.name as referred_facility',
                'bb.description as ff_brgy',
                'mm.description as ff_muncity',
                'pp.description as ff_province',
                'pregnant_form.health_worker',
                DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as woman_name'),
                DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS woman_age"),
                'patients.sex',
                'barangay.description as patient_brgy',
                'muncity.description as patient_muncity',
                'province.description as patient_province',
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
                'pregnant_form.covid_number',
                'pregnant_form.refer_clinical_status',
                'pregnant_form.refer_sur_category'
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
                        DB::raw("DATE_FORMAT(baby.dob,'%M %d, %Y %h:%i %p') as baby_dob"),
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
        }
        return array(
            'form' => $form,
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
            $user = Session::get('auth');
            $data = Tracking::select(
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
            {
                $data = $data->where('tracking.status',$option_filter);
            }
            if($facility_filter)
            {
                $data = $data->where('tracking.referred_to',$facility_filter);
            }
            if($department_filter)
            {
                $data = $data->where('tracking.department_id',$department_filter);
            }

            if($date)
            {
                $range = explode('-',str_replace(' ', '', $date));
                $start = $range[0];
                $end = $range[1];
            }

            $start_date = Carbon::parse($start)->startOfDay();
            $end_date = Carbon::parse($end)->endOfDay();

            $data = $data->whereBetween('tracking.date_referred',[$start_date,$end_date]);

            $data = $data->orderBy('date_referred','desc')
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
            'department_filter' => $department_filter
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
        {
            return 'denied';
        }

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
        if($track->status=='accepted' || $track->status=='rejected')
        {
            Session::put('incoming_denied',true);
            return 'denied';
        }

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

        $hosp = Facility::find($user->facility_id)->name;
        $msg = "$track->code discharged from $hosp.";
        DeviceTokenCtrl::send('Discharged',$msg,$track->referred_from);
        return date('M d, Y h:i A',strtotime($date));
    }

    public function transfer(Request $req, $track_id)
    {
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

    public function redirect(Request $req, $activity_id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');

        $track = Activity::select('activity.*','tracking.type','tracking.form_id')
                ->join('tracking','tracking.code','=','activity.code')
                ->where('activity.id',$activity_id)
                ->first();
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
        $activity = Activity::create($data);

        $tracking = Tracking::where('code',$track->code)
            ->where('referred_from',$user->facility_id)
            ->first();

        if($tracking){
            $tracking->update([
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
        }


        $patient = Patients::select(
            DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            'patients.sex',
            DB::raw('CONCAT(fname," ",mname," ",lname) as patient_name')
        )
            ->where('id',$track->patient_id)
            ->first();
        $user_md = User::find($user->id);


        $form_type = '#normalFormModal';
        if($track->type=='pregnant'){
            $form_type = '#pregnantFormModal';
        }

        return array(
            'code' => $track->code,
            'date' => date('M d, Y h:i A',strtotime($date)),
            'patient_name' => $patient->patient_name,
            'age' => $patient->age,
            'sex' => $patient->sex,
            'action_md' => "$user_md->fname $user_md->mname $user_md->lname",
            'form_type' => $form_type,
            'track_id' => $tracking->id,
            'activity_id' => $activity->id,
            'referred_facility' => Facility::find($req->facility)->name
        );
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

        $f = Feedback::create($data);

        $doc = User::find($user->id);
        $name = ucwords(mb_strtolower($doc->fname))." ".ucwords(mb_strtolower($doc->lname));

        /*$msg = "From: Dr. $name\nReferral Code: $req->code\nMessage: $req->message";
        $facility_id = Tracking::where('code',$req->code)
                            ->first()
                            ->referred_from;

        DeviceTokenCtrl::send('New Feedback/Comment',$msg,$facility_id);
        return $f->id;*/

        return view('doctor.feedback_append',[
            "name" => $name,
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

}
