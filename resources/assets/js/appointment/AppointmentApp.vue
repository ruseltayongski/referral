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
        ></appointment-time>
      </div>
    </div>
  </div>
  
</template>
<script>
import AppointmentFacility from "./AppointmentFacility.vue";
import AppointmentCalendar from "./AppointmentCalendar.vue";
import AppointmentTime from "./AppointmentTime.vue";

let baseUrlgetConfig = `${window.baseUrl}/doctor/getconfigappointment`;
export default {
  name: "AppointmentApp",
  components: {
    AppointmentFacility,
    AppointmentCalendar,
    AppointmentTime,
  },
  props: ["user", "appointment_slot", "appointment_config"],
  data() {
    return {
      facilitySelectedId: 0,
      appointedTimes: [],
      configTimeSlot: [],
      appointmentclickDate: null,
      manualDate: null
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
      //console.log("config_appointedTime::", payload);
      this.configTimeSlot = payload;
    },
  },

};
</script>
<style>
@import "./css/index.css";
</style>
