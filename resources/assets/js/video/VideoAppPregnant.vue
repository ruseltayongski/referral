<script>
import axios from "axios";
import { Transition } from "vue";
import AgoraRTC from "agora-rtc-sdk-ng";
import PrescriptionModal from "./PrescriptionModal.vue";
import LabRequestModal from "./LabRequestModal.vue"; // I add this
import FeedbackModal from "./FeedbackModal.vue";
import PDFViewerModal from "./PDFViewerModal.vue";
export default {
  name: "RecoApp",
  components: {
    PrescriptionModal,
    LabRequestModal,
    PDFViewerModal,
    FeedbackModal,
  },
  data() {
    return {
      //start video in minutes
      callMinutes: 0,
      callTimer: null,
      callDuration: "00:00:000", // New variable for formatted time
      startTime: null, // Store the exact start time
      showAudio: false,
      showVedio: false,
      showEndcall: false,
      showMic: false,
      showTooltip: false,
      showTooltipFeedback: false,
      Endcall: false,
      showUpward: false,
      showPrescription: false,
      showLab: false,
      ringingPhoneUrl: $("#broadcasting_url").val() + "/public/ringing.mp3",
      baseUrl: $("#broadcasting_url").val(),
      doctorUrl:
        $("#broadcasting_url").val() + "/resources/img/video/Doctor5.png",
      doctorUrl1:
        $("#broadcasting_url").val() + "/resources/img/video/Doctor6.png",
      declineUrl:
        $("#broadcasting_url").val() + "/resources/img/video/decline.png",
      videoCallUrl:
        $("#broadcasting_url").val() + "/resources/img/video/videocall.png",
      micUrl: $("#broadcasting_url").val() + "/resources/img/video/mic.png",
      dohLogoUrl:
        $("#broadcasting_url").val() + "/resources/img/video/doh-logo.png",
      tracking_id: this.getUrlVars()["id"],
      referral_code: this.getUrlVars()["code"],
      referring_md: this.getUrlVars()["referring_md"],
      activity_id: this.getUrlVars()["activity_id"],
      options: {
        // Pass your App ID here.
        appId: "0fc02f6b7ce04fbcb1991d71df2dbe0d",
        // Set the channel name.
        channel: this.getUrlVars()["code"],
        // Pass your temp token here.
        token: null,
        // Set the user ID.
        uid: 0,
      },

      form: {
        pregnant: {
          notes_diagnoses: "", // Set this to the value of $form['pregnant']->notes_diagnoses
          other_diagnoses: "", // Set this to the value of $form['pregnant']->other_diagnoses
          other_reason_referral: "", // Set this to the value of $form['pregnant']->other_reason_referral
        },
      },
      patient_age: "",
      file_path: [],
      file_name: [],
      icd: [],
      reason: {},
      formBaby: {},

      videoStreaming: true,
      audioStreaming: true,
      channelParameters: {
        // A variable to hold a local audio track.
        localAudioTrack: null,
        // A variable to hold a local video track.
        localVideoTrack: null,
        // A variable to hold a remote audio track.
        remoteAudioTrack: null,
        // A variable to hold a remote video track.
        remoteVideoTrack: null,
        // A variable to hold the remote user id.s
        remoteUid: null,
      },
      showDiv: false,
      prescription: "",
      prescriptionSubmitted: false,
      form_type: "pregnant",
      PdfUrl: "", // <-- Add this line

      loading: false,
      uploadProgress: 0,
      feedbackModalVisible: false,
      currentCode: "",
      feedbackUrl: "",
      baseUrlFeed: "",
      doctorfeedback: "",
      // netSpeedMbps: null,
      // netSpeedStatus: '', // 'fast' or 'slow'
    };
  },
  mounted() {
    document.title = "TELEMEDICINE";
      // Change favicon
      const link = document.querySelector("link[rel~='icon']");
      if (link) {
        link.href = this.dohLogoUrl; // Make sure logo.png is in your public folder
      } else {
        const newLink = document.createElement('link');
        newLink.rel = 'icon';
        newLink.href = this.dohLogoUrl;
        document.head.appendChild(newLink);
      }

     // Automatically start screen recording when the component is mounted
     if (this.referring_md === "yes") {
        this.startScreenRecording();
      }
    
    
    window.addEventListener('beforeunload', this.preventCloseWhileUploading);
    window.addEventListener('beforeunload', this.stopCallTimer);
    axios
      .get(
        `${this.baseUrl}/doctor/referral/video/pregnant/form/${this.tracking_id}`
      )
      .then((res) => {
        const response = res.data;
        console.log("testing");
        console.log(response);
        this.form = response.form["pregnant"];
        this.formBaby = response.form["baby"];

        if (response.age_type === "y")
          this.patient_age = response.patient_age + " Years Old";
        else if (response.age_type === "m")
          this.patient_age = response.patient_age + " Months Old";

        this.icd = response.icd;
        console.log("testing\n" + this.icd);

        this.file_path = response.file_path;
        this.file_name = response.file_name;
        this.reason = response.reason;

        console.log(response);
      })
      .catch((error) => {
        console.log(error);
      });

    //this.hideDivAfterTimeout();
    window.addEventListener("click", this.showDivAgain);
  },
  beforeUnmount() {
    //this.clearTimeout();
    window.removeEventListener("click", this.showDivAgain);
    window.removeEventListener('beforeunload', this.preventCloseWhileUploading);
    this.stopCallTimer();
     // Remove event listener when component is destroyed
     window.removeEventListener('resize', this.handleResize);
  },
  props: ["user"],
  created() {
    let self = this;
    $(document).ready(function () {
      console.log("ready!");
      self.ringingPhoneFunc();
    });
    this.startBasicCall();
  },
  methods: {
    closeFeedbackModal() {
      this.feedbackModalVisible = false;
    },
    handleResize() {
    // Get current window dimensions
      const windowHeight = window.innerHeight;
      const windowWidth = window.innerWidth;
      const isLandscape = windowWidth > windowHeight;
      
      // Detect device type for more specific adjustments
      const isMobile = windowWidth < 768;
      const isTablet = windowWidth >= 768 && windowWidth < 1024;
      const isDesktop = windowWidth >= 1024;
      
      // Log resize information for debugging
      console.log(`Window resized: ${windowWidth}x${windowHeight}, ${isLandscape ? 'landscape' : 'portrait'}`);
      
      // Apply layout adjustments based on screen size
      if (isMobile) {
        // Mobile specific adjustments
        this.applyMobileLayout(isLandscape);
      } else if (isTablet) {
        // Tablet specific adjustments
        this.applyTabletLayout(isLandscape);
      } else {
        // Desktop specific adjustments
        this.applyDesktopLayout();
      }
      
      // Adjust video and container sizes
      this.adjustVideoSize();
      
      // Ensure draggable element stays within bounds after resize
      this.enforceContainerBounds();
      
      // Recalculate any dynamic UI elements
      this.updateUIElementsPositions();
    },
  enforceContainerBounds() {
    // Ensure draggable element stays within bounds after resize
    if (this.draggableDiv) {
      const containerRect = document.querySelector('.mainPic').getBoundingClientRect();
      const draggableRect = this.draggableDiv.getBoundingClientRect();
      
      // Check if draggable element is outside bounds
      if (this.xOffset > containerRect.width - draggableRect.width) {
        this.xOffset = containerRect.width - draggableRect.width;
      }
      
      if (this.yOffset > containerRect.height - draggableRect.height) {
        this.yOffset = containerRect.height - draggableRect.height;
      }
      
      // Apply corrected position
      this.setTranslate(this.xOffset, this.yOffset, this.draggableDiv);
    }
  },
    adjustVideoSize() {
      // Example: Get window dimensions and adjust component sizes
      const windowHeight = window.innerHeight;
      const windowWidth = window.innerWidth;
      
      // You can use these dimensions to dynamically set sizes
      // This is an example - adjust based on your specific needs
      const remoteVideo = document.querySelector('.remotePlayerDiv');
      if (remoteVideo) {
        // Example adjustment - customize as needed
        const isLandscape = windowWidth > windowHeight;
        if (isLandscape && windowWidth < 1200) {
          // Adjust remote video container
          if (remoteVideo) {
            remoteVideo.style.height = 'auto';
            remoteVideo.style.maxHeight = '60vh';
          }
          
          // Adjust the tooltip container positioning
          const tooltipContainer = document.querySelector('.tooltip-container');
          if (tooltipContainer) {
            tooltipContainer.style.bottom = '5px';
            tooltipContainer.style.left = '50%';
            tooltipContainer.style.transform = 'translateX(-50%)';
          }
          
          // Make buttons in the control panel smaller
          const buttons = document.querySelectorAll('.iconCall button');
          buttons.forEach(button => {
            button.classList.remove('btn-md');
            button.classList.add('btn-sm');
          });
          
          // Adjust local video position and size
          const localVideo = document.getElementById('draggable-div');
          if (localVideo) {
            localVideo.style.width = '20%'; // Smaller width
            localVideo.style.maxWidth = '120px';
            
            // Reposition to a good default spot
            this.xOffset = windowWidth * 0.75;
            this.yOffset = windowHeight * 0.15;
            this.setTranslate(this.xOffset, this.yOffset, localVideo);
          }
          
          // Adjust form layout
          const formContainer = document.querySelector('.form-container');
          if (formContainer) {
            formContainer.style.width = '100%';
            formContainer.style.maxHeight = '50vh';
          }
          
          // Make the main container flex direction change for better display
          const mainContainer = document.querySelector('.main-container');
          if (mainContainer) {
            mainContainer.style.flexDirection = 'column';
          }
          
          // Adjust form text to be more readable on small screens
          const formDetails = document.querySelectorAll('.forDetails, .caseforDetails, .recoSummary, .mdHcw');
          formDetails.forEach(element => {
            element.style.fontSize = '0.9rem';
          });
        }
      }
    },
    async startScreenRecording() {

      try {
        // Check for browser compatibility
        const isSupported = !!navigator.mediaDevices.getDisplayMedia && !!navigator.mediaDevices.getUserMedia;
        if (!isSupported) {
          Lobibox.alert("error", {
            msg: "Your browser does not support screen recording with microphone audio. Please use the latest version of Chrome, Edge, or Firefox.",
            closeButton: false,
          });
          return;
        }

        // Inform the user about permissions
        console.log("Requesting permissions for screen and microphone...");

        // Request screen capture with system audio
        const screenStream = await navigator.mediaDevices.getDisplayMedia({
          video: true,
          audio: true, // Request system audio
        });

        console.log("Screen stream obtained:", screenStream);

        // Request microphone access
        const micStream = await navigator.mediaDevices.getUserMedia({
          audio: {
            echoCancellation: true, // Reduce echo
            noiseSuppression: true, // Reduce background noise
            sampleRate: 44100,      // Set sample rate for better quality
          },
        });

        console.log("Microphone stream obtained:", micStream);

        // Debugging: Log audio tracks from microphone
        micStream.getAudioTracks().forEach((track) => {
          console.log("Microphone track:", track);
        });

        // Create an AudioContext for mixing audio
        const audioContext = new AudioContext();
        const destination = audioContext.createMediaStreamDestination();

        // Connect system audio to the AudioContext
        if (screenStream.getAudioTracks().length > 0) {
          const systemAudioSource = audioContext.createMediaStreamSource(screenStream);
          systemAudioSource.connect(destination);
        } else {
          console.warn("No system audio track found in screen stream.");
        }

        // Connect microphone audio to the AudioContext
        if (micStream.getAudioTracks().length > 0) {
          const micAudioSource = audioContext.createMediaStreamSource(micStream);
          micAudioSource.connect(destination);
        } else {
          console.warn("No microphone audio track found.");
        }

        // Combine video from screenStream and mixed audio
        const combinedStream = new MediaStream([
          ...screenStream.getVideoTracks(),  // Desktop video
          ...destination.stream.getAudioTracks(), // Mixed audio (system + microphone)
        ]);

        console.log("Combined stream created:", combinedStream);

        // Initialize MediaRecorder with the combined stream
        this.screenRecorder = new MediaRecorder(combinedStream, {
          mimeType: "video/webm; codecs=vp8", // WebM format
        });
        this.recordedChunks = [];

        // Collect recorded data
        this.screenRecorder.ondataavailable = (event) => {
          if (event.data.size > 0) {
            this.recordedChunks.push(event.data);
          }
        };

        // Debugging: Monitor video and audio tracks for lag
        combinedStream.getTracks().forEach((track) => {
          console.log(`Track kind: ${track.kind}, readyState: ${track.readyState}`);
          track.onended = () => console.log(`Track ended: ${track.kind}`);
        });

        // Start recording
        this.screenRecorder.start();
        //for minutes timer
        // this.startCallTimer();
        console.log("Screen recording started with desktop and microphone audio.");
      } catch (error) {
        console.error("Error starting screen recording:", error);

        // Handle permission denial or other errors
        if (error.name === "NotAllowedError") {
          Lobibox.alert("error", {
            msg: "Screen recording permissions were denied. Please allow access to your screen and microphone.",
            closeButton: false,
            callback: function () {
              window.top.close();
            },
          });
        } else if (error.name === "NotFoundError") {
          Lobibox.alert("error", {
            msg: "No screen or microphone devices found. Please ensure your devices are connected and try again.",
            closeButton: false,
            callback: function () {
              window.top.close();
            },
          });
        } else {
          Lobibox.alert("error", {
            msg: "An unexpected error occurred while starting screen recording. Please try again.",
            closeButton: false,
            callback: function () {
              window.top.close();
            },
          });
        }
      }
    },
    async saveScreenRecording(closeAfterUpload = false) {
      if (this.recordedChunks.length > 0) {
        this.loading = true; // Show loader

        // Convert recorded chunks to a Blob
        const blob = new Blob(this.recordedChunks, { type: "video/webm" });

        // --- Max file size check (2GB) ---
        const maxSize = 2 * 1024 * 1024 * 1024; // 2GB in bytes
        if (blob.size > maxSize) {
          this.loading = false;
          Lobibox.alert("error", {
            msg: "The recording is too large to upload (max 2GB). Please record a shorter session.",
          });
          return;
        }

        // Generate the filename
        const patientCode = this.form.code || "Unknown_Patient";
        const activityId = this.activity_id;
        const referring_md = this.form.referring_md;
        const referred = this.form.action_md;
        const currentDate = new Date();
        const dateSave = currentDate.toISOString().split("T")[0]; // Format: YYYY-MM-DD
        const timeStart = new Date(this.startTime).toLocaleTimeString("en-US", { hour12: false }).replace(/:/g, "-");
        const timeEnd = currentDate.toLocaleTimeString("en-US", { hour12: false }).replace(/:/g, "-");

        const fileName = `${patientCode}_${activityId}_${referring_md}_${referred}_${dateSave}_${timeStart}_${timeEnd}.webm`;

        // Get facility name for folder (sanitize on server)
        const username = this.user.username || "UnknownUser";

        let chunkSize = 5 * 1024 * 1024; // Default to 5MB
        const totalChunks = Math.ceil(blob.size / chunkSize);

        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
          const start = chunkIndex * chunkSize;
          const end = Math.min(blob.size, start + chunkSize);
          const chunk = blob.slice(start, end);

          const formData = new FormData();
          formData.append("video", chunk, fileName);
          formData.append("fileName", fileName);
          formData.append("chunkIndex", chunkIndex);
          formData.append("totalChunks", totalChunks);
          formData.append("username", username); // <-- Add facility name

          try {
            await axios.post("https://telemedapi.cvchd7.com/api/save-screen-record", formData, {
              headers: { "Content-Type": "multipart/form-data" },
            });
            // Update progress after each chunk
            this.uploadProgress = Math.round(((chunkIndex + 1) / totalChunks) * 100);
          } catch (error) {
            this.loading = false;
            this.uploadProgress = 0; // Reset on error
            Lobibox.alert("error", {
              msg: `Failed to upload chunk ${chunkIndex + 1}/${totalChunks}: ` +
                (error.response?.data?.message || error.message),
            });
            return;
          }
        }

        this.uploadProgress = 100; // Ensure it's 100% at the end
        this.recordedChunks = []; // Clear recorded chunks to free memory
        this.loading = false; // Hide loader
        this.uploadProgress = 0; // Reset progress

        if (closeAfterUpload) {
          window.top.close();
        }
      } else {
        console.error("No recorded data available to save.");
      }
    },
    preventCloseWhileUploading(event) {
        if (this.loading) {
          event.preventDefault();
          event.returnValue = "File upload in progress. Please wait until it finishes.";
          return event.returnValue;
        }
    },
    startCallTimer() {
   // Store the start time in milliseconds
    this.startTime = Date.now();

    // Update the timer every 10 milliseconds
    this.callTimer = setInterval(() => {
      const elapsedTime = Date.now() - this.startTime;

      // Calculate minutes, seconds, and milliseconds
      const hours = Math.floor(elapsedTime / 3600000);
      const minutes = Math.floor((elapsedTime % 3600000) / 60000);
      const seconds = Math.floor((elapsedTime % 60000) / 1000);


      // Format the time as mm:ss:ms
    
      if(hours == 0){
        this.callDuration = `${String(minutes).padStart(1, "0")} : ${String(seconds).padStart(2, "0")} `;
      }else {
        this.callDuration = `${String(hours).padStart(1, "0")} : ${String(minutes).padStart(2, "0")} : ${String(seconds).padStart(2, "0")} `;
      }
      
    }, 10);
    },
  async startBasicCall() {
      const agoraEngine = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

      if (!this.channelParameters) {
        this.channelParameters = {};
      }
      this.channelParameters.userCount = 0;
      this.channelParameters.maxUsers = 2;

      const remotePlayerContainer = document.createElement("div");
      const localPlayerContainer = document.createElement("div");
      localPlayerContainer.id = this.options.uid;

      let self = this;

      agoraEngine.on("user-joined", async (user) => {
        self.channelParameters.userCount++;
        if (self.channelParameters.userCount > self.channelParameters.maxUsers) {
          self.showChannelFullMessage && self.showChannelFullMessage();
          await agoraEngine.leave();
          self.channelParameters.userCount--;
          return;
        }
      });

      agoraEngine.on("user-published", async (user, mediaType) => {
        if (self.channelParameters.userCount > self.channelParameters.maxUsers) {
          return;
        }
        await agoraEngine.subscribe(user, mediaType);

        if (mediaType === "video") {
          if (self.$refs && self.$refs.ringingPhone) {
            self.$refs.ringingPhone.pause();
          }
          self.channelParameters.remoteVideoTrack = user.videoTrack;
          self.channelParameters.remoteAudioTrack = user.audioTrack;
          self.channelParameters.remoteUid = user.uid.toString();
          remotePlayerContainer.id = user.uid.toString();

          document.body.append(remotePlayerContainer);
          document.querySelector(".remotePlayerDiv").innerHTML = "";
          document.querySelector(".remotePlayerDiv").append(remotePlayerContainer);
          remotePlayerContainer.classList.add("remotePlayerLayer");

          self.channelParameters.remoteVideoTrack.play(remotePlayerContainer);
        }

        if (mediaType === "audio") {
          self.channelParameters.remoteAudioTrack = user.audioTrack;
          self.channelParameters.remoteAudioTrack.play();
        }

        if (!self.callTimer) {
          self.startCallTimer && self.startCallTimer();
        }
      });

      agoraEngine.on("user-left", (user) => {
        self.channelParameters.userCount = Math.max(0, self.channelParameters.userCount - 1);
      });

      try {
        await agoraEngine.join(
          self.options.appId,
          self.options.channel,
          self.options.token,
          self.options.uid
        );

        self.channelParameters.localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
        self.channelParameters.localVideoTrack = await AgoraRTC.createCameraVideoTrack();

        document.body.append(localPlayerContainer);
        document.querySelector(".localPlayerDiv").innerHTML = "";
        document.querySelector(".localPlayerDiv").append(localPlayerContainer);
        localPlayerContainer.classList.add("localPlayerLayer");

        await agoraEngine.publish([
          self.channelParameters.localAudioTrack,
          self.channelParameters.localVideoTrack,
        ]);

        self.channelParameters.localVideoTrack.play(localPlayerContainer);

        window.onload = function () {
          self.joinVideo &&
            self.joinVideo(
              agoraEngine,
              self.channelParameters,
              localPlayerContainer,
              self
            );
        };
      } catch (error) {
        console.error("Error joining channel:", error);
      }
    },
    showChannelFullMessage() {
      alert("Channel is full! Maximum users reached.");
    },
    getUrlVars() {
      var vars = [],
        hash;
      var hashes = window.location.href
        .slice(window.location.href.indexOf("?") + 1)
        .split("&");
      for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split("=");
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
      }
      return vars;
    },
    async joinVideo(
      agoraEngine,
      channelParameters,
      localPlayerContainer,
      self
    ) {
      console.log("local");
      // Join a channel.
      await agoraEngine.join(
        self.options.appId,
        self.options.channel,
        self.options.token,
        self.options.uid
      );
      // Create a local audio track from the audio sampled by a microphone.
      channelParameters.localAudioTrack =
        await AgoraRTC.createMicrophoneAudioTrack();
      // Create a local video track from the video captured by a camera.
      channelParameters.localVideoTrack =
        await AgoraRTC.createCameraVideoTrack();
      // Append the local video container to the page body.
      document.body.append(localPlayerContainer);
      $(".localPlayerDiv").html(localPlayerContainer);
      $(localPlayerContainer).addClass("localPlayerLayer");
      // Publish the local audio and video tracks in the channel.
      await agoraEngine.publish([
        channelParameters.localAudioTrack,
        channelParameters.localVideoTrack,
      ]);
      // Play the local video track.
      channelParameters.localVideoTrack.play(localPlayerContainer);
      console.log("publish success!");
    },
   async sendCallDuration() {
      if (this.isLeavingChannel) return; // Prevent duplicate sends
      this.isLeavingChannel = true;

      // Parse callDuration string (supports "mm : ss" or "hh : mm : ss")
      let duration = this.callDuration.replace(/\s/g, ''); // Remove spaces
      let parts = duration.split(':').map(Number);
      let totalMinutes = 0;

      if (parts.length === 2) {
        // Format: mm:ss
        totalMinutes = parts[0];
        if (parts[1] >= 30) totalMinutes += 1; // round up if 30+ seconds
      } else if (parts.length === 3) {
        // Format: hh:mm:ss
        totalMinutes = (parts[0] * 60) + parts[1];
        if (parts[2] >= 30) totalMinutes += 1; // round up if 30+ seconds
      }

      // Ensure integer and at least 1 minute if any call happened
      totalMinutes = Math.max(1, parseInt(totalMinutes, 10));

      try {
        const response = await axios.post(`${this.baseUrl}/save-call-duration`, {
          call_duration: totalMinutes, // send as int(11)
          tracking_id: this.tracking_id,
          referral_code: this.referral_code
        });

        console.log("Call duration saved (minutes):", totalMinutes, response.data);
        localStorage.removeItem('callStartTime'); // Clean up
        return true;
      } catch (error) {
        console.error("Error saving call duration:", error);
        return false;
      }
    },
    async leaveChannel() {
      // if (confirm("Are you sure you want to leave this channel?")) {
        
      //   // Wait for duration to be sent before closing
      //   if(this.referring_md == 'yes'){
      //     clearInterval(this.callTimer); // Stop the timer
      //     await this.sendCallDuration();

      //     // Give more time for the request to complete
      //     setTimeout(() => {
      //       window.top.close();
      //     }, 2000);
      //   }
      //     window.top.close();

      // }

      if (confirm("Are you sure you want to leave this channel?")) {
            // Stop screen recording and save the file
            if (this.screenRecorder && this.screenRecorder.state !== "inactive") {
                this.screenRecorder.stop();
                this.screenRecorder.onstop = () => {
                    this.saveScreenRecording(true);
                };
            }

            // Wait for duration to be sent before closing
            if (this.referring_md === "yes") {
                clearInterval(this.callTimer); // Stop the timer
                await this.sendCallDuration();

                // // Give more time for the request to complete
                // setTimeout(() => {
                //     window.top.close();
                // }, 10000);
            } else {
                window.top.close();
            }
        }
    },
   stopCallTimer() {
    if (this.callTimer) {
      clearInterval(this.callTimer);
    }
    },
    videoStreamingOnAndOff() {
      this.videoStreaming = this.videoStreaming ? false : true;
      this.channelParameters.localVideoTrack.setEnabled(this.videoStreaming);
    },
    audioStreamingOnAnddOff() {
      this.audioStreaming = this.audioStreaming ? false : true;
      this.channelParameters.localAudioTrack.setEnabled(this.audioStreaming);
    },
    // hideDivAfterTimeout() {
    //     setTimeout(() => {
    //         $(".iconCall").removeClass("fade-in");
    //         this.showDiv = false;
    //     }, 10000);
    // },
    showDivAgain() {
      this.showDiv = true;
      //this.hideDivAfterTimeout();
    },
    clearTimeout() {
      // Clear the timeout if the component is about to be unmounted
      // to prevent memory leaks
      clearTimeout(this.timeoutId);
    },
    async ringingPhoneFunc() {
      await this.$refs.ringingPhone.play();
      let self = this;
      setTimeout(function () {
        console.log("pause");
        self.$refs.ringingPhone.pause();
      }, 60000);
    },
    submitPrescription() {
      if (this.prescription) {
        const updatePrescription = {
          code: this.referral_code,
          prescription: this.prescription,
          form_type: this.form_type,
        };
        axios
          .post(
            `${this.baseUrl}/api/video/prescription/update`,
            updatePrescription
          )
          .then((response) => {
            console.log(response);
            if (response.data === "success") {
              this.prescriptionSubmitted = true;
              Lobibox.alert("success", {
                msg: "Successfully submitted prescription!",
              });
            } else {
              Lobibox.alert("error", {
                msg: "Error in server!",
              });
            }
          });
      } else {
        Lobibox.alert("error", {
          msg: "No prescription inputted!",
        });
      }
    },
    generatePrescription() {
    const getPrescription = {
      code: this.referral_code,
      form_type: this.form_type,
      tracking_id: this.tracking_id,
    };

    axios
        .post(`${this.baseUrl}/api/video/prescription/check`, getPrescription)
        .then((response) => {
          if (response.data.status === "success") {
            const prescribedActivityId = response.data.prescriptions[0].prescribed_activity_id;

            // Set the PDF URL
            this.PdfUrl = `${this.baseUrl}/doctor/print/prescription/${this.tracking_id}/${prescribedActivityId}`;

            // Show the modal using the ref method
            this.$nextTick(() => {
              this.$refs.pdfViewer.openModal();
            });
          } else {
            Lobibox.alert("error", {
              msg: "No added prescription!",
            });
          }
        })
        .catch((error) => {
          console.error(error);
        });
    },
    generateLabrequest() {
       const url = `${this.baseUrl}/api/check/labresult`;
        const payload = {
          activity_id: this.activity_id 
        };

      axios
        .post(url, payload)
        .then((response) => {
          if (response.data.id) {
            const pdfUrl = `${this.baseUrl}/doctor/print/labresult/${this.activity_id}`;
            
            // Set the PDF URL for the modal
            this.PdfUrl = pdfUrl;

            // Show the PDF in the custom modal
            this.$nextTick(() => {
              this.$refs.pdfViewer.openModal();
            });
          } else {
            Lobibox.alert("error", {
              msg: "No lab request has been created by the referred doctor",
            });
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    endorseUpward() {
      let self = this;
      Lobibox.confirm({
        msg: "Do you want to endorse this patient for an upward level of referral?",
        callback: function ($this, type, ev) {
          if (type == "yes") {
            const endorseUpward = {
              code: self.referral_code,
              form_type: self.form_type,
            };
            axios
              .post(`${self.baseUrl}/api/video/upward`, endorseUpward)
              .then((response) => {
                console.log(response.data);
                var successData = response.data.trim();
                if (successData === "success") {
                  Lobibox.alert("success", {
                    msg: "Successfully endorse the patient for upward referral!",
                  });
                } else {
                  Lobibox.alert("error", {
                    msg: "Error in server!",
                  });
                }
              });
          }
        },
      });
    },
  },
};

document.addEventListener("DOMContentLoaded", function () {
    const draggableDiv = document.getElementById("draggable-div");
    const mainPic = document.querySelector(".mainPic");

    if (!draggableDiv) {
        console.error("❌ Error: Draggable element not found!");
        return;
    }

    let isDragging = false;
    let offsetX = 0, offsetY = 0;

    // Position the draggable div in the bottom-right corner initially
    positionInBottomRight();

    // Mouse events for desktop
    draggableDiv.addEventListener("mousedown", startDrag);
    document.addEventListener("mousemove", drag);
    document.addEventListener("mouseup", endDrag);

    // Touch events for mobile
    draggableDiv.addEventListener("touchstart", startDragTouch);
    document.addEventListener("touchmove", dragTouch);
    document.addEventListener("touchend", endDrag);

    function positionInBottomRight() {
        const containerBounds = mainPic ? mainPic.getBoundingClientRect() : document.body.getBoundingClientRect();
        const padding = 20;

        draggableDiv.style.position = "absolute";
        draggableDiv.style.left = `${containerBounds.right - draggableDiv.offsetWidth - padding}px`;
        draggableDiv.style.top = `${containerBounds.bottom - draggableDiv.offsetHeight - padding}px`;
    }

    function startDrag(event) {
        isDragging = true;
        const rect = draggableDiv.getBoundingClientRect();
        offsetX = event.clientX - rect.left;
        offsetY = event.clientY - rect.top;
        draggableDiv.style.cursor = "grabbing";
    }

    function startDragTouch(event) {
        if (event.touches.length !== 1) return;
        isDragging = true;
        const touch = event.touches[0];
        const rect = draggableDiv.getBoundingClientRect();
        offsetX = touch.clientX - rect.left;
        offsetY = touch.clientY - rect.top;
    }

    function drag(event) {
        if (!isDragging) return;
        event.preventDefault();
        moveElement(event.clientX, event.clientY);
    }

    function dragTouch(event) {
        if (!isDragging || event.touches.length !== 1) return;
        event.preventDefault();
        const touch = event.touches[0];
        moveElement(touch.clientX, touch.clientY);
    }

    function moveElement(clientX, clientY) {
        const containerBounds = mainPic ? mainPic.getBoundingClientRect() : document.body.getBoundingClientRect();
        const newX = Math.min(
            Math.max(clientX - offsetX, containerBounds.left),
            containerBounds.right - draggableDiv.offsetWidth
        );
        const newY = Math.min(
            Math.max(clientY - offsetY, containerBounds.top),
            containerBounds.bottom - draggableDiv.offsetHeight
        );

        draggableDiv.style.left = `${newX}px`;
        draggableDiv.style.top = `${newY}px`;
    }

    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        draggableDiv.style.cursor = "grab";
    }

    // Ensure the draggable div stays within bounds on window resize
    window.addEventListener("resize", positionInBottomRight);
});
</script>
<template>
    <div v-if="loading" class="loader-overlay">
    <div class="loader" style="margin-right: 20px"></div>
    <div style="width: 300px; margin-top: 20px;">
      <div style="background: #444; border-radius: 8px; overflow: hidden;">
        <div
          :style="{
            width: uploadProgress + '%',
            background: '#4caf50',
            height: '18px',
            transition: 'width 0.3s'
          }"
        >
      </div>
      </div>
      <p style="color: white; text-align: center; margin: 5px 0 0 0;">
        Please wait until upload is complete.<br>Do not close this window. {{ uploadProgress }}%
      </p>
    </div>
  </div> 
    <!-- <div
      v-if="netSpeedMbps"
      class="net-speed-indicator"
      :class="netSpeedStatus"
    >
      <span>
        {{ netSpeedMbps }} Mbps
        <span v-if="netSpeedStatus === 'fast'">(Fast)</span>
        <span v-else>(Slow)</span>
      </span>
    </div> -->
  <audio ref="ringingPhone" :src="ringingPhoneUrl" loop></audio>
<div class="fullscreen-div">
    <div class="main-container">
      <div class="video-container">
        <div class="mainPic">
          <div class="remotePlayerDiv">
            <div id="calling">
              <h3>Calling...</h3>
            </div>
            <img :src="doctorUrl" class="remote-img" alt="Image1" />
          </div>
          <div class="call-duration">
            <span id="call-timer">{{ callDuration }}</span>
          </div>
          <Transition name="fade">
            <div class="iconCall position-absolute fade-in" v-if="showDiv">
              <div class="button-container">
                  <div
                    v-if="showMic"
                    class="tooltip-text"
                    style="background-color: #138496"
                  >
                    Audio
                  </div>
                  <button
                    class="btn btn-info btn-md mic-button"
                    :class="{ 'mic-button-slash': !audioStreaming }"
                    @click="audioStreamingOnAnddOff"
                    type="button"
                    @mouseover="showMic = true"
                    @mouseleave="showMic = false"
                  >
                    <i class="bi-mic-fill"></i>
                  </button>
                </div>
                &nbsp;
                <div class="button-container">
                  <div
                    v-if="showVedio"
                    class="tooltip-text"
                    style="background-color: #218838"
                  >
                    Video
                  </div>
                  <button
                    class="btn btn-success btn-md video-button"
                    :class="{ 'video-button-slash': !videoStreaming }"
                    @click="videoStreamingOnAndOff"
                    type="button"
                    @mouseover="showVedio = true"
                    @mouseleave="showVedio = false"
                  >
                    <i class="bi-camera-video-fill"></i>
                  </button>
                </div>
                &nbsp;
                <div class="button-container">
                  <div
                    v-if="showEndcall"
                    class="tooltip-text"
                    style="background-color: #c82333"
                  >
                    End Call
                  </div>
                  <button
                    class="btn btn-danger btn-md decline-button"
                    @click="leaveChannel"
                    type="button"
                    @mouseover="showEndcall = true"
                    @mouseleave="showEndcall = false"
                    :disabled="loading"
                  >
                    <i class="bi-telephone-x-fill"></i>
                  </button>
                </div>
                &nbsp;
                <div class="button-container">
                  <div
                    v-if="showUpward"
                    class="tooltip-text"
                    style="background-color: #e0a800"
                  >
                    Upward
                  </div>
                  <button
                    class="btn btn-warning btn-md upward-button"
                    @click="endorseUpward"
                    type="button"
                    v-if="referring_md == 'no'"
                    @mouseover="showUpward = true"
                    @mouseleave="showUpward = false"
                  >
                    <i class="bi-hospital"></i>
                  </button>
                </div>
                <div class="button-container">
                  <div
                    v-if="showPrescription"
                    class="tooltip-text"
                    style="background-color: #218838"
                  >
                    Prescription
                  </div>
                  <button
                    class="btn btn-success btn-md prescription-button"
                    data-toggle="modal"
                    data-target="#prescriptionModal"
                    type="button"
                    v-if="referring_md == 'yes'"
                    @mouseover="showPrescription = true"
                    @mouseleave="showPrescription = false"
                  >
                    <i class="bi bi-prescription"></i>
                  </button>
                </div>
                <div class="button-container">
                  <div
                    v-if="showTooltip"
                    class="tooltip-text"
                    style="background-color: #007bff;"
                  >
                    Lab Request
                  </div>
                  <button
                    class="btn btn-primary btn-md prescription-button"
                    data-toggle="modal"
                    data-target="#labRequestModal"
                    type="button"
                    v-if="referring_md == 'yes'"
                    @mouseover="showTooltip = true"
                    @mouseleave="showTooltip = false"
                  >
                    <i class="bi bi-prescription2"></i>
                  </button>
                </div>
                
                 <div class="button-container">
                  <div
                    v-if="showTooltipFeedback"
                    class="tooltip-text"
                    style="background-color: #17a2b8;"
                  >
                    Chat
                  </div>
                  <button
                    class="btn btn-info btn-md reco-button"
                    data-toggle="modal"
                    data-target="#feedbackModal"
                    :data-code="referral_code"
                    onclick="viewReco($(this))"
                    @mouseover="showTooltipFeedback = true"
                    @mouseleave="showTooltipFeedback = false"
                  >
                    <i class="bi bi-chat-left-text"></i>
                  </button>
                </div>
            </div>
          </Transition>
             <div class="localPlayerDiv" id="draggable-div">
            <img :src="doctorUrl1" id="local-image" class="img2" alt="Image2" draggable="true"/>
          </div>
        </div>
      </div>
      <div class="form-container">
        <div class="telemedForm">
          <div class="form-scrollable">
            <div class="form-header-container">
              <img :src="dohLogoUrl" alt="Image3" class="dohLogo" />
              <div class="formHeader">
                  <div>
                  <p>Republic of the Philippines</p>
                  <p>DEPARTMENT OF HEALTH</p>
                  <p><b>CENTRAL VISAYAS CENTER for HEALTH DEVELOPMENT</b></p>
                  <p>Osmeña Boulevard Sambag II, Cebu City, 6000 Philippines</p>
                  <p>
                    Regional Director's Office Tel. No. (032) 253-6355 Fax No. (032)
                    254-0109
                  </p>
                  <p>
                    Official Website:
                    <span style="color: blue">http://www.ro7.doh.gov.ph</span> Email
                    Address: dohro7@gmail.com
                  </p>
                </div>
              </div>
              <div class="clinical">
                <span style="color: #4caf50"><b>BEmONC/CEmONC REFERRAL FORM</b></span>
              </div>
            </div>
         
            <div class="tableForm">
              <table class="table table-striped form-label formTable">
                <tbody>
                  <tr>
                    <th colspan="12">REFERRAL RECORD</th>
                  </tr>
                  <tr>
                    <td colspan="6">Who is referring</td>
                    <td colspan="6">
                      Record Number:
                      <span class="forDetails">{{ form.record_no }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="12">
                      Referred Date:
                      <span class="forDetails">{{ form.referred_date }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6">
                      Referring Name:
                      <span class="forDetails">{{ form.md_referring }}</span>
                    </td>
                    <td colspan="6">
                      Arrival Date:
                      <span class="forDetails">{{ form.arrival_date }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="12">
                      Contact # of Referring MD/HCW:
                      <span class="forDetails">{{
                        form.referring_md_contact
                      }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="12">
                      Referring Facility:
                      <span class="forDetails">{{
                        form.referring_facility
                      }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="12">
                      Facility Contact #:
                      <span class="forDetails">
                        {{ form.referring_contact }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="12">
                      Accompanied by the Health Worker:
                      <span class="forDetails">{{ form.health_worker }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6">
                      Referred to:
                      <span class="forDetails">{{ form.referred_facility }}</span>
                    </td>
                    <td colspan="6">
                      Department:
                      <span class="forDetails"> {{ form.department }} </span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="12">
                      Covid Number:
                      <span class="forDetails"> {{ form.covid_number }} </span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="12">
                      Clinical Status:
                      <span class="forDetails">
                        {{ form.refer_clinical_status }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="12">
                      Surviellance Category:
                      <span class="forDetails">
                        {{ form.refer_sur_category }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="row">
                <div class="col">
                  <table>
                    <tbody>
                      <tr class="bg-gray">
                        <td colspan="6" class="padded-header">
                          <strong>WOMAN</strong>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="3">
                          Name:
                          <span class="forDetails">{{ form.woman_name }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Age: <span class="forDetails">{{ form.woman_age }}</span
                          ><br /><small>(at time of referral)</small>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Address:
                          <br />
                          <span class="forDetails">{{
                            form.patient_address
                          }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Main Reason for Referral:
                          <br />
                          <span class="forDetails">{{ form.woman_reason }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Major Findings (Clinica and BP,Temp,Lab)
                          <br />
                          <span class="forDetails" style="white-space: pre-line">
                            {{ form.woman_major_findings }}
                          </span>
                        </td>
                      </tr>

                      <tr class="bg-gray">
                        <td colspan="6" class="padded-header">
                          <strong>Treatments Give Time</strong>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Before Referral:
                          <br />
                          <span class="forDetails">{{
                            form.woman_before_treatment
                          }}</span>
                          -
                          <span class="forDetails">{{
                            form.woman_before_given_time
                          }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          During Referral:
                          <br />
                          <span class="forDetails">{{
                            form.woman_during_transport
                          }}</span>
                          -
                          <span class="forDetails">{{
                            form.woman_transport_given_time
                          }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Information Given to the Woman and Companion About the
                          Reason for Referral
                          <br />
                          <span
                            class="forDetails"
                            style="white-space: pre-line"
                            >{{ form.woman_information_given }}</span
                          >
                        </td>
                      </tr>
                      <tr v-if="icd.length > 0" class="padded-row">
                        <td colspan="6">
                          ICD-10 Code and Description:
                          <li v-for="i in icd" :key="i.code">
                            <span class="forDetails"
                              >{{ i.code }} - {{ i.description }}</span
                            >
                          </li>
                        </td>
                      </tr>
                      <tr v-if="form.notes_diagnoses" class="padded-row">
                        <td colspan="6">
                          Diagnosis/Impression:
                          <br />
                          <span
                            class="forDetails"
                            style="white-space: pre-line"
                            >{{ form.notes_diagnoses }}</span
                          >
                        </td>
                      </tr>
                      <tr v-if="form.other_diagnoses" class="padded-row">
                        <td colspan="6">
                          Other Diagnoses:
                          <br />
                          <span
                            class="forDetails"
                            style="white-space: pre-line"
                            >{{ form.other_diagnoses }}</span
                          >
                        </td>
                      </tr>
                      <tr v-if="reason" class="padded-row">
                        <td colspan="6">
                          Reason for referral:
                          <br />
                          <span
                            class="forDetails"
                            style="white-space: pre-line"
                            >{{ reason.reason }}</span
                          >
                        </td>
                      </tr>
                      <tr v-if="form.other_reason_referral" class="padded-row">
                        <td colspan="6">
                          Reason for referral:
                          <br />
                          <span
                            class="forDetails"
                            style="white-space: pre-line"
                            >{{ form.other_reason_referral }}</span
                          >
                        </td>
                      </tr>
                      <tr v-if="file_path" class="padded-row">
                        <td colspan="6">
                          <span v-if="file_path.length > 1"
                            >File Attachments:
                          </span>
                          <span v-else>File Attachment: </span>
                          <br />
                          <span v-for="(path, index) in file_path" :key="index">
                            <a
                              :href="path"
                              :key="index"
                              id="file_download"
                              class="reason"
                              target="_blank"
                              download
                              >{{ file_name[index] }}</a
                            >
                            <span v-if="index + 1 !== file_path.length"
                              >,&nbsp;</span
                            >
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col">
                  <table>
                    <tbody>
                      <tr class="bg-gray">
                        <th colspan="6" class="padded-header">BABY</th>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Name:
                          <span class="forDetails">{{ formBaby.baby_name }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Date of Birth:
                          <span class="forDetails">{{ formBaby.baby_dob }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Birth Weight:
                          <span class="forDetails">{{ formBaby.weight }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Gestational Age:
                          <span class="forDetails">{{
                            formBaby.gestational_age
                          }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Main Reason for Referral:
                          <br />
                          <span class="forDetails">{{
                            formBaby.baby_reason
                          }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Major Findings (Clinical and BP,Temp,Lab)
                          <br />
                          <span
                            class="forDetails"
                            style="white-space: pre-line"
                            >{{ formBaby.baby_major_findings }}</span
                          >
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Last (Breast) Feed (Time):
                          <br />
                          <span class="forDetails">{{
                            formBaby.baby_last_feed
                          }}</span>
                        </td>
                      </tr>
                      <tr class="bg-gray">
                        <td colspan="6" class="padded-header">
                          <strong>Treatments Give Time</strong>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Before Referral:
                          <br />
                          <span class="forDetails">{{
                            formBaby.baby_before_treatment
                          }}</span>
                          -
                          <span class="forDetails">{{
                            formBaby.baby_before_given_time
                          }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          During Transport:
                          <br />
                          <span class="forDetails">{{
                            formBaby.baby_during_transport
                          }}</span>
                          -
                          <span class="forDetails">{{
                            formBaby.baby_transport_given_time
                          }}</span>
                        </td>
                      </tr>
                      <tr class="padded-row">
                        <td colspan="6">
                          Information Given to the Woman and Companion About the
                          Reason for Referral
                          <br />
                          <span
                            class="forDetails"
                            style="white-space: pre-line"
                            >{{ formBaby.baby_information_given }}</span
                          >
                        </td>
                      </tr>
                    </tbody>
                  </table>  
                  <!-- ======================================================================= -->
                </div>
              </div>
                 <div class="row g-0">
                    <div class="col-6">
                        <button class="btn btn-success btn-md w-100 ml-2"  type="button" @click="generatePrescription()">
                            <i class="bi bi-prescription"></i> Generate Prescription
                        </button>
                    </div>
                    <div class="col-6">
                    <button class="btn btn-primary btn-md w-100" type="button" @click="generateLabrequest()" 
                        style="background-color: #0d6efd; border-color: #0d6efd; box-shadow: none; pointer-events: auto;"
                        onmouseover="this.style.backgroundColor='#0d6efd'; this.style.borderColor='#0d6efd';"
                        onmouseout="this.style.backgroundColor='#0d6efd'; this.style.borderColor='#0d6efd';">
                        <i class="bi bi-clipboard2-pulse"></i> Generate Lab Request
                    </button>
                    </div>
                </div>
              <!-- ======================================================================= -->
           </div>
          </div>
        </div>
      </div>
    </div>
     <PrescriptionModal
      :activity_id="parseInt(activity_id)"
      :baseUrl="baseUrl"
      :code="referral_code"
      :form_type="form_type"
    />
    <LabRequestModal
      :activity_id="parseInt(activity_id)"
      :requested_by="parseInt(user.id)"
    />
    
    <FeedbackModal
      :isVisible="feedbackModalVisible"
      :code="currentCode"
      :userId="user.id"
      :fetchUrl="feedbackUrl"
      :imageUrl="baseUrlFeed"
      :postUrl="doctorfeedback"
      @close-modal="closeFeedbackModal"
    />
    <PDFViewerModal ref="pdfViewer" :pdfUrl="PdfUrl" />
  </div>
</template>

<style scoped>
@import "./css/index.css";

td {
 padding:5px; 
}

/* Fullscreen layout */
.fullscreen-div {
  width: 100vw;
  height: 100vh;
  overflow: hidden; /* Prevent scrolling */
  position: fixed; /* Keep it fixed in the viewport */
  top: 0;
  left: 0;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Main container layout */
.main-container {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: row;
  overflow: hidden;
}

/* Video container (left side) */
.video-container {
  flex: 1.4;
  height: 100%;
  min-width: 0;
  position: relative;
  overflow: hidden;
}

/* Form container (right side) */
.form-container {
  flex: 1;
  padding: 5px;
  height: 100%;
  min-width: 0;
  position: relative;
  overflow: hidden;
}

/* Responsive layout for smaller screens */
@media (max-width: 992px) {
  .main-container {
    flex-direction: column;
  }
  
  .video-container, .form-container {
    width: 100%;
    flex: none;
  }
  
  .video-container {
    height: 50%;
  }
  
  .form-container {
    height: 50%;
  }
}

.remote-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.form-scrollable {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  background: white;
  padding-left: 10px;
  padding-right: 10px;
  padding-bottom: 10px;
  
}

.form-header-container {
  position: sticky;
  background-color: #fff;
  z-index: 2;
  padding-bottom: 2px;
}


.clinical {
  text-align: center;
  margin: 10px 0;
  font-size: 1.2rem;
}

.formTable {
  width: 100%;
  font-size: 0.85rem;
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

#draggable-div {
    width: 195px;
    height: 200px;
    min-width: 150px;
    min-height: 200px;
    max-width: 150px;
    max-height: 200px;
}

.loader-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.loader {
  border: 8px solid #f3f3f3;
  border-top: 8px solid #3498db;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

#call-timer {
    font-size: 16px;
    background: rgba(0, 0, 0, 0.425);
    color: #fff;
    padding: 4px 10px;
    border-radius: 5px;
    letter-spacing: 2px;
}

.call-duration {
  position: absolute;
  top: 20px;
  left: 20px;
  z-index: 10;
}

.net-speed-indicator {
  position: fixed;
  right: 20px;
  bottom: 20px;
  z-index: 10000;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 1rem;
  background: rgba(15, 15, 15, 0.103);
  color: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  pointer-events: none;
}
.net-speed-indicator.fast {
  border: 2px solid #4caf50;
  color: #4caf50;
}
.net-speed-indicator.slow {
  border: 2px solid #e53935;
  color: #e53935;
}
</style>