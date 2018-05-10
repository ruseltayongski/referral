<script>
    <?php $user=Session::get('auth');?>
    var myfacility_name = "{{ \App\Facility::find($user->facility_id)->name }}";
    var referralRef = dbRef.ref('Referral');
    var seenRef = dbRef.ref('Seen');
    var acceptRef = dbRef.ref('Accept');
    var rejectRef = dbRef.ref('Reject');
    var callRef = dbRef.ref('Call');
    var arriveRef = dbRef.ref('Arrival');
    var admitRef = dbRef.ref('Admit');
    var dischargeRef = dbRef.ref('Discharge');
    var transferRef = dbRef.ref('Transfer');

    seenRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var item = '.code-'+data.code;
        var activity = '#activity-'+data.activity_id;
        var date = data.date;
        item = $(item).find('#item-'+data.item);

        $(item).removeClass('pregnant-section normal-section').addClass('read-section');
        $(item).find('.icon').removeClass('fa-ambulance').addClass('fa-eye');
        $(item).find('.date_activity').html(date);
        $(activity).removeClass('pregnant-section normal-section').addClass('read-section');
        $(activity).find('.icon').removeClass('fa-ambulance').addClass('fa-eye');
        $(activity).find('.date_activity').html(date);
    });

//    var referralContent = '';
//    referralRef.on('child_added',function(snapshot){
//        snapshot.forEach(function(childSnapshot) {
//            var data = childSnapshot.val();
//            var date = data.date;
//            var action_md = data.action_md;
//            var facility_name = data.facility_name;
//            var patient_name = data.patient_name;
//            console.log(snapshot.val());
//            referralContent = '<li>' +
//                '<div class="timeline-item normal-section" id="activity-'+data.activity_id+'">\n' +
//                '    <span class="time"><i class="icon fa fa-ambulance"></i> <span class="date_activity">'+date+'</span></span>\n' +
//                '    <a>\n' +
//                '        <div class="timeline-header no-border">\n' +
//                '            '+data.name+'  was referred by <span class="text-success">Dr. '+data.referring_md+'</span> to <span class="facility">'+data.referred_facility+'.</span>\n' +
//                '        </div>\n' +
//                '    </a>\n' +
//                '</div>' +
//                '</li>';
//
//            $('.code-'+data.patient_code+' > li:nth-child(1)').after(referralContent);
//        });
//
//        //$('.code-'+data.code).append(acceptContent);
//    });

    var acceptContent = '';
    var rejectContent = '';
    acceptRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var action_md = data.action_md;
        var facility_name = data.facility_name;
        var patient_name = data.patient_name;
        acceptContent = '<li>\n' +
            '    <div class="timeline-item read-section">\n' +
            '        <span class="time"><i class="fa fa-user-plus"></i> '+date+'</span>\n' +
            '        <a>\n' +
            '            <div class="timeline-header no-border">\n' +
            '                '+patient_name+'  was accepted by <span class="text-success">Dr. '+action_md+'</span> of <span class="facility">'+facility_name+'</span>.</span>\n' +
            '            <br />' +
            '            <div class="text-remarks">Remarks: '+data.reason+'</div>'+
            '            </div>\n' +
            '        </a>\n' +
            '\n' +
            '    </div>\n' +
            '</li>';
        $('.code-'+data.code+' > li:nth-child(1)').after(acceptContent);
        //$('.code-'+data.code).append(acceptContent);
    });

    rejectRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var action_md = data.action_md;
        var old_facility = data.old_facility;
        var new_facility = data.new_facility;
        console.log(data);

        rejectContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-user-times"></i> '+date+'</span>\n' +
            '    <a>\n' +
            '        <div class="timeline-header no-border">\n' +
            '            <span class="text-danger">Dr. '+action_md+'</span> of <span class="facility">'+old_facility+'</span> recommended to redirect <span class="text-success">'+patient_name+'</span> to other facility .\n' +
            '            <br />' +
            '            <div class="text-remarks">Remarks: '+data.reason+'</div>';
        if(data.referred_from=="{{ $user->facility_id }}"){
            rejectContent += '<button class="btn btn-success btn-xs btn-referred" data-toggle="modal" data-target="#referredFormModal" data-activity_id="'+data.activity_id+'">\n' +
                '    <i class="fa fa-ambulance"></i> Refer to other facility\n' +
                '</button>';
        }

         rejectContent +='        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code+' > li:nth-child(1)').after(rejectContent);
        //$('.code-'+data.code).append(rejectContent);
    });

    var callContent = '';
    callRef.on('child_added',function(snapshot){
        var data = snapshot.val();

        var date = data.date;
        var action_md = data.action_md;
        var facility_calling = data.facility_calling;
        console.log(data.code);
        callContent = '<li><div class="timeline-item normal-section">\n' +
            '    <span class="time"><i class="fa fa-phone"></i> '+date+'</span>\n' +
            '    <a>\n' +
            '        <div class="timeline-header no-border">\n' +
            '            <span class="text-info">Dr. '+action_md+'</span> of <span class="facility">'+facility_calling+'</span> is requesting a call from <span class="facility">'+data.referred_name+'</span>. ';

        if(data.referred_from=="{{ $user->facility_id }}"){
            callContent += 'Please contact this number <span class="text-danger">('+data.contact+')</span>.</span>\n' +
                '<br />\n' +
                '  <button type="button" class="btn btn-success btn-sm btn-call"\n' +
                '   data-action_md = "'+data.action_md+'"\n' +
                '     data-facility_name = "'+data.facility_calling+'"\n' +
                '      data-activity_id="'+data.activity_id+'"><i class="fa fa-phone"></i> Called</button>\n';
        }

        callContent += '        <div class="text-remarks hide"></div>' +
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code+' > li:nth-child(1)').after(callContent);
        //$('.code-'+data.code).append(callContent);
    });

    var arriveContent = '';
    arriveRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        arriveContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-wheelchair"></i> '+date+'</span>\n' +
            '    <a>\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+' arrived at <span class="facility">'+current_facility+'</span>.\n' +
            '            <br />' +
            '            <div class="text-remarks">Remarks: '+data.remarks+'</div>'+
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code+' > li:nth-child(1)').after(arriveContent);
        //$('.code-'+data.code).append(arriveContent);

    });

    var admitContent = '';
    admitRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        admitContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-stethoscope"></i> '+date+'</span>\n' +
            '    <a>\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+' admitted at <span class="facility">'+current_facility+'</span>.\n' +
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code+' > li:nth-child(1)').after(admitContent);
//        $('.code-'+data.code).append(admitContent);

    });

    var dischargeContent = '';
    dischargeRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        dischargeContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-wheelchair-alt"></i> '+date+'</span>\n' +
            '    <a>\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+' discharged from <span class="facility">'+current_facility+'</span>.\n' +
            '            <br />' +
            '            <div class="text-remarks">Remarks: '+data.remarks+'</div>'+
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code+' > li:nth-child(1)').after(dischargeContent);
//        $('.code-'+data.code).append(dischargeContent);

    });

    var transferContent = '';
    transferRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var action_md = data.action_md;
        var old_facility = data.old_facility;
        var new_facility = data.new_facility;

        transferContent = '<li><div class="timeline-item normal-section" id="activity-'+data.activity_id+'">\n' +
            '    <span class="time"><i class="icon fa fa-ambulance"></i> <span class="date_activity">'+date+'</span></span>\n' +
            '    <a>\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+'  was referred by <span class="text-success">Dr. '+action_md+'</span> of <span class="facility">'+old_facility+'</span> to <span class="facility">'+new_facility+'.</span>\n';
            if(data.reason){
                transferContent += '<br />' +
                    '            <div class="text-remarks">Remarks: '+data.reason+'</div>';
            }
        transferContent += '        </div>\n' +
            '    </a>\n' +
            '</div></li>';
        $('.code-'+data.code+' > li:nth-child(1)').after(transferContent);
//        $('.code-'+data.code).append(transferContent);
    });
</script>

{{--Script for Call Button--}}
<script>
    $('body').on('click','.btn-call',function(){
        $('.loading').show();
        var action_md = $(this).data('action_md');
        var facility_name = $(this).data('facility_name');
        var activity_id = $(this).data('activity_id');
        var md_name = "{{ $user->fname }} {{ $user->mname }} {{ $user->lname }}";
        var div = $(this).parent().closest('.timeline-item');
        div.removeClass('normal-section').addClass('read-section');
        $(this).hide();
        div.find('.text-remarks').removeClass('hide').html('Remarks: Dr. '+md_name+' called '+facility_name);
        $.ajax({
            url: "{{ url('doctor/referral/call/') }}/" + activity_id,
            type: 'GET',
            success: function() {
                setTimeout(function(){
                    $('.loading').hide();
                },300);
            },
            error: function(){
                $('#serverModal').modal();
            }
        });
    });
</script>

{{--script for refer to other facility--}}
<script>
    $('.select_facility').on('change',function(){
        var id = $(this).val();
        var url = "{{ url('location/facility/') }}";
        $.ajax({
            url: url+'/'+id,
            type: 'GET',
            success: function(data){
                console.log(data);
                $('.facility_address').html(data.address);

                $('.select_department').empty()
                    .append($('<option>', {
                        value: '',
                        text : 'Select Department...'
                    }));
                jQuery.each(data.departments, function(i,val){
                    $('.select_department').append($('<option>', {
                        value: val.id,
                        text : val.description
                    }));
                });
            },
            error: function(){
                $('#serverModal').modal();
            }
        });

    });
    var activity_id = 0;
    $('body').on('click','.btn-referred',function(){
        activity_id = $(this).data('activity_id');
    });

    $('body').on('submit','#referredForm',function(e){
         e.preventDefault();
         $('.loading').show();
        var referred_to = $('#referredForm').find('.new_facility').val();
        var department_name = $('.select_department_referred :selected').text();
        var department_id = $('.select_department_referred').val();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/referral/redirect/') }}/"+activity_id,
            type: 'POST',
            success: function(data){
                console.log(data);

                transferRef.push({
                    date: data.date,
                    item: data.track_id,
                    new_facility: data.referred_facility,
                    old_facility: myfacility_name,
                    action_md: data.action_md,
                    patient_name: data.patient_name,
                    code: data.code,
                    activity_id: data.activity_id
                });

                transferRef.on('child_added',function(d){
                    transferRef.child(d.key).remove();
                });

                var connRef = dbRef.ref('Referral');
                var refer_data = {
                    referring_name: myfacility_name,
                    patient_code: data.code,
                    name: data.patient_name,
                    age: data.age,
                    sex: data.sex,
                    date: data.date,
                    form_type: data.form_type,
                    tracking_id: data.track_id,
                    referring_md: data.action_md,
                    department_name: department_name,
                    department_id: department_id,
                    activity_id: data.activity_id,
                    referred_facility: data.referred_facility
                };
                console.log(refer_data);
                connRef.child(referred_to).push(refer_data);

                connRef.on('child_added',function(data){
                    setTimeout(function(){
                        connRef.child(data.key).remove();
                        window.location.reload(false);
                    },500);
                });

                var data = {
                    "to": "/topics/ReferralSystem",
                    "data": {
                        "subject": "New Referral",
                        "date": data.date,
                        "body": data.patient_name+" was referred to your facility from "+myfacility_name+"!"
                    }
                };
                $.ajax({
                    url: 'https://fcm.googleapis.com/fcm/send',
                    type: 'post',
                    data: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'key=AAAAJjRh3xQ:APA91bFJ3YMPNZZkuGMZq8MU8IKCMwF2PpuwmQHnUi84y9bKiozphvLFiWXa5I8T-lP4aHVup0Ch83PIxx8XwdkUZnyY-LutEUGvzk2mu_YWPar8PmPXYlftZnsJCazvpma3y5BI7QHP'
                    },
                    dataType: 'json',
                    success: function (data) {
                        console.info(data);
                    }
                });
            },
            error: function(){
                $('#serverModal').modal();
            }
        });
    });
</script>

{{--show and hide activity--}}
<script>
    $('ul.timeline li').not(":first-child").not(":nth-child(2)").hide();
    $('.btn-activity').on('click',function(){
        var item = $(this).parent().parent().parent().parent().find('li');
        item.not(":first-child").not(":nth-child(2)").toggle();
    });
//    $('ul.timeline li:first-child').on('click',function(){
//        var item = $(this).parent().find('li');
//        item.not(":first-child").not(":nth-child(2)").toggle();
//    });
</script>

{{--VIEW FORM--}}
<script>
    var id = 0;
    $('.view_form').on('click',function(){
        $('.loading').show();
        code = $(this).data('code');
        form_type = $(this).data('type');
        id = $(this).data('id');

        $('#normalFormModal').find('span').html('');
        $('#pregnantFormModal').find('span').html('');

        if(form_type=='normal'){
            getNormalForm();
        }else{
            getPregnantForm();
        }
    });
    function getNormalForm()
    {
        $.ajax({
            url: "{{ url('doctor/referral/data/normal') }}/"+id,
            type: "GET",
            success: function(data){
                var print_url = "{{ url('doctor/print/form/') }}/"+id;
                $('.btn-refer-normal').attr('href',print_url);
                patient_name = data.patient_name;
                referring_name = data.referring_name;

                var address='';
                var patient_address='';
                var referred_address = '';

                address += (data.facility_brgy) ? data.facility_brgy+', ': '';
                address += (data.facility_muncity) ? data.facility_muncity+', ': '';
                address += (data.facility_province) ? data.facility_province: '';

                referred_address += (data.ff_brgy) ? data.ff_brgy+', ': '';
                referred_address += (data.ff_muncity) ? data.ff_muncity+', ': '';
                referred_address += (data.ff_province) ? data.ff_province: '';

                patient_address += (data.patient_brgy) ? data.patient_brgy+', ': '';
                patient_address += (data.patient_muncity) ? data.patient_muncity+', ': '';
                patient_address += (data.patient_province) ? data.patient_province: '';

                var case_summary = data.case_summary;
                if (/\n/g.test(case_summary))
                {
                    case_summary = case_summary.replace(/\n/g, '<br>');
                }

                var reco_summary = data.reco_summary;
                if (/\n/g.test(reco_summary))
                {
                    reco_summary = reco_summary.replace(/\n/g, '<br>');
                }

                var diagnosis = data.diagnosis;
                if (/\n/g.test(diagnosis))
                {
                    diagnosis = diagnosis.replace(/\n/g, '<br>');
                }

                var reason = data.reason;
                if (/\n/g.test(reason))
                {
                    reason = reason.replace(/\n/g, '<br>');
                }


                age = data.age;
                sex = data.sex;
                referring_contact = data.referring_contact;
                referring_md_contact = data.referring_md_contact;
                referred_name = data.referring_name;

                $('span.referring_name').html(data.referring_name);
                $('span.department_name').html(data.department);
                $('span.referring_contact').html(data.referring_contact);
                $('span.referring_address').html(address);
                $('span.referred_name').html(data.referred_name);
                $('span.referred_address').html(referred_address);
                $('span.time_referred').html(data.time_referred);
                $('span.patient_name').html(data.patient_name);
                $('span.patient_age').html(data.age);
                $('span.patient_sex').html(data.sex);
                $('span.patient_status').html(data.civil_status);
                $('span.patient_address').html(patient_address);
                $('span.phic_status').html(data.phic_status);
                $('span.phic_id').html(data.phic_id);
                $('span.case_summary').append(case_summary);
                $('span.reco_summary').html(reco_summary);
                $('span.diagnosis').html(diagnosis);
                $('span.reason').html(reason);
                $('span.referring_md').html(data.md_referring);
                $('span.referring_md_contact').html(data.referring_md_contact);
                $('span.referred_md').html(data.md_referred);
                $('.loading').hide();
            },
            error: function(){
                $('#serverModal').modal();
                $('.loading').hide();
            }

        });
    }

    function getPregnantForm()
    {
        $.ajax({
            url: "{{ url('doctor/referral/data/pregnant') }}/"+id,
            type: "GET",
            success: function(record){
                var print_url = "{{ url('doctor/print/form/') }}/"+id;
                $('.btn-refer-pregnant').attr('href',print_url);
                console.log(record);
                var data = record.form;
                var baby = record.baby;
                var patient_address='';
                var referred_address= '';

                patient_address += (data.patient_brgy) ? data.patient_brgy+', ': '';
                patient_address += (data.patient_muncity) ? data.patient_muncity+', ': '';
                patient_address += (data.patient_province) ? data.patient_province: '';

                referred_address += (data.ff_brgy) ? data.ff_brgy+', ': '';
                referred_address += (data.ff_muncity) ? data.ff_muncity+', ': '';
                referred_address += (data.ff_province) ? data.ff_province: '';

                var woman_major_findings = data.woman_major_findings;
                if (/\n/g.test(woman_major_findings))
                {
                    woman_major_findings = woman_major_findings.replace(/\n/g, '<br>');
                }

                var woman_information_given = data.woman_information_given;
                if (/\n/g.test(woman_information_given))
                {
                    woman_information_given = woman_information_given.replace(/\n/g, '<br>');
                }

                if(baby){
                    var baby_major_findings = baby.baby_major_findings;
                    if (/\n/g.test(baby_major_findings))
                    {
                        baby_major_findings = baby_major_findings.replace(/\n/g, '<br>');
                    }

                    var baby_information_given = baby.baby_information_given;
                    if (/\n/g.test(baby_information_given))
                    {
                        baby_information_given = baby_information_given.replace(/\n/g, '<br>');
                    }
                }

                age = data.woman_age;
                sex = data.sex;
                referring_contact = data.referring_contact;
                referring_md_contact = data.referring_md_contact;
                referred_name = data.referring_facility;

                $('span.record_no').html(data.record_no);
                $('span.referred_date').html(data.referred_date);
                $('span.md_referring').html(data.md_referring);
                $('span.referring_md_contact').html(data.referring_md_contact);
                $('span.referring_facility').html(data.referring_facility);
                $('span.referred_name').html(data.referred_facility);
                $('span.referred_address').html(referred_address);
                $('span.department_name').html(data.department);
                $('span.referring_contact').html(data.referring_contact);
                $('span.facility_brgy').html(data.facility_brgy);
                $('span.facility_muncity').html(data.facility_muncity);
                $('span.facility_province').html(data.facility_province);
                $('span.health_worker').html(data.health_worker);
                $('span.woman_name').html(data.woman_name);
                $('span.woman_age').html(data.woman_age);
                $('span.woman_address').html(patient_address);
                $('span.woman_reason').html(data.woman_reason);
                $('span.woman_major_findings').html(woman_major_findings);
                $('span.woman_before_treatment').html(data.woman_before_treatment);
                $('span.woman_before_given_time').html(data.woman_before_given_time);
                $('span.woman_during_transport').html(data.woman_during_transport);
                $('span.woman_transport_given_time').html(data.woman_transport_given_time);
                $('span.woman_information_given').html(woman_information_given);

                if(baby){

                    $('span.baby_name').html(baby.baby_name);
                    $('span.baby_dob').html(baby.baby_dob);
                    $('span.weight').html(baby.weight);
                    $('span.gestational_age').html(baby.gestational_age);
                    $('span.baby_reason').html(baby.baby_reason);
                    $('span.baby_major_findings').html(baby_major_findings);
                    $('span.baby_last_feed').html(baby.baby_last_feed);
                    $('span.baby_before_treatment').html(baby.baby_before_treatment);
                    $('span.baby_before_given_time').html(baby.baby_before_given_time);
                    $('span.baby_during_transport').html(baby.baby_during_transport);
                    $('span.baby_transport_given_time').html(baby.baby_transport_given_time);
                    $('span.baby_information_given').html(baby_information_given);
                }
                $('.loading').hide();
            },
            error: function(){
                $('#serverModal').modal();
                $('.loading').hide();
            }
        });
    }
</script>