<!-- Message. Default to the left -->
<?php
    $user = \Illuminate\Support\Facades\Session::get('auth');
    $session = 0;
    use App\Feedback;
?>
<div class="reco-body{{ $code }}">
    @if(count($data)>0)
        @foreach($data as $row)
            <?php
                $pull = 'right';
                $position = 'left';
                if($session==0){
                    \Illuminate\Support\Facades\Session::put('last_scroll_id',$row->id);
                    $session = 1;
                }
                if($user->id==$row->sender){
                    $pull = 'left';
                    $position = 'right';
                }

                $file_link = (Feedback::select('filename')->where('id', $row->id)->first())->filename;
            
                $filePaths = [];
                $file_names = [];

                if(!empty($file_link)){
                    $filePaths = explode('|', $file_link);

                    $filePaths = array_filter($filePaths, function($path) {
                       return !empty(trim($path));    
                    });

                    foreach ($filePaths as $path) {
                        $file_names[] = basename($path);
                    }
                }

            
            ?>
            <div class="direct-chat-msgs {{ $position }}">
                <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name text-info pull-{{ $position }}">{{ $row->facility }}</span><br>
                    <span class="direct-chat-name pull-{{ $position }}">{{ ucwords(mb_strtolower($row->fname)) }} {{ ucwords(mb_strtolower($row->lname)) }}</span>
                    <span class="direct-chat-timestamp pull-{{ $pull }}">{{ date('d M h:i a',strtotime($row->date)) }}</span>
                </div>
                <!-- /.direct-chat-info -->
                <?php
                    $icon = 'receiver.png';
                    if($user->id==$row->sender)
                        $icon = 'sender.png';
                ?>
                
                <img class="direct-chat-img" title="{{ $row->facility }}" src="{{ url('resources/img/'.$icon) }}" alt="Message User Image"><!-- /.direct-chat-img -->
                @php
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $pdfExtensions = ['pdf'];
                @endphp
                <div class="direct-chat-text">
                    {{-- File Preview --}}
                    @if(count($filePaths) > 0)
                        <div class="attachment-wrapper" style="margin-bottom: 10px; white-space: nowrap; overflow-x: auto;">
                            @foreach($filePaths as $index => $file)
                                @if(trim($file) !== '')
                                    @php
                                        $extension = strtolower(pathinfo(parse_url($file, PHP_URL_PATH), PATHINFO_EXTENSION));
                                        $fileName = basename($file);
                                        $displayName = strlen($fileName) > 10 ? substr($fileName, 0, 7) . '...' : $fileName;

                                        $fileUrl = asset($file);
                                    @endphp

                                    <div style="display: inline-block; text-align: center; width: 60px; margin-right: 5px;">
                                    <a href="javascript:void(0)" class="file-preview-trigger" 
                                        data-file-type="{{ $extension }}"
                                        data-file-url="{{ $fileUrl }}"
                                        data-file-name="{{ $fileName }}"
                                        data-feedback-code="{{ $row->code }}"
                                        data-file-paths="{{ implode('|', array_map(function($path) { return url($path); }, $filePaths)) }}"
                                        data-current-index="{{ $index }}">
                                        @if(in_array($extension, $imageExtensions))
                                            <img class="attachment-thumb"
                                                src="{{ $fileUrl }}"
                                                alt="{{ strtoupper($extension) }} file"
                                                style="width: 50px; height: 50px; object-fit: cover; border:1px solid green; border-radius: 4px;">
                                        @elseif(in_array($extension, $pdfExtensions))
                                            <img class="attachment-thumb"
                                                src="{{ asset('public/fileupload/pdffile.png') }}"
                                                alt="PDF file"
                                                style="width: 50px; height: 50px; object-fit: contain; border:1px solid green; border-radius: 4px;">
                                        @else
                                            <img class="attachment-thumb"
                                                src="{{ asset('public/fileupload/imageFile2.png') }}"
                                                alt="File"
                                                style="width: 50px; height: 50px; object-fit: contain; border:1px solid green; border-radius: 4px;">
                                        @endif
                                    </a>
                                    <!-- <div style="font-size: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $fileName }}">
                                        {{ $displayName }}
                                    </div> -->
                                </div>

                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Message Text --}}
                    @if(!empty($row->message))
                        @if($row->sender == $user->id)
                            <div class="caption-text" style="margin-top: 5px; color: white;">
                                {!! nl2br($row->message) !!}
                            </div>
                        @else
                            <div class="caption-text" style="margin-top: 5px;">
                                {!! nl2br($row->message) !!}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>

$(document).ready(function() {
    var url = "{{ url('get-feedback-files') }}";
    let currentFiles = [];
    let currentIndex = 0;
    let isfeedbackview = false;

   $('.file-preview-trigger').not('.realtime-file-preview').click(function() {
        const clickedFileUrl = $(this).data('file-url');
        const feedbackCode = $(this).data('feedback-code'); // You'll need to add this data attribute
        
        isfeedbackview = false;
        // Get all files from the same feedback record
        getAllFilesFromFeedback(feedbackCode, clickedFileUrl);
        $('#filePreviewContentReco').modal('show');
    });

    window.setupfeedbackFilePreview = function(filesArray, startIndex, feedbackCode,desc){
        currentFiles = filesArray;
        currentIndex = startIndex;
        isfeedbackview = true;
        console.log("desc:::", desc);
         console.log('Setting up realtime preview:', {
            files: currentFiles,
            index: currentIndex,
            code: feedbackCode
        });

        getAllFilesFromFeedback(feedbackCode, filesArray, desc);

        RecoshowFilePreview();
    }
   
    function getAllFilesFromFeedback(feedbackcode, clickedFileUrl, desc){
        console.log("clickedFileUrl blade:", clickedFileUrl);
        $.ajax({
            url: url, 
            method: 'GET',
            data: {
                 code: feedbackcode,
                 desc: desc
            },
            success: function(response) {
                if(response.success && response.files){
                    currentFiles = response.files;

                    currentIndex = currentFiles.findIndex(file => file.includes(clickedFileUrl.split('/').pop()));
                    if(currentIndex == -1) currentIndex = 0;

                    RecoshowFilePreview();
                }
            },
            error: function(xhr, status, error){
                console.error('Error fetching feedback files:', error);
                // Fallback to single file preview
                currentFiles = [clickedFileUrl];
                currentIndex = 0;
                RecoshowFilePreview();
            }
        })
    }

    $('#prevBtn').click(function() {
        if(currentIndex > 0) {
            currentIndex--;

            RecoshowFilePreview();
        }
    });

    $('#nextBtn').click(function() {
        if(currentIndex < currentFiles.length - 1){
             currentIndex++;
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

    function RecoshowFilePreview() {
        if(currentFiles === 0) return;

        const fileUrl = currentFiles[currentIndex];
        // const filename = fileUrl.split('/').pop();
        const filename = (fileUrl || '').split('/').pop();
        const extension = filename.split('.').pop().toLowerCase();

        $('#fileCounter').text(`${currentIndex + 1} of ${currentFiles.length}`);

        // $('#fileInfo').text(filename);

        $('#filePreviewContent').html('');

        if(['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)){
           $('#filePreviewContent').html(`
                <img src="${fileUrl}" alt="${filename}" 
                     style="max-width: 100%; max-height: 400px; object-fit: contain;">
            `);
        }else if(extension === 'pdf'){
            $('#filePreviewContent').html(`
                <embed src="${fileUrl}" type="application/pdf" 
                       style="width: 100%; height: 400px;">
            `);
        }else{
            $('#filePreviewContent').html(`
                <div style="text-align: center; padding: 50px;">
                    <div style="font-size: 48px; color: #ccc; margin-bottom: 20px;">
                        <span class="glyphicon glyphicon-file"></span>
                    </div>
                    <h4>${filename}</h4>
                    <p>Preview not available for this file type.</p>
                    <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                        <span class="glyphicon glyphicon-download-alt"></span>
                        Download to View
                    </a>
                </div>
            `);
        }
    }

});

</script>

<style>

.modal.modal-front {
    z-index: 1060;
}
.modal-backdrop.modal-backdrop-front {
    z-index: 1055;
}
.attachment-thumb:hover {
    transform: scale(1.1);
    border-color: #007bff !important;
    transition: transform 0.2s;
}

.nav-btn:hover {
    background: rgba(0,0,0,0.9) !important;
    transition: background-color 0.3s;
}

.file-preview-trigger {
    cursor: pointer;
    text-decoration: none;
}

.file-preview-trigger:hover {
    text-decoration: none;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

</style>

