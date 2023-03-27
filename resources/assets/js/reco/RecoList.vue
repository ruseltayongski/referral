<template>
    <ul>
        <li v-for="rec in reco" :key="rec.reco_id" @click="selectReco(rec)" :class="{ 'selected': rec.code == selected }">
            <img :src="logo" class="doh_logo" alt="">
            <div>
                <h2>
                    <span :class="{ 'unread-reco': !rec.reco_seen && rec.userid_sender !== user.id }">{{ rec.patient_name }}</span>
                </h2>
                <span class="status green"></span>&nbsp;
                <span :class="{ 'text-blue unread-reco': !rec.reco_seen && rec.userid_sender !== user.id }">{{ rec.message }}</span>
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
                logo : String,
                messagesInfo: []
            }
        },
        props: ["reco","user"],
        mounted() {
            this.logo = $("#doh_logo").val()
            console.log(this.messagesInfo)
        },
        methods: {
            selectReco(rec) {
                try{
                    this.reco.map((item) => item.reco_id === rec.reco_id ? item.reco_seen = 1 : item )
                    this.selected = rec.code
                    this.$emit('selectrec', rec)
                } catch(error){
                    console.error(error)
                    alert("Something went wrong! will restart this page")
                    window.location.reload()
                }
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