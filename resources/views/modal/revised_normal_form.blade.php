@extends('layouts.app')

<?php
$user = Session::get('auth');
$facilities = \App\Facility::select('id','name')
    ->where('id','!=',$user->facility_id)
    ->where('status',1)
    ->where('referral_used','yes')
    ->orderBy('name','asc')->get();
$myfacility = \App\Facility::find($user->facility_id);
$facility_address = \App\Http\Controllers\LocationCtrl::facilityAddress($myfacility->id);
$inventory = \App\Inventory::where("facility_id",$myfacility->id)->get();
$reason_for_referral = \App\ReasonForReferral::get();
?>

<style>
    .file-upload {
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

<div class="modal fade" role="dialog" id="normalFormModal" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ url('doctor/patient') }}" method="POST" class="form-submit normal_form">
                <div class="jim-content">
                    @include('include.header_form')
                    <div class="form-group-sm form-inline">
                        {{ csrf_field() }}
                        <input type="hidden" name="patient_id" class="patient_id" value="" />
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
                                &nbsp;<span >{{ $facility_address['address'] }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success">Name of referring MD/HCW</small><br>
                                &nbsp;<span >Dr. {{ $user->fname }} {{ $user->mname }} {{ $user->lname }}</span>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-success">Date/Time Referred (ReCo)</small><br>
                                <span >{{ date('l F d, Y h:i A') }}</span>
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
                                <select name="referred_department" class="form-control-select select_department select_department_normal" style="width: 100%;" required>
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
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_illness_history" aria-expanded="false" aria-controls="collapse_illness_history">
                                        <b>HISTORY OF PRESENT ILLNESS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_illness_history" style="width: 100%">
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
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_diagnosis" aria-expanded="false" aria-controls="collapse_diagnosis">
                                        <b>DIAGNOSIS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse " id="collapse_diagnosis" style="width: 100%">
                                    <b>ICD-10 Diagnosis</b><br>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a data-toggle="modal" data-target="#icd-modal" type="button" class="btn btn-sm btn-success" onclick="searchICD10()">
                                                    <i class="fa fa-medkit"></i> Add ICD-10
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" onclick="addNotesDiagnosis()"><i class="fa fa-plus"></i> Add notes in diagnosis</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" id="clear_icd" class="btn btn-sm btn-danger" onclick="clearICD()"> Clear ICD-10</button>
                                                <div><span class="text-green" id="icd_selected"></span></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" id="clear_notes" class="btn btn-sm btn-info" onclick="clearNotesDiagnosis()"> Clear notes diagnosis</button>
                                                <div id="add_notes_diagnosis"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" id="clear_other_diag" class="btn btn-sm btn-warning" onclick="clearOtherDiagnosis()"> Clear other diagnosis</button>
                                                <div id="others_diagnosis"></div>
                                            </div>
                                        </div>
                                    </div><br><br>
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
                                    <b>COMORBIDITIES</b><br>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> None</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                Hypertension <span id="comor_hyper">x <input type="number" min="0" style="width: 20%"> years
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                Diabetes Mellitus <span id="comor_diab"> x <input type="number" min="0" style="width: 20%"> years</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                Bronchial Asthma <span id="comor_asthma"> x <input type="number" min="0" style="width: 20%"> years</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_copd_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> COPD</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_dyslip_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Dyslipidemia</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Thyroid Disease</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Cancer <i>(specify)</i>:</span>
                                                <textarea class="form-control" name="comor_cancer" id="comor_cancer" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Other(s): </span>
                                                <textarea class="form-control" name="comor_others" id="comor_others" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>ALLERGIES</b><br>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-1">
                                                <input class="form-check-input" id="allergy_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> None</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="allergy_food_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Food(s): </span>
                                                <textarea class="form-control" id="allergy_food" name="allergy_food" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="allergy_drug_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Drug(s): </span>
                                                <textarea class="form-control" id="allergy_drug" name="allergy_drug" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="allergy_other_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Other(s): </span>
                                                <textarea class="form-control" id="allergy_other" name="allergy_other" style="resize: none;width: 100%;" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>HEREDOFAMILIAL DISEASES</b> <i>(Specify which side of the family)</i>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> None</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Hypertension: </span>
                                                <input type="text" id="heredo_hyper" name="heredo_hypertension_side">
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Diabetes Mellitus: </span>
                                                <input type="text" id="heredo_diab" name="heredo_diabetes_side">
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Bronchial Asthma: </span>
                                                <input type="text" id="heredo_asthma" name="heredo_asthma_side">
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Cancer: </span>
                                                <input type="text" id="heredo_cancer" name="heredo_cancer_side">
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_kidney_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Kidney: </span>
                                                <input type="text" id="heredo_kidney" name="heredo_kidney_side">
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Thyroid Disease: </span>
                                                <input type="text" id="heredo_thyroid" name="heredo_thyroid_side">
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="heredo_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Other(s): </span>
                                                <input type="text" id="heredo_others" name="heredo_others">
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PREVIOUS HOSPITALIZATION(S) and OPERATION(S)</b><br>
                                    <textarea class="form-control" name="previous_hospitalization" style="resize: none;width: 100%;" rows="3"></textarea><br><br>
                                </div>
                            </div>
                        </div>

                        {{--@if(age <= 18) --}} {{--TODO: COMPARE AGE IF <=18--}}
                        <div class="row" id="pedia_show">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_pedia_history" aria-expanded="false" aria-controls="collapse_pedia_history">
                                        <div class="web-view"><b>PEDIATRIC HISTORY</b> <i> (as applicable)</i></div>
                                        <div class="mobile-view"><b>PEDIATRIC HISTORY</b><br> <i> (as applicable)</i></div>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_pedia_history" style="width: 100%;">
                                    <b>PRENATAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>A <input type="number" style="width: 8%" min="0">
                                                     year old G <input type="number" style="width: 8%" min="0"> P <input type="number" style="width: 8%" min="0"> mother
                                                    <input class="referral-radio-btn" type="radio" id="prenatal_radiowith" name="prenatal_radio" value="with"> with
                                                    <input class="referral-radio-btn" type="radio" id="prenatal_radiowout" name="prenatal_radio" value="without"> without
                                                    MATERNAL ILLNESS, if Yes (specify)
                                                    <textarea class="form-control" id="prenatal_mat_illness" name="prenatal_maternal_illness" style="resize: none;width: 50%;" rows="2"></textarea>
                                                </span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>NATAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>Born at
                                                    <select class="form-control-select">
                                                        <option value="home">Home</option>
                                                        <option value="clinic">Clinic</option>
                                                        <option value="hospital">Hospital</option>
                                                    </select>
                                                    <input type="text" id="hospital_name" style="width: 30%;" placeholder="Specify where">
                                                    by
                                                    <select class="form-control-select">
                                                        <option value="md">MD</option>
                                                        <option value="midwife">Midwife</option>
                                                        <option value="hilot">"Hilot"</option>
                                                    </select>
                                                    via
                                                    <select class="form-control-select">
                                                        <option value="nsd">NSD</option>
                                                        <option value="cs">CS</option>
                                                    </select>
                                                    (indication)
                                                    <input type="text" id="cs_indication" style="width: 20%;">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>
                                                    <select class="form-control-select">
                                                        <option value="preterm">Preterm</option>
                                                        <option value="fullterm">Full Term</option>
                                                        <option value="postterm">Post Term</option>
                                                    </select>
                                                    , weighing <input type="number" style="width: 8%" min="0" step="0.01"> kg,
                                                    BR <input type="text" style="width: 20%">, with Good Cry
                                                    <select class="form-control-select">
                                                        <option value="1">Yes</option>
                                                        <option value="0">No</option>
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
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="postnatal_bfeed"> Breastfed
                                                    <span id="breastfed">
                                                        x <input type="number" style="width: 7%;" min="0"> mos.
                                                    </span>
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" id="postnatal_ffeed" type="checkbox"> Formula Fed,
                                                    <span id="formula_fed">
                                                        (specify) <input type="text" style="width: 15%">
                                                    </span>
                                                </span>
                                                started semi solid foods at
                                                <input type="number" style="width: 10%" min="0"> mos
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Immunization History</i><br>&emsp;
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> BCG
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_dpt_cbox"> DPT/OPV
                                                <span id="immu_dpt">
                                                    x <input type="number" style="width: 7%;" min="0"> doses
                                                </span>
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_hepb_cbox"> Hep B
                                                <span id="immu_hepb">
                                                    x <input type="number" style="width: 7%;" min="0"> doses
                                                </span>
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> Measles
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> MMR
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_others_cbox"> Other(s)
                                                <span id="immu_others">
                                                    <input type="text" style="width: 20%;">
                                                </span>
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Developmental Milestones</i><br>&emsp;
                                                <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_under">
                                                <label for="dev_miles_under">
                                                    Under developed
                                                </label>
                                                <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_par">
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
                                    <b>MENARCHE </b> @ <input type="number" min="9" style="width: 10%;"> years old &emsp;&emsp;&emsp;&emsp;
                                    <b>MENOPAUSE: </b>&emsp;
                                    <input type="radio" class="referral-radio-btn" name="menopausal" id="menopausal">
                                    <label for="menopausal">Yes</label>
                                    <input type="radio" class="referral-radio-btn" name="menopausal" id="non_menopausal">
                                    <label for="non_menopausal">No</label>
                                    <span id="menopausal_age">(age) <input type="number" style="width: 10%;" min="9"></span><br><br>

                                    <b>MENSTRUAL CYCLE</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_reg_radio">
                                                <label for="mens_reg_radio">Regular</label>
                                                <input type="radio" class="referral-radio-btn" name="mens_cycle" id="mens_irreg_radio">
                                                <label for="mens_irreg_radio">Irregular</label>
                                                <span id="mens_irreg">x <input type="number" style="width: 15%;" min="0"> mos</span>
                                            </div>
                                            <div class="col-md-6">
                                                <b>Dysmenorrhea:</b> &emsp;
                                                <input type="radio" class="referral-radio-btn" name="dysme" id="dysme_yes">
                                                <label for="dysme_yes">Yes</label>
                                                <input type="radio"  class="referral-radio-btn" name="dysme" id="dysme_no">
                                                <label for="dysme_no">No</label><br>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <b>Duration:</b> <input type="number" style="width:15%;" min="0"> days &emsp;
                                                <b>Pads/day:</b> <input type="number" style="width:15%;" min="0">
                                            </div>
                                            <div class="col-md-6">
                                                <b>Medication: </b> <input type="text" style="width:70%;">
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>CONTRACEPTIVE HISTORY</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> None
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_pills_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> Pills
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_iud_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> IUD
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_rhythm_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> Rhythm
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="contraceptive_condom_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> Condom
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input class="form-check-input" id="contraceptive_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> Other(s)
                                                <textarea class="form-control" id="contraceptive_others" name="contraceptive_other" style="resize: none;width: 50%;" rows="2"></textarea><br>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PARITY</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <b>G</b> <input type="number" min="0" style="width:8%;">
                                                <b>P</b> <input type="number" min="0" style="width:8%;">&emsp;
                                                <b>(FT </b> <input type="text" style="width:8%;">
                                                <b> PT </b> <input type="text" style="width:8%;">
                                                <b> A </b> <input type="text" style="width:8%;">
                                                <b> L </b> <input type="text" style="width:8%;"><b>)</b>
                                            </div>
                                        </div>
                                    </div><br>

                                    <div class="container-referral">
                                        <b>LNMP</b>
                                        <input type="text" style="width:15%;">&emsp;&emsp;&emsp;
                                        <b>EDC</b><i>(if pregnant)</i>
                                        <input type="text" style="width:15%;">
                                    </div><br>

                                    <b>AOG</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <b>by LNMP </b> <input type="number" min="0" style="width:25%;"> <b>wks</b>
                                            </div>
                                            <div class="col-md-4">
                                                <b>by EUTZ </b> <input type="number" min="0" style="width:25%;"> <b>wks</b>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PRENATAL HISTORY</b><br>
                                    <textarea class="form-control" name="prenatal_history" style="resize: none;width: 100%;" rows="4"></textarea><br><br>
                                    <div style="overflow-x: auto">
                                        <table class="table-sm table-responsive table-bordered" id="prenatal_table">
                                            <thead>
                                            <tr style="font-size: 11pt;">
                                                <th width="50%" class="text-center">Pregnancy Order</th>
                                                <th class="text-center">Year</th>
                                                <th class="text-center">Gestation Completed</th>
                                                <th class="text-center">Pregnancy Outcome</th>
                                                <th class="text-center">Place of Birth</th>
                                                <th class="text-center">Sex</th>
                                                <th class="text-center">Birth Weight</th>
                                                <th class="text-center">Present Status</th>
                                                <th class="text-center">Complication(s)</th>
                                            </tr>
                                            </thead>
                                            <tr style="font-size: 10pt">
                                                <td><input class="form-control" type="text"></td>
                                                <td><input class="form-control" type="number" min="1900"></td>
                                                <td><input class="form-control" id="gestation" type="text"></td>
                                                <td><input class="form-control" type="text"></td>
                                                <td><input class="form-control" type="text"></td>
                                                <td width="150%">
                                                    <select class="select" name="prenatal_history_sex">
                                                        <option value="">Choose...</option>
                                                        <option value="M">Male</option>
                                                        <option value="F">Female</option>
                                                    </select>
                                                </td>
                                                <td><input class="form-control" type="number" min="0" step="0.01"></td>
                                                <td><input class="form-control" type="text"></td>
                                                <td><input class="form-control" type="text"></td>
                                            </tr>
                                        </table><br>
                                        <button class="btn-sm btn-success" id="prenatal_add_row" type="button">
                                            <i class="fa fa-plus"> Add Row</i>
                                        </button><br><br>
                                        {{--<div class="row" style="text-align: center">
                                            <div class="col-md-1"><span><b>Pregnancy Order</b></span></div>
                                            <div class="col-md-1"><span><b>Year</b></span></div>
                                            <div class="col-md-1"><span><b>Gestation Completed</b></span></div>
                                            <div class="col-md-2"><span><b>Pregnancy Outcome</b></span></div>
                                            <div class="col-md-2"><span><b>Place of Birth</b></span></div>
                                            <div class="col-md-1"><span><b>Sex</b></span></div>
                                            <div class="col-md-1"><span><b>Birth Weight</b></span></div>
                                            <div class="col-md-1"><span><b>Present Status</b></span></div>
                                            <div class="col-md-2"><span><b>Complication(s)</b></span></div>
                                        </div>--}}
                                        {{--<div class="row">
                                            <div class="col-md-1"><input type="text"></div>
                                            <div class="col-md-1"><input type="text"></div>
                                            <div class="col-md-1"><input type="text"></div>
                                            <div class="col-md-2"><input type="text"></div>
                                            <div class="col-md-2"><input type="text"></div>
                                            <div class="col-md-1"><input type="text"></div>
                                            <div class="col-md-1"><input type="text"></div>
                                            <div class="col-md-1"><input type="text"></div>
                                            <div class="col-md-2"><input type="text" style="width:100%;"></div>
                                        </div>--}}
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
                                    <b>SMOKING</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_yes">
                                                <label for="smoke_yes">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_no">
                                                <label for="smoke_no">No</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_quit">
                                                <label for="smoke_quit">Quit</label>
                                            </div>
                                            <div class="col-md-6">
                                                <span>Other Remarks: <input type="text" style="width:100%;"></span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>ALCOHOL DRINKING</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_yes_radio">
                                                <label for="alcohol_yes_radio">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_no_radio">
                                                <label for="alcohol_no_radio">No</label>
                                            </div>
                                            <div class="col-md-5">
                                                <span id="alcohol_bottles">Bottles per day: <input type="number" min="0"></span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>ILLICIT DRUGS</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="radio" name="illicit_drugs" id="drugs_yes_radio" class="referral-radio-btn">
                                                <label for="drugs_yes_radio">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" name="illicit_drugs" id="drugs_no_radio" class="referral-radio-btn">
                                                <label for="drugs_no_radio">No</label>
                                            </div>
                                            <div class="col-md-8" id="drugs_text">
                                                If yes, specify:
                                                <textarea class="form-control" style="resize: none;width:50%;" name="illicit_drugs_token"></textarea>
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
                                    <i>Specify number of doses given and time of last dose given.</i>
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
                                    <i> Attach all applicable labs in one file.</i>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> UA</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> CBC</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> X-RAY</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="file-upload">
                                                    <div class="image-upload-wrap">
                                                        <input class="file-upload-input" type='file' name="file_upload" onchange="readURL(this);" accept="image/png, image/jpeg, image/jpg, image/gif, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/pdf"/>
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
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_review_system" aria-expanded="false" aria-controls="collapse_review_system">
                                        <b>REVIEW OF SYSTEMS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_review_system" style="width: 100%;">
                                    <b>SKIN</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Rashes</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Itching</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Change in hair or nails</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>HEAD</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Headaches</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Head injury</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>EYES</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Glasses or Contacts</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Change in vision</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Eye pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Double Vision</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Flashing lights</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Glaucoma/Cataracts</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Last eye exam</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>EARS</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Change in hearing</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Ear pain</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Ear discharge</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Ringing</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Dizziness</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>NOSE/SINUSES</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Nose bleeds</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Nasal stuffiness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Frequent Colds</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>MOUTH/THROAT</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Bleeding gums</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Sore tongue</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Sore throat</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Hoarseness</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>NECK</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Lumps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Swollen glands</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Goiter</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Stiffness</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>BREAST</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Lumps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Nipple discharge</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> BSE</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>RESPIRATORY/CARDIAC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Shortness of breath</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Cough</span>
                                            </div>
                                            <div class="col-sm-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Production of phlegm, color</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Wheezing</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Coughing up blood</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Chest pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Fever</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Night sweats</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Swelling in hands/feet</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Blue fingers/toes</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> High blood pressure</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Skipping heart beats</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Heart murmur</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> HX of heart medication</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Bronchitis/emphysema</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Rheumatic heart disease</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>GASTROINTESTINAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Change of appetite or weight</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Problems swallowing</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Nausea</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Heartburn</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Vomiting</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Vomiting Blood</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Constipation</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Diarrhea</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Change in bowel habits</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Abdominal pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Excessive belching</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Excessive flatus</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Yellow color of skin (Jaundice/Hepatitis)</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Food intolerance</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Rectal bleeding/Hemorrhoids</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>URINARY</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Difficulty in urination</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Pain or burning on urination</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Frequent urination at night</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Urgent need to urinate</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Incontinence of urine</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Dribbling</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Decreased urine stream</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Blood in urine</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> UTI/stones/prostate infection</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PERIPHERAL VASCULAR</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Leg cramps</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Varicose veins</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Clots in veins</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>MUSCULOSKELETAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Swelling</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Stiffness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Decreased joint motion</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Broken bone</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Serious sprains</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Arthritis</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Gout</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>NEUROLOGIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Headaches</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Seizures</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Loss of Consciousness/Fainting</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Paralysis</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Weakness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Loss of muscle size</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Muscle Spasm</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Tremor</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Involuntary movement</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Incoordination</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Numbness</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Feeling of "pins and needles/tingles"</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>HEMATOLOGIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Anemia</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Easy bruising/bleeding</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Past Transfusions</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>ENDOCRINE</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Abnormal growth</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Increased appetite</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Increased thirst</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Increased urine production</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Thyroid trouble</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Heat/cold intolerance</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Excessive sweating</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Diabetes</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PSYCHIATRIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Tension/Anxiety</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Depression/suicide ideation</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Memory problems</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Unusual problems</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Sleep problems</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                <span> Past treatment with psychiatrist</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
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
                                    <b>Diet</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_none">
                                                <label for="diet_none"> None </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_oral">
                                                <label for="diet_oral"> Oral </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tube">
                                                <label for="diet_tube"> Tube </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tpn">
                                                <label for="diet_tpn"> TPN </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio", id="diet_npo">
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
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_vital_signs" aria-expanded="false" aria-controls="collapse_vital_signs">
                                        <b>LATEST VITAL SIGNS</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_vital_signs" style="width: 100%;">
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                Temperature: <input type="number" step="0.01" style="width:30%;" min="0"> &#176;C
                                            </div>
                                            <div class="col-md-4">
                                                Pulse Rate/Heart Rate: <input type="number" step="0.01" style="width:30%;" min="0"> bpm
                                            </div>
                                            <div class="col-md-4">
                                                Respiratory Rate: <input type="number" step="0.01" style="width:30%;" min="0"> cpm
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                Blood Pressure: <input type="number" step="0.01" style="width:30%;" min="0"> mmHg
                                            </div>
                                            <div class="col-md-4">
                                                O2 Saturation <input type="number" step="0.01" style="width:30%;" min="0"> %
                                            </div>
                                        </div><br>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="container-referral2">
                                    <button class="btn btn-m collapsed" type="button" style="width: 100%;" data-toggle="collapse" data-target="#collapse_glasgow" aria-expanded="false" aria-controls="collapse_glasgow">
                                        <b>GLASGOW COMA SCALE</b>
                                        <span class="pull-right"><i class="fa fa-plus"></i></span>
                                    </button><br><br>
                                </div>
                                <div class="collapse" id="collapse_glasgow" style="width: 100%;">
                                    <b>Pupil Size Chart</b> &emsp;
                                    <input type="button" class="btn-m btn-warning btn-rounded" onclick="resetPupilSize()" value="Reset">
                                    <div class="container-referral">
                                        <div class="row web-view">
                                            <div class="col-lg-1"></div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_1">
                                                    <b>1</b><br>
                                                    <span class="glasgow-dot" style="height: 6px; width: 6px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow_1">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_2">
                                                    <b>2</b><br>
                                                    <span class="glasgow-dot" style="height: 10px; width: 10px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow_2">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_3">
                                                    <b>3</b><br>
                                                    <span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow_3">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_4">
                                                    <b>4</b><br>
                                                    <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow_4">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_5">
                                                    <b>5</b><br>
                                                    <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow_5">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_6">
                                                    <b>6</b><br>
                                                    <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow_6">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow-7">
                                                    <b>7</b><br>
                                                    <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow-7">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_8">
                                                    <b>8</b><br>
                                                    <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow_8">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_9">
                                                    <b>9</b><br>
                                                    <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_btn" type="radio" id="glasgow_9">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <b>10</b><br>
                                                <label for="glasgow_10">
                                                    <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                </label>
                                                <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_10">
                                            </div>
                                        </div>
                                        <div class="mobile-view">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <b>1</b>
                                                    <label for="glasgow_1">
                                                    <span class="glasgow-dot" style="height: 6px; width: 6px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_1">&emsp;&emsp;

                                                    <b>2</b>
                                                    <label for="glasgow_2">
                                                        <span class="glasgow-dot" style="height: 10px; width: 10px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_2">&emsp;&emsp;

                                                    <b>3</b>
                                                    <label for="glasgow_3">
                                                        <br><span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_3">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <b>4</b>
                                                    <label for="glasgow_4">
                                                        <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_4">&emsp;&emsp;

                                                    <b>5</b>
                                                    <label for="glasgow_5">
                                                        <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_5">&emsp;&emsp;

                                                    <b>6</b>
                                                    <label for="glasgow_6">
                                                        <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_6">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-1">
                                                    <b>7</b>
                                                    <label for="glasgow-7">
                                                        <span class="glasgow-dot" style="height: 28px; width: 28px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow-7">&emsp;&emsp;

                                                    <b>8</b>
                                                    <label for="glasgow_8">
                                                        <span class="glasgow-dot" style="height: 32px; width: 32px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_8">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1"></div>
                                                <div class="col-md-1">
                                                    <b>9</b>
                                                    <label for="glasgow_9">
                                                        <span class="glasgow-dot" style="height: 36px; width: 36px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_9">&emsp;&emsp;

                                                    <b>10</b>
                                                    <label for="glasgow_10">
                                                        <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_btn" type="radio" id="glasgow_10">
                                                </div>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        {{--<div class="col-md-6">
                                            <b>Motor Response</b>
                                            <div class="container glasgow-table">
                                                <div class="row" style="background-color:#ddd;">
                                                    <div class="col-md-4" style="text-align: center;">
                                                        <b>ADULT AND CHILD</b>
                                                    </div>
                                                    <div class="col-md-4" style="text-align: center;">
                                                        <b>INFANT (2 MONTHS)</b>
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center;">
                                                        <b>POINTS</b>
                                                    </div>
                                                    <div class="col-md-1" style="text-align: center;">
                                                        <b>OPTION</b>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        Obeys Command
                                                    </div>
                                                    <div class="col-md-4">
                                                        Spontaneous Movement
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center;">
                                                        6
                                                    </div>
                                                    <div class="col-md-1" style="text-align: center;">
                                                        <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                    </div>
                                                </div>
                                                <div class="row" style="background-color: #e8e4e473;">
                                                    <div class="col-md-4">
                                                        Localizes Pain
                                                    </div>
                                                    <div class="col-md-4">
                                                        Withdraws (Touch)
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center;">
                                                        5
                                                    </div>
                                                    <div class="col-md-1" style="text-align: center;">
                                                        <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        Withdraws
                                                    </div>
                                                    <div class="col-md-4">
                                                        Withdraws (Pain)
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center;">
                                                        4
                                                    </div>
                                                    <div class="col-md-1" style="text-align: center;">
                                                        <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                    </div>
                                                </div>
                                                <div class="row" style="background-color: #e8e4e473;">
                                                    <div class="col-md-4">
                                                        Flexion to Pain
                                                    </div>
                                                    <div class="col-md-4">
                                                        Flexion to Pain
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center;">
                                                        3
                                                    </div>
                                                    <div class="col-md-1" style="text-align: center;">
                                                        <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        Extension to Pain
                                                    </div>
                                                    <div class="col-md-4">
                                                        Extension to Pain
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center;">
                                                        2
                                                    </div>
                                                    <div class="col-md-1" style="text-align: center;">
                                                        <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                    </div>
                                                </div>
                                                <div class="row" style="background-color: #e8e4e473;">
                                                    <div class="col-md-4">
                                                        None
                                                    </div>
                                                    <div class="col-md-4">
                                                        None
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center;">
                                                        1
                                                    </div>
                                                    <div class="col-md-1" style="text-align: center;">
                                                        <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>--}}
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
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="motor_radio[2]" name="motor_radio" value=5></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Withdraws</td>
                                                        <td>Withdraws (Pain)</td>
                                                        <td style="text-align: center">4</td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="motor_radio[3]" name="motor_radio" value=4></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Flexion to Pain</td>
                                                        <td>Flexion to Pain</td>
                                                        <td style="text-align: center">3</td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="motor_radio[]" name="motor_radio" value=3></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Extension to Pain</td>
                                                        <td>Extension to Pain</td>
                                                        <td style="text-align: center">2</td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="motor_radio[]" name="motor_radio" value=2></td>
                                                    </tr>
                                                    <tr>
                                                        <td>None</td>
                                                        <td>None</td>
                                                        <td style="text-align: center">1</td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="motor_radio[]" name="motor_radio" value=1></td>
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
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="verbal_radio" name="verbal_radio" value=5></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Confused</td>
                                                        <td>Irritable Cry</td>
                                                        <td style="text-align: center">4</td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="verbal_radio" name="verbal_radio" value=4></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Inappropriate</td>
                                                        <td>Cries to Pain</td>
                                                        <td style="text-align: center">3</td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="verbal_radio" name="verbal_radio" value=3></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Incomprehensible</td>
                                                        <td>Moans to Pain</td>
                                                        <td style="text-align: center">2</td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="verbal_radio" name="verbal_radio" value=2></td>
                                                    </tr>
                                                    <tr>
                                                        <td>None</td>
                                                        <td>None</td>
                                                        <td style="text-align: center">1</td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="verbal_radio" name="verbal_radio" value=1></td>
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
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio" name="eye_radio" value=4></td>
                                                    </tr>
                                                    <tr>
                                                        <td>To Command</td>
                                                        <td>To Voice</td>
                                                        <td style="text-align: center">3 </td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio" name="eye_radio" value=3></td>
                                                    </tr>
                                                    <tr>
                                                        <td>To Pain</td>
                                                        <td>To Pain</td>
                                                        <td style="text-align: center">2 </td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio" name="eye_radio" value=2></td>
                                                    </tr>
                                                    <tr>
                                                        <td>None</td>
                                                        <td>None</td>
                                                        <td style="text-align: center">1 </td>
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" id="eye_radio" name="eye_radio" value=1></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <br><br><input type="button" class="btn-m btn-info btn-rounded" onclick="calculateGCS()" value="Calculate GCS Score:">
                                            <input class="number" name="gcs_score" id="gcs_score" style="text-align: center"min="0" value="0" readonly>
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                        </div>

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
                                        <select name="reason_referral1" class="form-control-select select2 reason_referral" style="width: 100%" required="">
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
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@section('js')
<script>
    $("#normalFormModal").modal("show");
//    $('#pedia_show').hide();
//    $('#menarche_show').hide();
//
//    var pt_age = $('.patient_age').val();
//    var pt_sex = $('.patient_sex').val();
//    console.log("pt age: " + pt_age + "   patient_sex: " + pt_sex);
//    if(pt_age <= 18)
////        $('#pedia_show').show();
//    else if(pt_age >= 9 && pt_sex == 'Female')
//        $('#menarche_show').show();

    $(".collapse").on('show.bs.collapse', function(){
        $(this).prev(".container-referral2").find(".fa").removeClass("fa-plus").addClass("fa-minus");
    }).on('hide.bs.collapse', function(){
        $(this).prev(".container-referral2").find(".fa").removeClass("fa-minus").addClass("fa-plus");
    });

    $('#prenatal_add_row').on('click', function() {
        $('#prenatal_table').append('<tr style="font-size: 10pt">\n' +
            '<td><input class="form-control" type="text" style="width:100%;"></td>\n' +
            '<td><input class="form-control" type="number" min="1900"></td>\n' +
            '<td><input class="form-control" id="gestation" type="text" style="width:100%;"></td>\n' +
            '<td><input class="form-control" type="text"></td>\n' +
            '<td><input class="form-control" type="text"></td>\n' +
            '<td width="150%">\n' +
            '        <select class="select" name="prenatal_history_sex">\n' +
            '        <option value="">Choose...</option>\n' +
            '        <option value="M">Male</option>\n' +
            '        <option value="F">Female</option>\n' +
            '        </select>\n' +
            '</td>\n' +
            '<td><input class="form-control" type="number" min="0" step="0.01"></td>\n' +
            '<td><input class="form-control" type="text"></td>\n' +
            '<td><input class="form-control" type="text"></td>\n' +
            '</tr>');
    });

    /* *****COMORBIDITY***** */
    $('#comor_diab, #comor_asthma, #comor_hyper, #comor_cancer, #comor_others').hide();
    $('#comor_hyper_cbox').on('click', function() {
       if($(this).is(':checked')){
           $('#comor_hyper').show();
           $('#comor_none_cbox').prop('checked', false);
       }else
           $('#comor_hyper').hide();
    });
    $('#comor_diab_cbox').on('click', function() {
        if($(this).is(':checked')){
            $('#comor_diab').show();
            $('#comor_none_cbox').prop('checked', false);
        }else
            $('#comor_diab').hide();
    });
    $('#comor_asthma_cbox').on('click', function() {
        if($(this).is(':checked')){
           $('#comor_asthma').show();
           $('#comor_none_cbox').prop('checked', false);
       }
       else
           $('#comor_asthma').hide();
    });
    $('#comor_cancer_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#comor_cancer').show();
            $('#comor_none_cbox').prop('checked', false);
        }
        else
            $('#comor_cancer').hide();
    });
    $('#comor_others_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#comor_others').show();
            $('#comor_none_cbox').prop('checked', false);
        }
        else
            $('#comor_others').hide();
    });
    $('#comor_copd_cbox,#comor_dyslip_cbox,#comor_thyroid_cbox').on('click', function() {
        $('#comor_none_cbox').prop('checked', false);
    });
    $('#comor_none_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#comor_hyper_cbox, #comor_diab_cbox, #comor_asthma_cbox, #comor_copd_cbox').prop('checked', false);
            $('#comor_dyslip_cbox, #comor_thyroid_cbox, #comor_cancer_cbox, #comor_others_cbox').prop('checked', false);
            $('#comor_asthma, #comor_diab, #comor_hyper, #comor_cancer, #comor_others').hide();
        }
    });

    /* *****ALLERGY***** */
    $('#allergy_food, #allergy_drug, #allergy_other').hide();
    $('#allergy_food_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#allergy_food').show();
            $('#allergy_none_cbox').prop('checked', false);
        }
        else
            $('#allergy_food').hide();
    });
    $('#allergy_drug_cbox').on('click', function() {
       if($(this).is(':checked')) {
           $('#allergy_drug').show();
           $('#allergy_none_cbox').prop('checked', false);
       }
       else
           $('#allergy_drug').hide();
    });
    $('#allergy_other_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#allergy_other').show();
            $('#allergy_none_cbox').prop('checked', false);
        }
        else
            $('#allergy_other').hide();
    });
    $('#allergy_none_cbox').on('click', function() {
       if($(this).is(':checked')) {
           $('#allergy_food_cbox, #allergy_drug_cbox, #allergy_other_cbox').prop('checked', false);
           $('#allergy_food, #allergy_drug, #allergy_other').hide();
       }
    });

    /* *****HEREDOFAMILIAL***** */
    $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid, #heredo_others').hide();
    $('#heredo_hyper_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#heredo_hyper').show();
            $('#heredo_none_cbox').prop('checked', false);
        }
        else
            $('#heredo_hyper').hide();
    });
    $('#heredo_diab_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#heredo_diab').show();
            $('#heredo_none_cbox').prop('checked', false);
        }
        else
            $('#heredo_diab').hide();
    });
    $('#heredo_asthma_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#heredo_asthma').show();
            $('#heredo_none_cbox').prop('checked', false);

        }
        else
            $('#heredo_asthma').hide();
    });
    $('#heredo_cancer_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#heredo_cancer').show();
            $('#heredo_none_cbox').prop('checked', false);
        }
        else
            $('#heredo_cancer').hide();
    });
    $('#heredo_kidney_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#heredo_kidney').show();
            $('#heredo_none_cbox').prop('checked', false);
        }
        else
            $('#heredo_kidney').hide();
    });
    $('#heredo_thyroid_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#heredo_thyroid').show();
            $('#heredo_none_cbox').prop('checked', false);
        }
        else
            $('#heredo_thyroid').hide();
    });
    $('#heredo_others_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#heredo_others').show();
            $('#heredo_none_cbox').prop('checked', false);
        }
        else
            $('#heredo_others').hide();
    });
    $('#heredo_none_cbox').on('click', function() {
        if($(this).is(':checked')) {
           $('#heredo_hyper_cbox, #heredo_diab_cbox, #heredo_asthma_cbox, #heredo_cancer_cbox').prop('checked', false);
           $('#heredo_kidney_cbox, #heredo_thyroid_cbox, #heredo_others_cbox').prop('checked', false);
           $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid, #heredo_others').hide();
       }
    });

    /* *****PRENATAL***** */
    $('#prenatal_mat_illness').hide();
    $('#prenatal_radiowith').on('click', function() {
        if($(this).is(':checked'))
            $('#prenatal_mat_illness').show();
    });
    $('#prenatal_radiowout').on('click', function() {
       if($(this).is(':checked'))
           $('#prenatal_mat_illness').hide();
    });

    /* *****POST NATAL (FEEDING HISTORY)****** */
    $('#breastfed, #formula_fed').hide();
    $('#postnatal_bfeed').on('click', function() {
        if($(this).is(':checked'))
            $('#breastfed').show();
        else
            $('#breastfed').hide();
    });
    $('#postnatal_ffeed').on('click', function() {
        if($(this).is(':checked'))
            $('#formula_fed').show();
        else
            $('#formula_fed').hide();
    });

    /* *****POST NATAL (IMMUNIZATION HISTORY)****** */
    $('#immu_dpt, #immu_hepb, #immu_others').hide();
    $('#immu_dpt_cbox').on('click', function() {
        if($(this).is(':checked'))
            $('#immu_dpt').show();
        else
            $('#immu_dpt').hide();
    });
    $('#immu_hepb_cbox').on('click', function() {
       if($(this).is(':checked'))
           $('#immu_hepb').show();
       else
           $('#immu_hepb').hide();
    });
    $('#immu_others_cbox').on('click', function() {
       if($(this).is(':checked'))
           $('#immu_others').show();
       else
           $('#immu_others').hide();
    });

    /* *****MENSTRUAL/MENOPAUSAL***** */
    $('#mens_irreg, #menopausal_age').hide();
    $('#mens_irreg_radio').on('click', function() {
        if($(this).is(':checked'))
            $('#mens_irreg').show();
    });
    $('#mens_reg_radio').on('click', function() {
        if($(this).is(':checked'))
            $('#mens_irreg').hide();
    });
    $('#menopausal').on('click', function() {
        if($(this).is(':checked'))
            $('#menopausal_age').show();
    });
    $('#non_menopausal').on('click', function() {
        if($(this).is(':checked'))
            $('#menopausal_age').hide();
    });

    /* *****CONTRACEPTIVES***** */
    $('#contraceptive_others').hide();
    $('#contraceptive_others_cbox').on('click', function() {
       if($(this).is(':checked')) {
           $('#contraceptive_others').show();
           $('#contraceptive_none_cbox').prop('checked', false);
       }
       else
           $('#contraceptive_others').hide();
    });
    $('#contraceptive_none_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#contraceptive_others').hide();
            $('#contraceptive_pills_cbox, #contraceptive_iud_cbox, #contraceptive_rhythm_cbox, #contraceptive_condom_cbox, #contraceptive_others_cbox').prop('checked', false);
        }
    });
    $('#contraceptive_pills_cbox,#contraceptive_iud_cbox,#contraceptive_rhythm_cbox,#contraceptive_condom_cbox').on('click', function() {
        $('#contraceptive_none_cbox').prop('checked', false);
    });

    /* *****ALCOHOL***** */
    $('#alcohol_bottles').hide();
    $('#alcohol_yes_radio').on('click', function() {
        if($(this).is(':checked'))
            $('#alcohol_bottles').show();
    })
    $('#alcohol_no_radio').on('click', function() {
        if($(this).is(':checked'))
            $('#alcohol_bottles').hide();
    });

    /* *****ILLICIT DRUGS***** */
    $('#drugs_text').hide();
    $('#drugs_yes_radio').on('click', function() {
        if($(this).is(':checked'))
            $('#drugs_text').show();
    });
    $('#drugs_no_radio').on('click', function() {
        if($(this).is(':checked'))
            $('#drugs_text').hide();
    });

    /* *****MOTOR/VERBAL/EYE RESPONSE (GLASGOW COMA SCALE)***** */
    function resetPupilSize() {
        $('input[name="glasgow_btn"]:checked').each(function() {
            if($(this).is(':checked'))
                $(this).prop('checked', false);
        })
    }
    function calculateGCS() {
        var motor_radio = $('input[name="motor_radio"]:checked');
        var verbal_radio = $('input[name="verbal_radio"]:checked');
        var eye_radio = $('input[name="eye_radio"]:checked');

        var total = 0;
        if(motor_radio.length > 0) {
            total = total + parseInt(motor_radio.val(), 10);
        }

        if(verbal_radio.length > 0) {
            total = total + parseInt(verbal_radio.val(), 10);
        }

        if(eye_radio.length > 0) {
            total = total + parseInt(eye_radio.val(), 10);
        }

        console.log('gcs score: '+total);
        $('#gcs_score').val(total);
    }

    /**************************************************************************/


    $("#clear_icd, #clear_notes, #clear_other_diag").hide();

    $("#sbmitBtn").on('click',function(e){
        if(!($("#icd").val()) && !($("#other_diag").val())){
            Lobibox.alert("error", {
                msg: "Select ICD-10 diagnosis!"
            });
            return false;
        }
    });

    function clearICD() {
        $("#icd_selected").html("");
        $("#clear_icd").hide();
    }

    function clearOtherDiagnosis() {
        $("#others_diagnosis").html("");
        $("#clear_other_diag").hide();
    }

    function clearNotesDiagnosis() {
        $("#add_notes_diagnosis").html("");
        $("#clear_notes").hide();
    }

    function clearOtherReasonReferral() {
        $("#other_reason_referral").html("");
    }

    function addNotesDiagnosis() {
        $("#add_notes_diagnosis").html(loading);
        $("#clear_notes").show();
        setTimeout(function(){
            $("#add_notes_diagnosis").html('<span class="text-success">Add notes in diagnosis:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control add_notes_diagnosis" name="diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
        },500);
    }

    $('.reason_referral').on('change', function() {
        var value = $(this).val();
        if(value == '-1') {
            $("#other_reason_referral").html(loading);
            setTimeout(function(){
                $("#other_reason_referral").html('<span>Other Reason for Referral:</span>\n' +
                    '                                <br />\n' +
                    '                                <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7" required></textarea>')
            },500);
            $("#other_reason_referral").show();
        }else{
            clearOtherReasonReferral();
        }
    });

    function searchICD10() {
        $("#others_diagnosis").html("");
        $(".icd_body").html(loading);
        var url = "<?php echo asset('icd/search'); ?>";
        var json = {
            "_token" : "<?php echo csrf_token(); ?>",
            "icd_keyword" : $("#icd10_keyword").val()
        };
        $.post(url,json,function(result){
            setTimeout(function(){
                $(".icd_body").html(result);
            },500);
        });
    }

    function getAllCheckBox() {
        $('#icd-modal').modal('toggle');
        $("#clear_icd").show();
        var values = [];

        $('input[name="icd_checkbox[]"]:checked').each(function () {
            values[values.length] = (this.checked ? $(this).parent().parent().siblings("td").eq(1).text() : "");
            var icd_description = $(this).parent().parent().siblings("td").eq(1).text();
            var id = $(this).val();
            if(this.checked){
                $("#icd_selected").append('=> '+icd_description+' '+'<br><input id="icd" type="hidden" name="icd_ids[]" value="'+id+'">');
                console.log("icd id: " + id);
            }
        });
        console.log(values);
    }

    function othersDiagnosis(){
        $('#icd-modal').modal('hide');
        $("#others_diagnosis").html(loading);
        $("#clear_other_diag").show();
        setTimeout(function(){
            $("#others_diagnosis").html('<span class="text-success">Other diagnosis:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea id="other_diag" class="form-control reason_referral" name="other_diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
        },500);
    }

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

    $('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
    });
</script>
@endsection