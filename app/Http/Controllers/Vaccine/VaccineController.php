<?php

namespace App\Http\Controllers\Vaccine;

use App\Facility;
use App\Muncity;
use App\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VaccineController extends Controller
{
    public function index(){
        return view("vaccine.dashboard");
    }

    public function vaccineView(){
        return view("vaccine.vaccineview");
    }

    public function vaccinatedContent(){
        $province = Province::get();
        $muncity = Muncity::get();
        $facility = Facility::get();


        return view("vaccine.vaccinated_content",[
            "province" => $province,
            "muncity" => $muncity,
            "facility"=> $facility,

        ]);


    }

}


