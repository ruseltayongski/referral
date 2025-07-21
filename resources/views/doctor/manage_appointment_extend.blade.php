    <?php 
            $appointmentSconfig =  \App\AppointmentSchedule::select('id','configId','opdCategory','appointed_date','date_end')->get(); 
            $configs = \App\Cofig_schedule::select('id','department_id', 'subopd_id','facility_id','description')
                        ->where('department_id', 5)
                        ->where('facility_id',  $user->facility_id)
                        ->where('subopd_id', $user->subopd_id)->get();
    ?>
    <!-- Add Modal -->
    <div class="modal fade" role="dialog" id="addAppointmentModal" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="addAppointmentForm_add" action="{{ route('create-appointment') }}" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend><i class="fa fa-calendar-plus-o"></i> Add Appointment 
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="Add-close-apppoint">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    @if($user->level != 'support')
                                        <div class="appointment-container">
                                            <div class="form-check form-check-inline d-flex">
                                                <input type="checkbox" class="form-check-input custom-checkbox" id="enable_config_appointment" name="enable_appointment" value="config appointment">
                                                <label class="form-check-label custom-label-config" for="enable_appointment">Appointment Config <small><i>(Optional)</i></small></label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="label-border">
                                
                                        <div style="display: none;" id="side_Config">
                                            <label for="appointed_date" id="effective_label">Effective Date:</label>   <!-- Config Appointment -->
                                            <input type="date" class="form-control Effective_date" name="effective_date" id="effective_date">
                                            
                                            <label for="defaultCategory" >Choose default schedule: </label><!-- Config Appointment -->
                                            <select class="form-control select2" id="defaultCategorySelect" name="config_id">  <!-- Config Appointment -->
                                                <option selected value="">Select Default Category</option>
                                                @foreach($configs as $config)
                                                    <option value="{{$config->id}}">{{$config->description}}</option>
                                                @endforeach
                                                
                                            </select>
                                               
                                            <label for="department_id">Opd Category:</label>
                                            <input type="hidden" class="form-control" name="department_id" id="department_id" value="5">
                                            <input type="hidden" name="subopd_id" value="{{ $user->subopd_id }}">
                                            <input type="text" class="form-control"  value="{{ $getSubOpd->description }}" readonly>
                                        </div>
                                        
                                        <label for="facility_id">Facility:</label>
                                        @foreach($facility as $Facility)
                                            <input type="text" class="form-control" name="facility_id" id="facility_id" value="{{ $Facility->facility->name }}" readonly>
                                            <input type="hidden" class="form-control" name="facility_id" id="id" value="{{ $Facility->facility->id }}">
                                        @endforeach

                                        @if($user->level === 'support')
                                            <label for="add_opdCategory">OPD Category:</label>
                                            <select class="form-control select2" id="add_department" name="addsubOpd_id">
                                                <option selected value="">Select OPD Category</option>
                                                @foreach($subOpd as $sub)
                                                    <option value="{{$sub->id}}">{{ $sub->description }}</option>
                                                @endforeach 
                                            </select>  
                                        @endif

                                        <label for="appointed_date" id="appointment_date_label">Appointment Date:</label>

                                        <input type="date" class="form-control appointment_date" name="appointed_date" id="appointment_date">
                                        <input type="hidden" name="appointment_count" class="appointment_count" value="1">
                                        
                                    </div>
                                </div>

                                <div class="col-md-8" style="display: none;" id="please_select_categ">
                                    <!-- <div class="text-center" style="display: flex; justify-content: center; align-items: center;"> -->
                                        <div class="panel panel-default" style="padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                            <div class="panel-body">
                                                <label class="text-center text-warning" style="margin-top: 10px; font-size: 14px;">
                                                    Select a default schedule to proceed with the appointment setup.
                                                </label>
                                            </div>
                                        </div>
                                    <!-- </div> -->
                                </div>

                                <div class="col-md-8" style="display: none;" id="week_time_slot">
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
                                                                <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm add-time-slot" data-day="Monday">
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
                                                                <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm add-time-slot" data-day="Tuesday">
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
                                                                <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm add-time-slot" data-day="Wednesday">
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
                                                                <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm add-time-slot" data-day="Thursday">
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
                                                                <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm add-time-slot" data-day="Friday">
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
                                                                <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm add-time-slot" data-day="Saturday">
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
                                                                <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm add-time-slot" data-day="Sunday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" style="padding: 0;">
                                                    <label for="slot" style="padding:0;">Slot :</label>
                                                    <input type="number" id="number_slot" name="number_slot" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <p id="SchedCategory"></p>
                                </div>

                                <div class="col-md-8" id="Manual-time-slot">
                                    <div class ="time-input-group">
                                        <div class="label-border">
                                            <div id="opdCategoryContainer">
                                                <div>
                                                    <div class="row">
                                                            <div class="col-md-12">
                                                                <label for="appointed_time">Appointment Time:</label><br>
                                                                <div class="col-md-6">
                                                                    <span>From:</span>
                                                                    <input type="time" class="form-control add-appointment-field" id="add_appointed_time_1"  name="add_appointed_time1">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <span>To:</span>
                                                                    <input type="time" class="form-control add-appointment-field" id="add_appointed_time_to1" name="add_appointed_time_to1">
                                                                </div>
                                                                <div class="col-md-6" style="margin-top: 10px;">
                                                                    <span>Slot:</span>
                                                                    <input type="number" class="form-control" name="slot1">
                                                                </div>
                                                                <br>
                                                                <!-- <div class="col-md-12" id="additionalTimeContainer" style="display: none;"></div>
                                                                <div style="margin-top: 15px;">
                                                                    <button type="button" class="btn btn-info btn-xs" id="add_slots" onclick="addTimeInput()">Add Appointment</button>
                                                                </div> -->
                                                                <div class="col-md-12" id="additionalTimeContainer" style="display: none;"></div> 
                                                                <div class="col-md-12" style="margin-top: 10px;">
                                                                    <button type="button" class="btn btn-info btn-xs" id="add_slots" onclick="addTimeInput()">Add Appointment</button>
                                                                </div>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="Add_Cancel_appointment"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm" id="Addappointment"><i class="fa fa-send"></i> Submit</button>
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


    <!-- Update Config Appointment -->
    <div class="modal fade" role="dialog" id="UpdateConfigAppointment" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="update_configAppointment" action="{{ route('update-doctor-config')}}" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend><i class="fa fa-calendar-plus-o"></i> Update Config Appointment
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="edit-config-close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="label-border">
                                
                                        <label for="appointed_date">Effective Date:</label>   <!-- Config Appointment -->
                                        <input type="date" class="form-control editffective_date" name="edit_effective_date" id="editffective_date">
                                        <input type="hidden" id="Appointment_schedule_id" name="Appointment_schedule_id">
                                        <label for="defaultCategory" >Choose default schedule: </label><!-- Config Appointment -->
                                        <select class="form-control select2" id="editdefaultCateg" name="edit_config_id">  <!-- Config Appointment -->
                                            <option selected value="">Select Default Category</option>
                                            @foreach($configs as $config)
                                                <option value="{{$config->id}}">{{$config->description}}</option>
                                            @endforeach
                                        </select>

                                        {{-- <label for="department_id">OPD Category:</label>
                                            
                                        <input type="hidden" class="form-control" name="edit_department_id" id="department_id" value="5">
                                        <input type="hidden" class="form-control" name="SubOpdId" value="{{ $getSubOpd->id }}">
                                        <input type="text" class="form-control" value="{{ $getSubOpd->description }}" readonly>

                                        <label for="facility_id">Facility:</label>
                                        @foreach($facility as $Facility)
                                                <input type="text" class="form-control" id="facility_id" value="{{ $Facility->facility->name }}" readonly>
                                                <input type="hidden" class="form-control" name="edit_facility_id" id="id" value="{{ $Facility->facility->id }}" readonly>
                                        @endforeach --}}
                                    </div>
                                </div>

                                <div class="col-md-8">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <strong>Repeat</strong>
                                            </div>
                                            <div class="panel-body">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="editday-checkbox" name="editdays[]" value="Monday"> Monday
                                                    </label>
                                                    <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="edit_time_from[Monday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="edit_time_to[Monday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm editadd-time-slot" data-day="Monday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="editday-checkbox" name="editdays[]" value="Tuesday"> Tuesday
                                                    </label>
                                                    <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="edit_time_from[Tuesday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="edit_time_to[Tuesday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm editadd-time-slot" data-day="Tuesday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="editday-checkbox" name="editdays[]" value="Wednesday"> Wednesday
                                                    </label>
                                                    <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="edit_time_from[Wednesday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="edit_time_to[Wednesday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm editadd-time-slot" data-day="Wednesday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="editday-checkbox" name="editdays[]" value="Thursday"> Thursday
                                                    </label>
                                                    <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="edit_time_from[Thursday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="edit_time_to[Thursday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm editadd-time-slot" data-day="Thursday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="editday-checkbox" name="editdays[]" value="Friday"> Friday
                                                    </label>
                                                    <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="edit_time_from[Friday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="edit_time_to[Friday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm editadd-time-slot" data-day="Friday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="editday-checkbox" name="editdays[]" value="Saturday"> Saturday
                                                    </label>
                                                    <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="edit_time_from[Saturday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="edit_time_to[Saturday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm editadd-time-slot" data-day="Saturday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="editday-checkbox" name="editdays[]" value="Sunday"> Sunday
                                                    </label>
                                                    <div class="edit-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="edit_time_from[Sunday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="edit_time_to[Sunday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm editadd-time-slot" data-day="Sunday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p id="EditSchedCategory"></p>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="Edit_Cancel_Config"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-send"></i> Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Appointment Schedule Config -->
    <div class="modal fade" role="dialog" id="deleteConfigAppointment" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="delete_configAppointment" action="" method="POST">
                        {{ csrf_field() }}
                        <fieldset>
                            <legend><i class="fa fa-calendar-plus-o"></i> Delete Config Appointment
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="edit-config-close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </legend>
                        </fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="label-border">
                                
                                        <label for="appointed_date">Effective Date:</label>   <!-- Config Appointment -->
                                        <input type="date" class="form-control deleteffective_date" name="delete_effective_date" id="deleteffective_date">
                                        <input type="hidden" id="Appointment_schedule_id" name="delete_schedule_id">
                                        <label for="defaultCategory" >Choose default schedule: </label><!-- Config Appointment -->
                                        <select class="form-control select2" id="deletedefaultCateg" name="delete_config_id">  <!-- Config Appointment -->
                                            <option selected value="">Select Default Category</option>
                                            @foreach($configs as $config)
                                                @if($department_id === $config->department_id)
                                                    <option value="{{$config->id}}">{{$config->description}}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                        <label for="department_id">Department Category:</label>
                                        @if($department === 'OPD')
                                            
                                            <input type="text" class="form-control" id="delete_department_id" value="{{ $department }}" readonly>
                                            <input type="hidden" class="form-control" name="delete_department_id" id="department_id" value="5">
                                        @else
                                            <div class="alert-department" data-department="{{ $department }}"></div>
                                        @endif

                                        <label for="facility_id">Facility:</label>
                                        @foreach($facility as $Facility)
                                                <input type="text" class="form-control" id="facility_id" value="{{ $Facility->facility->name }}" readonly>
                                                <input type="hidden" class="form-control" name="delete_facility_id" id="id" value="{{ $Facility->facility->id }}" readonly>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-8">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <strong>Repeat</strong>
                                            </div>
                                            <div class="panel-body">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="deleteday-checkbox" name="editdays[]" value="Monday"> Monday
                                                    </label>
                                                    <div class="delete-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="delete_time_from[Monday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="delete_time_to[Monday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm deleteadd-time-slot" data-day="Monday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="deleteday-checkbox" name="editdays[]" value="Tuesday"> Tuesday
                                                    </label>
                                                    <div class="delete-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="delete_time_from[Tuesday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="delete_time_to[Tuesday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm deleteremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm deleteadd-time-slot" data-day="Tuesday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="editday-checkbox" name="deletedays[]" value="Wednesday"> Wednesday
                                                    </label>
                                                    <div class="delete-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="delete_time_from[Wednesday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="delete_time_to[Wednesday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm deleteadd-time-slot" data-day="Wednesday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="deleteday-checkbox" name="editdays[]" value="Thursday"> Thursday
                                                    </label>
                                                    <div class="delete-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="delete_time_from[Thursday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="delete_time_to[Thursday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm deleteadd-time-slot" data-day="Thursday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="deleteday-checkbox" name="editdays[]" value="Friday"> Friday
                                                    </label>
                                                    <div class="delte-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="delete_time_from[Friday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="delete_time_to[Friday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm deleteremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm deleteadd-time-slot" data-day="Friday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="deleteday-checkbox" name="deletdays[]" value="Saturday"> Saturday
                                                    </label>
                                                    <div class="delete-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="delete_time_from[Saturday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="delete_time_to[Saturday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm deleteadd-time-slot" data-day="Saturday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="deleteday-checkbox" name="deletedays[]" value="Sunday"> Sunday
                                                    </label>
                                                    <div class="delete-time-slots" style="margin-left: 20px; display:none;">
                                                        <div class="row edit_time-slot">
                                                            <div class="col-md-5">
                                                                <label>Time From:</label>
                                                                <input type="time" name="delete_time_from[Sunday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Time To:</label>
                                                                <input type="time" name="delete_time_to[Sunday][]" class="form-control input-sm">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm deleteadd-time-slot" data-day="Sunday">
                                                            <i class="fa fa-plus"></i> Add Time Slot
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p id="EditSchedCategory"></p>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="Edit_Cancel_Config"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-send"></i> Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>

$(document).ready(function () {

    $("#Add_Cancel_appointment").on('click', function () {
        $("#please_select_categ").hide();
    });

    $('.modal').on('hidden.bs.modal', function () {
        $("#please_select_categ").hide(); // Ensure it's hidden when modal closes
    });

    $('#defaultCategorySelect').prop('disabled', true);

    $('#effective_date').on('input change', function () {
        if($(this).val()){
            $('#defaultCategorySelect').prop('disabled', false);
        }else{
            $('#defaultCategorySelect').prop('disabled', true);
        }
    });

    const todayAppointment = new Date().toISOString().split('T')[0];

    document.getElementById("appointment_date").setAttribute('min', todayAppointment);
    document.getElementById("effective_date").setAttribute('min', todayAppointment);

});


const deleteConfigUrl = "{{ url('delete-Config') }}";
const editConfigUrl = "{{ url('get-config-data-sched') }}"
// function DeleteConfig(scheduleId, configId){
//     console.log(`Requesting URL: delete-Config/${scheduleId}/${configId}`);
    $.ajax({
        url:`${deleteConfigUrl}/${scheduleId}`,
        method: 'GET',
        success: function(res) {

            console.log("res Id:", res.schedId, res.configId);
        },
        error: function(error){
            console.error('Error fetching config:', {
                status: error.status,
                statusText: error.statusText,
                response: error.responseText
            });
        }
    })
    
    $('#deleteConfigAppointment').modal('show');
//}

function UpdateConfig(config_appointment_id, config_id) {

    const configData = @json($config_sched_data);
    const appointmentConfig = @json($appointmentSconfig);
    // console.log("appointmentConfig", appointmentConfig);
    let selectedConfig = configData.find(config => config.id === config_id);
    let days = selectedConfig.days.split('|');
    let timeSlots = selectedConfig.time.split('|');
    let category =  selectedConfig.category;

    const $defaulCategory = $("#editdefaultCateg");
    // console.log("Select Config::", selectedConfig);
    $defaulCategory.val(config_id).trigger('change');

    $('#Appointment_schedule_id').val(config_appointment_id);

    const Appointment_Config = appointmentConfig.find(ap => ap.id === config_appointment_id);
    // console.log("data of appointmenr", Appointment_Config);
    $('#opddepartment_config').val(Appointment_Config.opdCategory).trigger('change');
    $defaulCategory.on('change', function () {
        const update_config_id = Number($(this).val());
        selectedConfig = configData.find(config => config.id === update_config_id);
        // console.log("selectedConfig inside handler:", selectedConfig.id);
        $('#Config_schedule_id').val(selectedConfig.id);
        if (selectedConfig) {
            days = selectedConfig.days.split('|');
            timeSlots = selectedConfig.time.split('|');
            category = selectedConfig.category;
            // console.log("Days:", days, "Time Slots:", timeSlots);
            
            // Reset and update UI elements
            $('.edit-time-slots').hide().find(".edit_time-slot").remove();
            updateTimeSlots(days, timeSlots);
        }
    });

    $("#editffective_date").val(Appointment_Config.appointed_date);
    updateScheduleDisplay(Appointment_Config.appointed_date, category);

    // Initial setup of time slots
    $('.edit-time-slots').hide().find(".edit_time-slot").remove();
    updateTimeSlots(days, timeSlots);

    // Helper function to update time slots
    function updateTimeSlots(days, timeSlots) {

        $('.edit-time-slots').hide().find('.edit_time-slot, .time-slot_edit').remove();
        $(`.editday-checkbox`).prop("checked", false);

        const dayTimemap = {};
        let currentDay = null;

        timeSlots.forEach(slot => {
            if(isNaN(slot[0])){
                currentDay = slot;
                dayTimemap[currentDay] = [];
            }else if(currentDay){
                dayTimemap[currentDay].push(slot);
            }
        });
        
        days.forEach(day => {
            $(`.editday-checkbox[value="${day}"]`).prop("checked", true);
            const $timeSlotsDiv = $(`.editday-checkbox[value="${day}"]`).closest('.checkbox').find('.edit-time-slots');
            $timeSlotsDiv.show();

            if(dayTimemap[day]){
                dayTimemap[day].forEach(sched => {
                    const [timeFrom, timeTo] = sched.split('-');
                    // console.log("config time", timeFrom);
                    const timeSlotHtml = `
                        <div class="row edit_time-slot"> 
                            <div class="col-md-5">
                                <label>Time From:</label>
                                <input type="time" name="edit_time_from[${day}][]" class="form-control input-sm" value="${timeFrom}">
                            </div>
                            <div class="col-md-5">
                                <label>Time To:</label>
                                <input type="time" name="edit_time_to[${day}][]" class="form-control input-sm" value="${timeTo}">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm editremove-time-slot" style="margin-top: 32px;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>`;
                    $timeSlotsDiv.append(timeSlotHtml);
                });
            }
        });
    }

   
    // Event handlers
    $(document).on('click', '.editadd-time-slot', function () {
        let day = $(this).data('day');
        let timeSlot = `
        <div class="row time-slot_edit">
            <div class="col-md-5">
                <label>Time From:</label>
                <input type="time" name="edit_time_from[${day}][]" class="form-control input-sm update_time_from">
            </div>
            <div class="col-md-5">
                <label>Time To:</label>
                <input type="time" name="edit_time_to[${day}][]" class="form-control input-sm update_time_to">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm update_remove-time-slot">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>`;
        $(this).before(timeSlot);
    });

    $("#editffective_date").on('change', function() {
        const selectedDate = $(this).val();
        updateScheduleDisplay(selectedDate, category);
    });

    $(document).on('click', '.update_remove-time-slot', function () {
        $(this).closest('.time-slot_edit').remove();
    });

    $(document).on('click', '.editremove-time-slot', function() {
        // Get the current time slot row
        const $timeSlotRow = $(this).closest('.edit_time-slot');

        // Get the timeFrom and timeTo values from the inputs in the row
        const timeFrom = $timeSlotRow.find('input[name^="edit_time_from"]').val();
        const timeTo = $timeSlotRow.find('input[name^="edit_time_to"]').val();

        // Get the day value from the closest checkbox or container
        const $dayCheckbox = $timeSlotRow.closest('.checkbox').find('.editday-checkbox:checked');
        const day = $dayCheckbox.val();

        // Log the values
        // console.log(`Day: ${day}, Time From: ${timeFrom}, Time To: ${timeTo}`);

        // Remove the time slot row
        $timeSlotRow.remove();
    });

    function updateScheduleDisplay(dateValue, WeekToMonth) {
        // console.log("WeekToMonth", WeekToMonth);
        if(dateValue){
            const startDate = new Date(dateValue);
            let endDate;

            if(WeekToMonth === "1 Week"){
                endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 6);
            }else if(WeekToMonth === "1 Month"){
                endDate = new Date(startDate);
                endDate.setMonth(startDate.getMonth() + 1);
                endDate.setDate(endDate.getDate() - 1);
            }

            $("#EditSchedCategory").html(`
                <div class="col-md-12 schedule-output text-center" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; margin-top: 10px;">
                    <span class="schedule-range">
                        <i class="fa fa-calendar"></i> <strong>Start Date:</strong> <span class="schedule-category">${formatedSchedule(startDate)}</span> <strong> - </strong> 
                        <input type='hidden' name="startDate" value="${formatedSchedule(startDate)}">
                        <input type='hidden' name="endDate" value="${formatedSchedule(endDate)}">
                        <i class="fa fa-calendar"></i> <strong>End Date:</strong> <span class="schedule-category">${formatedSchedule(endDate)}</span>
                            &nbsp; <span class="schedule-category" style="font-weight: bold;"> (${WeekToMonth})</span>
                    </span>
                </div>
            `);
        }
    }

    function formatedSchedule(date){
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${month}-${day}-${year}`;
    }

    $(document).on('click', '.editremove-time-slot', function() {
        $(this).closest('.edit-time-slots').remove();
    });

    $(document).on('change', '.editday-checkbox', function () {
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

    $('#UpdateConfigAppointment').modal('show');
}
   

</script>

       