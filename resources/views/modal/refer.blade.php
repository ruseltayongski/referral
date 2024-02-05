<?php
$user = Session::get('auth');
$facilities = \App\Facility::select('id','name')
    ->where('id','!=',$user->facility_id)
    //->where('province',$user->province)
    ->where('status',1)
    ->where('referral_used','yes')
    ->orderBy('name','asc')->get();
?>
<style>
    .custom-img-size {
        max-width: 150px; /* or any other size */
        height: auto;
        display: block;
        margin: 0 auto; /* for centering if needed */
    }

    .custom-file{
        display: inline-block;
        cursor: pointer;
        padding: 10px 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    #file-input {
      display: none;
    }
    #file-inputed {
      display: none;
    }
    #files-input{
      display: none;
    }
    #file-upload {
      display: none;
    }
    #file-upload-update{
      display: none;
    }

    #file-label {
      background-color: #3498db;
      color: #fff;
      padding: 10px 15px;
      cursor: pointer;
      display: inline-block;
    }

    #file-list {
      margin-top: 20px;
      overflow: hidden;
    }

    /* .preview-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      margin-top: 20px;
    }*/

    .preview {
      width: 150px;
      height: 150px;
      margin: 10px;
    } 
    .preview-container {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    .container-preview {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    .preview-item {
        padding: 5px;
        margin-right: 12px;
        margin-left: 12px;  
        
    }

    /* .pdf-container,
    .image-container{
        margin-bottom: 10px;
    } */

    .image-container {
        position: relative;
        display: inline-block; /* Ensure the container only takes the necessary space */
    }
    .pdf-container {
        position: relative;
        display: inline-block; /* Ensure the container only takes the necessary space */
        margin-right: 10px; /* Adjust the margin as needed */
        margin-top: 10px;
    }
    .remove-icon {
        position: absolute;
        top: 0;
        right: 0;
        background-color: red; /* Set a background color to make the icon more visible */
        padding: 5px; /* Add some padding for better visibility */
        cursor: pointer; /* Change the cursor to a pointer to indicate it's clickable */
    }


</style>
<script>

   

    // select multiple image & pdf to preview
    // document.getElementById('file-input').addEventListener('change', handleFileSelect);
    document.addEventListener('DOMContentLoaded', function () {
    // Your code here
    document.getElementById('telemedicineFollowupForm').addEventListener('submit', function(event) {
            var filesInput = document.getElementById('file-input');
            if(filesInput.files.length === 0){
                event.preventDefault();
                $("#err-msgpdf").html("Please select at least one file.");
            $("#err-msgpdf").css('color', 'red');
            }
            console.log('files inputed!', filesInput);
        });


    document.getElementById('file-input').addEventListener('change', handleFileSelect);
   
    });

    function handleFileSelect(event) {
        const fileList = event.target.files;
        console.log('fileList', fileList);
        // const fileListView = document.getElementById('file-list');
        const previewContainer = document.getElementById('preview-container');
        // fileListView.innerHTML = '';
        previewContainer.innerHTML = '';
         console.log('containers', previewContainer);
        for (const file of fileList) {
            const listItem = document.createElement('div');
            listItem.textContent = file.name;
            // fileListView.appendChild(listItem);
            let allowexten = ["pdf", "png", "jpeg", "jpg"];
            let filearr = Array.from(fileList).map(file=>file.name);

            let validext = filearr.every(filename => {
                let ext = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
                return allowexten.includes(ext);
            });
        //    const submitButton = $("#followup_submit");
            if(validext){
                isvalidFile = true;
                $("#telemedicineFollowupForm").off('submit'); // Remove previous submit event handler
                    // Display image preview for image files
                    if (file.type.startsWith('image/')) {
                    displayImagePreview(file, previewContainer);
                    }
                    // Display PDF preview for .pdf files
                    else if (file.type === 'application/pdf') {
                    // Create a container for each PDF and its remove icon
                        const pdfContainer = document.createElement('div');
                        pdfContainer.classList.add('pdf-container');
                        //console.log("pdf name:",file.name);
                        // Display PDF preview
                        const pdfPreview = displayPdfPreview(file.name, '../public/fileupload/PDF_file_icon.png'); // Replace the placeholder URL
                        const removedFiles = [];
                        // Create the remove icon
                        const removeIcon = document.createElement('i');
                        removeIcon.classList.add('fa', 'fa-times', 'remove-icon');
                        removeIcon.addEventListener('click', function() {
                            const filename = file.name;
                            removedFiles.push(filename);
                            
                            const fileInput = document.getElementById('file-input');
                            const currentfile = fileInput.files;
                            const updatefile = Array.from(currentfile).filter(file => !removedFiles.includes(file.name));
                            console.log('current pdf file:',currentfile);
                            const newTransfer = new DataTransfer();
                            updatefile.forEach(file => newTransfer.items.add(file));
                            fileInput.files = newTransfer.files;
                            console.log('update pdf:', updatefile);
                            console.log('remove:', removedFiles);
                            pdfContainer.remove();
                        
                        });
                      
                        // $("#telemedicineFollowupForm").submit(function(event) {
                        //                     removedFiles.forEach(filename => {
                        //                     $(this).append('<input type="hidden" name="removefiles[]" value="' + filename + '">');
                        //                 }); 
                        //             });
                        // Append the PDF preview and remove icon to the container

                        pdfContainer.appendChild(pdfPreview);
                        pdfContainer.appendChild(removeIcon);

                        // Append the container to the main preview container
                        previewContainer.appendChild(pdfContainer);
                    }
            }else{
                    isvalidFile = false;
                    previewContainer.innerHTML = '';
                    const errmsId = 'error-messages';
                    let existmsg = document.getElementById(errmsId);
                        console.log('filname', file.name);
                        if(!existmsg){
                            const errmsg = document.createElement('p');
                            console.log('error: ', errmsg);
                            
                            errmsg.textContent = 'Please upload a valid pdf or images file..';
                            errmsg.style.color = 'red';
                            errmsg.id = errmsId;
                            $("#err-msgpdf").html(""); //to empty the error messages submission
                            previewContainer.appendChild(errmsg);

                        }
                         break;
                           
                }
        }
                         $("#telemedicineFollowupForm").submit(function(event){
                            if(!isvalidFile){
                                event.preventDefault();
                            }else{
                                //  $("#telemedicineFollowupForm").show();
                                  previewContainer.innerHTML = '';
                        
                            }
                        });
    }
 
    function displayImagePreview(file, container) {
        const reader = new FileReader();
        // console.log('my file', file.name);
        console.log('my container', container);
        reader.onload = function (e) {
            // Create a container for each image and its remove icon
            const imageContainer = document.createElement('div');
            imageContainer.classList.add('image-container');

            // Create the image preview
            const preview = document.createElement('img');
            preview.setAttribute('src', e.target.result);
            preview.style.width = '150px';
            preview.style.height = '150px';
            preview.setAttribute('alt', file.name);
            preview.classList.add('preview');
            preview.addEventListener('click', function () { 
               // console.log(e.target.result);
               //var imgsrc = e.target.result;
               //console.log('img src:', imgsrc);
                displayLargeImage(e.target.result,file.name, container);
                
            });
            const removedFiles = []; 
            $("#err-msgpdf").html(""); //to empty the error messages submission
            // Create the remove icon
            const removeIcon = document.createElement('i');
            removeIcon.classList.add('fa', 'fa-times', 'remove-icon');
            removeIcon.addEventListener('click', function() {
                container.removeChild(imageContainer);

                const filename = file.name;
                removedFiles.push(filename);

                const fileInput = document.getElementById('file-input');
                const currentfiles = fileInput.files;
                console.log('my current files: ',currentfiles);
                const updatefiles = Array.from(currentfiles).filter(file => !removedFiles.includes(file.name))
                console.log("update files:", updatefiles);
                const newTransfer = new DataTransfer();
                updatefiles.forEach(file => newTransfer.items.add(file));
                fileInput.files = newTransfer.files;
                // $("#filecounter").val(removedFiles.join(','));
                // var namefile = $("#filecount").val();
                console.log('remove:', removedFiles);
                if (container.children.length === 0) {
                    // Reset the file input value
                    const fileInput = document.getElementById('file-input'); 
                    fileInput.value = '';
                    // Optionally, trigger the change event
                    const event = new Event('change');
                    fileInput.dispatchEvent(event);
                }
            });

            // $("#telemedicineFollowupForm").submit(function(event) {
            //     removedFiles.forEach(filename => {
            //             $(this).append('<input type="hidden" name="removefiles[]" value="' + filename + '">');
            //     });         
            // });

            // Append the image and remove icon to the container
            imageContainer.appendChild(preview);
            imageContainer.appendChild(removeIcon);

            // Append the container to the main container
            container.appendChild(imageContainer);
        };

        reader.readAsDataURL(file);
    }

        // Function to display the larger image
        function displayLargeImage(imgsrc, filename, container) {
        // Create a modal or overlay element
        console.log('img src:',imgsrc);    
        console.log('img src:',filename);
 
        $(".modal-title").html(filename);
       
        $("#imageView").attr('src', imgsrc);
        $("#imageView").attr('width', '100%');
        $("#imageView").attr('height', 'auto');

        $("#viewLargerFileModal").css('z-index', 1060);
        $("#viewLargerFileModal").modal("show");
    }



    function displayPdfPreview(file, placeholderUrl, container) {
        console.log('originame pdf filename:', file);
    // displayFilePreview(file, container, placeholderUrl);
        const pdfPreview = document.createElement('embed');
        pdfPreview.setAttribute('src', placeholderUrl); // Replace with actual PDF preview logic
    
        pdfPreview.style.width = '150px';
        pdfPreview.style.height = '150px';
        pdfPreview.dataset.originalFilename = file;
        $("#err-msgpdf").html(""); //to empty the error messages submission
        pdfPreview.addEventListener('click', function () { 
               
            pdfshow(file,placeholderUrl);
                
            });
       
        return pdfPreview;
    }

    function  pdfshow(file){
        console.log('filename:', file);
        
            $(".modal-title").html(file);
            $("#files").html('<i class="fa fa-file-pdf-o"></i> ' + file );
            $("#viewpdf").css('z-index', 1060);
            $("#viewpdf").modal("show");
        
    }

    function displayFilePreview(file, container, placeholderUrl) {
        // For unsupported file types, display a placeholder image
        const preview = document.createElement('img');
        preview.setAttribute('src', placeholderUrl);
        preview.setAttribute('alt', file.name);
        preview.classList.add('preview');
        container.appendChild(preview);
    }


</script>


<div class="modal fade" role="dialog" id="referFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4>REFER TO OTHER FACILITY</h4>
                <hr />
                <form method="post" id="rejectForm">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding:0px;">REASON FOR REDIRECTION:</label>
                        <textarea class="form-control reject_reason" rows="5" style="resize: none;" name="remarks" required></textarea>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-send"></i> Send</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="redirectedFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;">Redirect to other facility</h4>
                <hr />
                <form method="POST" action="{{ asset("doctor/referral/redirect") }}" id="redirectedForm">
                    <input type="hidden" name="code" id="redirected_code" value="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding:0px;">SELECT FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" style="width: 100%;" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">SELECT DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_referred" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="redirected_submit" class="btn btn-success btn-flat"><i class="fa fa-ambulance"></i> Redirect</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="telemedicineRedirectedFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;">Refer Patient</h4>
                <hr />
                <form method="POST" action="{{ asset("doctor/referral/redirect") }}" id="telemedicineRedirectedForm">
                    <input type="hidden" name="code" id="telemedicine_redirected_code" value="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding:0px;">SELECT FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" style="width: 100%;" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">SELECT DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_referred" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="redirected_submit_telemedicine" class="btn btn-success btn-flat"><i class="fa fa-ambulance"></i> Refer</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="viewpdf" tabindex="-1" role="dialog" aria-labelledby="viewpdfFileModalLabel" aria-hidden="true"  style="z-index: 1060;" >
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!-- Modal header content goes here -->
                <h5 class="modal-title" id="viewLargerFileModalLabel">Modal Title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                        <div class="row">
                            <div class="card">
                                <div class="card-body preview-item">
                                    <!-- <embed src="" id="pdfPreviewContainer" type="application/pdf"/> -->
                                    <h4 id="files"></h>
                                    <img src="" id="pdfView">
                                </div>
                            </div>
                        </div>
            </div>
            <div class="modal-footer">
                <!-- Modal footer content goes here -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewLargerFileModal" tabindex="-1" role="dialog" aria-labelledby="viewLargerFileModalLabel" aria-hidden="true"  style="z-index: 1060;" >
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!-- Modal header content goes here -->
                <h5 class="modal-title" id="viewLargerFileModalLabel">Modal Title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                        <div class="row">
                            <div class="card">
                                <div class="card-body preview-item">
                                    <!-- <embed src="" id="pdfPreviewContainer" type="application/pdf"/> -->
                                    <h4 id="files"></h>
                                    <img src="" id="imageView">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    </div>
            </div>
            <div class="modal-footer">
                <!-- Modal footer content goes here -->
            </div>
        </div>
    </div>
</div>
<!------------------------------Starting Adding file in first follow up------------------------------------>
<div class="modal fade" id="telemedicineFollowupFormModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-green" style="font-size: 15pt;" id="followup_header"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
        </div>
      <div class="modal-body">
        
            <form method="POST" action="{{ asset("api/video/followup") }}" id="telemedicineFollowupForm" enctype="multipart/form-data"><!--I add this enctype="multipart/form-data-->
                    <input type="hidden" name="code" id="telemed_follow_code" value="">
                    <input type="hidden" name="followup_id" id="telemedicine_follow_id" value=""><!--I add this for followup_id-->

                    <input type="hidden" class="telemedicine" value="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding:0px;">SELECT FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" style="width: 100%;" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">SELECT DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_referred" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <div class="form-group">
                            <label id="file-label" for="file-input" class="btn btn-primary custom-file">Select Files
                            <input type="file" id="file-input" name="files[]" multiple class="d-none"></label>
                            <!-- <input type="hidden" id="filecounter" name="removefiles[]" multiple class="d-none"> -->
                            <!-- <div id="file-list" class="mt-3"></div> -->
                            <!-- <div class="preview-container" id="preview-container"></div> -->
                    </div>  
                    <div class="row">
                        <div class="card">
                            <div class="card-body preview-item">
                                <p id="err-msgpdf" class="text-center"></p>
                                <div class="preview-container" id="preview-container"></div>
                               
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="followup_submit" class="btn btn-success btn-flat"><i class="fa fa-upload" aria-hidden="true"></i> Submit</button>
                    </div>
            </form>
            <div class="clearfix"></div>

      </div>
    </div>
  </div>
</div>

<!------------------------------Add files if empty or more files----------------------------->

<div class="modal fade" id="FollowupAddEmptyFileFormModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header">
                        <h4 class="text-green" style="font-size: 15pt;" id="Add_followup_headerform"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
            <div class="modal-body">

                <form method="POST" action="{{ asset("api/video/addfileIfempty") }}" id="AddEmptyFileFollowupForm" enctype="multipart/form-data"><!--I add this enctype="multipart/form-data-->
                        <input type="hidden" name="code" id="telemedicine_followup_code" value="">
                        <input type="hidden" name="followup_id" id="telemedicine_followup_id" value=""><!--I add this for followup_id-->
                        <input type="hidden" name="referred_id" id="telemedicine_referred_id" value=""><!--I add this for followup_id-->
                        <input type="hidden" name="position_count" id="position_counter" value=""><!--I add this for followup_id-->
                        <input type="hidden" name="filename" id="filenames" value="">
                        <input type="hidden" class="telemedicine" value="">
                        <div id="removedFilesContainer"></div>
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label id="file-label" for="files-input" class="btn btn-primary custom-file">Select Files
                            <input type="file" id="files-input" name="filesInput[]" multiple class="d-none"></label>
                            <!-- <input type="hidden" id="filecount" name="removefile[]" multiple class="d-none"> -->
                            <!-- <div id="file-list" class="mt-3"></div> -->
                        </div>
                        <div class="row">
                            <div class="card">
                                <div class="card-body preview-item">
                                    <p id="err-message" class="text-center"></p>
                                    <div class="container-preview" id="container-preview"></div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-fotter pull-right">
                            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                            <button type="submit" id="followup_submit_telemedicine" class="btn btn-success btn-flat"><i class="fa fa-upload" aria-hidden="true"></i> Submit</button>
                        </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<!------------------------------End of file------------------------------------->
<!------------------------------for update the file----------------------------------------->

<div class="modal fade" role="dialog" id="telemedicineUpateFileFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;" id="Update_followup_header"></h4>
                <hr />
               
                <form method="POST" action="{{ asset("api/video/editfilefollowup") }}" id="telemedicineUpateFileForm" enctype="multipart/form-data">
                   
                    <input type="hidden" name="code" id="edit_telemedicine_followup_code" value="">
                    <input type="hidden" name="followup_id" id="edit_telemedicine_followup_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="referred_id" id="edit_telemedicine_referred_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="position_count_number" id="position_count_number" value="">
                    <input type="hidden" class="telemedicine" value="">
                    {{ csrf_field() }}
                    <!-- <div class="form-group">
                        <label style="padding:0px;">SELECT FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" style="width: 100%;" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">SELECT DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_referred" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div> -->
                    <div class="form-group">
                        <label id="file-label" for="file-upload-update" class="btn btn-primary">Select Files
                        <!-- <input type="file" id="file-upload" name="files" class="d-none" onchange="displayFileName()" > -->
                        <input type="file" id="file-upload-update" name="files" class="d-none"  onchange="readURL(this)"></label>
                        <input type="hidden" id="selected-file-name-input" name="selectedFileName" value="">
                        <!-- <div id="file-list" class="mt-3"></div> -->
                            
                            <div class="preview-container" id="preview-container">
                                <p id="file-preview-red"></p>
                                <p id="file-preview-black" class="text-center"></p>
                                <p id="file-empty"></p>
                            </div>
                            <div class="row">
                                <div class="card">
                                    <div class="card-body">
                                        <img id="img-preview" src="#" onerror="this.src=''" class="custom-img-size" />
                                        <!-- <img id="pdf-preview" src="#" alt="image preview" class="custom-img-size" /> -->

                                    </div>
                                </div>
                            </div>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="followup_submit_telemedicine" class="btn btn-success btn-flat"><i class="fa fa-upload" aria-hidden="true"></i> Submit</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!---------------------------End of the file update---------------------------------------------------->


<!-- ----------------------------Add more file----------------------------------------->




<!-------------------------- delete modal file -------------------------------------->
<div class="modal fade" role="dialog" id="telemedicineDeleteFileFollowupFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-danger" style="font-size: 15pt;" id="Delete_followup_header"></h4>
                <hr />
                <form method="POST" action="{{ asset("api/video/deletefilefollowup") }}" id="telemedicineDeleteFileForm" enctype="multipart/form-data">
                   
                    <input type="hidden"  name="code"  id="telemedicine_code" value="">
                    <input type="hidden" name="followup_id" id="delete_telemedicine_followup_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="referred_id" id="delete_telemedicine_referred_id" value=""><!--I add this for followup_id-->
                    <input type="hidden"  id="position_counterer" name="position_counter" value="">
                    <input type="hidden" class="telemedicine" value="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="hidden" id="selected-file-name" name="selectedFileName" value="">
                        <div id="file-name" class="mt-3 text-center"></div>

                        <div class="preview-container" id="preview-containerfor">
                            <img id="delete-image" src="" alt="delete Image?" style="max-width: 100%; max-height: 300px;">
                        </div>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="followup_submit_telemedicine" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i> Submit</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-------------------------- delete modal file -------------------------------------->

<div class="modal fade" role="dialog" id="referAcceptFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;">Transfer to other facility</h4>
                <hr />
                <form method="post" id="referAcceptForm" action="{{ asset("doctor/referral/transfer") }}">
                    {{ csrf_field() }}
                    <input type="hidden" id="transfer_tracking_id" name="transfer_tracking_id">
                    <div class="form-group">
                        <label style="padding:0px;">REMARKS:</label>
                        <textarea class="form-control reject_reason" rows="5" style="resize: none;" name="remarks" required></textarea>
                    </div>
                    <div class="form-group">
                        <label style="padding:0px;">FACILITY:</label>
                        <select class="form-control select2 new_facility select_facility" name="facility" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">DEPARTMENT:</label>
                        <select name="department" class="form-control select_department select_department_accept" style="padding: 3px" required>
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-flat" id="transferred_submit"><i class="fa fa-ambulance"></i> Refer</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
$(document).ready(function() {
    @if(session('file_save'))
    var number = "{{ session('file_save') }}";
        Lobibox.notify('success', {
            msg: 'Successfully saved file!' + number +' Position'
        });
    @endif

    @if(session('delete_file'))
    var number = "{{ session('delete_file') }}";
        Lobibox.notify('success', {
            msg: 'Deleted file Successfully in ' + number + ' Position'
        });
    @endif

    @if(session('update_file'))
    var number = "{{ session('update_file') }}";
    Lobibox.notify('success', {
        msg: 'Updated Successfully in ' + number +' position'

    });
    @endif
});

</script>