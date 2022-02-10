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
                                <small class="text-success"><b>NAME OF REFERRING FACILITY:</b></small><br>
                                &nbsp;<span>{{ $myfacility->name }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success"><b>ADDRESS:</b></small><br>
                                &nbsp;<span >{{ $facility_address['address'] }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success"><b>NAME OF REFERRING MD/HCW:</b></small><br>
                                &nbsp;<span >Dr. {{ $user->fname }} {{ $user->mname }} {{ $user->lname }}</span>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-success"><b>DATE/TIME REFERRED (ReCo):</b></small><br>
                                &nbsp;<span >{{ date('l F d, Y h:i A') }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success"><b>NAME OF PATIENT:</b></small><br>
                                &nbsp;<span class="patient_name"></span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success"><b>ADDRESS:</b></small><br>
                                &nbsp;<span class="patient_address"></span>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-success"><b>REFERRED TO:</b></small> &nbsp;<span class="text-red">*</span><br>
                                <select name="referred_facility" class="select2 select_facility form-control" required>
                                    <option value="">Select Facility...</option>
                                    @foreach($facilities as $row)
                                        <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success"><b>DEPARTMENT:</b></small> <span class="text-red">*</span><br>
                                <select name="referred_department" class="form-control select_department select_department_normal" style="width: 100%;" required>
                                    <option value="">Select Option</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small class="text-success"><b>ADDRESS:</b></small><br>
                                &nbsp;<span class="text-yellow facility_address"></span>
                            </div>
                        </div><br>

                        <div class="web-view">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-success"><b>AGE: </b></small><br>
                                    &nbsp;<span class="patient_age"></span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-success"><b>SEX: </b></small> <span class="text-red">*</span><br>
                                    <select name="patient_sex" class="patient_sex form-control" style="width: 100%;" required>
                                        <option value="">Select...</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-success"><b>CIVIL STATUS: </b></small> <span class="text-red">*</span><br>
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
                        </div>

                        <div class="mobile-view">
                            <table class="table">
                                <tr>
                                    <td>
                                        <small class="text-success"><b>AGE: </b></small><br>
                                        &nbsp;<span class="patient_age"></span>
                                    </td>
                                    <td>
                                        <small class="text-success"><b>SEX: </b></small> <span class="text-red">*</span><br>
                                        <select name="patient_sex" class="patient_sex form-control" style="width: 100%;" required>
                                            <option value="">Select...</option>
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                    </td>
                                    <td>
                                        <small class="text-success"><b>CIVIL STATUS: </b></small> <span class="text-red">*</span><br>
                                        <select name="civil_status" style="width: 100%;" class="civil_status form-control" required>
                                            <option value="">Select...</option>
                                            <option>Single</option>
                                            <option>Married</option>
                                            <option>Divorced</option>
                                            <option>Separated</option>
                                            <option>Widowed</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-success"><b>COVID NUMBER</b></small><br>
                                <input type="text" class="form-control" name="covid_number" style="width: 100%;">
                            </div>
                            <div class="col-md-4">
                                <small class="text-success"><b>CLINICAL STATUS</b></small><br>
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
                                <small class="text-success"><b>SURVEILLANCE CATEGORY</b></small><br>
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
                            <div class="col-md-12">
                                <small class="text-success"><b>CASE SUMMARY:</b> <i>(pertinent Hx/PE, including meds, labs, course etc.)</i></small> <span class="text-red">*</span><br />
                                <textarea class="form-control" name="case_summary" style="resize: none;width: 100%;" rows="7" required></textarea>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-success"><b>SUMMARY OF RECO:</b> <i>(pls. refer to ReCo Guide in Referring Patients Checklist)</i></small> <span class="text-red">*</span><br />
                                <textarea class="form-control" name="reco_summary" style="resize: none;width: 100%;" rows="7" required></textarea>
                            </div>
                        </div><br>

                        {{--<div class="row">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<span class="text-success">--}}
                                {{--@if(Session::get('auth')->level == 'opcen')--}}
                                {{--Chief Complaints--}}
                                {{--@else--}}
                                {{--Diagnosis/Impression:--}}
                                {{--@endif--}}
                                {{--</span> <span class="text-red">*</span>--}}
                                {{--<br />--}}
                                {{--<textarea class="form-control" rows="7" name="diagnosis" style="resize: none;width: 100%;margin-top: 1%" required></textarea>--}}
                            {{--</div>--}}
                        {{--</div>--}}

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
                        </div><br>

                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-success"><b>REASON FOR REFERRAL:</b></small> <span class="text-red">*</span><br>
                                <select name="reason_referral1" class="form-control-select select2 reason_referral" style="width: 100%" required="">
                                    <option value="">Select reason for referral</option>
                                    <option value="-1">Other reason for referral</option>
                                    @foreach($reason_for_referral as $reason_referral)
                                        <option value="{{ $reason_referral->id }}">{{ $reason_referral->reason }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row" style="padding-top: 10px">
                            <div class="col-md-12">
                                <div id="other_reason_referral"></div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-success"><b>NAME OF REFERRED:</b> <i>(MD/HCW- Mobile Contact # (ReCo))</i></small><br>
                                <select name="reffered_md" class="referred_md form-control-select select2" style="width: 100%">
                                    <option value="">Any...</option>
                                </select>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-success"><b>FILE ATTACHMENT:</b></small><br>
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
                        </div>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Back</button>
                        <button type="submit" id="sbmitBtn" class="btn btn-success btn-flat btn-submit"><i class="fa fa-send"></i> Submit</button>
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

<script>
    $('#clear_icd, #clear_notes, #clear_other_diag, #icd_selected').hide();

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
        $("#clear_icd, #icd_selected").hide();
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
            $("#add_notes_diagnosis").html('<small class="text-success">ADD NOTES IN DIAGNOSIS:</small> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control add_notes_diagnosis" name="diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>')
        },500);
    }

    $('.reason_referral').on('change', function() {
        var value = $(this).val();
        if(value == '-1') {
            $("#other_reason_referral").html(loading);
            setTimeout(function(){
                $("#other_reason_referral").html('<small class="text-success">OTHER REASON FOR REFERRAL:</small> <span class="text-red">*</span>\n' +
                    '                                <br />\n' +
                    '                                <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7" required></textarea>')
            },500);
            $("#other_reason_referral").show();
        }else{
            clearOtherReasonReferral();
        }
    });

    function searchICD10() {
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
        $('#clear_icd, #icd_selected').show();
        var values = [];

        $('input[name="icd_checkbox[]"]:checked').each(function () {
            values[values.length] = (this.checked ? $(this).parent().parent().siblings("td").eq(1).text() : "");
            var icd_description = $(this).parent().parent().siblings("td").eq(1).text();
            var id = $(this).val();
            if(this.checked){
                $("#icd_selected").append('=> '+icd_description+' '+'<br><input id="icd" type="hidden" name="icd_ids[]" value="'+id+'">');
                console.log("icd id: " + id);
            }
            clearOtherDiagnosis();
        });
        console.log(values);
    }

    function othersDiagnosis(){
        $('#icd-modal').modal('hide');
        $("#others_diagnosis").html(loading);
        $("#clear_other_diag").show();
        setTimeout(function(){
            $("#others_diagnosis").html('<small class="text-success">OTHER DIAGNOSIS:</small> <span class="text-red">*</span>\n' +
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
