<!DOCTYPE html>
<html>
    <head>
        <title>E-REFERRAL</title>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link href="https://fonts.googleapis.com/css?family=DM+Sans:400,500,700&display=swap" rel="stylesheet">
        <script src="{{ asset('resources/assets/js/jquery.min.js?v='.date('mdHis')) }}"></script>
        <script src="{{ asset('public/js/app_video.js?version=').date('YmdHis') }}" defer></script>
    </head>
    <body>
        <input type="hidden" id="broadcasting_url" value="{{ url("/") }}">
        <div id="app_video">
            <video-app :user="{{ Session::get('auth') }}"></video-app>
        </div>
    </body>
</html>
