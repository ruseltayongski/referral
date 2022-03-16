<script>
    $('#diag_prompt').hide();

    $("#edit_save_btn").on('click',function(e){
        if(!($('input[name="icd_ids[]"]').val()) && !($("#other_diagnosis").val())){
            e.preventDefault();
            $('#diag_prompt').show();
            $('#diag_prompt').focus();
            return false;
        }
    });

    $('.icd_selected, .notes_diagnosis, .other_diag').hide();

    $('.patient_sex').html("<?php echo $form->patient_sex;?>");
    $('.civil_status').html("<?php echo $form->patient_status;?>");
    $('.phic_id').val("<?php echo $form->phic_id;?>");
    $('.phic_status').val("<?php echo $form->phic_status;?>");
    $('.covid_number').val("<?php echo $form->covid_number;?>");
    $('.clinical_status').val("<?php echo $form->refer_clinical_status;?>");
    $('.surve_category').val("<?php echo $form->refer_sur_category;?>");
    $('.case_summary').val("<?php echo $form->case_summary;?>");
    $('.reco_summary').val("<?php echo $form->reco_summary;?>");

    @if(isset($icd[0]))
        $('.icd_selected').show();;
    @endif

    @if(isset($form->diagnosis))
    $('.notes_diagnosis').show();
    $('.normal_notes_diagnosis').val("<?php echo $form->diagnosis;?>");
    $('.add_notes_btn').hide();
    @endif

    @if(isset($form->other_diagnoses))
    $('.other_diag').show();
    $('#other_diagnosis').val("<?php echo $form->other_diagnoses;?>");
    @endif

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

    $('.reason_referral').val("<?php echo $reason->id;?>");
    @if(isset($form->other_reason_referral))
        $('.reason_referral').val("-1");
    @else
        $('.other_reason_referral').hide();
    @endif
    $('.other_reason_referral').html("<textarea class='form-control' id='other_reason' name='other_reason_referral' style='resize: none;width: 100%;' rows='3'>" + "<?php echo $form->other_reason_referral;?>" + "</textarea>");

    function clearIcdNormal() {
        $("#icd_selected").html("");
        $(".icd_selected").hide();
        $('#icd_cleared').val("true");
        $('input[name="icd_ids[]"]').empty();
    }

    function clearOtherDiagnosis() {
        $("#other_diagnosis").val("");
        $(".other_diag").hide();
        $('.other_diag_cleared').val("true");
    }

    function clearNotesDiagnosis() {
        $(".normal_notes_diagnosis").val("");
        $(".notes_diagnosis").hide();
        $(".add_notes_btn").show();
        $('#notes_diag_cleared').val("true");
    }

    function clearOtherReasonReferral() {
        $("#other_reason").val("");
        $('.other_reason_referral').hide();
    }

    function addNotesDiagnosis() {
        $(".notes_diagnosis").show();
        $(".normal_notes_diagnosis").html(loading);
        $(".normal_notes_diagnosis").html("");
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
                    $('.other_reason_referral').html("<textarea class='form-control' id='other_reason' name='other_reason_referral' style='resize: none;width: 100%;' rows='3'>" + "<?php echo $form->other_reason_referral;?>" + "</textarea>");
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
            clearOtherDiagnosis();
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