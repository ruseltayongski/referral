<?php
$user = Session::get('auth');
$facilities = \App\Facility::select('id','name')
    ->where('id','!=',$user->facility_id)
    ->where('status',1)
    ->where('referral_used','yes')
    ->orderBy('name','asc')->get();
$myfacility = \App\Facility::find($user->facility_id);
$facility_address = \App\Http\Controllers\LocationCtrl::facilityAddress($myfacility->id);
$departments = \App\Http\Controllers\LocationCtrl::getDepartmentByFacility($myfacility->id);
$referral_reasons = \App\ReasonForReferral::get();
?>
<div class="modal fade" role="dialog" id="normalFormModalWalkIn">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ url('doctor/patient') }}" method="POST" class="form-submit normal_form_walkin">
                <div class="jim-content">
                    <div class="title-form">CENTRAL VISAYAS HEALTH REFERRAL SYSTEM<br /><small>Clinical Referral Form</small></div>
                    <div class="form-group-sm form-inline">
                        {{ csrf_field() }}
                        <input type="hidden" name="patient_id" class="patient_id" value="" />
                        <input type="hidden" name="date_referred" class="date_referred" value="{{ date('Y-m-d H:i:s') }}" />
                        <input type="hidden" name="code" value="" />
                        <input type="hidden" name="source" value="{{ $source }}" />
                        <input type="hidden" class="referring_name" value="" />
                        <table class="table table-striped">
                            <tr>
                                <td colspan="6">
                                    <div class="row">
                                        <div class="col-md-3">
                                            Name of Referring Facility:
                                        </div>
                                        <div class="col-md-9">
                                            <select name="referring_facility_walkin" class="form-control-select select2 select_facility_walkin" style="width: 100%;" required>
                                                <option value="">Select Facility...</option>
                                                @foreach($facilities as $row)
                                                    <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div class="row">
                                        <div class="col-md-3">
                                            Address:
                                        </div>
                                        <div class="col-md-9">
                                            <span class="text-primary facility_address"></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    Referred to:
                                    <span class="text-success">{{ $myfacility->name }}</span>
                                </td>
                                <td colspan="3">
                                    Department: <select name="referred_department" class="form-control-select select_department select_department_normal" style="padding: 3px" required>
                                        <option value="">Select Department...</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}">{{ $d->description }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">Address: <span class="text-success">{{ $facility_address['address'] }}</span></td>
                            </tr>
                            <tr>
                                <td colspan="3">Date/Time Referred (ReCo): <span class="text-success">{{ date('l F d, Y h:i A') }}</span> </td>
                                <td colspan="3">Date/Time Transferred: </td>
                            </tr>
                            <tr>
                                <td colspan="3">Name of Patient: <span class="text-danger patient_name"></span></td>
                                <td>Age: <span class="text-danger patient_age"></span></td>
                                <td>Sex:
                                    <select name="patient_sex" class="patient_sex form-control-select" style="padding: 3px" required>
                                        <option value="">Select...</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select></td>
                                <td>Status:
                                    <select name="civil_status" class="civil_status form-control-select" style="padding: 3px" required>
                                        <option value="">Select...</option>
                                        <option>Single</option>
                                        <option>Married</option>
                                        <option>Divorced</option>
                                        <option>Separated</option>
                                        <option>Widowed</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">Address: <span class="text-danger patient_address"></span></td>
                            </tr>
                            <tr>
                                <td colspan="3">PhilHealth status:
                                    <label>None <input type="radio" name="phic_status" value="None" checked></label>
                                    <label>Member <input type="radio" name="phic_status" value="Member"></label>
                                    <label>Dependent <input type="radio" name="phic_status" value="Dependent"></label>
                                </td>
                                <td colspan="3">PhilHealth #: <input type="text" class="text-danger form-control phic_id" name="phic_id" /> </td>
                            </tr>
                            <!--
                            <tr>
                                <td colspan="6">
                                    Diagnosis/Impression: <small class="text-primary">(Auto search from ICD10)</small>
                                    <input type="text" value="" id="icd_code_walkin" name="icd_code_walkin" readonly><br>
                                    <textarea class="form-control" onkeyup="Icd10Checker_walkin($(this))" id="diagnosis_walkin" rows="4" name="diagnosis" style="resize: none;width: 100%;margin-top: 1%" required></textarea>
                                    <br />
                                </td>
                            </tr>
                            -->
                            <tr>
                                <td colspan="6">
                                    Case Summary (pertinent Hx/PE, including meds, labs, course etc.):<br />
                                    <textarea class="form-control" name="case_summary" style="resize: none;width: 100%;" rows="7" required></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist):
                                    <br />
                                    <textarea class="form-control" name="reco_summary" style="resize: none;width: 100%;" rows="7" required></textarea>
                                </td>
                            </tr>
                            <!-- tr>
                                <td colspan="6">
                                    Diagnosis/Impression:
                                    <br />
                                    <textarea class="form-control" rows="7" name="diagnosis" style="resize: none;width: 100%;margin-top: 1%" required></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    Reason for referral:
                                    <br />
                                    <textarea class="form-control reason_referral" name="reason" style="resize: none;width: 100%;" rows="7" required></textarea>
                                </td>
                            </tr> -->
                            <tr>
                                <td colspan="6">
                                    <span class="text-success">Diagnosis</span>
                                    <span class="text-red">*</span><br><br>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="clearIcd()">Clear ICD-10</button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="clearOtherDiagnoses()">Clear other Diagnosis</button>
                                    <button type="button" class="btn btn-sm btn-info" onclick="clearNotesDiagnoses()">Clear notes diagnosis</button>
                                    <a data-toggle="modal" data-target="#modal-icd" type="button" class="btn btn-sm btn-success" onclick="searchICD()">
                                        <i class="fa fa-medkit"></i> Add ICD-10
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success" onclick="addNotesDiagnoses()"><i class="fa fa-plus"></i> Add Notes in Diagnosis
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div id="selected_icd"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div id="add_diagnosis_notes"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div id="other_diagnosis"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <span class="text-success">Reason for Referral: </span>
                                    <span class="text-red">*</span><br>
                                    <select name="reason_referral1" class="form-control-select select2 reason_referral" style="width: 100%" required>
                                        <option value="">Select reason for referral</option>
                                        <option value="-1">Other reason for referral</option>
                                        @foreach($referral_reasons as $reason)
                                            <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                                        @endforeach
                                    </select><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div id="other_referral_reason"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Name of referred <br>
                                            <small class="text-success">MD/HCW- Mobile Contact # (ReCo):</small>
                                        </div>
                                        <div class="col-md-8">
                                            <select name="referred_md" class="referred_md select2 form-control-select" style="width: 100%;">
                                                <option value="">Any...</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Back</button>
                        <button type="submit" class="btn btn-success btn-flat btn-submit"><i class="fa fa-send"></i> Submit</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal-icd">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
                <h4 class="modal-title" style="font-size: 17pt;">Search ICD-10 by keyword:</h4>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-lg">
                    <input type="text" id="icd10-keyword" class="form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat" onclick="searchICD()">Find</button>
                    </span>
                </div><br>
                <div class="body_icd"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="otherDiagnoses()">Other Diagnosis</button>
                <button type="button" class="btn btn-success" onclick="getAllCheckbox()"><i class="fa fa-save"></i> Save selected check</button>
            </div>
        </div>
    </div>
</div>

<script>
    function clearIcd(){
        $('#selected_icd').html("");
    }

    function clearOtherDiagnoses(){
        $("#other_diagnosis").html("");
    }

    function clearNotesDiagnoses(){
        $("#add_diagnosis_notes").html("");
    }

    function clearOtherReferralReason(){
        $("#other_referral_reason").html("");
    }

    function addNotesDiagnoses(){
        $("#add_diagnosis_notes").html(loading);
        setTimeout(function(){
            $("#add_diagnosis_notes").html('<span class="text-success">Add notes in diagnosis:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control add_notes_diagnosis" name="diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
        },500);
    }

    $('.reason_referral').on('change', function() {
        var value = $(this).val();
        if(value == '-1') {
            $("#other_referral_reason").html(loading);
            setTimeout(function(){
                $("#other_referral_reason").html('<span class="text-success">Other Reason for Referral:</span> <span class="text-red">*</span>\n' +
                    '                                <br />\n' +
                    '                                <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7" required></textarea>')
            },500);
            $("#other_referral_reason").show();
        }else{
            clearOtherReferralReason();
        }
    });

    function searchICD() {
        $("#other_diagnosis").html("");
        $(".body_icd").html(loading);
        var url = "<?php echo asset('icd/search'); ?>";
        var json = {
            "_token" : "<?php echo csrf_token(); ?>",
            "icd_keyword" : $("#icd10-keyword").val()
        };
        $.post(url,json,function(result){
            setTimeout(function(){
                $(".body_icd").html(result);
            },500);
        });
    }

    function getAllCheckbox() {
        $('#modal-icd').modal('toggle');
        $("#selected_icd").html("");
        var values = [];

        $('input[name="icd_checkbox[]"]:checked').each(function () {
            values[values.length] = (this.checked ? $(this).parent().parent().siblings("td").eq(1).text() : "");
            var icd_description = $(this).parent().parent().siblings("td").eq(1).text();
            var id = $(this).val();
            if(this.checked){
                $("#selected_icd").append('<span class="text-green">'+'=> '+icd_description+' '+'</span><br><input type="hidden" name="icd_ids[]" value="'+id+'">');
            }
        });
        console.log(values);
    }

    function otherDiagnoses(){
        $('#modal-icd').modal('hide');
        $("#other_diagnosis").html(loading);
        setTimeout(function(){
            $("#other_diagnosis").html('<span class="text-success">Other diagnosis:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control reason_referral" name="other_diagnoses" style="resize: none;width: 100%;" rows="7" required></textarea>')
        },500);
    }
</script>