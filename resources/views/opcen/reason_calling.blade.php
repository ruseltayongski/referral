@if($reason == 'inquiry' || $reason == 'others')
<table class="table table-hover table-bordered">
    <tr>
        <td >
            <small>Notes</small>
            <textarea name="reason_notes" id="" cols="30" rows="5" class="form-control">{{ Session::get("client")->reason_notes }}</textarea>
        </td>
    </tr>
    <tr>
        <td >
            <small>Notes for action taken</small>
            <textarea name="reason_action_taken" id="" cols="30" rows="5" class="form-control">{{ Session::get("client")->reason_action_taken }}</textarea>
        </td>
    </tr>
</table>
@else
    <table class="table table-hover table-bordered" style="width: 100%">
        <tr>
            <td >
                <small>Patient Data(Name,Age,Gender)</small>
                <textarea name="reason_patient_data" id="" cols="30" rows="5" class="form-control">{{ Session::get("client")->reason_patient_data }}</textarea>
            </td>
            <td >
                <small>Chief Complains</small>
                <textarea name="reason_chief_complains" id="" cols="30" rows="5" class="form-control">{{ Session::get("client")->reason_chief_complains }}</textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <small>Notes for action taken</small>
                <textarea name="reason_action_taken" id="" cols="30" rows="5" class="form-control">{{ Session::get("client")->reason_action_taken }}</textarea>
            </td>
        </tr>
    </table>
@endif