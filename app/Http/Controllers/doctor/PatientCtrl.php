<?php

namespace App\Http\Controllers\doctor;

use App\Baby;
use App\Barangay;
use App\Http\Controllers\ParamCtrl;
use App\Muncity;
use App\PatientForm;
use App\Patients;
use App\PregnantForm;
use App\Profile;
use App\Province;
use App\Tracking;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PatientCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    public function searchProfile(Request $req)
    {
        $data = array(
            'keyword' => $req->keyword,
            'brgy' => $req->brgy,
            'muncity' => $req->muncity,
            'others' => $req->others
        );
        Session::put('profileSearch',$data);
        return self::index();
    }

    public function index()
    {
        $source='referral';
        $user = Session::get('auth');
        $muncity = Muncity::where('province_id',$user->province)->orderby('description','asc')->get();

        $keyword = '';
        $brgy = '';
        $mun = '';
        $others = '';
        $session = Session::get('profileSearch');
        if(isset($session))
        {
            $keyword = $session['keyword'];
            $brgy = $session['brgy'];
            $mun = $session['muncity'];
            $others = $session['others'];
        }

        $data = array();

        if(isset($keyword) && isset($mun) && (isset($brgy) || isset($others) ) ){
            $data = Patients::orderBy('lname','asc');
            if(isset($brgy)){
                $data = $data->where('brgy',$brgy);
            }else if(isset($mun) && $mun!='others'){
                $data = $data->where('muncity',$mun);
            }else if(isset($others)){
                $data = $data->where('address','like',"%$others%");
            }

            $data = $data->where(function($q) use($keyword){
                $q->where('lname',"$keyword")
                    ->orwhere(DB::raw('concat(fname," ",lname)'),"$keyword");
            });

            $data = $data->paginate(20);
        }

        return view('doctor.patient',[
            'title' => 'Patient List',
            'data' => $data,
            'muncity' => $muncity,
            'source' => $source,
            'sidebar' => 'filter_profile'
        ]);
    }

    public function addPatient()
    {
        $user = Session::get('auth');
        $muncity = Muncity::where('province_id',$user->province)->orderby('description','asc')->get();
        return view('doctor.addPatient',[
            'title' => 'Add New Patient',
            'muncity' => $muncity,
            'method' => 'store'
        ]);
    }

    public function storePatient(Request $req)
    {
        $user = Session::get('auth');
        $unique = array(
            $req->fname,
            $req->mname,
            $req->lname,
            date('Ymd',strtotime($req->dob)),
            $req->brgy
        );
        $unique = implode($unique);

        $match = array('unique_id'=>$unique);
        $data = array(
            'phic_status' => $req->phic_status,
            'phic_id' => ($req->phicID) ? $req->phicID: '',
            'fname' => $req->fname,
            'mname' => $req->mname,
            'lname' => $req->lname,
            'dob' => $req->dob,
            'sex' => $req->sex,
            'civil_status' => $req->civil_status,
            'muncity' => $req->muncity,
            'province' => $user->province,
            'brgy' => ($req->brgy) ? $req->brgy:'' ,
            'address' => ($req->others) ? $req->others: ''
        );

        Patients::updateOrCreate($match,$data);
        return redirect()->back()->with('status','added');
    }

    public function showPatientProfile($id)
    {
        $data = Patients::find($id);
        if($data->brgy)
        {
            $brgy = Barangay::find($data->brgy)->description;
            $muncity = Muncity::find($data->muncity)->description;
            $province = Province::find($data->province)->description;
            $data->address = "$brgy, $muncity, $province";
        }else{
            $data->address = $data->address;
        }
        $data->patient_name = "$data->fname $data->mname $data->lname";
        $data->age = ParamCtrl::getAge($data->dob);
        return $data;
    }

    public function showTsekapProfile($id)
    {
        $data = Profile::find($id);
        if($data->barangay_id)
        {
            $brgy = Barangay::find($data->barangay_id)->description;
            $muncity = Muncity::find($data->muncity_id)->description;
            $province = Province::find($data->province_id)->description;
            $data->address = "$brgy, $muncity, $province";
        }else{
            $data->address = 'N/A';
        }
        $data->patient_name = "$data->fname $data->mname $data->lname";
        $data->age = ParamCtrl::getAge($data->dob);
        $data->civil_status = 'Single';
        $data->phic_status = 'None';
        $data->phic_id = $data->phicID;
        return $data;
    }

    public function searchTsekap(Request $req)
    {
        $data = array(
            'keyword' => $req->keyword,
            'brgy' => $req->brgy,
            'muncity' => $req->muncity
        );
        Session::put('tsekapSearch',$data);
        return self::tsekap();
    }

    public function tsekap()
    {
        $keyword = '';
        $brgy = '';
        $mun = '';
        $session = Session::get('tsekapSearch');
        if(isset($session))
        {
            $keyword = $session['keyword'];
            $brgy = $session['brgy'];
            $mun = $session['muncity'];
        }
        $user = Session::get('auth');
        $muncity = Muncity::where('province_id',$user->province)->orderby('description','asc')->get();

        $data = array();

        if(isset($keyword) && isset($brgy) && isset($mun)){
            $data = Profile::orderBy('lname','asc');
            if(isset($brgy)){
                $data = $data->where('barangay_id',$brgy);
            }else if(isset($mun)){
                $data = $data->where('muncity_id',$mun);
            }

            $data = $data->where(function($q) use($keyword){
                    $q->where('lname',"$keyword")
                        ->orwhere(DB::raw('concat(fname," ",lname)'),"$keyword");
                });

            $data = $data->where('barangay_id','>',0)
                        ->paginate(20);
        }

        return view('doctor.tsekap',[
            'title' => 'Patient List: Tsekap Profiles',
            'data' => $data,
            'muncity' => $muncity,
            'source' => 'tsekap',
            'sidebar' => 'tsekap_profile'
        ]);
    }

    function referPatient(Request $req,$type)
    {
        $user = Session::get('auth');
        $patient_id = $req->patient_id;
        $user_code = str_pad($user->facility_id,3,0,STR_PAD_LEFT);
        $code = date('ymd').'-'.$user_code.'-'.date('His');

        if($req->source==='tsekap')
        {
            $patient_id = self::importTsekap($req->patient_id,$req->patient_status,$req->phic_id,$req->phic_status);
        }

        $match = array(
            'code' => $code
        );
        $track = array(
            'patient_id' => $patient_id,
            'code' => $code,
            'date_referred' => $req->date_referred,
            'referred_from' => $user->facility_id,
            'referred_to' => $req->referred_facility,
            'referring_md' => $user->id,
            'action_md' => '',
            'remarks' => ($req->reason) ? $req->reason: '',
            'status' => 'referred'
        );
        $tracking = Tracking::updateOrCreate($match,$track);
        $tracking_id = $tracking->id;

        $unique_id = "$patient_id-$user->facility_id-".date('ymdH');
        $match = array(
            'unique_id' => $unique_id
        );

        if($type==='normal')
        {
            Patients::where('id',$patient_id)
                ->update([
                    'sex' => $req->patient_sex,
                    'civil_status' => $req->civil_status,
                    'phic_status' => $req->phic_status,
                    'phic_id' => $req->phic_id
                ]);

            $data = array(
                'code' => $code,
                'referring_facility' => $user->facility_id,
                'referred_to' => $req->referred_facility,
                'time_referred' => $req->date_referred,
                'time_transferred' => '',
                'patient_id' => $patient_id,
                'case_summary' => $req->case_summary,
                'reco_summary' => $req->reco_summary,
                'diagnosis' => $req->diagnosis,
                'reason' => $req->reason,
                'referring_md' => $user->id,
                'referred_md' => ($req->reffered_md) ? $req->reffered_md: 0,
            );
            $form = PatientForm::updateOrCreate($match,$data);
        }
        else if($type==='pregnant')
        {
            $baby = array(
                'fname' => ($req->baby_fname) ? $req->baby_fname: '',
                'mname' => ($req->baby_mname) ? $req->baby_mname: '',
                'lname' => ($req->baby_lname) ? $req->baby_lname: '',
                'dob' => ($req->baby_dob) ? $req->baby_dob: '',
                'civil_status' => 'Single'
            );
            $baby_id = self::storeBabyAsPatient($baby,$patient_id);

            Tracking::updateOrCreate([
                'code' => $code
            ],[
                'remarks' => $req->woman_information_given
            ]);

            Baby::updateOrCreate([
                'baby_id' => $baby_id,
                'mother_id' => $patient_id
            ],[
                'weight' => ($req->baby_weight) ? $req->baby_weight:'',
                'gestational_age' => ($req->baby_gestational_age) ? $req->baby_gestational_age: ''
            ]);

            $data = array(
                'code' => $code,
                'referring_facility' => ($user->facility_id) ? $user->facility_id: '',
                'referred_by' => ($user->id) ? $user->id: '',
                'record_no' => ($req->record_no) ? $req->record_no: '',
                'referred_date' => ($req->date_referred) ? $req->date_referred: '',
                'referred_to' => ($req->referred_facility) ? $req->referred_facility: '',
                'health_worker' => ($req->health_worker) ? $req->health_worker: '',
                'patient_woman_id' => $patient_id,
                'woman_reason' => ($req->woman_reason) ? $req->woman_reason: '',
                'woman_major_findings' => ($req->woman_major_findings) ? $req->woman_major_findings: '',
                'woman_before_treatment' => ($req->woman_before_treatment) ? $req->woman_before_treatment: '',
                'woman_before_given_time' => ($req->woman_before_given_time) ? $req->woman_before_given_time: '',
                'woman_during_transport' => ($req->woman_during_treatment) ? $req->woman_during_treatment: '',
                'woman_transport_given_time' => ($req->woman_during_given_time) ? $req->woman_during_given_time: '',
                'woman_information_given' => ($req->woman_information_given) ? $req->woman_information_given: '',
                'patient_baby_id' => $baby_id,
                'baby_reason' => ($req->baby_reason) ? $req->baby_reason: '',
                'baby_major_findings' => ($req->baby_major_findings) ? $req->baby_major_findings: '',
                'baby_last_feed' => ($req->baby_last_feed) ? $req->baby_last_feed: '',
                'baby_before_treatment' => ($req->baby_before_treatment) ? $req->baby_before_treatment: '',
                'baby_before_given_time' => ($req->baby_before_given_time) ? $req->baby_before_given_time: '',
                'baby_during_transport' => ($req->baby_during_treatment) ? $req->baby_during_treatment: '',
                'baby_transport_given_time' => ($req->baby_during_given_time) ? $req->baby_during_given_time: '',
                'baby_information_given' => ($req->baby_information_given) ? $req->baby_information_given: '',
            );
            $form = PregnantForm::updateOrCreate($match,$data);
        }
        Tracking::where('id',$tracking_id)
            ->update([
                'type' => $type,
                'form_id' => $form->id
            ]);

        return array(
            'id' => $tracking_id,
            'patient_code' => $code,
            'referred_date' => date('M d, Y h:i A',strtotime($req->date_referred))
        );
    }

    function storeBabyAsPatient($data,$mother_id)
    {
        if($data['fname']){
            $mother = Patients::find($mother_id);
            $data['brgy'] = $mother->brgy;
            $data['muncity'] = $mother->muncity;
            $data['province'] = $mother->province;
            $dob = date('ymd',strtotime($data['dob']));
            $tmp = array(
                $data['fname'],
                $data['mname'],
                $data['lname'],
                $data['brgy'],
                $dob
            );
            $unique = implode($tmp);
            $match = array(
                'unique_id' => $unique
            );

            $patient = Patients::updateOrCreate($match,$data);
            return $patient->id;
        }else{
            return '0';
        }


    }

    function importTsekap($patient_id,$civil_status='',$phic_id='',$phic_status='')
    {
        $profile = Profile::find($patient_id);

        $unique = array(
            $profile->fname,
            $profile->mname,
            $profile->lname,
            date('Ymd',strtotime($profile->dob)),
            $profile->barangay_id
        );
        $unique = implode($unique);
        $match = array(
            'unique_id' => $unique
        );
        $data = array(
            'fname' => $profile->fname,
            'mname' => $profile->mname,
            'lname' => $profile->lname,
            'dob' => $profile->dob,
            'sex' => $profile->sex,
            'civil_status' => ($civil_status) ? $civil_status: 'N/A',
            'phic_id' => ($phic_id) ? $phic_id: 'N/A',
            'phic_status' => ($phic_status) ? $phic_status: 'N/A',
            'brgy' => $profile->barangay_id,
            'muncity' => $profile->muncity_id,
            'province' => $profile->province_id,
            'tsekap_patient' => 1
        );
        $patient = Patients::updateOrCreate($match,$data);
        return $patient->id;
    }

    function accepted()
    {
        $user = Session::get('auth');
        $data = Tracking::select(
                    'tracking.id',
                    'tracking.type',
                    'tracking.code',
                    'facility.name',
                    DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
                    DB::raw("DATE_FORMAT(tracking.date_accepted,'%M %d, %Y %h:%i %p') as date_accepted")
                )
                ->join('facility','facility.id','=','tracking.referred_from')
                ->join('patients','patients.id','=','tracking.patient_id')
                ->where('referred_to',$user->facility_id)
                ->where('tracking.status','accepted')
                ->orderBy('id','desc')
                ->paginate(15);
        return view('doctor.accepted',[
            'title' => 'Accepted Patients',
            'data' => $data
        ]);
    }
}
