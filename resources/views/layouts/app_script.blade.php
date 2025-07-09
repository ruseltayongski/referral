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
</style>

<script>
    // tinymce.init({
    //     selector: ".mytextarea1",
    //     plugins: "emoticons autoresize",
    //     toolbar: "emoticons",
    //     toolbar_location: "bottom",
    //     menubar: false,
    //     statusbar: false,
    //     setup: function (editor) {
    //         editor.on('init', function () {
    //             editor.getContainer().style.width = "100%";
    //         });
    //     }
    // });

    // let currentFile = null;
    // let currentEditor = null;
    // let uploadedFiles = new Map();

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
    //                 input.setAttribute('multiple', true);
                    
    //                 input.onchange = function() {
    //                     var file = this.files;
                        
    //                     if (file) {
    //                         var allowedTypes = [
    //                             'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
    //                             'image/bmp', 'image/webp', 'image/svg+xml', 'application/pdf'
    //                         ];

    //                         if (!allowedTypes.includes(file.type)) {
    //                             alert('Only image files and PDF files are allowed');
    //                             return;
    //                         }

    //                         if (files.length > 0) {
    //                             // Process each selected file
    //                             for (let i = 0; i < files.length; i++) {
    //                                 processFile(files[i], editor);
    //                             }
    //                         }

    //                         const fileId = 'file-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

    //                         // uploadedFiles.set(fileId, file);

    //                         currentFile = file;
    //                         showFilePreview(file);
    //                         // Create local URL for the file (no server upload)
    //                         var fileURL = URL.createObjectURL(file);

    //                         // Set a fixed size for consistency
    //                         var previewWidth = '80px';
    //                         var previewHeight = '80px';
    //                         var commonStyle = `style="width: 80px; cursor:pointer; height: 80px; object-fit: contain; display:inline-block; margin: 0; padding: 0; border:1px solid green;"`;

    //                         editor.insertContent(`<div style="display:flex; flex-wrap:wrap; gap:6px;">`);
    //                         console.log("file pdf image", file);
    //                         // For each file inside your loop or upload handler
    //                         if (file.type.startsWith('image')) {
    //                             editor.insertContent(`
    //                                 <div contenteditable="false" style="display:flex; flex-direction:column; align-items:center; border:1px solid #ccc; border-radius:4px; width:80px; padding:4px;">
    //                                     <img src="${fileURL}" alt="${file.name}" style="width:100%; height:auto; max-height:60px;" data-file-id="${fileId}" />
    //                                     <div title="${file.name}" style="font-size:10px; margin-top:2px; width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">
    //                                         ${file.name}
    //                                     </div>
    //                                 </div>
    //                             `);
    //                         } else if (file.type === 'application/pdf') {
    //                           console.log("file.name", file.name);
    //                             editor.insertContent(`
    //                                 <div contenteditable="false" style="display:flex; flex-direction:column; align-items:center; border:1px solid #ccc; border-radius:4px; width:80px; padding:4px;">
    //                                     <a href="${fileURL}" target="_blank">
    //                                         <img src="{{ asset('public/fileupload/pdffile.png') }}" alt="PDF File" style="width:100%; height:auto; max-height:60px;" data-file-id="${fileId}"/>
    //                                     </a>
    //                                     <div title="${file.name}" style="font-size:10px; margin-top:2px; width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">
    //                                         ${file.name}
    //                                     </div>
    //                                 </div>
    //                             `);
    //                         }

    //                         // End of container
    //                         editor.insertContent(`</div>`);

    //                     }
    //                 };
                    
    //                 input.click();
    //             }
    //         });
            
    //         editor.ui.registry.addIcon('custom-upload', '<i class="fa fa-upload"></i>');
    //            // Add click handler for inserted files
    //             editor.on('click', function(e) {
    //                 const target = e.target;
                    
    //                  console.log("sample file click:", target);

    //                 if (target.tagName === 'IMG' && target.getAttribute('data-file-id')) {
    //                     const fileId = target.getAttribute('data-file-id');
    //                     const storedFile = uploadedFiles.get(fileId);
                        
    //                      if (storedFile) {
    //                         // Show preview modal for the clicked file
    //                         showFilePreview(storedFile, true); 
    //                     }
    //                 }
    //             });
                
    //             // Add double-click handler as alternative
    //             editor.on('dblclick', function(e) {
    //                 const target = e.target;
    //                 if (target.tagName === 'IMG' && target.getAttribute('data-file-id')) {
    //                     const fileId = target.getAttribute('data-file-id');
    //                     const storedFile = uploadedFiles.get(fileId);
                        
    //                     if (storedFile) {
    //                         showFilePreview(storedFile, true);
    //                     }
    //                 }
    //             });
            
    //         editor.on('init', function () {
    //             editor.getContainer().style.width = "100%";
    //         });
    //     }
    // });

    window.uploadedFiles = window.uploadedFiles || new Map();
    let currentEditor = null;
    let currentFile = null;

    // TinyMCE initialization for selecting files (pdf, images) and input text 
    tinymce.init({
        selector: ".mytextarea1",
        plugins: "emoticons autoresize",
        toolbar: "emoticons uploadfile",
        toolbar_location: "bottom",
        menubar: false,
        statusbar: false,
        automatic_uploads: true,
        setup: function (editor) {

            // Add custom upload button
            editor.ui.registry.addButton('uploadfile', {
                icon: 'upload',
                tooltip: 'Upload Image or PDF',
                onAction: function() {
                    // Trigger file picker
                    currentEditor = editor;
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*,.pdf');
                    input.setAttribute('multiple', true); // Allow multiple files
                    
                    input.onchange = function() {
                        var files = this.files;
                        if (files.length > 0) {
                             console.log("editor:", editor);
                            // Process each selected file
                            for (let i = 0; i < files.length; i++) {
                                processFile(files[i], editor);
                            }
                        }
                    };
                    
                    input.click();
                }
            });
            
            editor.ui.registry.addIcon('custom-upload', '<i class="fa fa-upload"></i>');
            
            // Add click handler for inserted files
            editor.on('click', function(e) {
                const target = e.target;

                if (target.tagName === 'IMG' && target.getAttribute('data-file-id')) {
                    const fileId = target.getAttribute('data-file-id');
                    const storedFile = window.uploadedFiles.get(fileId);
                    
                    if (storedFile) {
                         $('#filePreviewModalReco').modal('show');
                        // Show preview modal for the clicked file
                        showFilePreview(storedFile, true); 
                    }
                }
            });
            
            // Add double-click handler as alternative
            editor.on('dblclick', function(e) {
                const target = e.target;
                if (target.tagName === 'IMG' && target.getAttribute('data-file-id')) {
                    const fileId = target.getAttribute('data-file-id');
                    const storedFile = window.uploadedFiles.get(fileId);
                    
                    if (storedFile) {
                         $('#filePreviewModalReco').modal('show');
                        showFilePreview(storedFile, true);
                    }
                }
            });
            
            editor.on('init', function () {
                editor.getContainer().style.width = "100%";
            });
        }
    });

    //Function to process individual files
    // function processFile(file, editor) {
    //     var allowedTypes = [
    //         'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
    //         'image/bmp', 'image/webp', 'image/svg+xml', 'application/pdf'
    //     ];

    //     if (!allowedTypes.includes(file.type)) {
    //         alert('Only image files and PDF files are allowed');
    //         return;
    //     }

    //     const fileId = 'file-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

    //     // Store the file in the Map
    //     window.uploadedFiles.set(fileId, file);
        
    //     // Show file preview
    //     showFilePreview(file);
        
    //     // Create local URL for the file (no server upload)
    //     var fileURL = URL.createObjectURL(file);

    //     console.log("file pdf image", file);

    //     // editor.insertContent(`<div style="display:flex; flex-wrap:wrap; gap:6px;">`);
    //     console.log("file pdf image", file);
    //     // For each file inside your loop or upload handler
    //     if (file.type.startsWith('image')) {
    //         console.log("fileId:", fileId);
    //         editor.insertContent(`
    //             <div contenteditable="false" style="display:inline-block; flex-direction:column; align-items:center; border:1px solid #ccc; border-radius:4px; width:80px; padding:4px;">
    //                 <img src="${fileURL}" alt="${file.name}" style="width:100%; height:auto; max-height:60px;" data-file-id="${fileId}" />
    //                 <div title="${file.name}" style="font-size:10px; margin-top:2px; width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">
    //                     ${file.name}
    //                 </div>
    //             </div>
    //         `);
    //     } else if (file.type === 'application/pdf') {
    //         console.log("file.name", file.name);
    //         editor.insertContent(`
    //             <div contenteditable="false" style="display:inline-block; flex-direction:column; align-items:center; border:1px solid #ccc; border-radius:4px; width:80px; padding:4px;">
    //                 <a href="${fileURL}" target="_blank">
    //                     <img src="{{ asset('public/fileupload/pdffile.png') }}" alt="PDF File" style="width:100%; height:auto; max-height:60px;" data-file-id="${fileId}"/>
    //                 </a>
    //                 <div title="${file.name}" style="font-size:10px; margin-top:2px; width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">
    //                     ${file.name}
    //                 </div>
    //             </div>
    //         `);
    //     }
    //     // End of container
    //     // editor.insertContent(`</div>`);
        
    //     editor.insertContent(`<br>`); 
    // }

     let fileUploadBatch = false;
    let fileUploadTimeout = null;

    //Function to process individual files
    function processFile(file, editor) {
        var allowedTypes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
            'image/bmp', 'image/webp', 'image/svg+xml', 'application/pdf'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Only image files and PDF files are allowed');
            return;
        }

        const fileId = 'file-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

        // Store the file in the Map
        window.uploadedFiles.set(fileId, file);
        
        // Show file preview
        showFilePreview(file);
        
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

        // For each file inside your loop or upload handler
        if (file.type.startsWith('image')) {
            console.log("fileId:", fileId);
            editor.insertContent(`<span contenteditable="false" style="display:inline-block; vertical-align:top; border:1px solid #ccc; border-radius:4px; width:80px; padding:4px; margin:2px;">
                    <a href="${fileURL}" target="_blank">
                        <img src="{{ asset('public/fileupload/imageFile2.png') }}" alt="${file.name}" style="width:100%; height:auto; max-height:60px; display:block;" data-file-id="${fileId}" />
                    </a>
                    <div title="${file.name}" style="font-size:0.6em; margin-top:2px; width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">
                        ${truncatedFilename}
                    </div>
                </span>`);
        } else if (file.type === 'application/pdf') {
            console.log("file.name", file.name);
            editor.insertContent(`<span contenteditable="false" style="display:inline-block; vertical-align:top; border:1px solid #ccc; border-radius:4px; width:80px; padding:4px; margin:2px;">
                    <a href="${fileURL}" target="_blank">
                        <img src="{{ asset('public/fileupload/pdffile.png') }}" alt="PDF File" style="width:100%; height:auto; max-height:60px; display:block;" data-file-id="${fileId}"/>
                    </a>
                    <div title="${file.name}" style="font-size:0.6em; margin-top:2px; width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">
                        ${truncatedFilename}
                    </div>
                </span>`);
        }
        
        // Set batch flag
        fileUploadBatch = true;
        
        // Clear existing timeout
        if (fileUploadTimeout) {
            clearTimeout(fileUploadTimeout);
        }
        
        // Add line break only after all files are processed (500ms delay)
        fileUploadTimeout = setTimeout(() => {
            if (fileUploadBatch) {
                editor.insertContent('<br>');
                fileUploadBatch = false;
            }
        }, 500);
    }

    function showFilePreview(file) {
        console.log('file uplaod::', file);
        const previewContainer = document.getElementById('filePreviewContainer');
        const modalTitle = document.getElementById('filePreviewModalLabel');
        
        // Clear previous content and show loading state
        previewContainer.innerHTML = `
            <div class="text-center">
                <div class="loading-spinner"></div>
                <p class="text-muted" style="margin-top: 10px;">Loading preview...</p>
            </div>
        `;
        
        // Create file URL
        const fileURL = URL.createObjectURL(file);
        
        // Determine file type badge
        const fileTypeBadge = file.type.startsWith('image/') ? 
            '<span class="file-type-badge image">Image</span>' : 
            '<span class="file-type-badge pdf">PDF</span>';
        
        // Set modal title with Bootstrap 3 styling
        modalTitle.innerHTML = `
            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
            File Preview ${fileTypeBadge}
        `;
        
        // Simulate slight delay for better UX (optional)
        setTimeout(() => {
            if (file.type.startsWith('image/')) {
                // Image preview with Bootstrap 3 styling
                previewContainer.innerHTML = `
                    <div class="row">
                        <div class="col-md-12">
                            <div class="thumbnail">
                                <img src="${fileURL}" alt="${file.name}" class="file-preview-image text-center" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="file-info">
                                <div class="info-row">
                                    <span class="info-label">
                                        <span class="glyphicon glyphicon-file" aria-hidden="true"></span>
                                        File Name:
                                    </span>
                                    <span class="info-value">${file.name}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">
                                        <span class="glyphicon glyphicon-tags" aria-hidden="true"></span>
                                        File Type:
                                    </span>
                                    <span class="info-value">${file.type}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">
                                        <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                                        File Size:
                                    </span>
                                    <span class="info-value">${formatFileSize(file.size)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (file.type === 'application/pdf') {
                // PDF preview with Bootstrap 3 styling
                previewContainer.innerHTML = `
                    <div class="row">
                        <div class="col-md-12">
                            <div class="embed-responsive embed-responsive-4by3">
                                <iframe src="${fileURL}" class="embed-responsive-item pdf-preview" type="application/pdf"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <div class="file-info">
                                <div class="info-row">
                                    <span class="info-label">
                                        <span class="glyphicon glyphicon-file" aria-hidden="true"></span>
                                        File Name:
                                    </span>
                                    <span class="info-value">${file.name}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">
                                        <span class="glyphicon glyphicon-tags" aria-hidden="true"></span>
                                        File Type:
                                    </span>
                                    <span class="info-value">${file.type}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">
                                        <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                                        File Size:
                                    </span>
                                    <span class="info-value">${formatFileSize(file.size)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }, 300);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

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