<script>
import { appointmentScheduleDate, appointmentScheduleHours, appointmentConfigHours, appointmentConfigData } from "./api/index";
export default {
  name: "AppointmentTime",
  props: {
    appointedTimes: {
      type: Object,
    },
    appointmentclickDate: {
      type: Object,
      default: null,
    },
    storeTimeSlot: {
      type: Object,
      default: null,
    },
    manualDate: {
      type: [Object, String],
    },
    configTimeSlot: {
      type: Object
    },
    appointmentAssign: {
      type: Object
    },
    facilitySelectedId: {
      type: Number,
    },
    user: {
      type: Object,
    },
  },
  data() {
    return {
      configAppoinmentTime: [],
      selectedAppointmentTime: null,
      selectedAppointmentDoctor: null,
      selectedCategory: null,
      showAppointmentTime: false,
      base: $("#broadcasting_url").val(),
      followUpReferredId: 0,
      followDeclined: null,
      followUpCode: null,
      configSelectedTime: null,
      configOpdcategory: null,
      selectedDepartment: null,
      selectedOpdCategory: null,
      opdSubcategories: [
        'Family Medicine',
        'Internal Medicine',
        'General Surgery',
        'Trauma Care',
        'Burn Care',
        'Opthalmology',
        'ENT',
        'Neurology',
        'Urosurgery',
        'Toxicology',
        'OB-GYNE',
        'Pediatric',
        'Oncology',
        'Nephrology',
        'Dermatology', 
        'Surgery',
        'Geriatics Medicine',
        'Physical and Rehabilitation Medicine',
        'Orthopedics',
        'Cardiology',
      ],
      sub_opd_id: null,
      highlightedCategory: null, 
      pulseCategory: null,
      timeSlots: []
    };
  },
  mounted() {
    const telemedicineFollowUp = JSON.parse(
      decodeURIComponent(
        new URL(window.location.href).searchParams.get("appointment")
      )
    );
    if (telemedicineFollowUp) {
      this.followUpReferredId = telemedicineFollowUp[0].referred_id;
      console.log(this.followUpReferredId);
      this.followDeclined = telemedicineFollowUp[0].Lateststatus;
      this.followUpCode = telemedicineFollowUp[0].code;
    }
  },
  watch: {
   appointedTimes(payload) {
        if (payload && Array.isArray(payload.slots)) {
            this.timeSlots = payload.slots;
        } else {
            this.timeSlots = [];
        }
        this.showAppointmentTime = this.timeSlots.length > 0;
    },
    facilitySelectedId: async function (newValue, oldValue) {
      this.showAppointmentTime = false;
      this.selectedAppointmentTime = null;
      this.selectedAppointmentDoctor = null;
      this.selectedCategory = null;
      this.sub_opd_id = null;
    },
  },
  computed: {
    sortedTimeSlots() {
      return [...this.timeSlots].sort((a, b) => {
        const timeA = a.appointedTime.split(':').map(Number);
        const timeB = b.appointedTime.split(':').map(Number);
        return timeA[0] * 60 + timeA[1] - (timeB[0] * 60 + timeB[1]);
      });
    },
  },
  methods: {
    formatTime(time) {
      const [hours, minutes] = time.split(':');
      const hour = parseInt(hours);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const displayHour = hour % 12 || 12;
      return `${displayHour}:${minutes} ${ampm}`;
    },
    isSlotPast(slot){
        const now = new Date()
        const slotDateTime = new Date(
            `${slot.appointment_date}T${slot.appointedTime}`
        );
        
        const today =
          now.getFullYear() + '-' +
          String(now.getMonth() + 1).padStart(2, '0') + '-' +
          String(now.getDate()).padStart(2, '0');

        return slot.appointment_date === today && slotDateTime <= now;
    },  
    formatTimeRange(timeFrom, timeTo) {
      return `${this.formatTime(timeFrom)} - ${this.formatTime(timeTo)}`;
    },
    
    selectTimeSlot(slot) {
      console.log("my sloe sched", slot);
      this.selectedAppointmentTime = slot.id;
      // this.selectedAppointmentDoctor = slot.assignedDoctors.length > 0 
      //   ? slot.assignedDoctors[0].id 
      //   : null;
      this.selectedAppointmentDoctor = slot.createdId.length > 0 
        ? slot.createdId
        : null;
    },
    
    getDoctorName(slot) {
      if (slot.assignedDoctors && slot.assignedDoctors.length > 0) {
        return slot.assignedDoctors[0].name || slot.createdBy;
      }
      return slot.createdBy;
    },
    
    getSelectedSlot() {
      return this.timeSlots.find(slot => slot.id === this.selectedAppointmentTime);
    },
    
    handleProceedAppointment() {
      const selectedSlot = this.getSelectedSlot();
      
      if (!selectedSlot) {
        Lobibox.alert("error", {
          msg: "Please select a time slot",
        });
        return;
      }
      
      // Pass the selected slot data to proceedAppointment
      this.proceedAppointment(
        null, // configtime
        selectedSlot.appointment_date, // configDate
        selectedSlot.id, // appointmentId
        null, // configId
        selectedSlot.opdCategory, // opdSubcateg
        selectedSlot.departmentId
      );
    },
    
    proceedAppointment(configtime, configDate, appointmentId, configId, opdSubcateg, departmentId) { 
      if ((!configId && !this.selectedAppointmentTime) || (configId && !configtime)) {
        Lobibox.alert("error", {
          msg: "Please Select Time",
        });
        return;
      }
     
      if (this.followUpReferredId) {
        console.log("AppointmentDoctor", this.selectedAppointmentDoctor, opdSubcateg);
        const [timeFrom, timeTo] = (String(configtime || "00:00-23:59")).split('-');
        $("#telemed_follow_code").val(this.followUpCode);
        $("#telemedicine_follow_id").val(this.followUpReferredId);
        $(".telemedicine").val(1);
        $("#AppointmentId").val(this.selectedAppointmentTime);
        $("#DoctorId").val(this.selectedAppointmentDoctor);
        $("#followup_facility_id").val(this.facilitySelectedId);
        $("#configId").val(parseInt(opdSubcateg));
        $("#configAppointmentId").val(appointmentId);
        $("#configDate").val(configDate);
        $("#configTimefrom").val(timeFrom);
        $("#configTimeto").val(timeTo);
        $("#OPD_SubId").val(parseInt(opdSubcateg));

        console.log("follow declined:",this.followDeclined);
        if(this.followDeclined){
          $("#rebookAppoitment").val(this.followDeclined);
          $("#followup_header").html("Rebook Appointment");
          $("#fileUploadGroup").remove();
          $("#followUpHr").remove();
        }else{
          $("#followup_header").html("Follow Up Patient");
        }
        
        $("#telemedicineFollowupFormModal").modal("show");
      } else {
        let appointment = null;
        if(this.selectedCategory){
          appointment = {
            facility_id: this.facilitySelectedId,
            appointmentId: this.selectedAppointmentTime,
            config_id: this.selectedCategory,
            configDate: configDate,
            configtime: configtime,
            subOpdId: parseInt(opdSubcateg),
            departmentId: parseInt(departmentId)
          };
          this.$emit("proceed-appointment", appointment);
        } else {
          appointment = {
            facility_id: this.facilitySelectedId,
            appointmentId: this.selectedAppointmentTime,
            doctorId: this.selectedAppointmentDoctor,
            subOpdId: parseInt(opdSubcateg),
            departmentId: parseInt(departmentId)
          };
        }

        window.location.href = `${
          this.base
        }/doctor/patient?appointmentKey=${this.generateAppointmentKey(
          255
        )}&appointment=${encodeURIComponent(JSON.stringify([appointment]))}`;
      }
    },
    
    generateAppointmentKey(length) {
      const characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      let key = "";

      for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        key += characters.charAt(randomIndex);
      }

      return key;
    },
  },
};
</script>

<template>
  <div class="col-md-3">
    <div class="jim-content">
      <h3 class="page-header">Time Slot</h3>
      <div class="calendar-container">
        <section class="content" style="padding-left: 0px; padding-right: 0px;">
          <div class="box box-primary">
            <div class="box-body no-padding">
              <div v-if="!showAppointmentTime" class="no-slots-message">
                <p style="padding: 20px; text-align: center; color: #999;">
                  No available time slots. Please select a date.
                </p>
              </div>
              
              <div v-else class="appointment-time-list">
                <div 
                  v-for="slot in sortedTimeSlots" 
                  :key="slot.id" 
                  class="time-slot-item"
                  :class="{ 
                    'selected': selectedAppointmentTime === slot.id,
                    'disabled': isSlotPast(slot) || slot.slot <= 0 
                  }"
                  @click="(!isSlotPast(slot) && slot.slot > 0) ? selectTimeSlot(slot) : null"
                >
                  <div class="time-slot-content">
                    <input 
                      type="radio" 
                      :id="'slot_' + slot.id"
                      :value="slot.id"
                      v-model="selectedAppointmentTime"
                      class="hours_radio"
                      :disabled="isSlotPast(slot) || slot.slot <= 0"
                      @change="selectTimeSlot(slot)"
                    />
                    <label :for="'slot_' + slot.id" class="time-slot-label">
                      <div class="time-info">
                        <span class="time-range">
                          {{ formatTimeRange(slot.appointedTime, slot.appointedTimeTo) }}
                        </span>
                        <span class="slot-count" v-if="slot.slot">
                          ({{ slot.slot }} slot {{ slot.slot > 1 ? 's' : '' }})
                        </span>
                      </div>
                      <div class="doctor-info">
                        <i class="fa fa-user-md"></i>
                        <span class="doctor-name">{{ getDoctorName(slot) }}</span>
                      </div>
                    </label>
                  </div>
                </div>
                
                <!-- Proceed Button -->
                <div class="proceed-button-container" v-if="selectedAppointmentTime">
                  <button 
                    @click="handleProceedAppointment"
                    class="btn btn-success btn-block btn-proceed"
                  >
                    <i class="fa fa-check-circle"></i> Proceed Appointment
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<style scoped>
.time-slot-item.disabled {
  border-color: #dc3545;
  background-color: #fff5f5;
  cursor: not-allowed;
  opacity: 0.7;
}

.time-slot-item.disabled:hover {
  border-color: #dc3545;
  box-shadow: none;
}

.time-slot-item.disabled .hours_radio {
  cursor: not-allowed;
}

.time-slot-item.disabled .time-slot-label {
  cursor: not-allowed;
  color: #999;
}

.appointment-time-list {
  padding: 10px;
}

.time-slot-item {
  margin-bottom: 10px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  padding: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  background-color: #fff;
}

.time-slot-item:hover {
  border-color: #00a65a;
  box-shadow: 0 2px 8px rgba(0, 166, 90, 0.1);
}

.time-slot-item.selected {
  border-color: #00a65a;
  background-color: #f0f9f4;
  box-shadow: 0 2px 8px rgba(0, 166, 90, 0.2);
}

.time-slot-content {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.hours_radio {
  margin-top: 5px;
  transform: scale(1.5);
  cursor: pointer;
  accent-color: #00a65a;
  flex-shrink: 0;
}

.time-slot-label {
  flex: 1;
  cursor: pointer;
  margin: 0;
  font-weight: normal;
}

.time-info {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
}

.time-range {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

.slot-count {
  font-size: 13px;
  color: #666;
  font-weight: normal;
}

.doctor-info {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #666;
  font-size: 14px;
}

.doctor-info i {
  color: #00a65a;
}

.doctor-name {
  font-style: italic;
}

.page-header {
  margin: 10px 0 0 0;
  font-size: 22px;
}

.no-slots-message {
  min-height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.box {
  margin-bottom: 0;
}

.box-body {
  padding: 0;
}
</style>