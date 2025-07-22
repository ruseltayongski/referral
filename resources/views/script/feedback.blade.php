
<script>
    //var objDiv = document.getElementById("feedback-181105-023-134025");
    <?php $user = \Illuminate\Support\Facades\Session::get('auth'); ?>
    var code = 0;
    //var feedbackRef = dbRef.ref('Feedback');
    var last_id = 0;

    // $('.btn-feedback').on('click',function () {
    //     console.log("feedback");
    //     code = $(this).data('code');
    //     $('.feedback_code').html(code);
    //     $('.direct-chat-messages').attr('id',code);
    //     $('#message').addClass("message input-"+code+"-{{ $user->id }}");

    //     $("#"+code).html("Loading...");
    //     var url = "<?php echo asset('doctor/feedback').'/'; ?>"+code;
    //     $.get(url,function(data){
    //         setTimeout(function(){
    //             $("#"+code).html(data);
    //             scrolldownFeedback(code);
    //         },500);
    //     });

    //     $("#current_code").val(code);
    // });

    function scrolldownFeedback(code){
        console.log(code);
        var objDiv = document.getElementById(code);

        setTimeout(function () {
            objDiv.scrollTop = objDiv.scrollHeight;
        },500);
    }

    function reloadMessage() {
        $("#message").val('').focus();
        $("#"+code).html("Loading...");
        $("#"+code).load("{{ url('doctor/feedback/') }}/"+code);
        $("#current_code").val(code);

        scrolldownFeedback(code);
    }

      function normalizeUrl(url) {
        return url.replace(/([^:]\/)\/+/g, "$1");
    }

    var globalFiles = [];
    function viewReco(data) {
        code = data.data("code");
        console.log("viewRecos");
        let baseUrl = "{{ asset('') }}";
        baseUrl = baseUrl.replace(/[\/|]$/, "");
        var reco_seen_url = "<?php echo asset('reco/seen1').'/'; ?>"+code;
        $.get(reco_seen_url,function(){ });

        $('#feedbackModal').bind('shown', function() {
            $('textarea.mytextarea1').tinymce({

            });
            console.log("wew")
        });

        $('.feedback_code').html(code);
        $('.direct-chat-messages').attr('id',code);
        $('#message').addClass("message input-"+code+"-{{ $user->id }}");

        $("#"+code).html("Loading...");
        var url = "<?php echo asset('doctor/feedback').'/'; ?>"+code;
        $.get(url,function(response){
            setTimeout(function() {
                let FilesArray = response.data.filter((item) =>  item.filename != '');
               globalFiles = FilesArray.flatMap(file =>
                    file.filename.split('|').map(path => {
                        return `${baseUrl}${path}`;
                    })
                );
                $("#"+code).html(response.html);
                // $("#"+code).html(response);
                scrolldownFeedback(code);
            },500);
        });

        $("#current_code").val(code);
    }

    $('.btn-doh').on('click',function () {
        console.log('doh');
        code = $(this).data('code');
        $('.feedback_monitoring_code').html(code);
        $("#doh_monitoring").html("Loading....");
        var url = "<?php echo asset('monitoring/feedback').'/'; ?>"+code;
        $.get(url,function(data){
            setTimeout(function(){
                $("#doh_monitoring").html(data);
            },500);
        });
    });

    $('#feedbackForm').submit(function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        // Make sure TinyMCE content is saved
        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
            tinyMCE.triggerSave();
        }
        
        var str = $(".mytextarea1").val();
        str = str.replace(/^\<p\>/,"").replace(/\<\/p\>$/,"");
        const temp = $("<div>").html(str); 
        
        const fileIds = [];
        $('#fileDisplayBar img[data-file-id]').each(function() {
            const fileId = $(this).attr('data-file-id');
            if (fileId) {
                fileIds.push(fileId);
            }
        });

        temp.find("span[contenteditable='false']").remove();
        str = temp.text().trim(); // get plain text content

        if(str || fileIds.length > 0) { // Allow submission if there's text OR files

            const $submitBtn = $('#feedbackForm button[type="submit"]');
            $submitBtn.prop('disabled', true).text('Sending...');
            // Clear TinyMCE content
            if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
                tinyMCE.activeEditor.setContent('');
            }
            
            // Create FormData for file upload
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}");
            formData.append('message', str);
            formData.append('code', typeof code !== 'undefined' ? code : '');
        
            // Add files to FormData - FIXED: forEach with capital E
            fileIds.forEach((fileId, index) => {
                const file = window.uploadedFiles.get(fileId);
                console.log("Adding file to FormData:", file);
                if (file) {
                    // Use array notation for multiple files
                    formData.append('file_upload[]', file);
                }
            });
            
            const senderImager = "{{ asset('/resources/img/sender.png') }}";
            const senderMessage = str;
            const senderCurrentTime = typeof moment !== 'undefined' ? moment().format('D MMM LT') : new Date().toLocaleString();
            const senderFacility = "{{ \App\Facility::find($user->facility_id)->name ?? '' }}";
            const senderName = "{{ ($user->fname ?? '') . ' ' . ($user->lname ?? '') }}";
            
            // Send to server with files first
            $.ajax({
                url: "{{ url('doctor/feedback') }}",
                type: 'post',
                data: formData,
                processData: false, // Important for file upload
                contentType: false, // Important for file upload
                success: function(data) {
                    
                    let filePreviewHtml = '';
                    let fileUrlsArray = [];
                    let newGlobalFiles = [];
                    let baseUrl = "{{ asset('') }}";
                    baseUrl = baseUrl.replace(/[\/|]$/, "");
                    const startingGlobalIndex = globalFiles ? globalFiles.length : 0;
                    // Use data.filename from server response instead of blob URLs
                    if (data.filename && data.filename.length > 0) {
                        filePreviewHtml = '<div style="margin-top: 5px;">';
                        filePreviewHtml += '<div class="file-preview-row" style="display: flex; flex-wrap: wrap; gap: 4px;">';
                        
                        // Handle both string and array formats
                        let filenames = [];
                        if (typeof data.filename === 'string') {
                            // If it's a string, split by pipe separator
                            filenames = data.filename.split('|').filter(f => f.trim());
                        } else if (Array.isArray(data.filename)) {
                            // If it's already an array, use as is
                            filenames = data.filename;
                        }
                        
                        filenames.forEach((filename, index) => {

                            const globalFileIndex = startingGlobalIndex + index;

                            // Create server file URL path (handle full paths)
                            let serverFileUrl;
                            if (filename.startsWith('/public/storage/')) {
                                // If filename already contains the full path
                                serverFileUrl = "{{ asset('') }}" + filename;
                            } else {
                                // If filename is just the filename
                                serverFileUrl = "{{ asset('public/fileupload/') }}/" + filename;
                            }

                            fileUrlsArray.push(serverFileUrl);

                            let globalFileUrl;
                            if (filename.startsWith('/')) {
                                globalFileUrl = baseUrl + filename;
                            } else {
                                globalFileUrl = baseUrl + '/public/fileupload/' + filename;
                            }
                            newGlobalFiles.push(globalFileUrl);
                            
                            // Get file extension for type checking
                            const fileExtension = filename.split('.').pop().toLowerCase();
                            const baseFilename = filename.split('/').pop(); // Get just the filename for display
                            const fileId = Math.random().toString(36).substr(2, 9);

                            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
                                filePreviewHtml += 
                                    `<div contenteditable="false" style="display:inline-block; text-align:center; width:60px; margin-right:5px;">
                                        <a href="javascript:void(0);" class="file-preview-trigger" data-file-url="${serverFileUrl}"
                                        data-code="${code}" data-local-index="${index}"
                                        data-current-index="${globalFileIndex}" 
                                        data-files='${JSON.stringify(fileUrlsArray)}'
                                        data-use-global="true"> 
                                            <img src="${serverFileUrl}" class="attachment-thumb" 
                                                alt="${baseFilename}" style="width:50px; height:50px; object-fit:contain; border:1px solid green;" 
                                                data-file-id="${fileId}" />
                                        </a>
                                    </div>`;
            
                            } else if (fileExtension === 'pdf') {
                                filePreviewHtml += 
                                    `<div contenteditable="false" style="display:inline-block; text-align:center; width:60px; margin-right:5px;">
                                        <a href="javascript:void(0);" class="file-preview-trigger" data-file-url="${serverFileUrl}"
                                        data-code="${code}" data-local-index="${index}"
                                        data-current-index="${globalFileIndex}" 
                                        data-files='${JSON.stringify(fileUrlsArray)}'
                                        data-use-global="true">
                                            <img src="{{ asset('public/fileupload/pdffile.png') }}" class="attachment-thumb" 
                                                alt="PDF File" style="width:50px; height:50px; object-fit:contain; border:1px solid green;" 
                                                data-file-id="${fileId}"/>
                                        </a>
                                    </div>`;
                            } 
                        });
                        filePreviewHtml += '</div></div>';
                    }

                    if (newGlobalFiles.length > 0) {
                        // Option 1: Append new files to existing globalFiles array
                        if (typeof globalFiles !== 'undefined') {
                            globalFiles = globalFiles.concat(newGlobalFiles);
                        } else {
                            globalFiles = newGlobalFiles;
                        }
                    }
                    
                    const recoAppend = '<div class="direct-chat-msgs right">\n' +
                        '    <div class="direct-chat-info clearfix">\n' +
                        '        <span class="direct-chat-name text-info pull-right">'+senderFacility+'</span><br>\n' +
                        '        <span class="direct-chat-name pull-right">'+senderName+'</span>\n' +
                        '        <span class="direct-chat-timestamp pull-left">'+senderCurrentTime+'</span>\n' +
                        '    </div>\n' +
                        '    <img class="direct-chat-img" title="" src="'+senderImager+'" alt="Message User Image"><!-- /.direct-chat-img -->\n' +
                        '    <div class="direct-chat-text">\n' +
                        '        '+filePreviewHtml+senderMessage+
                        '    </div>\n' +
                        '</div>';

                    // Append to chat if elements exist
                    if ($(".reco-body" + (typeof code !== 'undefined' ? code : '')).length > 0) {
                        $(".reco-body" + (typeof code !== 'undefined' ? code : '')).append(recoAppend);
                    }

                    // Set the fileUrlsArray to window.feedbackPreviewFiles
                    // window.feedbackPreviewFiles = fileUrlsArray;
                    if (!window.feedbackPreviewFilesMap) window.feedbackPreviewFilesMap = {};
                    window.feedbackPreviewFilesMap[code] = fileUrlsArray;
                
                    // Setup file preview click handlers
                    $(document).off('click', '.file-preview-trigger').on('click', '.file-preview-trigger', function(e) {
                        const fileUrls = $(this).data('file-url');
                        const code = $(this).data('code');
                        const useGlobal = $(this).data('use-global');
                        const globalIndex = parseInt($(this).data('current-index'));
                        const localIndex = parseInt($(this).data('local-index'));
                        // let files = window.feedbackPreviewFilesMap[code] || [];
                        let filesAttr =  $(this).attr('data-files');
                
                        // var descend = 'desc';

                        let files = [];
                        let startIndex = 0;
                        
                        if (useGlobal && globalFiles && globalFiles.length > 0) {
                            // Use globalFiles array for navigation
                            files = globalFiles.map(normalizeUrl);
                            startIndex = globalIndex;
                        } else {
                            // Fallback to local files from data attribute
                            let filesAttr = $(this).attr('data-files');
                            try {
                                if (filesAttr) {
                                    files = JSON.parse(filesAttr);
                                    files = files.map(normalizeUrl);
                                    startIndex = localIndex;
                                } else {
                                    console.warn("data-files attribute is missing or empty.");
                                    return;
                                }
                            } catch (e) {
                                console.error("Invalid JSON in data-files:", filesAttr, e);
                                return;
                            }
                        }
                        
                        if (Array.isArray(files) && files.length > 0) {
                            window.setupfeedbackFilePreview(files, startIndex, code);
                            $('#filePreviewContentReco').modal('show');
                        }
                    });

                    // Scroll to bottom if element exists
                    if (typeof code !== 'undefined' && document.getElementById(code)) {
                        var objDiv = document.getElementById(code);
                        objDiv.scrollTop = objDiv.scrollHeight;
                    }
                    
                    // Clear message input
                    $("#message").val('').attr('placeholder','Type Message...');
                    
                    $('#fileDisplayBar').html('<div class="upload-prompt" id="uploadPrompt"></div>');
                    
                    // Clear uploaded files from memory for this message
                    fileIds.forEach(fileId => {
                        window.uploadedFiles.delete(fileId);
                    });

                    $submitBtn.prop('disabled', false).text('Send');
                },
                error: function(xhr, status, error) {
                    console.error("Error sending message:", error);
                    console.log("XHR:", xhr);
                    console.log("Status:", status);
                    
                    // Show error message if Lobibox is available
                    if (typeof Lobibox !== 'undefined') {
                        Lobibox.alert("error", {
                            msg: "Failed to send message. Please try again."
                        });
                    } else {
                        alert("Failed to send message. Please try again.");
                    }
                }
            });

        }
        else {
            // Show error message if no content
            if (typeof Lobibox !== 'undefined') {
                Lobibox.alert("error", {
                    msg: "Please enter a message or select files to upload!"
                });
            } else {
                alert("Please enter a message or select files to upload!");
            }
        }
    });


    // function FeedbackFilePreviewSubmit(){
    //     $(document).off('click', '.file-preview-submit').on('click', '.realtime-file-preview', function(e) {
    //         e.preventDefault();
    //         const filepaths = $(this).data('file-paths');
    //         const feedbackCode = $(this).data('feedback-code');
    //         console.log("file paths", filepaths);
    //         console.log("code path", feedbackCode);
    //         window.setupfeedbackFilePreview(filepaths,null,feedbackCode);

    //         $('#filePreviewContentReco').modal('show');
    //     });
    // }

    // Optional: Function to check if files are still in memory
    function checkUploadedFiles() {
        console.log("Current uploaded files:", window.uploadedFiles);
        console.log("Number of files in memory:", window.uploadedFiles.size);
    }

    // Optional: Function to clear all uploaded files
    function clearAllUploadedFiles() {
        window.uploadedFiles.clear();
        console.log("All uploaded files cleared from memory");
    }


    $('body').on('click','.btn-issue-referred',function(){
        var code = $(this).data('code');
        var tracking_id = $(this).data('tracking_id');
        var referred_from = $(this).data('referred_from');
        var user_facility_id = "<?php echo \Illuminate\Support\Facades\Session::get('auth')->facility_id; ?>";

        if(user_facility_id != referred_from)
            $(".issue_footer").remove();

        $('.issue_concern_code').html(code);
        $('#issue_tracking_id').val(tracking_id);
        $("#issue_and_concern_body").html("Loading....");
        var url = "<?php echo asset('issue/concern').'/'; ?>"+tracking_id+"/"+referred_from;
        $.get(url,function(data){
            setTimeout(function(){
                $("#issue_and_concern_body").html(data);
            },500);
        });
    });

    $('.btn-issue-incoming').on('click',function () {
        console.log('issue');
        $(".issue_footer").remove();
        var code = $(this).data('code');
        var tracking_id = $(this).data('tracking_id');
        var referred_from = $(this).data('referred_from');
        $('.issue_concern_code').html(code);
        $("#issue_and_concern_body").html("Loading....");
        var url = "<?php echo asset('issue/concern').'/'; ?>"+tracking_id+"/"+referred_from;
        $.get(url,function(data){
            setTimeout(function(){
                $("#issue_and_concern_body").html(data);
            },500);
        });
    });

    $('#sendIssue').submit(function (e) {
        e.preventDefault();
        var issue_message = $("#issue_message").val();
        $("#issue_message").val('').attr('placeholder','Sending...');
        $.ajax({
            url: "{{ url('issue/concern/submit') }}",
            type: 'post',
            data: {
                _token : "{{ csrf_token() }}",
                issue: issue_message,
                tracking_id : $("#issue_tracking_id").val()
            },
            success: function(data) {
                $("#issue_and_concern_body").append(data);
                $("#message").val('').attr('placeholder','Type a message for your issue and concern regarding your referral..');
            }
        });
    });

    /*$('.direct-chat-messages').scroll(function() {
        var current_top_element = $('.direct-chat-messages').children().first();
        var previous_height = 0;


        var pos = $('.direct-chat-messages').scrollTop();
        var objDiv = document.getElementById(code);


        if (pos == 0) {
            $.ajax({
                url: "{{ url('doctor/feedback/load/') }}/"+code,
                type: "get",
                success: function (data) {
                    if(data!=0){
                        $("#"+code).prepend(data);
                        current_top_element.prevAll().each(function() {
                            previous_height += $(this).outerHeight();
                        });

                        objDiv.scrollTop = previous_height;
                    }
                }
            })
        }
    });*/

    /*feedbackRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var c = data.code;

        $("#"+c).append(data.content);
        var url = "<?php echo asset('referral/doctor/feedback/reply')."/"; ?>"+data.id;
        $.ajax({
            url: url,
            type: 'get',
            success: function(content) {
                $("#"+c).append(content);

                if(data.user_id == "{{ $user->id }}"){
                    var input_id = ".input-"+data.code+"-"+data.user_id;
                    $(input_id).val('');
                    var objDiv = document.getElementById(code);
                    objDiv.scrollTop = objDiv.scrollHeight;
                }
            }
        });
    });*/
</script>
