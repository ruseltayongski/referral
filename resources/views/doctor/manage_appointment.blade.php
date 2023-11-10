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


    <!-- Modal -->
    <div class="modal fade" role="dialog" id="addAppointmentModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <form action="{{ route('create-appointment') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-body">
                        <fieldset>
                            <legend><i class="fa fa-calendar-plus-o"></i> Add Appointment</legend>
                        </fieldset>
                        <div class="form-group">
                            <label for="appointed_date">Appointed Date:</label>
                            <input type="date" class="form-control" name="appointed_date" required>

                            <label for="appointed_time">Appointed Time:</label>
                            <input type="time" class="form-control" name="appointed_time" required>

                            <label for="created_by">Created By:</label>
                            <input type="number" class="form-control" name="created_by" required>

                            <label for="facility_id">Facility:</label>
                            <input type="number" class="form-control" name="facility_id" required>

                            <label for="department_id">Department:</label>
                            <input type="number" class="form-control" name="department_id" required>

                            <label for="appointed_by">Appointed By:</label>
                            <input type="number" class="form-control" name="appointed_by" required>

                            <label for="code">Code:</label>
                            <input type="text" class="form-control" name="code" required>

                            <label for="status">Status:</label>
                            <input type="text" class="form-control" name="status" required>

                            <label for="slot">Slot:</label>
                            <input type="number" class="form-control" name="slot" required>
                        </div>
                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="resetSignatureField()"><i class="fa fa-times"></i> Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-send"></i> Submit</button>
                    </div>
                </div>
            </form>
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
                            <td style="white-space: nowrap;">
                                <b>
                                    <a href="#appt_modal" data-toggle="modal" onclick="ApptBody('{{ $row->id }}')">
                                    {{ $row->appointed_date }} <!-- Display the date or other relevant field -->
                                    </a>
                                </b>
                            </td>
                            <td> {{ $row->appointed_time }} </td>
                            <td> {{ $row->created_by }} </td>
                            <td> {{ $row->facility_id }} </td>
                            <td> {{ $row->department_id }} </td>
                            <td> {{ $row->appointed_by }} </td>
                            <td> {{ $row->code }} </td>
                            <td> {{ $row->status }} </td>
                            <td> {{ $row->slot }} </td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-appointment" data-id="{{ $row->id }}">Edit</button>
                                {{--<a href="{{ route('edit-appointment', ['id' => $row->id]) }}">Edit</a>--}}
                                <button class="btn btn-danger btn-sm delete-appointment" data-id="{{ $row->id }}">Delete</button>
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

    <!--=============================================-->
    <!-- Edit Appointment Modal -->
    <div class="modal fade" id="editAppointmentModal" role="dialog"  data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editAppointmentModalLabel">Edit Appointment</h4>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Appointment Modal -->
    <div class="modal fade" id="deleteAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="deleteAppointmentModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteAppointmentModalLabel">Delete Appointment</h4>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <p>Are you sure you want to delete this appointment?</p>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--=============================================-->

@endsection
@section('js')
    <script>
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

        $(document).ready(function () {
            // Handle Edit Appointment Modal
            $('.edit-appointment').click(function () {
                var appointmentId = $(this).data('id');
                // Fetch appointment data via AJAX and populate the edit modal
                $.get('/edit-appointment/' + appointmentId, function (data) {
                    $('#editAppointmentModal .modal-body').html(data);
                });
                $('#editAppointmentModal').modal('show');
            });

            // Handle Delete Appointment Modal
            $('.delete-appointment').click(function () {
                var appointmentId = $(this).data('id');
                $('#deleteAppointmentModal form').attr('action', '/delete-appointment/' + appointmentId);
                $('#deleteAppointmentModal').modal('show');
            });
        });
    </script>
@endsection
