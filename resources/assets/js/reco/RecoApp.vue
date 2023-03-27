<template>
    <div class="row col-md-12">
        <div class="box">
            <div class="row">
                <div class="col-md-4">
                    <aside>
                        <header>
                            <input type="text" class="form-control" @keyup="searchList()" v-model="search" placeholder="search" style="height: 50px;">
                        </header>
                        <reco-list :reco="reco" :user="user" @selectrec="selectRec"></reco-list>
                    </aside>
                </div>
                <div class="col-md-8">
                    <reco-messages :messages="messages" :user="user" :select_rec="select_rec" :track_url="track_url" @listenreco="listenReco"></reco-messages>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import RecoList from './RecoList.vue'
    import RecoMessages from './RecoMessages.vue'
    export default {
        name: 'RecoApp',
        components: {
            RecoList,
            RecoMessages
        },
        data() {
            return {
                reco : [],
                reco_handler : [],
                reco_handler_order : Object,
                reco_seen_new : Object,
                select_rec : Object,
                track_url : String,
                messages : [],
                search : ""
            }
        },
        props : ["user"],
        created() {
            this.fetchMessages()
            //this.track_url = $("#broadcasting_url").val()+"/doctor/referred?referredCode=190604-004-194729"
        },
        methods: {
            async fetchMessages() {
                await axios.get('reco/fetch').then(response => {
                    const dataMap = response.data.map((item) => {
                        const message = item.message.replace(/<\/?[^>]+(>|$)/g, "")
                        return {
                            ...item,
                            patient_name: item.patient_name.length >= 25 ? item.patient_name.substring(0,25)+".." : item.patient_name,
                            message: message.substring(0,24)+'..'
                        }
                    })
                    this.reco = dataMap
                    this.reco_handler = dataMap
                });
            },
            async selectRec(payload) {
                this.select_rec = payload

                this.reco_seen_new = {
                    reco_id : payload.reco_id,
                    seen_userid : this.user.id,
                    seen_facility_id : this.user.facility_id,
                    code : payload.code
                }
                this.recoSeen(this.reco_seen_new)

                this.track_url = $("#broadcasting_url").val()+"/doctor/referred?referredCode="+payload.code
                await axios.get('reco/select/'+payload.code).then(response => {
                    this.messages = response.data
                });
            },
            searchList() {
                if(this.search) {
                    let filter = this.reco_handler.filter((rec) => rec.patient_name.toLowerCase().includes(this.search.toLowerCase()) || rec.code === this.search )
                    if(filter.length > 0)
                        this.reco = filter
                    else
                        this.reco = []
                }
                else
                    this.reco = this.reco_handler
            },
            newRecoNotification(message) {
                Lobibox.notify('success', {
                    delay: false,
                    closeOnClick: false,
                    title: 'New Reco',
                    msg: "<small>"+message+"</small>",
                    img: $("#broadcasting_url").val()+"/resources/img/ro7.png"
                });
            },
            listenReco(payload) {
                let filter = this.reco_handler.filter((rec) => rec.code === payload.code )
                if(!filter.length) {
                    axios.get('reco/new/'+payload.code).then(response => {
                        if(response.data.code) {
                            this.reco_handler.unshift(response.data)
                            this.reco.unshift(response.data)
                            this.newRecoNotification(response.data.message)
                        }
                    });
                }
                else if(filter.length) {
                    this.reco.map((item) => item.code === payload.code ? (item.message = payload.message,item.reco_seen = null,item.userid_sender = payload.userid_sender) : item ) //para ma update reco list
                    this.orderRecoList(payload.code)
                    if(this.user.id !== payload.userid_sender) {
                        this.newRecoNotification(payload.message)
                    }
                }
            },
            recoSeen(data) {
                axios.post('reco/seen', data).then(response => {

                });
            },
            orderRecoList(code) {
                this.reco_handler_order = this.reco.filter((rec) => rec.code === code)
                this.reco = this.reco.filter((rec) => rec.code !== code)
                this.reco.unshift(JSON.parse(JSON.stringify(this.reco_handler_order))[0])
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import './css/main.scss';
</style>
