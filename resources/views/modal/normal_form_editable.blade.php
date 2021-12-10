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
                        </div>
                        <table class="table table-striped">
                            <!--
                            <tr>
                                <td colspan="6">
                                    Diagnosis/Impression: <small class="text-primary">(Auto search from ICD10)</small>
                                    <input type="text" value="" id="icd_code" name="icd_code" readonly><br>
                                    <textarea class="form-control" onkeyup="Icd10Checker($(this))" id="diagnosis" rows="4" name="diagnosis" style="resize: none;width: 100%;margin-top: 1%" required></textarea>
                                </td>
                            </tr>
                            -->
                            <tr>
                                <td colspan="6">
                                    <span class="text-success">Case Summary (pertinent Hx/PE, including meds, labs, course etc.):</span> <span class="text-red">*</span><br />
                                    <textarea class="form-control" name="case_summary" style="resize: none;width: 100%;" rows="7" required></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <span class="text-success">Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist):</span> <span class="text-red">*</span>
                                    <br />
                                    <textarea class="form-control" name="reco_summary" style="resize: none;width: 100%;" rows="7" required></textarea>
                                </td>
                            </tr>
                            {{--<tr>
                                <td colspan="6">
                                    <span class="text-success">
                                        @if(Session::get('auth')->level == 'opcen')
                                            Chief Complaints
                                        @else
                                            Diagnosis/Impression:
                                        @endif
                                    </span> <span class="text-red">*</span>
                                    <br />
                                    <textarea class="form-control" rows="7" name="diagnosis" style="resize: none;width: 100%;margin-top: 1%" required></textarea>
                                </td>
                            </tr>--}}
                            <tr>
                                <td colspan="6">
                                    <span class="text-success">Diagnosis</span> <span class="text-red">*</span>
                                    <br><br>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="clearICD()"> Clear ICD-10</button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="clearOtherDiagnosis()"> Clear other diagnosis</button>
                                    <button type="button" class="btn btn-sm btn-info" onclick="clearNotesDiagnosis()"> Clear notes diagnosis</button>
                                    <button type="button" class="btn btn-sm btn-success" onclick="addNotesDiagnosis()"><i class="fa fa-plus"></i> Add notes in diagnosis</button>
                                    <a data-toggle="modal" data-target="#icd-modal" type="button" class="btn btn-sm btn-success" onclick="searchICD10()">
                                        <i class="fa fa-medkit"></i> Add ICD-10
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div id="icd-selected">

                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div id="add_notes_diagnosis">

                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div id="others-diagnosis">

                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <span class="text-success">Reason for referral:</span> <span class="text-red">*</span>
                                    <br>
                                    <select name="" class="form-control-select select2" style="width: 100%" required>
                                        <option value="">Select reason for referral</option>
                                        @foreach($reason_for_referral as $reason_referral)
                                            <option value="{{ $reason_referral->id }}">{{ $reason_referral->reason }}</option>
                                        @endforeach
                                    </select><br><br>
                                    <button type="button" class="btn btn-sm btn-info" onclick="clearOtherReasonReferral()"> Clear other reason for referral</button>
                                    <button type="button" class="btn btn-sm btn-success" onclick="addOtherReasonReferral()"><i class="fa fa-plus"></i> Other reason for referral</button>
                                    <!--
                                    <textarea class="form-control reason_referral" name="reason" style="resize: none;width: 100%;" rows="7" required></textarea>
                                    -->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div id="other_reason_referral">

                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <span class="text-success">Name of referred: (MD/HCW- Mobile Contact # (ReCo))</span><br>
                                    <select name="reffered_md" class="referred_md form-control-select select2" style="width: 100%">
                                        <option value="">Any...</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div class="file-upload">
                                        <!--
                                        <button class="file-upload-btn" type="button" onclick="$('.file-upload-input').trigger( 'click' )">Add Image</button>
                                        -->
                                        <div class="image-upload-wrap">
                                            <input class="file-upload-input" type='file' onchange="readURL(this);" accept="image/*" />
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
    function clearICD() {
        $("#icd-selected").html("");
    }

    function clearOtherDiagnosis() {
        $("#others-diagnosis").html("");
    }

    function clearNotesDiagnosis() {
        $("#add_notes_diagnosis").html("");
    }

    function clearOtherReasonReferral() {
        $("#other_reason_referral").html("");
    }

    function addNotesDiagnosis() {
        $("#add_notes_diagnosis").html(loading);
        setTimeout(function(){
            $("#add_notes_diagnosis").html('<span class="text-success">Notes diagnosis:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control add_notes_diagnosis" name="reason" style="resize: none;width: 100%;" rows="7" required></textarea>')
        },500);
    }

    function addOtherReasonReferral() {
        $("#other_reason_referral").html(loading);
        setTimeout(function(){
            $("#other_reason_referral").html('<span class="text-success">Other Reason for Referral:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control" name="other_reason_referral" style="resize: none;width: 100%;" rows="7" required></textarea>')
        },500);
    }

    function searchICD10() {
        $("#others-diagnosis").html("");
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
        $("#icd-selected").html("");
        var values = [];
        $('input[name="icd-checkbox[]"]:checked').each(function () {
            values[values.length] = (this.checked ? $(this).parent().parent().siblings("td").eq(1).text() : "");
            var icd_description = $(this).parent().parent().siblings("td").eq(1).text();
            if(this.checked){
                $("#icd-selected").append('<span class="text-green">'+'=> '+icd_description+'</span><br>');
            }
        });
        console.log(values);
    }

    function reasonReferralCheck(data) {
        if(data.val() == 'others') {
            $("#reason_referral_others_body").html(loading);
            setTimeout(function(){
                $("#reason_referral_others_body").html('<span class="text-success">Reason for referral others:</span> <span class="text-red">*</span>\n' +
                    '                                <br />\n' +
                    '                                <textarea class="form-control reason_referral" name="reason" style="resize: none;width: 100%;" rows="7" required></textarea>')
            },500);
        } else {
            $("#reason_referral_others_body").html("");
        }
    }

    function othersDiagnosis(){
        $('#icd-modal').modal('hide');
        $("#others-diagnosis").html(loading);
        setTimeout(function(){
            $("#others-diagnosis").html('<span class="text-success">Others diagnosis:</span> <span class="text-red">*</span>\n' +
                '                                <br />\n' +
                '                                <textarea class="form-control reason_referral" name="reason" style="resize: none;width: 100%;" rows="7" required></textarea>')
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
