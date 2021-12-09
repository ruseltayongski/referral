@if(!isset(Session::get("auth")->id))
    <div class="modal fade" tabindex="-1" role="dialog" id="notificationModal" style="margin-top: 30px;z-index: 99999 ">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php $dateNow = date('Y-m-d'); ?>
                    <h3 style="font-weight: bold" class="text-success">WHAT'S NEW?</h3>
                    <div class="">
                            <span class="text-info" style="font-size:1.1em;">
                                <strong>Central Visayas Electronic Health Referral System(CVe-HRS) Version 4.5</strong><br>
                                <ol>
                                    <li>
                                        BED AVAILABILITY STATUS <small class="badge bg-red" style="font-size: 6pt;"> New</small>
                                    </li>
                                    <li>
                                        NOT ACCEPTED within 30 minutes <small class="badge bg-red" style="font-size: 6pt;"> New</small>
                                    </li>
                                    <li>
                                        WALK-IN Monitoring <small class="badge bg-red" style="font-size: 6pt;"> New</small>
                                    </li>
                                    <li>
                                        Issue and Concern <small class="badge bg-red" style="font-size: 6pt;"> New</small>
                                    </li>
                                    <li>
                                        Onboard Users <small class="badge bg-red" style="font-size: 6pt;"> New</small>
                                    </li>
                                    <li>
                                        Login Status <small class="badge bg-red" style="font-size: 6pt;"> New</small>
                                    </li>
                                </ol>
                            </span>
                    </div>
                    <div class="text-warning">
                        <span style="font-size:1.1em;color: #8a6d3b;">
                            <strong><i class="fa fa-wifi"></i> Central Visayas Electronic Health Referral System(CVe-HRS) URL</strong><br>
                            <ol>
                                <li>https://cvehrs.doh.gov.ph/doh/referral/login</li>
                            </ol>
                        </span>
                    </div>
                    <div >
                        <div class="text-success">
                            <i class="fa fa-phone-square"></i> For further assistance, please message these following:
                            <ol type="I" style="color: #2f8030">
                                <li>Technical</li>
                                <ol type="A">
                                    <li >System Error</li>
                                    <ol>
                                        <li>Rusel T. Tayong - 09238309990</li>
                                    </ol>
                                    <li >Server - The request URL not found</li>
                                    <ol>
                                        <li>Garizaldy B. Epistola - 09338161374</li>
                                        <li>Reyan M. Sugabo - 09359504269</li>
                                        <li>Gerwin D. Gorosin - 09436467174 or 09154512989</li>
                                        <li>Harry John Divina - 09323633961 or 09158411553</li>
                                    </ol>
                                    <li >System Implementation/Training</li>
                                    <ol>
                                        <li>Rachel Sumalinog - 09484693136</li>
                                        <li>Harry John Divina - 09323633961 or 09158411553</li>
                                        <li>Ann Ermac - 09988449332</li>
                                    </ol>
                                    <li >
                                        Register Account and Forget Password
                                    </li>
                                    <ol>
                                        <li>Jaypee Dingal - 260 9740 local(436) - 09267313376 </li>
                                        <li>Rogemar Sumalinog - 09914100134 </li>
                                        <li>Remwel Sanchez - 09067334425 </li>
                                        <li>Jonaden Dela Cerna - 09068482000 </li>
                                        <li>Val Vincent Villan - 09686361161 </li>
                                        <li>Alex Jumao-as - 09997416211 </li>
                                    </ol>
                                </ol>
                                <li>Non - Technical</li>
                                <ol type="A">
                                    <ol>
                                        <li >Dr. Jera C. Armendarez - 09178243162</li>
                                        <li >Jane Michelle E. Raagas - 09173100611 </li>
                                        <li >Grace E. Petalcorin - 09673259481</li>
                                    </ol>
                                </ol>
                            </ol>
                        </div>
                    </div>
                    <div class="text-danger">
                        <div style="font-size:1.1em;">
                            <i class="fa fa-phone-square"></i> 711 DOH CVCHD HealthLine <strong>(032)411-6900</strong>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
        $('#notificationModal').modal('show');
    </script>
@endif
