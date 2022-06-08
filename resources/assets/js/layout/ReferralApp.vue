<template></template>
<script>
    export default {
        name : "ReferralApp",
        props: {
            user: Object,
            count_referral: Number
        },
        data(){
            return {
                increment_referral: Number
            }
        },
        methods: {
            playAudio() {
                audioElement.play();
                setTimeout(function(){
                    audioElement.pause();
                },10000);
            },
            initializedAudio() {
                let audioElement = document.createElement('audio');
                audioElement.setAttribute('src', $("#broadcasting_url").val()+"/public/notify.mp3");
            },
            notifyReco(code, picture, content) {
                Lobibox.notify("success", {
                    title: code,
                    size: 'normal',
                    delay: false,
                    closeOnClick: false,
                    img: picture,
                    msg: content
                });
            },
            notifyReferral(patient_name, referring_md, referring_name) {
                Lobibox.notify('success', {
                    delay: false,
                    title: 'New Referral',
                    msg: patient_name+' was referred by Dr. '+referring_md+' of '+referring_name,
                    img: $("#broadcasting_url").val()+"/resources/img/ro7.png",
                    sound: false
                });
            },
            notifyReferralSeen(patient_name, seen_by, seen_by_facility, patient_code, activity_id) {
                $("#seen_progress"+patient_code+activity_id).addClass("completed");
                Lobibox.notify('info', {
                    delay: false,
                    title: 'Referral Seen',
                    msg: patient_name+' was seen by Dr. '+seen_by+' of '+seen_by_facility,
                    img: $("#broadcasting_url").val()+"/resources/img/ro7.png"
                });
            },
            notifyReferralAccepted(patient_name, accepting_doctor, accepting_facility_name, activity_id, patient_code, tracking_id, date_accepted, remarks) {
                $("#accepted_progress"+patient_code+activity_id).addClass("completed");
                $("#html_websocket_departed"+patient_code).html(this.buttonDeparted(tracking_id));
                $("#prepend_from_websocket"+patient_code).prepend('<tr class="toggle toggle67525" style="display: table-row;">\n' +
                    '                                                            <td>'+date_accepted+'</td>\n' +
                    '                                                            <td>\n' +
                    '                                                                <span class="txtPatient">'+patient_name+'</span>  was accepted by <span class="txtDoctor">Dr. '+accepting_doctor+'</span> of <span class="txtHospital">'+accepting_facility_name+'</span>.\n' +
                    '                                                                <span class="remarks">Remarks: '+remarks+'</span>\n' +
                    '                                                            </td>\n' +
                    '                                                        </tr>');
                Lobibox.notify('success', {
                    delay: false,
                    title: 'Accepted',
                    msg: patient_name+' was accepted by Dr. '+accepting_doctor+' of '+accepting_facility_name,
                    img: $("#broadcasting_url").val()+"/resources/img/ro7.png",
                });
            },
            notifyReferralRejected(patient_code, date_rejected, rejected_by, rejected_by_facility, patient_name, remarks, activity_id) {
                $("#accepted_progress"+patient_code+activity_id).addClass("completed");
                $("#rejected_progress"+patient_code+activity_id).addClass("bg-red");
                $("#rejected_name"+patient_code+activity_id).html("Rejected");
                $("#prepend_from_websocket"+patient_code).prepend('' +
                    '<tr>\n' +
                    '                                                    <td>'+date_rejected+'</td>\n' +
                    '                                                    <td>\n' +
                    '                                                        <span class="txtDoctor">'+rejected_by+'</span> of <span class="txtHospital">'+rejected_by_facility+'</span> recommended to redirect <span class="txtPatient">'+patient_name+'</span> to other facility.\n' +
                    '                                                        <span class="remarks">Remarks: '+remarks+'</span>\n' +
                    '                                                        <br>\n' +
                    '                                                           <button class="btn btn-success btn-xs btn-redirected" data-toggle="modal" data-target="#redirectedFormModal" data-activity_code="'+patient_code+'">\n' +
                    '                                                                <i class="fa fa-ambulance"></i> Redirect to other facility\n' +
                    '                                                            </button>\n' +
                    '                                                     </td>\n' +
                    '                                                </tr>');
                Lobibox.notify('error', {
                    delay: false,
                    title: 'Rejected',
                    msg: patient_name+' was recommend to redirect by Dr. '+rejected_by+' of '+rejected_by_facility,
                    img: $("#broadcasting_url").val()+"/resources/img/ro7.png",
                });
            },
            notifyReferralCall(patient_code, count_caller, caller_date, caller_by, caller_by_facility, called_to_facility, caller_by_contact) {
                $("#count_caller"+patient_code).html(count_caller);
                $("#prepend_from_websocket"+patient_code).prepend('<tr>\n' +
                    '                                                       <td>'+caller_date+'</td>\n' +
                    '                                                            <td>\n' +
                    '                                                                <span class="txtDoctor">Dr. '+caller_by+'</span> of <span class="txtHospital">'+caller_by_facility+'</span> is requesting a call from <span class="txtHospital">'+called_to_facility+'</span>.\n' +
                    '                                                                 Please contact this number <span class="txtInfo">('+caller_by_contact+')</span> .\n' +
                    '                                                           </td>\n' +
                    '                                                        </tr>');
                Lobibox.notify('warning', {
                    delay: false,
                    title: 'Requesting a Call',
                    msg: 'Dr. '+caller_by+' of '+caller_by_facility+' is requesting a call. Please contact this number ('+caller_by_contact+') <br>'+ caller_date,
                    img: $("#broadcasting_url").val()+"/resources/img/ro7.png",
                });
            },
            notifyReferralDeparted(patient_name, departed_by, departed_by_facility, departed_date, mode_transportation) {
                Lobibox.notify('info', {
                    delay: false,
                    closeOnClick: false,
                    title: 'Referral Departed',
                    msg: patient_name+' was departed by Dr. '+departed_by+' of '+departed_by_facility+' and transported by '+mode_transportation+'<br>'+departed_date,
                    img: $("#broadcasting_url").val()+"/resources/img/ro7.png"
                });
            },
            notifyReferralArrived() {

            },
            notifyReferralNotArrived() {

            },
            notifyReferralAdmitted() {

            },
            notifyReferralDischarged() {

            },
            buttonSeen(count_seen, tracking_id) {
                return count_seen > 0 ? '<a href="#seenModal" data-toggle="modal" data-id="'+tracking_id+'" class="btn btn-success btn-xs btn-seen" style="margin-left:3px;"><i class="fa fa-user-md"></i> Seen\n' +
                    '                <small class="badge bg-green-active">'+count_seen+'</small>\n' +
                    '          </a>' : '';
            },
            buttonActivity(count_activity, tracking_id) {
                return count_activity > 0 ? '<a href="#" data-toggle="modal" data-id="'+tracking_id+'" class="btn btn-danger btn-xs btn-caller" style="margin-left:3px;"><i class="fa fa-chevron-circle-right"></i> Redirected\n' +
                    '                                            <small class="badge bg-red-active">'+count_activity+'</small>\n' +
                    '                                    </a>'
                    : '';
            },
            buttonReco(code,count_reco) {
                return '<button class="btn btn-xs btn-info btn-feedback margin-left" data-toggle="modal" data-target="#feedbackModal" data-code="'+code+'" onclick="viewReco($(this))" style="margin-left:3px;">\n' +
                    '            <i class="fa fa-comments"></i>\n' +
                    '            ReCo\n' +
                    '            <span class="badge bg-blue" id="reco_count'+code+'">'+count_reco+'</span>\n' +
                    '        </button>';
            },
            buttonDeparted(tracking_id) {
                return '<a href="#transferModal" data-toggle="modal" data-id="'+tracking_id+'" class="btn btn-xs btn-success btn-transfer"><i class="fa fa-ambulance"></i> Depart</a>';
            },
            buttonTrack(patient_code) {
                return '<br><a href="'+$("#broadcasting_url").val()+'/doctor/referred?referredCode='+patient_code+'" class="btn btn-xs btn-warning" target="_blank">\n' +
                    '                                                <i class="fa fa-stethoscope"></i> Track\n' +
                    '                                            </a>';
            }
        },
        created() {
            console.log("VUE JS VERSION 3!!!")
            this.initializedAudio()
            this.increment_referral = count_referral
            Echo.join('new_referral')
                .listen('NewReferral', (event) => {
                    if(this.user.facility_id === event.payload.referred_to) {
                        this.playAudio()
                        this.increment_referral++
                        if($("#referral_page_check").val()) {
                            console.log("append the refer patient");
                            $('.count_referral').html(this.increment_referral);
                            let type = event.payload.form_type;
                            type = type=='normal' ? 'normal-section':'pregnant-section';
                            let referral_type = (type=='normal-section') ? 'normal':'pregnant';
                            content = '<li>' +
                                '    <i class="fa fa-ambulance bg-blue-active"></i>\n' +
                                '    <div class="timeline-item '+type+'" id="item-'+event.payload.tracking_id+'">\n' +
                                '        <span class="time"><i class="icon fa fa-ambulance"></i> <span class="date_activity">'+event.payload.referred_date+'</span></span>\n' +
                                '        <h3 class="timeline-header no-border">' +
                                '           <strong class="text-bold">    '+
                                '           <a href="'+$("#broadcasting_url").val()+'/doctor/referred?referredCode='+event.payload.patient_code+'" class="patient_name" target="_blank">'+event.payload.patient_name+'</a>' +
                                '           </strong>'+
                                '           <small class="status">[ '+event.payload.patient_sex+', '+event.payload.age+' ]</small> was <span class="badge bg-blue">'+event.payload.status+'</span> to <span class="text-danger">'+event.payload.referred_department+'</span> by <span class="text-warning">Dr. '+event.payload.referring_md+'</span> of <span class="facility">'+event.payload.referring_name+'</span></h3>\n' +
                                '        <div class="timeline-footer">\n';

                            /*if(my_department_id==data.department_id) {*/
                            content +=  '    <div class="form-group">' +
                                '                <a class="btn btn-warning btn-xs view_form" href="#referralForm"\n' +
                                '                   data-toggle="modal"\n' +
                                '                   data-code="'+event.payload.patient_code+'"\n' +
                                '                   data-item="#item-'+event.payload.tracking_id+'"\n' +
                                '                   data-referral_status="referred"\n' +
                                '                   data-type="'+referral_type+'"\n' +
                                '                   data-id="'+event.payload.tracking_id+'"\n' +
                                '                   data-referred_from="'+event.payload.referred_from+'"\n' +
                                '                   data-patient_name="'+event.payload.patient_name+'"\n' +
                                '                   data-backdrop="static">\n' +
                                '                <i class="fa fa-folder"></i> View Form\n' +
                                '               </a>' +
                                                this.buttonSeen(event.payload.count_seen, event.payload.tracking_id)+
                                                this.buttonActivity(event.payload.count_activity, event.payload.tracking_id)+
                                                this.buttonReco(event.payload.patient_code, event.payload.count_reco)+
                                '               <h5 class="text-red blink_new_referral pull-right">New Referral</h5>'+
                                '             </div>';
                            /*}*/

                            content +=   '' +
                                '        </div>\n' +
                                '    </div>\n' +
                                '</li>';

                            $('.timeline').prepend(content);
                        }
                        this.notifyReferral(event.payload.patient_name, event.payload.referring_md, event.payload.referring_name)
                    }
                });

            Echo.join('reco')
                .listen('SocketReco', (event) => {
                    $("#reco_count"+event.payload.code).html(event.payload.feedback_count);
                    axios.get($("#broadcasting_url").val()+'/activity/check/'+event.payload.code+'/'+this.user.facility_id).then(response => {
                        if(response.data && event.payload.sender_facility !== this.user.facility_id) {
                            console.log("New Reco")
                            $(".reco-body").append(event.payload.feedback_receiver);
                            try {
                                let objDiv = document.getElementById(event.payload.code);
                                objDiv.scrollTop = objDiv.scrollHeight;
                                if (!objDiv.scrollTop)
                                    this.notifyReco(event.payload.code, event.payload.picture, event.payload.content)
                            } catch(err){
                                console.log("modal not open");
                                this.notifyReco(event.payload.code, event.payload.picture, event.payload.content)
                            }
                        }
                    });
                });

            Echo.join('referral_seen')
                .listen('SocketReferralSeen', (event) => {
                    $("#count_seen"+event.payload.patient_code).html(event.payload.count_seen); //increment seen both referring and referred
                    if(event.payload.referring_facility_id === this.user.facility_id) {
                        this.notifyReferralSeen(event.payload.patient_name, event.payload.seen_by, event.payload.seen_by_facility, event.payload.patient_code, event.payload.activity_id)
                    }
                });

            Echo.join('referral_accepted')
                .listen('SocketReferralAccepted', (event) => {
                    if(event.payload.referred_from === this.user.facility_id) {
                        this.notifyReferralAccepted(event.payload.patient_name, event.payload.accepting_doctor, event.payload.accepting_facility_name, event.payload.activity_id, event.payload.patient_code, event.payload.tracking_id ,event.payload.date_accepted, event.payload.remarks)
                    }
                });

            Echo.join('referral_rejected')
                .listen('SocketReferralRejected', (event) => {
                    if(event.payload.referred_from === this.user.facility_id) {
                        this.notifyReferralRejected(event.payload.patient_code, event.payload.date_rejected, event.payload.rejected_by, event.payload.rejected_by_facility, event.payload.patient_name, event.payload.remarks, event.payload.activity_id)
                    }
                });

            Echo.join('referral_call')
                .listen('SocketReferralCall', (event) => {
                    if(event.payload.called_to === this.user.facility_id) {
                        this.notifyReferralCall(event.payload.patient_code, event.payload.count_caller, event.payload.caller_date, event.payload.caller_by, event.payload.caller_by_facility, event.payload.called_to_facility, event.payload.caller_by_contact)
                    }
                });

            Echo.join('referral_departed')
                .listen('SocketReferralDeparted', (event) => {
                    if(event.payload.referred_to === this.user.facility_id) {
                        this.notifyReferralDeparted(event.payload.patient_name, event.payload.departed_by, event.payload.departed_by_facility, event.payload.departed_date, event.payload.mode_transportation)
                    }
                });

        }
    }
</script>