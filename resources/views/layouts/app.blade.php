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
    <!-- SELECT 2 -->
    <link href="{{ asset('resources/plugin/select2/select2.min.css') }}" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('resources/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/assets/css/bootstrap-theme.min.css') }}" rel="stylesheet">

    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('resources/plugin/Ionicons/css/ionicons.min.css') }}">

    <!-- Font awesome -->
    <link href="{{ asset('resources/assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="{{ asset('resources/assets/css/ie10-viewport-bug-workaround.css') }}" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="{{ asset('resources/assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('resources/assets/css/AdminLTE.min.css') }}">
    <!-- bootstrap datepicker -->

    <link href="{{ asset('resources/plugin/datepicker/datepicker3.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('resources/plugin/Lobibox/lobibox.css') }}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset('resources/plugin/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
    <link href="{{ asset('resources/plugin/daterangepicker_old/daterangepicker-bs3.css') }}" rel="stylesheet">

    <link href="{{ asset('resources/plugin/table-fixed-header/table-fixed-header.css') }}" rel="stylesheet">

    <link rel="manifest" href="{{ asset('/manifest.json') }}" />
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{ asset('resources/assets/js/jquery.min.js?v='.date('mdHis')) }}"></script>
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

        #myBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
            font-size: 18px;
            border: none;
            outline: none;
            background-color: rgba(38, 125, 61, 0.92);
            color: white;
            cursor: pointer;
            padding: 15px;
            border-radius: 4px;
        }
        #myBtn:hover {
            background-color: #555;
        }
        .form-label {
            font-size: 10pt;
        }

        .form-details {
            font-size: 12pt;
            color: #e08e0b;;
        }
    </style>
</head>
<body>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-default fixed-top">
        <div class="header" style="background-color:#2F4054;padding:8px;">
            <div class="col-lg-4">
                <div style="padding: 2px;">
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
            </div>
            <div class="col-lg-4">
                <div style="padding: 2px;">
                    <?php
                    $user_logs = \App\Login::where("userId",$user->id)->orderBy("id","desc")->first();
                    $login_time = $user_logs->login;
                    $user_logs->logout == "0000-00-00 00:00:00" ? $logout_time = explode(' ',$user_logs->login)[0].' 23:59:59' : $logout_time = $user_logs->logout;
                    $logout_time = date("M d, Y H:i:s",strtotime($logout_time));
                    ?>
                    <span class="title-info">Logout Time: </span> <strong class="text-red" id="logout_time"> </strong>&nbsp;
                    <button href="#setLogoutTime" data-toggle="modal" class="btn btn-xs btn-danger" onclick="openLogoutTime();"><i class="fa clock-o"></i> Set Time to Logout</button>
                </div>
            </div>
            <div class="col-lg-4">
                <div style="padding: 2px;">
                    @if($user->level != 'vaccine')
                        <span class="title-desc">{{ \App\Facility::find($user->facility_id)->name }}</span>
                    @endif
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="header" style="background-color:#59ab91;padding:10px;">
            <div class="container">
                @if($user->level == 'opcen')
                    <img src="{{ asset('resources/img/opcen_banner1.png') }}" class="img-responsive" />
                @elseif($user->level == 'bed_tracker')
                    <img src="{{ asset('resources/img/bed_banner.png') }}" class="img-responsive" />
                @elseif($user->level == 'vaccine')
                    <img src="{{ asset('resources/img/updated_vaccine_logo.png') }}" class="img-responsive" />
                @else
                    <img src="{{ asset('resources/img/referral_banner.png') }}" class="img-responsive" />
                @endif
            </div>
        </div>
        <div class="container-fluid">
            @include('layouts.navbar')
        </div>
    </nav>

    @if(isset(Request::segments()[3]))
        <div class="{{ in_array(Request::segments()[0].'/'.Request::segments()[1].'/'.Request::segments()[2].'/'.Request::segments()[3], array('admin/report/patient/incoming','admin/report/patient/outgoing','admin/report/consolidated/incoming','admin/report/graph/incoming','admin/report/consolidated/incomingv2','admin/report/graph/bar_chart'), true)
            ? 'container-fluid' : 'container' }}" >
            <div class="loading"></div>
            @yield('content')
            <div class="clearfix"></div>
        </div> <!-- /container -->
    @elseif(isset(Request::segments()[2]))
        <div class="{{ in_array(Request::segments()[0].'/'.Request::segments()[1].'/'.Request::segments()[2],
                        array('vaccine/vaccineview/1','vaccine/vaccineview/2','vaccine/vaccineview/3','vaccine/vaccineview/4',
                        'onboard/facility/1','onboard/facility/2','onboard/facility/3','onboard/facility/4',
                        'offline/facility/1','offline/facility/2','offline/facility/3','offline/facility/4',
                        'vaccine/facility/cebu','vaccine/facility/mandaue','vaccine/facility/lapu','weekly/report/1','weekly/report/2','weekly/report/3','weekly/report/4',
                        'admin/statistics/incoming1','admin/statistics/incoming','admin/statistics/1','admin/statistics/2','admin/statistics/3','admin/statistics/4','admin/statistics/0')
                       , true) ? 'container-fluid' : 'container' }}" >
            <div class="loading"></div>
            @yield('content')
            <div class="clearfix"></div>
        </div> <!-- /container -->
    @else
        <div class="{{ in_array(Request::segments()[0], array('vaccine','bed_admin','reports','monitoring'), true) ? 'container-fluid' : 'container' }}" id="container">
            <div class="loading"></div>
            <div class="row">
                @yield('content')
            </div>
            <div class="clearfix"></div>
        </div> <!-- /container -->
    @endif


    @include('modal.server')
    @include('modal.password')
    @include('modal.duty')
    @include('modal.login')
    @include('modal.incoming')
    @include('modal.feedback')

    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fa fa-arrow-up"></i> Go Top</button>
    <footer class="footer">
        <div class="container">
            <p class="pull-right">All Rights Reserveds {{ date("Y") }} | Version 5.7</p>
        </div>
    </footer>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('resources/plugin/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>

    <script src="{{ asset('resources/assets/js/jquery.form.min.js') }}"></script>
    <script src="{{ asset('resources/assets/js/jquery-validate.js') }}"></script>
    <script src="{{ asset('resources/assets/js/bootstrap.min.js') }}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ asset('resources/assets/js/ie10-viewport-bug-workaround.js') }}"></script>
    <script src="{{ asset('resources/assets/js/script.js') }}?v=1"></script>

    <script src="{{ asset('resources/plugin/Lobibox/Lobibox.js') }}?v=1"></script>
    <script src="{{ asset('resources/plugin/select2/select2.min.js') }}?v=1"></script>

    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{ asset('resources/plugin/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}?v=1"></script>

    <script src="{{ url('resources/plugin/daterangepicker_old/moment.min.js') }}"></script>
    <script src="{{ url('resources/plugin/daterangepicker_old/daterangepicker.js') }}"></script>

    <script src="{{ asset('resources/assets/js/jquery.canvasjs.min.js') }}?v=1"></script>

    <!-- TABLE-HEADER-FIXED -->
    <script src="{{ asset('resources/plugin/table-fixed-header/table-fixed-header.js') }}"></script>

    <audio id="carteSoudCtrl">
        <source src="{{ url('public/notify.mp3') }}" type="audio/mpeg">
    </audio>

    <script>

        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', "{{ url('public/notify.mp3') }}");

        audioElement.addEventListener('ended', function() {
            this.play();
        }, false);

        $(".select2").select2({ width: '100%' });

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
            document.getElementById("logout_time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";

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
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            $('body,html').animate({
                scrollTop : 0 // Scroll to top of body
            }, 500);
        }

        $('.select_facility').on('change',function(){
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
                    },
                    error: function(error){
                        $('#serverModal').modal();
                    }
                });
            }
        });

    </script>

    @include('script.firebase')
    @include('script.newreferral')
    @include('script.password')
    @include('script.duty')
    @include('script.desktop-notification')
    @include('script.notification_new')
    @include('script.feedback')

    @yield('js')

</body>
</html>