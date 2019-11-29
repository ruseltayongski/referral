<?php
$dateNow = date('Y-m-d');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DOHRO7 Referral System | Log in</title>
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
        <center>
            <img src="{{ asset('resources/img/doh.png') }}" style="width: 25%"/><br>
            <label>Central Visayas Electronic Health Referral System(CVe-HRS)</label>
        </center>
          <form role="form" method="POST" action="{{ url('/login') }}" class="form-submit" >
              {{ csrf_field() }}
              <div class="login-box-body">
                <p class="login-box-msg">Sign in to start your session</p>
                  <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                    <input id="username" autocomplete="off" type="text" placeholder="Login ID" autofocus class="form-control" name="username" value="{{ old('username') }}">
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

    <div class="modal fade" tabindex="-1" role="dialog" id="notificationModal" style="margin-top: 30px;z-index: 99999 ">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    @if($dateNow >= '2019-11-19' && $dateNow <= '2019-11-30')
                        <div >
                            <span class="text-info" style="font-size:1.1em;">
                                <strong><i class="fa fa-info"></i> Version 2.1 was successfully launch</strong><br>
                                <ol type="I" style="color: #31708f;font-size: 10pt;margin-top: 10px;">
                                    <li><i><b>Editable Patient</b></i> - Allowing the user to edit misspelled / typo informations</li>
                                    <li><i><b>Facility Dropdown</b></i> - Allowing the dropdown be search by keyword</li>
                                    <li><i><b>Outgoing Referral Report</b></i> - Adding the department to be filter</li>
                                    <li><i><b>Login Lifetime</b></i> - Session will expire in 30 minutes</li>
                                    <li><i><b>Input Date Range</b></i> - Filter date range UI interface improve</li>
                                    <li><i><b>Incoming Page</b></i> - UI interface improve and fixed bugs</li>
                                </ol>
                            </span>
                        </div>
                    @endif
                    @if($dateNow >= '2019-11-19' && $dateNow <= '2019-11-30')
                        <div >
                            <span class="text-warning" style="font-size:1.1em;">
                                <strong><i class="fa fa-plus"></i> Network server was successfully upgrade</strong><br>
                                <!--
                                <ol type="I" style="color: #f34a0f !important;font-size: 10pt;margin-top: 10px;">
                                    <li>
                                        new URL addresses will be the following:
                                    </li>
                                    <ol>
                                        <li><span class="badge bg-maroon">http://122.3.84.178/doh/referral/login</span></li>
                                        <li><span class="badge bg-maroon">http://203.177.67.125/doh/referral/login</span></li>
                                    </ol>
                                </ol>
                                -->
                            </span>
                        </div>
                    @endif
                    <h3 style="font-weight: bold" class="text-success">Contact Person:</h3>
                    <div >
                        <p class="text-success">
                            <i class="fa fa-phone-square"></i> For further assistance, please message these following:
                        <ol type="I" style="color: #2f8030">
                            <li>Technical</li>
                            <ol type="A">
                                <li >Web</li>
                                <ul>
                                    <li>Rusel T. Tayong - 09238309990</li>
                                    <li>Christian Dave L. Tipactipac - 09286039028</li>
                                </ul>
                                <li >Server - Can't access in web http://ro7sys.doh.gov.ph/doh/referral/login</li>
                                <ul>
                                    <li>Garizaldy B. Epistola - 09338161374</li>
                                    <li>Reyan M. Sugabo - 09359504269</li>
                                    <li>Ryan A. Padilla - 09294771871</li>
                                    <li>Gerwin D. Gorosin - 09436467174 or 09154512989</li>
                                </ul>
                            </ol>
                            <li>Non - Technical</li>
                            <ol type="A">
                                <ul>
                                    <li class="text-danger">Ronadith Capala Arriesgado - 09952100815 Please reach via message only</li>
                                    <li class="text-danger">Andrei Bacalan - 09396071936 Please reach via message only</li>
                                    <li class="text-danger">Grace R. Flores - 09328596338 Please reach via message only</li>
                                </ul>
                            </ol>
                            <h3 class="text-center" style="color: #2f8030">Thank you! &#128512;</h3>
                        </ol>
                        </p>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- jQuery 2.1.4 -->
    <script src="{{ asset('resources/assets/js/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="{{ asset('resources/assets/js/bootstrap.min.js') }}"></script>
    <script>
        $('#notificationModal').modal('show');
        $('.btn-submit').on('click',function(){
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Validating...');

            event.preventDefault();
            var username = $("#username").val();
            var password = $("#password").val();
            console.log(username);
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
                        if(data == "error"){
                            console.log("error if");
                            $('.has-feedback').addClass('has-error ');
                            $('.help-block').removeClass('hide').html('<strong>These credentials do not match our records.</strong>');
                            $('.btn-submit').html('<i class="fa fa-lock"></i>&nbsp;&nbsp;Sign In');

                        }else if(data == "denied"){
                            $('.has-feedback').addClass('has-error ');
                            $('.help-block').removeClass('hide').html('<strong>You don\'t have access in this system.</strong>');
                            $('.btn-submit').html('<i class="fa fa-lock"></i>&nbsp;&nbsp;Sign In');
                        }else{
                            window.location.href = "{{ url('/') }}/"+data;
                        }
                    },500);

                },
                error: function(){
                    setTimeout(function(){
                        window.location.reload(false);
                    },5000);
                }
            });
        });

        /*$('.form-submit').submit(function(e){

        });*/
    </script>
  </body>
</html>
