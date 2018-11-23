<script>
    <?php $user=Session::get('auth');?>
    var acceptRef = dbRef.ref('Accept');
    var rejectRef = dbRef.ref('Reject');
    var callRef = dbRef.ref('Call');
    var arriveRef = dbRef.ref('Arrival');
    var admitRef = dbRef.ref('Admit');
    var dischargeRef = dbRef.ref('Discharge');
    var transferRef = dbRef.ref('Transfer');

    acceptRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var action_md = data.action_md;
        var facility_name = data.facility_name;
        var patient_name = data.patient_name;

        var msg = patient_name+'  was accepted by Dr. '+action_md+' of '+facility_name+
            '<br />'+ data.date;
        verify(data.code,'success','Accepted',msg);
    });

    rejectRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var patient_name = data.patient_name;
        var action_md = data.action_md;
        var old_facility = data.old_facility;

        var msg = 'Dr. '+action_md+' of '+old_facility+' recommended to redirect '+patient_name+' to other facility.'+
                '<br />'+ data.date;
        verify(data.code,'error','Redirected',msg);
    });

    callRef.on('child_added',function(snapshot){
        var data = snapshot.val();

        var action_md = data.action_md;
        var facility_calling = data.facility_calling;

        var msg = 'Dr. '+action_md+' of '+facility_calling+' is requesting a call from '+data.referred_name+
            '<br />'+ data.date;
        verify(data.code,'warning','Requesting a Call',msg);
    });

    arriveRef.on('child_added',function(snapshot){

        var data = snapshot.val();
        console.log(data);
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        var msg = patient_name+' arrived at '+current_facility+
            '<br />'+ data.date;
        verify(data.code,'success','Arrived',msg);
    });

    admitRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        var msg = patient_name+' admitted at '+current_facility+
            '<br />'+ data.date;
        verify(data.code,'info','Admitted',msg);
    });

    dischargeRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var current_facility = data.current_facility;

        var msg = patient_name+' discharged from '+current_facility+
            '<br />'+ data.date;
        verify(data.code,'info','Discharged',msg);
    });

    transferRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var date = data.date;
        var patient_name = data.patient_name;
        var action_md = data.action_md;
        var old_facility = data.old_facility;
        var new_facility = data.new_facility;

        var msg = patient_name+'  was referred by Dr. '+action_md+' of '+old_facility+' to '+new_facility+
            '<br />'+ data.date;
        verify(data.code,'warning','Transferred',msg);

    });

    function lobibox(status,title,msg)
    {
        Lobibox.notify(status, {
            delay: false,
            title: title,
            msg: msg,
            img: "{{ url('resources/img/ro7.png') }}",
            sound: false
        });
    }

    function verify(code,status,title,msg)
    {
        $.ajax({
            url: "{{ url('doctor/verify/') }}/"+ code,
            type: "GET",
            success: function(data){
                if(data==1){
                    lobibox(status,title,msg);
                }
            }
        });
    }
</script>