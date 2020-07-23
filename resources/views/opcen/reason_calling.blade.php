@if($reason == 'inquiry' || $reason == 'others')
<table class="table table-hover table-bordered">
    <tr>
        <td >
            <small>Notes</small>
            <textarea name="" id="" cols="30" rows="5" class="form-control"></textarea>
        </td>
    </tr>
</table>
@else
    <table class="table table-hover table-bordered" style="width: 100%">
        <tr>
            <td >
                <small>Patient Data(Name,Age,Gender)</small>
                <textarea name="" id="" cols="30" rows="5" class="form-control"></textarea>
            </td>
            <td >
                <small>Chief Complains</small>
                <textarea name="" id="" cols="30" rows="5" class="form-control"></textarea>
            </td>
        </tr>
    </table>
@endif