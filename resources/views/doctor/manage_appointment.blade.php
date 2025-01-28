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
            margin-top: 5px;
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
   
    /* for Config-Appointment */

    .appointment-container {
        border: 1px solid #CCCCCC;
        border-radius: 8px;
        padding: 3px;
        display: flex;
        justify-content: center;
        align-items: center;
        /* max-width: 210px; Limit the container width */
        /*  margin: auto;Center horizontally */
        background-color: #f9f9f9; /* Light gray background */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        transition: box-shadow 0.3s ease;
    }
    .custom-label-config{
        padding: 0 0 !important;
    }

    .appointment-container:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Enhance shadow on hover */
    }

    @media (max-width: 768px) {
        .appointment-container {
            max-width: 90%; /* Adjust width for smaller screens */
            padding: 10px;
        }
    }
    /* End for Config-Appointment */

    /* range of the date Sched of "SchedCategory" */
   
    .schedule-output {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        margin-top: -10px !important;
    }

    .schedule-range {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        color: #333;
    }

    .schedule-category {
        font-weight: bold;
        font-size: 16px;
        color: #007bff; /* Use a theme color */
    }
    .input-error-config {
        border: 2px solid red;
    }

    .add-time-slot{
        margin-top: 10px; 
        padding: 0 5px;
    }

    .remove-time-slot{
        margin-top: 32px;
    }

    .editadd-time-slot{
        margin-top: 10px; 
        padding: 0 5px;
    }
    .editremove-time-slot{
        margin-top: 32px;
    }
    </style>
    
@endsection

@section('content')
    @php
        use App\Cofig_schedule;
        $user = Session::get('auth');
        $department_id = $user->department_id;
        $department = null;

        $getSubOpd = \App\SubOpd::find($user->subopd_id);
    
        $Getdepartment = \App\Department::select('id','description')->get();   
        $config_sched_data =  \App\Cofig_schedule::get(); 
        $subOpd = \App\SubOpd::get();

        foreach ($Getdepartment as $row) {
            if($user->department_id === $row->id){
                $department = $row->description;
                break;
            }
        } 
        
    @endphp

    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right form-inline">
                <div class="form-group" style="margin-bottom: 10px;">

                    <form id="filterForm" method="GET">
                        <select name="filterappointment" id="appointment_filter" class="form-control" style="width: 200px;">
                            <option value="">Select Appointment</option>
                            <option value="config" {{ request('appointmentfilter') == 'config' ? 'selected' : '' }}>
                                Config Appointment 
                            </option>
                            <option value="manual" {{ request('appointmentfilter') == 'manual' ? 'selected' : '' }}>
                                Manual Appointment 
                            </option>
                        </select>

                        <button type="submit" class="btn btn-success btn-sm btn-flat">
                            <i class="fa fa-filter"></i> Apply Filter
                        </button>
                 

                        <!-- <input type="text" class="form-control" name="appt_keyword" value="{{ $keyword }}" id="keyword" placeholder="Search...">
                        <button type="submit" class="btn btn-success btn-sm btn-flat">
                            <i class="fa fa-search"></i> Search
                        </button> -->

                        <button type="button" class="btn btn-primary btn-sm btn-flat" id="add-appointment" data-toggle="modal" data-target="#addAppointmentModal">
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
                    </form>
                </div>
            </div>
            <h3>APPOINTMENTS</h3>
        </div>

        <!-- Table List -->
        <div class="box-body appointments">
            @if(count($appointment_schedule)>0)
                        <div class="table-responsive">
                        @if($appointment_filter == 'config')
                            <table class="table table-bordered table-striped table-hover table-fixed-header">
                                <tr class="bg-success bg-navy-active">
                                    <th class="text-center">Date Start</th>
                                    <th class="text-center">Date End</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Schedule</th>
                                    <th class="text-center">Facility</th>
                                    <th class="text-center">Created By</th>
                                    <th class="text-center">Action</th>
                                </tr>
                        @elseif($appointment_filter == 'manual')
                            <table class="table table-bordered table-striped table-hover table-fixed-header">
                                <tr class="bg-success bg-navy-active">
                                    <th class="text-center">Date</th>
                                    <th class="text-center">From</th>
                                    <th class="text-center">To</th>
                                    <th class="text-center">Created By</th>
                                    <th class="text-center">Facility</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">OPD Category</th> 
                                    <th class="text-center">Available Doctor</th>
                                    <!-- <th class="text-center">Slot</th> -->
                                    <th class="text-center">Action</th>
                                </tr>
                        @else
                            <p>No data Found</p>
                        @endif
                            
                        @foreach($appointment_schedule as $row)
                            @if($appointment_filter == 'config')
                                @php $monthweeks =  Cofig_schedule::where('id', $row->configId)->first()->category;@endphp
                                <tr style="font-size: 12px">
                                    <td>{{  \Carbon\Carbon::parse($row->appointed_date)->format('F d, Y')  }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->date_end)->format('F d, Y')}}</td>
                                    <td>{{  $monthweeks }}</td>
                                    <td>

                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#scheduleModal{{$row->id}}">
                                        <i class="fa fa-eye"></i>
                                    </button>

                                    </td>
                                    <td>{{ $row->facility->name }}</td>
                                    <td>{{ $row->createdBy->username }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm" onclick="UpdateConfig({{ $row->id }}, {{ $row->configId}})">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="DeleteConfig({{ $row->id }},  {{ $row->configId}}))">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <!-- Modal Structure for each schedule -->
                            <div class="modal fade" id="scheduleModal{{$row->id}}" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel{{$row->id}}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="scheduleModalLabel{{$row->id}}">
                                                <i class="fa fa-user-md" aria-hidden="true"></i>
                                                <strong>Doctor Schedule for {{ $monthweeks }}</strong>
                                            </h4> 
                                        </div>
                                        <div class="modal-body">
                                            <div class="list-group">
                                                @php
                                                    $config = Cofig_schedule::where('id', $row->configId)->first();
                                                    // Split days and times into arrays
                                                    $days = explode('|', $config->days);
                                                    $times = explode('|', $config->time);

                                                    // Map times to their respective days
                                                    $dayTimes = [];
                                                    $currentDay = null;

                                                    foreach ($times as $time) {
                                                        if (in_array($time, $days)) {
                                                            // If the current item is a day, set it as the current day
                                                            $currentDay = $time;
                                                            $dayTimes[$currentDay] = [];
                                                        } else {
                                                            // Otherwise, add the time to the current day
                                                            $dayTimes[$currentDay][] = $time;
                                                        }
                                                    }
                                                @endphp
                                                <p><strong>Start Date:</strong> {{\Carbon\Carbon::parse($row->appointed_date)->format('F d, Y')}}</p>
                                                <p style="padding: 5px;"><strong>End Date:</strong> {{\Carbon\Carbon::parse($row->date_end)->format('F d, Y')}}</p> 
                                                @foreach($dayTimes as $day => $times)
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div class="day">
                                                            <i class="fa fa-calendar" aria-hidden="true"></i> <strong>{{ $day }}</strong>
                                                        </div>
                                                        <div class="time text-right">
                                                            <i class="fa fa-clock-o" aria-hidden="true"></i> {{ implode(', ', $times) }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @elseif($appointment_filter == 'manual')
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
                            @endif
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

    <!-- My Update Appointment Version -->

    <div class="modal fade" role="dialog" id="updateConfirmationModal" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="addAppointmentFormUpdate" action="{{ route('update-appointment') }}" method="POST">
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
                                        @if($department == 'OPD')
                                            <input type="text" class="form-control" name="department_id" id="department_id" value="{{ $department }}" readonly>
                                        @else
                                            <div class="alert-department" data-department="{{ $department }}"></div>
                                        @endif

                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="label-border">
                                        <div id="opdCategoryContainer">
                                          <div id="update_additionalTimeContainer" style="display: none;"></div>
                                                <input type="hidden" class="form-control selected_date" id="update_appointed_date" value="{{$row->appointed_date}}" required>
                                                <div style="margin-top: 15px;">
                                                    <button type="button" class="btn btn-info btn-sm" id="update_add_slots" data-appointed-date="{{$row->appointed_date}}" onclick="updateAddTimeInput()" disabled>Add Slot</button>
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
                                        @if($department == 'OPD')
                                            <input type="text" class="form-control" name="department_id" id="department_id" value="{{ $department }}" readonly>
                                        @else
                                            <div class="alert-department" data-department="{{ $department }}"></div>
                                        @endif

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

@include('doctor.manage_appointment_extend')

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
        //---------------- for Config-Appointment ---------------//

        $(document).ready(function () {

            const $defaultCategorySelect = $("#defaultCategorySelect");
            const $effectiveDate = $("#effective_date");
            const $weekTimeSlot = $("#week_time_slot");
    
            const configData = @json($config_sched_data);
           
            function categslot(){

                $('.time-slots').hide().find('.time-slots').remove();

                const selectedConfigId = $defaultCategorySelect.val();
                const effectiveDateValue = $effectiveDate.val();
                const selectedConfig = configData.find(config => config.id == selectedConfigId);
                
                if(selectedConfig){
                   
                    const days = selectedConfig.days.split('|');
                    const timeSlots = selectedConfig.time.split('|');
                    
                    $('.day-checkbox').prop("checked", false);
                    $('.time-slots').hide().find(".time-slot").remove();
                    $("#week_time_slot").css("display", "block");
                    $('#please_select_categ').css("display", "none");

                    const dayTimeMap = {};
                    let currentDay = null;

                    timeSlots.forEach(slot => {
                        if(isNaN(slot[0])) {

                            currentDay = slot;
                            dayTimeMap[currentDay] = [];

                        } else if(currentDay){
                            dayTimeMap[currentDay].push(slot);
                        }
                    });
                    console.log("days", days);
                    days.forEach(day => {
                        $(`.day-checkbox[value="${day}"]`).prop("checked", true);
                        const $timeSlotsDiv = $(`.day-checkbox[value="${day}"]`).closest('.checkbox').find('.time-slots');
                        
                        $timeSlotsDiv.show();

                        if(dayTimeMap[day]) {

                            dayTimeMap[day].forEach(range => {
                                const [timeFrom, timeTo] = range.split('-');
                                const timeSlotHtml = `
                                    <div class="row time-slots" id="append_timeslot">
                                    <div class="col-md-5">
                                        <label>Time From:</label>
                                        <input type="time" name="time_from[${day}][]" class="form-control input-sm" value="${timeFrom}">
                                    </div>
                                    <div class="col-md-5">
                                        <label>Time To:</label>
                                        <input type="time" name="time_to[${day}][]" class="form-control input-sm" value="${timeTo}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div`;
                                $timeSlotsDiv.append(timeSlotHtml);
                            });

                        }
                   });
                    var OneweekToOneMonth = selectedConfig.category;
                   
                    console.log("effectiveDate::", effectiveDateValue);

                    if(effectiveDateValue){
                        const startDate = new Date(effectiveDateValue);
                        let endDate;

                        if(OneweekToOneMonth === "1 Week"){
                            endDate = new Date(startDate);
                            endDate.setDate(startDate.getDate() + 6);
                        } else if (OneweekToOneMonth === "1 Month"){
                            endDate = new Date(startDate);
                            endDate.setMonth(startDate.getMonth() + 1);
                            endDate.setDate(endDate.getDate() - 1);
                        }
                        const formattedSched = `<strong>Start Date:</strong> <span class="schedule-category">${formatedSchedule(startDate)}</span> - <strong>End Date:</strong> <span class="schedule-category">${formatedSchedule(endDate)}</span>`;
                        $("#SchedCategory").html(`
                          <div class="col-md-12 schedule-output text-center" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; margin-top: 10px;">
                                <span class="schedule-range">
                                    <i class="fa fa-calendar"></i> <strong>Start Date:</strong> <span class="schedule-category">${formatedSchedule(startDate)}</span> <strong> - </strong> 
                                    <input type='hidden' name="startDate" value="${formatedSchedule(startDate)}">
                                    <input type='hidden' name="endDate" value="${formatedSchedule(endDate)}">
                                    <i class="fa fa-calendar"></i> <strong>End Date:</strong> <span class="schedule-category">${formatedSchedule(endDate)}</span>
                                     &nbsp; <span class="schedule-category" style="font-weight: bold;"> (${OneweekToOneMonth})</span>
                                </span>
                            </div>

                        `);
                    }
                 
                }else{
                    $('#please_select_categ').css("display", "block");
                    $("#week_time_slot").css("display", "none");
                }
            }

            $defaultCategorySelect.on("change", categslot);
            $effectiveDate.on("change", categslot);

            function formatedSchedule(date){
                const year = date.getFullYear();
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const day = date.getDate().toString().padStart(2, '0');
                return `${month}-${day}-${year}`;
            }
        });

        document.addEventListener('DOMContentLoaded', function () {

            const effectiveDate = document.getElementById('effective_date');
            const defaultCategorySelect = document.getElementById('defaultCategorySelect');
               
            function highlightEmptyFields() {
                if (!effectiveDate.value) {
                    effectiveDate.classList.add('input-error-config');
                } else {
                    effectiveDate.classList.remove('input-error-config');
                }

                if (!defaultCategorySelect.value) {
                    defaultCategorySelect.classList.add('input-error-config');
                } else {
                    defaultCategorySelect.classList.remove('input-error-config');
                }
            }

            $('#addAppointmentModal').on('show.bs.modal', function () {
                highlightEmptyFields();
            });
            effectiveDate.addEventListener('input', highlightEmptyFields);
            defaultCategorySelect.addEventListener('change', highlightEmptyFields);


            const configCheckbox = document.getElementById('enable_config_appointment');
            const elementsToToggle = [
                { element: document.getElementById('appointment_date_label'), showOnCheck: false },
                { element: document.getElementById('appointment_date'), showOnCheck: false },
                { element: document.getElementById('side_Config'), showOnCheck: true },
                // { element: document.getElementById('week_time_slot'), showOnCheck: true},
                { element: document.getElementById('please_select_categ'), showOnCheck: true},
                { element: document.getElementById('Manual-time-slot'), showOnCheck: false}
            ];

            configCheckbox.addEventListener('change', function () {
                const isChecked = this.checked;
                if(isChecked){
                    $("#week_time_slot").css("display", "none");
                }else{
                    $("#week_time_slot").css("display", "none");
                }
                // Toggle visibility for each element based on checkbox state
                elementsToToggle.forEach(({ element, showOnCheck }) => {
                    element.style.display = isChecked === showOnCheck ? 'inline' : 'none';
                });
            });

        });

        $('#defaultCategory').change(function() {
            var selectedCategory = $(this).val();
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(today.getDate() + 1); // Tomorrow (one day after today)

            var dateRangeInput = $('#config_date_range');

            // Reset the input field if no category is selected
            if (!selectedCategory) {
                dateRangeInput.val('');
                return;
            }

            var startDate = tomorrow;
            var endDate;

            if (selectedCategory === '1 Week') {
                // Calculate one week from tomorrow
                endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 7); // Add 7 days
            } else if (selectedCategory === '1 Month') {
                // Calculate one month from tomorrow
                endDate = new Date(startDate);
                endDate.setMonth(startDate.getMonth() + 1); // Add 1 month
            }

            // Format the start and end dates as m/d/Y (for example, 12/1/2024)
            var formattedStartDate = formatDate(startDate);
            var formattedEndDate = formatDate(endDate);

            // Set the date range value in the input field
            dateRangeInput.val(formattedStartDate + ' - ' + formattedEndDate);
        });

        // Helper function to format the date as m/d/Y
        function formatDate(date) {
            var month = date.getMonth() + 1; // Month is 0-based, so we add 1
            var day = date.getDate();
            var year = date.getFullYear();
            
            return month + '/' + day + '/' + year;
        }

        $(document).on('click', '.add-time-slot', function() {
            let day = $(this).data('day');
            let timeSlot = `
                <div class="row time-slot">
                    <div class="col-md-5">
                        <label>Time From:</label>
                        <input type="time" name="time_from[${day}][]" class="form-control input-sm">
                    </div>
                    <div class="col-md-5">
                        <label>Time To:</label>
                        <input type="time" name="time_to[${day}][]" class="form-control input-sm">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-time-slots" style="margin-top: 32px;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                `;
            $(this).before(timeSlot);
        });

        $(document).on('click', '.remove-time-slots', function() {
            $(this).closest('.time-slot').remove();
        });

        $(document).on('change', '.day-checkbox', function() {
            let  checkboxContainer = $(this).closest('.checkbox');
            if($(this).is(':checked')){
                $(this).closest('.checkbox').find('.time-slots').slideDown(); // Show time-slots
            }else{
                let timeSlots =  checkboxContainer.find('.time-slots');
                timeSlots.slideUp(); 
                timeSlots.find('input[type="time"]').val('');
                timeSlots.find('.time-slot:not(:first)').remove();
            }
        });

        //---------------- for Config-Appointment ---------------//

        // function UpdateModal(appointmentId) {
        $(document).ready(function() {
            let shownDepartments = new Set();

            $('.alert-department').each(function() {
                var departmentName = $(this).data('department');

                console.log("departmentName", departmentName);

                if(departmentName && !shownDepartments.has(departmentName)){

                    shownDepartments.add(departmentName);

                    Lobibox.alert('error', {
                        msg: 'Only the Opd Department is allowed to create appointments. Department: ' + departmentName + ' is not allowed.',
                        closeOnEsc: true,
                        closeButton: true,
                        callback: function() {
                            $('#addAppointmentModal').modal('hide');
                            $('#updateConfirmationModal').modal('hide');
                            $('#deleteConfirmationModal').modal('hide');
                        }
                    });

                    $('#add-appointment').prop('disabled', true);
                }
           
            });
            shownDepartments.clear();

            // $('#addAppointmentModal').on('show.bs.modal', function () {
            //     var departmentName = $('.alert-department').data('department');
            //     console.log("department name::", departmentName);

            //     if(departmentName){
            //         $(this).modal('hide');
            //         Lobibox.alert('error', {
            //             msg: 'Only the Opd Department is allowed to create appointments. Department: ' + departmentName + ' is not allowed.',
            //         });
            //     }
            // });

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

                    let available_doctor = appointment.telemed_assigned_doctor.appointment_by;

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
        // var query_doctor_store = [];
        //     $(document).ready(function() {
        //         var facility_id = $(`#id`).val();
        //         console.log(facility_id);
        //         if(facility_id) {
        //             $.get("{{ url('get-doctors').'/' }}" + facility_id, function (result) {
        //                 query_doctor_store = result;
        //                 const current_appointment_count = $(".appointment_count").val();
        //                 for(var i=1; i<=current_appointment_count; i++) {
        //                     $(`.available_doctor${i}`).html('');
        //                     $(`.available_doctor${i}`).append($('<option>', {
        //                         value: "",
        //                         text: "Select Doctors"
        //                     }));
        //                     $.each(query_doctor_store, function (index, userData) {
        //                         $(`.available_doctor${i}`).append($('<option>', {
        //                             value: userData.id,
        //                             text: "Dr. "+userData.fname + ' ' + userData.lname
        //                         }));

        //                     });
        //                 }
        //             });
        //         }
        //     });

        $(document).ready(function() {
            var facility_id = $(`#id`).val();
            console.log(facility_id);

            if (facility_id) {
                $.get("{{ url('get-doctors').'/' }}" + facility_id, function(result) {
                    query_doctor_store = result;
                    const current_appointment_count = $(".appointment_count").val();

                    for (var i = 1; i <= current_appointment_count; i++) {
                        const doctorSelect = $(`.available_doctor${i}`);
                        
                        // Clear existing options
                        doctorSelect.empty();

                        // Add "Select Doctors" placeholder option
                        doctorSelect.append($('<option>', {
                            value: "",
                            text: "Select Doctors",
                            disabled: true // Only a placeholder, non-selectable
                        }));

                        // Append doctor options
                        $.each(query_doctor_store, function(index, userData) {
                            doctorSelect.append($('<option>', {
                                value: userData.id,
                                text: "Dr. " + userData.fname + ' ' + userData.lname
                            }));
                        });

                        // Event listener to update the selected option
                        doctorSelect.on('change', function() {
                            const selectedValue = $(this).val();
                            if (selectedValue) {
                                // If a doctor is selected, remove the "Select Doctors" placeholder
                                $(this).find('option[value=""]').remove();
                            }
                        });
                    }
                });
            }
        });

        let deleteCount = 0;
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
                                                        <input type="time" class="form-control  add-appointment-field" id="add_appointed_time_${currentCount}" name="add_appointed_time${currentCount}" placeholder="HH:mm" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span>To:</span>
                                                        <input type="time" class="form-control  add-appointment-field" id="add_appointed_time_to${currentCount}" name="add_appointed_time_to${currentCount}" placeholder="HH:mm" required>
                                                    </div>
                                                    <label for="opdCategory">OPD Category:</label>
                                                    <select class="form-control select2  add-appointment-field" id='add_opdCategory_${currentCount}' name="add_opdCategory${currentCount}" required>
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
                                                <select class="form-control select2 available_doctor${currentCount}  add-appointment-field" id='add_available_doctor_${currentCount}' name="add_available_doctor${currentCount}[]" multiple="multiple" data-placeholder="Select Doctor" style="width: 100%;" required></select>
                                            </div>
                                        </div>`;

            var deleteBtn = '<div><button type="button" class="btn btn-danger btn-sm delete-time-input"  data-slot-id="${currentCount}"  data-index="0" style="margin-top: 15px;"><span><i class="fa fa-trash"></i></span></button></div>';
            timeInputGroup.append(deleteBtn);
            timeInputGroup.append(additionalTimeInput);
            $('#additionalTimeContainer').append(timeInputGroup);

            timeInputGroup.find('.delete-time-input').on('click', function () {
                deleteCount++;
                timeInputGroup.remove();
                slotFormFields(); 
                // $('#add_slots').prop('disabled', false);
            });

            // $(document).ready(function() {
                $('.select2').select2();
                
                $.each(query_doctor_store, function (index, userData) {
                    $(`.available_doctor${currentCount}`).append($('<option>', {
                        value: userData.id,
                        text: "Dr. "+userData.fname + ' ' + userData.lname
                    }));
                });

                // $('.select2').select2();
            // });

              // Reinitialize the select2 plugin after adding a new element
            $(`.available_doctor${currentCount}`).select2();

            $('#additionalTimeContainer').show();

           slotFormFields(); 
        }
        //disabled Add appointment button

        function initializeSelect2() {
            $('.available_doctor').select2(); // Adjust the selector as needed
        }
        
        function slotFormFields(){
            let allfilled = true;

            const currentCount = $(".appointment_count").val();

            $('.time-input-group').each(function() {

                const timefrom = $(this).find('input[name^="add_appointed_time"]').val();
                const timeTo = $(this).find('input[name^="add_appointed_time_to"]').val();
                const opdCategory = $(this).find('select[name^="add_opdCategory"]').val();
                const availableDoctor = $(this).find('select[name^="add_available_doctor"]').val();

                if(!timefrom || !timeTo || !opdCategory ||  !availableDoctor || availableDoctor == '' || timefrom == '') {
                    allfilled = false;
                }
                console.log("timefrom", timefrom, 'timeTo', timeTo, 'opdCategory', opdCategory, 'availableDoctor', availableDoctor);
            });
          
            console.log("!allfilled", !allfilled);
            $('#add_slots').prop('disabled', !allfilled);
        }
        
        $(document).on('input change', 'input[name^="add_appointed_time"],input[name^="add_appointed_time_to"],select[name^="add_opdCategory"],select[name^="add_available_doctor"]', function() {
            slotFormFields();
        })
        $(document).ready(function(){
            slotFormFields();
        });
        
        $('#addAppointmentForm').on('show.bs.modal', function() {
            
            //  $('#addAppointmentModal').find('input').val('');

            // $('#addAppointmentModal').find('select').prop('selectedIndex', 0);

            // $('#addAppointmentModal').find('.time-input-group').remove();

            // $('#addAppointmentModal').find('.update-appointment-field').remove();
            $('#addAppointmentFormUpdate').find(`[name^="update_appointed_time"], [name^="update_appointed_time_to"], [name^="Update_available_doctor"]`).val('');
        });

        $('#updateConfirmationModal').on('hidden.bs.modal', function () {
            // Clear all input fields when the Update modal is closed
            // $(this).find('input').val('');

            // Reset all select fields to default
            $(this).find('select').prop('selectedIndex', 0);

            // Remove dynamically added slots or fields
            $(this).find('.time-input-group').remove();
        });

        let skipAlertCLose = false; 
        
        $('#Add_Cancel_appointment, #Add-close-apppoint').on('click', function() {
            skipAlertCLose = true;
            // $(this).find('.time-input-group').remove();
            $('#addAppointmentForm_add')[0].reset();
            $('.select2').val(null).trigger('change'); 
            $('#additionalTimeContainer').empty();
            $(".appointment_count").val(1);
            console.log("wok ra");

            setTimeout(() => {
                skipAlertCLose = false;
            }, 200);

        });

        // function slotFormFields() {
          
        //     let allFilled = true;
        //     const currentCount = $(".appointment_count").val();
        
        //     const timeFrom = $(`#add_appointed_time_${currentCount}`).val();  // Get the "From" time for each slot
        //     const timeTo = $(`#add_appointed_time_to${currentCount}`).val();  // Get the "To" time for each slot
        //     const opdCategory = $(`#add_opdCategory_${currentCount}`).val();  // Get OPD category
        //     const availableDoctor = $(`#add_available_doctor_${currentCount}`).val();  // Get the selected doctor

    
        //     if (!timeFrom || !timeTo || !opdCategory|| !availableDoctor) {
        //         allFilled = false;
        //     }

        //     console.log(`timefrom ss${currentCount}`, timeFrom, "timeTo1 ss ", timeTo, 'opdCategory ss', opdCategory , 'doctor', availableDoctor);
        
        
        //     $('#add_slots').prop('disabled', !allFilled);
        // }

        // // Event listeners for Add Appointment form fields
        // $(document).on('input change', '.add-appointment-field', function() {
        //     slotFormFields();
        // });

        // // Initialize validation on page load
        // $(document).ready(function() {
        //     slotFormFields();
        // });

//-----------------My Append Update Edit appointment Time---------------------------//
    let UpdatedeleteCount = 0;
    function updateAddTimeInput(appointment, assignedDoc) 
    {
            // console.log('welcome appointment', appointment)
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
                                                    <select class="form-control select2 available_doctor${currentCount}" name="Update_available_doctor${currentCount}[]" multiple="multiple" name="Update_available_doctor" id="Update_available_doctor${currentCount}" data-placeholder="Select Doctor" style="width: 100%;">
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
                                                            <input type="time" class="form-control update-appointment-field" name="Add_appointed_time${currentCount}" id="empty_appointed_time${currentCount}" >
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span>To:</span>
                                                            <input type="time" class="form-control update-appointment-field" name="Add_appointed_time_to${currentCount}" id="empty_appointedTime_to${currentCount}">
                                                        </div>
                                                        <label for="opdCategory">OPD Category:</label>
                                                        <select class="form-control select update-appointment-field" name="opdCategory${currentCount}" id="Add_update_opdCategory${currentCount}">
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
                                                    <select class="form-control select2 available_doctors${currentCount} update-appointment-field" name="Add_Update_available_doctor${currentCount}[]" multiple="multiple" id="Add_Update_available_doctor${currentCount}" data-placeholder="Select Doctor" style="width: 100%;">

                                                    </select>
                                                </div>
                                            </div>`;
                                        }       
                                 
            // Add the delete button
            var deleteBtn = '<div><button type="button" class="btn btn-danger btn-sm delete-time-input" data-index="{{index}}" style="margin-top: 15px;"><span><i class="fa fa-trash"></i></span></button></div>';
            if(currentCount > 2){
                timeInputGroup.append(deleteBtn);

            }                         

            timeInputGroup.append(additionalTimeInput);
            $('#update_additionalTimeContainer').append(timeInputGroup);
          
            let totalSlot = $('.time-input-group').length;

            $('#cancelUpdate, #closeUpdate').on('click', function() { // reset currentCount for closing the modal update
                currentCount = 1;
                $(".appointment_count").val(currentCount);

                $('#addAppointmentFormUpdate').find(`[name^="update_appointed_time"], [name^="update_appointed_time_to"], [name^="Update_available_doctor"]`).val('');

                console.log("it is work");
            });

            timeInputGroup.find('.delete-time-input').on('click', function () {
                UpdatedeleteCount++;
                timeInputGroup.remove(); 
                updateAddSlotButtonState();
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
        //-----------------------for update fix
 
        let appointmentSlot = [];
    
        function checkConflict(slotIndex, fromTime, toTime, doctors) {
            console.log("OrigslotIndex conflict slot for validation: ", slotIndex)
            for (let i = 0; i < appointmentSlot.length; i++) {
                if (i === slotIndex) continue; // Skip checking against itself for updates

                const slot = appointmentSlot[i];
                
                // Check for time overlap
                const timeOverlap = (fromTime < slot.to && toTime > slot.from);

                // if (timeOverlap) {
                    // Check for doctor conflict
                    const conflictingDoctors = doctors.filter(doctor => slot.doctors.includes(doctor));
                    if (conflictingDoctors.length > 0 && timeOverlap) {
                        console.log("conflict appointmentSlot", appointmentSlot);
                        return {
                            hasConflict: true,
                            message: `Time slot ${fromTime.toLocaleTimeString()} - ${toTime.toLocaleTimeString()} conflicts with existing appointment ${slot.from.toLocaleTimeString()} - ${slot.to.toLocaleTimeString()} and doctor(s) ${conflictingDoctors.join(', ')} are already scheduled.`
                        };
                    }
                //}
            }
  
            return { hasConflict: false };
        }
        
        let removeClickCount = 0; 
        function addOrUpdateSlot(slotIndex, fromTime, toTime, doctors) {
            
            let OrigslotIndex  = slotIndex - (UpdatedeleteCount + 1);  
                
            console.log("OrigslotIndex update", "equal", OrigslotIndex);

            const conflict = checkConflict(OrigslotIndex, fromTime, toTime, doctors);
            if (conflict.hasConflict) {
                console.log(conflict.message);
                
                Lobibox.alert("error", {
                    msg: conflict.message
                 });

                $(`#Update_available_doctor${slotIndex + 1}`).val('').trigger('change');
                $(`#Add_Update_available_doctor${slotIndex + 1}`).val('').trigger('change');
                return true;
            }

            const newSlot = {
                from: fromTime,
                to: toTime,
                doctors: doctors,
            };
            
            if (OrigslotIndex < appointmentSlot.length) {
                // Update existing slot
                appointmentSlot[OrigslotIndex] = newSlot;
            } else {
                // Add new slot
                appointmentSlot.push(newSlot);
            
            }
            console.log("total update slot", appointmentSlot);
            return true;

        }

        function validateTimeSlot(fromTime, toTime) {
            if (toTime <= fromTime) {
                Lobibox.alert("error", {
                    msg: "End time must be after start time!"
                });
                return false;
            }
            return true;
        }

         let newSlotAdded = false;
        
        function updateAddSlotButtonState() {
            let allExistingSlotsFilledAndValid = true;
            let hasExistingSlots = false;
            let hasPartiallyFilledNewSlot = false;
            let allNewSlotsFilled = true;

            // Start from index 2 for existing slots
            for (let i = 2; i <= 15; i++) { // Assuming a maximum of 9 slots (adjust if needed)
                // Existing slots logic
                let fromTime = $(`#update_appointed_time${i}`).val();
                let toTime = $(`#update_appointedTime_to${i}`).val();
                let categ = $(`#update_opdCategory${i}`).val();
                let doctors = $(`#Update_available_doctor${i}`).val();
                
                if (fromTime || toTime || categ || (doctors && doctors.length > 0)) {
                    hasExistingSlots = true;
                    if (!fromTime || !toTime || !categ || !doctors || doctors.length === 0) {
                        allExistingSlotsFilledAndValid = false;
                    }
                }

                // New slots logic
                let newFromTime = $(`#empty_appointed_time${i}`).val();
                let newToTime = $(`#empty_appointedTime_to${i}`).val();
                let newcategory = $(`#Add_update_opdCategory${i}`).val();
                let newDoctors = $(`#Add_Update_available_doctor${i}`).val();
                
                if (newFromTime || newToTime || newcategory || (newDoctors && newDoctors.length > 0) || (newFromTime == "" || newToTime == "" || newcategory == "")) {
                    hasPartiallyFilledNewSlot = true;
                    //if(newSlotAdded){
                        if (!newFromTime || !newToTime || !newcategory || !newDoctors || newDoctors.length === 0) {
                            allNewSlotsFilled = false;
                        }
                    // }
                }

                console.log('newFromTime', newFromTime, 'newToTime', newToTime, 'newcategory', newcategory, 'newDoctors', newDoctors);
            }

            let shouldEnable = (allExistingSlotsFilledAndValid && hasExistingSlots) &&
                               (!hasPartiallyFilledNewSlot || (hasPartiallyFilledNewSlot && allNewSlotsFilled));
            $('#update_add_slots').prop('disabled', !shouldEnable);

        }

        $(document).on('input change', '.update-appointment-field', function() {
            updateAddSlotButtonState();
        });

        $(document).ready(function() {
            // Initial call to update the button state on page load
            updateAddSlotButtonState();

            // Use event delegation to listen for input changes
            $(document).on('input change', '.label-border-time input, .label-border-time select', function() {
                updateAddSlotButtonState();
            });

            // $('#update_add_slots').on('click', function() {
            //     true;
            //     updateAddSlotButtonState();
            // })
        });

        let deleteTimefrom = [];
        // Initialize appointmentSlot with existing data
        $(document).ready(function() {
        
            $('.time-input-group').each(function(index) {
                let appointedDate = $("#updateAppointmentId").val();
                let fromTime = new Date(appointedDate + "T" + $(`#update_appointed_time${index+1}`).val());
                let toTime = new Date(appointedDate + "T" + $(`#update_appointedTime_to${index+1}`).val());
                let doctors = $(`#Update_available_doctor${index+1}`).val() || [];
                
                let newfromTime = new Date(appointedDate + "T" + $(`#empty_appointed_time${index+1}`).val());
                let newtoTime = new Date(appointedDate + "T" + $(`#empty_appointedTime_to${index+1}`).val());
     
                let slotIndexs = index-1;
                //existing slot
                if (!appointmentSlot[index]) {
                    // Only add valid slots (ensure that the date is not invalid)
                    if (!isNaN(fromTime.getTime()) && !isNaN(toTime.getTime())) {
                        appointmentSlot.push({ //existing slot
                            from: fromTime ,
                            to: toTime,
                            doctors: doctors,
                        });
                    }

                    if (!isNaN(newfromTime.getTime()) && !isNaN(newtoTime.getTime())) {
                        appointmentSlot.push({ //newly added slot
                            from: newfromTime ,
                            to: newtoTime,
                            doctors: doctors,
                        });
                    }
                }
      
            });
                
            appointmentSlot = appointmentSlot.filter(slot => {
                return !isNaN(slot.from.getTime()) && !isNaN(slot.to.getTime());
            });
            console.log("Initial appointmentSlot array:", appointmentSlot);
            //updateAddSlotButtonState();
        });


        // for retrieving existing slots
        $(document).on('change', `[name^="update_appointed_time"], [name^="update_appointed_time_to"], [name^="Update_available_doctor"]`, function () {
            let slotIndex = parseInt($(this).attr('name').match(/\d+/)[0], 10) - 1; // Adjust index to be 0-based
            var UpdateDate = $("#updateAppointmentId").val();

            let updatefromTime = $(`#update_appointed_time${slotIndex+1}`).val().slice(0,5);
            let updatetoTime = $(`#update_appointedTime_to${slotIndex+1}`).val().slice(0,5);
            let doctor = $(`#Update_available_doctor${slotIndex+1}`).val() || [];

            //updateAddSlotButtonState();
            
            if (!updatefromTime || !updatetoTime || !UpdateDate) {
                console.log("Missing time or date input");
                return;
            }

            var UpfromTimeObj = new Date(UpdateDate + "T" + updatefromTime);
            var UptoTimeObj = new Date(UpdateDate + "T" + updatetoTime);

            if (validateTimeSlot(UpfromTimeObj, UptoTimeObj)) {
                if (addOrUpdateSlot(slotIndex, UpfromTimeObj, UptoTimeObj, doctor)) {
                    // console.log("Slot updated successfully");
                } else {
                    // Revert to original values if update fails
                    let originalSlot = appointmentSlot[slotIndex];
                    console.log("originalSlot", originalSlot);
                    if (originalSlot) {
                        $(`#update_appointed_time${slotIndex+1}`).val(originalSlot.from.toTimeString().slice(0,5));
                        $(`#update_appointedTime_to${slotIndex+1}`).val(originalSlot.to.toTimeString().slice(0,5));
                        $(`#Update_available_doctor${slotIndex+1}`).val(originalSlot.doctors).trigger('change');
                    }
                }
            } else {
                $(`#update_appointedTime_to${slotIndex+1}`).val("");
            }
            
        });
        
        // for adding new slots
        $(document).on('change', `[name^="Add_appointed_time"], [name^="Add_appointed_time_to"], [name^="Add_Update_available_doctor"]`, function () {
            let index =  parseInt($(this).attr('name').match(/\d+/)[0], 10) - 1;
            let newfromTime = $(`#empty_appointed_time${index+1}`).val().slice(0,5);
            let newTotime = $(`#empty_appointedTime_to${index+1}`).val().slice(0,5);
            let newdoctor = $(`#Add_Update_available_doctor${index+1}`).val() || [];
            var UpdateDate = $("#updateAppointmentId").val();
            
            deleteTimefrom = newfromTime && newTotime;
            //updateAddSlotButtonState();

            if (!newfromTime || !newTotime || !UpdateDate) {
                console.log("Missing time or date input");
                return;
            }

            var UpfromTimeObj = new Date(UpdateDate + "T" + newfromTime);
            var UptoTimeObj = new Date(UpdateDate + "T" + newTotime);

            if (validateTimeSlot(UpfromTimeObj, UptoTimeObj)) {
              
                if (addOrUpdateSlot(index, UpfromTimeObj, UptoTimeObj, newdoctor)) {
                    // console.log("New slot added successfully");
                } else {
            
                }
            } else {
                $(`#empty_appointedTime_to${index+1}`).val("");
            }
           
        });

        //-----------------------for update fix

            if(assignedDoc){
                document.getElementById('appointed_date').disabled = true; 
            }else{
                document.getElementById('appointed_date').disabled = false;
            }

            const update_slots = document.getElementById('update_add_slots');
            const appointedDate = $("#updateAppointmentId").val();
            
            if(appointedDate){ // hide Add slot button if past date
                const currentDate = new Date();
                const appointmentDate = new Date(appointedDate);

                currentDate.setHours(0, 0, 0, 0);
                appointmentDate.setHours(0, 0, 0, 0);
                
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

            $('#appointment_filter').select2({
            placeholder: "Select a Appointment",
            allowClear: true,
            width: 'resolve'
        });
    });
    function generateDoctorsOptions(doctorData){
        var options = '';
        var selectedDoctor = new Set();

        if(Array.isArray(doctorData)){  
            doctorData.forEach(function (doctor){
                selectedDoctor.add(doctor.doctor.id);// Add the doctor ID to the set
                options += `<option value="${doctor.doctor.id}" selected>${doctor.doctor.fname} ${doctor.doctor.lname}</option>`;
            });
        }
        $.each(query_doctor_store, function (index, userData){

            if(!selectedDoctor.has(userData.id)){// Exclude selected doctors
                options += `<option value="${userData.id}">${userData.fname} ${userData.lname}</option>`;
            }
        });

        return options;
    }
    //----------------------Trapping Appointment Time From and Time To and Date----------------------------//    
    const today = new Date().toISOString().split('T')[0]; // i add this for disabled the past date in appointment date
    document.querySelector('input[name="appointed_date"]').setAttribute('min', today);
    console.log('today', today);


    $(document).ready(function() {

        var allAppointmentTimes = [];
        var currentCounts = 1;
        var counts = 1;
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
            // selectElement.find('option:selected').prop('selected', false);
            // selectElement.trigger('change');
            if(selectElement.length > 0){
                selectElement.val(null).trigger('change'); 
            }else {
                console.warn('Select2 element not found:', selectElement); // Log an error for debugging
            }
        }
        //Add Appointment Validation

        $(document).on('change', 'input[name^="add_appointed_time"], input[name^="add_appointed_time_to"], select[name^="add_available_doctor"]', function() {


            var UpdateDate = $("#updateAppointmentId").val();
            var appointmentDate = $("#appointment_date").val().trim();

            var timeInputGroup = $(this).closest('.time-input-group');
            var index = $('.time-input-group').index(timeInputGroup);
            currentCounts = index + 1;
            counts = index;


            var fromInput = timeInputGroup.find(`input[name="add_appointed_time${currentCounts}"]`);
            var toInput = timeInputGroup.find(`input[name="add_appointed_time_to${currentCounts}"]`);
            var selectedDoctor = timeInputGroup.find(`select[name="add_available_doctor${currentCounts}[]"]`).val();

            var fromTime = fromInput.val();
            var toTime = toInput.val();
            console.log("fromInput::", fromTime, 'toInput :::', toTime);
            let currentFrom = [];
            let currentTo = [];
            let currentDoctors = [];
            let timeSlots = [];
            let cleardoctors = [];
            $('.time-input-group').each(function() {
                // Capture the "from" time and "to" time
                let fromTimeObj = $(this).find('input[name^="add_appointed_time"]').val().slice(0,5);
                let toTimeObj = $(this).find('input[name^="add_appointed_time_to"]').val().slice(0,5);


                // Capture the selected doctors from the select2 dropdown
                let selectedDoctors = $(this).find('select[name^="add_available_doctor"]').val() || [];
            
                // If both times are selected, construct the object
                if (fromTimeObj && toTimeObj) {
                    var timeObject = {
                        from: fromTimeObj,
                        to: toTimeObj,
                        doctors: selectedDoctors
                    };
                    timeSlots.push(timeObject);
                }
                slotFormFields();
            });

            timeSlots.forEach(function(slot, index) {
                currentFrom.push(slot.from);
                currentTo.push(slot.to)
                currentDoctors.push(slot.doctors)
                cleardoctors.push(index);
            });


            var fromTimeObj = new Date(appointmentDate + "T" + fromTime);
            var toTimeObj = new Date(appointmentDate + "T" + toTime);


            // var toTimeCur = new Date(appointmentDate + "T" + currentTo);
            // var FromTimeCur = new Date(appointmentDate + "T" + currentFrom);
            var toTimeCur = currentTo.length > 0 ?  new Date(appointmentDate + "T" + currentTo[currentCounts - 1]) : null;
            var FromTimeCur =currentFrom.length > 0 ? new Date(appointmentDate + "T" + currentFrom[currentCounts -1]) : null;
            var selectedDoctors = $(`.available_doctor${currentCounts}`).val() ? $(`.available_doctor${currentCounts}`).val() : currentDoctors[currentCounts - 1];


            var isUnique = true;
            var conflictDoctors = [];


            var timeObject = {
                from: FromTimeCur,
                to:  toTimeCur,
                doctors: selectedDoctors
            };

            $.ajax({
                    url: "{{ route('get-booked-dates') }}",
                    type: 'GET',
                    success: function(response){
                            // console.log("response::", response);
                            var timeConflict = false;
                            var dateConflict = false;
                
                            response.forEach(function(appoint){
                                if(appoint.appointed_date === appointmentDate){


                                    dateConflict = true;
                                    var existDoctor = [];


                                    var existFromtime = new Date(appointmentDate + "T" + appoint.appointed_time);
                                    var existTotime = new Date(appointmentDate + "T" + appoint.appointedTime_to);
                            
                                    if(Array.isArray(appoint.telemed_assigned_doctor)){
                                        existDoctor = appoint.telemed_assigned_doctor.map(function(doctor){
                                            return doctor.doctor_id;
                                        });
                                    }
                                
                                var timeROverlap = (timeObject.from >= existFromtime && timeObject.from < existTotime) ||
                                    (timeObject.to > existFromtime && timeObject.to <= existTotime) ||
                                    (timeObject.from <= existFromtime && timeObject.to >= existTotime);


                                    var doctorSOverlap =  Array.isArray(existDoctor) && Array.isArray(timeObject.doctors) && existDoctor.some(function(doctorId) {
                                        return timeObject.doctors.includes(String(doctorId));
                                    });


                                    // $(document).on('change', '[name^="available_doctor"]', function () {
                                    //     var selectedDoctors = $(this).val();
                                    
                                    // });


                                    if(timeROverlap && doctorSOverlap){
                                        timeConflict = true;
                                        return false; // Break the loop
                                    }
                                }
                            });
                            // console.log("timeConflict", timeConflict);
                            if (timeConflict) {
                                // fromInput.val('');
                                // toInput.val('');
                                clearSelectElement($(`.available_doctor${currentCounts}`));
                                alert('This time and doctor is already taken. From this Appointed Date!');
                                return;
                            }
                        
                    },
                    error: function(xhr, status, error){
                        console.error(xhr, responseText);
                    }
                });



            if(UpdateDate || appointmentDate){
            
            }else{


                if((!appointmentDate || !UpdateDate) && !skipAlertCLose){
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
                    conflictDoctors = conflictDoctors.concat(
                            existingTime.doctors.filter(doctor => timeObject.doctors.includes(doctor))
                        );
                }
            }


            if(!isUnique) {
                alert('Appointment time and Assigned Doctor is already taken');
                var selectElement =  $(`select[name="add_available_doctor${currentCounts + deleteCount}[]"]`);
                //$(`.available_doctor${currentCounts + deleteCount}`);
            
                // filterConflictingDoctors(selectElement, conflictDoctors, selectedDoctors);


                if ($(this).is('select')) {
                    var nonConflictingDoctors = selectedDoctors.filter(doctor => !conflictDoctors.includes(doctor));
                    $(this).val(nonConflictingDoctors).trigger('change');
                }


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


            console.log("allAppointmentTimes", allAppointmentTimes);
            // console.log("selectedDoctors::", timeObject);
            // console.log('slot', index);
            $('input[name="appointed_date"]').data('fromTimeObj', fromTimeObj);
            $('input[name="appointed_date"]').data('Totime', toTimeObj);


            $('input[name="appointed_date"]').trigger('change');




            $('.delete-time-input').on('click', function () {


                console.log("I remove the", allAppointmentTimes, 'index count', index);


            });
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




