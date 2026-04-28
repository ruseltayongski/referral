<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" style="position: relative;">

            <!-- 🔹 OPTIONAL MODAL OVERLAY LOADER -->
            <div id="modalLoader" style="
                display:none;
                position:absolute;
                top:0; left:0;
                width:100%; height:100%;
                background:rgba(255,255,255,0.7);
                z-index:9999;
                text-align:center;
                padding-top:50%;
            ">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
            </div>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title">Follow Up Schedule</h4>
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

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" id="schedule_date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label>Time From</label>
                        <input type="time" class="form-control" id="schedule_time_from" name="time_from" required>
                    </div>

                    <div class="form-group">
                        <label>Time To</label>
                        <input type="time" class="form-control" id="schedule_time_to" name="time_to" required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>

                <!-- 🔹 BUTTON WITH LOADER -->
                <button type="button" class="btn btn-primary btn-sm" id="saveScheduleBtn">
                    <span id="saveBtnText">Save</span>
                    <span id="saveBtnLoader" style="display:none;">
                        <i class="fa fa-spinner fa-spin"></i> Saving...
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    let isSubmitting = false; // 🔹 GLOBAL LOCK

    $(document).on('click', '.btn-followup', function() {
        $('#schedule_code').val($(this).data('code') || '');
        $('#schedule_followup_facility_telemed').val($(this).data('followup-facility') || '');
        $('#schedule_appointment_id').val($(this).data('appointment-id') || '');
        $('#schedule_config_id').val($(this).data('config-id') || '');
        $('#schedule_telemedicine').val($(this).data('telemedicine') || 0);

        $('#schedule_date').val('');
        $('#schedule_time_from').val('');
        $('#schedule_time_to').val('');
    });

    $('#saveScheduleBtn').on('click', function() {

        if (isSubmitting) return; // 🚫 BLOCK SPAM CLICK

        var btn = $(this);

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

        function showLoader() {
            isSubmitting = true; // 🔒 LOCK
            btn.prop('disabled', true);
            $('#saveBtnText').hide();
            $('#saveBtnLoader').show();
            $('#modalLoader').show();
        }

        function resetLoader() {
            isSubmitting = false; // 🔓 UNLOCK
            btn.prop('disabled', false);
            $('#saveBtnText').show();
            $('#saveBtnLoader').hide();
            $('#modalLoader').hide();
        }

        function submitFollowUp(useExisting) {
            payload.use_existing_schedule = useExisting;

            axios.post('{{ asset('api/patient/followup') }}', payload)
                .then(function(response) {
                    Lobibox.alert('success', {
                        msg: response.data.message || 'Follow-up scheduled successfully.'
                    });
                    $('#scheduleModal').modal('hide');
                    resetLoader(); // ✅ reset on success
                })
                .catch(function(error) {

                    if (error.response && error.response.status === 409) {

                        // ❗ DO NOT RESET LOADER HERE
                        Lobibox.confirm({
                            msg: error.response.data.message || 'Schedule conflict detected. Use existing schedule?',
                            callback: function($this, type) {

                                if (type === 'yes') {
                                    submitFollowUp(true); // 🔁 retry (still locked)
                                } else {
                                    resetLoader(); // 🔓 unlock ONLY if user cancels
                                }

                            }
                        });

                    } else {
                        var message = 'Unable to save follow-up schedule.';
                        if (error.response?.data?.message) {
                            message = error.response.data.message;
                        }

                        Lobibox.alert('error', { msg: message });
                        resetLoader(); // ✅ reset on real error
                    }
                });
        }

        showLoader();
        submitFollowUp(false);
    });

});
</script>