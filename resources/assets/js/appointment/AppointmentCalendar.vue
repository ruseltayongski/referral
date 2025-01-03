<style scoped>
.page-header {
  margin: 10px 0 0 0;
  font-size: 22px;
}
</style>
<template>
  <div class="col-md-9">
    <div class="jim-content">
      <h3 class="page-header">Appointment Calendar</h3>
      <div class="calendar-container">
        <section class="content">
          <div class="row">
            <div class="box box-primary">
              <div class="box-body no-padding">
                <div id="calendar"></div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
<script>
import { appointmentScheduleDate, appointmentScheduleHours } from "./api/index";
export default {
  name: "AppointmentCalendar",
  data() {
    return {
      calendar: null,
      header: {
        left: "prev,next today",
        center: "title",
        right: "month,agendaWeek,agendaDay",
      },
      buttonText: {
        today: "today",
        month: "month",
        week: "week",
        day: "day",
      },
      events: [],
    };
  },
  props: {
    facilitySelectedId: {
      type: Number,
    },
    appointmentSlot: {
      type: Array,
      default: () => [], // Ensure it's an empty array by default
    },
  },
  watch: {
    facilitySelectedId: async function (payload, old) {
      this.events = await this.__appointmentScheduleDate(payload);
      this.updateCalendarEvents();
    },
  },
  mounted() {
    this.ini_events($("#external-events div.external-event"));
    this.generateCalendar();
  },
  methods: {
    ini_events(ele) {
      ele.each(function () {
        var eventObject = {
          title: $.trim($(this).text()), // use the element's text as the event title
        };
        $(this).data("eventObject", eventObject);
        $(this).draggable({
          zIndex: 1070,
          revert: true,
          revertDuration: 0,
        });
      });
    },
    async generateCalendar() {
      let self = this;
      this.calendar = $("#calendar").fullCalendar({
        dayRender: this.dayRenderFunction.bind(this),
        eventRender: this.eventRenderFunction.bind(this),
        dayClick: this.dayClickFunction.bind(this),
        header: self.header,
        buttonText: self.buttonText,
        events: this.events,
        editable: true,
        droppable: true,
        drop: this.handleDrop,
      });
    },
    dayRenderFunction(date, cell) {
      var eventsOnDate = this.events.filter(function (event) {
        return moment(event.start).isSame(date, "day");
      });

      if (eventsOnDate.length > 0) {
        cell.css("background-color", "green");
        cell.addClass("add-cursor-pointer");
      }
    },
    eventRenderFunction(event, element) {
      console.log("event dateTime", event);
      //console.log(event,event.start.format('YYYY-MM-DD'))
      // let currentDate = new Date().toISOString().split("T")[0];
      let currentDateTime = new Date(); // get the current date and time
      this.$nextTick(() => {
        const targetTd = $(
          ".fc-day[data-date='" + event.start.format("YYYY-MM-DD") + "']"
        );
        const targetdrag = $(
          ".fc-draggable[data-date='" + event.start.format("YYYY-MM-DD") + "']"
        );
        const targetGrid = $(".fc-day-grid-event");
        const dateString = targetTd.attr("data-date");
        let timeslot = null;

        
        const isfullyBooked = this.appointmentSlot.some((appointment) => {

          // Config Appointment
          appointment.appointment_schedules.forEach((sched) =>{
            if(sched.configId){

              const Date_start = new Date(sched.appointed_date); // Start date
              const date_end = new Date(sched.date_end); // End date
              const timeSlot = sched.config_schedule.time.split('|');
              const daysSched = sched.config_schedule.days.split('|');

              console.log("Date_start:", Date_start, "date_end:", date_end, "daysSched", daysSched, 'timeSlot', timeSlot);
            
                // Iterate through all days in the range
                let currentDate = new Date(Date_start); // Initialize with start date
                console.log("currentDate", currentDate);
                while (currentDate <= date_end) {
                  const currentDayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' }); // Get current day's name
                  
                  if (daysSched.includes(currentDayName)) {
                    // Highlight specific day if it matches
                    const targetTd = $(".fc-day[data-date='" + moment(currentDate).format("YYYY-MM-DD") + "']");
                    targetTd.css("background-color", "#00a65a"); // Green for available
                    targetTd.css("border-color", "#00a65a");
                  }
                  
                  // Move to the next day
                  currentDate.setDate(currentDate.getDate() + 1);
                }
            }
            
          });

          // Manual Appointment
          if (appointment.appointment_schedules.length > 0) {
            const slotOndate = appointment.appointment_schedules.filter(
              (slot) => slot.appointed_date === dateString
            );
            // Group by appointment_id
            const groupedByAppointmentId = slotOndate.reduce((acc, slot) => {
              timeslot = slot.appointed_time;
              const id = slot.appointment_id;
              if (!acc[id]) acc[id] = [];
              acc[id].push(
                slot.telemed_assigned_doctor.map(
                  (doctor) => doctor.appointment_by
                )
              );
              return acc;
            }, {});
            // Check if all appointment_by for each appointment_id are assigned
            return Object.values(groupedByAppointmentId).some((appointments) =>
              appointments.every((appointment_by_list) =>
                appointment_by_list.every((appointment_by) => appointment_by)
              )
            );
          }

          return false;
        });

      //Config Appointment 
      // const dateRangeBackground = this.appointmentSlot.appointment_schedules.some((config) => {

      //   const Date_start = new Date(config.appointed_date); // Start date
      //   const date_end = new Date(config.date_end); // End date
      //   const daysSched = config.config_schedule.days.split('|'); // Schedule days
      //   console.log("Date_start:", Date_start, "date_end:", date_end, "daysSched", daysSched)
      //   const currentDate = new Date(dateString); // Current date in the calendar
      //   const currentDayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' }); // Current day name
        
      //   const withinDateRange = currentDate >= Date_start && currentDate <= date_end; // Date within range
      //   console.log("withinDateRange", withinDateRange);
      //   const isInSchedule = daysSched.includes(currentDayName); // Day is in schedule
      //   console.log("withinDateRange", withinDateRange);
      //   if (withinDateRange && isInSchedule) {
      //     // Weekly (recurs every 7 days) or Monthly logic
      //     const diffDays = Math.ceil((currentDate - Date_start) / (1000 * 60 * 60 * 24)); // Days difference
      //     const isWeekly = diffDays % 7 === 0; // Weekly recurrence
      //     const isMonthly = Date_start.getDate() === currentDate.getDate(); // Monthly recurrence

      //     if (isWeekly || isMonthly) {
      //       targetTd.css("background-color", "#00a65a"); // Green for available
      //       targetTd.css("border-color", "#00a65a");
            
      //       return true; // Found a match
      //     }

      //   }

      // });

      // const dateRangeBackground = this.appointmentSlot[0].appointment_schedules.some((config) => {
      //   const Date_start = new Date(config.appointed_date); // Start date
      //   const date_end = new Date(config.date_end); // End date
      //   const daysSched = config.config_schedule.days.split('|'); // Selected days e.g., ['Monday', 'Wednesday', 'Thursday']

      //   console.log("Date_start:", Date_start, "date_end:", date_end, "daysSched", daysSched);

      //   // Iterate through all days in the range
      //   let currentDate = new Date(Date_start); // Initialize with start date
      //   console.log("currentDate", config);
      //   while (currentDate <= date_end) {
      //     const currentDayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' }); // Get current day's name
          
      //     if (daysSched.includes(currentDayName)) {
      //       // Highlight specific day if it matches
      //       const targetTd = $(".fc-day[data-date='" + moment(currentDate).format("YYYY-MM-DD") + "']");
      //       targetTd.css("background-color", "#00a65a"); // Green for available
      //       targetTd.css("border-color", "#00a65a");
      //     }
          
      //     // Move to the next day
      //     currentDate.setDate(currentDate.getDate() + 1);
      //   }
      // });



        
        const dateTimeAppointed =  new Date(`${dateString}T${timeslot}`);

        if (dateTimeAppointed <= currentDateTime || isfullyBooked) {
          targetTd.css("background-color", "rgb(255 214 214)"); //disable color'
          targetTd.css("border-color", "rgb(230 193 193)");
        }else {
          targetTd.css("background-color", "#00a65a"); //available color green'
          targetdrag.css("border-color", "#00a65a");
        }
        targetGrid.remove();
        targetTd.addClass("add-cursor-pointer");
        $(".fc-content").remove();
      });
    },
    async dayClickFunction(date, allDay, jsEvent, view) {
      const eventsOnDate = this.events.filter(function (event) {
        return moment(event.start).isSame(date, "day");
      });
      if (eventsOnDate.length > 0) {
        const params = JSON.parse(JSON.stringify(eventsOnDate))[0];
        const responseBody = {
          selected_date: params.start,
          facility_id: params.facility_id,
        };
        const response = await this.__appointmentScheduleHours(responseBody);
        this.$emit("appointedTime", response.data);
      }
    },
    updateCalendarEvents() {
      this.calendar.fullCalendar("removeEvents");
      this.events.forEach((event) => {
        this.calendar.fullCalendar("renderEvent", event, true);
      });
    },
    getRandomInt(min, max) {
      min = Math.ceil(min);
      max = Math.floor(max);
      return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    async __appointmentScheduleDate(facility_id) {
      const response = await appointmentScheduleDate(facility_id);
      return response.data.facility_data.map((item) => {
        return {
          title: "Appointment",
          start: new Date(item.appointed_date),
          backgroundColor: "#00a65a",
          borderColor: "#00a65a",
          facility_id: item.facility_id,
        };
      });
    },
    async __appointmentScheduleHours(params) {
      return await appointmentScheduleHours(params);
    },
  },
};
</script>
