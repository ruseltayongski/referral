@extends('layouts.app')

@section('css')

<style>
    .icon-center {
        text-align: center;  /* Center the contents */
        vertical-align: middle; /* Align the button vertically in the middle of the cell */
    }

    .list-group-item {
        padding: 10px 15px;
        border: 1px solid #ddd;
        margin-bottom: 5px;
        border-radius: 4px;
    }

    .list-group-item .day {
        font-weight: bold;
        font-size: 14px;
    }

    .list-group-item .time {
        font-size: 12px;
        color: #666;
    }
    
    .days-checkbox{
        margin-top: 32px;
    }
    .add-time-slot{
        margin-top: 10px;
    }
    .Update-time-slot{
        margin-top: 10px;
    }
</style>
@endsection

@section('content')

<?php
use App\Facility;
?>

<div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right form-inline">
                <div class="form-group" style="margin-bottom: 10px;">
                    <input type="text" class="form-control" name="appt_keyword" value="{{ $keyword }}" id="keyword" placeholder="Search...">
                    <button type="submit" class="btn btn-success btn-sm btn-flat">
                        <i class="fa fa-search"></i> Search
                    </button>

                    <button type="button" class="btn btn-primary btn-sm btn-flat" id="add-appointment" data-toggle="modal" data-target="#addconfigModal">
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
            <h3>Config Schedule</h3>
        </div>
    
        <!-- Table List -->
        <div class="box-body appointments">
            @if(count($config)>0) 
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-fixed-header">
                        <tr class="bg-success bg-navy-active">
                            <th class="text-center">Description</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Facility</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Schedule</th>
                            <th class="text-center">Created by</th>
                            <th class="text-center">Action</th>
                        </tr>
                        @foreach($config as $schedule)
                        @php
                            $createdBy = $schedule->created_by;
                            $createdByParts = explode('|', $createdBy);
                            $name = $createdByParts[1] ?? $createdBy;
                            $configId = $schedule->id;
                           $facility = Facility::select('id','name')->where('id', $schedule->facility_id)->first();
                        @endphp
                            <tr style="font-size: 12px">
                                <td>{{$schedule->description}}</td>
                                <td>  
                                    {{ $department->firstWhere('id',  $schedule->department_id)->description ?? 'N/A' }}
                                </td>
                                <td>{{ $facility->name }}</td>
                                <td>{{ $schedule->category }}</td>
                                <td class="icon-center">
                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#scheduleModal{{$schedule->id}}">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                                <td>{{ $name }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal{{$schedule->id}}">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <!-- <button class="btn btn-primary btn-sm" onclick="UpdateConfigSched( {{$schedule->id}} )">
                                        <i class="fa fa-pencil"></i>
                                    </button> -->
                                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash" data-toggle="modal" data-target="#deleteConfig{{$schedule->id}}"></i></button> 
                                </td>
                            </tr>
                            <!-- Modal Structure for each schedule -->
                            <div class="modal fade" id="scheduleModal{{$schedule->id}}" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel{{$schedule->id}}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="scheduleModalLabel{{$schedule->id}}">
                                                <i class="fa fa-user-md" aria-hidden="true"></i>
                                                <strong>Doctor Schedule for {{$schedule->category}}</strong>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="list-group">
                                                @php
                                                    // Split days and times into arrays
                                                    $days = explode('|', $schedule->days);
                                                    $times = explode('|', $schedule->time);

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

                                <!-- Update Config Appointment -->
                                <div class="modal fade" role="dialog" id="editModal{{$configId}}" data-backdrop="static" data-keyboard="false" aria-labelledby="editModalLabel{{$configId}}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                    <fieldset>
                                                        <legend>
                                                            <i class="fa fa-calendar-plus-o"></i> Update Config Appointment
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="Add-close-apppoint">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </legend>
                                                    </fieldset>
                                                <form id="UpdateAppointmentConfigModal" action="{{ route('edit.configSched') }}" method="POST">

                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="label-border">

                                                                    <label for="configdesc">Description:</label>
                                                                    <input type="text" class="form-control" name="Updateconfigdesc" id="Configdesc" value="{{$schedule->description}}" required>
                                                                    <input type="hidden" name="configId" value="{{$configId}}">
                                                                   
                                                                    <label for="update_opdCategory">Department Category:</label>
                                                                    <select class="form-control select2" id="add_department" name="update_department_id" required>
                                                                        <option selected value="">Select Department Category</option>
                                                                    @foreach($department as $dept)
                                                                        <option value="{{$dept->id}}" @if($dept->id === $schedule->department_id) selected @endif>{{$dept->description}}</option>
                                                                    @endforeach
                                                                    </select>

                                                                    <label for="defaultCategory">Default Category:</label>
                                                                    <select class="form-control select2" id="defaultCategory" name="Update_default_category" required>
                                                                        <option selected value="">Select Default Category</option>
                                                                        <option value="1 Week" @if($schedule->category === '1 Week') selected @endif>1 Week</option>
                                                                        <option value="1 Month"  @if($schedule->category === '1 Month') selected @endif>1 Month</option>
                                                                    </select>
                                                                
                                                                    <label for="Facility">Facility:</label>
                                                                    <input type="hidden" name="Updatefacility_id" value="{{$fact[0]->id}}">
                                                                    <input type="text" class="form-control" name="Update_facility_name" id="Facility" value="{{ $fact[0]->name }}" required disabled>
                                                                </div>
                                                            </div>
                                                            @php
                                                                $days_available = explode('|', $schedule->days);
                                                                $timeSlots = []; 
                                                                $timeEntries = explode('|', $schedule->time);

                                                                $check_days = ["Monday", "Tuesday", "Wednesday","Thursday","Friday", "Saturday", "Sunday"];

                                                                $currentDay = null;
                                                                foreach($timeEntries as $entry){
                                                                    if(preg_match('/[a-zA-Z]+/', $entry)){
                                                                        $currentDay = $entry;
                                                                    }else{
                                                                        $timeSlots[$currentDay][] = $entry;
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class="col-md-6">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <strong>Repeat</strong>
                                                                    </div>
                                                                    <div class="panel-body">
                                                                        @foreach($check_days as $day)
                                                                            @php
                                                                                $isAvailable = in_array($day, $days_available);
                                                                            @endphp
                                                                            <div class="checkbox">
                                                                                <label>
                                                                                    <input type="checkbox" class="update-day-checkbox" name="updatedays[]" value="{{ $day }}" {{ $isAvailable  ? 'checked' : '' }}>
                                                                                    {{ $day }}
                                                                                </label>
                                                                                <div class="time-slots" style="margin-left: 20px; {{ $isAvailable ? '' : 'display:none;' }}">
                                                                                    @if($isAvailable && isset($timeSlots[$day]))
                                                                                        @foreach($timeSlots[$day] as $index => $slot)
                                                                                            @php
                                                                                                [$timeFrom, $timeTo] = explode('-', $slot);
                                                                                            @endphp
                                                                                            <div class="row time-slot_edit">
                                                                                                <div class="col-md-5">
                                                                                                    <label>Time From:</label>
                                                                                                    <input type="time" name="update_time_from[{{ $day }}][]" class="form-control input-sm" value="{{ $timeFrom }}">
                                                                                                </div>
                                                                                                <div class="col-md-5">
                                                                                                    <label>Time To:</label>
                                                                                                    <input type="time" name="update_time_to[][]" class="form-control input-sm" value="{{ $timeTo }}">
                                                                                                </div>
                                                                                                <div class="col-md-2">
                                                                                                    @if($index > 0)
                                                                                                        <button type="button" class="btn btn-danger btn-sm update_remove-time-slot">
                                                                                                            <i class="fa fa-trash"></i>
                                                                                                        </button>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    @else
                                                                                    <div class="row time-slot_edit">
                                                                                        <div class="col-md-5">
                                                                                            <label>Time From:</label>
                                                                                            <input type="time" name="update_time_from[{{ $day }}][]" class="form-control input-sm">
                                                                                        </div>
                                                                                        <div class="col-md-5">
                                                                                            <label>Time To:</label>
                                                                                            <input type="time" name="update_time_to[{{ $day }}][]" class="form-control input-sm">
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <button type="button" class="btn btn-danger btn-sm update_remove-time-slot">
                                                                                                <i class="fa fa-trash"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                    @endif
                                                                                    <button type="button" class="btn btn-primary btn-xs Update-time-slot" data-day="{{ $day }}" style="margin-top: 10px;">
                                                                                        <i class="fa fa-plus"></i> Add Time Slot
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="Add_Cancel_appointment">
                                                            <i class="fa fa-times"></i> Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fa fa-send"></i> Submit
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Delete Config Appointment -->
                                <div class="modal fade" role="dialog" id="deleteConfig{{$configId}}" data-backdrop="static" data-keyboard="false" aria-labelledby="deleteModalLabel{{$configId}}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <form id="deleteAppointmentConfigModal" action="{{ route('delete.configSched') }}" method="POST">
                                                    {{ csrf_field() }}
                                                    <fieldset>
                                                        <legend>
                                                            <i class="fa fa-calendar-plus-o"></i> Delete Config Appointment
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="Add-close-apppoint">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </legend>
                                                    </fieldset>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="label-border">

                                                                    <label for="configdesc">Description:</label>
                                                                    <input type="text" class="form-control" name="deleteconfigdesc" id="Configdesc" value="{{$schedule->description}}" required disabled>
                                                                    <input type="hidden" name="configId" value="{{$configId}}">
                                                                    <label for="update_opdCategory">Department Category:</label>
                                                                    <select class="form-control select2" id="add_department" name="delete_department_id" required disabled>
                                                                        <option selected value="">Select Department Category</option>
                                                                    @foreach($department as $dept)
                                                                        <option value="{{$dept->id}}" @if($dept->id === $schedule->department_id) selected @endif>{{$dept->description}}</option>
                                                                    @endforeach
                                                                    </select>

                                                                    <label for="defaultCategory">Default Category:</label>
                                                                    <select class="form-control select2" id="defaultCategory" name="delete_default_category" required disabled>
                                                                        <option selected value="">Select Default Category</option>
                                                                        <option value="1 Week" @if($schedule->category === '1 Week') selected @endif>1 Week</option>
                                                                        <option value="1 Month"  @if($schedule->category === '1 Month') selected @endif>1 Month</option>
                                                                    </select>
                                                                
                                                                    <label for="Facility">Facility:</label>
                                                                    <input type="hidden" name="deletefacility_id" value="{{$fact[0]->id}}">
                                                                    <input type="text" class="form-control" name="delete_facility_name" id="Facility" value="{{ $fact[0]->name }}" required disabled>
                                                                </div>
                                                            </div>
                                                            @php
                                                                $days_available = explode('|', $schedule->days);
                                                                $timeSlots = []; 
                                                                $timeEntries = explode('|', $schedule->time);

                                                                $check_days = ["Monday", "Tuesday", "Wednesday","Thursday","Friday", "Saturday", "Sunday"];

                                                                $currentDay = null;
                                                                foreach($timeEntries as $entry){
                                                                    if(preg_match('/[a-zA-Z]+/', $entry)){
                                                                        $currentDay = $entry;
                                                                    }else{
                                                                        $timeSlots[$currentDay][] = $entry;
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class="col-md-6">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <strong>Repeat</strong>
                                                                    </div>
                                                                    <div class="panel-body">
                                                                    @foreach($check_days as $day)
                                                                        @php
                                                                            $isAvailable = in_array($day, $days_available);
                                                                        @endphp
                                                                                <div class="checkbox">
                                                                                    <label>
                                                                                        <input type="checkbox" class="delete-day-checkbox" name="deletedays[]" value="{{ $day }}" {{ $isAvailable  ? 'checked' : '' }} disabled>
                                                                                        {{ $day }}
                                                                                    </label>
                                                                                    <div class="time-slots" style="margin-left: 20px; {{ $isAvailable ? '' : 'display:none;' }}">
                                                                                        @if($isAvailable && isset($timeSlots[$day]))
                                                                                            @foreach($timeSlots[$day] as $slot)
                                                                                                @php
                                                                                                    [$timeFrom, $timeTo] = explode('-', $slot);
                                                                                                @endphp
                                                                                                <div class="row time-slot">
                                                                                                    <div class="col-md-5">
                                                                                                        <label>Time From:</label>
                                                                                                        <input type="time" name="delete_time_from[{{ $day }}][]" class="form-control input-sm" value="{{ $timeFrom }}" disabled>
                                                                                                    </div>
                                                                                                    <div class="col-md-5">
                                                                                                        <label>Time To:</label>
                                                                                                        <input type="time" name="delete_time_to[{{ $day }}][]" class="form-control input-sm" value="{{ $timeTo }}" disabled>
                                                                                                    </div>
                                                                                                    <div class="col-md-2">
                                                                                                        <button type="button" class="btn btn-danger btn-sm delete_remove-time-slot" disabled>
                                                                                                            <i class="fa fa-trash"></i>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        @endif
                                                                                        <button type="button" class="btn btn-primary btn-xs delete-time-slot" data-day="{{ $day }}" style="margin-top: 10px;" disabled>
                                                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                    @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="Add_Cancel_appointment">
                                                            <i class="fa fa-times"></i> Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fa fa-send"></i> Submit
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    </table>
                    <div class="text-center">
                      
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
</div>
    <!-- Add Config Appointment -->
    <div class="modal fade" role="dialog" id="addconfigModal" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="addAppointmentConfigModal" action="{{ route('add.configSched') }}" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend>
                                <i class="fa fa-calendar-plus-o"></i> Add Config Appointment
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="Add-close-apppoint">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="label-border">

                                        <label for="configdesc">Description:</label>
                                        <input type="text" class="form-control" name="configdesc" id="Configdesc" required>

                                        <label for="add_opdCategory">Department Category:</label>
                                        <select class="form-control select2" id="add_department" name="department_id" required>
                                            <option selected value="">Select Department Category</option>
                                        @foreach($department as $dept)
                                            <option value="{{$dept->id}}">{{$dept->description}}</option>
                                        @endforeach
                                        </select>

                                        <label for="defaultCategory">Default Category:</label>
                                        <select class="form-control select2" id="defaultCategory" name="default_category" required>
                                            <option selected value="">Select Default Category</option>
                                            <option value="1 Week">1 Week</option>
                                            <option value="1 Month">1 Month</option>
                                        </select>

                                        <label for="defaultCategory">Date Range:</label>
                                        <!-- <input type="date" name="date_range" class="form-control"  id="dateRange"> -->
                                        <input type="text" class="form-control" name="date_range" id="config_date_range" readonly>

                                    
                                        <label for="Facility">Facility:</label>
                                        <input type="hidden" name="facility_id" value="{{$fact[0]->id}}">
                                        <input type="text" class="form-control" name="facility_name" id="Facility" value="{{ $fact[0]->name }}" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <strong>Repeat</strong>
                                        </div>
                                        <div class="panel-body">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="day-checkbox" name="days[]" value="Monday"> Monday
                                                </label>
                                                <div class="time-slots" style="margin-left: 20px; display:none;">
                                                    <div class="row time-slot">
                                                        <div class="col-md-5">
                                                            <label>Time From:</label>
                                                            <input type="time" name="time_from[Monday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Time To:</label>
                                                            <input type="time" name="time_to[Monday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-time-slot days-checkbox">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-xs add-time-slot" data-day="Monday">
                                                        <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="day-checkbox" name="days[]" value="Tuesday"> Tuesday
                                                </label>
                                                <div class="time-slots" style="margin-left: 20px; display:none;">
                                                    <div class="row time-slot">
                                                        <div class="col-md-5">
                                                            <label>Time From:</label>
                                                            <input type="time" name="time_from[Tuesday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Time To:</label>
                                                            <input type="time" name="time_to[Tuesday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-time-slot days-checkbox">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-xs add-time-slot" data-day="Tuesday">
                                                        <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="day-checkbox" name="days[]" value="Wednesday"> Wednesday
                                                </label>
                                                <div class="time-slots" style="margin-left: 20px; display:none;">
                                                    <div class="row time-slot">
                                                        <div class="col-md-5">
                                                            <label>Time From:</label>
                                                            <input type="time" name="time_from[Wednesday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Time To:</label>
                                                            <input type="time" name="time_to[Wednesday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-time-slot days-checkbox">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-xs add-time-slot" data-day="Wednesday">
                                                        <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="day-checkbox" name="days[]" value="Thursday"> Thursday
                                                </label>
                                                <div class="time-slots" style="margin-left: 20px; display:none;">
                                                    <div class="row time-slot">
                                                        <div class="col-md-5">
                                                            <label>Time From:</label>
                                                            <input type="time" name="time_from[Thursday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Time To:</label>
                                                            <input type="time" name="time_to[Thursday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-time-slot days-checkbox">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-xs add-time-slot" data-day="Thursday">
                                                        <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="day-checkbox" name="days[]" value="Friday"> Friday
                                                </label>
                                                <div class="time-slots" style="margin-left: 20px; display:none;">
                                                    <div class="row time-slot">
                                                        <div class="col-md-5">
                                                            <label>Time From:</label>
                                                            <input type="time" name="time_from[Friday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Time To:</label>
                                                            <input type="time" name="time_to[Friday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-time-slot days-checkbox">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-xs add-time-slot" data-day="Friday">
                                                        <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="day-checkbox" name="days[]" value="Saturday"> Saturday
                                                </label>
                                                <div class="time-slots" style="margin-left: 20px; display:none;">
                                                    <div class="row time-slot">
                                                        <div class="col-md-5">
                                                            <label>Time From:</label>
                                                            <input type="time" name="time_from[Saturday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Time To:</label>
                                                            <input type="time" name="time_to[Saturday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-time-slot days-checkbox">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-xs add-time-slot" data-day="Saturday">
                                                        <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="day-checkbox" name="days[]" value="Sunday"> Sunday
                                                </label>
                                                <div class="time-slots" style="margin-left: 20px; display:none;">
                                                    <div class="row time-slot">
                                                        <div class="col-md-5">
                                                            <label>Time From:</label>
                                                            <input type="time" name="time_from[Sunday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Time To:</label>
                                                            <input type="time" name="time_to[Sunday][]" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-time-slot days-checkbox">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-xs add-time-slot" data-day="Sunday">
                                                        <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="Add_Cancel_appointment">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-send"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    </div>

@endsection

@section('js')

<script>

// function UpdateConfig(configId) {
//     console.log('UpdateConfig called with ID:', configId);
//     $('#UpdateconfigModal').modal('show'); 
// }

$(document).ready(function() {

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
            endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 7); // Add 7 days
        } else if (selectedCategory === '1 Month') {
           
            endDate = new Date(startDate);
            endDate.setMonth(startDate.getMonth() + 1); // Add 1 month
        }

        var formattedStartDate = formatDate(startDate);
        var formattedEndDate = formatDate(endDate);

        dateRangeInput.val(formattedStartDate + ' - ' + formattedEndDate);

    });

    // Helper function to format the date as m/d/Y
    function formatDate(date) {
    var month = date.getMonth() + 1; // Month is 0-based, so we add 1
    var day = date.getDate();
    var year = date.getFullYear();
    
    return month + '/' + day + '/' + year;
    }

});
    // Add Config
    $(document).on('click', '.add-time-slot', function() {
        let day = $(this).data('day');
        let timeSlot = 
            `<div class="row time-slot">
                <div class="col-md-5">
                    <label>Time From:</label>
                    <input type="time" name="time_from[${day}][]" class="form-control input-sm">
                </div>
                <div class="col-md-5">
                    <label>Time To:</label>
                    <input type="time" name="time_to[${day}][]" class="form-control input-sm">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-time-slots days-checkbox">
                        <i class="fa fa-trash"></i>
                    </button>   
                </div>
            </div>`;

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

//*********************update Config Set multiple time********************//

        $(document).on('click', '.Update-time-slot', function () {
            let day = $(this).data('day');

            let timeSlot = `
            <div class="row time-slot_edit">
                <div class="col-md-5">
                    <label>Time From:</label>
                    <input type="time" name="update_time_from[${day}][]" class="form-control input-sm update_time_from">
                </div>
                <div class="col-md-5">
                    <label>Time To:</label>
                    <input type="time" name="update_time_to[${day}][]" class="form-control input-sm update_time_to">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm update_remove-time-slot">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>`;
            $(this).before(timeSlot);
        });

        // Remove time slot
        $(document).on('click', '.update_remove-time-slot', function () {
            $(this).closest('.time-slot_edit').remove();
        });


        // Show or hide time slots based on checkbox
        $(document).on('change', '.update-day-checkbox', function () {
            let checkboxContainer = $(this).closest('.checkbox');
            if ($(this).is(':checked')) {
                checkboxContainer.find('.time-slots').slideDown();
            } else {
                let timeSlots = checkboxContainer.find('.time-slots');
                timeSlots.slideUp();
                timeSlots.find('input[type="time"]').val('');
                timeSlots.find('.time-slot:not(:first)').remove();
            }
        });


    
</script>
@endsection
