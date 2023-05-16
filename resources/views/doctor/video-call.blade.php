<!DOCTYPE html>
<html>
    <head>
        <title>E-REFERRAL</title>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>


        <link href="https://fonts.googleapis.com/css?family=DM+Sans:400,500,700&display=swap" rel="stylesheet">

       {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>--}}

        {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>--}}

        {{--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">--}}
        <link rel="stylesheet" href="{{ asset('resources/assets/bootstrap-4.6.2-dist/css/bootstrap-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('resources/assets/bootstrap-4.6.2-dist/css/bootstrap.min.css') }}">
        <script src="{{ asset('resources/assets/js/jquery.min.js?v='.date('mdHis')) }}"></script>
        <script src="{{ asset('resources/assets/bootstrap-4.6.2-dist/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('public/js/app_video.js?version=').date('YmdHis') }}" defer></script>
    </head>
    <body>
        <input type="hidden" id="broadcasting_url" value="{{ url("/") }}">
        <div id="app_video">
            <video-app :user="{{ Session::get('auth') }}"></video-app>
        </div>
    </body>
</html>
