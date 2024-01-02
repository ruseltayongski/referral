<script>
    export default {
        name: 'AppointmentTime',
        props: {
            appointedTimes: {
                type: Object
            },

        },
        watch: {
            appointedTimes: async function (payload) {
                console.log(payload, "check")
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
                                <div id="external-events">
                                    <div class="external-event bg-green">Available Slot</div>
                                </div>
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Select Time of Appointment</h3>
                                        <div id="date-selected"></div>
                                    </div>
                                    <div class="box-body" v-if="appointedTimes.length > 0">
                                        <div id="appointment-time-list" v-for="appointment in appointedTimes" :key="appointment.id">
                                            <input type="radio" name="foo" id="hours_radio">&nbsp;&nbsp;{{ appointment.appointed_time }} to {{ appointment.appointedTime_to }} - Available Slots: {{ appointment.slot }}
                                        </div>
                                        <button type="button" id="consultation" class="btn btn-success bt-md btn-block"><i class="fa fa-stethoscope"></i>&nbsp;Consultation</button>
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
    #appointment-time-list {
        display: flex;padding: 5px;
    }
    #hours_radio {
        margin-bottom: 5px;
        transform: scale(1.5);
        cursor: pointer;
        accent-color: #00a65a; 
    }
    #consultation {
        margin-top: 20px;
    }
</style>