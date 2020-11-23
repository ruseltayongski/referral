<?php

namespace App\Http\Controllers;

use App\Facility;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use App\BedTracker;
use Illuminate\Support\Facades\Session;

class BedTrackerCtrl extends Controller
{
    public function bed($facility_id){
        $facility = Facility::find($facility_id);

        return view('bed_tracker.bed_in_facility',[
            "facility" => $facility
        ]);
    }

    public function bedUpdate(Request $request){
        $facility = Facility::find($request->facility_id);

        $facility->update([
            $request->column => $request->value
        ]);

        $bed_tracker = new BedTracker();
        $bed_tracker->encoded_by = Session::get('auth')->id;
        $bed_tracker->facility_id = $facility->id;
        $bed_tracker->emergency_room_covid = $facility->emergency_room_covid;
        $bed_tracker->icu_covid = $facility->icu_covid;
        $bed_tracker->beds_covid = $facility->beds_covid;
        $bed_tracker->isolation_covid = $facility->isolation_covid;
        $bed_tracker->mechanical_used_covid = $facility->mechanical_used_covid;
        $bed_tracker->mechanical_vacant_covid = $facility->mechanical_vacant_covid;
        $bed_tracker->emergency_room_non = $facility->emergency_room_non;
        $bed_tracker->icu_non = $facility->icu_non;
        $bed_tracker->beds_non = $facility->beds_non;
        $bed_tracker->isolation_non = $facility->isolation_non;
        $bed_tracker->mechanical_used_non = $facility->mechanical_used_non;
        $bed_tracker->mechanical_vacant_non = $facility->mechanical_vacant_non;
        $bed_tracker->emergency_room_covid_wait = $facility->emergency_room_covid_wait;
        $bed_tracker->icu_covid_wait = $facility->icu_covid_wait;
        $bed_tracker->emergency_room_non_wait = $facility->emergency_room_non_wait;
        $bed_tracker->icu_non_wait = $facility->icu_non_wait;
        $bed_tracker->remarks = $facility->remarks;
        $bed_tracker->save();

        $user = Session::get('auth');
        $data["encoded_by"] = ucfirst($user->fname).' '.ucfirst($user->mname[0]).'. '.ucfirst($user->lname);
        $data["encoded_date"] = date("F d,Y");
        $data["encoded_time"] = "(".date("g:i a").")";

        return $data;
    }

    public function bedAdmin(){
        $facility = Facility::where(function($q){
                $q->where("hospital_type","government")->orWhere("hospital_type","private");
            })
            ->orderBy("province","asc")
            ->orderBy("name","asc")
            ->get();

        return view('bed_tracker.bed_in_admin',[
            "facility" => $facility
        ]);
    }

}
