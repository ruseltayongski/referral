<script>
    <?php $user=Session::get('auth');?>
    var myfacility_name = "{{ \App\Facility::find($user->facility_id)->name }}";

    function telemedicineReferPatient(endorseUpward,alreadyRedirected,alreadyFollowup,code,referred_id) {
        const upwardIsCompleted = $('#upward_progress'+code+referred_id).hasClass('completed');
        $(".telemedicine").val(0);
        $("#telemedicine_redirected_code").val(code);
        if(endorseUpward && upwardIsCompleted && !alreadyRedirected && !alreadyFollowup) {
            $("#telemedicineRedirectedFormModal").modal('show');
        }
        else if(alreadyRedirected) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been referred!"
                });
        } 
        else if(alreadyFollowup) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been follow-up!"
                });
        }
        else {
            Lobibox.alert("error",
                {
                    msg: "You cannot refer a patient to an upward level because the receiving doctor has not yet endorsed your patient's referral to an upward level."
                });
        }
    }

    function telemedicineTreatedPatient(alreadyUpward, examinedPatient,alreadyTreated,code,referred_id) {
        const prescriptionIsCompleted = $('#prescribed_progress'+code+referred_id).hasClass('completed');
        const upwardIsCompleted = $('#upward_progress'+code+referred_id).hasClass('completed');
        if((examinedPatient || prescriptionIsCompleted) && !alreadyTreated && (!alreadyUpward || !upwardIsCompleted)) {
            Lobibox.confirm({
                msg: "Do you want to treat this patient?",
                callback: function ($this, type, ev) {
                    if(type === 'yes') {
                        var json = {
                            "_token" : "<?php echo csrf_token(); ?>",
                            "code" : code
                        };
                        var url = "<?php echo asset('api/video/treated') ?>";
                        $.post(url,json,function(result){
                            if(result === 'success') {
                                Lobibox.alert("success",
                                {
                                    msg: "The patient was successfully treated."
                                });
                                $("#treated_progress"+code+referred_id).addClass('completed');
                            }
                        })
                    }
                }
            });
        } else if(alreadyTreated) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been treated!"
                });
        } else if(alreadyUpward || upwardIsCompleted) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been upward!"
                });
        }
        else {
            Lobibox.alert("error",
                {
                    msg: "You can't treat a patient because the patient has not been examined."
                });
        }
    }

    function telemedicineFollowUpPatient(alreadyReferred, alreadyEnded, examinedPatient, alreadyFollowUp, code, referred_id) {
        $("#telemedicine_followup_code").val(code);
        $("#telemedicine_followup_id").val(referred_id);
        $(".telemedicine").val(1);
        const treatedIsCompleted = $('#treated_progress'+code+referred_id).hasClass('completed');
        if(alreadyFollowUp) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been follow up!"
                });
        }
        else if(alreadyReferred) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been referred!"
                });
        }
        else if(alreadyEnded) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been ended!"
                });
        }
        else if(treatedIsCompleted) {
            telemedicine = 1;
            $("#telemedicineFollowupFormModal").modal('show');
        }
        else if(!examinedPatient) {
            Lobibox.alert("error",
                {
                    msg: "You cannot follow up on a patient because it has not yet been examined."
                });
        }
        else {
            $("#followup_header").html("Follow Up Patient");
            telemedicine = 1;
            $("#telemedicineFollowupFormModal").modal('show');
        }
    }

    function consultToOtherFacilities(code) {
        $("#followup_header").html("Consult to other facilities");
        $("#telemedicine_followup_code").val(code);
        $(".telemedicine").val(1);
        telemedicine = 1;
        $("#telemedicineFollowupFormModal").modal('show');
    }

    function telemedicineExamined(tracking_id, code, alreadyAccepted, action_md, referring_md, activity_id, form_tpe) {
        if(alreadyAccepted || $("#accepted_progress"+code+activity_id).hasClass("completed")) {
            var url = "<?php echo asset('api/video/call'); ?>";
            var json = {
                "_token" : "<?php echo csrf_token(); ?>",
                "tracking_id" : tracking_id,
                "code" : code,
                "action_md" : action_md ? action_md : $("#accepted_progress"+code+activity_id).attr("data-actionmd"),
                "referring_md" : referring_md,
                "trigger_by" : "{{ $user->id }}",
                "form_type" : form_tpe,
                "activity_id" : activity_id
            };
            $.post(url,json,function(){

            });
            var windowName = 'NewWindow'; // Name of the new window
            var windowFeatures = 'width=600,height=400'; // Features for the new window (size, position, etc.)
            var newWindow = window.open("{{ asset('doctor/telemedicine?id=') }}"+tracking_id+"&code="+code+"&form_type="+form_tpe+"&referring_md=yes", windowName, windowFeatures);
            if (newWindow && newWindow.outerWidth) {
                // If the window was successfully opened, attempt to maximize it
                newWindow.moveTo(0, 0);
                newWindow.resizeTo(screen.availWidth, screen.availHeight);
            }
        } else if(!alreadyAccepted) {
            Lobibox.alert("error",
            {
                msg: "You cannot follow up on a patient because it has not yet been examined."
            });
        }
    }

    function telemedicinePrescription(track_id, activity_id, referred_code, referred_id) {
        const prescriptionIsCompleted = $('#prescribed_progress'+referred_code+referred_id).hasClass('completed');
        const url = "{{ asset('doctor/print/prescription') }}";
        if(activity_id) {
            window.open(`${url}/${track_id}/${activity_id}`);
        } else if(prescriptionIsCompleted) {
            window.open(`${url}/${track_id}/${referred_id}?prescription_new=true`);
        }
        else {
            Lobibox.alert("error",
            {
                msg: "No prescription has been created by the referred doctor"
            });
        }
    }

    function telemedicineEndPatient(alreadyTreated, alreadyReferred, alreadyFollowUp, alreadyEnd, code, referred_id) {
        const endIsCompleted = $('#end_progress'+code+referred_id).hasClass('completed');
        if(alreadyTreated && !alreadyReferred && !alreadyFollowUp && (!alreadyEnd || !endIsCompleted)) {
            Lobibox.confirm({
                msg: "Do you want to cycle end this patient?",
                callback: function ($this, type, ev) {
                    if(type === 'yes') {
                        var json = {
                            "_token" : "<?php echo csrf_token(); ?>",
                            "code" : code
                        };
                        var url = "<?php echo asset('api/video/end') ?>";
                        $.post(url,json,function(result){
                            if(result === 'success') {
                                Lobibox.alert("success",
                                    {
                                        msg: "The patient was successfully end cycle."
                                    });
                                $("#end_progress"+code+referred_id).addClass('completed');
                            }
                        })
                    }
                }
            });
        } else if(alreadyReferred) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been referred!"
                });
        } else if(alreadyFollowUp) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been follow up!"
                });
        }
        else if(alreadyEnd || endIsCompleted) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been end cycle!"
                });
        }
        else {
            Lobibox.alert("error",
                {
                    msg: "You can't end cycle a patient because the patient has not been examined."
                });
        }
    }

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
    $('body').on('click','.btn-redirected',function(){
        console.log("redirected!");
        $("#redirected_code").val($(this).data('activity_code'));
    });
    $('body').on('submit','#redirectedForm',function(e){
        $("#redirected_submit").attr("disabled",true);
    });
    $('body').on('submit','#telemedicineRedirectedForm',function(e){
        $("#redirected_submit_telemedicine").attr("disabled",true);
    });
    $('body').on('submit','#telemedicineFollowupForm',function(e){
        $("#followup_submit_telemedicine").attr("disabled",true);
    });
</script>

{{--show and hide activity--}}
<script>
    $('ul.timeline li').not(":first-child").not(":nth-child(2)").hide();
    $('.btn-activity').on('click',function(){
        var item = $(this).parent().parent().parent().parent().parent().parent().find('li');
        item.not(":first-child").not(":nth-child(2)").toggle();
    });
//    $('ul.timeline li:first-child').on('click',function(){
//        var item = $(this).parent().find('li');
//        item.not(":first-child").not(":nth-child(2)").toggle();
//    });
</script>

@include('script.view_form')

{{--SEEN BY--}}
<script>
    $('body').on('click','.btn-seen',function(){
        var de = '<hr />\n' +
            '                    LOADING...\n' +
            '                    <br />\n' +
            '                    <br />';
        $('#seenBy_section').html(de);
        var id = $(this).data('id');
        var seenUrl = "{{ url('doctor/referral/seenBy/list/') }}/"+id;
        $.ajax({
            url: seenUrl,
            type: "GET",
            success: function(data){
                var content = '<div class="list-group">';
                jQuery.each(data, function(i,val){
                    content += '<a href="#" class="list-group-item clearfix">\n' +
                        '<strong class="text-green">Dr. '+val.user_md+'</strong>\n' +
                        '<br />\n' +
                        '<small>\n' +
                        'Facility: <b>'+val.facility_name+'</b>\n' +
                        '</small>\n' +
                        '<br />\n' +
                        '<small>\n' +
                        'Seen: <b>'+val.date_seen+'</b>\n' +
                        '</small>\n' +
                        '<br />\n' +
                        '<small>\n' +
                        'Contact: <b>'+val.contact+'</b>\n' +
                        '</small>\n' +
                        '</a>';
                });
                content += '</div>';
                setTimeout(function () {
                    $('#seenBy_section').html(content);
                },500);
            },
            error: function () {
                $('#serverModal').modal('show');
            }
        });
    });

    $('body').on('click','.btn-caller',function(){
        var de = '<hr />\n' +
            '                    LOADING...\n' +
            '                    <br />\n' +
            '                    <br />';
        $('#callerBy_section').html(de);
        var id = $(this).data('id');
        var callerUrl = "{{ url('doctor/referral/callerBy/list/') }}/"+id;
        $.ajax({
            url: callerUrl,
            type: "GET",
            success: function(data){
                console.log(id);
                var content = '<div class="list-group">';

                jQuery.each(data, function(i,val){
                    content += '<a href="#" class="list-group-item clearfix">\n' +
                        '<span class="title-info">'+val.user_md+'</span>\n' +
                        '<br />\n' +
                        '<small class="text-primary">\n' +
                        'Time: '+val.date_call+'\n' +
                        '</small>\n' +
                        '<br />\n' +
                        '<small class="text-success">\n' +
                        'Contact: '+val.contact+'\n' +
                        '</small>\n' +
                        '</a>';
                });
                content += '</div>';
                setTimeout(function () {
                    $('#callerBy_section').html(content);
                },500);
            },
            error: function () {
                $('#serverModal').modal('show');
            }
        });
    });
</script>

{{--CANCEL REFERRAL--}}
<script>
    $('body').on('click','.btn-cancel',function() {
        var id = $(this).data('id');
        var user = $(this).data('user');
        if(user === 'admin')
            $('#cancelAdmin').val(user);
        var url = "{{ url('doctor/referred/cancel') }}/"+id;
        $("#cancelReferralForm").attr('action',url);
    });
    $('#cancelReferralForm').on('submit', function(e) {
        $("#btn-cancel-submit").attr('disabled', true);
    });
    $('#undoCancelForm').on('submit', function(e) {
        $("#btn-undocancel-submit").attr('disabled', true);
    });
</script>