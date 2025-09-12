<style>


/* Center image and make it responsive */
.file-preview-image {
  display: block;
  max-width: 100%;
  height: auto;
  margin: 0 auto;
}

/* Responsive iframe (PDF preview) */
.pdf-preview {
  width: 100%;
  height: 70vh; /* Adjust height as needed */
  border: none;
}

/* Info rows spacing */
.file-info .info-row {
  margin-bottom: 10px;
}

/* Responsive thumbnail container */
.thumbnail {
  text-align: center;
  border: none;
  padding: 10px;
}

/* File type badge */
.file-type-badge {
  padding: 4px 8px;
  font-size: 12px;
  border-radius: 4px;
  margin-left: 10px;
}
.file-type-badge.image {
  background-color: #5bc0de;
  color: white;
}
.file-type-badge.pdf {
  background-color: #d9534f;
  color: white;
}

/* Spinner style (optional loading) */
.loading-spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #337ab7;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  margin: 0 auto;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}

 .file-display-bar::-webkit-scrollbar {
        height: 6px;
    }

    .file-display-bar::-webkit-scrollbar-track {
        background: #2d2d2d;
    }

    .file-display-bar::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 3px;
    }
 .upload-prompt {
            color: #888;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .upload-prompt.hidden {
            display: none;
        }

</style>

<script>
    window.uploadedFiles = window.uploadedFiles || new Map();
    let currentEditor = null;
    let currentFile = null;
    let currentCode = null;
    let userID = null;
    let telemed = null;
    let VideoApp = null;

    // TinyMCE initialization for selecting files (pdf, images) and input text 
    // tinymce.init({
    //     selector: ".mytextarea1",
    //     plugins: "emoticons autoresize",
    //     toolbar: "emoticons uploadfile",
    //     toolbar_location: "bottom",
    //     menubar: false,
    //     statusbar: false,
    //     automatic_uploads: true,
    //     setup: function (editor) {

    //         // Add custom upload button
    //         editor.ui.registry.addButton('uploadfile', {
    //             icon: 'upload',
    //             tooltip: 'Upload Image or PDF',
    //             onAction: function() {
    //                 // Trigger file picker
    //                 currentEditor = editor;
                    
    //                 var input = document.createElement('input');
    //                 input.setAttribute('type', 'file');
    //                 input.setAttribute('accept', 'image/*,.pdf');
    //                 input.setAttribute('multiple', true); // Allow multiple files
                    
    //                 input.onchange = function() {
    //                     var files = this.files;
    //                     if (files.length > 0) {
    //                          console.log("editor:", editor);
    //                         // Process each selected file
    //                         for (let i = 0; i < files.length; i++) {
    //                             processFileReco(files[i], editor);
    //                         }
    //                     }
    //                 };
                    
    //                 input.click();
    //             }
    //         });
            
    //         editor.ui.registry.addIcon('custom-upload', '<i class="fa fa-upload"></i>');
            
    //         // Add click handler for inserted files
    //         editor.on('click', function(e) {
    //             const target = e.target;

    //             if (target.tagName === 'IMG' && target.getAttribute('data-file-id')) {
    //                 const fileId = target.getAttribute('data-file-id');
    //                 const storedFile = window.uploadedFiles.get(fileId);
                    
    //                 if (storedFile) {
    //                      $('#filePreviewModalReco').modal('show');
    //                     // Show preview modal for the clicked file
    //                     // showFilePreview(storedFile, true); 
    //                 }
    //             }
    //         });
            
    //         // Add double-click handler as alternative
    //         editor.on('dblclick', function(e) {
    //             const target = e.target;
    //             if (target.tagName === 'IMG' && target.getAttribute('data-file-id')) {
    //                 const fileId = target.getAttribute('data-file-id');
    //                 const storedFile = window.uploadedFiles.get(fileId);
                    
    //                 if (storedFile) {
    //                      $('#filePreviewModalReco').modal('show');
    //                     // showFilePreview(storedFile, true);
    //                 }
    //             }
    //         });
            
    //         editor.on('init', function () {
    //             editor.getContainer().style.width = "100%";
    //         });
    //     }
    // });

    function initTinyMCEWithCode(code,userId,videoApp) {
        var url = "<?php echo asset('tracking').'/'; ?>" + code;
        currentCode = code;
        userID = userId;
        VideoApp =  Number(videoApp);;
        // console.log("video appp:", videoApp, videoApp == 0);
       
        $.get(url, function (res) {
            telemed = res.tracking.telemedicine;
            console.log("tracking telemedicine:", res);
            console.log("condition of referrin md", userID == res.referring_md_Status.referring_md);
            console.log("user id:", userID, "action md", res.tracking.action_md);
            // Build toolbar string based on telemedicine
            let toolbarItems = "emoticons uploadfile";
            if (telemed == 0 && (userID == res.tracking.action_md || userID == res.referring_md_Status.referring_md) && VideoApp !== 0) {
                toolbarItems += " callbutton"; // add call button only if telemedicine is 0
            }
            
            console.log("code latest", currentCode);
            tinymce.init({
                selector: ".mytextarea1",
                plugins: "emoticons autoresize",
                toolbar: toolbarItems,
                toolbar_location: "bottom",
                toolbar_mode: "wrap",
                menubar: false,
                statusbar: false,
                automatic_uploads: true,
                setup: function (editor) {
                    // Call button (only registered if telemedicine == 0)
                    if (telemed == 0) {
                        editor.ui.registry.addIcon('call-icon',
                            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' +
                            '<path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24 11.72 11.72 0 003.69.59 1 1 0 011 1v3.61a1 1 0 01-1 1A17 17 0 013 5a1 1 0 011-1h3.61a1 1 0 011 1 11.72 11.72 0 00.59 3.69 1 1 0 01-.24 1.01l-2.34 2.09z"/>' +
                            '</svg>'
                        );
                        editor.ui.registry.addButton('callbutton', {
                            icon: 'call-icon',
                            tooltip: 'Start Call',
                            onAction: function () {
                                console.log('call button clicked dcsdsdf');
                                telemedicineExamined(
                                    res.tracking.tracking_id,
                                    currentCode,
                                    res.tracking.action_md,
                                    res.tracking.track_referring_md,
                                    res.tracking.activity_id,
                                    res.tracking.type,
                                    userID == res.tracking.action_md ? res.tracking.referred_from : res.tracking.referred_to,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    res.tracking.telemedicine
                                );
                            }
                        });
                    }
                    
                    // Upload button
                    editor.ui.registry.addButton('uploadfile', {
                        icon: 'upload',
                        tooltip: 'Upload Image or PDF',
                        onAction: function () {
                            currentEditor = editor;
                            var input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/*,.pdf');
                            input.setAttribute('multiple', true);
                            input.onchange = function () {
                                var files = this.files;
                                if (files.length > 0) {
                                    for (let i = 0; i < files.length; i++) {
                                        processFileReco(files[i], editor);
                                    }
                                }
                            };
                            input.click();
                        }
                    });

                    // File click + dblclick handlers
                    editor.on('click dblclick', function (e) {
                        const target = e.target;
                        if (target.tagName === 'IMG' && target.getAttribute('data-file-id')) {
                            const fileId = target.getAttribute('data-file-id');
                            const storedFile = window.uploadedFiles.get(fileId);
                            if (storedFile) $('#filePreviewModalReco').modal('show');
                        }
                    });

                    editor.on('init', function () {
                        editor.getContainer().style.width = "100%";
                    });
                }
            });
        });
    }

    let fileUploadBatch = false;
    let fileUploadTimeout = null;

    //Function to process individual files
    function processFileReco(file, editor) {
        var allowedTypes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
            'image/bmp', 'image/webp', 'image/svg+xml', 'application/pdf'
        ];

        if (!editor || typeof editor.insertContent !== 'function') {
            alert('Invalid editor instance or insertContent method not available');
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            alert('Only image files and PDF files are allowed');
            return;
        }

        const fileId = 'file-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

        // Store the file in the Map
        window.uploadedFiles.set(fileId, file);
        
        // Show file preview
        // showFilePreview(file);
        
        // Create local URL for the file (no server upload)
        var fileURL = URL.createObjectURL(file);

        console.log("file pdf image", file);

        function truncateFileName(fileName, maxLength = 12) {
            if (fileName.length <= maxLength) {
                return fileName;
            }
            
            // Get file extension
            const lastDotIndex = fileName.lastIndexOf('.');
            const extension = lastDotIndex !== -1 ? fileName.slice(lastDotIndex) : '';
            const nameWithoutExt = lastDotIndex !== -1 ? fileName.slice(0, lastDotIndex) : fileName;
            
            // Calculate available space for name (total - extension length - 3 dots)
            const availableSpace = maxLength - extension.length - 3;
            
            if (availableSpace <= 0) {
                return fileName.slice(0, maxLength - 3) + '...';
            }
            
            return nameWithoutExt.slice(0, availableSpace) + '...' + extension;
        }

        const truncatedFilename = truncateFileName(file.name, 15);
        let content = '';
        const removeIconStyle = 'position:absolute;top:-8px;right:-8px;background:#f44336;color:#fff;border-radius:50%;padding:0 5px;font-size:12px;cursor:pointer;line-height:1;'

        const fileDisplayBar = document.getElementById('fileDisplayBar');
        const uploadPrompt = document.getElementById('uploadPrompt');
        uploadPrompt.style.display = 'none'; // Hide prompt once files are added

        if (file.type.startsWith('image')) {
            content = `
            <div class="file-preview-wrapper" data-file-id="${fileId}" style="position:relative; display:inline-block; margin:2px;">
                <span class="remove-file-feedback" style="${removeIconStyle}" title="Remove">&times;</span>
                <a href="${fileURL}" target="_blank">
                    <img src="${fileURL}" alt="${file.name}" style="width:80px; height:60px; object-fit:contain; border:1px solid #ccc;" data-file-id="${fileId}" />
                </a>
                <div title="${file.name}" style="font-size:0.6em; width:80px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">
                    ${truncatedFilename}
                </div>
            </div>`;
        } else if (file.type === 'application/pdf') {
            content = `
            <div class="file-preview-wrapper" data-file-id="${fileId}" style="position:relative; display:inline-block; margin:2px;">
                <span class="remove-file-feedback" style="${removeIconStyle}" title="Remove">&times;</span>
                <a href="${fileURL}" target="_blank">
                    <img src="{{ asset('public/fileupload/pdffile.png') }}" alt="PDF File" style="width:80px; height:60px; object-fit:contain; border:1px solid #ccc;" data-file-id="${fileId}" />
                </a>
                <div title="${file.name}" style="font-size:0.6em; width:80px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">
                    ${truncatedFilename}
                </div>
            </div>`;
        }
        
        // editor.insertContent(content);

        const fileWrapper = document.createElement('span');
        fileWrapper.innerHTML = content;
        fileDisplayBar.appendChild(fileWrapper);

        fileUploadBatch = true;

        // Clear existing timeout
        if (fileUploadTimeout) {
            clearTimeout(fileUploadTimeout);
        }
    }

    $(document).on('click', '.remove-file-feedback', function () {
        const fileWrapper = $(this).closest('.file-preview-wrapper');
        const fileId = fileWrapper.attr('data-file-id');

        if(fileId && window.uploadedFiles.has(fileId)){
            window.uploadedFiles.delete(fileId);
            fileWrapper.remove();

            if($('#fileDisplayBar .file-preview-wrapper').length === 0) {
                $('#uploadPrompt').show();
            }
        }
    });
    
    $('#feedbackModal').on('hidden.bs.modal', function () {
        // tinymce.get($('.mytextarea1').attr('id')).setContent('');
        const editor = tinymce.get($('.mytextarea1').attr('id'));
        if (editor) {
            editor.setContent('');
        }
        window.uploadedFiles.clear();
        $('#filePreviewModalReco').modal('hide');
        tinymce.remove('.mytextarea1'); 
        $('.direct-chat-messages').html('Loading...');
        $('#feedbackForm')[0].reset();
        $('#fileDisplayBar').html('<div class="upload-prompt" id="uploadPrompt"></div>');

        // 🔥 RESET THESE
        currentIndex = 0;
        currentFiles = [];

    });

    $(".select2").select2({ 
        width: '100%',
    });

    $(document).ready(function() {
        $('.modal-select2').select2({
            width: '100%',
            // dropdownParent: $('#normalFormModal'),
            // dropdownParent2: $('#pregnantFormModal'),
        });
        
    });
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    
    var path_gif = "<?php echo asset('resources/img/loading.gif'); ?>";
    var loading = '<center><img src="'+path_gif+'" alt=""></center>';

    var urlParams = new URLSearchParams(window.location.search);
    var query_string_search = urlParams.get('search') ? urlParams.get('search') : '';
    var query_string_date_range = urlParams.get('date_range') ? urlParams.get('date_range') : '';
    var query_string_typeof_vaccine = urlParams.get('typeof_vaccine_filter') ? urlParams.get('typeof_vaccine_filter') : '';
    var query_string_muncity = urlParams.get('muncity_filter') ? urlParams.get('muncity_filter') : '';
    var query_string_facility = urlParams.get('facility_filter') ? urlParams.get('facility_filter') : '';
    var query_string_department = urlParams.get('department_filter') ? urlParams.get('department_filter') : '';
    var query_string_option = urlParams.get('option_filter') ? urlParams.get('option_filter') : '';

    $(".pagination").children().each(function(index){
        var _href = $($(this).children().get(0)).attr('href');

        if(_href){
            _href = _href.replace("http:",location.protocol);
            $($(this).children().get(0)).attr('href',_href+'&search='+query_string_search+'&date_range='+query_string_date_range+'&typeof_vaccine_filter='+query_string_typeof_vaccine+'&muncity_filter='+query_string_muncity+'&facility_filter='+query_string_facility+'&department_filter='+query_string_department+'&option_filter='+query_string_option);
        }

    });

    function refreshPage(){
        <?php
        use Illuminate\Support\Facades\Route;
        $current_route = Route::getFacadeRoot()->current()->uri();
        ?>
        $('.loading').show();
        window.location.replace("<?php echo asset($current_route) ?>");
    }

    function loadPage(){
        $('.loading').show();
    }

    function openLogoutTime(){
        var login_time = "<?php echo date('H:i'); ?>";
        var logout_time = "<?php echo date('H:i',strtotime($logout_time)); ?>";
        var input_element = $("#input_time_logout");
        input_element.attr({
            "min" : login_time
        });
        input_element.val(logout_time);
    }

    // Set the date we're counting down to
    var countDownDate = new Date("{{ $logout_time }}").getTime();
    // Update the count down every 1 second
    var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output the result in an element with id="demo"
        try{
            document.getElementById("logout_time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
        }
        catch(e) {}

        // If the count down is over, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("logout_time").innerHTML = "EXPIRED";
            window.location.replace("<?php echo asset('/logout') ?>");
        }
    }, 1000);

    @if(Session::get('logout_time'))
    Lobibox.notify('success', {
        title: "",
        msg: "Successfully set logout time",
        size: 'mini',
        rounded: true
    });
    <?php Session::put("logout_time",false); ?>
    @endif


    //Get the button
    var mybutton = document.getElementById("myBtn");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        try {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        } catch(e) {}
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        $('body,html').animate({
            scrollTop : 0 // Scroll to top of body
        }, 500);
    }

    $('.select_facility').on('change',function() {
        var id = $(this).val();
        referred_facility = id;
        if(referred_facility){
            var url = "{{ url('location/facility/') }}";
            $.ajax({
                url: url+'/'+id,
                type: 'GET',
                success: function(data){
                    $('.facility_address').html(data.address);
                    $('.select_department').empty()
                        .append($('<option>', {
                            value: '',
                            text : 'Select Department...'
                        }));
                    jQuery.each(data.departments, function(i,val){
                        $('.select_department').append($('<option>', {
                            value: val.id,
                            text : val.description
                        }));
                    });
                    facilityForTelemedicine(data.departments);
                },
                error: function(error){
                    $('#serverModal').modal();
                }
            });
        }
    });

    function facilityForTelemedicine(departments) {
        const telemedicine = parseInt($(".telemedicine").val());
        const checkOPD = departments.find(obj => obj.id === 5);
        if(telemedicine && !checkOPD) {
            Lobibox.alert("error", {
                msg: "This facility is not allowed to perform telemedicine because there is no doctor assigned to the OPD department. Please call this facility for verification."
            });
            const select_facility = $('.select_facility');
            select_facility.val(select_facility.find('option:first').val()).trigger('change');

            const select_department = $('.select_department');
            select_department.empty().append($('<option>', {
                value: '',
                text : 'Select Department...'
            })).trigger('change');
        }
        else if(telemedicine && checkOPD) {
            const select_department = $('.select_department');
            select_department.empty().append($('<option>', {
                value: '5',
                text : 'OPD'
            })).trigger('change');
        }
    }


    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    }

    function getCookie(name) {
        const keyValue = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
    }

    function generateAppointmentKey(length) {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let key = '';

        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            key += characters.charAt(randomIndex);
        }

        return key;
    }

</script>