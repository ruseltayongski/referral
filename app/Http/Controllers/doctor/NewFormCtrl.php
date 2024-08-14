<?php

namespace App\Http\Controllers\doctor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PastMedicalHistory;

class NewFormCtrl extends Controller
{
    public function index(){
        return view('modal/revised_normal_form');
    }
    
    public function saveReferral(Request $request)
    {
        $commordities = $request->comor_hyper_cbox . ',' . $request->hyper_year;
        $heredofamilial_diseases = $request->heredo_hyper_cbox;

        if ($request->comor_all_cbox == "Select All") {
            $commordities = implode(',', $request->comor_hyper_cbox) . ',' . $request->hyper_year;
        }

        if ($request->heredo_all_cbox == "Select All") {
            $heredofamilial_diseases = implode(',', $request->heredo_hyper_cbox);
        }

        $past_medical_history_data = array(
            'patient_id' => $request->patient_id,
            'commordities' => $commordities,
            'heredofamilial_diseases' => $heredofamilial_diseases,
            'previous_hospitalization' => $request->previous_hospitalization,
        );

        // Insert the data into the database using Eloquent
        PastMedicalHistory::create($past_medical_history_data);

        // Redirect or return a response
        return redirect()->back()->with('success', 'Past medical history saved successfully!');
    }
    
}
