<template>
  <div class="row">
    <div class="col-md-12">
      <div class="jim-content">
        <h3 class="page-header">Select Facility</h3>
        <div class="row">
          <div class="scroll-container">
            <appointment-facility
              v-if="appointment_slot"
              v-for="appointment in appointment_slot"
              :key="appointment.id"
              :facilitySelectedId="facilitySelectedId"
              :appointment="appointment"
              :config_appoint="appointment_config"
              :user="user"
              @facilitySelected="facilitySelected"
            ></appointment-facility>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <appointment-calendar
          :facilitySelectedId="facilitySelectedId"
          :appointmentSlot="appointment_slot"
          :user="user"
          @appointedTime="appointedTime"
          @config_appointedTime="config_appointedTime"
          @day-click-date="handleDayClickdate"
          @manual-click-date="manualClickDate"
        ></appointment-calendar>
        <appointment-time
          ref="appointmentTimeRef"
          :facilitySelectedId="facilitySelectedId"
          :appointedTimes="appointedTimes"
          :configTimeSlot ="configTimeSlot"
          :appointmentclickDate ="appointmentclickDate"
          :selected-date="selectedDate"
          :manualDate ="manualDate"
          :user="user"
          @patient-booking-proceed="handlePatientBookingProceed"
        ></appointment-time>
      </div>
    </div>
  </div>

  <!-- Telemedicine Consultation Form Modal -->
  <telemedicine-consultation-form
    :user="user"
    :appointmentData="appointmentData"
    :patientData="patientData"
  ></telemedicine-consultation-form>
  
</template>
<script>
import AppointmentFacility from "./AppointmentFacility.vue";
import AppointmentCalendar from "./AppointmentCalendar.vue";
import AppointmentTime from "./AppointmentTime.vue";
import TelemedicineConsultationForm from "./TelemedicineConsultationForm.vue";

let baseUrlgetConfig = `${window.baseUrl}/doctor/getconfigappointment`;
export default {
  name: "AppointmentApp",
  components: {
    AppointmentFacility,
    AppointmentCalendar,
    AppointmentTime,
    TelemedicineConsultationForm,
  },
  props: ["user", "appointment_slot", "appointment_config"],
  data() {
    return {
      facilitySelectedId: 0,
      appointedTimes: [],
      configTimeSlot: [],
      appointmentclickDate: null,
      manualDate: null,
      selectedDate: null,
      appointmentData: {},
      patientData: {},
    };
  },
  mounted() {},
  methods: {
    focusAppointmentTimeSidebar() {
    // Method 1: Using Vue $refs
      try {
        if (this.$refs.appointmentTimeRef && this.$refs.appointmentTimeRef.$el) {
          // Scroll to the sidebar with smooth animation
          this.$refs.appointmentTimeRef.$el.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
            inline: 'nearest'
          });
          
          // Add visual highlight
          this.highlightSidebar();
        } else {
          console.warn('AppointmentTime ref not found');
        }
      } catch (error) {
        console.error('Error focusing sidebar:', error);
      }
  },
  highlightSidebar() {
    const sidebarElement = this.$refs.appointmentTimeRef?.$el;
    if (sidebarElement) {
      // Add temporary highlight class
      sidebarElement.classList.add('highlight-sidebar');
      
      // Remove highlight after animation
      setTimeout(() => {
        sidebarElement.classList.remove('highlight-sidebar');
      }, 2000);
    }
  },
    manualClickDate(date){
      this.manualDate = date;

      if(date){
        this.$nextTick(() => {
          this.focusAppointmentTimeSidebar();
        });
      }
    },
    handleDayClickdate(payload){
        this.appointmentclickDate = payload;
       
       if(payload && payload.length > 0){
         this.$nextTick(() => {
            this.focusAppointmentTimeSidebar();
          });
       }
    },
    facilitySelected(payload) {
      this.facilitySelectedId = payload;
    },
    appointedTime(payload) {
      this.appointedTimes = payload;

      if (payload && payload.length > 0) {
        this.$nextTick(() => {
          this.focusAppointmentTimeSidebar();
        });
      }
    },
    config_appointedTime(payload){
      this.configTimeSlot = payload;
    },
    handlePatientBookingProceed(payload) {
      // Populate appointment data for the telemedicine form
      this.appointmentData = payload.appointmentData;
      
      // Fetch patient data if patient_id exists
      if (payload.user && payload.user.patient_id) {
        this.fetchPatientData(payload.user.patient_id);
      } else {
        // Open modal immediately if no patient_id
        this.openTelemedicineModal();
      }
    },
    
    fetchPatientData(patientId) {
      const baseUrl = $("#broadcasting_url").val() || window.location.origin;
      const url = `${baseUrl}/doctor/patient/info/${patientId}`;
      
      $.ajax({
        url: url,
        type: 'GET',
        success: (response) => {
          console.log('Patient data fetched:', response);
          this.patientData = response;
          this.openTelemedicineModal();
        },
        error: (error) => {
          console.error('Error fetching patient data:', error);
          Lobibox.notify('error', {
            msg: 'Failed to load patient information'
          });
          // Open modal anyway with available data
          this.openTelemedicineModal();
        }
      });
    },
    
    openTelemedicineModal() {
      this.$nextTick(() => {
        window.dispatchEvent(new Event('telemedicine:request-open'));
      });
    },
  },

};
</script>
<style>
@import "./css/index.css";
</style>