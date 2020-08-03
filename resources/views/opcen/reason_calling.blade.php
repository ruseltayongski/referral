@if($reason == 'inquiry' || $reason == 'others')
<table class="table table-hover table-bordered">
    <tr>
        <td >
            <small>@if(Session::get("client")->reason_notes) Previous Notes @else Notes @endif</small>
            <textarea name="reason_notes" id="" cols="30" rows="5" class="form-control" <?php if(Session::get("client")->reason_notes) echo 'readonly';?>>{{ Session::get("client")->reason_notes }}</textarea>
        </td>
    </tr>
    @if(Session::get("client")->reason_notes)
    <tr>
        <td >
            <small>Notes</small>
            <textarea name="reason_notes1" id="" cols="30" rows="5" class="form-control"></textarea>
        </td>
    </tr>
    @endif
    <tr>
        <td >
            <small>@if(Session::get("client")->reason_notes) Previous Notes for action taken @else Notes for action taken @endif</small>
            <textarea name="reason_action_taken" id="" cols="30" rows="5" class="form-control" <?php if(Session::get("client")->reason_notes) echo 'readonly';?>>{{ Session::get("client")->reason_notes }}</textarea>
        </td>
    </tr>
    @if(Session::get("client")->reason_action_taken)
        <tr>
            <td >
                <small>Notes for action taken</small>
                <textarea name="reason_action_taken1" id="" cols="30" rows="5" class="form-control"></textarea>
            </td>
        </tr>
    @endif
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
            <td >
                <small>Relationship to patient (Patient, Family & Others)</small>
                <textarea name="relationship" id="" cols="30" rows="5" class="form-control">{{ Session::get("client")->relationship }}</textarea>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <small>@if(Session::get("client")->reason_notes) Previous Notes for action taken @else Notes for action taken @endif</small>
                <textarea name="reason_action_taken" id="" cols="30" rows="5" class="form-control" <?php if(Session::get("client")->reason_notes) echo 'readonly';?>>{{ Session::get("client")->reason_notes }}</textarea>
            </td>
        </tr>
        @if(Session::get("client")->reason_action_taken)
            <tr>
                <td colspan="3">
                    <small>Notes for action taken</small>
                    <textarea name="reason_action_taken1" id="" cols="30" rows="5" class="form-control"></textarea>
                </td>
            </tr>
        @endif
    </table>
@endif