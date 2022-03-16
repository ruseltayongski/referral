<script>
    $('#diag_prompt').hide();

    $("#edit_save_btn").on('click',function(e){
        if(!($('input[name="icd_ids[]"]').val()) && !($(".other_diagnosis").val())){
            e.preventDefault();
            $('#diag_prompt').show();
            $('#diag_prompt').focus();
            return false;
        }
    });

    /* INITIALIZE WOMAN VALUES */
    $('.covid_number').val("<?php echo $form['pregnant']->covid_number;?>");
    $('.clinical_status').val("<?php echo $form['pregnant']->refer_clinical_status;?>");
    $('.surve_category').val("<?php echo $form['pregnant']->refer_sur_category;?>");
    $('.woman_major_findings').val("<?php echo $form['pregnant']->woman_major_findings;?>");
    $('.woman_before_treatment').val("<?php echo $form['pregnant']->woman_before_treatment;?>");
    $('.woman_before_given_time').val("<?php echo $form['pregnant']->woman_before_given_time;?>");
    $('.woman_during_transport').val("<?php echo $form['pregnant']->woman_during_transport;?>");
    $('.woman_transport_given_time').val("<?php echo $form['pregnant']->woman_transport_given_time;?>");
    $('.woman_information_given').val("<?php echo $form['pregnant']->woman_information_given;?>");

    /* INITIALIZE BABY VALUES */
    $('.baby_fname').val("<?php echo $form['baby']->baby_fname;?>");
    $('.baby_mname').val("<?php echo $form['baby']->baby_mname;?>");
    $('.baby_lname').val("<?php echo $form['baby']->baby_lname;?>");
    $('.baby_dob').val("<?php echo $form['baby']->baby_dob;?>");
    $('.baby_weight').val("<?php echo $form['baby']->weight;?>");
    $('.baby_gestational_age').val("<?php echo $form['baby']->gestational_age;?>");
    $('.baby_major_findings').val("<?php echo $form['baby']->baby_major_findings;?>");
    $('.baby_last_feed').val("<?php echo $form['baby']->baby_last_feed?>");
    $('.baby_before_treatment').val("<?php echo $form['baby']->baby_before_treatment;?>");
    $('.baby_before_given_time').val("<?php echo $form['baby']->baby_before_given_time;?>");
    $('.baby_during_transport').val("<?php echo $form['baby']->baby_during_transport;?>");
    $('.baby_transport_given_time').val("<?php echo $form['baby']->baby_transport_given_time?>");
    $('.baby_information_given').val("<?php echo $form['baby']->baby_information_given;?>");

    $('.baby_fname, .baby_mname, .baby_lname').on('change', function() {
        var fname = $('.baby_fname').val();
        var mname = $('.baby_mname').val();
        var lname = $('.baby_lname').val();
        if((fname != "") || (mname != "") || (lname != "")) {
            $('.baby_fname, .baby_lname').prop('required', true);
            $('.baby_dob, .baby_weight, .baby_gestational_age').prop('required', true);
        }
        else {
            $('.baby_fname, .baby_lname').prop('required', false);
            $('.baby_dob, .baby_weight, .baby_gestational_age').prop('required', false);
        }
    });

    $('.icd_selected, .notes_diag, .other_diag').hide();

    @if(isset($icd[0]))
    $('.icd_selected').show();;
    @endif

    @if(isset($form['pregnant']->notes_diagnoses))
    $('.notes_diag').show();
    $('.notes_diagnosis').val("<?php echo $form['pregnant']->notes_diagnoses;?>");
    $('.add_notes_btn').hide();
    @endif

    @if(isset($form['pregnant']->other_diagnoses))
    $('.other_diag').show();
    $('.other_diagnosis').val("<?php echo $form['pregnant']->other_diagnoses;?>");
    @endif

    $('.reason_referral').val("<?php echo $reason->id;?>");
    @if(isset($form['pregnant']->other_reason_referral))
    $('.reason_referral').val("-1");
    @else
    $('.other_reason_referral').hide();
    @endif
    $('.other_reason_referral').html("<textarea class='form-control' id='other_reason' name='other_reason_referral' style='resize: none;' rows='3'>" + "<?php echo $form['pregnant']->other_reason_referral;?>" + "</textarea>");

    @if(isset($file_path))
    $('.with_file_attached').removeClass('hide');;
    @else
    $('.no_file_attached').removeClass('hide');
    @endif

    function clearFileUpload() {
        $('.with_file_attached').addClass('hide');
        $('.no_file_attached').removeClass('hide');
        $('#file_cleared').val("true");
    }

    function clearIcdNormal() {
        $("#icd_selected").html("");
        $(".icd_selected").hide();
        $('#icd_cleared').val("true");
        $('input[name="icd_ids[]"]').empty();
    }

    function clearOtherDiagnosisPregnant() {
        $(".other_diagnosis").val("");
        $(".other_diag").hide();
        $('.other_diag_cleared').val("true");
    }

    function clearNotesDiagnosisPregnant() {
        $(".notes_diagnosis").val("");
        $(".notes_diag").hide();
        $(".add_notes_btn").show();
        $('#notes_diag_cleared').val("true");
    }

    function clearOtherReasonReferral() {
        $("#other_reason").val("");
        $('.other_reason_referral').hide();
    }

    function addNotesDiagnosis() {
        $(".notes_diag").show();
        $(".notes_diagnosis").html(loading);
        $(".notes_diagnosis").html("");
    }

    function searchICD10() {
        $(".icd_body").html(loading);
        var url = "<?php echo asset('icd/search'); ?>";
        var json = {
            "_token" : "<?php echo csrf_token(); ?>",
            "icd_keyword" : $("#icd10_keyword").val()
        };
        $.post(url,json,function(result){
            setTimeout(function(){
                $(".icd_body").html(result);
            },500);
        });
    }

    function othersDiagnosis() {
        $('#icd-modal').modal('hide');
        $(".other_diag").show();
        $('#diag_prompt').hide();
    }

    $('.reason_referral').on('change', function() {
        var value = $(this).val();
        if(value == '-1') {
            setTimeout(function(){
                $('.other_reason_referral').show();
                @if(isset($form->other_reason_referral))
                $('.other_reason_referral').html("<textarea class='form-control' id='other_reason' name='other_reason_referral' style='resize: none;width: 100%;' rows='3'>" + "<?php echo $form['pregnant']->other_reason_referral;?>" + "</textarea>");
                @else
                $('.other_reason_referral').html("<textarea class='form-control' id='other_reason' name='other_reason_referral' style='resize: none;width: 100%;' rows='3' placeholder='Other reason for referral'></textarea>");
                @endif
            },500);
        }else{
            clearOtherReasonReferral();
        }
    });

    function getAllCheckBox() {
        $('#icd-modal').modal('toggle');
        $('.icd_selected').show();
        $('#diag_prompt').hide();
        var values = [];

        $('input[name="icd_checkbox[]"]:checked').each(function () {
            values[values.length] = (this.checked ? $(this).parent().parent().siblings("td").eq(1).text() : "");
            var icd_description = $(this).parent().parent().siblings("td").eq(1).text();
            var id = $(this).val();
            if(this.checked){
                console.log(icd_description);
                $("#icd_selected").append('=> '+icd_description+' '+'<br><input id="icd" type="hidden" name="icd_ids[]" value="'+id+'">');
            }
            clearOtherDiagnosisPregnant();
        });
    }

    function readURL(input) {
        if (input.files && input.files[0]) {

            var reader = new FileReader();

            reader.onload = function(e) {
                $('.image-upload-wrap').hide();

                $('.file-upload-image').attr('src', e.target.result);
                $('.file-upload-content').show();

                $('.image-title').html(input.files[0].name);
            };

            reader.readAsDataURL(input.files[0]);

        } else {
            removeUpload();
        }
    }

    function removeUpload() {
        $('.file-upload-input').replaceWith($('.file-upload-input').clone());
        $('.file-upload-content').hide();
        $('.image-upload-wrap').show();
    }

    $('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
    });
</script>