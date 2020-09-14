<?php
    $user = Session::get('auth');
    $facilities = \App\Facility::select('id','name')
        ->where('id','!=',$user->facility_id)
        ->where('status',1)
        ->orderBy('name','asc')->get();
    $myfacility = \App\Facility::find($user->facility_id);
    $facility_address = \App\Http\Controllers\LocationCtrl::facilityAddress($myfacility->id);
    $inventory = \App\Inventory::where("facility_id",$myfacility->id)->get();
?>
<div class="modal fade" role="dialog" id="normalFormModal" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ url('doctor/patient') }}" method="POST" class="form-submit normal_form">
            <div class="jim-content">
                <div style="margin-left: 58%;margin-top:10%;position: absolute;font-size: 8pt;background-color: white;" class="inventory_body">

                </div>
                <center>
                    <b>CENTRAL VISAYAS HEALTH REFERRAL SYSTEM</b><br>
                    <span style="font-size: 12pt;">Clinical Referral Form</span>
                </center>
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
                            <small>Name of Referring Facility</small><br>
                            &nbsp;<span class="text-yellow">{{ $myfacility->name }}</span>
                        </div>
                        <div class="col-md-4">
                            <small>Address</small><br>
                            &nbsp;<span class="text-yellow">{{ $facility_address['address'] }}</span>
                        </div>
                        <div class="col-md-4">
                            <small>Name of referring MD/HCW</small><br>
                            &nbsp;<span class="text-yellow">Dr. {{ $user->fname }} {{ $user->mname }} {{ $user->lname }}</span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <small>Date/Time Referred (ReCo)</small><br>
                            <span class="text-yellow">{{ date('l F d, Y h:i A') }}</span>
                        </div>
                        <div class="col-md-4">
                            <small>Name of Patient</small><br>
                            <span class="text-yellow patient_name"></span>
                        </div>
                        <div class="col-md-4">
                            <small>Address</small><br>
                            <span class="text-yellow patient_address"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <small>Referred to</small> <span class="text-red">*</span><br>
                            <select name="referred_facility" class="select2 select_facility" required>
                                <option value="">Select Facility...</option>
                                @foreach($facilities as $row)
                                    <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <small>Department</small> <span class="text-red">*</span><br>
                            <select name="referred_department" class="form-control-select select_department select_department_normal" style="width: 100%;" required>
                                <option value="">Select Option</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <small>Address</small><br>
                            <span class="text-yellow facility_address"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <small>Age</small><br>
                            <span class="text-yellow patient_age"></span>
                        </div>
                        <div class="col-md-4">
                            <small>Sex</small> <span class="text-red">*</span><br>
                            <select name="patient_sex" class="patient_sex form-control" style="width: 100%;" required>
                                <option value="">Select...</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <small>Civil Status</small> <span class="text-red">*</span><br>
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
                            <small>Covid Number</small><br>
                            <input type="text" name="covid_number" style="width: 100%;">
                        </div>
                        <div class="col-md-4">
                            <small>Clinical Status</small><br>
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
                            <small>Surveillance Category</small><br>
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
                                Case Summary (pertinent Hx/PE, including meds, labs, course etc.): <span class="text-red">*</span><br />
                                <textarea class="form-control" name="case_summary" style="resize: none;width: 100%;" rows="7" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist): <span class="text-red">*</span>
                                <br />
                                <textarea class="form-control" name="reco_summary" style="resize: none;width: 100%;" rows="7" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                Diagnosis/Impression: <span class="text-red">*</span>
                                <br />
                                <textarea class="form-control" rows="7" name="diagnosis" style="resize: none;width: 100%;margin-top: 1%" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                Reason for referral: <span class="text-red">*</span>
                                <br />
                                <textarea class="form-control reason_referral" name="reason" style="resize: none;width: 100%;" rows="7" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <div class="row">
                                    <div class="col-md-5">
                                        Name of referred :<br>
                                        <small class="text-success">MD/HCW- Mobile Contact # (ReCo)</small>
                                    </div>
                                    <div class="col-md-7">
                                        <select name="reffered_md" class="referred_md form-control-select select2" style="width: 100%">
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
