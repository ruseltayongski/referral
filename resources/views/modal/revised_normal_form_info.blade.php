
<?php
$user = Session::get('auth');
$reason_for_referral = \App\ReasonForReferral::get();
$facilities = \App\Facility::select('id','name')
    ->where('status',1)
    ->where('referral_used','yes')
    ->orderBy('name','asc')->get();
$myfacility = \App\Facility::find($user->facility_id);
$facility_address = \App\Http\Controllers\LocationCtrl::facilityAddress($myfacility->id);
?>

<style>
  /* .file-upload {
        background-color: #ffffff;
        width: 600px;
        margin: 0 auto;
        padding: 20px;
    }

    .file-upload .file-upload-btn {
        width: 100%;
        margin: 0;
        color: #fff;
        background: #1FB264;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #15824B;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 700;
    }

    .file-upload .file-upload-btn:hover {
        background: #1AA059;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .file-upload .file-upload-btn:active {
        border: 0;
        transition: all .2s ease;
    }

    .file-upload .file-upload-content {
        display: none;
        text-align: center;
    }

    .file-upload .file-upload-input {
        position: absolute;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        outline: none;
        opacity: 0;
        cursor: pointer;
    }

    .file-upload .image-upload-wrap {
        margin-top: 20px;
        border: 4px dashed #1FB264;
        position: relative;
    }

    .file-upload .image-dropping,
    .file-upload .image-upload-wrap:hover {
        background-color: #1FB264;
        border: 4px dashed #ffffff;
    }

    .file-upload .image-title-wrap {
        padding: 0 15px 15px 15px;
        color: #222;
    }

    .file-upload .drag-text {
        text-align: center;
    }

    .file-upload .drag-text h3 {
        font-weight: 100;
        text-transform: uppercase;
        color: #15824B;
        padding: 60px 0;
    }

    .file-upload .file-upload-image {
        max-height: 200px;
        max-width: 200px;
        margin: auto;
        padding: 20px;
    }

    .file-upload .remove-image {
        width: 200px;
        margin: 0;
        color: #fff;
        background: #cd4535;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #b02818;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 700;
    }

    .file-upload .remove-image:hover {
        background: #c13b2a;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .file-upload .remove-image:active {
        border: 0;
        transition: all .2s ease;
    } */

    .container-referral {
        border: 1px solid lightgrey;
        width: 100%;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 5px;
        padding-right: 5px;
    }

   .glasgow-table {
        border: 1px solid lightgrey;
        width: 100%;
    }

    .glasgow-dot {
        background-color: #494646;
        border-radius: 50%;
        display: inline-block;
    }

    .referral-radio-btn {
        height:18px;
        width:18px;
        vertical-align: middle;
    }

    .mobile-view {
        display: none;
        visibility: hidden;
    }
    #prenatal_table {
        display: block;
        white-space: nowrap;
    }

    #glasgow_table_1, tr td:nth-child(1) {width: 35%;}
    #glasgow_table_2 tr td:nth-child(2) {width: 35%;}  

    @media only screen and (max-width: 720px) {
        .web-view {
            display: none;
            visibility: hidden;
        }
        .mobile-view {
            display: block;
            visibility: visible;
        }
    }
</style>

<style>
    .file-upload {
        background-color: #ffffff;
        width: 100%;
        height: 35%;
        margin: 0 auto;
        padding: 20px;
        border: 1px dashed dimgrey;
    }
    .file-upload-btn {
        width: 100%;
        margin: 0;
        color: #fff;
        background: #1FB264;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #15824B;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 700;
    }
    .file-upload-btn:hover {
        background: #1AA059;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }
    .file-upload-btn:active {
        border: 0;
        transition: all .2s ease;
    }
    .file-upload-content {
        display: none;
        text-align: center;
    }
    .file-upload-input {
        position: absolute;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        outline: none;
        opacity: 0;
        cursor: pointer;
    }
    .image-upload-wrap {
        margin-top: 20px;
        border: 4px dashed #1FB264;
        position: relative;
    }
    .image-dropping,
    .image-upload-wrap:hover {
        background-color: /*#6ab155*/#1FB264;
        border: 4px dashed #ffffff;
    }
    .image-title-wrap {
        padding: 0 15px 15px 15px;
        color: #222;
    }
    .drag-text {
        text-align: center;
    }
    .drag-text h3 {
        font-weight: 100;
        text-transform: uppercase;
        color: #15824B;
        padding: 60px 0;
    }
    .file-upload-image {
        max-height: 75%;
        max-width: 75%;
        margin: auto;
        padding: 20px;
    }
    .remove-image {
        width: 100%;
        margin: 0;
        color: #fff;
        background: #cd4535;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #b02818;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 600;
    }
    .remove-image:hover {
        background: #c13b2a;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }
    .remove-image:active {
        border: 0;
        transition: all .2s ease;
    }
    @media only screen and (max-width: 720px) {
        .file-upload {
            background-color: #ffffff;
            width: 50%;
        }
        .file-upload-image {
            max-height: 50%;
            max-width: 50%;
        }
    }

    .remove-icon-btn{
        position: absolute;
        top: -2px; /* Adjust as needed */
        right: 12px; /* Adjust as needed */
        background: transparent;
        border: none;
        font-weight: bold;
        font-size: 24px;
        color: #ff0000; /* Optional: Trash icon color */
        font-size: 18px; /* Optional: Adjust icon size */
        cursor: pointer;
        transition: transform 0.2s;
    }
    .remove-icon-btn:hover {
        transform: scale(1.1); /* Slightly increase size on hover */
    }
    .remove-icon-btn i {
        pointer-events: none; /* To ensure the button handles the click, not the icon */
    }
    .unclickable {
        pointer-events: none; /* Disables click actions */
        background-color: #f0f0f0;
        color: black;
    }
    
</style>

   
    <form action="{{ url('update-referral', ['patient_id' => $patient_id, 'id' => $id, 'type'=>'normal', 'status' => $status]) }}" method="POST" class="edit_normal_form" enctype="multipart/form-data">
     
        @include('include.header_form', ['optionHeader' => 'edit'])<br>
                <div class="form-group-sm form-inline">
                        {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" name="referral_status" value="{{ $referral_status }}">
                    <input type="hidden" name="form_type" value="normal">
                    <input type="hidden" name="username" value="{{ $username }}">
                    
                            <div class="row" style="margin: 5px;">
                                <div class="col-md-4">
                                    <small class="text-success">Name of Referring Facility</small><br>
                                    &nbsp;<span>{{ $myfacility->name }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-success">Address</small><br>
                                    &nbsp;<span>{{ $facility_address['address'] }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-success">Name of referring MD/HCW</small><br>
                                    &nbsp;<span>Dr. {{ $user->fname }} {{ $user->mname }} {{ $user->lname }}</span>
                                </div>
                            </div>
                            <br>
                            <div class="row" style="margin: 5px;">
                                <div class="col-md-4">
                                    <small class="text-success">Date/Time Referred (ReCo)</small><br>
                                    <span>{{ date('l F d, Y h:i A') }}</span>
                                </div>
                         
                                <div class="col-md-4">
                                    <small class="text-success">Name of Patient</small><br>
                                    <span class="patient_name">{{$form->patient_name}}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-success">Address</small><br>
                                    <span class="patient_address">{{$form->patient_address}}</span>
                                </div>
                            </div>
                        <br>
                <div class="row" style="margin: 5px;">
                    <div class="col-md-4">
                        <small class="text-success"><b>REFERRED TO: </b></small> &nbsp;<span class="text-red">*</span><br>
                        <input type="hidden" name="old_facility" value="{{ $form->referred_fac_id }}">
                        <select name="referred_to" class="select2 edit_facility_normal form-control" require>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                @if($row->id == $form->referred_fac_id)
                                    <option data-name="{{ $row->name }}" value="{{ $row->id }}" selected>{{ $row->name }}</option>
                                @else
                                    <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <small class="text-success"><b>DEPARTMENT: </b></small> <span class="text-red">*</span><br>
                        <select name="department_id" class="form-control edit_department_normal" style="width: 100%;" required>
                            <option value="">Select Option</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <small ><small class="text-success">ADDRESS:</small></small><br>
                        &nbsp;<span class="text-yellow edit_fac_address_normal"></span>
                        </div>
                        </div>
                        <br>
                        <div class="row" style="margin:5px">
                            <div class="col-md-4">
                                <small >Age</small><br>
                                <span class="patient_age">
                                @if($age_type == "y")
                                    @if($patient_age == 1)
                                        {{ $patient_age }} year old
                                    @else
                                        {{ $patient_age }} years old
                                    @endif
                                @elseif($age_type == "m")
                                    @if($patient_age['month'])
                                        {{ $patient_age['month'] }} mo,
                                    @else
                                        {{ $patient_age['month'] }} mos,
                                    @endif
                                    @if($patient_age['days'] == 1)
                                        {{ $patient_age['days'] }} day old
                                    @else
                                        {{ $patient_age['days'] }} days old
                                    @endif
                                @endif
                                </span>
                                
                            </div>
                            <div class="col-md-4">
                                    <small ><small class="text-success">SEX: </small></small><br>&nbsp;
                                    <span class="patient_sex"></span>
                                </div>
                                <div class="col-md-4">
                                    <small ><small class="text-success">STATUS: </small></small><br> &nbsp;
                                    <span class="civil_status"></span>
                                </div>
                            </div>
                        <br>
                        <div class="row" style="margin:5px">
                            <div class="col-md-4">
                                <small >Covid Number</small><br>
                                <input type="text" name="covid_number" style="width: 100%;" value="{{ $form->covid_number }}">
                            </div>
                            <div class="col-md-4">
                                <small >Clinical Status</small><br>
                                <select name="clinical_status" class="form-control-select" style="width: 100%;">

                                @if($form->refer_clinical_status == "Asymptomatic")
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    @elseif($form->refer_clinical_status == "Mild")
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    <option value="asymptomatic">Asymptomatic</option>
                                    @elseif($form->refer_clinical_status == "Moderate")
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    @elseif($form->refer_clinical_status == "Severe")
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    @elseif($form->refer_clinical_status == "Critical")
                                    <option value="critical">Critical</option>
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    @else
                                    <option value="">Select option</option>   
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small >Surveillance Category</small><br>
                                <select name="sur_category" class="form-control-select" style="width: 100%;">
                                @if ($form->refer_sur_category == "Contact (PUM)")
                                    <option value="contact_pum">Contact (PUM)</option>
                                    <option value="suspect">Suspect</option>
                                    <option value="probable">Probable</option>
                                    <option value="confirmed">Confirmed</option>
                                @elseif ($form->refer_sur_category == "Suspect")
                                    <option value="suspect">Suspect</option>
                                    <option value="probable">Probable</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="contact_pum">Contact (PUM)</option>
                                @elseif ($form->refer_sur_category == "Probable")
                                    <option value="probable">Probable</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="contact_pum">Contact (PUM)</option>
                                    <option value="suspect">Suspect</option>
                                @elseif ($form->refer_sur_category == "Confirmed")
                                    <option value="confirmed">Confirmed</option>
                                    <option value="contact_pum">Contact (PUM)</option>
                                    <option value="suspect">Suspect</option>
                                    <option value="probable">Probable</option>
                                @else
                                    <option value="">Select option</option>
                                    <option value="contact_pum">Contact (PUM)</option>
                                    <option value="suspect">Suspect</option>
                                    <option value="probable">Probable</option>
                                    <option value="confirmed">Confirmed</option>
                                    @endif
                                </select>
                            </div>
                        </div><br>

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_illness_history_normInfo" aria-expanded="false" aria-controls="collapse_illness_history_normInfo">
                                        <b>HISTORY OF PRESENT ILLNESS</b><i> (required)</i><span class="text-red">*</span>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_illness_history_normInfo" style="width: 100%">
                                    <small class="text-success"><b>CASE SUMMARY:</b></small>
                                    <textarea class="form-control" name="case_summary" style="resize: none;width: 100%;" rows="7" required>{{$form->case_summary}}</textarea><br><br>
                                    <small class="text-success"><b>CHIEF COMPLAINTS:</b></small>
                                    <textarea class="form-control" name="reco_summary" style="resize: none;width: 100%;" rows="7" required>{{$form->reco_summary}}</textarea><br><br>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_diagnosis_normInfo" aria-expanded="false" aria-controls="collapse_diagnosis_normInfo">
                                        <b>DIAGNOSIS</b><i> (required)</i><span class="text-red">*</span>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse " id="collapse_diagnosis_normInfo" style="width: 100%">
                                <div class="row">
                                <div class="col-md-12">
                                    <small class="text-success"><b>DIAGNOSIS</b></small> <span class="text-red">*</span>
                                    <small><b><input id="diag_prompt" style="text-align: center; color: red; border-color: transparent; width:30%;" value="SELECT ICD-10 / OTHER DIAGNOSIS" readonly></b></small><br><br>
                                    <a data-toggle="modal" data-target="#icd-modal" type="button" class="btn btn-sm btn-success" onclick="searchICD10()">
                                        <i class="fa fa-medkit"></i> Add ICD-10
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success add_notes_btn" onclick="addNotesDiagnosis()"><i class="fa fa-plus"></i> Add notes in diagnosis</button>
                                </div>
                            </div><br>

                            <div class="row icd_selected" style="padding-top: 10px;">
                                <div class="col-md-12">
                                    <small class="text-success"><b>ICD-10 Code and Description: </b></small>&emsp;
                                    <button type="button" class="btn btn-xs btn-danger" onclick="clearIcdNormal()">Clear ICD 10</button><br>
                                    <input type="hidden" id="icd_cleared" name="icd_cleared" value="">
                                    <div id="icd_selected" style="padding-top: 5px">
                                        @if(isset($icd))
                                            @foreach($icd as $i)
                                                <span> => {{ $i->description }}</span><br>
                                                <input type="hidden" id="icd_ids" name="icd_ids[]" value="{{ $i->id }}">
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div><br>

                            <div class="row notes_diagnosis" style="padding-top: 10px;">
                                <div class="col-md-12">
                                    <small class="text-success"><b>Notes in Diagnosis: </b></small>&emsp;
                                    <input type="hidden" name="notes_diag_cleared" id="notes_diag_cleared" value="">
                                    <button type="button" class="btn btn-xs btn-info" onclick="clearNotesDiagnosis()"> Clear notes diagnosis</button>
                                    <textarea class="form-control normal_notes_diagnosis" name="diagnosis" style="resize: none;width: 100%;" rows="5">{{ $form->diagnosis }}</textarea>
                                </div>
                            </div><br>

                            <div class="row other_diag" style="padding-top: 10px">
                                <div class="col-md-12">
                                    <small class="text-success"><b>Other Diagnosis: </b></small>&emsp;
                                    <input type="hidden" name="other_diag_cleared" class="other_diag_cleared" value="">
                                    <button type="button" class="btn btn-xs btn-warning" onclick="clearOtherDiagnosis(true)"> Clear other diagnosis</button>
                                    <textarea class="form-control" id="other_diagnosis" name="other_diagnosis" style="resize: none;width: 100%;" rows="5">{{ $form->other_diagnoses }}</textarea>
                                </div>
                            </div><br>
                                </div>
                            </div>
                        </div>

                        <?php
                        $all_data = $all_data ?? (object) [];

                        function isChecked($all_data, $category, $symptom)
                        {
                            if (!isset($all_data->$category) || !is_string($all_data->$category)) {
                                return '';
                            }
                            $safeCategory = htmlspecialchars($all_data->$category, ENT_QUOTES, 'UTF-8');
                            if (stripos($safeCategory, $symptom) !== false) {
                                return 'checked';
                            }
                            return '';
                        }

                        ?>

                        @php
                            $past_array_filter = array_filter([
                                $past_medical_history->commordities ?? null,
                                $past_medical_history->commordities_hyper_year ?? null,
                                $past_medical_history->commordities_diabetes_year ?? null,
                                $past_medical_history->commordities_asthma_year ?? null,
                                $past_medical_history->commordities_others ?? null,
                                $past_medical_history->commordities_cancer ?? null,
                                $past_medical_history->heredofamilial_diseases ?? null,
                                $past_medical_history->heredo_hyper_side ?? null,
                                $past_medical_history->heredo_diab_side ?? null,
                                $past_medical_history->heredo_asthma_side ?? null,
                                $past_medical_history->heredo_cancer_side ?? null,
                                $past_medical_history->heredo_kidney_side ?? null,
                                $past_medical_history->heredo_thyroid_side ?? null,
                                $past_medical_history->heredo_others ?? null,
                                $past_medical_history->allergies ?? null,
                                $past_medical_history->allergy_food_cause ?? null,
                                $past_medical_history->allergy_drugs_cause ?? null,
                                $past_medical_history->allergy_others_cause ?? null,
                                $past_medical_history->previous_hospitalization ?? null,
                            ]);
                        @endphp

                        @if(!empty($past_array_filter))
                            <script>
                                $("#collapse_medical_history").collapse('show');

                                // Ensure button toggle works properly
                                $(".btn[data-target='#collapse_medical_history']").on("click", function () {
                                    $("#collapse_medical_history").collapse("toggle");
                                });                            
                            </script>
                        @endif

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_medical_history" aria-expanded="false" aria-controls="collapse_medical_history">
                                        <b>PAST MEDICAL HISTORY</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>

                                <div class="collapse" id="collapse_medical_history" style="width: 100%;">
                                    <small class="text-success"><b>COMORBIDITIES</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_all_cbox" name="comor_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'Select All'); ?>>
                                                <span>Select All</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_none_cbox" name="comor_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_hyper_cbox" name="comor_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'Hypertension'); ?>>
                                                Hypertension
                                                <span id="comor_hyper"> since
                                                    <select class="form-control select" name="hyper_year" style="font-size: 10pt;">
                                                        <option value="">Select Option</option>
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option " . ($year == htmlspecialchars($past_medical_history->commordities_hyper_year) ? 'selected' : '') . ">$year</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                            <div class="col-md-4">                                             
                                                <input class="form-check-input" id="comor_diab_cbox" name="comor_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'Diabetes'); ?>>
                                                Diabetes Mellitus
                                                <span id="comor_diab"> since
                                                    <select class="form-control select" name="diab_year" style="font-size: 10pt;">
                                                        <option value="">Select Option</option>
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option " . ($year == htmlspecialchars($past_medical_history->commordities_diabetes_year) ? 'selected' : '') . ">$year</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_asthma_cbox" name="comor_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'Asthma'); ?>>
                                                Bronchial Asthma
                                                <span id="comor_asthma"> since
                                                    <select class="form-control select" name="asthma_year" style="font-size: 10pt;">
                                                        <option value="">Select Option</option>
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option " . ($year == htmlspecialchars($past_medical_history->commordities_asthma_year) ? 'selected' : '') . ">$year</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_copd_cbox" name="comor_copd_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'COPD'); ?>>
                                                <span> COPD</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_dyslip_cbox" name="comor_dyslip_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'Dyslipidemia'); ?>>
                                                <span> Dyslipidemia</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_thyroid_cbox" name="comor_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'Thyroid Disease'); ?>>
                                                <span> Thyroid Disease</span>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_cancer_cbox" name="comor_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'Cancer'); ?>>
                                                <span>Cancer <i>(specify)</i>:</span>
                                                <textarea class="form-control" name="comor_cancer" id="comor_cancer" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($past_medical_history->commordities_cancer); ?></textarea>
                                            </div>

                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_others_cbox" name="comor_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'commordities', 'Others'); ?>>
                                                <span>Other(s):</span>
                                                <textarea class="form-control" name="comor_others" id="comor_others" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($past_medical_history->commordities_others); ?></textarea>
                                            </div>
                                        </div>


                                        </div><br>

                                        <small class="text-success"><b>ALLERGIES</b></small><i> (Specify)</i><br>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input class="form-check-input" id="allergy_all_cbox" name="allergy_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'allergies', 'Select All'); ?>>
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-check-input" id="allergy_none_cbox" name="allergy_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'allergies', 'None'); ?>>
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input class="form-check-input" id="allergy_food_cbox" name="allergy_food_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'allergies', 'Food'); ?>>
                                                    <span> Food(s): <i>(ex. crustaceans, eggs)</i></span>
                                                    <textarea class="form-control" id="allergy_food" name="allergy_food_cause" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($past_medical_history->allergy_food_cause); ?></textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-check-input" id="allergy_drug_cbox" name="allergy_drug_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'allergies', 'Drugs'); ?>>
                                                    <span> Drug(s): <i>(ex. Ibuprofen, NSAIDS)</i></span>
                                                    <textarea class="form-control" id="allergy_drug" name="allergy_drug_cause" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($past_medical_history->allergy_drugs_cause); ?></textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-check-input" id="allergy_other_cbox" name="allergy_other_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'allergies', 'Others'); ?>>
                                                    <span> Other(s):</span>
                                                    <textarea class="form-control" id="allergy_other" name="allergy_other_cause" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($past_medical_history->allergy_others_cause); ?></textarea>
                                                </div>
                                            </div>
                                        </div><br>


                                        <small class="text-success"><b>HEREDOFAMILIAL DISEASES</b></small> <i>(Specify which side of the family: maternal, paternal, both)</i>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_all_cbox" name="heredo_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'Select All'); ?>>
                                                    <span>Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_none_cbox" name="heredo_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'None'); ?>>
                                                    <span>None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_hyper_cbox" name="heredo_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'Hypertension'); ?>>
                                                    <span>Hypertension:</span>
                                                    <select class="form-control-select" id="heredo_hyper" name="heredo_hypertension_side">
                                                        <option value="">Select Option</option>
                                                        <option value="Maternal" <?php echo ($past_medical_history->heredo_hyper_side === 'Maternal') ? 'selected' : ''; ?>>Maternal</option>
                                                        <option value="Paternal" <?php echo ($past_medical_history->heredo_hyper_side === 'Paternal') ? 'selected' : ''; ?>>Paternal</option>
                                                        <option value="Both" <?php echo ($past_medical_history->heredo_hyper_side === 'Both') ? 'selected' : ''; ?>>Both</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_diab_cbox" name="heredo_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'Diabetes'); ?>>
                                                    <span>Diabetes Mellitus:</span>
                                                    <select class="form-control-select" id="heredo_diab" name="heredo_diabetes_side">
                                                        <option value="">Select Option</option>
                                                        <option value="Maternal" <?php echo ($past_medical_history->heredo_diab_side === 'Maternal') ? 'selected' : ''; ?>>Maternal</option>
                                                        <option value="Paternal" <?php echo ($past_medical_history->heredo_diab_side === 'Paternal') ? 'selected' : ''; ?>>Paternal</option>
                                                        <option value="Both" <?php echo ($past_medical_history->heredo_diab_side === 'Both') ? 'selected' : ''; ?>>Both</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_asthma_cbox" name="heredo_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'Asthma'); ?>>
                                                    <span>Bronchial Asthma:</span>
                                                    <select class="form-control-select" id="heredo_asthma" name="heredo_asthma_side">
                                                        <option value="">Select Option</option>
                                                        <option value="Maternal" <?php echo ($past_medical_history->heredo_asthma_side === 'Maternal') ? 'selected' : ''; ?>>Maternal</option>
                                                        <option value="Paternal" <?php echo ($past_medical_history->heredo_asthma_side === 'Paternal') ? 'selected' : ''; ?>>Paternal</option>
                                                        <option value="Both" <?php echo ($past_medical_history->heredo_asthma_side === 'Both') ? 'selected' : ''; ?>>Both</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_cancer_cbox" name="heredo_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'Cancer'); ?>>
                                                    <span>Cancer:</span>
                                                    <select class="form-control-select" id="heredo_cancer" name="heredo_cancer_side">
                                                        <option value="">Select Option</option>
                                                        <option value="Maternal" <?php echo ($past_medical_history->heredo_cancer_side === 'Maternal') ? 'selected' : ''; ?>>Maternal</option>
                                                        <option value="Paternal" <?php echo ($past_medical_history->heredo_cancer_side === 'Paternal') ? 'selected' : ''; ?>>Paternal</option>
                                                        <option value="Both" <?php echo ($past_medical_history->heredo_cancer_side === 'Both') ? 'selected' : ''; ?>>Both</option>
                                                    </select>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_kidney_cbox" name="heredo_kidney_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'Kidney Disease'); ?>>
                                                    <span>Kidney:</span>
                                                    <select class="form-control-select" id="heredo_kidney" name="heredo_kidney_side">
                                                        <option value="">Select Option</option>
                                                        <option value="Maternal" <?php echo ($past_medical_history->heredo_kidney_side === 'Maternal') ? 'selected' : ''; ?>>Maternal</option>
                                                        <option value="Paternal" <?php echo ($past_medical_history->heredo_kidney_side === 'Paternal') ? 'selected' : ''; ?>>Paternal</option>
                                                        <option value="Both" <?php echo ($past_medical_history->heredo_kidney_side === 'Both') ? 'selected' : ''; ?>>Both</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_thyroid_cbox" name="heredo_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'Thyroid Disease'); ?>>
                                                    <span>Thyroid Disease:</span>
                                                    <select class="form-control-select" id="heredo_thyroid" name="heredo_thyroid_side">
                                                        <option value="">Select Option</option>
                                                        <option value="Maternal" <?php echo ($past_medical_history->heredo_kidney_side === 'Maternal') ? 'selected' : ''; ?>>Maternal</option>
                                                        <option value="Paternal" <?php echo ($past_medical_history->heredo_kidney_side === 'Paternal') ? 'selected' : ''; ?>>Paternal</option>
                                                        <option value="Both" <?php echo ($past_medical_history->heredo_kidney_side === 'Both') ? 'selected' : ''; ?>>Both</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_others_cbox" name="heredo_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes" <?= isChecked($past_medical_history, 'heredofamilial_diseases', 'Others'); ?>>
                                                    <span>Other(s):</span>
                                                    <input type="text" id="heredo_others" name="heredo_others_side" value="<?php echo htmlspecialchars($past_medical_history->heredo_others); ?>">
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>PREVIOUS HOSPITALIZATION(S) and OPERATION(S)</b></small><br>
                                        <textarea class="form-control" name="previous_hospitalization" style="resize: none;width: 100%;" rows="3">{{ $past_medical_history->previous_hospitalization }}</textarea><br><br>
                                </div>
                                </div>
                            </div>
                      


                        @if($patient_age <=18)
                        <div class="row" style="margin: 5px;" id="pedia_show">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_pedia_history" aria-expanded="false" aria-controls="collapse_pedia_history">
                                        <div class="web-view"><b>PEDIATRIC HISTORY</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view"><b>PEDIATRIC HISTORY</b><br> <i> (as applicable)</i></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_pedia_history" style="width: 100%;">
                                    <small class="text-success"><b>PRENATAL</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>A <input type="number" name="prenatal_age" style="width: 8%" min="0" value="{{$data->prenatal_a}}">
                                                    year old G <input type="number" name="prenatal_g" style="width: 8%" min="0" value="{{$data->prenatal_g}}"> P <input type="number" style="width: 8%" min="0" name="prenatal_p" value="{{$pediatric_history->prenatal_p}}"> mother
                                                    <input class="referral-radio-btn" name="prenatal_radiowith_or_without" type="radio" id="prenatal_radiowith" value="with" <?= isChecked($pediatric_history, 'prenatal_radiowith_or_without', 'with'); ?>> with
                                                    <input class="referral-radio-btn" name="prenatal_radiowith_or_without" type="radio" id="prenatal_radiowout" value="without" <?= isChecked($pediatric_history, 'prenatal_radiowith_or_without', 'without'); ?>> without
                                                    MATERNAL ILLNESS, if Yes (specify)
                                                    <textarea class="form-control" id="prenatal_mat_illness" name="prenatal_maternal_illness" style="resize: none;width: 50%;" rows="2">{{$pediatric_history->prenatal_with_maternal_illness}}</textarea>
                                                </span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>NATAL</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>Born at
                                                    <select class="form-control-select" name="natal_bornat">
                                                        <option value="">Select Option</option>
                                                        <option value="home" <?php echo ($pediatric_history->natal_born_at == 'home') ? 'selected' : ''; ?>>Home</option>
                                                        <option value="clinic" <?php echo ($pediatric_history->natal_born_at == 'clinic') ? 'selected' : ''; ?>>Clinic</option>
                                                        <option value="hospital" <?php echo ($pediatric_history->natal_born_at == 'hospital') ? 'selected' : ''; ?>>Hospital</option>
                                                    </select>
                                                    <input type="text" id="hospital_name" name="natal_born_address" style="width: 30%;" placeholder="Specify where" value="{{ $data->natal_born_address }}">
                                                    by
                                                    <select class="form-control-select" name="natal_by">
                                                        <option value="">Select Option</option>
                                                        <option value="md" <?php echo ($pediatric_history->natal_by == 'md') ? 'selected' : ''; ?>>MD</option>
                                                        <option value="midwife" <?php echo ($pediatric_history->natal_by == 'midwife') ? 'selected' : ''; ?>>Midwife</option>
                                                        <option value="hilot" <?php echo ($pediatric_history->natal_by == 'hilot') ? 'selected' : ''; ?>>Hilot</option>
                                                    </select>
                                                    via
                                                    <select class="form-control-select" name="natal_via">
                                                        <option value="">Select Option</option>
                                                        <option value="nsd" <?php echo ($pediatric_history->natal_via == 'nsd') ? 'selected' : ''; ?>>NSD</option>
                                                        <option value="cs" <?php echo ($pediatric_history->natal_via == 'cs') ? 'selected' : ''; ?>>CS</option>
                                                    </select>
                                                    (indication)
                                                    <input type="text" id="cs_indication" name="cs_indication" style="width: 20%;" value="{{ $data->natal_indication }}">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>
                                                    <select class="form-control-select" name="natal_term">
                                                        <option value="">Select Option</option>
                                                        <option value="preterm" <?php echo ($pediatric_history->natal_term == 'preterm') ? 'selected' : ''; ?>>Preterm</option>
                                                        <option value="fullterm" <?php echo ($pediatric_history->natal_term == 'fullterm') ? 'selected' : ''; ?>>Full Term</option>
                                                        <option value="postterm" <?php echo ($pediatric_history->natal_term == 'postterm') ? 'selected' : ''; ?>>Post Term</option>
                                                    </select>
                                                    , weighing <input type="number" name="natal_weight" style="width: 8%" min="0" step="0.01" value="{{$data->natal_weight}}"> kg,
                                                    BR <input type="text" name="natal_br" style="width: 20%" value="{{$data->natal_br}}">, with Good Cry
                                                    <select class="form-control-select" name="natal_withGoodCry">
                                                        <option value="">Select Option</option>
                                                        <option value="1" <?php echo ($pediatric_history->natal_with_good_cry == 1) ? 'selected' : ''; ?>>Yes</option>
                                                        <option value="0" <?php echo ($pediatric_history->natal_with_good_cry == 0) ? 'selected' : ''; ?>>No</option>
                                                    </select><br>
                                                    Other complications:
                                                    <textarea class="form-control" name="natal_complications" style="resize: none;width: 30%;" rows="2">{{ $data->natal_other_complications }}</textarea>
                                                </span>
                                            </div>
                                        </div>
                                    </div><br>
                                    <?php
                                    $postnatal_bfeed_xmos_value = htmlspecialchars($pediatric_history['postnatal_bfeed_xmos']);
                                    $postnatal_ffeed_specify_value = htmlspecialchars($pediatric_history['postnatal_ffeed_specify']);
                                    $postnatal_started_semisolidfood_at_value = htmlspecialchars($pediatric_history['postnatal_started_semisolidfood_at']);
                                    $immu_dpt_doses_value = htmlspecialchars($pediatric_history['immu_dpt_doses']);
                                    $immu_hepb_doses_value = htmlspecialchars($pediatric_history['immu_hepb_doses']);
                                    $immu_others_value = htmlspecialchars($pediatric_history['immu_others']);
                                    ?>
                                                  
                                    <small class="text-success"><b>POST NATAL</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Feeding History</i><br>&emsp;
                                                <span>
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="postnatal_bfeed" name="postnatal_bfeed" value="Yes" <?= isChecked($pediatric_history, 'post_natal_bfeed', 'Yes'); ?>> Breastfed
                                                    <span id="breastfed">
                                                        x <input type="number" name="postnatal_bfeed_xmos" style="width: 7%;" min="0" value="<?php echo $postnatal_bfeed_xmos_value; ?>"> mos.
                                                    </span>
                                               
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" id="postnatal_ffeed" type="checkbox" name="postnatal_ffeed" value="Yes" <?= isChecked($pediatric_history, 'post_natal_formula_feed', 'Yes'); ?>> Formula Fed,
                                                    <span id="formula_fed">
                                                        (specify) <input type="text" style="width: 15%" name="postnatal_ffeed_specify" value="<?php echo $postnatal_ffeed_specify_value; ?>">
                                                    </span>
                                                </span>
                                                started semi solid foods at
                                                <input type="number" name="postnatal_started_semisolidfood_at" style="width: 10%" min="0" value="<?php echo $postnatal_started_semisolidfood_at_value; ?>"> mos
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Immunization History</i><br>&emsp;
                                        
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_bcg_cbox" value="Yes" <?= isChecked($pediatric_history, 'post_natal_bcg', 'Yes'); ?>> BCG
                                             
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_dpt_cbox" name="immu_dpt_cbox" value="Yes" <?= isChecked($pediatric_history, 'post_natal_dpt_opv_x', 'Yes'); ?>> DPT/OPV
                                                <span id="immu_dpt">
                                                    x <input type="number" name="immu_dpt_doses" style="width: 7%;" min="0" value="<?php echo $immu_dpt_doses_value; ?>"> doses
                                                </span>
                                           
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_hepb_cbox" name="immu_hepb_cbox" value="Yes" <?= isChecked($pediatric_history, 'post_natal_hepB_cbox', 'Yes'); ?>> Hep B
                                                <span id="immu_hepb">
                                                    x <input type="number" name="immu_hepb_doses" style="width: 7%;" min="0" value="<?php echo $immu_hepb_doses_value; ?>"> doses
                                                </span>
                                     
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_measles_cbox" value="Yes" <?= isChecked($pediatric_history, 'post_natal_immu_measles_cbox', 'Yes'); ?>> Measles
                                             
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_mmr_cbox" value="Yes" <?= isChecked($pediatric_history, 'post_natal_mmr_cbox', 'Yes'); ?>> MMR
                                            
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_others_cbox" name="immu_others_cbox" value="Yes" <?= isChecked($pediatric_history, 'post_natal_others_cbox', 'Yes'); ?>> Other(s)
                                                <span id="immu_others">
                                                    <input type="text" name="immu_others" style="width: 20%;" value="<?php echo $immu_others_value; ?>">
                                                </span>
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Developmental Milestones</i><br>&emsp;
                                                <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_under" value="Under developed" <?= isChecked($pediatric_history, 'post_natal_development_milestones', 'Under developed'); ?>>
                                                <label for="dev_miles_under">Under developed</label>
                                                <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_par" value="At par with age" <?= isChecked($pediatric_history, 'post_natal_development_milestones', 'At par with age'); ?>>
                                                <label for="dev_miles_par">At par with age</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <?php

                            $menarche = $obstetric_and_gynecologic_history->menarche;
                            $gynecological_data = [
                                'menarche' => (int) $menarche,
                                'menopausal_age' => $obstetric_and_gynecologic_history->menopausal_age,
                                'mens_irreg_xmos' => $obstetric_and_gynecologic_history->mens_irreg_xmos,
                                'menstrual_cycle_duration' => $obstetric_and_gynecologic_history->menstrual_cycle_duration,
                                'menstrual_cycle_padsperday' => $obstetric_and_gynecologic_history->menstrual_cycle_padsperday,
                                'menstrual_cycle_medication' => $obstetric_and_gynecologic_history->menstrual_cycle_medication,
                                'contraceptive_others' => $obstetric_and_gynecologic_history->contraceptive_others,
                                'parity_g' => $obstetric_and_gynecologic_history->parity_g,
                                'parity_p' => $obstetric_and_gynecologic_history->parity_p,
                                'parity_ft' => $obstetric_and_gynecologic_history->parity_ft,
                                'parity_pt' => $obstetric_and_gynecologic_history->parity_pt,
                                'parity_a' => $obstetric_and_gynecologic_history->parity_a,
                                'parity_l' => $obstetric_and_gynecologic_history->parity_l,
                                'parity_lnmp' => $obstetric_and_gynecologic_history->parity_lnmp,
                                'parity_edc' => $obstetric_and_gynecologic_history->parity_edc,
                                'aog_lnmp' => $obstetric_and_gynecologic_history->aog_lnmp,
                                'aog_eutz' => $obstetric_and_gynecologic_history->aog_eutz,
                            ];

                            $menarche_value = htmlspecialchars($gynecological_data['menarche']);
                            $menopausal_age_value = htmlspecialchars($gynecological_data['menopausal_age']);
                            $mens_irreg_xmos_value = htmlspecialchars($gynecological_data['mens_irreg_xmos']);
                            $menstrual_cycle_duration_value = htmlspecialchars($gynecological_data['menstrual_cycle_duration']);
                            $menstrual_cycle_padsperday_value = htmlspecialchars($gynecological_data['menstrual_cycle_padsperday']);
                            $menstrual_cycle_medication_value = htmlspecialchars($gynecological_data['menstrual_cycle_medication']);
                            $contraceptive_others_value = htmlspecialchars($gynecological_data['contraceptive_others']);
                            $parity_g_value = htmlspecialchars($gynecological_data['parity_g']);
                            $parity_p_value = htmlspecialchars($gynecological_data['parity_p']);
                            $parity_pt_value = htmlspecialchars($gynecological_data['parity_pt']);
                            $parity_a_value = htmlspecialchars($gynecological_data['parity_a']);
                            $parity_l_value = htmlspecialchars($gynecological_data['parity_l']);
                            $parity_lnmp_value = htmlspecialchars($gynecological_data['parity_lnmp']);
                            $parity_edc_value = htmlspecialchars($gynecological_data['parity_edc']);
                            $aog_lnmp_value = htmlspecialchars($gynecological_data['aog_lnmp']);
                            $aog_eutz_value = htmlspecialchars($gynecological_data['aog_eutz']);
                            ?>

                        {{--TODO: COMPARE AGE IF >= 9 AND ONLY IF PT IS WOMAN--}}
                            
                        @php
                            $obstetric_array_filter = array_filter([
                                $obstetric_and_gynecologic_history->menarche ?? null,
                                $obstetric_and_gynecologic_history->menopause ?? null,
                                $obstetric_and_gynecologic_history->menopausal_age ?? null,
                                $obstetric_and_gynecologic_history->menstrual_cycle ?? null,
                                $obstetric_and_gynecologic_history->mens_irreg_xmos ?? null,
                                $obstetric_and_gynecologic_history->menstrual_cycle_dysmenorrhea ?? null,
                                $obstetric_and_gynecologic_history->menstrual_cycle_duration ?? null,
                                $obstetric_and_gynecologic_history->menstrual_cycle_padsperday ?? null,
                                $obstetric_and_gynecologic_history->menstrual_cycle_medication ?? null,
                                $obstetric_and_gynecologic_history->contraceptive_history ?? "",
                                $obstetric_and_gynecologic_history->contraceptive_others ?? null,
                                $obstetric_and_gynecologic_history->parity_g ?? null,
                                $obstetric_and_gynecologic_history->parity_p ?? null,
                                $obstetric_and_gynecologic_history->parity_ft ?? null,
                                $obstetric_and_gynecologic_history->parity_pt ?? null,
                                $obstetric_and_gynecologic_history->parity_a ?? null,
                                $obstetric_and_gynecologic_history->parity_l ?? null,
                                $obstetric_and_gynecologic_history->parity_lnmp ?? null,
                                $obstetric_and_gynecologic_history->parity_edc ?? null,
                                $obstetric_and_gynecologic_history->aog_lnmp ?? null,
                                $obstetric_and_gynecologic_history->aog_eutz ?? null,
                                $obstetric_and_gynecologic_history->prenatal_history ?? null,
                            ]);
                        @endphp


                        @if(!empty($obstetric_array_filter))
                            <script>
                                $("#collapse_gyne_history").collapse('show');

                                // Ensure button toggle works properly
                                $(".btn[data-target='#collapse_gyne_history']").on("click", function () {
                                    $("#collapse_gyne_history").collapse("toggle");
                                });                            
                            </script>
                        @endif
                
                        @if ($patient_age >= 9 && $form->patient_sex === "Female" && $age_type==="y")
                        <div class="row" style="margin: 5px;" id="menarche_show">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_gyne_history" aria-expanded="false" aria-controls="collapse_gyne_history">
                                        <div class="web-view"><b>OBSTETRIC AND GYNECOLOGIC HISTORY</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view">
                                            <b>OBSTETRIC AND GYNECOLOGIC<br> HISTORY</b><br> <i> (as applicable)</i></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_gyne_history" style="width: 100%;">
                                    <small class="text-success"><b>MENARCHE</b></small> @ <input type="number" style="width: 10%;" name="menarche" value="<?php echo $menarche_value; ?>"> years old &emsp;&emsp;&emsp;&emsp;
                                    <small class="text-success"><b>MENOPAUSE:</b></small> &emsp;
                                    <input type="radio" class="referral-radio-btn" name="menopausal" id="menopausal" value="Yes" <?= isChecked($obstetric_and_gynecologic_history, 'menopause', 'Yes'); ?>>
                                    <label for="menopausal">Yes</label>
                                    <input type="radio" class="referral-radio-btn" name="menopausal" id="non_menopausal" value="No" <?= isChecked($obstetric_and_gynecologic_history, 'menopause', 'No'); ?>>
                                    <label for="non_menopausal">No</label>
                                    <span id="menopausal_age">(age) <input type="number" name="menopausal_age" style="width: 10%;" min="9" value="<?php echo $menopausal_age_value; ?>"></span><br><br>

                                    <small class="text-success"><b>MENSTRUAL CYCLE</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_reg_radio" value="regular" <?= isChecked($obstetric_and_gynecologic_history, 'menstrual_cycle', 'regular'); ?>>
                                                    Regular
                                                <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_cycle_irreg" value="irregular" <?= isChecked($obstetric_and_gynecologic_history, 'menstrual_cycle', 'irregular'); ?>>
                                                    Irregular
                                                <span id="mens_irreg">x <input type="number" name="mens_irreg_xmos" style="width: 15%;" min="0" value="<?php echo $mens_irreg_xmos_value; ?>"> mos</span>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-success">Dysmenorrhea:</small> &emsp;
                                                <input type="radio" class="referral-radio-btn" name="dysme" id="dysme_yes" value="Yes" <?= isChecked($obstetric_and_gynecologic_history, 'menstrual_cycle_dysmenorrhea', 'Yes'); ?>>
                                                <label for="dysmenorrhea_yes">Yes</label>
                                                <input type="radio" class="referral-radio-btn" name="dysme" id="dysme_no" value="No" <?= isChecked($obstetric_and_gynecologic_history, 'menstrual_cycle_dysmenorrhea', 'No'); ?>>
                                                <label for="dysmenorrhea_no">No</label><br>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-success">Duration:</small> <input type="number" style="width:15%;" min="0" name="mens_duration" value="<?php echo $menstrual_cycle_duration_value; ?>"> days &emsp;
                                                <small class="text-success">Pads/day:</small> <input type="number" style="width:15%;" min="0" name="mens_padsperday" value="<?php echo $menstrual_cycle_padsperday_value; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-success">Medication:</small> <input type="text" style="width:70%;" name="mens_medication" value="<?php echo $menstrual_cycle_medication_value; ?>">
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>CONTRACEPTIVE HISTORY</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_none" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_none_cbox" value="none" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'none'); ?>> None
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_pills" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_pills_cbox" value="Pills" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'Pills'); ?>> Pills
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_iud" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_iud_cbox" value="IUD" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'IUD'); ?>> IUD
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_rhythm" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_rhythm_cbox" value="Rhythm" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'Rhythm'); ?>> Rhythm / Calendar
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_condom" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_condom_cbox" value="Condom" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'Condom'); ?>> Condom
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_withdrawal_normal_info_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_withdrawal_cbox" value="Yes" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'Withdrawal'); ?>> Withdrawal
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_injections_normal_info_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_injections_cbox" value="Yes" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'Injections'); ?>> Injections
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input class="form-check-input" id="contraceptive_others" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_other_cbox" value="Other(s)" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'Other'); ?>> Other(s)
                                                <textarea class="form-control" id="contraceptive_others_text" name="contraceptive_others" style="resize: none;width: 50%;" rows="2"><?php echo htmlspecialchars($contraceptive_others_value); ?></textarea><br>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>PARITY</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <small class="text-success">G</small> <input type="number" min="0" style="width:8%;" name="parity_g" value="<?php echo $parity_g_value; ?>">
                                                <small class="text-success">P</small> <input type="number" min="0" style="width:8%;" name="parity_p" value="<?php echo $parity_p_value; ?>">&emsp;
                                                <small class="text-success">(FT</small> <input type="text" style="width:8%;" name="parity_ft" value="{{$data->parity_ft}}">
                                                <small class="text-success">PT</small> <input type="text" style="width:8%;" name="parity_pt" value="<?php echo $parity_pt_value; ?>">
                                                <small class="text-success">A</small> <input type="text" style="width:8%;" name="parity_a" value="<?php echo $parity_a_value; ?>">
                                                <small class="text-success">L</small> <input type="text" style="width:8%;" name="parity_l" value="<?php echo $parity_l_value; ?>"><small class="text-success">)</small>
                                            </div>
                                        </div>
                                    </div><br>

                                    <div class="container-referral">
                                        <small class="text-success">LMP</small>
                                        <input type="text" class="form-control form_datetime" min="0" name="parity_lnmp" value="<?php echo $parity_lnmp_value; ?>" placeholder="Date/Time">
                                        <small class="text-success">EDC</small><i>(if pregnant)</i>
                                        <input type="text" class="form-control form_datetime" min="0" name="parity_edc_ifpregnant" value="<?php echo $parity_edc_value; ?>" placeholder="Date/Time">
                                    </div><br>

                                    <small class="text-success"><b>AOG</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-success">by LMP</small> <input type="number" min="0" style="width:25%;" name="aog_bylnmp" value="<?php echo $aog_lnmp_value; ?>"> <small class="text-success">wks</small>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-success">by UTZ</small> <input type="number" min="0" style="width:25%;" name="aog_byEUTZ" value="<?php echo $aog_eutz_value; ?>"> <small class="text-success">wks</small>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>PRENATAL HISTORY</b></small><br>
                                    <textarea class="form-control" name="prenatal_history" style="resize: none;width: 100%;" rows="4"><?php echo $obstetric_and_gynecologic_history->prenatal_history; ?></textarea><br><br>
                                    <div class="table-responsive" style="overflow-x: auto;">
                                        <table class="table table-bordered" id="prenatal_table">
                                            <thead>
                                                <tr style="font-size: 10pt;">
                                                    <th class="text-center">Pregnancy Order</th>
                                                    <th class="text-center">Year of Birth</th>
                                                    <th class="text-center">Gestation Completed</th>
                                                    <th class="text-center">Pregnancy Outcome</th>
                                                    <th class="text-center">Place of Birth</th>
                                                    <th class="text-center">Biological Sex</th>
                                                    <th class="text-center">Birth Weight</th>
                                                    <th class="text-center">Present Status</th>
                                                    <th class="text-center">Complication(s)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pregnancy as $preg)
                                                <tr style="font-size: 10pt;">
                                                    <td>
                                                        <input class="form-control" type="text" name="pregnancy_history_order[]" value="{{ $preg['pregnancy_order'] }}">
                                                    </td>
                                                    <td>
                                                        <select class="form-control select" name="pregnancy_history_year[]">
                                                            <option value="">Choose...</option>
                                                            @foreach(range(date('Y'), 1950) as $year)
                                                            <option value="{{ $year }}" {{ $year == $preg['pregnancy_year'] ? 'selected' : '' }}>{{ $year }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="form-control" id="gestation_normal_info" type="text" name="pregnancy_history_gestation[]" value="{{ $preg['pregnancy_gestation_completed'] }}">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" name="pregnancy_history_outcome[]" value="{{ $preg['pregnancy_outcome'] }}">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" name="pregnancy_history_placeofbirth[]" value="{{ $preg['pregnancy_place_of_birth'] }}">
                                                    </td>
                                                    <td>
                                                        <select class="select form-control" name="prenatal_history_sex[]">
                                                            <option value="">Choose...</option>
                                                            <option value="M" {{ $preg['pregnancy_sex'] == 'M' ? 'selected' : '' }}>Male</option>
                                                            <option value="F" {{ $preg['pregnancy_sex'] == 'F' ? 'selected' : '' }}>Female</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" name="pregnancy_history_birthweight[]" value="{{ $preg['pregnancy_birth_weight'] }}">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" name="pregnancy_history_presentstatus[]" value="{{ $preg['pregnancy_present_status'] }}">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" name="pregnancy_history_complications[]" value="{{ $preg['pregnancy_complication'] }}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <button class="btn-sm btn-success" id="prenatal_add_row" type="button">
                                            <i class="fa fa-plus"> Add Row</i>
                                        </button><br><br>
                                    </div>

                                </div>
                            </div>
                        </div>
                       @endif

                       @php
                         $personal_history_array_filter = array_filter([
                            $personal_and_social_history->smoking ?? null,
                            $personal_and_social_history->smoking_sticks_per_day ?? null,
                            $personal_and_social_history->smoking_quit_year ?? null,
                            $personal_and_social_history->smoking_remarks ?? null,
                            $personal_and_social_history->alcohol_drinking ?? null,
                            $personal_and_social_history->alcohol_liquor_type ?? null,
                            $personal_and_social_history->alcohol_bottles_per_day ?? null,
                            $personal_and_social_history->alcohol_drinking_quit_year ?? null,
                            $personal_and_social_history->illicit_drugs ?? null,
                            $personal_and_social_history->illicit_drugs_taken ?? null,
                            $personal_and_social_history->illicit_drugs_quit_year ?? null
                         ]);
                       @endphp

            
                       @if(!empty($personal_history_array_filter))
                            <script>
                                $("#collapse_personal_history").collapse('show');

                                // Ensure button toggle works properly
                                $(".btn[data-target='#collapse_personal_history']").on("click", function () {
                                    $("#collapse_personal_history").collapse("toggle");
                                });                            
                            </script>
                        @endif

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_personal_history" aria-expanded="false" aria-controls="collapse_personal_history">
                                        <div class="web-view"><b>PERSONAL and SOCIAL HISTORY</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view"><b>PERSONAL and SOCIAL HISTORY</b><br> <i> (as applicable)</i></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_personal_history" style="width: 100%;">
                                    <small class="text-success"><b>SMOKING</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_yes" value="Yes" <?= isChecked($personal_and_social_history, 'smoking', 'Yes'); ?>>
                                                <label for="smoke_yes">Yes</label><br>
                                                <span id="smoking_sticks">Sticks per day: <input type="number" min="0" style="width:30%;" name="smoking_sticks_per_day" value="<?php echo htmlspecialchars($personal_and_social_history['smoking_sticks_per_day']); ?>"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_no" value="No" <?= isChecked($personal_and_social_history, 'smoking', 'No'); ?>>
                                                <label for="smoke_no">No</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_quit" value="Yes" <?= isChecked($personal_and_social_history, 'smoking', 'Quit'); ?>>
                                                <label for="smoke_quit">Quit</label>
                                                <span id="smoking_quit_year"> since
                                                    <select class="form-control select" name="smoking_year_quit">
                                                        <option value="">Select Option</option>
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option " . ($year == htmlspecialchars($personal_and_social_history->smoking_quit_year) ? 'selected' : '') . ">$year</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <span>Other Remarks: <textarea class="form-control" style="resize: none;width:50%;" rows="2" name="smoking_other_remarks"><?php echo htmlspecialchars($personal_and_social_history['smoking_remarks']); ?></textarea></span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>ALCOHOL DRINKING</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_yes_radio" value="Yes" <?= isChecked($personal_and_social_history, 'alcohol_drinking', 'Yes'); ?>>
                                                <label for="alcohol_yes_radio">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_no_radio" value="No" <?= isChecked($personal_and_social_history, 'alcohol_drinking', 'No'); ?>>
                                                <label for="alcohol_no_radio">No</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_quit_radio" value="Quit" <?= isChecked($personal_and_social_history, 'alcohol_drinking', 'Quit'); ?>>
                                                <label for="alcohol_quit_radio">Quit</label>
                                                <span id="alcohol_quit_year"> since
                                                    <select class="form-control select" name="alcohol_year_quit">
                                                        <option value="">Select Option</option>
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option " . ($year == htmlspecialchars($personal_and_social_history->alcohol_drinking_quit_year) ? 'selected' : '') . ">$year</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span id="alcohol_type">Liquor Type: <textarea class="form-control" style="resize: none;" rows="2" name="alcohol_type"><?php echo htmlspecialchars($personal_and_social_history->alcohol_liquor_type); ?></textarea></span>
                                            </div>
                                            <div class="col-md-4">
                                                <span id="alcohol_bottles">Bottles per day: <input type="number" min="0" style="width:25%;" name="alcohol_bottles" value="<?php echo htmlspecialchars($personal_and_social_history->alcohol_bottles_per_day); ?>"></span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>ILLICIT DRUGS</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="radio" name="illicit_drugs" id="drugs_yes_radio" class="referral-radio-btn" value="Yes" <?= isChecked($personal_and_social_history, 'illicit_drugs', 'Yes'); ?>>
                                                <label for="drugs_yes_radio">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" name="illicit_drugs" id="drugs_no_radio" class="referral-radio-btn" value="No" <?= isChecked($personal_and_social_history, 'illicit_drugs', 'No'); ?>>
                                                <label for="drugs_no_radio">No</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" name="illicit_drugs" id="drugs_quit_radio" class="referral-radio-btn" value="Quit" <?= isChecked($personal_and_social_history, 'illicit_drugs', 'Quit'); ?>>
                                                <label for="drugs_quit_radio">Quit</label>
                                                <span id="drugs_quit_year"> since
                                                    <select class="form-control select" name="drugs_year_quit">
                                                        <option value="">Select Option</option>
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option " . ($year == htmlspecialchars($personal_and_social_history->illicit_drugs_quit_year) ? 'selected' : '') . ">$year</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8" id="drugs_text">
                                               <small class="text-success"> Specify drugs taken:</small>
                                                <textarea class="form-control" rows="2" style="resize: none;width:50%;" name="illicit_drugs_token"><?php echo htmlspecialchars($personal_and_social_history->illicit_drugs_taken); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_medication" aria-expanded="false" aria-controls="collapse_medication">
                                        <b>CURRENT MEDICATION(S)</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_medication" style="width: 100%;">
                                   <small class="text-success"> <i>Specify number of doses given and time of last dose given.</i></small>
                                    <textarea class="form-control" name="current_meds" style="resize: none;width: 100%;" rows="5" required><?php echo htmlspecialchars($personal_and_social_history->current_medications); ?></textarea><br><br>
                                </div>
                            </div>
                        </div>

                        @php
                            $pertinent_array_filter = array_filter([
                                $pertinent_laboratory->pertinent_laboratory_and_procedures ?? null,
                                $pertinent_laboratory->lab_procedure_other ?? null,
                            ]);
                        @endphp

        

                        @if(!empty($pertinent_array_filter))
                            <script>
                                $("#collapse_lab_procedures").collapse('show');

                                // Ensure button toggle works properly
                                $(".btn[data-target='#collapse_lab_procedures']").on("click", function () {
                                    $("#collapse_lab_procedures").collapse("toggle");
                                });                            
                            </script>
                        @endif

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_lab_procedures" aria-expanded="false" aria-controls="collapse_lab_procedures">
                                        <div class="web-view">
                                            <b>PERTINENT LABORATORY and OTHER ANCILLARY PROCEDURES</b><br> <i>(include Dates)</i>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </div>
                                        <div class="mobile-view">
                                            <b>PERTINENT LABORATORY and <br>OTHER ANCILLARY PROCEDURES</b><br> <i>(include Dates)</i>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </div>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_lab_procedures" style="width: 100%;">
                                    <small class="text-success"><i> Attach all applicable labs in one file.</i></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_all_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_ua_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ua_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'UA'); ?>>
                                                <span> UA</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_cbc_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_cbc_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'CBC'); ?>>
                                                <span> CBC</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_xray_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_xray_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'X-RAY'); ?>>
                                                <span> X-RAY</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_ultrasound_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ultrasound_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'ULTRA SOUND'); ?>>
                                                <span> ULTRA SOUND</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_ctscan_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ctscan_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'CT SCAN'); ?>>
                                                <span>CT SCAN</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_ctgmonitoring_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ctgmonitoring_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'CTG MONITORING') ?>;>
                                                <span> CTG MONITORING</span>
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-check-input" id="lab_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_others_cbox" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'Others'); ?>> Others:
                                                <textarea id="lab_others" class="form-control" name="lab_procedure_other" style="resize: none;" rows="2" name="lab_procedure_other"><?php echo htmlspecialchars($pertinent_laboratory->lab_procedure_other); ?></textarea>
                                            </div>
                                        </div>
                                       
                                        <div class="row with_file_attached">
                                            <div class="col-md-12">
                                                <small class="text-success">
                                                        @if(count($file_path) > 1) FILE ATTACHMENTS: @else FILE ATTACHMENT: @endif
                                                    </small>
                                                <input type="hidden" name="file_cleared" id="file_cleared" value="">
                                                <button type="button" class="btn btn-xs btn-warning" onclick="clearFileUpload()"> Remove All Files</button><br/><br/>
                                                @for($i = 0; $i < count($file_path); $i++)
                                                    <a href="{{ $file_path[$i] }}" id="file_download" class="reason" target="_blank" style="font-size: 12pt;" download>{{ $file_name[$i] }}</a>
                                                    @if($i + 1 != count($file_path))
                                                        ,&nbsp;&nbsp;
                                                    @endif
                                                @endfor
                                                {{--<a href="{{ asset($file_path) }}" class="reason" style="font-size: 12pt;" download>{{ $file_name }}</a>--}}
                                            </div>
                                        </div><br>

                                        <div class="row no_file_attached">
                                            <div class="col-md-12">
                                                <small class="text-success">FILE ATTACHMENTS:</small></small> &emsp;
                                                <button type="button" class="btn btn-md btn-danger" id="remove_files_btn_normal" onclick="removeFiles(2)">Remove Files</button><br><br>
                                                <div class="attachment_normal">
                                                    <div class="col-md-3" id="upload1">
                                                        <div class="file-upload">
                                                            <div class="text-center image-upload-wrap" id="image-upload-wrap_normal1">
                                                                <input class="file-upload-input files" multiple id="file_upload_input_normal1" type='file' name="file_upload[]" onchange="readUrl(this, 1,2);" accept="image/png, image/jpeg, image/jpg, image/gif, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/pdf"/>
                                                                <img src="{{ asset('resources/img/add_file.png') }}" style="width: 50%; height: 50%;">
                                                            </div>
                                                            <div class="file-upload-content" id="file-upload-content_normal1">
                                                                <img class="file-upload-image" id="file-upload-image_normal1" src="#"/>
                                                                <div class="image-title-wrap">
                                                                    <small class="text-success"><small class="image-title" id="image-title_normal1" style="display:block; word-wrap: break-word;"></small></small>
                                                                    {{--<button type="button" onclick="removeFile(1,2)" class="remove-image"> Remove </button>--}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                         
                        @php
                          $review_system_array_filter = array_filter([
                            $review_of_system->skin ?? null,
                            $review_of_system->head ?? null,
                            $review_of_system->eyes ?? null,
                            $review_of_system->ears ?? null,
                            $review_of_system->nose_or_sinuses ?? null,
                            $review_of_system->mouth_or_throat ?? null,
                            $review_of_system->neck ?? null,
                            $review_of_system->breast ?? null,
                            $review_of_system->respiratory_or_cardiac ?? null,
                            $review_of_system->gastrointestinal ?? null,
                            $review_of_system->urinary ?? null,
                            $review_of_system->peripheral_vascular ?? null,
                            $review_of_system->musculoskeletal ?? null,
                            $review_of_system->neurologic ?? null,
                            $review_of_system->hematologic ?? null,
                            $review_of_system->endocrine ?? null,
                            $review_of_system->psychiatric ?? null,
                          ]);

                        @endphp

                        @if(!empty($review_system_array_filter))
                            <script>
                                $("#collapse_review_system").collapse('show');

                                // Ensure button toggle works properly
                                $(".btn[data-target='#collapse_review_system']").on("click", function () {
                                    $("#collapse_review_system").collapse("toggle");
                                });                            
                            </script>
                        @endif

                        <div class="row" style="margin: 5px;">
                           
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_review_system" aria-expanded="false" aria-controls="collapse_review_system">
                                        <b>REVIEW OF SYSTEMS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_review_system" style="width: 100%;">
                                    <small class="text-success"><b>SKIN</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_skin_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_all_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_skin_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_none_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_skin_rashes_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_rashes_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Rashes'); ?>>
                                                <span> Rashes</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_skin_itching_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_itching_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Itching'); ?>>
                                                <span> Itching</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_skin_hairchange_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_hairchange_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Change in hair or nails'); ?>>
                                                <span> Change in hair or nails</span>
                                            </div>
                                            <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_abrasion_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_abrasion_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Abrasion'); ?>>
                                                    <span> Abrasion</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_laceration_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_laceration_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Laceration'); ?>>
                                                    <span> Laceration</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_contusion_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_contusion_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Contusion (bruises)'); ?>>
                                                    <span> Contusion (bruises)</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_avulsion_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_avulsion_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Avulsion'); ?>>
                                                    <span> Avulsion</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_incision_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_incision_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Incision'); ?>>
                                                    <span> Incision</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_others_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Others'); ?>>
                                                    <span> Others(specify part of the body / symptoms)</span>
                                                    <textarea class="form-control" name="rs_skin_others" id="rs_skin_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->skin_others); ?></textarea>
                                                </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>HEAD</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_all_cbox" value="Yes" <?= isChecked($review_of_system, 'head', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_none_cbox" value="Yes" <?= isChecked($review_of_system, 'head', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_headache_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_headache_cbox" value="Yes" <?= isChecked($review_of_system, 'head', 'Headaches'); ?>>
                                                <span> Headaches</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_injury_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_injury_cbox" value="Yes" <?= isChecked($review_of_system, 'head', 'Head injury'); ?>>
                                                <span> Head injury</span>
                                            </div>
                                            <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_head_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_others_cbox" value="Yes" <?= isChecked($review_of_system, 'head', 'Others'); ?>>
                                                    <span> Others(specify part of the body / symptoms)</span>
                                                    <textarea class="form-control" name="rs_head_others" id="rs_head_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->head_others); ?></textarea>
                                                </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>EYES</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_all_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_none_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_glasses_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_glasses_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'Glasses or Contacts'); ?>>
                                                <span> Glasses or Contacts</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_vision_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_vision_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'Change in vision'); ?>>
                                                <span> Change in vision</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_pain_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'Eye pain'); ?>>
                                                <span> Eye pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_doublevision_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_doublevision_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'Double Vision'); ?>>
                                                <span> Double Vision</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_flashing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_flashing_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'Flashing lights'); ?>>
                                                <span> Flashing lights</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_glaucoma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_glaucoma_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'Glaucoma/Cataracts'); ?>>
                                                <span> Glaucoma/Cataracts</span>
                                            </div>
                                            <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_others_cbox" value="Yes" <?= isChecked($review_of_system, 'eyes', 'Others'); ?>>
                                                    <span> Others(specify part of the body / symptoms)</span>
                                                    <textarea class="form-control" name="rs_eyes_others" id="rs_eyes_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->eyes_others); ?></textarea>
                                                </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>EARS</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_ears_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_all_cbox" value="Yes" <?= isChecked($review_of_system, 'ears', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_ears_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_none_cbox" value="Yes" <?= isChecked($review_of_system, 'ears', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_ears_changehearing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_changehearing_cbox" value="Yes" <?= isChecked($review_of_system, 'ears', 'Change in hearing'); ?>>
                                                <span> Change in hearing</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="rs_ears_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_pain_cbox" value="Yes" <?= isChecked($review_of_system, 'ears', 'Ear pain'); ?>>
                                                <span> Ear pain</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="rs_ears_discharge_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_discharge_cbox" value="Yes" <?= isChecked($review_of_system, 'ears', 'Ear discharge'); ?>>
                                                <span> Ear discharge</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="rs_ears_ringing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_ringing_cbox" value="Yes" <?= isChecked($review_of_system, 'ears', 'Ringing'); ?>>
                                                <span> Ringing</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="rs_ears_dizziness_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_dizziness_cbox" value="Yes" <?= isChecked($review_of_system, 'ears', 'Dizziness'); ?>>
                                                <span> Dizziness</span>
                                            </div>
                                            <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_ears_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_others_cbox" value="Yes" <?= isChecked($review_of_system, 'ears', 'Others'); ?>>
                                                    <span> Others(specify part of the body / symptoms)</span>
                                                    <textarea class="form-control" name="rs_ears_others" id="rs_ears_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->ears_others); ?></textarea>
                                                </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>NOSE/SINUSES</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_all_cbox" value="Yes" <?= isChecked($review_of_system, 'nose_or_sinuses', 'Select All'); ?>>
                                                <span> Select All </span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_none_cbox" value="Yes" <?= isChecked($review_of_system, 'nose_or_sinuses', 'None'); ?>>
                                                <span> None </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_bleeds_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_bleeds_cbox" value="Yes" <?= isChecked($review_of_system, 'nose_or_sinuses', 'Nose bleeds'); ?>>
                                                <span> Nose bleeds</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_stuff_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_stuff_cbox" value="Yes" <?= isChecked($review_of_system, 'nose_or_sinuses', 'Nasal stuffiness'); ?>>
                                                <span> Nasal stuffiness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_colds_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_colds_cbox" value="Yes" <?= isChecked($review_of_system, 'nose_or_sinuses', 'Frequent Colds'); ?>>
                                                <span> Frequent Colds</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_others_cbox" value="Yes" <?= isChecked($review_of_system, 'nose_or_sinuses', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_nose_others" id="rs_nose_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->nose_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>MOUTH/THROAT</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_all_cbox" value="Yes" <?= isChecked($review_of_system, 'mouth_or_throat', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_none_cbox" value="Yes" <?= isChecked($review_of_system, 'mouth_or_throat', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_bleed_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_bleed_cbox" value="Yes" <?= isChecked($review_of_system, 'mouth_or_throat', 'Bleeding gums'); ?>>
                                                <span> Bleeding gums</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_soretongue_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_soretongue_cbox" value="Yes" <?= isChecked($review_of_system, 'mouth_or_throat', 'Sore tongue'); ?>>
                                                <span> Sore tongue</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_sorethroat_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_sorethroat_cbox" value="Yes" <?= isChecked($review_of_system, 'mouth_or_throat', 'Sore throat'); ?>>
                                                <span> Sore throat</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_hoarse_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_hoarse_cbox" value="Yes" <?= isChecked($review_of_system, 'mouth_or_throat', 'Hoarseness'); ?>>
                                                <span> Hoarseness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_others_cbox" value="Yes" <?= isChecked($review_of_system, 'mouth_or_throat', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_mouth_others" id="rs_mouth_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->mouth_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>NECK</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_all_cbox" value="Yes" <?= isChecked($review_of_system, 'neck', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_none_cbox" value="Yes" <?= isChecked($review_of_system, 'neck', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_lumps_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_lumps_cbox" value="Yes" <?= isChecked($review_of_system, 'neck', 'Lumps'); ?>>
                                                <span> Lumps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_swollen_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_swollen_cbox" value="Yes" <?= isChecked($review_of_system, 'neck', 'Swollen glands'); ?>>
                                                <span> Swollen glands</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_goiter_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_goiter_cbox" value="Yes" <?= isChecked($review_of_system, 'neck', 'Goiter'); ?>>
                                                <span> Goiter</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_stiff_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_stiff_cbox" value="Yes" <?= isChecked($review_of_system, 'neck', 'Stiffness'); ?>>
                                                <span> Stiffness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_others_cbox" value="Yes" <?= isChecked($review_of_system, 'neck', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_neck_others" id="rs_neck_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->neck_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>BREAST</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_all_cbox" value="Yes" <?= isChecked($review_of_system, 'breast', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_none_cbox" value="Yes" <?= isChecked($review_of_system, 'breast', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_lumps_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_lumps_cbox" value="Yes" <?= isChecked($review_of_system, 'breast', 'Lumps'); ?>>
                                                <span> Lumps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_pain_cbox" value="Yes" <?= isChecked($review_of_system, 'breast', 'Pain'); ?>>
                                                <span> Pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_discharge_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_discharge_cbox" value="Yes" <?= isChecked($review_of_system, 'breast', 'Nipple discharge'); ?>>
                                                <span> Nipple discharge</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_bse_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_bse_cbox" value="Yes" <?= isChecked($review_of_system, 'breast', 'BSE'); ?>>
                                                <span> BSE</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_others_cbox" value="Yes" <?= isChecked($review_of_system, 'breast', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_breast_others" id="rs_breast_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->respiratory_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>RESPIRATORY/CARDIAC</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_all_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_none_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_shortness_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_shortness_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Shortness of breath'); ?>>
                                                <span> Shortness of breath</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_cough_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_cough_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Cough'); ?>>
                                                <span> Cough</span>
                                            </div>
                                            <div class="col-sm-3">
                                                <input class="form-check-input" id="rs_respi_phlegm_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_phlegm_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Production of phlegm color'); ?>>
                                                <span> Production of phlegm, color</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_wheezing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_wheezing_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Wheezing'); ?>>
                                                <span> Wheezing</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_coughblood_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_coughblood_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Coughing up blood'); ?>>
                                                <span> Coughing up blood</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_chestpain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_chestpain_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Chest pain'); ?>>
                                                <span> Chest pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_fever_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_fever_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Fever'); ?>>
                                                <span> Fever</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_sweats_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_sweats_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Night sweats'); ?>>
                                                <span> Night sweats</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_swelling_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_swelling_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Swelling in hands/feet'); ?>>
                                                <span> Swelling in hands/feet</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_bluefingers_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_bluefingers_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Blue fingers/toes'); ?>>
                                                <span> Blue fingers/toes</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_highbp_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_highbp_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'High blood pressure'); ?>>
                                                <span> High blood pressure</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_skipheartbeats_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_skipheartbeats_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Skipping heart beats'); ?>>
                                                <span> Skipping heart beats</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_heartmurmur_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_heartmurmur_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Heart murmur'); ?>>
                                                <span> Heart murmur</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_hxheart_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_hxheart_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'HX of heart medication'); ?>>
                                                <span> HX of heart medication</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_brochitis_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_brochitis_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Bronchitis/emphysema'); ?>>
                                                <span> Bronchitis/emphysema</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_rheumaticheart_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_rheumaticheart_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Rheumatic heart disease'); ?>>
                                                <span> Rheumatic heart disease</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respiratory_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respiratory_others_cbox" value="Yes" <?= isChecked($review_of_system, 'respiratory_or_cardiac', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_respiratory_others" id="rs_respiratory_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->respiratory_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>GASTROINTESTINAL</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_all_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_none_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <input class="form-check-input" id="rs_gastro_appetite_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_appetite_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Change of appetite or weight'); ?>>
                                                <span> Change of appetite or weight</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_swallow_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_swallow_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Problems swallowing'); ?>>
                                                <span> Problems swallowing</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_nausea_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_nausea_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Nausea'); ?>>
                                                <span> Nausea</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_heartburn_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_heartburn_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Heartburn'); ?>>
                                                <span> Heartburn</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_vomit_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_vomit_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Vomiting'); ?>>
                                                <span> Vomiting</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_vomitblood_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_vomitblood_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Vomiting Blood'); ?>>
                                                <span> Vomiting Blood</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_constipation_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_constipation_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Constipation'); ?>>
                                                <span> Constipation</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_diarrhea_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_diarrhea_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Diarrhea'); ?>>
                                                <span> Diarrhea</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_bowel_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_bowel_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Change in bowel habits'); ?>>
                                                <span> Change in bowel habits</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_abdominal_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_abdominal_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Abdominal pain'); ?>>
                                                <span> Abdominal pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_belching_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_belching_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Excessive belching'); ?>>
                                                <span> Excessive belching</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_flatus_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_flatus_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Excessive flatus'); ?>>
                                                <span> Excessive flatus</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_jaundice_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_jaundice_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Yellow color of skin (Jaundice/Hepatitis)'); ?>>
                                                <span> Yellow color of skin (Jaundice/Hepatitis)</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_intolerance_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_intolerance_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Food intolerance'); ?>>
                                                <span> Food intolerance</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_rectalbleed_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_rectalbleed_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Rectal bleeding/Hemorrhoids'); ?>>
                                                <span> Rectal bleeding/Hemorrhoids</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastrointestinal_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastrointestinal_others_cbox" value="Yes" <?= isChecked($review_of_system, 'gastrointestinal', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_gastrointestinal_others" id="rs_gastrointestinal_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->respiratory_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>URINARY</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_all_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_none_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_difficult_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_difficult_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Difficulty in urination'); ?>>
                                                <span> Difficulty in urination</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_pain_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Pain or burning on urination'); ?>>
                                                <span> Pain or burning on urination</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_frequent_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_frequent_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Frequent urination at night'); ?>>
                                                <span> Frequent urination at night</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_urgent_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_urgent_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Urgent need to urinate'); ?>>
                                                <span> Urgent need to urinate</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_incontinence_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_incontinence_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Incontinence of urine'); ?>>
                                                <span> Incontinence of urine</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_dribbling_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_dribbling_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Dribbling'); ?>>
                                                <span> Dribbling</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_decreased_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_decreased_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Decreased urine stream'); ?>>
                                                <span> Decreased urine stream</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_blood_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_blood_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Blood in urine'); ?>>
                                                <span> Blood in urine</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_uti_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_uti_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'UTI/stones/prostate infection'); ?>>
                                                <span> UTI/stones/prostate infection</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urinary_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urinary_others_cbox" value="Yes" <?= isChecked($review_of_system, 'urinary', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_urinary_others" id="rs_urinary_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->urinary_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>PERIPHERAL VASCULAR</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_all_cbox" value="Yes" <?= isChecked($review_of_system, 'peripheral_vascular', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_none_cbox" value="Yes" <?= isChecked($review_of_system, 'peripheral_vascular', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_legcramp_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_legcramp_cbox" value="Yes" <?= isChecked($review_of_system, 'peripheral_vascular', 'Leg cramps'); ?>>
                                                <span> Leg cramps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_varicose_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_varicose_cbox" value="Yes" <?= isChecked($review_of_system, 'peripheral_vascular', 'Varicose veins'); ?>>
                                                <span> Varicose veins</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_veinclot_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_veinclot_cbox" value="Yes" <?= isChecked($review_of_system, 'peripheral_vascular', 'Clots in veins'); ?>>
                                                <span> Clots in veins</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_edema_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_edema_cbox" value="Yes">
                                                <span> Edema</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peripheral_vascular_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peripheral_vascular_others_cbox" value="Yes" <?= isChecked($review_of_system, 'peripheral_vascular', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_peripheral_vascular_others" id="rs_peripheral_vascular_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->peripheral_vascular_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>MUSCULOSKELETAL</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_all_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_none_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_musclgit_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_musclgit_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Pain'); ?>>
                                                <span> Pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_swell_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_swell_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Swelling'); ?>>
                                                <span> Swelling</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_stiff_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_stiff_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Stiffness'); ?>>
                                                <span> Stiffness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_decmotion_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_decmotion_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Decreased joint motion'); ?>>
                                                <span> Decreased joint motion</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_sizeloss_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_sizeloss_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Loss of muscle size'); ?>>
                                                <span> Loss of muscle size</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_fracture_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_fracture_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Fracture'); ?>>
                                                <span> Fracture</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_sprain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_sprain_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Serious sprains'); ?>>
                                                <span> Serious sprains</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_arthritis_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_arthritis_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Arthritis'); ?>>
                                                <span> Arthritis</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_gout_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_gout_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Gout'); ?>>
                                                <span> Gout</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_spasm_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_spasm_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Muscle Spasm'); ?>>
                                                <span> Muscle Spasm</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_musculoskeletal_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_musculoskeletal_others_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_musculoskeletal_others" id="rs_musculoskeletal_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->musculoskeletal_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>NEUROLOGIC</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_all_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_none_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_headache_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_headache_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Headaches'); ?>>
                                                <span> Headaches</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_seizure_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_seizure_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Seizures'); ?>>
                                                <span> Seizures</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_faint_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_faint_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Loss of Consciousness/Fainting'); ?>>
                                                <span> Loss of Consciousness/Fainting</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_paralysis_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_paralysis_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Paralysis'); ?>>
                                                <span> Paralysis</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_weakness_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_weakness_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Weakness'); ?>>
                                                <span> Weakness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_tremor_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_tremor_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Tremor'); ?>>
                                                <span> Tremor</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_disorientation_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_disorientation_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Disorientation'); ?>>
                                                <span> Disorientation</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_slurringspeech_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_slurringspeech_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Slurring Speech'); ?>>
                                                <span> Slurring Speech</span>
                                            </div>  
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_involuntary_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_involuntary_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Involuntary movement'); ?>>
                                                <span> Involuntary movement</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_unsteadygait_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_unsteadygait_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Unsteady Gait'); ?>>
                                                <span> Unsteady Gait</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_numbness_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_numbness_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Numbness'); ?>>
                                                <span> Numbness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_tingles_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_tingles_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Feeling of pins and needles/tingles'); ?>>
                                                <span> Feeling of pins and needles/tingles</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neurologic_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neurologic_others_cbox" value="Yes" <?= isChecked($review_of_system, 'neurologic', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_neurologic_others" id="rs_neurologic_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->neurologic_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>HEMATOLOGIC</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_all_cbox" value="Yes" <?= isChecked($review_of_system, 'hematologic', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_none_cbox" value="Yes" <?= isChecked($review_of_system, 'hematologic', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_anemia_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_anemia_cbox" value="Yes" <?= isChecked($review_of_system, 'hematologic', 'Anemia'); ?>>
                                                <span> Anemia</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_bruising_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_bruising_cbox" value="Yes" <?= isChecked($review_of_system, 'hematologic', 'Easy bruising/bleeding'); ?>>
                                                <span> Easy bruising/bleeding</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rss_hema_transfusion_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rss_hema_transfusion_cbox" value="Yes" <?= isChecked($review_of_system, 'hematologic', 'Past Transfusions'); ?>>
                                                <span> Past Transfusions</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hematologic_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hematologic_others_cbox" value="Yes" <?= isChecked($review_of_system, 'hematologic', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_hematologic_others" id="rs_hematologic_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->hematologic_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>ENDOCRINE</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_all_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_none_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_abnormal_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_abnormal_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'Abnormal growth'); ?>>
                                                <span> Abnormal growth</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_appetite_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_appetite_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'Increased appetite'); ?>>
                                                <span> Increased appetite</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_thirst_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_thirst_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'Increased thirst'); ?>>
                                                <span> Increased thirst</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_urine_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_urine_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'Increased urine production'); ?>>
                                                <span> Increased urine production</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_heatcold_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_heatcold_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'Heat/cold intolerance'); ?>>
                                                <span> Heat/cold intolerance</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_sweat_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_sweat_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'Excessive sweating'); ?>>
                                                <span> Excessive sweating</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endocrine_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endocrine_others_cbox" value="Yes" <?= isChecked($review_of_system, 'endocrine', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_endocrine_others" id="rs_endocrine_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->endocrine_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>PSYCHIATRIC</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_all_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Select All'); ?>>
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_none_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'None'); ?>>
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_tension_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_tension_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Tension/Anxiety'); ?>>
                                                <span> Tension/Anxiety</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_depression_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_depression_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Depression/suicide ideation'); ?>>
                                                <span> Depression</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_suicideideation_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_suicideideation_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Suicide Ideation'); ?>>
                                                <span> Suicide Ideation</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_memory_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_memory_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Memory problems'); ?>>
                                                <span> Memory problems</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_unusual_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_unusual_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Unusual problems'); ?>>
                                                <span> Unusual problems</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_sleep_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_sleep_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Sleep problems'); ?>>
                                                <span> Sleep problems</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_treatment_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_treatment_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Past treatment with psychiatrist'); ?>>
                                                <span> Past treatment with psychiatrist</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_moodchange_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_moodchange_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Change in mood/change in attitude towards family/friends'); ?>>
                                                <span> Change in mood/change in attitude towards family/friends</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psychiatric_others_cbox_normal_info" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psychiatric_others_cbox" value="Yes" <?= isChecked($review_of_system, 'psychiatric', 'Others'); ?>>
                                                <span> Others(specify part of the body / symptoms)</span>
                                                <textarea class="form-control" name="rs_psychiatric_others" id="rs_psychiatric_others_normal_info" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($review_of_system->psychiatric_others); ?></textarea>
                                            </div>
                                        </div>
                                    </div><br>
                                </div>
                        </div>

                        @php 
                            $nutrition_stat_array_filter = array_filter([
                              $nutritional_status->diet ?? null,
                              $nutritional_status->specify_diets ?? null,
                            ]);
                        @endphp

                        @if(!empty($nutrition_stat_array_filter))
                            <script>
                                $("#collapse_nutri_status").collapse('show');

                                // Ensure button toggle works properly
                                $(".btn[data-target='#collapse_nutri_status']").on("click", function () {
                                    $("#collapse_nutri_status").collapse("toggle");
                                });                            
                            </script>
                        @endif

                        <div class="row" style="margin:5px">
                           
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_nutri_status" aria-expanded="false" aria-controls="collapse_nutri_status">
                                       <b>NUTRITIONAL STATUS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_nutri_status" style="width: 100%;">
                                    <small class="text-success"><b>Diet</b></small>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_none" value="None" <?= isChecked($nutritional_status, 'diet', 'None'); ?>>
                                                None 
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_oral" value="Oral" <?= isChecked($nutritional_status, 'diet', 'Oral'); ?>>
                                                 Oral 
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tube" value="Tube" <?= isChecked($nutritional_status, 'diet', 'Tube'); ?>>
                                                  Tube 
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tpn" value="TPN" <?= isChecked($nutritional_status, 'diet', 'TPN'); ?>>
                                                 TPN 
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_npo" value="NPO" <?= isChecked($nutritional_status, 'diet', 'NPO'); ?>>
                                                 NPO 
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                               <small class="text-success"> Specify Diets: </small><textarea class="form-control" name="diet" style="resize: none;width: 100%;" rows="3"><?php echo htmlspecialchars($nutritional_status->specify_diets); ?></textarea><br><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                          
                        </div>

                        <?php

                        $blood_pressure = isset($latest_vital_signs->blood_pressure) ? explode('/', $latest_vital_signs->blood_pressure) : ['', ''];
                        $systolic = $blood_pressure[0]; 
                        $diastolic = $blood_pressure[1]; 
                        ?>

                        <div class="row" style="margin:5px">
                           
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_vital_signs" aria-expanded="false" aria-controls="collapse_vital_signs">
                                        <b>LATEST VITAL SIGNS</b><i> (required)</i><span class="text-red">*</span>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_vital_signs" style="width: 100%;">
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                              <small class="text-success"><b>Temperature:</b></small><span class="text-red">*</span> <input type="number" step="0.01" style="width:30%;" min="0" name="vital_temp" value="<?php echo htmlspecialchars( $latest_vital_signs->temperature); ?>" required> &#176;C
                                            </div>
                                            <div class="col-md-4">
                                              <small class="text-success"><b>Pulse Rate/Heart Rate:</b></small><span class="text-red">*</span> <input type="number" step="0.01" style="width:30%;" min="0" name="vital_pulse" value="<?php echo htmlspecialchars( $latest_vital_signs->pulse_rate); ?>" required> bpm
                                            </div>
                                            <div class="col-md-4">
                                               <small class="text-success"><b> Respiratory Rate: </b></small><span class="text-red">*</span><input type="number" step="0.01" style="width:30%;" min="0" name="vital_respi_rate" value="<?php echo htmlspecialchars( $latest_vital_signs->respiratory_rate); ?>" required> cpm
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-4">
                                            <?php
                                                // Ensure blood pressure value exists and is formatted correctly (e.g., "120/80")
                                                $bp = isset($latest_vital_signs->blood_pressure) ? explode("/", $latest_vital_signs->blood_pressure) : [null, null];
                                                $systolic = htmlspecialchars($bp[0] ?? '');
                                                $diastolic = htmlspecialchars($bp[1] ?? '');
                                            ?>
                                               <small class="text-success"><b>Blood Pressure:</b></small><span class="text-red">*</span></label>
                                               <input type="number" id="systolic_normal_info" placeholder="Systolic (e.g., 100)" 
                                                    style="width:18%;" min="0" max="300" 
                                                    value="<?= $systolic; ?>" 
                                                    oninput="updateBloodPressure()"> /

                                                <input type="number" id="diastolic_normal_info" placeholder="Diastolic (e.g., 90)" 
                                                    style="width:18%;" min="0" max="200" 
                                                    value="<?= $diastolic; ?>" 
                                                    oninput="updateBloodPressure()">mmHg

                                                <!-- Hidden input to store the combined value -->
                                                <input type="hidden" name="vital_bp" id="vital_bp_normal_info" 
                                                    value="<?php echo htmlspecialchars($latest_vital_signs->blood_pressure); ?>">
                                                </div>
                                            <div class="col-md-4">
                                               <small class="text-success"> O2 Saturation</small><span class="text-red">*</span> <input type="number" step="0.01" style="width:30%;" min="0" name="vital_oxy_saturation" value="<?php echo htmlspecialchars( $latest_vital_signs->oxygen_saturation); ?>" required> %
                                            </div>
                                        </div><br>
                                    </div>
                                </div>
                        </div>

                        <script>
                            function updateBloodPressure() {
                                // Get systolic and diastolic values
                                const systolic_normal_info = document.getElementById('systolic_normal_info').value;
                                const diastolic = document.getElementById('diastolic_normal_info').value;
                                
                                // Combine into "100/90" format
                                document.getElementById('vital_bp_normal_info').value = systolic_normal_info + '/' + diastolic;
                            }
                        </script>

                        @php
                            $glasgocoma_array_filter = array_filter([
                                $glasgocoma_scale->pupil_size_chart ?? null,
                                $glasgocoma_scale->motor_response ?? null,
                                $glasgocoma_scale->verbal_response ?? null,
                                $glasgocoma_scale->eye_response ?? null,
                            ]);
                        @endphp
                        
                        @if(!empty($glasgocoma_array_filter))
                            <script>
                                $("#collapse_glasgow").collapse('show');

                                // Ensure button toggle works properly
                                $(".btn[data-target='#collapse_glasgow']").on("click", function () {
                                    $("#collapse_glasgow").collapse("toggle");
                                });                            
                            </script>
                        @endif

                        <div class="row" style="margin:5px">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_glasgow" aria-expanded="false" aria-controls="collapse_glasgow">
                                        <b>GLASGOW COMA SCALE</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_glasgow" style="width: 100%;">
                                    <small class="text-success"><b>Pupil Size Chart</b></small> &emsp;
                                    <input type="button" class="btn-m btn-warning btn-rounded" onclick="window.glasgowScale.resetToOriginal()" value="Reset">
                                    <div class="container-referral">
                                        <div class="row web-view">
                                            <div class="col-lg-1"></div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_1">
                                                    <b>1</b><br>
                                                    <span class="glasgow-dot" style="height: 6px; width: 6px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_1" value="1" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '1'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_2">
                                                    <b>2</b><br>
                                                    <span class="glasgow-dot" style="height: 10px; width: 10px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_2" value="2" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '2'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_3">
                                                    <b>3</b><br>
                                                    <span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_3" value="3" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '3'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_4">
                                                    <b>4</b><br>
                                                    <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_4" value="4" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '4'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_5">
                                                    <b>5</b><br>
                                                    <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_5" value="5" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '5'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_6">
                                                    <b>6</b><br>
                                                    <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_6" value="6" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '6'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_7">
                                                    <b>7</b><br>
                                                    <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_7" value="7" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '7'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_8">
                                                    <b>8</b><br>
                                                    <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_8" value="8" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '8'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_9">
                                                    <b>9</b><br>
                                                    <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_9" value="9" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '9'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <b>10</b><br>
                                                <label for="glasgow_10">
                                                    <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                </label>
                                                <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_10" value="10" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '10'); ?>>
                                            </div>
                                        </div>
                                        <div class="mobile-view">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <b>1</b>
                                                    <label for="glasgow_info_1">
                                                        <span class="glasgow-dot" style="height: 6px; width: 6px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_1" value="1">&emsp;&emsp;

                                                    <b>2</b>
                                                    <label for="glasgow_info_2">
                                                        <span class="glasgow-dot" style="height: 10px; width: 10px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_2" value="2">&emsp;&emsp;

                                                    <b>3</b>
                                                    <label for="glasgow_info_3">
                                                        <br><span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_3" value="3">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <b>4</b>
                                                    <label for="glasgow_info_4">
                                                        <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_4" value="4">&emsp;&emsp;

                                                    <b>5</b>
                                                    <label for="glasgow_info_5">
                                                        <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_5" value="5">&emsp;&emsp;

                                                    <b>6</b>
                                                    <label for="glasgow_info_6">
                                                        <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_6" value="6">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-1">
                                                    <b>7</b>
                                                    <label for="glasgow_info_7">
                                                        <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_7" value="7">&emsp;&emsp;

                                                    <b>8</b>
                                                    <label for="glasgow_info_8">
                                                        <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_8" value="8">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-1">
                                                    <b>9</b>
                                                    <label for="glasgow_info_9">
                                                        <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_9" value="9">&emsp;&emsp;

                                                    <b>10</b>
                                                    <label for="glasgow_info_10">
                                                        <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_info_10" value="10">
                                                </div>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-success"><b>Motor Response</b></small>
                                            <div class="container-referral table table-responsive" style="overflow-x:auto">
                                                <table class="table-md table-bordered table-hover">
                                                    <thead>
                                                        <tr style="font-size: 11px;">
                                                            <th id="glasgow_table_1" style="text-align: center">ADULT AND CHILD</th>
                                                            <th id="glasgow_table_2" style="text-align: center">INFANT (2 MONTHS)</th>
                                                            <th style="text-align: center">POINTS</th>
                                                            <th style="text-align: center">OPTIONS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Obeys Command</td>
                                                            <td>Spontaneous Movement</td>
                                                            <td style="text-align: center">6</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=6 <?= isChecked($glasgocoma_scale, 'motor_response', '6'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Localizes Pain</td>
                                                            <td>Withdraws (Touch)</td>
                                                            <td style="text-align: center">5</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=5 <?= isChecked($glasgocoma_scale, 'motor_response', '5'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Withdraws</td>
                                                            <td>Withdraws (Pain)</td>
                                                            <td style="text-align: center">4</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=4 <?= isChecked($glasgocoma_scale, 'motor_response', '4'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Flexion to Pain</td>
                                                            <td>Flexion to Pain</td>
                                                            <td style="text-align: center">3</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=3 <?= isChecked($glasgocoma_scale, 'motor_response', '3'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Extension to Pain</td>
                                                            <td>Extension to Pain</td>
                                                            <td style="text-align: center">2</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=2 <?= isChecked($glasgocoma_scale, 'motor_response', '2'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>None</td>
                                                            <td>None</td>
                                                            <td style="text-align: center">1</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=1 <?= isChecked($glasgocoma_scale, 'motor_response', '1'); ?>></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-success"><b>Verbal Response</b></small>
                                            <div class="container-referral table table-responsive" style="overflow-x:auto">
                                                <table class="table-md table-bordered table-hover">
                                                    <thead>
                                                        <tr style="font-size: 11px;">
                                                            <th id="glasgow_table_1" style="width:35%; text-align: center">ADULT AND CHILD</th>
                                                            <th id="glasgow_table_2" style="width:40%; text-align: center">INFANT (2 MONTHS)</th>
                                                            <th style="text-align: center">POINTS</th>
                                                            <th style="text-align: center">OPTIONS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Oriented</td>
                                                            <td>Coos and Babbles</td>
                                                            <td style="text-align: center">5</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=5 <?= isChecked($glasgocoma_scale, 'verbal_response', '5'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Confused</td>
                                                            <td>Irritable Cry</td>
                                                            <td style="text-align: center">4</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=4 <?= isChecked($glasgocoma_scale, 'verbal_response', '4'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Inappropriate</td>
                                                            <td>Cries to Pain</td>
                                                            <td style="text-align: center">3</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=3 <?= isChecked($glasgocoma_scale, 'verbal_response', '3'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Incomprehensible</td>
                                                            <td>Moans to Pain</td>
                                                            <td style="text-align: center">2</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=2 <?= isChecked($glasgocoma_scale, 'verbal_response', '2'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>None</td>
                                                            <td>None</td>
                                                            <td style="text-align: center">1</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=1 <?= isChecked($glasgocoma_scale, 'verbal_response', '1'); ?>></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-success"><b>Eye Response</b></small>
                                            <div class="container-referral table table-responsive" style="overflow-x:auto">
                                                <table class="table-md table-bordered table-hover">
                                                    <thead>
                                                        <tr style="font-size: 11px;">
                                                            <th id="glasgow_table_1" style="width:40%; text-align: center">ADULT AND CHILD</th>
                                                            <th id="glasgow_table_2" style="text-align: center">INFANT (2 MONTHS)</th>
                                                            <th style="text-align: center">POINTS</th>
                                                            <th style="text-align: center">OPTIONS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Spontaneous</td>
                                                            <td>Spontaneous</td>
                                                            <td style="text-align: center">4 </td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_normal_info_4" name="eye_radio" value=4 <?= isChecked($glasgocoma_scale, 'eye_response', '4'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>To Command</td>
                                                            <td>To Voice</td>
                                                            <td style="text-align: center">3 </td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_normal_info_3" name="eye_radio" value=3 <?= isChecked($glasgocoma_scale, 'eye_response', '3'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>To Pain</td>
                                                            <td>To Pain</td>
                                                            <td style="text-align: center">2 </td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_normal_info_2" name="eye_radio" value=2 <?= isChecked($glasgocoma_scale, 'eye_response', '2'); ?>></td>
                                                        </tr>
                                                        <tr>
                                                            <td>None</td>
                                                            <td>None</td>
                                                            <td style="text-align: center">1 </td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_normal_info_1" name="eye_radio" value=1 <?= isChecked($glasgocoma_scale, 'eye_response', '1'); ?>></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <br><br><small class="text-success"><b>GCS Score: </b></small>
                                            <input class="number" name="gcs_score" id="gcs_score" style="text-align: center" min="0" value="<?php echo htmlspecialchars($glasgocoma_scale->gsc_score); ?>" readonly>
                                        </div>
                                    </div><br>
                                </div>
                          
                        </div>
                        <div class="row" style="margin: 5px;">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_reason_referral_normInfo" aria-expanded="false" aria-controls="collapse_reason_referral_normInfo">
                                        <b>REASON FOR REFERRAL</b><i> (required)</i><span class="text-red">*</span>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_reason_referral_normInfo" style="width: 100%;">
                                <small class="text-success"><b>REASON FOR REFERRAL: </b></small> <span class="text-red">*</span><br>
                                    <select name="reason_referral" class="form-control-select select2 reason_referral" required="">
                                        <option value="">Select reason for referral</option>
                                        <option value="-1">Other reason for referral</option>
                                        @foreach($reason_for_referral as $reason_referral)
                                            <option value="{{ $reason_referral->id }}">{{ $reason_referral->reason }}</option>
                                        @endforeach
                                    </select><br><br>
                                        <div id="other_reason_referral"></div>
                                    </div>
                                </div>                          
                        </div>
                        <div class="form-footer pull-right" style="margin: 10px;">
                        <button type="submit" id="edit_save_btn" class="btn btn-primary btn-flat btn-submit"><i class="fa fa-send"></i> Update</button>
                </div>
            </form>
                     

    <div class="modal fade" role="dialog" id="patient_modal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body patient_body">
                    <center>
                        <img src="{{ asset('resources/img/loading.gif') }}" alt="">
                    </center>
                </div>
            </div>
        </div>
    </div>

<!-- <div class="modal fade" id="icd-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title" style="font-size: 17pt;">Search ICD-10 by keyword</h4>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-lg">
                    <input type="text" id="icd10_keyword" class="form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat" onclick="searchICD10()">Find</button>
                    </span>
                </div><br>
                <div class="icd_body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="othersDiagnosis()"> Other Diagnosis</button>
                <button type="button" class="btn btn-success" onclick="getAllCheckBox()"><i class="fa fa-save"></i> Save selected check</button>
            </div>
        </div>
    </div>
</div> -->


<script>
    $(document).ready(function () {
        // Open the collapse when the form loads
        $("#collapse_illness_history_normInfo").collapse('show');
        $("#collapse_diagnosis_normInfo").collapse('show');
        $("#collapse_reason_referral_normInfo").collapse('show');
        $("#collapse_vital_signs").collapse('show');
        $("#collapse_medication").collapse('show');

        // Ensure button toggle works properly
        $(".btn[data-target='#collapse_illness_history_normInfo']").on("click", function () {
            $("#collapse_illness_history_normInfo").collapse("toggle");
        });
        $(".btn[data-target='#collapse_diagnosis_normInfo']").on("click", function () {
            $("#collapse_diagnosis_normInfo").collapse("toggle");
        });
        $(".btn[data-target='#collapse_vital_signs']").on("click", function () {
            $("#collapse_vital_signs").collapse("toggle");
        });
        $(".btn[data-target='#collapse_medication']").on("click", function () {
            $("#collapse_medication").collapse("toggle");
        });
        $(".btn[data-target='#collapse_reason_referral_normInfo']").on("click", function () {
            $("#collapse_reason_referral_normInfo").collapse("toggle");
        });
    });
</script>

<script>

    $(".collapse").on('show.bs.collapse', function() {
        $(this).prev(".container-referral2").find(".fa").removeClass("fa-plus").addClass("fa-minus");
    }).on('hide.bs.collapse', function() {
        $(this).prev(".container-referral2").find(".fa").removeClass("fa-minus").addClass("fa-plus");
    });

    /*****Populate data**********/
    // var retrieved_data = @json($data);
    // console.log(JSON.stringify(retrieved_data));
    // function check_checkbox(){

    // }


    /* *****PRENATAL ADD ROW***** */
    <?php
    $select_year = "";
    foreach (range(date('Y'), 1950) as $year)
        $select_year .= "<option>" . $year . "</option>";
    ?>
    var select_year = "<?php echo $select_year; ?>";

    $('#prenatal_add_row').on('click', function() {
        var rowCount = $('#prenatal_table tr').length;
        $('#prenatal_table').append('<tr style="font-size: 11pt">\n' +
            '<td><input class="form-control" type="text" name="pregnancy_history_order[]"></td>\n' +
            '<td><select class="form-control select" name="pregnancy_history_year[]">\n' +
            select_year +
            '</select></td>\n' +
            '<td><input class="form-control" id="gestation_normal_info" type="text" name="pregnancy_history_gestation[]"></td>\n' +
            '<td><input class="form-control" type="text" name="pregnancy_history_outcome[]"></td>\n' +
            '<td><input class="form-control" type="text" name="pregnancy_history_placeofbirth[]"></td>\n' +
            '<td width="150%">\n' +
            '    <select class="select form-control" name="prenatal_history_sex[]">\n' +
            '        <option value="">Choose...</option>\n' +
            '        <option value="M">Male</option>\n' +
            '        <option value="F">Female</option>\n' +
            '    </select>\n' +
            '</td>\n' +
            '<td><input class="form-control" type="number" min="0" step="0.01" name="pregnancy_history_birthweight[]"></td>\n' +
            '<td><input class="form-control" type="text" name="pregnancy_history_presentstatus[]"></td>\n' +
            '<td><input class="form-control" type="text" name="pregnancy_history_complications[]"></td>\n' +
            '</tr>');
    });

    /* *****COMORBIDITY***** */
    // $('#comor_diab, #comor_asthma, #comor_hyper, #comor_cancer, #comor_others').hide();
    $('#comor_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#comor_none_cbox').prop('checked', false);
            $('#comor_hyper_cbox, #comor_diab_cbox, #comor_asthma_cbox, #comor_copd_cbox').prop('checked', true);
            $('#comor_dyslip_cbox, #comor_thyroid_cbox, #comor_cancer_cbox').prop('checked', true);
            $('#comor_asthma, #comor_diab, #comor_hyper, #comor_cancer').show();
        } else {
            $('#comor_hyper_cbox, #comor_diab_cbox, #comor_asthma_cbox, #comor_copd_cbox').prop('checked', false);
            $('#comor_dyslip_cbox, #comor_thyroid_cbox, #comor_cancer_cbox, #comor_others_cbox').prop('checked', false);
            $('#comor_asthma, #comor_diab, #comor_hyper, #comor_cancer, #comor_others').hide();
        }
    });
    $('#comor_hyper_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        $('#comor_all_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_hyper').show();
        else
            $('#comor_hyper').hide();
    });
    $('#comor_diab_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_diab').show();
        else
            $('#comor_diab').hide();
    });
    $('#comor_asthma_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_asthma').show();
        else
            $('#comor_asthma').hide();
    });
    $('#comor_cancer_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_cancer').show();
        else
            $('#comor_cancer').hide();
    });
    $('#comor_others_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_others').show();
        else
            $('#comor_others').hide();
    });
    $('#comor_copd_cbox,#comor_dyslip_cbox,#comor_thyroid_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
    });
    $('#comor_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#comor_all_cbox, #comor_hyper_cbox, #comor_diab_cbox, #comor_asthma_cbox, #comor_copd_cbox').prop('checked', false);
            $('#comor_dyslip_cbox, #comor_thyroid_cbox, #comor_cancer_cbox, #comor_others_cbox').prop('checked', false);
            $('#comor_asthma, #comor_diab, #comor_hyper, #comor_cancer, #comor_others').hide();
        }
    });

    /* *****ALLERGY***** */
    // $('#allergy_food, #allergy_drug, #allergy_other').hide();
    $('#allergy_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#allergy_none_cbox').prop('checked', false);
            $('#allergy_food_cbox, #allergy_drug_cbox').prop('checked', true);
            $('#allergy_food, #allergy_drug').show();
        } else {
            $('#allergy_food_cbox, #allergy_drug_cbox').prop('checked', false);
            $('#allergy_food, #allergy_drug').hide();
        }
    });
    $('#allergy_food_cbox').on('click', function() {
        $('#allergy_all_cbox, #allergy_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#allergy_food').show();
        else
            $('#allergy_food').hide();
    });
    $('#allergy_drug_cbox').on('click', function() {
        $('#allergy_all_cbox, #allergy_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#allergy_drug').show();
        else
            $('#allergy_drug').hide();
    });
    $('#allergy_other_cbox').on('click', function() {
        $('#allergy_all_cbox, #allergy_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#allergy_other').show();
        else
            $('#allergy_other').hide();
    });
    $('#allergy_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#allergy_food_cbox, #allergy_drug_cbox, #allergy_other_cbox, #allergy_all_cbox').prop('checked', false);
            $('#allergy_food, #allergy_drug, #allergy_other').hide();
        }
    });

    /* *****HEREDOFAMILIAL***** */
    // $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid, #heredo_others').hide();
    $('#heredo_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#heredo_none_cbox').prop('checked', false);
            $('#heredo_hyper_cbox, #heredo_diab_cbox, #heredo_asthma_cbox, #heredo_cancer_cbox').prop('checked', true);
            $('#heredo_kidney_cbox, #heredo_thyroid_cbox').prop('checked', true);
            $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid').show();
        } else {
            $('#heredo_hyper_cbox, #heredo_diab_cbox, #heredo_asthma_cbox, #heredo_cancer_cbox').prop('checked', false);
            $('#heredo_kidney_cbox, #heredo_thyroid_cbox').prop('checked', false);
            $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid').hide();
        }
    });
    $('#heredo_hyper_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_hyper').show();
        else
            $('#heredo_hyper').hide();
    });
    $('#heredo_diab_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_diab').show();
        else
            $('#heredo_diab').hide();
    });
    $('#heredo_asthma_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_asthma').show();
        else
            $('#heredo_asthma').hide();
    });
    $('#heredo_cancer_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_cancer').show();
        else
            $('#heredo_cancer').hide();
    });
    $('#heredo_kidney_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_kidney').show();
        else
            $('#heredo_kidney').hide();
    });
    $('#heredo_thyroid_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_thyroid').show();
        else
            $('#heredo_thyroid').hide();
    });
    $('#heredo_others_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_others').show();
        else
            $('#heredo_others').hide();
    });
    $('#heredo_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#heredo_all_cbox, #heredo_hyper_cbox, #heredo_diab_cbox, #heredo_asthma_cbox, #heredo_cancer_cbox').prop('checked', false);
            $('#heredo_kidney_cbox, #heredo_thyroid_cbox, #heredo_others_cbox').prop('checked', false);
            $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid, #heredo_others').hide();
        }
    });

    /* *****LAB PROCEDURES***** */
    // $('#lab_others').hide();
    $('#lab_all_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#lab_ua_cbox, #lab_cbc_cbox, #lab_xray_cbox').prop('checked', true);
        else
            $('#lab_ua_cbox, #lab_cbc_cbox, #lab_xray_cbox').prop('checked', false);
    });
    $('#lab_others_cbox').on('click', function() {
        $('#lab_all_cbox').prop('checked', false);
        if ($(this).is(':checked'))
            $('#lab_others').show();
        else
            $('#lab_others').hide();
    });
    $('#lab_ua_cbox, #lab_cbc_cbox, #lab_xray_cbox').on('click', function() {
        $('#lab_all_cbox').prop('checked', false);
    });

    /* *****PRENATAL***** */
    // $('#prenatal_mat_illness').hide();
    $('#prenatal_radiowith').on('click', function() {
        if ($(this).is(':checked'))
            $('#prenatal_mat_illness').show();
    });
    $('#prenatal_radiowout').on('click', function() {
        if ($(this).is(':checked'))
            $('#prenatal_mat_illness').hide();
    });

    /* *****POST NATAL (FEEDING HISTORY)****** */
    // $('#breastfed, #formula_fed').hide();
    $('#postnatal_bfeed').on('click', function() {
        if ($(this).is(':checked'))
            $('#breastfed').show();
        else
            $('#breastfed').hide();
    });
    $('#postnatal_ffeed').on('click', function() {
        if ($(this).is(':checked'))
            $('#formula_fed').show();
        else
            $('#formula_fed').hide();
    });

    /* *****POST NATAL (IMMUNIZATION HISTORY)****** */
    // $('#immu_dpt, #immu_hepb, #immu_others').hide();
    $('#immu_dpt_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#immu_dpt').show();
        else
            $('#immu_dpt').hide();
    });
    $('#immu_hepb_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#immu_hepb').show();
        else
            $('#immu_hepb').hide();
    });
    $('#immu_others_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#immu_others').show();
        else
            $('#immu_others').hide();
    });

    /* *****MENSTRUAL/MENOPAUSAL***** */
    // $('#mens_irreg, #menopausal_age').hide();
    $('#mens_irreg_radio').on('click', function() {
        if ($(this).is(':checked'))
            $('#mens_irreg').show();
    });
    $('#mens_reg_radio').on('click', function() {
        if ($(this).is(':checked'))
            $('#mens_irreg').hide();
    });
    $('#menopausal').on('click', function() {
        if ($(this).is(':checked'))
            $('#menopausal_age').show();
    });
    $('#non_menopausal').on('click', function() {
        if ($(this).is(':checked'))
            $('#menopausal_age').hide();
    });

    /* *****CONTRACEPTIVES***** */
    // $('#contraceptive_others').hide();
    $('#contraceptive_others_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#contraceptive_others').show();
            $('#contraceptive_none_cbox').prop('checked', false);
        } else
            $('#contraceptive_others').hide();
    });
    $('#contraceptive_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#contraceptive_others').hide();
            $('#contraceptive_pills_cbox, #contraceptive_iud_cbox, #contraceptive_rhythm_cbox, #contraceptive_condom_cbox, #contraceptive_others_cbox').prop('checked', false);
        }
    });
    $('#contraceptive_pills_cbox,#contraceptive_iud_cbox,#contraceptive_rhythm_cbox,#contraceptive_condom_cbox').on('click', function() {
        $('#contraceptive_none_cbox').prop('checked', false);
    });

    /* *****SMOKING***** */
    // $('#smoking_sticks').hide();
    // $('#smoking_quit_year').hide();
    $('#smoke_yes').on('click', function() {
        if ($(this).is(':checked')) {
            $('#smoking_sticks').show();
            $('#smoking_quit_year').hide();
        }
    });
    $('#smoke_quit').on('click', function() {
        if ($(this).is(':checked')) {
            $('#smoking_quit_year').show();
            $('#smoking_sticks').hide();
        }
    });
    $('#smoke_no').on('click', function() {
        if ($(this).is(':checked')) {
            $('#smoking_sticks').hide();
            $('#smoking_quit_year').hide();
        }
    });

    /* *****ALCOHOL***** */
    // $('#alcohol_bottles').hide();
    // $('#alcohol_type').hide();
    // $('#alcohol_quit_year').hide();
    $('#alcohol_yes_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#alcohol_bottles').show();
            $('#alcohol_type').show();
            $('#alcohol_quit_year').hide();
        }
    })
    $('#alcohol_no_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#alcohol_bottles').hide();
            $('#alcohol_type').hide();
            $('#alcohol_quit_year').hide();
        }
    });
    $('#alcohol_quit_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#alcohol_quit_year').show();
            $('#alcohol_bottles').hide();
            $('#alcohol_type').hide();
        }
    });

    /* *****ILLICIT DRUGS***** */
    // $('#drugs_text').hide();
    // $('#drugs_quit_year').hide();
    $('#drugs_yes_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#drugs_text').show();
            $('#drugs_quit_year').hide();
        }
    });
    $('#drugs_no_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#drugs_text').hide();
            $('#drugs_quit_year').hide();
        }
    });
    $('#drugs_quit_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#drugs_quit_year').show();
            $('#drugs_text').hide();
        }
    });

    /* *****MOTOR/VERBAL/EYE RESPONSE (GLASGOW COMA SCALE)***** */
        // Constants for score limits
    const MAX_TOTAL_SCORE = 15;
    const MIN_TOTAL_SCORE = 3;

    class GlasgowComaScale {
        constructor() {
            this.scores = {
                motor: 0,
                verbal: 0,
                eye: 0
            };
            
            this.originalScores = {
                motor: 0,
                verbal: 0,
                eye: 0,
                total: 0
            };
            
            this.initializeScores();
            this.attachEventListeners();
        }
        
        initializeScores() {
            // Get initial values from checked radio buttons
            this.scores.motor = parseInt($('input[name="motor_radio"]:checked').val()) || 0;
            this.scores.verbal = parseInt($('input[name="verbal_radio"]:checked').val()) || 0;
            this.scores.eye = parseInt($('input[name="eye_radio"]:checked').val()) || 0;
            
            // Store original values
            this.originalScores.motor = this.scores.motor;
            this.originalScores.verbal = this.scores.verbal;
            this.originalScores.eye = this.scores.eye;
            this.originalScores.total = parseInt($('#gcs_score').val()) || 0;
            
            // Set initial total if components exist but total doesn't
            if (this.scores.motor || this.scores.verbal || this.scores.eye) {
                const calculatedTotal = this.calculateTotal();
                if (calculatedTotal >= MIN_TOTAL_SCORE && calculatedTotal <= MAX_TOTAL_SCORE) {
                    $('#gcs_score').val(calculatedTotal);
                }
            }
        }
        
        calculateTotal() {
            return this.scores.motor + this.scores.verbal + this.scores.eye;
        }
        
        updateScore(type, newValue) {
            const oldValue = this.scores[type];
            this.scores[type] = parseInt(newValue) || 0;
            
            const currentTotal = parseInt($('#gcs_score').val()) || 0;
            const newTotal = currentTotal - oldValue + this.scores[type];
            
            if (newTotal >= MIN_TOTAL_SCORE && newTotal <= MAX_TOTAL_SCORE) {
                $('#gcs_score').val(newTotal);
            } else {
                // Revert if total would be invalid
                this.scores[type] = oldValue;
                this.resetToOriginal();
            }
        }
        
        resetToOriginal() {
            // Reset scores to original values
            this.scores = { ...this.originalScores };
            
            // Reset radio buttons
            if (this.originalScores.motor) {
                $(`input[name="motor_radio"][value="${this.originalScores.motor}"]`).prop('checked', true);
            } else {
                $('input[name="motor_radio"]').prop('checked', false);
            }
            
            if (this.originalScores.verbal) {
                $(`input[name="verbal_radio"][value="${this.originalScores.verbal}"]`).prop('checked', true);
            } else {
                $('input[name="verbal_radio"]').prop('checked', false);
            }
            
            if (this.originalScores.eye) {
                $(`input[name="eye_radio"][value="${this.originalScores.eye}"]`).prop('checked', true);
            } else {
                $('input[name="eye_radio"]').prop('checked', false);
            }
            
            // Reset pupil size and total score
            $('input[name="glasgow_pupil_btn"]').prop('checked', false);
            $('#gcs_score').val(this.originalScores.total);
        }
        
        attachEventListeners() {
            // Motor score change handler
            $('input[name="motor_radio"]').on('change', (e) => {
                this.updateScore('motor', e.target.value);
            });
            
            // Verbal score change handler
            $('input[name="verbal_radio"]').on('change', (e) => {
                this.updateScore('verbal', e.target.value);
            });
            
            // Eye score change handler
            $('input[name="eye_radio"]').on('change', (e) => {
                this.updateScore('eye', e.target.value);
            });
            
            // Reset button handler
            $('.btn-warning').on('click', () => this.resetToOriginal());
        }
    }

    // Initialize when document is ready
    $(document).ready(() => {
        window.glasgowScale = new GlasgowComaScale();
    });

    /* *****REVIEW OF SYSTEMS***** */
    /* SKIN */
    $('#rs_skin_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox, #rs_skin_abrasion_cbox_normal_info, #rs_skin_laceration_cbox_normal_info, #rs_skin_contusion_cbox_normal_info, #rs_skin_avulsion_cbox_normal_info, #rs_skin_incision_cbox').prop('checked', true);
            $('#rs_skin_none_cbox').prop('checked', false);
        } else
            $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox, #rs_skin_abrasion_cbox_normal_info, #rs_skin_laceration_cbox_normal_info, #rs_skin_contusion_cbox_normal_info, #rs_skin_avulsion_cbox_normal_info, #rs_skin_incision_cbox').prop('checked', false);
    });
    $('#rs_skin_others_normal_info').show(); 
        $('#rs_skin_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_skin_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_skin_others_normal_info").val("");
                $('#rs_skin_others_normal_info').hide(); // Hide when unchecked
            }
        });
    $('#rs_skin_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_skin_all_cbox, #rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox, #rs_skin_abrasion_cbox_normal_info, #rs_skin_laceration_cbox_normal_info, #rs_skin_contusion_cbox_normal_info, #rs_skin_avulsion_cbox_normal_info, #rs_skin_incision_cbox').prop('checked', false);
    });
    $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox, #rs_skin_abrasion_cbox_normal_info, #rs_skin_laceration_cbox_normal_info, #rs_skin_contusion_cbox_normal_info, #rs_skin_avulsion_cbox_normal_info, #rs_skin_incision_cbox').on('click', function() {
        $('#rs_skin_all_cbox, #rs_skin_none_cbox').prop('checked', false);
    });

    /* HEAD */
    $('#rs_head_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_head_headache_cbox, #rs_head_injury_cbox').prop('checked', true);
            $('#rs_head_none_cbox').prop('checked', false);
        } else
            $('#rs_head_headache_cbox, #rs_head_injury_cbox').prop('checked', false);
    });
    $('#rs_head_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_head_all_cbox, #rs_head_headache_cbox, #rs_head_injury_cbox').prop('checked', false);
    });
    $('#rs_head_headache_cbox, #rs_head_injury_cbox').on('click', function() {
        $('#rs_head_all_cbox, #rs_head_none_cbox').prop('checked', false);
    })
    $('#rs_head_others_normal_info').show(); 
        $('#rs_head_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_head_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_head_others_normal_info").val("");
                $('#rs_head_others_normal_info').hide(); // Hide when unchecked
            }
        });

    /* EYES */
    $('#rs_eyes_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_eyes_glasses_cbox, #rs_eyes_vision_cbox, #rs_eyes_pain_cbox, #rs_eyes_doublevision_cbox').prop('checked', true);
            $('#rs_eyes_flashing_cbox, #rs_eyes_glaucoma_cbox').prop('checked', true);
            $('#rs_eyes_none_cbox').prop('checked', false);
        } else {
            $('#rs_eyes_glasses_cbox, #rs_eyes_vision_cbox, #rs_eyes_pain_cbox, #rs_eyes_doublevision_cbox').prop('checked', false);
            $('#rs_eyes_flashing_cbox, #rs_eyes_glaucoma_cbox').prop('checked', false);
        }
    });
    $('#rs_eyes_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_eyes_glasses_cbox, #rs_eyes_vision_cbox, #rs_eyes_pain_cbox, #rs_eyes_doublevision_cbox').prop('checked', false);
            $('#rs_eyes_all_cbox, #rs_eyes_flashing_cbox, #rs_eyes_glaucoma_cbox').prop('checked', false);
        }
    });
    $('#rs_eyes_glasses_cbox, #rs_eyes_vision_cbox, #rs_eyes_pain_cbox, #rs_eyes_doublevision_cbox, #rs_eyes_flashing_cbox, #rs_eyes_glaucoma_cbox').on('click', function() {
        $('#rs_eyes_all_cbox, #rs_eyes_none_cbox').prop('checked', false);
    });
    $('#rs_eyes_others_normal_info').show(); 
        $('#rs_eyes_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_eyes_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_eyes_others_normal_info").val("");
                $('#rs_eyes_others_normal_info').hide(); // Hide when unchecked
            }
        });

    /* EARS */
    $('#rs_ears_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_ears_changehearing_cbox, #rs_ears_pain_cbox, #rs_ears_discharge_cbox, #rs_ears_ringing_cbox, #rs_ears_dizziness_cbox').prop('checked', true);
            $('#rs_ears_none_cbox').prop('checked', false);
        } else
            $('#rs_ears_changehearing_cbox, #rs_ears_pain_cbox, #rs_ears_discharge_cbox, #rs_ears_ringing_cbox, #rs_ears_dizziness_cbox').prop('checked', false);
    });
    $('#rs_ears_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_ears_all_cbox, #rs_ears_changehearing_cbox, #rs_ears_pain_cbox, #rs_ears_discharge_cbox, #rs_ears_ringing_cbox, #rs_ears_dizziness_cbox').prop('checked', false);
    });
    $('#rs_ears_changehearing_cbox, #rs_ears_pain_cbox, #rs_ears_discharge_cbox, #rs_ears_ringing_cbox, #rs_ears_dizziness_cbox').on('click', function() {
        $('#rs_ears_all_cbox, #rs_ears_none_cbox').prop('checked', false);
    });
    $('#rs_ears_others_normal_info').show(); 
        $('#rs_ears_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_ears_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_ears_others_normal_info").val("");
                $('#rs_ears_others_normal_info').hide(); // Hide when unchecked
            }
        });

    /* NOSE/SINUSES */
    $('#rs_nose_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_nose_bleeds_cbox, #rs_nose_stuff_cbox, #rs_nose_colds_cbox').prop('checked', true);
            $('#rs_nose_none_cbox').prop('checked', false);
        } else
            $('#rs_nose_bleeds_cbox, #rs_nose_stuff_cbox, #rs_nose_colds_cbox').prop('checked', false);
    });
    $('#rs_nose_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_nose_all_cbox, #rs_nose_bleeds_cbox, #rs_nose_stuff_cbox, #rs_nose_colds_cbox').prop('checked', false);
    });
    $('#rs_nose_bleeds_cbox, #rs_nose_stuff_cbox, #rs_nose_colds_cbox').on('click', function() {
        $('#rs_nose_all_cbox, #rs_nose_none_cbox').prop('checked', false);
    });
    $('#rs_nose_others_normal_info').show(); 
        $('#rs_nose_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_nose_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_nose_others_normal_info").val("");
                $('#rs_nose_others_normal_info').hide(); // Hide when unchecked
            }
        });

    /* MOUTH/THROAT */
    $('#rs_mouth_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_mouth_bleed_cbox, #rs_mouth_soretongue_cbox, #rs_mouth_sorethroat_cbox, #rs_mouth_hoarse_cbox').prop('checked', true);
            $('#rs_mouth_none_cbox').prop('checked', false);
        } else
            $('#rs_mouth_bleed_cbox, #rs_mouth_soretongue_cbox, #rs_mouth_sorethroat_cbox, #rs_mouth_hoarse_cbox').prop('checked', false);
    });
    $('#rs_mouth_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_mouth_all_cbox, #rs_mouth_bleed_cbox, #rs_mouth_soretongue_cbox, #rs_mouth_sorethroat_cbox, #rs_mouth_hoarse_cbox').prop('checked', false);
    });
    $('#rs_mouth_bleed_cbox, #rs_mouth_soretongue_cbox, #rs_mouth_sorethroat_cbox, #rs_mouth_hoarse_cbox').on('click', function() {
        $('#rs_mouth_all_cbox, #rs_mouth_none_cbox').prop('checked', false);
    });
    $('#rs_mouth_others_normal_info').show(); 
        $('#rs_mouth_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_mouth_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_mouth_others_normal_info").val("");
                $('#rs_mouth_others_normal_info').hide(); // Hide when unchecked
            }
        });

    /* NECK */
    $('#rs_neck_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_neck_lumps_cbox, #rs_neck_swollen_cbox, #rs_neck_goiter_cbox, #rs_neck_stiff_cbox').prop('checked', true);
            $('#rs_neck_none_cbox').prop('checked', false);
        } else
            $('#rs_neck_lumps_cbox, #rs_neck_swollen_cbox, #rs_neck_goiter_cbox, #rs_neck_stiff_cbox').prop('checked', false);
    });
    $('#rs_neck_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_neck_all_cbox, #rs_neck_lumps_cbox, #rs_neck_swollen_cbox, #rs_neck_goiter_cbox, #rs_neck_stiff_cbox').prop('checked', false);
    });
    $('#rs_neck_lumps_cbox, #rs_neck_swollen_cbox, #rs_neck_goiter_cbox, #rs_neck_stiff_cbox').on('click', function() {
        $('#rs_neck_all_cbox, #rs_neck_none_cbox').prop('checked', false);
    });
    $('#rs_neck_others_normal_info').show(); 
        $('#rs_neck_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_neck_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_neck_others_normal_info").val("");
                $('#rs_neck_others_normal_info').hide(); // Hide when unchecked
            }
        });

    /* BREAST */
    $('#rs_breast_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_breast_lumps_cbox, #rs_breast_pain_cbox, #rs_breast_discharge_cbox, #rs_breast_bse_cbox').prop('checked', true);
            $('#rs_breast_none_cbox').prop('checked', false);
        } else
            $('#rs_breast_lumps_cbox, #rs_breast_pain_cbox, #rs_breast_discharge_cbox, #rs_breast_bse_cbox').prop('checked', false);
    });
    $('#rs_breast_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_breast_all_cbox, #rs_breast_lumps_cbox, #rs_breast_pain_cbox, #rs_breast_discharge_cbox, #rs_breast_bse_cbox').prop('checked', false);
    });
    $('#rs_breast_lumps_cbox, #rs_breast_pain_cbox, #rs_breast_discharge_cbox, #rs_breast_bse_cbox').on('click', function() {
        $('#rs_breast_all_cbox, #rs_breast_none_cbox').prop('checked', false);
    });
   
    $('#rs_breast_others_normal_info').show(); 
        $('#rs_breast_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_breast_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_breast_others_normal_info").val("");
                $('#rs_breast_others_normal_info').hide(); // Hide when unchecked
            }
        });

    /* RESPIRATORY/CARDIAC */
    $('#rs_respi_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_respi_shortness_cbox, #rs_respi_cough_cbox, #rs_respi_phlegm_cbox, #rs_respi_wheezing_cbox, #rs_respi_coughblood_cbox').prop('checked', true);
            $('#rs_respi_chestpain_cbox, #rs_respi_fever_cbox, #rs_respi_sweats_cbox, #rs_respi_swelling_cbox, #rs_respi_bluefingers_cbox').prop('checked', true);
            $('#rs_respi_highbp_cbox, #rs_respi_skipheartbeats_cbox, #rs_respi_heartmurmur_cbox, #rs_respi_hxheart_cbox, #rs_respi_brochitis_cbox, #rs_respi_rheumaticheart_cbox').prop('checked', true);
            $('#rs_respi_none_cbox').prop('checked', false);
        } else {
            $('#rs_respi_shortness_cbox, #rs_respi_cough_cbox, #rs_respi_phlegm_cbox, #rs_respi_wheezing_cbox, #rs_respi_coughblood_cbox').prop('checked', false);
            $('#rs_respi_chestpain_cbox, #rs_respi_fever_cbox, #rs_respi_sweats_cbox, #rs_respi_swelling_cbox, #rs_respi_bluefingers_cbox').prop('checked', false);
            $('#rs_respi_highbp_cbox, #rs_respi_skipheartbeats_cbox, #rs_respi_heartmurmur_cbox, #rs_respi_hxheart_cbox, #rs_respi_brochitis_cbox, #rs_respi_rheumaticheart_cbox').prop('checked', false);
        }
    });
    $('#rs_respi_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_respi_all_cbox, #rs_respi_shortness_cbox, #rs_respi_cough_cbox, #rs_respi_phlegm_cbox, #rs_respi_wheezing_cbox, #rs_respi_coughblood_cbox').prop('checked', false);
            $('#rs_respi_chestpain_cbox, #rs_respi_fever_cbox, #rs_respi_sweats_cbox, #rs_respi_swelling_cbox, #rs_respi_bluefingers_cbox').prop('checked', false);
            $('#rs_respi_highbp_cbox, #rs_respi_skipheartbeats_cbox, #rs_respi_heartmurmur_cbox, #rs_respi_hxheart_cbox, #rs_respi_brochitis_cbox, #rs_respi_rheumaticheart_cbox').prop('checked', false);
        }
    });
    $('#rs_respi_shortness_cbox, #rs_respi_cough_cbox, #rs_respi_phlegm_cbox, #rs_respi_wheezing_cbox, #rs_respi_coughblood_cbox').on('click', function() {
        $('#rs_respi_all_cbox, #rs_respi_none_cbox').prop('checked', false);
    });
    $('#rs_respi_chestpain_cbox, #rs_respi_fever_cbox, #rs_respi_sweats_cbox, #rs_respi_swelling_cbox, #rs_respi_bluefingers_cbox').on('click', function() {
        $('#rs_respi_all_cbox, #rs_respi_none_cbox').prop('checked', false);
    });
    $('#rs_respi_highbp_cbox, #rs_respi_skipheartbeats_cbox, #rs_respi_heartmurmur_cbox, #rs_respi_hxheart_cbox, #rs_respi_brochitis_cbox, #rs_respi_rheumaticheart_cbox').on('click', function() {
        $('#rs_respi_all_cbox, #rs_respi_none_cbox').prop('checked', false);
    });
    $('#rs_respiratory_others_normal_info').show(); 
        $('#rs_respiratory_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_respiratory_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_respiratory_others_normal_info").val("");
                $('#rs_respiratory_others_normal_info').hide(); // Hide when unchecked
            }
        });
    /* GASTROINTESTINAL */
    $('#rs_gastro_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_gastro_appetite_cbox, #rs_gastro_swallow_cbox, #rs_gastro_nausea_cbox, #rs_gastro_heartburn_cbox, #rs_gastro_vomit_cbox, #rs_gastro_vomitblood_cbox').prop('checked', true);
            $('#rs_gastro_constipation_cbox, #rs_gastro_diarrhea_cbox, #rs_gastro_bowel_cbox, #rs_gastro_abdominal_cbox, #rs_gastro_belching_cbox, #rs_gastro_flatus_cbox').prop('checked', true);
            $('#rs_gastro_jaundice_cbox, #rs_gastro_intolerance_cbox, #rs_gastro_rectalbleed_cbox').prop('checked', true);
            $('#rs_gastro_none_cbox').prop('checked', false);
        } else {
            $('#rs_gastro_appetite_cbox, #rs_gastro_swallow_cbox, #rs_gastro_nausea_cbox, #rs_gastro_heartburn_cbox, #rs_gastro_vomit_cbox, #rs_gastro_vomitblood_cbox').prop('checked', false);
            $('#rs_gastro_constipation_cbox, #rs_gastro_diarrhea_cbox, #rs_gastro_bowel_cbox, #rs_gastro_abdominal_cbox, #rs_gastro_belching_cbox, #rs_gastro_flatus_cbox').prop('checked', false);
            $('#rs_gastro_jaundice_cbox, #rs_gastro_intolerance_cbox, #rs_gastro_rectalbleed_cbox').prop('checked', false);
        }
    });
    $('#rs_gastro_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_gastro_appetite_cbox, #rs_gastro_swallow_cbox, #rs_gastro_nausea_cbox, #rs_gastro_heartburn_cbox, #rs_gastro_vomit_cbox, #rs_gastro_vomitblood_cbox').prop('checked', false);
            $('#rs_gastro_constipation_cbox, #rs_gastro_diarrhea_cbox, #rs_gastro_bowel_cbox, #rs_gastro_abdominal_cbox, #rs_gastro_belching_cbox, #rs_gastro_flatus_cbox').prop('checked', false);
            $('#rs_gastro_all_cbox, #rs_gastro_jaundice_cbox, #rs_gastro_intolerance_cbox, #rs_gastro_rectalbleed_cbox').prop('checked', false);
        }
    });
    $('#rs_gastro_appetite_cbox, #rs_gastro_swallow_cbox, #rs_gastro_nausea_cbox, #rs_gastro_heartburn_cbox, #rs_gastro_vomit_cbox, #rs_gastro_vomitblood_cbox').on('click', function() {
        $('#rs_gastro_all_cbox, #rs_gastro_none_cbox').prop('checked', false);
    });
    $('#rs_gastro_constipation_cbox, #rs_gastro_diarrhea_cbox, #rs_gastro_bowel_cbox, #rs_gastro_abdominal_cbox, #rs_gastro_belching_cbox, #rs_gastro_flatus_cbox').on('click', function() {
        $('#rs_gastro_all_cbox, #rs_gastro_none_cbox').prop('checked', false);
    });
    $('#rs_gastro_jaundice_cbox, #rs_gastro_intolerance_cbox, #rs_gastro_rectalbleed_cbox').on('click', function() {
        $('#rs_gastro_all_cbox, #rs_gastro_none_cbox').prop('checked', false);
    });
    $('#rs_gastrointestinal_others_normal_info').show(); 
        $('#rs_gastrointestinal_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_gastrointestinal_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_gastrointestinal_others_normal_info").val("");
                $('#rs_gastrointestinal_others_normal_info').hide(); // Hide when unchecked
            }
        });
    /* URINARY */
    $('#rs_urin_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_urin_difficult_cbox, #rs_urin_pain_cbox, #rs_urin_frequent_cbox, #rs_urin_urgent_cbox, #rs_urin_incontinence_cbox').prop('checked', true);
            $('#rs_urin_dribbling_cbox, #rs_urin_decreased_cbox, #rs_urin_blood_cbox, #rs_urin_uti_cbox').prop('checked', true);
            $('#rs_urin_none_cbox').prop('checked', false);
        } else {
            $('#rs_urin_difficult_cbox, #rs_urin_pain_cbox, #rs_urin_frequent_cbox, #rs_urin_urgent_cbox, #rs_urin_incontinence_cbox').prop('checked', false);
            $('#rs_urin_dribbling_cbox, #rs_urin_decreased_cbox, #rs_urin_blood_cbox, #rs_urin_uti_cbox').prop('checked', false);
        }
    });
    $('#rs_urin_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_urin_difficult_cbox, #rs_urin_pain_cbox, #rs_urin_frequent_cbox, #rs_urin_urgent_cbox, #rs_urin_incontinence_cbox').prop('checked', false);
            $('#rs_urin_all_cbox, #rs_urin_dribbling_cbox, #rs_urin_decreased_cbox, #rs_urin_blood_cbox, #rs_urin_uti_cbox').prop('checked', false);
        }
    });
    $('#rs_urin_difficult_cbox, #rs_urin_pain_cbox, #rs_urin_frequent_cbox, #rs_urin_urgent_cbox, #rs_urin_incontinence_cbox').on('click', function() {
        $('#rs_urin_all_cbox, #rs_urin_none_cbox').prop('checked', false);
    });
    $('#rs_urin_dribbling_cbox, #rs_urin_decreased_cbox, #rs_urin_blood_cbox, #rs_urin_uti_cbox').on('click', function() {
        $('#rs_urin_all_cbox, #rs_urin_none_cbox').prop('checked', false);
    });
    $('#rs_urinary_others_normal_info').show(); 
        $('#rs_urinary_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_urinary_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_urinary_others_normal_info").val("");
                $('#rs_urinary_others_normal_info').hide(); // Hide when unchecked
            }
        });
    /* PERIPHERAL VASCULAR */
    $('#rs_peri_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox, #rs_peri_edema_cbox').prop('checked', true);
            $('#rs_peri_none_cbox').prop('checked', false);
        } else
            $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox, #rs_peri_edema_cbox').prop('checked', false);
    });
    $('#rs_peri_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_peri_all_cbox, #rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox, #rs_peri_edema_cbox').prop('checked', false);
    });
    $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox, #rs_peri_edema_cbox').on('click', function() {
        $('#rs_peri_all_cbox, #rs_peri_none_cbox').prop('checked', false);
    });
    $('#rs_peripheral_vascular_others_normal_info').show(); 
        $('#rs_peripheral_vascular_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_peripheral_vascular_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_peripheral_vascular_others_normal_info").val("");
                $('#rs_peripheral_vascular_others_normal_info').hide(); // Hide when unchecked
            }
        });

    /* MUSCULOSKELETAL */
    $('#rs_muscle_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_muscle_spasm_cbox, #rs_muscle_sizeloss_cbox, #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').prop('checked', true);
            $('#rs_muscle_none_cbox').prop('checked', false);
        } else {
            $('#rs_muscle_spasm_cbox, #rs_muscle_sizeloss_cbox, #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').prop('checked', false);
        }
    });

    $('#rs_muscle_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_muscle_all_cbox, #rs_muscle_spasm_cbox, #rs_muscle_sizeloss_cbox,  #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').prop('checked', false);
        }
    });

    $('#rs_muscle_spasm_cbox, #rs_muscle_sizeloss_cbox, #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').on('click', function() {
        $('#rs_muscle_all_cbox, #rs_muscle_none_cbox').prop('checked', false);
    });
    $('#rs_musculoskeletal_others_normal_info').show(); 
        $('#rs_musculoskeletal_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_musculoskeletal_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_musculoskeletal_others_normal_info").val("");
                $('#rs_musculoskeletal_others_normal_info').hide(); // Hide when unchecked
            }
        });
    /* NEUROLOGIC */
    $('#rs_neuro_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').prop('checked', true);
            $('#rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', true);
            $('#rs_neuro_none_cbox').prop('checked', false);
        } else {
            $('#rs_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').prop('checked', false);
            $('#rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', false);
        }
    });
    $('#rs_neuro_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_neuro_all_cbox, #rs_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').prop('checked', false);
            $(' #rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', false);
        }
    });
    $('#rs_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').on('click', function() {
        $('#rs_neuro_all_cbox, #rs_neuro_none_cbox').prop('checked', false);
    });
    $(' #rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').on('click', function() {
        $('#rs_neuro_all_cbox, #rs_neuro_none_cbox').prop('checked', false);
    });
    $('#rs_neurologic_others_normal_info').show(); 
        $('#rs_neurologic_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_neurologic_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_neurologic_others_normal_info").val("");
                $('#rs_neurologic_others_normal_info').hide(); // Hide when unchecked
            }
        });
    /* HEMATOLOGIC */
    $('#rs_hema_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_hema_anemia_cbox, #rs_hema_bruising_cbox, #rss_hema_transfusion_cbox').prop('checked', true);
            $('#rs_hema_none_cbox').prop('checked', false);
        } else
            $('#rs_hema_anemia_cbox, #rs_hema_bruising_cbox, #rss_hema_transfusion_cbox').prop('checked', false);
    });
    $('#rs_hema_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_hema_all_cbox, #rs_hema_anemia_cbox, #rs_hema_bruising_cbox, #rss_hema_transfusion_cbox').prop('checked', false);
    });
    $('#rs_hema_anemia_cbox, #rs_hema_bruising_cbox, #rss_hema_transfusion_cbox').on('click', function() {
        $('#rs_hema_all_cbox, #rs_hema_none_cbox').prop('checked', false);
    });
    $('#rs_hematologic_others_normal_info').show(); 
        $('#rs_hematologic_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_hematologic_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_hematologic_others_normal_info").val("");
                $('#rs_hematologic_others_normal_info').hide(); // Hide when unchecked
            }
        });
    /* ENDOCRINE */
    $('#rs_endo_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_endo_abnormal_cbox, #rs_endo_appetite_cbox, #rs_endo_thirst_cbox, #rs_endo_urine_cbox').prop('checked', true);
            $('#rs_endo_heatcold_cbox, #rs_endo_sweat_cbox').prop('checked', true);
            $('#rs_endo_none_cbox').prop('checked', false);
        } else {
            $('#rs_endo_abnormal_cbox, #rs_endo_appetite_cbox, #rs_endo_thirst_cbox, #rs_endo_urine_cbox').prop('checked', false);
            $('#rs_endo_heatcold_cbox, #rs_endo_sweat_cbox').prop('checked', false);
        }
    });
    $('#rs_endo_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_endo_abnormal_cbox, #rs_endo_appetite_cbox, #rs_endo_thirst_cbox, #rs_endo_urine_cbox').prop('checked', false);
            $('#rs_endo_all_cbox, #rs_endo_heatcold_cbox, #rs_endo_sweat_cbox').prop('checked', false);
        }
    });
    $('#rs_endo_abnormal_cbox, #rs_endo_appetite_cbox, #rs_endo_thirst_cbox, #rs_endo_urine_cbox').on('click', function() {
        $('#rs_endo_all_cbox, #rs_endo_none_cbox').prop('checked', false);
    });
    $('#rs_endo_heatcold_cbox, #rs_endo_sweat_cbox').on('click', function() {
        $('#rs_endo_all_cbox, #rs_endo_none_cbox').prop('checked', false);
    });
    $('#rs_endocrine_others_normal_info').show(); 
        $('#rs_endocrine_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_endocrine_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_endocrine_others_normal_info").val("");
                $('#rs_endocrine_others_normal_info').hide(); // Hide when unchecked
            }
        });
    /* PSYCHIATRIC */
    $('#rs_psych_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_psych_tension_cbox, #rs_psych_depression_cbox, #rs_psych_suicideideation_cbox, #rs_psych_memory_cbox, #rs_psych_unusual_cbox, #rs_psych_sleep_cbox, #rs_psych_treatment_cbox, #rs_psych_moodchange_cbox').prop('checked', true);
            $('#rs_psych_none_cbox').prop('checked', false);
        } else
            $('#rs_psych_tension_cbox, #rs_psych_depression_cbox, #rs_psych_suicideideation_cbox, #rs_psych_memory_cbox, #rs_psych_unusual_cbox, #rs_psych_sleep_cbox, #rs_psych_treatment_cbox, #rs_psych_moodchange_cbox').prop('checked', false);
    });
    $('#rs_psych_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_psych_all_cbox, #rs_psych_tension_cbox, #rs_psych_depression_cbox, #rs_psych_suicideideation_cbox, #rs_psych_memory_cbox, #rs_psych_unusual_cbox, #rs_psych_sleep_cbox, #rs_psych_treatment_cbox, #rs_psych_moodchange_cbox').prop('checked', false);
    });
    $('#rs_psych_tension_cbox, #rs_psych_depression_cbox, #rs_psych_suicideideation_cbox, #rs_psych_memory_cbox, #rs_psych_unusual_cbox, #rs_psych_sleep_cbox, #rs_psych_treatment_cbox, #rs_psych_moodchange_cbox').on('click', function() {
        $('#rs_psych_all_cbox, #rs_psych_none_cbox').prop('checked', false);
    });
    $('#rs_psychiatric_others_normal_info').show(); 
        $('#rs_psychiatric_others_cbox_normal_info').on('click', function() {
            if ($(this).is(':checked')) {
                $('#rs_psychiatric_others_normal_info').show(); // Show when checked
            } else {
                $("#rs_psychiatric_others_normal_info").val("");
                $('#rs_psychiatric_others_normal_info').hide(); // Hide when unchecked
            }
        });
    /**************************************************************************/

    // $("#sbmitBtnNormalRevised").on('click',function(e){
    //     if(!($("#icd").val()) && !($("#other_diag").val())){
    //         Lobibox.alert("error", {
    //             msg: "Select ICD-10 diagnosis!"
    //         });
    //         return false;
    //     }
    // });

    // $('.reason_referral').on('change', function() {
    //     var value = $(this).val();
    //     if (value == '-1') {
    //         $("#other_reason_referral").html(loading);
    //         setTimeout(function() {
    //             $("#other_reason_referral").html('<span>Other Reason for Referral:</span>\n' +
    //                 '                                <br />\n' +
    //                 '                                <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7" required></textarea>')
    //         }, 500);
    //         $("#other_reason_referral").show();
    //     } else {
    //         clearOtherReasonReferral();
    //     }
    // });

</script>
@include('script.datetime')
@include('script.edit_referred')