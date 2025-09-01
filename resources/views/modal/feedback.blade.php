<div class="modal fade" role="dialog" id="feedbackModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="box box-success direct-chat direct-chat-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span class="feedback_code"></span>
                    </h3>

                    <div class="box-tools pull-right">
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
                        <div class="file-display-bar" id="fileDisplayBar">
                            <div class="upload-prompt" id="uploadPrompt">
                            </div>
                        </div>

                        <input type="hidden" id="current_code" name="code" />
                        <div class="input-group">
                            <textarea placeholder="Type Message ..." class="mytextarea1"></textarea>
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-success btn-md">Send</button>
                            </span>
                            &nbsp;
                                <button type="button" id="closeBtn" class="btn btn-default btn-md" style="display: none;">
                                    Close
                                </button>
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

<!-- File Preview Modal - Bootstrap 3 Style -->
<div class="modal fade" id="filePreviewModalReco" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                <h4 class="modal-title" id="filePreviewModalLabel">
                    <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                    File Preview
                </h4>
            </div>
            <div class="modal-body">
                <div class="file-preview-container" id="filePreviewContainer">
                    <!-- File preview content will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    Cancel
                </button>
                <!-- <button type="button" class="btn btn-primary" id="confirmUpload">
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                    Insert File
                </button> -->
            </div>
        </div>
    </div>
</div>

<!-- File Preview Modal dynamic files -->
<!-- <div class="modal fade modal-front" id="filePreviewContentReco" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel" aria-hidden="true"> 
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="file-preview-container" id="filePreviewContainer" style="position: relative; min-height: 400px;">
                    {{-- Navigation Controls --}}
                    <div class="navigation-controls" style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); z-index: 10;">
                        <button class="nav-btn" id="prevBtn" title="Previous File" 
                                style="background: rgba(0,0,0,0.7); color: white; border: none; padding: 15px 20px; border-radius: 50%; cursor: pointer; font-size: 18px;">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                        </button>
                    </div>
                    
                    <div class="navigation-controls" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); z-index: 10;">
                        <button class="nav-btn" id="nextBtn" title="Next File"
                                style="background: rgba(0,0,0,0.7); color: white; border: none; padding: 15px 20px; border-radius: 50%; cursor: pointer; font-size: 18px;">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                        </button>
                    </div>
                    
                    {{-- File Counter --}}
                    <div id="fileCounter" style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; z-index: 10;">
                        1 of 1
                    </div>
                    
                    {{-- File Info --}}
                    <div id="fileInfo" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.7); color: white; padding: 5px 15px; border-radius: 15px; font-size: 12px; z-index: 10;">
                        Loading...
                    </div> -->
                    
                    {{-- File Preview Content --}}
                    <!-- <div class="file-preview-content" id="filePreviewContent" style="text-align: center; display: flex; align-items: center; justify-content: center; min-height: 400px;">
                      
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="downloadBtn">
                    <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    Download
                </button>
            </div>
        </div>
    </div>
</div> -->
 <div class="modal fade modal-front modal-fullscreen" id="filePreviewContentReco" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel" aria-hidden="true"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-left-feedback" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <button type="button"  id="downloadBtn">
                    <i class="fa fa-download" aria-hidden="true"></i>
                  </button>
            </div>
            <div class="modal-body">
                <div class="file-preview-container" id="filePreviewContainer">
                    <!-- Navigation Controls -->
                    <div class="navigation-controls left">
                        <button class="nav-btn" id="prevBtn" title="Previous File">
                            <!-- <span class="glyphicon glyphicon-chevron-left"></span> -->
                               <i class="fa fa-chevron-left"></i>
                        </button>
                    </div>
                    
                    <div class="navigation-controls right">
                        <button class="nav-btn" id="nextBtn" title="Next File">
                            <!-- <span class="glyphicon glyphicon-chevron-right"></span> -->
                               <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <!-- File Counter -->
                    <!-- <div class="file-counter" id="fileCounter">
                        1 of 1
                    </div> -->
                    
                    <!-- File Preview Content -->
                    <div class="file-preview-content" id="filePreviewContent">
                        <div class="loading-spinner"></div>
                    </div>
                </div>
                <!-- File Thumbnails Navigation -->
                <div id="fileThumbnails" class="file-thumbnails-bar">
                    <!-- Thumbnails will be appended dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" role="dialog" id="feedbackDOH">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="box box-success direct-chat direct-chat-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span class="feedback_monitoring_code"></span>
                    </h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Conversations are loaded here -->
                    <div id="doh_monitoring" style="padding: 10px;">

                    </div>
                </div>
            </div>
            <!--/.direct-chat -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" role="dialog" id="IssueAndConcern">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="box box-danger direct-chat direct-chat-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span class="issue_concern_code"></span>
                    </h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Conversations are loaded here -->
                    <div id="issue_and_concern_body" style="padding: 10px;">

                    </div>
                </div>

                <div class="box-footer issue_footer">
                    <form action="" method="post" id="sendIssue">
                        {{ csrf_field() }}
                        <input type="hidden" id="issue_tracking_id" />
                        <div class="input-group">
                            <textarea id="issue_message" rows="3" required placeholder="Type a message for your issue and concern regarding your referral.." class="form-control"></textarea>
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-success btn-lg">Send</button>
                            </span>
                        </div>
                    </form>
                </div>
                <!-- /.box-footer-->
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

    // $(document).ready(function () {
    //     $("#feedbackModal .modal-dialog").draggable({
    //         handle: ".box-header",
    //         containment: "window"
    //     });
    // });

    function isMobile() {
        return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
    }

    document.addEventListener("DOMContentLoaded", function () {
        const closeBtn = document.getElementById("closeBtn");
        const modal = document.getElementById("feedbackModal");

        // Show close button only if mobile
        if (isMobile()) {
            closeBtn.style.display = "inline-block";
        }

        // Handle close button click
        closeBtn.addEventListener("click", function () {
            // If you're using Bootstrap modal:
            $('#feedbackModal').modal('hide');

            // If you're using plain JS modal:
            // modal.style.display = "none";
        });
    });
</script>