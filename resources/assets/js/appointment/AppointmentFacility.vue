<template>
    <div class="col-md-4 scroll-item">
        <div :class="{ 'highlighted': appointment.facility.id == isSelectedId }" class="box box-widget widget-user with-badge">
            <div class="widget-user-header">
                <h3 class="widget-user-username">
                    {{ appointment.facility.name }} {{ appointment.facility.id }}
                </h3>
                <h5 class="widget-user-desc">
                    {{ appointment.facility.address }}
                </h5>
            </div>
            <div class="widget-user-image">
                <img :src="doh_logo" class="img-circle" alt="User Avatar"/>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header">{{ appointment.slot }}</h5>
                            <span class="description-text">Slot</span>
                        </div>
                    </div>
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header">13,000</h5>
                            <span class="description-text">Available</span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="description-block">
                            <button class="btn btn-block btn-success btn-select" id="selected_data" name="selected_data" @click="facilitySelected(appointment.facility.id)"> Select</button>
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
            isSelectedId: {
                type: Number
            }
        },
        watch: {
            isSelectedId: function(value) {
                
            }
        },
        methods: {
            facilitySelected(id) {
                //remove element
                $(".fc-day").css("background-color","")
                $(".fc-day").removeClass("add-cursor-pointer")
                //
                this.$emit('isSelected', id)
            }
        }
    }
</script>
