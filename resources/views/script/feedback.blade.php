<script>
    //var objDiv = document.getElementById("feedback-181105-023-134025");
    <?php $user = \Illuminate\Support\Facades\Session::get('auth'); ?>
    var code = 0;
    var feedbackRef = dbRef.ref('Feedback');
    var last_id = 0;

    $('.btn-feedback').on('click',function () {
        code = $(this).data('code');
        $('.feedback_code').html(code);
        $('.direct-chat-messages').attr('id',code);
        $('#message').addClass("message input-"+code+"-{{ $user->id }}");
        $("#"+code).html("Loading...");
        $("#"+code).load("{{ url('doctor/feedback/') }}/"+code);
        $("#current_code").val(code);
    });

    $('#feedbackModal').on('shown.bs.modal', function (e) {
        var objDiv = document.getElementById(code);

        setTimeout(function () {
            objDiv.scrollTop = objDiv.scrollHeight;
        },500);
    });

    function reloadMessage() {
        $("#message").val('').focus();
        $("#"+code).html("Loading...");
        $("#"+code).load("{{ url('doctor/feedback/') }}/"+code);
        $("#current_code").val(code);

        var objDiv = document.getElementById(code);
        setTimeout(function () {
            objDiv.scrollTop = objDiv.scrollHeight;
        },500);
    }

    $('#feedbackForm').submit(function (e) {
        e.preventDefault();
        var msg = $("#message").val();
        $("#message").val('').attr('placeholder','Sending...');
        $.ajax({
            url: "{{ url('doctor/feedback') }}",
            type: 'post',
            data: {
                _token : "{{ csrf_token() }}",
                message: msg,
                code : code
            },
            success: function(data) {
                //console.log(data);
                $(".reco-body").append(data);
                feedbackRef.push({
                    id: data,
                    code: code,
                    msg: msg,
                    user_id: "{{ $user->id }}"
                });
                feedbackRef.on('child_added',function(data){
                    setTimeout(function(){
                        feedbackRef.child(data.key).remove();
                    },200);
                });

                var objDiv = document.getElementById(code);
                objDiv.scrollTop = objDiv.scrollHeight;
                $("#message").val('').attr('placeholder','Type Message...');
            }
        });
    });

    $('.direct-chat-messages').scroll(function() {
        var current_top_element = $('.direct-chat-messages').children().first();
        var previous_height = 0;


        var pos = $('.direct-chat-messages').scrollTop();
        var objDiv = document.getElementById(code);


        if (pos == 0) {
            $.ajax({
                url: "{{ url('doctor/feedback/load/') }}/"+code,
                type: "get",
                success: function (data) {
                    if(data!=0){
                        $("#"+code).prepend(data);
                        current_top_element.prevAll().each(function() {
                            previous_height += $(this).outerHeight();
                        });

                        objDiv.scrollTop = previous_height;
                    }
                }
            })
        }
    });

    feedbackRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var c = data.code;

        $("#"+c).append(data.content);
        var url = "<?php echo asset('referral/doctor/feedback/reply')."/"; ?>"+data.id;
        $.ajax({
            url: url,
            type: 'get',
            success: function(content) {
                $("#"+c).append(content);

                if(data.user_id == "{{ $user->id }}"){
                    var input_id = ".input-"+data.code+"-"+data.user_id;
                    $(input_id).val('');
                    var objDiv = document.getElementById(code);
                    objDiv.scrollTop = objDiv.scrollHeight;
                }
            }
        });
    });
</script>