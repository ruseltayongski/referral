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
                                        <input type="date" class="form-control" name="appointed_date" required>
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
                                            <button type="button" class="btn btn-info btn-sm" id="add_slots" onclick="addTimeInput()">Add Appointment</button>
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
    <div class="modal fade" role="dialog" id="updateConfirmationModal" data-backdrop="static" data-keyboard="false" aria-labelledby="updateConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="updateAppointmentForm" method="post" action="{{ route('update-appointment') }}">
                        {{ csrf_field() }}
                        <legend><i class="fa fa-edit"></i> Edit Appointment
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </legend>
                        <div class="form-group">
                            <input type="hidden" name="update_appointment_id" id="updateAppointmentId" value="" class="form-control">

                            <label for="update_appointed_date">Appointed Date:</label>
                            <input type="date" class="form-control" name="update_appointed_date" id="update_appointed_date">

                            <label for="update_appointed_time">Appointed Time:</label><br>
                            <span>From: </span>
                            <input type="time" class="form-control" name="update_appointed_time" id="update_appointed_time">
                            <span> To: </span>
                            <input type="time" class="form-control" name="update_appointedTime_to" id="update_appointedTime_to">

                            <label for="update_facility_id">Facility:</label>
                            <select class="form-control" name="update_facility_id" id="update_facility_id" onchange="onchangeUpdateDepartment($(this))" required>
                                <!-- <select class="form-control" name="update_facility_id" id="update_facility_id"> -->
                                @foreach($facilityList as $facility)
                                    <option value="{{ $facility->id }}" selected>{{ $facility->name }}</option>
                                @endforeach
                            </select>
                            <label for="update_department_id">Department:</label>
                            <select class="form-control" name="update_department_id" id="update_department_id" required>
                                @foreach($departmentList as $department)
                                    <option value="{{ $department->id }}">{{ $department->description }}</option>
                                @endforeach
                            </select>
                            <label for="update_opdCategory">OPD Category:</label>
                            <select class="form-control" name="update_opdCategory" id="update_opdCategory" required>
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

                            <!-- <label for="update_slot">Slot:</label>
                            <input type="number" class="form-control" name="update_slot" id="update_slot" required> -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm" onclick="updateAppointment()"><i class="fa fa-check"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Appointment Modal -->
    <div class="modal fade" role="dialog" id="deleteConfirmationModal" data-backdrop="static" data-keyboard="false" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="deleteAppointmentForm" method="post" action="{{ route('delete-appointment') }}">
                        {{ csrf_field() }}
                        <fieldset>
                            <h5><strong><p>Are you sure you want to delete this appointment?</p></strong></h5>
                        </fieldset>
                        <div class="form-group">
                            <input type="hidden" name="appointment_id" id="deleteAppointmentId" value="" class="form-control" readonly>

                            <label for="del_appointed_date">Appointed Date:</label>
                            <input type="date" class="form-control" name="del_appointed_date" id="del_appointed_date" readonly>

                            <label for="del_appointed_time">Appointed Time:</label><br>
                            <span>From:</span>
                            <input type="time" class="form-control" name="del_appointed_time" id="del_appointed_time" readonly>
                            <span>To:</span>
                            <input type="time" class="form-control" name="del_appointedTime_to" id="del_appointedTime_to" readonly>

                            <label for="del_created_by">Created By:</label>
                            <input type="text" class="form-control" name="del_created_by" id="del_created_by" readonly>

                            <label for="del_facility_id">Facility:</label>
                            <select class="form-control" name="del_facility_id" id="del_facility_id" disabled>
                                @foreach($facilityList as $facility)
                                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                @endforeach
                            </select>

                            <label for="del_department_id">Department:</label>
                            <select class="form-control" name="del_department_id" id="del_department_id" disabled>
                                @foreach($departmentList as $department)
                                    <option value="{{ $department->id }}">{{ $department->description }}</option>
                                @endforeach
                            </select>

                            {{--<label for="del_appointed_by">Appointed By:</label>
                            <input type="number" class="form-control" name="del_appointed_by" id="del_appointed_by" readonly>--}}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteAppointment()">
                                <i class="fa fa-trash"></i> Confirm Delete
                            </button>
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
        
        //----------------------------------------------------------------
        function UpdateModal(appointmentId) {
            $('#updateAppointmentId').val(appointmentId);

            var url = "{{ route('get-appointment-data', ':id') }}";
            url = url.replace(':id', appointmentId);

            $.get(url, function(data) {
                console.log(data);

                $('#update_appointed_date').val(data.appointed_date);
                $('#update_appointed_time').val(data.appointed_time);
                $('#update_appointedTime_to').val(data.appointedTime_to);
                $('#update_created_by').val(data.created_by);
                $('#update_facility_id').val(data.facility_id);
                $('#update_department_id').val(data.department_id);
                $('#update_opdCategory').val(data.opdCategory);
                $('#update_appointed_by').val(data.appointed_by);
                $('#update_code').val(data.code);
                $('#update_status').val(data.status);
                $('#update_slot').val(data.slot);

            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error: " + errorThrown);
            });

            $('#updateConfirmationModal').modal('show');
        }

        //--------------------------------------------------------------
        function updateAppointment() {
            var appointmentId = $('#updateAppointmentId').val();
            var appointedDate = $('#update_appointed_date').val();
            var appointedTime = $('#update_appointed_time').val();
            var appointedTo = $('#update_appointedTime_to').val();
            var appointmentCreatedBy = $('#update_created_by').val();
            var appointmentFacilityId = $('#update_facility_id').val();
            var appointmentDepartmentId = $('#update_department_id').val();
            var appointmentOpdCategory = $('#update_opdCategory').val();
            var appointmentAppointedBy = $('#update_appointed_by').val();
            var appointmentCode = $('#update_code').val();
            var appointmentStatus = $('#update_status').val();
            var appointmentSlot = $('#update_slot').val();

            var url = "{{ route('update-appointment') }}";
            var data = {
                _token: "{{ csrf_token() }}",
                id: appointmentId,
                appointed_date: appointedDate,
                appointed_time: appointedTime,
                appointedTime_to: appointedTo,
                created_by: appointmentCreatedBy,
                facility_id: appointmentFacilityId,
                department_id: appointmentDepartmentId,
                opdCategory: appointmentOpdCategory,
                appointed_by: appointmentAppointedBy,
                code: appointmentCode,
                status: appointmentStatus,
                slot: appointmentSlot
            };

            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function(response) {
                    $('#editAppointmentModal').modal('hide');
                    alert(response.message);
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error("AJAX Request Failed: " + errorThrown);
                }
            });
        }

        //--------------------------------------------------------------
        function DeleteModal(appointmentId) {
            $('#deleteAppointmentId').val(appointmentId);

            var url = "{{ route('get-appointment-data', ':id') }}";
            url = url.replace(':id', appointmentId);

            $.get(url, function(data) {
                //console.log(data);
                $('#del_appointed_date').val(data.appointed_date);
                $('#del_appointed_time').val(data.appointed_time);
                $('#del_appointedTime_to').val(data.appointedTime_to);
                $('#del_created_by').val(data.created_by);
                $('#del_facility_id').val(data.facility_id);
                $('#del_department_id').val(data.department_id);
                $('#del_appointed_by').val(data.appointed_by);
                var createdByUserId = data.created_by;
                $.get("{{ route('get-user-data', ':id') }}".replace(':id', createdByUserId), function(user) {
                    $('#del_created_by').val(user.username);
                });

            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error: " + errorThrown);
            });

            $('#deleteConfirmationModal').modal('show');

            //------------------------------------
           
            //------------------------------------
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

            $('#additionalTimeContainer').show();
            $(document).ready(function() {
                $('.select2').select2();
                $.each(query_doctor_store, function (index, userData) {
                    $(`.available_doctor${currentCount}`).append($('<option>', {
                        value: userData.id,
                        text: "Dr. "+userData.fname + ' ' + userData.lname
                    }));
                });
            });
        }


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
        //--------------------------------------------------------------

    </script>
@endsection
