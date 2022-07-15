<script>
    <?php $user=Session::get('auth');?>
    var myfacility_name = "{{ \App\Facility::find($user->facility_id)->name }}";
    /*var referralRef = dbRef.ref('Referral');
    var seenRef = dbRef.ref('Seen');
    var acceptRef = dbRef.ref('Accept');
    var rejectRef = dbRef.ref('Reject');
    var callRef = dbRef.ref('Call');
    var arriveRef = dbRef.ref('Arrival');
    var admitRef = dbRef.ref('Admit');
    var dischargeRef = dbRef.ref('Discharge');
    var transferRef = dbRef.ref('Transfer');
    var redirectedRef = dbRef.ref('Redirected');

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
    });*/

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
    /*acceptRef.on('child_added',function(snapshot){
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
    });

    rejectRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var action_md = data.action_md;
        var facility = data.old_facility;
        var reason = data.reason;
        var code = data.code;
        console.log("receive recommend to redirect");
        $(".prepend_from_firebase"+data.code).prepend('' +
            '<tr>\n' +
            '                                                    <td>'+date+'</td>\n' +
            '                                                    <td>\n' +
            '                                                        <span class="txtDoctor">'+action_md+'</span> of <span class="txtHospital">'+facility+'</span> recommended to redirect <span class="txtPatient">'+patient_name+'</span> to other facility.\n' +
            '                                                        <span class="remarks">Remarks: '+reason+'</span>\n' +
            '                                                        <br>\n' +
            '                                                                                                                    <button class="btn btn-success btn-xs btn-redirected" data-toggle="modal" data-target="#redirectedFormModal" data-activity_code="'+code+'">\n' +
            '                                                                <i class="fa fa-ambulance"></i> Redirect to other facility\n' +
            '                                                            </button>\n' +
            '                                                                                                            </td>\n' +
            '                                                </tr>');
    });*/

    /*var callContent = '';
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
    });
*/
    /*var arriveContent = '';
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
    });*/

    /*var admitContent = '';
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
    });*/
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
    $('body').on('click','.btn-cancel',function(){
        var id = $(this).data('id');
        var url = "{{ url('doctor/referred/cancel') }}/"+id;
        $("#cancelReferralForm").attr('action',url);
    });
</script>
{{--<div class="list-group">--}}
{{--<a href="#" class="list-group-item clearfix">--}}
{{--<span class="title-info">Dr. Jimmy</span>--}}
{{--<br />--}}
{{--<small class="text-primary">--}}
{{--Seen: May 15, 2018 03:15 PM--}}
{{--</small>--}}
{{--<br />--}}
{{--<small class="text-success">--}}
{{--Contact: 0916-207-2427--}}
{{--</small>--}}
{{--</a>--}}

{{--</div>--}}