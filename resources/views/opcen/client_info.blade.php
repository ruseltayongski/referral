<fieldset>
    <legend>
        <i class="fa fa-user-secret"></i> Client Info
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </legend>
</fieldset>
<table class="table table-hover table-bordered" style="width: 100%;">
    <tr>
        <td >
            <small>Time Started</small><br>
            &nbsp;&nbsp;<b class="text-yellow" >{{ $client->time_started }}</b>
        </td>
        <td>
            <small>Call Classification</small><br>
            &nbsp;&nbsp;<b class="<?php if($client->call_classification == 'repeat_call') echo 'text-red'; else echo 'text-blue'; ?>"><?php if($client->call_classification == 'repeat_call') echo 'Repeat Call'; else echo 'New Call'; ?></b>
        </td>
        <td >
            <small>Reference Number</small><br>
            &nbsp;&nbsp;<b class="text-green">{{ $client->reference_number }}</b>
        </td>
    </tr>
    <tr>
        <td >
            <small>Province</small><br>
            &nbsp;&nbsp;<b >{{ \App\Province::find($client->province_id)->description }}</b>
        </td>
        <td>
            <small>Municipality</small><br>
            &nbsp;&nbsp;<b >{{ \App\Muncity::find($client->municipality_id)->description }}</b>
        </td>
        <td >
            <small>Barangay</small><br>
            &nbsp;&nbsp;<b >{{ \App\Barangay::find($client->barangay_id)->description }}</b>
        </td>
    </tr>
    <tr>
        <td >
            <small>Sitio</small><br>
            &nbsp;&nbsp;<b >{{ $client->sitio }}</b>
        </td>
        <td>
            <small>Contact Number</small><br>
            &nbsp;&nbsp;<b >{{ $client->contact_number }}</b>
        </td>
    </tr>
</table>