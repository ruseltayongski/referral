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
                    <div class="">
                            <span class="text-info" style="font-size:1.1em;">
                                <strong>Central Visayas Electronic Health Referral System(CVe-HRS) Version 4.0</strong><br>
                                <ol>
                                    <li>
                                        STATISTICS REPORT <small class="badge bg-red" style="font-size: 6pt;"> New</small>
                                    </li>
                                    <li>
                                        CHAT MENU <small class="badge bg-green" style="font-size: 6pt;"> New</small>
                                    </li>
                                    <li>
                                        IMPROVED DASHBOARD <small class="badge bg-green" style="font-size: 6pt;"> New</small>
                                    </li>
                                </ol>
                            </span>
                    </div>
                @endif
                @if($dateNow >= '2019-07-01' && $dateNow <= '2019-12-30')
                    <br><div class="text-info">
                        <strong >ANNOUNCEMENT</strong>
                        <blockquote style="font-size:1.1em;">
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
                <div class="text-warning">
                    <span style="font-size:1.1em;color: #8a6d3b;">
                        <strong><i class="fa fa-wifi"></i> Central Visayas Electronic Health Referral System(CVe-HRS) URL</strong><br>
                        <ol>
                            <li>http://122.3.84.178/doh/referral/login</li>
                            <li>http://124.6.144.166/doh/referral/login</li>
                        </ol>
                    </span>
                </div>
                <div >
                    <p class="text-success">
                        <i class="fa fa-phone-square"></i> For further assistance, please message these following:
                    <ol type="I" style="color: #2f8030">
                        <li>Technical</li>
                        <ol type="A">
                            <li >System Error</li>
                            <ul>
                                <li>Rusel T. Tayong - 09238309990</li>
                                <li>Keith Joseph Damandaman - 09293780114</li>
                            </ul>
                            <li >Server - The request URL not found</li>
                            <ul>
                                <li>Garizaldy B. Epistola - 09338161374</li>
                                <li>Reyan M. Sugabo - 09359504269</li>
                                <li>Gerwin D. Gorosin - 09436467174 or 09154512989</li>
                                <li>Harry John Divina - 09323633961 or 09158411553</li>
                            </ul>
                            <li >System Implementation/Training</li>
                            <ul>
                                <li>Ryan A. Padilla - 09294771871</li>
                                <li>Rachel Sumalinog - 09484693136</li>
                                <li>Kasilyn Lariosa - 09331720608</li>
                                <li>Harry John Divina - 09323633961 or 09158411553</li>
                            </ul>
                            <li >
                                Register Account and Forget Password
                            </li>
                            <ul>
                                <li>Buch Angelou Fuentes - 09059553214 <small class="badge bg-red" style="font-size: 6pt;"> New</small></li>
                                <li>Jaypee Dingal - 260 9740 local(436) - 09267313376 <small class="badge bg-red" style="font-size: 6pt;"> New</small></li>
                                <li>Amalio Enero Jr - 260 9740 local(438) - 09101604890 <small class="badge bg-red" style="font-size: 6pt;"> New</small></li>
                                <li>John L. Ardiente - 260 9740 local(435) - 09208658303 <small class="badge bg-red" style="font-size: 6pt;"> New</small></li>
                            </ul>
                        </ol>
                        <li>Non - Technical</li>
                        <ol type="A">
                            <ul>
                                <li >Ronadith Capala Arriesgado - 09952100815</li>
                                <li >Rolly Villarin - 09173209917 </li>
                                <li >Gracel R. Flores - 09453816462</li>
                            </ul>
                        </ol>
                        <h3 class="text-center" style="color: #2f8030">DOH hotline number: 260 9740</h3>
                    </ol>
                    </p>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $('#notificationModal').modal('show');
</script>