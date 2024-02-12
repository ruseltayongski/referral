<script>
    <?php $user=Session::get('auth');?>
    var myfacility_name = "{{ \App\Facility::find($user->facility_id)->name }}";

    function telemedicineReferPatient(endorseUpward,alreadyRedirected,alreadyFollowup,code,referred_id) {
        const upwardIsCompleted = $('#upward_progress'+code+referred_id).hasClass('completed');
        $(".telemedicine").val(0);
        $("#telemedicine_redirected_code").val(code);
        if(endorseUpward && upwardIsCompleted && !alreadyRedirected && !alreadyFollowup) {
            $("#telemedicineRedirectedFormModal").modal('show');
        }
        else if(alreadyRedirected) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been referred!"
                });
        } 
        else if(alreadyFollowup) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been follow-up!"
                });
        }
        else {
            Lobibox.alert("error",
                {
                    msg: "You cannot refer a patient to an upward level because the receiving doctor has not yet endorsed your patient's referral to an upward level."
                });
        }
    }

    function telemedicineTreatedPatient(alreadyUpward, examinedPatient,alreadyTreated,code,referred_id) {
        const prescriptionIsCompleted = $('#prescribed_progress'+code+referred_id).hasClass('completed');
        const upwardIsCompleted = $('#upward_progress'+code+referred_id).hasClass('completed');
        if((examinedPatient || prescriptionIsCompleted) && !alreadyTreated && (!alreadyUpward || !upwardIsCompleted)) {
            Lobibox.confirm({
                msg: "Do you want to treat this patient?",
                callback: function ($this, type, ev) {
                    if(type === 'yes') {
                        var json = {
                            "_token" : "<?php echo csrf_token(); ?>",
                            "code" : code
                        };
                        var url = "<?php echo asset('api/video/treated') ?>";
                        $.post(url,json,function(result){
                            if(result === 'success') {
                                Lobibox.alert("success",
                                {
                                    msg: "The patient was successfully treated."
                                });
                                $("#treated_progress"+code+referred_id).addClass('completed');
                            }
                        })
                    }
                }
            });
        } else if(alreadyTreated) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been treated!"
                });
        } else if(alreadyUpward || upwardIsCompleted) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been upward!"
                });
        }
        else {
            Lobibox.alert("error",
                {
                    msg: "You can't treat a patient because the patient has not been examined."
                });
        }
    }
    //for empty error submission 
    document.getElementById('AddEmptyFileFollowupForm').addEventListener('submit', function(event) {
        var filesInput = document.getElementById('files-input');
        if (filesInput.files.length === 0) {
            // File input is empty, prevent form submission
            event.preventDefault();
            $("#err-message").html("Please select at least one file.");
            $("#err-message").css('color', 'red');
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Your code here
        document.getElementById('files-input').addEventListener('change', SelectedFile);
    });


    function SelectedFile(event) {
        const listfile = event.target.files;
        console.log('listfile :', listfile);
        const prevContainer = document.getElementById('container-preview');
        prevContainer.innerHTML = '';
        console.log('container', prevContainer);
        
        for(const file of listfile){
            const listItems = document.createElement('div');
            listItems.textContent = file.name;
            let allowedextension =  ["pdf", "png", "jpeg", "jpg"];
            let arrayfile = Array.from(listfile).map(file=>file.name);

            let allextension =  arrayfile.every(fileName => {
                let ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
                return allowedextension.includes(ext);
            });
            
            if(allextension){ 
                
                isvalidFiles = true;
                if (file.type.startsWith('image/')) {
                    console.log("imagesss:", file);
                    ImagePrev(file, prevContainer,listfile);
                }
                // Display PDF preview for .pdf files
                else if(file.type === 'application/pdf'){
                    // Create a container for each PDF and its remove icon
                    const Containerpdf = document.createElement('div');
                    Containerpdf.classList.add('pdf-container');
                    const pdfPreview = PdfPreview(file.name, '../public/fileupload/PDF_file_icon.png'); // Replace the placeholder URL
                    const removedFiles = [];
                    // Create the remove icon
                    const removeIcon = document.createElement('i');
                    removeIcon.classList.add('fa', 'fa-times', 'remove-icon');

                    removeIcon.addEventListener('click', function() {
                        const filename = file.name;
                        removedFiles.push(filename);
                        $("#filecount").val(removedFiles.join(''));
                        Containerpdf.remove();

                        const fileInput = document.getElementById('files-input');
                        const currentfile = fileInput.files;
                        const updatefiles = Array.from(currentfile).filter(file => !removedFiles.includes(file.name));
                        console.log('update files pdf',fileInput.files);
                        const newTransfer = new DataTransfer();
                        updatefiles.forEach(file => newTransfer.items.add(file));
                        fileInput.files = newTransfer.files;

                        if(prevContainer.children.length === 0) {
                            const fileInput = document.getElementById('files-input')
                            fileInput.value = '';
                            const event = new Event('change');
                            fileInput.dispatchEvent(event);
                        }
                                
                    });
                    Containerpdf.appendChild(pdfPreview);
                    Containerpdf.appendChild(removeIcon);
                    // Append the container to the main preview container
                    prevContainer.appendChild(Containerpdf);
                }
               
            }else if(!event.target.files.length && !prevContainer){
            
                event.preventDefault();
                alert('Please select a file before submitting.');
                return;
            }else{
                isvalidFiles = false;
                prevContainer.innerHTML = '';
                const errmsId = 'error-messages';
                let existmsg = document.getElementById(errmsId);
                console.log('filname', file.name);
                    if(!existmsg){
                        const errmsg = document.createElement('p');
                        console.log('error: ', errmsg);
                        
                        errmsg.textContent = 'Please upload a valid pdf or images file..';
                        errmsg.style.color = 'red';
                        errmsg.id = errmsId;
                        $("#err-message").html("");
                        prevContainer.appendChild(errmsg);
                    }
                        // Break out of the loop as there's an invalid file
                break;         
            }
        }
            $("#AddEmptyFileFollowupForm").submit(function(event){
                if(!isvalidFiles){
                    event.preventDefault();
                }else{
                    // $("#AddEmptyFileFollowupForm").show();
                        prevContainer.innerHTML = '';
                }  
            });

    }


    function ImagePrev(file, container, listFile){
        const reader = new FileReader();
        reader.onload = function (e){
            const imageCons = document.createElement('div');
            imageCons.classList.add('image-container');

            const prevImage = document.createElement('img');
            prevImage.setAttribute('src', e.target.result);
            prevImage.style.width = '150px';
            prevImage.style.height = '150px';
            prevImage.setAttribute('alt', file.name);
            prevImage.classList.add('preview');
            $("#err-message").html(""); // this will remove the display erro messages if empty
            prevImage.addEventListener('click', function () { 
    
                viewImage(e.target.result,file.name, container);
                
            });
            const removedFiles = []; 
            // Create the remove icon
            const removeIcon = document.createElement('i');
            removeIcon.classList.add('fa', 'fa-times', 'remove-icon');
            removeIcon.addEventListener('click', function() {
                container.removeChild(imageCons);

                const filename = file.name;
                removedFiles.push(filename);
                const fileInput = document.getElementById('files-input');
                const currentFiles = fileInput.files;
                const updatedFiles = Array.from(currentFiles).filter(file => !removedFiles.includes(file.name));
                // Create a new DataTransfer object to set the updated files
                const newTransfer = new DataTransfer();
                updatedFiles.forEach(file => newTransfer.items.add(file));
                // Set the new DataTransfer object to the file input
                fileInput.files = newTransfer.files;
                if(container.children.length === 0) {
                    const fileInput = document.getElementById('files-input')
                    fileInput.value = '';
                    const event = new Event('change');
                    fileInput.dispatchEvent(event);
                }

            });
            imageCons.appendChild(prevImage);
            imageCons.appendChild(removeIcon);
            container.appendChild(imageCons);
        };
        reader.readAsDataURL(file);// to start reading the file's data and trigger the onload
    }

    function viewImage(imgsrc, filename){
        $(".modal-title").html(filename);
        $("#imageView").attr('src', imgsrc);
        $("#imageView").attr('width', '100%');
        $("#imageView").attr('height', 'auto');
        $("#viewLargerFileModal").css('z-index', 1060);
        $("#viewLargerFileModal").modal("show");
    }
        
    function PdfPreview(file, placeholderUrl, container){
        // displayFilePreview(file, container, placeholderUrl);
        const pdfPreview = document.createElement('embed');
        pdfPreview.setAttribute('src', placeholderUrl); // Replace with actual PDF preview logic
        pdfPreview.style.width = '150px';
        pdfPreview.style.height = '150px';
        pdfPreview.dataset.originalFilename = file;
        $("#err-message").html(""); // this will remove the display erro messages if empty

        pdfPreview.addEventListener('click', function () { 
            pdfshow(file,placeholderUrl);
                
        });
       
        return pdfPreview;
    }

    //------------------------my adding for update file uploader Follow up-----------------------------//
    function editFileforFollowup(baseUrl,fileNames,code,activity_id,follow_id,position)
    {
        console.log("updated filename: ", fileNames);
        event.preventDefault();
        $(".telemedicine").val(1);
        $("#edit_telemedicine_followup_code").val(code);
        $("#edit_telemedicine_referred_id").val(activity_id);
        $("#edit_telemedicine_followup_id").val(follow_id);
        $("#file-list").text(fileNames);
        $("#selected-file-name-input").val(fileNames);
        $("#position_count_number").val(position);
        var currentPosition = $("#position_count_number").val();
        var fileExtension = fileNames.split('.').pop().toLowerCase();

        if(fileExtension === 'pdf'){
            $("#file-preview-black").html(fileNames);
             $("#img-preview").attr('src','../public/fileupload/PDF_file_icon.png');
             $("#img-preview").css('width', '50%');
        }else{
            $("#file-preview-black").html(fileNames);
            $("#img-preview").attr('src', `${baseUrl}/${fileNames}`);
        }

        $("#Update_followup_header").html("Update File");
        $("#telemedicineUpateFileFormModal").modal('show');
        $("#carouselmodaId").modal('hide'); 
       
    }

    $('#telemedicineUpateFileFormModal').on('hidden.bs.modal', function () {
        $('#file-upload-update').val();
        $("#carouselmodaId").remove();

    });
      //------------------------my adding for update file uploader Follow up End-----------------------------//

      //------------------------Add files if empty add more-----------------------------//
     function addfilesInFollowupIfempty(position,code,referred_id,follow_id,filenames){
        event.preventDefault();
        $(".telemedicine").val(1);
        $("#filenames").val(filenames);
        $("#position_counter").val(position);
        $("#telemedicine_followup_code").val(code);
        $("#telemedicine_referred_id").val(referred_id);
        $("#telemedicine_followup_id").val(follow_id);
       
        document.getElementById("container-preview").innerHTML = "";
        $("#err-message").html("");
        $("#Add_followup_headerform").html("Add Files")
        $("#FollowupAddEmptyFileFormModal").modal('show');
        $("#FollowupAddEmptyFileFormModal").on('hidden.bs.modal', function(){
            //  location.reload();
            // $("#carouselmodaId").hide();
            // $("#carouselmodaId").hide();
        });
   
        
     }
     $('#FollowupAddEmptyFileFormModal').on('hidden.bs.modal', function () {
            $('#files-input').val('');
            $('#container-preview').empty();
            $("#imageView").attr('src', '');
            $("#carouselmodaId").remove();
        });

 //------------------------ delete file-----------------------------//
     function DeleteFileforFollowup(baseUrl,fileNames,code,referred_id,follow_id,position){
        event.preventDefault();
        var fileExtension = fileNames.split('.').pop().toLowerCase();

        if (fileExtension === 'pdf') {
            var pdfViewer = '<embed id="pdfViewer" src="' + `${baseUrl}/${fileNames}` + '" type="application/pdf" width="100%" height="300px" />';
            $("#preview-containerfor").html(pdfViewer);
            $("#pdfViewer").attr('src', `${baseUrl}/${fileNames}`)
        }else{
            $("#delete-image").attr("src", `${baseUrl}/${fileNames}`);
        }
    
        $("#file-name").text(fileNames);
        $("#telemedicine_code").val(code);
        $("#delete_telemedicine_followup_id").val(follow_id);
        $("#delete_telemedicine_referred_id").val(referred_id);
        var position_counter = $("#position_counterer");
        var selectedFileNameInput = $("#selected-file-name");
        selectedFileNameInput.val(fileNames);
        position_counter.val(position);
        
        $("#Delete_followup_header").html("Are you sure You want to delete this file?");
        $("#telemedicineDeleteFileFollowupFormModal").modal('show');
     }//end of the function 

    $('#telemedicineDeleteFileFollowupFormModal').on('hidden.bs.modal', function () {
        $("#telemedicine_code").val("");
        $("#delete_telemedicine_followup_id").val("");
        $("#delete_telemedicine_referred_id").val("");
        $("#position_counterer").val("");
        $("#selected-file-name").val("");
        // Clear text and image previews
        $("#file-name").text("");
        // $("#delete-image").attr("src", "");
        $("#pdfViewer").remove();
        $("#carouselmodaId").remove();

        });
     // select single image & pdf to preview
    function readURL(input) {
        var url = input.value;
        var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
        console.log('input', input);
        if (input.files && input.files[0]) {
            var fileName = input.files[0].name;
            $('#img-preview').attr('src', '').css('display', 'none');
            $('#file-preview-black').html("");
            $("#file-preview-red").html("");
            $("#file-empty").html("");

            if (ext === "pdf") {
                isvalidFile = true;
                $('#img-preview').attr('src', '../public/fileupload/PDF_file_icon.png').css('display', 'block');
                $("#img-preview").css('width', '50%');
                $('#file-preview-black').html('<i class="fa fa-file-pdf-o"></i> ' + fileName);
            } else if (ext === "png" || ext === "jpeg" || ext === "jpg" || ext === "PNG" || ext === "JPEG" || ext === "JPG") {
                isvalidFile = true;
                $('#file-preview-black').html('<i class="fa fa-file-image-o"></i> ' + fileName);
                $('#img-preview').attr('src', URL.createObjectURL(input.files[0])).css('display', 'block');
            } else {
                isvalidFile = false;
                $("#file-preview-red").html("Please upload a valid pdf or image file.").css('color', 'red');
            }
            $("#telemedicineUpateFileForm").submit(function(event){
                if(!isvalidFile){
                    event.preventDefault();
                }else{
                    $("#telemedicineUpateFileForm").show();
                }
            })
        }
    }
     

    function telemedicineFollowUpPatient(alreadyReferred, alreadyEnded, examinedPatient, alreadyFollowUp, code, referred_id) {
        $("#telemed_follow_code").val(code);
        $("#telemedicine_follow_id").val(referred_id); //I add this add this to get the followup_id jondy
        $(".telemedicine").val(1);
        const treatedIsCompleted = $('#treated_progress'+code+referred_id).hasClass('completed');
        if(alreadyFollowUp) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been follow up!"
                });
        }
        else if(alreadyReferred) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been referred!"
                });
        }
        else if(alreadyEnded) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been ended!"
                });
        }
        else if(treatedIsCompleted) {
            telemedicine = 1;
            $("#telemedicineFollowupFormModal").modal('show');
        }
        else if(!examinedPatient) {
            Lobibox.alert("error",
                {
                    msg: "You cannot follow up on a patient because it has not yet been examined."
                });
        }
        else {
            $("#followup_header").html("Follow Up Patient");
            telemedicine = 1;
            $("#telemedicineFollowupFormModal").modal('show');

            $("#telemedicineFollowupFormModal").on('hidden.bs.modal', function(){
            // location.reload();
        });

        }
    }

    function consultToOtherFacilities(code) {
        $("#followup_header").html("Consult to other facilities");
        $("#telemedicine_followup_code").val(code);
        $(".telemedicine").val(1);
        telemedicine = 1;
        $("#telemedicineFollowupFormModal").modal('show');
    }

    function telemedicineExamined(tracking_id, code, alreadyAccepted, action_md, referring_md, activity_id, form_tpe) {
        if(alreadyAccepted || $("#accepted_progress"+code+activity_id).hasClass("completed")) {
            var url = "<?php echo asset('api/video/call'); ?>";
            var json = {
                "_token" : "<?php echo csrf_token(); ?>",
                "tracking_id" : tracking_id,
                "code" : code,
                "action_md" : action_md ? action_md : $("#accepted_progress"+code+activity_id).attr("data-actionmd"),
                "referring_md" : referring_md,
                "trigger_by" : "{{ $user->id }}",
                "form_type" : form_tpe,
                "activity_id" : activity_id
            };
            $.post(url,json,function(){

            });
            var windowName = 'NewWindow'; // Name of the new window
            var windowFeatures = 'width=600,height=400'; // Features for the new window (size, position, etc.)
            var newWindow = window.open("{{ asset('doctor/telemedicine?id=') }}"+tracking_id+"&code="+code+"&form_type="+form_tpe+"&referring_md=yes", windowName, windowFeatures);
            if (newWindow && newWindow.outerWidth) {
                // If the window was successfully opened, attempt to maximize it
                newWindow.moveTo(0, 0);
                newWindow.resizeTo(screen.availWidth, screen.availHeight);
            }
        } else if(!alreadyAccepted) {
            Lobibox.alert("error",
            {
                msg: "You cannot follow up on a patient because it has not yet been examined."
            });
        }
    }

    function telemedicinePrescription(track_id, activity_id, referred_code, referred_id) {
        const prescriptionIsCompleted = $('#prescribed_progress'+referred_code+referred_id).hasClass('completed');
        const url = "{{ asset('doctor/print/prescription') }}";
        if(activity_id) {
            window.open(`${url}/${track_id}/${activity_id}`);
        } else if(prescriptionIsCompleted) {
            window.open(`${url}/${track_id}/${referred_id}?prescription_new=true`);
        }
        else {
            Lobibox.alert("error",
            {
                msg: "No prescription has been created by the referred doctor"
            });
        }
    }

    function telemedicineEndPatient(alreadyTreated, alreadyReferred, alreadyFollowUp, alreadyEnd, code, referred_id) {
        const endIsCompleted = $('#end_progress'+code+referred_id).hasClass('completed');
        if(alreadyTreated && !alreadyReferred && !alreadyFollowUp && (!alreadyEnd || !endIsCompleted)) {
            Lobibox.confirm({
                msg: "Do you want to cycle end this patient?",
                callback: function ($this, type, ev) {
                    if(type === 'yes') {
                        var json = {
                            "_token" : "<?php echo csrf_token(); ?>",
                            "code" : code
                        };
                        var url = "<?php echo asset('api/video/end') ?>";
                        $.post(url,json,function(result){
                            if(result === 'success') {
                                Lobibox.alert("success",
                                    {
                                        msg: "The patient was successfully end cycle."
                                    });
                                $("#end_progress"+code+referred_id).addClass('completed');
                            }
                        })
                    }
                }
            });
        } else if(alreadyReferred) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been referred!"
                });
        } else if(alreadyFollowUp) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been follow up!"
                });
        }
        else if(alreadyEnd || endIsCompleted) {
            Lobibox.alert("error",
                {
                    msg: "This tracking area has already been end cycle!"
                });
        }
        else {
            Lobibox.alert("error",
                {
                    msg: "You can't end cycle a patient because the patient has not been examined."
                });
        }
    }

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
    $('body').on('submit','#telemedicineRedirectedForm',function(e){
        $("#redirected_submit_telemedicine").attr("disabled",true);
    });
    $('body').on('submit','#telemedicineFollowupForm',function(e){
        $("#followup_submit_telemedicine").attr("disabled",true);
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
    $('body').on('click','.btn-cancel',function() {
        var id = $(this).data('id');
        var user = $(this).data('user');
        if(user === 'admin')
            $('#cancelAdmin').val(user);
        var url = "{{ url('doctor/referred/cancel') }}/"+id;
        $("#cancelReferralForm").attr('action',url);
    });
    $('#cancelReferralForm').on('submit', function(e) {
        $("#btn-cancel-submit").attr('disabled', true);
    });
    $('#undoCancelForm').on('submit', function(e) {
        $("#btn-undocancel-submit").attr('disabled', true);
    });
</script>