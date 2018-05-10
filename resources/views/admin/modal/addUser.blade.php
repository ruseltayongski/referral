<div class="modal fade" role="dialog" id="addUserModal">
    <div class="modal-dialog modal-sm" role="document">
        <form method="POST" id="addUserForm">
            {{ csrf_field() }}
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
                        <input type="text" class="form-control" name="mname">
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
                        <label>Email Address <small class="text-muted"><em>(Optional):</em></small></label>
                        <input type="text" class="form-control" name="email">
                    </div>
                    <hr />

                    <div class="form-group">
                        <label>Facility:</label>
                        <select class="form-control" name="facility_id" required>
                            @foreach($facility as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Designation:</label>
                        <input type="text" class="form-control" name="designation" required>
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
                        <input type="text" class="form-control mname" name="mname">
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
                        <label>Email Address <small class="text-muted"><em>(Optional):</em></small></label>
                        <input type="email" class="form-control email" name="email">
                    </div>
                    <hr />

                    <div class="form-group">
                        <label>Facility:</label>
                        <select class="form-control facility_id" name="facility_id" required>
                            @foreach($facility as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Designation:</label>
                        <input type="text" class="form-control designation" name="designation" required>
                    </div>

                    <hr />
                    <div class="form-group">
                        <label>Status:</label>
                        <select class="form-control status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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