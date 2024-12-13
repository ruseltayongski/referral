   <!-- Add Modal -->
   <div class="modal fade" role="dialog" id="UpdateConfigAppointment" data-backdrop="static" data-keyboard="false" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="update_configAppointment" action="{{ route('create-appointment') }}" method="POST">
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
                                    <div class="appointment-container">
                                        <div class="form-check form-check-inline d-flex">
                                            <input type="checkbox" class="form-check-input custom-checkbox" id="enable_config_appointment" name="enable_appointment" value="config appointment">
                                            <label class="form-check-label custom-label-config" for="enable_appointment">Appointment Config</label>
                                        </div>
                                    </div>
                                    <div class="label-border">
                                
                                        <div style="display: none;" id="side_Config">
                                            <label for="appointed_date" id="effective_label">Effective Date:</label>   <!-- Config Appointment -->
                                            <input type="date" class="form-control Effective_date" name="effective_date" id="effective_date">

                                            <label for="defaultCategory" >Choose default schedule: </label><!-- Config Appointment -->
                                            <select class="form-control select2" id="defaultCategorySelect" name="config_id">  <!-- Config Appointment -->
                                                <option selected value="">Select Default Category</option>
                                                @foreach($configs as $config)
                                                    @if($department_id === $config->department_id)
                                                        <option value="{{$config->id}}">{{$config->description}}</option>
                                                    @endif
                                                @endforeach
                                                
                                            </select>
                                        </div>

                                        <label for="department_id">Department Category:</label>
                                        @if($department === 'OPD')
                                           
                                            <input type="text" class="form-control" id="department_id" value="{{ $department }}" readonly>
                                            <input type="hidden" class="form-control" name="department_id" id="department_id" value="5">
                                        @else
                                            <div class="alert-department" data-department="{{ $department }}"></div>
                                        @endif

                                        <label for="facility_id">Facility:</label>
                                        @foreach($facility as $Facility)
                                                <input type="text" class="form-control" name="facility_id" id="facility_id" value="{{ $Facility->facility->name }}" readonly>
                                                <input type="hidden" class="form-control" name="facility_id" id="id" value="{{ $Facility->facility->id }}" readonly>
                                        @endforeach

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
                                                            <label for="opdCategory">OPD Category:</label>
                                                            <select class="form-control select2 add-appointment-field" id="add_opdCategory_1" name="add_opdCategory1" id="opdCategory">
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
                                                        <select class="form-control select2 available_doctor1 add-appointment-field" id="add_available_doctor_1" name="add_available_doctor1[]" multiple="multiple" data-placeholder="Select Doctor" style="width: 100%;"></select>
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
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="Add_Cancel_appointment"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-send"></i> Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
