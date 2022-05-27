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
                //this.playAudio();
            }
        },
        created() {
            console.log("VUE JS VERSION 3...")
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
                                '           <small class="status">[ '+event.payload.patient_sex+', '+event.payload.age+' ]</small> was <span class="badge bg-blue">referred</span> to <span class="text-danger">'+event.payload.referred_department+'</span> by <span class="text-warning">Dr. '+event.payload.referring_md+'</span> of <span class="facility">'+event.payload.referring_name+'</span></h3>\n' +
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
                                '               <button class="btn btn-xs btn-info btn-feedback" data-toggle="modal" data-target="#feedbackModal" data-code="'+event.payload.patient_code+'">\n' +
                                '                   <i class="fa fa-comments"></i>\n' +
                                '                       ReCo' +
                                '                </button>'+
                                '               <h5 class="text-red blink_new_referral pull-right">New Referral</h5>'+
                                '             </div>';
                            /*}*/

                            content +=   '' +
                                '        </div>\n' +
                                '    </div>\n' +
                                '</li>';

                            $('.timeline').prepend(content);
                        }

                        Lobibox.notify('success', {
                            delay: false,
                            title: 'New Referral',
                            msg: event.payload.patient_name+' was referred by Dr. '+event.payload.referring_md+' of '+event.payload.referring_name,
                            img: "https://cvehrs.doh.gov.ph/doh/referral/resources/img/ro7.png",
                            sound: false
                        });
                    }
                });
        }
    }
</script>