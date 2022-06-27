<template>
    <header v-if="select_rec.code">
        <img :src="logo" class="doh_logo" alt="">
        <div>
            <h2>{{ select_rec.patient_name }}</h2>
            <a :href="track_url" class="btn btn-md btn-success" target="_blank">
                <i class="fa fa-stethoscope"></i> Track
            </a>
        </div>
    </header>
</template>
<script>
    export default {
        name : "RecoHeader",
        data() {
            return {
                logo : String,
                track_button : String
            }
        },
        props : ["select_rec","track_url"],
        created() {
            this.logo = $("#doh_logo").val()
        }
    }
</script>

<style lang="scss" scoped>
    @import './css/main.scss';
</style>
