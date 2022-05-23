<?php
$user = Session::get('auth');
$facilities = \App\Facility::select('id','name')
    ->where('id','!=',$user->facility_id)
    //->where('province',$user->province)
    ->where('status',1)
    ->where('referral_used','yes')
    ->orderBy('name','asc')->get();
?>

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
                <form method="post" id="redirectedForm">
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
                        <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-ambulance"></i> Redirected</button>
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
                <form method="post" id="referAcceptForm">
                    {{ csrf_field() }}
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
                        <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-ambulance"></i> Refer</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


