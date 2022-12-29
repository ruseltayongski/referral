<input type="hidden" class="user_id" name="user_id" value="{{ $user->id }}" />

<fieldset>
    <legend><i class="fa fa-user-md"></i> Update Facility Assignment</legend>
</fieldset>
<div class="form-group">
    <label>First Name:</label>
    <input type="text" class="form-control fname" autofocus name="fname" readonly style="background-color: white" value="{{ $user->fname }}">
</div>
<div class="form-group">
    <label>Middle Name:</label>
    <input type="text" class="form-control mname" name="mname" readonly style="background-color: white" value="{{ $user->mname }}">
</div>
<div class="form-group">
    <label>Last Name:</label>
    <input type="text" class="form-control lname" name="lname" readonly style="background-color: white" value="{{ $user->lname }}">
</div>
<hr />

<div class="form-group">
    <label>Affiliated Facilities: </label>
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered table-striped" id="" onshow="disableRemoveBtn()">
            <thead>
            <tr style="font-size: 13px;">
                <th class="text-center" style="width:30%;">Facility Name</th>
                <th class="text-center" style="width:20%;">Department</th>
                <th class="text-center" style="width:25%;">Contact Number</th>
                <th class="text-center" style="width:30%;">Email Address</th>
                <th class="text-center" style="width:50%;">Status</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            @if(count($user_faci) > 0)
                @foreach($user_faci as $f)
                    <?php $faci_count++; ?>
                    <tr style="font-size: 10pt; width: 100%;" class="faci_apil{{$counter}}">
                        <td>
                            <select class="form-control select2" name="faci[]" required>
                                <option value="">Select...</option>
                                @foreach($facilities as $faci)
                                    <option <?php if($faci->id == $f->faci_id && $f->facility_code != '' && $f->facility_code != null) echo 'selected'?> value="{{  $faci->id }}">{{ $faci->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control select2" name="department[]" required>
                                <option value="">Select...</option>
                                @foreach($departments as $dep)
                                    <option <?php if($f->department_id == $dep->id) echo 'selected'?> value="{{ $dep->id }}">{{ $dep->description }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea class="form-control" rows="2" required style="resize: none" name="contact[]" >{{ $f->contact }} </textarea>
                            {{--<input class="form-control" type="text" value="{{ $f->contact }}" name="contact[]" style="font-size: 12px; width:100%;" required>--}}
                        </td>
                        <td>
                            <textarea class="form-control" rows="2" required style="resize: none;" name="email[]">{{ $f->email }}</textarea>
                            {{--<input type="text" value="{{ $f->email }}" name="email[]" style="font-size: 12px; width:100%;" required">--}}
                        </td>
                        <td>
                            <select class="form-control">
                                <option selected value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn-xs btn-danger text-center" id="btn_remove{{$counter}}" value="{{ $f->facility_code }}" onclick="removeFacility({{$counter++}})"type="button" style="width: 100%">Remove</button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div><br>
    <div class="text-center">
        <button class="btn-xs btn-warning text-center" id="facility_add_row" type="button">
            <i class="fa fa-plus"> Add Row</i>
        </button><br><br>
    </div>
</div>
<hr />
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
    <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i> Update</button>
</div>

<script>
    $('.select2').select2();
    disableRemoveBtn();
    function disableRemoveBtn() {
        for(var i = 0; i <= counter; i++) {
            if(faci_count > 1)
                $('#btn_remove'+i).show();
            else
                $('#btn_remove'+i).hide();
        }
    }
</script>