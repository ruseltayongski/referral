<?php
    $user = Session::get('auth');
    $myfacility = \App\Facility::find($user->facility_id);
    $facilities = \App\Facility::select('id','name')
        ->where('id','!=',$user->facility_id)
        ->where('status',1)
        ->where('referral_used','yes')
        ->orderBy('name','asc')->get();
    $reason_for_referral = \App\ReasonForReferral::get();
?>
<div class="modal fade" role="dialog" id="pregnantFormModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" class="form-submit pregnant_form">
            <div class="jim-content">
                @include('include.header_form')
                <div class="form-group-sm form-inline">
                {{ csrf_field() }}
                    <input type="hidden" name="patient_id" class="patient_id" value="" />
                    <input type="hidden" name="date_referred" class="date_referred" value="{{ date('Y-m-d H:i:s') }}" />
                    <input type="hidden" name="code" value="" />
                    <input type="hidden" name="source" value="{{ $source }}" />
                    <input type="hidden" class="referring_name" value="{{ $myfacility->name }}" />
                    <table class="table table-striped">
                        <tr>
                            <th colspan="4">REFERRAL RECORD</th>
                        </tr>
                        <tr>
                            <td>Who is Referring: </td>
                            <td>Record Number: <input type="text" class="form-control" name="record_no" placeholder="" /></td>
                            <td>Referred Date: <span class="text-success">{{ date('l M d, Y') }}</span> </td>
                            <td>Time: <span class="text-success">{{ date('h:i A') }}</span> </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Name of referring MD/HCW: <span class="text-success">Name Dr. {{ $user->fname }} {{ $user->mname }} {{ $user->lname }}</span>
                            </td>
                            <td>Arrival Date</td>
                            <td>Time</td>
                        </tr>
                        <tr>
                            <td colspan="4">Referring Facility: <span class="text-success">{{ $myfacility->name }}</span> </td>
                        </tr>
                        <tr>
                            <td colspan="4">Accompanied by the Health Worker: <input type="text" class="form-control" name="health_worker" style="width: 50%;" placeholder="Name of Health Worker"/> </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="row">
                                    <div class="col-md-2">
                                        Referred to:
                                    </div>
                                    <div class="col-md-6">
                                        <select name="referred_facility" class="form-control-select select2 select_facility" style="width: 100%" required>
                                            <option value="">Select Facility...</option>
                                            @foreach($facilities as $row)
                                                <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="referred_department" class="form-control select_department select_department_pregnant" required>
                                            <option value="">Select Department...</option>
                                        </select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="row">
                                    <div class="col-md-2">
                                        Address:
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-primary facility_address"></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="row">
                        <div class="col-md-4">
                            <small >Covid Number</small><br>
                            <input type="text" name="covid_number" style="width: 100%;">
                        </div>
                        <div class="col-md-4">
                            <small >Clinical Status</small><br>
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
                            <small >Surveillance Category</small><br>
                            <select name="sur_category" id="" class="form-control-select" style="width: 100%;">
                                <option value="">Select option</option>
                                <option value="contact_pum">Contact (PUM)</option>
                                <option value="suspect">Suspect</option>
                                <option value="probable">Probable</option>
                                <option value="confirmed">Confirmed</option>
                            </select>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table bg-warning">
                                <tr class="bg-gray">
                                    <th colspan="4">WOMAN</th>
                                </tr>
                                <tr>
                                    <td colspan="3">Name: <span class="text-danger patient_name"></span></td>
                                    <td>Age: <span class="text-danger patient_age"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="4">Address: <span class="text-danger patient_address"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        Main Reason for Referral
                                        <label><input type="radio" name="woman_reason" value="None" checked /> None </label>
                                        <label><input type="radio" name="woman_reason" value="Emergency" /> Emergency </label>
                                        <label><input type="radio" name="woman_reason" value="Non-Emergency" /> Non-Emergency </label>
                                        <label><input type="radio" name="woman_reason" value="To accompany the baby" /> To accompany the baby </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">Major Findings (Clinical and BP,Temp,Lab)
                                        <br />
                                        <textarea class="form-control" name="woman_major_findings" style="resize: none;width: 100%" rows="5" required></textarea>
                                    </td>
                                </tr>
                                <tr class="bg-gray">
                                    <td colspan="4">Treatments Give Time</td>
                                </tr>
                                <tr>
                                    <td colspan="4">Before Referral
                                        <br />
                                        <input type="text" class="form-control" name="woman_before_treatment" placeholder="Treatment Given" />
                                        <input type="text" class="form-control form_datetime" name="woman_before_given_time" placeholder="Date/Time Given" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">During Transport
                                        <br />
                                        <input type="text" class="form-control" name="woman_during_treatment" placeholder="Treatment Given" />
                                        <input type="text" class="form-control form_datetime" name="woman_during_given_time" placeholder="Date/Time Given" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">Information Given to the Woman and Companion About the Reason for Referral
                                        <br />
                                        <textarea class="form-control woman_information_given" name="woman_information_given" style="resize: none;width: 100%" rows="5" required></textarea>
                                    </td>
                                </tr>
                                <tr>
                                     <td colspan="6">
                                        <span class="text-success">Diagnosis</span> <span class="text-red">*</span>
                                        <br><br>
                                        <a data-toggle="modal" data-target="#icd-modal-pregnant" type="button" class="btn btn-sm btn-success" onclick="searchICD10Pregnant()">
                                            <i class="fa fa-medkit"></i>  Add ICD-10
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addNotesDiagnosisPregnant()" hidden="hidden"><i class="fa fa-plus"></i> Add notes in diagnosis</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <button type="button" id="clear_icd_pregnant" class="btn btn-sm btn-danger" onclick="clearICDPregnant()" hidden="hidden"> Clear ICD-10</button>
                                        <div><span class="text-green" id="icd_selected_pregnant"></span></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <button type="button" id="clear_notes_pregnant" class="btn btn-sm btn-info" onclick="clearNotesDiagnosisPregnant()" hidden="hidden"> Clear notes diagnosis</button>
                                        <div id="add_notes_diagnosis_pregnant">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <button type="button" id="clear_other_diag_pregnant" class="btn btn-sm btn-warning" onclick="clearOtherDiagnosisPregnant()" hidden="hidden"> Clear other diagnosis</button>
                                        <div id="others_diagnosis_pregnant">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="text-success">Reason for referral:</span> <span class="text-red">*</span>
                                    <select name="reason_referral1" class="form-control-select select2 reason_referral" style="width: 100%" required="">
                                        <option value="">Select reason for referral</option>
                                        <option value="-1">Other reason for referral</option>
                                        @foreach($reason_for_referral as $reason_referral)
                                            <option value="{{ $reason_referral->id }}">{{ $reason_referral->reason }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="other_reason_referral_pregnant"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <table class="table bg-warning">
                                <tr class="bg-gray">
                                    <th colspan="4">BABY</th>
                                </tr>
                                <tr>
                                    <td colspan="2">Name :<br />
                                        <input type="text" class="form-control" style="width: 100%" name="baby_fname" placeholder="First Name" /><br />
                                        <input type="text" class="form-control" style="width: 100%" name="baby_mname" placeholder="Middle Name" /><br />
                                        <input type="text" class="form-control" style="width: 100%" name="baby_lname" placeholder="Last Name" />
                                    </td>
                                    <td style="vertical-align: top !important;">Date and Hour of Birth:
                                        <br />
                                        <input type="text" class="form-control  form_datetime" style="width: 100%" name="baby_dob" placeholder="Date/Time" /><br />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Birth Weight: <input type="text" class="form-control" style="width: 100%" name="baby_weight" placeholder="kg or lbs" /><br /></td>
                                    <td>Gestational Age: <input type="text" class="form-control" style="width: 100%" name="baby_gestational_age" placeholder="age" /><br /></td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        Main Reason for Referral
                                        <label><input type="radio" name="baby_reason" value="None" checked /> None </label>
                                        <label><input type="radio" name="baby_reason" value="Emergency" /> Emergency </label>
                                        <label><input type="radio" name="baby_reason" value="Non-Emergency" /> Non-Emergency </label>
                                        <label><input type="radio" name="baby_reason" value="To accompany the mother" /> To accompany the mother </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">Major Findings (Clinica and BP,Temp,Lab)
                                        <br />
                                        <textarea class="form-control" name="baby_major_findings" style="resize: none;width: 100%" rows="5"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">Last (Breast) Feed (Time): <input type="text" class="form-control form_datetime" style="width: 100%" name="baby_last_feed" placeholder="Date/Time" /><br /></td>
                                </tr>
                                <tr class="bg-gray">
                                    <td colspan="4">Treatments Give Time</td>
                                </tr>
                                <tr>
                                    <td colspan="4">Before Referral
                                        <br />
                                        <input type="text" class="form-control" name="baby_before_treatment" placeholder="Treatment Given" />
                                        <input type="text" class="form-control form_datetime" name="baby_before_given_time" placeholder="Date/Time Given" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">During Transport
                                        <br />
                                        <input type="text" class="form-control" name="baby_during_treatment" placeholder="Treatment Given" />
                                        <input type="text" class="form-control form_datetime" name="baby_during_given_time" placeholder="Date/Time Given" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">Information Given to the Woman and Companion About the Reason for Referral
                                        <br />
                                        <textarea class="form-control" name="baby_information_given" style="resize: none;width: 100%" rows="5"></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
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
                <table class="table table-striped col-sm-6"></table>
                <hr />
                     <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Back</button>
                        <button type="submit" id="sbmtPreg" class="btn btn-success btn-flat btn-submit"><i class="fa fa-send"></i> Submit</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
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

<div class="modal fade" id="icd-modal-pregnant">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title" style="font-size: 17pt;">Search ICD-10 by keyword</h4>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-lg">
                    <input type="text" id="icd10_keyword_pregnant" class="form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat" onclick="searchICD10Pregnant()">Find</button>
                    </span>
                </div><br>
                <div class="icd_body_pregnant"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="othersDiagnosisPregnant()"> Other Diagnosis</button>
                <button type="button" class="btn btn-success" onclick="getAllCheckBoxPregnant()"><i class="fa fa-save"></i> Save selected check</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    $("#clear_icd_pregnant").hide();
    $("#clear_notes_pregnant").hide();
    $("#clear_other_diag_pregnant").hide();

    $("#sbmtPreg").on('click',function(e){
        if(!($("#icd_preg").val()) && !($("#other_diag_preg").val())){
            Lobibox.alert("error", {
                msg: "Select ICD-10 diagnosis!"
            });
            return false;
        }
    });

    function clearICDPregnant() {
        $("#icd_selected_pregnant").html("");
        $("#clear_icd_pregnant").hide();
    }

    function clearOtherDiagnosisPregnant() {
        $("#others_diagnosis_pregnant").html("");
        $("#clear_other_diag_pregnant").hide();
    }

    function clearNotesDiagnosisPregnant() {
        $("#add_notes_diagnosis_pregnant").html("");
        $("#clear_notes_pregnant").hide();
    }

    function clearOtherReasonReferralPregnant() {
        $("#other_reason_referral_pregnant").html("");
    }

    function addNotesDiagnosisPregnant() {
        $("#add_notes_diagnosis_pregnant").html(loading);
        $("#clear_notes_pregnant").show();
        setTimeout(function(){
            $("#add_notes_diagnosis_pregnant").html('<span class="text-success">Add notes in diagnosis:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control add_notes_diagnosis" name="notes_diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
        },500);
    }

    $('.reason_referral').on('change', function() {
        var value = $(this).val();
        if(value == '-1') {
            $("#other_reason_referral_pregnant").html(loading);
            setTimeout(function(){
                $("#other_reason_referral_pregnant").html('<span class="text-success">Other Reason for Referral:</span> <span class="text-red">*</span>\n' +
                    '                                <br />\n' +
                    '                                <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7" required></textarea>')
            },500);
            $("#other_reason_referral_pregnant").show();
        }else{
            clearOtherReasonReferralPregnant();
        }
    });

    function searchICD10Pregnant() {
        $("#others_diagnosis_pregnant").html("");
        $(".icd_body_pregnant").html(loading);
        var url = "<?php echo asset('icd/search'); ?>";
        var json = {
            "_token" : "<?php echo csrf_token(); ?>",
            "icd_keyword" : $("#icd10_keyword_pregnant").val()
        };
        $.post(url,json,function(result){
            setTimeout(function(){
                $(".icd_body_pregnant").html(result);
            },500);
        });
    }

    function getAllCheckBoxPregnant() {
        $('#icd-modal-pregnant').modal('toggle');
        $("#clear_icd_pregnant").show();
        var values = [];

        $('input[name="icd_checkbox[]"]:checked').each(function () {
            values[values.length] = (this.checked ? $(this).parent().parent().siblings("td").eq(1).text() : "");
            var icd_description = $(this).parent().parent().siblings("td").eq(1).text();
            var id = $(this).val();
            if(this.checked){
                $("#icd_selected_pregnant").append('=> '+icd_description+' '+'<br><input id="icd_preg" type="hidden" name="icd_ids[]" value="'+id+'">');
            }
        });
        console.log(values);
    }

    function othersDiagnosisPregnant(){
        $('#icd-modal-pregnant').modal('hide');
        $("#others_diagnosis_pregnant").html(loading);
        $("#clear_other_diag_pregnant").show();
        setTimeout(function(){
            $("#others_diagnosis_pregnant").html('<span class="text-success">Other diagnosis:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control reason_referral" id="other_diag_preg" name="other_diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
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