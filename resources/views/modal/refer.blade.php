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

    #file-input {
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
        overflow-y: scroll;
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

</style>
<script>

  // select multiple image & pdf to preview

 // document.getElementById('file-input').addEventListener('change', handleFileSelect);
    document.addEventListener('DOMContentLoaded', function () {
    // Your code here
    document.getElementById('file-input').addEventListener('change', handleFileSelect);
});
    function handleFileSelect(event) {
    const fileList = event.target.files;
    const fileListView = document.getElementById('file-list');
    const previewContainer = document.getElementById('preview-container');
    // fileListView.innerHTML = '';
    // previewContainer.innerHTML = '';

    for (const file of fileList) {
        const listItem = document.createElement('div');
        listItem.textContent = file.name;
        fileListView.appendChild(listItem);

        // Display image preview for image files
        if (file.type.startsWith('image/')) {
        displayImagePreview(file, previewContainer);
        }
        // Display document preview for .doc and .docx files
        else if (file.name.toLowerCase().endsWith('.doc') || file.name.toLowerCase().endsWith('.docx')) {
        displayDocumentPreview(file, previewContainer, 'https://placehold.it/100x100'); // You can replace the placeholder URL
        }
        // Display spreadsheet preview for .xls and .xlsx files
        else if (file.name.toLowerCase().endsWith('.xls') || file.name.toLowerCase().endsWith('.xlsx')) {
        displaySpreadsheetPreview(file, previewContainer, 'https://placehold.it/100x100'); // You can replace the placeholder URL
        }
        // Display PDF preview for .pdf files
        else if (file.type === 'application/pdf') {
             // Create a container for each image and its remove icon
             const pdfContainer = document.createElement('div');
                    pdfContainer.classList.add('pdf-container');
        displayPdfPreview(file, previewContainer, '../public/fileupload/PDF_file_icon.png'); // You can replace the placeholder URL
        }

        
    }
    }

    function displayImagePreview(file, container) {
    const reader = new FileReader();

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

        // Create the remove icon
        const removeIcon = document.createElement('i');
        removeIcon.classList.add('fa', 'fa-times', 'remove-icon');
        removeIcon.addEventListener('click', function() {
            // Remove the associated image preview when the remove icon is clicked
            container.removeChild(imageContainer);
        });

        // Append the image and remove icon to the container
        imageContainer.appendChild(preview);
        imageContainer.appendChild(removeIcon);

        // Append the container to the main container
        container.appendChild(imageContainer);
    };

    reader.readAsDataURL(file);
}

    function displayDocumentPreview(file, container, placeholderUrl) {
    displayFilePreview(file, container, placeholderUrl);
    }

    function displaySpreadsheetPreview(file, container, placeholderUrl) {
    displayFilePreview(file, container, placeholderUrl);
    }

    function displayPdfPreview(file, container, placeholderUrl) {
    displayFilePreview(file, container, placeholderUrl);
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
                            <label id="file-label" for="file-input" class="btn btn-primary">Select Files</label>
                            <input type="file" id="file-input" name="files[]" multiple class="d-none">
                            <!-- <div id="file-list" class="mt-3"></div> -->
                            <!-- <div class="preview-container" id="preview-container"></div> -->
                    </div>

                    <!--=========================================================================================-->

                   
                    <div class="row">
                        <div class="card">
                            <div class="card-body preview-item">
                                <div class="preview-container" id="preview-container"></div>
                               
                            </div>
                        </div>
                    </div>
                    
                    

                    <!--=========================================================================================-->



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

<!------------------------------Add files if empty----------------------------->

<div class="modal fade" role="dialog" id="FollowupAddEmptyFileFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;" id="Add_followup_headerform"></h4>
                <hr />
                <form method="POST" action="{{ asset("api/video/addfileIfempty") }}" id="telemedicineAddEmptyFileFollowupForm" enctype="multipart/form-data"><!--I add this enctype="multipart/form-data-->
                    <input type="hidden" name="code" id="telemedicine_followup_code" value="">
                    <input type="hidden" name="followup_id" id="telemedicine_followup_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="referred_id" id="telemedicine_referred_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="position_count" id="position_counter" value=""><!--I add this for followup_id-->
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
                                <label id="file-label" for="files-input" class="btn btn-primary">Select Files</label>
                                <input type="file" id="files-input" name="filesInput[]" multiple class="d-none">
    
                            <div id="file-list" class="mt-3"></div>
                    
                            <div class="preview-container" id="preview-container"></div>
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

<!------------------------------End of file------------------------------------->
<!------------------------------for update the file----------------------------------------->

<div class="modal fade" role="dialog" id="telemedicineUpateFileFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;" id="Update_followup_header"></h4>
                <hr />
               
                <form method="POST" action="{{ asset("api/video/editfilefollowup") }}" id="telemedicineUpateFileForm"enctype="multipart/form-data">
                   
                    <input type="hidden" name="code" id="edit_telemedicine_followup_code" value="">
                    <input type="hidden" name="followup_id" id="edit_telemedicine_followup_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="referred_id" id="edit_telemedicine_referred_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="position_count_number" id="position_count_number" value="">
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
                        <label id="file-label" for="file-upload-update" class="btn btn-primary">Select Files</label>
                        <!-- <input type="file" id="file-upload" name="files" class="d-none" onchange="displayFileName()" > -->
                        <input type="file" id="file-upload-update" name="files" class="d-none"  onchange="readURL(this)">
                        <input type="hidden" id="selected-file-name-input" name="selectedFileName" value="">
                        <div id="file-list" class="mt-3"></div>
                       
                        <div class="preview-container" id="preview-container">
                            <p id="file-preview-text"></p>
                            <img id="img-preview" src="#" alt="image preview" style="max-width: 100%; max-height: 300px; display: none;" />
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


<!------------------------------Add more file----------------------------------------->

<div class="modal fade" role="dialog" id="telemedicineAddFileFollowupFormModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4 class="text-green" style="font-size: 15pt;" id="Add_followup_header"></h4>
                <hr />
                <form method="POST" action="{{ asset("api/video/addfilefollowup") }}" id="telemedicineAddFileForm" enctype="multipart/form-data">
                   
                    <input type="hidden" name="code" id="add_telemedicine_followup_code" value="">
                    <input type="hidden" name="addfollowup_id" id="add_telemedicine_followup_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="addreferred_id" id="add_telemedicine_referred_id" value=""><!--I add this for followup_id-->
                    <input type="hidden" name="addposition_counter" id="position_counter_number" value="">
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
                        <label id="file-label" for="file-input" class="btn btn-primary">Select Files</label>
                        <!-- <input type="file" id="file-upload" name="files" class="d-none" onchange="displayFileName()" > -->
                        <input type="file" id="file-input" name="files[]" multiple class="d-none">
                        <input type="hidden" id="selected-file-name-input" name="selectedFileName" value="">
                        <div id="file-list" class="mt-3"></div>

                        <div class="preview-container" id="preview-container">
                            
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



