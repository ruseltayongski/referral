<template>
    <ul>
        <li v-for="rec in reco" :key="rec.id" @click="selectReco(rec)" :class="{ 'selected': rec.code == selected }">
            <img :src="logo" class="doh_logo" alt="">
            <div>
                <h2 v-if="rec.patient_name.length >= 27">
                    <span :class="{ 'unread-reco': !rec.reco_seen }">{{ rec.patient_name.substring(0,27)+".." }}</span>
                </h2>
                <h2 v-else>
                    <span :class="{ 'unread-reco': !rec.reco_seen }">{{ rec.patient_name }}</span>
                </h2>
                <h3 v-if="rec.message.length >= 27">
                    <span class="status green"></span>&nbsp;
                    <span :class="{ 'text-blue unread-reco': !rec.reco_seen }">{{ rec.message.substring(0,27)+".." }}</span>
                </h3>
                <h3 v-else>
                    <span class="status green"></span>&nbsp;
                    <span :class="{ 'text-blue unread-reco': !rec.reco_seen }">{{ rec.message }}</span>
                </h3>
            </div>
        </li>
    </ul>
</template>
<script>
    export default {
        name : "RecoList",
        data() {
            return {
                selected : String,
                logo : String
            }
        },
        props: ["reco"],
        watch : {

        },
        created() {
            this.logo = $("#doh_logo").val()
        },
        methods: {
            selectReco(rec) {
                this.reco.map((item) => item.reco_id === rec.reco_id ? item.reco_seen = 1 : item )

                this.selected = rec.code
                this.$emit('selectrec', rec);
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import './css/main.scss';
    .unread-reco {
        font-weight: bold;
    }
</style>