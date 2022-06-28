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
                    <reco-messages :messages="messages" :select_rec="select_rec" :track_url="track_url"></reco-messages>
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
                select_rec : Object,
                track_url : String,
                messages : [],
                search : ""
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
                    this.reco_handler = response.data
                });
            },
            selectRec(payload){
                this.select_rec = payload
                this.track_url = $("#broadcasting_url").val()+"/doctor/referred?referredCode="+payload.code
                axios.get('reco/select/'+payload.code).then(response => {
                    this.messages = response.data
                });
            },
            searchList() {
                if(this.search) {
                    let filter = this.reco_handler.filter((rec) => rec.patient_name.toLowerCase().includes(this.search.toLowerCase()))
                    if(filter.length > 0)
                        this.reco = filter
                    else
                        this.reco = []
                }
                else
                    this.reco = this.reco_handler
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import './css/main.scss';
</style>
