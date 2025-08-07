<template>
    <div class="box direct-chat direct-chat-primary">
        <div class="box-header with-border">
            <h1 class="box-title" v-if="select_rec.code"><a :href="track_url" target="_blank">{{ select_rec.code }}</a></h1>
            <h1 class="box-title" v-else>Direct Chat</h1>
        </div>
        <div class="box-body">
            <div class="direct-chat-messages" style="height: 520px;" :id="'vuefeedback'+select_rec.code">
                <div v-if="loadingFlag" class="text-center">
                    <img :src="loadingPath" alt="">
                </div>
                <div v-for="message in messages" :key="message.id" class="direct-chat-msg" v-bind:class="message.position" v-else>
                    <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name text-primary" v-bind:class="{ 'pull-right' : message.position == 'right','pull-left' : message.position == 'left' }">{{ message.facility_name }}</span><br>
                        <span class="direct-chat-name" v-bind:class="{ 'pull-right' : message.position == 'right','pull-left' : message.position == 'left' }">{{ message.sender_name }}</span>
                        <span class="direct-chat-timestamp" v-bind:class="{ 'pull-left' : message.position == 'right','pull-right' : message.position == 'left' }">{{ message.send_date }}</span>
                    </div>
                    <img class="direct-chat-img" :src="message.chat_image" alt="message user image">
                    <div class="attachment-wrapper direct-chat-text"
                        v-if="message.filename"
                        style="margin-bottom: 10px; white-space: nowrap; overflow-x: auto;">
                        <div v-for="(file, index) in parseFiles(message.filename)"
                            :key="index"
                            style="display: inline-block; text-align: center; width: 60px; margin-right: 5px;">
                            <a href="javascript:void(0)"
                            @click="triggerPreview(file, parseFiles(message.filename), index)">
                            <img class="attachment-thumb"
                                :src="getThumbnail(file)"
                                :alt="getFileExtension(file).toUpperCase() + ' file'"
                                style="width: 50px; height: 50px; object-fit: cover; border:1px solid green; border-radius: 4px;">
                            </a>
                        </div>
                        <div v-if="message.message && message.message.trim()" v-html="message.message" 
                            style="white-space: normal; word-break: break-word; margin-top: 5px;"></div>
                    </div>
                    <div class="direct-chat-text" v-if="!message.filename" v-html="message.message"></div>
                </div>
            </div>
        </div>
        <div class="box-footer">
             <!-- <div class="file-display-bar" id="fileDisplayBar">
                <div class="upload-prompt" id="uploadPrompt">
                </div>
            </div> -->

            <div id="fileDisplayBar" class="file-display-container" v-if="globalFiles.length > 0">
                <div 
                    v-for="file in globalFiles" 
                    :key="file.id"
                    class="file-preview-wrapper" 
                    :data-file-id="file.id"
                    style="position:relative; display:inline-block; margin:2px;"
                >
                    <span 
                    class="remove-file-feedback" 
                    @click="removeFile(file.id)"
                    style="position:absolute;top:-8px;right:-8px;background:#f44336;color:#fff;border-radius:50%;padding:0 5px;font-size:12px;cursor:pointer;line-height:1;"
                    title="Remove"
                    >&times;</span>
                    <a :href="file.url" target="_blank">
                    <img 
                        v-if="file.type.startsWith('image')"
                        :src="file.url" 
                        :alt="file.name" 
                        style="width:80px; height:60px; object-fit:contain; border:1px solid #ccc;" 
                        :data-file-id="file.id" 
                    />
                    <img 
                        v-else-if="file.type === 'application/pdf'"
                        :src="this.BaseUrlFile + '/public/fileupload/pdffile.png'" 
                        alt="PDF File" 
                        style="width:80px; height:60px; object-fit:contain; border:1px solid #ccc;" 
                        :data-file-id="file.id" 
                    />
                    </a>
                    
                    <div 
                    :title="file.name" 
                    style="font-size:0.6em; width:80px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center;"
                    >
                    {{ file.truncatedName }}
                    </div>
                </div>
            </div>
            <div class="input-group">
                <textarea placeholder="Type your message" id="mytextarea"></textarea>
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
        name : "RecoMessages",
        data() {
            return {
                globalFiles: [],
                selectedFile: [],
                uploadedFiles: new Map(),
                showUploadPrompt: true,
                logo : String,
                sender_name : "",
                socket_message: "",
                new_message : Object,
                BaseUrlFile: '',
                loadingPath: $("#broadcasting_url").val()+'/resources/img/loading.gif',
                loadingFlag: false
            }
        },
        props : ["messages","select_rec","track_url","user"],
        mounted() {
            this.initializeTinyMCE();
            this.BaseUrlFile = $("#broadcasting_url").val();
        },
        methods: {
             getFileExtension(file) {
                return file.split('.').pop().toLowerCase();
            },
            getFileName(file) {
                return file.split('/').pop();
            },
        //    parseFiles(fileString) {
        //         if (!fileString) return [];
        //         // Get the base path (e.g., /referral)
        //         const basePath = window.location.pathname.split('/').filter(Boolean)[0];
        //         return fileString.split('|').map(file => {
        //             if (file.startsWith('http')) return file;
        //             // Ensure leading slash
        //             const filePath = file.startsWith('/') ? file : '/' + file;
        //             // Add base path if not already present
        //             return `/${basePath}${filePath}`;
        //         });
        //     },        
            parseFiles(fileData) {
                if (!fileData) return [];
                
                // Handle different types of file data
                
                // Case 1: If it's an array of File objects (from file upload)
                if (Array.isArray(fileData)) {
                    return fileData.map(file => {
                        if (file instanceof File) {
                            // For File objects, create a temporary URL for preview
                            return URL.createObjectURL(file);
                        } else if (typeof file === 'object' && file.name) {
                            // For file metadata objects
                            return file.url || file.path || file.name;
                        } else if (typeof file === 'string') {
                            // For string paths, apply the existing logic
                            return this.processFilePath(file);
                        }
                        return file;
                    });
                }
                
                // Case 2: If it's a single File object
                if (fileData instanceof File) {
                    return [URL.createObjectURL(fileData)];
                }
                
                // Case 3: If it's an object with file metadata
                if (typeof fileData === 'object' && fileData.name) {
                    return [fileData.url || fileData.path || fileData.name];
                }
                
                // Case 4: If it's a string (original functionality)
                if (typeof fileData === 'string') {
                    return fileData.split('|').map(file => this.processFilePath(file));
                }
                
                // Fallback
                return [];
            },

            // Helper method to process file paths (extracted from original logic)
            processFilePath(file) {
                if (file.startsWith('http')) return file;
                
                // Get the base path (e.g., /referral)
                const basePath = window.location.pathname.split('/').filter(Boolean)[0];
                
                // Ensure leading slash
                const filePath = file.startsWith('/') ? file : '/' + file;
                
                // Add base path if not already present
                return `/${basePath}${filePath}`;
            },
            getThumbnail(file) {
                const ext = this.getFileExtension(file);
                // console.log("file :", cleanedFile, "baseUrl", this.BaseUrlFile); 
                
                // let baseUrl = $("#broadcasting_url").val().replace(/[\/|]$/, "");
                // console.log("my patrhererre:", baseUrl);
                window.globalFiles = window.globalFiles || [];
                // console.log("globalFiles update:", window.globalFiles);
                const files = file.split('|');
                files.forEach(path => {

                    const Filepath = path.replace(/^\/reco/, '');
                    console.log("file path clean", Filepath);
                    if(Filepath.includes("/RecoChat/")){
                        const fullPath = `${this.BaseUrlFile}/${path.replace(/^\/?(referral\/)?/, '')}`;
                        if(!window.globalFiles.includes(fullPath)){
                            window.globalFiles.push(fullPath);
                        }
                    }
                });
                
                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                return file;
                } else if (ext === 'pdf') { 
                return $("#broadcasting_url").val() +'/public/fileupload/pdffile.png';
                } 
            },
            triggerPreview(fileUrl, allFiles, index) {
                // console.log("all files", allFiles);
                // console.log("triggerPreview", fileUrl, index);

                let baseUrl = $("#broadcasting_url").val().replace(/[\/|]$/, "");
               
                const fullfile = `${baseUrl}/${fileUrl.replace(/^\/?(referral\/)?/, '')}`;

                window.setupfeedbackFilePreview(fullfile, index, this.select_rec.code);
                $('#filePreviewContentReco').modal('show');
            },
            initializeTinyMCE(){
                const self = this;
                tinymce.init({
                    selector: "#mytextarea",
                    plugins: "emoticons autoresize",
                    toolbar: "emoticons uploadfile",
                    toolbar_location: "bottom",
                    menubar: false,
                    statusbar: false,
                    automatic_uploads: true,
                    setup: function (editor) {
                        self.Editor = editor;

                        // Add custom upload button
                        editor.ui.registry.addButton('uploadfile', {
                            icon: 'upload',
                            tooltip: 'Upload Image or PDF',
                            onAction: function() {
                                self.triggerFileUpload();
                            }
                        });

                        // Add click handler for inserted files
                        editor.on('click', function(e) {
                            const target = e.target;
                            if (target.tagName === 'IMG' && target.getAttribute('data-file-id')) {
                                const fileId = target.getAttribute('data-file-id');
                                const storedFile = self.uploadedFiles.get(fileId);
                                
                                if (storedFile) {
                                    $('#filePreviewModalReco').modal('show');
                                }
                            }
                        });
                        console.log("editor uploadfiles:", self.uploadedFiles);
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
                    if (files.length > 0) {
                        for (let i = 0; i < files.length; i++) {
                            this.processFileFeedback(files[i]);
                        }
                    }
                };
                
                input.click();
            },

            processFileFeedback(file) {
                const allowedTypes = [
                    'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                    'image/bmp', 'image/webp', 'image/svg+xml', 'application/pdf'
                ];

                if (!allowedTypes.includes(file.type)) {
                    this.$toast.error('Only image files and PDF files are allowed');
                    return;
                }

                const fileId = 'file-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
                
                // Store the file in Vue data
                this.uploadedFiles.set(fileId, file);
                
                // Create local URL for the file
                const fileURL = URL.createObjectURL(file);
                const truncatedFilename = this.truncateFileName(file.name, 15);
                // Add to global files array for display
                this.globalFiles.push({
                    id: fileId,
                    file: file,
                    url: fileURL,
                    name: file.name,
                    truncatedName: truncatedFilename,
                    type: file.type
                });

                this.fileUploadBatch = true;

                // Clear existing timeout
                if (this.fileUploadTimeout) {
                    clearTimeout(this.fileUploadTimeout);
                }
            },

            truncateFileName(fileName, maxLength = 12) {
                if (fileName.length <= maxLength) {
                    return fileName;
                }
                
                const lastDotIndex = fileName.lastIndexOf('.');
                const extension = lastDotIndex !== -1 ? fileName.slice(lastDotIndex) : '';
                const nameWithoutExt = lastDotIndex !== -1 ? fileName.slice(0, lastDotIndex) : fileName;
                
                const availableSpace = maxLength - extension.length - 3;
                
                if (availableSpace <= 0) {
                    return fileName.slice(0, maxLength - 3) + '...';
                }
                
                return nameWithoutExt.slice(0, availableSpace) + '...' + extension;
            },

            removeFile(fileId) {
                if (fileId && this.uploadedFiles.has(fileId)) {
                    this.uploadedFiles.delete(fileId);
                    this.globalFiles = this.globalFiles.filter(file => file.id !== fileId);
                }
             },
             constructFileUrl(filePath) {
                const base = this.BaseUrlFile || $("#broadcasting_url").val() || '';
                return base + '/' + filePath;
             },
             getFileTypeFromPath(filePath) {
                 const ext = filePath.split('.').pop().toLowerCase();
                 if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                     return 'image/' + ext;
                 } else if (ext === 'pdf') {
                     return 'application/pdf';
                 }
                 return 'unknown';      
            },
            sendFeedback() {    
                console.log("sendFeedback called", this.uploadedFiles);
                if(this.select_rec.code) {
                    tinyMCE.triggerSave();
                    let str = $("#mytextarea").val();
                    console.log("str stirng data", str);
                    // str = str.replace(/^\<p\>/,"").replace(/\<\/p\>$/,"");
                    
                    str = str.replace(/^\<p\>/, "").replace(/\<\/p\>$/, "");
                    str = str.replace(/&nbsp;/g, " ").trim();
                    str = str.replace(/\s+/g, " ");

                    if(str || this.uploadedFiles.size > 0) {
                        tinyMCE.activeEditor.setContent('');

                        this.sender_name = ""
                        if (this.user.level === "doctor")
                            this.sender_name += "Dr. "
                        
                        console.log("str message:", str);

                        this.sender_name += this.user.fname + " " + this.user.mname + " " + this.user.lname
                        
                        const filesArray = Array.from(this.uploadedFiles.values());
                        
                        let fileNamesString = '';
                        if (filesArray.length > 0) {
                            fileNamesString = filesArray.map(file => file.name).join('|');
                        }
                        console.log("fileNamesString", fileNamesString);
                        this.new_message = {
                            code: this.select_rec.code,
                            message: str,
                            filename: fileNamesString,  
                            sender: this.user.id,
                            sender_name: this.sender_name,
                            send_date: moment().format('D MMM h:m a'),
                            position: "right",
                            chat_image: $("#receiver_pic").val(),
                            facility_name: $("#facility_name").val()
                        }
                        this.messages.push(this.new_message)
                        this.scrolldownFeedback(this.select_rec.code)
                        // this.socket_message = {
                        //     code: this.select_rec.code,
                        //     message: str
                        // }
                        // axios.post('doctor/feedback', this.socket_message).then(response => {});
                        const formData = new FormData();
                        formData.append('code', this.select_rec.code);
                        formData.append('message', str);

                       filesArray.forEach((file, index) => {
                            console.log(`Adding file ${index}:`, file.name, file);
                            formData.append(`file_upload[${index}]`, file);
                        });

                        axios.post('doctor/feedback', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }).then(response => {
                            console.log('Message sent successfully:', response.data);

                            if (response.data.filename) {
                                    this.uploadedFiles.clear();
                                    this.globalFiles = []; // Clear the file display
                                    this.showUploadPrompt = true;
                                    this.showFileDisplayBar = false; // Hide the file display bar
                                    
                                    // Update the last message with the server filename if needed
                                    if (response.data.filename) {
                                        const lastMessageIndex = this.messages.length - 1;
                                        this.messages[lastMessageIndex].filename = response.data.filename;
                                    }
                               // });
                                
                                // Replace globalFiles with server files for display
                               // this.globalFiles = newGlobalFiles;
                            }
                
                            this.uploadedFiles.clear();
                            
                            this.showUploadPrompt = true;
                        }).catch(error => {
                            console.error('Error sending message:', error);
                            this.$toast.error('Failed to send message');
                        });
                    }
                    else {
                        Lobibox.alert("error",
                            {
                                msg: "ReCo message was empty!"
                            });
                    }
                }
                else {
                    Lobibox.alert("error",
                    {
                        msg: "You must select a ReCo"
                    });
                }
            },
            handleFileUpload(files) {
                // Your custom logic
                if (!files || files.length === 0) {
                    return;
                }
                
                // Process each uploaded file
                for (let i = 0; i < files.length; i++) {
                    this.processFileFeedback(files[i]);
                }
                
                // Hide upload prompt and show file display bar
                this.showUploadPrompt = false;
                this.showFileDisplayBar = true;
            },
            scrolldownFeedback(code) {
                this.loadingFlag = false
                let objDiv = document.getElementById("vuefeedback"+code);
                setTimeout(function () {
                    objDiv.scrollTop = objDiv.scrollHeight
                },500);
            }
        },
        watch: {
            messages: function() {
                this.loadingFlag = false
                this.scrolldownFeedback(this.select_rec.code)
            },
            select_rec: function (new_val, old_val) {
                this.loadingFlag = true
            },
            globalFiles(newVal) {
                console.log("Global files updated:", newVal);
            }
        },
        created() {
            //console.log(this.loadingPath)
            this.logo = $("#doh_logo").val()
            Echo.join('reco')
                .listen('SocketReco', (event) => {
                    console.log("SocketReco event received:", event);
                    if(event.payload.code === this.select_rec.code && event.payload.userid_sender !== this.user.id) {
                        this.new_message = {
                            code: this.select_rec.code,
                            message: event.payload.message,
                            filename: event.payload.filepath,
                            sender: event.payload.userid_sender,
                            sender_name :  event.payload.name_sender,
                            send_date : moment().format('D MMM h:m a'),
                            position: event.payload.userid_sender === this.user.id ? 'right' : 'left',
                            chat_image: event.payload.userid_sender === this.user.id ? $("#receiver_pic").val() : $("#sender_pic").val(),
                            facility_name: event.payload.facility_sender
                        }
                        this.messages.push(this.new_message)
                        this.scrolldownFeedback(this.select_rec.code)
                        console.log("listen1", this.messages);
                        // Lobibox.notify('success', {
                        //     delay: false,
                        //     closeOnClick: false,
                        //     title: 'New Reco',
                        //     msg: "<small>"+event.payload.message+"</small>",
                        //     img: $("#broadcasting_url").val()+"/resources/img/ro7.png"
                        // });
                    }

                    event.payload.alreadyNotifyReco = true
                    this.$emit('listenreco', event.payload);
                });
        }
    }
</script>
<style lang="scss" scoped>
    @import './css/main.scss';
</style>

