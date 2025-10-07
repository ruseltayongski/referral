<template>
  <!-- <div
    class="col-md-4 scroll-item"
    v-if="appointment.id !== user.facility_id && shouldDisplayFacility"
  > -->
  <div class="col-md-4 scroll-item" v-if="shouldDisplayFacility">
    <div
      :class="{ highlighted: appointment.id == facilitySelectedId }"
      class="box box-widget widget-user with-badge"
    >
      <div class="widget-user-header">
        <h3 class="widget-user-username">
          {{ appointment.name }}
        </h3>
        <h5 class="widget-user-desc">
          {{ appointment.address }}
        </h5>
      </div>
      <div class="widget-user-image">
        <img :src="doh_logo" class="img-circle" alt="User Avatar" />
      </div>

      <div class="box-footer">
        <div class="row">
          <div class="col-sm-6">
            <div class="description-block">
              <h5 class="description-header">
                {{ balanceSlotThisMonth }}
              </h5>
              <span class="description-text">Total Appointments</span>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="description-block">
              <h5 class="description-header">
                {{ AvailableSlot }}
              </h5>
              <span class="description-text">Available Slot</span>
            </div>
          </div>
          <div class="col-sm-4 text-right">
            <div class="description-block">
              <button
                class="btn btn-block btn-success btn-select"
                id="selected_data"
                name="selected_data"
                style="width: auto"
                @click="facilitySelected(appointment.id)"
              >
                Select
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "AppointmentFacility",
  data() {
    return {
      asigned_slot: null,
      doh_logo:
        $("#broadcasting_url").val() + "/resources/img/video/doh-logo.png",
    };
  },
  props: {
    user: {
      type: Object,
    },
    appointment: {
      type: Object,
    },
    config_appoint: {
      type: Object,
    },
    facilitySelectedId: {
      type: Number,
    },
  },
  computed: {
    AvailableSlot() {
      // let totalSlots = 0;  // Total number of slots
      // let assignedSlots = 0; // Slots that have been assigned
      // let expiredSlots  = 0;
      let availableSlots = 0;

      const now = new Date();
      // Step 1: Sum all slots from appointment_schedules
      if (this.appointment && this.appointment.appointment_schedules) {
        this.appointment.appointment_schedules.forEach((sched) => {
          const countslot = sched.slot || 0;
          const scheduleDateTime = new Date(
            `${sched.appointed_date}T${sched.appointed_time}`
          );

          if (
            scheduleDateTime > now &&
            sched.telemed_assigned_doctor.length < countslot
          ) {
            availableSlots += countslot - sched.telemed_assigned_doctor.length;
          }
        });
      }

      return availableSlots;
    },
    balanceSlotThisMonth() {
      let totalAppointments = 0;
      const now = new Date();
      const currentMonth = now.getMonth();
      const currentYear = now.getFullYear();

      if (this.appointment && this.appointment.appointment_schedules) {
        this.appointment.appointment_schedules.forEach((sched) => {
          const countSlot = sched.slot || 0;
          const scheduleDateTime = new Date(
            `${sched.appointed_date}T${sched.appointed_time}`
          );

          const isCurrentMonth =
            scheduleDateTime.getMonth() === currentMonth &&
            scheduleDateTime.getFullYear() === currentYear;

          if (isCurrentMonth) {
            if (
              sched.telemed_assigned_doctor &&
              sched.telemed_assigned_doctor.length > 0
            ) {
              totalAppointments += sched.telemed_assigned_doctor.length;
            }

            if (scheduleDateTime < now) {
              totalAppointments +=
                countSlot - (sched.telemed_assigned_doctor.length || 0);
            }
          }
        });
      }

      return totalAppointments;
    },
    shouldDisplayFacility() {
      const now = new Date();

      const hasValidAppointment = this.appointment.appointment_schedules.some(
        (sched) => {
          // console.log("sched", sched);
          //for config Schedule display facility
          let DisplayConfigFacility;
          if (sched.configId) {
            const effectiveDate = new Date(sched.appointed_date);
            const date_endMidnight = new Date(sched.date_end);
            const configTime = sched.config_schedule.time.split("|");
            const timeSlot = configTime.filter((item) => item.includes("-"));

            const isWithinTimeRange = timeSlot.some((timeRange) => {
              const [start, end] = timeRange.split("-");
              const startTime = new Date();
              const endTime = new Date();

              const [startHours, startMinutes] = start.split(":").map(Number);
              const [endHours, endMinutes] = end.split(":").map(Number);

              startTime.setHours(startHours, startMinutes, 0, 0);
              endTime.setHours(endHours, endMinutes, 0, 0);

              //return now <= startTime && now <= endTime
              const appointmentDatetime = new Date(
                `${sched.date_end} ${timeRange}`
              );
              const midnightAppointmentDay = new Date(appointmentDatetime);
              midnightAppointmentDay.setHours(24, 0, 0, 0);

              return now < midnightAppointmentDay;
            });

            DisplayConfigFacility = isWithinTimeRange;
          }

          // for manual Schedule
          const appointmentDatetime = new Date(
            `${sched.appointed_date} ${sched.appointed_time}`
          );

          // Set midnight of the appointment date
          const midnightAppointmentDay = new Date(appointmentDatetime);
          midnightAppointmentDay.setHours(24, 0, 0, 0);

          // Display facility if current time is before midnight of the appointment day
          const shouldDisplayManualFacility = now < midnightAppointmentDay;

          return shouldDisplayManualFacility || DisplayConfigFacility;
        }
      );

      return hasValidAppointment;
    },
  },
  watch: {
    facilitySelectedId: function (value) {},
  },
  methods: {
    facilitySelected(id) {
      if (this.facilitySelectedId !== id) {
        //remove element
        $(".fc-day").css("background-color", "");
        $(".fc-day").removeClass("add-cursor-pointer");
        $(".fc-day").removeClass("selected-date-indicator");
        // $(".selected-date-indicator").remove();
      }
      this.$emit("facilitySelected", id);
    },
  },
};
</script>
