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

    #normalFormModal .modal-dialog {
    max-width: 60%; /* Adjusts modal to 90% of the screen width */
    width: auto; /* Allows the modal to shrink or grow depending on content */
    }
</style>


<div class="modal fade" role="dialog" id="normalFormModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('submit-referral') }}" method="POST" class="form-submit normal_form">
                <div class="jim-content">
                    @include('include.header_form')
                    <div class="form-group-sm form-inline">
                        {{ csrf_field() }}
                        <input type="hidden" name="patient_id" class="patient_id" value="" />
                        <input type="hidden" class="pt_age"/>
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
                                    <b>Diagnosis/Impression: </b>
                                    <textarea class="form-control" rows="7" name="diagnosis" style="resize: none;width: 100%;margin-top: 1%" required></textarea><br><br>
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
                                <?php
                                // Your commordities string
                                $commorditiesString = $data->commordities;

                                // Split by commas to separate different conditions
                                $conditions = explode(',', $commorditiesString);

                                // Initialize an array to map conditions to their values
                                $conditionsArray = [];

                                // Iterate over each condition
                                foreach ($conditions as $condition) {
                                    // Split by 'Year =>' to identify conditions with years
                                    $parts = explode(' Year => ', $condition);
                                    if (count($parts) == 2) {
                                        // Condition with a year
                                        $conditionsArray[trim($parts[0])] = trim($parts[1]);
                                    } else {
                                        // Handle conditions without years or notes
                                        $conditionsArray[trim($parts[0])] = trim($condition);
                                    }
                                }

                                // Additional handling for Cancer and Others
                                $conditionsArray['Cancer'] = $conditionsArray['Cancer'] ?? '';
                                $conditionsArray['Others'] = $conditionsArray['Others'] ?? '';
                                ?>
                                
                                <div class="collapse" id="collapse_medical_history" style="width: 100%;">
                                    <b>COMORBIDITIES</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_all_cbox" name="comor_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Select All">
                                                <span>Select All</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-check-input" id="comor_none_cbox" name="comor_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="None">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                            <input type="hidden" name="comor_hyper_cbox" value="No">
                                                <input class="form-check-input" id="comor_hyper_cbox" name="comor_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                @if (array_key_exists('Hypertension', $conditionsArray))
                                                    checked
                                                @endif
                                                > Hypertension
                                                <span id="comor_hyper"> since
                                                <select class="form-control select" name="hyper_year" style="font-size: 10pt;">
                                                @foreach (range(date('Y'), 1950) as $year)
                                                    <option value="{{ $year }}" @if ($conditionsArray['Hypertension'] == $year) selected @endif>{{ $year }}</option>
                                                @endforeach
                                                 </select>
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                            <input type="hidden" name="comor_diab_cbox" value="No">
                                            <input class="form-check-input" id="comor_diab_cbox" name="comor_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                @if (array_key_exists('Diabetes', $conditionsArray) && $conditionsArray['Diabetes'] !== 'NoYear')
                                                    checked
                                                @endif
                                            >
                                            Diabetes Mellitus
                                            <span id="comor_diab"> since
                                                <select class="form-control select" name="diab_year" style="font-size: 10pt;">
                                                    @foreach (range(date('Y'), 1950) as $year)
                                                        <option value="{{ $year }}" @if (isset($conditionsArray['Diabetes']) && $conditionsArray['Diabetes'] == $year) selected @endif>{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </span>
                                            </div>
                                            <div class="col-md-4">
                                            <input type="hidden" name="comor_asthma_cbox" value="No">
                                                <input class="form-check-input" id="comor_asthma_cbox" name="comor_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                @if (array_key_exists('Asthma', $conditionsArray) && $conditionsArray['Asthma'] !== 'NoYear')
                                                    checked
                                                @endif
                                                > Bronchial Asthma
                                                <span id="comor_asthma"> since
                                                <select class="form-control select" name="asthma_year" style="font-size: 10pt;">
                                                    @foreach (range(date('Y'), 1950) as $year)
                                                        <option value="{{ $year }}" @if (isset($conditionsArray['Asthma']) && $conditionsArray['Asthma'] == $year) selected @endif>{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                            <input type="hidden" name="comor_copd_cbox" value="No">
                                                <input class="form-check-input" id="comor_copd_cbox" name="comor_copd_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                @if (array_key_exists('COPD', $conditionsArray))
                                                    checked
                                                @endif
                                                >
                                                <span> COPD</span>
                                            </div>
                                            <div class="col-md-4">
                                            <input type="hidden" name="comor_dyslip_cbox" value="No">
                                                <input class="form-check-input" id="comor_dyslip_cbox" name="comor_dyslip_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                @if (array_key_exists('Thyroid Disease', $conditionsArray))
                                                    checked
                                                @endif
                                                >
                                                <span> Dyslipidemia</span>
                                            </div>
                                            <div class="col-md-4">
                                            <input type="hidden" name="comor_thyroid_cbox" value="No">
                                                <input class="form-check-input" id="comor_thyroid_cbox" name="comor_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                @if (array_key_exists('Thyroid Disease', $conditionsArray))
                                                    checked
                                                @endif
                                                >
                                                <span> Thyroid Disease</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                        
                                            <div class="col-md-4">
                                                <input type="hidden" name="comor_cancer_cbox" value="No">
                                                <input class="form-check-input" id="comor_cancer_cbox" name="comor_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                    <?php if (array_key_exists('Cancer', $conditionsArray) && !empty($conditionsArray['Cancer'])) echo 'checked'; ?>
                                                >
                                                <span>Cancer <i>(specify)</i>:</span>
                                                <textarea class="form-control" name="comor_cancer" id="comor_cancer" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($conditionsArray['Cancer']); ?></textarea>
                                            </div>
                                        
                                            <div class="col-md-4">
                                                <input type="hidden" name="comor_others_cbox" value="No">
                                                <input class="form-check-input" id="comor_others_cbox" name="comor_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                    <?php if (array_key_exists('Others', $conditionsArray) && !empty($conditionsArray['Others'])) echo 'checked'; ?>
                                                >
                                                <span>Other(s):</span>
                                                <textarea class="form-control" name="comor_others" id="comor_others" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($conditionsArray['Others']); ?></textarea>
                                            </div>
                                        </div>


                                    </div><br>
                                    
                                    <?php
                                        // Assuming $data->allergies is available in your PHP script
                                        $allergiesString = $data->allergies;

                                        // Initialize an array to map allergies to their causes or descriptions
                                        $allergiesArray = [
                                            'Food Allergy' => '',
                                            'Drug Allergy' => '',
                                            'Other Allergy' => ''
                                        ];

                                        // Split the allergies string by commas
                                        $allergies = explode(',', $allergiesString);

                                        // Iterate over each allergy
                                        foreach ($allergies as $key => $allergy) {
                                            $allergy = trim($allergy);

                                            // Look for the "Cause =>" pattern and get the cause
                                            if (strpos($allergy, 'Cause =>') !== false) {
                                                $parts = explode('Cause =>', $allergy);
                                                $type = trim($allergies[$key - 1]); // Get the type from the previous element
                                                $cause = trim($parts[1]);

                                                if (isset($allergiesArray[$type])) {
                                                    $allergiesArray[$type] = $cause;
                                                }
                                            }
                                        }
                                        ?>


                                    <b>ALLERGIES</b><i> (Specify)</i><br>
                                    <div class="container-referral">
                                        <div class="row">
                                        <div class="col-md-4">
                                            <input class="form-check-input" id="allergy_all_cbox" name="allergy_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="select all">
                                            <span> Select All</span>
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-check-input" id="allergy_none_cbox" name="allergy_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="none">
                                            <span> None</span>
                                        </div>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-4">
                                            <input type="hidden" name="allergy_food_cbox" value="No">
                                            <input class="form-check-input" id="allergy_food_cbox" name="allergy_food_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($allergiesArray['Food Allergy'])) echo 'checked'; ?>>
                                            <span> Food(s): <i>(ex. crustaceans, eggs)</i></span>
                                            <textarea class="form-control" id="allergy_food" name="allergy_food_cause" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($allergiesArray['Food Allergy']); ?></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="hidden" name="allergy_drug_cbox" value="No">
                                            <input class="form-check-input" id="allergy_drug_cbox" name="allergy_drug_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($allergiesArray['Drug Allergy'])) echo 'checked'; ?>>
                                            <span> Drug(s): <i>(ex. Ibuprofen, NSAIDS)</i></span>
                                            <textarea class="form-control" id="allergy_drug" name="allergy_drug_cause" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($allergiesArray['Drug Allergy']); ?></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="hidden" name="allergy_other_cbox" value="No">
                                            <input class="form-check-input" id="allergy_other_cbox" name="allergy_other_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($allergiesArray['Other Allergy'])) echo 'checked'; ?>>
                                            <span> Other(s):</span>
                                            <textarea class="form-control" id="allergy_other" name="allergy_other_cause" style="resize: none;width: 100%;" rows="2"><?php echo htmlspecialchars($allergiesArray['Other Allergy']); ?></textarea>
                                        </div>
                                        </div>
                                    </div><br>
                                    
                                    <?php
                                        // Assuming $data->heredofamilial_diseases is available in your PHP script
                                        $heredoString = $data->heredofamilial_diseases;

                                        // Initialize an array to map heredofamilial diseases to their respective sides
                                        $heredoArray = [
                                            'Hypertension' => '',
                                            'Diabetes' => '',
                                            'Asthma' => '',
                                            'Cancer' => '',
                                            'Kidney Disease' => '',
                                            'Thyroid Disease' => '',
                                            'Others' => ''
                                        ];

                                        // Split the heredofamilial diseases string by commas
                                        $diseases = explode(',', $heredoString);

                                        // Iterate over each disease
                                        foreach ($diseases as $disease) {
                                            // Look for the "side =>" pattern and get the side
                                            if (strpos($disease, 'side =>') !== false) {
                                                $parts = explode('side =>', $disease);
                                                $type = trim($parts[0]); // Disease type
                                                $side = trim($parts[1]); // Side of the family

                                                if (isset($heredoArray[$type])) {
                                                    $heredoArray[$type] = $side;
                                                }
                                            }
                                        }
                                        ?>

                                    <script>
                                  function toggleInputVisibility(inputId) {
                                        var checkbox = document.getElementById(inputId + '_cbox');
                                        var inputField = document.getElementById(inputId);

                                        if (checkbox.checked) {
                                            inputField.style.display = 'inline-block';
                                        } else {
                                            inputField.style.display = 'none';
                                            inputField.value = '';  // Optionally clear the input if unchecked
                                        }
                                    }
                                    </script>

                                    <b>HEREDOFAMILIAL DISEASES</b> <i>(Specify which side of the family: maternal, paternal, both)</i>
                                    <div class="container-referral">
                                        <div class="row">
                                        <div class="col-md-3">
                                            <input class="form-check-input" id="heredo_all_cbox" name="heredo_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="select all">
                                            <span>Select All</span>
                                        </div>
                                        <div class="col-md-3">
                                            <input class="form-check-input" id="heredo_none_cbox" name="heredo_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="none">
                                            <span>None</span>
                                        </div>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-3">
                                            <input type="hidden" name="heredo_hyper_cbox" value="No">
                                            <input class="form-check-input" id="heredo_hyper_cbox" name="heredo_hyper_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($heredoArray['Hypertension'])) echo 'checked'; ?>>
                                            <span>Hypertension:</span>
                                            <input type="text" id="heredo_hyper" name="heredo_hypertension_side" value="<?php echo htmlspecialchars($heredoArray['Hypertension']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="heredo_diab_cbox" value="No">
                                            <input class="form-check-input" id="heredo_diab_cbox" name="heredo_diab_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($heredoArray['Diabetes'])) echo 'checked'; ?>>
                                            <span>Diabetes Mellitus:</span>
                                            <input type="text" id="heredo_diab" name="heredo_diabetes_side" value="<?php echo htmlspecialchars($heredoArray['Diabetes']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="heredo_asthma_cbox" value="No">
                                            <input class="form-check-input" id="heredo_asthma_cbox" name="heredo_asthma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($heredoArray['Asthma'])) echo 'checked'; ?>>
                                            <span>Bronchial Asthma:</span>
                                            <input type="text" id="heredo_asthma" name="heredo_asthma_side" value="<?php echo htmlspecialchars($heredoArray['Asthma']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="heredo_cancer_cbox" value="No">
                                            <input class="form-check-input" id="heredo_cancer_cbox" name="heredo_cancer_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($heredoArray['Cancer'])) echo 'checked'; ?>>
                                            <span>Cancer:</span>
                                            <input type="text" id="heredo_cancer" name="heredo_cancer_side" value="<?php echo htmlspecialchars($heredoArray['Cancer']); ?>">
                                        </div>
                                        </div><br>
                                        <div class="row">
                                        <div class="col-md-3">
                                            <input type="hidden" name="heredo_kidney_cbox" value="No">
                                            <input class="form-check-input" id="heredo_kidney_cbox" name="heredo_kidney_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($heredoArray['Kidney Disease'])) echo 'checked'; ?>>
                                            <span>Kidney:</span>
                                            <input type="text" id="heredo_kidney" name="heredo_kidney_side" value="<?php echo htmlspecialchars($heredoArray['Kidney Disease']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="heredo_thyroid_cbox" value="No">
                                            <input class="form-check-input" id="heredo_thyroid_cbox" name="heredo_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($heredoArray['Thyroid Disease'])) echo 'checked'; ?>>
                                            <span>Thyroid Disease:</span>
                                            <input type="text" id="heredo_thyroid" name="heredo_thyroid_side" value="<?php echo htmlspecialchars($heredoArray['Thyroid Disease']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="heredo_others_cbox" value="No">
                                            <input class="form-check-input" id="heredo_others_cbox" name="heredo_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" value="Yes"
                                                <?php if(!empty($heredoArray['Others'])) echo 'checked'; ?>>
                                            <span>Other(s):</span>
                                            <input type="text" id="heredo_others" name="heredo_others_side" value="<?php echo htmlspecialchars($heredoArray['Others']); ?>">
                                        </div>
                                        </div>
                                    </div><br>

                                    <b>PREVIOUS HOSPITALIZATION(S) and OPERATION(S)</b><br>
                                    <textarea class="form-control" name="previous_hospitalization" style="resize: none;width: 100%;" rows="3">{{ $data->previous_hospitalization }}</textarea><br><br>
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
                                                <span>A <input type="number" name="prenatal_age" style="width: 8%" min="0" value="{{$data->prenatal_a}}">
                                                     year old G <input type="number" name="prenatal_g" style="width: 8%" min="0" value="{{$data->prenatal_g}}"> P <input type="number" style="width: 8%" min="0" name="prenatal_p" value="{{$data->prenatal_p}}"> mother
                                                     <?php
                                                       
                                                        $prenatalData = $data->prenatal_radiowith_or_without;

                                                        // Determine which radio button should be checked
                                                        $withChecked = ($prenatalData === 'with') ? 'checked' : '';
                                                        $withoutChecked = ($prenatalData === 'without') ? 'checked' : '';
                                                        ?>
                                                     <input class="referral-radio-btn" name="prenatal_radio" type="radio" id="prenatal_radiowith" value="with" <?php echo $withChecked; ?>> with
                                                     <input class="referral-radio-btn" name="prenatal_radio" type="radio" id="prenatal_radiowout" value="without" <?php echo $withoutChecked; ?>> without
                                                    MATERNAL ILLNESS, if Yes (specify)
                                                    <textarea class="form-control" id="prenatal_mat_illness" name="prenatal_maternal_illness" style="resize: none;width: 50%;" rows="2">{{$data->prenatal_with_maternal_illness}}</textarea>
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
                                                    <option value="home" <?php echo ($data->natal_born_at == 'home') ? 'selected' : ''; ?>>Home</option>
                                                    <option value="clinic" <?php echo ($data->natal_born_at == 'clinic') ? 'selected' : ''; ?>>Clinic</option>
                                                    <option value="hospital" <?php echo ($data->natal_born_at == 'hospital') ? 'selected' : ''; ?>>Hospital</option>
                                                </select>
                                                <input type="text" id="hospital_name" name="natal_born_address" style="width: 30%;" placeholder="Specify where" value="{{ $data->natal_born_address }}">
                                                    by
                                                    <select class="form-control-select" name="natal_by">
                                                        <option value="md" <?php echo ($data->natal_by == 'md') ? 'selected' : ''; ?>>MD</option>
                                                        <option value="midwife" <?php echo ($data->natal_by == 'midwife') ? 'selected' : ''; ?>>Midwife</option>
                                                        <option value="hilot" <?php echo ($data->natal_by == 'hilot') ? 'selected' : ''; ?>>Hilot</option>
                                                    </select>
                                                    via
                                                    <select class="form-control-select" name="natal_via">
                                                        <option value="nsd" <?php echo ($data->natal_via == 'nsd') ? 'selected' : ''; ?>>NSD</option>
                                                        <option value="cs" <?php echo ($data->natal_via == 'cs') ? 'selected' : ''; ?>>CS</option>
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
                                                    <option value="preterm" <?php echo ($data->natal_term == 'preterm') ? 'selected' : ''; ?>>Preterm</option>
                                                    <option value="fullterm" <?php echo ($data->natal_term == 'fullterm') ? 'selected' : ''; ?>>Full Term</option>
                                                    <option value="postterm" <?php echo ($data->natal_term == 'postterm') ? 'selected' : ''; ?>>Post Term</option>
                                                </select>
                                                    , weighing <input type="number" name="natal_weight" style="width: 8%" min="0" step="0.01" value="{{$data->natal_weight}}"> kg,
                                                    BR <input type="text" name="natal_br" style="width: 20%" value="{{$data->natal_br}}">, with Good Cry
                                                    <select class="form-control-select" name="natal_withGoodCry">
                                                        <option value="1" <?php echo ($data->natal_with_good_cry == 1) ? 'selected' : ''; ?>>Yes</option>
                                                        <option value="0" <?php echo ($data->natal_with_good_cry == 0) ? 'selected' : ''; ?>>No</option>
                                                    </select><br>
                                                    Other complications:
                                                    <textarea class="form-control" name="natal_complications" style="resize: none;width: 30%;" rows="2">{{ $data->natal_other_complications }}</textarea>
                                                </span>
                                            </div>
                                        </div>
                                    </div><br>
                                    <?php
                                        // Sample data from database
                                        $postnatal_data = [
                                            'postnatal_bfeed' => $data->post_natal_bfeed,
                                            'postnatal_bfeed_xmos' => $data->post_natal_bfeedx_month,
                                            'postnatal_ffeed' => $data->post_natal_formula_feed,
                                            'postnatal_ffeed_specify' => $data->post_natal_ffeed_specify,
                                            'postnatal_started_semisolidfood_at' => $data->post_natal_started_semifoods,
                                            'immu_bcg_cbox' => $data->post_natal_bcg,
                                            'immu_dpt_cbox' => $data->post_natal_dpt_opv_x,
                                            'immu_dpt_doses' => $data->post_dpt_doses,
                                            'immu_hepb_cbox' => $data->post_natal_hepB_cbox,
                                            'immu_hepb_doses' => $data->post_natal_hepB_x_doses,
                                            'immu_measles_cbox' => $data->post_natal_immu_measles_cbox,
                                            'immu_mmr_cbox' => $data->post_natal_mmr_cbox,
                                            'immu_others_cbox' => $data->post_natal_others_cbox,
                                            'immu_others' => $data->post_natal_others,
                                            'prenatal_milestone' => $data->post_natal_development_milestones,
                                        ];

                                        // Feeding History
                                        $postnatal_bfeed_checked = ($postnatal_data['postnatal_bfeed'] === 'Yes') ? 'checked' : '';
                                        $postnatal_bfeed_xmos_value = htmlspecialchars($postnatal_data['postnatal_bfeed_xmos']);
                                        $postnatal_ffeed_checked = ($postnatal_data['postnatal_ffeed'] === 'Yes') ? 'checked' : '';
                                        $postnatal_ffeed_specify_value = htmlspecialchars($postnatal_data['postnatal_ffeed_specify']);
                                        $postnatal_started_semisolidfood_at_value = htmlspecialchars($postnatal_data['postnatal_started_semisolidfood_at']);

                                        // Immunization History
                                        $immu_bcg_checked = ($postnatal_data['immu_bcg_cbox'] === 'Yes') ? 'checked' : '';
                                        $immu_dpt_checked = ($postnatal_data['immu_dpt_cbox'] === 'Yes') ? 'checked' : '';
                                        $immu_dpt_doses_value = htmlspecialchars($postnatal_data['immu_dpt_doses']);
                                        $immu_hepb_checked = ($postnatal_data['immu_hepb_cbox'] === 'Yes') ? 'checked' : '';
                                        $immu_hepb_doses_value = htmlspecialchars($postnatal_data['immu_hepb_doses']);
                                        $immu_measles_checked = ($postnatal_data['immu_measles_cbox'] === 'Yes') ? 'checked' : '';
                                        $immu_mmr_checked = ($postnatal_data['immu_mmr_cbox'] === 'Yes') ? 'checked' : '';
                                        $immu_others_checked = ($postnatal_data['immu_others_cbox'] === 'Yes') ? 'checked' : '';
                                        $immu_others_value = htmlspecialchars($postnatal_data['immu_others']);

                                        // Developmental Milestones
                                        $dev_miles_under_checked = ($postnatal_data['prenatal_milestone'] === 'Under developed') ? 'checked' : '';
                                        $dev_miles_par_checked = ($postnatal_data['prenatal_milestone'] === 'At par with age') ? 'checked' : '';
                                    ?>


                                    <b>POST NATAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Feeding History</i><br>&emsp;
                                                <span>
                                                <input type="hidden" name="postnatal_bfeed" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="postnatal_bfeed" name="postnatal_bfeed" value="Yes" <?php echo $postnatal_bfeed_checked; ?>> Breastfed
                                                    <span id="breastfed">
                                                    x <input type="number" name="postnatal_bfeed_xmos" style="width: 7%;" min="0" value="<?php echo $postnatal_bfeed_xmos_value; ?>"> mos.
                                                    </span>
                                                    <input type="hidden" name="postnatal_ffeed" value="No">
                                                    <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" id="postnatal_ffeed" type="checkbox" name="postnatal_ffeed" value="Yes" <?php echo $postnatal_ffeed_checked; ?>> Formula Fed,
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
                                                <input type="hidden" name="immu_bcg_cbox" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_bcg_cbox" value="Yes" <?php echo $immu_bcg_checked; ?>> BCG
                                                <input type="hidden" name="immu_dpt_cbox" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_dpt_cbox" name="immu_dpt_cbox" value="Yes" <?php echo $immu_dpt_checked; ?>> DPT/OPV
                                                <span id="immu_dpt">
                                                     x <input type="number" name="immu_dpt_doses" style="width: 7%;" min="0" value="<?php echo $immu_dpt_doses_value; ?>"> doses
                                                </span>
                                                <input type="hidden" name="immu_hepb_cbox" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_hepb_cbox" name="immu_hepb_cbox" value="Yes" <?php echo $immu_hepb_checked; ?>> Hep B
                                                <span id="immu_hepb">
                                                     x <input type="number" name="immu_hepb_doses" style="width: 7%;" min="0" value="<?php echo $immu_hepb_doses_value; ?>"> doses
                                                </span>
                                                <input type="hidden" name="immu_measles_cbox" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_measles_cbox" value="Yes" <?php echo $immu_measles_checked; ?>> Measles
                                                <input type="hidden" name="immu_mmr_cbox" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="immu_mmr_cbox" value="Yes" <?php echo $immu_mmr_checked; ?>> MMR
                                                <input type="hidden" name="immu_others_cbox" value="No">
                                                <input class="form-check-input" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" id="immu_others_cbox" name="immu_others_cbox" value="Yes" <?php echo $immu_others_checked; ?>> Other(s)
                                                <span id="immu_others">
                                                    <input type="text" name="immu_others" style="width: 20%;" value="<?php echo $immu_others_value; ?>">
                                                </span>
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <i>Developmental Milestones</i><br>&emsp;
                                                <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_under" value="Under developed" <?php echo $dev_miles_under_checked; ?>>
                                                <label for="dev_miles_under">Under developed</label>
                                                <input type="radio" class="referral-radio-btn" name="prenatal_milestone" id="dev_miles_par" value="At par with age" <?php echo $dev_miles_par_checked; ?>>
                                                <label for="dev_miles_par">At par with age</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            // Sample data from database
                            $menarche = $data->menarche;
                            $gynecological_data = [
                                'menarche' => (int)$menarche,
                                'menopause' => $data->menopause,
                                'menopausal_age' => $data->menopausal_age,
                                'menstrual_cycle' => $data->menstrual_cycle,
                                'mens_irreg_xmos' => $data->mens_irreg_xmos,
                                'menstrual_cycle_dysmenorrhea' => $data->menstrual_cycle_dysmenorrhea,
                                'menstrual_cycle_duration' => $data->menstrual_cycle_duration,
                                'menstrual_cycle_padsperday' => $data->menstrual_cycle_padsperday,
                                'menstrual_cycle_medication' => $data->menstrual_cycle_medication,
                                'contraceptive_history' => $data->contraceptive_history,
                                'parity_g' => $data->parity_g,
                                'parity_p' => $data->parity_p,
                                'parity_ft' => $data->parity_ft,
                                'parity_pt' => $data->parity_pt,
                                'parity_a' => $data->parity_a,
                                'parity_l' => $data->parity_l,
                                'parity_lnmp' => $data->parity_lnmp,
                                'parity_edc' => $data->parity_edc,
                                'aog_lnmp' => $data->aog_lnmp,
                                'aog_eutz' => $data->aog_eutz,
                            ];
                            
                            // Process form data
                            
                            $menarche_value = htmlspecialchars($gynecological_data['menarche']);
                            $menopause_checked_yes = ($gynecological_data['menopause'] === 'Yes') ? 'checked' : '';
                            $menopause_checked_no = ($gynecological_data['menopause'] === 'No') ? 'checked' : '';
                            $menopausal_age_value = htmlspecialchars($gynecological_data['menopausal_age']);
                            $menstrual_cycle_reg_checked = ($gynecological_data['menstrual_cycle'] === 'regular') ? 'checked' : '';
                            $menstrual_cycle_irreg_checked = ($gynecological_data['menstrual_cycle'] === 'irregular') ? 'checked' : '';
                            $mens_irreg_xmos_value = htmlspecialchars($gynecological_data['mens_irreg_xmos']);
                            $dysmenorrhea_checked_yes = ($gynecological_data['menstrual_cycle_dysmenorrhea'] === 'Yes') ? 'checked' : '';
                            $dysmenorrhea_checked_no = ($gynecological_data['menstrual_cycle_dysmenorrhea'] === 'No') ? 'checked' : '';
                            $menstrual_cycle_duration_value = htmlspecialchars($gynecological_data['menstrual_cycle_duration']);
                            $menstrual_cycle_padsperday_value = htmlspecialchars($gynecological_data['menstrual_cycle_padsperday']);
                            $menstrual_cycle_medication_value = htmlspecialchars($gynecological_data['menstrual_cycle_medication']);
                            $contraceptive_data = explode(',', $gynecological_data['contraceptive_history']);
                            $contraceptive_none_checked = in_array('none', $contraceptive_data) ? 'checked' : '';
                            $contraceptive_pills_checked = in_array('Pills', $contraceptive_data) ? 'checked' : '';
                            $contraceptive_iud_checked = in_array('IUD', $contraceptive_data) ? 'checked' : '';
                            $contraceptive_rhythm_checked = in_array('Rhythm', $contraceptive_data) ? 'checked' : '';
                            $contraceptive_condom_checked = in_array('Condom', $contraceptive_data) ? 'checked' : '';
                            $contraceptive_others_checked = in_array('Other(s)', $contraceptive_data) ? 'checked' : '';
                            $contraceptive_others_value = ''; // Assuming no 'Other(s)' data provided in the example
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
                                <b>MENARCHE</b> @ <input type="number" min="9" style="width: 10%;" name="menarche" value="<?php echo $menarche_value; ?>"> years old &emsp;&emsp;&emsp;&emsp;
                                <b>MENOPAUSE:</b> &emsp;
                                <input type="radio" class="referral-radio-btn" name="menopause" id="menopause_yes" value="Yes" <?php echo $menopause_checked_yes; ?>>
                                <label for="menopause_yes">Yes</label>
                                <input type="radio" class="referral-radio-btn" name="menopause" id="menopause_no" value="No" <?php echo $menopause_checked_no; ?>>
                                <label for="menopause_no">No</label>
                                <span id="menopausal_age">(age) <input type="number" name="menopausal_age" style="width: 10%;" min="9" value="<?php echo $menopausal_age_value; ?>"></span><br><br>

                                <b>MENSTRUAL CYCLE</b>
                                <div class="container-referral">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="radio" class="referral-radio-btn" name="menstrual_cycle" id="mens_cycle_reg" value="regular" <?php echo $menstrual_cycle_reg_checked; ?>>
                                            <label for="mens_cycle_reg">Regular</label>
                                            <input type="radio" class="referral-radio-btn" name="menstrual_cycle" id="mens_cycle_irreg" value="irregular" <?php echo $menstrual_cycle_irreg_checked; ?>>
                                            <label for="mens_cycle_irreg">Irregular</label>
                                            <span id="mens_irreg">x <input type="number" name="mens_irreg_xmos" style="width: 15%;" min="0" value="<?php echo $mens_irreg_xmos_value; ?>"> mos</span>
                                        </div>
                                        <div class="col-md-6">
                                            <b>Dysmenorrhea:</b> &emsp;
                                            <input type="radio" class="referral-radio-btn" name="menstrual_cycle_dysmenorrhea" id="dysmenorrhea_yes" value="Yes" <?php echo $dysmenorrhea_checked_yes; ?>>
                                            <label for="dysmenorrhea_yes">Yes</label>
                                            <input type="radio" class="referral-radio-btn" name="menstrual_cycle_dysmenorrhea" id="dysmenorrhea_no" value="No" <?php echo $dysmenorrhea_checked_no; ?>>
                                            <label for="dysmenorrhea_no">No</label><br>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Duration:</b> <input type="number" style="width:15%;" min="0" name="menstrual_cycle_duration" value="<?php echo $menstrual_cycle_duration_value; ?>"> days &emsp;
                                            <b>Pads/day:</b> <input type="number" style="width:15%;" min="0" name="menstrual_cycle_padsperday" value="<?php echo $menstrual_cycle_padsperday_value; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <b>Medication:</b> <input type="text" style="width:70%;" name="menstrual_cycle_medication" value="<?php echo $menstrual_cycle_medication_value; ?>">
                                        </div>
                                    </div>
                                </div><br>

                                <b>CONTRACEPTIVE HISTORY</b>
                                <div class="container-referral">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input class="form-check-input" id="contraceptive_none" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_history[]" value="none" <?php echo $contraceptive_none_checked; ?>> None
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-check-input" id="contraceptive_pills" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_history[]" value="Pills" <?php echo $contraceptive_pills_checked; ?>> Pills
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-check-input" id="contraceptive_iud" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_history[]" value="IUD" <?php echo $contraceptive_iud_checked; ?>> IUD
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-check-input" id="contraceptive_rhythm" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_history[]" value="Rhythm" <?php echo $contraceptive_rhythm_checked; ?>> Rhythm
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-check-input" id="contraceptive_condom" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_history[]" value="Condom" <?php echo $contraceptive_condom_checked; ?>> Condom
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input class="form-check-input" id="contraceptive_others" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="contraceptive_history[]" value="Other(s)" <?php echo $contraceptive_others_checked; ?>> Other(s)
                                            <textarea class="form-control" id="contraceptive_others_text" name="contraceptive_others" style="resize: none;width: 50%;" rows="2"><?php echo htmlspecialchars($contraceptive_others_value); ?></textarea><br>
                                        </div>
                                    </div>
                                </div><br>
                                
                                <b>PARITY</b>
                                <div class="container-referral">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <b>G</b> <input type="number" min="0" style="width:8%;" name="parity_g" value="<?php echo $parity_g_value; ?>">
                                            <b>P</b> <input type="number" min="0" style="width:8%;" name="parity_p" value="<?php echo $parity_p_value; ?>">&emsp;
                                            <b>(FT</b> <input type="text" style="width:8%;" name="parity_ft" value="{{$data->parity_ft}}">
                                            <b>PT</b> <input type="text" style="width:8%;" name="parity_pt" value="<?php echo $parity_pt_value; ?>">
                                            <b>A</b> <input type="text" style="width:8%;" name="parity_a" value="<?php echo $parity_a_value; ?>">
                                            <b>L</b> <input type="text" style="width:8%;" name="parity_l" value="<?php echo $parity_l_value; ?>"><b>)</b>
                                        </div>
                                    </div>
                                </div><br>

                                <div class="container-referral">
                                    <b>LNMP</b>
                                    <input type="text" style="width:15%;" name="parity_lnmp" value="<?php echo $parity_lnmp_value; ?>">&emsp;&emsp;&emsp;
                                    <b>EDC</b><i>(if pregnant)</i>
                                    <input type="text" style="width:15%;" name="parity_edc" value="<?php echo $parity_edc_value; ?>">
                                </div><br>

                                <b>AOG</b>
                                <div class="container-referral">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <b>by LNMP</b> <input type="number" min="0" style="width:25%;" name="aog_lnmp" value="<?php echo $aog_lnmp_value; ?>"> <b>wks</b>
                                        </div>
                                        <div class="col-md-4">
                                            <b>by EUTZ</b> <input type="number" min="0" style="width:25%;" name="aog_eutz" value="<?php echo $aog_eutz_value; ?>"> <b>wks</b>
                                        </div>
                                    </div>
                                </div><br>

                                    <b>PRENATAL HISTORY</b><br>
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
                                                        <?php
                                                        foreach (range(date('Y'), 1950) as $year) {
                                                            echo "<option>".$year."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td><input class="form-control" id="gestation" type="text" name="pregnancy_history_gestation[]"></td>
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
                                                <td><input class="form-control" type="text" name="pregnancy_history_complications[]"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                        <button class="btn-sm btn-success" id="prenatal_add_row" type="button">
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
                                    <b>SMOKING</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_yes" value="Yes">
                                                <label for="smoke_yes">Yes</label><br>
                                                <span id="smoking_sticks">Sticks per day: <input type="number" min="0" style="width:30%;" name="smoking_sticks_per_day"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_no" value="No">
                                                <label for="smoke_no">No</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="smoking_radio" id="smoke_quit" value="Quit">
                                                <label for="smoke_quit">Quit</label>
                                                <span id="smoking_quit_year"> since
                                                    <select class="form-control select" name="smoking_year_quit">
                                                        <?php
                                                            foreach(range(date('Y'), 1950) as $year)
                                                                echo "<option>".$year."</option>";
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
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_yes_radio" value="Yes">
                                                <label for="alcohol_yes_radio">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_no_radio" value="No">
                                                <label for="alcohol_no_radio">No</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" class="referral-radio-btn" name="alcohol_radio" id="alcohol_quit_radio" value="Quit">
                                                <label for="alcohol_quit_radio">Quit</label>
                                                <span id="alcohol_quit_year"> since
                                                    <select class="form-control select" name="alcohol_year_quit">
                                                        <?php
                                                        foreach(range(date('Y'), 1950) as $year)
                                                            echo "<option>".$year."</option>";
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

                                    <b>ILLICIT DRUGS</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="radio" name="illicit_drugs" id="drugs_yes_radio" class="referral-radio-btn" value="Yes">
                                                <label for="drugs_yes_radio">Yes</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="radio" name="illicit_drugs" id="drugs_no_radio" class="referral-radio-btn" value="No">
                                                <label for="drugs_no_radio">No</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" name="illicit_drugs" id="drugs_quit_radio" class="referral-radio-btn" value="Quit">
                                                <label for="drugs_quit_radio">Quit</label>
                                                <span id="drugs_quit_year"> since
                                                    <select class="form-control select" name="drugs_year_quit">
                                                        <?php
                                                        foreach(range(date('Y'), 1950) as $year)
                                                            echo "<option>".$year."</option>";
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
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_all_cbox" value="Select All">
                                                <span> Select All</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_ua_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_ua_cbox" value="UA">
                                                <span> UA</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_cbc_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_cbc_cbox" value="CBC">
                                                <span> CBC</span>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input" id="lab_xray_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="lab_xray_cbox" value="X-RAY">
                                                <span> X-RAY</span>
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-check-input" id="lab_others_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox"> Others:
                                                <textarea id="lab_others" class="form-control" name="lab_procedure_other" style="resize: none;" rows="2"></textarea>
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
                                                <input class="form-check-input" id="rs_skin_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_all_cbox" value="Select All">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_skin_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_skin_rashes_cbox" value="No">
                                                <input class="form-check-input" id="rs_skin_rashes_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_rashes_cbox" value="Yes">
                                                <span> Rashes</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_skin_itching_cbox" value="No">
                                                <input class="form-check-input" id="rs_skin_itching_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_itching_cbox" value="Yes">
                                                <span> Itching</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_skin_hairchange_cbox" value="No">
                                                <input class="form-check-input" id="rs_skin_hairchange_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_skin_hairchange_cbox" value="Yes">
                                                <span> Change in hair or nails</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>HEAD</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_all_cbox" value="Select All">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_head_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_head_headache_cbox" value="No">
                                                <input class="form-check-input" id="rs_head_headache_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_headache_cbox" value="Yes">
                                                <span> Headaches</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_head_injury_cbox" value="No">
                                                <input class="form-check-input" id="rs_head_injury_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_head_injury_cbox" value="No">
                                                <span> Head injury</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>EYES</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_all_cbox" value="Select All">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_eyes_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_glasses_cbox" value="No">
                                                <input class="form-check-input" id="rs_eyes_glasses_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_glasses_cbox" value="Yes">
                                                <span> Glasses or Contacts</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_vision_cbox" value="No">
                                                <input class="form-check-input" id="rs_eyes_vision_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_vision_cbox" value="Yes">
                                                <span> Change in vision</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_pain_cbox" value="No">
                                                <input class="form-check-input" id="rs_eyes_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_pain_cbox" value="Yes">
                                                <span> Eye pain</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_doublevision_cbox" value="No">
                                                <input class="form-check-input" id="rs_eyes_doublevision_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_doublevision_cbox" value="Yes">
                                                <span> Double Vision</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                            <input type="hidden" name="rs_eyes_flashing_cbox" value="No">
                                                <input class="form-check-input" id="rs_eyes_flashing_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_flashing_cbox" value="Yes">
                                                <span> Flashing lights</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" name="rs_eyes_glaucoma_cbox" value="No">
                                                <input class="form-check-input" id="rs_eyes_glaucoma_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eyes_glaucoma_cbox" value="Yes">
                                                <span> Glaucoma/Cataracts</span>
                                            </div>
                                            <div class="col-md-3">
                                                
                                                <input class="form-check-input" id="rs_eye_exam_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_eye_exam_cbox" value="Yes">
                                                <span> Last eye exam</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>EARS</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_ears_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_ears_all_cbox" value="Select All">
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

                                    <b>NOSE/SINUSES</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_nose_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_nose_all_cbox" value="Select All">
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

                                    <b>MOUTH/THROAT</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_mouth_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_mouth_all_cbox" value="Select All">
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

                                    <b>NECK</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neck_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neck_all_cbox" value="Select All">
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

                                    <b>BREAST</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_breast_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_breast_all_cbox" value="Select All">
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

                                    <b>RESPIRATORY/CARDIAC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_respi_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_respi_all_cbox" value="Select All">
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

                                    <b>GASTROINTESTINAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_gastro_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_gastro_all_cbox" value="Select All">
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

                                    <b>URINARY</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_urin_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_urin_all_cbox" value="Select All">
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

                                    <b>PERIPHERAL VASCULAR</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_peri_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_peri_all_cbox" value="Select All">
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
                                        </div>
                                    </div><br>

                                    <b>MUSCULOSKELETAL</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_all_cbox" value="Select All">
                                                <span> Select All</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_none_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_none_cbox" value="Yes">
                                                <span> None</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_musclgit e_pain_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_musclgit e_pain_cbox" value="Yes">
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
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_muscle_brokenbone_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_muscle_brokenbone_cbox" value="Yes">
                                                <span> Broken bone</span>
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
                                        </div>
                                    </div><br>

                                    <b>NEUROLOGIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_all_cbox" value="Select All">
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
                                                <input class="form-check-input" id="rs_neuro_sizeloss_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_sizeloss_cbox" value="Yes">
                                                <span> Loss of muscle size</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_spasm_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_spasm_cbox" value="Yes">
                                                <span> Muscle Spasm</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_tremor_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_tremor_cbox" value="Yes">
                                                <span> Tremor</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_involuntary_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_involuntary_cbox" value="Yes">
                                                <span> Involuntary movement</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_neuro_incoordination_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_neuro_incoordination_cbox" value="Yes">
                                                <span> Incoordination</span>
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

                                    <b>HEMATOLOGIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_hema_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_hema_all_cbox" value="Select All">
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

                                    <b>ENDOCRINE</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_all_cbox" value="Select All">
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
                                                <input class="form-check-input" id="rs_endo_thyroid_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_thyroid_cbox" value="Yes">
                                                <span> Thyroid trouble</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_heatcold_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_heatcold_cbox" value="Yes">
                                                <span> Heat/cold intolerance</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_sweat_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_sweat_cbox" value="Yes">
                                                <span> Excessive sweating</span>
                                            </div>
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_endo_diabetes_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_endo_diabetes_cbox" value="Yes">
                                                <span> Diabetes</span>
                                            </div>
                                        </div>
                                    </div><br>

                                    <b>PSYCHIATRIC</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input class="form-check-input" id="rs_psych_all_cbox" style="height: 18px;width: 18px;cursor: pointer;" type="checkbox" name="rs_psych_all_cbox" value="Select All">
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
                                                <span> Depression/suicide ideation</span>
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
                                    <b>Diet</b>
                                    <div class="container-referral">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_none" value="None">
                                                <label for="diet_none"> None </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_oral" value="Oral">
                                                <label for="diet_oral"> Oral </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tube" value="Tube">
                                                <label for="diet_tube"> Tube </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio" id="diet_tpn" value="TPN">
                                                <label for="diet_tpn"> TPN </label>
                                            </div>
                                            <div class="col-md-2">
                                                <input class="form-check-input referral-radio-btn" name="diet_radio" type="radio", id="diet_npo" value="NPO">
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
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_1" value="1">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_2">
                                                    <b>2</b><br>
                                                    <span class="glasgow-dot" style="height: 10px; width: 10px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_2" value="2">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_3">
                                                    <b>3</b><br>
                                                    <span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_3" value="3">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_4">
                                                    <b>4</b><br>
                                                    <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_4" value="4">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_5">
                                                    <b>5</b><br>
                                                    <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_5" value="5">
                                            </div>
                                            <div class="col-lg-1" style="text-align: center">
                                                <label for="glasgow_6">
                                                    <b>6</b><br>
                                                    <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                </label>
                                                <input class="form-control-input referral-radio-btn text-center" name="glasgow_pupil_btn" type="radio" id="glasgow_6" value="6">
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
                                                <label for="glasgow_10">
                                                    <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                </label>
                                                <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_10" value="10">
                                            </div>
                                        </div>
                                        <div class="mobile-view">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <b>1</b>
                                                    <label for="glasgow_1">
                                                    <span class="glasgow-dot" style="height: 6px; width: 6px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_1" value="1">&emsp;&emsp;

                                                    <b>2</b>
                                                    <label for="glasgow_2">
                                                        <span class="glasgow-dot" style="height: 10px; width: 10px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_2" value="2">&emsp;&emsp;

                                                    <b>3</b>
                                                    <label for="glasgow_3">
                                                        <br><span class="glasgow-dot" style="height: 13px; width: 13px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_3" value="3">
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <b>4</b>
                                                    <label for="glasgow_4">
                                                        <span class="glasgow-dot" style="height: 16px; width: 16px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_4" value="4">&emsp;&emsp;

                                                    <b>5</b>
                                                    <label for="glasgow_5">
                                                        <span class="glasgow-dot" style="height: 20px; width: 20px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_5" value="5">&emsp;&emsp;

                                                    <b>6</b>
                                                    <label for="glasgow_6">
                                                        <span class="glasgow-dot" style="height: 24px; width: 24px;"></span><br>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_6" value="6">
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
                                                    <label for="glasgow_10">
                                                        <span class="glasgow-dot" style="height: 40px; width: 40px;"></span>
                                                    </label>
                                                    <input class="form-control-input referral-radio-btn" name="glasgow_pupil_btn" type="radio" id="glasgow_10" value="10">
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
                                                        <td style="text-align: center"><input class="referral-radio-btn" type="radio" name="motor_radio"  value=6></td>
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
                                            <br><br><b>GCS Score: </b>
                                            <input class="number" name="gcs_score" id="gcs_score" style="text-align: center" min="0" value="0" readonly>
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
                            <button type="submit" id="sbmitBtn" class="btn btn-primary btn-flat btn-submit"><i class="fa fa-send"></i> Update</button>
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

@section('js')
<script>
    $("#normalFormModal").modal("show");

//    $('#pedia_show').hide();
//    $('#menarche_show').hide();
//
//    var pt_age = parseInt($('.pt_age').val(), 10);
//    if(pt_age > 18)
//        $('#pedia_show').show();
//    if($('.patient_sex').val() === "Female")
//        $('#menarche_show').show();

    $(".collapse").on('show.bs.collapse', function(){
        $(this).prev(".container-referral2").find(".fa").removeClass("fa-plus").addClass("fa-minus");
    }).on('hide.bs.collapse', function(){
        $(this).prev(".container-referral2").find(".fa").removeClass("fa-minus").addClass("fa-plus");
    });

    /*****Populate data**********/
    var retrieved_data = @json($data);

    console.log(JSON.stringify(retrieved_data));
    // function check_checkbox(){
        
    // }


    /* *****PRENATAL ADD ROW***** */
    <?php
        $select_year = "";
        foreach(range(date('Y'), 1950) as $year)
            $select_year .= "<option>".$year."</option>";
    ?>
    var select_year = "<?php echo $select_year; ?>";

    $('#prenatal_add_row').on('click', function() {
        var rowCount = $('#prenatal_table tr').length;
        $('#prenatal_table').append('<tr style="font-size: 11pt">\n' +
            '<td><input class="form-control" type="text" name="pregnancy_history_order[]"></td>\n' +
            '<td><select class="form-control select" name="pregnancy_history_year[]">\n' +
            select_year +
            '</select></td>\n' +
            '<td><input class="form-control" id="gestation" type="text" name="pregnancy_history_gestation[]"></td>\n' +
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
       if($(this).is(':checked')) {
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
        if($(this).is(':checked'))
           $('#comor_hyper').show();
        else
           $('#comor_hyper').hide();
    });
    $('#comor_diab_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#comor_diab').show();
        else
            $('#comor_diab').hide();
    });
    $('#comor_asthma_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        if($(this).is(':checked'))
           $('#comor_asthma').show();
        else
           $('#comor_asthma').hide();
    });
    $('#comor_cancer_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#comor_cancer').show();
        else
            $('#comor_cancer').hide();
    });
    $('#comor_others_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#comor_others').show();
        else
            $('#comor_others').hide();
    });
    $('#comor_copd_cbox,#comor_dyslip_cbox,#comor_thyroid_cbox').on('click', function() {
        $('#comor_none_cbox, #comor_all_cbox').prop('checked', false);
    });
    $('#comor_none_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#comor_all_cbox, #comor_hyper_cbox, #comor_diab_cbox, #comor_asthma_cbox, #comor_copd_cbox').prop('checked', false);
            $('#comor_dyslip_cbox, #comor_thyroid_cbox, #comor_cancer_cbox, #comor_others_cbox').prop('checked', false);
            $('#comor_asthma, #comor_diab, #comor_hyper, #comor_cancer, #comor_others').hide();
        }
    });

    /* *****ALLERGY***** */
    // $('#allergy_food, #allergy_drug, #allergy_other').hide();
    $('#allergy_all_cbox').on('click', function () {
        if($(this).is(':checked')) {
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
        if($(this).is(':checked'))
            $('#allergy_food').show();
        else
            $('#allergy_food').hide();
    });
    $('#allergy_drug_cbox').on('click', function() {
        $('#allergy_all_cbox, #allergy_none_cbox').prop('checked', false);
        if($(this).is(':checked'))
           $('#allergy_drug').show();
        else
           $('#allergy_drug').hide();
    });
    $('#allergy_other_cbox').on('click', function() {
        $('#allergy_all_cbox, #allergy_none_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#allergy_other').show();
        else
            $('#allergy_other').hide();
    });
    $('#allergy_none_cbox').on('click', function() {
       if($(this).is(':checked')) {
           $('#allergy_food_cbox, #allergy_drug_cbox, #allergy_other_cbox, #allergy_all_cbox').prop('checked', false);
           $('#allergy_food, #allergy_drug, #allergy_other').hide();
       }
    });

    /* *****HEREDOFAMILIAL***** */
    // $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid, #heredo_others').hide();
    $('#heredo_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
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
        if($(this).is(':checked'))
            $('#heredo_hyper').show();
        else
            $('#heredo_hyper').hide();
    });
    $('#heredo_diab_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#heredo_diab').show();
        else
            $('#heredo_diab').hide();
    });
    $('#heredo_asthma_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#heredo_asthma').show();
        else
            $('#heredo_asthma').hide();
    });
    $('#heredo_cancer_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#heredo_cancer').show();
        else
            $('#heredo_cancer').hide();
    });
    $('#heredo_kidney_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#heredo_kidney').show();
        else
            $('#heredo_kidney').hide();
    });
    $('#heredo_thyroid_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#heredo_thyroid').show();
        else
            $('#heredo_thyroid').hide();
    });
    $('#heredo_others_cbox').on('click', function() {
        $('#heredo_all_cbox, #heredo_none_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#heredo_others').show();
        else
            $('#heredo_others').hide();
    });
    $('#heredo_none_cbox').on('click', function() {
        if($(this).is(':checked')) {
           $('#heredo_all_cbox, #heredo_hyper_cbox, #heredo_diab_cbox, #heredo_asthma_cbox, #heredo_cancer_cbox').prop('checked', false);
           $('#heredo_kidney_cbox, #heredo_thyroid_cbox, #heredo_others_cbox').prop('checked', false);
           $('#heredo_hyper, #heredo_diab, #heredo_asthma, #heredo_cancer, #heredo_kidney, #heredo_thyroid, #heredo_others').hide();
       }
    });
 
    /* *****LAB PROCEDURES***** */
    // $('#lab_others').hide();
    $('#lab_all_cbox').on('click', function () {
       if($(this).is(':checked'))
           $('#lab_ua_cbox, #lab_cbc_cbox, #lab_xray_cbox').prop('checked', true);
       else
           $('#lab_ua_cbox, #lab_cbc_cbox, #lab_xray_cbox').prop('checked', false);
    });
    $('#lab_others_cbox').on('click', function () {
        $('#lab_all_cbox').prop('checked', false);
        if($(this).is(':checked'))
            $('#lab_others').show();
        else
            $('#lab_others').hide();
    });
    $('#lab_ua_cbox, #lab_cbc_cbox, #lab_xray_cbox').on('click', function () {
        $('#lab_all_cbox').prop('checked', false);
    });

    /* *****PRENATAL***** */
    // $('#prenatal_mat_illness').hide();
    $('#prenatal_radiowith').on('click', function() {
        if($(this).is(':checked'))
            $('#prenatal_mat_illness').show();
    });
    $('#prenatal_radiowout').on('click', function() {
       if($(this).is(':checked'))
           $('#prenatal_mat_illness').hide();
    });

    /* *****POST NATAL (FEEDING HISTORY)****** */
    // $('#breastfed, #formula_fed').hide();
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
    // $('#immu_dpt, #immu_hepb, #immu_others').hide();
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
    // $('#mens_irreg, #menopausal_age').hide();
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
    // $('#contraceptive_others').hide();
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

    /* *****SMOKING***** */
    // $('#smoking_sticks').hide();
    // $('#smoking_quit_year').hide();
    $('#smoke_yes').on('click', function() {
        if($(this).is(':checked')) {
            $('#smoking_sticks').show();
            $('#smoking_quit_year').hide();
        }
    });
    $('#smoke_quit').on('click', function() {
        if($(this).is(':checked')) {
            $('#smoking_quit_year').show();
            $('#smoking_sticks').hide();
        }
    });
    $('#smoke_no').on('click', function() {
        if($(this).is(':checked')) {
            $('#smoking_sticks').hide();
            $('#smoking_quit_year').hide();
        }
    });

    /* *****ALCOHOL***** */
    // $('#alcohol_bottles').hide();
    // $('#alcohol_type').hide();
    // $('#alcohol_quit_year').hide();
    $('#alcohol_yes_radio').on('click', function() {
        if($(this).is(':checked')) {
            $('#alcohol_bottles').show();
            $('#alcohol_type').show();
            $('#alcohol_quit_year').hide();
        }
    })
    $('#alcohol_no_radio').on('click', function() {
        if($(this).is(':checked')) {
            $('#alcohol_bottles').hide();
            $('#alcohol_type').hide();
            $('#alcohol_quit_year').hide();
        }
    });
    $('#alcohol_quit_radio').on('click', function() {
        if($(this).is(':checked')) {
            $('#alcohol_quit_year').show();
            $('#alcohol_bottles').hide();
            $('#alcohol_type').hide();
        }
    });

    /* *****ILLICIT DRUGS***** */
    // $('#drugs_text').hide();
    // $('#drugs_quit_year').hide();
    $('#drugs_yes_radio').on('click', function() {
        if($(this).is(':checked')) {
            $('#drugs_text').show();
            $('#drugs_quit_year').hide();
        }
    });
    $('#drugs_no_radio').on('click', function() {
        if($(this).is(':checked')) {
            $('#drugs_text').hide();
            $('#drugs_quit_year').hide();
        }
    });
    $('#drugs_quit_radio').on('click', function() {
        if($(this).is(':checked')) {
            $('#drugs_quit_year').show();
            $('#drugs_text').hide();
        }
    });

    /* *****MOTOR/VERBAL/EYE RESPONSE (GLASGOW COMA SCALE)***** */
    function resetPupilSize() {
        $('input[name="glasgow_btn"]:checked').each(function() {
            if($(this).is(':checked'))
                $(this).prop('checked', false);
        })
    }
    var last_motor = last_verbal = last_eye = 0;
    $('input[name="motor_radio"]').on('change', function() {
        var motor = parseInt($('input[name="motor_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score').val(), 10);
        var total = 0;
        if(last_motor == 0)
            total = gcs + motor;
        else
            total = (gcs - last_motor) + motor;

        last_motor = motor;
        $('#gcs_score').val(total);
    });
    $('input[name="verbal_radio"]').on('change', function() {
        var verbal = parseInt($('input[name="verbal_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score').val(), 10);
        var total = 0;
        if(last_verbal == 0)
            total = gcs + verbal;
        else
            total = (gcs - last_verbal) + verbal;

        last_verbal = verbal;
        $('#gcs_score').val(total);
    });
    $('input[name="eye_radio"]').on('change', function() {
        var eye = parseInt($('input[name="eye_radio"]:checked').val(), 10);
        var gcs = parseInt($('#gcs_score').val(), 10);
        var total = 0;
        if(last_eye == 0)
            total = gcs + eye;
        else
            total = (gcs - last_eye) + eye;

        last_eye = eye;
        $('#gcs_score').val(total);
    });

    /* *****REVIEW OF SYSTEMS***** */
    /* SKIN */
    $('#rs_skin_all_cbox').on('click', function () {
       if($(this).is(':checked')) {
           $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox').prop('checked', true);
           $('#rs_skin_none_cbox').prop('checked', false);
       } else
           $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox').prop('checked', false);
    });
    $('#rs_skin_none_cbox').on('click', function () {
       if($(this).is(':checked'))
           $('#rs_skin_all_cbox, #rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox').prop('checked', false);
    });
    $('#rs_skin_rashes_cbox, #rs_skin_itching_cbox, #rs_skin_hairchange_cbox').on('click', function() {
        $('#rs_skin_all_cbox, #rs_skin_none_cbox').prop('checked', false);
    });

    /* HEAD */
    $('#rs_head_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_head_headache_cbox, #rs_head_injury_cbox').prop('checked', true);
            $('#rs_head_none_cbox').prop('checked', false);
        } else
            $('#rs_head_headache_cbox, #rs_head_injury_cbox').prop('checked', false);
    });
    $('#rs_head_none_cbox').on('click', function() {
       if($(this).is(':checked'))
           $('#rs_head_all_cbox, #rs_head_headache_cbox, #rs_head_injury_cbox').prop('checked', false);
    });
    $('#rs_head_headache_cbox, #rs_head_injury_cbox').on('click', function() {
        $('#rs_head_all_cbox, #rs_head_none_cbox').prop('checked', false);
    })

    /* EYES */
    $('#rs_eyes_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_eyes_glasses_cbox, #rs_eyes_vision_cbox, #rs_eyes_pain_cbox, #rs_eyes_doublevision_cbox').prop('checked', true);
            $('#rs_eyes_flashing_cbox, #rs_eyes_glaucoma_cbox, #rs_eye_exam_cbox').prop('checked', true);
            $('#rs_eyes_none_cbox').prop('checked', false);
        } else {
            $('#rs_eyes_glasses_cbox, #rs_eyes_vision_cbox, #rs_eyes_pain_cbox, #rs_eyes_doublevision_cbox').prop('checked', false);
            $('#rs_eyes_flashing_cbox, #rs_eyes_glaucoma_cbox, #rs_eye_exam_cbox').prop('checked', false);
        }
    });
    $('#rs_eyes_none_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_eyes_glasses_cbox, #rs_eyes_vision_cbox, #rs_eyes_pain_cbox, #rs_eyes_doublevision_cbox').prop('checked', false);
            $('#rs_eyes_all_cbox, #rs_eyes_flashing_cbox, #rs_eyes_glaucoma_cbox, #rs_eye_exam_cbox').prop('checked', false);
        }
    });
    $('#rs_eyes_glasses_cbox, #rs_eyes_vision_cbox, #rs_eyes_pain_cbox, #rs_eyes_doublevision_cbox, #rs_eyes_flashing_cbox, #rs_eyes_glaucoma_cbox, #rs_eye_exam_cbox').on('click', function() {
        $('#rs_eyes_all_cbox, #rs_eyes_none_cbox').prop('checked', false);
    });

    /* EARS */
    $('#rs_ears_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_ears_changehearing_cbox, #rs_ears_pain_cbox, #rs_ears_discharge_cbox, #rs_ears_ringing_cbox, #rs_ears_dizziness_cbox').prop('checked', true);
            $('#rs_ears_none_cbox').prop('checked', false);
        } else
            $('#rs_ears_changehearing_cbox, #rs_ears_pain_cbox, #rs_ears_discharge_cbox, #rs_ears_ringing_cbox, #rs_ears_dizziness_cbox').prop('checked', false);
    });
    $('#rs_ears_none_cbox').on('click', function() {
       if($(this).is(':checked'))
           $('#rs_ears_all_cbox, #rs_ears_changehearing_cbox, #rs_ears_pain_cbox, #rs_ears_discharge_cbox, #rs_ears_ringing_cbox, #rs_ears_dizziness_cbox').prop('checked', false);
    });
    $('#rs_ears_changehearing_cbox, #rs_ears_pain_cbox, #rs_ears_discharge_cbox, #rs_ears_ringing_cbox, #rs_ears_dizziness_cbox').on('click', function() {
       $('#rs_ears_all_cbox, #rs_ears_none_cbox').prop('checked', false);
    });

    /* NOSE/SINUSES */
    $('#rs_nose_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_nose_bleeds_cbox, #rs_nose_stuff_cbox, #rs_nose_colds_cbox').prop('checked', true);
            $('#rs_nose_none_cbox').prop('checked', false);
        }else
            $('#rs_nose_bleeds_cbox, #rs_nose_stuff_cbox, #rs_nose_colds_cbox').prop('checked', false);
    });
    $('#rs_nose_none_cbox').on('click', function() {
        if($(this).is(':checked'))
            $('#rs_nose_all_cbox, #rs_nose_bleeds_cbox, #rs_nose_stuff_cbox, #rs_nose_colds_cbox').prop('checked', false);
    });
    $('#rs_nose_bleeds_cbox, #rs_nose_stuff_cbox, #rs_nose_colds_cbox').on('click', function() {
       $('#rs_nose_all_cbox, #rs_nose_none_cbox').prop('checked', false);
    });

    /* MOUTH/THROAT */
    $('#rs_mouth_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_mouth_bleed_cbox, #rs_mouth_soretongue_cbox, #rs_mouth_sorethroat_cbox, #rs_mouth_hoarse_cbox').prop('checked', true);
            $('#rs_mouth_none_cbox').prop('checked', false);
        } else
            $('#rs_mouth_bleed_cbox, #rs_mouth_soretongue_cbox, #rs_mouth_sorethroat_cbox, #rs_mouth_hoarse_cbox').prop('checked', false);
    });
    $('#rs_mouth_none_cbox').on('click', function() {
        if($(this).is(':checked'))
            $('#rs_mouth_all_cbox, #rs_mouth_bleed_cbox, #rs_mouth_soretongue_cbox, #rs_mouth_sorethroat_cbox, #rs_mouth_hoarse_cbox').prop('checked', false);
    });
    $('#rs_mouth_bleed_cbox, #rs_mouth_soretongue_cbox, #rs_mouth_sorethroat_cbox, #rs_mouth_hoarse_cbox').on('click', function() {
       $('#rs_mouth_all_cbox, #rs_mouth_none_cbox').prop('checked', false);
    });

    /* NECK */
    $('#rs_neck_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_neck_lumps_cbox, #rs_neck_swollen_cbox, #rs_neck_goiter_cbox, #rs_neck_stiff_cbox').prop('checked', true);
            $('#rs_neck_none_cbox').prop('checked', false);
        } else
            $('#rs_neck_lumps_cbox, #rs_neck_swollen_cbox, #rs_neck_goiter_cbox, #rs_neck_stiff_cbox').prop('checked', false);
    });
    $('#rs_neck_none_cbox').on('click', function() {
        if($(this).is(':checked'))
            $('#rs_neck_all_cbox, #rs_neck_lumps_cbox, #rs_neck_swollen_cbox, #rs_neck_goiter_cbox, #rs_neck_stiff_cbox').prop('checked', false);
    });
    $('#rs_neck_lumps_cbox, #rs_neck_swollen_cbox, #rs_neck_goiter_cbox, #rs_neck_stiff_cbox').on('click', function() {
        $('#rs_neck_all_cbox, #rs_neck_none_cbox').prop('checked', false);
    });

    /* BREAST */
    $('#rs_breast_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_breast_lumps_cbox, #rs_breast_pain_cbox, #rs_breast_discharge_cbox, #rs_breast_bse_cbox').prop('checked', true);
            $('#rs_breast_none_cbox').prop('checked', false);
        } else
            $('#rs_breast_lumps_cbox, #rs_breast_pain_cbox, #rs_breast_discharge_cbox, #rs_breast_bse_cbox').prop('checked', false);
    });
    $('#rs_breast_none_cbox').on('click', function() {
        if($(this).is(':checked'))
            $('#rs_breast_all_cbox, #rs_breast_lumps_cbox, #rs_breast_pain_cbox, #rs_breast_discharge_cbox, #rs_breast_bse_cbox').prop('checked', false);
    });
    $('#rs_breast_lumps_cbox, #rs_breast_pain_cbox, #rs_breast_discharge_cbox, #rs_breast_bse_cbox').on('click', function() {
        $('#rs_breast_all_cbox, #rs_breast_none_cbox').prop('checked', false);
    });

    /* RESPIRATORY/CARDIAC */
    $('#rs_respi_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
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
        if($(this).is(':checked')) {
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
        if($(this).is(':checked')) {
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
        if($(this).is(':checked')) {
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
        if($(this).is(':checked')) {
            $('#rs_urin_difficult_cbox, #rs_urin_pain_cbox, #rs_urin_frequent_cbox, #rs_urin_urgent_cbox, #rs_urin_incontinence_cbox').prop('checked', true);
            $('#rs_urin_dribbling_cbox, #rs_urin_decreased_cbox, #rs_urin_blood_cbox, #rs_urin_uti_cbox').prop('checked', true);
            $('#rs_urin_none_cbox').prop('checked', false);
        } else {
            $('#rs_urin_difficult_cbox, #rs_urin_pain_cbox, #rs_urin_frequent_cbox, #rs_urin_urgent_cbox, #rs_urin_incontinence_cbox').prop('checked', false);
            $('#rs_urin_dribbling_cbox, #rs_urin_decreased_cbox, #rs_urin_blood_cbox, #rs_urin_uti_cbox').prop('checked', false);
        }
    });
    $('#rs_urin_none_cbox').on('click', function() {
       if($(this).is(':checked')) {
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
        if($(this).is(':checked')) {
            $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox').prop('checked', true);
            $('#rs_peri_none_cbox').prop('checked', false);
        } else
            $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox').prop('checked', false);
    });
    $('#rs_peri_none_cbox').on('click', function() {
        if($(this).is(':checked'))
            $('#rs_peri_all_cbox, #rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox').prop('checked', false);
    });
    $('#rs_peri_legcramp_cbox, #rs_peri_varicose_cbox, #rs_peri_veinclot_cbox').on('click', function() {
        $('#rs_peri_all_cbox, #rs_peri_none_cbox').prop('checked', false);
    });

    /* MUSCULOSKELETAL */
    $('#rs_muscle_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_brokenbone_cbox').prop('checked', true);
            $('#rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox').prop('checked', true);
            $('#rs_muscle_none_cbox').prop('checked', false);
        } else {
            $('#rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_brokenbone_cbox').prop('checked', false);
            $('#rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox').prop('checked', false);
        }
    });
    $('#rs_muscle_none_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_muscle_all_cbox, #rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_brokenbone_cbox').prop('checked', false);
            $('#rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox').prop('checked', false);
        }
    });
    $('#rs_muscle_pain_cbox, #rs_muscle_swell_cbox, #rs_muscle_stiff_cbox, #rs_muscle_decmotion_cbox, #rs_muscle_brokenbone_cbox').on('click', function() {
        $('#rs_muscle_all_cbox, #rs_muscle_none_cbox').prop('checked', false);
    });
    $('#rs_muscle_sprain_cbox, #rs_muscle_arthritis_cbox, #rs_muscle_gout_cbox').on('click', function() {
        $('#rs_muscle_all_cbox, #rs_muscle_none_cbox').prop('checked', false);
    });

    /* NEUROLOGIC */
    $('#rs_neuro_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox, #rs_neuro_sizeloss_cbox').prop('checked', true);
            $('#rs_neuro_spasm_cbox, #rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_incoordination_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', true);
            $('#rs_neuro_none_cbox').prop('checked', false);
        } else {
            $('#rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox, #rs_neuro_sizeloss_cbox').prop('checked', false);
            $('#rs_neuro_spasm_cbox, #rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_incoordination_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', false);
        }
    });
    $('#rs_neuro_none_cbox').on('click', function() {
       if($(this).is(':checked')) {
           $('#rs_neuro_all_cbox, #rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox, #rs_neuro_sizeloss_cbox').prop('checked', false);
           $('#rs_neuro_spasm_cbox, #rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_incoordination_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').prop('checked', false);
       }
    });
    $('#rs_neuro_headache_cbox, #rs_neuro_seizure_cbox, #rs_neuro_faint_cbox, #rs_neuro_paralysis_cbox, #rs_neuro_weakness_cbox, #rs_neuro_sizeloss_cbox').on('click', function() {
        $('#rs_neuro_all_cbox, #rs_neuro_none_cbox').prop('checked', false);
    });
    $('#rs_neuro_spasm_cbox, #rs_neuro_tremor_cbox, #rs_neuro_involuntary_cbox, #rs_neuro_incoordination_cbox, #rs_neuro_numbness_cbox, #rs_neuro_tingles_cbox').on('click', function() {
        $('#rs_neuro_all_cbox, #rs_neuro_none_cbox').prop('checked', false);
    });

    /* HEMATOLOGIC */
    $('#rs_hema_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_hema_anemia_cbox, #rs_hema_bruising_cbox, #rss_hema_transfusion_cbox').prop('checked', true);
            $('#rs_hema_none_cbox').prop('checked', false);
        } else
            $('#rs_hema_anemia_cbox, #rs_hema_bruising_cbox, #rss_hema_transfusion_cbox').prop('checked', false);
    });
    $('#rs_hema_none_cbox').on('click', function() {
        if($(this).is(':checked'))
            $('#rs_hema_all_cbox, #rs_hema_anemia_cbox, #rs_hema_bruising_cbox, #rss_hema_transfusion_cbox').prop('checked', false);
    });
    $('#rs_hema_anemia_cbox, #rs_hema_bruising_cbox, #rss_hema_transfusion_cbox').on('click', function() {
        $('#rs_hema_all_cbox, #rs_hema_none_cbox').prop('checked', false);
    });

    /* ENDOCRINE */
    $('#rs_endo_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_endo_abnormal_cbox, #rs_endo_appetite_cbox, #rs_endo_thirst_cbox, #rs_endo_urine_cbox, #rs_endo_thyroid_cbox').prop('checked', true);
            $('#rs_endo_heatcold_cbox, #rs_endo_sweat_cbox, #rs_endo_diabetes_cbox').prop('checked', true);
            $('#rs_endo_none_cbox').prop('checked', false);
        } else {
            $('#rs_endo_abnormal_cbox, #rs_endo_appetite_cbox, #rs_endo_thirst_cbox, #rs_endo_urine_cbox, #rs_endo_thyroid_cbox').prop('checked', false);
            $('#rs_endo_heatcold_cbox, #rs_endo_sweat_cbox, #rs_endo_diabetes_cbox').prop('checked', false);
        }
    });
    $('#rs_endo_none_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_endo_abnormal_cbox, #rs_endo_appetite_cbox, #rs_endo_thirst_cbox, #rs_endo_urine_cbox, #rs_endo_thyroid_cbox').prop('checked', false);
            $('#rs_endo_all_cbox, #rs_endo_heatcold_cbox, #rs_endo_sweat_cbox, #rs_endo_diabetes_cbox').prop('checked', false);
        }
    });
    $('#rs_endo_abnormal_cbox, #rs_endo_appetite_cbox, #rs_endo_thirst_cbox, #rs_endo_urine_cbox, #rs_endo_thyroid_cbox').on('click', function() {
        $('#rs_endo_all_cbox, #rs_endo_none_cbox').prop('checked', false);
    });
    $('#rs_endo_heatcold_cbox, #rs_endo_sweat_cbox, #rs_endo_diabetes_cbox').on('click', function() {
        $('#rs_endo_all_cbox, #rs_endo_none_cbox').prop('checked', false);
    });

    /* PSYCHIATRIC */
    $('#rs_psych_all_cbox').on('click', function() {
        if($(this).is(':checked')) {
            $('#rs_psych_tension_cbox, #rs_psych_depression_cbox, #rs_psych_memory_cbox, #rs_psych_unusual_cbox, #rs_psych_sleep_cbox, #rs_psych_treatment_cbox, #rs_psych_moodchange_cbox').prop('checked', true);
            $('#rs_psych_none_cbox').prop('checked', false);
        } else
            $('#rs_psych_tension_cbox, #rs_psych_depression_cbox, #rs_psych_memory_cbox, #rs_psych_unusual_cbox, #rs_psych_sleep_cbox, #rs_psych_treatment_cbox, #rs_psych_moodchange_cbox').prop('checked', false);
    });
    $('#rs_psych_none_cbox').on('click', function() {
        if($(this).is(':checked'))
            $('#rs_psych_all_cbox, #rs_psych_tension_cbox, #rs_psych_depression_cbox, #rs_psych_memory_cbox, #rs_psych_unusual_cbox, #rs_psych_sleep_cbox, #rs_psych_treatment_cbox, #rs_psych_moodchange_cbox').prop('checked', false);
    });
    $('#rs_psych_tension_cbox, #rs_psych_depression_cbox, #rs_psych_memory_cbox, #rs_psych_unusual_cbox, #rs_psych_sleep_cbox, #rs_psych_treatment_cbox, #rs_psych_moodchange_cbox').on('click', function() {
        $('#rs_psych_all_cbox, #rs_psych_none_cbox').prop('checked', false);
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