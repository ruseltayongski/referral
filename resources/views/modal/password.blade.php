<div class="modal fade" role="dialog" id="resetPasswordModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4>CHANGE PASSWORD</h4>
                <hr />
                <form method="post" id="resetForm">
                    {{ csrf_field() }}
                    <div class="password_success hide alert alert-success">
                        <span class="text-success">
                            <i class="fa fa-check"></i> <span class="info">df</span>
                        </span>
                    </div>
                    <div class="form-group">
                        <label style="padding:0px;">Current Password:</label>
                        <input type="password" class="form-control" name="current" id="reset_password_current" autofocus required />
                    </div>
                    <div class="form-group">
                        <label style="padding:0px;">New Password:</label>
                        <input type="password" class="form-control" name="new" id="reset_password_new" pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}" title="Minimum 8 characters, including letters, numbers, and special characters" placeholder="Minimum 8 characters, including letters, numbers, and special characters" required />
                    </div>
                    <div class="form-group">
                        <label style="padding:0px;">Confirm Password:</label>
                        <input type="password" class="form-control" name="confirm" id="reset_password_confirm" pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}" title="Minimum 8 characters, including letters, numbers, and special characters" required />
                    </div>
                    <div class="password_error hide alert alert-danger">
                        <span class="text-danger">
                            <i class="fa fa-info"></i> <span class="info">dsf</span>
                        </span>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-pencil"></i> Change</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

