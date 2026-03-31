<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="scheduleModalLabel">Follow Up Schedule</h4>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <input type="hidden" id="schedule_code" name="code">
                    <input type="hidden" id="schedule_appointment_id" name="Appointment_id">
                    <input type="hidden" id="schedule_followup_facility_telemed" name="followup_facility_telemed">
                    <input type="hidden" id="schedule_config_id" name="configId">
                    <input type="hidden" id="schedule_telemedicine" name="telemedicine" value="1">
                    <input type="hidden" id="schedule_doctor_id" name="doctor_id" value="{{ auth()->id() }}">
                    <input type="hidden" id="schedule_username" name="username" value="{{ auth()->user()->username ?? '' }}">
                    <!-- <div class="form-group">
                        <label for="schedule_name">Schedule</label>
                        <input type="text" class="form-control" id="schedule_name" name="schedule" placeholder="Enter schedule" required>
                    </div> -->
                    <div class="form-group">
                        <label for="schedule_date">Date</label>
                        <input type="date" class="form-control" id="schedule_date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="schedule_time_from">Time From</label>
                        <input type="time" class="form-control" id="schedule_time_from" name="time_from" required>
                    </div>
                    <div class="form-group">
                        <label for="schedule_time_to">Time To</label>
                        <input type="time" class="form-control" id="schedule_time_to" name="time_to" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="saveScheduleBtn">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-followup', function() {
            var code = $(this).data('code') || '';
            var facility = $(this).data('followup-facility') || '';
            var appointmentId = $(this).data('appointment-id') || '';
            var configId = $(this).data('config-id') || '';
            var telemedicine = $(this).data('telemedicine') || 0;

            $('#schedule_code').val(code);
            $('#schedule_followup_facility_telemed').val(facility);
            $('#schedule_appointment_id').val(appointmentId);
            $('#schedule_config_id').val(configId);
            $('#schedule_telemedicine').val(telemedicine);
            $('#schedule_date').val('');
            $('#schedule_time_from').val('');
            $('#schedule_time_to').val('');
        });

        $('#saveScheduleBtn').on('click', function() {
            var payload = {
                doctor_id: $('#schedule_doctor_id').val(),
                username: $('#schedule_username').val(),
                telemedicine: $('#schedule_telemedicine').val(),
                code: $('#schedule_code').val(),
                date: $('#schedule_date').val(),
                timeFrom: $('#schedule_time_from').val(),
                timeTo: $('#schedule_time_to').val(),
                followup_facility_telemed: $('#schedule_followup_facility_telemed').val(),
                Appointment_id: $('#schedule_appointment_id').val() || null,
                configId: $('#schedule_config_id').val() || null,
                use_existing_schedule: false
            };

            if (!payload.code || !payload.date || !payload.timeFrom || !payload.timeTo) {
                Lobibox.alert('warning', {
                    msg: 'Please fill in all schedule fields before saving.'
                });
                return;
            }

            function submitFollowUp(useExisting) {
                payload.use_existing_schedule = useExisting;
                axios.post('{{ asset('api/patient/followup') }}', payload)
                    .then(function(response) {
                        Lobibox.alert('success', {
                            msg: response.data.message || 'Follow-up scheduled successfully.'
                        });
                        $('#scheduleModal').modal('hide');
                    })
                    .catch(function(error) {
                        if (error.response && error.response.status === 409) {
                            var existing = error.response.data.existing_schedule || null;
                            var msg = error.response.data.message || 'Schedule conflict detected. Use existing schedule?';
                            Lobibox.confirm({
                                msg: msg,
                                callback: function($this, type) {
                                    if (type === 'yes') {
                                        submitFollowUp(true);
                                    }
                                }
                            });
                        } else {
                            var message = 'Unable to save follow-up schedule.';
                            if (error.response && error.response.data && error.response.data.message) {
                                message = error.response.data.message;
                            }
                            Lobibox.alert('error', {
                                msg: message
                            });
                        }
                    });
            }

            submitFollowUp(false);
        });
    });
</script>
        </div>
    </div>
</div>
