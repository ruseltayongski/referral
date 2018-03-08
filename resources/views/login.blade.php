<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DOHRO7 Refferal System | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ asset('resources/assets/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('resources/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/assets/css/AdminLTE.min.css') }}">
    <link rel="icon" href="{{ asset('resources/img/favicon.png') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  </head>
  <body class="hold-transition login-page">
    @if(Session::has('ok'))
        <div class="row">
            <div class="alert alert-success text-center">
                <strong class="text-center">{{ Session::get('ok') }}</strong>
            </div>
        </div>
    @endif

    <div class="login-box">
      <div class="login-logo">
        <img src="{{ asset('resources/img/logo.png') }}" />
        <br />
        <a href="{{ url('') }}"><b>Referral</b> System</a>

      </div><!-- /.login-logo -->
      <form role="form" method="POST" action="{{ url('/login') }}" class="form-submit" >
          {{ csrf_field() }}
          <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>        
              <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                <input id="username" autocomplete="off" type="text" placeholder="Login ID" class="form-control" name="username" value="{{ old('username') }}">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                <span class="help-block hide">

                </span>
              </div>

              <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" class="form-control" name="password" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              </div>
                <div class="row">
                    <div class="col-xs-7">
                        <div class="form-group">

                        </div>
                    </div><!-- /.col -->
                    <div class="col-xs-5">
                        <button type="submit" class="btn btn-primary btn-block btn-flat btn-submit">
                            <i class="fa fa-lock"></i>&nbsp;&nbsp;Sign In
                        </button>
                    </div><!-- /.col -->
                </div> 
            </div><!-- /.login-box-body -->
            
      </form>        
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="{{ asset('resources/assets/js/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="{{ asset('resources/assets/js/bootstrap.min.js') }}"></script>
    <script>
        $('.btn-submit').on('click',function(){
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Validating...');
        });

        $('.form-submit').submit(function(e){
             e.preventDefault();
             var username = $(this).find(':input#username').val();
             var password = $(this).find(':input#password').val();
             var link = "{{ url('login') }}";
             $.ajax({
                 url: link,
                 type: 'POST',
                 data: {
                     _token: "{{ csrf_token() }}",
                     username: username,
                     password: password
                 },
                 success: function(data){
                     console.log(data);
                     setTimeout(function(){
                         if(data==='error'){
                             $('.has-feedback').addClass('has-error ');
                             $('.help-block').removeClass('hide').html('<strong>These credentials do not match our records.</strong>');
                             $('.btn-submit').html('<i class="fa fa-lock"></i>&nbsp;&nbsp;Sign In');

                         }else if(data==='denied'){
                             $('.has-feedback').addClass('has-error ');
                             $('.help-block').removeClass('hide').html('<strong>You don\'t have access in this system.</strong>');
                             $('.btn-submit').html('<i class="fa fa-lock"></i>&nbsp;&nbsp;Sign In');
                         }else{
                             window.location.href = "{{ url('/') }}/"+data;
                         }
                     },500);

                 }
             });
        });
    </script>
  </body>
</html>
