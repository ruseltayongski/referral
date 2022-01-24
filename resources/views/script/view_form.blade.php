{{--Normal and Pregnant Form--}}
<script>
    $('body').on('click','.view_form',function () {
        code = $(this).data('code');
        console.log(code);
        item = $(this).data('item');
        status = $(this).data('status');
        type = $(this).data('type');
        form_id = $(this).data('id');
        referred_from = $(this).data('referred_from');
        patient_name = $(this).data('patient_name');
        facility = $(item).find('.facility').html();
        var referral_status = $(this).data('referral_status');

        if(referral_status == 'referred' || referral_status == 'redirected') {
            var seenUrl = "{{ url('doctor/referral/seenBy_save/') }}/"+form_id+"/"+code;
            $.ajax({
                url: seenUrl,
                type: "GET",
                success: function(result){

                },
                error: function(){
                    console.log('error');
                }
            });
        }

        if(type == 'normal') {
            form_type = '#referralForm';
            var form_url = "{{ url('doctor/referral/data/normal') }}/"+form_id+"/"+referral_status+"/"+type;
            $(".referral_body").html(loading);
            $.ajax({
                url: form_url,
                type: "GET",
                success: function(data) {
                    console.log(form_url);
                    setTimeout(function(){
                        $(".referral_body").html(data);
                    },300);
                },
                error: function(){
                    $('#serverModal').modal();
                }
            });
        }
        else if(type == 'pregnant') {
            form_type = '#referralForm';
            $(".referral_body").html(loading);
            $.ajax({
                url: "{{ url('doctor/referral/data/pregnant') }}/"+form_id+"/"+referral_status+"/"+type,
                type: "GET",
                success: function(request){
                    setTimeout(function() {
                        $(".referral_body").html(request);
                    },300);
                    /*var data = record.form;
                    patient_name = data.woman_name;
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

                    $('span.covid_number').html(data.covid_number);
                    $('span.clinical_status').html(data.refer_clinical_status);
                    $('span.surveillance_category').html(data.refer_sur_category);

                    var print_url = "{{ url('doctor/print/form/') }}/"+data.tracking_id;
                    $('.btn-refer-pregnant').attr('href',print_url);
                    console.log(data);

                    if(baby)
                    {
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
                    }*/

                },
                error: function(){
                    $('#serverModal').modal();
                }
            });

        }
    });

    $('body').on('submit','#acceptForm',function(e) {
        e.preventDefault();
        console.log(code);
        $('.loading').show();
        var tracking_id = form_id;
        var reason = $('.accept_remarks').val();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/referral/accept/') }}/" + tracking_id,
            type: 'POST',
            success: function (tracking_id) {
                if(tracking_id=='denied') {
                    window.location.reload(false);
                } else {
                    console.log(patient_name);
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

    $('body').on('submit','#rejectForm',function(e) {
        e.preventDefault();
        $('.loading').show();
        referred_to = $('.new_facility').val();
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
                        activity_id: tracking_id,
                        old_facility: old_facility,
                        action_md: action_md,
                        patient_name: patient_name,
                        code: code,
                        reason: reason,
                        referred_from: referred_from
                    });

                    rejectRef.on('child_added',function(data){
                        setTimeout(function(){
                            rejectRef.child(data.key).remove();
                            window.location.reload(false);
                        },500);
                    });
                }

            },
            error: function() {
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

</script>