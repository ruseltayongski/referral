@extends('layouts.app')

@section('content')
    <?php $user = Session::get('auth'); ?>
    <!-- VUE Scripts -->
    <script src="{{ asset('public/js/app_reco.js?version=').date('YmdHis') }}" defer></script>
    <input type="hidden" id="doh_logo" value="{{ asset('resources/img/doh.png') }}">
    <input type="hidden" id="receiver_pic" value="{{ asset('resources/img/receiver.png') }}">
    <input type="hidden" id="sender_pic" value="{{ asset('resources/img/sender.png') }}">
    <input type="hidden" id="facility_name" value="{{ \App\Facility::find($user->facility_id)->name }}">
    <input type="hidden" id="archived_reco_page" value="true">
    <div id="app_reco">
        <reco-app :user="{{ $user }}"></reco-app>
    </div>
@endsection

@section('js')
    <script>
        // tinymce.init({
        //     selector: "#mytextarea",
        //     plugins: "emoticons autoresize",
        //     toolbar: "emoticons",
        //     toolbar_location: "bottom",
        //     menubar: false,
        //     statusbar: false
        // });

        $(document).ready(function() {
            let currentFiles = [];
            let currentIndex = 0;
            let realtimeIndex = 0
            let isfeedbackview = false;
            //add this function to handle file preview setup in the global scope
            window.setupfeedbackFilePreview = function(files, index, code) {
            //    console.log("Reco.vue messages:", globalFiles);
            
                currentFiles = [];
                getAllFilesFromFeedback(code, files, index);
            };

            function getAllFilesFromFeedback(feedbackcode, clickedFileInfo, startIndex) {
                const targetUrl = Array.isArray(clickedFileInfo) ? 
                clickedFileInfo[startIndex] : clickedFileInfo;
                
                const decodedTargetUrl = decodeURIComponent(targetUrl);
                currentIndex = globalFiles.findIndex(file => file === decodedTargetUrl);
            
                if(currentIndex === -1) {
                    console.warn('File not found in server list Using startIndex:', startIndex);
                    currentIndex = startIndex < globalFiles.length ? startIndex : 0;
                }

                if (!currentFiles.length) {
                    currentFiles = globalFiles;
                }

                RecoshowFilePreview();
            }

            $('#prevBtn').click(function() {
                if(currentIndex > 0) {
                    currentIndex--;
                    realtimeIndex= null;
                    RecoshowFilePreview();
                }
            });

            $('#nextBtn').click(function() {
                if(currentIndex < currentFiles.length - 1){
                    currentIndex++;
                    realtimeIndex= null;
                    RecoshowFilePreview();
                }
            })

            $('#downloadBtn').click(function() {
                if(currentFiles.length > 0){
                    const fileUrl = currentFiles[currentIndex];
                    const fileName = fileUrl.split('/').pop();

                    const link = document.createElement('a');
                    link.href = fileUrl;
                    link.download = fileName;
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            })

            $(document).keydown(function(e) {
                if ($('#filePreviewContentReco').hasClass('in')) {
                    if (e.key === 'ArrowLeft') {
                        $('#prevBtn').click();
                    } else if (e.key === 'ArrowRight') {
                        $('#nextBtn').click();
                    } else if (e.key === 'Escape') {
                        $('#filePreviewContentReco').modal('hide');
                    }
                }
            });


            // function RecoshowFilePreview() {
            //     if (!currentFiles || currentFiles.length === 0) return;
            //     const fileData = currentFiles[currentIndex];
            //     let fileUrl = '';
            //     let filename = '';
            //     let extension = '';

            //     if (typeof fileData === 'object' && fileData.url) {
            //         // Blob-style file object
            //         fileUrl = fileData.url;
            //         filename = fileData.name || 'unknown';
            //         extension = (filename.split('.').pop() || '').toLowerCase();
            //     } else if (typeof fileData === 'string') {
            //         // Static file URL
            //         fileUrl = fileData;
            //         filename = fileUrl.split('/').pop() || 'unknown';
            //         extension = (filename.split('.').pop() || '').toLowerCase();
            //     }

            //     $('#fileCounter').text(`${currentIndex + 1} of ${currentFiles.length}`);
            //     $('#filePreviewContent').html('');

            //     if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            //         $('#filePreviewContent').html(`
            //             <img src="${fileUrl}" alt="${filename}" 
            //                 style="max-width: 100%; max-height: 400px; object-fit: contain;">
            //         `);
            //     } else if (extension === 'pdf') {
            //         $('#filePreviewContent').html(`
            //             <embed src="${fileUrl}" type="application/pdf" 
            //                 style="width: 100%; height: 400px;">
            //         `);
            //     } else {
            //         $('#filePreviewContent').html(`
            //             <div style="text-align: center; padding: 50px;">
            //                 <div style="font-size: 48px; color: #ccc; margin-bottom: 20px;">
            //                     <span class="glyphicon glyphicon-file"></span>
            //                 </div>
            //                 <h4>${filename}</h4>
            //                 <p>Preview not available for this file type.</p>
            //                 <a href="${fileUrl}" target="_blank" class="btn btn-primary">
            //                     <span class="glyphicon glyphicon-download-alt"></span>
            //                     Download to View
            //                 </a>
            //             </div>
            //         `);
            //     }
            // }

            // $(document).keydown(function(e) {
            //     if ($('#filePreviewContentReco').hasClass('in')) {
            //         if (e.key === 'ArrowLeft') {
            //             $('#prevBtn').click();
            //         } else if (e.key === 'ArrowRight') {
            //             $('#nextBtn').click();
            //         } else if (e.key === 'Escape') {
            //             $('#filePreviewContentReco').modal('hide');
            //         }
            //     }
            // });


             function RecoshowFilePreview() {
                if (!currentFiles || currentFiles.length === 0) return;
                
                const fileData = currentFiles[currentIndex];
                let fileUrl = '';
                let filename = '';
                let extension = '';

                if (typeof fileData === 'object' && fileData.url) {
                    fileUrl = fileData.url;
                    filename = fileData.name || 'unknown';
                    extension = (filename.split('.').pop() || '').toLowerCase();
                } else if (typeof fileData === 'string') {
                    fileUrl = fileData;
                    filename = fileUrl.split('/').pop() || 'unknown';
                    extension = (filename.split('.').pop() || '').toLowerCase();
                }

                // Update counter
                $('#fileCounter').text(`${currentIndex + 1} of ${currentFiles.length}`);
                
                // Update navigation buttons
                $('#prevBtn').prop('disabled', currentIndex === 0);
                $('#nextBtn').prop('disabled', currentIndex === currentFiles.length - 1);
                
                // Show loading
                $('#filePreviewContent').html('<div class="loading-spinner"></div>');
                
                 $('.file-preview-container').css({
                    'background-image': '',
                    'background-size': '',
                    'background-repeat': '',
                    'background-position': ''
                });
                
                ThumbnailsFeedbackVue();
                // Preview content based on file type
                if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'].includes(extension)) {
                    const img = new Image();
                    img.onload = function() {

                         // FIXED: Apply background to the container and clear content
                        $('.file-preview-container').css({
                            'background-image': `url("${fileUrl}")`,
                            'background-size': 'cover',
                            'background-repeat': 'no-repeat',
                            'background-position': 'center',
                            'background-color': 'rgba(0, 0, 0, 0.7)', 
                            'backdrop-filter': 'blur(8px)', 
                            '-webkit-backdrop-filter': 'blur(8px)'
                        });
                        
                        $('#filePreviewContent').html(`
                            <img src="${fileUrl}" alt="${filename}" class="preview-image">
                        `);
                    };
                    img.onerror = function() {
                        showFileError(filename);
                    };
                    img.src = fileUrl;
                } else if (extension === 'pdf') {
                    $('#filePreviewContent').html(`
                        <embed src="${fileUrl}" type="application/pdf" class="preview-pdf">
                    `);
                } else if (['mp4', 'webm', 'ogg'].includes(extension)) {
                    $('#filePreviewContent').html(`
                        <video controls class="preview-image" style="max-width: calc(100vw - 40px); max-height: calc(100vh - 160px);">
                            <source src="${fileUrl}" type="video/${extension}">
                            Your browser does not support the video tag.
                        </video>
                    `);
                } else if (['mp3', 'wav', 'ogg', 'flac'].includes(extension)) {
                    $('#filePreviewContent').html(`
                        <div class="file-icon-container">
                            <div class="file-icon">
                                <span class="glyphicon glyphicon-music"></span>
                            </div>
                            <div class="file-name">${filename}</div>
                            <audio controls style="width: 100%; margin-top: 20px;">
                                <source src="${fileUrl}" type="audio/${extension}">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    `);
                } else {
                    showUnsupportedFile(filename, fileUrl);
                }
            }

            function showFileError(filename) {
                $('#filePreviewContent').html(`
                    <div class="file-icon-container">
                        <div class="file-icon">
                            <span class="glyphicon glyphicon-warning-sign" style="color: #ff6666;"></span>
                        </div>
                        <div class="file-name">${filename}</div>
                        <div class="file-message">Failed to load file</div>
                        <button class="btn btn-primary" onclick="location.reload()">
                            <span class="glyphicon glyphicon-refresh"></span>
                            Retry
                        </button>
                    </div>
                `);
            }

            function showUnsupportedFile(filename, fileUrl) {
                $('#filePreviewContent').html(`
                    <div class="file-icon-container">
                        <div class="file-icon">
                            <span class="glyphicon glyphicon-file"></span>
                        </div>
                        <div class="file-name">${filename}</div>
                        <div class="file-message">Preview not available for this file type</div>
                        <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                            <span class="glyphicon glyphicon-download-alt"></span>
                            Download to View
                        </a>
                    </div>
                `);
            }

            let thumbPageStart = 0;
            const thumbPageSize = 22;

            function ThumbnailsFeedbackVue(){
                const container = $('#fileThumbnails');
                container.empty();

                if(currentIndex < thumbPageStart){
                    thumbPageStart = Math.max(currentIndex - thumbPageSize + 1, 0);
                }else if(currentIndex >= thumbPageStart + thumbPageSize){
                    thumbPageStart = currentIndex - thumbPageSize + 1;
                }

                let end = Math.min(thumbPageStart + thumbPageSize, currentFiles.length);
                
                for (let i = thumbPageStart; i < end; i++) {
                    const file = currentFiles[i];
                    let url = typeof file === 'object' ? file.url : file;
                    const extension = (url.split('.').pop() || '').toLowerCase();

                    let thumbHtml = '';
                    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension)) {
                        thumbHtml = `<img src="${url}" alt="thumb">`;
                    } else if (extension === 'pdf') {
                        thumbHtml = `<embed src="${url}#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" />`;
                    } else {
                        thumbHtml = `<div style="width:100%;height:100%;background:#555;color:white;display:flex;align-items:center;justify-content:center;font-size:12px;">${extension.toUpperCase()}</div>`;
                    }

                    const isActive = (currentIndex === i);
                    const thumb = $(`<div class="file-thumbnail ${isActive ? 'active' : 'inactive-thumbnail'}" data-index="${i}">${thumbHtml}</div>`);

                    thumb.on('click', function () {
                        currentIndex = parseInt($(this).attr('data-index'));
                        RecoshowFilePreview();
                    });

                    container.append(thumb);
                }
            }

            // Handle modal events
            $('#filePreviewContentReco').on('shown.bs.modal', function() {
                $('body').addClass('modal-open');
            });

            $('#filePreviewContentReco').on('hidden.bs.modal', function() {
                $('body').removeClass('modal-open');
            });

             $('#feedbackModal').on('hidden.bs.modal', function () {
                currentIndex = 0;        
                currentFiles = [];       
                $('#fileThumbnails').empty();
                
                // Clean up button handlers
                $('#prevBtn').off('click');
                $('#nextBtn').off('click');

                $(document).off('keydown.filePreview'); 
            });

        }); 

    
    </script>
@endsection
