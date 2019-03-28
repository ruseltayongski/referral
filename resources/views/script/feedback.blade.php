<script>
    //var objDiv = document.getElementById("feedback-181105-023-134025");
    var code = 0;
    var feedbackRef = dbRef.ref('Feedback');

    $('.btn-feedback').on('click',function () {
        code = $(this).data('code');
        $('.feedback_code').html(code);
        $('.direct-chat-messages').attr('id',code);
        $("#"+code).html("Loading...");
        $("#"+code).load("{{ url('doctor/feedback/') }}/"+code);
        $("#current_code").val(code);
    });

    $('#feedbackModal').on('shown.bs.modal', function (e) {
        var objDiv = document.getElementById(code);

        setTimeout(function () {
            objDiv.scrollTop = objDiv.scrollHeight;
        },500);
        $('#message').focus();
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
        $('#message').focus();
    }

    $('#feedbackForm').submit(function (e) {
        e.preventDefault();
        var msg = $("#message").val()
        $("#message").val('').focus();
        $.ajax({
            url: "{{ url('doctor/feedback') }}",
            type: 'post',
            data: {
                _token : "{{ csrf_token() }}",
                message: msg,
                code : code
            },
            success: function(data) {
                feedbackRef.push({
                    id: data,
                    code: code
                });
                feedbackRef.on('child_added',function(data){
                    setTimeout(function(){
                        feedbackRef.child(data.key).remove();
                    },200);
                });

                var objDiv = document.getElementById(code);
                objDiv.scrollTop = objDiv.scrollHeight;
            }
        });
    });

    feedbackRef.on('child_added',function(snapshot){
        var data = snapshot.val();
        var c = data.code;
        $("#"+c).append(data.content);
        $.ajax({
            url: "{{ url('doctor/feedback/reply/') }}/"+data.id,
            type: 'get',
            success: function(content) {
                $("#"+c).append(content);
                var objDiv = document.getElementById(code);
                objDiv.scrollTop = objDiv.scrollHeight;
            }
        });
    });
</script>