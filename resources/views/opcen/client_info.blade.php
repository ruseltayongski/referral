<fieldset>
    <legend>
        <i class="fa fa-user-secret"></i> Client Info
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </legend>
</fieldset>
<span class="text-blue" style="font-size: 12pt;">Personal Information</span>
<table class="table table-hover table-bordered" style="width: 100%;">
    <tr>
        <td >
            <small>Reference Number</small><br>
            &nbsp;&nbsp;<span class="text-green">{{ $client->reference_number }}</span>
        </td>
        <td >
            <small>Time Started</small><br>
            &nbsp;&nbsp;<span class="text-yellow" >{{ date('F d, Y H:i:s',strtotime($client->time_started)) }}</span>
        </td>
        <td>
            <small>Call Classification</small><br>
            &nbsp;&nbsp;<span class="<?php if($client->call_classification == 'repeat_call') echo 'text-red'; else echo 'text-blue'; ?>"><?php if($client->call_classification == 'repeat_call') echo 'Repeat Call'; else echo 'New Call'; ?></span>
        </td>
    </tr>
    <tr>
        <td >
            <small>Name</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->name }}</span>
        </td>
        <td>
            <small>Age</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->age }}</span>
        </td>
        <td >
            <small>Gender</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->sex }}</span>
        </td>
    </tr>
    <tr>
        <td >
            <small>Province</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ \App\Province::find($client->province_id)->description }}</span>
        </td>
        <td>
            <small>Municipality</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ \App\Muncity::find($client->municipality_id)->description }}</span>
        </td>
        <td >
            <small>Barangay</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ \App\Barangay::find($client->barangay_id)->description }}</span>
        </td>
    </tr>
    <tr>
        <td >
            <small>Sitio</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->sitio }}</span>
        </td>
        <td>
            <small>Contact Number</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->contact_number }}</span>
        </td>
    </tr>
</table>
<span class="text-blue" style="font-size: 12pt;">Reason for Calling {{ "- ".ucfirst($client->reason_calling) }}</span>
@if($client->reason_calling == 'inquiry' || $client->reason_calling == 'others')
<table class="table table-hover table-bordered" style="width: 100%;">
    <tr>
        <td>
            <small>Notes</small><br>
            &nbsp;&nbsp;<span class="text-yellow" >{{ $client->reason_notes }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <small>Notes for action taken</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->reason_action_taken }}</span>
        </td>
    </tr>
</table>
@elseif($client->reason_calling == 'referral')
<table class="table table-hover table-bordered" style="width: 100%;">
    <tr>
        <td width="33%">
            <small>Patient Data(Name,Age,Gender)</small><br>
            &nbsp;&nbsp;<span class="text-yellow" >{{ $client->reason_patient_data }}</span>
        </td>
        <td width="33%">
            <small>Chief Complains</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->reason_chief_complains }}</span>
        </td>
        <td width="33%">
            <small style="font-size: 9pt;">Relationship to patient (Patient, Family & Others)</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->relationship }}</span>
        </td>
    </tr>
    <tr>
        <td width="25%" colspan="3">
            <small>Notes for action taken</small><br>
            &nbsp;&nbsp;<span class="text-yellow">{{ $client->reason_action_taken }}</span>
        </td>
    </tr>
</table>
@endif
<span class="text-blue" style="font-size: 12pt;">Status of transaction</span>
@if($client->transaction_complete)
<table class="table table-hover table-bordered" style="width: 100%;">
    <tr>
        <td >
            <small>Complete</small><br>
            &nbsp;&nbsp;<span class="text-yellow" >
                <?php
                    if($client->transaction_complete == 'concern_address')
                        echo 'Concern Address';
                    elseif($client->transaction_complete == 'need_provide')
                        echo 'Need Provided';
                    elseif($client->transaction_complete == 'complete_call')
                        echo 'Completed Call';
                ?>
            </span>
        </td>
        <td >
            <small>Time Ended</small><br>
            &nbsp;&nbsp;<span class="text-yellow" >
                {{ date('F d, Y H:i:s',strtotime($client->time_ended)) }}
            </span>
        </td>
        <td >
            <small>Time Duration</small><br>
            &nbsp;&nbsp;<span class="text-yellow" >
                {{ date('H:i:s',strtotime($client->time_duration)) }}
            </span>
        </td>
    </tr>
</table>
@elseif($client->transaction_incomplete)
    <table class="table table-hover table-bordered" style="width: 100%;">
        <tr>
            <td >
                <small>In Complete</small><br>
                &nbsp;&nbsp;<span class="text-yellow" >
                    <?php
                        if($client->transaction_incomplete == 'drop_call')
                            echo 'Dropped Calls';
                        elseif($client->transaction_incomplete == 'hung_up')
                            echo 'Hung up';
                        elseif($client->transaction_incomplete == 'prank_call')
                            echo 'Prank Calls';
                    ?>
                </span>
            </td>
        </tr>
    </table>
@endif