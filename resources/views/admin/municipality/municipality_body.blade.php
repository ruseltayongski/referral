<form method="POST" action="{{ asset('admin/municipality/crud/add') }}">
    {{ csrf_field() }}
    <fieldset>
        <legend><i class="fa fa-hospital-o"></i> Municipality</legend>
    </fieldset>
    <input type="hidden" value="@if(isset($muncity->id)){{ $muncity->id }}@endif" name="id">
    <input type="hidden" value="{{ $province_id }}" name="province_id">
    <div class="form-group">
        <label>Province Name:</label>
        <input type="text" class="form-control" value="{{ $province_name }} Province" disabled>
    </div>
    <div class="form-group">
        <label>Municipality Name:</label>
        <input type="text" class="form-control" value="@if(isset($muncity->description)){{ $muncity->description }}@endif" autofocus name="description" readonly>
    </div>
    <div class="form-group">
        <label>Municipality Code:</label>
        <input type="text" class="form-control" value="@if(isset($muncity->muncity_code)){{ $muncity->muncity_code }}@endif" name="muncity_code" readonly>
    </div>
    <div class="form-group">
        <label>Frontline Health Workers:</label>
        <input type="text" class="form-control" value="@if(isset($muncity->frontline_health_workers)){{ $muncity->frontline_health_workers }}@endif" name="frontline_health_workers">
    </div>
    <div class="form-group">
        <label>Senior Citizens</label>
        <input type="text" class="form-control" value="@if(isset($muncity->senior_citizens)){{ $muncity->senior_citizens }}@endif" name="senior_citizens">
    </div>
    <div class="form-group">
        <label>Sinovac Allocated</label>
        <input type="text" class="form-control" value="@if(isset($muncity->sinovac_allocated)){{ $muncity->sinovac_allocated}}@endif" name="sinovac_allocated">
    </div>
    <div class="form-group">
        <label>Astrazeneca Allocated</label>
        <input type="text" class="form-control" value="@if(isset($muncity->astrazeneca_allocated)){{ $muncity->astrazeneca_allocated}}@endif" name="astrazeneca_allocated">
    </div>
    <hr />
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
        @if(isset($muncity->id))
           <!-- <a href="#municipality_delete" data-toggle="modal" class="btn btn-danger btn-sm btn-flat" onclick="MunicipalityDelete('<?php echo $muncity->id; ?>')">
                <i class="fa fa-trash"></i> Remove
            </a> -->
        @endif
        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Save</button>
    </div>
</form>



