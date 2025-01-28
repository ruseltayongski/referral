<template>
  <div
    class="col-md-4 scroll-item"
    v-if="appointment.id !== user.facility_id && shouldDisplayFacility"
  >
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
          <div class="col-sm-4 border-right">
            <div class="description-block">
              <h5 class="description-header">
                {{ balanceSlotThisMonth }}
              </h5>
              <span class="description-text">Total Appointments</span>
            </div>
          </div>
          <div class="col-sm-4 border-right">
            <div class="description-block">
              <h5 class="description-header">
                {{ emptyAppointmentByCount }}
              </h5>
              <span class="description-text">Available Slot</span>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="description-block">
              <button
                class="btn btn-block btn-success btn-select"
                id="selected_data"
                name="selected_data"
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
    emptyAppointmentByCount() {
      let count = 0;
     // let totalTimeSlots = 0;
      const now = new Date();
      //for config Schedule display facility 


      // for manual Schedule
      if (this.appointment && this.appointment.appointment_schedules) {
        const appointmentIdMap = new Map();

        this.appointment.appointment_schedules.forEach((sched) => {
          // Combine appointed_date and appointed_time into a single Date object
          const appointedDatetime = new Date(
            `${sched.appointed_date}T${sched.appointed_time}`
          );

          // Check if the schedule is in the future
          if (appointedDatetime > now) {
            sched.telemed_assigned_doctor.forEach((doctor) => {
              const appointmentId = doctor.appointment_id;
              // Initialize the map for this appointment_id if not done already
              if (!appointmentIdMap.has(appointmentId)) {
                appointmentIdMap.set(appointmentId, {
                  allNull: true, // Assume all appointment_by are null initially
                  hasNull: false, // Track if there's at least one null
                });
              }
              // Initialize the map for this appointment_id if not done already
              const status = appointmentIdMap.get(appointmentId);
              if (doctor.appointment_by) {
                status.allNull = false;
              } else {
                status.hasNull = true;
              }
            });
          }
        });
        // Count the number of appointment_ids where all appointment_by are null
        appointmentIdMap.forEach((status, appointmentId) => {
          if (status.hasNull) {
            count++;
          }
        });
      }
      return count;
    },
    shouldDisplayFacility() {
      const now = new Date();

      const hasValidAppointment = this.appointment.appointment_schedules.some(
        (sched) => {
          // console.log("sched", sched);
          //for config Schedule display facility 
          let DisplayConfigFacility;
          if(sched.configId){

              const effectiveDate = new Date(sched.appointed_date);
              const date_endMidnight = new Date(sched.date_end);
              const configTime = sched.config_schedule.time.split('|');
              const timeSlot = configTime.filter((item) => item.includes('-'));

              // const configDateRange = now >= effectiveDate && now <= date_end;
              // console.log("time", configTime, 'day::', everydaySched, "effectiveDate:", effectiveDate, "date end: ", date_end);
              // console.log("configDateRange", configDateRange);
              // const validDaysmonth = [];
              // let currentDate = new Date(effectiveDate);
              // while (currentDate <= date_end){
              //   validDaysmonth.push(currentDate.getDate());
              //   currentDate.setDate(currentDate.getDate() +1);// Move to the next day
              // }
              // // Check if today is in the valid range of days of the month
              // const todayDate = now.getDate();
              // const isTodayInMonthSched = validDaysmonth.includes(todayDate);

              // // Check if today matches a valid day of the week
              // const today = now.toLocaleString('en-US', { weekday: 'long'});
              // console.log('today', today);
              // const isTodayInSchedule = everydaySched.includes(today);

              const isWithinTimeRange = timeSlot.some((timeRange) => {
               const [start, end] = timeRange.split('-');
                const startTime = new Date();
                const endTime = new Date();
                
                const [startHours, startMinutes] = start.split(':').map(Number);
                const [endHours, endMinutes] = end.split(':').map(Number);

                startTime.setHours(startHours, startMinutes, 0, 0);
                endTime.setHours(endHours, endMinutes, 0, 0);
     
                // return now <= startTime && now <= endTime
                   const appointmentDatetime = new Date(
                  `${sched.date_end} ${timeRange}`
                );
                const midnightAppointmentDay = new Date(appointmentDatetime);
                midnightAppointmentDay.setHours(24, 0, 0, 0); 

                return now < midnightAppointmentDay;
            });
            
          // console.log('isWithinTimeRange:',isWithinTimeRange, 'isTodayInSchedule:', isTodayInSchedule , isTodayInMonthSched);
          //   DisplayConfigFacility = isWithinTimeRange && (isTodayInSchedule || isTodayInMonthSched);
            DisplayConfigFacility = isWithinTimeRange 
          }
       
          // for manual Schedule
          const appointmentDatetime = new Date(
            `${sched.appointed_date} ${sched.appointed_time}`
          );
     
          // Set midnight of the appointment date
          const midnightAppointmentDay = new Date(appointmentDatetime);
          midnightAppointmentDay.setHours(24, 0, 0, 0);

          // Display facility if current time is before midnight of the appointment day
          const shouldDisplayManualFacility  = now < midnightAppointmentDay;

          return shouldDisplayManualFacility || DisplayConfigFacility;
        }
      );

      return hasValidAppointment;
    },
    balanceSlotThisMonth() {
      let usedCount = 0;
      let expiredCount = 0;
      const now = new Date();
      const currentyear = now.getFullYear();
      const currentmonth = now.getMonth();
      if (this.appointment && this.appointment.appointment_schedules) {
        // Step 1: Filter schedules by appointed_date (current mnoth and year)
        const filteredSchedules = this.appointment.appointment_schedules.filter(
          (sched) => {
            const appointedDate = new Date(sched.appointed_date);
            const appointedYear = appointedDate.getFullYear();
            const appointedMonth = appointedDate.getMonth();
            return (
              appointedYear === currentyear && appointedMonth === currentmonth
            );
          }
        );

        const appointmentIdToDate = filteredSchedules.reduce((acc, sched) => {
          acc[sched.id] = sched.appointed_date;
          return acc;
        }, {});

        const groupedDoctorByDate = {};
        this.appointment.appointment_schedules.forEach((schedule) => {
          const doctors = schedule.telemed_assigned_doctor;
          if (doctors && Array.isArray(doctors)) {
            doctors.forEach((doctor) => {
              const appointedDate = appointmentIdToDate[doctor.appointment_id];
              if (appointedDate) {
                if (!groupedDoctorByDate[appointedDate])
                  groupedDoctorByDate[appointedDate] = [];
                groupedDoctorByDate[appointedDate].push(doctor);
              }
            });
          }
        });

        Object.entries(groupedDoctorByDate).forEach(
          ([appointedDate, doctors]) => {
            const doctorsByAppointmentId = doctors.reduce((acc, doctor) => {
              if (!acc[doctor.appointment_id]) acc[doctor.appointment_id] = [];
              acc[doctor.appointment_id].push(doctor);
              return acc;
            }, {});

            Object.values(doctorsByAppointmentId).forEach(
              (appointmentDoctors) => {
                // Find the schedule associated with this appointmentDoctors group
                const schedule = filteredSchedules.find(
                  (sched) => sched.id === appointmentDoctors[0].appointment_id
                );

                if (schedule) {
                  const appointedDatetime = new Date(
                    `${schedule.appointed_date}T${schedule.appointed_time}`
                  );
                  const allAssigned = appointmentDoctors.every(
                    (doctor) => doctor.appointment_by !== null
                  );

                  if (allAssigned) {
                    usedCount++;
                  }
                  // Check if the appointment is in the future or past (expired)
                  if (appointedDatetime > now) {
                    // If all doctors are assigned, increase the used count
                  } else {
                    // If the appointment has expired, but at least one doctor is assigned, count as expired
                    console.log("appointmentDoctors", appointmentDoctors);
                    const someAssigned = appointmentDoctors.some(
                      (doctor) => doctor.appointment_by === null
                    );
                    if (someAssigned) {
                      expiredCount++;
                    }
                  }
                }
              }
            );
          }
        );
      }
      const totalcount = usedCount + expiredCount;
      return totalcount;
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
      }
      this.$emit("facilitySelected", id);
    },
  },
};
</script>
