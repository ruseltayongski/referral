<?php
    $error = \Illuminate\Support\Facades\Input::get('error');
?>
@extends('layouts.app')

@section('content')
    <div class="col-md-9">
        <div class="jim-content">
            @if($error)
            <div class="alert alert-danger">
                <span class="text-danger">
                    <i class="fa fa-times"></i> Error swtiching account! Please try again.
                </span>
            </div>
            @endif
            <h3 class="page-header">Monthly Activity
            </h3>
            <div class="chart">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        @include('sidebar.quick')
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="notificationModal" style="margin-top: 30px;z-index: 99999 ">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                    <h3 style="font-weight: bold" class="text-success">WHAT'S NEW?</h3>
                    <?php
                    $dateNow = date('Y-m-d');
                    ?>
                    @if($dateNow==='2019-07-30')
                        <div class="alert alert-info">
                            <p class="text-info" style="font-size:1.3em;text-align: center;">
                                <strong>There will be a server maintenance TODAY (July 30, 2019) at 1:15PM to 02:00PM. Server optimization!</strong>
                            </p>
                        </div>
                    @endif
                    @if($dateNow >= '2020-07-01' && $dateNow <= '2020-12-30')
                        <div class="alert alert-info">
                            <blockquote class="text-info" style="font-size:1.1em;">
                                <strong  style="font-size: 15pt;">ANNOUNCEMENT</strong>
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
                    @if($dateNow >= '2019-11-19' && $dateNow <= '2019-11-30')
                        <div class="alert alert-info">
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
                        <div class="alert alert-warning">
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
                    <div class="alert alert-success ">
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
                                    <li>Ryan A. Padilla - 09294771871</li>
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
@endsection

@section('js')
@include('script.chart')
<script>
    $('#notificationModal').modal('show');
    var accepted = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    var rejected = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    var link = "{{ url('chart') }}";
    $.ajax({
        url: link,
        type: "GET",
        success: function(data){
            console.log(data)
            var chartdata = {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    // labels: month,
                    datasets: [
                        {
                            label: 'Referred',
                            backgroundColor: '#8e9cff',
                            data: data.referred
                        },
                        {
                            label: 'Accepted',
                            backgroundColor: '#26B99A',
                            data: data.accepted
                        },
                        {
                            label: 'Redirected',
                            backgroundColor: '#03586A',
                            data: data.rejected
                        }
                    ]
                }
            };


            var ctx = document.getElementById('barChart').getContext('2d');
            new Chart(ctx, chartdata);
        }
    });

</script>
@endsection

