<div class="modal fade" role="dialog" id="issueModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4><i class="fa fa-exclamation-triangle"></i> Issue and concern </h4>
                <div id="seenBy_section">
                    <hr />
                    <form method="post" action="" id="issueReferralForm">
                        {{ csrf_field() }}
                        <div class="form-group issue_body">

                        </div>
                        <a href="#" onclick="addIssue();" class="pull-right"><i class="fa fa-plus"></i> Add</a><br><br>
                        <div class="pull-right">
                            <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">
                                <i class="fa fa-times"></i> Close
                            </button>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-check"></i> Submit
                            </button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    function addIssue(){
        $(".issue_body").append('<input class="form-control" name="issue[]" placeholder="Type the issue here.." required><br>');
        event.preventDefault();
    }
</script>
