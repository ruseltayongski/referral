<form method="POST" action="{{ asset('admin/facility/add') }}">
    {{ csrf_field() }}
    <fieldset>
        <legend><i class="fa fa-plus"></i> Add Facility</legend>
    </fieldset>
    <input type="hidden" value="@if(isset($data->id)){{ $data->id }}@endif" name="id">
    <input type="hidden" value="1" name="status">
    <div class="form-group">
        <label>Facility name:</label>
        <input type="text" class="form-control" value="@if(isset($data->name)){{ $data->name }}@endif" autofocus name="name" required>
    </div>
    <div class="form-group">
        <label>Abbr:</label>
        <input type="text" class="form-control" value="@if(isset($data->abbr)){{ $data->abbr }}@endif" name="abbr">
    </div>
    <div class="form-group">
        <label>Province:</label>
        <select class="form-control select_province" name="province" required>
            @if(!isset($data->province))
            <option value="">Select Province</option>
            @endif
            @foreach(\App\Province::get() as $row)
                <option value="{{ $row->id }}"
                    <?php
                        if(isset($data->province)){
                            if($data->province == $row->id){
                                echo 'selected';
                            }
                        }
                    ?>
                >{{ $row->description }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Municipality:</label>
        <select class="form-control select_muncity" name="muncity" required>
            @if(isset($data->muncity))
                @foreach(\App\Muncity::where("province_id",$data->province)->get() as $row)
                    <option value="{{ $row->id }}" <?php if($data->muncity == $row->id)echo 'selected'; ?> >{{ $row->description }}</option>
                @endforeach
            @else
                <option value="">Select Municipality</option>
            @endif
        </select>
    </div>
    <div class="form-group">
        <label>Barangay:</label>
        <select class="form-control select_barangay" name="brgy" required>
            @if(isset($data->brgy))
                @foreach(\App\Barangay::where("province_id",$data->province)->where("muncity_id",$data->muncity)->get() as $row)
                    <option value="{{ $row->id }}" <?php if($data->brgy == $row->id)echo 'selected'; ?> >{{ $row->description }}</option>
                @endforeach
            @else
                <option value="">Select Barangay</option>
            @endif
        </select>
    </div>
    <div class="form-group">
        <label>Address:</label>
        <input type="text" class="form-control" value="@if(isset($data->address)){{ $data->address }}@endif" name="address" required>
    </div>
    <div class="form-group">
        <label>Contact:</label>
        <input type="text" class="form-control" value="@if(isset($data->contact)){{ $data->contact }}@endif" name="contact" required>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input type="text" class="form-control" value="@if(isset($data->email)){{ $data->email }}@endif" name="email" required>
    </div>
    <div class="form-group">
        <label>Chief of hospital(name):</label>
        <input type="text" class="form-control" value="@if(isset($data->chief_hospital)){{ $data->chief_hospital }}@endif" name="chief_hospital" required>
    </div>
    <div class="form-group">
        <label>Hospital Level:</label>
        <select class="form-control" name="level" >
            @if(!isset($data->level))
            <option value="">Select Hospital Level</option>
            @endif
            <option value="1"
                <?php
                    if(isset($data->level)){
                        if($data->level == 1){
                            echo 'selected';
                        }
                    }
                ?>
            >Level 1</option>
            <option value="2"
                <?php
                    if(isset($data->level)){
                        if($data->level == 2){
                            echo 'selected';
                        }
                    }
                ?>
            >Level 2</option>
            <option value="3"
                <?php
                    if(isset($data->level)){
                        if($data->level == 3){
                            echo 'selected';
                        }
                    }
                ?>
            >Level 3</option>
        </select>
    </div>
    <div class="form-group">
        <label>Hospital Type:</label>
        <select class="form-control" name="hospital_type">
            @if(!isset($data->hospital_type))
            <option value="">Select Hospital Type</option>
            @endif
            <option value="government"
                <?php
                    if(isset($data->hospital_type)){
                        if($data->hospital_type == 'government'){
                            echo 'selected';
                        }
                    }
                ?>
            >Government</option>
            <option value="private"
                <?php
                    if(isset($data->hospital_type)){
                        if($data->hospital_type == 'private'){
                            echo 'selected';
                        }
                    }
                ?>
            >Private</option>
        </select>
    </div>
    <hr />
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
        @if(isset($data->id))
        <a href="#facility_delete" data-toggle="modal" class="btn btn-danger btn-sm btn-flat" onclick="FacilityDelete('<?php echo $data->id; ?>')">
            <i class="fa fa-trash"></i> Remove
        </a>
        @endif
        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Save</button>
    </div>
</form>

<script>
    $('.select_province').on('change',function(){
        var province_id = $(this).val();
        var url = "{{ url('location/muncity/') }}";
        $.ajax({
            url: url+'/'+province_id,
            type: 'GET',
            success: function(data){
                console.log(data);
                $('.select_muncity').empty()
                    .append($('<option>', {
                        value: '',
                        text : 'Select Municipality'
                    }));
                $('.select_barangay').empty()
                    .append($('<option>', {
                        value: '',
                        text : 'Select Barangay'
                    }));
                jQuery.each(data, function(i,val){
                    $('.select_muncity').append($('<option>', {
                        value: val.id,
                        text : val.description
                    }));
                });
            },
            error: function(){
                $('#serverModal').modal();
            }
        });

    });

    $('.select_muncity').on('change',function(){
        var province_id = $(".select_province").val();
        var muncity_id = $(this).val();
        var url = "{{ url('location/barangay/') }}";
        $.ajax({
            url: url+'/'+province_id+'/'+muncity_id,
            type: 'GET',
            success: function(data){
                $('.select_barangay').empty()
                    .append($('<option>', {
                        value: '',
                        text : 'Select Barangay'
                    }));
                jQuery.each(data, function(i,val){
                    $('.select_barangay').append($('<option>', {
                        value: val.id,
                        text : val.description
                    }));
                });
            },
            error: function(){
                $('#serverModal').modal();
            }
        });

    });
</script>

