<template>
    <div class="box direct-chat direct-chat-primary">
        <div class="box-header with-border">
            <h1 class="box-title" v-if="select_rec.code"><a :href="track_url" target="_blank">{{ select_rec.code }}</a></h1>
            <h1 class="box-title" v-else>Direct Chat</h1>
        </div>
        <div class="box-body">
            <div class="direct-chat-messages" style="height: 520px;" :id="select_rec.code">
                <div v-for="message in messages" :key="message.id" class="direct-chat-msg" v-bind:class="message.position">
                    <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name" v-bind:class="{ 'pull-right' : message.position == 'right','pull-left' : message.position == 'left' }">Sarah Bullock</span>
                        <span class="direct-chat-timestamp" v-bind:class="{ 'pull-left' : message.position == 'right','pull-right' : message.position == 'left' }">23 Jan 2:05 pm</span>
                    </div>
                    <img class="direct-chat-img" :src="logo" alt="message user image">
                    <div class="direct-chat-text">
                        {{ message.message }}
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <form action="#" method="post">
                <div class="input-group">
                    <textarea placeholder="Type your message" id="mytextarea"></textarea>
                    <span class="input-group-btn">
                        <a class="btn btn-app">
                            <i class="fa fa-save"></i> Send
                        </a>
                    </span>
                </div>
            </form>
        </div>

    </div>
</template>
<script>
    export default {
        name : "RecoMessages",
        data() {
            return {
                logo : String
            }
        },
        props : ["messages","select_rec","track_url"],
        methods: {
            scrolldownFeedback(code) {
                let objDiv = document.getElementById(code);
                setTimeout(function () {
                    objDiv.scrollTop = objDiv.scrollHeight;
                },500);
            }
        },
        watch: {
            messages: function() {
                this.scrolldownFeedback(this.select_rec.code)
            }
        },
        created(){
            this.logo = $("#doh_logo").val()
        }
    }
</script>
<style lang="scss" scoped>
    @import './css/main.scss';
</style>

