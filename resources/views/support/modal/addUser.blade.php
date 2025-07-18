<?php
    $user = \Illuminate\Support\Facades\Session::get('auth');
    $opdSub = \App\SubOpd::get();
?>
<div class="modal fade" role="dialog" id="addUserModal">
    <div class="modal-dialog modal-sm" role="document">
        <form method="POST" id="addUserForm">
            {{ csrf_field() }}
            <input type="hidden" name="facility_id" value="{{ $user->facility_id }}" />
            <div class="modal-content">
                <div class="modal-body">
                    <fieldset>
                        <legend><i class="fa fa-user-plus"></i> Add User</legend>
                    </fieldset>
                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" class="form-control" autofocus name="fname" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Name:</label>
                        <input type="text" class="form-control" name="mname" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" class="form-control" name="lname" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Number:</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address:</label>
                        <input type="text" class="form-control" name="email" value="None" required>
                    </div>
                    <hr />

                    <div class="form-group">
                        <label>Designation:</label>
                        <input type="text" class="form-control" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label>Department:</label>
                        <select class="form-control select2" name="department_id" id="department_select" required>
                            <option value="">Select Department...</option>
                            @foreach($departments as $dept)
                                $depatment_id = $dept->id;
                                <option value="{{ $dept->id }}">{{ $dept->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="subOpdSection" style="display: none;">
                        <label>Sub Opd:</label>
                        <select class="form-control select2" name="AddopdSub_id">
                            <option value="">Select Sub Opd...</option>
                            @forEach($opdSub as $opd)
                                    <option value="{{ $opd->id }}">{{ $opd->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Other Department:</label>
                        <select class="form-control select2" name="other_department_id[]" id="other_department_select" multiple>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="othersubOpdSection" style="display: none;">
                        <label>Sub Opd:</label>
                        <select class="form-control select2" name="opdSub_id">
                            <option value="">Select Sub Opd...</option>
                            @forEach($opdSub as $opd)
                                    <option value="{{ $opd->id }}">{{ $opd->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr />
                    <div class="form-group">
                        <label>Level:</label>
                        <select class="form-control level" name="level" required>
                            <option value="">Select Option</option>
                            <option value="doctor">Doctor</option>
                            <option value="midwife">Midwife</option>
                            <option value="medical_dispatcher">Medical Dispatcher</option>
                            <option value="nurse">Nurse</option>
                            <!-- <option value="mcc">Medical Center Chief</option> -->
                        </select>
                    </div>
                    <hr />
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control username_1" name="username" required>
                        <div class="username-has-error text-bold text-danger hide">
                            <small>Username already taken!</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" pattern=".{3,}" title="Password - minimum of 3 character" class="form-control" id="password1" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" pattern=".{3,}" title="Confirm password - minimum of 3 Character" class="form-control" id="password2" name="confirm" required>
                        <div class="password-has-error has-error text-bold text-danger hide">
                            <small>Password not match!</small>
                        </div>
                        <div class="password-has-match has-match text-bold text-success hide">
                            <small><i class="fa fa-check-circle"></i> Password matched!</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Save</button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="updateUserModal">
    <div class="modal-dialog modal-sm" role="document">
        <form method="POST" id="updateUserForm">
            {{ csrf_field() }}
            <input type="hidden" name="facility_id" value="{{ $user->facility_id }}" />
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" class="user_id" name="user_id" value="" />
                    <fieldset>
                        <legend><i class="fa fa-user-md"></i> Update User</legend>
                    </fieldset>
                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" class="form-control fname" autofocus name="fname" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Name:</label>
                        <input type="text" class="form-control mname" name="mname" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" class="form-control lname" name="lname" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Number:</label>
                        <input type="text" class="form-control contact" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address:</label>
                        <input type="text" class="form-control email" name="email" required>
                    </div>
                    <hr />

                    <div class="form-group">
                        <label>Designation:</label>
                        <input type="text" class="form-control designation" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label>Department:</label>
                        <select class="form-control department_id" name="department_id" id="editdeparment_select" required>
                            <option value="">No Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" id="opd_id">
                    <div class="form-group" id="editsubOpdSection" style="display: none;">
                        <label>Sub Opd:</label>
                        <select class="form-control subOpdCateg" name="AddeditopdSub_id" id="editsubOpdSelect">
                            <option value="">Select Sub Opd...</option>
                            @forEach($opdSub as $opd)
                                    <option value="{{ $opd->id }}">{{ $opd->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Other Department:</label>
                        <select class="form-control select2 edit_other_department_select" name="edit_other_department_id[]" id="edit_other_department_select" multiple="multiple">
                            @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="othereditsubOpdSection" style="display: none;">
                        <label>Sub Opd:</label>
                        <select class="form-control subOpdCateg" name="editopdSub_id" id="other_editsubOpdSelect">
                            <option value="">Select Sub Opd...</option>
                            @forEach($opdSub as $opd)
                                    <option value="{{ $opd->id }}">{{ $opd->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr />
                    <div class="form-group">
                        <label>Status:</label>
                        <select class="form-control status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Level:</label>
                        <select class="form-control level" name="level" required>
                            <option value="">Select Option</option>
                            <option value="doctor">Doctor</option>
                            <option value="midwife">Midwife</option>
                            <option value="medical_dispatcher">Medical Dispatcher</option>
                            <option value="nurse">Nurse</option>
                        </select>
                    </div>
                    <hr />
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control username" name="username" required>
                        <div class="username-has-error text-bold text-danger hide">
                            <small>Username already taken!</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" pattern=".{3,}" title="Password - minimum of 3 character" class="form-control password_1" name="password" placeholder="Password Unchanged">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" pattern=".{3,}" title="Confirm password - minimum of 3 Character" class="form-control password_2" name="confirm"  placeholder="Password Unchanged">
                        <div class="password-has-error has-error text-bold text-danger hide">
                            <small>Password not match!</small>
                        </div>
                        <div class="password-has-match has-match text-bold text-success hide">
                            <small><i class="fa fa-check-circle"></i> Password matched!</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                    <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i> Update</button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>

$(document).ready(function() {

    function updateOtherDepartmentOptions() {
        var selectedDept = $('#department_select').val(); // Get selected department ID

        // Reset all options first
        $('#other_department_select option').prop('disabled', false);

        if (selectedDept) {
            // Disable the selected department in the "Other Department" dropdown
            $('#other_department_select option[value="' + selectedDept + '"]').prop('disabled', true);
            
            // If the option was previously selected, remove it from selection
            var currentSelections = $('#other_department_select').val() || [];
            if (currentSelections.includes(selectedDept)) {
                currentSelections = currentSelections.filter(id => id !== selectedDept);
                $('#other_department_select').val(currentSelections);
            }
        }
        
        // Refresh Select2 to reflect changes
        $('#other_department_select').trigger('change.select2');
    }

    // Run when department selection changes
    $('#department_select').on('change', function() {
        updateOtherDepartmentOptions();
    });

    // Run once on page load
    updateOtherDepartmentOptions();

      // Initialize Select2 for both dropdowns with explicit width
    $('.select2').select2({
            placeholder: "Select...",
            allowClear: true,
            width: '100%'
        });

});

</script>