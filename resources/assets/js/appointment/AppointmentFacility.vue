<template>
    <div class="col-md-4 scroll-item">
        <div :class="{ 'highlighted': appointment.id == facilitySelectedId }" class="box box-widget widget-user with-badge">
            <div class="widget-user-header">
                <h3 class="widget-user-username">
                    {{ appointment.name }}
                </h3>
                <h5 class="widget-user-desc">
                    {{ appointment.address }}
                </h5>
            </div>
            <div class="widget-user-image">
                <img :src="doh_logo" class="img-circle" alt="User Avatar"/>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header">
                               {{ balanceSlotThisMonth }}
                            </h5>
                            <span class="description-text">Total Slot</span>
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
                            <button class="btn btn-block btn-success btn-select" id="selected_data" name="selected_data" @click="facilitySelected(appointment.id)"> Select</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'AppointmentFacility',
        data() {
            return {
                doh_logo: $("#broadcasting_url").val()+"/resources/img/video/doh-logo.png",
            };
        },
        props: {
            appointment: {
                type: Object, 
            },
            facilitySelectedId: {
                type: Number
            }
        },
        computed: {
            emptyAppointmentByCount() {
                let count = 0;
                const now = new Date();

                if(this.appointment && this.appointment.appointment_schedules){
                    this.appointment.appointment_schedules.forEach(sched => {
                        // Combine appointed_date and appointed_time into a single Date object
                        const appointedDatetime = new Date(`${sched.appointed_date}T${sched.appointed_time}`);
                        // Check if the schedule is in the future
                        if(appointedDatetime > now){
                            sched.telemed_assigned_doctor.forEach(doctor =>{
                                if(!doctor.appointment_by){
                                    count++;
                                }
                            });
                        }
                    });
                }
                return count;
            },
            balanceSlotThisMonth() {
                let usedCount = 0;
                let expiredCount = 0;
                const now = new Date();
                const currentyear = now.getFullYear();
                const currentmonth = now.getMonth();

                if(this.appointment && this.appointment.appointment_schedules){
                    this.appointment.appointment_schedules.forEach(sched => {
                        const appointedDate = new Date(sched.appointed_date);
                        const appointedYear = appointedDate.getFullYear();
                        const appointedMonth = appointedDate.getMonth();
                       
                        if(appointedYear === currentyear && appointedMonth === currentmonth){
                            const appointedDatetime = new Date(`${sched.appointed_date}T${sched.appointed_time}`);

                            if(appointedDatetime > now){
                               sched.telemed_assigned_doctor.forEach(doctor => {
                                    if(doctor.appointment_by){
                                        usedCount++;
                                    }
                               });
                               
                            }else{
                               expiredCount++;
                            }
                        }
                    });
                }
                  const totalcount = usedCount + expiredCount;
                return totalcount;
                
            },
        },
        watch: {
            facilitySelectedId: function(value) {
                
            }
        },
        methods: {
            facilitySelected(id) {
                if(this.facilitySelectedId !== id) {
                    //remove element
                    $(".fc-day").css("background-color","")
                    $(".fc-day").removeClass("add-cursor-pointer")
                    //
                }
                this.$emit('facilitySelected', id)
            }
        }
    }
</script>
