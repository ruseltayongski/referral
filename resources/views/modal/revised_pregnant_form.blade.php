<?php
    $appointmentParam = $_GET['appointment']; // I add this
    $facility_id_telemed = json_decode(json_decode($appointmentParam, true), true)[0]['facility_id'] ?? json_decode($appointmentParam, true)[0]['facility_id'];
    $telemedicine_appointment_id = json_decode(json_decode($appointmentParam, true),true)[0]['appointmentId'] ?? json_decode($appointmentParam, true)[0]['appointmentId'];
    $telemedicine_doctor_id = json_decode(json_decode($appointmentParam, true),true)[0]['doctorId'] ?? json_decode($appointmentParam, true)[0]['doctorId'];

    $user = Session::get('auth');
    $myfacility = \App\Facility::find($user->facility_id);

    if($facility_id_telemed){
        $facilities = \App\Facility::select('id','name')
        ->where('id','!=',$user->facility_id)
        ->where('id', $facility_id_telemed) // I am adding this to get the specific facility name
        ->where('status',1)
        ->where('referral_used','yes')
        ->orderBy('name','asc')->get();
    }else{
        $facilities = \App\Facility::select('id','name')
        ->where('id','!=',$user->facility_id)
        ->where('status',1)
        ->where('referral_used','yes')
        ->orderBy('name','asc')->get();
    }
        
    $reason_for_referral = \App\ReasonForReferral::get();
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
    }

    .container-referral {
        border: 1px solid lightgrey;
        width: 100%;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 5px;
        padding-right: 5px;
    }

    /*.glasgow-table {
        border: 1px solid lightgrey;
        width: 100%;
    }*/

    /* .glasgow-dot {
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


    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    #glasgow_table_1, tr td:nth-child(1) {width: 35%;}
    #glasgow_table_2 tr td:nth-child(2) {width: 35%;} */ 

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

    <div class="modal fade" role="dialog" id="revisedpregnantFormModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{url('submit-referral/pregnant')}}" method="POST" class="form-submit revised_pregnant_form">
                    <div class="jim-content">
                        @include('include.header_form')

                    <div class="form-group-sm form-inline">
                        {{ csrf_field() }}
                        <input type="hidden" name="patient_id" class="patient_id" value="" />
                        <input type="hidden" class="pt_age" />
                        <input type="hidden" name="date_referred" class="date_referred" value="{{ date('Y-m-d H:i:s') }}" />
                        <input type="hidden" name="code" value="" />
                        <input type="hidden" name="source" value="{{ $source }}" />
                        <input type="hidden" class="referring_name" value="{{ $myfacility->name }}" />
                        <br>
                        <div class="row">
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
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-success">Date/Time Referred (ReCo)</small><br>
                                <span>{{ date('l F d, Y h:i A') }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Name of Patient</small><br>
                                <span class="patient_name"></span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Address</small><br>
                                <span class="patient_address"></span>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-success">Referred to</small> <span class="text-red">*</span><br>
                                <select name="referred_facility" class="select2 select_facility" required>
                                    <option value="">Select Facility...</option>
                                    @foreach($facilities as $row)
                                    <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Department</small> <span class="text-red">*</span><br>
                                <select name="referred_department" class="form-control-select select_department select_department_pregnant" style="width: 100%;" required>
                                    <option value="">Select Option</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Address</small><br>
                                <span class="text-yellow facility_address"></span>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-success">Age</small><br>
                                <span class="patient_age"></span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Sex</small> <span class="text-red">*</span><br>
                                <select name="patient_sex" class="patient_sex form-control" style="width: 100%;" required>
                                    <option value="">Select...</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Civil Status</small> <span class="text-red">*</span><br>
                                <select name="civil_status" style="width: 100%;" class="civil_status form-control" required>
                                    <option value="">Select...</option>
                                    <option>Single</option>
                                    <option>Married</option>
                                    <option>Divorced</option>
                                    <option>Separated</option>
                                    <option>Widowed</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-success">Covid Number</small><br>
                                <input type="text" name="covid_number" style="width: 100%;">
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Clinical Status</small><br>
                                <select name="clinical_status" id="" class="form-control-select" style="width: 100%;">
                                    <option value="">Select option</option>
                                    <option value="asymptomatic">Asymptomatic</option>
                                    <option value="mild">Mild</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Surveillance Category</small><br>
                                <select name="sur_category" id="" class="form-control-select" style="width: 100%;">
                                    <option value="">Select option</option>
                                    <option value="contact_pum">Contact (PUM)</option>
                                    <option value="suspect">Suspect</option>
                                    <option value="probable">Probable</option>
                                    <option value="confirmed">Confirmed</option>
                                </select>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_illness_history_pregnant" aria-expanded="false" aria-controls="collapse_illness_history_pregnant">
                                        <b>HISTORY OF PRESENT ILLNESS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_illness_history_pregnant" style="width: 100%">
                                    <b>CASE SUMMARY:</b>
                                    <textarea class="form-control" name="case_summary" style="resize: none;width: 100%;" rows="7" required></textarea><br><br>
                                    <b>SUMMARY OF RECO:</b>
                                    <textarea class="form-control" name="reco_summary" style="resize: none;width: 100%;" rows="7" required></textarea><br><br>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_diagnosis_pregnant" aria-expanded="false" aria-controls="collapse_diagnosis_pregnant">
                                        <b>DIAGNOSIS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse " id="collapse_diagnosis_pregnant" style="width: 100%">

                                    <b>Diagnosis/Impression: </b>
                                    <textarea class="form-control" rows="7" name="diagnosis" style="resize: none;width: 100%;margin-top: 1%" required></textarea><br><br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <small class="text-success"><b>DIAGNOSIS</b></small> <span class="text-red">*</span>
                                            <br><br>
                                            <a data-toggle="modal" data-target="#icd-modal" type="button" class="btn btn-sm btn-success" onclick="searchICD10()">
                                                <i class="fa fa-medkit"></i> Add ICD-10
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" onclick="addNotesDiagnosis()"><i class="fa fa-plus"></i> Add notes in diagnosis</button>
                                        </div>
                                    </div>

                                    <div class="row" style="padding-top: 10px;">
                                        <div class="col-md-12">
                                            <button type="button" id="clear_icd" class="btn btn-xs btn-danger" onclick="clearICD()"> Clear ICD-10</button><br>
                                            <div class="text-success" id="icd_selected" style="padding-top: 5px"></div>
                                        </div>
                                    </div>

                                    <div class="row" style="padding-top: 10px;">
                                        <div class="col-md-12">
                                            <button type="button" id="clear_notes" class="btn btn-xs btn-info" onclick="clearNotesDiagnosis()"> Clear notes diagnosis</button>
                                            <div id="add_notes_diagnosis" style="padding-top: 5px"></div>
                                        </div>
                                    </div>

                                    <div class="row" style="padding-top: 10px">
                                        <div class="col-md-12">
                                            <button type="button" id="clear_other_diag" class="btn btn-xs btn-warning" onclick="clearOtherDiagnosis()"> Clear other diagnosis</button>
                                            <div id="others_diagnosis" style="padding-top: 5px"></div>
                                        </div>
                                </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_medical_history_pregnant" aria-expanded="false" aria-controls="collapse_medical_history_pregnant">
                                        <b>PAST MEDICAL HISTORY</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_medical_history_pregnant" style="width: 100%;">
                                    <b>COMORBIDITIES</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_all_cbox_pregnant" name="comor_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="comor_all_cbox" value="Yes">
                                                <span>Select All</span>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_none_cbox_pregnant" name="comor_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="comor_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_hyper_cbox_pregnant" name="comor_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"> Hypertension
                                                <span id="comor_hyper_pregnant"> since
                                                    <select class="form-control select" name="hyper_year" style="font-size: 10pt;">
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year) {
                                                            echo "<option>$year</option>\n";
                                                        }
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_diab_cbox_pregnant" name="comor_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"> Diabetes Mellitus
                                                <span id="comor_diab_pregnant"> since
                                                    <select class="form-control select" name="diab_year" style="font-size: 10pt;">
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year) {
                                                            echo "<option>$year</option>\n";
                                                        }
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_asthma_cbox_pregnant" name="comor_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"> Bronchial Asthma
                                                <span id="comor_asthma_pregnant"> since
                                                    <select class="form-control select" name="asthma_year" style="font-size: 10pt;">
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year) {
                                                            echo "<option>$year</option>\n";
                                                        }
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_copd_cbox_pregnant" name="comor_copd_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> COPD</span>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_dyslip_cbox_pregnant" name="comor_dyslip_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Dyslipidemia</span>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_thyroid_cbox_pregnant" name="comor_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Thyroid Disease</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_cancer_cbox_pregnant" name="comor_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Cancer <i>(specify)</i>:</span>
                                                <textarea class="form-control" name="comor_cancer" id="comor_cancer_pregnant" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="comor_others_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Other(s): </span>
                                                <textarea class="form-control" name="comor_others" id="comor_others_pregnant" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>ALLERGIES</b><i> (Specify)</i><br>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="allergy_all_cbox_pregnant" name="allergy_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="allergy_none_cbox_pregnant" name="allergy_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="allergy_food_cbox_pregnant" name="allergy_food_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Food(s): <i>(ex. crustaceans, eggs)</i> </span>
                                                <textarea class="form-control" id="allergy_food_pregnant" name="allergy_food_cause" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="allergy_drug_cbox_pregnant" name="allergy_drug_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Drug(s): <i>(ex. Ibuprofen, NSAIDS)</i></span>
                                                <textarea class="form-control" id="allergy_drug_pregnant" name="allergy_drug_cause" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-4">

                                                <input class="form-check-input" id="allergy_other_cbox_pregnant" name="allergy_other_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Other(s): </span>
                                                <textarea class="form-control" id="allergy_other_pregnant" name="allergy_other_cause" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>HEREDOFAMILIAL DISEASES</b> <i>(Specify which side of the family: maternal, paternal, both)</i>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="heredo_all_cbox_pregnant" name="heredo_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="heredo_none_cbox_pregnant" name="heredo_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="heredo_hyper_cbox_pregnant" name="heredo_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Hypertension: </span>
                                                <select class="form-control-select" id="heredo_hyper_pregnant" name="heredo_hypertension_side">
                                                    <option value="Maternal">Maternal</option>
                                                    <option value="Paternal">Paternal</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <!-- <input type="text" id="heredo_hyper" name="heredo_hypertension_side"> -->
                                            </div>
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="heredo_diab_cbox_pregnant" name="heredo_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Diabetes Mellitus: </span>
                                                <select class="form-control-select" id="heredo_diab_pregnant" name="heredo_diabetes_side">
                                                    <option value="Maternal">Maternal</option>
                                                    <option value="Paternal">Paternal</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <!-- <input type="text" id="heredo_diab" name="heredo_diabetes_side"> -->
                                            </div>
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="heredo_asthma_cbox_pregnant" name="heredo_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Bronchial Asthma: </span>
                                                <select class="form-control-select" id="heredo_asthma_pregnant" name="heredo_asthma_side">
                                                    <option value="Maternal">Maternal</option>
                                                    <option value="Paternal">Paternal</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <!-- <input type="text" id="heredo_asthma" name="heredo_asthma_side"> -->
                                            </div>
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="heredo_cancer_cbox_pregnant" name="heredo_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Cancer: </span>
                                                <select class="form-control-select" id="heredo_cancer_pregnant" name="heredo_cancer_side">
                                                    <option value="Maternal">Maternal</option>
                                                    <option value="Paternal">Paternal</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <!-- <input type="text" id="heredo_cancer" name="heredo_cancer_side"> -->
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="heredo_kidney_cbox_pregnant" name="heredo_kidney_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Kidney: </span>
                                                <select class="form-control-select" id="heredo_kidney_pregnant" name="heredo_kidney_side">
                                                    <option value="Maternal">Maternal</option>
                                                    <option value="Paternal">Paternal</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <!-- <input type="text" id="heredo_kidney" name="heredo_kidney_side"> -->
                                            </div>
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="heredo_thyroid_cbox_pregnant" name="heredo_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Thyroid Disease: </span>
                                                <select class="form-control-select" id="heredo_thyroid_pregnant" name="heredo_thyroid_side">
                                                    <option value="Maternal">Maternal</option>
                                                    <option value="Paternal">Paternal</option>
                                                    <option value="Both">Both</option>
                                                </select>
                                                <!-- <input type="text" id="heredo_thyroid" name="heredo_thyroid_side"> -->
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_others_cbox_pregnant" name="heredo_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                <span> Other(s): </span>
                                                <input type="text" id="heredo_others_pregnant" name="heredo_others_side">
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PREVIOUS HOSPITALIZATION(S) and OPERATION(S)</b><br>
                                    <textarea class="form-control" name="previous_hospitalization" style="resize: none;width: 100%;" rows="3"></textarea><br><br>
                                </div>
                            </div>
                        </div>
                        
                      

                        {{--@if(age <= 18) --}} {{--TODO: COMPARE AGE IF <=18--}}
                        <div class="row" id="pedia_show_pregnant">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_pedia_history_pregnant" aria-expanded="false" aria-controls="collapse_pedia_history_pregnant">
                                        <div class="web-view"><b>PEDIATRIC HISTORY</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view"><b>PEDIATRIC HISTORY</b><br> <i> (as applicable)</i></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_pedia_history_pregnant" style="width: 100%;">
                                    <b>PRENATAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>A <input type="number" name="prenatal_age" style="width: 8%" min="0">
                                                    year old G <input type="number" name="prenatal_g" style="width: 8%" min="0"> P <input type="number" style="width: 8%" min="0" name="prenatal_p"> mother
                                                    <input class="referral-radio-btn" name="prenatal_radiowith_or_without" type="radio" id="prenatal_radiowith_pregnant" name="prenatal_radio" value="with"> with
                                                    <input class="referral-radio-btn" name="prenatal_radiowith_or_without" type="radio" id="prenatal_radiowout_pregnant" name="prenatal_radio" value="without"> without
                                                    MATERNAL ILLNESS, if Yes (specify)
                                                    <textarea class="form-control" id="prenatal_mat_illness_pregnant" name="prenatal_maternal_illness" style="resize: none;width: 50%;" rows="2"></textarea>
                                                </span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>NATAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>Born at
                                                    <select class="form-control-select" name="natal_bornat">
                                                        <option value="home">Home</option>
                                                        <option value="clinic">Clinic</option>
                                                        <option value="hospital">Hospital</option>
                                                    </select>
                                                    <input type="text" id="hospital_name_pregnant" name="natal_born_address" style="width: 30%;" placeholder="Specify where">
                                                    by
                                                    <select class="form-control-select" name="natal_by">
                                                        <option value="md">MD</option>
                                                        <option value="midwife">Midwife</option>
                                                        <option value="hilot">"Hilot"</option>
                                                    </select>
                                                    via
                                                    <select class="form-control-select" name="natal_via">
                                                        <option value="nsd">NSD</option>
                                                        <option value="cs">CS</option>
                                                    </select>
                                                    (indication)
                                                    <input type="text" id="cs_indication_pregnant" name="cs_indication" style="width: 20%;">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>
                                                    <select class="form-control-select" name="natal_term">
                                                        <option value="preterm">Preterm</option>
                                                        <option value="fullterm">Full Term</option>
                                                        <option value="postterm">Post Term</option>
                                                    </select>
                                                    , weighing <input type="number" name="natal_weight" style="width: 8%" min="0" step="0.01"> kg,
                                                    BR <input type="text" name="natal_br" style="width: 20%">, with Good Cry
                                                    <select class="form-control-select" name="natal_withGoodCry">
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select><br>
                                                    Other complications:
                                                    <textarea class="form-control" name="natal_complications" style="resize: none;width: 30%;" rows="2"></textarea>
                                                </span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>POST NATAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Feeding History</i><br>&emsp;
                                                <span>
                                                    <input type="hidden" name="postnatal_bfeed" value="No">
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="postnatal_bfeed_pregnant" name="postnatal_bfeed" value="Yes"> Breastfed
                                                    <span id="breastfed_pregnant">
                                                        x <input type="number" name="postnatal_bfeed_xmos" style="width: 7%;" min="0"> mos.
                                                    </span>
                                                    <input type="hidden" name="postnatal_ffeed" value="No">
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" id="postnatal_ffeed_pregnant" type="checkbox" name="postnatal_ffeed" value="Yes"> Formula Fed,
                                                    <span id="formula_fed_pregnant">
                                                        (specify) <input type="text" style="width: 15%" name="postnatal_ffeed_specify">
                                                    </span>
                                                </span>
                                                started semi solid foods at
                                                <input type="number" name="postnatal_started_semisolidfood_at" style="width: 10%" min="0"> mos
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Immunization History</i><br>&emsp;
                                                <input type="hidden" name="immu_bcg_cbox_pregnant" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_bcg_cbox" value="Yes"> BCG
                                                <input type="hidden" name="immu_dpt_cbox_pregnant" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_dpt_cbox_pregnant" name="immu_dpt_cbox" value="Yes"> DPT/OPV
                                                <span id="immu_dpt_pregnant">
                                                    x <input type="number" name="immu_dpt_doses" style="width: 7%;" min="0"> doses
                                                </span>
                                                <input type="hidden" name="immu_hepb_cbox_pregnant" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_hepb_cbox_pregnant" name="immu_hepb_cbox" value="Yes"> Hep B
                                                <span id="immu_hepb_pregnant">
                                                    x <input type="number" name="immu_hepb_doses" style="width: 7%;" min="0"> doses
                                                </span>
                                                <input type="hidden" name="immu_measles_cbox_pregnant" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_measles_cbox" value="Yes"> Measles
                                                <input type="hidden" name="immu_mmr_cbox_pregnant" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_mmr_cbox" value="Yes"> MMR
                                                <input type="hidden" name="immu_others_cbox_pregnant" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_others_cbox_pregnant" name="immu_others_cbox" value="Yes"> Other(s)
                                                <span id="immu_others_pregnant">
                                                    <input type="text" name="immu_others" style="width: 20%;">
                                                </span>
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Developmental Milestones</i><br>&emsp;
                                                <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_under_pregnant" value="Under developed">
                                                <label for="dev_miles_under">
                                                    Under developed
                                                </label>
                                                <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_par_pregnant" value="At par with age">
                                                <label for="dev_miles_par">
                                                    At par with age
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="baby_show_pregnant">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#baby_collapsed_pregnant" aria-expanded="false" aria-controls="baby_collapsed_pregnant">
                                        <div class="web-view"><b>BABY DELIVERED</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view"><b>BABY DELIVERED</b><br> <i> (as applicable)</i></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="baby_collapsed_pregnant" style="width: 100%;">
                                <table class="table bg-warning">
                                    <tr>
                                        <td colspan="2"><small class="text-success"><b>NAME: </b></small><br />
                                            <input type="text" class="form-control" style="width: 100%" name="baby_fname" placeholder="First Name" /><br />
                                            <input type="text" class="form-control" style="width: 100%" name="baby_mname" placeholder="Middle Name" /><br />
                                            <input type="text" class="form-control" style="width: 100%" name="baby_lname" placeholder="Last Name" />
                                        </td>
                                        <td style="vertical-align: top !important;"><small class="text-success"><b>DATE AND HOUR OF BIRTH: </b></small>
                                            <br />
                                            <input type="text" class="form-control  form_datetime" style="width: 100%" name="baby_dob" placeholder="Date/Time" /><br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><small class="text-success"><b>BIRTH WEIGHT: </b></small> <input type="text" class="form-control" style="width: 100%" name="baby_weight" placeholder="kg or lbs" /><br /></td>
                                        <td><small class="text-success"><b>GESTATIONAL AGE: </b></small> <input type="text" class="form-control" style="width: 100%" name="baby_gestational_age" placeholder="age" /><br /></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <small class="text-success"><b>MAIN REASON FOR REFERRAL: </b></small>
                                            <label><input type="radio" name="baby_reason" value="None" checked /> None </label>
                                            <label><input type="radio" name="baby_reason" value="Emergency" /> Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="Non-Emergency" /> Non-Emergency </label>
                                            <label><input type="radio" name="baby_reason" value="To accompany the mother" /> To accompany the mother </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><small class="text-success"><b>MAJOR FINDINGS: </b><i>(Clinica and BP,Temp,Lab)</i></small>
                                            <br />
                                            <textarea class="form-control" name="baby_major_findings" style="resize: none;width: 100%" rows="5"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><small class="text-success"><b>LAST (BREAST) FEED (TIME): </b></small><input type="text" class="form-control form_datetime" style="width: 100%" name="baby_last_feed" placeholder="Date/Time" /><br /></td>
                                    </tr>
                                    <tr class="bg-gray">
                                        <td colspan="4">Treatments Give Time</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><small class="text-success"><b>BEFORE REFERRAL</b></small>
                                            <br />
                                            <input type="text" class="form-control" name="baby_before_treatment" placeholder="Treatment Given" />
                                            <input type="text" class="form-control form_datetime" name="baby_before_given_time" placeholder="Date/Time Given" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><small class="text-success"><b>DURING TRANSPORT</b></small>
                                            <br />
                                            <input type="text" class="form-control" name="baby_during_treatment" placeholder="Treatment Given" />
                                            <input type="text" class="form-control form_datetime" name="baby_during_given_time" placeholder="Date/Time Given" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><small class="text-success"><b>INFORMATION GIVEN TO THE WOMAN AND COMPANION ABOUT THE REASON FOR REFERRAL</b></small>
                                            <br />
                                            <textarea class="form-control" name="baby_information_given" style="resize: none;width: 100%" rows="5"></textarea>
                                        </td>
                                    </tr>
                            </table>
                                </div>
                            </div>
                        </div> 

                        {{--TODO: COMPARE AGE IF >= 9 AND ONLY IF PT IS WOMAN--}}
                        <div class="row" id="menarche_show_pregnant">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_gyne_history_pregnant" aria-expanded="false" aria-controls="collapse_gyne_history_pregnant">
                                        <div class="web-view"><b>OBSTETRIC AND GYNECOLOGIC HISTORY</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view"><b>OBSTETRIC AND GYNECOLOGIC HISTORY</b><br> <i> (as applicable)</i></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_gyne_history_pregnant" style="width: 100%;">
                                    <b>MENARCHE </b> @ <input type="number" min="9" style="width: 10%;" name="menarche"> years old &emsp;&emsp;&emsp;&emsp;
                                    <b>MENOPAUSE: </b>&emsp;
                                    <input type="radio" class="referral-radio-btn" name="menopausal" id="menopausal_pregnant" value="Yes">
                                    <label for="menopausal">Yes</label>
                                    <input type="radio" class="referral-radio-btn" name="menopausal" id="non_menopausal_pregnant" value="No">
                                    <label for="non_menopausal">No</label>
                                    <span id="menopausal_age_pregnant">(age) <input type="number" name="menopausal_age" style="width: 10%;" min="9"></span><br><br>

                                    <b>MENSTRUAL CYCLE</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_reg_radio_pregnant" value="regular">
                                                <label for="mens_reg_radio">Regular</label>
                                                <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_irreg_radio_pregnant" value="irregular">
                                                <label for="mens_irreg_radio">Irregular</label>
                                                <span id="mens_irreg_pregnant">x <input type="number" name="mens_irreg_xmos" style="width: 15%;" min="0"> mos</span>
                                            </div>
                                            <div class="col-md-6">
                                                <b>Dysmenorrhea:</b> &emsp;
                                                <input type="radio" class="referral-radio-btn" name="dysme" id="dysme_yes_pregnant" value="Yes">
                                                <label for="dysme_yes">Yes</label>
                                                <input type="radio" class="referral-radio-btn" name="dysme" id="dysme_no_pregnant" value="No">
                                                <label for="dysme_no">No</label><br>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <b>Duration:</b> <input type="number" style="width:15%;" min="0" name="mens_duration"> days &emsp;
                                                <b>Pads/day:</b> <input type="number" style="width:15%;" min="0" name="mens_padsperday">
                                            </div>
                                            <div class="col-md-6">
                                                <b>Medication: </b> <input type="text" style="width:70%;" name="mens_medication">
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>CONTRACEPTIVE HISTORY</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_none_cbox" value="Yes"> None
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_pills_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_pills_cbox" value="Yes"> Pills
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_iud_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_iud_cbox" value="Yes"> IUD
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_rhythm_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_rhythm_cbox" value="Yes"> Rhythm
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_condom_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_condom_cbox" value="Yes"> Condom
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input class="form-check-input" id="contraceptive_others_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_other_cbox" value="Yes"> Other(s)
                                                <textarea class="form-control" id="contraceptive_others_pregnant" name="contraceptive_other" style="resize: none;width: 50%;" rows="2"></textarea><br>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PARITY</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <b>G</b> <input type="number" min="0" style="width:8%;" name="parity_g">
                                                <b>P</b> <input type="number" min="0" style="width:8%;" name="parity_p">&emsp;
                                                <b>(FT </b> <input type="text" style="width:8%;" name="parity_ft">
                                                <b> PT </b> <input type="text" style="width:8%;" name="parity_pt">
                                                <b> A </b> <input type="text" style="width:8%;" name="parity_a">
                                                <b> L </b> <input type="text" style="width:8%;" name="parity_l"><b>)</b>
                                            </div>
                                        </div>
                                    </div><br>

                                    <div class="container-referral">
                                        <b>LNMP</b>
                                        <input type="text" style="width:15%;" name="parity_lnmp">&emsp;&emsp;&emsp;
                                        <b>EDC</b><i>(if pregnant)</i>
                                        <input type="text" style="width:15%;" name="parity_edc_ifpregnant">
                                    </div><br>

                                    <b>AOG</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <b>by LNMP </b> <input type="number" min="0" style="width:25%;" name="aog_bylnmp"> <b>wks</b>
                                            </div>
                                            <div class="col-md-4">
                                                <b>by EUTZ </b> <input type="number" min="0" style="width:25%;" name="aog_byEUTZ"> <b>wks</b>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PRENATAL HISTORY</b><br>
                                    <textarea class="form-control" name="prenatal_history" style="resize: none;width: 100%;" rows="4"></textarea><br><br>
                                    <div class="table-responsive" style="overflow-x: auto">
                                        <table class="table table-bordered" id="prenatal_table_pregnant">
                                            <thead>
                                                <tr style="font-size: 10pt;">
                                                    <th class="text-center" style="width:50%;">Pregnancy Order</th>
                                                    <th class="text-center" style="width:20%;">Year</th>
                                                    <th class="text-center">Gestation Completed</th>
                                                    <th class="text-center">Pregnancy Outcome</th>
                                                    <th class="text-center">Place of Birth</th>
                                                    <th class="text-center">Sex</th>
                                                    <th class="text-center" style="width:50%;">Birth Weight</th>
                                                    <th class="text-center">Present Status</th>
                                                    <th class="text-center">Complication(s)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr style="font-size: 10pt">
                                                    <td><input class="form-control" type="text" name="pregnancy_history_order[]"></td>
                                                    <td>
                                                        <select class="form-control select" name="pregnancy_history_year[]">
                                                            <?php
                                                            foreach (range(date('Y'), 1950) as $year) {
                                                                echo "<option>" . $year . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td><input class="form-control" id="gestation_pregnant" type="text" name="pregnancy_history_gestation[]"></td>
                                                    <td><input class="form-control" type="text" name="pregnancy_history_outcome[]"></td>
                                                    <td><input class="form-control" type="text" name="pregnancy_history_placeofbirth[]"></td>
                                                    <td>
                                                        <select class="select form-control" name="prenatal_history_sex[]">
                                                            <option value="">Choose...</option>
                                                            <option value="M">Male</option>
                                                            <option value="F">Female</option>
                                                        </select>
                                                    </td>
                                                    <td><input class="form-control" type="number" min="0" step="0.01" name="pregnancy_history_birthweight[]"></td>
                                                    <td><input class="form-control" type="text" name="pregnancy_history_presentstatus[]"></td>
                                                    <td><input class="form-control" type="text" maxlength="38" name="pregnancy_history_complications[]"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button class="btn-sm btn-success" id="prenatal_add_row_pregnant" type="button">
                                            <i class="fa fa-plus"> Add Row</i>
                                        </button><br><br>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_persocial_history_pregnancy" aria-expanded="false" aria-controls="collapse_persocial_history_pregnancy">
                                        <div class="web-view"><b>PERSONAL and SOCIAL HISTORY</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view"><b>PERSONAL and SOCIAL HISTORY</b><br> <i> (as applicable)</i></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_persocial_history_pregnancy" style="width: 100%;">
                                    <b>SMOKING</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_yes_pregnant" value="Yes">
                                                <label for="smoke_yes">Yes</label><br>
                                                <span id="smoking_sticks_pregnant">Sticks per day: <input type="number" min="0" style="width:30%;" name="smoking_sticks_per_day"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_no_pregnant" value="No">
                                                <label for="smoke_no">No</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_quit_pregnant" value="Yes">
                                                <label for="smoke_quit">Quit</label>
                                                <span id="smoking_quit_year_pregnant"> since
                                                    <select class="form-control select" name="smoking_year_quit">
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option>" . $year . "</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <span>Other Remarks: <textarea class="form-control" style="resize: none;width:50%;" rows="2" name="smoking_other_remarks"></textarea></span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>ALCOHOL DRINKING</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_yes_radio_pregnant" value="Yes">
                                                <label for="alcohol_yes_radio">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_no_radio_pregnant" value="Yes">
                                                <label for="alcohol_no_radio">No</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_quit_radio_pregnant" value="Yes">
                                                <label for="alcohol_quit_radio">Quit</label>
                                                <span id="alcohol_quit_year_pregnant"> since
                                                    <select class="form-control select" name="alcohol_year_quit">
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option>" . $year . "</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span id="alcohol_type_pregnant">Liquor Type: <textarea class="form-control" style="resize: none;" rows="2" name="alcohol_type"></textarea></span>
                                            </div>
                                            <div class="col-md-4">
                                                <span id="alcohol_bottles_pregnant">Bottles per day: <input type="number" min="0" style="width:25%;" name="alcohol_bottles"></span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>ILLICIT DRUGS</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="radio" name="illicit_drugs" id="drugs_yes_radio_pregnant" class="referral-radio-btn" value="Yes">
                                                <label for="drugs_yes_radio">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" name="illicit_drugs" id="drugs_no_radio_pregnant" class="referral-radio-btn" value="No">
                                                <label for="drugs_no_radio">No</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" name="illicit_drugs" id="drugs_quit_radio_pregnant" class="referral-radio-btn" value="Quit">
                                                <label for="drugs_quit_radio">Quit</label>
                                                <span id="drugs_quit_year_pregnant"> since
                                                    <select class="form-control select" name="drugs_year_quit">
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year)
                                                            echo "<option>" . $year . "</option>";
                                                        ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8" id="drugs_text_pregnant">
                                                Specify drugs taken:
                                                <textarea class="form-control" rows="2" style="resize: none;width:50%;" name="illicit_drugs_token"></textarea>
                                            </div>
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_medication_pregnant" aria-expanded="false" aria-controls="collapse_medication_pregnant">
                                        <b>CURRENT MEDICATION(S)</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_medication_pregnant" style="width: 100%;">
                                    <i>Specify number of doses given and time of last dose given.</i>
                                    <textarea class="form-control" name="current_meds" style="resize: none;width: 100%;" rows="5"></textarea><br><br>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_lab_procedures_pregnant" aria-expanded="false" aria-controls="collapse_lab_procedures_pregnant">
                                        <div class="web-view">
                                            <b>PERTINENT LABORATORY and OTHER ANCILLARY PROCEDURES</b> <i>(include Dates)</i>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </div>
                                        <div class="mobile-view">
                                            <b>PERTINENT LABORATORY and <br>OTHER ANCILLARY PROCEDURES</b><br> <i>(include Dates)</i>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </div>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_lab_procedures_pregnant" style="width: 100%;">
                                    <i> Attach all applicable labs in one file.</i>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_ua_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ua_cbox" value="Yes">
                                                <span> UA</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_cbc_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_cbc_cbox" value="Yes">
                                                <span> CBC</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_xray_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_xray_cbox" value="Yes">
                                                <span> X-RAY</span>
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-check-input" id="lab_others_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_others_cbox" value="Yes"> Others:
                                                <textarea id="lab_others_pregnant" class="form-control" name="lab_procedure_other" style="resize: none;" rows="2" name="lab_procedure_other"></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="file-upload">
                                                    <div class="image-upload-wrap">
                                                        <input class="file-upload-input" type='file' name="file_upload" onchange="readURL(this);" accept="image/png, image/jpeg, image/jpg, image/gif, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/pdf" />
                                                        <div class="drag-text">
                                                            <h3>Drag and drop a file or select add Image</h3>
                                                        </div>
                                                    </div>
                                                    <div class="file-upload-content">
                                                        <img class="file-upload-image" src="#" alt="your image" />
                                                        <div class="image-title-wrap">
                                                            <button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_review_system_pregnant" aria-expanded="false" aria-controls="collapse_review_system_pregnant">
                                        <b>REVIEW OF SYSTEMS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_review_system_pregnant" style="width: 100%;">
                                    <b>SKIN</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_skin_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_skin_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_skin_rashes_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_skin_rashes_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_rashes_cbox" value="Yes">
                                                <span> Rashes</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_skin_itching_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_skin_itching_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_itching_cbox" value="Yes">
                                                <span> Itching</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_skin_hairchange_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_skin_hairchange_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_hairchange_cbox" value="Yes">
                                                <span> Change in hair or nails</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>HEAD</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_head_headache_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_head_headache_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_headache_cbox" value="Yes">
                                                <span> Headaches</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_injury_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_injury_cbox" value="Yes">
                                                <span> Head injury</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>EYES</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_glasses_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_eyes_glasses_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_glasses_cbox" value="Yes">
                                                <span> Glasses or Contacts</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_vision_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_eyes_vision_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_vision_cbox" value="Yes">
                                                <span> Change in vision</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_pain_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_eyes_pain_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_pain_cbox" value="Yes">
                                                <span> Eye pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_doublevision_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_eyes_doublevision_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_doublevision_cbox" value="Yes">
                                                <span> Double Vision</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_flashing_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_eyes_flashing_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_flashing_cbox" value="Yes">
                                                <span> Flashing lights</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_glaucoma_cbox_pregnant" value="No">
                                                <input class="form-check-input" id="rs_eyes_glaucoma_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_glaucoma_cbox" value="Yes">
                                                <span> Glaucoma/Cataracts</span>
                                            </div>
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="rs_eye_exam_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eye_exam_cbox" value="Yes">
                                                <span> Last eye exam</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>EARS</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_ears_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_ears_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">

                                                <input class="form-check-input" id="rs_ears_changehearing_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_changehearing_cbox" value="Yes">
                                                <span> Change in hearing</span>
                                            </div>
                                            <div class="col-md-2">

                                                <input class="form-check-input" id="rs_ears_pain_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_pain_cbox" value="Yes">
                                                <span> Ear pain</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="rs_ears_discharge_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_discharge_cbox" value="Yes">
                                                <span> Ear discharge</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="rs_ears_ringing_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_ringing_cbox" value="Yes">
                                                <span> Ringing</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="rs_ears_dizziness_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_dizziness_cbox" value="Yes">
                                                <span> Dizziness</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>NOSE/SINUSES</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_all_cbox" value="Yes">
                                                <span> Select All </span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_none_cbox" value="Yes">
                                                <span> None </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_bleeds_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_bleeds_cbox" value="Yes">
                                                <span> Nose bleeds</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_stuff_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_stuff_cbox" value="Yes">
                                                <span> Nasal stuffiness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_colds_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_colds_cbox" value="Yes">
                                                <span> Frequent Colds</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>MOUTH/THROAT</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_bleed_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_bleed_cbox" value="Yes">
                                                <span> Bleeding gums</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_soretongue_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_soretongue_cbox" value="Yes">
                                                <span> Sore tongue</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_sorethroat_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_sorethroat_cbox" value="Yes">
                                                <span> Sore throat</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_hoarse_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_hoarse_cbox" value="Yes">
                                                <span> Hoarseness</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>NECK</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_lumps_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_lumps_cbox" value="Yes">
                                                <span> Lumps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_swollen_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_swollen_cbox" value="Yes">
                                                <span> Swollen glands</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_goiter_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_goiter_cbox" value="Yes">
                                                <span> Goiter</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_stiff_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_stiff_cbox" value="Yes">
                                                <span> Stiffness</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>BREAST</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_lumps_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_lumps_cbox" value="Yes">
                                                <span> Lumps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_pain_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_pain_cbox_pregnant" value="Yes">
                                                <span> Pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_discharge_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_discharge_cbox" value="Yes">
                                                <span> Nipple discharge</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_bse_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_bse_cbox" value="Yes">
                                                <span> BSE</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>RESPIRATORY/CARDIAC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_shortness_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_shortness_cbox" value="Yes">
                                                <span> Shortness of breath</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_cough_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_cough_cbox" value="Yes">
                                                <span> Cough</span>
                                            </div>
                                            <div class="col-sm-3">
                                                <input class="form-check-input" id="rs_respi_phlegm_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_phlegm_cbox" value="Yes">
                                                <span> Production of phlegm, color</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_wheezing_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_wheezing_cbox" value="Yes">
                                                <span> Wheezing</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_coughblood_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_coughblood_cbox" value="Yes">
                                                <span> Coughing up blood</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_chestpain_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_chestpain_cbox" value="Yes">
                                                <span> Chest pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_fever_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_fever_cbox" value="Yes">
                                                <span> Fever</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_sweats_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_sweats_cbox" value="Yes">
                                                <span> Night sweats</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_swelling_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_swelling_cbox" value="Yes">
                                                <span> Swelling in hands/feet</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_bluefingers_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_bluefingers_cbox" value="Yes">
                                                <span> Blue fingers/toes</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_highbp_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_highbp_cbox" value="Yes">
                                                <span> High blood pressure</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_skipheartbeats_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_skipheartbeats_cbox" value="Yes">
                                                <span> Skipping heart beats</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_heartmurmur_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_heartmurmur_cbox" value="Yes">
                                                <span> Heart murmur</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_hxheart_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_hxheart_cbox" value="Yes">
                                                <span> HX of heart medication</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_brochitis_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_brochitis_cbox" value="Yes">
                                                <span> Bronchitis/emphysema</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_rheumaticheart_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_rheumaticheart_cbox" value="Yes">
                                                <span> Rheumatic heart disease</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>GASTROINTESTINAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <input class="form-check-input" id="rs_gastro_appetite_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_appetite_cbox" value="Yes">
                                                <span> Change of appetite or weight</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_swallow_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_swallow_cbox" value="Yes">
                                                <span> Problems swallowing</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_nausea_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_nausea_cbox" value="Yes">
                                                <span> Nausea</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_heartburn_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_heartburn_cbox" value="Yes">
                                                <span> Heartburn</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_vomit_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_vomit_cbox" value="Yes">
                                                <span> Vomiting</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_vomitblood_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_vomitblood_cbox" value="Yes">
                                                <span> Vomiting Blood</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_constipation_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_constipation_cbox" value="Yes">
                                                <span> Constipation</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_diarrhea_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_diarrhea_cbox" value="Yes">
                                                <span> Diarrhea</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_bowel_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_bowel_cbox" value="Yes">
                                                <span> Change in bowel habits</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_abdominal_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_abdominal_cbox" value="Yes">
                                                <span> Abdominal pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_belching_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_belching_cbox" value="Yes">
                                                <span> Excessive belching</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_flatus_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_flatus_cbox" value="Yes">
                                                <span> Excessive flatus</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_jaundice_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_jaundice_cbox" value="Yes">
                                                <span> Yellow color of skin (Jaundice/Hepatitis)</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_intolerance_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_intolerance_cbox" value="Yes">
                                                <span> Food intolerance</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_rectalbleed_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_rectalbleed_cbox" value="Yes">
                                                <span> Rectal bleeding/Hemorrhoids</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>URINARY</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_difficult_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_difficult_cbox" value="Yes">
                                                <span> Difficulty in urination</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_pain_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_pain_cbox" value="Yes">
                                                <span> Pain or burning on urination</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_frequent_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_frequent_cbox" value="Yes">
                                                <span> Frequent urination at night</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_urgent_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_urgent_cbox" value="Yes">
                                                <span> Urgent need to urinate</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_incontinence_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_incontinence_cbox" value="Yes">
                                                <span> Incontinence of urine</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_dribbling_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_dribbling_cbox" value="Yes">
                                                <span> Dribbling</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_decreased_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_decreased_cbox" value="Yes">
                                                <span> Decreased urine stream</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_blood_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_blood_cbox" value="Yes">
                                                <span> Blood in urine</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_uti_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_uti_cbox" value="Yes">
                                                <span> UTI/stones/prostate infection</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PERIPHERAL VASCULAR</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_legcramp_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_legcramp_cbox" value="Yes">
                                                <span> Leg cramps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_varicose_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_varicose_cbox" value="Yes">
                                                <span> Varicose veins</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_veinclot_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_veinclot_cbox" value="Yes">
                                                <span> Clots in veins</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>MUSCULOSKELETAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_musclgit_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_musclgit_cbox" value="Yes">
                                                <span> Pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_swell_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_swell_cbox" value="Yes">
                                                <span> Swelling</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_stiff_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_stiff_cbox" value="Yes">
                                                <span> Stiffness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_decmotion_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_decmotion_cbox" value="Yes">
                                                <span> Decreased joint motion</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_brokenbone_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_brokenbone_cbox" value="Yes">
                                                <span> Broken bone</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_sprain_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_sprain_cbox" value="Yes">
                                                <span> Serious sprains</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_arthritis_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_arthritis_cbox" value="Yes">
                                                <span> Arthritis</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_gout_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_gout_cbox" value="Yes">
                                                <span> Gout</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>NEUROLOGIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_headache_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_headache_cbox" value="Yes">
                                                <span> Headaches</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_seizure_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_seizure_cbox" value="Yes">
                                                <span> Seizures</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_faint_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_faint_cbox" value="Yes">
                                                <span> Loss of Consciousness/Fainting</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_paralysis_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_paralysis_cbox" value="Yes">
                                                <span> Paralysis</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_weakness_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_weakness_cbox" value="Yes">
                                                <span> Weakness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_sizeloss_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_sizeloss_cbox" value="Yes">
                                                <span> Loss of muscle size</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_spasm_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_spasm_cbox" value="Yes">
                                                <span> Muscle Spasm</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_tremor_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_tremor_cbox" value="Yes">
                                                <span> Tremor</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_involuntary_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_involuntary_cbox" value="Yes">
                                                <span> Involuntary movement</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_incoordination_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_incoordination_cbox" value="Yes">
                                                <span> Incoordination</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_numbness_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_numbness_cbox" value="Yes">
                                                <span> Numbness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_tingles_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_tingles_cbox" value="Yes">
                                                <span> Feeling of "pins and needles/tingles"</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>HEMATOLOGIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_anemia_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_anemia_cbox" value="Yes">
                                                <span> Anemia</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_bruising_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_bruising_cbox" value="Yes">
                                                <span> Easy bruising/bleeding</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rss_hema_transfusion_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rss_hema_transfusion_cbox" value="Yes">
                                                <span> Past Transfusions</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>ENDOCRINE</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_abnormal_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_abnormal_cbox" value="Yes">
                                                <span> Abnormal growth</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_appetite_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_appetite_cbox" value="Yes">
                                                <span> Increased appetite</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_thirst_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_thirst_cbox" value="Yes">
                                                <span> Increased thirst</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_urine_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_urine_cbox" value="Yes">
                                                <span> Increased urine production</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_thyroid_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_thyroid_cbox" value="Yes">
                                                <span> Thyroid trouble</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_heatcold_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_heatcold_cbox" value="Yes">
                                                <span> Heat/cold intolerance</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_sweat_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_sweat_cbox" value="Yes">
                                                <span> Excessive sweating</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_diabetes_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_diabetes_cbox" value="Yes">
                                                <span> Diabetes</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PSYCHIATRIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_all_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_all_cbox" value="Yes">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_none_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_tension_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_tension_cbox" value="Yes">
                                                <span> Tension/Anxiety</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_depression_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_depression_cbox" value="Yes">
                                                <span> Depression/suicide ideation</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_memory_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_memory_cbox" value="Yes">
                                                <span> Memory problems</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_unusual_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_unusual_cbox" value="Yes">
                                                <span> Unusual problems</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_sleep_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_sleep_cbox" value="Yes">
                                                <span> Sleep problems</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_treatment_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_treatment_cbox" value="Yes">
                                                <span> Past treatment with psychiatrist</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_moodchange_cbox_pregnant" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_moodchange_cbox" value="Yes">
                                                <span> Change in mood/change in attitude towards family/friends</span>
                                            </div>
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_nutri_status_pregnant" aria-expanded="false" aria-controls="collapse_nutri_status_pregnant">
                                        <b>NUTRITIONAL STATUS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_nutri_status_pregnant" style="width: 100%;">
                                    <b>Diet</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_none_pregnant" value="None">
                                                <label for="diet_none"> None </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_oral_pregnant" value="Oral">
                                                <label for="diet_oral"> Oral </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tube_pregnant" value="Tube">
                                                <label for="diet_tube"> Tube </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tpn_pregnant" value="TPN">
                                                <label for="diet_tpn"> TPN </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" , id="diet_npo_pregnant" value="NPO">
                                                <label for="diet_npo"> NPO </label>
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                Specify Diets: <textarea class="form-control" name="diet" style="resize: none;width: 100%;" rows="3"></textarea><br><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_vital_signs_pregnant" aria-expanded="false" aria-controls="collapse_vital_signs_pregnant">
                                        <b>LATEST VITAL SIGNS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_vital_signs_pregnant" style="width: 100%;">
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                Temperature: <input type="number" step="0.01" style="width:30%;" min="0" name="vital_temp"> &#176;C
                                            </div>
                                            <div class="col-md-4">
                                                Pulse Rate/Heart Rate: <input type="number" step="0.01" style="width:30%;" min="0" name="vital_pulse"> bpm
                                            </div>
                                            <div class="col-md-4">
                                                Respiratory Rate: <input type="number" step="0.01" style="width:30%;" min="0" name="vital_respi_rate"> cpm
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                Blood Pressure: <input type="number" step="0.01" style="width:30%;" min="0" name="vital_bp"> mmHg
                                            </div>
                                            <div class="col-md-4">
                                                O2 Saturation <input type="number" step="0.01" style="width:30%;" min="0" name="vital_oxy_saturation"> %
                                            </div>
                                        </div><br>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_glasgow_pregnant" aria-expanded="false" aria-controls="collapse_glasgow_pregnant">
                                        <b>GLASGOW COMA SCALE</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_glasgow_pregnant" style="width: 100%;">
                                    <b>Pupil Size Chart</b> &emsp;
                                    <input type="button" class="btn-m btn-warning btn-rounded" onclick="resetPupilSize()" value="Reset">
                                    <div class="container-referral">
                                        <div class="row web-view">
                                            <div class="col-lg-1"></div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant_1">
                                                    <b>1</b><br>
                                                    <span class="glasgow-dot" style="height: 6px; width: 6px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_1" value="1">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant_2">
                                                    <b>2</b><br>
                                                    <span class="glasgow-dot" style="height: 10px; width: 10px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_2" value="2">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant_3">
                                                    <b>3</b><br>
                                                    <span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_3" value="3">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant_4">
                                                    <b>4</b><br>
                                                    <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_4" value="4">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant_5">
                                                    <b>5</b><br>
                                                    <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_5" value="5">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_pregnant_6">
                                                    <b>6</b><br>
                                                    <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_6" value="6">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow-7">
                                                    <b>7</b><br>
                                                    <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow-7" value="7">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_8">
                                                    <b>8</b><br>
                                                    <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_8" value="8">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_9">
                                                    <b>9</b><br>
                                                    <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_9" value="9">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <b>10</b><br>
                                                <label for="glasgow_pregnant_10">
                                                    <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                </label>
                                                <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_pregnant_10" value="10">
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
                                                    <label for="glasgow-7">
                                                        <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow-7" value="7">&emsp;&emsp;

                                                    <b>8</b>
                                                    <label for="glasgow_8">
                                                        <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_8" value="8">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-1">
                                                    <b>9</b>
                                                    <label for="glasgow_9">
                                                        <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_9" value="9">&emsp;&emsp;

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
                                            <b>Motor Response</b>
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
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=6></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Localizes Pain</td>
                                                            <td>Withdraws (Touch)</td>
                                                            <td style="text-align: center">5</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=5></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Withdraws</td>
                                                            <td>Withdraws (Pain)</td>
                                                            <td style="text-align: center">4</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=4></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Flexion to Pain</td>
                                                            <td>Flexion to Pain</td>
                                                            <td style="text-align: center">3</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=3></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Extension to Pain</td>
                                                            <td>Extension to Pain</td>
                                                            <td style="text-align: center">2</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=2></td>
                                                        </tr>
                                                        <tr>
                                                            <td>None</td>
                                                            <td>None</td>
                                                            <td style="text-align: center">1</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio" value=1></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <b>Verbal Response</b>
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
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=5></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Confused</td>
                                                            <td>Irritable Cry</td>
                                                            <td style="text-align: center">4</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=4></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Inappropriate</td>
                                                            <td>Cries to Pain</td>
                                                            <td style="text-align: center">3</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=3></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Incomprehensible</td>
                                                            <td>Moans to Pain</td>
                                                            <td style="text-align: center">2</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=2></td>
                                                        </tr>
                                                        <tr>
                                                            <td>None</td>
                                                            <td>None</td>
                                                            <td style="text-align: center">1</td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="verbal_radio" value=1></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Eye Response</b>
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
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_pregnant" name="eye_radio" value=4></td>
                                                        </tr>
                                                        <tr>
                                                            <td>To Command</td>
                                                            <td>To Voice</td>
                                                            <td style="text-align: center">3 </td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_pregnant" name="eye_radio" value=3></td>
                                                        </tr>
                                                        <tr>
                                                            <td>To Pain</td>
                                                            <td>To Pain</td>
                                                            <td style="text-align: center">2 </td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_pregnant" name="eye_radio" value=2></td>
                                                        </tr>
                                                        <tr>
                                                            <td>None</td>
                                                            <td>None</td>
                                                            <td style="text-align: center">1 </td>
                                                            <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_pregnant" name="eye_radio" value=1></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <br><br><b>GCS Score: </b>
                                            <input class="number" name="gcs_score" id="gcs_score_pregnant" style="text-align: center" min="0" value="0" readonly>
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_reason_referral_pregnant" aria-expanded="false" aria-controls="collapse_reason_referral_pregnant">
                                        <b>REASON FOR REFERRAL</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_reason_referral_pregnant" style="width: 100%;">
                                    <i>Select reason for referral:</i>
                                    <div class="container-referral">
                                        <select name="reason_referral1" class="form-control-select select2 reason_referral" style="width: 100%" required="">
                                            <option value="">Select reason for referral</option>
                                            <option value="-1">Other reason for referral</option>
                                            @foreach($reason_for_referral as $reason_referral)
                                                <option value="{{ $reason_referral->id }}">{{ $reason_referral->reason }}</option>
                                            @endforeach
                                        </select><br><br>
                                        <div id="pregnant_other_reason_referral_div" style="display:none;">
                                            <span>Other Reason for Referral:</span> <br/>
                                            <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr />
                        <div class="form-fotter pull-right">
                            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Back</button>
                            <button type="submit" id="sbmitBtn" class="btn btn-success btn-flat btn-submit"><i class="fa fa-send"></i> Submit</button>
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
            </div><!-- /.modal-content -->
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="icd-modal">
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>
    // $("#revisedpregnantFormModal").modal("show");

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

    /* *****PRENATAL ADD ROW***** */
    <?php
    $select_year = "";
    foreach (range(date('Y'), 1950) as $year)
        $select_year .= "<option>" . $year . "</option>";
    ?>
    var select_year = "<?php echo $select_year; ?>";

    $('#prenatal_add_row_pregnant').on('click', function() {
        var rowCount = $('#prenatal_table tr').length;
        $('#prenatal_table').append('<tr style="font-size: 11pt">\n' +
            '<td><input class="form-control" type="text" name="pregnancy_history_order[]"></td>\n' +
            '<td><select class="form-control select" name="pregnancy_history_year[]">\n' +
            select_year +
            '</select></td>\n' +
            '<td><input class="form-control" id="gestation_id" type="text" name="pregnancy_history_gestation[]"></td>\n' +
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
    $('#comor_diab_pregnant, #comor_asthma_pregnant, #comor_hyper_pregnant, #comor_cancer_pregnant, #comor_others_pregnant').hide();
    $('#comor_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#comor_none_cbox_pregnant').prop('checked', false);
            $('#comor_hyper_cbox_pregnant, #comor_diab_cbox_pregnant, #comor_asthma_cbox_pregnant, #comor_copd_cbox_pregnant').prop('checked', true);
            $('#comor_dyslip_cbox_pregnant, #comor_thyroid_cbox_pregnant, #comor_cancer_cbox_pregnant').prop('checked', true);
            $('#comor_asthma, #comor_diab, #comor_hyper, #comor_cancer').show();
        } else {
            $('#comor_hyper_cbox_pregnant, #comor_diab_cbox_pregnant, #comor_asthma_cbox_pregnant, #comor_copd_cbox_pregnant').prop('checked', false);
            $('#comor_dyslip_cbox_pregnant, #comor_thyroid_cbox_pregnant, #comor_cancer_cbox_pregnant, #comor_others_cbox_pregnant').prop('checked', false);
            $('#comor_asthma_pregnant, #comor_diab_pregnant, #comor_hyper_pregnant, #comor_cancer_pregnant, #comor_others_pregnant').hide();
        }
    });
    $('#comor_hyper_cbox_pregnant').on('click', function() {
        $('#comor_none_cbox_pregnant, #comor_all_cbox_pregnant').prop('checked', false);
        $('#comor_all_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_hyper_pregnant').show();
        else
            $('#comor_hyper_pregnant').hide();
    });
    $('#comor_diab_cbox_pregnant').on('click', function() {
        $('#comor_none_cbox_pregnant, #comor_all_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_diab_pregnant').show();
        else
            $('#comor_diab_pregnant').hide();
    });
    $('#comor_asthma_cbox_pregnant').on('click', function() {
        $('#comor_none_cbox_pregnant, #comor_all_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_asthma_pregnant').show();
        else
            $('#comor_asthma_pregnant').hide();
    });
    $('#comor_cancer_cbox_pregnant').on('click', function() {
        $('#comor_none_cbox_pregnant, #comor_all_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_cancer').show();
        else
            $('#comor_cancer').hide();
    });
    $('#comor_others_cbox_pregnant').on('click', function() {
        $('#comor_none_cbox_pregnant, #comor_all_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#comor_others_pregnant').show();
        else
            $('#comor_others_pregnant').hide();
    });
    $('#comor_copd_cbox_pregnant,#comor_dyslip_cbox_pregnant,#comor_thyroid_cbox_pregnant').on('click', function() {
        $('#comor_none_cbox_pregnant, #comor_all_cbox_pregnant').prop('checked', false);
    });
    $('#comor_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#comor_all_cbox_pregnant, #comor_hyper_cbox_pregnant, #comor_diab_cbox_pregnant, #comor_asthma_cbox_pregnant, #comor_copd_cbox_pregnant').prop('checked', false);
            $('#comor_dyslip_cbox_pregnant, #comor_thyroid_cbox_pregnant, #comor_cancer_cbox_pregnant, #comor_others_cbox_pregnant').prop('checked', false);
            $('#comor_asthma_pregnant, #comor_diab_pregnant, #comor_hyper_pregnant, #comor_cancer_pregnant, #comor_others_pregnant').hide();
        }
    });

    /* *****ALLERGY***** */
    $('#allergy_food, #allergy_drug, #allergy_other').hide();
    $('#allergy_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#allergy_none_cbox_pregnant').prop('checked', false);
            $('#allergy_food_cbox_pregnant, #allergy_drug_cbox_pregnant').prop('checked', true);
            $('#allergy_food_pregnant, #allergy_drug_pregnant').show();
        } else {
            $('#allergy_food_cbox_pregnant, #allergy_drug_cbox_pregnant').prop('checked', false);
            $('#allergy_food_pregnant, #allergy_drug_pregnant').hide();
        }
    });
    $('#allergy_food_cbox_pregnant').on('click', function() {
        $('#allergy_all_cbox_pregnant, #allergy_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#allergy_food_pregnant').show();
        else
            $('#allergy_food_pregnant').hide();
    });
    $('#allergy_drug_cbox_pregnant').on('click', function() {
        $('#allergy_all_cbox_pregnant, #allergy_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#allergy_drug_pregnant').show();
        else
            $('#allergy_drug_pregnant').hide();
    });
    $('#allergy_other_cbox_pregnant').on('click', function() {
        $('#allergy_all_cbox_pregnant, #allergy_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#allergy_other_pregnant').show();
        else
            $('#allergy_other_pregnant').hide();
    });
    $('#allergy_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#allergy_food_cbox_pregnant, #allergy_drug_cbox_pregnant, #allergy_other_cbox_pregnant, #allergy_all_cbox_pregnant').prop('checked', false);
            $('#allergy_food_pregnant, #allergy_drug_pregnant, #allergy_other_pregnant').hide();
        }
    });

    /* *****HEREDOFAMILIAL***** */
    $('#heredo_hyper_pregnant, #heredo_diab_pregnant, #heredo_asthma_pregnant, #heredo_cancer_pregnant, #heredo_kidney_pregnant, #heredo_thyroid_pregnant, #heredo_others_pregnant').hide();
    $('#heredo_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#heredo_none_cbox_pregnant').prop('checked', false);
            $('#heredo_hyper_cbox_pregnant, #heredo_diab_cbox_pregnant, #heredo_asthma_cbox_pregnant, #heredo_cancer_cbox_pregnant').prop('checked', true);
            $('#heredo_kidney_cbox_pregnant, #heredo_thyroid_cbox_pregnant').prop('checked', true);
            $('#heredo_hyper_pregnant, #heredo_diab_pregnant, #heredo_asthma_pregnant, #heredo_cancer_pregnant, #heredo_kidney_pregnant, #heredo_thyroid_pregnant').show();
        } else {
            $('#heredo_hyper_cbox_pregnant, #heredo_diab_cbox_pregnant, #heredo_asthma_cbox_pregnant, #heredo_cancer_cbox_pregnant').prop('checked', false);
            $('#heredo_kidney_cbox_pregnant, #heredo_thyroid_cbox_pregnant').prop('checked', false);
            $('#heredo_hyper_pregnant, #heredo_diab_pregnant, #heredo_asthma_pregnant, #heredo_cancer_pregnant, #heredo_kidney_pregnant, #heredo_thyroid_pregnant').hide();
        }
    });
    $('#heredo_hyper_cbox_pregnant').on('click', function() {
        $('#heredo_all_cbox_pregnant, #heredo_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_hyper_pregnant').show();
        else
            $('#heredo_hyper_pregnant').hide();
    });
    $('#heredo_diab_cbox_pregnant').on('click', function() {
        $('#heredo_all_cbox_pregnant, #heredo_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_diab_pregnant').show();
        else
            $('#heredo_diab_pregnant').hide();
    });
    $('#heredo_asthma_cbox_pregnant').on('click', function() {
        $('#heredo_all_cbox_pregnant, #heredo_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_asthma_pregnant').show();
        else
            $('#heredo_asthma_pregnant').hide();
    });
    $('#heredo_cancer_cbox_pregnant').on('click', function() {
        $('#heredo_all_cbox_pregnant, #heredo_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_cancer_pregnant').show();
        else
            $('#heredo_cancer_pregnant').hide();
    });
    $('#heredo_kidney_cbox_pregnant').on('click', function() {
        $('#heredo_all_cbox_pregnant, #heredo_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_kidney_pregnant').show();
        else
            $('#heredo_kidney_pregnant').hide();
    });
    $('#heredo_thyroid_cbox_pregnant').on('click', function() {
        $('#heredo_all_cbox_pregnant, #heredo_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_thyroid_pregnant').show();
        else
            $('#heredo_thyroid_pregnant').hide();
    });
    $('#heredo_others_cbox_pregnant').on('click', function() {
        $('#heredo_all_cbox_pregnant, #heredo_none_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#heredo_others_pregnant').show();
        else
            $('#heredo_others_pregnant').hide();
    });
    $('#heredo_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#heredo_all_cbox_pregnant, #heredo_hyper_cbox_pregnant, #heredo_diab_cbox_pregnant, #heredo_asthma_cbox_pregnant, #heredo_cancer_cbox_pregnant').prop('checked', false);
            $('#heredo_kidney_cbox_pregnant, #heredo_thyroid_cbox_pregnant, #heredo_others_cbox_pregnant').prop('checked', false);
            $('#heredo_hyper_pregnant, #heredo_diab_pregnant, #heredo_asthma_pregnant, #heredo_cancer_pregnant, #heredo_kidney_pregnant, #heredo_thyroid_pregnant, #heredo_others_pregnant').hide();
        }
    });

    /* *****LAB PROCEDURES***** */
    $('#lab_others_pregnant').hide();
    $('#lab_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#lab_ua_cbox_pregnant, #lab_cbc_cbox_pregnant, #lab_xray_cbox_pregnant').prop('checked', true);
        else
            $('#lab_ua_cbox_pregnant, #lab_cbc_cbox_pregnant, #lab_xray_cbox_pregnant').prop('checked', false);
    });
    $('#lab_others_cbox_pregnant').on('click', function() {
        $('#lab_all_cbox_pregnant').prop('checked', false);
        if ($(this).is(':checked'))
            $('#lab_others_pregnant').show();
        else
            $('#lab_others_pregnant').hide();
    });
    $('#lab_ua_cbox_pregnant, #lab_cbc_cbox_pregnant, #lab_xray_cbox_pregnant').on('click', function() {
        $('#lab_all_cbox_pregnant').prop('checked', false);
    });

    /* *****PRENATAL***** */
    $('#prenatal_mat_illness_pregnant').hide();
    $('#prenatal_radiowith_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#prenatal_mat_illness_pregnant').show();
    });
    $('#prenatal_radiowout_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#prenatal_mat_illness_pregnant').hide();
    });

    /* *****POST NATAL (FEEDING HISTORY)****** */
    $('#breastfed_pregnant, #formula_fed_pregnant').hide();
    $('#postnatal_bfeed_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#breastfed_pregnant').show();
        else
            $('#breastfed_pregnant').hide();
    });
    $('#postnatal_ffeed_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#formula_fed_pregnant').show();
        else
            $('#formula_fed_pregnant').hide();
    });

    /* *****POST NATAL (IMMUNIZATION HISTORY)****** */
    $('#immu_dpt_pregnant, #immu_hepb_pregnant, #immu_others_pregnant').hide();
    $('#immu_dpt_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#immu_dpt_pregnant').show();
        else
            $('#immu_dpt_pregnant').hide();
    });
    $('#immu_hepb_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#immu_hepb_pregnant').show();
        else
            $('#immu_hepb_pregnant').hide();
    });
    $('#immu_others_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#immu_others_pregnant').show();
        else
            $('#immu_others_pregnant').hide();
    });

    /* *****MENSTRUAL/MENOPAUSAL***** */
    $('#mens_irreg_pregnant, #menopausal_age_pregnant').hide();
    $('#mens_irreg_radio_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#mens_irreg_pregnant').show();
    });
    $('#mens_reg_radio_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#mens_irreg_pregnant').hide();
    });
    $('#menopausal_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#menopausal_age_pregnant').show();
    });
    $('#non_menopausal_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#menopausal_age_pregnant').hide();
    });

    /* *****CONTRACEPTIVES***** */
    $('#contraceptive_others').hide();
    $('#contraceptive_others_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#contraceptive_others').show();
            $('#contraceptive_none_cbox_pregnant').prop('checked', false);
        } else
            $('#contraceptive_others').hide();
    });
    $('#contraceptive_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#contraceptive_others').hide();
            $('#contraceptive_pills_cbox_pregnant, #contraceptive_iud_cbox_pregnant, #contraceptive_rhythm_cbox_pregnant, #contraceptive_condom_cbox_pregnant, #contraceptive_others_cbox_pregnant').prop('checked', false);
        }
    });
    $('#contraceptive_pills_cbox_pregnant,#contraceptive_iud_cbox_pregnant,#contraceptive_rhythm_cbox_pregnant,#contraceptive_condom_cbox_pregnant').on('click', function() {
        $('#contraceptive_none_cbox_pregnant').prop('checked', false);
    });

    /* *****SMOKING***** */
    $('#smoking_sticks_pregnant').hide();
    $('#smoking_quit_year_pregnant').hide();
    $('#smoke_yes_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#smoking_sticks_pregnant').show();
            $('#smoking_quit_year_pregnant').hide();
        }
    });
    $('#smoke_quit_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#smoking_quit_year_pregnant').show();
            $('#smoking_sticks_pregnant').hide();
        }
    });
    $('#smoke_no_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#smoking_sticks_pregnant').hide();
            $('#smoking_quit_year_pregnant').hide();
        }
    });

    /* *****ALCOHOL***** */
    $('#alcohol_bottles_pregnant').hide();
    $('#alcohol_type_pregnant').hide();
    $('#alcohol_quit_year_pregnant').hide();
    $('#alcohol_yes_radio_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#alcohol_bottles_pregnant').show();
            $('#alcohol_type_pregnant').show();
            $('#alcohol_quit_year_pregnant').hide();
        }
    })
    $('#alcohol_no_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#alcohol_bottles_pregnant').hide();
            $('#alcohol_type_pregnant').hide();
            $('#alcohol_quit_year_pregnant').hide();
        }
    });
    $('#alcohol_quit_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#alcohol_quit_year_pregnant').show();
            $('#alcohol_bottles_pregnant').hide();
            $('#alcohol_type_pregnant').hide();
        }
    });

    /* *****ILLICIT DRUGS***** */
    $('#drugs_text_pregnant').hide();
    $('#drugs_quit_year_pregnant').hide();
    $('#drugs_yes_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#drugs_text_pregnant').show();
            $('#drugs_quit_year_pregnant').hide();
        }
    });
    $('#drugs_no_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#drugs_text_pregnant').hide();
            $('#drugs_quit_year_pregnant').hide();
        }
    });
    $('#drugs_quit_radio').on('click', function() {
        if ($(this).is(':checked')) {
            $('#drugs_quit_year_pregnant').show();
            $('#drugs_text_pregnant').hide();
        }
    });

    /* *****MOTOR/VERBAL/EYE RESPONSE (GLASGOW COMA SCALE)***** */
    function resetPupilSize() {
        $('input[name="glasgow_btn"]:checked').each(function() {
            if ($(this).is(':checked'))
                $(this).prop('checked', false);
        })
    }
    var last_motor = last_verbal = last_eye = 0;
    $('input[name="motor_radio"]').on('change', function() {
        var motor = parseInt($('input[name="motor_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score_pregnant').val(), 10);
        var total = 0;
        if (last_motor == 0)
            total = gcs + motor;
        else
            total = (gcs - last_motor) + motor;

        last_motor = motor;
        $('#gcs_score_pregnant').val(total);
    });
    $('input[name="verbal_radio"]').on('change', function() {
        var verbal = parseInt($('input[name="verbal_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score_pregnant').val(), 10);
        var total = 0;
        if (last_verbal == 0)
            total = gcs + verbal;
        else
            total = (gcs - last_verbal) + verbal;

        last_verbal = verbal;
        $('#gcs_score_pregnant').val(total);
    });
    $('input[name="eye_radio"]').on('change', function() {
        var eye = parseInt($('input[name="eye_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score_pregnant').val(), 10);
        var total = 0;
        if (last_eye == 0)
            total = gcs + eye;
        else
            total = (gcs - last_eye) + eye;

        last_eye = eye;
        $('#gcs_score_pregnant').val(total);
    });

    /* *****REVIEW OF SYSTEMS***** */
    /* SKIN */
    $('#rs_skin_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_skin_rashes_cbox_pregnant, #rs_skin_itching_cbox_pregnant, #rs_skin_hairchange_cbox_pregnant').prop('checked', true);
            $('#rs_skin_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_skin_rashes_cbox_pregnant, #rs_skin_itching_cbox_pregnant, #rs_skin_hairchange_cbox_pregnant').prop('checked', false);
    });
    $('#rs_skin_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_skin_all_cbox_pregnant, #rs_skin_rashes_cbox_pregnant, #rs_skin_itching_cbox_pregnant, #rs_skin_hairchange_cbox_pregnant').prop('checked', false);
    });
    $('#rs_skin_rashes_cbox_pregnant, #rs_skin_itching_cbox_pregnant, #rs_skin_hairchange_cbox_pregnant').on('click', function() {
        $('#rs_skin_all_cbox_pregnant, #rs_skin_none_cbox_pregnant').prop('checked', false);
    });

    /* HEAD */
    $('#rs_head_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_head_headache_cbox_pregnant, #rs_head_injury_cbox_pregnant').prop('checked', true);
            $('#rs_head_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_head_headache_cbox_pregnant, #rs_head_injury_cbox_pregnant').prop('checked', false);
    });
    $('#rs_head_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_head_all_cbox_pregnant, #rs_head_headache_cbox_pregnant, #rs_head_injury_cbox_pregnant').prop('checked', false);
    });
    $('#rs_head_headache_cbox_pregnant, #rs_head_injury_cbox_pregnant').on('click', function() {
        $('#rs_head_all_cbox_pregnant, #rs_head_none_cbox_pregnant').prop('checked', false);
    })

    /* EYES */
    $('#rs_eyes_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_eyes_glasses_cbox_pregnant, #rs_eyes_vision_cbox_pregnant, #rs_eyes_pain_cbox_pregnant, #rs_eyes_doublevision_cbox_pregnant').prop('checked', true);
            $('#rs_eyes_flashing_cbox_pregnant, #rs_eyes_glaucoma_cbox_pregnant, #rs_eye_exam_cbox_pregnant').prop('checked', true);
            $('#rs_eyes_none_cbox_pregnant').prop('checked', false);
        } else {
            $('#rs_eyes_glasses_cbox_pregnant, #rs_eyes_vision_cbox_pregnant, #rs_eyes_pain_cbox_pregnant, #rs_eyes_doublevision_cbox_pregnant').prop('checked', false);
            $('#rs_eyes_flashing_cbox_pregnant, #rs_eyes_glaucoma_cbox_pregnant, #rs_eye_exam_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_eyes_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_eyes_glasses_cbox_pregnant, #rs_eyes_vision_cbox_pregnant, #rs_eyes_pain_cbox_pregnant, #rs_eyes_doublevision_cbox_pregnant').prop('checked', false);
            $('#rs_eyes_all_cbox_pregnant, #rs_eyes_flashing_cbox_pregnant, #rs_eyes_glaucoma_cbox_pregnant, #rs_eye_exam_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_eyes_glasses_cbox_pregnant, #rs_eyes_vision_cbox_pregnant, #rs_eyes_pain_cbox_pregnant, #rs_eyes_doublevision_cbox_pregnant, #rs_eyes_flashing_cbox_pregnant, #rs_eyes_glaucoma_cbox_pregnant, #rs_eye_exam_cbox_pregnant').on('click', function() {
        $('#rs_eyes_all_cbox_pregnant, #rs_eyes_none_cbox_pregnant').prop('checked', false);
    });

    /* EARS */
    $('#rs_ears_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_ears_changehearing_cbox_pregnant, #rs_ears_pain_cbox_pregnant, #rs_ears_discharge_cbox_pregnant, #rs_ears_ringing_cbox_pregnant, #rs_ears_dizziness_cbox_pregnant').prop('checked', true);
            $('#rs_ears_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_ears_changehearing_cbox_pregnant, #rs_ears_pain_cbox_pregnant, #rs_ears_discharge_cbox_pregnant, #rs_ears_ringing_cbox_pregnant, #rs_ears_dizziness_cbox_pregnant').prop('checked', false);
    });
    $('#rs_ears_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_ears_all_cbox_pregnant, #rs_ears_changehearing_cbox_pregnant, #rs_ears_pain_cbox_pregnant, #rs_ears_discharge_cbox_pregnant, #rs_ears_ringing_cbox_pregnant, #rs_ears_dizziness_cbox_pregnant').prop('checked', false);
    });
    $('#rs_ears_changehearing_cbox_pregnant, #rs_ears_pain_cbox_pregnant, #rs_ears_discharge_cbox_pregnant, #rs_ears_ringing_cbox_pregnant, #rs_ears_dizziness_cbox_pregnant').on('click', function() {
        $('#rs_ears_all_cbox_pregnant, #rs_ears_none_cbox_pregnant').prop('checked', false);
    });

    /* NOSE/SINUSES */
    $('#rs_nose_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_nose_bleeds_cbox_pregnant, #rs_nose_stuff_cbox_pregnant, #rs_nose_colds_cbox_pregnant').prop('checked', true);
            $('#rs_nose_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_nose_bleeds_cbox_pregnant, #rs_nose_stuff_cbox_pregnant, #rs_nose_colds_cbox_pregnant').prop('checked', false);
    });
    $('#rs_nose_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_nose_all_cbox_pregnant, #rs_nose_bleeds_cbox_pregnant, #rs_nose_stuff_cbox_pregnant, #rs_nose_colds_cbox_pregnant').prop('checked', false);
    });
    $('#rs_nose_bleeds_cbox_pregnant, #rs_nose_stuff_cbox_pregnant, #rs_nose_colds_cbox_pregnant').on('click', function() {
        $('#rs_nose_all_cbox_pregnant, #rs_nose_none_cbox_pregnant').prop('checked', false);
    });

    /* MOUTH/THROAT */
    $('#rs_mouth_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_mouth_bleed_cbox_pregnant, #rs_mouth_soretongue_cbox_pregnant, #rs_mouth_sorethroat_cbox_pregnant, #rs_mouth_hoarse_cbox_pregnant').prop('checked', true);
            $('#rs_mouth_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_mouth_bleed_cbox_pregnant, #rs_mouth_soretongue_cbox_pregnant, #rs_mouth_sorethroat_cbox_pregnant, #rs_mouth_hoarse_cbox_pregnant').prop('checked', false);
    });
    $('#rs_mouth_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_mouth_all_cbox_pregnant, #rs_mouth_bleed_cbox_pregnant, #rs_mouth_soretongue_cbox_pregnant, #rs_mouth_sorethroat_cbox_pregnant, #rs_mouth_hoarse_cbox_pregnant').prop('checked', false);
    });
    $('#rs_mouth_bleed_cbox_pregnant, #rs_mouth_soretongue_cbox_pregnant, #rs_mouth_sorethroat_cbox_pregnant, #rs_mouth_hoarse_cbox_pregnant').on('click', function() {
        $('#rs_mouth_all_cbox_pregnant, #rs_mouth_none_cbox_pregnant').prop('checked', false);
    });

    /* NECK */
    $('#rs_neck_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_neck_lumps_cbox_pregnant, #rs_neck_swollen_cbox_pregnant, #rs_neck_goiter_cbox_pregnant, #rs_neck_stiff_cbox_pregnant').prop('checked', true);
            $('#rs_neck_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_neck_lumps_cbox_pregnant, #rs_neck_swollen_cbox_pregnant, #rs_neck_goiter_cbox_pregnant, #rs_neck_stiff_cbox_pregnant').prop('checked', false);
    });
    $('#rs_neck_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_neck_all_cbox_pregnant, #rs_neck_lumps_cbox_pregnant, #rs_neck_swollen_cbox_pregnant, #rs_neck_goiter_cbox_pregnant, #rs_neck_stiff_cbox_pregnant').prop('checked', false);
    });
    $('#rs_neck_lumps_cbox_pregnant, #rs_neck_swollen_cbox_pregnant, #rs_neck_goiter_cbox_pregnant, #rs_neck_stiff_cbox_pregnant').on('click', function() {
        $('#rs_neck_all_cbox_pregnant, #rs_neck_none_cbox_pregnant').prop('checked', false);
    });

    /* BREAST */
    $('#rs_breast_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_breast_lumps_cbox_pregnant, #rs_breast_pain_cbox_pregnant, #rs_breast_discharge_cbox_pregnant, #rs_breast_bse_cbox_pregnant').prop('checked', true);
            $('#rs_breast_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_breast_lumps_cbox_pregnant, #rs_breast_pain_cbox_pregnant, #rs_breast_discharge_cbox_pregnant, #rs_breast_bse_cbox_pregnant').prop('checked', false);
    });
    $('#rs_breast_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_breast_all_cbox_pregnant, #rs_breast_lumps_cbox_pregnant, #rs_breast_pain_cbox_pregnant, #rs_breast_discharge_cbox_pregnant, #rs_breast_bse_cbox_pregnant').prop('checked', false);
    });
    $('#rs_breast_lumps_cbox_pregnant, #rs_breast_pain_cbox_pregnant, #rs_breast_discharge_cbox_pregnant, #rs_breast_bse_cbox_pregnant').on('click', function() {
        $('#rs_breast_all_cbox_pregnant, #rs_breast_none_cbox_pregnant').prop('checked', false);
    });

    /* RESPIRATORY/CARDIAC */
    $('#rs_respi_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_respi_shortness_cbox_pregnant, #rs_respi_cough_cbox_pregnant, #rs_respi_phlegm_cbox_pregnant, #rs_respi_wheezing_cbox_pregnant, #rs_respi_coughblood_cbox_pregnant').prop('checked', true);
            $('#rs_respi_chestpain_cbox_pregnant, #rs_respi_fever_cbox_pregnant, #rs_respi_sweats_cbox_pregnant, #rs_respi_swelling_cbox_pregnant, #rs_respi_bluefingers_cbox_pregnant').prop('checked', true);
            $('#rs_respi_highbp_cbox_pregnant, #rs_respi_skipheartbeats_cbox_pregnant, #rs_respi_heartmurmur_cbox_pregnant, #rs_respi_hxheart_cbox_pregnant, #rs_respi_brochitis_cbox_pregnant, #rs_respi_rheumaticheart_cbox_pregnant').prop('checked', true);
            $('#rs_respi_none_cbox_pregnant').prop('checked', false);
        } else {
            $('#rs_respi_shortness_cbox_pregnant, #rs_respi_cough_cbox_pregnant, #rs_respi_phlegm_cbox_pregnant, #rs_respi_wheezing_cbox_pregnant, #rs_respi_coughblood_cbox_pregnant').prop('checked', false);
            $('#rs_respi_chestpain_cbox_pregnant, #rs_respi_fever_cbox_pregnant, #rs_respi_sweats_cbox_pregnant, #rs_respi_swelling_cbox_pregnant, #rs_respi_bluefingers_cbox_pregnant').prop('checked', false);
            $('#rs_respi_highbp_cbox_pregnant, #rs_respi_skipheartbeats_cbox_pregnant, #rs_respi_heartmurmur_cbox_pregnant, #rs_respi_hxheart_cbox_pregnant, #rs_respi_brochitis_cbox_pregnant, #rs_respi_rheumaticheart_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_respi_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_respi_all_cbox_pregnant, #rs_respi_shortness_cbox_pregnant, #rs_respi_cough_cbox_pregnant, #rs_respi_phlegm_cbox_pregnant, #rs_respi_wheezing_cbox_pregnant, #rs_respi_coughblood_cbox_pregnant').prop('checked', false);
            $('#rs_respi_chestpain_cbox_pregnant, #rs_respi_fever_cbox_pregnant, #rs_respi_sweats_cbox_pregnant, #rs_respi_swelling_cbox_pregnant, #rs_respi_bluefingers_cbox_pregnant').prop('checked', false);
            $('#rs_respi_highbp_cbox_pregnant, #rs_respi_skipheartbeats_cbox_pregnant, #rs_respi_heartmurmur_cbox_pregnant, #rs_respi_hxheart_cbox_pregnant, #rs_respi_brochitis_cbox_pregnant, #rs_respi_rheumaticheart_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_respi_shortness_cbox_pregnant, #rs_respi_cough_cbox_pregnant, #rs_respi_phlegm_cbox_pregnant, #rs_respi_wheezing_cbox_pregnant, #rs_respi_coughblood_cbox_pregnant').on('click', function() {
        $('#rs_respi_all_cbox_pregnant, #rs_respi_none_cbox_pregnant').prop('checked', false);
    });
    $('#rs_respi_chestpain_cbox_pregnant, #rs_respi_fever_cbox_pregnant, #rs_respi_sweats_cbox_pregnant, #rs_respi_swelling_cbox_pregnant, #rs_respi_bluefingers_cbox_pregnant').on('click', function() {
        $('#rs_respi_all_cbox_pregnant, #rs_respi_none_cbox_pregnant').prop('checked', false);
    });
    $('#rs_respi_highbp_cbox_pregnant, #rs_respi_skipheartbeats_cbox_pregnant, #rs_respi_heartmurmur_cbox_pregnant, #rs_respi_hxheart_cbox_pregnant, #rs_respi_brochitis_cbox_pregnant, #rs_respi_rheumaticheart_cbox_pregnant').on('click', function() {
        $('#rs_respi_all_cbox_pregnant, #rs_respi_none_cbox_pregnant').prop('checked', false);
    });

    /* GASTROINTESTINAL */
    $('#rs_gastro_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_gastro_appetite_cbox_pregnant, #rs_gastro_swallow_cbox_pregnant, #rs_gastro_nausea_cbox_pregnant, #rs_gastro_heartburn_cbox_pregnant, #rs_gastro_vomit_cbox_pregnant, #rs_gastro_vomitblood_cbox_pregnant').prop('checked', true);
            $('#rs_gastro_constipation_cbox_pregnant, #rs_gastro_diarrhea_cbox_pregnant, #rs_gastro_bowel_cbox_pregnant, #rs_gastro_abdominal_cbox_pregnant, #rs_gastro_belching_cbox_pregnant, #rs_gastro_flatus_cbox_pregnant').prop('checked', true);
            $('#rs_gastro_jaundice_cbox_pregnant, #rs_gastro_intolerance_cbox_pregnant, #rs_gastro_rectalbleed_cbox_pregnant').prop('checked', true);
            $('#rs_gastro_none_cbox_pregnant').prop('checked', false);
        } else {
            $('#rs_gastro_appetite_cbox_pregnant, #rs_gastro_swallow_cbox_pregnant, #rs_gastro_nausea_cbox_pregnant, #rs_gastro_heartburn_cbox_pregnant, #rs_gastro_vomit_cbox_pregnant, #rs_gastro_vomitblood_cbox_pregnant').prop('checked', false);
            $('#rs_gastro_constipation_cbox_pregnant, #rs_gastro_diarrhea_cbox_pregnant, #rs_gastro_bowel_cbox_pregnant, #rs_gastro_abdominal_cbox_pregnant, #rs_gastro_belching_cbox_pregnant, #rs_gastro_flatus_cbox_pregnant').prop('checked', false);
            $('#rs_gastro_jaundice_cbox_pregnant, #rs_gastro_intolerance_cbox_pregnant, #rs_gastro_rectalbleed_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_gastro_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_gastro_appetite_cbox_pregnant, #rs_gastro_swallow_cbox_pregnant, #rs_gastro_nausea_cbox_pregnant, #rs_gastro_heartburn_cbox_pregnant, #rs_gastro_vomit_cbox_pregnant, #rs_gastro_vomitblood_cbox_pregnant').prop('checked', false);
            $('#rs_gastro_constipation_cbox_pregnant, #rs_gastro_diarrhea_cbox_pregnant, #rs_gastro_bowel_cbox_pregnant, #rs_gastro_abdominal_cbox_pregnant, #rs_gastro_belching_cbox_pregnant, #rs_gastro_flatus_cbox_pregnant').prop('checked', false);
            $('#rs_gastro_all_cbox_pregnant, #rs_gastro_jaundice_cbox_pregnant, #rs_gastro_intolerance_cbox_pregnant, #rs_gastro_rectalbleed_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_gastro_appetite_cbox_pregnant, #rs_gastro_swallow_cbox_pregnant, #rs_gastro_nausea_cbox_pregnant, #rs_gastro_heartburn_cbox_pregnant, #rs_gastro_vomit_cbox_pregnant, #rs_gastro_vomitblood_cbox_pregnant').on('click', function() {
        $('#rs_gastro_all_cbox_pregnant, #rs_gastro_none_cbox_pregnant').prop('checked', false);
    });
    $('#rs_gastro_constipation_cbox_pregnant, #rs_gastro_diarrhea_cbox_pregnant, #rs_gastro_bowel_cbox_pregnant, #rs_gastro_abdominal_cbox_pregnant, #rs_gastro_belching_cbox_pregnant, #rs_gastro_flatus_cbox_pregnant').on('click', function() {
        $('#rs_gastro_all_cbox_pregnant, #rs_gastro_none_cbox_pregnant').prop('checked', false);
    });
    $('#rs_gastro_jaundice_cbox_pregnant, #rs_gastro_intolerance_cbox_pregnant, #rs_gastro_rectalbleed_cbox_pregnant').on('click', function() {
        $('#rs_gastro_all_cbox_pregnant, #rs_gastro_none_cbox_pregnant').prop('checked', false);
    });

    /* URINARY */
    $('#rs_urin_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_urin_difficult_cbox_pregnant, #rs_urin_pain_cbox_pregnant, #rs_urin_frequent_cbox_pregnant, #rs_urin_urgent_cbox_pregnant, #rs_urin_incontinence_cbox_pregnant').prop('checked', true);
            $('#rs_urin_dribbling_cbox_pregnant, #rs_urin_decreased_cbox_pregnant, #rs_urin_blood_cbox_pregnant, #rs_urin_uti_cbox_pregnant').prop('checked', true);
            $('#rs_urin_none_cbox_pregnant').prop('checked', false);
        } else {
            $('#rs_urin_difficult_cbox_pregnant, #rs_urin_pain_cbox_pregnant, #rs_urin_frequent_cbox_pregnant, #rs_urin_urgent_cbox_pregnant, #rs_urin_incontinence_cbox_pregnant').prop('checked', false);
            $('#rs_urin_dribbling_cbox_pregnant, #rs_urin_decreased_cbox_pregnant, #rs_urin_blood_cbox_pregnant, #rs_urin_uti_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_urin_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_urin_difficult_cbox_pregnant, #rs_urin_pain_cbox_pregnant, #rs_urin_frequent_cbox_pregnant, #rs_urin_urgent_cbox_pregnant, #rs_urin_incontinence_cbox_pregnant').prop('checked', false);
            $('#rs_urin_all_cbox_pregnant, #rs_urin_dribbling_cbox_pregnant, #rs_urin_decreased_cbox_pregnant, #rs_urin_blood_cbox_pregnant, #rs_urin_uti_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_urin_difficult_cbox_pregnant, #rs_urin_pain_cbox_pregnant, #rs_urin_frequent_cbox_pregnant, #rs_urin_urgent_cbox_pregnant, #rs_urin_incontinence_cbox_pregnant').on('click', function() {
        $('#rs_urin_all_cbox_pregnant, #rs_urin_none_cbox_pregnant').prop('checked', false);
    });
    $('#rs_urin_dribbling_cbox_pregnant, #rs_urin_decreased_cbox_pregnant, #rs_urin_blood_cbox_pregnant, #rs_urin_uti_cbox_pregnant').on('click', function() {
        $('#rs_urin_all_cbox_pregnant, #rs_urin_none_cbox_pregnant').prop('checked', false);
    });

    /* PERIPHERAL VASCULAR */
    $('#rs_peri_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_peri_legcramp_cbox_pregnant, #rs_peri_varicose_cbox_pregnant, #rs_peri_veinclot_cbox_pregnant').prop('checked', true);
            $('#rs_peri_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_peri_legcramp_cbox_pregnant, #rs_peri_varicose_cbox_pregnant, #rs_peri_veinclot_cbox_pregnant').prop('checked', false);
    });
    $('#rs_peri_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_peri_all_cbox_pregnant, #rs_peri_legcramp_cbox_pregnant, #rs_peri_varicose_cbox_pregnant, #rs_peri_veinclot_cbox_pregnant').prop('checked', false);
    });
    $('#rs_peri_legcramp_cbox_pregnant, #rs_peri_varicose_cbox_pregnant, #rs_peri_veinclot_cbox_pregnant').on('click', function() {
        $('#rs_peri_all_cbox_pregnant, #rs_peri_none_cbox_pregnant').prop('checked', false);
    });

    /* MUSCULOSKELETAL */
    $('#rs_muscle_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_muscle_pain_cbox_pregnant, #rs_muscle_swell_cbox_pregnant, #rs_muscle_stiff_cbox_pregnant, #rs_muscle_decmotion_cbox_pregnant, #rs_muscle_brokenbone_cbox_pregnant, #rs_muscle_sprain_cbox_pregnant, #rs_muscle_arthritis_cbox_pregnant, #rs_muscle_gout_cbox_pregnant, #rs_musclgit_cbox_pregnant').prop('checked', true);
            $('#rs_muscle_none_cbox_pregnant').prop('checked', false);
        } else {
            $('#rs_muscle_pain_cbox_pregnant, #rs_muscle_swell_cbox_pregnant, #rs_muscle_stiff_cbox_pregnant, #rs_muscle_decmotion_cbox_pregnant, #rs_muscle_brokenbone_cbox_pregnant, #rs_muscle_sprain_cbox_pregnant, #rs_muscle_arthritis_cbox_pregnant, #rs_muscle_gout_cbox_pregnant, #rs_musclgit_cbox_pregnant').prop('checked', false);
        }
    });

    $('#rs_muscle_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_muscle_all_cbox_pregnant, #rs_muscle_pain_cbox_pregnant, #rs_muscle_swell_cbox_pregnant, #rs_muscle_stiff_cbox_pregnant, #rs_muscle_decmotion_cbox_pregnant, #rs_muscle_brokenbone_cbox_pregnant, #rs_muscle_sprain_cbox_pregnant, #rs_muscle_arthritis_cbox_pregnant, #rs_muscle_gout_cbox_pregnant, #rs_musclgit_cbox_pregnant').prop('checked', false);
        }
    });

    $('#rs_muscle_pain_cbox_pregnant, #rs_muscle_swell_cbox_pregnant, #rs_muscle_stiff_cbox_pregnant, #rs_muscle_decmotion_cbox_pregnant, #rs_muscle_brokenbone_cbox_pregnant, #rs_muscle_sprain_cbox_pregnant, #rs_muscle_arthritis_cbox_pregnant, #rs_muscle_gout_cbox_pregnant, #rs_musclgit_cbox_pregnant').on('click', function() {
        $('#rs_muscle_all_cbox_pregnant, #rs_muscle_none_cbox_pregnant').prop('checked', false);
    });


    /* NEUROLOGIC */
    $('#rs_neuro_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_neuro_headache_cbox_pregnant, #rs_neuro_seizure_cbox_pregnant, #rs_neuro_faint_cbox_pregnant, #rs_neuro_paralysis_cbox_pregnant, #rs_neuro_weakness_cbox_pregnant, #rs_neuro_sizeloss_cbox_pregnant').prop('checked', true);
            $('#rs_neuro_spasm_cbox_pregnant, #rs_neuro_tremor_cbox_pregnant, #rs_neuro_involuntary_cbox_pregnant, #rs_neuro_incoordination_cbox_pregnant, #rs_neuro_numbness_cbox_pregnant, #rs_neuro_tingles_cbox_pregnant').prop('checked', true);
            $('#rs_neuro_none_cbox_pregnant').prop('checked', false);
        } else {
            $('#rs_neuro_headache_cbox_pregnant, #rs_neuro_seizure_cbox_pregnant, #rs_neuro_faint_cbox_pregnant, #rs_neuro_paralysis_cbox_pregnant, #rs_neuro_weakness_cbox_pregnant, #rs_neuro_sizeloss_cbox_pregnant').prop('checked', false);
            $('#rs_neuro_spasm_cbox_pregnant, #rs_neuro_tremor_cbox_pregnant, #rs_neuro_involuntary_cbox_pregnant, #rs_neuro_incoordination_cbox_pregnant, #rs_neuro_numbness_cbox_pregnant, #rs_neuro_tingles_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_neuro_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_neuro_all_cbox_pregnant, #rs_neuro_headache_cbox_pregnant, #rs_neuro_seizure_cbox_pregnant, #rs_neuro_faint_cbox_pregnant, #rs_neuro_paralysis_cbox_pregnant, #rs_neuro_weakness_cbox_pregnant, #rs_neuro_sizeloss_cbox_pregnant').prop('checked', false);
            $('#rs_neuro_spasm_cbox_pregnant, #rs_neuro_tremor_cbox_pregnant, #rs_neuro_involuntary_cbox_pregnant, #rs_neuro_incoordination_cbox_pregnant, #rs_neuro_numbness_cbox_pregnant, #rs_neuro_tingles_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_neuro_headache_cbox_pregnant, #rs_neuro_seizure_cbox_pregnant, #rs_neuro_faint_cbox_pregnant, #rs_neuro_paralysis_cbox_pregnant, #rs_neuro_weakness_cbox_pregnant, #rs_neuro_sizeloss_cbox_pregnant').on('click', function() {
        $('#rs_neuro_all_cbox_pregnant, #rs_neuro_none_cbox_pregnant').prop('checked', false);
    });
    $('#rs_neuro_spasm_cbox_pregnant, #rs_neuro_tremor_cbox_pregnant, #rs_neuro_involuntary_cbox_pregnant, #rs_neuro_incoordination_cbox_pregnant, #rs_neuro_numbness_cbox_pregnant, #rs_neuro_tingles_cbox_pregnant').on('click', function() {
        $('#rs_neuro_all_cbox_pregnant, #rs_neuro_none_cbox_pregnant').prop('checked', false);
    });

    /* HEMATOLOGIC */
    $('#rs_hema_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_hema_anemia_cbox_pregnant, #rs_hema_bruising_cbox_pregnant, #rss_hema_transfusion_cbox_pregnant').prop('checked', true);
            $('#rs_hema_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_hema_anemia_cbox_pregnant, #rs_hema_bruising_cbox_pregnant, #rss_hema_transfusion_cbox_pregnant').prop('checked', false);
    });
    $('#rs_hema_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_hema_all_cbox_pregnant, #rs_hema_anemia_cbox_pregnant, #rs_hema_bruising_cbox_pregnant, #rss_hema_transfusion_cbox_pregnant').prop('checked', false);
    });
    $('#rs_hema_anemia_cbox_pregnant, #rs_hema_bruising_cbox_pregnant, #rss_hema_transfusion_cbox_pregnant').on('click', function() {
        $('#rs_hema_all_cbox_pregnant, #rs_hema_none_cbox_pregnant').prop('checked', false);
    });

    /* ENDOCRINE */
    $('#rs_endo_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_endo_abnormal_cbox_pregnant, #rs_endo_appetite_cbox_pregnant, #rs_endo_thirst_cbox_pregnant, #rs_endo_urine_cbox_pregnant, #rs_endo_thyroid_cbox_pregnant').prop('checked', true);
            $('#rs_endo_heatcold_cbox_pregnant, #rs_endo_sweat_cbox_pregnant, #rs_endo_diabetes_cbox_pregnant').prop('checked', true);
            $('#rs_endo_none_cbox_pregnant').prop('checked', false);
        } else {
            $('#rs_endo_abnormal_cbox_pregnant, #rs_endo_appetite_cbox_pregnant, #rs_endo_thirst_cbox_pregnant, #rs_endo_urine_cbox_pregnant, #rs_endo_thyroid_cbox_pregnant').prop('checked', false);
            $('#rs_endo_heatcold_cbox_pregnant, #rs_endo_sweat_cbox_pregnant, #rs_endo_diabetes_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_endo_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_endo_abnormal_cbox_pregnant, #rs_endo_appetite_cbox_pregnant, #rs_endo_thirst_cbox_pregnant, #rs_endo_urine_cbox_pregnant, #rs_endo_thyroid_cbox_pregnant').prop('checked', false);
            $('#rs_endo_all_cbox_pregnant, #rs_endo_heatcold_cbox_pregnant, #rs_endo_sweat_cbox_pregnant, #rs_endo_diabetes_cbox_pregnant').prop('checked', false);
        }
    });
    $('#rs_endo_abnormal_cbox_pregnant, #rs_endo_appetite_cbox_pregnant, #rs_endo_thirst_cbox_pregnant, #rs_endo_urine_cbox_pregnant, #rs_endo_thyroid_cbox_pregnant').on('click', function() {
        $('#rs_endo_all_cbox_pregnant, #rs_endo_none_cbox_pregnant').prop('checked', false);
    });
    $('#rs_endo_heatcold_cbox_pregnant, #rs_endo_sweat_cbox_pregnant, #rs_endo_diabetes_cbox_pregnant').on('click', function() {
        $('#rs_endo_all_cbox_pregnant, #rs_endo_none_cbox_pregnant').prop('checked', false);
    });

    /* PSYCHIATRIC */
    $('#rs_psych_all_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_psych_tension_cbox_pregnant, #rs_psych_depression_cbox_pregnant, #rs_psych_memory_cbox_pregnant, #rs_psych_unusual_cbox_pregnant, #rs_psych_sleep_cbox_pregnant, #rs_psych_treatment_cbox_pregnant, #rs_psych_moodchange_cbox_pregnant').prop('checked', true);
            $('#rs_psych_none_cbox_pregnant').prop('checked', false);
        } else
            $('#rs_psych_tension_cbox_pregnant, #rs_psych_depression_cbox_pregnant, #rs_psych_memory_cbox_pregnant, #rs_psych_unusual_cbox_pregnant, #rs_psych_sleep_cbox_pregnant, #rs_psych_treatment_cbox_pregnant, #rs_psych_moodchange_cbox_pregnant').prop('checked', false);
    });
    $('#rs_psych_none_cbox_pregnant').on('click', function() {
        if ($(this).is(':checked'))
            $('#rs_psych_all_cbox_pregnant, #rs_psych_tension_cbox_pregnant, #rs_psych_depression_cbox_pregnant, #rs_psych_memory_cbox_pregnant, #rs_psych_unusual_cbox_pregnant, #rs_psych_sleep_cbox_pregnant, #rs_psych_treatment_cbox_pregnant, #rs_psych_moodchange_cbox_pregnant').prop('checked', false);
    });
    $('#rs_psych_tension_cbox_pregnant, #rs_psych_depression_cbox_pregnant, #rs_psych_memory_cbox_pregnant, #rs_psych_unusual_cbox_pregnant, #rs_psych_sleep_cbox_pregnant, #rs_psych_treatment_cbox_pregnant, #rs_psych_moodchange_cbox_pregnant').on('click', function() {
        $('#rs_psych_all_cbox_pregnant, #rs_psych_none_cbox_pregnant').prop('checked', false);
    });

    /**************************************************************************/

    // $("#sbmitBtn").on('click',function(e){
    //     if(!($("#icd").val()) && !($("#other_diag").val())){
    //         Lobibox.alert("error", {
    //             msg: "Select ICD-10 diagnosis!"
    //         });
    //         return false;
    //     }
    // });

   
        $('.reason_referral').on('change', function() {
            var value = $(this).val();
            
            if (value == -1) {
                // Show the "Other Reason for Referral" textarea if "-1" is selected
                console.log("VALUE: ", value);
                $('#pregnant_other_reason_referral_div').show();
            } else {
                // Hide the "Other Reason for Referral" textarea if another option is selected
                console.log("VALUE: ", value);  
                $('#pregnant_other_reason_referral_div').hide();
            }
        });


    function readURL(input) {
        if (input.files && input.files[0]) {

            var reader = new FileReader();

            reader.onload = function(e) {
                $('.image-upload-wrap').hide();

                $('.file-upload-image').attr('src', e.target.result);
                $('.file-upload-content').show();

                $('.image-title').html(input.files[0].name);
            };

            reader.readAsDataURL(input.files[0]);

        } else {
            removeUpload();
        }
    }

    function removeUpload() {
        $('.file-upload-input').replaceWith($('.file-upload-input').clone());
        $('.file-upload-content').hide();
        $('.image-upload-wrap').show();
    }

    $('.image-upload-wrap').bind('dragover', function() {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function() {
        $('.image-upload-wrap').removeClass('image-dropping');
    });
</script>