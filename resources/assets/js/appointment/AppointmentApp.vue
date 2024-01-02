<template>
    <div class="row">
        <div class="col-md-12">
            <div class="jim-content">
                <h3 class="page-header">Select Facility</h3>
                <div class="row">
                    <div class="scroll-container">
                        <appointment-facility v-if="appointment_sched" v-for="appointment in appointment_sched" :key="appointment.id" :isSelectedId="isSelectedId" :appointment="appointment" @isSelected="isSelected"></appointment-facility>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <appointment-calendar :isSelectedId="isSelectedId" @appointedTime="appointedTime"></appointment-calendar>
                <appointment-time :appointedTimes="appointedTimes"></appointment-time>
            </div>
        </div> 
    </div>
</template>
<script>
    import AppointmentFacility from './AppointmentFacility.vue'
    import AppointmentCalendar from './AppointmentCalendar.vue'
    import AppointmentTime from './AppointmentTime.vue'
    export default {
        name: 'AppointmentApp',
        components: {
            AppointmentFacility,
            AppointmentCalendar,
            AppointmentTime
        },
        props: [
            "user",
            "appointment_sched"
        ],
        data() {
            return {
                isSelectedId : 0,
                appointedTimes: []
            }
        },
        mounted() {

        },
        methods: {
            isSelected(payload) {
                this.isSelectedId = payload
            },
            appointedTime(payload) {
                this.appointedTimes = payload
                //console.log(this.)
            }
        }
    }
</script>
<style>
    @import './css/index.css';
</style>    