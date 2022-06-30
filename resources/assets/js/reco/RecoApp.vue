<template>
    <div class="row col-md-12">
        <div class="box">
            <div class="row">
                <div class="col-md-4">
                    <aside>
                        <header>
                            <input type="text" class="form-control" @keyup="searchList()" v-model="search" placeholder="search" style="height: 50px;">
                        </header>
                        <reco-list :reco="reco" @selectrec="selectRec"></reco-list>
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
            this.track_url = $("#broadcasting_url").val()+"/doctor/referred?referredCode=190604-004-194729"
        },
        methods: {
            fetchMessages() {
                axios.get('reco/fetch').then(response => {
                    this.reco = response.data
                    this.reco_handler = response.data
                });
            },
            selectRec(payload) {
                this.select_rec = payload

                this.reco_seen_new = {
                    reco_id : payload.reco_id,
                    seen_userid : this.user.id,
                    seen_facility_id : this.user.facility_id,
                    code : payload.code
                }
                this.recoSeen(this.reco_seen_new)

                this.track_url = $("#broadcasting_url").val()+"/doctor/referred?referredCode="+payload.code
                axios.get('reco/select/'+payload.code).then(response => {
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
            listenReco(payload) {
                let filter = this.reco_handler.filter((rec) => rec.code === payload.code )
                if(!filter.length) {
                    axios.get('reco/new/'+payload.code).then(response => {
                        if(response.data.code) {
                            this.reco_handler.unshift(response.data)
                            Lobibox.notify('success', {
                                delay: false,
                                closeOnClick: false,
                                title: 'New Reco',
                                msg: "<small>"+response.data.message+"</small>",
                                img: $("#broadcasting_url").val()+"/resources/img/ro7.png"
                            });
                        }
                    });
                }
            },
            recoSeen(data) {
                axios.post('reco/seen', data).then(response => {

                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import './css/main.scss';
</style>
