<!DOCTYPE html>
<html>
    <head>
        <title>E-REFERRAL</title>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://fonts.googleapis.com/css?family=DM+Sans:400,500,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('resources/assets/bootstrap-4.6.2-dist/css/bootstrap-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('resources/assets/bootstrap-4.6.2-dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('resources/plugin/Lobibox/lobibox.css') }}">
        <script src="{{ asset('resources/assets/js/jquery.min.js?v='.date('mdHis')) }}"></script>
        <script src="{{ asset('resources/assets/bootstrap-4.6.2-dist/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('resources/plugin/Lobibox/Lobibox.js') }}?v=2"></script>
        @if($referral_type == 'normal')
        <script src="{{ asset('public/js/app_video.js?version=').date('YmdHis') }}" defer></script>
        @elseif($referral_type == 'pregnant')
        <script src="{{ asset('public/js/app_video_pregnant.js?version=').date('YmdHis') }}" defer></script>
        @endif
        <!-- Font awesome -->
        <link href="{{ asset('resources/assets/css/font-awesome.min.css') }}" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="{{ asset('resources/assets/css/style.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('resources/assets/css/AdminLTE.min.css') }}">

        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet" href="{{ asset('resources/plugin/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
        <link href="{{ asset('resources/plugin/daterangepicker_old/daterangepicker-bs3.css') }}" rel="stylesheet">

        <!-- Bootstrap WYSIHTML5 -->
        <script src="{{ asset('resources/plugin/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}?v=1"></script>

        <!-- tinymce -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.0.3/tinymce.min.js"></script>

        <!-- SELECT 2 -->
        <link href="{{ asset('resources/plugin/select2/select2.min.css') }}" rel="stylesheet">
        <script src="{{ asset('resources/plugin/select2/select2.min.js') }}?v=1"></script>

        <script src="{{ url('resources/plugin/daterangepicker_old/moment.min.js') }}"></script>

        @include('modal.feedback')
        @include('layouts.app_script')
        @include('script.feedback')        
    </head>
    <body>
        <input type="hidden" id="broadcasting_url" value="{{ url("/") }}">
        @if($referral_type == 'normal')
            <div id="app_video">
                <video-app :user="{{ Session::get('auth') }}"></video-app>
            </div>
        @elseif($referral_type == 'pregnant')
            <div id="app_video_pregnant">
                <video-app-pregnant :user="{{ Session::get('auth') }}"></video-app-pregnant>
            </div>
        @endif
    </body>
</html>
