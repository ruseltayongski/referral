<?php
$departments = \App\Department::all();
?>

<div class="modal fade" role="dialog" id="editProfileModal">
    <div class="modal-dialog modal-md" role="document">
        <form method="POST" action="{{ asset('doctor/editProfile') }}">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" class="user_id" name="id" value="{{ $user->id }}" />
                    <fieldset>
                        <legend><i class="fa fa-user-md"></i> Edit Profile</legend>
                    </fieldset>

                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" class="form-control fname" autofocus name="fname" value="{{ $user->fname }}" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Name:</label>
                        <input type="text" class="form-control mname" name="mname" value="{{ $user->mname }}">
                    </div>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" class="form-control lname" name="lname" value="{{ $user->lname }}" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Number:</label>
                        <input type="text" class="form-control contact" name="contact" value="{{ $user->contact }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address:</label>
                        <input type="text" class="form-control email" name="email" value="{{ $user->email }}" required>
                    </div>

                    <div class="form-group">
                        <label>Designation:</label>
                        <input type="text" class="form-control designation" name="designation" value="{{ $user->designation }}" required>
                    </div>
                    <hr />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                    <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i> Update</button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

