<script>
    //var objDiv = document.getElementById("feedback-181105-023-134025");
    <?php $user = \Illuminate\Support\Facades\Session::get('auth'); ?>
    var code = 0;
    //var feedbackRef = dbRef.ref('Feedback');
    var last_id = 0;

    $('.btn-feedback').on('click',function () {
        console.log("feedback");
        code = $(this).data('code');
        $('.feedback_code').html(code);
        $('.direct-chat-messages').attr('id',code);
        $('#message').addClass("message input-"+code+"-{{ $user->id }}");

        $("#"+code).html("Loading...");
        var url = "<?php echo asset('doctor/feedback').'/'; ?>"+code;
        $.get(url,function(data){
            setTimeout(function(){
                $("#"+code).html(data);
                scrolldownFeedback(code);
            },500);
        });

        $("#current_code").val(code);
    });

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

    function viewReco(data) {
        code = data.data("code");
        console.log("viewRecos");

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
        $.get(url,function(data){
            setTimeout(function(){
                $("#"+code).html(data);
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

    // $('#feedbackForm').submit(function (e) {
    //     e.preventDefault();
    //     e.stopImmediatePropagation();
    //     tinyMCE.triggerSave();
    //     var str = $(".mytextarea1").val();
    //     str = str.replace(/^\<p\>/,"").replace(/\<\/p\>$/,"");
    //     const temp = $("<div>").html(str);
        
    //     const fileElements = temp.find("div[contenteditable='false'] img[data-file-id]");
    //     const fileIds = [];
    //     fileElements.each(function() {
    //         const fileId = $(this).attr('data-file-id');
    //         if (fileId) {
    //             fileIds.push(fileId);
    //         }
    //     });

    //     temp.find("div[contenteditable='false']").remove(); // remove uploaded file previews
    //     str = temp.text().trim(); // get plain text content

    //     console.log("feedback_sendsd", str);
    //     console.log("fileIds", fileIds);

    //     if(str) {
    //         tinyMCE.activeEditor.setContent('');
    //         const senderImager = "{{ asset("/resources/img/sender.png") }}";
    //         const senderMessage = str;
    //         const senderCurrentTime = moment().format('D MMM LT');
    //         const senderFacility = "{{ \App\Facility::find($user->facility_id)->name }}";
    //         const senderName = "{{ $user->fname.' '.$user->lname }}";
    //         const recoAppend = '<div class="direct-chat-msg right">\n' +
    //             '    <div class="direct-chat-info clearfix">\n' +
    //             '        <span class="direct-chat-name text-info pull-right">'+senderFacility+'</span><br>\n' +
    //             '        <span class="direct-chat-name pull-right">'+senderName+'</span>\n' +
    //             '        <span class="direct-chat-timestamp pull-left">'+senderCurrentTime+'</span>\n' +
    //             '    </div>\n' +
    //             '    <img class="direct-chat-img" title="" src="'+senderImager+'" alt="Message User Image"><!-- /.direct-chat-img -->\n' +
    //             '    <div class="direct-chat-text">\n' +
    //             '        '+senderMessage+
    //             '    </div>\n' +
    //             '</div>';
    //         $(".reco-body"+code).append(recoAppend);
    //         var objDiv = document.getElementById(code);
    //         objDiv.scrollTop = objDiv.scrollHeight;
    //         $("#message").val('').attr('placeholder','Type Message...');
    //         $.ajax({
    //             url: "{{ url('doctor/feedback') }}",
    //             type: 'post',
    //             data: {
    //                 _token : "{{ csrf_token() }}",
    //                 message: str,
    //                 code : code
    //             },
    //             success: function(data) {}
    //         });
    //     }
    //     else {
    //         Lobibox.alert("error",
    //         {
    //             msg: "ReCo message was empty!"
    //         });
    //     }
    // });

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
        
        // Extract file IDs from the content
        const fileElements = temp.find("span[contenteditable='false'] img[data-file-id]");
        const fileIds = [];
        fileElements.each(function() {
            const fileId = $(this).attr('data-file-id');
            if (fileId) {
                fileIds.push(fileId);
            }
        });

        temp.find("span[contenteditable='false']").remove();
        str = temp.text().trim(); // get plain text content

        console.log("feedback_message", str);
        console.log("fileIds", fileIds);
        console.log("uploadedFiles Map:", window.uploadedFiles);

        if(str || fileIds.length > 0) { // Allow submission if there's text OR files
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
            
            let filePreviewHtml = '';
            if (fileIds.length > 0) {
                filePreviewHtml = '<div style="margin-top: 5px;">';
                fileIds.forEach(fileId => {
                    const file = window.uploadedFiles.get(fileId);
            
                    if (file) {
                        const fileURL = URL.createObjectURL(file); // Generate a temporary URL for preview
                        const fileId = Math.random().toString(36).substr(2, 9); // Optional: use actual ID if needed

                        // Initialize container if not already
                        if (!filePreviewHtml.includes('file-preview-row')) {
                            filePreviewHtml += `<div class="file-preview-row" style="display: flex; flex-wrap: wrap; gap: 4px;">`;
                        }

                        if (file.type.startsWith('image')) {
                            filePreviewHtml += `
                                <div contenteditable="false" style="display:inline-block; text-align:center; width:60px; margin-right:5px;">
                                    <a href="${fileURL}" target="_blank"> 
                                        <img src="{{ asset('public/fileupload/imageFile2.png') }}" class="attachment-thumb" 
                                            alt="${file.name}" style="width:50px; height:50px; object-fit:contain; border:1px solid green;" 
                                            data-file-id="${fileId}" />
                                    </a>
                                    <div title="${file.name}" style="font-size:10px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-align:center;">
                                        ${file.name}
                                    </div>
                                </div>
                            `;
                        } else if (file.type === 'application/pdf') {
                            filePreviewHtml += `
                                <div contenteditable="false" style="display:inline-block; text-align:center; width:60px; margin-right:5px;">
                                    <a href="${fileURL}" target="_blank">
                                        <img src="{{ asset('public/fileupload/pdffile.png') }}" class="attachment-thumb" 
                                            alt="PDF File" style="width:50px; height:50px; object-fit:contain; border:1px solid green;" 
                                            data-file-id="${fileId}"/>
                                    </a>
                                    <div title="${file.name}" style="font-size:10px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-align:center;">
                                        ${file.name}
                                    </div>
                                </div>
                            `;
                        }
                    }
                });
                filePreviewHtml += '</div>';
            }
            
            const recoAppend = '<div class="direct-chat-msgs right">\n' +
                '    <div class="direct-chat-info clearfix">\n' +
                '        <span class="direct-chat-name text-info pull-right">'+senderFacility+'</span><br>\n' +
                '        <span class="direct-chat-name pull-right">'+senderName+'</span>\n' +
                '        <span class="direct-chat-timestamp pull-left">'+senderCurrentTime+'</span>\n' +
                '    </div>\n' +
                '    <img class="direct-chat-img" title="" src="'+senderImager+'" alt="Message User Image"><!-- /.direct-chat-img -->\n' +
                '    <div class="direct-chat-text">\n' +
                '        '+senderMessage+filePreviewHtml+
                '    </div>\n' +
                '</div>';
            
            // Append to chat if elements exist
            if ($(".reco-body" + (typeof code !== 'undefined' ? code : '')).length > 0) {
                $(".reco-body" + (typeof code !== 'undefined' ? code : '')).append(recoAppend);
            }
            
            // Scroll to bottom if element exists
            if (typeof code !== 'undefined' && document.getElementById(code)) {
                var objDiv = document.getElementById(code);
                objDiv.scrollTop = objDiv.scrollHeight;
            }
            
            // Clear message input
            $("#message").val('').attr('placeholder','Type Message...');
            
            // Clear uploaded files from memory for this message
            fileIds.forEach(fileId => {
                window.uploadedFiles.delete(fileId);
            });

             console.log("near ajax formData", formData);
        
            // Send to server with files
            $.ajax({
                url: "{{ url('doctor/feedback') }}",
                type: 'post',
                data: formData,
                processData: false, // Important for file upload
                contentType: false, // Important for file upload
                success: function(data) {
                    console.log("Message and files sent successfully:", data);
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
