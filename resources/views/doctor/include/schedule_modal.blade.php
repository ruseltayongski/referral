<style>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
}

.modal-content-custom {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  width: 90%;
  max-width: 460px;
  overflow: hidden;
}

.modal-header-custom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e0e0e0;
  background-color: #f5f5f5;
}

.modal-header-custom h4 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.modal-body-custom {
  padding: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #333;
  font-size: 14px;
}

.form-control {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  font-family: inherit;
}

.form-control:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.modal-footer-custom {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 15px 20px;
  border-top: 1px solid #e0e0e0;
  background-color: #f5f5f5;
}

.slots-container {
  margin: 15px 0;
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 4px;
  border: 1px solid #e9ecef;
}

.existing-slots {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 10px;
}

.slot-item {
  padding: 12px;
  border: 2px solid #dee2e6;
  border-radius: 4px;
  background-color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.slot-item:hover {
  border-color: #007bff;
  background-color: #e7f3ff;
}

.slot-item.slot-selected {
  border-color: #28a745;
  background-color: #d4edda;
  font-weight: 600;
}

.slot-available {
  border-left: 4px solid #28a745;
}

.slot-partial {
  border-left: 4px solid #ffc107;
}

.slot-full {
  border-left: 4px solid #dc3545;
  cursor: not-allowed;
  opacity: 0.6;
}

.slot-full:hover {
  border-color: #dc3545;
  background-color: #f8d7da;
}

.slot-time {
  font-weight: 600;
  color: #333;
  font-size: 14px;
  margin-bottom: 5px;
}

.slot-info {
  font-size: 12px;
  color: #666;
}

.manual-entry {
  margin-top: 10px;
  padding-top: 15px;
  border-top: 1px solid #e0e0e0;
}

@media (max-width: 768px) {
  .existing-slots {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content-custom" style="position: relative;">

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

            <div class="modal-header-custom">
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title">Follow Up Schedule</h4>
            </div>

            <div class="modal-body-custom">
                <form id="scheduleForm">
                    <input type="hidden" id="schedule_code" name="code">
                    <input type="hidden" id="schedule_appointment_id" name="Appointment_id">
                    <input type="hidden" id="schedule_followup_facility_telemed" name="followup_facility_telemed">
                    <input type="hidden" id="schedule_config_id" name="configId">
                    <input type="hidden" id="schedule_telemedicine" name="telemedicine" value="1">
                    <input type="hidden" id="schedule_doctor_id" name="doctor_id" value="{{ auth()->id() }}">
                    <input type="hidden" id="schedule_username" name="username" value="{{ auth()->user()->username ?? '' }}">
                    <input type="hidden" id="selected_slot_id" name="schedule_id">
                    <input type="hidden" id="use_existing_slot" name="use_existing_schedule" value="0">

                    <div class="form-group">
                        <label>Date</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="date" class="form-control" id="schedule_date" name="date" required style="flex: 1;" min="{{ date('Y-m-d') }}">
                            <button type="button" class="btn btn-info btn-sm" id="checkSlotsBtn" style="white-space: nowrap; display: none;">
                                <i class="fa fa-search"></i> Check Slots
                            </button>
                        </div>
                        <div id="dateLoadingIndicator" style="display: none; margin-top: 5px;">
                            <small class="text-muted">
                                <i class="fa fa-spinner fa-spin"></i> Checking available slots...
                            </small>
                        </div>
                    </div>

                    <!-- Existing Time Slots Display -->
                    <div id="existingSlotsContainer" class="slots-container" style="display: none;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                            <label style="font-weight: 600; margin: 0; color: #2c3e50;">
                                <i class="fa fa-calendar-check-o" style="margin-right: 8px;"></i>
                                Available Slots
                            </label>
                            <span id="slotsCount" class="badge badge-info" style="font-size: 12px;"></span>
                        </div>
                        <div id="existingSlotsList" class="existing-slots"></div>
                        <div id="noSlotsMessage" style="display: none; text-align: center; padding: 20px; color: #6c757d;">
                            <i class="fa fa-info-circle fa-2x" style="margin-bottom: 10px; display: block;"></i>
                            <strong>No existing slots for this date.</strong><br>
                            <small>You can create a new schedule below.</small>
                        </div>
                    </div>

                    <!-- Manual Time Entry Section -->
                    <div id="manualEntrySection" class="manual-entry">
                        <div id="manualEntryLabel" style="font-weight: 600; display: none; margin: 15px 0 10px 0;">Or Create New Schedule:</div>
                        
                        <div class="form-group">
                            <label>Time From</label>
                            <input type="time" class="form-control" id="schedule_time_from" name="time_from" required>
                        </div>

                        <div class="form-group">
                            <label>Time To</label>
                            <input type="time" class="form-control" id="schedule_time_to" name="time_to" required>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer-custom">
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
    let existingSlots = []; // Store fetched slots

    window.addEventListener('message', function(event) {
        if (!event.data || event.data.type !== 'openFollowUp') return;

        var d = event.data;

        $('#schedule_code').val(d.code || '');
        $('#schedule_followup_facility_telemed').val(d.followupFacility || '');
        $('#schedule_appointment_id').val(d.appointmentId || '');
        $('#schedule_config_id').val(d.configId || '');
        $('#schedule_telemedicine').val(d.telemedicine || 1);

        // Reset fields
        $('#schedule_date').val('');
        $('#schedule_time_from').val('');
        $('#schedule_time_to').val('');
        $('#selected_slot_id').val('');
        $('#use_existing_slot').val('0');
        existingSlots = [];
        resetSlotsDisplay();

        // Open the modal
        $('#scheduleModal').modal('show');
    });

    function getTodayISO() {
        const today = new Date();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        return `${today.getFullYear()}-${mm}-${dd}`;
    }

    function setMinDate() {
        const minDate = getTodayISO();
        $('#schedule_date').attr('min', minDate);
    }

    setMinDate();

    $(document).on('click', '.btn-followup', function() {
        var followupFacility = $(this).data('followup-facility');

        $('#schedule_code').val($(this).data('code') || '');
        $('#schedule_followup_facility_telemed').val(followupFacility || '');
        $('#schedule_appointment_id').val($(this).data('appointment-id') || '');
        $('#schedule_config_id').val($(this).data('config-id') || '');
        $('#schedule_telemedicine').val($(this).data('telemedicine') || 0);

        setMinDate();
        
        $('#schedule_date').val('');
        $('#schedule_time_from').val('');
        $('#schedule_time_to').val('');
        $('#selected_slot_id').val('');
        $('#use_existing_slot').val('0');
        existingSlots = [];
        
        resetSlotsDisplay();
    });

    // Auto-check slots when date is selected
    $('#schedule_date').on('change', function() {
        var selectedDate = $(this).val();
        var minDate = getTodayISO();

        if (!selectedDate) {
            resetSlotsDisplay();
            return;
        }

        if (selectedDate < minDate) {
            Lobibox.alert('warning', {
                msg: 'Please select today or a future date.'
            });
            $(this).val('');
            resetSlotsDisplay();
            return;
        }

        // Show loading indicator
        $('#dateLoadingIndicator').show();
        $('#existingSlotsContainer').hide();

        var params = {
            date: selectedDate
        };

        $.ajax({
            url: '{{ url('api/schedule/check-slots') }}',
            method: 'GET',
            data: params,
            success: function(response) {
                let slots = response.slots || [];
                let followupFacility = $('#schedule_followup_facility_telemed').val();
                if (followupFacility && followupFacility !== '' && followupFacility !== 'null' && followupFacility !== 'undefined') {
                    slots = slots.filter(slot => slot.facility_id == followupFacility);
                }
                existingSlots = slots;
                displaySlots(existingSlots);
                $('#dateLoadingIndicator').hide();
            },
            error: function(error) {
                $('#dateLoadingIndicator').hide();
                resetSlotsDisplay();
                Lobibox.alert('error', {
                    msg: 'Error fetching available slots. Please try again.'
                });
            }
        });
    });

    // Manual check slots button (kept for fallback)
    $('#checkSlotsBtn').on('click', function() {
        $('#schedule_date').trigger('change');
    });

    function displaySlots(slots) {
        if (slots.length === 0) {
            $('#existingSlotsContainer').show();
            $('#existingSlotsList').hide();
            $('#noSlotsMessage').show();
            $('#slotsCount').text('0 slots');
            $('#manualEntryLabel').show();
            return;
        }

        $('#existingSlotsContainer').show();
        $('#existingSlotsList').show();
        $('#noSlotsMessage').hide();
        $('#slotsCount').text(slots.length + ' slot' + (slots.length !== 1 ? 's' : ''));
        $('#manualEntryLabel').show();
        
        var slotsHtml = '';
        slots.forEach(function(slot) {
            var availabilityClass = '';
            var availabilityIcon = '';
            var availabilityText = slot.availability || 'Available';
            var isAvailable = slot.is_available !== false;

            if (availabilityText === 'Full') {
                availabilityClass = 'slot-full';
                availabilityIcon = '<i class="fa fa-times-circle text-danger"></i>';
            } else if (availabilityText.includes('slots left')) {
                availabilityClass = 'slot-partial';
                availabilityIcon = '<i class="fa fa-exclamation-triangle text-warning"></i>';
            } else {
                availabilityClass = 'slot-available';
                availabilityIcon = '<i class="fa fa-check-circle text-success"></i>';
            }

            slotsHtml += `
                <div class="slot-item ${availabilityClass}" data-slot-id="${slot.id}" data-time-from="${slot.time_from}" data-time-to="${slot.time_to}">
                    <div class="slot-time">
                        <i class="fa fa-clock-o" style="margin-right: 5px;"></i>
                        ${slot.time_from} - ${slot.time_to}
                    </div>
                    <div class="slot-info" style="margin-bottom: 8px;">
                        <strong>${slot.department || 'General'}</strong>
                    </div>
                    <div class="slot-info" style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                        ${availabilityIcon}
                        <span>${availabilityText}</span>
                    </div>
                    ${!isAvailable ? '<div style="position: absolute; top: 5px; right: 5px; background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px;"><i class="fa fa-lock"></i></div>' : ''}
                </div>
            `;
        });

        $('#existingSlotsList').html(slotsHtml);

        // Add click handlers for slot items
        $('.slot-item').off('click').on('click', function() {
            var $this = $(this);
            var isAvailable = !$this.hasClass('slot-full');

            if (!isAvailable) {
                Lobibox.alert('warning', {
                    msg: 'This slot is fully booked. Please select another slot or create a new schedule.'
                });
                return;
            }

            $('.slot-item').removeClass('slot-selected').css({
                'border-color': '#dee2e6',
                'background-color': 'white',
                'transform': 'scale(1)',
                'box-shadow': '0 2px 4px rgba(0,0,0,0.1)'
            });

            $this.addClass('slot-selected').css({
                'border-color': '#28a745',
                'background-color': '#d4edda',
                'transform': 'scale(1.02)',
                'box-shadow': '0 4px 8px rgba(40, 167, 69, 0.3)'
            });

            var slotId = $this.data('slot-id');
            var timeFrom = $this.data('time-from');
            var timeTo = $this.data('time-to');

            $('#selected_slot_id').val(slotId);
            $('#schedule_time_from').val(timeFrom);
            $('#schedule_time_to').val(timeTo);
            $('#use_existing_slot').val('1');
        });

        // Add hover effects
        $('.slot-item').hover(
            function() {
                if ($(this).hasClass('slot-full')) return;
                $(this).css({
                    'transform': 'translateY(-2px)',
                    'box-shadow': '0 4px 12px rgba(0,0,0,0.15)'
                });
            },
            function() {
                if ($(this).hasClass('slot-selected')) {
                    $(this).css({
                        'transform': 'scale(1.02)',
                        'box-shadow': '0 4px 8px rgba(40, 167, 69, 0.3)'
                    });
                } else {
                    $(this).css({
                        'transform': 'scale(1)',
                        'box-shadow': '0 2px 4px rgba(0,0,0,0.1)'
                    });
                }
            }
        );
    }

    function resetSlotsDisplay() {
        $('#existingSlotsContainer').hide();
        $('#existingSlotsList').html('').hide();
        $('#noSlotsMessage').hide();
        $('#slotsCount').text('');
        $('#manualEntryLabel').hide();
        $('#selected_slot_id').val('');
        $('#schedule_time_from').val('');
        $('#schedule_time_to').val('');
        $('#use_existing_slot').val('0');
        $('#dateLoadingIndicator').hide();
    }

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
            use_existing_schedule: $('#use_existing_slot').val() === '1',
            schedule_id: $('#selected_slot_id').val() || null
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
                    resetSlotsDisplay();
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