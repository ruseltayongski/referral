<?php

namespace App\Http\Controllers\doctor;

use App\Activity;
use App\Facility;
use App\PatientForm;
use App\Patients;
use App\PregnantForm;
use App\Tracking;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReferralCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    public function searchReferral(Request $req)
    {
        $data = array(
            'keyword' => $req->keyword,
            'option' => $req->option
        );
        Session::put('search_referral',$data);
        return self::index();
    }

    public function index()
    {
        $search = Session::get('search_referral');
        $user = Session::get('auth');
        $data = Tracking::select(
                    'tracking.*',
                    DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
                    DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
                    'patients.sex',
                    'facility.name as facility_name',
                    DB::raw('CONCAT(users.fname," ",users.mname," ",users.lname) as referring_md'),
                    DB::raw('CONCAT(action.fname," ",action.mname," ",action.lname) as action_md')
                )
                ->join('patients','patients.id','=','tracking.patient_id')
                ->join('facility','facility.id','=','tracking.referred_from')
                ->join('users','users.id','=','tracking.referring_md')
                ->leftJoin('users as action','action.id','=','tracking.action_md')
                ->where('referred_to',$user->facility_id);
        if($search['keyword'])
        {
            $keyword = $search['keyword'];
            $data = $data->where(function($q) use ($keyword){
                $q->where('patients.lname',"$keyword")
                    ->orwhere(DB::raw('concat(patients.fname," ",patients.lname)'),"$keyword");
            });
        }

        if($search['option'])
        {
            $option = $search['option'];
            if($option=='referred'){
                $data = $data->where(function($q){
                    $q->where('tracking.status','referred')
                        ->orwhere('tracking.status','seen');
                });
            }else{
                $data = $data->where('tracking.status','accepted');
            }
        }else{
            $data = $data->where(function($q){
                $q->where('tracking.status','referred')
                    ->orwhere('tracking.status','seen')
                    ->orwhere('tracking.status','accepted')
                    ->orwhere('tracking.status','redirected')
                    ->orwhere('tracking.status','rejected');
            });
        }
        $data = $data->orderBy('date_referred','desc')
                ->paginate(15);
        return view('doctor.referral',[
            'title' => 'Incoming Patients',
            'data' => $data
        ]);
    }

    static function countReferral()
    {
        $user = Session::get('auth');
        $count = Tracking::where('referred_to',$user->facility_id)
            ->where(function($q){
                $q->where('status','referred')
                    ->orwhere('status','seen');
            })
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

    function normalForm($code)
    {
        $user = Session::get('auth');
        $form = PatientForm::select(
                    //'patient_form.*',
                    DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
                    DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
                    'patients.sex',
                    'patients.civil_status',
                    'patients.phic_status',
                    'patients.phic_id',
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
                    'facility.name as referring_name',
                    DB::raw("DATE_FORMAT(patient_form.time_referred,'%M %d, %Y %h:%i %p') as time_referred"),
                    DB::raw("DATE_FORMAT(patient_form.time_transferred,'%M %d, %Y %h:%i %p') as time_transferred"),
                    DB::raw('CONCAT("Dr. ",users.fname," ",users.mname," ",users.lname) as md_referring'),
                    DB::raw('CONCAT("Dr. ",u.fname," ",u.mname," ",u.lname) as md_referred'),
                    'facility.contact as referring_contact',
                    'users.contact as referring_md_contact',
                    'department.description as department'
                )
                ->join('patients','patients.id','=','patient_form.patient_id')
                ->join('tracking','tracking.form_id','=','patient_form.id')
                ->join('facility','facility.id','=','tracking.referred_from')
                ->join('users','users.id','=','patient_form.referring_md')
                ->leftJoin('users as u','u.id','=','patient_form.referred_md')
                ->join('barangay','barangay.id','=','patients.brgy')
                ->join('muncity','muncity.id','=','patients.muncity')
                ->join('province','province.id','=','patients.province')
                ->leftJoin('barangay as b','b.id','=','facility.brgy')
                ->leftJoin('muncity as m','m.id','=','facility.muncity')
                ->leftJoin('province as p','p.id','=','facility.province')
                ->leftJoin('department','department.id','=','patient_form.department_id')
                ->where('patient_form.code',$code)
                ->where('tracking.referred_to',$user->facility_id)
                ->first();
        return $form;
    }

    function pregnantForm($code)
    {
        $user = Session::get('auth');
        $form = PregnantForm::select(
                'pregnant_form.patient_baby_id',
                'pregnant_form.record_no',
                DB::raw("DATE_FORMAT(pregnant_form.referred_date,'%M %d, %Y %h:%i %p') as referred_date"),
                DB::raw('CONCAT("Dr. ",users.fname," ",users.mname," ",users.lname) as md_referring'),
                'facility.name as referring_facility',
                'b.description as facility_brgy',
                'm.description as facility_muncity',
                'p.description as facility_province',
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
                'users.contact as referring_md_contact',
                'department.description as department'
            )
            ->join('patients','patients.id','=','pregnant_form.patient_woman_id')
            ->join('tracking','tracking.form_id','=','pregnant_form.id')
            ->join('facility','facility.id','=','tracking.referred_from')
            ->join('users','users.id','=','pregnant_form.referred_by')
            ->join('barangay','barangay.id','=','patients.brgy')
            ->join('muncity','muncity.id','=','patients.muncity')
            ->join('province','province.id','=','patients.province')
            ->leftJoin('barangay as b','b.id','=','facility.brgy')
            ->leftJoin('muncity as m','m.id','=','facility.muncity')
            ->leftJoin('province as p','p.id','=','facility.province')
            ->leftJoin('department','department.id','=','pregnant_form.department_id')
            ->where('pregnant_form.code',$code)
            ->where('tracking.referred_to',$user->facility_id)
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
                    ->where('pregnant_form.code',$code)
                    ->where('tracking.referred_to',$user->facility_id)
                    ->first();
        }
        return array(
            'form' => $form,
            'baby' => $baby
        );
    }

    public function referred()
    {
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
            ->join('users','users.id','=','tracking.referring_md')
            ->where('referred_from',$user->facility_id)
            ->where(function($q){
                $q->where('tracking.status','referred')
                    ->orwhere('tracking.status','seen')
                    ->orwhere('tracking.status','accepted')
                    ->orwhere('tracking.status','transferred')
                    ->orwhere('tracking.status','discharged')
                    ->orwhere('tracking.status','rejected');
            })
            ->orderBy('date_referred','desc')
            ->paginate(15);

        return view('doctor.referred',[
            'title' => 'Referred Patients',
            'data' => $data
        ]);
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
            'remarks' => $req->remarks,
            'status' => $track->status
        );
        Activity::create($data);

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

        return date('M d, Y h:i A',strtotime($date));
    }

    public function discharge(Request $req, $track_id)
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
            'remarks' => $req->remarks,
            'status' => 'discharged'
        );
        Activity::create($data);

        Tracking::where('id',$track_id)
            ->update([
                'status' => 'discharged'
            ]);
        return date('M d, Y h:i A',strtotime($date));
    }

    public function transfer(Request $req, $track_id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');

        Tracking::where('id',$track_id)
            ->update([
                'status' => 'transferred',
                'action_md' => $user->id
            ]);
        $track = Tracking::find($track_id);
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $track->referred_to,
            'referred_to' => $req->facility,
            'department_id' => $req->department,
            'action_md' => $user->id,
            'remarks' => $req->remarks,
            'status' => $track->status
        );
        $activity = Activity::create($data);

        $new_data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'code' => $track->code,
            'date_referred' => $date,
            'date_arrived' => '',
            'date_seen' => '',
            'department_id' => $req->department,
            'referred_from' => $track->referred_to,
            'referred_to' => $req->facility,
            'remarks' => $track->remarks,
            'referring_md' => $user->id,
            'status' => 'referred',
            'type' => $track->type,
            'form_id' => $track->form_id
        );

        $patient = Patients::select(
                        DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
                        'patients.sex'
                    )
                    ->where('id',$track->patient_id)
                    ->first();
        $user_md = User::find($user->id);
        $tracking = Tracking::create($new_data);

        $form_type = '#normalFormModal';
        if($track->type=='pregnant'){
            $form_type = '#pregnantFormModal';
        }

        return array(
            'date' => date('M d, Y h:i A',strtotime($date)),
            'age' => $patient->age,
            'sex' => $patient->sex,
            'action_md' => "$user_md->fname $user_md->mname $user_md->lname",
            'form_type' => $form_type,
            'track_id' => $tracking->id,
            'activity_id' => $activity->id,
            'referred_facility' => $track->referred_to
        );
    }

    public function redirect(Request $req, $activity_id)
    {
        $user = Session::get('auth');
        $date = date('Y-m-d H:i:s');

        Activity::where('id',$activity_id)
            ->update([
                'referred_to' => $user->facility_id,
                'department_id' => 1
            ]);
        $track = Activity::select('activity.*','tracking.type','tracking.form_id')
                ->join('tracking','tracking.code','=','activity.code')
                ->where('activity.id',$activity_id)
                ->first();
        $data = array(
            'code' => $track->code,
            'patient_id' => $track->patient_id,
            'date_referred' => $date,
            'referred_from' => $track->referred_to,
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
                'referred_from' => $track->referred_to,
                'referred_to' => $req->facility,
                'remarks' => '',
                'referring_md' => $user->id,
                'status' => 'referred'
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
}
