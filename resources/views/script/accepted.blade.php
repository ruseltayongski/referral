<script>
    <?php $user = Session::get('auth'); ?>

    var arriveRef = dbRef.ref('Arrival');
    var admitRef = dbRef.ref('Admit');
    var dischargetRef = dbRef.ref('Discharge');
    var transferRef = dbRef.ref('Transfer');

    //initializes variables
    var current_facility, code, patient_name, track_id;
    current_facility = "{{ \App\Facility::find($user->facility_id)->name }}";

    $('body').on('click','.btn-action',function(){
        code = $(this).data('code');
        patient_name = $(this).data('patient_name');
        track_id = $(this).data('track_id');
    });

    $('#arriveForm').on('submit',function(e){
        $('.loading').show();
         e.preventDefault();
         var remarks = $(this).find('.remarks').val();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/referral/arrive/') }}/" + track_id,
            type: 'POST',
            success: function(date){
                var arrive_data = {
                    code: code,
                    patient_name: patient_name,
                    track_id: track_id,
                    date: date,
                    current_facility: current_facility,
                    remarks: remarks
                };
                arriveRef.push(arrive_data);
                arriveRef.on('child_added',function(data){
                    setTimeout(function(){
                        arriveRef.child(data.key).remove();
                        var msg = 'Patient arrived to your facility';
                        $('.info').removeClass('hide').find('.message').html(msg);
                        $('#arriveModal').modal('hide');
                        $('.loading').hide();
                    },500);
                });
            },
            error: function(){
                $('#serverModal').modal();
            }
        });
    });

    $('#admitForm').on('submit',function(e){
        $('.loading').show();
        e.preventDefault();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/referral/admit/') }}/" + track_id,
            type: 'POST',
            success: function(date){
                var arrive_data = {
                    code: code,
                    patient_name: patient_name,
                    track_id: track_id,
                    date: date,
                    current_facility: current_facility
                };
                admitRef.push(arrive_data);
                admitRef.on('child_added',function(data){
                    setTimeout(function(){
                        admitRef.child(data.key).remove();
                        var msg = 'Patient admitted to your facility';
                        $('.info').removeClass('hide').find('.message').html(msg);
                        $('#admitModal').modal('hide');
                        $('.loading').hide();
                    },500);
                });
            },
            error: function(){
                $('#serverModal').modal();
            }
        });
    });


    $('#dischargeForm').on('submit',function(e){
        $('.loading').show();
        e.preventDefault();
        var remarks = $(this).find('.remarks').val();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/referral/discharge/') }}/" + track_id,
            type: 'POST',
            success: function(date){
                var msg = 'Patient admitted to your facility';
                $('.info').removeClass('hide').find('.message').html(msg);
                var arrive_data = {
                    code: code,
                    patient_name: patient_name,
                    track_id: track_id,
                    date: date,
                    current_facility: current_facility,
                    remarks: remarks
                };
                dischargetRef.push(arrive_data);
                dischargetRef.on('child_added',function(data){
                    setTimeout(function(){
                        dischargetRef.child(data.key).remove();
                        window.location.reload(false);
                    },500);
                });
            },
            error: function(){
                $('#serverModal').modal();
            }
        });
    });



    $('body').on('click','.btn-transfer',function(){
        $('.loading').hide();
    });

    $('body').on('submit','#referForm',function(e){
        $('.loading').show();
        e.preventDefault();
        referred_to = $('.new_facility').val();
        var new_facility = $('.new_facility').find(':selected').html();
        var reason = $('.reject_reason').val();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/referral/transfer/') }}/"+track_id,
            type: 'POST',
            success: function(data){
                transferRef.push({
                    date: data.date,
                    item: track_id,
                    new_facility: new_facility,
                    old_facility: current_facility,
                    action_md: data.action_md,
                    patient_name: patient_name,
                    code: code,
                    reason: reason
                });

                transferRef.on('child_added',function(data){
                    setTimeout(function(){
                        transferRef.child(data.key).remove();
                    },500);
                });

                var connRef = dbRef.ref('Referral');
                var refer_data = {
                    referring_name: current_facility,
                    patient_code: code,
                    name: patient_name,
                    age: data.age,
                    sex: data.sex,
                    date: data.date,
                    form_type: data.form_type,
                    tracking_id: data.track_id,
                    referring_md: data.action_md
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
                    "body": patient_name+" was referred to your facility from "+current_facility+"!"
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