<template>
  <div>
    <!-- Feedback Modal -->
    <div
      v-if="isVisible"
      class="modal fade show"
      role="dialog"
      style="display: block; background-color: rgba(0, 0, 0, 0.5)"
    >
         <div class="modal-dialog modal-md" id="feedbackModal">
        <div class="modal-content">
          <div class="box box-success direct-chat direct-chat-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Feedback for {{ code }}</h3>
              <button type="button" class="btn btn-box-tool" @click="$emit('close-modal')">
                <i class="fa fa-times"></i>
              </button>
            </div>
            <div class="box-body">
              <div class="direct-chat-messages" ref="chatMessages">
                <div
                  v-for="(message, index) in messages"
                  :key="index"
                  :class="{
                    'direct-chat-msg': true,
                    right: message.senderId === userId,
                  }"
                >
                  <div class="direct-chat-info">
                    <span class="direct-chat-name" :class="{ 'pull-right': message.senderId === userId }">
                      {{ message.senderName }}
                    </span>
                    <span class="direct-chat-timestamp">
                      {{ message.time }}
                    </span>
                  </div>
                  <img class="direct-chat-img" :src="message.senderImage" alt="User Image" />
                  <div class="direct-chat-text">{{ message.text }}</div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <textarea
                v-model="newMessage"
                class="form-control"
                placeholder="Type a message..."
              ></textarea>
              <button class="btn btn-success btn-lg" @click="sendMessage">Send</button>
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
  },
  data() {
    return {
        messages: [],
        newMessage: "",
    };
  },
  methods: {
    fetchMessages() {
        axios.get(`${this.fetchUrl}/${this.code}`).then((response) => {
        this.messages = response.data;
        this.scrollToBottom();

        });
    },
    sendMessage(){
         if (this.newMessage.trim()) {
        const newMsg = {
          senderId: this.userId,
          senderName: "You",
          text: this.newMessage.trim(),
          time: new Date().toLocaleString(),
          senderImage: "path_to_sender_image", // Replace with a valid image URL
        };
        this.messages.push(newMsg);
        this.scrollToBottom();
        axios.post(this.postUrl, {
          message: this.newMessage,
          code: this.code,
        });
        this.newMessage = "";
      }
    },
      scrollToBottom() {
      this.$nextTick(() => {
        const chatBox = this.$refs.chatMessages;
        chatBox.scrollTop = chatBox.scrollHeight;
      });
    },

  },
  watch: {
    code: "fetchMessages",
  },
    mounted() {
    this.fetchMessages();
  },
};
</script>
