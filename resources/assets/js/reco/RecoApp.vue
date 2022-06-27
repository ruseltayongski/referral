<template>
    <div class="row col-md-12">
        <div class="box box-success">
            <div id="container">
                <div class="row">
                    <div class="col-md-4">
                        <aside>
                            <header>
                                <input type="text" placeholder="search">
                            </header>
                            <reco-list :reco="reco" @selectrec="selectRec"></reco-list>
                        </aside>
                    </div>
                    <div class="col-md-8">
                        <main>
                            <reco-header :select_rec="select_rec" :track_url="track_url"></reco-header>
                            <reco-messages :messages="messages"></reco-messages>
                            <footer>
                                <textarea placeholder="Type your message"></textarea>
                                <i class="fa fa-file-picture-o"></i>
                                <a href="#">Send</a>
                            </footer>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import RecoList from './RecoList.vue'
    import RecoHeader from './RecoHeader.vue'
    import RecoMessages from './RecoMessages.vue'
    export default {
        name: 'RecoApp',
        components: {
            RecoList,
            RecoHeader,
            RecoMessages
        },
        data() {
            return {
                reco : [],
                select_rec : Object,
                track_url : String,
                messages : []
            }
        },
        props : {
            data: ""
        },
        created() {
            this.fetchMessages()
            this.track_url = $("#broadcasting_url").val()+"/doctor/referred?referredCode=190604-004-194729"
        },
        methods: {
            fetchMessages() {
                axios.get('reco/fetch').then(response => {
                    this.reco = response.data
                });
            },
            selectRec(payload){
                this.select_rec = payload
                this.track_url = $("#broadcasting_url").val()+"/doctor/referred?referredCode="+payload.code
                axios.get('reco/select/'+payload.code).then(response => {
                    this.messages = response.data
                });
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

    main header{
        height:110px;
        padding:30px 20px 30px 40px;
    }
    main header > *{
        display:inline-block;
        vertical-align:top;
    }
    main header img:first-child{
        border-radius:50%;
    }
    main header img:last-child{
        width:24px;
        margin-top:8px;
    }
    main header div{
        margin-left:10px;
        margin-right:145px;
    }
    main header h2{
        font-size:16px;
        margin-bottom:5px;
    }
    main header h3{
        font-size:14px;
        font-weight:normal;
        /*color:#7e818a;*/
    }

    #chat{
        padding-left:0;
        margin:0;
        list-style-type:none;
        overflow-y:scroll;
        height:535px;
        border-top:2px solid #fff;
        border-bottom:2px solid #fff;
        width: 100%;
    }
    #chat li{
        padding:10px 30px;
    }
    #chat h2,#chat h3{
        display:inline-block;
        font-size:13px;
        font-weight:normal;
    }
    #chat h3{
        color:#bbb;
    }
    #chat .entete{
        margin-bottom:5px;
    }
    #chat .message{
        padding:20px;
        color:#fff;
        line-height:25px;
        max-width:90%;
        display:inline-block;
        text-align:left;
        border-radius:5px;
    }
    #chat .me{
        text-align:right;
    }
    #chat .you .message{
        background-color:#59ab91;
    }
    #chat .me .message{
        background-color:#6fbced;
    }
    #chat .triangle{
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 10px 10px 10px;
    }
    #chat .you .triangle{
        border-color: transparent transparent #58b666 transparent;
        margin-left:15px;
    }
    #chat .me .triangle{
        border-color: transparent transparent #6fbced transparent;
        margin-left: 95%;
    }

    main footer{
        height:155px;
        padding:20px 30px 10px 20px;
    }
    main footer textarea{
        resize:none;
        border:none;
        display:block;
        width:100%;
        height:80px;
        border-radius:3px;
        padding:20px;
        font-size:13px;
        margin-bottom:13px;
    }
    main footer textarea::placeholder{
        color:#ddd;
    }
    main footer img{
        height:30px;
        cursor:pointer;
    }
    main footer a{
        text-decoration:none;
        text-transform:uppercase;
        font-weight:bold;
        color:#6fbced;
        vertical-align:top;
        margin-left:333px;
        margin-top:5px;
        display:inline-block;
    }
    li {
        cursor: pointer;
    }
</style>
