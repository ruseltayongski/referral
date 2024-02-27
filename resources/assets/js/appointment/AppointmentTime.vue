<script>
    export default {
        name: 'AppointmentTime',
        props: {
            appointedTimes: {
                type: Object
            },
            facilitySelectedId: {
                type: Number
            }
        },
        data() {
            return {
                selectedAppointmentTime: null,
                selectedAppointmentDoctor: null,
                showAppointmentTime: false,
                base: $("#broadcasting_url").val(),
                followUpReferredId: 0,
                followUpCode: null
            }
        },
        mounted() {
            const telemedicineFollowUp = JSON.parse(decodeURIComponent(new URL(window.location.href).searchParams.get('appointment')));
            this.followUpReferredId = telemedicineFollowUp[0].referred_id;
            this.followUpCode = telemedicineFollowUp[0].code;
        },
        watch: {
            appointedTimes: async function (payload) {
                this.showAppointmentTime = true;
                this.selectedAppointmentTime = null;
                this.selectedAppointmentDoctor = null;
            },
            facilitySelectedId: async function (newValue, oldValue) {
                this.showAppointmentTime = false;
            }
        },
        methods: {
            areAllDoctorsNotAvailable(doctors) {
                return doctors.every(doctor => doctor.appointment_by);
            },
            proceedAppointment() {
                if(!this.selectedAppointmentTime) {
                    Lobibox.alert("error",
                    {
                        msg: "Please Select Time"
                    });
                    return;
                }
                else if(!this.selectedAppointmentDoctor) {
                    Lobibox.alert("error",
                    {
                        msg: "Please Select Doctor"
                    });
                    return;
                }
                
                if(this.followUpReferredId) {
                    $("#telemed_follow_code").val(this.followUpCode);
                    $("#telemedicine_follow_id").val(this.followUpReferredId);
                    $(".telemedicine").val(1);
                    $("#followup_header").html("Follow Up Patient");
                    $("#telemedicineFollowupFormModal").modal('show');
                    $("#followup_facility_id").val(this.facilitySelectedId);
                        //immediately close the form modal after submission
                }
                else {
                    const appointment = {
                        facility_id: this.facilitySelectedId,
                        appointmentId: this.selectedAppointmentTime,
                        doctorId: this.selectedAppointmentDoctor
                    }
                    console.log(facility_id);
                    window.location.href = `${this.base}/doctor/patient?appointmentKey=${this.generateAppointmentKey(255)}&appointment=${encodeURIComponent(JSON.stringify([appointment]))}`;
                }
            },
            generateAppointmentKey(length) {
                const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                let key = '';

                for (let i = 0; i < length; i++) {
                    const randomIndex = Math.floor(Math.random() * characters.length);
                    key += characters.charAt(randomIndex);
                }

                return key;
            },
            handleAppointmentTimeChange() {
                this.selectedAppointmentDoctor = null
            },
            handleDoctorChange(doctorId) {
                console.log(doctorId)
                this.selectedAppointmentDoctor = doctorId
            }
        }
    }
</script>
<template>
    <div class="col-md-3">
        <div class="jim-content">
            <h3 class="page-header">Time Slot</h3>
            <div class="calendar-container">
                <section class="content">
                    <div class="row">
                        <div class="box box-primary">
                            <div class="box-body no-padding">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Legends</h4>
                                </div>
                                <div>
                                    <div class="external-event bg-green">Available Slot</div>
                                </div>
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Select Time and Doctor</h3>
                                        <div id="date-selected"></div>
                                    </div>
                                    <div class="box-body" v-if="appointedTimes.length > 0 && showAppointmentTime">
                                        <div class="appointment-time-list" v-for="appointment in appointedTimes" :key="appointment.id">
                                            <input 
                                                type="radio" 
                                                class="hours_radio" 
                                                v-model="selectedAppointmentTime" 
                                                :value="appointment.id" 
                                                @change="handleAppointmentTimeChange"
                                                :disabled="areAllDoctorsNotAvailable(appointment.telemed_assigned_doctor)"
                                            >&nbsp;&nbsp;
                                            <span :class="{ 'text-green' : !areAllDoctorsNotAvailable(appointment.telemed_assigned_doctor),'text-red' : areAllDoctorsNotAvailable(appointment.telemed_assigned_doctor) }">{{ appointment.appointed_time }} to {{ appointment.appointedTime_to }}</span>
                                            <ul v-if="appointment.id == selectedAppointmentTime" class="doctor-list" v-for="assignedDoctor in appointment.telemed_assigned_doctor" :key="assignedDoctor.id">
                                                <li>
                                                    <input 
                                                        type="radio" 
                                                        class="hours_radio" 
                                                        v-model="selectedAppointmentDoctor" 
                                                        :value="assignedDoctor.doctor.id" 
                                                        @change="handleDoctorChange(assignedDoctor.doctor.id)"
                                                        :disabled="assignedDoctor.appointment_by"
                                                    >&nbsp;&nbsp;
                                                    <small :class="{ 'text-green' : !assignedDoctor.appointment_by,'text-red' : assignedDoctor.appointment_by }">
                                                        {{ `Dr. ${assignedDoctor.doctor.fname} ${assignedDoctor.doctor.lname}` }}
                                                    </small>
                                                </li>
                                            </ul>
                                        </div>
                                        <button type="button" id="consultation" class="btn btn-success bt-md btn-block" @click="proceedAppointment"><i class="fa fa-calendar"></i>&nbsp;Appointment</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div> 
        </div>
    </div>
</template>
<style scoped>
    .appointment-time-list {
        /* display: flex;  */
        padding: 10px;
    }
    .appointment-time-list > .doctor-list {
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
</style>