<template>
    <ul>
        <li v-for="rec in reco" :key="rec.id" @click="selectReco(rec)" :class="{ 'selected': rec.code == selected }">
            <img :src="logo" class="doh_logo" alt="">
            <div>
                <h2 v-if="rec.patient_name.length >= 27">
                    {{ rec.patient_name.substring(0,27)+".." }}
                </h2>
                <h2 v-else>
                    {{ rec.patient_name }}
                </h2>
                <h3 v-if="rec.message.length >= 27">
                    <span class="status blue"></span>
                    {{ rec.message.substring(0,27)+".." }}
                </h3>
                <h3 v-else>
                    <span class="status blue"></span>
                    {{ rec.message }}
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
        created() {
            this.logo = $("#doh_logo").val()
        },
        methods: {
            selectReco(rec) {
                this.selected = rec.code
                this.$emit('selectrec', rec);
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import './css/main.scss';
</style>