<?php

namespace App\Http\Controllers;

use App\Barangay;
use App\Facility;
use App\Muncity;
use App\Province;
use Illuminate\Http\Request;

class LocationCtrl extends Controller
{
    function getBarangay($muncity_id)
    {
        $brgy = Barangay::where('muncity_id',$muncity_id)
            ->orderBy('description','asc')
            ->get();
        return $brgy;
    }

    static function facilityAddress($facility_id)
    {
        $facility = Facility::select(
            'barangay.description as brgy',
                'muncity.description as muncity',
                'province.description as province',
                'facility.address'
            )
            ->leftJoin('barangay','barangay.id','=','facility.brgy')
            ->leftJoin('muncity','muncity.id','=','facility.muncity')
            ->leftJoin('province','province.id','=','facility.province')
            ->where('facility.id',$facility_id)
            ->first();

        $address = '';
        if(!$facility){
            return 'N/A';
        }

        $address .= ($facility->address) ? $facility->address.', ':null;
        $address .= ($facility->brgy) ? $facility->brgy.', ':null;
        $address .= ($facility->muncity) ? $facility->muncity.', ':null;
        $address .= ($facility->province) ? $facility->province:null;

        return $address;
    }

    static function getBrgyName($id)
    {
        $name = Barangay::find($id);
        if($name){
            return $name->description;
        }
        return 'N/A';
    }

    static function getMuncityName($id)
    {
        $name = Muncity::find($id);
        if($name){
            return $name->description;
        }
        return 'N/A';
    }

    static function getProvinceName($id)
    {
        $name = Province::find($id);
        if($name){
            return $name->description;
        }
        return 'N/A';
    }
}
