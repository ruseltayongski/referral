<?php

namespace App\Http\Controllers\doctor;

use App\Baby;
use App\Muncity;
use App\PatientForm;
use App\Patients;
use App\PregnantForm;
use App\Profile;
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

    public function index()
    {
        $data = array();
        $source='referral';
        $user = Session::get('auth');
        $muncity = Muncity::where('province_id',$user->province)->orderby('description','asc')->get();

        return view('doctor.patient',[
            'title' => 'Patient List',
            'data' => $data,
            'muncity' => $muncity,
            'source' => $source,
            'sidebar' => 'filter_profile'
        ]);
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

        if(isset($keyword) || isset($brgy) || isset($mun)){
            $data = Profile::orderBy('lname','asc');
            if(isset($brgy)){
                $data = $data->where('barangay_id',$brgy);
            }else if(isset($mun)){
                $data = $data->where('muncity_id',$mun);
            }

            if(isset($keyword)){
                $data = $data->where(function($q) use($keyword){
                    $q = $q->where('fname','like',"%$keyword%")
                        ->orwhere('lname','like',"%$keyword%")
                        ->orwhere('mname','like',"%$keyword%")
                        ->orwhere(DB::raw('concat(fname," ",mname," ",lname)'),'like',"%$keyword%")
                        ->orwhere(DB::raw('concat(fname," ",lname)'),'like',"%$keyword%")
                        ->orwhere(DB::raw('concat(lname," ",fname," ",mname)'),'like',"%$keyword%");
                });
            }

            $data = $data->where('barangay_id','>',0)
                        ->paginate(20);
        }
//        print_r($data);
//        exit();
        return view('doctor.patient',[
            'title' => 'Patient List',
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
            'referred_from' => $user->id,
            'referred_to' => $req->referred_facility,
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
                'fname' => $req->baby_fname,
                'mname' => $req->baby_mname,
                'lname' => $req->baby_lname,
                'dob' => $req->baby_dob,
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
                'weight' => $req->baby_weight,
                'gestational_age' => $req->baby_gestational_age
            ]);

            $data = array(
                'code' => $code,
                'referring_facility' => $user->facility_id,
                'referred_by' => $user->id,
                'record_no' => $req->record_no,
                'referred_date' => $req->date_referred,
                'referred_to' => $req->referred_facility,
                'health_worker' => $req->health_worker,
                'patient_woman_id' => $patient_id,
                'woman_reason' => $req->woman_reason,
                'woman_major_findings' => $req->woman_major_findings,
                'woman_before_treatment' => $req->woman_before_treatment,
                'woman_before_given_time' => $req->woman_before_given_time,
                'woman_during_transport' => $req->woman_during_treatment,
                'woman_transport_given_time' => $req->woman_during_given_time,
                'woman_information_given' => $req->woman_information_given,
                'patient_baby_id' => $baby_id,
                'baby_reason' => $req->baby_reason,
                'baby_major_findings' => $req->baby_major_findings,
                'baby_last_feed' => $req->baby_last_feed,
                'baby_before_treatment' => $req->baby_before_treatment,
                'baby_before_given_time' => $req->baby_before_given_time,
                'baby_during_transport' => $req->baby_during_treatment,
                'baby_transport_given_time' => $req->baby_during_given_time,
                'baby_information_given' => $req->baby_information_given,
            );
            $form = PregnantForm::updateOrCreate($match,$data);
        }
        Tracking::where('id',$tracking_id)
            ->update([
                'type' => $type,
                'form_id' => $form->id
            ]);

        return array(
            'id' => $form->id,
            'ref_no' => $code
        );
    }

    function storeBabyAsPatient($data,$mother_id)
    {
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

    }

    function importTsekap($patient_id,$civil_status='',$phic_id='',$phic_status='')
    {
        $profile = Profile::find($patient_id);

        $match = array(
            'unique_id' => $profile->unique_id
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
        return view('doctor.accepted',[
            'title' => 'Accepted Patients'
        ]);
    }
}
