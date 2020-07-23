<table class="table table-hover table-bordered" style="width: 100%">
    <tr>
        <td colspan="3">
            <div class="input-group input-group-md">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default"><b class="text-green">Client Code :</b></button>
                </div>
                <input type="text" class="form-control" value="{{ date('Y').Session::get('auth')->id.date('mdHis').sha1(time())[5] }}" readonly>
            </div>
        </td>
    </tr>
    <tr>
        <td >
            <small>Name</small>
            <input type="text" class="form-control">
        </td>
        <td >
            <small>Age</small>
            <input type="text" class="form-control">
        </td>
        <td >
            <small>Gender</small>
            <select name="" id="" class="form-control">
                <option value="">Select Option</option>
                <option value="">Male</option>
                <option value="">Female</option>
            </select>
        </td>
    </tr>
    <tr>
        <td >
            <small>Sitio</small>
            <input type="text" class="form-control">
        </td>
        <td >
            <small>Barangay:</small>
            <input type="text" class="form-control">
        </td>
        <td >
            <small>Municipality</small>
            <input type="text" class="form-control">
        </td>
    </tr>
    <tr>
        <td >
            <small>Active contact number:</small>
            <input type="text" class="form-control">
        </td>
        <td >
            <small>Relationship to patient (Patient, Family & Others)</small>
            <input type="text" class="form-control">
        </td>
    </tr>
</table>
<table class="table table-hover table-bordered">
    <tr>
        <th>Reason for calling:
            <button class="btn-xs btn-info" onclick="reasonCalling('inquiry')">Inquiry</button>
            <button class="btn-xs btn-warning" onclick="reasonCalling('referral')">Referral</button>
            <button class="btn-xs btn-success" onclick="reasonCalling('others')">Others</button>
        </th>
    </tr>
</table>
<div class="reason_calling">

</div>
<table class="table table-hover table-bordered">
    <tr>
        <th>Action taken transaction:
            <button class="btn-xs btn-primary" onclick="actionComplete()">Complete</button>
            <button class="btn-xs btn-danger" onclick="actionInComplete()">Incomplete</button>
        </th>
    </tr>
</table>
<table class="table table-hover table-bordered">
    <tr>
        <td >
            <button class="btn btn-danger" onclick="endTransaction()">End Transaction</button>
            <button class="btn btn-success">Save</button>
        </td>
        <th class="pull-right">Time Ended: <input type="text" id="time_ended" readonly></th>
    </tr>
</table>