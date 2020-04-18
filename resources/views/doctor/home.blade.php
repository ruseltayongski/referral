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
                    @if($dateNow >= '2019-07-31' && $dateNow <= '2019-08-31')
                        <div class="alert alert-info">
                            <p class="text-info" style="font-size:1.1em;">
                                <strong><i class="fa fa-info"></i> Version 2.0 was successfully launch</strong><br>
                                <ol type="I" style="color: #31708f;">
                                    <li>Other Salient Feactures(Recommended & Suggestion as of June 27,2019)</li>
                                    <ol type="A">
                                        <li >Name of Referred MD/HCW</li>
                                        <ul>
                                            <li>Instead of browsing (Scrolling up and down) in searching the referred MD / HCW, you can now search the name of the referred MD in the search bar.</li>
                                        </ul>
                                        <li >Feedback</li>
                                        <ul>
                                            <li>Changed the label from “Feedback” to “ReCo”</li>
                                        </ul>
                                        <li >Added Issues and Concern</li>
                                        <ul>
                                            <li>Added an optional “Issues and Concern” form, right after referring the patient to a specific MD / HCW</li>
                                        </ul>
                                        <li >Added Date and Time Transferred</li>
                                        <ul>
                                            <li>Added a “Travel button”, An event where a patient is already dispatched from hospital to another facility, and then timestamped with accurate Time and Date.</li>
                                        </ul>
                                        <li >Added Referral Logbook Matrix from the Referral Manual</li>
                                        <ul>
                                            <li>Department of Health Region – 7 Monitoring team can now have an overview of the incoming and outgoing patients.</li>
                                        </ul>
                                    </ol>
                                    <li>Vicente Sotto Memorial Medical Center Concern:</li>
                                    <ol type="A">
                                        <li >Chat & Feedback</li>
                                        <ul>
                                            <li>Fixed bugs in "can't display after sending the message"</li>
                                        </ul>
                                        <li >Disposition</li>
                                        <ul>
                                            <li>Fixed the "accepted and redirected patients" that labeled ACCEPTED only</li>
                                        </ul>
                                    </ol>
                                </ol>
                            </p>
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
                                <li >Web</li>
                                <ul>
                                    <li>Rusel T. Tayong - 09238309990</li>
                                    <li>Christian Dave L. Tipactipac - 09286039028</li>
                                    <li>Keith Joseph Damandaman - 09293780114</li>
                                </ul>
                                <li >Server - Can't access </li>
                                <ul>
                                    <li>Garizaldy B. Epistola - 09338161374</li>
                                    <li>Reyan M. Sugabo - 09359504269</li>
                                    <li>Ryan A. Padilla - 09294771871</li>
                                    <li>Gerwin D. Gorosin - 09436467174 or 09154512989</li>
                                    <li>Harry John Divina - 09323633961 or 09158411553</li>
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

