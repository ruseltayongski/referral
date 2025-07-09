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

                //        $path = self::securedFile($file_link);
                //        $file_name = basename($path);
               
                $path = [];
                $file_name = [];

                if($file_link != null && $file_link != "") {
                    $explode = explode("|",$file_link);
                    foreach($explode as $link) {
                        $path_tmp = \App\Http\Controllers\doctor\ReferralCtrl::securedFile($link);
                        if($path_tmp != '') {
                            array_push($path, $path_tmp);
                            array_push($file_name, basename($path_tmp));
                        }
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
                $filePaths =  $path;
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $pdfExtensions = ['pdf'];

                // Filter out empty file paths
                $filePaths = array_filter($filePaths, function ($f) {
                    return trim($f) !== '';
                });
                
            @endphp

            <div class="direct-chat-text">
                {{-- File Preview --}}
                @if(count($filePaths) > 0)
                    <div class="attachment-wrapper" style="margin-bottom: 10px; white-space: nowrap; overflow-x: auto;">
                        @foreach($filePaths as $file)
                            @if(trim($file) !== '')
                                @php
                                    $extension = strtolower(pathinfo(parse_url($file, PHP_URL_PATH), PATHINFO_EXTENSION));
                                    $fileName = basename($file);
                                    $displayName = strlen($fileName) > 10 ? substr($fileName, 0, 7) . '...' : $fileName;
                                @endphp

                                <div style="display: inline-block; text-align: center; width: 60px; margin-right: 5px;">
                                    <a href="{{ $file }}" target="_blank" rel="noopener">
                                        <img class="attachment-thumb file-preview-trigger"
                                            src="{{ in_array($extension, $pdfExtensions) ? asset('public/fileupload/pdffile.png') : asset('public/fileupload/imageFile.png') }}"
                                            alt="{{ strtoupper($extension) }} file"
                                            data-file-type="{{ $extension }}"
                                            data-file-url="{{ $file }}"
                                            data-file-name="{{ $fileName }}"
                                            style="width: 50px; height: 50px; object-fit: contain; border:1px solid green;">
                                    </a>
                                    <div style="font-size: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $fileName }}">
                                        {{ $displayName }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Message Text --}}
                @if(!empty($row->message))
                    @if($row->sender == $user->id)
                        <div class="caption-text" style="margin-top: 5px; color: white;">
                            {!! nl2br(e($row->message)) !!}
                        </div>
                    @else
                        <div class="caption-text" style="margin-top: 5px;">
                            {!! nl2br(e($row->message)) !!}
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    @endif
</div>



<style>
.modal.modal-front {
    z-index: 1060; /* Higher than Bootstrap's default modal z-index of 1050 */
}
.modal-backdrop.modal-backdrop-front {
    z-index: 1055; /* Slightly below the modal but above default backdrop */
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

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

