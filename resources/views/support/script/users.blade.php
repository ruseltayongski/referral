<?php $user = Session::get('auth'); ?>
<script>    
$('#department_select').on('change', function () {
    const selectedDepartment = parseInt($(this).val());

    if(selectedDepartment === 5){
        $('#subOpdSection').show();
    }else{
        $('#subOpdSection').hide();
    }
    
});

$('#other_department_select').on('change', function () {
    const currentSelected = $(this).val() || []; 

    if(currentSelected.includes("5")){
        $("#othersubOpdSection").show();
    }else{
        $("#othersubOpdSection").hide();
    }

});

var proceed,user_id,user_info;
$('#addUserForm').on('submit',function(e){
    proceed = 0;
    e.preventDefault();
    $('.loading').show();
    var string = $('.username_1').val();
    var link = "{{ url('support/users/check_username') }}/"+string;
    console.log(link);
    $.ajax({
        url: link,
        type: "GET",
        success: function(data){
            if(data==1){
                $('.username-has-error').removeClass('hide');
            }else{
                $('.username-has-error').addClass('hide');
                proceed += 1;
            }

            var password1 = $('#password1').val();
            var password2 = $('#password2').val();
            if(password1 && password2){
                if(password1==password2){
                    proceed += 1;
                    $('.password-has-error').addClass('hide');
                    $('.password-has-match').removeClass('hide');
                }else{
                    $('.password-has-error').removeClass('hide');
                    $('.password-has-match').addClass('hide');
                }
            }

            if(proceed==2){
                $('#addUserForm').ajaxSubmit({
                    url:  "{{ url('support/users/store') }}",
                    type: "POST",
                    success: function(data){
                        setTimeout(function(){
                            window.location.reload(false);
                        },500);
                    },
                    error: function(){
                        $('#serverModal').modal();
                    }
                });
            }else{
                console.log('dont submit');
                $('.loading').hide();
            }
        }
    });


});

$('#updateUserForm').on('submit',function(e){
    proceed = 0;
    e.preventDefault();
    $('.loading').show();
    var string = $('.username').val();
    $.ajax({
        url: "{{ url('support/users/check_username/update/') }}/"+string+"/"+user_id,
        type: "GET",
        success: function(data){
            if(data==1){
                $('.username-has-error').removeClass('hide');
            }else{
                $('.username-has-error').addClass('hide');
                proceed += 1;
            }


            var password1 = $('.password_1').val();
            var password2 = $('.password_2').val();

            if(password1==password2){
                proceed += 1;
                $('.password-has-error').addClass('hide');
                $('.password-has-match').removeClass('hide');
            }else{
                $('.password-has-error').removeClass('hide');
                $('.password-has-match').addClass('hide');
            }

            if(proceed==2){
                $('#updateUserForm').ajaxSubmit({
                    url:  "{{ url('support/users/update') }}",
                    type: "POST",
                    success: function(data){

                        console.log("data for users:", data);

                        setTimeout(function(){
                            window.location.reload(false);
                        },500);
                    },
                    error: function(){
                        $('#serverModal').modal();
                    }
                });
            }else{
                console.log('dont submit');
                $('.loading').hide();
            }
            console.log(proceed);
        }
    });
});

$(document).ready(function () {

    const departmentSelect = $('#editdeparment_select');
    const subOpdSection = $('#editsubOpdSection');
    const subOpdSelect = $('#editsubOpdSelect');

    function handleDepartmentselect(){

        const selectedDepartment  = parseInt(departmentSelect.val());

        if(selectedDepartment === 5){
            subOpdSection.show();
        }else{
            subOpdSection.hide();
            // subOpdSelect.val('');
        }
    }

    $("#updateUserModal").on('shown.bs.modal', function () {
        handleDepartmentselect();
    });

    departmentSelect.on('change', function () {
        handleDepartmentselect();
    });
});

$('a[href="#addUserModal"]').on('click',function(){
    $('.btn-add').removeClass('hide');
    $('.btn-update').addClass('hide');
});

$('.update_info').on('click',function(){
    user_id = $(this).data('id');
    $.ajax({
        url: "{{ url('support/users/info') }}/"+user_id,
        type: "GET",
        success: function(data)
        {
            user_info = data;
            updateProfile();
        },
        error: function(){
            $('#serverModal').modal();
        }
    });
});

$('#edit_other_department_select').on('change', function () {
    const currentSelected = $(this).val() || []; 

    if(currentSelected.includes("5")){
        $("#othereditsubOpdSection").show();
    }else{
        $("#othereditsubOpdSection").hide();
    }

});

function updateProfile() {
    console.log("user_info", user_info);
    $('.user_id').val(user_id);
    $('.fname').val(user_info.fname);
    $('.mname').val(user_info.mname);
    $('.lname').val(user_info.lname);
    $('.contact').val(user_info.contact);
    $('.email').val(user_info.email);
    $('.designation').val(user_info.designation);
    $('.department_id').val(user_info.department_id).trigger('change');
    $('.edit_other_department_select').val(user_info.other_department_telemed).trigger('change');
    $('.username').val(user_info.username);
    $('.status').val(user_info.status);
    $('.level').val(user_info.level);

    $('#editsubOpdSelect').val(user_info.subopd_id).trigger('change');
    $('#other_editsubOpdSelect').val(user_info.subopd_id).trigger('change');

    $(".edit_other_department_select option").each(function() {

        if($(this).val() == user_info.department_id){
            $(this).remove();
        }
   });

    if (user_info.other_department_telemed) {
        // Split the comma-separated string into an array
        var otherDeptIds = user_info.other_department_telemed.split(',').map(function(id) {
            return id.trim();
        });
        console.log("otherDeptIds", otherDeptIds);
        // Set the values for the multi-select
        $('.edit_other_department_select').val(otherDeptIds).trigger('change');
    } 

}

$('#updateUserModal').on('hidden.bs.modal', function () {

    $(this).find('form')[0].reset();

    $(this).find('select.select2').val(null).trigger('change');

    $(this).find('.dynamic-content').empty();

});
</script>