<?php

$user = Session::get('auth');
$multi_faci = Session::get('multiple_login');

$facility_exclude =  \App\Facility::select('id')
    ->whereRaw("LOWER(name) LIKE ?", ['%birthing facility%'])
    ->pluck('id')
    ->toArray();

?>
<div class="navbar-header">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="#"></a>
</div>
<input type="hidden" id="reco_count_val" value="{{ $reco_count }}">
<div id="navbar" class="navbar-collapse collapse" style="font-size: 8pt;">
  <ul class="nav navbar-nav">   <!-- id="navbar-main" -->
        @if(!$multi_faci && ($user->level=='doctor' || $user->level=='midwife' || $user->level=='medical_dispatcher' || $user->level=='nurse' || $user->level=='mayor' || $user->level=='dmo'))
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-home"></i> Dashboard</a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('doctor') }}"><i class="fa fa-line-chart"></i> Dashboard</a></li>
                    <li><a href="{{ url('dashboard') }}"><i class="fa fa-line-chart"></i> Bed Tracker Dashboard</a></li>
                </ul>
            </li>
            @if($user->level != 'mayor' && $user->level != 'dmo')
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-users"></i> Patients <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('doctor/patient') }}"><i class="fa fa-table"></i> List of Patients</a></li>
                    @if($user->facility_id == 101)
                        <li><a href="{{ url('opcen/ckd') }}"><i class="fa fa-table"></i> CKD</a></li>
                    @endif
                    {{-- <li><a href="{{ url('doctor/appointment/calendar') }}"><i class="fa fa-table"></i> Appointment Calendar</a></li> --}}
                    <!-- @if($user->level == 'support')
                        <li><a href="{{ url('configSchedule')}}"><i class="fa fa-table"></i> Config Schedule</a></li>
                    @endif -->
                    <li class="divider"></li>
                    <li><a href="{{ url('doctor/accepted') }}"><i class="fa fa-user-plus"></i> Accepted Patients</a></li>
                    <li><a href="{{ url('doctor/discharge') }}"><i class="fa fa-users"></i> Discharged Patients</a></li>
                    <li><a href="{{ url('doctor/transferred') }}"><i class="fa fa-ambulance"></i> Transferred Patients</a></li>
                    <li><a href="{{ url('doctor/cancelled') }}"><i class="fa fa-user-times"></i> Cancelled Patients</a></li>
                    <li><a href="{{ url('doctor/archived') }}"><i class="fa fa-archive"></i> Archived Patients</a></li>
                    <li><a href="{{ url('doctor/redirected') }}"><i class="fa fa-external-link"></i> Redirected Patients</a></li>
                    <li><a href="{{ url('doctor/redirect/reco') }}"><i class="fa fa-external-link-square"></i> Recommended to be Redirected</a></li>
                    <li class="divider"></li>
                    <li><a href="{{ url('doctor/referred/track') }}"><i class="fa fa-line-chart"></i> Track Patient</a></li>
                    <li class="hide"><a href="{{ url('maintenance') }}"><i class="fa fa-line-chart"></i> Rerouted Patients</a></li>
                </ul>
            </li>
            @endif
            <!--
                <li><a href="{{ url('inventory').'/'.$user->facility_id }}"><i class="fa fa-calculator"></i> Inventory </a></li>
             -->
                
            @if($user->level != 'mayor' && $user->level != 'dmo')
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wheelchair"></i> Referral <span class="badge" style="font-size: 8pt;"><span class="count_referral">{{ $count }}</span> New</span><span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('doctor/referral') }}?filterRef=0"><i class="fa fa-ambulance"></i> Incoming &nbsp;&nbsp; <span class="badge"><span class="count_referral">{{ $count }}</span> New</span></a></li>
                    <li><a href="{{ url('doctor/referred') }}?filterRef=0"><i class="fa fa-user"></i>&nbsp; Referred Patients</a></li>
                    <li class="divider"></li>
                    <li><a href="{{ url('doctor/duplicate') }}"><i class="fa fa-files-o"></i> Duplicate Referrals</a></li>
                    <!--
                    <li><a href="{{ url('maintenance') }}"><i class="fa fa-hospital-o"></i> Emergency Walk-In</a></li>
                    <li><a href="{{ url('doctor/report/incoming') }}"><i class="fa fa-sign-in"></i> Incoming Referral Report</a></li>
                    <li><a href="{{ url('doctor/report/outgoing') }}"><i class="fa fa-sign-out"></i> Outgoing Referral Report</a></li>
                    -->
                </ul>
            </li>
           
            <!-- Telemed Dropdown -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-video-camera"></i> Telemed <span class="badge" style="font-size: 8pt;"><span class="count_referral">{{ $countTelemed }}</span> New</span><span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    @if(!in_array($user->facility_id, $facility_exclude))
                        <li><a href="{{ url('doctor/appointment/calendar') }}"><i class="fa fa-table"></i> Book Appointment</a></li>
                    @endif
                    <li>
                        <a href="{{ url('doctor/referral') }}?filterRef=1"  id="incoming-link">
                            <i class="fa fa-ambulance incoming_nav"></i> Incoming &nbsp;&nbsp; 
                            {{-- <span class="badge">
                                <span class="count_referral_telemed">{{ $countTelemed }}</span> New
                            </span> --}}
                        </a>
                    </li>
                    <li>    
                        <a href="{{ url('doctor/referred') }}?filterRef=1">
                            <i class="fa fa-ambulance outgoing_nav"></i> Outgoing &nbsp;&nbsp; 
                            <!-- <span class="badge">
                                <span class="count_referral_telemed">{{ $countTelemed }}</span> New
                            </span> -->
                        </a>
                    </li>
                    <li class="divider"></li>
                    @if(!in_array($user->facility_id, $facility_exclude))
                        <li class="dropdown-submenu">
                            <a href="#"><i class="fa fa-table"></i> Manage Appointment</a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ url('manage/appointment?type=upcoming') }}" data-toggle="modal"><i class="fa fa-calendar-check-o"></i>Upcoming Appointment</a></li>
                                <li><a href="{{ url('manage/appointment?type=past') }}" data-toggle="modal"><i class="fa fa-history"></i>Past Appointment</a></li>
                                <li><a href="{{ url('configSchedule')}}" id="configSched_Id"><i class="fa fa-table"></i> Config Schedule</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="dropdown-submenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i> Reports </a>
                    <ul class="dropdown-menu">
                        <!-- <li><a href="{{ url('admin/report/top/icd?telemedicine=1') }}" data-toggle="modal"><i class="fa fa-odnoklassniki-square"></i>Top ICD-10 Diagnosis</a></li> -->
                        <li><a href="{{ url('count/Consultation') }}" data-toggle="modal"><i class="fa fa-pie-chart"></i>Consolidated Report</a></li>
                        <!-- <li><a href="#" data-toggle="modal"> Report 2</a></li>
                        <li><a href="#" data-toggle="modal"> Report 3</a></li> -->
                    </ul>
                </li>

                <!-- </li> -->
                </ul>
            </li>
            @endif
            @include('layouts.report_menu')
        @elseif($user->level=='support')
            <li><a href="{{ url('support/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
            @include('layouts.report_menu')
            <!-- <li><a href="{{ url('configSchedule')}}"><i class="fa fa-table"></i> Config Schedule</a></li> -->
            <!-- <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-table"></i> Manage Appointment<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('configSchedule')}}"><i class="fa fa-table"></i> Config Schedule</a></li>
                    <li><a href="{{ url('manage/appointment') }}"><i class="fa fa-table"></i> Manual Appointment</a></li>
                </ul>
            </li> -->
            <li><a href="{{ url('support/users') }}"><i class="fa fa-user-md"></i> Manage Users</a></li>
            <li><a href="{{ url('support/hospital') }}"><i class="fa fa-hospital-o"></i> Hospital Info</a></li>
                   
        <!--
                <li><a href="{{ url('inventory').'/'.$user->facility_id }}"><i class="fa fa-calculator"></i> Inventory <span class="badge bg-red"> New</span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i> Reports
                        @if($count>0)
            <span class="badge">
        <span class="count_referral">{{ $count }}</span>
                    </span>
                        @endif
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="{{ url('support/report/users') }}"><i class="fa fa-users"></i>Daily Users</a></li>
                        <li><a href="{{ url('support/report/referral') }}"><i class="fa fa-wheelchair"></i>Daily Referrals</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ url('support/report/incoming') }}"><i class="fa fa-ambulance"></i>Incoming Referral
                                @if($count>0)
            <span class="badge">
            <span class="count_referral">{{ $count }}</span>
                                </span>
                                @endif
                </a>
            </li>
            <li>
                <a href="{{ url('support/chat') }}">
                                <i class="fa fa-comments"></i> IT Support: Group Chat
                            </a>
                        </li>
                    </ul>
                </li>
                -->
        @elseif($user->level=='mcc')
            <li><a href="{{ url('mcc/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <!--
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i> Report <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('mcc/report/online') }}" data-toggle="modal"><i class="fa fa-users"></i> Online Department</a></li>
                        <li><a href="{{ url('mcc/report/incoming') }}" data-toggle="modal"><i class="fa fa-line-chart"></i> Incoming Referral</a></li>
                        <li><a href="{{ url('mcc/report/timeframe') }}" data-toggle="modal"><i class="fa fa-calendar"></i> Referral Time Frame</a></li>
                        <li><a tabindex="-1" href="{{ url('admin/report/consolidated/incomingv2') }}"><i class="fa fa-file-archive-o"></i> Consolidated</a></li>
                    </ul>
                </li>
                -->
            <li><a href="{{ url('mcc/track') }}"><i class="fa fa-line-chart"></i> Track</a></li>
            @include('layouts.report_menu')
        @elseif($user->level=='admin')
            <li><a href="{{ url('admin/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ambulance"></i> E-Referral<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('doctor/patient') }}"><i class="fa fa-table"></i> List of Patients</a></li>
                    <li><a href="{{ url('doctor/referred') }}"><i class="fa fa-ambulance"></i> Referred Patients</a></li>
                    <li><a href="{{ url('doctor/referred/track') }}"><i class="fa fa-line-chart"></i> Track Patient</a></li>
                    <li class="divider"></li>
                    <li><a href="{{ url('doctor/duplicate') }}"><i class="fa fa-files-o"></i> Duplicate Referrals</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-phone"></i> Call <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('opcen/client') }}"><i class="fa fa-phone"></i> Call Center</a></li>
                    <li><a href="{{ url('it/client') }}"><i class="fa fa-phone"></i> IT</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wrench"></i> Manage <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('admin/users') }}" ><i class="fa fa-users"></i> IT Support/ Call Center/ Bed</a></li>
                    {{--<li><a href="{{ url('admin/doctor/assignment') }}" ><i class="fa fa-user-md"></i>&nbsp; Doctor's Facility Assignment</a></li>--}}
                    <li><a href="{{ url('admin/facility') }}" ><i class="fa fa-hospital-o"></i>&nbsp; Facilities</a></li>
                    <li><a href="{{ url('admin/province') }}" ><i class="fa fa-hospital-o"></i>&nbsp; Province</a></li>
                    <li class="dropdown-submenu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <span class="nav-label"><i class="fa fa-hospital-o"></i>&nbsp;&nbsp;&nbsp; Municipality</span></a>
                        <ul class="dropdown-menu">
                            @foreach(\App\Province::get() as $prov)
                                <li><a href="{{ asset('admin/municipality').'/'.$prov->id }}">{{ $prov->description }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li><a href="{{ url('admin/reason-referral') }}"><i class="fa fa-wrench"></i> Reason for Referral</a></li>
                    <li><a href="{{ url('admin/icd') }}"><i class="fa fa-file-text-o"></i> ICD-10</a></li>
                    <li><a href="{{ url('admin/appointment') }}"><i class="fa fa-pencil-square-o"></i> Appointments </a></li>
                    <li><a href="{{ url('admin/user_feedback') }}"><i class="fa fa-comments-o"></i> User Feedbacks </a></li>
                <!-- <li><a href="{{ url('excel/import') }}"><i class="fa fa-file-excel-o"></i> Import</a></li> -->
                </ul>
            </li>
            @include('layouts.report_menu')
        @elseif($user->level=='eoc_region')
            <li><a href="{{ url('eoc_region/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="{{ url('eoc_city/graph') }}"><i class="fa fa-line-chart"></i> Graph</a></li>
        @elseif($user->level=='eoc_city')
            <li><a href="{{ url('eoc_city/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="{{ url('eoc_city/graph') }}"><i class="fa fa-line-chart"></i> Graph</a></li>
        @elseif($user->level=='opcen')
            <li><a href="{{ url('opcen') }}"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="{{ url('opcen/client') }}"><i class="fa fa-phone"></i> Call</a></li>
            <li><a href="{{ asset('public/directory/Call-Center-Directory.xlsx') }}"><i class="fa fa-print"></i> Directory</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ambulance"></i> E-Referral <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('doctor/patient') }}"><i class="fa fa-table"></i> List of Patients</a></li>
                    <li><a href="{{ url('doctor/referred') }}"><i class="fa fa-ambulance"></i> Referred Patients</a></li>
                    <li><a href="{{ url('doctor/referred/track') }}"><i class="fa fa-line-chart"></i> Track Patient</a></li>
                    <li><a href="{{ url('opcen/ckd') }}"><i class="fa fa-table"></i> CKD</a></li>
                    <!-- <li><a href="{{ url('revised/referral') }}"><i class="fa fa-file-text"></i> E-Referral Form <small class="badge bg-red"> New</small></a></li> -->
                </ul>
            </li>
            @include('layouts.report_menu')
        @elseif($user->level == 'bed_tracker')
            <li><a href="{{ url('bed_tracker') }}"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="{{ url('bed').'/'.$user->facility_id }}"><i class="fa fa-bed"></i> Update Bed Availability <small class="badge bg-red"> New</small></a></li>
        @elseif($user->level=='vaccine')
            <li><a href="{{ url('vaccine') }}"><i class="fa fa-home"></i> Dashboard</a></li>
            @foreach(\App\Province::get() as $prov)
                <li><a href="{{ asset('vaccine/vaccineview').'/'.$prov->id }}">{{ $prov->description }}</a></li>
            @endforeach
            <li><a href="{{ asset('vaccine/facility').'/cebu' }}">Cebu City</a></li>
            <li><a href="{{ asset('vaccine/facility').'/mandaue' }}">Mandaue City</a></li>
            <li><a href="{{ asset('vaccine/facility').'/lapu' }}">Lapu-Lapu City</a></li>
        @elseif($user->level=="capitol")
            @include('layouts.report_menu')                       
        @endif
        @if($user->level == 'admin')
            <li><a href="{{ url('admin/login') }}"><i class="fa fa-sign-in"></i> Login As</a></li>
        @endif
        @if(!$multi_faci && $user->level != 'vaccine')
            {{--<li><a href="{{ asset('public/manual/Ereferral-User-Manual.pdf') }}" target="_blank"><i class="fa fa-file-pdf-o"></i> E-REFERRAL Manual </a></li>--}}
            <!-- <li class="dropdown-submenu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wechat"></i> Reco Messages
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('reco') }}"><i class="fa fa-wechat"></i> ReCo <span class="badge bg-green"><span id="reco_count">{{ $reco_count }}</span> New</span></a></li>
                </ul>
            </li> -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" onclick='fetchRecoNotifications()'>
                    <i class="fa fa-bell"></i> Reco
                    <span class="badge badge-danger" id="reco_count">{{ $reco_count }}</span>
                </a>
                <ul class="dropdown-menu referral-menu shadow-lg">
                    <!-- HEADER -->
                    <li class="referral-header">
                        <i class="fa fa-info-circle"></i> Reco Messages
                    </li>
                    <!-- SCROLLABLE BODY -->
                    <li>
                        <div class="referral-scroll" id="reco_notifications"></div>
                    </li>
                    <!-- FOOTER -->
                    <li class="referral-footer">
                        <a href="{{ url('reco') }}">View All Notifications</a>
                    </li>
                </ul>
            </li>

            @if($user->level == 'admin')
                <li><a href="{{ url('patient/walkin') }}"><i class="fa fa-odnoklassniki"></i> Walk-in Patients Monitoring </a></li>
            @endif
            <li><a href="{{ url('monitoring') }}"><i class="fa fa-clock-o"></i> NA within 30 minutes </a></li>
            <li><a href="{{ url('issue/concern') }}"><i class="fa fa fa-exclamation-triangle"></i> IAC </a></li>
            {{--<li><a href="{{ url('chat') }}"><i class="fa fa-wechat"></i> Chat <span class="badge bg-green"><span>{{ $count_chat }}</span> New</span></a></li>--}}
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                More <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <!-- <li class="dropdown-submenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-wechat"></i> Reco Messages
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('reco') }}"><i class="fa fa-wechat"></i> ReCo <span class="badge bg-green"><span id="reco_count">{{ $reco_count }}</span> New</span></a></li>
                    </ul>
                </li> -->
                <li><a href="{{ url('bed_admin') }}"><i class="fa fa-bed"></i> BAS</a></li>
                <li class="dropdown-submenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user-md"></i> Who's Online
                    </a>
                @endif
                @if(!$multi_faci)
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('doctor/list') }}"><i class="fa fa-user-md"></i> Who's Online</a></li>
                    </ul>
                </li>
                @endif
                <li class="dropdown-submenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-gear"></i> Settings </a>
                    <ul class="dropdown-menu">
                        @if($user->level == 'opcen')
                            <li><a href="{{ url('admin/login') }}"><i class="fa fa-sign-in"></i> Login As </a></li>
                        @endif
                        <li><a href="#setLogoutTime" data-toggle="modal" onclick="openLogoutTime();"><i class="fa fa-clock-o"></i> Set Time to Logout</a></li>
                        <li><a href="#editProfileModal" data-toggle="modal"><i class="fa fa-pencil"></i> Edit Profile</a></li>
                        <li><a href="#resetPasswordModal" data-toggle="modal"><i class="fa fa-key"></i> Change Password</a></li>
                        @if($user->level=='doctor' || $user->level=='midwife')
                            <li><a href="#dutyModal" data-toggle="modal"><i class="fa fa-user-md"></i> Change Login Status</a></li>
                            <li class="divider"></li>
                            <li><a href="#loginModal" data-toggle="modal"><i class="fa fa-users"></i> Switch User</a></li>
                        @else
                            <li class="divider"></li>
                        @endif
                        
                        @if(Session::get('admin'))
                            <?php
                            $check_login_as = \App\User::find($user->id);
                            ?>
                            <li><a href="{{ url('admin/account/return') }}"><i class="fa fa-user-secret"></i> <?php echo $check_login_as->level == 'admin' ? 'Back as Admin' : 'Back as Agent'; ?></a></li>
                        @endif
                    </ul>
                </li>
                <li>
                    <a href="{{ url('logout') }}"><i class="fa fa-sign-out"></i> Logout</a>
                </li>
            </ul>
        </li>
    </ul>
</div><!--/.nav-collapse -->

<script>

    document.addEventListener('DOMContentLoaded', function() {
        const userDept = {{ $user->department_id }};
        const userSubOpd = {{ $user->subopd_id ?? 'null' }};

        const incomingLink = document.getElementById('incoming-link');
        const configSchedLink = document.getElementById('configSched_Id');

        function showWarning(event) {
            event.preventDefault();
            Lobibox.alert("warning",
            {
                msg: "You are not authorized to access this section (OPD only)."
            });
        }

        if(incomingLink){
            incomingLink.addEventListener('click', function(event) {
                if (userDept !== 5 && !userSubOpd) {
                    showWarning(event);
                }
            });
        }

        if(configSchedLink){
            configSchedLink.addEventListener('click', function(event) {
                if (userDept !== 5 && !userSubOpd) {
                    showWarning(event);
                }
            });
        }
        

    }); 

    function timeAgo(datetime){
        const created = new Date(datetime.replace(' ', 'T'));
        const now = new Date();

        const seconds = Math.floor((now - created) / 1000);

        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);

        const daysGrammar = days > 1 ? "days" : "day";
        if(minutes < 1){
            return "Just now";
        }

        if(minutes < 60){
            return minutes + " min ago";
        }

        if(hours < 24){
            return hours + " hr ago";
        }

        return days + " " + daysGrammar + " ago";
    
    }
    
    let profilePic = "{{ url('resources/img/sender.png') }}";
    let recoFetchUrl = "{{ url('reco/fetch') }}";
    let recoBaseUrl = "{{ url('reco') }}";
    let recoUnreadCount = 0;
    let recoPayloadMap = {};
    function fetchRecoNotifications() {

        $("#reco_notifications").html(`
            <div class="notification-loader">
                <i class="fa fa-spinner"></i> Loading notifications...
            </div>
        `);

        $.ajax({
            url: recoFetchUrl,
            type: "GET",
            success: function(response){
                let user_id = {{ $user->id }};
                
                let count_reco_unread = response
                    .filter(item => !item.reco_seen && user_id != item.userid_sender)
                    .length;

                let UnreadNotifications = response
                    .filter(item => !item.reco_seen && user_id != item.userid_sender)
                    .slice(0, 7);

                let count = UnreadNotifications.length;
                let html = "";
               
                UnreadNotifications.forEach(item => {
                    let time = timeAgo(item.feedback_created_at);
                    let patient = (item.patient_name || "").replace(/<\/?[^>]+(>|$)/g, "");

                    let message = (item.message || "");
                    let cleanmessages = message.length > 80 ? message.substring(0, 80) + "…" : message;
                    let position = item.user_level == 'doctor' ? "Dr. " : "";
                    let notifId = item.code; 
                    recoPayloadMap[notifId] = item;
                    html += `
                        <a href="javascript:void(0)" 
                            data-id="${notifId}"
                            onclick="openReco(this)"
                            class="referral-item ${!item.reco_seen ? 'unread' : ''}">
                            <div class="referral-left">
                                <img src="${profilePic}" class="referral-avatar" alt="Profile Pic">
                            </div>
                            <div class="referral-right">
                                <div class="referral-top">
                                    <span class="referral-patient">
                                        ${patient}
                                        <span class="patient-tag">Patient</span>
                                    </span>
                                    <span class="referral-time">${time}</span>
                                </div>
                                <div class="referral-message">${cleanmessages}</div>
                                <div class="referral-meta">
                                    ${position}${item.referring_md}
                                    <span class="referral-department badge">${item.department_name || ''}</span>
                                </div>
                            </div>
                        </a>
                    `;
                });

                $("#reco_notifications").html(html);
                recoUnreadCount = count_reco_unread; 
                $("#reco_count").text(count_reco_unread);

                $("#reco_notifications").removeClass("loading");
            }
        });
    }
    // real time data listening using Laravel Echo and Pusher
    window.addEventListener("reco-notify", function(event) {
        let payload = event.detail.payload;
        let patient = (payload.patient_name || "").replace(/<\/?[^>]+(>|$)/g, "");
        let notifId = payload.code;
        recoPayloadMap[notifId] = payload;
        let existing = $(`#reco_notifications a[data-id='${notifId}']`);

        let message = (payload.message || "");
        message = message.length > 80 ? message.substring(0, 80) + "…" : message;

        let time = payload.date_now ? timeAgo(payload.date_now) : "Just now";
        let position = payload.user_level == 'doctor' ? "Dr. " : "";

        let html = "";

        html += `
                <a href="javascript:void(0)"
                    data-id="${notifId}"
                    onclick="openReco(this)"
                    class="referral-item ${!payload.reco_seen ? 'unread' : ''}">
                    <div class="referral-left">
                        <img src="${profilePic}" class="referral-avatar" alt="Profile Pic">
                    </div>
                    <div class="referral-right">
                        <div class="referral-top">
                            <span class="referral-patient">
                                ${patient}
                                <span class="patient-tag">Patient</span>
                            </span>
                            <span class="referral-time">${time}</span>
                        </div>
                        <div class="referral-message">${message}</div>
                        <div class="referral-meta">
                            ${position}${payload.name_sender}
                            <span class="referral-department badge">${payload.department_name || ''}</span>
                        </div>
                    </div>
                </a>
            `;

        existing.remove();

        $("#reco_notifications")
            .prepend(html)
            .hide()
            .fadeIn(200);

        if(!existing.length){
                var reco_count_here = parseInt($('#reco_count').text(), 10);
                $("#reco_count").text(reco_count_here);
            }
    });
    // not real time
    function openReco(element){
        // let payload = JSON.parse(
        //     decodeURIComponent(element.dataset.payload)
        // );
        let notifId = element.dataset.id;
        let payload = recoPayloadMap[notifId];
    
        sessionStorage.setItem('reco_payload', JSON.stringify(payload));
        window.location.href = recoBaseUrl;
    }
    

</script>
<style>
    .patient-tag{
        font-size:10px;
        background:#28a745;
        color:#fff;
        padding:2px 5px;
        border-radius:4px;
        margin-left:5px;
        font-weight:500;
    }

    .referral-menu{
        /* width: 400px;
        padding:0;
        border-radius:8px; */
        width:400px;
        max-width:400px;
        overflow-x:hidden;
    }

    /* Header */
    .referral-header{
        padding:12px 15px;
        font-weight:600;
        background:#f1f3f6;
        border-bottom:1px solid #ddd;
        font-size:14px;
        display:flex;
        align-items:center;
        gap:5px;
    }

    /* Scrollable area */
    .referral-scroll{
        max-height:350px;
        overflow-y:auto;
    }

    /* Scrollbar styling */
    .referral-scroll::-webkit-scrollbar{
        width:6px;
    }
    .referral-scroll::-webkit-scrollbar-thumb{
        background: rgba(0,0,0,0.2);
        border-radius:3px;
    }

    /* Notification item */
    .referral-item{
        display:flex;
        padding:10px 12px;
        border-bottom:1px solid #f0f0f0;
        text-decoration:none;
        transition: background 0.2s;
        overflow:hidden;
    }
    .referral-item:hover{
        background:#e6f0ff;
    }
    .referral-item.unread{
        background:#dceaff;
        font-weight:600;
    }

    /* Avatar */
    .referral-left{
        flex:0 0 40px;
        margin-right:10px;
    }
    .referral-avatar{
        width:40px;
        height:40px;
        border-radius:50%;
        object-fit:cover;
    }

    /* Right content */
    .referral-right{
        flex:1;
    }

    /* Top row */
    .referral-top{
        display:flex;
        justify-content:space-between;
        margin-bottom:3px;
    }
    .referral-patient{
        font-size:14px;
        font-weight:600;
    }
    .referral-time{
        font-size:12px;
        color:#999;
    }

    /* Message */
    .referral-message{
        font-size: 13px;
        color: #555;
        display: -webkit-box;
        -webkit-line-clamp: 2;      
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Meta */
    .referral-meta{
        font-size:12px;
        color:#888;
        display:flex;
        gap:5px;
        flex-wrap:wrap;
    }
    .referral-meta .referral-department {
        background-color: #007bff;
        color: #fff;
        font-size:10px;;
        padding:2px 5px;
        border-radius:4px;
        margin-left:5px;
        font-weight:500;
    }

    /* Footer */
    .referral-footer{
        border-top:1px solid #eee;
        text-align:center;
    }
    .referral-footer a{
        display:block;
        padding:10px;
        font-weight:600;
        color:#007bff;
    }

    /* notification reload effect */
   .notification-loader{
        padding:20px;
        text-align:center;
        color:#888;
        font-size:13px;
    }

    .notification-loader i{
        animation: spin 0.8s linear infinite;
        margin-right:5px;
    }

    @keyframes spin{
        from{transform:rotate(0deg);}
        to{transform:rotate(360deg);}
    }

</style>