<div class="modal fade" role="dialog" id="facility_modal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body facility_body">
                <center>
                    <img src="{{ asset('resources/img/loading.gif') }}" alt="">
                </center>
            </div><!-- /.modal-content -->
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="facility_delete">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ asset('admin/facility/delete') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="facility_id" class="facility_id">
                    <fieldset>
                        <legend><i class="fa fa-trash"></i> Remove Facility</legend>
                    </fieldset>
                    <div class="alert alert-danger">
                        <label for="" class="text-danger">Are you sure you want to delete?</label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-check"></i> Yes</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->





