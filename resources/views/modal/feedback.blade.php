<div class="modal fade" role="dialog" id="feedbackModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="box box-success direct-chat direct-chat-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span class="feedback_code">181105-023-134025</span>
                    </h3>

                    <div class="box-tools pull-right">
                        <span data-toggle="tooltip" title="3 New Messages" class="badge bg-green">3</span>
                        <button type="button" class="btn btn-box-tool" onclick="reloadMessage()">
                            <i class="fa fa-refresh"></i></button>
                        <button type="button" data-dismiss="modal" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Conversations are loaded here -->
                    <div class="direct-chat-messages">
                        Loading...
                    </div>
                    <!--/.direct-chat-messages-->
                    <!-- /.direct-chat-pane -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <form action="{{ url('doctor/feedback') }}" method="post" id="feedbackForm">
                        {{ csrf_field() }}
                        <input type="hidden" id="current_code" name="code" />
                        <div class="input-group">
                            <input type="text" name="message" id="message" required placeholder="Type Message ..." class="form-control">
                            <span class="input-group-btn">
                        <button type="submit" class="btn btn-success btn-flat">Send</button>
                      </span>
                        </div>
                    </form>
                </div>
                <!-- /.box-footer-->
            </div>
            <!--/.direct-chat -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->