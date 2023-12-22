<form id="updateAppointmentForm" method="post" action="{{ route('update-appointment') }}">
                        {{ csrf_field() }}
                        <legend><i class="fa fa-edit"></i> Edit Appointment
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </legend>        
                     
                        <div class="form-group">
                            <input type="hidden" name="update_appointment_id" id="updateAppointmentId" value="" class="form-control">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="label-border">
                                        <label for="update_appointed_date">Appointed Date:</label>
                                        <input type="date" class="form-control" name="update_appointed_date" id="update_appointed_date">

                                        <label for="update_facility_id">Facility:</label>
                                        <select class="form-control" name="update_facility_id" id="update_facility_id" onchange="onchangeDepartment($(this))" required>
                                            <option selected>Select Facility</option>
                                            @foreach($facility as $Facility)
                                                <option value="{{ $Facility->facility->id }}" @if($Facility->facility->id == $appointment_schedule->facility_id) selected @endif>{{ $Facility->facility->name }}</option>
                                            @endforeach
                                        </select>

                                        <label for="update_department_id">Department:</label>
                                        <!-- <input type="text" class="form-control" name="update_department_id" id="update_department_id" value="OPD" readonly> -->
                                        <input type="text" class="form-control" name="update_department_id" id="update_department_id"   readonly>
                                    </div>
                                </div>
                              
                                
                                <div class="col-md-8">
                                    <div class="label-border">
                                        <div id="opdCategoryContainer">
                                            <div class="label-border">
                                                <div class="row">
                                                    <div class="col-md-12">
                                          
                                                        <label for="update_appointed_time">Appointed Time:</label><br>
                                                        <div class="col-md-6">
                                                            <span>From: </span>
                                                            <input type="time" class="form-control" name="update_appointed_time" id="update_appointed_time">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span> To: </span>
                                                            <input type="time" class="form-control" name="update_appointedTime_to" id="update_appointedTime_to">
                                                        </div>
                                                        <label for="update_opdCategory">OPD Category:</label>
                                                        <select class="form-control select2" name="update_opdCategory1" id="update_opdCategory" required>
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
                                                    <div class="col-md-12">
                                                        <label for="update_slot">Slot:</label>
                                                        <input type="number" class="form-control" name="update_slot1" id="update_slot" required>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label>Available Doctor</label>
                                                    <select class="form-control select2 available_doctor1" name="update_available_doctor[]" multiple="multiple" data-placeholder="Select Doctor" style="width: 100%;" required>
                                                    <option value=""></option>
                                                   </select>
                                                </div>
                                                <div style="margin-top: 15px;">
                                                    <button type="button" class="btn btn-info btn-sm" id="update_add_slots" onclick="addTimeInput()">Add More Category and Slot</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="update_additionalTimeContainer" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
     

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm" onclick="updateAppointment()"><i class="fa fa-check"></i> Update</button>
                        </div>
                    </form>