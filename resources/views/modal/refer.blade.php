<?php
$user = Session::get('auth');
$facilities = \App\Facility::select('id','name')
    ->where('id','!=',$user->facility_id)
    //->where('province',$user->province)
    ->where('status',1)
    ->where('referral_used','yes')
    ->orderBy('name','asc')->get();
?>
<style>
    #file-input {
      display: none;
    }

    #file-label {
      background-color: #3498db;
      color: #fff;
      padding: 10px 15px;
      cursor: pointer;
      display: inline-block;
    }

    #file-list {
      margin-top: 20px;
      overflow: hidden;
    }

    .preview-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      margin-top: 20px;
    }

    .preview {
      max-width: 100%;
      height: auto;
      margin: 10px;
    }
</style>
<div class="modal fade" role="dialog" id="referFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4>REFER TO OTHER FACILITY</h4>
                <hr />
                <form method="post" id="rejectForm">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding:0px;">REASON FOR REDIRECTION:</label>
                        <textarea class="form-control reject_reason" rows="5" style="resize: none;" name="remarks" required></textarea>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-send"></i> Send</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="redirectedFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;">Redirect to other facility</h4>
                <hr />
                <form method="POST" action="{{ asset("doctor/referral/redirect") }}" id="redirectedForm">
                    <input type="hidden" name="code" id="redirected_code" value="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding:0px;">SELECT FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" style="width: 100%;" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">SELECT DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_referred" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="redirected_submit" class="btn btn-success btn-flat"><i class="fa fa-ambulance"></i> Redirect</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="telemedicineRedirectedFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;">Refer Patient</h4>
                <hr />
                <form method="POST" action="{{ asset("doctor/referral/redirect") }}" id="telemedicineRedirectedForm">
                    <input type="hidden" name="code" id="telemedicine_redirected_code" value="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding:0px;">SELECT FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" style="width: 100%;" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">SELECT DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_referred" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="redirected_submit_telemedicine" class="btn btn-success btn-flat"><i class="fa fa-ambulance"></i> Refer</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="telemedicineFollowupFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;" id="followup_header"></h4>
                <hr />
                <form method="POST" action="{{ asset("api/video/followup") }}" id="telemedicineFollowupForm" enctype="multipart/form-data"><!--I add this enctype="multipart/form-data-->
                    <input type="hidden" name="code" id="telemedicine_followup_code" value="">
                    <input type="hidden" name="followup_id" id="telemedicine_followup_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" class="telemedicine" value="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding:0px;">SELECT FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" style="width: 100%;" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">SELECT DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_referred" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <!-- -----------------------Add file--------------------------- -->
                    <div class="form-group">
                        
                       <!-- <form id="upload-form" enctype="multipart/form-data"> -->
                            <label id="file-label" for="file-input" class="btn btn-primary">Select Files</label>
                            <input type="file" id="file-input" name="files[]" multiple class="d-none">
                        <!-- </form> -->
                
                        <div id="file-list" class="mt-3"></div>
                
                        <div class="preview-container" id="preview-container"></div>
                    </div>
                     <!-- -----------------------End of file--------------------------- -->
                    <!-- <div class="form-group">
                        <form id="upload-form" enctype="multipart/form-data">
                            <label id="file-label" for="file-input" class="btn btn-primary">Select Files</label>
                            <input type="file" id="file-input" name="files[]" multiple class="d-none">
                        </form>
                
                        <div id="file-list" class="mt-3"></div>
                
                        <div class="preview-container" id="preview-container"></div>
                    </div> -->
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="followup_submit_telemedicine" class="btn btn-success btn-flat"><i class="fa fa-ambulance"></i> Submit</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="referAcceptFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;">Transfer to other facility</h4>
                <hr />
                <form method="post" id="referAcceptForm" action="{{ asset("doctor/referral/transfer") }}">
                    {{ csrf_field() }}
                    <input type="hidden" id="transfer_tracking_id" name="transfer_tracking_id">
                    <div class="form-group">
                        <label style="padding:0px;">REMARKS:</label>
                        <textarea class="form-control reject_reason" rows="5" style="resize: none;" name="remarks" required></textarea>
                    </div>
                    <div class="form-group">
                        <label style="padding:0px;">FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_accept" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-flat" id="transferred_submit"><i class="fa fa-ambulance"></i> Refer</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



