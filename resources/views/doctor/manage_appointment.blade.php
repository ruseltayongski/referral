@extends('layouts.app')

@section('css')
    <style>
        .bg_new {
            background-color: #ffcba4;
        }

        .bg_seen {
            background-color: #fbf7f3;
        }

        .bg_resolved {
            background-color: #ace1af;
        }

        .label-border {
            border: 1px solid #ccc;
            padding: 5px 5px 10px 5px;
            margin-top: 12px;
            border-radius: 5px;
            display: block;
            width: 100%;
        }
        .label-border-time {
            border: 1px solid lightslategray;
            padding: 5px 5px 10px 5px;
            margin-top: 12px;
            border-radius: 5px;
            display: block;
            width: 100%;
        }

        .already-appointed {
        background-color: #FFA07A; /* trapping background color of the Date appointment */
    }

    .btn-xs {
    padding: .90rem .15rem;
    font-size: .880rem;
    line-height: .5;
    border-radius: .2rem;
    }
   

    </style>
@endsection

@section('content')

    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right form-inline">
                <div class="form-group" style="margin-bottom: 10px;">
                    <input type="text" class="form-control" name="appt_keyword" value="{{ $keyword }}" id="keyword" placeholder="Search...">
                    <button type="submit" class="btn btn-success btn-sm btn-flat">
                        <i class="fa fa-search"></i> Search
                    </button>

                    <button type="button" class="btn btn-primary btn-sm btn-flat" data-toggle="modal" data-target="#addAppointmentModal">
                        <i class="fa fa-calendar-plus-o"></i> Add
                    </button>

                    <button type="submit" value="view_all" name="view_all" class="btn btn-warning btn-sm btn-flat">
                        <i class="fa fa-eye"></i> View All
                    </button>
                    <br><br>
                    {{-- <input type="date" name="date_filter" id="date_filter" class="form-control" value="{{ $date }}">
                    <button type="submit" class="btn btn-info btn-sm btn-flat">
                        <i class="fa fa-filter"></i> Filter
                    </button> --}}
                </div>
            </div>
            <h3>APPOINTMENTS</h3>
        </div>

        <!-- Table List -->
        <div class="box-body appointments">
            @if(count($appointment_schedule)>0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-fixed-header">
                        <tr class="bg-success bg-navy-active">
                            <th class="text-center">Date</th>
                            <th class="text-center">From</th>
                            <th class="text-center">To</th>
                            <th class="text-center">Created By</th>
                            <th class="text-center">Facility</th>
                            <th class="text-center">Department</th>
                            {{-- <th class="text-center">OPD Category</th> --}}
                            <th class="text-center">Available Doctor</th>
                            <!-- <th class="text-center">Slot</th> -->
                            <th class="text-center">Action</th>
                        </tr>
                        @foreach($appointment_schedule as $row)
                            <tr style="font-size: 12px">
                                <td> {{ $row->appointed_date }} </td>
                                <td> {{ $row->appointed_time }} </td>
                                <td> {{ $row->appointedTime_to }}</td>
                                <td> {{ $row->createdBy->username }} </td>
                                <td> {{ $row->facility->name }} </td>
                                <td> {{ $row->department->description }} </td>
                                {{-- <td> {{ $row->opdCategory }}</td> --}}
                                <td>
                                    <ul>
                                        @foreach($row->telemedAssignedDoctor as $doctorAssigned)
                                        <li>{{ 'Dr. '.$doctorAssigned->doctor->fname.' '.$doctorAssigned->doctor->lname }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <!-- <td> {{ count($row->telemedAssignedDoctor) }} </td> -->
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm" onclick="UpdateModal({{ $row->id }})"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="DeleteModal({{ $row->id }})"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="text-center">
                        {!! $appointment_schedule->links() !!}
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <span class="text-warning">
                        <i class="fa fa-warning"></i> No appointments schedule found!
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" role="dialog" id="addAppointmentModal" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="addAppointmentForm" action="{{ route('create-appointment') }}" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend><i class="fa fa-calendar-plus-o"></i> Add Appointment
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="label-border">
                                        <label for="appointed_date">Appointment Date:</label>
                                        <input type="date" class="form-control appointment_date" name="appointed_date" id="appointment_date" required>
                                        <input type="hidden" name="appointment_count" class="appointment_count" value="1">

                                        <label for="facility_id">Facility:</label>
                                        @foreach($facility as $Facility)
                                        <input type="text" class="form-control" name="facility_id" id="facility_id" value="{{ $Facility->facility->name }}" readonly>
                                        <input type="hidden" class="form-control" name="facility_id" id="id" value="{{ $Facility->facility->id }}" readonly>
                                        @endforeach

                                        <label for="department_id">Department:</label>
                                        <input type="text" class="form-control" name="department_id" id="department_id" value="OPD" readonly>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class ="time-input-group">
                                        <div class="label-border">
                                            <div id="opdCategoryContainer">
                                                <div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label for="appointed_time">Appointment Time:</label><br>
                                                            <div class="col-md-6">
                                                                <span>From:</span>
                                                                <input type="time" class="form-control" name="appointed_time1" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <span>To:</span>
                                                                <input type="time" class="form-control" name="appointed_time_to1" required>
                                                            </div>
                                                            <label for="opdCategory">OPD Category:</label>
                                                            <select class="form-control select2" name="opdCategory1" id="opdCategory" required>
                                                                <option selected value="">Select OPD Category</option>
                                                                <option value="Family Medicine">Family Medicine</option>
                                                                <option value="Internal Medicine">Internal Medicine</option>
                                                                <option value="General Surgery">General Surgery</option>
                                                                <option value="Trauma Care">Trauma Care</option>
                                                                <option value="Burn Care">Burn Care</option>
                                                                <option value="Ophthalmology">Ophthalmology</option>
                                                                <option value="Plastic and Reconstructive">Plastic and Reconstructive</option>
                                                                <option value="ENT">ENT</option>
                                                                <option value="Neurosurgery">Neurosurgery</option>
                                                                <option value="Urosurgery">Urosurgery</option>
                                                                <option value="Toxicology">Toxicology</option>
                                                                <option value="OB-GYNE">OB-GYNE</option>
                                                                <option value="Pediatric">Pediatric</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label>Available Doctor</label>
                                                        <select class="form-control select2 available_doctor1" name="available_doctor1[]" multiple="multiple" data-placeholder="Select Doctor" style="width: 100%;" required></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="additionalTimeContainer" style="display: none;"></div>
                                            <div style="margin-top: 15px;">
                                                <button type="button" class="btn btn-info btn-xs" id="add_slots" onclick="addTimeInput()">Add Appointment</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-send"></i> Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="appt_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><b>APPOINTMENT SCHEDULE</b></h4>
                </div>
                <div class="modal-body appt_body">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Appointment Modal -->
    <!-- old update -->
    <!-- My Update Appointment Version -->

    <div class="modal fade" role="dialog" id="updateConfirmationModal" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="addAppointmentForm" action="{{ route('update-appointment') }}" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend><i class="fa fa-calendar-plus-o"></i> Update Appointment
                                <button type="button" id ="closeUpdate" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="label-border">
                                        <label for="appointed_date">Appointment Date:</label>
                                        <input type="date" class="form-control appointment_date" id="appointed_date" name="appointed_date" required>
                                        <input type="hidden" name="appointment_count" class="appointment_count" value="1">

                                        <label for="facility_id">Facility:</label>
                                        @foreach($facility as $Facility)
                                        <input type="text" class="form-control" name="facility_id" id="facility_id" value="{{ $Facility->facility->name }}" readonly>
                                        <input type="hidden" class="form-control" name="facility_id" id="id" value="{{ $Facility->facility->id }}" readonly>
                                        @endforeach
                                        <label for="department_id">Department:</label>
                                        <input type="text" class="form-control" name="department_id" id="department_id" value="OPD" readonly>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="label-border">
                                        <div id="opdCategoryContainer">
                                          <div id="update_additionalTimeContainer" style="display: none;"></div>
                                                <input type="hidden" class="form-control selected_date" id="update_appointed_date" value="{{$row->appointed_date}}" required>
                                                <div style="margin-top: 15px;">
                                                    <button type="button" class="btn btn-info btn-sm" id="update_add_slots" data-appointed-date="{{$row->appointed_date}}" onclick="updateAddTimeInput()">Add Slot</button>
                                                </div>    
                                           </div>    
                                        </div>
                                        <!-- <div id="update_additionalTimeContainer" style="display: none;"></div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" id ="cancelUpdate" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm" onclick="updateAppointment()"><i class="fa fa-check"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<!-- End of Update Appointment Version -->
    <!-- Delete Appointment Modal -->
     <!-- remove old delete code -->
    <!--End Delete Appointment Modal -->

    <!-- Delete Appointment Modal -->
    <div class="modal fade" role="dialog" id="deleteConfirmationModal" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="addAppointmentForm" action="{{ route('delete-appointment') }}" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend style="color:red"><i class="fa fa-calendar-plus-o"></i> Delete Appointment
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="label-border">
                                        <label for="appointed_date">Appointment Date:</label>
                                        <input type="date" class="form-control appointment_date" id="delete_appointed_date" name="appointed_date" readonly>
                                        <input type="hidden" name="appointment_count" class="appointment_count" value="1">

                                        <label for="facility_id">Facility:</label>
                                        @foreach($facility as $Facility)
                                        <input type="text" class="form-control" name="facility_id" id="facility_id" value="{{ $Facility->facility->name }}" readonly>
                                        <input type="hidden" class="form-control" name="facility_id" id="id" value="{{ $Facility->facility->id }}" readonly>
                                        @endforeach
                                        <label for="department_id">Department:</label>
                                        <input type="text" class="form-control" name="department_id" id="department_id" value="OPD" readonly>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="label-border">
                                        <div id="opdCategoryContainer">
                                          <div id="delete_additionalTimeContainer" style="display: none;"></div>
                                                <div style="margin-top: 15px;">
                                                    <!-- <button type="button" class="btn btn-info btn-sm" id="update_add_slots" onclick="deleteTimeInput()">Add Slot</button> -->
                                                </div>    
                                           </div>    
                                        </div>
                                        <!-- <div id="update_additionalTimeContainer" style="display: none;"></div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"  id="cancelbtn-clear"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('js')
    <script>
        @if(Session::get('appointment_save'))
        Lobibox.notify('success', {
            title: "",
            msg: "Appointment Susccessfully Save!",
            size: 'mini',
            rounded: true
        });
        <?php Session::put('appointment_save',false); ?>
        @endif
        
        @if(Session::get('appointment_delete'))
        Lobibox.notify('success', {
            title: "",
            msg: "Appointment Susccessfully Deleted!",
            size: 'mini',
            rounded: true
        });
        <?php Session::put('appointment_delete',false); ?>
        @endif
        //----------------------------------------------------------------
        // function UpdateModal(appointmentId) {
        //remove the older update version
        // reset the delete modal if cancel or close it

        $(document).ready(function() {
            $('#deleteConfirmationModal').on('hidden.bs.modal', function () {
               location.reload();
            });
        });

        function UpdateModal(appointmentId) {
            $('#updateAppointmentId').val(appointmentId);
            console.log('sfdfssd',appointmentId);
            var url = "{{ url('display/appointment').'/'}}"+appointmentId;
           // url = url.replace(':id', appointmentId);

            $.get(url, function(data) {
                console.log('my appointed Id:asdasd',data);

                $('#update_additionalTimeContainer').empty();//to empty the previous  generate form
                if(data && data.length > 0){
                    data.forEach(function(appointment){   
                        // $('#update_department_id').val(appointment.department.description);
                        // $('#update_facility_id').val(appointment.facility_id).trigger('change');
                        $('#appointed_date').val(appointment.appointed_date);
                        let doctorAssigned = appointment.telemed_assigned_doctor[0].appointment_by;

                        updateAddTimeInput(appointment,doctorAssigned);
                    });
                }else{
                    console.log('No appointments found.');
                }
                

            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error: " + errorThrown);
            });
            $('#updateConfirmationModal').modal('show');
        }

        //-------------------End of my version UpdateModal

        //--------------------------------------------------------------

        function DeleteModal(appointmentId){

            var url = "{{ url('deleteSched/appointment').'/'}}"+appointmentId;
            $.get(url, function(data){
                
            let alertshown = false;
            let showdeletModal = true;

              if(data && data.length > 0){

                data.forEach(function(appointment){

                    let currentDate = new Date().toISOString().split('T')[0];

                    let available_doctor = appointment.telemed_assigned_doctor[0].appointment_by;

                    let appointedDate = appointment.appointed_date;
                    let date = new Date(appointedDate);
                    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

                    let month = monthNames[date.getMonth()];
                    let day = date.getDate();
                    let year = date.getFullYear();
                    let formattedDate = `${month} ${day}, ${year}`;
                    console.log("date format",formattedDate);
                    // if(currentDate <= appointment.appointed_date && !alertshown && available_doctor){
                    if(available_doctor && !alertshown){
                        // alert("Are you sure you want to delete this Present or Future Appointment?");
                        alertshown = true;
                        showdeletModal = false;
                        console.log('showdeletModal',showdeletModal);
                        Lobibox.alert("error",
                            {
                                msg: `You cannot delete this appointment. It has already been scheduled by the assigned doctor on ${formattedDate}.`
                            });
                        
                    }

                    $('#delete_appointed_date').val(appointment.appointed_date);
                   

                });
              
                if (showdeletModal) {

                    $('.time-input-group').empty();
                 
                    data.forEach(function(appointment) {
                        deleteTimeInput(appointment);  // Assuming deleteTimeInput appends new data to .time-input-group
                    });
                        
                    $('#deleteConfirmationModal').modal('show');

                }
            }
             // else {

            //     $('#deleteConfirmationModal').modal('hide');
                
            // }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error: " + errorThrown);
            });
            
        }
        //--------------------------------------------------------------
        function deleteAppointment() {
            var appointmentId = $('#deleteAppointmentId').val();

            $.ajax({
                type: 'POST',
                url: "{{ route('delete-appointment') }}",
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': appointmentId
                },
                success: function (data) {
                    //console.log(data);
                    $('#deleteConfirmationModal').modal('hide');

                    // Add auto-refresh after a successful deletion
                    setTimeout(function () {
                        location.reload();
                    }, 300);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("AJAX Error: " + errorThrown);
                }
            });
        }

        //--------------------------------------------------------------
        function onchangeUpdateDepartment(data){
            if(data.val()) {
                $.get("{{ url('department/get').'/' }}"+data.val(), function(result) {
                    //console.log('Department Data:', result);
                    $('#update_department_id').html('');
                    $('#update_department_id').append($('<option>', {
                        value: "",
                        text: "Select Department"
                    }));
                    // Use an object to store unique department IDs
                    var uniqueDepartments = {};
                    $.each(result, function(index, userData){
                        if (userData.department && userData.department.description && !uniqueDepartments[userData.department.id]) {
                            $('#update_department_id').append($('<option>', {
                                value: userData.department.id,
                                text: userData.department.description,
                            }));
                            // Mark department ID as visited to avoid duplicates
                            uniqueDepartments[userData.department.id] = true;
                        }
                    });
                });
            }
        }

        //--------------------------------------------------------------
        var query_doctor_store = [];
            $(document).ready(function() {
                var facility_id = $(`#id`).val();
                console.log(facility_id);
                if(facility_id) {
                    $.get("{{ url('get-doctors').'/' }}" + facility_id, function (result) {
                        query_doctor_store = result;
                        const current_appointment_count = $(".appointment_count").val();
                        for(var i=1; i<=current_appointment_count; i++) {
                            $(`.available_doctor${i}`).html('');
                            $(`.available_doctor${i}`).append($('<option>', {
                                value: "",
                                text: "Select Doctors"
                            }));
                            $.each(query_doctor_store, function (index, userData) {
                                $(`.available_doctor${i}`).append($('<option>', {
                                    value: userData.id,
                                    text: "Dr. "+userData.fname + ' ' + userData.lname
                                }));

                            });
                        }
                    });
                }
            });
        
        function addTimeInput(ok) {
            let currentCount = $(".appointment_count").val();
            $(".appointment_count").val(++currentCount);
            var timeInputGroup = $('<div class="time-input-group">');
            var additionalTimeInput = `<div class="label-border-time">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="appointed_time">Appointed Time:</label><br>
                                                    <div class="col-md-6">
                                                        <span>From:</span>
                                                        <input type="time" class="form-control" name="appointed_time${currentCount}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span>To:</span>
                                                        <input type="time" class="form-control" name="appointed_time_to${currentCount}" required>
                                                    </div>
                                                    <label for="opdCategory">OPD Category:</label>
                                                    <select class="form-control select2" name="opdCategory${currentCount}" required>
                                                        <option selected>Select OPD Category</option>
                                                        <option value="Family Medicine">Family Medicine</option>
                                                        <option value="Internal Medicine">Internal Medicine</option>
                                                        <option value="General Surgery">General Surgery</option>
                                                        <option value="Trauma Care">Trauma Care</option>
                                                        <option value="Burn Care">Burn Care</option>
                                                        <option value="Ophthalmology">Ophthalmology</option>
                                                        <option value="Plastic and Reconstructive">Plastic and Reconstructive</option>
                                                        <option value="ENT">ENT</option>
                                                        <option value="Neurosurgery">Neurosurgery</option>
                                                        <option value="Urosurgery">Urosurgery</option>
                                                        <option value="Toxicology">Toxicology</option>
                                                        <option value="OB-GYNE">OB-GYNE</option>
                                                        <option value="Pediatric">Pediatric</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <label>Available Doctor</label>
                                                <select class="form-control select2 available_doctor${currentCount}" name="available_doctor${currentCount}[]" multiple="multiple" data-placeholder="Select Doctor" style="width: 100%;" required></select>
                                            </div>
                                        </div>`;

            var deleteBtn = '<div><button type="button" class="btn btn-danger btn-sm delete-time-input" style="margin-top: 15px;"><span><i class="fa fa-trash"></i></span></button></div>';
            timeInputGroup.append(deleteBtn);
            timeInputGroup.append(additionalTimeInput);
            $('#additionalTimeContainer').append(timeInputGroup);

            timeInputGroup.find('.delete-time-input').on('click', function () {
                timeInputGroup.remove();
            });
            // Reinitialize the select2 plugin after adding a new element
            $(`.available_doctor${currentCount}`).select2();

            $('#additionalTimeContainer').show();
            $(document).ready(function() {
                $('.select2').select2();
                $.each(query_doctor_store, function (index, userData) {
                    $(`.available_doctor${currentCount}`).append($('<option>', {
                        value: userData.id,
                        text: "Dr. "+userData.fname + ' ' + userData.lname
                    }));
                });

                $('.select2').select2();
            });
        }

//-----------------My Append Update Edit appointment Time---------------------------//

    function updateAddTimeInput(appointment, assignedDoc) 
    {
            console.log('welcome appointment', appointment)
            let currentCount = $(".appointment_count").val();
            $(".appointment_count").val(++currentCount);
            var timeInputGroup = $('<div class="time-input-group">');
            var appointments = appointment ? appointment : '';
            // var doctorId = appointment.telemed_assign_doctor.map(doctorId=>doctorId.user.id);
            var appointmentDate = appointment && appointment.appointed_date ? appointment.appointed_date : '';
               if(appointment){
                var doctor = appointment.telemed_assigned_doctor;
                var selectId = "Update_available_doctor" + currentCount;
                var additionalTimeInput = `<div class="label-border-time">
                                                <div class="row">
                                                <input type="hidden" id="updateAppointmentId" name="update_appointment_date" value="${appointmentDate}" class="form-control">
                                                    <div class="col-md-12">
                                                    {{csrf_field()}}
                                                        <label for="appointed_time">
                                                        Appointed Time: </label>  
                                                           <span style="float: right;"> 
                                                                ${assignedDoc ? '<i class="fa fa-user" aria-hidden="true"></i> Assigned ': ''}
                                                            </span><br>
                                                        <input type="hidden" name="appointment_id${currentCount}" class="appointment_id" value="${appointment.id}">
                                                        <div class="col-md-6">
                                                            <span>From:</span>
                                                            <input type="time" class="form-control" id="update_appointed_time${currentCount}" name="update_appointed_time${currentCount}" value="${appointments.appointed_time}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span>To:</span>
                                                            <input type="time" class="form-control" id="update_appointedTime_to${currentCount}" name="update_appointed_time_to${currentCount}" value="${appointments.appointedTime_to}">
                                                        </div>
                                                        <label for="opdCategory">OPD Category:</label>
                                                        <select class="form-control select2" name="opdCategory${currentCount}" id="update_opdCategory${currentCount}">
                                                            <option selected>${appointments.opdCategory}</option>
                                                            <option value="Family Medicine">Family Medicine</option>
                                                            <option value="Internal Medicine">Internal Medicine</option>
                                                            <option value="General Surgery">General Surgery</option>
                                                            <option value="Trauma Care">Trauma Care</option>
                                                            <option value="Burn Care">Burn Care</option>
                                                            <option value="Ophthalmology">Ophthalmology</option>
                                                            <option value="Plastic and Reconstructive">Plastic and Reconstructive</option>
                                                            <option value="ENT">ENT</option>
                                                            <option value="Neurosurgery">Neurosurgery</option>
                                                            <option value="Urosurgery">Urosurgery</option>
                                                            <option value="Toxicology">Toxicology</option>
                                                            <option value="OB-GYNE">OB-GYNE</option>
                                                            <option value="Pediatric">Pediatric</option>
                                                        </select>
                                                    </div>
                                                   
                                                </div>
                                                <div>
                                                    <label>Available Doctor</label>
                                                    <select class="form-control select2 available_doctor${currentCount}" name="available_doctor${currentCount}[]" multiple="multiple" name="Update_available_doctor" id="Update_available_doctor${currentCount}" data-placeholder="Select Doctor" style="width: 100%;">
                                                      ${generateDoctorsOptions(doctor,appointments)}
                                                    </select>
                                                </div>
                                            </div>`;

                                        }else{
                                             //this will add a new form 
                                            var additionalTimeInput = `<div class="label-border-time">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="appointed_time">Appointed Time:</label><br>
                                                        <div class="col-md-6">
                                                            <span>From:</span>
                                                            <input type="time" class="form-control" name="appointed_time${currentCount}" id="empty_appointed_time" >
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span>To:</span>
                                                            <input type="time" class="form-control" name="appointed_time_to${currentCount}" id="empty_appointedTime_to">
                                                        </div>
                                                        <label for="opdCategory">OPD Category:</label>
                                                        <select class="form-control select" name="opdCategory${currentCount}" id="update_opdCategory">
                                                            <option selected></option>
                                                            <option value="Family Medicine">Family Medicine</option>
                                                            <option value="Internal Medicine">Internal Medicine</option>
                                                            <option value="General Surgery">General Surgery</option>
                                                            <option value="Trauma Care">Trauma Care</option>
                                                            <option value="Burn Care">Burn Care</option>
                                                            <option value="Ophthalmology">Ophthalmology</option>
                                                            <option value="Plastic and Reconstructive">Plastic and Reconstructive</option>
                                                            <option value="ENT">ENT</option>
                                                            <option value="Neurosurgery">Neurosurgery</option>
                                                            <option value="Urosurgery">Urosurgery</option>
                                                            <option value="Toxicology">Toxicology</option> 
                                                            <option value="OB-GYNE">OB-GYNE</option>
                                                            <option value="Pediatric">Pediatric</option>
                                                        </select>
                                                    </div>
                                                 
                                                </div>
                                                <div>
                                                    <label>Available Doctor</label>
                                                    <select class="form-control select2 available_doctors${currentCount}" name="available_doctor${currentCount}[]" multiple="multiple" id="update_available_doctor" data-placeholder="Select Doctor" style="width: 100%;">

                                                    </select>
                                                </div>
                                            </div>`;
                                        }       
                                 
            // Add the delete button
            var deleteBtn = '<div><button type="button" class="btn btn-danger btn-sm delete-time-input" style="margin-top: 15px;"><span><i class="fa fa-trash"></i></span></button></div>';
            if(currentCount > 2){
                timeInputGroup.append(deleteBtn);

            }                         
            console.log("currentCount",currentCount);

            timeInputGroup.append(additionalTimeInput);
            $('#update_additionalTimeContainer').append(timeInputGroup);
          
            let totalSlot = $('.time-input-group').length;

            $('#cancelUpdate, #closeUpdate').on('click', function() { // reset currentCount for closing the modal update
                currentCount = 1;
                $(".appointment_count").val(currentCount);
                console.log("it is work");
            });
          
            if(appointment){
                // console.log('timeInputGroup', timeInputGroup.length);
                timeInputGroup.find('.delete-time-input').on('click', function () {
                    console.log('my appointment delete', appointment);
                    var appoint_id = appointment.id;
                    var url = "{{ route('delete-timeSlot', ':id') }}";
                    url = url.replace(':id', appoint_id);
                    let doctorAssigned = appointment.telemed_assigned_doctor[0].appointment_by;
            
                    if(doctorAssigned){
                        Lobibox.alert("error",
                        {
                            msg: "You cannot delete this appointment. It has already been scheduled by the assigned doctor!"
                        });

                    }else{

                        Lobibox.confirm({
                            msg: "Are you sure you want to delete this time slot?",
                            callback: function ($this, type, ev) {
                                if(type == 'yes') {
                                        $.ajax({
                                        url: url,
                                        type: 'DELETE',
                                        data: {
                                            _token: $('meta[name="csrf-token"]').attr('content'), 
                                            id: appoint_id
                                        },
                                        success: function(message,appointedSlot){
                                            // alert("Time Slot Successfuly deleted!");
                                                Lobibox.alert("success",
                                                {
                                                    msg: "Time Slot Successfuly deleted!"
                                                });
                                                timeInputGroup.remove();
                                            
                                        },
                                        error: function(error) {
                                            alert('Error deleting appointment', error);
                                        }
                                    });
                                }
                            }
                        });

                    }

            });
            }else{
                timeInputGroup.find('.delete-time-input').on('click', function () {

                    timeInputGroup.remove();
                });
            }

            $('#update_additionalTimeContainer').show();
            $(document).ready(function() {
                $('.select2').select2();
                $.each(query_doctor_store, function (index, userData) {
                    $(`.available_doctors${currentCount}`).append($('<option>', {
                        value: userData.id,
                        text: userData.fname + ' ' + userData.lname
                    }));
                });
            });

            let existingTimeSlots = [];
            for(let i=2; i<=currentCount; i++) { 
                let existingFromTime = $(`#update_appointed_time${i}`).val();
                let existingToTime = $(`#update_appointedTime_to${i}`).val();

                if(existingFromTime && existingToTime){
                    existingTimeSlots.push({
                        from: new Date("2000-01-01T" + existingFromTime),
                        to: new Date("2000-01-01T" + existingToTime)
                    })
                }
            }
            console.log("hass",existingTimeSlots );
            function hasTimeConflict(newfromTime, newToTime) {
                for(let slot of existingTimeSlots){
                    if(newfromTime < slot.to && newToTime > slot.from){
                        return true;
                    }
                }
                return false;
            }

            $(document).on('change', `#empty_appointed_time, #empty_appointedTime_to`, function () {
                let newfromTime = new Date("2000-01-01T" + $('#empty_appointed_time').val());
                let newTotime = new Date("2000-01-01T" + $('#empty_appointedTime_to').val());

                if(hasTimeConflict(newfromTime, newTotime)){
                    Lobibox.alert("error",
                            {
                                msg: `Time conflict detected! Please choose a different time slot.`
                            });
                    $('#empty_appointed_time').val('');
                    $('#empty_appointedTime_to').val('');
                }

                if(newTotime <= newfromTime){

                    $('#empty_appointed_time').val('');
                    $('#empty_appointedTime_to').val('');
                    Lobibox.alert("error",
                        {
                            msg: `End time must be after start time.`
                        });
                }
            });

            if(assignedDoc){
                document.getElementById('appointed_date').disabled = true; //disabled the date if it is already assigned
            }else{
                document.getElementById('appointed_date').disabled = false;
            }

            const update_slots = document.getElementById('update_add_slots');
            const appointedDate = appointment.appointed_date;
            
            if(appointedDate){ // hide Add slot button if past date
                const currentDate = new Date();
                const appointmentDate = new Date(appointedDate);

                currentDate.setHours(0, 0, 0, 0);
                appointmentDate.setHours(0, 0, 0, 0);

                console.log("appointmentDate",  appointedDate);
                

                if(appointmentDate < currentDate){ //disabled all slot if it is already in the past date
                    update_slots.style.display = 'none';
                    document.getElementById('appointed_date').disabled = true;
                    if(appointmentDate <= currentDate){
                        document.getElementById('update_appointed_time' + currentCount).disabled = true;
                        document.getElementById('update_appointedTime_to' + currentCount).disabled = true;
                        document.getElementById('update_opdCategory' + currentCount).disabled = true;
                        document.getElementById('Update_available_doctor' + currentCount).disabled = true;
                    }
                    document.getElementById('update_appointed_time' + currentCount).disabled = true;
                    document.getElementById('update_appointedTime_to' + currentCount).disabled = true;
                    document.getElementById('update_opdCategory' + currentCount).disabled = true;
                    document.getElementById('Update_available_doctor' + currentCount).disabled = true;
                }else{
                    update_slots.style.display = 'block';
                }
            }


    }
        
  

//---------------- Ending My Append Update Edit appointment Time---------------------//


function deleteTimeInput(appointment){
    
    let currentCount = $(".appointment_count").val();
    $(".appointment_count").val(++currentCount);
    console.log("welcome Appointment:", appointment);
    var appointments = appointment ? appointment : '';
    var doctor = appointment.telemed_assigned_doctor;
    var timeInputGroup = $('<div class="time-input-group">');
    var additionalTimeInput = 
        `<div class="label-border-time">
            <div class="row">
                <div class="col-md-12">
                {{csrf_field()}}
                    <label for="appointed_time">Appointed Time:</label><br>
                    <input type="hidden" name="appointment_id${currentCount}" class="appointment_id" value="${appointments.id}">
                    <div class="col-md-6">
                        <span>From:</span>
                        <input type="time" class="form-control" id="update_appointed_time" name="update_appointed_time${currentCount}" value="${appointments.appointed_time}" readonly>
                    </div>
                    <div class="col-md-6">
                        <span>To:</span>
                        <input type="time" class="form-control" id="update_appointedTime_to" name="update_appointed_to${currentCount}" value="${appointments.appointedTime_to}" readonly>
                    </div>
                    <label for="opdCategory">OPD Category:</label>
                    <select class="form-control select2" name="opdCategory${currentCount}" id="update_opdCategory" disabled>
                        <option selected>${appointments.opdCategory}</option>
                    </select>
                </div>
                
            </div>
            <div>
                <label>Available Doctor</label>
                <select class="form-control select2 available_doctor${currentCount}" name="available_doctor${currentCount}" multiple="multiple" name="Update_available_doctor" id="Update_available_doctor" data-placeholder="Select Doctor" style="width: 100%;" disabled>
                        ${generateDoctorsOptions(doctor,appointments)}
                </select>
            </div>
        </div>`;
        
        timeInputGroup.append(additionalTimeInput);
        $('#delete_additionalTimeContainer').append(timeInputGroup);
        $('#delete_additionalTimeContainer').show();


        $(document).ready(function() {
                $('.select2').select2();
                // $.each(query_doctor_store, function (index, userData) {
                //     $(`.available_doctors${currentCount}`).append($('<option>', {
                //         value: userData.id,
                //         text: userData.lname + '' + userData.lname
                //     }));
                // });
            });  
}

    $(document).ready(function() {
        $('#cancelbtn-clear').on('click', function() {
             $('#delete_additionalTimeContainer').empty();
            console.log("it works");
        });
    });
    function generateDoctorsOptions(doctorData){
        var options = '';
        if(Array.isArray(doctorData)){
            doctorData.forEach(function (doctor){
            console.log("doctorData::", doctor.doctor.id);
            options += `<option value="${doctor.doctor.id}" selected>${doctor.doctor.fname} ${doctor.doctor.lname}</option>`;
            });
        }else if(typeof doctorData === 'object' && doctorData !== null){
        options += `<option value="${doctorData.id}" selected>${doctorData.fname} ${doctorData.lname}</option>`;
        }
        return options;
    }
    //----------------------Trapping Appointment Time From and Time To and Date----------------------------//    
    const today = new Date().toISOString().split('T')[0]; // i add this for disabled the past date in appointment date
    document.querySelector('input[name="appointed_date"]').setAttribute('min', today);
    console.log('today', today);

    $(document).ready(function() {
        $.ajax({
            url: "{{ route('get-booked-dates') }}",
            type: 'GET',
            success: function(response){
                //  console.log("response", response);

                $(function() {
                    $('#appointment_date').on('input', function() { 
                        var selectedDate = $(this).val();
                        if (response.includes(selectedDate)) {
                            $(this).val('');
                            $(this).addClass('already-appointed');
                            alert('This date is Already Appointed. Please select another date.');
                        }else{
                            $(this).removeClass('already-appointed');
                        }
                    });
                });
    
            },
            error: function(xhr, status, error){
                console.error(xhr, responseText);
            }
        });
    });

    $(document).ready(function() {

        var allAppointmentTimes = [];
        var currentCounts = 1;

        function checkConflicts() {
            for (let i = 0; i < allAppointmentTimes.length; i++) {
                for (let j = i + 1; j < allAppointmentTimes.length; j++) {
                    const slot1 = allAppointmentTimes[i];
                    const slot2 = allAppointmentTimes[j];

                    if (!slot1 || !slot2) continue;

                    const timeOverlap = (slot1.from < slot2.to && slot1.to > slot2.from);
                    const doctorsOverlap = Array.isArray(slot1.doctors) && Array.isArray(slot2.doctors) &&
                                        slot1.doctors.some(doctor => slot2.doctors.includes(doctor));

                    if (timeOverlap && doctorsOverlap) {
                        return true; // Conflict found
                    }
                }
            }
            return false; // No conflicts
        }

        function clearSelectElement(selectElement) {
            selectElement.find('option:selected').prop('selected', false);
            selectElement.trigger('change');
        }

            
        $(document).on('change', 'input[type="time"]', function() {

            var UpdateDate = $("#updateAppointmentId").val();
            var appointmentDate = $("#appointment_date").val().trim();
            
            var timeInputGroup = $(this).closest('.time-input-group');
            var index = $('.time-input-group').index(timeInputGroup);
            currentCounts = index + 1;
            var fromInput = timeInputGroup.find('input[name^="appointed_time' + currentCounts + '"]');
            var toInput = timeInputGroup.find('input[name^="appointed_time_to' + currentCounts + '"]');
            var selectedDoctors = $(`.available_doctor${currentCounts}`).val();

            var fromTime = fromInput.val();
            var toTime = toInput.val();

            var fromTimeObj = new Date(appointmentDate + "T" + fromTime);
            var toTimeObj = new Date(appointmentDate + "T" + toTime);
            // console.log("toTimeObj", fromTime);
            
            var isUnique = true;
            var timeObject = {
                from: fromTimeObj,
                to: toTimeObj,
                doctors: selectedDoctors
            };
    
                if(UpdateDate || appointmentDate){
                    
                }else{
                    if(!appointmentDate || !UpdateDate){
                        alert('Please select Date first!');
                        fromInput.val('');
                        toInput.val('');
                    }
                }
            

            if(!Array.isArray(allAppointmentTimes)){
                allAppointmentTimes = [];
            }

            for (var i =0; i < allAppointmentTimes.length; i++){
                var existingTime = allAppointmentTimes[i];
   
                var timeOverlap = (timeObject.from >= existingTime.from && timeObject.from < existingTime.to) ||(timeObject.to > existingTime.from && timeObject.to <= existingTime.to) ||
                                    (timeObject.from <= existingTime.from && timeObject.to >= existingTime.to)
                
                var doctorsOverlap = Array.isArray(existingTime.doctors) && Array.isArray(timeObject.doctors) && 
                                    existingTime.doctors.some(doctor => timeObject.doctors.includes(doctor));

                if (timeOverlap && doctorsOverlap && i !== index) {
                    isUnique = false;
                    break;
                }
            }

            if(!isUnique) {
                alert('Appointment time and Assigned Doctor is already taken');
                fromInput.val('');
                toInput.val('');
                return;
            }

            if (toTimeObj <= fromTimeObj) {

                alert('End time must be after start time');
                toInput.val('');
                return;
            }

            const now = new Date();
            const nowDate = now.toISOString().split('T')[0];
            //  console.log("appointment Date", appointmentDate);
            //  console.log("nowDate", nowDate);
            if(appointmentDate === nowDate && fromTimeObj < now){
                
                if(fromTimeObj < now){
                    Lobibox.alert("error",
                    {
                        msg: "Appointment Time cannot be in the past"
                    });
                    toInput.val('');
                    fromInput.val('');
                }
            }

            // If unique, add or update the time slot
            allAppointmentTimes[index] = timeObject;
            allAppointmentTimes = allAppointmentTimes.filter(appointment =>appointment.to instanceof Date && !isNaN(appointment.to));

            
            console.log("selectedDoctors", timeObject);
            console.log('slot', index);

            $('input[name="appointed_date"]').data('fromTimeObj', fromTimeObj);
            $('input[name="appointed_date"]').data('Totime', toTimeObj);

            $('input[name="appointed_date"]').trigger('change');
        });  
           
        $(document).on('change', '[name^="available_doctor"]', function () {
            var selectedDoctors = $(this).val();
            var selectedDoctorsElement = $(this);

            var timeInputGroup = $(this).closest('.time-input-group');
            var index = $('.time-input-group').index(timeInputGroup);

            if(allAppointmentTimes[index]){
                // Update the doctors field in the specific slot
                allAppointmentTimes[index].doctors = selectedDoctors;

                if (checkConflicts()) {

                    alert('Appointment time and Assigned Doctor is already taken');
                    
                    clearSelectElement(selectedDoctorsElement);
                    allAppointmentTimes[index].doctors = []; // Clear the doctors for this slot
                }
            }   

            console.log('slot', index);
            console.log("Updated appointment slot", allAppointmentTimes[index]);
        });

    });   
        
 //----------------------End Trapping Appointment Time From and Time To----------------------------// 
    
        @if(Session::get('appt_notif'))
        Lobibox.notify('success', {
            title: "",
            msg: "<?php echo Session::get("appt_msg"); ?>",
            size: 'mini',
            rounded: true
        });
        <?php
        Session::put("appt_notif",false);
        Session::put("appt_msg",false)
        ?>
        @endif

        function ApptBody(id){
            $('.appt_body').html(loading);
            var json = {
                "id" : id,
                "_token" : "<?php echo csrf_token()?>"
            };
            var url = "<?php echo asset('admin/appointment/details') ?>";
            $.post(url,json,function(response){
                $('.appt_body').html(response);
            });
        }
        //------------------------
    </script>
@endsection
