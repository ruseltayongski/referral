<div class="modal fade" role="dialog" id="contactModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4>CALL REFERRING FACILITY</h4>
                <hr />
                <form method="post" id="callForm">
                    {{ csrf_field() }}
                    <table class="table table-striped">
                        <tr><td>Facility Contact #:</td></tr>
                        <tr><td class="title-info referring_contact">123-456</td></tr>
                        <tr><td>Referring MD/HCW Contact #:</td></tr>
                        <tr><td class="title-info referring_md_contact">123-456</td></tr>
                    </table>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

