<?php
    $user = Session::get('auth');
    $facilities = \App\Facility::select('id','name')
        ->where('id','!=',$user->facility_id)
        ->where('province',$user->province)
        ->where('status',1)
        ->orderBy('name','asc')->get();
    $myfacility = \App\Facility::find($user->facility_id);
    $facility_address = \App\Http\Controllers\LocationCtrl::facilityAddress($myfacility->id);
?>
<div class="modal fade" role="dialog" id="normalFormModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ url('doctor/patient') }}" method="POST" class="form-submit normal_form">
            <div class="jim-content">
                <div class="title-form">CENTRAL VISAYAS HEALTH REFERRAL SYSTEM<br /><small>Clinical Referral Form</small></div>
                <div class="form-group-sm form-inline">
                {{ csrf_field() }}
                <input type="hidden" name="patient_id" class="patient_id" value="" />
                <input type="hidden" name="date_referred" class="date_referred" value="{{ date('Y-m-d H:i:s') }}" />
                <input type="hidden" name="code" value="" />
                <input type="hidden" name="source" value="{{ $source }}" />
                <input type="hidden" class="referring_name" value="{{ $myfacility->name }}" />
                <table class="table table-striped">
                    <tr>
                        <td colspan="6">
                            Name of Referring Facility: <span class="text-success">{{ $myfacility->name }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">Address: <span class="text-success">{{ $facility_address['address'] }}</span> </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            Referred to:
                            <select name="referred_facility" class="form-control-select select_facility" style="padding: 3px" required>
                                <option value="">Select Facility...</option>
                                @foreach($facilities as $row)
                                    <option data-name="{{ $row->name }}" value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            <select name="referred_department" class="form-control-select select_department select_department_normal" style="padding: 3px" required>
                                <option value="">Select Department...</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">Address: <span class="text-primary facility_address"></span></td>
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
                    <tr>
                        <td colspan="6">
                            Diagnosis/Impression:
                            <br />
                            <textarea class="form-control" name="diagnosis" style="resize: none;width: 100%;" rows="7" required></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            Reason for referral:
                            <br />
                            <textarea class="form-control reason_referral" name="reason" style="resize: none;width: 100%;" rows="7" required></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            Name of referring MD/HCW:
                            <span class="text-success">Dr. {{ $user->fname }} {{ $user->mname }} {{ $user->lname }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            Name of referred MD/HCW- Mobile Contact # (ReCo):
                            <select name="referred_md" class="referred_md form-control-select" style="padding: 3px">
                                <option value="">Any...</option>
                            </select>
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