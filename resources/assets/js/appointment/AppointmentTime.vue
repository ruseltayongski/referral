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
      this.followUpCode = telemedicineFollowUp[0].code;
    }
  },
  watch: {
    appointedTimes: async function (payload) {
      this.sub_opd_id = payload[0]['opdCategory'];
      if(this.facilitySelectedId == this.user.facility_id) {
        Lobibox.alert("error", {
            msg: "You cannot book your own facility"
        });
        return;
      }
      this.showAppointmentTime = true;
      this.selectedAppointmentTime = null;
      this.selectedAppointmentDoctor = null;
      this.selectedCategory = null;

       this.highlightFirstAvailableCategory();
    },
    facilitySelectedId: async function (newValue, oldValue) {
      this.showAppointmentTime = false;
    },
    configSelectedTime(newValue) {
      // console.log("my value::", newValue);
      this.emitCurrentData();
    },
    currentConfig: {
      deep: true,
      handler() {
        this.emitCurrentData();  
      },
    },
  },
  computed: {
     groupedAppointments() {
    const grouped = {};
    
    this.appointedTimes.forEach(appointment => {
      const categoryId = appointment.sub_opd.id;
      const categoryDescription = appointment.sub_opd.description;
      
      if (!grouped[categoryId]) {
        grouped[categoryId] = {
          description: categoryDescription,
          appointments: []
        };
      }
      
      grouped[categoryId].appointments.push(appointment);
    });
    
    return grouped;
  },

     selectedDepartmentId() {
      return this.appointedTimes.find(app => app.id === this.selectedAppointmentTime);
    },
    currentConfig() {
      if(Array.isArray(this.configTimeSlot)){
        return this.configTimeSlot[0] || {};
      }
      return Object.values(this.configTimeSlot)[0] || {};
    },
     showConfigTimeSlot() {
      // Check if configTimeSlot has data
      return this.configTimeSlot && Object.keys(this.configTimeSlot).length > 0;
    },
    areAllAppointmentFull() {

    let currentDateTime = new Date();
    let currentDate = currentDateTime.toISOString().split("T")[0];
    let currentTime = currentDateTime.toTimeString().split(" ")[0].substring(0, 5);

    return this.appointedTimes.every((appointment) => {
        let { appointed_date: date, appointed_time: time } = appointment;

        // Check if the appointment date is in the past
        if (date < currentDate) {
            return true; // Past date, slot is unavailable
        }

        // Check if the appointment time is in the past for the current date
        if (date === currentDate && time < currentTime) {
            return true; // Past time, slot is unavailable
        }

        return this.areAllSlotAvailable(
            appointment.telemed_assigned_doctor,
            date,
            time
        );
    });
}
  },
  methods: {
    shouldHighlightCategory(categoryId) {
      return this.highlightedCategory === categoryId;
    },
    shouldPulseCategory(categoryId) {
      return this.pulseCategory === categoryId;
    },
    highlightFirstAvailableCategory() {
      // Find the first available category
      const availableCategories = Object.keys(this.groupedAppointments).filter(categoryId => {
        const categoryGroup = this.groupedAppointments[categoryId];
        return !this.isCategoryFullyBooked(categoryGroup.appointments);
      });
      
      if (availableCategories.length > 0) {
        // Option 1: Just highlight without auto-selecting
        this.highlightedCategory = availableCategories[0];
        this.pulseCategory = availableCategories[0];
        
        // Option 2: Auto-select the first available category (uncomment if desired)
        // this.selectedCategory = availableCategories[0];
        
        // Remove pulse after 3 seconds
        setTimeout(() => {
          this.pulseCategory = null;
        }, 3000);
        
        // Remove highlight after 10 seconds or when user selects a category
        setTimeout(() => {
          if (this.highlightedCategory === availableCategories[0]) {
            this.highlightedCategory = null;
          }
        }, 10000);
      }
    },
    handleCategoryChange(){
      this.selectedAppointmentTime = '';
    },
     emitCurrentData() {
       const appointment = {
          selectedTime: this.currentConfig.timeSlots,
          date: this.currentConfig.date,
          appointmentId: this.currentConfig.appointment_id,
        };

        // Assign the appointment object to _appointmentConfigData
        this._appointmentConfigData(appointment);

    },
    handleconfigTimeSelection(timeSlot){
      this.configSelectedTime = timeSlot;
      this.selectedDepartment = null;
    },
    handleconfigcategory(opdSubcateory){
       this.configOpdcategory = opdSubcateory;
    },
    formatTimeSlot(timeSlot){
      return timeSlot.replace('-', ' to ');
    },
    areAllSlotAvailable(telemed_assigned_doctor, date, time) {

      if (!telemed_assigned_doctor || !Array.isArray(telemed_assigned_doctor) || telemed_assigned_doctor.length === 0) {
          return false; 
      }

      const assignedCount = telemed_assigned_doctor.length;
      const appointmentId = telemed_assigned_doctor[0]?.appointment_id;
    
      let slotCapacity = this.getSlotCapacity(appointmentId);

        if (slotCapacity === undefined) {
            slotCapacity = 1;
        }
        const isSlotFull = assignedCount >= slotCapacity;
      
      // console.log("assignedCount", assignedCount, 'slotCapacity', appointmentId, "isSlotFull", isSlotFull);
      
      if (date) {
          let currentDateTime = new Date();
          let currentDate = currentDateTime.toISOString().split("T")[0];
          let currentTime = currentDateTime.toTimeString().split(" ")[0].substring(0, 5);

          if (date < currentDate) {
              return true; // Past date, slot is unavailable
          }

          if (date === currentDate && time < currentTime) {
              return true; // Past time on current date, slot is unavailable
          }
      }
      
      return isSlotFull; // Return true if slot is full (to disable it)
  },
  isCategoryFullyBooked(appointments){
    return appointments.every(appointment => 
      this.areAllSlotAvailable(
        appointment.telemed_assigned_doctor,
        appointment.appointed_date,
        appointment.appointed_time
      ) || this.isPastDatetime(appointment.appointed_date, appointment.appointed_time)
    );
  },
    getSlotCapacity(appoinmentId){
       if (!appoinmentId) {
            return 1; // Default to 1 if no appointmentId
        }
        
       const appointment = this.appointedTimes.find(app => app.id === appoinmentId);
    
      if (!appointment) {
          console.warn(`Warning: No appointment found with id ${appoinmentId}`);
          return 1; // Default to 1 if not found
      }
      
      // Return the slot value from the appointment
      return appointment.slot || 1; // Default to 1 if no slot property
        
    },
    isPastDatetime(appointedDate, appointedTime){
       const now = new Date();
       const appointmentDateTime = new Date(`${appointedDate}T${appointedTime}`);

        return appointmentDateTime < now;
    },
    proceedAppointment(configtime,configDate,appointmentId,configId,opdSubcateg) { 
   
      if ((!configId && !this.selectedAppointmentTime) || (configId && !configtime)) {
        Lobibox.alert("error", {
          msg: "Please Select Time",
        });
        return;
      } else if (!this.selectedCategory) {
        Lobibox.alert("error", {
          msg: "Please Select Opd Sub category",
        });
        return;
      }else if (this.followUpReferredId && !this.selectedCategory) {
        Lobibox.alert("error", {
          msg: "Configuration ID is required for follow-up appointments.",
        });
        return;
      }
     
      if (this.followUpReferredId) {
        const [timeFrom, timeTo] = (String(configtime || "00:00-23:59")).split('-');
        // console.log("follow upd opdId:", this.selectedCategory, this.selectedAppointmentTime);
        $("#telemed_follow_code").val(this.followUpCode);
        $("#telemedicine_follow_id").val(this.followUpReferredId);
        $(".telemedicine").val(1);
        $("#AppointmentId").val(this.selectedAppointmentTime);
        $("#DoctorId").val(this.selectedAppointmentDoctor);
        $("#followup_facility_id").val(this.facilitySelectedId);

        $("#configId").val(this.selectedCategory);
        $("#configAppointmentId").val(appointmentId);
        $("#configDate").val(configDate);
        $("#configTimefrom").val(timeFrom);
        $("#configTimeto").val(timeTo);

        $("#followup_header").html("Follow Up Patient");
        $("#telemedicineFollowupFormModal").modal("show");
      } else {
        let appointment = null;
        // console.log("appointmentID:", appointmentId);
        if(this.selectedCategory){
            appointment = {
              facility_id: this.facilitySelectedId,
              appointmentId: this.selectedAppointmentTime,
              config_id: this.selectedCategory,
              configDate: configDate,
              configtime: configtime,
              subOpdId: opdSubcateg,
              departmentId: this.selectedDepartmentId.department_id
            };
            this.$emit("proceed-appointment", appointment);
        } else {
            appointment = {
              facility_id: this.facilitySelectedId,
              appointmentId: this.selectedAppointmentTime,
              doctorId: this.selectedAppointmentDoctor,
              subOpdId: parseInt(this.sub_opd_id),
            };
        }

        // console.log(appointment);
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
    handleAppointmentTimeChange() {
      this.selectedAppointmentDoctor = null;
    },
    handleDoctorChange(doctorId, appointmentId) {
      // console.log(doctorId, 'appointmentId', appointmentId);

      this.selectedAppointmentDoctor = doctorId;
    },
    async _appointmentConfigData(payload){ 
      const response =  await appointmentConfigData(payload);
      this.configAppoinmentTime = response.data;

      this.$emit("getconfig-Appointment",  response.data);
    },
    normalizeTimeFormat(timeString) {
      // Normalize time to HH:mm (no seconds)
      const [hours, minutes] = timeString.split(":");
      return `${hours}:${minutes}`;
    },

    configAppointmentNot(timeSlot) {
      const [timeSlot_start, timeSlot_end] = timeSlot.split("-");
      const normalizedTimeSlotStart = this.normalizeTimeFormat(timeSlot_start);
      const normalizedTimeSlotEnd = this.normalizeTimeFormat(timeSlot_end);

      return this.configAppoinmentTime.some((config) => {
        const normalizedConfigStartTime = this.normalizeTimeFormat(config.start_time);
        const normalizedConfigEndTime = this.normalizeTimeFormat(config.end_time);
        
        return normalizedConfigStartTime === normalizedTimeSlotStart && normalizedConfigEndTime === normalizedTimeSlotEnd;
      });
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
          <!-- <div class="row"> -->
            <div class="box box-primary">
              <div class="box-body no-padding">
                <!-- <div class="box-header with-border">
                  <h4 class="box-title">Legends</h4>
                </div> -->
                <div>
                  <div class="external-event bg-green">Available Slot</div>
                  <div
                    class="external-event"
                    style="background-color: #dd4b39; color: #ffff"  
                  >
                    Full SLot
                  </div>
                </div>
                <div class="box box-solid">
                  <div class="box-header with-border">
                    <div id="date-selected"></div>
                  </div>

                  <div
                    class="box-body"
                    v-if="appointedTimes.length > 0 && showAppointmentTime && manualDate"
                  >
                    <div
                      class="appointment-category-list"
                      v-for="(categoryGroup, categoryId) in groupedAppointments"
                      :key="categoryId"
                    >
                      <div class="category-header"
                      >
                        <input
                          type="radio"
                          class="category_radio"
                          v-model="selectedCategory"
                          :value="categoryId"
                          @change="handleCategoryChange"
                          :disabled="isCategoryFullyBooked(categoryGroup.appointments)"
                        />&nbsp;&nbsp;
                        <span 
                           :class="{
                              'text-success': !isCategoryFullyBooked(categoryGroup.appointments),
                              'text-danger': isCategoryFullyBooked(categoryGroup.appointments)
                            }"                     
                         style="font-size: 16px;">
                          {{ categoryGroup.description }}
                          <span v-if="isCategoryFullyBooked(categoryGroup.appointments)">
                          </span>
                        </span>
                      </div>

                      <!-- Timeslot Dropdown for Selected Category -->
                      <div 
                        class="timeslot-dropdown-container" 
                        v-if="selectedCategory === categoryId"
                        style="margin-left: 25px; margin-top: 10px;"
                      >
                        <label for="timeslot-select" class="form-label text-success">Select Time Slot:</label>
                        <select
                          id="timeslot-select"
                          class="form-control"
                          v-model="selectedAppointmentTime"
                          @change="handleAppointmentTimeChange"
                          style="width: 100%; max-width: 300px;"
                        >
                          <option value="" disabled>Choose a time slot</option>
                          <option
                            v-for="appointment in categoryGroup.appointments"
                            :key="appointment.id"
                            :value="appointment.id"
                            :disabled="
                              areAllSlotAvailable(
                                appointment.telemed_assigned_doctor,
                                appointment.appointed_date,
                                appointment.appointed_time
                              ) || !appointment.telemed_assigned_doctor || isPastDatetime(appointment.appointed_date, appointment.appointed_time)
                            "
                            :class="{
                              'text-success': !areAllSlotAvailable(appointment.telemed_assigned_doctor),
                              'text-danger': areAllSlotAvailable(appointment.telemed_assigned_doctor) || !appointment.telemed_assigned_doctor || isPastDatetime(appointment.appointed_date, appointment.appointed_time)
                            }"
                          >
                            {{ appointment.appointed_time }} to {{ appointment.appointedTime_to }} {{ appointment.created_by ? '- Dr. ' + appointment.created_by.fname + ' ' + appointment.created_by.lname : ''  }}
                            <span v-if="areAllSlotAvailable(appointment.telemed_assigned_doctor) || !appointment.telemed_assigned_doctor || isPastDatetime(appointment.appointed_date, appointment.appointed_time)">
                              (Unavailable)
                            </span>
                          </option>
                          
                        </select>
                      </div>
                    </div>

                    <button
                      v-if="!areAllAppointmentFull"
                      type="button"
                      id="consultation"
                      class="btn btn-success bt-md btn-block"
                      @click="proceedAppointment"
                      style="margin-top: 20px;"
                    >
                      <i class="fa fa-calendar"></i>&nbsp;&nbsp;Appointment
                    </button>
                      <label
                        v-else
                        id="consultation"
                        class="btn bt-md btn-block"
                        style="background-color: #dd4b39; font-weight: bold; color: rgb(255, 255, 255); margin-top: 20px; pointer-events: none; cursor: default;"
                        aria-readonly="true"
                      >
                        <i class="fa fa-calendar"></i>&nbsp;&nbsp;All appointments are full
                      </label>
                  </div>
                  

                </div>
              </div>
            </div>
          <!-- </div> -->
        </section>
      </div>
    </div>
  </div>
</template>
<style scoped>
/* .category-highlight {
  background: linear-gradient(90deg, rgba(0, 166, 90, 0.1) 0%, rgba(0, 166, 90, 0.05) 100%);
  border-left: 4px solid #00a65a;
  padding: 8px;
  margin: 2px 0;
  border-radius: 4px;
  transition: all 0.3s ease;
} */
/* .category-pulse {
  animation: category-pulse 2s ease-in-out 3;
}

@keyframes category-pulse {
  0% { 
    box-shadow: 0 0 0 0 rgba(0, 166, 90, 0.4);
    transform: scale(1);
  }
  50% { 
    box-shadow: 0 0 0 10px rgba(0, 166, 90, 0.1);
    transform: scale(1.02);
  }
  100% { 
    box-shadow: 0 0 0 0 rgba(0, 166, 90, 0);
    transform: scale(1);
  }
} */

.appointment-time-list {
  /* display: flex;  */
  padding: 10px;
}
.appointment-time-list > .doctor-list {
  display: block !important;
  list-style-type: none;
  margin-top: 7px;
}

.appointment-time-list1 {
  /* display: flex;  */
  padding: 10px;
}
.appointment-time-list1 .doctor-list1 {
  display: block !important;
  list-style-type: none;
  margin-top: 7px;
}

.hours_radio {
  margin-bottom: 5px;
  transform: scale(1.5);
  cursor: pointer;
  accent-color: #00a65a;
}
#consultation {
  margin-top: 20px;
}
.timeDoctor {
  font-size: 16px;
}
.page-header {
  margin: 10px 0 0 0;
  font-size: 22px;
}

</style>
