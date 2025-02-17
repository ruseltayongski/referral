<?php
$user = Session::get('auth');
$reason_for_referral = \App\ReasonForReferral::get();
$facilities = \App\Facility::select('id','name')
    ->where('status',1)
    ->where('referral_used','yes')
    ->orderBy('name','asc')->get();

?>

<style>
   /* .file-upload {
        background-color: #ffffff;
        width: 600px;
        margin: 0 auto;
        padding: 20px;
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
        background-color: #1FB264;
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
        max-height: 200px;
        max-width: 200px;
        margin: auto;
        padding: 20px;
    }

    .remove-image {
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

    .remove-image:hover {
        background: #c13b2a;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .remove-image:active {
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

    #glasgow_pregnant_info_table_1, tr td:nth-child(1) {width: 35%;}
    #glasgow_pregnant_info_table_2 tr td:nth-child(2) {width: 35%;}  

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
    .unclickable {
        pointer-events: none; /* Disables click actions */
        background-color: #f0f0f0;
        color: black;
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
</style>


<form action="{{ url('update-referral', ['patient_id' => $patient_id, 'id' => $id, 'type'=>'pregnant', 'status' => $status]) }}" method="POST" class="edit_normal_form" enctype="multipart/form-data">  
                @include('include.header_form')<br>
                <div class="form-group-sm form-inline">
                        {{ csrf_field() }}
                      
                        <input type="hidden" name="mother_id" value="{{ $patient_id }}">
                        <input type="hidden" name="form_type" value="pregnant">
                        <input type="hidden" name="referral_status" value="{{ $referral_status }}">
                        <input type="hidden" class="pt_age" />
                        <input type="hidden" name="referred_date" class="referred_date" value="{{ date('Y-m-d H:i:s') }}" />
                        <input type="hidden" name="code" value="" />
                        <input type="hidden" name="source" value="{{ $source }}" />
                        <input type="hidden" class="referring_name" value="{{ $myfacility->name }}" />
                        <input type="hidden" name="username" value="{{ $username }}">
                        <input type="hidden" name="baby_id" value="{{ $form['baby']->baby_id }}">
                        <br>
                        <div class="row" style="margin: 5px;">
                            <div class="col-md-4">
                                <small class="text-success">Name of Referring Facility</small><br>
                                &nbsp;<span>{{ $form['pregnant']->referring_facility }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Address</small><br>
                                &nbsp;<span>{{ $form['pregnant']->facility_brgy }}, {{ $form['pregnant']->facility_muncity }}, {{ $form['pregnant']->facility_province }}</span>
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
                                <span>{{ $form['pregnant']->referred_date }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Name of Patient</small><br>
                                <span class="patient_name">{{ $form['pregnant']->woman_name }}</span>
                            </div>
                            <div class="col-md-4">
                                <small >Address</small><br>
                                <span class="patient_address">{{ $form['pregnant']->patient_address }}</span>
                            </div>
                        </div>
                        <br>
                        <div class="row" style="margin: 5px;">
                            <div class="col-md-4">
                            <small class="text-success">REFERRED TO:</small><span class="text-red"><b>*</b></span><br>
                                <input type="hidden" name="old_facility" value="{{ $form['pregnant']->referred_facility_id }}">
                                <select name="referred_to" class="select2 edit_facility_pregnant form-control" style="width:250px" require>
                                    <option value="">Select Facility...</option>
                                    @foreach($facilities as $row)
                                        <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                                {{--<span class="referred_name">{{ $form['pregnant']->referred_facility }}</span>--}}
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">DEPARTMENT: </small><span class="text-red"><b>*</b></span><br>&emsp;
                                <select name="department_id" class="form-control-select edit_department_pregnant" required>
                                    <option value="">Select Department...</option>
                                </select>
                                {{--<span class="department_name">{{ $form['pregnant']->department }}</span>--}}
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Address</small><br>
                                <span class="text-yellow facility_address">{{ $referred_to_address }}</span>
                            </div>
                        </div>
                        <br>
                        <div class="row" style="margin: 5px;">
                            <div class="col-md-4">
                                <small class="text-success">Age</small><br>
                                <span class="patient_age">
                                    @if($form['pregnant']->woman_age == 1)
                                        {{ $form['pregnant']->woman_age }} year old
                                    @else
                                        {{ $form['pregnant']->woman_age }} years old
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Sex</small> <span class="text-red">*</span><br>
                                <select name="patient_sex" class="patient_sex form-control" style="width: 100%;" required>
                                    @if( $form['pregnant']->sex == "Male")
                                    <option>Male</option>
                                    <option>Female</option>
                                    @else
                                    <option>Female</option>
                                    <option>Male</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Civil Status</small> <span class="text-red">*</span><br>
                                <select name="civil_status" style="width: 100%;" class="civil_status form-control" required>
                                @if ( $civil_status == "Single")
                                    <option>Single</option>
                                    <option>Married</option>
                                    <option>Divorced</option>
                                    <option>Separated</option>
                                    <option>Widowed</option>
                                    @elseif($civil_status == "Married")
                                    <option>Married</option>
                                    <option>Divorced</option>
                                    <option>Separated</option>
                                    <option>Widowed</option>
                                    <option>Single</option>
                                    @elseif($civil_status == "Divorced")
                                    <option>Divorced</option>
                                    <option>Separated</option>
                                    <option>Widowed</option>
                                    <option>Single</option>
                                    <option>Married</option>
                                    @elseif($civil_status == "Separated")
                                    <option>Separated</option>
                                    <option>Widowed</option>
                                    <option>Single</option>
                                    <option>Married</option>
                                    <option>Divorced</option>
                                    @elseif($civil_status == "Widowed")
                                    <option>Widowed</option>
                                    <option>Single</option>
                                    <option>Married</option>
                                    <option>Divorced</option>
                                    <option>Separated</option>
                                    @else
                                    <option value="">Select...</option>    
                                    <option>Single</option>
                                    <option>Married</option>
                                    <option>Divorced</option>
                                    <option>Separated</option>
                                    <option>Widowed</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row" style="margin: 5px;">
                            <div class="col-md-4">
                                <small class="text-success">Covid Number</small><br>
                                <input type="text" name="covid_number" style="width: 100%;" value="{{ $form['pregnant']->covid_number }}">
                            </div>
                            <div class="col-md-4">
                                <small >Clinical Status</small><br>
                                <select name="clinical_status" class="form-control-select" style="width: 100%;">

                                @if($form['pregnant']->refer_clinical_status == "asymptomatic")
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    @elseif($form['pregnant']->refer_clinical_status == "mild")
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    <option value="asymptomatic">Asymptomatic</option>
                                    @elseif($form['pregnant']->refer_clinical_status == "moderate")
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    @elseif($form['pregnant']->refer_clinical_status == "severe")
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    @elseif($form['pregnant']->refer_clinical_status == "critical")
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
                                <small class="text-success">Surveillance Category</small><br>
                                <select name="sur_category" class="form-control-select" style="width: 100%;">
                                @if ($form['pregnant']->refer_sur_category == "contact_pum")
                                    <option value="contact_pum">Contact (PUM)</option>
                                    <option value="suspect">Suspect</option>
                                    <option value="probable">Probable</option>
                                    <option value="confirmed">Confirmed</option>
                                @elseif ($form['pregnant']->refer_sur_category == "suspect")
                                    <option value="suspect">Suspect</option>
                                    <option value="probable">Probable</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="contact_pum">Contact (PUM)</option>
                                @elseif ($form['pregnant']->refer_sur_category == "probable")
                                    <option value="probable">Probable</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="contact_pum">Contact (PUM)</option>
                                    <option value="suspect">Suspect</option>
                                @elseif ($form['pregnant']->refer_sur_category == "confirmed")
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
                        </div><br><br>

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width:100%;" data-toggle="collapse" data-target="#patient_treatment_give_time" aria-expanded="false" aria-controls="patient_treatment_give_time">
                                    <b>TREATMENTS GIVE TIME</b><i> (required)</i><span class="text-red">*</span>
                                    <span class="pull-right"><i class="fa fa-plus"></i></span>    
                                </button><br><br>
                                </div>
                               
                                <div class="collapse" id="patient_treatment_give_time" style="width: 100%;">
                                <small class="text-success"><b>MAIN REASON FOR REFERRAL:</b> </small>
                                    <div class="container-referral">
                                    <label><input <?php if($form['pregnant']->woman_reason=='None') echo 'checked'; ?> type="radio" name="woman_reason" value="None" checked /> None </label>
                                    <label><input <?php if($form['pregnant']->woman_reason=='Emergency') echo 'checked'; ?> type="radio" name="woman_reason" value="Emergency" /> Emergency </label>
                                    <label><input <?php if($form['pregnant']->woman_reason=='Non-Emergency') echo 'checked'; ?> type="radio" name="woman_reason" value="Non-Emergency" /> Non-Emergency </label>
                                    <label><input <?php if($form['pregnant']->woman_reason=='To accompany the baby') echo 'checked'; ?> type="radio" name="woman_reason" value="To accompany the baby" /> To accompany the baby </label>
                                    </div><br>
                                
                                    <div class="continer-referral">
                                    <small class="text-success"><b>MAJOR FINDINGS: </b></small><i> (Clinical and BP,Temp,Lab)</i>
                                    <textarea class="form-control woman_major_findings" name="woman_major_findings" style="resize: none;width: 100%" rows="5" required>{{ $form['pregnant']->woman_major_findings }}</textarea>
                                    </div><br>

                                    <div class="container-referral" style="padding:5px">
                                    <small class="text-success"><b>BEFORE REFERRAL</b></small>
                                    <input type="text" class="form-control woman_before_treatment" name="woman_before_treatment" placeholder="Treatment Given" />
                                    <input type="text" class="form-control form_datetime woman_before_given_time" name="woman_before_given_time" placeholder="Date/Time Given" /></br>
                                    <small class="text-success"><b>DURING TRANSPORT </b></small>
                                        <input type="text" class="form-control" name="woman_during_transport" placeholder="Treatment Given" />
                                        <input type="text" class="form-control form_datetime woman_transport_given_time" name="woman_transport_given_time" placeholder="Date/Time Given" />
                                    </div><br>


                                    <small class="text-success"><b>INFORMATION GIVEN TO THE WOMAN AND COMPANION ABOUT THE REASON FOR REFERRAL</b></small>
                                        <textarea class="form-control woman_information_given" name="woman_information_given" style="resize: none;width: 100%" rows="5">{{ $form['pregnant']->woman_information_given }}</textarea><br><br>
                                </div>
                            </div>
                        </div>          

                        <!-- <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_illness_history_pregInfo" aria-expanded="false" aria-controls="collapse_illness_history_pregInfo">
                                        <small class="text-success">HISTORY OF PRESENT ILLNESS</small><i> (required)</i><span class="text-red">*</span>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                              
                                <div class="collapse" id="collapse_illness_history_pregInfo" style="width: 100%">
                                    <small class="text-success">CASE SUMMARY:</small>
                                    <textarea class="form-control" name="case_summary" style="resize: none;width: 100%;" rows="7" required>{{$form['pregnant']->case_summary}}</textarea><br><br>
                                    <small class="text-success">CHIEF COMPLAINTS:</small>
                                    <textarea class="form-control" name="reco_summary" style="resize: none;width: 100%;" rows="7" required>{{$form['pregnant']->reco_summary}}</textarea><br><br>
                                </div>
                            </div>
                        </div> -->

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_diagnosis_pregInfo" aria-expanded="false" aria-controls="collapse_diagnosis_pregInfo">
                                        <b>DIAGNOSIS</b><i> (required)</i><span class="text-red">*</span>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                    </div>
                                    <div class="collapse " id="collapse_diagnosis_pregInfo" style="width: 100%">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <small class="text-success"><b>DIAGNOSIS: </b></small><span class="text-red">*</span>
                                                <small><b><input id="diag_prompt" style="text-align: center; color: red; border-color: transparent; width:70%;" value="SELECT ICD-10 / OTHER DIAGNOSIS" readonly></b></small><br><br>
                                                <a data-toggle="modal" data-target="#icd-modal" type="button" class="btn btn-sm btn-success" onclick="searchICD10()">
                                                    <i class="fa fa-medkit"></i> Add ICD-10
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success add_notes_btn" onclick="addNotesDiagnosis()"><i class="fa fa-plus"></i> Add notes in diagnosis</button></span>
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
                                                                <input type="hidden" id="icd_ids" name="icd_ids[]" value="{{ $i->id }}">
                                                                <small> => {{ $i->description }}</small><br>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                            </div>
                                        </div><br>

                                        <div class="row notes_diag">
                                            <div colspan="col-md-12">
                                                <small class="text-success"><b>Notes in Diagnosis: </b></small>&emsp;
                                                <input type="hidden" name="notes_diag_cleared" id="notes_diag_cleared" value="">
                                                <button type="button" class="btn btn-xs btn-info" onclick="clearNotesDiagnosisPregnant()">Clear Notes Diagnosis</button>
                                                <textarea class="form-control notes_diagnosis" name="notes_diagnosis" style="resize: none; width: 100%" rows="5">{{ $form['pregnant']->notes_diagnoses }}</textarea>
                                            </div>
                                        </div>
                                 
                                        <div class="row other_diag" style="padding-top: 10px">
                                            <div class="col-md-12">
                                                <small class="text-success"><b>Other Diagnosis: </b></small>&emsp;
                                                <input type="hidden" name="other_diag_cleared" class="other_diag_cleared" value="">
                                                <button type="button" class="btn btn-xs btn-warning" onclick="clearOtherDiagnosisPregnant()">Clear other diagnosis</button>
                                                <textarea class="form-control other_diagnosis" name="other_diagnosis" style="resize: none;width: 100%" rows="5">{{ $form['pregnant']->other_diagnoses }}</textarea>
                                            </div>
                                        </div><br>
                                </div>
                            </div><br>
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
                                                <input class="form-check-input" id="comor_none_cbox" name="comor_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="None" <?= isChecked($past_medical_history, 'commordities', 'None'); ?>>
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

                        <div class="row" style="margin: 5px;" id="baby_show_pregnant">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#baby_collapsed_pregnant" aria-expanded="false" aria-controls="baby_collapsed_pregnant">
                                        <div class="web-view"><b>BABY DELIVERED</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view"><b>BABY DELIVERED</b><br> <i> (as applicable)</i></span></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="baby_collapsed_pregnant" style="width: 100%;">

                                <div class="container-referral" style="padding:5px">
                                    <small class="text-success"><b>NAME:</b></small><br />
                                    <input type="text" class="form-control" style="width: 25%" name="baby_fname" placeholder="First Name" value="{{$form['baby']->baby_fname}}"/><br>
                                            <input type="text" class="form-control" style="width: 25%" name="baby_mname" placeholder="Middle Name" value="{{$form['baby']->baby_mname}}"/><br>
                                            <input type="text" class="form-control" style="width: 25%" name="baby_lname" placeholder="Last Name" value="{{$form['baby']->baby_lname}}"/><br>
                                    <br/><small class="text-success"><b>DATE AND HOUR OF BIRTH: </b></small>
                                    <input type="text" class="form-control  form_datetime" name="baby_dob" placeholder="Date/Time" value="{{$form['baby']->baby_dob}}"/><br>
                                    <small class="text-success"><b>GESTATIONAL AGE: </b></small>
                                    <input type="text" class="form-control" name="baby_weight" placeholder="kg or lbs" value="{{$form['baby']->weight}}"/>
                                    <small class="text-success"><b>BIRTH WEIGHT: </b></small>
                                    <input type="text" class="form-control" name="baby_gestational_age" placeholder="age" value="{{$form['baby']->gestational_age}}"/>
                                </div><br>
                               
                                <div class="container-referral" style="padding:5px">
                                    <small class="text-success"><b>MAIN REASON FOR REFERRAL</b></small>
                                    @if($form['baby']->baby_reason === "None")
                                            <label><input type="radio" name="baby_reason" value="None" checked /> None </label>
                                            <label><input type="radio" name="baby_reason" value="Emergency" /> Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="Non-Emergency" /> Non-Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="To accompany the mother" /> To accompany the mother </label>
                                            @elseif($form['baby']->baby_reason === "Emergency")
                                            <label><input type="radio" name="baby_reason" value="None"/> None </label>
                                            <label><input type="radio" name="baby_reason" value="Emergency" checked/> Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="Non-Emergency" /> Non-Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="To accompany the mother" /> To accompany the mother </label>
                                            @elseif($form['baby']->baby_reason === "Non-Emergency")
                                            <label><input type="radio" name="baby_reason" value="None"/> None </label>
                                            <label><input type="radio" name="baby_reason" value="Emergency" /> Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="Non-Emergency" checked/> Non-Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="To accompany the mother" /> To accompany the mother </label>
                                            @elseif($form['baby']->baby_reason === "to accompany the mother")
                                            <label><input type="radio" name="baby_reason" value="None"/> None </label>
                                            <label><input type="radio" name="baby_reason" value="Emergency" /> Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="Non-Emergency" /> Non-Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="To accompany the mother" checked/> To accompany the mother </label>
                                            @else
                                            <label><input type="radio" name="baby_reason" value="None" checked /> None </label>
                                            <label><input type="radio" name="baby_reason" value="Emergency" /> Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="Non-Emergency" /> Non-Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="To accompany the mother" /> To accompany the mother </label>
                                            @endif
                                </div><br>
                        
                                <small class="text-success"><b>MAJOR FINDINGS</b></small>
                                <textarea class="form-control" name="baby_major_findings" style="resize: none;width: 100%" rows="5">{{$form['baby']->baby_major_findings}}</textarea><br><br>
                                
                                <small class="text-success"><b>TREATMENTS GIVE TIME</b></small>
                                <div class="container-referral" style="padding: 5px;">  

                                <small class="text-success"><b>LAST (BREAST) FEED (TIME):</b></small>
                                <input type="text" class="form-control form_datetime" style="width: 100%" name="baby_last_feed" placeholder="Date/Time" value="{{$form['baby']->baby_last_feed}}"/><br>
                                <small class="text-success"><b>BEFORE REFERRAL</b></small>
                                <input type="text" class="form-control" name="baby_before_treatment" placeholder="Treatment Given" value="{{$form['baby']->baby_before_treatment}}"/>
                                <input type="text" class="form-control form_datetime" name="baby_before_given_time" placeholder="Date/Time Given" value="{{$form['baby']->baby_before_given_time}}"/><br>
                                <small class="text-success"><b>DURING TRANSPORT</b></small>
                                <input type="text" class="form-control" name="baby_during_transport" placeholder="Treatment Given" value="{{$form['baby']->baby_during_transport}}"/>
                                <input type="text" class="form-control form_datetime" name="baby_transport_given_time" placeholder="Date/Time Given" value="{{$form['baby']->baby_transport_given_time}}"/>
                                </div><br>
                                 
                                <small class="text-success"><b>INFORMATION GIVEN TO THE WOMAN AND COMPANION ABOUT THE REASON FOR REFERRAL</b></small>
                                <textarea class="form-control" name="baby_information_given" style="resize: none;width: 100%" rows="5" value="baby_information_given">{{$form['baby']->baby_information_given}}</textarea><br><br>
                                </div>
                                </div>
                            
                            </div>
                        </div> 

                            {{--TODO: COMPARE AGE IF >= 9 AND ONLY IF PT IS WOMAN--}}
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
                                                <label for="mens_reg_radio">Regular</label>
                                                <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_cycle_irreg" value="irregular" <?= isChecked($obstetric_and_gynecologic_history, 'menstrual_cycle', 'irregular'); ?>>
                                                <label for="mens_cycle_irreg">Irregular</label>
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
                                                <input class="form-check-input" id="contraceptive_withdrawal_pregnant_info_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_withdrawal_cbox" value="Yes" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'Withdrawal'); ?>> Withdrawal
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_injections_pregnant_info_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_injections_cbox" value="Yes" <?= isChecked($obstetric_and_gynecologic_history, 'contraceptive_history', 'Injections'); ?>> Injections
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
                                        <input type="number" step="0.01" style="width:15%;" name="parity_lnmp" value="<?php echo $parity_lnmp_value; ?>">&emsp;&emsp;&emsp;
                                        <small class="text-success">EDC</small><i>(if pregnant)</i>
                                        <input type="number" step="0.01" style="width:15%;" name="parity_edc_ifpregnant" value="<?php echo $parity_edc_value; ?>">
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
                                                        <input class="form-control" id="gestation_pregnant_info" type="text" name="pregnancy_history_gestation[]" value="{{ $preg['pregnancy_gestation_completed'] }}">
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
                                                        <input class="form-control" type="number" min="0" step="0.01" name="pregnancy_history_birthweight[]" value="{{ $preg['pregnancy_birth_weight'] }}">
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
                                                Specify drugs taken:
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
                                    <small class="text-success"><i>Specify number of doses given and time of last dose given.</i></small>
                                    <textarea class="form-control" name="current_meds" style="resize: none;width: 100%;" rows="5"><?php echo htmlspecialchars($personal_and_social_history->current_medications); ?></textarea><br><br>
                                </div>
                            </div>
                        </div>

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
                                                <input class="form-check-input" id="lab_ultrasound_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ultrasound_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'ULTRA SOUND'); ?>>
                                                <span> ULTRA SOUND</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_ctscan_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ctscan_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'CT SCAN'); ?>>
                                                <span>CT SCAN</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_ctgmonitoring_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ctgmonitoring_cbox" value="Yes" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'CTG MONITORING'); ?>>
                                                <span>CTG MONITORING</span>
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-check-input" id="lab_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_others_cbox" <?= isChecked($pertinent_laboratory, 'pertinent_laboratory_and_procedures', 'Others'); ?>> Others:
                                                <textarea id="lab_others" class="form-control" name="lab_procedure_other" style="resize: none;" rows="2" name="lab_procedure_other"><?php echo htmlspecialchars($pertinent_laboratory->lab_procedure_other); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-12">
                                            <div class="with_file_attached hide">
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
                                        {{--                <a href="{{ asset($file_path) }}" class="reason" style="font-size: 12pt;" download>{{ $file_name }}</a>--}}
                                            </div>
                                        <div class="no_file_attached">
                                                <small class="text-success">FILE ATTACHMENTS:</small> &emsp;
                                                <button type="button" class="btn btn-md btn-danger" id="preg_remove_files_" onclick="removeFilePregnant(2)">Remove Files</button><br><br>
                                                <div class="pregnant_file_attachment_">
                                                    <div class="col-md-3" id="pregnant_upload_1">
                                                        <div class="file-upload">
                                                            <div class="text-center image-upload-wrap" id="pregnant_image-upload-wrap_1">
                                                                <input class="file-upload-input" multiple type="file" name="file_upload[]" onchange="readURLPregnant(this, 1, 2);" accept="image/png, image/jpeg, image/jpg, image/gif, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/pdf" />
                                                                <img src="{{ asset('resources/img/add_file.png') }}" style="width: 50%; height: 50%;">
                                                            </div>
                                                            <div class="file-upload-content" id="pregnant_file-upload-content_1">
                                                                <img class="file-upload-image" id="pregnant_file-upload-image_1"/>
                                                                <div class="image-title-wrap">
                                                                    <small class="image-title" id="pregnant_image-title_1" style="display:block; word_-wrap: break-word_;">Uploaded File</small>
                                                                    {{--<button type="button" id="pregnant_remove_upload1" onclick="removeUploadPregnant(1, 2)" class="btn-sm remove-image">Remove</button>--}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                            {{--<div class="file-upload">--}}
                                                {{--<div class="image-upload-wrap">--}}
                                                    {{--<input class="file-upload-input" type='file' name="file_upload" onchange="readURL(this);" accept="image/png, image/jpeg, image/jpg, image/gif, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/pdf"/>--}}
                                                    {{--<div class="drag-text">--}}
                                                        {{--<h3>Drag and drop a file or select add Image</h3>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<div class="file-upload-content">--}}
                                                    {{--<img class="file-upload-image" src="#" alt="your image" />--}}
                                                    {{--<div class="image-title-wrap">--}}
                                                        {{--<button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        </div>
                                        </div><br>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
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
                                                <input class="form-check-input" id="rs_skin_hairchange_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_hairchange_cbox" value="Yes" <?= isChecked($review_of_system, 'skin', 'Rashes'); ?>>
                                                <span> Change in hair or nails</span>
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
                                        </div>
                                    </div><br>

                                    <small class="text-success"><b>PERIPHERAL </b></small>
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
                                                <input class="form-check-input" id="rs_peri_edema_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_edema_cbox" value="Yes" <?= isChecked($review_of_system, 'peripheral_vascular', 'Edema'); ?>>
                                                <span>Edema</span>
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
                                                <input class="form-check-input" id="rs_muscle_fracture_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_fracture_cbox" value="Yes" <?= isChecked($review_of_system, 'musculoskeletal', 'Fractured'); ?>>
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
                                        </div>
                                    </div><br>

                                    <small class="text-success"><d>ENDOCRINE</d></small>
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
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
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
                                                Specify Diets: <textarea class="form-control" name="diet" style="resize: none;width: 100%;" rows="3"><?php echo htmlspecialchars($nutritional_status->specify_diets); ?></textarea><br><br>
                                            </div>
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
                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_vital_signs" aria-expanded="false" aria-controls="collapse_vital_signs">
                                        <b>LATEST VITAL SIGNS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_vital_signs" style="width: 100%;">
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                              <small class="text-success">  Temperature: </small><input type="number" step="0.01" style="width:30%;" min="0" name="vital_temp" value="<?php echo htmlspecialchars( $latest_vital_signs->temperature); ?>"> &#176;C
                                            </div>
                                            <div class="col-md-4">
                                              <small class="text-success">  Pulse Rate/Heart Rate: </small><input type="number" step="0.01" style="width:30%;" min="0" name="vital_pulse" value="<?php echo htmlspecialchars( $latest_vital_signs->pulse_rate); ?>"> bpm
                                            </div>
                                            <div class="col-md-4">
                                              <small class="text-success">  Respiratory Rate: </small><input type="number" step="0.01" style="width:30%;" min="0" name="vital_respi_rate" value="<?php echo htmlspecialchars( $latest_vital_signs->respiratory_rate); ?>"> cpm
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-4">
                                            <small class="text-success"> Blood Pressure:</small>
                                                <input type="number" id="systolic_pregnant_info" placeholder="Systolic (e.g., 100)" 
                                                        style="width:18%;" min="0" max="300" 
                                                        value="<?php echo htmlspecialchars($systolic); ?>" 
                                                        oninput="updateBloodPressure()"> /

                                                    <input type="number" id="diastolic_pregnant_info" placeholder="Diastolic (e.g., 90)" 
                                                        style="width:18%;" min="0" max="200" 
                                                        value="<?php echo htmlspecialchars($diastolic); ?>" 
                                                        oninput="updateBloodPressure()">mmHg

                                                    <!-- Hidden input to store the combined value -->
                                                    <input type="hidden" name="vital_bp" id="vital_bp_pregnant_info" 
                                                        value="<?php echo htmlspecialchars($latest_vital_signs->blood_pressure); ?>">
                                            </div>
                                            <div class="col-md-4">
                                              <small class="text-success">  O2 Saturation </small><input type="number" step="0.01" style="width:30%;" min="0" name="vital_oxy_saturation" value="<?php echo htmlspecialchars( $latest_vital_signs->oxygen_saturation); ?>"> %
                                            </div>
                                        </div><br>
                                    </div><br>
                                </div>
                            </div>
                        </div>

                        <script>
                            function updateBloodPressure() {
                                // Get systolic and diastolic values
                                const systolic = document.getElementById('systolic_pregnant_info').value;
                                const diastolic = document.getElementById('diastolic_pregnant_info').value;
                                
                                // Combine into "100/90" format
                                document.getElementById('vital_bp_pregnant_info').value = systolic + '/' + diastolic;
                            }
                        </script>

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_glasgow" aria-expanded="false" aria-controls="collapse_glasgow">
                                        <b>GLASGOW COMA SCALE</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_glasgow" style="width: 100%;">
                                    <small class="text-success"> <b>Pupil Size Chart </b></small> &emsp;
                                    <input type="button" class="btn-m btn-warning btn-rounded" onclick="resetPupilSize()" value="Reset">
                                    <div class="container-referral">
                                        <div class="row web-view">
                                            <div class="col-lg-1"></div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant1">
                                                    <b>1</b><br>
                                                    <span class="glasgow-dot" style="height: 6px; width: 6px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant1" value="1" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '1'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant2">
                                                    <b>2</b><br>
                                                    <span class="glasgow-dot" style="height: 10px; width: 10px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant2" value="2" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '2'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant3">
                                                    <b>3</b><br>
                                                    <span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant3" value="3" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '3'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant4">
                                                    <b>4</b><br>
                                                    <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant4" value="4" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '4'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant5">
                                                    <b>5</b><br>
                                                    <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant5" value="5" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '5'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant6">
                                                    <b>6</b><br>
                                                    <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant6" value="6" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '6'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregtnant7">
                                                    <b>7</b><br>
                                                    <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant7" value="7" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '7'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant8">
                                                    <b>8</b><br>
                                                    <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant8" value="8" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '8'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant9">
                                                    <b>9</b><br>
                                                    <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant9" value="9" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '9'); ?>>
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <b>10</b><br>
                                                <label for="glasgow_pregnant10">
                                                    <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                </label>
                                                <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant10" value="10" <?= isChecked($glasgocoma_scale, 'pupil_size_chart', '10'); ?>>
                                            </div>
                                        </div>
                                        <div class="mobile-view">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <b>1</b>
                                                    <label for="glasgow_pregnant_1">
                                                        <span class="glasgow-dot" style="height: 6px; width: 6px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_1" value="1">&emsp;&emsp;

                                                    <b>2</b>
                                                    <label for="glasgow_pregnant_2">
                                                        <span class="glasgow-dot" style="height: 10px; width: 10px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_2" value="2">&emsp;&emsp;

                                                    <b>3</b>
                                                    <label for="glasgow_pregnant_3">
                                                        <br><span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_3" value="3">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <b>4</b>
                                                    <label for="glasgow_pregnant_4">
                                                        <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_4" value="4">&emsp;&emsp;

                                                    <b>5</b>
                                                    <label for="glasgow_pregnant_5">
                                                        <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_5" value="5">&emsp;&emsp;

                                                    <b>6</b>
                                                    <label for="glasgow_pregnant_6">
                                                        <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_6" value="6">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-1">
                                                    <b>7</b>
                                                    <label for="glasgow_pregnant_7">
                                                        <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_7" value="7">&emsp;&emsp;

                                                    <b>8</b>
                                                    <label for="glasgow_pregnant_8">
                                                        <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_8" value="8">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-1">
                                                    <b>9</b>
                                                    <label for="glasgow_pregnant_9">
                                                        <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_9" value="9">&emsp;&emsp;

                                                    <b>10</b>
                                                    <label for="glasgow_pregnant_10">
                                                        <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_10" value="10">
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
                                                            <th id="glasgow_pregnant_info_table_1" style="text-align: center">ADULT AND CHILD</th>
                                                            <th id="glasgow_pregnant_info_table_2" style="text-align: center">INFANT (2 MONTHS)</th>
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
                                                            <th id="glasgow_pregnant_info_table_1" style="width:35%; text-align: center">ADULT AND CHILD</th>
                                                            <th id="glasgow_pregnant_info_table_2" style="width:40%; text-align: center">INFANT (2 MONTHS)</th>
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
                                                            <th id="glasgow_pregnant_info_table_1" style="width:40%; text-align: center">ADULT AND CHILD</th>
                                                            <th id="glasgow_pregnant_info_table_2" style="text-align: center">INFANT (2 MONTHS)</th>
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
                                            <input class="number" name="gcs_score" id="gcs_score_pregnant_info" style="text-align: center" min="0" value="<?php echo htmlspecialchars($glasgocoma_scale->gsc_score); ?>" readonly>
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin: 5px;">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_reason_referral_pregInfo" aria-expanded="false" aria-controls="collapse_reason_referral_pregInfo">
                                       <b>REASON FOR REFERRAL</b><i> (required)</i><span class="text-red">*</span>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_reason_referral_pregInfo" style="width: 100%;">
                                <small class="text-success"> <i>Select reason for referral:</i> </small>
                                    <div class="container-referral">
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
                        </div> 

                        <div class="form-footer pull-right" style="margin: 10px;">
                            <button type="submit" id="edit_save_btn" class="btn btn-primary btn-flat btn-submit"><i class="fa fa-send"></i> Update</button>
                            </div>
                            <div class="clearfix"></div> 
                        </div>{{--/.form-group--}}
                </div> {{--/.jim-content--}}
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



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

<script>

     // Uncollapse the REASON FOR REFERRAL section
    //  const referralCollapse = document.getElementById("collapse_reason_referral_pregInfo");
    // const referralButton = document.querySelector("[data-target='#collapse_reason_referral_pregInfo']");
    // if (referralCollapse && referralButton) {
    //     referralCollapse.classList.add("show");
    //     referralButton.setAttribute("aria-expanded", "true");
    // } else {
    //     console.warn("Reason for Referral section or button not found.");
    // }

    // Uncollapse the HISTORY OF PRESENT ILLNESS section
    // const illnessCollapse = document.getElementById("collapse_illness_history_pregInfo");
    // const illnessButton = document.querySelector("[data-target='#collapse_illness_history_pregInfo']");
    // if (illnessCollapse && illnessButton) {
    //     illnessCollapse.classList.add("show");
    //     illnessButton.setAttribute("aria-expanded", "true");
    // } else {
    //     console.warn("History of Present Illness section or button not found.");
    // }

    // Uncollapse the DIAGNOSIS section
    // const diagnosisCollapse = document.getElementById("collapse_diagnosis_pregInfo");
    // const diagnosisButton = document.querySelector("[data-target='#collapse_diagnosis_pregInfo']");
    // if (diagnosisCollapse && diagnosisButton) {
    //     diagnosisCollapse.classList.add("show");
    //     diagnosisButton.setAttribute("aria-expanded", "true");
    // } else {
    //     console.warn("Diagnosis section or button not found.");
    // }

    //  // Uncollapse the TREATMENTS GIVE TIME section
    //  const treatmentCollapse = document.getElementById("patient_treatment_give_time");
    //     const treatmentButton = document.querySelector("[data-target='#patient_treatment_give_time']");
    // if (treatmentCollapse && treatmentButton) {
    //     treatmentCollapse.classList.add("show");
    //     treatmentButton.setAttribute("aria-expanded", "true");
    // }

    // $('.select_facility').select2();
    //    $('#pedia_show').hide();
    //    $('#menarche_show').hide();
    //
    //    var pt_age = parseInt($('.pt_age').val(), 10);
    //    if(pt_age > 18)
    //        $('#pedia_show').show();
    //    if($('.patient_sex').val() === "Female")
    //        $('#menarche_show').show();

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
            '<option value="">Choose...</option>\n' +
            select_year +
            '</select></td>\n' +
            '<td><input class="form-control" id="gestation_pregnant_info" type="text" name="pregnancy_history_gestation[]"></td>\n' +
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
   // Initialize variables with existing data or defaults
function initializeGlasgowScale() {
    const existingScore = parseInt($('#gcs_score_pregnant_info').val()) || 0;
    
    // Initialize last values based on checked radio buttons
    let last_motor = parseInt($('input[name="motor_radio"]:checked').val()) || 0;
    let last_verbal = parseInt($('input[name="verbal_radio"]:checked').val()) || 0;
    let last_eye = parseInt($('input[name="eye_radio"]:checked').val()) || 0;
    
    // Store original values
    const original_motor = last_motor;
    const original_verbal = last_verbal;
    const original_eye = last_eye;
    const original_score = existingScore;
    
    function resetPupilSize() {
        // Reset to original values instead of zeroing everything
        $('input[name="glasgow_pupil_btn"]').prop('checked', false);
        
        if (original_motor) {
            $(`input[name="motor_radio"][value="${original_motor}"]`).prop('checked', true);
            last_motor = original_motor;
        } else {
            $('input[name="motor_radio"]').prop('checked', false);
            last_motor = 0;
        }
        
        if (original_verbal) {
            $(`input[name="verbal_radio"][value="${original_verbal}"]`).prop('checked', true);
            last_verbal = original_verbal;
        } else {
            $('input[name="verbal_radio"]').prop('checked', false);
            last_verbal = 0;
        }
        
        if (original_eye) {
            $(`input[name="eye_radio"][value="${original_eye}"]`).prop('checked', true);
            last_eye = original_eye;
        } else {
            $('input[name="eye_radio"]').prop('checked', false);
            last_eye = 0;
        }
        
        $('#gcs_score_pregnant_info').val(original_score);
    }

    $('input[name="motor_radio"]').on('change', function() {
        const motor = parseInt($(this).val(), 10);
        const gcs = parseInt($('#gcs_score_pregnant_info').val(), 10) || 0;
        let total = 0;
        
        if (last_motor === 0) {
            total = gcs + motor;
        } else {
            total = (gcs - last_motor) + motor;
        }
        
        last_motor = motor;
        check_total(total);
    });

    $('input[name="verbal_radio"]').on('change', function() {
        const verbal = parseInt($(this).val(), 10);
        const gcs = parseInt($('#gcs_score_pregnant_info').val(), 10) || 0;
        let total = 0;
        
        if (last_verbal === 0) {
            total = gcs + verbal;
        } else {
            total = (gcs - last_verbal) + verbal;
        }
        
        last_verbal = verbal;
        check_total(total);
    });

    $('input[name="eye_radio"]').on('change', function() {
        const eye = parseInt($(this).val(), 10);
        const gcs = parseInt($('#gcs_score_pregnant_info').val(), 10) || 0;
        let total = 0;
        
        if (last_eye === 0) {
            total = gcs + eye;
        } else {
            total = (gcs - last_eye) + eye;
        }
        
        last_eye = eye;
        check_total(total);
    });

    function check_total(total) {
        if (total >= 16) {
            // Instead of resetting to 0, revert to previous valid state
            $('#gcs_score_pregnant_info').val(original_score);
            resetPupilSize();
        } else {
            $('#gcs_score_pregnant_info').val(total);
        }
    }

    // Make reset function available globally
    window.resetPupilSize = resetPupilSize;
}

// Initialize when document is ready
$(document).ready(initializeGlasgowScale);

    /* *****REVIEW OF SYSTEMS***** */
    /* SKIN */
    $('#rs_skin_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox').prop('checked', true);
            $('#rs_skin_none_cbox').prop('checked', false);
        } else
            $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox').prop('checked', false);
    });
    $('#rs_skin_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_skin_all_cbox, #rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox').prop('checked', false);
    });
    $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox').on('click', function() {
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

    /* PERIPHERAL VASCULAR */
    $('#rs_peri_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox, #rs_peri_edema_cbox').prop('checked', true);
            $('#rs_peri_none_cbox').prop('checked', false);
        } else
            $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox').prop('checked', false);
    });
    $('#rs_peri_none_cbox').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_peri_all_cbox, #rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox, #rs_peri_edema_cbox').prop('checked', false);
    });
    $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox, #rs_peri_edema_cbox').on('click', function() {
        $('#rs_peri_all_cbox, #rs_peri_none_cbox').prop('checked', false);
    });

    /* MUSCULOSKELETAL */
    $('#rs_muscle_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_muscle_spasm_cbox, #rs_muscle_sizeloss_cbox, #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').prop('checked', true);
            $('#rs_muscle_none_cbox').prop('checked', false);
        } else {
            $('#rs_muscle_spasm_cbox, #rs_muscle_pain_cbox, #rs_muscle_sizeloss_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').prop('checked', false);
        }
    });

    $('#rs_muscle_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_muscle_all_cbox, #rs_muscle_spasm_cbox, #rs_muscle_sizeloss_cbox, #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').prop('checked', false);
        }
    });

    $('#rs_muscle_spasm_cbox, #rs_muscle_sizeloss_cbox, #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').on('click', function() {
        $('#rs_muscle_all_cbox, #rs_muscle_none_cbox').prop('checked', false);
    });

    /* NEUROLOGIC */
    $('#rs_neuro_all_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_neuro_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').prop('checked', true);
            $('#rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', true);
            $('#rs_neuro_none_cbox').prop('checked', false);
        } else {
            $('#rs_neuro_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').prop('checked', false);
            $('#rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', false);
        }
    });
    $('#rs_neuro_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_neuro_all_cbox, #rs_neuro_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').prop('checked', false);
            $('#rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', false);
        }
    });
    $('#rs_neuro_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').on('click', function() {
        $('#rs_neuro_all_cbox, #rs_neuro_none_cbox').prop('checked', false);
    });
    $('#rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').on('click', function() {
        $('#rs_neuro_all_cbox, #rs_neuro_none_cbox').prop('checked', false);
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

    /**************************************************************************/

    $('.reason_referral').on('change', function() {
        var value = $(this).val();
        if (value == '-1') {
            $("#other_reason_referral").html(loading);
            setTimeout(function() {
                $("#other_reason_referral").html('<span>Other Reason for Referral:</span>\n' +
                    '                                <br />\n' +
                    '                                <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7" required></textarea>')
            }, 500);
            $("#other_reason_referral").show();
        } else {
            clearOtherReasonReferral();
        }
    });
</script>

@include('script.datetime')
@include('script.edit_referred_pregnant')