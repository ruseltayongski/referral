<link href="{{ asset('public/css/telemedicine_panel.css?v=12') }}" rel="stylesheet">
<div class="panel-body panel-scoped-telemedicine">
    <?php
    $position = ["1st","2nd","3rd","4th","5th","6th","7th","8th","9th","10th","11th","12th"];
    $position_count = 0;
    $referred_track = \App\Activity::where("code",$row->code)->where("status","referred")->first();
    $queue_referred = \App\Activity::where('code',$row->code)->where('status','queued')->orderBy('id','desc')->first()->remarks;
    $referred_seen_track = \App\Seen::where("code",$referred_track->code)
        ->where("facility_id",$referred_track->referred_to)
        ->where("created_at",">=",$referred_track->created_at)
        ->exists();
    $referred_queued_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_to",$referred_track->referred_to)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","queued")
        ->exists();
    $referred_accepted_hold = \App\Activity::where("code",$referred_track->code)
        ->where("referred_to",$referred_track->referred_to)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","accepted");
    $referred_accepted_track = $referred_accepted_hold->exists();
    $referred_rejected_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_to",$referred_track->referred_to)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","rejected")
        ->exists();
    $referred_cancelled_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_to",$referred_track->referred_to)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","cancelled")
        ->exists();
    $referred_examined_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_from",$referred_track->referred_from)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","examined")
        ->exists();
    $referred_prescription_hold = \App\Activity::where("code",$referred_track->code)
        ->where("referred_from",$referred_track->referred_from)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","prescription");
    $referred_prescription_track = $referred_prescription_hold->exists();
    $referred_upward_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_from",$referred_track->referred_from)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","upward")
        ->exists();
    $referred_redirected_track = \App\Activity::where("code",$referred_track->code)
        ->where("status","redirected")
        ->exists();
    $referred_treated_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_from",$referred_track->referred_from)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","treated")
        ->exists();
    $referred_followup_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_from",$referred_track->referred_from)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","followup")
        ->exists();
    $referred_end_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_from",$referred_track->referred_from)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","end")
        ->exists();
    $redirected_track = \App\Activity::where("code",$row->code)
        ->where(function($query) {
            $query->where("status","redirected")
                ->orWhere("status","transferred");
        })
        ->get();
    $followup_track = \App\Activity::where("code", $row->code)
        ->where(function ($query) {
            $query->where("status", "followup");
        })
        ->get();

    //reset the variable in followup if followup not exist
    $followup_queued_track = 0;
    $followup_accepted_track = 0;
    $followup_rejected_track = 0;
    $followup_cancelled_track = 0;
    $followup_examined_track = 0;
    $followup_upward_track = 0;
    $followup_admitted_track = 0;
    $followup_discharged_track = 0;
    //end reset

    //reset the variable in redirected if redirected not exist
    $redirected_queued_track = 0;
    $redirected_accepted_track = 0;
    $redirected_rejected_track = 0;
    $redirected_cancelled_track = 0;
    $redirected_examined_track = 0;
    $redirected_upward_track = 0;
    $redirected_admitted_track = 0;
    $redirected_discharged_track = 0;
    //end reset
    ?>

    <small class="label bg-blue">{{ $position[$position_count].' appointment - '.\App\Facility::find($referred_track->referred_to)->name }}</small><br>
    <div class="stepper-wrapper">
        <div class="stepper-item completed">
            <div class="step-counter"><i class="fa fa-calendar" aria-hidden="true"></i></div>
            <div class="step-name">Appointment</div>
        </div>
        <div class="stepper-item @if($referred_seen_track || $referred_accepted_track || $referred_rejected_track) completed @endif" id="seen_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter"><i class="fa fa-eye" aria-hidden="true"></i></div>
            <div class="step-name">Seen</div>
        </div>
        <div class="text-center stepper-item @if($referred_accepted_track) completed @endif" data-actionmd="" id="accepted_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter
            <?php
                if($referred_cancelled_track)
                    echo "bg-yellow";
                elseif($referred_rejected_track)
                    echo "bg-red";
                elseif($referred_queued_track && !$referred_accepted_track)
                    echo "bg-orange";
            ?>
            "
             id="rejected_progress{{ $referred_track->code.$referred_track->id }}"
            ><span id="queue_number{{ $referred_track->code }}">{!! $referred_cancelled_track || $referred_rejected_track ? '<i class="fa fa-thumbs-down" aria-hidden="true" style="font-size:15px;"></i>' : ($referred_queued_track && !$referred_accepted_track ?  '<i class="fa fa-hourglass-half" aria-hidden="true" style="font-size:15px;"></i>' : '<i class="fa fa-thumbs-up" aria-hidden="true" style="font-size:15px;"></i>' ) !!}</i></span></span></div>
            <div class="text-center step-name" id="rejected_name{{ $referred_track->code.$referred_track->id }}">
                <?php
                if($referred_cancelled_track)
                    echo 'Cancelled';
                elseif($referred_rejected_track)
                    echo 'Declined';
                elseif($referred_queued_track && !$referred_accepted_track)
                    echo 'Queued at <br> <b>'. $queue_referred.'</b>';
                else
                    echo 'Accepted'
                ?>
            </div>
        </div>
        <div class="stepper-item @if($referred_examined_track) completed @endif" id="examined_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter step-counter-examined" onclick="telemedicineExamined('{{ $row->id }}', '{{ $referred_track->code }}', '{{ $referred_accepted_track }}', '{{ $referred_accepted_hold->first()->action_md }}', '{{ $referred_track->referring_md }}', '{{ $referred_track->id }}', '{{ $row->type }}')"><i class="fa fa-building" aria-hidden="true"></i></div>
            <div class="step-name">Consultation</div>
        </div>
        <div class="stepper-item stepper-item-prescription @if($referred_prescription_track) completed @endif" id="prescribed_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter step-counter-prescription" onclick="telemedicinePrescription('{{ $row->id }}','{{ $referred_prescription_hold->first()->id }}','{{ $referred_track->code }}','{{ $referred_track->id }}')"><i class="fa fa-home" aria-hidden="true"></i></div>
            <div class="step-name" style="margin-right:10px;">Disposition</div>
        </div>
        <div class="stepper-item-upward @if($referred_upward_track && !$referred_treated_track) completed @endif" id="upward_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter"><i class="fa fa-caret-up" aria-hidden="true" style="font-size:25px"></i></div>
            <div class="step-name">Upward</div>

            <div class="stepper-item stepper-item-follow_new @if($referred_followup_track && !$referred_rejected_track) completed @endif" id="departed_progress{{ $referred_track->code.$referred_track->id }}">
                <div class="step-counter-follow_new" onclick="telemedicineFollowUpPatient('{{ $referred_redirected_track }}','{{ $referred_end_track }}','{{ $referred_examined_track }}','{{ $referred_followup_track }}','{{ $referred_track->code }}','{{ $referred_track->id }}')"><i class="fa fa-paper-plane" aria-hidden="true"></i></div>
                <div class="step-name step-name-treated_new">Follow Up</div>
            </div>

            <div class="stepper-item stepper-item-treated_new @if($referred_end_track && !$referred_followup_track) completed @endif" id="treated_progress{{ $referred_track->code.$referred_track->id }}">
                <div class="step-counter-treated_new" onclick="telemedicineTreatedPatient('{{ $referred_upward_track }}','{{ $referred_examined_track }}','{{ $referred_treated_track }}','{{ $referred_track->code }}','{{ $referred_track->id }}')"><i class="fa fa-heart" aria-hidden="true"></i></div>
                <div class="step-name step-name-treated_new">Treated</div>
            </div>
        </div>
        <div class="stepper-item stepper-item-referred @if($referred_redirected_track) completed @endif" id="departed_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter step-counter-referred" onclick="telemedicineReferPatient('{{ $referred_upward_track }}','{{ $referred_redirected_track }}','{{ $referred_followup_track }}','{{ $referred_track->code }}','{{ $referred_track->id }}')"><i class="fa fa-share" aria-hidden="true"></i></div>
            <div class="step-name">Referred</div>
            {{-- <div class="stepper-item stepper-item-follow @if($referred_followup_track && !$referred_rejected_track) completed @endif" id="departed_progress{{ $referred_track->code.$referred_track->id }}">
                <div class="step-counter-follow" onclick="telemedicineFollowUpPatient('{{ $referred_redirected_track }}','{{ $referred_end_track }}','{{ $referred_examined_track }}','{{ $referred_followup_track }}','{{ $referred_treated_track }}','{{ $referred_track->code }}','{{ $referred_track->id }}')"><i class="fa fa-paper-plane" aria-hidden="true"></i></div>
                <div class="step-name">Follow Up</div>
            </div> --}}
            {{-- <div class="stepper-item stepper-item-end @if($referred_end_track && !$referred_followup_track) completed @endif" id="departed_progress{{ $referred_track->code.$referred_track->id }}">
                <div class="step-counter-end" onclick="telemedicineEndPatient('{{ $referred_treated_track }}','{{ $referred_redirected_track }}','{{ $referred_followup_track }}','{{ $referred_end_track }}','{{ $referred_track->code }}','{{ $referred_track->id }}')">7</div>
                <div class="step-name step-name-end">Ended</div>
            </div> --}}
        </div>
    </div>


   @if(count($followup_track) > 0)
        @foreach($followup_track as $follow_track)
            <?php
                $queue_follow = \App\Activity::where('code',$follow_track->code)->where('status','queued')->orderBy('id','desc')->first()->remarks;
                $position_count++;
                $follow_seen_track = \App\Seen::where("code",$follow_track->code)
                    ->where("facility_id",$follow_track->referred_to)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->exists();
                $follow_queued_track = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_to",$follow_track->referred_to)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->where("status","queued")
                    ->exists();
                $follow_accepted_hold = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_to",$follow_track->referred_to)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->where("status","accepted");
                $follow_accepted_track = $follow_accepted_hold->exists();
                $follow_rejected_track = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_to",$follow_track->referred_to)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->where("status","rejected")
                    ->exists();
                $follow_cancelled_track = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_to",$follow_track->referred_to)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->where("status","cancelled")
                    ->exists();
                $follow_examined_track = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_from",$follow_track->referred_from)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->where("status","examined")
                    ->exists();
                $follow_prescription_hold = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_from",$follow_track->referred_from)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->where("status","prescription");
                $follow_prescription_track = $follow_prescription_hold->exists();
                $follow_upward_track = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_from",$follow_track->referred_from)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->where("status","upward")
                    ->exists();
                $follow_redirected_track = \App\Activity::where("code",$follow_track->code)
                    ->where("status","redirected")
                    ->exists();
                $follow_treated_track = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_from",$follow_track->referred_from)
                    ->where("created_at",">=",$follow_track->created_at)
                    ->where("status","treated")
                    ->exists();
                $follow_followup_track = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_from",$follow_track->referred_from)
                    ->where("created_at",">",$follow_track->created_at)
                    ->where("status","followup")
                    ->exists();
                $follow_end_track = \App\Activity::where("code",$follow_track->code)
                    ->where("referred_from",$follow_track->referred_from)
                    ->where("created_at",">",$follow_track->created_at)
                    ->where("status","end")
                    ->exists();
            ?>

            <small class="label bg-blue">{{ $position[$position_count].' appointment - '.\App\Facility::find($follow_track->referred_to)->name }}</small><br>
            <div class="stepper-wrapper">
                <div class="stepper-item completed">
                    <div class="step-counter"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                    <div class="step-name">Appointment</div>
                </div>
                <div class="stepper-item @if($follow_seen_track || $follow_accepted_track || $follow_rejected_track) completed @endif" id="seen_progress{{ $follow_track->code.$follow_track->id }}">
                    <div class="step-counter"><i class="fa fa-eye" aria-hidden="true"></i></div>
                    <div class="step-name">Seen</div>
                </div>
                <div class="text-center stepper-item @if($follow_accepted_track || $follow_rejected_track) completed @endif" data-actionmd="" id="accepted_progress{{ $follow_track->code.$follow_track->id }}">
                    <div class="step-counter
                    <?php
                        if($follow_cancelled_track)
                            echo "bg-yellow";
                        elseif($follow_rejected_track)
                            echo "bg-red";
                        elseif($follow_queued_track && !$follow_accepted_track)
                            echo "bg-orange";
                    ?>"
                     id="rejected_progress{{ $follow_track->code.$follow_track->id }}"><span id="queue_number{{ $follow_track->code }}">{!! $follow_cancelled_track || $follow_rejected_track ? '<i class="fa fa-thumbs-down" aria-hidden="true" style="font-size:15px;"></i>' : ($follow_queued_track && !$follow_accepted_track ?  '<i class="fa fa-hourglass-half" aria-hidden="true" style="font-size:15px;"></i>' : '<i class="fa fa-thumbs-up" aria-hidden="true" style="font-size:15px;"></i>' ) !!}</span></div>
                    <div class="text-center step-name" id="rejected_name{{ $follow_track->code.$follow_track->id }}">
                    <?php
                        if($follow_cancelled_track)
                            echo 'Cancelled';
                        elseif($follow_rejected_track)
                            echo 'Declined';
                        elseif($follow_queued_track && !$follow_accepted_track)
                            echo 'Queued at <br> <b>'. $queue_referred.'</b>';
                        else
                            echo 'Accepted'
                    ?>
                    </div>
                </div>
                <div class="stepper-item @if($follow_examined_track) completed @endif" id="examined_progress{{ $follow_track->code.$follow_track->id }}">
                    <div class="step-counter step-counter-examined" onclick="telemedicineExamined('{{ $row->id }}', '{{ $follow_track->code }}', '{{ $follow_accepted_track }}', '{{ $follow_accepted_hold->first()->action_md }}', '{{ $follow_track->referring_md }}', '{{ $follow_track->id }}', '{{ $row->type }}')"><i class="fa fa-building" aria-hidden="true"></i></div>
                    <div class="step-name">Consultation</div>
                </div>
                <div class="stepper-item stepper-item-prescription @if($follow_examined_track) completed @endif" id="prescribed_progress{{ $follow_track->code.$follow_track->id }}">
                    <div class="step-counter step-counter-prescription" onclick="telemedicinePrescription('{{ $row->id }}','{{ $follow_prescription_hold->first()->id }}','{{ $follow_track->code }}','{{ $follow_track->id }}')"><i class="fa fa-home" aria-hidden="true"></i></div>
                    <div class="step-name" style="margin-right:10px;">Disposition</div>
                </div>
                <div class="stepper-item-upward @if($follow_upward_track) completed @endif" id="upward_progress{{ $follow_track->code.$follow_track->id }}">
                    <div class="step-counter"><i class="fa fa-caret-up" aria-hidden="true" style="font-size:25px"></i></div>
                    <div class="step-name">Upward</div>


                    <div class="stepper-item stepper-item-follow_new @if($follow_followup_track && !$follow_rejected_track) completed @endif" id="departed_progress{{ $follow_track->code.$follow_track->id }}">
                        <div class="step-counter-follow_new" onclick="telemedicineFollowUpPatient('{{ $follow_redirected_track }}','{{ $follow_end_track }}','{{ $follow_examined_track }}','{{ $follow_followup_track }}','{{ $follow_track->code }}','{{ $follow_track->id }}')"><i class="fa fa-paper-plane" aria-hidden="true"></i></div>
                        <div class="step-name">Follow Up</div>
                    </div>

                    <div class="stepper-item stepper-item-treated_new @if($follow_treated_track) completed @endif" id="treated_progress{{ $follow_track->code.$follow_track->id }}">
                        <div class="step-counter-treated_new" onclick="telemedicineTreatedPatient('{{ $follow_upward_track }}','{{ $follow_examined_track }}','{{ $follow_treated_track }}','{{ $follow_track->code }}','{{ $follow_track->id }}')"><i class="fa fa-heart" aria-hidden="true"></i></div>
                        <div class="step-name step-name-treated_new">Treated</div>
                    </div>
                </div>
                <div class="stepper-item stepper-item-referred @if($follow_redirected_track) completed @endif" id="departed_progress{{ $follow_track->code.$follow_track->id }}">
                    <div class="step-counter step-counter-referred" onclick="telemedicineReferPatient('{{ $follow_upward_track }}','{{ $follow_redirected_track }}','{{ $follow_followup_track }}','{{ $follow_track->code }}','{{ $follow_track->id }}')"><i class="fa fa-share" aria-hidden="true"></i></div>
                    <div class="step-name">Referred</div>
                    {{-- <div class="stepper-item stepper-item-follow @if($follow_followup_track && !$follow_rejected_track) completed @endif" id="departed_progress{{ $follow_track->code.$follow_track->id }}">
                        <div class="step-counter-follow" onclick="telemedicineFollowUpPatient('{{ $follow_redirected_track }}','{{ $follow_end_track }}','{{ $follow_examined_track }}','{{ $follow_followup_track }}','{{ $follow_treated_track }}','{{ $follow_track->code }}','{{ $follow_track->id }}')"><i class="fa fa-paper-plane" aria-hidden="true"></i></div>
                        <div class="step-name">Follow Up</div>
                    </div> --}}
                    {{-- <div class="stepper-item stepper-item-end @if($follow_end_track) completed @endif" id="end_progress{{ $follow_track->code.$follow_track->id }}">
                        <div class="step-counter-end" onclick="telemedicineEndPatient('{{ $follow_treated_track }}','{{ $follow_redirected_track }}','{{ $follow_followup_track }}','{{ $follow_end_track }}','{{ $follow_track->code }}','{{ $follow_track->id }}')">7</div>
                        <div class="step-name step-name-end">Ended</div>
                    </div> --}}
                </div>
            </div>
           
            <div class="stepper-wrapper">
                <p class="mt-0">
                    <?php
                        $referredActivities = $user->activities()
                            ->where('code', $follow_track->code) 
                            ->where('id', $referred_track->id)
                            ->get();
                       
                        $followActivities = $user->activities()
                            ->where('code', $follow_track->code)
                            ->where("status","followup")
                            ->get();  
                            
                            // dd($referredActivities, $followActivities);
                    ?>
                    <?php $pdfFiles = []; ?>
                    <?php $imageFiles = []; ?>
                    @foreach ($position as $index => $pos)
                        @if ( $index== 1)
                            <?php $referredFiles = []; ?>
                            @foreach ($referredActivities as $referredActivity)
                                <?php
                                $fileNames = explode('|', $referredActivity->generic_name);
                                $referredFiles = array_merge($referredFiles, $fileNames);
                                $activity_id = $referredActivity->id;
                                $activity_code = $referredActivity->code;

                                foreach($fileNames as $filename){
                                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                    if (in_array($extension, ['pdf'])){
                                        $pdfFiles[] = $filename;                                            
                                    }elseif (in_array($extension, ['JPG','JPEG','PNG','jpg', 'jpeg', 'png'])){
                                        $imageFiles[] = $filename;
                                    }
                                }
                        
                                ?>
                            @endforeach
                               <?php $sortedFiles = array_merge($pdfFiles, $imageFiles) ?>
                    <!-- @if ($pos == $position[$position_count]) -->
                            @foreach ($sortedFiles as $referredFile)
                                    <a href="javascript:void(0);" class="d-file" onclick="openFileViewer('{{$index}}','{{$activity_code}}','{{$activity_id}}','{{$follow_id}}','{{ asset('public/fileupload/' . $user->username . '/') }}', '{{ $referredFile }}', '{{$referredActivities}}')">
                                        {{ $referredFile }} 
                                    </a>&nbsp;
                                @endforeach
                                @if(empty($sortedFiles))
                                    <a href="" class="btn btn-success btn-xs" onclick="addfilesInFollowupIfempty('{{$index}}','{{$activity_code}}','{{$activity_id}}','{{$follow_id}}','{{$referredFile}}')">
                                        <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;add files
                                    </a>&nbsp;
                                @endif
                         
                    <!-- @endif -->
                        @elseif ($index >= 2)
                            <?php $pdfFiles_follow = []; ?>
                            <?php $imageFiles_follow = []; ?>
                            <?php $followFiles = []; ?>
                            @if (isset($followActivities[$index - 2]))
                                <?php
                                $followActivity = $followActivities[$index - 2];
                                $fileNames = explode('|', $followActivity->generic_name);
                                $followFiles = array_merge($followFiles, $fileNames);
                                $follow_id = $followActivity->id;

                                foreach($fileNames as $filename){
                                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                    if (in_array($extension, ['pdf'])){
                                        $pdfFiles_follow[] = $filename;                                            
                                    }elseif (in_array($extension, ['JPG','JPEG','PNG','jpg', 'jpeg', 'png'])){
                                        $imageFiles_follow[] = $filename;
                                    }
                                }
                                ?>
                            @endif
                                <?php $sortedFiles_follow = array_merge($pdfFiles_follow, $imageFiles_follow) ?>
                                <?php  $allfiles = implode('|', array_map('/',$imageFiles_follow)); ?>
                            @if ($pos == $position[$position_count])
                                    @foreach ($sortedFiles_follow as $referredFile)   
                                    <a href="javascript:void(0);" class="d-file" onclick="openFileViewer('{{$index}}','{{$activity_code}}','{{$activity_id}}','{{$follow_id}}','{{ asset('public/fileupload/' . $user->username . '/') }}', '{{ $referredFile }}', '{{$followActivities}}')">
                                        {{ $referredFile }}
                                    </a>&nbsp;
                                    @endforeach
                                    @if(empty($sortedFiles_follow))
                                        <a href="" class="btn btn-success btn-xs" onclick="addfilesInFollowupIfempty('{{$index}}','{{$activity_code}}','{{$activity_id}}','{{$follow_id}}','{{$referredFile}}')">
                                        <i class="fa fa-plus" aria-hidden="true"></i>add files
                                        </a>&nbsp;
                                    @endif
                                 
                            @endif
                        @endif
                    @endforeach
                  
                </p>
            </div>

        @endforeach
    @endif  

    <script>
      function isPDF(referredFile){
        console.log('hello', referredFile);
            return referredFile.toLowerCase().endsWith('.pdf'); 
      }


            $(document).ready(function() {
                // Listen for the slide event on the carousel
                $('#carousel-example-generic').on('slid.bs.carousel', function () {
                    // Get the filename of the active item
                    var activeFileName = $(this).find('.item.active').data('filename');
                    
                    // Update the href or onclick attributes of your action buttons here
                    // For example, updating the download link:
                    $('.filecolor').attr('download', activeFileName);
                    // Assuming you have a function to update the URL based on the filename
                    var updatedUrl = generateFileUrl(baseUrl, activeFileName);
                    $('.filecolor').attr('href', updatedUrl);

                    // Similarly, update the other action buttons like update and delete
                    // by modifying their onclick attributes or any relevant attributes
                    // to reflect the activeFileName
                });
            });

            // Helper function to generate file URL (modify as per your requirements)
            function generateFileUrl(baseUrl, fileName) {
                return `${baseUrl}/${fileName}`;
            }

        //---------------------------------------------------------------------------------------------------
        function openFileViewer(position,code, activity_id, follow_id, baseUrl, fileNames, allfiles) {
            console.log('filenames: ', fileNames);
            console.log('baseUrl: ', baseUrl);
           
            const parsedfiles = Array.isArray(allfiles)? allfiles : JSON.parse(allfiles);
            const allfilename = parsedfiles.map(file => file.generic_name.split('|')).flat();
            console.log('filenames', allfilename);
            
            const clickedFile = allfilename.findIndex(file => file === fileNames);
            console.log('selected file', clickedFile);
            let carouselItems = '';
            allfilename.forEach((file, index) => {
            let isActive = index === clickedFile ? 'active' : '';
            console.log('filname one', fileNames);
            var fileExtension = file.split('.').pop().toLowerCase();

            const fileUrl = `${baseUrl}/${file}`; 
           

            if(fileExtension === 'pdf'){
                carouselItems += `
                <div class="item ${isActive}" data-filename="${file}">
                    <embed src="${fileUrl}" type="application/pdf" style="width:100%;height:500px;" />
                </div>
                `
            }else{
                carouselItems += `
                <div class="item ${isActive}" data-filename="${file}">
                    <img src="${fileUrl}" alt="..." style="max-width:50%;height:Auto;">
                </div>
                `
            }
            // console.log("Is active: ", isActive, file);
            if(isActive == 'active'){
                console.log('Is active file:', file);
            }
        });

            var modalContent = `
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Additional wrapper for vertical centering -->
                        <div class="vertical-center-wrapper" style="display: table; width: 100%; height: 100vh;">
                            <div class="text-center" style="display: table-cell; vertical-align: middle;">
                                <div class="card" style="padding: 20px;">
                                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                                <div class="carousel-inner">
                                                    ${carouselItems}
                                                </div>
                                                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                                                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                                                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                        </div>
                                </div>
                                    
                                    <a href="${baseUrl}" class="btn btn-success filecolor" download="${fileNames}">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <a href="#" id="updateButton" onclick="editFileforFollowup('${baseUrl}','${code}','${activity_id}','${follow_id}','${position}'); closeModal()"  class="btn btn-success filecolor">
                                        <i class="fa fa-pencil-square-o"></i> Update
                                    </a>
                                    <a href="#" class="btn btn-success filecolor" onclick="addfilesInFollowupIfempty('${position}','${code}','${activity_id}','${follow_id}','${fileNames}'); closeModal()">
                                        <i class="fa fa-plus"></i> Add More
                                    </a>
                                    <a href="#" id="deleteButton" onclick="DeleteFileforFollowup('${baseUrl}','${fileNames}','${code}','${activity_id}','${follow_id}','${position}'); closeModal()" class="btn btn-danger filecolorDelete">
                                        <i class="fa fa-trash"></i> Delete
                                    </a> 
                                    <a href="#"  class="btn btn-default btn-flat" onclick="closeModalButton()" id="closeModalId">
                                        <i class="fa fa-times"></i> close
                                    </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                        
         `;

            var modal = document.createElement('div');
            modal.id = 'carouselmodaId'
            modal.innerHTML = modalContent;
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.backgroundColor = 'rgba(0,0,0,0.7)';
            modal.style.zIndex = '9999';

            document.body.appendChild(modal);

            modal.onclick = function (event) {
                if(event.target === modal){
                    modal.parentNode.removeChild(modal);
                }
            };

             getfilename(baseUrl,code,activity_id,follow_id,position); 
        }

        function closeModal() {
          $("#carouselmodaId").hide();
        }

       function closeModalButton() {
           $("#carouselmodaId").hide();
        //    location.reload();
       }

      

       function getfilename(baseUrl,code,activity_id,follow_id,position) {
            // Use delegated events to handle clicks for dynamically added elements
            var activeFileName = $('.carousel-inner .item.active').data('filename');
            $(document).on('click', '#updateButton', function(e) {
                e.preventDefault();
                // Now call your function with the activeFileName
                // e.g., editFileforFollowup(baseUrl, activeFileName, code, activity_id, follow_id, position);
                editFileforFollowup(baseUrl, activeFileName, code, activity_id, follow_id,position);
            });

            $(document).on('click', '#deleteButton', function(e) {
                e.preventDefault();
                DeleteFileforFollowup(baseUrl,activeFileName,code,activity_id,follow_id,position)
            });

            $(document).on('click', '#AddfileEmpty', function(e) {
                

            });
            // And so on for other buttons...

            // Handle carousel slide change to update button actions dynamically
            $('#carouselModalId').on('slid.bs.carousel', '#carousel-example-generic', function() {
                var activeFileName = $('.carousel-inner .item.active').data('filename');
                // You can now dynamically update button actions here if needed
               
            });
        }

      

    </script>


    @if(count($redirected_track) > 0)
        @foreach($redirected_track as $redirect_track)
            <?php
            $queue_redirected = \App\Activity::where('code',$redirect_track->code)->where('status','queued')->orderBy('id','desc')->first()->remarks;
            $position_count++;
            $redirected_seen_track = \App\Seen::where("code",$redirect_track->code)
                ->where("facility_id",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->exists();
            $redirected_queued_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_to",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","queued")
                ->exists();
            $redirected_accepted_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_to",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","accepted")
                ->exists();
            $redirected_rejected_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_to",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","rejected")
                ->exists();
            $redirected_cancelled_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_to",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","cancelled")
                ->exists();
            $redirected_travel_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_to",$redirect_track->referred_from)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","travel")
                ->exists();
            $redirected_arrived_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_from",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","arrived")
                ->exists();
            $redirected_notarrived_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_from",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","archived")
                ->exists();
            $redirected_admitted_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_from",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","admitted")
                ->exists();
            $redirected_discharged_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_from",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","discharged")
                ->exists();
            ?>
            <small class="label bg-blue">{{ $position[$position_count].' position - '.\App\Facility::find($redirect_track->referred_to)->name }}</small><br>
            <div class="stepper-wrapper">
                <div class="stepper-item completed">
                    <div class="step-counter">1</div>
                    <div class="step-name">{{ count($redirected_track) > 1 ? 'Redirected' : 'Referred' }}</div>
                </div>
                <div class="stepper-item @if($redirected_seen_track || $redirected_accepted_track || $redirected_rejected_track) completed @endif" id="seen_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter">2</div>
                    <div class="step-name">Seen</div>
                </div>
                <div class="stepper-item @if($redirected_accepted_track || $redirected_rejected_track || $redirected_cancelled_track) completed @endif" id="accepted_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter
                                                <?php
                    if($redirected_rejected_track)
                        echo "bg-red";
                    elseif($redirected_cancelled_track)
                        echo "bg-yellow";
                    elseif($redirected_queued_track && !$redirected_accepted_track)
                        echo "bg-orange";
                    ?>
                            " id="rejected_progress{{ $redirect_track->code.$redirect_track->id }}">3</div>
                    <div class="step-name text-center" id="rejected_name{{ $redirect_track->code.$redirect_track->id }}"><?php
                        if($redirected_rejected_track)
                            echo 'Declined';
                        elseif($redirected_cancelled_track)
                            echo 'Cancelled';
                        elseif($redirected_queued_track && !$redirected_accepted_track)
                            echo "Queued at <br><b>".$queue_redirected."</b>";
                        else
                            echo "Accepted"
                        ?></div>
                </div>
                <div class="stepper-item @if( ($redirected_travel_track || $redirected_arrived_track || $redirected_notarrived_track) && !$redirected_rejected_track && !$redirected_cancelled_track ) completed @endif" id="departed_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter">4</div>
                    <div class="step-name">Departed</div>
                </div>
                <div class="stepper-item @if( ($redirected_arrived_track || $redirected_notarrived_track) && !$redirected_rejected_track && !$redirected_cancelled_track ) completed @endif" id="arrived_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter {{ $redirected_notarrived_track && !$redirected_rejected_track ? "bg-red" : "" }}" id="notarrived_progress{{ $redirect_track->code.$redirect_track->id }}">5</div>
                    @if($redirected_notarrived_track)
                        <div class="step-name not_arrived">Not Arrived</div>
                    @else
                        <div class="step-name" id="arrived_name{{ $redirect_track->code.$redirect_track->id }}">Arrived</div>
                    @endif
                </div>
                <div class="stepper-item @if(($redirected_admitted_track || $redirected_discharged_track) && !$redirected_cancelled_track ) completed @endif" id="admitted_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter">6</div>
                    <div class="step-name">Admitted</div>
                </div>
                <div class="stepper-item @if($redirected_discharged_track && !$redirected_cancelled_track ) completed @endif" id="discharged_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter">7</div>
                    <div class="step-name">Discharged</div>
                </div>
            </div>
        @endforeach
    @endif
   
    @if(count($activities) > 0)
        <?php $first = 0;
        $latest_act = \App\Activity::where('code',$row->code)->latest('created_at')->first();
        ?>
        <div class="row">
            <div class="tracking col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped" style="font-size: 9pt;">
                        <thead class="prepend_from_firebase{{ $row->code }}" id="prepend_from_websocket{{ $row->code }}"></thead>
                        @foreach($activities as $act)
                            <?php
                            $act_name = \App\Patients::find($act->patient_id);
                            $old_facility_data = \App\Facility::find($act->referred_from);
                            $old_facility = $old_facility_data->name;
                            $old_facility_id = $old_facility_data->id;
                            if($act->status=='transferred' || $act->status=='redirected'|| $act->status=='referred' || $act->status=='calling'){
                                $act_icon = 'fa-ambulance';
                            }

                            $new_facility = 'N/A';
                            $tmp_new = \App\Facility::find($act->referred_to);

                            if($act->referred_to==0)
                            {
                                $tmp_new = \App\Facility::find($act->referred_from);
                            }

                            if($tmp_new){
                                $new_facility = $tmp_new->name;
                            }

                            ?>
                            @if($act->status=='rejected')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $act->fac_rejected }}</span> recommended to redirect <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> to other facility.
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                        <br />
                                        @if($user->facility_id==$act->referred_from && $latest_act->status=='rejected')
                                            <button class="btn btn-success btn-xs btn-redirected" onclick="consultToOtherFacilities('{{ $act->code }}')">
                                                <i class="fa fa-camera"></i> Consult other facilities<br>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @elseif($act->status=='referred' || $act->status=='redirected')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        @if($act->referring_md_id!=0)
                                            <?php
                                            if($old_facility_id == 63)
                                                $referred_md = $act->referring_md;
                                            else
                                                $referred_md = 'Dr. '.$act->referring_md;
                                            ?>
                                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  was {{ count($redirected_track) > 1 ? 'redirected' : 'referred' }} by <span class="txtDoctor">{{ $referred_md }}</span> of <span class="txtHospital">{{ $old_facility }}</span> to <span class="txtHospital">{{ $new_facility }}.</span>
                                            @if($act->remarks)
                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                            @endif
                                        @else
                                            <strong>Walk-In Patient:</strong> <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @elseif($act->status=='transferred')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  was {{ $act->status }} by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $old_facility }}</span> to <span class="txtHospital">{{ $new_facility }}.</span>
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='calling')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $new_facility }}</span> is requesting a call from <span class="txtHospital">{{ $old_facility }}</span>.
                                        @if($user->facility_id==$act->referred_from)
                                            Please contact this number <span class="txtInfo">({{ $act->contact }})</span> .
                                        @endif
                                    </td>
                                </tr>
                            @elseif($act->status=='accepted' || $act->status=='examined' || $act->status=='treated' || $act->status=='upward' || $act->status=='followup')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  was {{ $act->status }} by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $new_facility }}</span>.
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='arrived')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> arrived at <span class="txtHospital">{{ $new_facility }}</span>.
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='admitted')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was admitted at <span class="txtHospital">{{ $new_facility }}</span>.
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='discharged')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was discharged from <span class="txtHospital">{{ $new_facility }}</span>.
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                        <?php
                                        ($row->type=='normal') ? $covid_discharge = \App\PatientForm::where("code",$act->code)->first() : $covid_discharge = \App\PregnantForm::where("code",$act->code)->first();
                                        ?>
                                        @if($covid_discharge->dis_clinical_status or $covid_discharge->dis_sur_category)
                                            <span class="remarks">Clinical Status: <b>{{ ucfirst($covid_discharge->dis_clinical_status) }}</b></span>
                                            <span class="remarks">Surveillance Category: <b>{{ ucfirst($covid_discharge->dis_sur_category) }}</b></span>
                                        @endif
                                    </td>
                                </tr>
                            @elseif($act->status=='archived')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> didn't arrive to <span class="txtHospital">{{ $new_facility }}</span>.
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='cancelled')
                                <?php
                                $doctor = \App\User::find($act->action_md);
                                ?>
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        Referral was cancelled by
                                        <span class="txtDoctor">
                                                                <?php
                                            if($doctor->facility_id == 63)
                                                $cancel_doctor = $doctor->fname.' '.$doctor->mname.' '.$doctor->lname;
                                            else
                                                $cancel_doctor = 'Dr. '.$doctor->fname.' '.$doctor->mname.' '.$doctor->lname;
                                            ?>
                                            {{ $cancel_doctor }}.
                                                            </span>
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='travel')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  has departed by <span class="txtDoctor">{{ $act->remarks == 5 ? explode('-',$act->remarks)[1] : \App\ModeTransportation::find($act->remarks)->transportation }}</span>.
                                    </td>
                                </tr>
                            @elseif($act->status=='form_updated')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}'s</span> form was updated by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $old_facility }}</span>
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='queued')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}'s</span> was queued by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $new_facility }}</span>
                                        <span class="remarks">Remarks: Queued at <b>{{ $act->remarks }}</b></span>
                                    </td>
                                </tr>
                            @endif
                            <?php $first = 1; ?>
                        @endforeach
                    </table>
                </div>
                @if(count($activities)>1)
                    <div style="border-top: 1px solid #ccc;">
                        <div class="text-center">
                            <a href="#toggle" data-id="{{ $row->id }}">View More</a> <small class="text-muted">({{ count($activities) }})</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
<script>
        // $(document).ready(function() {
        //     $('#carousel-example-generic').carousel();
        // });

</script>
<style>
.carousel img {
    margin: 0 auto;
}

  .d-file{
    font-size: 12px;
    color: white;
    background-color: green;
    transform: translateY(-10px);
  }
  a.d-file:hover {
    background-color: #7AE205;
    transform: translateY(-10px);
}
 .filecolor{
    color: white;
    background: linear-gradient(180deg, #52C755, #056608);
    border: 1px solid #056608;
 }
 a.filecolor:hover{
    transform: translateY(-10px);
    color:white;
    background: linear-gradient(180deg, #056608, #8BD98D);
 }
 .filecolorDelete{
    color: white;
    background: linear-gradient(180deg, #EE3533, #ED1E24);
 }
 a.filecolorDelete:hover{
    transform: translateY(-10px);
    color: white;
    background: linear-gradient(180deg, #ED1E24, #F3787A);
 }
</style>
