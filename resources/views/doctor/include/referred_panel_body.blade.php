<link href="{{ asset('public/css/refer_panel.css?v=3') }}" rel="stylesheet">
<div class="panel-body panel-scoped-refer">
    <?php
    $position = ["1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th", "11th", "12th"];
    $position_count = 0;
    $referred_track = \App\Activity::where("code", $row->code)->where("status", "referred")->first();
    $queue_referred = \App\Activity::where('code', $row->code)->where('status', 'queued')->orderBy('id', 'desc')->first()->remarks;
    $referred_seen_track = \App\Seen::where("code", $referred_track->code)
        ->where("facility_id", $referred_track->referred_to)
        ->where("created_at", ">=", $referred_track->created_at)
        ->exists();
    $referred_queued_track = \App\Activity::where("code", $referred_track->code)
        ->where("referred_to", $referred_track->referred_to)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "queued")
        ->exists();
    $referred_accepted_track = \App\Activity::where("code", $referred_track->code)
        ->where("referred_to", $referred_track->referred_to)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "accepted")
        ->exists();
    Session::put('referred_accepted_track', $referred_accepted_track); // I add this on 24/3/24
    // $referred_rejected_track = \App\Activity::where("code", $referred_track->code)
    //     ->where("referred_to", $referred_track->referred_to)
    //     ->where("created_at", ">=", $referred_track->created_at)
    //     ->where("status", "rejected")
    //     ->exists();
    
    $referred_redirected_track = \App\Activity::where("code", $referred_track->code) // I add this
        ->where("referred_from", $referred_track->referred_from)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "redirected")
        ->exists();

    $referred_cancelled_track = \App\Activity::where("code", $referred_track->code)
        ->where("referred_from", $referred_track->referred_from)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "cancelled")
        ->exists();
    $referred_travel_track = \App\Activity::where("code", $referred_track->code)
        ->where("referred_to", $referred_track->referred_from)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "travel")
        ->exists();
    Session::put('referred_travel_track', $referred_travel_track);   // I add this on 20/3/24
    $referred_arrived_track = \App\Activity::where("code", $referred_track->code)
        ->where("referred_from", $referred_track->referred_to)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "arrived")
        ->exists();
    Session::put('referred_arrived_track', $referred_arrived_track); // I add this on 24/3/24
    $referred_notarrived_track = \App\Activity::where("code", $referred_track->code)
        ->where("referred_from", $referred_track->referred_to)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "archived")
        ->exists();
    Session::put('referred_notarrived_track', $referred_notarrived_track); // I add this on 
    $referred_admitted_track = \App\Activity::where("code", $referred_track->code)
        ->where("referred_from", $referred_track->referred_to)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "admitted")
        ->exists();
    $referred_discharged_track = \App\Activity::where("code", $referred_track->code)
        ->where("referred_from", $referred_track->referred_to)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "discharged")
        ->exists();
    $referred_discharged_ActId = \App\Activity::where("code", $referred_track->code)
        ->where("referred_from", $referred_track->referred_to)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "discharged")
        ->first();
    $refer_actId =   $referred_discharged_ActId ? $referred_discharged_ActId->id : null;
    $referred_transferred_track = \App\Activity::where("code", $referred_track->code) // I add this changes for discharged highlight complete
        ->where("referred_from", $referred_track->referred_from)
        ->where("created_at", ">=", $referred_track->created_at)
        ->where("status", "transferred")
        ->exists();
    $redirected_track = \App\Activity::where("code", $row->code)
        ->where(function ($query) {
            $query->where("status", "redirected")
                ->orWhere("status", "transferred")
                ->orWhere("status", "referred");
        })
        ->get();

    $secondTransferred = false;

    if (count($redirected_track) >= 2) {
        $secondPositionItem = $redirected_track[1]; // index 1 = second item

        if ($secondPositionItem->status === 'transferred' || $secondPositionItem->status === 'referred') {
            $secondTransferred = true;
        }
    }

    if($secondTransferred){
        $referred_rejected_track = \App\Activity::where("code", $referred_track->code)
            ->where("referred_from", $referred_track->referred_to)
            ->where("created_at", ">=", $referred_track->created_at)
            ->where("status", "rejected")
            ->exists();
    }else{
        $referred_rejected_track = \App\Activity::where("code", $referred_track->code)
            ->where("referred_from", $referred_track->referred_from)
            ->where("created_at", ">=", $referred_track->created_at)
            ->where("status", "rejected")
            ->exists();
    }

    $secondrejected = null;

     if (count($redirected_track) >= 2) {

        $secondrejected = $redirected_track[1]; 
        
        if($secondrejected->status == "redirected"){
            
            $secondrejected = 'redirected';
        }

     }

    // $count_referred = $referred_track ? 0 : 0;

    
    $referred_track_status = $redirected_track[1]->status;
    //reset the variable in redirected if redirected not exist
    $redirected_queued_track = 0;
    $redirected_accepted_track = 0;
    $redirected_rejected_track = 0;
    $redirected_cancelled_track = 0;
    $redirected_travel_track = 0;
    $redirected_arrived_track = 0;
    $redirected_admitted_track = 0;
    $redirected_discharged_track = 0;
    //end reset

    if(!$referred_track_status){
        $last_position_count_referred = $referred_track ? 0 : 0;
    }
     
    
    ?> 
    <script>

        // Initialize popover
        $(function () {
            $('.popoverReferral').popover({
                html: true
            });
        });

        function closePopover() {
            $('.popoverReferral').popover('hide')
        }

    </script>

    <small class="label bg-blue">{{ $position[$position_count].' position - '.\App\Facility::find($referred_track->referred_to)->name }}</small><br>
    <div class="stepper-wrapper">
        <div class="stepper-item completed">
            <div class="step-counter"><i class="fa fa-share" aria-hidden="true" style="font-size: 15px;"></i></div>
            <div class="step-name">Referred</div>
        </div>
        <div class="stepper-item @if($referred_seen_track || $referred_accepted_track || $referred_rejected_track) completed @endif" id="seen_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter"><i class="fa fa-eye" aria-hidden="true" style="font-size:15px;"></i></div>
            <div class="step-name">Seen</div>
        </div>
       
        <div class="text-center stepper-item @if($referred_accepted_track && (!$referred_rejected_track && !$referred_cancelled_track)) completed @endif" id="accepted_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter
                                        <?php
                                        if ($referred_cancelled_track && $secondrejected != 'redirected')
                                            echo "bg-yellow";
                                        elseif ($referred_rejected_track && (!$referred_accepted_track || !$referred_cancelled_track))
                                            echo "bg-red";
                                        elseif ($referred_rejected_track && $referred_travel_track)
                                            echo "bg-red";
                                        elseif ($referred_queued_track && !$referred_accepted_track)
                                            echo "bg-orange";
                                        ?>
            " id="rejected_progress{{ $referred_track->code.$referred_track->id }}"><span id="queue_number{{ $referred_track->code }}">
                    <?php
                    if ($referred_cancelled_track && $secondrejected != 'redirected')
                        echo '<i class="fa fa-times" aria-hidden="true" style="font-size:15px;"></i>';
                    elseif ($referred_rejected_track && (!$referred_accepted_track || !$referred_cancelled_track))
                        echo '<i class="fa fa-thumbs-down" aria-hidden="true" style="font-size:15px;"></i>';                        
                    elseif ($referred_queued_track && !$referred_accepted_track)
                        echo '<i class="fa fa-hourglass-half" aria-hidden="true" style="font-size:15px;"></i>';
                    else
                        echo '<i class="fa fa-thumbs-up" aria-hidden="true" style="font-size:15px;"></i>';
                    ?>
                </span></div>
            <div class="text-center step-name" id="rejected_name{{ $referred_track->code.$referred_track->id }}">
                <?php
                if ($referred_cancelled_track && $secondrejected != 'redirected')
                    echo 'Cancelled';
                elseif ($referred_rejected_track && (!$referred_accepted_track || !$referred_cancelled_track))
                    echo 'Declined';
                elseif ($referred_queued_track && !$referred_accepted_track)
                    echo 'Queued at <br> <b>' . $queue_referred . '</b>';
                else
                    echo 'Accepted'
                    ?>
            </div>
        </div>

        {{--<div class="stepper-item @if( ($referred_travel_track || $referred_arrived_track || $referred_notarrived_track) && $position_count < count($referred_track) && $referred_track->status == 'referred' && !$referred_rejected_track && !$referred_cancelled_track) completed @endif" id="departed_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter"><i class="fa fa-paper-plane fa-rotate-90" aria-hidden="true"></i></div>
            <div class="step-name">Departed</div>
        </div> --}} 
        <div class="stepper-item @if(($referred_travel_track || $referred_arrived_track || $referred_notarrived_track) && (!$referred_rejected_track && !$referred_cancelled_track)) completed @endif" id="departed_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter"><i class="fa fa-paper-plane fa-rotate-90" aria-hidden="true"></i></div>
            <div class="step-name">Departed</div>
        </div>
        <div class="stepper-item @if($referred_arrived_track && (!$referred_rejected_track && !$referred_cancelled_track)) completed @endif" id="arrived_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter {{ $referred_notarrived_track && !$referred_arrived_track && !$referred_rejected_track && !$referred_cancelled_track? 'bg-red' : '' }}" id="notarrived_progress{{ $referred_track->code.$referred_track->id }}">
                {!! $referred_notarrived_track && $position_count < count($referred_track) && (!$referred_arrived_track || !$referred_rejected_track || !$referred_cancelled_track) ? '<i class="fa fa-ambulance" aria-hidden="true" style="font-size: 15px;"></i>&nbsp;<i class="fa fa-cloud" aria-hidden="true" style="font-size: 10px;"></i>' :
                '<i class="fa fa-ambulance" aria-hidden="true" style="font-size: 15px;"></i>' !!}</div>
            @if($referred_notarrived_track && (!$referred_arrived_track || !$referred_rejected_track || !$referred_cancelled_track))
            <div class="step-name not_arrived">Not Arrived</div>
            @else
            <div class="step-name" id="arrived_name{{ $referred_track->code.$referred_track->id }}">Arrived</div>
            @endif
        </div>
        <div class="stepper-item @if(($referred_admitted_track || $referred_discharged_track) && !$referred_rejected_track && !$referred_cancelled_track) completed @endif" id="admitted_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter"><i class="fa fa-bed" aria-hidden="true" style="font-size: 15px;"></i></div>
            <div class="step-name">Admitted</div>
        </div>
        <div class="stepper-item @if($position_count == $count_referred  && $referred_discharged_track && (!$referred_redirected_track && !$referred_transferred_track && !$referred_rejected_track && !$referred_cancelled_track)) completed @endif" id="discharged_progress{{ $referred_track->code.$referred_track->id }}">
            {{-- <a onclick="ReferralDischargeResult(`{{$referred_track->code}}`,`{{$referred_discharged_track}}`, `{{$referred_transferred_track}}`, `{{$referred_rejected_track}}`)">
                <div class="step-counter step-counter-fileresult"><i class="fa fa-clipboard" aria-hidden="true" style="font-size: 15px;"></i><i class="fa fa-check" style="font-size: 15px; color: blue;"></i></div>
            </a> --}}
            <a href="#"
                onclick="event.preventDefault();"
                class="refer-popover"
                data-toggle="popover"
                data-html="true"
                data-code="{{ $referred_track->code }}"
                data-discharged_actid="{{ $refer_actId }}"
                data-discharged="{{ $referred_discharged_track && !$referred_rejected_track && !$referred_cancelled_track}}"
                data-transferred="{{ $referred_transferred_track }}"
                data-rejected="{{ $referred_rejected_track }}"
                data-cancelled="{{ $referred_cancelled_track }}"
                data-position="{{$position_count}}"
                data-last_position="{{$last_position_count_referred}}"
                data-status="referred"
                data-referred_track="{{ $referred_track_status }}"
                data-content="">
                <div class="step-counter step-counter-fileresult">
                    <i class="fa fa-clipboard" style="font-size: 15px;"></i>
                    <i class="fa fa-check" style="font-size: 15px; color: blue;"></i>
                </div>
            </a> 

            <div class="step-name">Discharged</div>
        </div>       
    </div>
    @php
        $redirectedTrack = json_decode($redirected_track, true);
        $referredFromArray = array_column($redirectedTrack, 'referred_from');

    @endphp

    <?php
        if ($redirected_track->isNotEmpty() && $redirected_track->first()->status === 'referred') {
            $redirected_track->shift(); // removes first index
        }
    ?>
    
    @if(count($redirected_track) > 0)
    @foreach($redirected_track as $redirect_track)

    <?php
    $queue_redirected = \App\Activity::where('code', $redirect_track->code)->where('status', 'queued')->orderBy('id', 'desc')->first()->remarks;
    $position_count++;
    $redirected_seen_track = \App\Seen::where("code", $redirect_track->code)
        ->where("facility_id", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->exists();
    $redirected_queued_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_to", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "queued")
        ->exists();
    $redirected_accepted_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_to", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "accepted")
        ->exists();
    Session::put('redirected_accepted_track', $redirected_accepted_track); // I add this on 24/3/24
    // $redirected_rejected_track = \App\Activity::where("code", $redirect_track->code)
    //     ->where("referred_to", $redirect_track->referred_to)
    //     ->where("created_at", ">=", $redirect_track->created_at)
    //     ->where("status", "rejected")
    //     ->exists();
    
    $redirected_redirected_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_to", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "redirected")
        ->exists();   
    $redirected_cancelled_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_from", $redirect_track->referred_from)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "cancelled")
        ->exists();
    $redirected_travel_track = \App\Activity::where("code", $redirect_track->code)
        // ->where("referred_to",$redirect_track->referred_from) // I uncomment this 
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "travel")
        ->exists();

    Session::put('redirected_travel_track', $redirected_travel_track); // I add this on 24/3/24
    $redirected_arrived_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_from", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "arrived")
        ->exists();
    Session::put('redirected_arrived_track', $redirected_arrived_track); // I add this on 24/3/24
    $redirected_notarrived_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_from", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "archived")
        ->exists();
    Session::put('redirected_notarrived_track', $redirected_notarrived_track); // I add this on 
    $redirected_admitted_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_from", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "admitted")
        ->exists();
    $redirected_transferred_track = \App\Activity::where("code", $redirect_track->code) // i add this for discharge condition
        // ->where("referred_from",$redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "transferred")
        ->exists();
    Session::put('redirected_transferred_track', $redirected_transferred_track);

    $redirected_transferred_track1 = \App\Activity::where("code", $redirect_track->code) // i add this for discharge condition
        ->where("referred_to",$redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "transferred")
        ->exists();
  $redirected_referred_track = \App\Activity::where("code", $redirect_track->code)
        ->orderBy('id', 'DESC') // latest
        ->first();

    $redirected_referred = $redirected_referred_track->status == "referred";

    $redirected_discharged_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_from", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "discharged")
        ->exists();

    $redirected_discharged_Act = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_from", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "discharged")
        ->get();

    // dd($redirected_discharged_Act);

     $redirected_discharged_actId = \App\Activity::select('id','status')->where("code", $row->code)
        ->where("status", "discharged")
        ->orderBy('id','asc')
        ->get();
    
    // $redirected_discharged_actId = $redirected_discharged_actId->slice(1)->values();
    
    $transferredtoReject = false;

    if ($redirect_track->status == 'transferred' || $redirect_track->status == 'referred' || $redirect_track->status == 'redirected') 
        {
            $transferredtoReject = true;
        }

    // if($transferredtoReject){
    //      $redirected_rejected_track = \App\Activity::where("code", $redirect_track->code)
    //         ->where("referred_to", $redirect_track->referred_to)
    //         ->where("created_at", ">=", $redirect_track->created_at)
    //         ->where("status", "rejected")
    //         ->exists();
    // }else{
    //     $redirected_rejected_track = \App\Activity::where("code", $redirect_track->code)
    //         ->where("referred_to", $redirect_track->referred_to)
    //         ->where("created_at", ">=", $redirect_track->created_at)
    //         ->where("status", "rejected")
    //         ->exists();
    // }

    $redirected_rejected_track = \App\Activity::where("code", $redirect_track->code)
        ->where("referred_to", $redirect_track->referred_to)
        ->where("created_at", ">=", $redirect_track->created_at)
        ->where("status", "rejected")
        ->exists();

    $transfer_referred = false;
    if($redirect_track->status == 'transferred' && $redirect_track->status == 'referred'){
        $transfer_referred = true;
    }

    $last_position_index = count($position[$position_count]) - 1; // Get the last index of the position array
    $last_position = $position[$last_position_index]; // Retrieve the last position
    $last_referred_from = $redirect_tracks[$last_position_index]->referred_from;

    $last_position_count = count($redirected_track);

?>
    
    <small class="label bg-blue">{{ $position[$position_count].' position - '.\App\Facility::find($redirect_track->referred_to)->name}}</small><br>
    <input type="hidden" id="pass_to_vue_facility" value="{{ end($referredFromArray)  }}">
   
    <div class="stepper-wrapper">
        <div class="stepper-item completed">
                 @if ($redirect_track->status == 'referred')
                    <div class="step-counter"><i class="fa fa-share" aria-hidden="true" style="font-size:15px;"></i></div>
                    {{-- <div class="step-name" style="font-size: 12px; margin-bottom: 10px;">
                       
                    </div> --}}
                    {{ ucfirst($redirect_track->status) }}
                @else
                    <div class="step-counter"><i class="fa fa-arrow-right" aria-hidden="true" style="font-size:15px;"></i></div>
                    <div class="step-name">{{ ucfirst($redirect_track->status) }}</div>
                @endif
        </div>
        <div class="stepper-item @if($redirected_seen_track || $redirected_accepted_track || $redirected_rejected_track || ($position_count < count($redirected_track))) completed @endif" id="seen_progress{{ $redirect_track->code.$redirect_track->id }}">
            <div class="step-counter"><i class="fa fa-eye" aria-hidden="true" style="font-size:15px;"></i></div>
            <div class="step-name">Seen</div>
        </div>
        <div class="stepper-item @if($redirected_accepted_track && (!$redirected_rejected_track || !$redirected_cancelled_track) || ($position_count < count($redirected_track)) ) completed @endif" id="accepted_progress{{ $redirect_track->code.$redirect_track->id }}">
            <div class="step-counter
                    <?php
                    if (
                        ($redirected_rejected_track && 
                        (($position_count < count($redirected_track) && $redirect_track->status == 'redirected') || ($position_count == count($redirected_track) && $redirect_track->status == 'redirected') ||
                          ($position_count < count($redirected_track) && $redirect_track->status == 'referred') || ($position_count == count($redirected_track) && $redirect_track->status == 'referred')) &&
                        $redirected_track[$position_count]->status != 'transferred' && !$redirected_cancelled_track)
                        // !($redirected_notarrived_track && !$redirected_arrived_track && !$redirected_rejected_track && !$redirected_cancelled_track) &&
                        // (!isset($redirected_track[$position_count]) || (isset($redirected_track[$position_count]) && $redirected_track[$position_count]->status == 'transferred'))
                        // (isset($redirected_track[$position_count]) && $redirected_track[$position_count]->status == 'transferred')
                        // $redirected_track[$position_count]->status == 'transferred' || $redirected_track[$position_count]->status == 'referred'
                        // !$redirected_admitted_track
                    )
                        echo "bg-red";
                    elseif ($redirected_cancelled_track)
                        echo "bg-yellow";
                    elseif ($redirected_queued_track && !$redirected_accepted_track)
                        echo "bg-orange";
                    ?>
                            " id="rejected_progress{{ $redirect_track->code.$redirect_track->id }}">
                <?php
                if (
                        ($redirected_rejected_track && 
                        (($position_count < count($redirected_track) && $redirect_track->status == 'redirected') || ($position_count == count($redirected_track) && $redirect_track->status == 'redirected') ||
                            ($position_count < count($redirected_track) && $redirect_track->status == 'referred') || ($position_count == count($redirected_track) && $redirect_track->status == 'referred')) &&
                        $redirected_track[$position_count]->status != 'transferred' && !$redirected_cancelled_track)
                )
                    echo '<i class="fa fa-thumbs-down" aria-hidden="true" style="font-size:15px;"></i>';
                elseif ($redirected_cancelled_track)
                    echo '<i class="fa fa-times" aria-hidden="true" style="font-size:15px;"></i>';
                // elseif ($redirected_queued_track && ($redirect_track->status == 'redirected' || $position_count < count($redirected_track) && $redirect_track->status == 'redirected'))
                elseif ($redirected_queued_track && !$redirected_accepted_track)
                    echo '<i class="fa fa-hourglass-half" aria-hidden="true" style="font-size:15px;"></i>';
                else
                    echo '<i class="fa fa-thumbs-up" aria-hidden="true" style="font-size:15px;"></i>';
                ?>
            </div>
            <div class="step-name text-center" id="rejected_name{{ $redirect_track->code.$redirect_track->id }}">
                <?php
                    if (
                         ($redirected_rejected_track && 
                        (($position_count < count($redirected_track) && $redirect_track->status == 'redirected') || ($position_count == count($redirected_track) && $redirect_track->status == 'redirected') ||
                          ($position_count < count($redirected_track) && $redirect_track->status == 'referred') || ($position_count == count($redirected_track) && $redirect_track->status == 'referred')) &&
                        $redirected_track[$position_count]->status != 'transferred' && !$redirected_cancelled_track)
                    )
                        echo 'Declined';
                    elseif ($redirected_cancelled_track)
                        echo 'Cancelled';
                    elseif ($redirected_queued_track && !$redirected_accepted_track)
                        echo "Queued at <br><b>" . $queue_redirected . "</b>";      
                    else
                        echo "Accepted";
                ?>
            </div>
        </div>
        <div class="stepper-item @if( (($redirected_travel_track || $redirected_arrived_track || $redirected_notarrived_track) && !$redirected_rejected_track && !$redirected_cancelled_track) || ($redirected_track[$position_count]->status == 'transferred' || $redirected_track[$position_count]->status == 'referred')) completed @endif" id="departed_progress{{ $redirect_track->code.$redirect_track->id }}">
            <div class="step-counter"><i class="fa fa-paper-plane fa-rotate-90" aria-hidden="true"></i></div>
            <!--!-Sample Only--! -->
            <div class="step-name">Departed</div>
        </div>
        <div class="stepper-item @if($redirected_arrived_track && !$redirected_rejected_track && !$redirected_cancelled_track || ($redirected_track[$position_count]->status == 'transferred' || $redirected_track[$position_count]->status == 'referred')) completed @endif" id="arrived_progress{{ $redirect_track->code.$redirect_track->id }}">
            <div class="step-counter {{ $redirected_notarrived_track && !$redirected_arrived_track && !$redirected_rejected_track ? 'bg-red' : '' }}" id="notarrived_progress{{ $redirect_track->code.$redirect_track->id }}">{!! $redirected_notarrived_track && !$redirected_rejected_track && !$redirected_cancelled_track ? '<i class="fa fa-ambulance" aria-hidden="true" style="font-size: 15px;"></i>&nbsp;<i class="fa fa-cloud" aria-hidden="true" style="font-size: 10px;"></i>' :
                '<i class="fa fa-ambulance" aria-hidden="true" style="font-size: 15px"></i>' !!}</div>
            @if($redirected_notarrived_track && !$redirected_arrived_track && !$redirected_rejected_track && !$redirected_cancelled_track)
            <div class="step-name not_arrived">Not Arrived</div>
            @else
            <div class="step-name" id="arrived_name{{ $redirect_track->code.$redirect_track->id }}">Arrived</div>
            @endif
        </div>
        <div class="stepper-item @if(($redirected_admitted_track || $redirected_discharged_track) && !$redirected_rejected_track || ($redirected_track[$position_count]->status == 'transferred' || $redirected_track[$position_count]->status == 'referred')) completed @endif" id="admitted_progress{{ $redirect_track->code.$redirect_track->id }}">
            <div class="step-counter"><i class="fa fa-bed" aria-hidden="true" style="font-size: 15px;"></i></div>
            <div class="step-name">Admitted</div>
        </div>
        <div class="stepper-item @if(($redirected_discharged_track && !$redirected_cancelled_track && !$redirected_rejected_track && !$redirected_transferred_track1 || ($position_count == count($redirected_track) && $redirected_admitted_track && $redirected_discharged_track) || ($redirected_transferred_track1 && $redirected_discharged_track && !$redirected_rejected_track)) && 
                    !(isset($redirected_track[$position_count+1]) && $redirected_track[$position_count]->status == 'transferred') || (!isset($redirected_track[$position_count+1]) && $redirected_discharged_track && ($redirected_track[$position_count]->status == 'transferred' || $redirected_track[$position_count]->status == 'referred'))) completed @endif" id="discharged_progress{{ $redirect_track->code.$redirect_track->id }}">
            {{--<a onclick="ReferralDischargeResult(`{{$redirect_track->code}}`,`{{$redirected_discharged_track}}`, `{{$redirected_cancelled_track}}`, `{{$redirected_rejected_track}}`, `{{$redirected_transferred_track1}}`)">
                <div class="step-counter step-counter-fileresult"><i class="fa fa-clipboard" aria-hidden="true" style="font-size: 15px;"></i><i class="fa fa-check" style="font-size: 15px; color: blue;"></i></div>
            </a> --}}
            <a href="#"
                onclick="event.preventDefault();"
                class="refer-popover"
                data-toggle="popover"
                data-html="true"
                data-code="{{ $redirect_track->code }}"
                data-discharged_actid="{{ $redirected_discharged_actId }}"
                data-discharged="{{($redirected_discharged_track && !$redirected_cancelled_track && !$redirected_rejected_track) && !(isset($redirected_track[$position_count+1]) && $redirected_track[$position_count]->status == 'transferred') || (!isset($redirected_track[$position_count+1]) && $redirected_discharged_track && ($redirected_track[$position_count]->status == 'transferred' || $redirected_track[$position_count]->status == 'referred'))}}"
                data-transferred="{{$position_count < count($redirected_track) && $redirect_track->status == 'transferred' }}"
                data-rejected="{{ $redirected_rejected_track }}"
                data-redirected_redirected="{{$redirected_redirected_track}}"
                data-position="{{$position_count}}"
                data-last_position="{{$last_position_count}}"
                data-status_track="{{$redirect_track->status}}"
                data-cancelled="{{ $redirected_cancelled_track }}"
                data-status="referred"
                data-referred_track="{{ $position_count < count($redirected_track) && $redirect_track->status == 'referred' }}"
                data-content="">
                <div class="step-counter step-counter-fileresult">
                    <i class="fa fa-clipboard" style="font-size: 15px;"></i>
                    <i class="fa fa-check" style="font-size: 15px; color: blue;"></i>
                </div>
            </a>

            <div class="step-name">Discharged</div>
        </div>
    </div>
    @endforeach
    @endif
    @if(count($activities) > 0)
    <?php $first = 0;
    $latest_act = \App\Activity::where('code', $row->code)->latest('created_at')->first();
    // $referred_act = \App\Activity::select('referred_from')->where('code', $row->code)->where('status', 'referred')->latest('created_at')->first();

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
                    if ($act->status == 'transferred' || $act->status == 'redirected' || $act->status == 'referred' || $act->status == 'calling') {
                        $act_icon = 'fa-ambulance';
                    }

                    $new_facility = 'N/A';
                    $tmp_new = \App\Facility::find($act->referred_to);

                    if ($act->referred_to == 0) {
                        $tmp_new = \App\Facility::find($act->referred_from);
                    }

                    if ($tmp_new) {
                        $new_facility = $tmp_new->name;
                    }

                    ?>
                    @if($act->status=='rejected')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $act->fac_rejected }}</span> recommended to redirect <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> to other facility.
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                            <br />
                           
                            @if($user->facility_id==$act->referred_from && $latest_act->status=='rejected')
                            <button class="btn btn-success btn-xs btn-redirected" data-toggle="modal" data-target="#redirectedFormModal" data-activity_code="{{ $act->code }}">
                                <i class="fa fa-ambulance"></i> Redirect to other facility<br>
                            </button>
                            @endif 

                            {{-- @if($user->facility_id==$referred_act->referred_from && $latest_act->status=='rejected')
                            <button class="btn btn-success btn-xs btn-redirected" data-toggle="modal" data-target="#redirectedFormModal" data-activity_code="{{ $act->code }}">
                                <i class="fa fa-ambulance"></i> Redirect to other facility<br>
                            </button>
                            @endif --}}
                        </td>
                    </tr>
                    @elseif($act->status=='referred' || $act->status=='redirected')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            @if($act->referring_md_id!=0)
                            <?php
                            if ($old_facility_id == 63)
                                $referred_md = $act->referring_md;
                            else
                                $referred_md = 'Dr. ' . $act->referring_md;
                            ?>
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was {{ $act->status }} by <span class="txtDoctor">{{ $referred_md }}</span> of <span class="txtHospital">{{ $old_facility }}</span> to <span class="txtHospital">{{ $new_facility }}.</span>
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
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was {{ $act->status }} by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $old_facility }}</span> to <span class="txtHospital">{{ $new_facility }}.</span>
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                        </td>
                    </tr>
                    @elseif($act->status=='calling')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $new_facility }}</span> is requesting a call from <span class="txtHospital">{{ $old_facility }}</span>.
                            @if($user->facility_id==$act->referred_from)
                            Please contact this number <span class="txtInfo">({{ $act->contact }})</span> .
                            @endif
                        </td>
                    </tr>
                    @elseif($act->status=='accepted')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was accepted by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $new_facility }}</span>.
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                        </td>
                    </tr>
                    @elseif($act->status=='arrived')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->created_at)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->created_at)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> arrived at <span class="txtHospital">{{ $new_facility }}</span>.
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                        </td>
                    </tr>
                    @elseif($act->status=='admitted')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->created_at)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->created_at)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was admitted at <span class="txtHospital">{{ $new_facility }}</span>.
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                        </td>
                    </tr>
                    @elseif($act->status=='discharged')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->created_at)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->created_at)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was discharged from <span class="txtHospital">{{ $new_facility }}</span>.
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                            <?php
                            ($row->type == 'normal') ? $covid_discharge = \App\PatientForm::where("code", $act->code)->first() : $covid_discharge = \App\PregnantForm::where("code", $act->code)->first();
                            ?>
                            @if($covid_discharge->dis_clinical_status or $covid_discharge->dis_sur_category)
                            <span class="remarks">Clinical Status: <b>{{ ucfirst($covid_discharge->dis_clinical_status) }}</b></span>
                            <span class="remarks">Surveillance Category: <b>{{ ucfirst($covid_discharge->dis_sur_category) }}</b></span>
                            @endif
                        </td>
                    </tr>
                    @elseif($act->status=='archived')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->created_at)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->created_at)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> didn't arrive to <span class="txtHospital">{{ $new_facility }}</span>.
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                        </td>
                    </tr>
                    @elseif($act->status=='cancelled')
                    <?php
                    $doctor = \App\User::find($act->action_md);
                    ?>
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            Referral was cancelled by
                            <span class="txtDoctor">
                                <?php
                                if ($doctor->facility_id == 63)
                                    $cancel_doctor = $doctor->fname . ' ' . $doctor->mname . ' ' . $doctor->lname;
                                else
                                    $cancel_doctor = 'Dr. ' . $doctor->fname . ' ' . $doctor->mname . ' ' . $doctor->lname;
                                ?>
                                {{ $cancel_doctor }}.
                            </span>
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                        </td>
                    </tr>
                    @elseif($act->status=='travel')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> has departed by <span class="txtDoctor">{{ $act->remarks == 5 ? explode('-',$act->remarks)[1] : \App\ModeTransportation::find($act->remarks)->transportation }}</span>.
                        </td>
                    </tr>
                    @elseif($act->status=='form_updated')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
                            <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}'s</span> form was updated by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $old_facility }}</span>
                            <span class="remarks">Remarks: {{ $act->remarks }}</span>
                        </td>
                    </tr>
                    @elseif($act->status=='queued')
                    <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                        <!-- <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td> -->
                        <td>
                            <div class="date-large" style="width: 75px;">
                                {{ date('M d, Y',strtotime($act->date_referred)) }}
                            </div>
                            <div class="time-small">
                                ({{ date('h:i A', strtotime($act->date_referred)) }})
                            </div>
                        </td>
                        <td style="width: 800px;">
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

<div id="referPopoverContent" style="display:none;">
    <div style="padding: 5px; width: 180px;">
        <div class="d-flex justify-content-between align-items-center">

            <button 
                class="btn btn-sm btn-light close-popover-btn"
                style="position:absolute; top:-6px; right:-6px; border-radius:50%; padding:2px 6px; font-size:12px; line-height:1;">
                ×
            </button>

            <div class="d-flex justify-content-between align-items-center">
                <!-- Down Referral -->
                <button 
                    class="btn btn-light text-center refer-btn"
                    style="flex:1; margin-right:5px; padding:8px; border:1px solid #ddd; border-radius:6px;">
                    <i class="fa fa-ambulance" style="font-size:14px; color:#007bff;"></i>
                    <div style="font-size:11px; margin-top:3px;">Refer other <br> facility</div>
                </button>

                <!-- Discharge Result -->
                <button 
                    class="btn btn-light text-center result-btn"
                    style="flex:1; margin-left:5px; padding:8px; border:1px solid #ddd; border-radius:6px;">
                    <i class="fa fa-clipboard" style="font-size:14px; color:#28a745;"></i>
                    <div style="font-size:11px; margin-top:3px;">Discharged <br> Instructions</div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.refer-btn', function () {
    let code = $(this).data('code');
    let status = $(this).data('status');
 
    $("#refer_code").val(code);
    $("#refer_referral").val(status);

    $('#downReferralModal').modal('show');
});


function refreshReferPopovers() {
    // Destroy old popovers first
    $('.refer-popover').popover('destroy');
    // Re-initialize
    $('.refer-popover').each(function () {
        //  console.log('Init popover for', $(this).data('code'));
        $(this).popover({
            trigger: 'click',
            placement: 'top',
            html: true,
            container: 'body',
            content: function () {
                let code = $(this).data('code');
                let status = $(this).data('status');
                let discharged = $(this).data('discharged');
                let transferred = $(this).data('transferred');
                let rejected = $(this).data('rejected');
                let cancelled = $(this).data('cancelled');
                let redirected_redirected = $(this).data('redirected_redirected');
                let reffered_second = $(this).data('referred_track');
                let status_track = $(this).data('status_track');
                let position = $(this).data('position');
                let last_position = $(this).data('last_position');

                let lab_result = $(this).data('lab_result');

                let activity_id = $(this).data('discharged_actid');
                // clone popover HTML template
                console.log("real time activity_id", activity_id);
                let pop = $('#referPopoverContent').clone();

                console.log("lab_result attachment:", lab_result);

                if(Array.isArray(activity_id)){  
                    activity_id = activity_id[position]?.id; 
                }
                
                console.log("position", position, "last position", last_position);

                if (discharged && position === last_position) {
                    
                }else{
                    pop.find('.refer-btn').remove();
                }

                // Show/hide Result button
                if (!discharged) {
                    pop.find('.result-btn').remove();
                }

                // if no buttons, return empty
                if (pop.find('.refer-btn').length === 0 && pop.find('.result-btn').length === 0) {
                    return "";
                }

                // set dynamic onclick for Result
                pop.find('.result-btn').attr(
                    'onclick',
                    `ReferralDischargeResult('${code}', '${discharged}','${cancelled}','${rejected}','${transferred}', '${activity_id }','${lab_result}')`
                );

                // set data for Refer
                pop.find('.refer-btn')
                    .attr('data-code', code)
                    .attr('data-status', status);

                return pop.html();
            }
        });
    });
}

$(document).on('click', '.close-popover-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    $('.refer-popover').popover('hide'); // closes all
});


$(document).ready(function () {
    refreshReferPopovers();
});

window.addEventListener("refresh-refer-popovers", function (e) {
    console.log('e', e.detail);
    let dischargedValue = e.detail.discharged; 
    $('.refer-popover').each(function() {
        
        if ($(this).data('code') === e.detail.code) {
            $(this).attr('data-discharged', dischargedValue);
            $(this).data('discharged', dischargedValue); 
            $(this).data('lab_result', e.detail.lab_result);
            $(this).data('discharged_actid', e.detail.activity_id); 
        }
    });

    refreshReferPopovers(); // call your function after logging
});

</script>


<style>

.toggle tr {
    display: none;
}

.time-small {
    font-size: 8px;
}
.date-large {
    font-size: 12px
}
.step-counter-fileresult:hover {
    cursor: pointer;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  }

</style>