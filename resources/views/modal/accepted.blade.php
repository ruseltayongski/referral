<div class="modal fade" role="dialog" id="arriveModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4>PATIENT ARRIVED</h4>
                <hr />
                <form method="post" id="arriveForm">
                    {{ csrf_field() }}
                    <div class="form-group-lg">
                        <div class="text-center text-bold text-success">
                            <small class="text-muted">Date/Time Arrived:</small><br />
                            {{ date('M d, Y h:i A') }}
                        </div>
                    </div>
                    <hr />
                    <div class="form-group-lg">
                        <label style="padding: 0px;">Remarks: </label>
                        <br />
                        <textarea name="remarks" class="remarks form-control" rows="5" style="resize: none" required></textarea>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-flat" id="arrived_button"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="archiveModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4>PATIENT DIDN'T ARRIVED</h4>
                <hr />
                <form method="post" id="archiveForm">
                    {{ csrf_field() }}
                    <div class="form-group-lg">
                        <label style="padding: 0px;">Remarks: </label>
                        <br />
                        <textarea name="remarks" class="remarks form-control" rows="5" style="resize: none"></textarea>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-flat" id="notarrived_button"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="admitModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4>ADMIT PATIENT</h4>
                <hr />
                <form method="post" id="admitForm">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding: 0px">Select Date/Time:</label>
                        <br />
                        <input type="text" value="{{ date('Y-m-d H:i') }}" class="form-control form_datetime" name="date_time" placeholder="Date/Time Admitted" />
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" id="admitted_button" class="btn btn-success btn-flat"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="dischargeModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="jim-content">
                <h4>DISCHARGE PATIENT</h4>
                <hr />
                <form method="post" id="dischargeForm">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label style="padding: 0px">Select Date/Time:</label>
                        <br />
                        <input type="text" value="{{ date('Y-m-d H:i') }}" class="form-control form_datetime" name="date_time" placeholder="Date/Time Admitted" />
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<label style="padding: 0px">ICD10</label>--}}
                        {{--<br />--}}
                        {{--<a data-toggle="modal"--}}
                           {{--data-target="#icd-modal-discharge"--}}
                           {{--type="button"--}}
                           {{--class="btn btn-sm btn-success"--}}
                           {{--onclick="searchICD10()">--}}
                            {{--<i class="fa fa-medkit"></i>  Add ICD-10--}}
                        {{--</a>--}}
                        {{--<button type="button" id="clear_icd" class="btn btn-sm btn-danger" onclick="clearICD()"> Clear ICD-10</button>--}}
                        {{--<div><span class="text-green" id="icd_selected"></span></div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label style="padding: 0px">Clinical Status</label>
                        <br />
                        <select name="clinical_status" class="form-control" >
                            <option value="">Select option</option>
                            <option value="asymptomatic">Asymptomatic for at least 3 days</option>
                            <option value="recovered">Recovered</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">Surveillance Category</label>
                        <br />
                        <select name="sur_category" class="form-control" >
                            <option value="">Select option</option>
                            <option value="contact_pum">Contact (PUM)</option>
                            <option value="suspect">Suspect</option>
                            <option value="probable">Probable</option>
                            <option value="confirmed">Confirmed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="padding: 0px">Enter Remarks:</label>
                        <br />
                        <textarea name="remarks" class="remarks form-control" rows="5" style="resize: none" required></textarea>
                    </div>

                    <div class="form-group">
                        <small class="text-success"><b>FILE ATTACHMENTS:</b></small> &emsp;
                        <!-- <button type="button" class="btn btn-xs btn-danger" id="remove_files_btn" onclick="removeFiles()">Remove Files</button> -->
                        <br><br>
                        <div class="attachment">
                            <div class="col-md-12 dischargedfile" id="upload1">
                                <div class="file-upload">
                                    <div class="text-center image-upload-wrap" id="image-upload-wrap1">
                                        <input class="file-upload-input files" multiple id="file_upload_input1" type='file' name="file_upload[]" onchange="readUrl(this, 1);" accept="image/png, image/jpeg, image/jpg, image/gif, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/pdf" />
                                        <img src="{{ asset('resources/img/add_file.png') }}" style="width: 100%; height: 100%;">
                                    </div>
                                    <!-- <div class="file-upload-content" id="file-upload-content1">
                                        <img class="file-upload-image" id="file-upload-image1" src="#" />
                                        <div class="image-title-wrap">
                                            <b><small class="image-title" id="image-title1" style="display:block; word-wrap: break-word;"></small></b>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="form-fotter pull-right">
                        <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-flat" id="discharged_button"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </form>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{--<div class="modal fade" id="icd-modal-discharge">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title" style="font-size: 17pt;">Search ICD-10 by keyword</h4>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-lg">
                    <input type="text" id="icd10_keyword" class="form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat" onclick="searchICD10()">Find</button>
                    </span>
                </div><br>
                <div class="icd_body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="getAllCheckBox()"><i class="fa fa-save"></i> Save selected check</button>
            </div>
        </div>
    </div>
</div>--}}

<script>

// var upload_pos = 2;
var upload_count = 0;
let fileInfoArray = [];
let fileQueue = [];
let isProcessing = false;

function readUrl(input, pos) {
    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            fileQueue.push({
                file: input.files[i],
                pos: pos + i + 1  // Add 1 to skip position 1 which is reserved for upload button
            });
        }
        processFileQueue();
        
        // Clear the input to allow uploading the same file again
        input.value = '';
    }
}

function processFileQueue() {
    if (isProcessing || fileQueue.length === 0) return;

    isProcessing = true;
    let { file, pos } = fileQueue.shift();
    processFile(file, pos);
}

function processFile(file, pos) {
    let reader = new FileReader();
    let type = file.type;
    let filename = file.name;

    reader.onloadend = function(e) {
        let iconSrc;
        
        fileInfoArray.push({
            name: filename,
            type: type,
            file: file,
            pos: pos
        });

        // Determine preview image/icon
        if (type === 'application/pdf') {
            iconSrc = '{{ asset('resources/img/pdf_icon.png') }}';
        } else if (type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            iconSrc = '{{ asset('resources/img/document_icon.png') }}';
        } else if (type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            iconSrc = '{{ asset('resources/img/sheet_icon.png') }}';
        } else if (type.startsWith('image/')) {
            iconSrc = e.target.result;
        } else {
            iconSrc = '{{asset('resources/img/file_icon.png') }}';
        }

        // Create new file preview - now appears after the upload button
        var previewDiv = $(
            '<div class="col-md-3 col-sm-4 col-xs-6 dischargedfile" id="upload' + pos + '">' +
            '   <div class="file-upload">' +
            '       <div class="file-upload-content" id="file-upload-content' + pos + '" style="display: block;">' +
            '           <img class="file-upload-image" id="file-upload-image' + pos + '" src="' + iconSrc + '" ' +
            '               style="max-height: 120px; width: auto; margin: 0 auto; display: block; object-fit: contain;"/>' +
            '           <div class="image-title-wrap" style="margin-top: 10px; position: relative;">' +
            '               <b><small class="image-title" id="image-title' + pos + '" ' +
            '                   style="display: block; word-wrap: break-word; padding-right: 20px;"></small></b>' +
            '               <button type="button" onclick="removeOneFile(' + pos + ')" ' +
            '                   class="btn btn-xs btn-danger" style="position: absolute; top: 0; right: 0;">' +
            '                   <i class="fa fa-trash"></i>' +
            '               </button>' +
            '           </div>' +
            '       </div>' +
            '   </div>' +
            '</div>'
        );

        // Insert after the upload button
        $('#upload1').after(previewDiv);

        // Update filename display
        var maxLength = 12;
        var truncatedName = filename.length > maxLength ? 
            filename.substring(0, maxLength) + '...' : 
            filename;
        $('#image-title' + pos).html(truncatedName).attr('title', filename);

        upload_count++;
        if (upload_count % 4 === 0) {
            $('.attachment').append('<div class="clearfix visible-md visible-lg"></div>');
        }
        if (upload_count % 3 === 0) {
            $('.attachment').append('<div class="clearfix visible-sm"></div>');
        }
        if (upload_count % 2 === 0) {
            $('.attachment').append('<div class="clearfix visible-xs"></div>');
        }

        $('#remove_files_btn').show();

        isProcessing = false;
        processFileQueue();
    };

    if (type.startsWith('image/')) {
        reader.readAsDataURL(file);
    } else {
        reader.readAsArrayBuffer(file);
    }
}

function removeOneFile(pos) {
    $('#upload' + pos).remove();
    
    // Find the index of the file to remove
    const indexToRemove = fileInfoArray.findIndex(file => file.pos === pos);
    if (indexToRemove !== -1) {
        fileInfoArray.splice(indexToRemove, 1);
    }
    
    upload_count--;
    console.log("fileInfoArray", fileInfoArray);
    if (upload_count === 0) {
        $('#remove_files_btn').hide();
    }

    
}

function removeFiles() {
    // Remove all file previews except the upload button
    $('.attachment .dischargedfile:not(#upload1)').remove();
    fileInfoArray = [];
    upload_count = 0;
    $('#file_upload_input1').val(''); // Clear file input
    $('#remove_files_btn').hide();
}

</script>

<style>

.file-upload {
    background-color: #ffffff;
    margin: 0 auto;
    padding: 10px;
    border: 2px dashed #28a745; /* Green dashed border */
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
}

.image-upload-wrap {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.file-upload-input {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-upload img {
    max-width: 80px; /* Adjust size of the icon */
    max-height: 80px;
}

.file-upload:hover {
    background-color: rgba(40, 167, 69, 0.1); /* Light green hover effect */
}
.dischargedfile{
    padding-bottom: 10px;
}
</style>