<template>
    <ul>
        <li v-for="rec in reco" :key="rec.id" @click="selectReco(rec)" :class="{ 'selected': rec.code == selected }">
            <img :src="logo" class="doh_logo" alt="">
            <div>
                <h2>{{ rec.patient_name }}</h2>
                <h3>
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

<style scoped>
    *{
        box-sizing:border-box;
    }
    body {
        background-color:#abd9e9;
        font-family: Arial;
    }
    #container{
        background:#eff3f7;
        margin:0 auto;
        font-size:0;
        border-radius:5px;
        overflow:hidden;
    }
    aside{
        width:100%;
        height:800px;
        /*background-color:#3b3e49;*/
        background-color: #dddddd;
        display:inline-block;
        font-size:15px;
        vertical-align:top;
    }
    main{
        width:100%;
        height:800px;
        display:inline-block;
        font-size:15px;
        vertical-align:top;
    }

    aside header{
        padding:30px 20px;
    }
    aside input{
        width:100%;
        height:50px;
        line-height:50px;
        padding:0 100px 0 20px;
        background-color: #b6c5d6;
        border:none;
        border-radius:3px;
        color: #3e3e3e;
        background-image:url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/ico_search.png);
        background-repeat:no-repeat;
        background-position:250px;
        background-size:40px;
    }
    aside input::placeholder{
        color: #444444;
    }
    aside ul{
        padding-left:0;
        margin:0;
        list-style-type:none;
        overflow-y:scroll;
        height:690px;
    }
    aside ul{
        padding-left:0;
        margin:0;
        list-style-type:none;
        overflow-y:scroll;
        height:690px;
    }
    aside li{
        padding:10px 0;
    }
    aside li:hover{
        background-color:#5e616a;
    }
    h2,h3{
        margin:0;
    }
    aside li img{
        border-radius:50%;
        margin-left:20px;
        margin-right:8px;
    }
    aside li div{
        display:inline-block;
        vertical-align:top;
        margin-top:12px;
    }
    aside li h2{
        font-size:14px;
        /*color:#fff;*/
        color: black;
        font-weight:normal;
        margin-bottom:5px;
    }
    aside li h3{
        font-size:12px;
        color:#7e818a;
        font-weight:normal;
    }

    .status{
        width:8px;
        height:8px;
        border-radius:50%;
        display:inline-block;
        margin-right:7px;
    }
    .green{
        background-color:#58b666;
    }
    .orange{
        background-color:#ff725d;
    }
    .blue{
        background-color:#6fbced;
        margin-right:0;
        margin-left:7px;
    }
    li {
        cursor: pointer;
    }
    .doh_logo {
        width: 50px;
    }
    .selected {
        background-color: darkgray;
    }
</style>