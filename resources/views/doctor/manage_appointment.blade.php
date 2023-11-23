@extends('layouts.app')

@section('content')
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
    </style>

    <!-- Add Modal -->
    <div class="modal fade" role="dialog" id="addAppointmentModal" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('create-appointment') }}" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend><i class="fa fa-calendar-plus-o"></i> Add Appointment
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <label for="appointed_date">Appointed Date:</label>
                            <input type="date" class="form-control" name="appointed_date" required>
                            <label for="appointed_time">Appointed Time:</label>
                            <input type="time" class="form-control" name="appointed_time" required>

                           {{-- <label for="created_by">Created By:</label>
                            <input type="number" class="form-control" name="created_by" required>--}}

                            <label for="facility_id">Facility:</label>
                            <select class="form-control select2" name="facility_id" id="facility_id" onchange="onchangeDepartment($(this))">
                                <option selected>Select Facility</option>
                                @foreach($facility as $Facility)
                                    <option value="{{ $Facility->id }}">{{ $Facility->name }}</option>
                                @endforeach
                            </select>

                            <label for="department_id">Department:</label>
                            <select class="form-control select2" name="department_id" id="department_id">
                                <option selected>Select Department</option>
                                <option value=""></option>
                            </select>

                            {{--<label for="appointed_by">Appointed By:</label>
                            <input type="number" class="form-control" name="appointed_by" required>
                            <label for="code">Code:</label>
                            <input type="text" class="form-control" name="code" required>
                            <label for="status">Status:</label>
                            <input type="text" class="form-control" name="status" required>--}}

                            <label for="slot">Slot:</label>
                            <input type="number" class="form-control" name="slot" required>
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
                    {{--<a href="{{ asset('admin/appointment/export') }}" class="btn btn-danger btn-sm btn-flat" target="_blank">
                        <i class="fa fa-file-excel-o"></i> Export
                    </a>--}}
                    <br><br>
                    {{--<select class="form-control select" id="status_filter" name="status_filter">
                        <option value="">Select status...</option>
                        <option value="new" @if($status == "new") selected @endif> New </option>
                        <option value="seen" @if($status == 'seen') selected @endif> Seen </option>
                        <option value="ongoing" @if($status == 'ongoing') selected @endif> Ongoing </option>
                        <option value="resolved" @if($status == 'resolved') selected @endif> Resolved </option>
                    </select>--}}
                    <input type="date" name="date_filter" id="date_filter" class="form-control" value="{{ $date }}">
                    <button type="submit" class="btn btn-info btn-sm btn-flat">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>
            </div>
            <h3>APPOINTMENTS {{--<small>{{ $appointment_schedule }}</small>--}}</h3>

        </div>
        <div class="box-body appointments">
            @if(count($appointment_schedule)>0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-fixed-header">
                    <tr class="bg-success bg-navy-active">
                        {{--<th class="text-center">Id</th>--}}
                        <th class="text-center">Date</th>
                        <th class="text-center">Time</th>
                        <th class="text-center">Created By</th>
                        <th class="text-center">Facility</th>
                        <th class="text-center">Department</th>
                        <th class="text-center">Appointed By</th>
                        <th class="text-center">Code</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Slot</th>
                        <th class="text-center">Action</th>
                    </tr>
                    @foreach($appointment_schedule as $row)
                        <tr style="font-size: 12px">
                            {{--<td style="white-space: nowrap;">
                                <b>
                                    <a href="#appt_modal" data-toggle="modal" onclick="ApptBody('{{ $row->id }}')">
                                    {{ $row->appointed_date }}
                                    </a>
                                </b>
                            </td>--}}
                           {{-- <td>{{ $row->id }}</td>--}}
                            <td> {{ $row->appointed_date }} </td>
                            <td> {{ $row->appointed_time }} </td>
                            <td> {{ $row->createdBy->username }} </td>
                            <td> {{ $row->facility->name }} </td>
                            <td> {{ $row->department->description }} </td>
                            <td> {{ $row->appointed_by }} </td>
                            <td> {{ $row->code }} </td>
                            <td> {{ $row->status }} </td>
                            <td> {{ $row->slot }} </td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="EditModal({{ $row->id }})"><i class="fa fa-pencil"></i></button>
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

    <div class="modal fade" role="dialog" id="appt_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><b>APPOINTMENT SCHEDULE</b></h4>
                </div>
                <div class="modal-body appt_body">
                </div><!-- /.modal-body -->
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close </button>
                </div>
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Edit Appointment Modal -->
    <div class="modal fade" role="dialog" id="editAppointmentModal" data-backdrop="static" data-keyboard="false" aria-labelledby="editAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="editAppointmentForm" method="post" action="{{ route('update-appointment') }}">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend><i class="fa fa-edit"></i> Edit Appointment
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <input type="hidden" name="appointment_id" id="editAppointmentId" value="" class="form-control">
                            <label for="edit_appointed_date">Appointed Date:</label>
                            <input type="date" class="form-control" name="edit_appointed_date" id="edit_appointed_date" required>
                            <label for="edit_appointed_time">Appointed Time:</label>
                            <input type="time" class="form-control" name="edit_appointed_time" id="edit_appointed_time" required>

                           {{-- <label for="edit_created_by">Created By:</label>
                            <input type="number" class="form-control" name="edit_created_by" id="edit_created_by" required>--}}

                            <label for="edit_facility_id">Facility:</label>
                            <select class="form-control select2" name="edit_facility_id" id="edit_facility_id">
                                <option value="" selected>Select Facility</option>
                                @foreach($facilityList as $facility)
                                    {{--<option value="{{ $facility->id }}">{{ $facility->name }}</option>--}}
                                    <option value="{{ $facility->id }}" {{ $facility->id == $row->facility_id ? 'selected' : '' }}>{{ $facility->name }}</option>
                                @endforeach
                            </select>

                            <label for="edit_department_id">Department:</label>
                            <select class="form-control select2" name="edit_department_id" id="edit_department_id">
                                <option value="" selected>Select Department</option>
                                @foreach($departmentList as $department)
                                    {{--<option value="{{ $department->id }}">{{ $department->description }}</option>--}}
                                    <option value="{{ $department->id }}" {{ $department->id == $row->department_id ? 'selected' : '' }}>{{ $department->description }}</option>
                                @endforeach
                            </select>

                           {{-- <label for="edit_appointed_by">Appointed By:</label>
                            <input type="number" class="form-control" name="edit_appointed_by" id="edit_appointed_by" required>
                            <label for="edit_code">Code:</label>
                            <input type="text" class="form-control" name="edit_code" id="edit_code" required>
                            <label for="edit_status">Status:</label>
                            <input type="text" class="form-control" name="edit_status" id="edit_status" required>--}}

                            <label for="edit_slot">Slot:</label>
                            <input type="number" class="form-control" name="edit_slot" id="edit_slot" required>
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
    <div class="modal fade" role="dialog" id="deleteConfirmationModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
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

                            <label for="del_appointed_time">Appointed Time:</label>
                            <input type="time" class="form-control" name="del_appointed_time" id="del_appointed_time" readonly>

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

        function onchangeDepartment(data) {
            if(data.val()) {
                $.get("{{ url('department/get').'/' }}"+data.val(), function(result) {
                    //console.log('Department Data:', result);
                    $('#department_id').html('');
                    $('#department_id').append($('<option>', {
                        value: "",
                        text: "Select Department"
                    }));
                    // Use an object to store unique department IDs
                    var uniqueDepartments = {};
                    $.each(result, function(index, userData){
                        if (userData.department && userData.department.description && !uniqueDepartments[userData.department.id]) {
                            $('#department_id').append($('<option>', {
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

        //--------------------------------------------------------------
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
        function EditModal(appointmentId) {
            $('#editAppointmentId').val(appointmentId);

            var url = "{{ route('get-appointment-data', ':id') }}";
            url = url.replace(':id', appointmentId);

            $.get(url, function(data) {
                console.log(data);
                $('#edit_appointed_date').val(data.appointed_date);
                $('#edit_appointed_time').val(data.appointed_time);
                $('#edit_created_by').val(data.created_by);
                /*$('#edit_facility_id').val(data.facility_id);
                $('#edit_department_id').val(data.department_id);*/
                $('#edit_appointed_by').val(data.appointed_by);
                $('#edit_code').val(data.code);
                $('#edit_status').val(data.status);
                $('#edit_slot').val(data.slot);

                // Use this
                $('#edit_facility_id').prop('selectedIndex', data.facility_id);
                $('#edit_department_id').prop('selectedIndex', data.department_id);


            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error: " + errorThrown);
            });

            $('#editAppointmentModal').modal('show');
        }
        //--------------------------------------------------------------
        /*function EditModal(appointmentId) {
            $('#editAppointmentId').val(appointmentId);

            var url = "{{ route('get-appointment-data', ':id') }}";
            url = url.replace(':id', appointmentId);

            // Create a promise for the AJAX call
            var getDataPromise = new Promise(function(resolve, reject) {
                $.get(url, function(data) {
                    resolve(data);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    reject(errorThrown);
                });
            });

            // Use the promise to handle the data
            getDataPromise.then(function(data) {
                console.log(data);
                // Populate the modal fields with the retrieved data
                $('#edit_appointed_date').val(data.appointed_date);
                $('#edit_appointed_time').val(data.appointed_time);
                $('#edit_created_by').val(data.created_by);

                // Set the selected option for Facility dropdown
                var facilitySelect = $('#edit_facility_id');
                facilitySelect.empty(); // Clear existing options
                facilitySelect.append('<option value="" selected>Select Facility</option>');
                @foreach($facilityList as $facility)
                (function() {
                    var facilityId = {{ $facility->id }};
                    facilitySelect.append('<option value="' + facilityId + '" ' + (facilityId == data.facility_id ? 'selected' : '') + '>{{ $facility->name }}</option>');
                })();
                @endforeach

                // Set the selected option for Department dropdown
                var departmentSelect = $('#edit_department_id');
                departmentSelect.empty(); // Clear existing options
                departmentSelect.append('<option value="" selected>Select Department</option>');
                @foreach($departmentList as $department)
                (function() {
                    var departmentId = {{ $department->id }};
                    departmentSelect.append('<option value="' + departmentId + '" ' + (departmentId == data.department_id ? 'selected' : '') + '>{{ $department->description }}</option>');
                })();
                @endforeach

                $('#edit_appointed_by').val(data.appointed_by);
                $('#edit_code').val(data.code);
                $('#edit_status').val(data.status);
                $('#edit_slot').val(data.slot);

                // Show the modal after the data is populated
                $('#editAppointmentModal').modal('show');
            }).catch(function(error) {
                console.log("AJAX Error: " + error);
            });
        }*/


        //--------------------------------------------------------------

        function updateAppointment() {
            var appointmentId = $('#editAppointmentId').val();
            var appointedDate = $('#edit_appointed_date').val();
            var appointedTime = $('#edit_appointed_time').val();
            var appointmentCreatedBy = $('#edit_created_by').val();
            var appointmentFacilityId = $('#edit_facility_id').val();
            var appointmentDepartmentId = $('#edit_department_id').val();
            var appointmentAppointedBy = $('#edit_appointed_by').val();
            var appointmentCode = $('#edit_code').val();
            var appointmentStatus = $('#edit_status').val();
            var appointmentSlot = $('#edit_slot').val();

            var url = "{{ route('update-appointment') }}";
            var data = {
                _token: "{{ csrf_token() }}",
                id: appointmentId,
                appointed_date: appointedDate,
                appointed_time: appointedTime,
                created_by: appointmentCreatedBy,
                facility_id: appointmentFacilityId,
                department_id: appointmentDepartmentId,
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
        }

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





    </script>
@endsection

