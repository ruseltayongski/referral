<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('resources/img/favicon.png') }}">
    <meta http-equiv="cache-control" content="max-age=0" />
    <title>{{ (isset($title)) ? $title : 'Referral System'}}</title>
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('resources/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/assets/css/bootstrap-theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="{{ asset('resources/assets/css/ie10-viewport-bug-workaround.css') }}" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="{{ asset('resources/assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('resources/assets/css/AdminLTE.min.css') }}">
    <!-- bootstrap datepicker -->
    <link href="{{ asset('resources/plugin/select2/select2.min.css') }}" rel="stylesheet">
    <!-- SELECT 2 -->
    <link href="{{ asset('resources/plugin/datepicker/datepicker3.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('resources/plugin/Lobibox/lobibox.css') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}" />
    <title>
        @yield('title','Home')
    </title>

    @yield('css')
    <style>
        body {
            background: url('{{ asset('resources/img/backdrop.png') }}'), -webkit-gradient(radial, center center, 0, center center, 460, from(#ccc), to(#ddd));
        }
        .loading {
            background: rgba(255, 255, 255, 0.9) url('{{ asset('resources/img/loading.gif')}}') no-repeat center;
            position:fixed;
            width:100%;
            height:100%;
            top:0px;
            left:0px;
            z-index:999999999;
            display: none;
        }
    </style>
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="{{ asset('resources/assets/js/ie-emulation-modes-warning.js') }}"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<!-- Fixed navbar -->

<nav class="navbar navbar-default navbar-static-top">
    <div class="header" style="background-color:#2F4054;padding:10px;">
        <div class="container">
            <div class="pull-left">
                <?php
                    $user = Session::get('auth');
                    $t = '';
                    $dept_desc = '';
                    if($user->level=='doctor')
                    {
                        $t='Dr.';
                    }else if($user->level=='support'){
                        $dept_desc = ' / IT Support';
                    }

                    if($user->department_id > 0)
                    {
                        $dept_desc = ' / ' . \App\Department::find($user->department_id)->description;
                    }

                ?>
                <span class="title-info">Welcome,</span> <span class="title-desc">{{ $t }} {{ $user->fname }} {{ $user->lname }} {{ $dept_desc }}</span>
            </div>

            <div class="pull-right">
                <span class="title-desc">{{ \App\Facility::find($user->facility_id)->name }}</span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="header" style="background-color:#59ab91;padding:15px;">
        <div class="container">
            <img src="{{ asset('resources/img/banner.png') }}" class="img-responsive" />
        </div>
    </div>
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
        </div>
        <?php
            $count = \App\Http\Controllers\doctor\ReferralCtrl::countReferral();
        ?>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                @if($user->level=='doctor')
                <li><a href="{{ url('doctor/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-users"></i> Patients <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('doctor/patient') }}"><i class="fa fa-table"></i> List of Patients</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ url('doctor/accepted') }}"><i class="fa fa-user-plus"></i> Accepted Patients</a></li>
                        <li><a href="{{ url('doctor/discharge') }}"><i class="fa fa-ambulance"></i> Discharged/Transfered Patients</a></li>
                        <li><a href="{{ url('doctor/cancelled') }}"><i class="fa fa-user-times"></i> Cancelled Patients</a></li>
                        <li><a href="{{ url('doctor/archived') }}"><i class="fa fa-archive"></i> Archived Patients</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ url('doctor/track/patient') }}"><i class="fa fa-line-chart"></i> Track Patient</a></li>
                        <li class="hide"><a href="{{ url('maintenance') }}"><i class="fa fa-line-chart"></i> Rerouted Patients</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wheelchair"></i> Referral <span class="badge"><span class="count_referral">{{ $count }}</span> New</span><span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('doctor/referral') }}"><i class="fa fa-ambulance"></i> Incoming &nbsp;&nbsp; <span class="badge"><span class="count_referral">{{ $count }}</span> New</span></a></li>
                        <li><a href="{{ url('doctor/referred') }}"><i class="fa fa-user"></i> Referred Patients</a></li>
                        <li class="divider"></li>
                        {{--<li><a href="{{ url('maintenance') }}"><i class="fa fa-hospital-o"></i> Emergency Walk-In</a></li>--}}
                        <li><a href="{{ url('doctor/report/incoming') }}"><i class="fa fa-sign-in"></i> Incoming Referral Report</a></li>
                        <li><a href="{{ url('doctor/report/outgoing') }}"><i class="fa fa-sign-out"></i> Outgoing Referral Report</a></li>
                    </ul>
                </li>
                <li><a href="{{ url('doctor/list') }}"><i class="fa fa-user-md"></i> Who's Online</a></li>
                @elseif($user->level=='support')
                <li><a href="{{ url('support/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li><a href="{{ url('support/users') }}"><i class="fa fa-user-md"></i> Manage Users</a></li>
                <li><a href="{{ url('support/hospital') }}"><i class="fa fa-hospital-o"></i> Hospital Info</a></li>
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
                        <li><a href="{{ url('support/report/users') }}"><i class="fa fa-users"></i>&nbsp; Daily Users</a></li>
                        <li><a href="{{ url('support/report/referral') }}"><i class="fa fa-wheelchair"></i>&nbsp; Daily Referrals</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ url('support/report/incoming') }}"><i class="fa fa-ambulance"></i>&nbsp; Incoming Referral
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
                @elseif($user->level=='mcc')
                <li><a href="{{ url('mcc/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i> Report <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('mcc/report/online') }}" data-toggle="modal"><i class="fa fa-users"></i>&nbsp; Online Doctors</a></li>
                        <li><a href="{{ url('mcc/report/incoming') }}" data-toggle="modal"><i class="fa fa-line-chart"></i>&nbsp; Incoming Referral</a></li>
                        <li><a href="{{ url('mcc/report/timeframe') }}" data-toggle="modal"><i class="fa fa-calendar"></i>&nbsp; Referral Time Frame</a></li>
                    </ul>
                </li>
                <li><a href="{{ url('mcc/track') }}"><i class="fa fa-line-chart"></i> Track</a></li>
                @elseif($user->level=='admin')
                <li><a href="{{ url('admin/') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wrench"></i> Manage <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('admin/users') }}" data-toggle="modal"><i class="fa fa-users"></i>&nbsp; IT Support</a></li>
                        <li><a href="{{ url('admin/facility') }}" data-toggle="modal"><i class="fa fa-hospital-o"></i>&nbsp; Facilities</a></li>
                        <li><a href="{{ url('admin/referral') }}" data-toggle="modal"><i class="fa fa-file-o"></i>&nbsp; Referral</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i> Report <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('admin/report/online') }}" data-toggle="modal"><i class="fa fa-users"></i>&nbsp; Online Users</a></li>
                        <li><a href="{{ url('admin/report/referral') }}" data-toggle="modal"><i class="fa fa-line-chart"></i>&nbsp; Referral Status</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ url('admin/daily/users') }}" data-toggle="modal"><i class="fa fa-users"></i>&nbsp; Daily Users</a></li>
                        <li class="dropdown-submenu">
                            <a class="test" tabindex="-1" href="#"><i class="fa fa-arrows-h"></i>&nbsp; Daily Referral</a>
                            <ul class="dropdown-menu">
                                <li><a tabindex="-1" href="{{ url('admin/daily/referral') }}">Hospital</a></li>
                                {{--EDITED--}}
                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Patient</a>
                                    <ul class="dropdown-menu">
                                        <li><a tabindex="-1" href="{{ url('admin/report/patient/incoming') }}">Incoming</a></li>
                                        <li><a tabindex="-1" href="{{ url('admin/report/patient/outgoing') }}">Outgoing</a></li>
                                    </ul>
                                </li>
                                {{--END--}}

                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a tabindex="-1" href="#"><i class="fa fa-folder-open-o"></i> Consolidated</a>
                            <ul class="dropdown-menu">
                                <li><a tabindex="-1" href="{{ url('admin/report/consolidated/incoming') }}">Incoming</a></li>
                                <li><a tabindex="-1" href="{{ url('admin/report/consolidated/outgoing') }}">Outgoing</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a href="{{ url('admin/login') }}"><i class="fa fa-sign-in"></i> Login As</a></li>
                @endif
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-gear"></i> Settings <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#resetPasswordModal" data-toggle="modal"><i class="fa fa-key"></i>&nbsp; Change Password</a></li>
                        @if($user->level=='doctor')
                        <li><a href="#dutyModal" data-toggle="modal"><i class="fa fa-user-md"></i>&nbsp; Change Login Status</a></li>
                        <li class="divider"></li>
                        <li><a href="#loginModal" data-toggle="modal"><i class="fa fa-users"></i>&nbsp; Switch User</a></li>
                        @else
                        <li class="divider"></li>
                        @endif
                        <li><a href="{{ url('logout') }}"><i class="fa fa-sign-out"></i> Logout</a></li>
                        @if(Session::get('admin'))
                        <li><a href="{{ url('admin/account/return') }}"><i class="fa fa-user-secret"></i> Back as Admin</a></li>
                        @endif
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>



@if(isset(Request::segments()[3]))
    <div class="{{ in_array(Request::segments()[0].'/'.Request::segments()[1].'/'.Request::segments()[2].'/'.Request::segments()[3], array('admin/report/patient/incoming','admin/report/patient/outgoing','admin/report/consolidated/incoming'), true) ? 'container-fluid' : 'container' }}">
        <div class="loading"></div>
        @yield('content')
        <div class="clearfix"></div>
    </div> <!-- /container -->
@else
    <div class="container">
        <div class="loading"></div>
        @yield('content')
        <div class="clearfix"></div>
    </div> <!-- /container -->
@endif

@include('modal.server')
@include('modal.password')
@include('modal.duty')
@include('modal.login')
@include('modal.incoming')
<footer class="footer">
    <div class="container">
        <p class="text-center">All Rights Reserved 2019 | Version 2.0</p>
    </div>
</footer>


        <!-- Bootstrap core JavaScript
    ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="{{ asset('resources/assets/js/jquery.min.js?v='.date('mdHis')) }}"></script>
<script src="{{ asset('resources/assets/js/jquery.form.min.js') }}"></script>
<script src="{{ asset('resources/assets/js/jquery-validate.js') }}"></script>
<script src="{{ asset('resources/assets/js/bootstrap.min.js') }}"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="{{ asset('resources/assets/js/ie10-viewport-bug-workaround.js') }}"></script>
<script src="{{ asset('resources/assets/js/script.js') }}?v=1"></script>
<script src="{{ asset('resources/plugin/Lobibox/Lobibox.js') }}?v=1"></script>
<script src="{{ asset('resources/plugin/select2/select2.min.js') }}?v=1"></script>
@include('script.firebase')
@include('script.newreferral')
@include('script.password')
@include('script.duty')
@include('script.desktop-notification')
@include('script.notification')
@yield('js')

</body>
</html>