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