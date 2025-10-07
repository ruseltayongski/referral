@php
    $assignedDoctor = $row->asignedDoctorId; 
@endphp

<style>

    .notice_category{
        font-size: 20px;
        font-weight: bold;
    }

    .modal-dialog1{
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<?php
$statusExamined = DB::table('activity')
                    ->select('status')
                    ->where('code', $row->code)
                    ->orderby('id', 'desc')
                    ->first();

$redirected_telemed = DB::table('activity')
                    ->select('status')
                    ->where('code', $row->code)
                    ->where('status', 'redirected')
                    ->get();
  
?>

<div class="timeline-footer">
    <div class="form-inline">
        {{--@if( ($row->status == 'referred' || $row->status == 'seen' || $row->status == 'redirected' || $row->status == 'transferred') && $user->department_id == $row->department_id )--}}
        @if($row->status == 'referred' || $row->status == 'seen' || $row->status == 'redirected' || $row->status == 'transferred' || $row->status == 'followup')
            <div class="form-group">
                <a class="btn btn-warning btn-xs view_form" href="javascript:void(0)"
                   data-toggle="modal"
                   data-code="{{ $row->code }}"
                   data-status="{{ $row->status }}"
                   data-telemed ="{{$row->telemedicine}}"
                   data-item="#item-{{ $row->id }}"
                   data-status="{{ $row->status }}"
                   data-referral_status="referred"
                   data-type="{{ $row->type }}"
                   data-id="{{ $row->id }}"
                   data-referred_from="{{ $row->referred_from }}"
                   data-patient_name="{{ $row->patient_name }}"
                   data-asigned_doctorid="{{ $row->asignedDoctorId }}"
                   data-backdrop="static">
                    <i class="fa fa-folder"></i> View Form
                </a>
            </div>
        @endif
    
        @if($row->status == 'accepted' && $row->telemedicine && !$redirected_telemed )
            <?php $latestReferredActivity = \App\Activity::where('code',$row->code)->where('status','referred')->orderBy('id','desc')->first() ?>
            <button class="btn-xs  bg-success btn-flat" id="telemedicine" onclick="openTelemedicine({{ $row->id }}, '{{ $row->code }}', '{{ $row->type }}', {{ $row->action_md_id }}, {{ $latestReferredActivity->id }});"><i class="fa fa-camera"></i> Join</button>
        @endif
        @if($statusExamined->status === 'examined' && $row->telemedicine)
            <button
                class="btn btn-warning btn-xs upward-button"
                id="upward_button{{ $row->code }}"
                onclick="endorseUpward('{{ $row->code }}','{{ $row->type }}')"
                type="button">
                <i class="fa fa-hospital-o"></i> Upward
            </button>
        @endif
        @if($seen > 0)
            <div class="form-group">
                <a href="#seenModal" data-toggle="modal"
                   data-id="{{ $row->id }}"
                   class="btn btn-success btn-xs btn-seen"><i class="fa fa-user-md"></i> Seen
                    @if($seen>0)
                        <small class="badge bg-green-active" id="count_seen{{ $row->code }}">{{ $seen }}</small>
                    @endif
                </a>
            </div>
        @endif
        @if($caller_md > 0)
            <div class="form-group">
                <a href="#callerModal" data-toggle="modal"
                   data-id="{{ $row->id }}"
                   class="btn btn-primary btn-xs btn-caller"><i class="fa fa-phone"></i> Caller
                    @if($caller_md>0)
                        <small class="badge bg-blue-active">{{ $caller_md }}</small>
                    @endif
                </a>
            </div>
        @endif
        <button class="btn btn-xs btn-info btn-feedback" data-toggle="modal"
                data-target="#feedbackModal"
                data-code="{{ $row->code }}"
                onclick="viewReco($(this))"
        >
            <i class="fa fa-comments"></i>
            ReCo
            <span class="badge bg-blue" id="reco_count{{ $row->code }}">{{ $feedback }}</span>
        </button>
        <?php $doh_remarks = \App\Monitoring::where("code","=",$row->code)->count(); ?>
        @if($doh_remarks>0)
            <button class="btn btn-xs btn btn-doh" data-toggle="modal" style="background-color: #dd7556;color: white"
                    data-target="#feedbackDOH"
                    data-code="{{ $row->code }}">
                <i class="fa fa-phone-square"></i> 711 DOH CVCHD HealthLine
                <span class="badge bg-green">{{ $doh_remarks }}</span>
            </button>
        @endif
        <?php $issue_and_concern = \App\Issue::where("tracking_id","=",$row->id)->count(); ?>
        @if($issue_and_concern>0)
            <button class="btn btn-xs btn-danger btn-issue-incoming" data-toggle="modal"
                    data-target="#IssueAndConcern"
                    data-code="{{ $row->code }}"
                    data-referred_from="{{ $row->referred_from }}"
                    data-tracking_id="{{ $row->id }}">
                <i class="fa fa fa-exclamation-triangle"></i> Issue and Concern
                <span class="badge bg-red">{{ $issue_and_concern }}</span>
            </button>
        @endif
       
        {{-- @if($row->telemedicine && $row->status !== 'accepted')
            @if($subdepartment->description)
                <span class="badge2 red">{{ $subdepartment->description }}</span>
            @else
                <span class="badge2 red">NO OPD SUB DEPARTMENT</span>
            @endif
        @endif --}}
    </div>
</div>

<script>

    function telemedicineExamined(tracking_id, code, action_md, referring_md, activity_id, form_tpe, referred_to, alreadyTreated, alreadyReferred, alreadyupward, alreadyfollow, ownfacility,telemedicine) {
        
        if(telemedicine === 0){
        
            console.log("please trigger this ", referred_to)
            var url = "<?php echo asset('api/video/call'); ?>";
            var json = {
                "_token" : "<?php echo csrf_token(); ?>",
                "tracking_id" : tracking_id,
                "code" : code,
                "action_md" : action_md ? action_md : '',
                "referring_md" : referring_md,
                "trigger_by" : "{{ $user->id }}",
                "form_type" : form_tpe,
                "referred_to" : referred_to
            };
            console.log("json data:", json);
            $.post(url,json,function(){});
            var windowName = 'NewWindow'; // Name of the new window
            var windowFeatures = 'width=600,height=400'; // Features for the new window (size, position, etc.)
            var newWindow = window.open("{{ asset('doctor/telemedicine?id=') }}"+tracking_id+"&code="+code+"&form_type="+form_tpe+"&referring_md=yes&activity_id="+activity_id, windowName, windowFeatures);
            if (newWindow && newWindow.outerWidth) {
                // If the window was successfully opened, attempt to maximize it
                newWindow.moveTo(0, 0);
                newWindow.resizeTo(screen.availWidth, screen.availHeight);
            }

        }
    }
   
//    $(document).ready(function() {
//         let selectedButtonData = null; 

//         $(".referral_body").html(loading); 
//         $(document).on('click', '.view_form', function () {
           
//             selectedButtonData = $(this).data(); 
//             let telemedValue = selectedButtonData.telemed; // Get telemedicine value
//             let followupTelemed = selectedButtonData.status; // Get status value
//             console.log("Telemed Value:", telemedValue);
//             if (telemedValue == 1 && (followupTelemed === 'followup' || followupTelemed === 'referred')) {
//                  $('#privacyNoticeModal').modal('show');
//             }
//         });

//         $('#privacyNoticeModal').on('shown.bs.modal', function () {
//             $('#privacyCheckbox').prop('checked', false);
//             $('#acceptPrivacyBtn').prop('disabled', true);
//         });

//         $('#privacyCheckbox').change(function () {
//             $('#acceptPrivacyBtn').prop('disabled', !this.checked);
//         });

//         $('#acceptPrivacyBtn').click(function () {
//             $('#privacyNoticeModal').modal('hide');
//             setTimeout(function() {
//                 $('#referralForm').modal('show');
//             }, 500);
//         });
//     });

</script>