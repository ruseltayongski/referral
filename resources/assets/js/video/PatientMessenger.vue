<template>
    <div class="box direct-chat direct-chat-primary">
        <div class="box-header with-border">
            <h1 class="box-title">Chat with Your Doctor</h1>
            <button type="button" class="patient-messenger-close" @click="$emit('close')">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="box-body">
            <div class="direct-chat-messages" style="height: 520px;" :id="'patientfeedback' + code">
                <div v-if="loadingFlag" class="text-center">
                    <img :src="loadingPath" alt="">
                </div>
                <div v-for="message in messages" :key="message.id" class="direct-chat-msg" v-bind:class="message.position" v-else>
                    <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name text-primary" v-bind:class="{ 'pull-right': message.position == 'right', 'pull-left': message.position == 'left' }">{{ message.facility_name }}</span><br>
                        <span class="direct-chat-name" v-bind:class="{ 'pull-right': message.position == 'right', 'pull-left': message.position == 'left' }">{{ message.sender_name }}</span>
                        <span class="direct-chat-timestamp" v-bind:class="{ 'pull-left': message.position == 'right', 'pull-right': message.position == 'left' }">{{ message.send_date }}</span>
                    </div>
                    <div class="attachment-wrapper direct-chat-text"
                        v-if="message.filename"
                        style="margin-bottom: 10px; white-space: nowrap; overflow-x: auto;">
                        <div v-for="(file, index) in parseFiles(message.filename)"
                            :key="index"
                            style="display: inline-block; text-align: center; width: 60px; margin-right: 5px;">
                            <a href="javascript:void(0)" @click="triggerPreview(file, index)">
                                <img class="attachment-thumb" :src="getThumbnail(file)" style="width: 50px; height: 50px; object-fit: cover; border:1px solid green; border-radius: 4px;">
                            </a>
                        </div>
                        <div v-if="message.message && message.message.trim()" v-html="message.message" style="white-space: normal; word-break: break-word; margin-top: 5px;"></div>
                    </div>
                    <div class="direct-chat-text" v-if="!message.filename" v-html="message.message"></div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <div id="fileDisplayBar" class="file-display-container" v-if="globalFiles.length > 0">
                <div v-for="file in globalFiles" :key="file.id" class="file-preview-wrapper" style="position:relative; display:inline-block; margin:2px;">
                    <span class="remove-file-feedback" @click="removeFile(file.id)"
                        style="position:absolute;top:-8px;right:-8px;background:#f44336;color:#fff;border-radius:50%;padding:0 5px;font-size:12px;cursor:pointer;">&times;</span>
                    <img :src="file.url" :alt="file.name" style="width:80px; height:60px; object-fit:contain; border:1px solid #ccc;">
                    <div :title="file.name" style="font-size:0.6em; width:80px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;">{{ file.truncatedName }}</div>
                </div>
            </div>
            <div class="input-group">
                <textarea placeholder="Type your message" id="patienttextarea"></textarea>
                <span class="input-group-btn">
                    <a class="btn btn-app" @click="sendFeedback">
                        <i class="fa fa-save"></i> Send
                    </a>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "PatientMessenger",
    // fetchUrl / sendUrl are the full SIGNED urls generated server-side
    // (URL::temporarySignedRoute). Do not construct routes client-side —
    // the signature is tied to the exact path + query string.
    props: {
        code: { type: String, required: true },
        fetchUrl: { type: String, required: true },
        sendUrl: { type: String, required: true },
        patientName: { type: String, default: 'Patient' },
        broadcastingUrl: { type: String, default: '' }
    },
    data() {
        return {
            messages: [],
            pendingMessages: [],
            globalFiles: [],
            uploadedFiles: new Map(),
            loadingPath: (this.broadcastingUrl || '') + '/resources/img/loading.gif',
            loadingFlag: true
        }
    },
    mounted() {
        this.initializeTinyMCE();
        this.fetchMessages();
        this.listenForMessages();
    },
    methods: {
        fetchMessages() {
            this.loadingFlag = true;
            axios.get(this.fetchUrl)
                .then(response => {
                    // guard against unexpected shape rather than assuming response.data.messages
                    this.messages = (response.data && response.data.messages) || [];
                })
                .catch(error => {
                    console.error('Failed to load messages:', error);
                })
                .finally(() => {
                    this.loadingFlag = false;
                    this.scrolldownFeedback();
                });
        },

        getFileExtension(file) {
            return file.split('.').pop().toLowerCase();
        },

        parseFiles(fileString) {
            if (!fileString) return [];
            return fileString.split('|').filter(Boolean);
        },

        getThumbnail(file) {
            const ext = this.getFileExtension(file);
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return file;
            if (ext === 'pdf') return (this.broadcastingUrl || '') + '/public/fileupload/pdffile.png';
            return file;
        },

        triggerPreview(fileUrl, index) {
            window.setupfeedbackFilePreview && window.setupfeedbackFilePreview(fileUrl, index, this.code);
            $('#filePreviewContentReco').modal('show');
        },

        initializeTinyMCE() {
            const self = this;
            tinymce.init({
                selector: "#patienttextarea",
                plugins: "emoticons autoresize",
                toolbar: "emoticons uploadfile",
                toolbar_location: "bottom",
                menubar: false,
                statusbar: false,
                setup: function (editor) {
                    self.Editor = editor;
                    editor.ui.registry.addButton('uploadfile', {
                        icon: 'upload',
                        tooltip: 'Upload Image or PDF',
                        onAction: () => self.triggerFileUpload()
                    });
                    editor.on('init', function () {
                        editor.getContainer().style.width = "100%";
                    });
                }
            });
        },

        triggerFileUpload() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*,.pdf');
            input.setAttribute('multiple', true);
            input.onchange = (event) => {
                const files = event.target.files;
                for (let i = 0; i < files.length; i++) {
                    this.processFileFeedback(files[i]);
                }
            };
            input.click();
        },

        processFileFeedback(file) {
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                this.$toast.error('Only image files and PDF files are allowed');
                return;
            }
            const fileId = 'file-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            this.uploadedFiles.set(fileId, file);
            this.globalFiles.push({
                id: fileId,
                url: URL.createObjectURL(file),
                name: file.name,
                truncatedName: this.truncateFileName(file.name, 15),
                type: file.type
            });
        },

        truncateFileName(fileName, maxLength = 12) {
            if (fileName.length <= maxLength) return fileName;
            const lastDot = fileName.lastIndexOf('.');
            const ext = lastDot !== -1 ? fileName.slice(lastDot) : '';
            const name = lastDot !== -1 ? fileName.slice(0, lastDot) : fileName;
            const space = maxLength - ext.length - 3;
            return space <= 0 ? fileName.slice(0, maxLength - 3) + '...' : name.slice(0, space) + '...' + ext;
        },

        removeFile(fileId) {
            this.uploadedFiles.delete(fileId);
            this.globalFiles = this.globalFiles.filter(f => f.id !== fileId);
        },

        sendFeedback() {
            tinyMCE.triggerSave();
            let str = $("#patienttextarea").val();
            str = str.replace(/^\<p\>/, "").replace(/\<\/p\>$/, "");
            str = str.replace(/&nbsp;/g, " ").trim().replace(/\s+/g, " ");

            if (!str && this.uploadedFiles.size === 0) {
                Lobibox.alert("error", { msg: "Message was empty!" });
                return;
            }

            tinyMCE.activeEditor.setContent('');

            // build filename string the same way RecoMessages does
            const filesArray = Array.from(this.uploadedFiles.values());
            let fileNamesString = '';
            if (filesArray.length > 0) {
                fileNamesString = filesArray.map(file => file.name).join('|');
            }

            // optimistic append so it feels instant; server broadcast will skip
            // echoing it back to this same client (sender check on the socket side)
            this.pendingMessages.push({ text: str, time: Date.now() });
            this.messages.push({
                id: 'local-' + Date.now(),
                code: this.code,
                message: str,
                filename: fileNamesString,   // now populated, matches RecoMessages
                sender: 0,
                sender_name: this.patientName,
                facility_name: 'Patient',
                send_date: moment().format('D MMM h:m a'),
                position: 'right'
            });
            this.scrolldownFeedback();
            console.log("Messages:", this.messages);
            const formData = new FormData();
            formData.append('message', str);
            formData.append('display_name', this.patientName);
            filesArray.forEach((file, index) => {
                formData.append(`file_upload[${index}]`, file);
            });

            axios.post(this.sendUrl, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            }).then(response => {
                this.uploadedFiles.clear();
                this.globalFiles = [];
            }).catch(error => {
                console.error('Error sending message:', error);
                this.$toast.error('Failed to send message');
            });
        },

        scrolldownFeedback() {
            const el = document.getElementById("patientfeedback" + this.code);
            if (!el) return;
            setTimeout(() => { el.scrollTop = el.scrollHeight; }, 300);
        },

        listenForMessages() {
            Echo.channel('reco.' + this.code)
                .listen('SocketReco', (event) => {
                    if (event.payload.code !== this.code) return;

                    const pendingIndex = this.pendingMessages.findIndex(
                        p => p.text === event.payload.message && (Date.now() - p.time) < 10000
                    );
                    if (pendingIndex !== -1) {
                        this.pendingMessages.splice(pendingIndex, 1);
                        return; // this is our own message echoing back
                    }

                    this.messages.push({
                        id: event.payload.id || ('srv-' + Date.now() + '-' + Math.random()),
                        code: this.code,
                        message: event.payload.message,
                        filename: event.payload.filepath,
                        sender: event.payload.userid_sender,
                        sender_name: event.payload.name_sender,
                        send_date: moment().format('D MMM h:m a'),
                        position: 'left',
                        facility_name: event.payload.facility_sender
                    });
                    this.scrolldownFeedback();
                });
        }
    },
    watch: {
        messages() {
            this.scrolldownFeedback();
        }
    },
    beforeUnmount() {
        Echo.leave('reco.' + this.code);
    }
}
</script>

<style lang="scss" scoped>
    .direct-chat.direct-chat-primary {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 360px;
    max-width: 90vw;
    z-index: 10000;
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}
.patient-messenger-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 16px;
    color: #666;
    cursor: pointer;
}
</style>