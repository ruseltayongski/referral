<template></template>
<script>
    export default {
        name : "ReferralApp",
        props: {
            user: Object,
            count_referral: Number
        },
        created() {
            console.log(this.user)
            Echo.join('new_referral')
                .listen('NewReferral', (event) => {
                    console.log(event.payload)
                    if(this.user.facility_id === event.payload.referred_to) {
                        console.log("append the refer patient");
                        let type = event.payload.form_type;
                        type = type=='normal' ? 'normal-section':'pregnant-section';
                        let referral_type = (type=='normal-section') ? 'normal':'pregnant';
                        $('.count_referral').html(count_referral+1);
                        content = '<li>' +
                            '    <i class="fa fa-ambulance bg-blue-active"></i>\n' +
                            '    <div class="timeline-item '+type+'" id="item-'+event.payload.tracking_id+'">\n' +
                            '        <span class="time"><i class="icon fa fa-ambulance"></i> <span class="date_activity">'+event.payload.referred_date+'</span></span>\n' +
                            '        <h3 class="timeline-header no-border">' +
                            '           <strong class="text-bold">    '+
                            '           <a href="'+'https://'+window.location.hostname+'/referral/doctor/referred?referredCode='+event.payload.patient_code+'" class="patient_name" target="_blank">'+event.payload.patient_name+'</a>' +
                            '           </strong>'+
                            '           <small class="status">[ '+event.payload.patient_sex+', '+event.payload.age+' ]</small> was <span class="badge bg-blue">referred</span> to <span class="text-danger">'+event.payload.referred_department+'</span> by <span class="text-warning">Dr. '+event.payload.referring_md+'</span> of <span class="facility">'+event.payload.referring_name+'</span></h3>\n' +
                            '        <div class="timeline-footer">\n';

                        /*if(my_department_id==data.department_id) {*/
                            content +=  '    <a class="btn btn-warning btn-xs view_form" href="#referralForm"\n' +
                            '               data-toggle="modal"\n' +
                            '               data-code="'+event.payload.patient_code+'"\n' +
                            '               data-item="#item-'+event.payload.tracking_id+'"\n' +
                            '               data-referral_status="referred"\n' +
                            '               data-type="'+referral_type+'"\n' +
                            '               data-id="'+event.payload.tracking_id+'"\n' +
                            '               data-referred_from="'+event.payload.referred_from+'"\n' +
                            '               data-patient_name="'+event.payload.patient_name+'"\n' +
                            '               data-backdrop="static">\n' +
                            '                <i class="fa fa-folder"></i> View Form\n' +
                            '            </a>' +
                            '<h5 class="text-red blink_new_referral pull-right">New Referral</h5>';
                        /*}*/

                        content +=   '' +
                            '        </div>\n' +
                            '    </div>\n' +
                            '</li>';

                        $('.timeline').prepend(content);

                        Lobibox.notify('success', {
                            delay: false,
                            title: 'New Referral',
                            msg: event.payload.patient_name+' was referred by Dr. '+event.payload.referring_md+' of '+event.payload.referring_name,
                            img: "https://cvehrs.doh.gov.ph/doh/referral/resources/img/ro7.png",
                            sound: false
                        });
                        $('#carteSoudCtrl')[0].play();
                    }
                });
        }
    }
</script>