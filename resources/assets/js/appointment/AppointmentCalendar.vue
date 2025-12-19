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
      slotInfo: {},
      AppointmentDept: {}
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
    user: {
      type: Object,
    },
  },
  watch: {
    facilitySelectedId: async function (payload, old) {
       $('.loading').show();
      try {
        this.events = await this.__appointmentScheduleDate(payload);
        this.buildEventsFromSlots();
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
    this.buildEventsFromSlots();
    this.ini_events($("#external-events div.external-event"));
    this.generateCalendar();
  },
  methods: {
    getBookedSlotsForDate(date) {
      const targetDate = moment(date).format("YYYY-MM-DD");
      const bookedSlots = [];

      this.appointmentSlot.forEach(appointment => {
        if (!appointment.appointment_schedules) return;

        appointment.appointment_schedules.forEach(slot => {
          // Check date AND booking
          if (
            slot.appointed_date === targetDate &&
            slot.telemed_assigned_doctor &&
            slot.telemed_assigned_doctor.length > 0
          ) {
            bookedSlots.push(slot);
          }
        });
      });

      return bookedSlots;
    },
    getFirstAvailableDate(){
      console.log("date appointment:",this.AppointmentDept);
    },  
    buildEventsFromSlots(dateselected) {
        if (!this.appointmentSlot || this.appointmentSlot.length === 0) return;

        const slotsForDate = [];

        // Collect all slots for the selected date
        this.appointmentSlot.forEach(appointment => {
            appointment.appointment_schedules
                .filter(slot => slot.facility_id === this.facilitySelectedId)
                .forEach(slot => {
                    const assignedCount = slot.telemed_assigned_doctor?.length || 0;
                    const isFull = assignedCount >= slot.slot;
                    const inPast = moment(slot.appointed_date).isBefore(moment().startOf('day'));
                    const maxSlot = Number(slot.slot) || 0;
                    const available = maxSlot - assignedCount;

                    if (!inPast) {
                        slotsForDate.push({
                            ...slot,
                           isAvailable: available > 0,
                        });
                    }
                });
        });

        // Group slots by department AND appointment date
        const groupedByDeptAndDate = {};

        slotsForDate.forEach(slot => {
            const deptName = slot.sub_opd?.description || 'Unknown';
            const dateKey = slot.appointed_date; // group by exact date
            const key = `${deptName}_${dateKey}`;
            const Available = slot.isAvailable

            if (!groupedByDeptAndDate[key]) {
                groupedByDeptAndDate[key] = {
                    deptName: deptName,
                    date: dateKey,
                    slots: [],
                    isAvailable: Available
                };
            }

            groupedByDeptAndDate[key].slots.push({
                id: slot.id,
                appointment_date: slot.appointed_date,
                appointedTime: slot.appointed_time,
                appointedTimeTo: slot.appointedTime_to,
                createdBy: `Dr. ${slot.created_by.fname} ${slot.created_by.lname}`,
                assignedDoctors: slot.telemed_assigned_doctor || [],
                opdCategory: slot.opdCategory,
                departmentId: slot.department_id,
                slot: (Number(slot.slot) || 0) - (slot.telemed_assigned_doctor?.length || 0)
            });
        });

        // Convert to events for the calendar
      this.AppointmentDept = groupedByDeptAndDate;

      this.events = Object.entries(groupedByDeptAndDate).map(
          ([key, group]) => {
              const slotsArray = group.slots; // this is the array we want
               this.slotInfo = group;
              // Check if any slot in this department-date group has the current user assigned
              const isUserBooked = slotsArray.some(slot => 
                  slot.assignedDoctors?.some(doc => doc.doctor_id === this.user.id)
              );
              const hasAvailable = group.slots.some(s => s.isAvailable);

              // console.log("is user booked?", slotsArray);
              const start = moment(group.date, 'YYYY-MM-DD', true);
              if(!start.isValid()) return null;

              return {
                  title: group.deptName,
                  start: start.format('YYYY-MM-DD'),
                  className: ["sub-opd-label", isUserBooked ? "with-pin" : null,
                     hasAvailable ? "sub-opd-label" : "slot-full",
                  ],
              };
          }
      ).filter(Boolean);
    },
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
      let selectedAppointment = {}
      let self = this;
      this.calendar = $("#calendar").fullCalendar({
        dayRender: this.dayRenderFunction.bind(this),
        eventRender: this.eventRenderFunction.bind(this),
        dayClick: this.dayClickFunction.bind(this),
        header: self.header,
        buttonText: self.buttonText,
        events: this.events,
        editable: false,
        eventClick: async (info) => {
          const event = info.event || info;
         
          const clickedDate = moment(event.start).format("YYYY-MM-DD");
          this.buildEventsFromSlots(clickedDate);

          const deptKey = event.title + '_' + clickedDate;

          const filterredSlots =  this.AppointmentDept[deptKey] || []; 
          this.$emit("appointedTime", filterredSlots);
          },
      });
    },
    scrollToHighlightedDate(){
       this.$nextTick(() => {
          const availableCell = document.querySelector(
            ".fc-day-grid-event.sub-opd-label"
          );
          
          if (availableCell) {
            availableCell.scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
          }
        });
    },
    dayRenderFunction(date, cell) {
      const targetDate = date.format("YYYY-MM-DD");
      const bookedSlots = this.getBookedSlotsForDate(date)

      let info = null;
      if (Array.isArray(this.slotInfo)) {
        // slotInfo is an array → use find
        info = this.slotInfo.find(
          slot => slot === targetDate
        );
      } else if (this.slotInfo && typeof this.slotInfo === "object") {
        // slotInfo is an object keyed by date
        info = this.slotInfo[targetDate];
      }

      // console.log("Slot info for",info);

      if (info) {
         console.log("my sllot of available", info);
        // if (fullyBooked || inPast) {
        //   // Red background for fully booked or past dates
        //   console.log("is this fulll");
        //   cell.css("background-color", "#dd4b39");
        //   cell.find(".fc-day-number").show(); // keep the date number visible
        // } else {
        //   // Green background for available dates
        //   // cell.css("background-color", "#32b77a");
        //   // cell.addClass("add-cursor-pointer");
        // }
      }

      if (bookedSlots.length > 0) {
        // Build tooltip text
        const tooltipText = bookedSlots
          .map(slot => {
            // const assignment = slot.telemed_assigned_doctor[0]; // the booking record
            return `Booked Slot\nTime: ${slot.appointed_time}\n`;
          })
          .join("\n\n");

        // Add tooltip
        cell.attr("title", tooltipText);

        // Add a class to mark this day as booked
        $(cell).addClass('fc-daybooked');
      }
     
      var eventsOnDate = this.events.filter(function (event) {
        return moment(event.start).isSame(date, "day");
      });
      if (eventsOnDate.length > 0) {
        // cell.css("background-color", "green");
        // cell.addClass("add-cursor-pointer");
      }
    },

    eventRenderFunction(event, element) {
     
      // let currentDateTime = new Date(); // get the current date and time
      // this.$nextTick(() => {
      //   const targetDate = event.start.format("YYYY-MM-DD");
      //   const targetTd = $(
      //     ".fc-day[data-date='" + event.start.format("YYYY-MM-DD") + "']"
      //   );
      //   const targetdrag = $(
      //     ".fc-draggable[data-date='" + event.start.format("YYYY-MM-DD") + "']"
      //   );
      //   const targetGrid = $(".fc-day-grid-event");
      //   const dateString = targetTd.attr("data-date");
      //   let timeslot = null;

      //  const allSlotsForDate = [];
      //   this.appointmentSlot.forEach(appointment => {
      //     if (appointment.appointment_schedules && appointment.appointment_schedules.length > 0) {
      //       const slotsOnDate = appointment.appointment_schedules.filter(
      //         slot => slot.appointed_date === targetDate
      //       );
            
      //       if (slotsOnDate.length > 0) {
      //         allSlotsForDate.push(...slotsOnDate);
      //       }
      //     }
      //   });
      //     // console.log(`All slots for ${targetDate}:`, allSlotsForDate);

      //   if (allSlotsForDate.length === 0) {
      //     // console.log(`No slots found for date ${targetDate}`);
      //     return;
      //   }

      // const facilitySlots = allSlotsForDate.filter(slot => slot.facility_id == this.facilitySelectedId);
      // // Check each slot booking status individually
      //   const slotStatus = facilitySlots.map(slot => {
      //     const assignedCount = slot.telemed_assigned_doctor ? 
      //       slot.telemed_assigned_doctor.filter(doctor => doctor.appointment_id === slot.id).length : 0;
          
      //     const isSlotFull = assignedCount >= slot.slot;
          
      //     // console.log(`Slot ID ${slot.id}, Time: ${slot.appointed_time}, Assigned: ${assignedCount}/${slot.slot}, Full: ${isSlotFull}`);
          
      //     return {
      //       id: slot.id,
      //       time: slot.appointed_time,
      //       assigned: assignedCount,
      //       capacity: slot.slot,
      //       isFull: isSlotFull
      //     };
      //   });

      //  // Check if ALL slots are fully booked
      // const allSlotsFullyBooked = slotStatus.every(slot => slot.isFull);
      // // console.log(`All slots fully booked for ${targetDate}: ${allSlotsFullyBooked}`);
      
      // // Check if all slots for this date are in the past
      // const allSlotsInPast = facilitySlots.every(slot => {

      //   // if(!slot.appointed_time) return true; bg-red if empty 

      //   const slotDateTime = new Date(`${targetDate}T${slot.appointed_time}`);
      //   const isPast = slotDateTime <= currentDateTime;
      //   // console.log(`Slot ${slot.id} time ${slot.appointed_time} is in past: ${isPast}`);
      //   return isPast;
      // });

      // this.slotInfo[targetDate] = {
      //   fullyBooked: allSlotsFullyBooked,
      //   inPast: allSlotsInPast,
      //   slots: slotStatus
      // };

      // // console.log(`All slots in past for ${targetDate}: ${allSlotsInPast}`);
      // if (allSlotsFullyBooked || allSlotsInPast) {
      //   targetTd.css("background-color", "#dd4b39"); // not available color red
      //   // targetTd.css("border-color", "#dd4b39");
      // } else {
        
      //   targetTd.css("background-color", "rgb(50, 183, 122)"); // available color green
      //   targetdrag.css("border-color", "#00a65a");
      // }
      //   // targetGrid.remove();
      //   // $(".fc-day-grid-event").not(".sub-opd-event").remove();
      //   targetTd.find(".fc-day-grid-event").not(".fc-event-container").remove(); 

      //   targetTd.addClass("add-cursor-pointer");
      //   // $(".fc-content").remove();
      //      targetTd.find(".fc-content").remove();


      // });
      this.$nextTick(() => {
        const slotData = event.extendedProps || {};
        const inPastOrFull = slotData.inPast || slotData.isFull;
        const today = moment().startOf('day');

        const targetDate = moment(event.start).format("YYYY-MM-DD");
        const targetTd = $(".fc-day[data-date='" + targetDate + "']");

        // Determine if the event is past or fully booked
        // const inPast = slotData.inPast || moment(event.start).isBefore(today);
        // const isFull = slotData.isFull || (slotData.telemed_assigned_doctor?.length >= slotData.slot);

        // Trap: past or fully booked → red, no sub-OPD labels
        // if (inPast || isFull) {
        //   element.find('.sub-opd-label').remove();
        //   element.css({
        //     'background-color': '#dd4b39',
        //     'border-color': '#dd4b39'
        //   });
        //   // Stop further processing for unavailable slots
        //   return;
        // }
        if(inPastOrFull) {
          targetTd.css({
            'background-color': '#dd4b39',
            'border-color': '#dd4b39'
          });

          // Hide sub-OPD labels in this event
          element.find('.sub-opd-label').remove();

          return;
        }

        // --- Existing functionality for available slots ---

        // Clean old events in the cell
        // const targetDate = moment(event.start).format("YYYY-MM-DD");
        // const targetTd = $(".fc-day[data-date='" + targetDate + "']");
        const targetGrid = $(".fc-day-grid-event");

        targetTd.find(".fc-day-grid-event").not(".fc-event-container").remove();
        targetTd.addClass("add-cursor-pointer");
        targetTd.find(".fc-content").remove();

        // Render sub-OPD labels
        element.addClass('sub-opd-label');
        element.css({
          'background-color': '', // default, green comes from CSS
          'border-color': ''
        });

        // Optional: tooltip showing booked slots
        if (slotData.telemed_assigned_doctor?.length > 0) {
          const tooltipText = `Booked: ${slotData.telemed_assigned_doctor.length}/${slotData.slot || 'N/A'}`;
          element.attr('title', tooltipText);
        }

        // Optional: store slot status for other parts of your calendar
        const allSlotsForDate = [];
        this.appointmentSlot.forEach(appointment => {
          if (appointment.appointment_schedules && appointment.appointment_schedules.length > 0) {
            const slotsOnDate = appointment.appointment_schedules.filter(
              slot => slot.appointed_date === targetDate
            );
            if (slotsOnDate.length > 0) allSlotsForDate.push(...slotsOnDate);
          }
        });

        if (allSlotsForDate.length > 0) {
          const facilitySlots = allSlotsForDate.filter(slot => slot.facility_id == this.facilitySelectedId);
          const slotStatus = facilitySlots.map(slot => {
            const assignedCount = slot.telemed_assigned_doctor?.length || 0;
            const isSlotFull = assignedCount >= slot.slot;
            return {
              id: slot.id,
              time: slot.appointed_time,
              assigned: assignedCount,
              capacity: slot.slot,
              isFull: isSlotFull
            };
          });

          const allSlotsFullyBooked = slotStatus.every(slot => slot.isFull);
          const allSlotsInPast = facilitySlots.every(slot => {
            const slotDateTime = new Date(`${targetDate}T${slot.appointed_time}`);
            return slotDateTime <= new Date();
          });

          this.slotInfo[targetDate] = {
            fullyBooked: allSlotsFullyBooked,
            inPast: allSlotsInPast,
            slots: slotStatus
          };
        }
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

        const response = this.__appointmentScheduleHours(responseBody);
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
.sub-opd-label {
  cursor: pointer !important;
}

.sub-opd-label:hover::after {
  position: absolute;
  background: #333;
  color: #fff;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  transform: translateY(-110%);
  z-index: 9999;
}

.page-header {
  margin: 10px 0 0 0;
  font-size: 22px;
}

/* ======== CALENDAR BORDERS ======== */
/* Force borders on all calendar cells */
/* .fc-day,
.fc-day-top {
  border: 1px solid #ddd !important;
} */

/* Ensure table borders are visible */
/* .fc-view-container .fc-view table {
  border-collapse: separate !important;
  border-spacing: 0 !important;
} */

.fc-view-container .fc-view td,
.fc-view-container .fc-view th {
  border: 1px solid #ddd !important;
}

/* Keep borders visible even with background colors */
.fc-day[style*="background-color"] {
  border: 1px solid #ddd !important;
  box-sizing: border-box;
}

/* ======== SELECTED DATE INDICATOR ======== */
.fc-day.selected-date-indicator {
  background-color: rgb(0, 166, 90) !important;
  box-shadow: inset 0 0 0 2px #007bff52, 0 2px 6px rgba(0, 123, 255, 0.048);
  outline-offset: -3px;
  box-sizing: border-box;
  position: relative;
  transform: scale(1.00);
  border: 1px solid #ddd !important; /* Ensure border remains */
}

.fc-day.selected-date-indicator:hover {
  cursor: pointer;
  transform: scale(1.00);
  transition: transform 0.2s ease;
}

/* ======== SUB-OPD LABELS ======== */
.fc-day {
  position: relative !important;
  overflow: visible !important;
}

.sub-opd-container {
  display: flex !important;
  flex-direction: column !important;
  margin-top: 35px !important;
  padding: 0 4px !important;
  gap: 3px !important;
  position: relative !important;
  z-index: 1000 !important;
  visibility: visible !important;
  pointer-events: auto !important;
  overflow: visible;
}

.sub-opd-label {
  background-color: rgb(0, 166, 90) !important;
  color: white !important;
  padding: 4px 6px !important;
  border-radius: 3px !important;
  text-align: center !important;
  font-weight: 600 !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
  white-space: nowrap !important;
  font-size: 10px !important;
  line-height: 1.3 !important;
  display: block !important;
  min-height: 18px !important;
  opacity: 1 !important;
  visibility: visible !important;
}

.fc-day .sub-opd-container,
.fc-day .sub-opd-label {
  max-height: none !important;
  height: auto !important;
}

.fc-day.add-cursor-pointer .sub-opd-container {
  position: relative;
  z-index: 2;
}

/* ======== CALENDAR EVENT BORDERS ======== */
.calendar-event {
  border-right: 1px solid #ddd;
  border-left: 1px solid #ddd;
}

/* Make sure booked days also show borders */
.fc-daybooked {
  border: 1px solid #ddd !important;
}

.fc-event {
  border: none !important;
}

.with-pin .fc-title:after {
  content: "📌";
  float: right;
  margin-left: 6px;

  background: #ffffff;
  padding: 2px 2px;
  border-radius: 6px;
  font-size: 1em;
  border: 1px solid #ddd;
}
</style>