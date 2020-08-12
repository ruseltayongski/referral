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
  </head>
  <body class="hold-transition login-page">
   <div class="login-box">
        <center>
            <img src="{{ asset('resources/img/doh.png') }}" style="width: 25%"/><br>
            <label>Central Visayas Electronic Health Referral System(CVe-HRS)</label>
        </center>
          <form role="form" method="POST" action="{{ asset('login') }}" class="form-submit" >
              {{ csrf_field() }}
              <div class="login-box-body">
                <p class="login-box-msg">Sign in to start your session</p>
                  <div class="form-group has-feedback {{ Session::has('error') ? ' has-error' : '' }}">
                    <input id="username" autocomplete="off" type="text" placeholder="Login ID" autofocus class="form-control" name="username" value="{{ Session::get('username') }}">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    <span class="help-block">
                        @if(Session::has('error'))
                            <strong>{{ Session::get('error') }}</strong>
                        @endif
                    </span>
                  </div>
                  <div class="form-group has-feedback ">
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
                    @if($dateNow >= '2019-07-01' && $dateNow <= '2019-12-30')
                        <div >
                            <blockquote class="text-info" style="font-size:1.1em;">
                                <b style="font-size: 15pt;">ANNOUNCEMENT</b>
                                <br><br>
                                Good day everyone!
                                <br><br>
                                Please be informed that there will be a new URL/Link for the E-Referral from 203.177.67.126/doh/referral to 124.6.144.166/doh/referral
                                <br><br>
                                The said new URL/Link will be accessible on AUGUST 2, 2020 at 3PM.
                                And there will be a downtime on AUGUST 2, 2020 at 1PM to 3PM for the configuration of our new URL/Link.
                                <br><br>
                                Please be guided accordingly.
                                <br><br>
                                Thank you very much and keep safe.
                            </blockquote>
                        </div>
                    @endif
                    <h3 style="font-weight: bold" class="text-success">Contact Person:</h3>
                    <div >
                        <p class="text-success">
                            <i class="fa fa-phone-square"></i> For further assistance, please message these following:
                        <ol type="I" style="color: #2f8030">
                            <li>Technical</li>
                            <ol type="A">
                                <li >System Error</li>
                                <ul>
                                    <li>Rusel T. Tayong - 09238309990</li>
                                    <li>Christian Dave L. Tipactipac - 09286039028</li>
                                    <li>Keith Joseph Damandaman - 09293780114</li>
                                </ul>
                                <li >Server - The request URL not found</li>
                                <ul>
                                    <li>Garizaldy B. Epistola - 09338161374</li>
                                    <li>Reyan M. Sugabo - 09359504269</li>
                                    <li>Gerwin D. Gorosin - 09436467174 or 09154512989</li>
                                    <li>Harry John Divina - 09323633961 or 09158411553</li>
                                </ul>
                                <li >System Implementation/Training <small class="badge bg-red" style="font-size: 6pt;"> New</small></li>
                                <ul>
                                    <li>Ryan A. Padilla - 09294771871</li>
                                    <li>Rachel Sumalinog - 09484693136 <small class="badge bg-red" style="font-size: 6pt;"> New</small></li>
                                    <li>Kasilyn Lariosa - 09331720608 <small class="badge bg-red" style="font-size: 6pt;"> New</small></li>
                                    <li>Harry John Divina - 09323633961 or 09158411553</li>
                                </ul>
                            </ol>
                            <li>Non - Technical</li>
                            <ol type="A">
                                <ul>
                                    <li >Ronadith Capala Arriesgado - 09952100815</li>
                                    <li >Rolly Villarin - 09173209917 <small class="badge bg-red" style="font-size: 6pt;"> New</small></li>
                                    <li >Gracel R. Flores - 09453816462</li>
                                </ul>
                            </ol>
                            <h3 class="text-center" style="color: #2f8030">Thank you!</h3>
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
        @if(!Session::has("error"))
        $('#notificationModal').modal('show');
        @endif

        $('.btn-submit').on('click',function(){
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Validating...');

        });

    </script>
  </body>
</html>
