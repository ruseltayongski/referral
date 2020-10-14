<?php

namespace App\Http\Controllers;

use App\Facility;
use Illuminate\Http\Request;

class BedTracker extends Controller
{
    public function bed($facility_id){
        $facility = Facility::find($facility_id);

        return view('bed_tracker.bed_in_facility',[
            "facility" => $facility
        ]);
    }

    public function bedUpdate(Request $request){
        Facility::find($request->facility_id)->update([
            $request->column => $request->value
        ]);

        return $request->all();
    }
}
