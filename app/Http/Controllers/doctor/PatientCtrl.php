<?php

namespace App\Http\Controllers\doctor;

use App\Muncity;
use App\PatientForm;
use App\Patients;
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
            'source' => $source
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
            'source' => 'tsekap'
        ]);
    }

    function referPatient(Request $req)
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
            'remarks' => $req->reason,
            'status' => 'referred'
        );
        Tracking::updateOrCreate($match,$track);

        $unique_id = "$patient_id-$user->facility_id-".date('ymdH');
        $match = array(
            'unique_id' => $unique_id
        );
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

        return $form->id;
    }

    function importTsekap($patient_id,$civil_status,$phic_id,$phic_status)
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
            'civil_status' => $civil_status,
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
}
