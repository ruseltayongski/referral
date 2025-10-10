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
import { appointmentScheduleDate, appointmentScheduleHours, appointmentConfigHours } from "./api/index";
export default {
  name: "AppointmentCalendar",
  data() {
    return {
      calendar: null,
      previouslyClickedDay: null,
      dateSelected: null,
      appointedParams:{} ,
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
       $('.loading').show();
      try {
        this.events = await this.__appointmentScheduleDate(payload);
        this.updateCalendarEvents();

        this.$nextTick(() => {
          this.scrollToHighlightedDate();
        });
       } catch (error) {
        console.error("Error fetching appointment schedule:", error);
      } finally {
        $('.loading').hide(); // always hide loader (success or error)
      }
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
    scrollToHighlightedDate(){
      // const highlightedCell = document.querySelector(
      //   ".fc-day[style*='background-color: rgb(221, 75, 57)'], " + // red slots
      //   ".fc-day[style*='background-color: rgb(0, 166, 90)']"      // green slots
      // );

      const greenSlot = document.querySelector(
        ".fc-day[style*='background-color: rgb(50, 183, 122)']" 
      );

      const redSlot = document.querySelector(
        ".fc-day[style*='background-color: rgb(221, 75, 57)']" 
      );

      const targetCell = greenSlot || redSlot;

      if(targetCell) {
        targetCell.scrollIntoView({
          behavior: "smooth",
          block: "center",
          inline: "nearest"
        });
      }
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
     
      let currentDateTime = new Date(); // get the current date and time
      this.$nextTick(() => {
          const targetDate = event.start.format("YYYY-MM-DD");
        const targetTd = $(
          ".fc-day[data-date='" + event.start.format("YYYY-MM-DD") + "']"
        );
        const targetdrag = $(
          ".fc-draggable[data-date='" + event.start.format("YYYY-MM-DD") + "']"
        );
        const targetGrid = $(".fc-day-grid-event");
        const dateString = targetTd.attr("data-date");
        let timeslot = null;

       const allSlotsForDate = [];
        this.appointmentSlot.forEach(appointment => {
          if (appointment.appointment_schedules && appointment.appointment_schedules.length > 0) {
            const slotsOnDate = appointment.appointment_schedules.filter(
              slot => slot.appointed_date === targetDate
            );
            
            if (slotsOnDate.length > 0) {
              allSlotsForDate.push(...slotsOnDate);
            }
          }
        });
          // console.log(`All slots for ${targetDate}:`, allSlotsForDate);

        if (allSlotsForDate.length === 0) {
          // console.log(`No slots found for date ${targetDate}`);
          return;
        }

      const facilitySlots = allSlotsForDate.filter(slot => slot.facility_id == this.facilitySelectedId);
      // Check each slot booking status individually
        const slotStatus = facilitySlots.map(slot => {
          const assignedCount = slot.telemed_assigned_doctor ? 
            slot.telemed_assigned_doctor.filter(doctor => doctor.appointment_id === slot.id).length : 0;
          
          const isSlotFull = assignedCount >= slot.slot;
          
          // console.log(`Slot ID ${slot.id}, Time: ${slot.appointed_time}, Assigned: ${assignedCount}/${slot.slot}, Full: ${isSlotFull}`);
          
          return {
            id: slot.id,
            time: slot.appointed_time,
            assigned: assignedCount,
            capacity: slot.slot,
            isFull: isSlotFull
          };
        });

       // Check if ALL slots are fully booked
      const allSlotsFullyBooked = slotStatus.every(slot => slot.isFull);
      // console.log(`All slots fully booked for ${targetDate}: ${allSlotsFullyBooked}`);
      
      // Check if all slots for this date are in the past
      const allSlotsInPast = facilitySlots.every(slot => {

        // if(!slot.appointed_time) return true; bg-red if empty 

        const slotDateTime = new Date(`${targetDate}T${slot.appointed_time}`);
        const isPast = slotDateTime <= currentDateTime;
        // console.log(`Slot ${slot.id} time ${slot.appointed_time} is in past: ${isPast}`);
        return isPast;
      });

      // console.log(`All slots in past for ${targetDate}: ${allSlotsInPast}`);
      if (allSlotsFullyBooked || allSlotsInPast) {
        targetTd.css("background-color", "#dd4b39"); // not available color red
        // targetTd.css("border-color", "#dd4b39");
      } else {
        
        targetTd.css("background-color", "rgb(50, 183, 122)"); // available color green
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
      //Config Appointment
      let AppointedDates = [];
      let configId = null;
      let apointmentId = null;
      let ScheduleIds = [];

      const clickedDate = moment(date._d).format("YYYY-MM-DD");
      const clickedDay = document.querySelector(`.fc-day[data-date='${clickedDate}']`);

      const hasSlot = this.appointmentSlot.some(appointment =>
        appointment.appointment_schedules &&
        appointment.appointment_schedules.some(sched => sched.appointed_date === clickedDate)
      );

      if(!hasSlot){
        return;
      }
       // Check if it's a green slot (available date)
      const isGreenSlot =
        clickedDay &&
        window.getComputedStyle(clickedDay).backgroundColor === "rgb(50, 183, 122)";

      // Remove previously selected highlight (keep others intact)
      if (this.previouslyClickedDay && this.previouslyClickedDay !== clickedDay) {
        this.previouslyClickedDay.classList.remove("selected-date-indicator");
      }

      // If available slot, mark it as selected
      if (isGreenSlot) {
        clickedDay.classList.add("selected-date-indicator");
        this.previouslyClickedDay = clickedDay;
      }

      const clickedDayName = new Date(clickedDate).toLocaleDateString('en-US', { weekday: 'long' });
      this.appointmentSlot.forEach((appointment) => {
        appointment.appointment_schedules.forEach((sched) => {
          // Check if schedule matches the facility and has a valid configId
          if (this.facilitySelectedId === sched.facility_id && sched.configId) {
            const startDate = new Date(sched.appointed_date);
            const endDate = new Date(sched.date_end);
            const daysSched = sched.config_schedule.days.split('|');

            // Check if clicked date is within range and matches the schedule's day
            if (new Date(clickedDate) >= startDate && new Date(clickedDate) <= endDate && daysSched.includes(clickedDayName)) {
              ScheduleIds.push(sched.id);

              // Process the first matched schedule
              if (sched.id === ScheduleIds[0]) {
                configId = sched.configId;
                apointmentId = sched.id;

                // Iterate through all dates in the range to build AppointedDates
                let currentDate = new Date(startDate);
                while (currentDate <= endDate) {
                  const currentDayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' });
                  if (daysSched.includes(currentDayName)) {
                    AppointedDates.push(moment(currentDate).format("YYYY-MM-DD"));
                  }
                  currentDate.setDate(currentDate.getDate() + 1); // Move to the next day
                }
              }
            }
          }
        });
      });

      let dateselect = date._d.toISOString().split('T')[0];
      let PassconfigId = null;
      let parameterDate = null;
     
      let params = JSON.parse(JSON.stringify(eventsOnDate))[0];

      let isManualAppointment = eventsOnDate.length > 0;
      if (isManualAppointment) {  //Manual Appointment
        const responseBody = {
          selected_date: params.start,
          facility_id: params.facility_id,
        };

        const response = await this.__appointmentScheduleHours(responseBody);
        this.$emit("appointedTime", response.data);
        PassconfigId = null;
        
        if(params.start === dateselect){
          parameterDate = params.start;
          this.$emit("manual-click-date", parameterDate);
        }
      }
      
      //Config Appointment
        const appointedData = await this.__appointmentScheduleDate(
          null,
          date._d,
          AppointedDates,
          configId,
          apointmentId,
        );

        if (appointedData) {

          this.appointedParams = appointedData; // Update state if needed elsewhere
          // console.log("appointedData config params", appointedData);

          const responseBody1 = {
            selected_date:  appointedData.start && !isNaN(new Date(appointedData.start)) ? new Date(appointedData.start).toISOString().split('T')[0] : '',
            facility_id: appointedData.facility_id,
            configId: appointedData.configId,
            appointedId:appointedData.appointedId,
          };

          const response1 = await this.__appointmentConfigHours(responseBody1);
          this.$emit("config_appointedTime", response1.data);
          const configsched = Object.values(response1.data)[0];

          if(AppointedDates.includes(dateselect)){
            // console.log("list of matched date:", AppointedDates);
             PassconfigId = configsched.configId;
          }
            //console.log("AppointedDates::", AppointedDates, 'dateselect',dateselect);
        }else{
            PassconfigId = null;
            // console.log("not matched", parameterDate);
        }
        
       this.$emit("day-click-date", PassconfigId);
       if (parameterDate) {
          this.$emit("manual-click-date", parameterDate);
        } else {
          this.$emit("manual-click-date", null);
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
    async __appointmentScheduleDate(facility_id, clickdate, appointed, configId,appointedId) {
      const response = await appointmentScheduleDate(facility_id); 
      
      let formattedClickDate = "";
      if(clickdate instanceof Date && !isNaN(clickdate)){
         formattedClickDate = clickdate.toISOString().split('T')[0]; 
      }else{
        //console.warn("Invalid clickdate:", clickdate);
      }

      if(!Array.isArray(appointed)){
          appointed = [];
      }
      const matchedate = appointed.includes(formattedClickDate) ? formattedClickDate: "";

      if(matchedate && configId){

        const appointedParam =  {
          title: "Appointment",
          start: new Date(matchedate),
          configId: configId,
          appointedId: appointedId,
          backgroundColor: "#00a65a",
          borderColor: "#00a65a",
          facility_id: this.facilitySelectedId,
        };

        this.appointedParams = appointedParam;
        return appointedParam;

      } else {
        const mapedData = response.data.facility_data.map((item) => {
          return {
            title: "Appointment",
            configId: null,
            start: item.appointed_date,
            backgroundColor: "#00a65a",
            borderColor: "#00a65a",
            facility_id: item.facility_id,
          };
        }); 
        
        return mapedData;
      }
    },
    async __appointmentScheduleHours(params) {
      return await appointmentScheduleHours(params);
    },
    async __appointmentConfigHours(params) {
      return await appointmentConfigHours(params);
    },
  },
};
</script>

<style>
/* .selected-green-slot{
 box-shadow: inset 0 0 0 3px #007bff;
  border-radius: 4px;
} */

/* Highlight the selected date professionally */
.fc-day.selected-date-indicator {
  background-color: rgb(0, 166, 90) !important; 
  box-shadow: inset 0 0 0 2px #007bff52, 0 2px 6px rgba(0, 123, 255, 0.048);
  position: relative;
  transition: all 0.3s ease;
  transform: scale(1.02);
}

/* Add a small checkmark for visual confirmation */
.fc-day.selected-date-indicator::after {
    content: "✔";
    position: absolute;
    top: 4px;
    right: 6px;
    font-size: 14px;
    background-color: white;
    border-radius: 50%;
    padding: 2px 4px;
    box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
}

/* Smooth hover animation for better UX */
.fc-day.selected-date-indicator:hover {
  cursor: pointer;
  transform: scale(1.02);
  transition: transform 0.2s ease;
}
.page-header {
  margin: 10px 0 0 0;
  font-size: 22px;
}
</style>
