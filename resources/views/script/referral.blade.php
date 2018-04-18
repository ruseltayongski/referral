<script>
    <?php $user = Session::get('auth');?>
    var code,
        item,
        status,
        patient_name,
        facility,
        referred_from,
        referred_name,
        type,
        form_id,
        age,
        sex,
        form_type,
        referring_name,
        referring_contact,
        referring_md_contact;
    var my_facility_name = "{{ \App\Facility::find($user->facility_id)->name }}";
</script>

<script>
    var action_md = "{{ $user->fname }} {{ $user->mname }} {{ $user->lname }}";
    var content = '';
    connRef.child(myfacility).on('child_added',function(snapshot){
        var data = snapshot.val();
        var type = data.form_type;
        type = (type=='#normalFormModal') ? 'normal-section':'pregnant-section';
        var referral_type = (type=='normal-section') ? 'normal':'pregnant';
        $('.count_referral').html(count_referral);
        $('.alert-section').empty();
        content = '<li>' +
            '    <i class="fa fa-ambulance bg-blue-active"></i>\n' +
            '    <div class="timeline-item '+type+'" id="item-'+data.tracking_id+'">\n' +
            '        <span class="time"><i class="icon fa fa-ambulance"></i> <span class="date_activity">'+data.date+'</span></span>\n' +
            '        <h3 class="timeline-header no-border"><a href="#" class="patient_name">'+data.name+'</a> <small class="status">[ '+data.sex+', '+data.age+' ]</small> was referred to your facility by <span class="text-warning">Dr. '+data.referring_md+'</span> of <span class="facility">'+data.referring_name+'</span></h3>\n' +
            '        <div class="timeline-footer">\n' +
            '            <a class="btn btn-warning btn-xs btn-refer" href="'+data.form_type+'"\n' +
            '               data-toggle="modal"\n' +
            '               data-code="'+data.patient_code+'"\n' +
            '               data-item="#item-'+data.tracking_id+'"\n' +
            '               data-status="referred"\n' +
            '               data-type="'+referral_type+'"\n' +
            '               data-id="'+data.tracking_id+'"\n' +
            '               data-referred_from="'+data.referred_from+'"\n' +
            '               data-backdrop="static">\n' +
            '                <i class="fa fa-folder"></i> View Form\n' +
            '            </a>\n' +
            '            <a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: '+data.patient_code+'</a>\n' +
            '        </div>\n' +
            '    </div>\n' +
            '</li>';

        $('.timeline').prepend(content);
    });
</script>

{{--Normal and Pregnant Form--}}
<script>

$('body').on('click','.btn-refer',function () {
    $('.loading').show();
    code = $(this).data('code');
    item = $(this).data('item');
    status = $(this).data('status');
    type = $(this).data('type');
    form_id = $(this).data('id');
    referred_from = $(this).data('referred_from');

    console.log(code);
    patient_name = $(item).find('.patient_name').html();
    facility = $(item).find('.facility').html();
    var count_referral = $('.count_referral').html();

    if(status=='referred'){
        seenMessage();
    }else{
        setTimeout(function () {
            $('.loading').hide();
        },500)
    }
    if(type=='normal'){
        form_type = '#normalFormModal';
        getNormalForm();
    }else if(type=='pregnant'){
        form_type = '#pregnantFormModal';
        getPregnantForm();
    }
});
$('body').on('submit','#acceptForm',function(e){
    e.preventDefault();
    $('.loading').show();
    var tracking_id = form_id;
    var reason = $('.accept_remarks').val();
    $(this).ajaxSubmit({
        url: "{{ url('doctor/referral/accept/') }}/" + tracking_id,
        type: 'POST',
        success: function (tracking_id) {
            if(tracking_id=='denied')
            {
                window.location.reload(false);
            }else{
                var acceptRef = dbRef.ref('Accept');
                acceptRef.push({
                    date: getDateReferred(),
                    item: tracking_id,
                    facility_name: "{{ \App\Facility::find($user->facility_id)->name }}",
                    action_md: action_md,
                    patient_name: patient_name,
                    code: code,
                    reason: reason
                });
                acceptRef.on('child_added',function(data){
                    setTimeout(function(){
                        acceptRef.child(data.key).remove();
                        window.location.reload(false);
                    },500);
                });
            }

        }
    });
});

$('body').on('submit','#referForm',function(e){
    e.preventDefault();
    $('.loading').show();
    referred_to = $('.new_facility').val();
    var new_facility = $('.new_facility').find(':selected').html();
    var old_facility = "{{ \App\Facility::find($user->facility_id)->name }}";
    var reason = $('.reject_reason').val();
    referring_name = old_facility;
    $(this).ajaxSubmit({
        url: "{{ url('doctor/referral/reject/') }}/"+form_id,
        type: 'POST',
        success: function(tracking_id){
            console.log(tracking_id);
            if(tracking_id=='denied')
            {
                window.location.reload(false);
            }else{
                var rejectRef = dbRef.ref('Reject');
                rejectRef.push({
                    date: getDateReferred(),
                    item: form_id,
                    new_facility: new_facility,
                    old_facility: old_facility,
                    action_md: action_md,
                    patient_name: patient_name,
                    code: code,
                    reason: reason
                });

                rejectRef.on('child_added',function(data){
                    setTimeout(function(){
                        rejectRef.child(data.key).remove();
                    },500);
                });

                var connRef = dbRef.ref('Referral');
                var refer_data = {
                    referring_name: referring_name,
                    patient_code: code,
                    name: patient_name,
                    age: age,
                    sex: sex,
                    date: getDateReferred(),
                    form_type: form_type,
                    tracking_id: tracking_id,
                    referring_md: action_md
                };
                console.log(refer_data);
                connRef.child(referred_to).push(refer_data);

            var data = {
                "to": "/topics/ReferralSystem",
                "data": {
                    "subject": "New Referral",
                    "date": getDateReferred(),
                    "body": patient_name+" was referred to your facility from "+referring_name+"!"
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

                connRef.on('child_added',function(data){
                    setTimeout(function(){
                        connRef.child(data.key).remove();
                        window.location.reload(false);
                    },500);
                });
            }

        },
        error: function(){
            $('#serverModal').modal();
        }
    });
});


function getDateReferred()
{
    var date = new Date();
    var months=["Jan","Feb","Mar","Apr","May","Jun","Jul",
        "Aug","Sep","Oct","Nov","Dec"];

    var day = (date.getDate()<10) ? "0"+date.getDate(): date.getDate();
    var val = months[date.getMonth()]+" "+day+", "+date.getFullYear();
    var hours = (date.getHours()<10) ? "0"+date.getHours(): date.getHours();
    var min = (date.getMinutes()<10) ? "0"+date.getMinutes():date.getMinutes();
    var mid = 'AM';
    if(hours==0){
        hours=12;
    }else if(hours>12){
        hours = hours - 12;
        mid = 'PM';
    }

    val +=" "+hours+":"+min+" "+mid;
    return val;
}

function seenMessage()
{
    $(item).removeClass('pregnant-section normal-section').addClass('read-section');
    $(item).find('.icon').removeClass('fa-ambulance').addClass('fa-eye');
    var curr_date = "{{ date('M d, Y h:i A') }}";
    $(item).find('.date_activity').html(curr_date);

    $.ajax({
        url: "{{ url('doctor/referral/seen') }}/"+form_id,
        type: "GET",
        success: function(){
            var seenRef = dbRef.ref('Seen');
            seenRef.push({
                date: getDateReferred(),
                item: form_id,
                code: code
            });

            seenRef.on('child_added',function(data){
                setTimeout(function(){
                    seenRef.child(data.key).remove();
                    $('.loading').hide();
                },500);
            });
        },
        error: function(){
            $('#serverModal').modal();
        }
    });
}

function getNormalForm()
{
    $.ajax({
        url: "{{ url('doctor/referral/data/normal') }}/"+code,
        type: "GET",
        success: function(data){

            patient_name = data.patient_name;
            referring_name = data.referring_name;

            var address='';
            var patient_address='';
            address += (data.facility_brgy) ? data.facility_brgy+', ': '';
            address += (data.facility_muncity) ? data.facility_muncity+', ': '';
            address += (data.facility_province) ? data.facility_province: '';

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
            $('span.referring_contact').html(data.referring_contact);
            $('span.referring_address').html(address);
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
        },
        error: function(){
            $('#serverModal').modal();
        }
    });
}

function getPregnantForm()
{
    $.ajax({
        url: "{{ url('doctor/referral/data/pregnant') }}/"+code,
        type: "GET",
        success: function(record){
            var data = record.form;
            var baby = record.baby;
            var patient_address='';
            patient_address += (data.patient_brgy) ? data.patient_brgy+', ': '';
            patient_address += (data.patient_muncity) ? data.patient_muncity+', ': '';
            patient_address += (data.patient_province) ? data.patient_province: '';

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

        },
        error: function(){
            $('#serverModal').modal();
        }
    });
}
</script>

{{--Firebase On Child Added in Seen, Reject and Accept--}}
<script>
    var accepts = dbRef.ref('Accept');
    var rejects = dbRef.ref('Reject');
    var seen = dbRef.ref('Seen');
    accepts.on('child_added',function(snapshot){
        var data = snapshot.val();
        var form = $('#item-'+data.item).parent();
        var content = '<i class="fa fa-user-plus bg-olive"></i>\n' +
            '<div class="timeline-item">\n' +
            '    <span class="time"><i class="fa fa-user-plus"></i> '+data.date+'</span>\n' +
            '    <h3 class="timeline-header no-border"><a href="#">'+data.patient_name+'</a> was ACCEPTED by <span class="text-success">Dr. '+data.action_md+'</span></h3>\n' +
            '\n' +
            '</div>';
        form.html(content);
    });

    rejects.on('child_added',function(snapshot){
        var data = snapshot.val();
        var form = $('#item-'+data.item).parent();
        var content = '<i class="fa fa-user-times bg-maroon"></i>\n' +
            '<div class="timeline-item">\n' +
            '    <span class="time"><i class="fa fa-calendar"></i> '+data.date+'</span>\n' +
            '    <h3 class="timeline-header no-border"><a href="#">'+data.patient_name+'</a> was REDIRECTED by <span class="text-danger">Dr. '+data.action_md+'</span></h3>\n' +
            '\n' +
            '</div>';
        form.html(content);
    });

    seen.on('child_added',function(snapshot){
        var data = snapshot.val();
        console.log(data);
        var item = '#item-'+data.item;
        var date = data.date;

        $(item).removeClass('pregnant-section normal-section').addClass('read-section');
        $(item).find('.icon').removeClass('fa-ambulance').addClass('fa-eye');
        $(item).find('.date_activity').html(date);
    });
</script>

{{--when call button is click--}}
<script>
$('body').on('click','.btn-call',function(){
    $('.referring_contact').html(referring_contact);
    $('.referring_md_contact').html(referring_md_contact);


    $.ajax({
        url: "{{ url('doctor/referral/call/') }}/" + form_id,
        type: 'GET',
        success: function(date) {
            var callRef = dbRef.ref('Call');
            var call_data = {
                date: date, //can be change to returne date
                facility_calling: my_facility_name,
                action_md: action_md,
                tracking_id: form_id,
                code: code
            };
            callRef.push(call_data);
            callRef.on('child_added',function(data){
                setTimeout(function(){
                    callRef.child(data.key).remove();
                    $('.loading').hide();
                },300);
            });
        },
        error: function(){
            $('#serverModal').modal();
        }
    });
});
</script>