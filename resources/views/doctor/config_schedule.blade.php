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
$subOpd = App\SubOpd::get();

?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right form-inline">
                <div class="form-group" style="margin-bottom: 10px;">
                    <input type="text" class="form-control" name="appt_keyword" value="{{ $keyword }}" id="keyword" placeholder="Search...">
                    <button type="submit" class="btn btn-success btn-sm btn-flat">
                        <i class="fa fa-search"></i> Search
                    </button>

                    <button type="button" class="btn btn-primary btn-sm btn-flat" id="add-appointment">
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
             <h3 class="upcoming-title">
                <i class="fa fa-calendar"></i> Config Schedule
            </h3>          
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
                                    {{$schedule->subOpdCateg->description}}
                                </td>
                                <td>{{ $facility->name }}</td>
                                <td>{{ $schedule->category }}</td>
                                <td class="icon-center">
                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#scheduleModal{{$schedule->id}}">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                               
                                 <td>
                                    {{$schedule->creator->fname}}
                                    {{$schedule->creator->mname}}
                                    {{$schedule->creator->lname}}

                                 </td>
                                
                                <td class="text-center">
                                    <!-- <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal{{$schedule->id}}">
                                        <i class="fa fa-pencil"></i>
                                    </button> -->

                                    <button class="btn btn-primary btn-sm" onclick="UpdateConfig({{$schedule->id}})">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <!-- do not delete this delete button -->
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
                                                                <label for="update_opdCategory">OPD Category:</label>
                                                                <input type="text" class="form-control" value="{{$schedule->subopd_id}}" disabled>
                                                                <input type="hidden" class="form-control" name="delete_department_id" value="{{$schedule->subopd_id}}">

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
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i> Delete
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
    <!-- UpdATE Config IT -->
    <div class="modal fade" role="dialog" id="IdetconfigModal" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="EditConfigIt" action="{{ route('edit.configSched') }}" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend>
                                <i class="fa fa-calendar-plus-o"></i> Update Config Appointment
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
                                        <input type="text" class="form-control" name="configdesc" id="Config_desc" required>
                                        <input type="hidden" id="update-config_id" name="edit_configId">

                                        <input type="hidden" name="department_id" id="" value="5">

                                        <label for="add_opdCategory">Opd Category:</label>
                                        <!-- <select class="form-control select2" id="editdepartment_config" name="subopd_id" required>
                                            <option selected value="">Select Opd Category</option>
                                            @foreach($subOpd as $sub)
                                                <option value="{{$sub->id}}">{{ $sub->description}}</option>
                                            @endforeach              
                                        </select> -->

                                        <input type="hidden" name="subopd_id" value="{{ $userInfo->subopd_id }}">
                                        <input type="text" class="form-control" value="{{  App\SubOpd::find($userInfo->subopd_id)->description }}" required readonly>

                                        <label for="defaultCategory">Default Category:</label>
                                        <select class="form-control select2" id="edit_default_Category" name="default_category" required>
                                            <option selected value="">Select Default Category</option>
                                            <option value="1 Week">1 Week</option>
                                            <option value="1 Month">1 Month</option>
                                        </select>
                                    
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
                                                    <input type="checkbox" class="update-day-checkbox" name="days[]" value="Monday"> Monday
                                                </label>
                                                <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                    <button type="button" class="btn btn-primary btn-xs Update-time-slot" data-day="Monday" style="margin-top: 10px;">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="update-day-checkbox" name="days[]" value="Tuesday"> Tuesday
                                                </label>
                                                <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                    <button type="button" class="btn btn-primary btn-xs Update-time-slot" data-day="Tuesday" style="margin-top: 10px;">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="update-day-checkbox" name="days[]" value="Wednesday"> Wednesday
                                                </label>
                                                <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                    <button type="button" class="btn btn-primary btn-xs Update-time-slot" data-day="Wednesday" style="margin-top: 10px;">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="update-day-checkbox" name="days[]" value="Thursday"> Thursday
                                                </label>
                                                <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                    <button type="button" class="btn btn-primary btn-xs Update-time-slot" data-day="Thursday" style="margin-top: 10px;">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="update-day-checkbox" name="days[]" value="Friday"> Friday
                                                </label>
                                                <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                    <button type="button" class="btn btn-primary btn-xs Update-time-slot" data-day="Friday" style="margin-top: 10px;">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="update-day-checkbox" name="days[]" value="Saturday"> Saturday
                                                </label>
                                                <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                    <button type="button" class="btn btn-primary btn-xs Update-time-slot" data-day="Saturday" style="margin-top: 10px;">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="update-day-checkbox" name="days[]" value="Sunday"> Sunday
                                                </label>
                                                <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                    <button type="button" class="btn btn-primary btn-xs Update-time-slot" data-day="Sunday" style="margin-top: 10px;">
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
                                    <input type="hidden" name="department_id" id="" value="5">                          
                                    <label for="add_opdCategory">OPD Category:</label>
                                    <!-- <select class="form-control select2" id="add_department" name="subOpd_id" required>
                                        <option selected value="">Select OPD Category</option>
                                        @foreach($subOpd as $sub)
                                            <option value="{{$sub->id}}" @if($userInfo->subopd_id == $sub->id) selected @endif>{{ $sub->description}}</option>
                                        @endforeach 
                                    </select>-->
                                
                                    <input type="hidden" name="subOpd_id" value="{{ $userInfo->subopd_id }}">
                                    <input type="text" class="form-control" value="{{  App\SubOpd::find($userInfo->subopd_id)->description }}" required readonly>
                                 
                                    <label for="defaultCategory">Default Category:</label>
                                    <select class="form-control select2" id="defaultCategory" name="default_category" required>
                                        <option selected value="">Select Default Category</option>
                                        <option value="1 Week">1 Week</option>
                                        <option value="1 Month">1 Month</option>
                                    </select>
                                
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
$(document).ready(function() {
    
    //Add Time Slot Validation
    let usertype = @json($userInfo);
    // console.log("usertype", usertype);
    $("#add-appointment").click(function (e) {
        if(usertype.level === "doctor"){
            $("#addconfigModal").modal("show");
  
        }else{
            e.preventDefault();
            Lobibox.alert("error", {
                msg: "You are not authorized to add a Config appointment. Only doctors can access this feature."
            });
        }

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
    document.getElementById("addAppointmentConfigModal").addEventListener("submit", function(event){
        let checkboxes = document.querySelectorAll(".day-checkbox");
        let isChecked = Array.from(checkboxes).some(check => check.checked);

        if(!isChecked){
            Lobibox.alert("error",
            {
                msg: `Please select at least one day!`
            });
            event.preventDefault();
        }
        
    });

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

    //will automatic populate the first time slot
    $(document).ready(function() {
        $('.day-checkbox').on('change', function() {
            // Get the parent time-slots div
            var timeSlotDiv = $(this).closest('.checkbox').find('.time-slots');
            var day = $(this).val(); // Get the day value from the checkbox
            
            if ($(this).is(':checked')) {
                // Show the time slots div
                timeSlotDiv.show();
                
                // Check if this is the first time slot for this day
                if (timeSlotDiv.find('.time-slot').length === 0) {
                    // Add the first time slot automatically
                    let firstTimeSlot = `
                    <div class="row time-slot">
                        <div class="col-md-5">
                            <label>Time From:</label>
                            <input type="time" name="time_from[${day}][]" class="form-control input-sm" required>
                        </div>
                        <div class="col-md-5">
                            <label>Time To:</label>
                            <input type="time" name="time_to[${day}][]" class="form-control input-sm" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm remove-time-slots">
                                <i class="fa fa-trash"></i>
                            </button>   
                        </div>
                    </div>`;
                    
                    // Add the time slot before the "Add Time Slot" button
                    timeSlotDiv.find('.add-time-slot').before(firstTimeSlot);
                }
            } else {
                timeSlotDiv.hide();
                timeSlotDiv.find('.time-slot').remove(); 
            }
        });
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

    $(document).on('change', 'input[type="time"]', function () {

        validateAllDays();
        
    });

    function validateAllDays(){

        let now = new Date();
        let today = now.toISOString().split('T')[0]; 
        let allSelectedTimes = {};

        $('.day-checkbox:checked').each(function () {

            let day = $(this).val(); // Get the selected day
            let timeSlots = $(this).closest('.checkbox').find('.time-slot');

            allSelectedTimes[day] = [];

            timeSlots.each(function () {
                let timeFrom = $(this).find('input[name^="time_from"]').val();
                let timeTo = $(this).find('input[name^="time_to"]').val();
            
                if(timeFrom && timeTo){
                    let startTime = new Date(`${today}T${timeFrom}`);
                    let endTime = new Date(`${today}T${timeTo}`);

                    if (startTime >= endTime) {
                        Lobibox.alert("error",
                        {
                            msg: `End time for ${day} must be after start time!`
                        });

                        $(this).find('input[name^="time_to"]').val("");
                        return;
                    }

                   for ( let prevSlot of allSelectedTimes[day]){
                     let prevStart = prevSlot.start;
                     let prevEnd =  prevSlot.end;

                     if (
                            (startTime >= prevStart && startTime < prevEnd) ||  
                            (endTime > prevStart && endTime <= prevEnd) || 
                            (startTime <= prevStart && endTime >= prevEnd)  
                        ) {
                            Lobibox.alert("error",
                            {
                                msg: `Time slot conflicts with an existing slot on ${day}!`
                            });

                            $(this).find('input[name^="time_from"]').val("");
                            $(this).find('input[name^="time_to"]').val("");
                            return;
                        }
                   }

                    allSelectedTimes[day].push({ start: startTime, end: endTime});
                 
                }
            });
        });
    }


//*********************update Config Set multiple time********************//
// remove the null value in request name attribute
    $('#EditConfigIt').on('submit', function (e) {
        
        $('input[name^="update_time_from"]').each(function () {
            if(!$(this).val()){
                $(this).remove();
            }
        });

        $('input[name^="update_time_to"]').each(function (){
            if(!$(this).val()){
                $(this).remove();
            }
        });
    });

    function UpdateConfig(configId, selectedDay = null) {
        
        $.ajax({
            url: `get-config/${configId}`,
            method: 'GET',
            success: function(res) {
                // console.log("res.deparment_subcategory::", res.subOpdId);
                const days = res.days;
                const times = res.times;
                const category = res.category.trim();
        
                $('#Config_desc').val(res.descript);
                $('#editdepartment_config').val(res.subOpdId).trigger('change');
                $('#edit_default_Category').val(category).trigger('change');
                $('#update-config_id').val(res.configId)

                $('.Update-time-slots').hide().find(".time-slots").remove();
                $(`.update-day-checkbox`).prop("checked", false);
                $('.edit-time-slots').hide().find(".remove-time-slot").remove();

                // $('.update-day-checkbox').prop('checked', false);
                // $('.edit-time-slots').hide().empty();
                days.forEach(day => {
                    if (selectedDay === null || selectedDay === day) {
                        $(`.update-day-checkbox[value="${day}"]`).prop("checked", true);

                        const $timeSlotsDiv = $(`input[name="days[]"][value="${day}"]`).closest('.checkbox').find('.edit-time-slots');
                        
                        $timeSlotsDiv.show();
                        const $addButton = $timeSlotsDiv.find('.Update-time-slot');
                        // $timeSlotsDiv.find('.Update-time-slots').remove();
                        
                        const dayTimes = times[day] || [];
                        dayTimes.forEach(slot => {
                            const [timeFrom, timeTo] = slot.split('-');
                            // console.log("dayTimes", timeTo);
                            const timeSlotHtml = `
                                <div class="row Update-time-slots" data-day="${day}"> 
                                    <div class="col-md-5">
                                        <label>Time From:</label>
                                        <input type="time" name="update_time_from[${day}][]" class="form-control input-sm" value="${timeFrom}">
                                    </div>
                                    <div class="col-md-5">
                                        <label>Time To:</label>
                                        <input type="time" name="update_time_to[${day}][]" class="form-control input-sm" value="${timeTo}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-time-slot_edit" style="margin-top: 30px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                     <input type="hidden" class="time-slot-day" value="${day}">
                                </div>`;
                            $(timeSlotHtml).insertBefore($addButton);
                        });
                    }
                });
            }, 
            error: function(error) {
                console.error('Error fetching config:', error);
            }
        })

        $('#IdetconfigModal').modal('show');
    }

    //Remove Slot
    $(document).on("click", ".remove-time-slot_edit", function () {

        let $slotRow = $(this).closest(".Update-time-slots");


        let day = $slotRow.find('.time-slot-day').val();  

        let timeFrom = $slotRow.find('input[name^="update_time_from"]').val();
        let timeTo = $slotRow.find('input[name^="update_time_to"]').val();
        let configId = $("#update-config_id").val();

        // console.log("my day::", day);

        var urlSlot = "<?php echo asset('/remove-time-slot') ?>";

        Lobibox.confirm({
            msg: "Are you sure you want to remove this time slot?",
            callback: function ($this, type, ev) {
                if(type === 'yes') {
                    var json = {
                        "_token" : "<?php echo csrf_token(); ?>",
                        "code" : code
                    };
                    $.ajax({
                        url: urlSlot,
                        method: "POST",
                        data: {
                            "_token": "<?php echo csrf_token(); ?>",
                            configId: configId,
                            timeFrom: timeFrom,
                            timeTo: timeTo,
                            day: day,
                        },
                        success: function (response){

                            // console.log("my TimeSlot Price", response.message);
                            if(response.message){
                                $slotRow.remove();
                                Lobibox.alert("success", {
                                    msg: "Time Slot succesfully removed."
                                });
                            }
                            else{
                                Lobibox.alert("error", {
                                    msg: "Failed to remove time slot."
                                });
                            }
                        },
                        error: function (error) {
                            console.error("Error removing time slot:", error);
                            alert("An error occurred.");
                        },
                    });
                }
            }
        });

        // $(this).closest('.Update-time-slots').remove();

    });

    $(document).on('click', '.Update-time-slot', function () {
        let day = $(this).data('day');
   
        let timeSlot = `
        <div class="row Update-time-slots">
            <div class="col-md-5">
                <label>Time From:</label>
                <input type="time" name="update_time_from[${day}][]" class="form-control input-sm update_time_from" required>
            </div>
            <div class="col-md-5">
                <label>Time To:</label>
                <input type="time" name="update_time_to[${day}][]" class="form-control input-sm update_time_to" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm editremove-time-slot" style="margin-top: 30px;">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>`;
        $(this).before(timeSlot);
    });

    // Remove a time slot
    $(document).on('click', '.editremove-time-slot', function () {
        $(this).closest('.Update-time-slots').remove();
    });

    //will automatic populate the first time slot
    $(document).ready(function() {
   
        $('.update-day-checkbox').on('change', function() {
           
            var timeSlotDiv = $(this).closest('label').siblings('.edit-time-slots');
            var day = $(this).val(); // Get the day value from the checkbox
            
            if ($(this).is(':checked')) {
                // Show the time slots div
                timeSlotDiv.show();
                
                // Add the first time slot automatically
                let firstTimeSlot = `
                <div class="row Update-time-slots">
                    <div class="col-md-5">
                        <label>Time From:</label>
                        <input type="time" name="update_time_from[${day}][]" class="form-control input-sm update_time_from" required>
                    </div>
                    <div class="col-md-5">
                        <label>Time To:</label>
                        <input type="time" name="update_time_to[${day}][]" class="form-control input-sm update_time_to" required>
                    </div>
                </div>`;
                
                // Add the time slot before the "Add Time Slot" button
                timeSlotDiv.find('.Update-time-slot').before(firstTimeSlot);
            } else {
                // If unchecked, hide the time slots div and remove any added time slots
                timeSlotDiv.hide();
                timeSlotDiv.find('.Update-time-slots').remove();
            }
        });

    });

    // Show or hide time slots based on checkbox
    $(document).on('change', '.update-day-checkbox', function () {
        let checkboxContainer = $(this).closest('.checkbox');
        if ($(this).is(':checked')) {
            checkboxContainer.find('.edit-time-slots').slideDown();
        } else {
            let timeSlots = checkboxContainer.find('.edit-time-slots');
            timeSlots.slideUp();
            timeSlots.find('input[type="time"]').val('');
            timeSlots.find('.edit-time-slots:not(:first)').remove();
        }
    });

    $(document).on('change', `[name^="update_time_from"], [name^="update_time_to"]`, function () {

        EditDays();

    });

    function EditDays(){

        let now = new Date();
        let today = now.toISOString().split('T')[0]; 
        let getSelectedTimes = {}; 

        $('.update-day-checkbox:checked').each(function () {

            let day = $(this).val(); // Get the selected day
            let timeSlots = $(this).closest('.checkbox').find('.Update-time-slots');
            
            getSelectedTimes[day] = [];

            timeSlots.each(function () {

                let edit_timeFrom = $(this).find('input[name^="update_time_from"]').val();
                let edit_timeTo = $(this).find('input[name^="update_time_to"]').val();

                if(edit_timeFrom && edit_timeTo){
                    let edit_startTime = new Date(`${today}T${edit_timeFrom}`);
                    let edit_endTime = new Date(`${today}T${edit_timeTo}`);

                    if (edit_startTime >= edit_endTime) {

                        Lobibox.alert("error",
                        {
                            msg: `End time for ${day} must be after start time!`
                        });
                       
                        $(this).find('input[name^="update_time_to"]').val("");
                        return;
                    }

                    for ( let prevSlot of getSelectedTimes[day]){
                     let prevStart = prevSlot.start;
                     let prevEnd =  prevSlot.end;

                     if (
                            (edit_startTime >= prevStart && edit_startTime < prevEnd) ||  
                            (edit_endTime > prevStart && edit_endTime <= prevEnd) || 
                            (edit_startTime <= prevStart && edit_endTime >= prevEnd)  
                        ) {
                            Lobibox.alert("error",
                            {
                                msg: `Time slot conflicts with an existing slot on ${day}!`
                            });
                          
                            $(this).find('input[name^="update_time_from"]').val("");
                            $(this).find('input[name^="update_time_to"]').val("");
                            return;
                        }
                   }

                   getSelectedTimes[day].push({ start: edit_startTime, end: edit_endTime});

                }
            });
        });
    }

    // clear all previous open modal

    function clearConfigModalform() {

        $('.update-day-checkbox').prop('checked', false);

        $('.edit-time-slots').hide();

        $('.Update-time-slots').remove();

        $('#EditConfigIt')[0].reset();
    }

    $('#IdetconfigModal').on('show.bs.modal', function (e){
        
        clearConfigModalform();
    });



</script>
@endsection
