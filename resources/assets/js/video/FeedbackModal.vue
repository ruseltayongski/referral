<template>
    
    <div>
    <!-- Feedback Modal -->
    <div
      v-if="isVisible"
      class="modal fade show"
      role="dialog"
      style="display: block; background-color: rgba(0, 0, 0, 0.5);"
    >
      <div class="modal-dialog modal-md" id="feedbackModal">
        <div class="modal-content">
          <div class="box box-success direct-chat direct-chat-primary">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
              <h5 class="box-title" style="margin-left:10px; font-size:18px; font-weight:500;">{{ code }}</h5>
              <button
                type="button"
                class="btn btn-box-tool"
                @click="$emit('close-modal')"
              >
                <i class="fa fa-times">X</i>
              </button>
            </div>
            <div class="box-body">
              <!-- Chat Messages -->
              <div
                class="direct-chat-messages chat-container"
                ref="chatMessages"
              >
                <div
                  v-for="(message, index) in messages"
                  :key="index"
                  :class="{
                    'chat-message': true,
                    'chat-sender': message.sender === userId,
                    'chat-receiver': message.sender !== userId,
                  }"
                >
               
                  <div class="chat-avatar">
                    <img
                      :src="message.sender === userId
                        ? (message.senderImage || getImagePath('sender.png'))
                        : (message.senderImage || getImagePath('receiver.png'))"
                      alt="User Image"
                    />
                  </div>
                  <div class="chat-content">
                    <div class="chat-meta" style="margin-left:10px;">
                      <span class="facility">{{ message.facility }}</span>
                      <span class="name">
                        {{ message.fname }} {{ message.lname }}
                      </span>
                      <!-- <span class="timestamp">{{ message.date }}</span> -->
                    </div>
                    <div
                      class="chat-bubble"
                      :class="{
                        'bubble-sender': message.sender === userId,
                        'bubble-receiver': message.sender !== userId,
                      }"
                    >
                      {{ message.message }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <textarea
                v-model="newMessage"
                class="form-control"
                placeholder="Type a message..."
                @keyup.enter="sendMessage"
              ></textarea>
              <!-- <button class="btn btn-success btn-sm" @click="sendMessage">
                Send
              </button> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>

export default {
  props: {
    isVisible: Boolean,
    code: String,
    userId: Number,
    fetchUrl: String,
    postUrl: String,
    imageUrl: String,
  },
  data() {
    return {
        messages: [],
        newMessage: "",
        baseUrl: $("#broadcasting_url").val(),
        messageData: null,
    };
  },
    mounted() {
    this.fetchMessages();
    // console.log("Modal Mounted, fetching messages");

    // window.Echo.channel("reco") // Replace "feedback-channel" with your channel name
    //     .listen("SocketReco", (event) => {
    //         console.log("my events socket reco incoming", event);
    //     });

    // Echo.join('reco').listen('SocketReco', function (event) {
    //     console.log("my events socket reco incoming", event);
    // });
    // Echo.join('reco')
    // .listen('SocketReco', (event) => {
    //     console.log("my submet update", event);
    // });
    
  },
  methods: {
     getImagePath(image) {
    // Use Laravel's `asset` if the path is passed from Blade to Vue.
        return image
        ? `${this.baseUrl}/resources/img/${image}`
        : `${this.baseUrl}/resources/img/receiver.png`; // Replace this with the correct public path
  },
    fetchMessages() {
        // console.log("path image url", this.baseUrl, 'userId::', this.userId);
        axios.get(`/${this.fetchUrl}/${this.code}?ajax=true`).then((response) => {
        this.messages = response.data.messages;
        // console.log(" response",  response);
        this.scrollToBottom();
        
        });
    },
    sendMessage(){
        // console.log("sender image",  this.getImagePath("sender.png"), this.messageData);
         if (this.newMessage.trim()) {
        const newMsg = {
          senderId: this.userId,
          facility: this.userId,
          fname: "Your Name",
          lname: "Your Last Name",
          date: new Date().toLocaleString(),
          message: this.newMessage.trim(),
          senderImage: this.getImagePath("sender.png"), // Replace with a valid image URL
        };
        this.messages.push(newMsg);
        this.scrollToBottom();
        axios.post(`${this.baseUrl}/doctor/feedback`, {
          message: this.newMessage,
          code: this.code,
        });
        this.newMessage = "";
      }
    },
    scrollToBottom() {
      // this.$nextTick(() => {
      //   const chatBox = this.$refs.chatMessages;
      //   chatBox.scrollTop = chatBox.scrollHeight;
      // });
    },

  },
  watch: {
    code: "fetchMessages",
      isVisible: {
        handler(newVal) {
        // console.log("isVisible changed to:", newVal);
        if (newVal) {
            this.fetchMessages();
        }
        },
        immediate: true
    }
  },
  
};
</script>
