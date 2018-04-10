<script>
    <?php $user=Session::get('auth');?>
    var myfacility = "{{ $user->facility_id }}";
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
        var date = data.date;
        item = $(item).find('#item-'+data.item);

        $(item).removeClass('pregnant-section normal-section').addClass('read-section');
        $(item).find('.icon').removeClass('fa-ambulance').addClass('fa-eye');
        $(item).find('.date_activity').html(date);
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
            '        <a href="#">\n' +
            '            <div class="timeline-header no-border">\n' +
            '                '+patient_name+'  was accepted by <span class="text-success">Dr. '+action_md+'</span> of <span class="facility">'+facility_name+'</span>.</span>\n' +
            '            <br />' +
            '            <span class="text-success">Remarks: '+data.reason+'</span>'+
            '            </div>\n' +
            '        </a>\n' +
            '\n' +
            '    </div>\n' +
            '</li>';
        $('.code-'+data.code).append(acceptContent);
    });

    rejectRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var action_md = data.action_md;
        var old_facility = data.old_facility;
        var new_facility = data.new_facility;

        rejectContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-user-times"></i> '+date+'</span>\n' +
            '    <a href="#">\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+'  was rejected by <span class="text-danger">Dr. '+action_md+'</span> of <span class="facility">'+old_facility+'</span> and referred to <span class="facility">'+new_facility+'.</span>\n' +
            '            <br />' +
            '            <span class="text-danger">Remarks: '+data.reason+'</span>'+
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code).append(rejectContent);
    });

    var callContent = '';
    callRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var action_md = data.action_md;
        var facility_calling = data.facility_calling;
        console.log(data.code);
        callContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-phone"></i> '+date+'</span>\n' +
            '    <a href="#">\n' +
            '        <div class="timeline-header no-border">\n' +
            '            <span class="text-info">Dr. '+action_md+'</span> of <span class="facility">'+facility_calling+'</span> called the referring facility.</span>\n' +
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code).append(callContent);

    });

    var arriveContent = '';
    arriveRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        arriveContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-wheelchair"></i> '+date+'</span>\n' +
            '    <a href="#">\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+' arrived at <span class="facility">'+current_facility+'</span>.\n' +
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code).append(arriveContent);

    });

    var admitContent = '';
    admitRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        admitContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-wheelchair"></i> '+date+'</span>\n' +
            '    <a href="#">\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+' admitted at <span class="facility">'+current_facility+'</span>.\n' +
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code).append(admitContent);

    });

    var dischargeContent = '';
    dischargeRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        dischargeContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-wheelchair"></i> '+date+'</span>\n' +
            '    <a href="#">\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+' discharged from <span class="facility">'+current_facility+'</span>.\n' +
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code).append(dischargeContent);

    });

    var transferContent = '';
    transferRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var action_md = data.action_md;
        var old_facility = data.old_facility;
        var new_facility = data.new_facility;

        transferContent = '<li><div class="timeline-item read-section">\n' +
            '    <span class="time"><i class="fa fa-ambulance"></i> '+date+'</span>\n' +
            '    <a href="#">\n' +
            '        <div class="timeline-header no-border">\n' +
            '            '+patient_name+'  was referred by <span class="text-success">Dr. '+action_md+'</span> of <span class="facility">'+old_facility+'</span> to <span class="facility">'+new_facility+'.</span>\n' +
            '            <br />' +
            '            <span class="text-info">Remarks: '+data.reason+'</span>'+
            '        </div>\n' +
            '    </a>\n' +
            '\n' +
            '</div></li>';
        $('.code-'+data.code).append(transferContent);
    });
</script>