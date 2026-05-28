<template>
  <teleport to="body">
    <transition name="modal-fade">
      <div v-if="visible" class="modal-overlay" @click.self="close">
        <div class="modal-content-custom">

          <!-- Modal Loader Overlay -->
          <div v-if="isSubmitting" class="modal-loader">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
          </div>

          <!-- Header -->
          <div class="modal-header-custom">
            <h4 class="modal-title">Follow Up Schedule</h4>
            <button type="button" class="close-btn" @click="close">&times;</button>
          </div>

          <!-- Body -->
          <div class="modal-body-custom">
            <form @submit.prevent>

              <!-- Date Field -->
              <div class="form-group">
                <label>Date</label>
                <div class="date-row">
                  <input
                    type="date"
                    class="form-control"
                    v-model="form.date"
                    :min="todayISO"
                    required
                    @change="onDateChange"
                  />
                </div>
                <div v-if="dateLoading" class="loading-indicator">
                  <small><i class="fa fa-spinner fa-spin"></i> Checking available slots...</small>
                </div>
              </div>

              <!-- Existing Slots -->
              <transition name="fade">
                <div v-if="slotsVisible" class="slots-container">
                  <div class="slots-header">
                    <label>
                      <i class="fa fa-calendar-check-o"></i>
                      Available Slots
                    </label>
                    <span class="badge-info">{{ filteredSlots.length }} slot{{ filteredSlots.length !== 1 ? 's' : '' }}</span>
                  </div>

                  <div v-if="filteredSlots.length > 0" class="existing-slots">
                    <div
                      v-for="slot in filteredSlots"
                      :key="slot.id"
                      class="slot-item"
                      :class="[slotAvailabilityClass(slot), { 'slot-selected': selectedSlotId === slot.id }]"
                      @click="selectSlot(slot)"
                    >
                      <div class="slot-time">
                        <i class="fa fa-clock-o"></i>
                        {{ slot.time_from }} - {{ slot.time_to }}
                      </div>
                      <div class="slot-dept">
                        <strong>{{ slot.department || 'General' }}</strong>
                      </div>
                      <div class="slot-avail">
                        <i :class="slotIcon(slot)"></i>
                        <span>{{ slot.availability || 'Available' }}</span>
                      </div>
                    </div>
                  </div>

                  <div v-else class="no-slots-message">
                    <i class="fa fa-info-circle fa-2x"></i>
                    <strong>No existing slots for this date.</strong><br />
                    <small>You can create a new schedule below.</small>
                  </div>
                </div>
              </transition>

              <!-- Manual Time Entry -->
              <div class="manual-entry">
                <div v-if="slotsVisible" class="manual-entry-label">Or Create New Schedule:</div>

                <div class="form-group">
                  <label>Time From</label>
                  <input type="time" class="form-control" v-model="form.timeFrom" required />
                </div>

                <div class="form-group">
                  <label>Time To</label>
                  <input type="time" class="form-control" v-model="form.timeTo" required />
                </div>
              </div>

            </form>
          </div>

          <!-- Footer -->
          <div class="modal-footer-custom">
            <button type="button" class="btn btn-default" @click="close">Cancel</button>
            <button
              type="button"
              class="btn btn-primary"
              :disabled="isSubmitting"
              @click="save"
            >
              <span v-if="!isSubmitting">Save</span>
              <span v-else><i class="fa fa-spinner fa-spin"></i> Saving...</span>
            </button>
          </div>

        </div>
      </div>
    </transition>
  </teleport>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ScheduleModal',

  props: {
    // Pass the authenticated doctor info as props
    doctorId: {
      type: [Number, String],
      default: null,
    },
    username: {
      type: String,
      default: '',
    },
    // Base URLs (can also be injected via provide/inject or env vars)
    checkSlotsUrl: {
      type: String,
      default: '/api/schedule/check-slots',
    },
    followupUrl: {
      type: String,
      default: '/api/patient/followup',
    },
  },

  emits: ['saved', 'closed'],

  data() {
    return {
      visible: false,
      isSubmitting: false,
      dateLoading: false,
      slotsVisible: false,

      // Slot data
      existingSlots: [],
      selectedSlotId: null,
      useExistingSlot: false,

      // Form fields
      form: {
        code: '',
        appointmentId: null,
        followupFacilityTelemed: '',
        configId: null,
        telemedicine: 1,
        date: '',
        timeFrom: '',
        timeTo: '',
      },
    };
  },

  computed: {
    todayISO() {
      const today = new Date();
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const dd = String(today.getDate()).padStart(2, '0');
      return `${today.getFullYear()}-${mm}-${dd}`;
    },

    filteredSlots() {
      const facility = this.form.followupFacilityTelemed;
      if (facility && facility !== '' && facility !== 'null' && facility !== 'undefined') {
        return this.existingSlots.filter(slot => String(slot.facility_id) === String(facility));
      }
      return this.existingSlots;
    },
  },

  mounted() {
    // Listen for postMessage-based open events (iframe / cross-component communication)
    window.addEventListener('message', this.handlePostMessage);
  },

  beforeUnmount() {
    window.removeEventListener('message', this.handlePostMessage);
  },

  methods: {
    // ─── Open / Close ────────────────────────────────────────────────────────

    /**
     * Open the modal programmatically.
     * @param {Object} data - { code, appointmentId, followupFacility, configId, telemedicine }
     */
    open(data = {}) {
      this.resetForm();
      this.form.code = data.code || '';
      // Use referredTo if available (fallback to followupFacility for backward compatibility)
      this.form.followupFacilityTelemed = data.referredTo || data.followupFacility || '';
      this.form.appointmentId = data.appointmentId || null;
      this.form.configId = data.configId || null;
      this.form.telemedicine = data.telemedicine ?? 1;
      this.visible = true;
    },

    close() {
      this.visible = false;
      this.resetForm();
      this.$emit('closed');
    },

    handlePostMessage(event) {
      if (!event.data || event.data.type !== 'openFollowUp') return;
      // Ensure referredTo is passed or fallback to followupFacility
      const data = event.data;
      if (!data.referredTo && data.followupFacility) {
        data.referredTo = data.followupFacility;
      }
      this.open(data);
    },

    // ─── Form Reset ──────────────────────────────────────────────────────────

    resetForm() {
      this.form = {
        code: '',
        appointmentId: null,
        followupFacilityTelemed: '',
        configId: null,
        telemedicine: 1,
        date: '',
        timeFrom: '',
        timeTo: '',
      };
      this.existingSlots = [];
      this.selectedSlotId = null;
      this.useExistingSlot = false;
      this.slotsVisible = false;
      this.dateLoading = false;
    },

    // ─── Date Change ─────────────────────────────────────────────────────────

    async onDateChange() {
      const selected = this.form.date;

      if (!selected) {
        this.slotsVisible = false;
        return;
      }

      if (selected < this.todayISO) {
        Lobibox.alert({
          msg: 'Please select today or a future date.',
          title: 'Invalid Date',
        });
        this.form.date = '';
        this.slotsVisible = false;
        return;
      }

      this.dateLoading = true;
      this.slotsVisible = false;
      this.selectedSlotId = null;
      this.useExistingSlot = false;
      this.form.timeFrom = '';
      this.form.timeTo = '';

      try {
        const response = await axios.get(this.checkSlotsUrl, {
          params: { date: selected },
        });
        this.existingSlots = response.data.slots || [];
      } catch {
        this.existingSlots = [];
        Lobibox.alert({
          msg: 'Error fetching available slots. Please try again.',
          title: 'API Error',
        });
      } finally {
        this.dateLoading = false;
        this.slotsVisible = true;
      }
    },

    // ─── Slot Helpers ─────────────────────────────────────────────────────────

    slotAvailabilityClass(slot) {
      const avail = slot.availability || 'Available';
      if (avail === 'Full') return 'slot-full';
      if (avail.includes('slots left')) return 'slot-partial';
      return 'slot-available';
    },

    slotIcon(slot) {
      const avail = slot.availability || 'Available';
      if (avail === 'Full') return 'fa fa-times-circle text-danger';
      if (avail.includes('slots left')) return 'fa fa-exclamation-triangle text-warning';
      return 'fa fa-check-circle text-success';
    },

    selectSlot(slot) {
      if (slot.availability === 'Full') {
        Lobibox.alert({
          msg: 'This slot is fully booked. Please select another slot or create a new schedule.',
          title: 'Slot Unavailable',
        });
        return;
      }

      this.selectedSlotId = slot.id;
      this.form.timeFrom = slot.time_from;
      this.form.timeTo = slot.time_to;
      this.useExistingSlot = true;
    },

    // ─── Save ─────────────────────────────────────────────────────────────────

    async save() {
      if (this.isSubmitting) return;

      const { code, date, timeFrom, timeTo } = this.form;

      if (!code || !date || !timeFrom || !timeTo) {
        Lobibox.alert({
          msg: 'Please fill in all schedule fields before saving.',
          title: 'Incomplete Form',
        });
        return;
      }

      this.isSubmitting = true;
      await this.submitFollowUp(this.useExistingSlot);
    },

    async submitFollowUp(useExisting) {
      const payload = {
        doctor_id: this.doctorId,
        username: this.username,
        telemedicine: this.form.telemedicine,
        code: this.form.code,
        date: this.form.date,
        timeFrom: this.form.timeFrom,
        timeTo: this.form.timeTo,
        followup_facility_telemed: this.form.followupFacilityTelemed,
        Appointment_id: this.form.appointmentId || null,
        configId: this.form.configId || null,
        use_existing_schedule: useExisting,
        schedule_id: this.selectedSlotId || null,
      };

      try {
        const response = await axios.post(this.followupUrl, payload);
        Lobibox.notify('success', {
          msg: response.data.message || 'Follow-up scheduled successfully.',
          title: 'Success',
        });
        this.$emit('saved', response.data);
        this.close();
      } catch (error) {
        if (error.response && error.response.status === 409) {
          // Conflict — ask user to confirm using existing schedule
          Lobibox.confirm({
            msg: error.response.data.message || 'Schedule conflict detected. Use existing schedule?',
            title: 'Conflict Resolution',
            callback: async (instance, type) => {
              if (type === 'yes') {
                await this.submitFollowUp(true);
              } else {
                this.isSubmitting = false;
              }
            },
          });
        } else {
          const message = error.response?.data?.message || 'Unable to save follow-up schedule.';
          Lobibox.alert({
            msg: message,
            title: 'Error',
          });
          this.isSubmitting = false;
        }
      } finally {
        if (error && error.response?.status !== 409) {
          // Already handled above
        }
      }
    },
  },
};
</script>

<style scoped>
/* ── Overlay & Modal Shell ───────────────────────────────────── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
}

.modal-content-custom {
  position: relative;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  width: 90%;
  max-width: 460px;
  overflow: hidden;
}

/* ── Loader ─────────────────────────────────────────────────── */
.modal-loader {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.7);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ── Header ─────────────────────────────────────────────────── */
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

.close-btn {
  background: none;
  border: none;
  font-size: 22px;
  line-height: 1;
  cursor: pointer;
  color: #666;
  padding: 0;
}

.close-btn:hover {
  color: #333;
}

/* ── Body ───────────────────────────────────────────────────── */
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
  box-sizing: border-box;
}

.form-control:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.date-row {
  display: flex;
  gap: 10px;
}

.loading-indicator {
  margin-top: 5px;
  color: #6c757d;
  font-size: 13px;
}

/* ── Slots Container ────────────────────────────────────────── */
.slots-container {
  margin: 15px 0;
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 4px;
  border: 1px solid #e9ecef;
}

.slots-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 15px;
}

.slots-header label {
  font-weight: 600;
  margin: 0;
  color: #2c3e50;
  font-size: 14px;
}

.slots-header label .fa {
  margin-right: 8px;
}

.badge-info {
  background-color: #17a2b8;
  color: #fff;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 12px;
}

.existing-slots {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 10px;
}

/* ── Slot Item ──────────────────────────────────────────────── */
.slot-item {
  padding: 12px;
  border: 2px solid #dee2e6;
  border-radius: 4px;
  background-color: #fff;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.slot-item:hover:not(.slot-full) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.slot-item.slot-selected {
  border-color: #28a745 !important;
  background-color: #d4edda !important;
  transform: scale(1.02);
  box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
  font-weight: 600;
}

.slot-available { border-left: 4px solid #28a745; }
.slot-partial   { border-left: 4px solid #ffc107; }
.slot-full      {
  border-left: 4px solid #dc3545;
  cursor: not-allowed;
  opacity: 0.6;
}

.slot-time {
  font-weight: 600;
  color: #333;
  font-size: 14px;
  margin-bottom: 5px;
}

.slot-time .fa { margin-right: 5px; }

.slot-dept {
  font-size: 12px;
  color: #555;
  margin-bottom: 8px;
}

.slot-avail {
  font-size: 12px;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}

/* ── No Slots ───────────────────────────────────────────────── */
.no-slots-message {
  text-align: center;
  padding: 20px;
  color: #6c757d;
}

.no-slots-message .fa {
  display: block;
  margin-bottom: 10px;
}

/* ── Manual Entry ───────────────────────────────────────────── */
.manual-entry {
  margin-top: 10px;
  padding-top: 15px;
  border-top: 1px solid #e0e0e0;
}

.manual-entry-label {
  font-weight: 600;
  margin-bottom: 10px;
  color: #333;
  font-size: 14px;
}

/* ── Footer ─────────────────────────────────────────────────── */
.modal-footer-custom {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 15px 20px;
  border-top: 1px solid #e0e0e0;
  background-color: #f5f5f5;
}

/* ── Buttons ────────────────────────────────────────────────── */
.btn {
  padding: 6px 14px;
  font-size: 13px;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  font-family: inherit;
}

.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.btn-default {
  background-color: #e0e0e0;
  color: #333;
}

.btn-default:hover:not(:disabled) {
  background-color: #ccc;
}

.btn-primary {
  background-color: #007bff;
  color: #fff;
}

.btn-primary:hover:not(:disabled) {
  background-color: #0069d9;
}

/* ── Transitions ─────────────────────────────────────────────── */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 480px) {
  .existing-slots {
    grid-template-columns: 1fr;
  }
}

/* ── Font Awesome color helpers ─────────────────────────────── */
.text-danger  { color: #dc3545; }
.text-warning { color: #ffc107; }
.text-success { color: #28a745; }
</style>