import req from "./req";

const base = $("#broadcasting_url").val();

export function appointmentScheduleDate(facility_id) {
    return req.get(base+"/appointment/getFacility/"+facility_id);
}

export function appointmentScheduleHours(params) {
    return req.post(base+"/appointment/available-time-slots", params);
}

export function appointmentConfigHours(params) {
    return req.post(base+"/appointment/config-time-Slot", params);
}

export function appointmentConfigData(params){
     console.log("result for", base+"/doctor/getconfigappointment", params);
    return req.post(base+"/doctor/getconfigappointment", params);

    // try {
    //     const response =  req.post(base+"/doctor/getconfigappointment", params);
    //     console.log("get configappointment AppointmentApp11111", response);

    //   } catch (error) {
    //     console.error("Error sending appointment data:", error);
    //   }
}