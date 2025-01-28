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
import { appointmentScheduleDate, appointmentScheduleHours, appointmentConfigHours } from "./api/index";
export default {
  name: "AppointmentCalendar",
  data() {
    return {
      calendar: null,
      selectedDate:null,
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
        let passconfigId = null;
        
        this.appointmentSlot.some((appointment) => {
         
          appointment.appointment_schedules.forEach((sched) => {
            if(sched.configId && this.facilitySelectedId === sched.facility_id) {
              // console.log("my sched list::", sched.telemed_assigned_doctor);
              
              const Date_start = new Date(sched.appointed_date); // Start date
              const date_end = new Date(sched.date_end); // End date
              const timeSlot = sched.config_schedule.time.split('|');
              const daysSched = sched.config_schedule.days.split('|');
                //console.log("daysSched", daysSched, "timeSlot", timeSlot);
                // Iterate through all days in the range
                let currentDate = new Date(Date_start); // Initialize with start date
              //  console.log("timeSlot", timeSlot);
                while (currentDate <= date_end) {
                  const currentDayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' }); // Get current day's name
                  
                  if (daysSched.includes(currentDayName) && !passconfigId) {
                    
                    // Highlight specific day if it matches
                    const targetTd = $(".fc-day[data-date='" + moment(currentDate).format("YYYY-MM-DD") + "']");


                     if(targetTd.length){
                        targetTd.css("background-color", "#00a65a"); // Green for available
                    }

                    const list_Appointed_date = targetTd.data("date");
                    
                    let selectedDates = Array.isArray(list_Appointed_date)
                      ? list_Appointed_date.filter((date) => date !== undefined)
                      : [list_Appointed_date].filter((date) => date !== undefined);

                    selectedDates = [...new Set(selectedDates.filter((date) => date))];
                   // Log only non-empty arrays
                    if (selectedDates.length > 0) {

                      console.log("Filtered selectedDates:", selectedDates);
                      selectedDates.forEach((date) => {
                          const dbEntriesForDate = sched.telemed_assigned_doctor.filter((entry) => entry.appointed_date === date);
                          
                          const allSlotsSaved = timeSlot.every((slot) => {
                              const [start, end] = slot.split("-");
                              console.log("Checking slot:", slot, "Start:", start, "End:", end);
                              return dbEntriesForDate.some(
                                (entry) => entry.start_time === `${start}:00` && entry.end_time === `${end}:00`
                              );
                          });

                          const targetTd = $(".fc-day[data-date='" + date + "']");
                          console.log("allSlotsSaved:", allSlotsSaved);
                          if(allSlotsSaved){
                            targetTd.css("background-color", "rgb(255 214 214)"); // Gray for disabled
                            // console.log(`Date ${date} is fully booked and disabled.`);
                          }else{
                            targetTd.css("background-color", "#00a65a") 
                            console.log(`Date ${date} is partially available.`);
                          }
                      });

                    } else {
                      // console.log("No valid dates found.");
                    }
                    
                  } else {
                    const targetTd = $(".fc-day[data-date='" + moment(currentDate).format("YYYY-MM-DD") + "']");
                    if (targetTd.length) {
                      targetTd.css("background-color", ""); // Remove background color
                      targetTd.removeClass("add-cursor-pointer");
                      
                      $(".fc-event-container").remove();
                      $(".fc-title").remove();
                      $(".fc-resizer").remove();
                    }
                  }
                  // Move to the next day
                  currentDate.setDate(currentDate.getDate() + 1);
                }
            }
            
          });
        });

        // Manual Appointment
        const isfullyBooked = this.appointmentSlot.some((appointment) => {
          if (appointment.appointment_schedules.length > 0) {
            const slotOndate = appointment.appointment_schedules.filter(
              (slot) => slot.appointed_date === dateString
            );
        
              // Group by appointment_id
              const groupedByAppointmentId = slotOndate.reduce((acc, slot) => {
                 
                passconfigId = slot.configId;
                if(!slot.configId){
                  timeslot = slot.appointed_time ;
                  const id = slot.appointment_id;
                  if (!acc[id]) acc[id] = [];
                  acc[id].push(
                    slot.telemed_assigned_doctor.map(
                      (doctor) => doctor.appointment_by
                    )
                  );

                }
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
         
        let dateTimeAppointed =  new Date(`${dateString}T${timeslot}`);

        if(!passconfigId) {
          // if (dateTimeAppointed <= currentDateTime || isfullyBooked) {
          if (dateTimeAppointed <= currentDateTime) {
            targetTd.css("background-color", "rgb(255 214 214)"); //not available color red
            targetTd.css("border-color", "rgb(230 193 193)");
          } else {
            targetTd.css("background-color", "#00a65a"); //available color green'
            targetdrag.css("border-color", "#00a65a");
          }
          
            targetGrid.remove();
            targetTd.addClass("add-cursor-pointer");
            $(".fc-content").remove();
        }

      });
    },
    async dayClickFunction(date, allDay, jsEvent, view) {
      //console.log("appointment:: ", this.appointmentSlot[0].appointment_schedules);
      const eventsOnDate = this.events.filter(function (event) {
        return moment(event.start).isSame(date, "day");
      });
      //Config Appointment
      let AppointedDates = [];
      let configId = null;
      let apointmentId = null;
      let ScheduleIds = [];

      const clickedDate = moment(date._d).format("YYYY-MM-DD"); // Format clicked date
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
        //console.log("manual appoint response", response.data);
        PassconfigId = null;
        //console.log("PassconfigId::", PassconfigId);
        //console.log("date selected condition ",  params.start, dateselect, "configId:", PassconfigId);
        
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
            console.log("list of matched date:", AppointedDates);
             PassconfigId = configsched.configId;
          }
            //console.log("AppointedDates::", AppointedDates, 'dateselect',dateselect);
        }else{
            PassconfigId = null;
            console.log("not matched", parameterDate);
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
          //console.log("response mapedData", mapedData);
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
      //console.log("parama:", params);
      return await appointmentScheduleHours(params);
    },
    async __appointmentConfigHours(params) {
      //console.log("parama config hrs:", params);
      return await appointmentConfigHours(params);
    },
  },
};
</script>
