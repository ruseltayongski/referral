<?php
      $appointmentParam = $_GET['appointment'];
      $facility_id_telemedicine = json_decode(json_decode($appointmentParam, true),true)[0]['facility_id'] ?? json_decode($appointmentParam, true)[0]['facility_id'];
      $telemedicine_appointment_id = json_decode(json_decode($appointmentParam, true),true)[0]['appointmentId'] ?? json_decode($appointmentParam, true)[0]['appointmentId'];
      $telemedicine_doctor_id = json_decode(json_decode($appointmentParam, true),true)[0]['doctorId'] ?? json_decode($appointmentParam, true)[0]['doctorId'];
  
      $user = Session::get('auth');
      $facilities = \App\Facility::select('id','name')
          ->where('id','!=',$user->facility_id)
          ->where('status',1)
          ->where('referral_used','yes');
      if($facility_id_telemedicine) {
          $facilities = $facilities->where('id', $facility_id_telemedicine);
      }
  
      $facilities = $facilities
          ->orderBy('name','asc')
          ->get();
  
      $myfacility = \App\Facility::find($user->facility_id);
      $facility_address = \App\Http\Controllers\LocationCtrl::facilityAddress($myfacility->id);
      $inventory = \App\Inventory::where("facility_id",$myfacility->id)->get();
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
    } */

    /* .image-upload-wrap {
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
    } */

    /* .remove-image {
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

    /* .container-referral {
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


    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    } */

    #glasgow_normaltable_1, tr td:nth-child(1) {width: 35%;}
    #glasgow_normaltable_2 tr td:nth-child(2) {width: 35%;}  

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
    <div class="modal fade" role="dialog" tabindex="-1" id="revisednormalFormModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{url('submit-referral/normal')}}" method="POST" class="form-submit revised_normal_form">
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
                                    <select name="referred_facility" class="modal-select2 select_facility form-control" required>
                                        <option value="">Select Facility...</option>
                                        @foreach($facilities as $row)
                                       {{-- @if ($row->id == 24) --}}
                                        <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                       {{-- @endif --}}
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-success"><small class="text-success">DEPARTMENT:</small></small> <span class="text-red">*</span><br>
                                    <select name="referred_department" class="form-control select_department select_department_normal" style="width: 100%;" required>
                                        <option value="">Select Department...</option>
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
                                    <select name="clinical_status" class="form-control-select" style="width: 100%;">
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
                                    <select name="sur_category" class="form-control-select" style="width: 100%;">
                                        <option value="">Select option</option>
                                        <option value="contact_pum">Contact (PUM)</option>
                                        <option value="suspect">Suspect</option>
                                        <option value="probable">Probable</option>
                                        <option value="confirmed">Confirmed</option>
                                    </select>
                                </div>
                            </div><br>

                            <div class="row" >
                                <div class="col-lg-12">
                                    <div class="container-referral2">
                                        <button class="btn btn-m collapsed " type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_illness_history_normal" aria-expanded="false" aria-controls="collapse_illness_history_normal">
                                            <b>HISTORY OF PRESENT ILLNESS</b><i> (required)</i><span class="text-red">*</span>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </button><br><br>
                                    </div>
                                    <div class="collapse" id="collapse_illness_history_normal" style="width: 100%">
                                        <small class="text-success"><b>CASE SUMMARY:</b></small><span class="text-red">*</span>
                                        <textarea class="form-control" name="case_summary" style="resize: none;width: 100%;" rows="7" required></textarea><br><br>
                                        <small class="text-success"><b>CHIEF COMPLAINTS:</b></small><span class="text-red">*</span>
                                        <textarea class="form-control" name="reco_summary" style="resize: none;width: 100%;" rows="7" required></textarea><br><br>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="container-referral2">
                                        <button class="btn btn-m collapsed " type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_diagnosis_normal" aria-expanded="false" aria-controls="collapse_diagnosis_normal">
                                            <b>DIAGNOSIS</b><i> (required)</i><span class="text-red">*</span>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </button><br><br>
                                    </div>
                                    <div class="collapse " id="collapse_diagnosis_normal" style="width: 100%">
                                    <br>
                                            <small class="text-success"><b>DIAGNOSIS</b></small> <span class="text-red">*</span>
                                                <br><br>
                                            <a data-toggle="modal" data-target="#icd-modal_revised" type="button" class="btn btn-sm btn-success" onclick="searchICD10Revised()">
                                                <i class="fa fa-medkit"></i> Add ICD-10
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" onclick="addNotesDiagnosisRevised()"><i class="fa fa-plus"></i> Add notes in diagnosis</button>
                                            <div class="row" style="padding-top: 10px;">
                                            <div class="col-md-12">
                                                <button type="button" id="clear_icd_revised" class="btn btn-xs btn-danger" onclick="clearICDRevised()"> Clear ICD-10</button><br>
                                                <div class="text-success" id="icd_selected_revised" style="padding-top: 5px"></div>
                                            </div>
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-md-12">
                                                <button type="button" id="clear_notes_revised" class="btn btn-xs btn-info" onclick="clearNotesDiagnosisRevised()"> Clear notes diagnosis</button>
                                                <div id="add_notes_diagnosis_revised" style="padding-top: 5px"></div>
                                            </div>
                                        </div>
                                        <div class="row" style="padding-top: 10px">
                                        <div class="col-md-12">
                                            <button type="button" id="clear_other_diag_revised" class="btn btn-xs btn-warning" onclick="clearOtherDiagnosisRevised()"> Clear other diagnosis</button>
                                            <div id="others_diagnosis_revised" style="padding-top: 5px"></div>
                                            </div>
                                        </div><br>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
                                                    <input class="form-check-input" id="comor_all_cbox" name="comor_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="comor_all_cbox" value="Yes">
                                                    <span>Select All</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-check-input" id="comor_none_cbox" name="comor_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="comor_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="comor_hyper_cbox" name="comor_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"> Hypertension
                                                    <span id="comor_hyper"> since
                                                        <select class="form-control select" name="hyper_year" style="font-size: 10pt;">
                                                            <option value="">Select</option>
                                                            <?php
                                                            foreach (range(date('Y'), 1950) as $year) {
                                                                echo "<option>$year</option>\n";
                                                            }
                                                            ?>
                                                        </select>
                                                    </span>
                                                </div>
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="comor_diab_cbox" name="comor_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"> Diabetes Mellitus
                                                    <span id="comor_diab"> since
                                                        <select class="form-control select" name="diab_year" style="font-size: 10pt;">
                                                            <option value="">Select</option>
                                                            <?php
                                                            foreach (range(date('Y'), 1950) as $year) {
                                                                echo "<option>$year</option>\n";
                                                            }
                                                            ?>
                                                        </select>
                                                    </span>
                                                </div>
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="comor_asthma_cbox" name="comor_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"> Bronchial Asthma
                                                    <span id="comor_asthma"> since
                                                        <select class="form-control select" name="asthma_year" style="font-size: 10pt;">
                                                            <option value="">Select</option>
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

                                                    <input class="form-check-input" id="comor_copd_cbox" name="comor_copd_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> COPD</span>
                                                </div>
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="comor_dyslip_cbox" name="comor_dyslip_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Dyslipidemia</span>
                                                </div>
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="comor_thyroid_cbox" name="comor_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Thyroid Disease</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="comor_cancer_cbox" name="comor_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Cancer <i>(specify)</i>:</span>
                                                    <textarea class="form-control" name="comor_cancer" id="comor_cancer" style="resize: none;width: 100%;" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="comor_others_cbox" name="comor_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Other(s): </span>
                                                    <textarea class="form-control" name="comor_others" id="comor_others" style="resize: none;width: 100%;" rows="2"></textarea>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>ALLERGIES</b></small><i> (Specify)</i><br>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="allergy_all_cbox" name="allergy_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="allergy_none_cbox" name="allergy_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="allergy_food_cbox" name="allergy_food_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Food(s): <i>(ex. crustaceans, eggs)</i> </span>
                                                    <textarea class="form-control" id="allergy_food" name="allergy_food_cause" style="resize: none;width: 100%;" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="allergy_drug_cbox" name="allergy_drug_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Drug(s): <i>(ex. Ibuprofen, NSAIDS)</i></span>
                                                    <textarea class="form-control" id="allergy_drug" name="allergy_drug_cause" style="resize: none;width: 100%;" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-4">

                                                    <input class="form-check-input" id="allergy_other_cbox" name="allergy_other_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Other(s): </span>
                                                    <textarea class="form-control" id="allergy_other" name="allergy_other_cause" style="resize: none;width: 100%;" rows="2"></textarea>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>HEREDOFAMILIAL DISEASES</b></small> <i>(Specify which side of the family: maternal, paternal, both)</i>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="heredo_all_cbox" name="heredo_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="heredo_none_cbox" name="heredo_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="heredo_hyper_cbox" name="heredo_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Hypertension: </span>
                                                    <select class="form-control-select" id="heredo_hyper" name="heredo_hypertension_side">
                                                        <option value="">Select</option>
                                                        <option value="Maternal">Maternal</option>
                                                        <option value="Paternal">Paternal</option>
                                                        <option value="Both">Both</option>
                                                    </select>
                                                    <!-- <input type="text" id="heredo_hyper" name="heredo_hypertension_side"> -->
                                                </div>
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="heredo_diab_cbox" name="heredo_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Diabetes Mellitus: </span>
                                                    <select class="form-control-select" id="heredo_diab" name="heredo_diabetes_side">
                                                        <option value="">Select</option>
                                                        <option value="Maternal">Maternal</option>
                                                        <option value="Paternal">Paternal</option>
                                                        <option value="Both">Both</option>
                                                    </select>
                                                    <!-- <input type="text" id="heredo_diab" name="heredo_diabetes_side"> -->
                                                </div>
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="heredo_asthma_cbox" name="heredo_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Bronchial Asthma: </span>
                                                    <select class="form-control-select" id="heredo_asthma" name="heredo_asthma_side">
                                                        <option value="">Select</option>
                                                        <option value="Maternal">Maternal</option>
                                                        <option value="Paternal">Paternal</option>
                                                        <option value="Both">Both</option>
                                                    </select>
                                                    <!-- <input type="text" id="heredo_asthma" name="heredo_asthma_side"> -->
                                                </div>
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="heredo_cancer_cbox" name="heredo_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Cancer: </span>
                                                    <select class="form-control-select" id="heredo_cancer" name="heredo_cancer_side">
                                                        <option value="">Select</option>
                                                        <option value="Maternal">Maternal</option>
                                                        <option value="Paternal">Paternal</option>
                                                        <option value="Both">Both</option>
                                                    </select>
                                                    <!-- <input type="text" id="heredo_cancer" name="heredo_cancer_side"> -->
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="heredo_kidney_cbox" name="heredo_kidney_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Kidney: </span>
                                                    <select class="form-control-select" id="heredo_kidney" name="heredo_kidney_side">
                                                        <option value="">Select</option>
                                                        <option value="Maternal">Maternal</option>
                                                        <option value="Paternal">Paternal</option>
                                                        <option value="Both">Both</option>
                                                    </select>
                                                    <!-- <input type="text" id="heredo_kidney" name="heredo_kidney_side"> -->
                                                </div>
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="heredo_thyroid_cbox" name="heredo_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Thyroid Disease: </span>
                                                    <select class="form-control-select" id="heredo_thyroid" name="heredo_thyroid_side">
                                                        <option value="">Select</option>
                                                        <option value="Maternal">Maternal</option>
                                                        <option value="Paternal">Paternal</option>
                                                        <option value="Both">Both</option>
                                                    </select>
                                                    <!-- <input type="text" id="heredo_thyroid" name="heredo_thyroid_side"> -->
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="heredo_others_cbox" name="heredo_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes">
                                                    <span> Other(s): </span>
                                                    <input type="text" id="heredo_others" name="heredo_others_side">
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>PREVIOUS HOSPITALIZATION(S) and OPERATION(S)</b></small><br>
                                        <textarea class="form-control" name="previous_hospitalization" style="resize: none;width: 100%;" rows="3"></textarea><br><br>
                                    </div>
                                </div>
                            </div>
                
                            <div class="row" id="pedia_show_normal">
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
                                                    <span>A <input type="number" name="prenatal_age" style="width: 8%" min="0">
                                                        year old G <input type="number" name="prenatal_g" style="width: 8%" min="0"> P <input type="number" style="width: 8%" min="0" name="prenatal_p"> mother
                                                        <input class="referral-radio-btn" name="prenatal_radiowith_or_without" type="radio" id="prenatal_radiowith" name="prenatal_radio" value="with"> with
                                                        <input class="referral-radio-btn" name="prenatal_radiowith_or_without" type="radio" id="prenatal_radiowout" name="prenatal_radio" value="without"> without
                                                        MATERNAL ILLNESS, if Yes (specify)
                                                        <textarea class="form-control" id="prenatal_mat_illness" name="prenatal_maternal_illness" style="resize: none;width: 50%;" rows="2"></textarea>
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
                                                            <option value="home">Home</option>
                                                            <option value="clinic">Clinic</option>
                                                            <option value="hospital">Hospital</option>
                                                        </select>
                                                        <input type="text" id="hospital_name" name="natal_born_address" style="width: 30%;" placeholder="Specify where">
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
                                                        <input type="text" id="cs_indication" name="cs_indication" style="width: 20%;">
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

                                        <small class="text-success"><b>POST NATAL</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <i>Feeding History</i><br>&emsp;
                                                    <span>
                                                    
                                                        <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="postnatal_bfeed" name="postnatal_bfeed" value="Yes"> Breastfed
                                                        <span id="breastfed">
                                                            x <input type="number" name="postnatal_bfeed_xmos" style="width: 7%;" min="0"> mos.
                                                        </span>
                                                    
                                                        <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" id="postnatal_ffeed" type="checkbox" name="postnatal_ffeed" value="Yes"> Formula Fed,
                                                        <span id="formula_fed">
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
                                                
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_bcg_cbox" value="Yes"> BCG
                                                
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_dpt_cbox" name="immu_dpt_cbox" value="Yes"> DPT/OPV
                                                    <span id="immu_dpt">
                                                        x <input type="number" name="immu_dpt_doses" style="width: 7%;" min="0"> doses
                                                    </span>
                                                    
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_hepb_cbox" name="immu_hepb_cbox" value="Yes"> Hep B
                                                    <span id="immu_hepb">
                                                        x <input type="number" name="immu_hepb_doses" style="width: 7%;" min="0"> doses
                                                    </span>
                                                
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_measles_cbox" value="Yes"> Measles
                                                    
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_mmr_cbox" value="Yes"> MMR
                                                
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_others_cbox" name="immu_others_cbox" value="Yes"> Other(s)
                                                    <span id="immu_others">
                                                        <input type="text" name="immu_others" style="width: 20%;">
                                                    </span>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <i>Developmental Milestones</i><br>&emsp;
                                                    <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_under" value="Under developed">
                                                    <label for="dev_miles_under">
                                                        Under developed
                                                    </label>
                                                    <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_par" value="At par with age">
                                                    <label for="dev_miles_par">
                                                        At par with age
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        

                            {{--TODO: COMPARE AGE IF >= 9 AND ONLY IF PT IS WOMAN--}}
                            <div class="row" id="menarche_show">
                                <div class="col-lg-12">
                                    <div class="container-referral2">
                                        <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_gyne_history" aria-expanded="false" aria-controls="collapse_gyne_history">
                                            <div class="web-view"><b>OBSTETRIC AND GYNECOLOGIC HISTORY</b> <i> (as applicable)</i></div>
                                            <div class="mobile-view"><b>OBSTETRIC AND GYNECOLOGIC HISTORY</b><br> <i> (as applicable)</i></div>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </button><br><br>
                                    </div>
                                    <div class="collapse" id="collapse_gyne_history" style="width: 100%;">
                                        <small class="text-success"><b>MENARCHE </b></small> @ <input type="number" style="width: 10%;" name="menarche"> years old &emsp;&emsp;&emsp;&emsp;
                                        <small class="text-success"><b>MENOPAUSE: </b></small>&emsp;
                                        <input type="radio" class="referral-radio-btn" name="menopausal" id="menopausal" value="Yes">
                                        Yes
                                        <input type="radio" class="referral-radio-btn" name="menopausal" id="non_menopausal" value="No">
                                        No
                                        <span id="menopausal_age">(age) <input type="number" name="menopausal_age" style="width: 10%;" min="9"></span><br><br>

                                        <small class="text-success"><b>MENSTRUAL CYCLE</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_reg_radio" value="regular">
                                                    Regular
                                                    <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_irreg_radio" value="irregular">
                                                    Irregular
                                                    <span id="mens_irreg">x <input type="number" name="mens_irreg_xmos" style="width: 15%;" min="0"> mos</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-success"><b>Dysmenorrhea:</b></small> &emsp;
                                                    <input type="radio" class="referral-radio-btn" name="dysme" id="dysme_yes" value="Yes">
                                                    Yes
                                                    <input type="radio" class="referral-radio-btn" name="dysme" id="dysme_no" value="No">
                                                    No<br>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-success">Duration:</small> <input type="number" style="width:15%;" min="0" name="mens_duration"><small class="text-success"> days </small>&emsp;
                                                    <small class="text-success"><b>Pads/day:</b></small> <input type="number" style="width:15%;" min="0" name="mens_padsperday">
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-success"><b>Medication: </b></small> <input type="text" style="width:70%;" name="mens_medication">
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>CONTRACEPTIVE HISTORY</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="contraceptive_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_none_cbox" value="Yes"> None
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="contraceptive_pills_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_pills_cbox" value="Yes"> Pills
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="contraceptive_iud_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_iud_cbox" value="Yes"> IUD
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="contraceptive_rhythm_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_rhythm_cbox" value="Yes"> Rhythm / Calendar
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="contraceptive_condom_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_condom_cbox" value="Yes"> Condom
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="contraceptive_withdrawal_normal_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_withdrawal_cbox" value="Yes"> Withdrawal
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="contraceptive_injections_normal_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_injections_cbox" value="Yes"> Injections
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input class="form-check-input" id="contraceptive_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_other_cbox" value="Yes"> Other(s)
                                                    <textarea class="form-control" id="contraceptive_others" name="contraceptive_other" style="resize: none;width: 50%;" rows="2"></textarea><br>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>PARITY</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <small class="text-success">G</small> <input type="number" min="0" style="width:8%;" name="parity_g">
                                                    <small class="text-success">P</small> <input type="number" min="0" style="width:8%;" name="parity_p">&emsp;
                                                    <small class="text-success">(FT </small> <input type="text" style="width:8%;" name="parity_ft">
                                                    <small class="text-success"> PT </small> <input type="text" style="width:8%;" name="parity_pt">
                                                    <small class="text-success"> A </small> <input type="text" style="width:8%;" name="parity_a">
                                                    <small class="text-success"> L </small> <input type="text" style="width:8%;" name="parity_l"><small class="text-success">)</small>
                                                </div>
                                            </div>
                                        </div><br>

                                        <div class="container-referral">
                                            <small class="text-success">LMP</small>
                                            <input type="text" class="form-control form_datetime" name="parity_lnmp" placeholder="Date/Time">
                                            <small class="text-success">EDC</small><i>(if pregnant)</i>
                                            <input type="text" class="form-control form_datetime" name="parity_edc_ifpregnant" placeholder="Date/Time">
                                        </div><br>

                                        <small class="text-success"><b>AOG</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <small class="text-success">by LMP </small> <input type="number" min="0" style="width:25%;" name="aog_bylnmp"> <small class="text-success">wks</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-success">by UTZ </small> <input type="number" min="0" style="width:25%;" name="aog_byEUTZ"> <small class="text-success">wks</small>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>PRENATAL HISTORY</b></small><br>
                                        <textarea class="form-control" name="prenatal_history" style="resize: none;width: 100%;" rows="4"></textarea><br><br>
                                        <div class="table-responsive" style="overflow-x: auto">
                                            <table class="table table-bordered" id="prenatal_table">
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
                                                                <option value="">Choose...</option>
                                                                <?php
                                                                foreach (range(date('Y'), 1950) as $year) {
                                                                    echo "<option>" . $year . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td><input class="form-control" id="gestation_normal" type="text" name="pregnancy_history_gestation[]"></td>
                                                        <td><input class="form-control" type="text" name="pregnancy_history_outcome[]"></td>
                                                        <td><input class="form-control" type="text" name="pregnancy_history_placeofbirth[]"></td>
                                                        <td>
                                                            <select class="select form-control" name="prenatal_history_sex[]">
                                                                <option value="">Choose...</option>
                                                                <option value="M">Male</option>
                                                                <option value="F">Female</option>
                                                            </select>
                                                        </td>
                                                        <td><input class="form-control" type="text" name="pregnancy_history_birthweight[]"></td>
                                                        <td><input class="form-control" type="text" name="pregnancy_history_presentstatus[]"></td>
                                                        <td><input class="form-control" type="text" maxlength="38" name="pregnancy_history_complications[]"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <button class="btn-sm btn-success" id="prenatal_add_row_normal" type="button">
                                                <i class="fa fa-plus"> Add Row</i>
                                            </button><br><br>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="container-referral2">
                                        <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_persocial_history" aria-expanded="false" aria-controls="collapse_persocial_history">
                                            <div class="web-view"><b>PERSONAL and SOCIAL HISTORY</b> <i> (as applicable)</i></div>
                                            <div class="mobile-view"><b>PERSONAL and SOCIAL HISTORY</b><br> <i> (as applicable)</i></div>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </button><br><br>
                                    </div>
                                    <div class="collapse" id="collapse_persocial_history" style="width: 100%;">
                                        <small class="text-success"><b>SMOKING</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_yes" value="Yes">
                                                    Yes<br>
                                                    <span id="smoking_sticks">Sticks per day: <input type="number" min="0" style="width:30%;" name="smoking_sticks_per_day"></span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_no" value="No">
                                                    No
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_quit" value="Yes">
                                                    Quit
                                                    <span id="smoking_quit_year"> since
                                                        <select class="form-control select" name="smoking_year_quit">
                                                            <option value="">Select Option</option>
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
                                                    <span><small class="text-success">Other Remarks: </small> <textarea class="form-control" style="resize: none;width:50%;" rows="2" name="smoking_other_remarks"></textarea></span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>ALCOHOL DRINKING</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_yes_radio" value="Yes">
                                                    Yes
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_no_radio" value="Yes">
                                                    No
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_quit_radio" value="Yes">
                                                    Quit
                                                    <span id="alcohol_quit_year"> since
                                                        <select class="form-control select" name="alcohol_year_quit">
                                                            <option value="">Select Option</option>
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
                                                    <span id="alcohol_type">Liquor Type: <textarea class="form-control" style="resize: none;" rows="2" name="alcohol_type"></textarea></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span id="alcohol_bottles">Bottles per day: <input type="number" min="0" style="width:25%;" name="alcohol_bottles"></span>
                                                </div>
                                            </div>
                                        </div><br>
                                                                
                                        <small class="text-success"><b>ILLICIT DRUGS</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <input type="radio" name="illicit_drugs" id="drugs_yes_radio" class="referral-radio-btn" value="Yes">
                                                    Yes
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="radio" name="illicit_drugs" id="drugs_no_radio" class="referral-radio-btn" value="No">
                                                No
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="radio" name="illicit_drugs" id="drugs_quit_radio" class="referral-radio-btn" value="Quit">
                                                    Quit
                                                    <span id="drugs_quit_year"> since
                                                        <select class="form-control select" name="drugs_year_quit">
                                                            <option value="">Select Option</option>
                                                            <?php
                                                            foreach (range(date('Y'), 1950) as $year)
                                                                echo "<option>" . $year . "</option>";
                                                            ?>
                                                        </select>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8" id="drugs_text">
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
                                        <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_medication" aria-expanded="false" aria-controls="collapse_medication">
                                            <b>CURRENT MEDICATION(S)</b>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </button><br><br>
                                    </div>
                                    <div class="collapse" id="collapse_medication" style="width: 100%;">
                                        <small class="text-success"><i>Specify number of doses given and time of last dose given.</i></small>
                                        <textarea class="form-control" name="current_meds" style="resize: none;width: 100%;" rows="5"></textarea><br><br>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="container-referral2">
                                        <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_lab_procedures" aria-expanded="false" aria-controls="collapse_lab_procedures">
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
                                    <div class="collapse" id="collapse_lab_procedures" style="width: 100%;">
                                        <small class="text-success"><i> Attach all applicable labs in one file.</i></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="lab_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="lab_ua_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ua_cbox" value="Yes">
                                                    <span> UA</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="lab_cbc_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_cbc_cbox" value="Yes">
                                                    <span> CBC</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="lab_xray_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_xray_cbox" value="Yes">
                                                    <span> X-RAY</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="lab_ultrasound_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ultrasound_cbox" value="Yes">
                                                    <span> ULTRA SOUND</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="lab_ctscan_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ctscan_cbox" value="Yes">
                                                    <span> CT SCAN</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="lab_ctgmonitoring_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ctgmonitoring_cbox" value="Yes">
                                                    <span> CTG MONITORING</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <input class="form-check-input" id="lab_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_others_cbox" value="Yes"> Others:
                                                    <textarea id="lab_others" class="form-control" name="lab_procedure_other" style="resize: none;" rows="2" name="lab_procedure_other"></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <small class="text-success">FILE ATTACHMENTS:</small> &emsp;
                                                    <button type="button" class="btn btn-md btn-danger" id="normal_remove_files" onclick="removeFileNormal()">Remove Files</button><br><br>
                                                    <div class="normal_file_attachment">
                                                        <div class="col-md-3" id="normal_upload1">
                                                            <div class="file-upload">
                                                                <div class="text-center image-upload-wrap" id="normal_image-upload-wrap1">
                                                                    <input class="file-upload-input" multiple type="file" name="file_upload[]" onchange="readURLNormal(this, 1);" accept="image/png, image/jpeg, image/jpg, image/gif, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/pdf"/>
                                                                    <img src="{{ asset('resources/img/add_file.png') }}" style="width: 50%; height: 50%;">
                                                                </div>
                                                                <div class="file-upload-content" id="normal_file-upload-content1">
                                                                    <img class="file-upload-image" id="normal_file-upload-image1"/>
                                                                    <div class="image-title-wrap">
                                                                        <small class="text-success"><small class="image-title" id="normal_image-title1" style="display:block; word_normal-wrap: break-word_normal;">Uploaded File</small></small>
                                                                        {{--<button type="button" id="normal_remove_upload1" onclick="removeUploadNormal(1)" class="btn-sm remove-image">Remove</button>--}}
                                                                    </div>
                                                                </div>
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
                                                    <input class="form-check-input" id="rs_skin_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_rashes_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_rashes_cbox" value="Yes">
                                                    <span> Rashes</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_itching_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_itching_cbox" value="Yes">
                                                    <span> Itching</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_skin_hairchange_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_hairchange_cbox" value="Yes">
                                                    <span> Change in hair or nails</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>HEAD</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_head_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_head_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_head_headache_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_headache_cbox" value="Yes">
                                                    <span> Headaches</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_head_injury_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_injury_cbox" value="Yes">
                                                    <span> Head injury</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>EYES</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_glasses_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_glasses_cbox" value="Yes">
                                                    <span> Glasses or Contacts</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_vision_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_vision_cbox" value="Yes">
                                                    <span> Change in vision</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_pain_cbox" value="Yes">
                                                    <span> Eye pain</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_doublevision_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_doublevision_cbox" value="Yes">
                                                    <span> Double Vision</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_flashing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_flashing_cbox" value="Yes">
                                                    <span> Flashing lights</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_eyes_glaucoma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_glaucoma_cbox" value="Yes">
                                                    <span> Glaucoma/Cataracts</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>EARS</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_ears_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_ears_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">

                                                    <input class="form-check-input" id="rs_ears_changehearing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_changehearing_cbox" value="Yes">
                                                    <span> Change in hearing</span>
                                                </div>
                                                <div class="col-md-2">

                                                    <input class="form-check-input" id="rs_ears_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_pain_cbox" value="Yes">
                                                    <span> Ear pain</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="rs_ears_discharge_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_discharge_cbox" value="Yes">
                                                    <span> Ear discharge</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="rs_ears_ringing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_ringing_cbox" value="Yes">
                                                    <span> Ringing</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input" id="rs_ears_dizziness_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_dizziness_cbox" value="Yes">
                                                    <span> Dizziness</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>NOSE/SINUSES</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_nose_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_all_cbox" value="Yes">
                                                    <span> Select All </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_nose_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_none_cbox" value="Yes">
                                                    <span> None </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_nose_bleeds_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_bleeds_cbox" value="Yes">
                                                    <span> Nose bleeds</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_nose_stuff_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_stuff_cbox" value="Yes">
                                                    <span> Nasal stuffiness</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_nose_colds_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_colds_cbox" value="Yes">
                                                    <span> Frequent Colds</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>MOUTH/THROAT</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_mouth_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_mouth_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_mouth_bleed_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_bleed_cbox" value="Yes">
                                                    <span> Bleeding gums</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_mouth_soretongue_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_soretongue_cbox" value="Yes">
                                                    <span> Sore tongue</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_mouth_sorethroat_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_sorethroat_cbox" value="Yes">
                                                    <span> Sore throat</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_mouth_hoarse_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_hoarse_cbox" value="Yes">
                                                    <span> Hoarseness</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>NECK</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neck_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neck_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neck_lumps_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_lumps_cbox" value="Yes">
                                                    <span> Lumps</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neck_swollen_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_swollen_cbox" value="Yes">
                                                    <span> Swollen glands</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neck_goiter_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_goiter_cbox" value="Yes">
                                                    <span> Goiter</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neck_stiff_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_stiff_cbox" value="Yes">
                                                    <span> Stiffness</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>BREAST</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_breast_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_breast_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_breast_lumps_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_lumps_cbox" value="Yes">
                                                    <span> Lumps</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_breast_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_pain_cbox" value="Yes">
                                                    <span> Pain</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_breast_discharge_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_discharge_cbox" value="Yes">
                                                    <span> Nipple discharge</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_breast_bse_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_bse_cbox" value="Yes">
                                                    <span> BSE</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>RESPIRATORY/CARDIAC</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_shortness_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_shortness_cbox" value="Yes">
                                                    <span> Shortness of breath</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_cough_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_cough_cbox" value="Yes">
                                                    <span> Cough</span>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input class="form-check-input" id="rs_respi_phlegm_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_phlegm_cbox" value="Yes">
                                                    <span> Production of phlegm, color</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_wheezing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_wheezing_cbox" value="Yes">
                                                    <span> Wheezing</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_coughblood_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_coughblood_cbox" value="Yes">
                                                    <span> Coughing up blood</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_chestpain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_chestpain_cbox" value="Yes">
                                                    <span> Chest pain</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_fever_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_fever_cbox" value="Yes">
                                                    <span> Fever</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_sweats_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_sweats_cbox" value="Yes">
                                                    <span> Night sweats</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_swelling_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_swelling_cbox" value="Yes">
                                                    <span> Swelling in hands/feet</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_bluefingers_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_bluefingers_cbox" value="Yes">
                                                    <span> Blue fingers/toes</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_highbp_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_highbp_cbox" value="Yes">
                                                    <span> High blood pressure</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_skipheartbeats_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_skipheartbeats_cbox" value="Yes">
                                                    <span> Skipping heart beats</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_heartmurmur_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_heartmurmur_cbox" value="Yes">
                                                    <span> Heart murmur</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_hxheart_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_hxheart_cbox" value="Yes">
                                                    <span> HX of heart medication</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_brochitis_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_brochitis_cbox" value="Yes">
                                                    <span> Bronchitis/emphysema</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_respi_rheumaticheart_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_rheumaticheart_cbox" value="Yes">
                                                    <span> Rheumatic heart disease</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>GASTROINTESTINAL</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <input class="form-check-input" id="rs_gastro_appetite_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_appetite_cbox" value="Yes">
                                                    <span> Change of appetite or weight</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_swallow_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_swallow_cbox" value="Yes">
                                                    <span> Problems swallowing</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_nausea_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_nausea_cbox" value="Yes">
                                                    <span> Nausea</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_heartburn_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_heartburn_cbox" value="Yes">
                                                    <span> Heartburn</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_vomit_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_vomit_cbox" value="Yes">
                                                    <span> Vomiting</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_vomitblood_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_vomitblood_cbox" value="Yes">
                                                    <span> Vomiting Blood</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_constipation_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_constipation_cbox" value="Yes">
                                                    <span> Constipation</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_diarrhea_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_diarrhea_cbox" value="Yes">
                                                    <span> Diarrhea</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_bowel_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_bowel_cbox" value="Yes">
                                                    <span> Change in bowel habits</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_abdominal_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_abdominal_cbox" value="Yes">
                                                    <span> Abdominal pain</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_belching_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_belching_cbox" value="Yes">
                                                    <span> Excessive belching</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_flatus_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_flatus_cbox" value="Yes">
                                                    <span> Excessive flatus</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_jaundice_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_jaundice_cbox" value="Yes">
                                                    <span> Yellow color of skin (Jaundice/Hepatitis)</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_intolerance_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_intolerance_cbox" value="Yes">
                                                    <span> Food intolerance</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_gastro_rectalbleed_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_rectalbleed_cbox" value="Yes">
                                                    <span> Rectal bleeding/Hemorrhoids</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>URINARY</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_difficult_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_difficult_cbox" value="Yes">
                                                    <span> Difficulty in urination</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_pain_cbox" value="Yes">
                                                    <span> Pain or burning on urination</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_frequent_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_frequent_cbox" value="Yes">
                                                    <span> Frequent urination at night</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_urgent_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_urgent_cbox" value="Yes">
                                                    <span> Urgent need to urinate</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_incontinence_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_incontinence_cbox" value="Yes">
                                                    <span> Incontinence of urine</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_dribbling_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_dribbling_cbox" value="Yes">
                                                    <span> Dribbling</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_decreased_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_decreased_cbox" value="Yes">
                                                    <span> Decreased urine stream</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_blood_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_blood_cbox" value="Yes">
                                                    <span> Blood in urine</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_urin_uti_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_uti_cbox" value="Yes">
                                                    <span> UTI/stones/prostate infection</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>PERIPHERAL VASCULAR</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_peri_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_peri_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_peri_legcramp_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_legcramp_cbox" value="Yes">
                                                    <span> Leg cramps</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_peri_varicose_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_varicose_cbox" value="Yes">
                                                    <span> Varicose veins</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_peri_veinclot_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_veinclot_cbox" value="Yes">
                                                    <span> Clots in veins</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_peri_edema_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_edema_cbox" value="Yes">
                                                    <span> Edema</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>MUSCULOSKELETAL</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_musclgit_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_musclgit_cbox" value="Yes">
                                                    <span> Pain</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_swell_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_swell_cbox" value="Yes">
                                                    <span> Swelling</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_stiff_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_stiff_cbox" value="Yes">
                                                    <span> Stiffness</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_decmotion_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_decmotion_cbox" value="Yes">
                                                    <span> Decreased joint motion</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_sizeloss_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_sizeloss_cbox" value="Yes">
                                                    <span> Loss of muscle size</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_fracture_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_fracture_cbox" value="Yes">
                                                    <span> Fracture</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_sprain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_sprain_cbox" value="Yes">
                                                    <span> Serious sprains</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_arthritis_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_arthritis_cbox" value="Yes">
                                                    <span> Arthritis</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_gout_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_gout_cbox" value="Yes">
                                                    <span> Gout</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_muscle_spasm_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_spasm_cbox" value="Yes">
                                                    <span> Muscle Spasm</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>NEUROLOGIC</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_headache_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_headache_cbox" value="Yes">
                                                    <span> Headaches</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_seizure_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_seizure_cbox" value="Yes">
                                                    <span> Seizures</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_faint_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_faint_cbox" value="Yes">
                                                    <span> Loss of Consciousness/Fainting</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_paralysis_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_paralysis_cbox" value="Yes">
                                                    <span> Paralysis</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_weakness_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_weakness_cbox" value="Yes">
                                                    <span> Weakness</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_tremor_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_tremor_cbox" value="Yes">
                                                    <span> Tremor</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_disorientation_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_disorientation_cbox" value="Yes">
                                                    <span> Disorientation</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_slurringspeech_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_slurringspeech_cbox" value="Yes">
                                                    <span> Slurring Speech</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_involuntary_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_involuntary_cbox" value="Yes">
                                                    <span> Involuntary movement</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_unsteadygait_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_unsteadygait_cbox" value="Yes">
                                                    <span> Unsteady Gait</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_numbness_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_numbness_cbox" value="Yes">
                                                    <span> Numbness</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_neuro_tingles_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_tingles_cbox" value="Yes">
                                                    <span> Feeling of "pins and needles/tingles"</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>HEMATOLOGIC</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_hema_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_hema_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_hema_anemia_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_anemia_cbox" value="Yes">
                                                    <span> Anemia</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_hema_bruising_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_bruising_cbox" value="Yes">
                                                    <span> Easy bruising/bleeding</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rss_hema_transfusion_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rss_hema_transfusion_cbox" value="Yes">
                                                    <span> Past Transfusions</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>ENDOCRINE</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_endo_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_endo_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_endo_abnormal_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_abnormal_cbox" value="Yes">
                                                    <span> Abnormal growth</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_endo_appetite_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_appetite_cbox" value="Yes">
                                                    <span> Increased appetite</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_endo_thirst_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_thirst_cbox" value="Yes">
                                                    <span> Increased thirst</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_endo_urine_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_urine_cbox" value="Yes">
                                                    <span> Increased urine production</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_endo_heatcold_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_heatcold_cbox" value="Yes">
                                                    <span> Heat/cold intolerance</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_endo_sweat_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_sweat_cbox" value="Yes">
                                                    <span> Excessive sweating</span>
                                                </div>
                                            </div>
                                        </div><br>

                                        <small class="text-success"><b>PSYCHIATRIC</b></small>
                                        <div class="container-referral">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_all_cbox" value="Yes">
                                                    <span> Select All</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_none_cbox" value="Yes">
                                                    <span> None</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_tension_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_tension_cbox" value="Yes">
                                                    <span> Tension/Anxiety</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_depression_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_depression_cbox" value="Yes">
                                                    <span> Depression</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_suicideideation_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_suicideideation_cbox" value="Yes">
                                                    <span> Suicide Ideation</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_memory_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_memory_cbox" value="Yes">
                                                    <span> Memory problems</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_unusual_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_unusual_cbox" value="Yes">
                                                    <span> Unusual problems</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_sleep_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_sleep_cbox" value="Yes">
                                                    <span> Sleep problems</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_treatment_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_treatment_cbox" value="Yes">
                                                    <span> Past treatment with psychiatrist</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="form-check-input" id="rs_psych_moodchange_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_moodchange_cbox" value="Yes">
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
                                                    <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_none" value="None">
                                                    None 
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_oral" value="Oral">
                                                    Oral 
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tube" value="Tube">
                                                    Tube 
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tpn" value="TPN">
                                                    TPN 
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" , id="diet_npo" value="NPO">
                                                    NPO 
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <small class="text-success">Specify Diets:</small> <textarea class="form-control" name="diet" style="resize: none;width: 100%;" rows="3"></textarea><br><br>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
                                                <small class="text-success"><b>Temperature:</b></small> <input type="number" step="0.01" style="width:30%;" min="0" name="vital_temp"> &#176;C
                                                </div>
                                                <div class="col-md-4">
                                                <small class="text-success"><b> Pulse Rate/Heart Rate:</b></small> <input type="number" step="0.01" style="width:30%;" min="0" name="vital_pulse"> bpm
                                                </div>
                                                <div class="col-md-4">
                                                <small class="text-success"><b>Respiratory Rate: </b></small><input type="number" step="0.01" style="width:30%;" min="0" name="vital_respi_rate"> cpm
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                    <div class="col-md-4">
                                                        <small class="text-success"><b>Blood Pressure:</b></small>
                                                        <input type="number" id="systolic_normal" placeholder="Systolic (e.g., 100)" 
                                                            style="width:18%;" min="0" max="300" 
                                                            oninput="updateBloodPressure()"> /
                                                        <input type="number" id="diastolic_normal" placeholder="Diastolic (e.g., 90)" 
                                                            style="width:18%;" min="0" max="200" 
                                                            oninput="updateBloodPressure()">mmHg

                                                        <!-- Hidden input to store the combined value -->
                                                        <input type="hidden" name="vital_bp" id="vital_bp_normal">
                                                    </div>
                                                <div class="col-md-4">
                                                <small class="text-success"><b>O2 Saturation</b></small> <input type="number" step="0.01" style="width:30%;" min="0" name="vital_oxy_saturation"> %
                                                </div>
                                            </div><br>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function updateBloodPressure() {
                                    // Get systolic and diastolic values
                                    const systolic = document.getElementById('systolic_normal').value;
                                    const diastolic = document.getElementById('diastolic_normal').value;
                                    
                                    // Combine into "100/90" format
                                    document.getElementById('vital_bp_normal').value = systolic + '/' + diastolic;
                                }
                            </script>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="container-referral2">
                                        <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_glasgow" aria-expanded="false" aria-controls="collapse_glasgow">
                                            <b>GLASGOW COMA SCALE</b>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </button><br><br>
                                    </div>
                                    <div class="collapse" id="collapse_glasgow" style="width: 100%;">
                                        <small class="text-success"><b>Pupil Size Chart</b></small> &emsp;
                                        <input type="button" class="btn-m btn-warning btn-rounded" onclick="resetPupilSize()" value="Reset">
                                        <div class="container-referral">
                                            <div class="row web-view">
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal1">
                                                        <b>1</b><br>
                                                        <span class="glasgow-dot" style="height: 6px; width: 6px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal1" value="1">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal2">
                                                        <b>2</b><br>
                                                        <span class="glasgow-dot" style="height: 10px; width: 10px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal2" value="2">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal3">
                                                        <b>3</b><br>
                                                        <span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal3" value="3">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal4">
                                                        <b>4</b><br>
                                                        <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal4" value="4">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal5">
                                                        <b>5</b><br>
                                                        <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal5" value="5">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal6">
                                                        <b>6</b><br>
                                                        <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal6" value="6">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal7">
                                                        <b>7</b><br>
                                                        <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal7" value="7">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal8">
                                                        <b>8</b><br>
                                                        <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal8" value="8">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <label for="glasgow_normal9">
                                                        <b>9</b><br>
                                                        <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_normal9" value="9">
                                                </div>
                                                <div class="col-lg-1" style="text-align: center">
                                                    <b>10</b><br>
                                                    <label for="glasgow_normal10">
                                                        <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal10" value="10">
                                                </div>
                                            </div>
                                            <div class="mobile-view">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <b>1</b>
                                                        <label for="glasgow_normal_1">
                                                            <span class="glasgow-dot" style="height: 6px; width: 6px;"></span>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_1" value="1">&emsp;&emsp;

                                                        <b>2</b>
                                                        <label for="glasgow_normal_2">
                                                            <span class="glasgow-dot" style="height: 10px; width: 10px;"></span>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_2" value="2">&emsp;&emsp;

                                                        <b>3</b>
                                                        <label for="glasgow_normal_3">
                                                            <br><span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_3" value="3">
                                                    </div>
                                                </div><br>
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <b>4</b>
                                                        <label for="glasgow_normal_4">
                                                            <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_4" value="4">&emsp;&emsp;

                                                        <b>5</b>
                                                        <label for="glasgow_normal_5">
                                                            <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_5" value="5">&emsp;&emsp;

                                                        <b>6</b>
                                                        <label for="glasgow_normal_6">
                                                            <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_6" value="6">
                                                    </div>
                                                </div><br>
                                                <div class="row">
                                                    <div class="col-md-1"></div>
                                                    <div class="col-md-1">
                                                        <b>7</b>
                                                        <label for="glasgow_normal_7">
                                                            <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_7" value="7">&emsp;&emsp;

                                                        <b>8</b>
                                                        <label for="glasgow_normal_8">
                                                            <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_8" value="8">
                                                    </div>
                                                </div><br>
                                                <div class="row">
                                                    <div class="col-md-1"></div>
                                                    <div class="col-md-1">
                                                        <b>9</b>
                                                        <label for="glasgow_normal_9">
                                                            <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_9" value="9">&emsp;&emsp;

                                                        <b>10</b>
                                                        <label for="glasgow_normal_10">
                                                            <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                        </label>
                                                        <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_normal_10" value="10">
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
                                                                <th id="glasgow_normaltable_1" style="text-align: center">ADULT AND CHILD</th>
                                                                <th id="glasgow_normaltable_2" style="text-align: center">INFANT (2 MONTHS)</th>
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
                                                <small class="text-success"><b>Verbal Response</b></small>
                                                <div class="container-referral table table-responsive" style="overflow-x:auto">
                                                    <table class="table-md table-bordered table-hover">
                                                        <thead>
                                                            <tr style="font-size: 11px;">
                                                                <th id="glasgow_normaltable_1" style="width:35%; text-align: center">ADULT AND CHILD</th>
                                                                <th id="glasgow_normaltable_2" style="width:40%; text-align: center">INFANT (2 MONTHS)</th>
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
                                                <small class="text-success"><b>Eye Response</b></small>
                                                <div class="container-referral table table-responsive" style="overflow-x:auto">
                                                    <table class="table-md table-bordered table-hover">
                                                        <thead>
                                                            <tr style="font-size: 11px;">
                                                                <th id="glasgow_normaltable_1" style="width:40%; text-align: center">ADULT AND CHILD</th>
                                                                <th id="glasgow_normaltable_2" style="text-align: center">INFANT (2 MONTHS)</th>
                                                                <th style="text-align: center">POINTS</th>
                                                                <th style="text-align: center">OPTIONS</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Spontaneous</td>
                                                                <td>Spontaneous</td>
                                                                <td style="text-align: center">4 </td>
                                                                <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_normal4" name="eye_radio" value=4></td>
                                                            </tr>
                                                            <tr>
                                                                <td>To Command</td>
                                                                <td>To Voice</td>
                                                                <td style="text-align: center">3 </td>
                                                                <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_normal3" name="eye_radio" value=3></td>
                                                            </tr>
                                                            <tr>
                                                                <td>To Pain</td>
                                                                <td>To Pain</td>
                                                                <td style="text-align: center">2 </td>
                                                                <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_normal2" name="eye_radio" value=2></td>
                                                            </tr>
                                                            <tr>
                                                                <td>None</td>
                                                                <td>None</td>
                                                                <td style="text-align: center">1 </td>
                                                                <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio_normal1" name="eye_radio" value=1></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <br><br><small class="text-success"><b>GCS Score: </b></small>
                                                <input class="number" name="gcs_score" id="gcs_score_normal" style="text-align: center" min="0" value="0" readonly>
                                            </div>
                                        </div><br>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="col-lg-12">
                                    <div class="container-referral2">
                                        <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_reason_referral" aria-expanded="false" aria-controls="collapse_reason_referral">
                                            <b>REASON FOR REFERRAL</b><i> (required)</i><span class="text-red">*</span>
                                            <span class="pull-right"><i class="fa fa-plus"></i></span>
                                        </button><br><br>
                                    </div>
                                    <div class="collapse" id="collapse_reason_referral" style="width: 100%;">
                                        <small class="text-success"><i>Select reason for referral:</i></small>
                                        <div class="container-referral">
                                            <select name="reason_referral" class="form-control-select select2 reason_referral" required="">
                                                <option value="">Select reason for referral</option>
                                                <option value="-1">Other reason for referral</option>
                                                @foreach($reason_for_referral as $reason_referral)
                                                <option value="{{ $reason_referral->id }}">{{ $reason_referral->reason }}</option>
                                                @endforeach
                                            </select><br><br>
                                            <div id="other_reason_referral_normal"></div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_reason_referral" aria-expanded="false" aria-controls="collapse_reason_referral">
                                        <b>REASON FOR REFERRAL</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_reason_referral" style="width: 100%;">
                                    <i>Select reason for referral:</i>
                                    <div class="container-referral">
                                        <select name="reason_referral1" class="form-control-select select2 reason_referral" require>
                                            <option value="">Select reason for referral</option>
                                            <option value="-1">Other reason for referral</option>
                                            @foreach($reason_for_referral as $reason_referral)
                                                <option value="{{ $reason_referral->id }}">{{ $reason_referral->reason }}</option>
                                            @endforeach
                                        </select><br><br>
                                        <div id="other_reason_referral_div" style="display:none;">
                                            <span>Other Reason for Referral:</span> <br/>
                                            <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7" require></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <hr />
                            <div class="form-footer pull-right">
                                <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Back</button>
                                <button type="submit" id="sbmitBtnNormal" class="btn btn-success btn-flat btn-submit"><i class="fa fa-send"></i> Submit</button>
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

<div class="modal fade" id="icd-modal_revised">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title" style="font-size: 17pt;">Search ICD-10 by keyword</h4>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-lg">
                    <input type="text" id="icd10_keyword_normalrevised" class="form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat" onclick="searchICD10Revised()">Find</button>
                    </span>
                </div><br>
                <div class="icd_body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="othersDiagnosisRevised()"> Other Diagnosis</button>
                <button type="button" class="btn btn-success" onclick="getAllCheckBoxRevised()"><i class="fa fa-save"></i> Save selected check</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $(document).ready(function () {
        // Open the collapse when the form loads
        $("#collapse_illness_history_normal").collapse('show');
        $("#collapse_diagnosis_normal").collapse('show');
        $("#collapse_reason_referral").collapse('show');

        // Ensure button toggle works properly
        $(".btn[data-target='#collapse_illness_history_normal']").on("click", function () {
            $("#collapse_illness_history_normal").collapse("toggle");
        });
        $(".btn[data-target='#collapse_diagnosis_normal']").on("click", function () {
            $("#collapse_diagnosis_normal").collapse("toggle");
        });
        $(".btn[data-target='#collapse_reason_referral']").on("click", function () {
            $("#collapse_reason_referral").collapse("toggle");
        });
    });
</script>

<script>
    // $('#referred_to_select').select2({
    //     dropdownParent: $('#revisednormalFormModal')
    // });

    $(document).ready(function() {
    $('#referred_to_select').select2();
    });
    // // Helper function to toggle collapsible sections
    // function toggleCollapse(sectionId, buttonSelector) {
    //     const collapseSection = document.getElementById(sectionId);
    //     const toggleButton = document.querySelector(buttonSelector);

    //     if (collapseSection && toggleButton) {
    //         // Initially uncollapse the section
    //         collapseSection.classList.add("show");
    //         toggleButton.setAttribute("aria-expanded", "true");

    //         // Add click event to toggle collapse state
    //         toggleButton.addEventListener("click", (event) => {
    //             event.preventDefault(); // Prevent default anchor behavior (if any)
    //             const isExpanded = collapseSection.classList.contains("show");

    //             // Toggle the 'show' class
    //             collapseSection.classList.toggle("show");
    //             toggleButton.setAttribute("aria-expanded", String(!isExpanded));
    //         });
    //     } else {
    //         console.warn(`Section or button not found for: ${sectionId}`);
    //     }
    // }

    // // Uncollapse and allow toggling for required sections
    // toggleCollapse("collapse_reason_referral_normal", "[data-target='#collapse_reason_referral_normal']");
    // toggleCollapse("collapse_illness_history_normal", "[data-target='#collapse_illness_history_normal']");
    // toggleCollapse("collapse_diagnosis_normal", "[data-target='#collapse_diagnosis_normal']");


    
    $('#clear_icd_revised, #clear_notes_revised, #clear_other_diag_revised, #icd_selected_revised').hide();
    $("#sbmitBtnNormal").on('click', function(e) {
        if (!($("#icd").val()) && !($("#other_diag").val())) {
            Lobibox.alert("error", {
                msg: "Select ICD-10 / Other diagnosis!"
            });
            return false;
        }
    });

    function clearICDRevised() {
        $("#icd_selected_revised").html("");
        $("#clear_icd_revised, #icd_selected_revised").hide();
    }

    function clearNotesDiagnosisRevised() {
        $("#add_notes_diagnosis_revised").html("");
        $("#clear_notes_revised").hide();
    }
    function addNotesDiagnosisRevised() {
        $("#add_notes_diagnosis_revised").html(loading);
        $("#clear_notes_revised").show();
        setTimeout(function() {
            $("#add_notes_diagnosis_revised").html('<small class="text-success">ADD NOTES IN DIAGNOSIS:</small> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control add_notes_diagnosis_revised" name="diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
        }, 500);
    }
    function clearOtherDiagnosisRevised() {
        $("#others_diagnosis_revised").html("");
        $("#clear_other_diag_revised").hide();
    }

    function getAllCheckBoxRevised() {
        $('#icd-modal_revised').modal('toggle');
        $('#clear_icd_revised, #icd_selected_revised').show();
        var values = [];
        $('input[name="icd_checkbox[]"]:checked').each(function() {
            values[values.length] = (this.checked ? $(this).parent().parent().siblings("td").eq(1).text() : "");
            var icd_description = $(this).parent().parent().siblings("td").eq(1).text();
            var id = $(this).val();
            if (this.checked) {
                $("#icd_selected_revised").append('=> ' + icd_description + ' ' + '<br><input id="icd" type="hidden" name="icd_ids[]" value="' + id + '">');
                console.log("icd id: " + id);
            }
            clearOtherDiagnosisRevised();
        });

        push_notification_diagnosis_ccmc = values.join(","); //diagnosis for CCMD for their push notification
        console.log(values);
    }

    function othersDiagnosisRevised() {
        $('#icd-modal_revised').modal('hide');
        $("#others_diagnosis_revised").html(loading);
        $("#clear_other_diag_revised").show();
        setTimeout(function() {
            $("#others_diagnosis_revised").html('<small class="text-success">OTHER DIAGNOSIS:</small> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea id="other_diag" class="form-control reason_referral" name="other_diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
        }, 500);
    }

    function othersDiagnosisRevised() {
        $('#icd-modal_revised').modal('hide');
        $("#others_diagnosis_revised").html(loading);
        $("#clear_other_diag_revised").show();
        setTimeout(function() {
            $("#others_diagnosis_revised").html('<small class="text-success">OTHER DIAGNOSIS:</small> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea id="other_diag" class="form-control reason_referral" name="other_diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
        }, 500);
    }

    function searchICD10Revised() {
        $(".icd_body").html(loading);
        var url = "<?php echo asset('icd/search'); ?>";
        var json = {
            "_token": "<?php echo csrf_token(); ?>",
            "icd_keyword": $("#icd10_keyword_normalrevised").val()
        };
        $.post(url, json, function(result) {
            setTimeout(function() {
                $(".icd_body").html(result);
            }, 500);
        });
    }
  

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

    $('#prenatal_add_row_normal').on('click', function() {
        var rowCount = $('#prenatal_table tr').length;
        $('#prenatal_table').append('<tr style="font-size: 11pt">\n' +
            '<td><input class="form-control" type="text" name="pregnancy_history_order[]"></td>\n' +
            '<td><select class="form-control select" name="pregnancy_history_year[]">\n' +
            select_year +
            '</select></td>\n' +
            '<td><input class="form-control" id="gestation_normal" type="text" name="pregnancy_history_gestation[]"></td>\n' +
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
    $('#comor_diab, #comor_asthma, #comor_hyper, #comor_cancer, #comor_others').hide();
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
    $('#allergy_food, #allergy_drug, #allergy_other').hide();
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
    $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid, #heredo_others').hide();
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
    $('#lab_others').hide();
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
    $('#prenatal_mat_illness').hide();
    $('#prenatal_radiowith').on('click', function() {
        if ($(this).is(':checked'))
            $('#prenatal_mat_illness').show();
    });
    $('#prenatal_radiowout').on('click', function() {
        if ($(this).is(':checked'))
            $('#prenatal_mat_illness').hide();
    });

    /* *****POST NATAL (FEEDING HISTORY)****** */
    $('#breastfed, #formula_fed').hide();
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
    $('#immu_dpt, #immu_hepb, #immu_others').hide();
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
    $('#mens_irreg, #menopausal_age').hide();
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
    $('#contraceptive_others').hide();
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
    $('#smoking_sticks').hide();
    $('#smoking_quit_year').hide();
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
    $('#alcohol_bottles').hide();
    $('#alcohol_type').hide();
    $('#alcohol_quit_year').hide();
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
    $('#drugs_text').hide();
    $('#drugs_quit_year').hide();
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
    function resetPupilSize() {
        $('input[name="glasgow_btn"]:checked').each(function() {
            if ($(this).is(':checked'))
                $(this).prop('checked', false);
        })
    }
    var last_motor = last_verbal = last_eye = 0;
    $('input[name="motor_radio"]').on('change', function() {
        var motor = parseInt($('input[name="motor_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score_normal').val(), 10);
        var total = 0;
        if (last_motor == 0)
            total = gcs + motor;
        else
            total = (gcs - last_motor) + motor;

        last_motor = motor;
        $('#gcs_score_normal').val(total);
    });
    $('input[name="verbal_radio"]').on('change', function() {
        var verbal = parseInt($('input[name="verbal_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score_normal').val(), 10);
        var total = 0;
        if (last_verbal == 0)
            total = gcs + verbal;
        else
            total = (gcs - last_verbal) + verbal;

        last_verbal = verbal;
        $('#gcs_score_normal').val(total);
    });
    $('input[name="eye_radio"]').on('change', function() {
        var eye = parseInt($('input[name="eye_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score_normal').val(), 10);
        var total = 0;
        if (last_eye == 0)
            total = gcs + eye;
        else
            total = (gcs - last_eye) + eye;

        last_eye = eye;
        $('#gcs_score_normal').val(total);
    });

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
            $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox, #rs_peri_edema_cbox').prop('checked', false);
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
            $('#rs_muscle_spasm_cbox, #rs_muscle_sizeloss_cbox, #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_fracture_cbox, #rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox, #rs_musclgit_cbox').prop('checked', false);
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
            $(' #rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', false);
        }
    });
    $('#rs_neuro_none_cbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#rs_neuro_all_cbox, #rs_neuro_slurringspeech_cbox, #rs_neuro_disorientation_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox').prop('checked', false);
            $(' #rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_unsteadygait_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', false);
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
            
            if (value == -1) {
                // Show the "Other Reason for Referral" textarea if "-1" is selected
                console.log("VALUE: ", value);
                $('#other_reason_referral_div').show();
            } else {
                // Hide the "Other Reason for Referral" textarea if another option is selected
                console.log("VALUE: ", value);  
                $('#other_reason_referral_div').hide();
            }
        });
    
    

    var normal_pos = 2;
    var normal_count = 0 ;
    
    function readURLNormal(input, pos) {
        // Asset paths for icons
        var word_normal = '{{ asset('resources/img/document_icon.png') }}';
        var pdf_normal = '{{ asset('resources/img/pdf_icon.png') }}'; // Fixed from pdf_normal_icon
        var excel_normal = '{{ asset('resources/img/sheet_icon.png') }}';

        if (input.files) {
            var tmp_pos = pos;

            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];

                if (file && file !== null) {
                    var reader = new FileReader();
                    var fileType = file.type; // Capture file type for the current file

                    // Onload logic for unsupported file types
                    reader.onloadend = (function(file, tmp_pos) {
                        return function (e) {
                            $('#normal_file-upload-image' + tmp_pos).attr('src', e.target.result);
                            $('#normal_image-upload-wrap' + tmp_pos).hide();
                            $('#normal_file-upload-content' + tmp_pos).show();
                            $('#normal_image-title' + tmp_pos).html(file.name);
                        };
                    })(file, tmp_pos);

                    // Set icons based on file type
                    if (fileType === 'application/pdf') {
                        $('#normal_file-upload-image' + tmp_pos).attr('src', pdf_normal);
                    } else if (fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        $('#normal_file-upload-image' + tmp_pos).attr('src', word_normal);
                    } else if (fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                        $('#normal_file-upload-image' + tmp_pos).attr('src', excel_normal);
                    } else {
                        // Read as data URL for other file types
                        reader.readAsDataURL(file);
                    }

                    // Update UI elements
                    $('#normal_image-upload-wrap' + tmp_pos).hide();
                    $('#normal_file-upload-content' + tmp_pos).show();
                    $('#normal_image-title' + tmp_pos).html(file.name);

                    // Increment counters and positions
                    tmp_pos += 1;
                    normal_count += 1;

                    // Add a new file upload row
                    addFileNormal();
                }
            }

            // Show the remove files button
            $('#normal_remove_files').show();
        }
    }

    function addFileNormal() {
        var add_file_icon = '{{ asset('resources/img/add_file.png') }}';

        if((normal_count % 4) == 0) {
            $('.normal_file_attachment').append(
                '<div class="clearfix"></div>'
            );
        }
        $('.normal_file_attachment').append(
            '<div class="col-md-3" id="normal_upload'+normal_pos+'">\n' +
            '   <div class="file-upload">\n' +
            '       <div class="text-center image-upload-wrap" id="normal_image-upload-wrap'+normal_pos+'">\n' +
            '           <input class="file-upload-input" multiple type="file" name="file_upload[]" onchange="readURLNormal(this, '+normal_pos+');" accept="image/png, image/jpeg, image/jpg, image/gif, application/vnd.openxmlformats-officedocument.word_normalprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/pdf_normal"/>\n' +
            '           <img src="'+add_file_icon   +'" style="width: 50%; height: 50%;">\n' +
            '       </div>\n' +
            '       <div class="file-upload-content" id="normal_file-upload-content'+normal_pos+'">\n' +
            '           <img class="file-upload-image" id="normal_file-upload-image'+normal_pos+'"/>\n' +
            '           <div class="image-title-wrap">\n' +
            '               <small class="text-success"><small class="image-title" id="normal_image-title'+normal_pos+'" style="display:block; word_normal-wrap: break-word_normal;">Uploaded File</small></small>\n' +
            '               <button type="button" id="normal_remove_upload'+normal_pos+'" onclick="removeUploadNormal('+normal_pos+')" class="remove-icon-btn"><i class="fa fa-trash"></i></button>\n' +
            '           </div>\n' +
            '       </div>\n' +
            '   </div>\n' +
            '</div>'
        );
        normal_pos+=1;
    }

    function removeFileNormal() {
        $('.normal_file_attachment').html("");
        normal_count = 0;
        normal_pos = 1;
        $('#normal_remove_files').hide();
        addFileNormal();
    }

    function removeUploadNormal(uploadCount){
        $('#normal_upload' + uploadCount).remove();
        upload_count -= 1;
        if(normal_pos > uploadCount){
            normal_pos -= 1;
        }
        if(uploadCount === 0){
            $('#remove_files_btn');
        }
    }

    $(document).ready(function() {
        for (var i = 0; i < normal_count; i++) {
            $('#normal_image-upload-wrap' + i).bind('dragover', function () {
                $('#normal_image-upload-wrap' + i).addClass('image-dropping');
            });
            $('#normal_image-upload-wrap' + i).bind('dragleave', function () {
                $('#normal_image-upload-wrap' + i).removeClass('image-dropping');
            });
        }
        $('#normal_remove_files').hide();
    });
    </script>
