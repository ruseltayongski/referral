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
                                    <div class="box-body">
                                        <div id="appointment-time-list">
                                        </div>
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
<script>
    export default {
        name: 'AppointmentTime'
    }
</script>