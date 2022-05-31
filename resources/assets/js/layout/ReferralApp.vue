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
                audioElement.setAttribute('src', "https://cvehrs.doh.gov.ph/doh/referral/public/notify.mp3");
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
                    img: "https://cvehrs.doh.gov.ph/doh/referral/resources/img/ro7.png",
                    sound: false
                });
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
            }
        },
        created() {
            console.log("VUE JS VERSION 3..")
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
                                                this.buttonActivity(event.payload.count_seen, event.payload.tracking_id)+
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
        }
    }
</script>