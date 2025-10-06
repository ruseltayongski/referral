<script>
import axios from "axios";
import { Transition } from "vue";
import AgoraRTC from "agora-rtc-sdk-ng";
import PrescriptionModal from "./PrescriptionModal.vue";
import LabRequestModal from "./LabRequestModal.vue";
import FeedbackModal from "./FeedbackModal.vue";
import PDFViewerModal from "./PDFViewerModal.vue";
import FormReferralComponent from "./FormReferralComponent.vue";

let baseUrlfeedback = `referral/doctor/vue/feedback`;
let doctorFeedback = `referral/doctor/feedback`;
export default {
  name: "RecoApp",
  components: {
    PrescriptionModal,
    LabRequestModal,
    FeedbackModal,
    PDFViewerModal,
    FormReferralComponent,
  },
  data() {
    return {
      // agoraEngine: null,
      isMobileDevice: false,
      showCameraSwitch: true,
      currentCameraId: null,
      availableCameras: [],
      //start video in minutes
      callMinutes: 0,
      callTimer: null,
      callDuration: "00:00:000", // New variable for formatted time
      startTime: null, // Store the exact start time
      isLeavingChannel: false,
      isUserJoined: false,
      telemedicine: null,
      form_version: null,
      //feedback
      feedbackUrl: baseUrlfeedback,
      doctorfeedback: doctorFeedback,
      feedbackModalVisible: false,
      currentCode: null,
      ImageUrl: "",
      baseUrlFeed: null,
      //end  for feedback
      showTooltipFeedback: false,
      showTooltip: false,
      showPrescription: false,
      showUpward: false,
      showEndcall: false,
      showVedio: false,
      showMic: false,
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
      },

      form: {},
      patient_age: "",
      file_path: [],
      file_name: [],
      icd: [],

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
      form_type: "normal",
      reco_count: $("#reco_count_val").val(),

      PdfUrl: "",

      loading: false,
      uploadProgress: 0,
      // netSpeedMbps: null,
      // netSpeedStatus: '', // 'fast' or 'slow'

      afkTimeout: null,
      afkDialogVisible: false,
      afkCountdown: 10,
      afkCountdownInterval: null,
      current_medication: null,

      past_medical_history: null,
      personal_and_social_history: null,
      pertinent_laboratory: null,
      review_of_system: null,
      nutritional_status: null,
      latest_vital_signs: null,
      glasgocoma_scale: null,
      obstetric_and_gynecologic_history: null,
      pregnancy: null,
    };
  },
  mounted() {
    window.addEventListener("keydown", this.feedbackKeydown);
    document.title = "TELEMEDICINE";
    // Change favicon
    const link = document.querySelector("link[rel~='icon']");
    this.initAfkDetection();
    if (link) {
      link.href = this.dohLogoUrl; // Make sure logo.png is in your public folder
    } else {
      const newLink = document.createElement("link");
      newLink.rel = "icon";
      newLink.href = this.dohLogoUrl;
      document.head.appendChild(newLink);
    }

    window.addEventListener("beforeunload", this.preventCloseWhileUploading);
    window.addEventListener("beforeunload", this.stopCallTimer);
    // Initialize draggable div logic
    this.$nextTick(() => {
      this.initDraggableDiv();
    });
    axios
      .get(`${this.baseUrl}/video/normal/newform/${this.tracking_id}`)
      .then((res) => {
        const response = res.data;
        if (response.success) {
          this.form_version = response.form_type;
          // console.log("Form type:", this.form_version);

          if (this.form_version === "version1") {
            axios
              .get(
                `${this.baseUrl}/doctor/referral/video/normal/form/${this.tracking_id}`
              )
              .then((res) => {
                const response = res.data;
                this.telemedicine = response.form.telemedicine;
                this.form = response.form;
                if (response.age_type === "y")
                  this.patient_age = response.patient_age + " Years Old";
                else if (response.age_type === "m")
                  this.patient_age = response.patient_age + " Months Old";

                this.icd = response.icd;
                // console.log("testing\n" + this.icd);

                this.file_path = response.file_path;
                this.file_name = response.file_name;

                // console.log(response);
              })
              .catch((error) => {
                console.log(error);
              });
          } else if (this.form_version === "version2") {
            axios
              .get(
                `${this.baseUrl}/video/normal/newform/data/${this.tracking_id}`
              )
              .then((res) => {
                const response = res.data;
                this.telemedicine = response.form.telemedicine;
                this.form = response.form;
                this.current_medication =
                  response.personal_and_social_history.current_medications;
                this.past_medical_history = response.past_medical_history;
                this.personal_and_social_history =
                  response.personal_and_social_history;
                this.pertinent_laboratory = response.pertinent_laboratory;
                this.review_of_system = response.review_of_system;
                this.nutritional_status = response.nutritional_status;
                this.latest_vital_signs = response.latest_vital_signs;
                this.glasgocoma_scale = response.glasgocoma_scale;
                this.obstetric_and_gynecologic_history =
                  response.obstetric_and_gynecologic_history;
                this.pregnancy = response.pregnancy;

                if (response.age_type === "y")
                  this.patient_age = response.patient_age + " Years Old";
                else if (response.age_type === "m")
                  this.patient_age = response.patient_age + " Months Old";

                console.log("Form response:", response);
                this.icd = response.icd;
                // console.log("testing\n" + this.icd);

                this.file_path = response.file_path;
                this.file_name = response.file_name;
              });
          }
        }
      })
      .catch((error) => {
        console.error(
          "Error fetching form type, defaulting to 'normal':",
          error
        );
      });

    //this.hideDivAfterTimeout();
    window.addEventListener("click", this.showDivAgain);

    //******************************************** start here */
    // Add window resize event listener
    // window.addEventListener("resize", this.handleResize);
    // Call once to set initial sizing
    // this.handleResize();
    // // Initialize camera devices
    this.getCameraDevices();
  },

  beforeUnmount() {
    window.removeEventListener("click", this.showDivAgain);
    window.removeEventListener("beforeunload", this.preventCloseWhileUploading);
    window.removeEventListener("keydown", this.feedbackKeydown);
    this.clearAfkTimers();
    this.stopCallTimer();
    // Remove event listener when component is destroyed
    // window.removeEventListener("resize", this.handleResize);
  },
  props: ["user"],
  created() {
    let self = this;
    $(document).ready(function () {
      self.ringingPhoneFunc();
    });
    this.startBasicCall();
    // setTimeout(() => {
    //   this.startRecording();
    // }, 2000);
    Echo.join("reco").listen("SocketReco", (event) => {
      $("#reco_count" + event.payload.code).html(event.payload.feedback_count);
      axios
        .get(
          $("#broadcasting_url").val() +
            "/activity/check/" +
            event.payload.code +
            "/" +
            this.user.facility_id
        )
        .then((response) => {
          if (
            response.data &&
            event.payload.sender_facility !== this.user.facility_id &&
            $("#archived_reco_page").val() !== "true"
          ) {
            this.reco_count++;
            $("#reco_count").html(this.reco_count);
            this.appendReco(
              event.payload.code,
              event.payload.name_sender,
              event.payload.facility_sender,
              event.payload.date_now,
              event.payload.message,
              event.payload.filepath
            );
            try {
              let objDiv = document.getElementById(event.payload.code);
              objDiv.scrollTop = objDiv.scrollHeight;
              if (!objDiv.scrollTop)
                this.notifyReco(
                  event.payload.code,
                  event.payload.feedback_count,
                  event.payload.redirect_track
                );
            } catch (err) {
              // console.log("modal not open");
              this.notifyReco(
                event.payload.code,
                event.payload.feedback_count,
                event.payload.redirect_track
              );
            }
          }
        });
    });
  },
  watch: {
    isUserJoined() {
      if (this.isUserJoined) {
        this.$refs.ringingPhone.pause();
        this.startCallTimer();
      }
    },
  },
  computed: {
    isMobile() {
      const isTabletSize =
        window.innerWidth >= 600 && window.innerWidth <= 1200;
      const isTabletUA =
        /Mobi|Android|iPhone|iPad|iPod|SM-T|Tablet|Tab|PlayBook|Silk|Kindle|Touch/i.test(
          navigator.userAgent
        );
      return isTabletUA || isTabletSize;
    },
  },
  methods: {
    // Mobile device detection removed to always show camera switch functionality

    async getCameraDevices() {
      try {
        // Get list of available video devices
        const devices = await AgoraRTC.getCameras();
        this.availableCameras = devices;
        // console.log('Available cameras:', devices); // Debug log

        if (devices.length > 0) {
          this.currentCameraId = devices[0].deviceId;
          this.showCameraSwitch = devices.length > 1; // Only show button if multiple cameras
          // console.log('Current camera ID:', this.currentCameraId);
        } else {
          console.warn("No cameras found");
          this.showCameraSwitch = false;
        }
      } catch (error) {
        console.error("Error getting cameras:", error);
        this.showCameraSwitch = false;
        Lobibox.alert("error", {
          msg: "Error accessing cameras. Please check your device settings.",
          closeButton: false,
        });
      }
    },

    switchCamera() {
      // console.log("Attempting to switch camera...");

      const track = this.channelParameters?.localVideoTrack;
      if (!track || track.isClosed) {
        Lobibox.alert("error", {
          msg: "Video track not initialized",
          closeButton: false,
        });
        return;
      }

      if (!this.availableCameras || this.availableCameras.length < 2) {
        Lobibox.alert("error", {
          msg: "Not enough cameras available",
          closeButton: false,
        });
        return;
      }

      // Find the next camera
      const currentIndex = this.availableCameras.findIndex(
        (camera) => camera.deviceId === this.currentCameraId
      );
      const nextIndex = (currentIndex + 1) % this.availableCameras.length;
      const nextCamera = this.availableCameras[nextIndex];

      // console.log("Switching to:", nextCamera.label || nextCamera.deviceId);

      // 🔑 Switch device on the SAME track
      track
        .setDevice(nextCamera.deviceId)
        .then(() => {
          // update current camera id
          this.currentCameraId = nextCamera.deviceId;
          // console.log("Camera switch successful (no republish needed)");

          // 🔑 re-play the track so the new camera feed shows immediately
          const container = document.getElementById(this.options.uid);
          if (container) {
            try {
              track.stop(); // stop old rendering
              track.play(container); // re-render new stream
            } catch (err) {
              console.warn("Replay failed:", err);
            }
          }
        })
        .catch((err) => {
          console.error("Camera switch failed:", err);
          Lobibox.alert("error", {
            msg: `Failed to switch camera: ${err.message}`,
            closeButton: false,
          });
        });
    },

    initDraggableDiv() {
      const draggableDiv = document.getElementById("draggable-div");
      const mainPic = document.querySelector(".mainPic");

      if (!draggableDiv) {
        console.error("❌ Error: Draggable element not found!");
        return;
      }

      let isDragging = false;
      let offsetX = 0,
        offsetY = 0;

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
        const containerBounds = mainPic
          ? mainPic.getBoundingClientRect()
          : document.body.getBoundingClientRect();
        const padding = 20;

        draggableDiv.style.position = "absolute";
        draggableDiv.style.left = `${
          containerBounds.right - draggableDiv.offsetWidth - padding
        }px`;
        draggableDiv.style.top = `${
          containerBounds.bottom - draggableDiv.offsetHeight - padding
        }px`;
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
        const containerBounds = mainPic
          ? mainPic.getBoundingClientRect()
          : document.body.getBoundingClientRect();
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
    },
    feedbackKeydown(e) {
      // Check if the modal from Blade is visible
      const previewModal = document.getElementById("filePreviewContentReco");

      if (previewModal && previewModal.classList.contains("show")) {
        if (e.key === "ArrowLeft") {
          const prev = document.getElementById("prevBtn");
          if (prev) prev.click(); // Trigger the Blade button click
        } else if (e.key === "ArrowRight") {
          const next = document.getElementById("nextBtn");
          if (next) next.click(); // Trigger the Blade button click
        } else if (e.key === "Escape") {
          $("#filePreviewContentReco").modal("hide"); // Close modal using jQuery
        }
      }
    },
    // AFK Detection Methods
    initAfkDetection() {
      // Listen for user activity
      const reset = this.resetAfkTimer;
      window.addEventListener("mousemove", reset);
      window.addEventListener("keydown", reset);
      window.addEventListener("mousedown", reset);
      window.addEventListener("touchstart", reset);
      this.resetAfkTimer();
    },
    resetAfkTimer() {
      // If dialog is open, close it and reset countdown
      if (this.afkDialogVisible) {
        this.closeAfkDialog();
      }
      clearTimeout(this.afkTimeout);
      this.afkTimeout = setTimeout(this.showAfkDialog, 2 * 60 * 1000); // 2 minutes
    },
    showAfkDialog() {
      this.afkDialogVisible = true;
      this.afkCountdown = 30;
      this.afkCountdownInterval = setInterval(() => {
        this.afkCountdown--;
        if (this.afkCountdown <= 0) {
          this.endCallAfk();
        }
      }, 1000);
    },
    closeAfkDialog() {
      this.afkDialogVisible = false;
      clearInterval(this.afkCountdownInterval);
      this.afkCountdown = 30;
      this.resetAfkTimer();
    },
    endCallAfk() {
      clearInterval(this.afkCountdownInterval);
      this.afkDialogVisible = false;
      // End the call (reuse your leaveChannel method)
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
        this.sendCallDuration();

        // // Give more time for the request to complete
        // setTimeout(() => {
        //     window.top.close();
        // }, 10000);
      } else {
        window.top.close();
      }
    },
    clearAfkTimers() {
      clearTimeout(this.afkTimeout);
      clearInterval(this.afkCountdownInterval);
      window.removeEventListener("mousemove", this.resetAfkTimer);
      window.removeEventListener("keydown", this.resetAfkTimer);
      window.removeEventListener("mousedown", this.resetAfkTimer);
      window.removeEventListener("touchstart", this.resetAfkTimer);
    },

    async startScreenRecording() {
      try {
        // Check for browser compatibility
        const isSupported =
          !!navigator.mediaDevices.getDisplayMedia &&
          !!navigator.mediaDevices.getUserMedia;
        if (!isSupported) {
          Lobibox.alert("warning", {
            msg: "Your browser does not support screen recording with microphone audio. Please use the latest version of Chrome, Edge, or Firefox.",
            closeButton: false,
          });
          return;
        }

        // Inform the user about permissions
        // console.log("Requesting permissions for screen and microphone...");

        // Request screen capture with system audio
        const screenStream = await navigator.mediaDevices.getDisplayMedia({
          video: true,
          audio: true, // Request system audio
        });

        // console.log("Screen stream obtained:", screenStream);

        // Request microphone access
        const micStream = await navigator.mediaDevices.getUserMedia({
          audio: {
            echoCancellation: true, // Reduce echo
            noiseSuppression: true, // Reduce background noise
            sampleRate: 44100, // Set sample rate for better quality
          },
        });

        // console.log("Microphone stream obtained:", micStream);

        // Debugging: Log audio tracks from microphone
        micStream.getAudioTracks().forEach((track) => {
          // console.log("Microphone track:", track);
        });

        // Create an AudioContext for mixing audio
        const audioContext = new AudioContext();
        const destination = audioContext.createMediaStreamDestination();

        // Connect system audio to the AudioContext
        if (screenStream.getAudioTracks().length > 0) {
          const systemAudioSource =
            audioContext.createMediaStreamSource(screenStream);
          systemAudioSource.connect(destination);
        } else {
          console.warn("No system audio track found in screen stream.");
        }

        // Connect microphone audio to the AudioContext
        if (micStream.getAudioTracks().length > 0) {
          const micAudioSource =
            audioContext.createMediaStreamSource(micStream);
          micAudioSource.connect(destination);
        } else {
          console.warn("No microphone audio track found.");
        }

        // Combine video from screenStream and mixed audio
        const combinedStream = new MediaStream([
          ...screenStream.getVideoTracks(), // Desktop video
          ...destination.stream.getAudioTracks(), // Mixed audio (system + microphone)
        ]);

        // console.log("Combined stream created:", combinedStream);

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
          // console.log(
          //   `Track kind: ${track.kind}, readyState: ${track.readyState}`
          // );
          track.onended = () => console.log(`Track ended: ${track.kind}`);
        });

        // Start recording
        this.screenRecorder.start();
        //for minutes timer
        //this.startCallTimer();
        // console.log(
        //   "Screen recording started with desktop and microphone audio."
        // );
      } catch (error) {
        console.error("Error starting screen recording:", error);

        // Handle permission denial or other errors
        if (error.name === "NotAllowedError") {
          Lobibox.alert("warning", {
            msg: "Screen recording permissions were denied. Please allow access to your screen and microphone.",
            closeButton: false,
          });
        } else if (error.name === "NotFoundError") {
          Lobibox.alert("warning", {
            msg: "Screen recording permissions were denied. Please allow access to your screen and microphone.",
            closeButton: false,
          });
        } else {
          Lobibox.alert("warning", {
            msg: "No microphone devices found. Please ensure your devices are connected and try again.",
            closeButton: false,
          });
        }
      }
    },
    preventCloseWhileUploading(event) {
      if (this.loading) {
        event.preventDefault();
        event.returnValue =
          "File upload in progress. Please wait until it finishes.";
        return event.returnValue;
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
        // const callDuration = this.callDuration.replace(/:/g, "-").replace(/\s+/g, "_");
        const currentDate = new Date();
        const dateSave = currentDate.toISOString().split("T")[0]; // Format: YYYY-MM-DD
        const timeStart = new Date(this.startTime)
          .toLocaleTimeString("en-US", { hour12: false })
          .replace(/:/g, "-");
        const timeEnd = currentDate
          .toLocaleTimeString("en-US", { hour12: false })
          .replace(/:/g, "-");

        const fileName = `${patientCode}_${activityId}_${referring_md}_${referred}_${dateSave}_${timeStart}_${timeEnd}.webm`;
        // Get facility name for folder (sanitize on server)
        const username = this.user.username || "UnknownUser";

        // --- Detect upload speed and set chunk size ---
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
            await axios.post(
              "https://telemedapi.cvchd7.com/api/save-screen-record",
              formData,
              {
                headers: { "Content-Type": "multipart/form-data" },
              }
            );
            // Update progress after each chunk
            this.uploadProgress = Math.round(
              ((chunkIndex + 1) / totalChunks) * 100
            );
          } catch (error) {
            this.loading = false;
            this.uploadProgress = 0; // Reset on error
            Lobibox.alert("error", {
              msg:
                `Failed to upload chunk ${chunkIndex + 1}/${totalChunks}: ` +
                (error.response?.data?.message || error.message),
              callback: function () {
                window.top.close();
              },
            });
            return;
          }
        }

        this.uploadProgress = 100; // Ensure it's 100% at the end
        this.recordedChunks = []; // Clear recorded chunks to free memory
        this.loading = false; // Hide loader
        this.uploadProgress = 0; // Reset progress

        if (closeAfterUpload) {
          Lobibox.alert("success", {
            msg: `Your conversation has been successfully recorded and uploaded.`,
            callback: function () {
              window.top.close();
            },
          });
        }
      } else {
        console.error("No recorded data available to save.");
      }
    },
    startRecording() {
      axios.post("https://telemedapi.cvchd7.com/api/recording/start", {
        channel: this.getUrlVars()["code"],
        uid: this.user.id.toString(),
      });
    },

    stopRecording() {
      axios.post("https://telemedapi.cvchd7.com/api/recording/stop", {
        channel: this.user.id.toString(),
      });
    },
    closeFeedbackModal() {
      this.feedbackModalVisible = false; // Hide the feedback modal
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

        if (hours == 0) {
          this.callDuration = `${String(minutes).padStart(1, "0")} : ${String(
            seconds
          ).padStart(2, "0")} `;
        } else {
          this.callDuration = `${String(hours).padStart(1, "0")} : ${String(
            minutes
          ).padStart(2, "0")} : ${String(seconds).padStart(2, "0")} `;
        }
      }, 10);
    },
    stopCallTimer() {
      if (this.callTimer) {
        clearInterval(this.callTimer);
      }
    },
    notifyReco(code, feedback_count, redirect_track) {
      let content =
        "<button class='btn btn-xs btn-info' onclick='viewReco($(this))' data-toggle='modal'\n" +
        "                               data-target='#feedbackModal'\n" +
        '                               data-code="' +
        code +
        '" ' +
        "                               >\n" +
        "                           <i class='fa fa-comments'></i> ReCo <span class='badge bg-blue' id=\"reco_count" +
        code +
        '">' +
        feedback_count +
        "</span>\n" +
        '                       </button><a href="' +
        redirect_track +
        "\" class='btn btn-xs btn-warning' target='_blank'>\n" +
        "                                                <i class='fa fa-stethoscope'></i> Track\n" +
        "                                            </a>";
      Lobibox.notify("success", {
        title: code,
        size: "normal",
        delay: false,
        closeOnClick: false,
        img: $("#broadcasting_url").val() + "/resources/img/ro7.png",
        msg: content,
      });
    },
    // appendReco(code, name_sender, facility_sender, date_now, msg) {
    //     let picture_sender = $("#broadcasting_url").val()+"/resources/img/receiver.png";
    //     let message = msg.replace(/^\<p\>/,"").replace(/\<\/p\>$/,"");
    //     $(".reco-body"+code).append('<div class=\'direct-chat-msg left\'>\n' +
    //         '                    <div class=\'direct-chat-info clearfix\'>\n' +
    //         '                    <span class="direct-chat-name text-info pull-left">'+facility_sender+'</span><br>'+
    //         '                    <span class=\'direct-chat-name pull-left\'>'+name_sender+'</span>\n' +
    //         '                    <span class=\'direct-chat-timestamp pull-right\'>'+date_now+'</span>\n' +
    //         '                    </div>\n' +
    //         '                    <img class=\'direct-chat-img\' title=\'\' src="'+picture_sender+'" alt=\'Message User Image\'>\n' +
    //         '                    <div class=\'direct-chat-text\'>\n' +
    //         '                    '+message+'\n' +
    //         '                    </div>\n' +
    //         '                    </div>')
    // },
    appendReco(code, name_sender, facility_sender, date_now, msg, filepath) {
      let picture_sender =
        $("#broadcasting_url").val() + "/resources/img/receiver.png";
      let message =
        msg && msg.trim() !== ""
          ? msg.replace(/^\<p\>/, "").replace(/\<\/p\>$/, "")
          : "";

      let fileHtml = "";
      const imageExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
      const pdfExtensions = ["pdf"];
      let filePaths = [];
      let newGlobalFiles = [];

      const startingGlobalIndex = globalFiles ? globalFiles.length : 0;
      if (filepath) {
        if (typeof filepath === "string") {
          // Split by pipe and filter out empty strings
          filePaths = filepath.split("|").filter((path) => path.trim() !== "");
        } else if (Array.isArray(filepath)) {
          filePaths = filepath.filter((path) => path.trim() !== "");
        }
      }

      if (filePaths.length > 0) {
        fileHtml +=
          '<div class="attachment-wrapper" white-space: nowrap; overflow-x: auto;">';
        const baseUrl = $("#broadcasting_url").val();

        filePaths.forEach((file, index) => {
          if (file.trim() !== "") {
            let url;

            const globalFileIndex = startingGlobalIndex + index;

            if (file.startsWith("http://") || file.startsWith("https://")) {
              // Already a full URL
              url = file;
            } else if (file.startsWith("/")) {
              // Absolute path
              url = baseUrl + file;
            }

            newGlobalFiles.push(url);

            try {
              url = new URL(file, baseUrl); // Use base in case it's a relative URL
            } catch (err) {
              console.error("Invalid file URL:", file, err);
              return; // skip this file
            }

            const fileName = url.pathname.split("/").pop();
            const extension = fileName.split(".").pop().toLowerCase();
            const displayName =
              fileName.length > 10
                ? fileName.substring(0, 7) + "..."
                : fileName;

            const isPDF = pdfExtensions.includes(extension);
            const icon = isPDF
              ? $("#broadcasting_url").val() + "/public/fileupload/pdffile.png"
              : $("#broadcasting_url").val() + `${file}`;

            fileHtml += `
                        <div style="display: inline-block; text-align: center; width: 60px; margin-right: 5px;">
                            <a href="javascript:void(0)" class="file-preview-trigger realtime-file-preview" 
                                data-file-type="${extension}"
                                data-file-url="${file}"
                                data-file-name="${fileName}"
                                data-feedback-code="${code}"
                                data-file-paths="${filePaths.join("|")}"
                                data-current-index="${globalFileIndex}"
                                data-local-index="${index}"
                                data-use-global="true">
                                
                                <img class="attachment-thumb"
                                    src="${icon}"
                                    alt="${extension.toUpperCase()} file"
                                    style="width: 50px; height: 50px; object-fit: contain; border:1px solid green; border-radius: 4px;">
                            </a>
                            <div style="font-size: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${fileName}">
                                ${displayName}
                            </div>
                        </div>
                    `;
          }
        });

        fileHtml += "</div>";
      }

      // UPDATE GLOBAL FILES ARRAY
      if (newGlobalFiles.length > 0) {
        // Initialize globalFiles if it doesn't exist
        if (typeof window.globalFiles === "undefined") {
          window.globalFiles = [];
        }

        // Add new files to global array
        window.globalFiles = window.globalFiles.concat(newGlobalFiles);

        // Store per-code basis for better organization
        if (!window.globalFilesByCode) {
          window.globalFilesByCode = {};
        }
        if (!window.globalFilesByCode[code]) {
          window.globalFilesByCode[code] = [];
        }
        window.globalFilesByCode[code] =
          window.globalFilesByCode[code].concat(newGlobalFiles);

        // If you're using Vue's reactive data, you might want to update a Vue data property
        if (this.$data && this.$data.globalFiles) {
          this.globalFiles = [...this.globalFiles, ...newGlobalFiles];
        }
      }
      let messageColor = 'style="margin-top: 5px;"';
      let messageText = `<div class="caption-text" ${messageColor}>${message}</div>`;

      $(".reco-body" + code).append(`
            <div class='direct-chat-msgs left'>
                <div class='direct-chat-info clearfix'>
                    <span class="direct-chat-name text-info pull-left">${facility_sender}</span><br>
                    <span class='direct-chat-name pull-left'>${name_sender}</span>
                    <span class='direct-chat-timestamp pull-right'>${date_now}</span>
                </div>
                <img class='direct-chat-img' title='' src="${picture_sender}" alt='Message User Image'>
                <div class='direct-chat-text'>
                    ${fileHtml}
                    ${messageText}
                </div>
            </div>
        `);

      this.FeedbackFilePreviewListeners();
    },
    FeedbackFilePreviewListeners() {
      $(document)
        .off("click", ".realtime-file-preview")
        .on("click", ".realtime-file-preview", function (e) {
          e.preventDefault();

          const baseUrl = $("#broadcasting_url").val();
          const filePathsString = $(this).data("file-paths");
          const filePaths =
            typeof filePathsString === "string"
              ? filePathsString.split("|").filter((p) => p.trim() !== "")
              : [];
          // var desc = 'desc';
          const fullfilePaths = filePaths.map((file) =>
            file.startsWith("http") ? file : baseUrl + file
          );
          const useGlobal = $(this).data("use-global");
          const globalIndex = parseInt($(this).data("current-index"));
          const localIndex = parseInt($(this).data("local-index"));
          const feedbackCode = $(this).data("feedback-code");

          let files = [];
          let startIndex = 0;

          if (useGlobal && globalFiles && globalFiles.length > 0) {
            // Use globalFiles array for navigation
            files = globalFiles.map(normalizeUrl);
            startIndex = globalIndex;
          } else {
            // Fallback to local files from data attribute
            let filesAttr = $(this).attr("data-files");
            try {
              if (filesAttr) {
                files = JSON.parse(filesAttr);
                files = files.map(normalizeUrl);
                startIndex = localIndex;
              } else {
                console.warn("data-files attribute is missing or empty.");
                return;
              }
            } catch (e) {
              console.error("Invalid JSON in data-files:", filesAttr, e);
              return;
            }
          }

          if (Array.isArray(files) && files.length > 0) {
            // console.log("Setting up file preview with files:", files);
            // console.log("Starting index:", startIndex);
            window.setupfeedbackFilePreview(files, startIndex, code);
            $("#filePreviewContentReco").modal("show");
          }
          $("#filePreviewContentReco").modal("show");
        });
    },
    async startBasicCall() {
      // Create an instance of the Agora Engine
      this.agoraEngine = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
      const agoraEngine = this.agoraEngine; // Use this reference

      // Setup channel parameters with user count tracking
      if (!this.channelParameters) {
        this.channelParameters = {};
      }
      this.channelParameters.userCount = 0; // Initialize user count
      this.channelParameters.maxUsers = 2; // Maximum 2 users allowed

      const remotePlayerContainer = document.createElement("div");
      const localPlayerContainer = document.createElement("div");
      localPlayerContainer.id = this.options.uid;

      let self = this;

      // Listen for when a user joins the channel
      agoraEngine.on("user-joined", async (user) => {
        console.log("User joined:", user.uid);
        self.channelParameters.userCount++;
        this.isUserJoined = true;

        console.log(
          "user count: ",
          self.channelParameters.userCount,
          "max users:",
          self.channelParameters.maxUsers
        );

        if (
          self.channelParameters.userCount >= self.channelParameters.maxUsers
        ) {
          self.showChannelFullMessage();
          await agoraEngine.leave();
          self.channelParameters.userCount--;
          return;
        } else {
          if (this.referring_md === "yes") {
            this.startScreenRecording();
            // this.startRecording();
          }
        }
      });
      // Listen for the "user-published" event to retrieve a AgoraRTCRemoteUser object
      //agora
      agoraEngine.on("user-published", async (user, mediaType) => {
        await agoraEngine.subscribe(user, mediaType);
        // console.log("subscribe success");
        console.log("option channel name:", this.options);
        if (mediaType === "video") {
          // Pause ringing audio when remote video is received
          if (self.$refs && self.$refs.ringingPhone) {
            self.$refs.ringingPhone.pause();
          }
          self.channelParameters.remoteVideoTrack = user.videoTrack;
          self.channelParameters.remoteAudioTrack = user.audioTrack;
          self.channelParameters.remoteUid = user.uid.toString();
          remotePlayerContainer.id = user.uid.toString();

          document.body.append(remotePlayerContainer);
          $(".remotePlayerDiv").html(remotePlayerContainer);
          $(".remotePlayerDiv").removeAttr("style").css("display", "unset");
          $(remotePlayerContainer).addClass("remotePlayerLayer");

          self.channelParameters.remoteVideoTrack.play(remotePlayerContainer);
        }

        if (mediaType === "audio") {
          self.channelParameters.remoteAudioTrack = user.audioTrack;
          self.channelParameters.remoteAudioTrack.play();
        }

        // Start the timer only if not already started
        if (!self.callTimer) {
          self.startCallTimer();
        }
      });

      // Listen for users leaving the channel
      agoraEngine.on("user-left", (user) => {
        // console.log(user.uid + " has left the channel");
        self.channelParameters.userCount = Math.max(
          0,
          self.channelParameters.userCount - 1
        );
      });

      try {
        // console.log("Attempting to join channel...", self.options.channel);
        await agoraEngine.join(
          self.options.appId,
          self.options.channel,
          self.options.token,
          self.options.uid
        );

        // console.log("Successfully joined channel");

        // Create audio track
        self.channelParameters.localAudioTrack =
          await AgoraRTC.createMicrophoneAudioTrack();

        // Check if camera is available before creating video track
        try {
          const devices = await AgoraRTC.getCameras();
          if (devices && devices.length > 0) {
            self.channelParameters.localVideoTrack =
              await AgoraRTC.createCameraVideoTrack();
            document.body.append(localPlayerContainer);
            $(".localPlayerDiv").html(localPlayerContainer);
            $(localPlayerContainer).addClass("localPlayerLayer");
            self.channelParameters.localVideoTrack.play(localPlayerContainer);
          } else {
            // console.log("No camera detected");
          }
        } catch (error) {
          console.warn("Error accessing camera:", error);
        }

        // Publish tracks based on availability
        const tracksToPublish = [self.channelParameters.localAudioTrack];
        if (self.channelParameters.localVideoTrack) {
          tracksToPublish.push(self.channelParameters.localVideoTrack);
          // Only play video if we have a track
          self.channelParameters.localVideoTrack.play(localPlayerContainer);
        }
        await agoraEngine.publish(tracksToPublish);
        // console.log("publish success!");

        window.onload = function () {
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
    // Method to show channel full message to user
    showChannelFullMessage() {
      // Create an alert or message to inform user
      const fullMessage = document.createElement("div");
      fullMessage.className = "channel-full-message";
      fullMessage.textContent =
        "This channel is full. Maximum 2 users allowed.";
      fullMessage.style.position = "fixed";
      fullMessage.style.top = "50%";
      fullMessage.style.left = "50%";
      fullMessage.style.transform = "translate(-50%, -50%)";
      fullMessage.style.padding = "20px";
      fullMessage.style.backgroundColor = "rgba(0,0,0,0.8)";
      fullMessage.style.color = "white";
      fullMessage.style.borderRadius = "5px";
      fullMessage.style.zIndex = "9999";

      document.body.appendChild(fullMessage);

      // Remove the message after a few seconds
      setTimeout(() => {
        fullMessage.remove();
        window.top.close();
      }, 5000);
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
      // console.log("local");
      try {
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

        // Check if camera is available before creating video track
        const devices = await AgoraRTC.getCameras();
        if (devices && devices.length > 0) {
          // Create a local video track from the video captured by a camera.
          channelParameters.localVideoTrack =
            await AgoraRTC.createCameraVideoTrack();
          // Append the local video container to the page body.
          document.body.append(localPlayerContainer);
          $(".localPlayerDiv").html(localPlayerContainer);
          $(localPlayerContainer).addClass("localPlayerLayer");

          // Play the local video track.
          channelParameters.localVideoTrack.play(localPlayerContainer);
        }

        // Publish the tracks that are available
        const tracksToPublish = [channelParameters.localAudioTrack];
        if (channelParameters.localVideoTrack) {
          tracksToPublish.push(channelParameters.localVideoTrack);
        }

        // Publish the local audio and video tracks in the channel.
        await agoraEngine.publish(tracksToPublish);
        // console.log("publish success!");
      } catch (error) {
        console.error("Error in joinVideo:", error);
      }
    },
    async sendCallDuration() {
      if (this.isLeavingChannel) return; // Prevent duplicate sends
      this.isLeavingChannel = true;

      // Parse callDuration string (supports "mm : ss" or "hh : mm : ss")
      let duration = this.callDuration.replace(/\s/g, ""); // Remove spaces
      let parts = duration.split(":").map(Number);
      let totalMinutes = 0;

      if (parts.length === 2) {
        // Format: mm:ss
        totalMinutes = parts[0];
        if (parts[1] >= 30) totalMinutes += 1; // round up if 30+ seconds
      } else if (parts.length === 3) {
        // Format: hh:mm:ss
        totalMinutes = parts[0] * 60 + parts[1];
        if (parts[2] >= 30) totalMinutes += 1; // round up if 30+ seconds
      }

      // Ensure integer and at least 1 minute if any call happened
      totalMinutes = Math.max(1, parseInt(totalMinutes, 10));

      try {
        const response = await axios.post(
          `${this.baseUrl}/save-call-duration`,
          {
            call_duration: totalMinutes, // send as int(11)
            tracking_id: this.tracking_id,
            referral_code: this.referral_code,
          }
        );

        // console.log(
        //   "Call duration saved (minutes):",
        //   totalMinutes,
        //   response.data
        // );
        localStorage.removeItem("callStartTime"); // Clean up
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
            // this.stopRecording();
          };
        } else {
          window.top.close();
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
    beforeDestroy() {
      clearInterval(this.callTimer);
      // Remove sendCallDuration from here since it's handled in leaveChannel
    },
    async videoStreamingOnAndOff() {
      this.videoStreaming = !this.videoStreaming;

      if (this.videoStreaming) {
        // If turning video on and we don't have a video track yet
        if (!this.channelParameters.localVideoTrack) {
          try {
            const devices = await AgoraRTC.getCameras();
            if (devices && devices.length > 0) {
              this.channelParameters.localVideoTrack =
                await AgoraRTC.createCameraVideoTrack();
              const localPlayerContainer = document.getElementById(
                this.options.uid
              );

              if (!localPlayerContainer) {
                const newContainer = document.createElement("div");
                newContainer.id = this.options.uid;
                document.body.append(newContainer);
                $(".localPlayerDiv").html(newContainer);
                $(newContainer).addClass("localPlayerLayer");
              }

              this.channelParameters.localVideoTrack.play(this.options.uid);

              // Publish the video track if we're connected
              if (this.channelParameters.localAudioTrack) {
                await agoraEngine.publish([
                  this.channelParameters.localVideoTrack,
                ]);
              }
            } else {
              // console.log("No camera detected");
              this.videoStreaming = false;
              return;
            }
          } catch (error) {
            console.warn("Error accessing camera:", error);
            this.videoStreaming = false;
            return;
          }
        } else {
          // If we already have a video track, just enable it
          this.channelParameters.localVideoTrack.setEnabled(true);
        }
      } else {
        // Turning video off
        if (this.channelParameters.localVideoTrack) {
          this.channelParameters.localVideoTrack.setEnabled(false);
        }
      }
    },
    audioStreamingOnAnddOff() {
      this.audioStreaming = this.audioStreaming ? false : true;
      this.channelParameters.localAudioTrack.setEnabled(this.audioStreaming);
    },
    // hideDivAfterTimeout() {
    // setTimeout(() => {
    //   $(".iconCall").removeClass("fade-in");
    //   this.showDiv = false;
    // }, 10000);
    // },
    showDivAgain() {
      this.showDiv = true;
      //  this.hideDivAfterTimeout();
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
        // console.log("pause");
        self.$refs.ringingPhone.pause();
      }, 60000);
    },

    //--------------------------------------------------------------------------

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
            const prescribedActivityId =
              response.data.prescriptions[0].prescribed_activity_id;

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
        activity_id: this.activity_id,
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
              code: self.referral_code || referralCode,
              form_type: self.form_type || formType,
            };
            axios
              .post(`${self.baseUrl}/api/video/upward`, endorseUpward)
              .then((response) => {
                // console.log(response.status);
                // console.log("data Upward:", response.data);
                // console.log("endorseUpward:", endorseUpward);
                if (response.data.trim() === "success") {
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
</script>

<template>
  <div v-if="loading" class="loader-overlay">
    <div class="loader" style="margin-right: 20px"></div>
    <div style="width: 300px; margin-top: 20px">
      <div style="background: #444; border-radius: 8px; overflow: hidden">
        <div
          :style="{
            width: uploadProgress + '%',
            background: '#4caf50',
            height: '18px',
            transition: 'width 0.3s',
          }"
        ></div>
      </div>
      <p style="color: white; text-align: center; margin: 5px 0 0 0">
        Please wait until upload is complete.<br />Do not close this window.
        {{ uploadProgress }}%
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
              <h3 v-if="!isUserJoined">Calling...</h3>
            </div>
            <img :src="doctorUrl" class="remote-img" alt="Image1" />
          </div>
          <div class="call-duration">
            <span id="call-timer">{{ callDuration }}</span>
          </div>
          <Transition name="fade">
            <div class="tooltip-container">
              <div class="iconCall position-absolute fade-in" v-if="showDiv">
                <div class="button-container">
                  <div
                    v-if="!isMobile && showMic"
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
                    v-if="!isMobile && showVedio"
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
                <!-- <div class="button-container" v-if="availableCameras.length > 1"> -->
                <div class="button-container" v-if="isMobile">
                  <div
                    v-if="!isMobile && showCameraSwitch"
                    class="tooltip-text"
                    style="background-color: #218838"
                  >
                    Switch Camera
                  </div>
                  <button
                    class="btn btn-success btn-md camera-switch-button"
                    @click="switchCamera"
                    type="button"
                    @mouseover="showCameraSwitch = true"
                    @mouseleave="showCameraSwitch = false"
                  >
                    <i class="bi-arrow-repeat"></i>
                  </button>
                </div>
                &nbsp;
                <div class="button-container">
                  <div
                    v-if="!isMobile && showEndcall"
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
                <div
                  class="button-container"
                  v-if="this.user.facility_id != 63 && this.telemedicine == 1"
                >
                  <div
                    v-if="!isMobile && showUpward"
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
                <div class="button-container" v-if="this.telemedicine == 1">
                  <div
                    v-if="!isMobile && showPrescription"
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
                <div class="button-container" v-if="this.telemedicine == 1">
                  <div
                    v-if="!isMobile && showTooltip"
                    class="tooltip-text"
                    style="background-color: #007bff"
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
                    v-if="!isMobile && showTooltipFeedback"
                    class="tooltip-text"
                    style="background-color: #17a2b8"
                  >
                    Chat
                  </div>
                  <button
                    class="btn btn-info btn-md reco-button"
                    data-toggle="modal"
                    data-target="#feedbackModal"
                    :data-code="referral_code"
                    onclick="viewReco($(this),0)"
                    @mouseover="showTooltipFeedback = true"
                    @mouseleave="showTooltipFeedback = false"
                  >
                    <i class="bi bi-chat-left-text"></i>
                  </button>
                </div>
              </div>
            </div>
          </Transition>
          <div class="localPlayerDiv" id="draggable-div">
            <img
              :src="doctorUrl1"
              id="local-image"
              class="img2"
              alt="Image2"
              draggable="true"
            />
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
                    Regional Director's Office Tel. No. (032) 253-6355 Fax No.
                    (032) 254-0109
                  </p>
                  <p>
                    Official Website:
                    <span style="color: blue">http://www.ro7.doh.gov.ph</span>
                    Email Address: dohro7@gmail.com
                  </p>
                </div>
              </div>
              <div class="clinical">
                <span style="color: #4caf50"
                  ><b>CLINICAL REFERRAL FORM</b></span
                >
              </div>
            </div>
            <div class="tableForm" v-if="telemedicine == 1">
              <FormReferralComponent
                :initialForm="{ ...form }"
                :file_path="file_path"
                :icd="icd"
                :patient_age="patient_age"
                :file_name="file_name"
                :form_version="form_version"
                :current_medication="current_medication"
                :telemedicine="telemedicine"
              />
              <div class="row g-0">
                <div class="col-6">
                  <button
                    class="btn btn-success btn-md w-100 ml-2"
                    type="button"
                    @click="generatePrescription()"
                  >
                    <i class="bi bi-prescription"></i> Generate Prescription
                  </button>
                </div>
                <div class="col-6">
                  <button
                    class="btn btn-primary btn-md w-100"
                    type="button"
                    @click="generateLabrequest()"
                    style="
                      background-color: #0d6efd;
                      border-color: #0d6efd;
                      box-shadow: none;
                      pointer-events: auto;
                    "
                    onmouseover="this.style.backgroundColor='#0d6efd'; this.style.borderColor='#0d6efd';"
                    onmouseout="this.style.backgroundColor='#0d6efd'; this.style.borderColor='#0d6efd';"
                  >
                    <i class="bi bi-clipboard2-pulse"></i> Generate Lab Request
                  </button>
                </div>
              </div>
            </div>
            <div class="telemedForm" v-else>
              <FormReferralComponent
                :initialForm="{ ...form }"
                :file_path="file_path"
                :icd="icd"
                :patient_age="patient_age"
                :file_name="file_name"
                :past_medical_history="{ ...past_medical_history }"
                :pertinent_laboratory="{ ...pertinent_laboratory }"
                :personal_and_social_history="{
                  ...personal_and_social_history,
                }"
                :review_of_system="{ ...review_of_system }"
                :nutritional_status="{ ...nutritional_status }"
                :latest_vital_signs="{ ...latest_vital_signs }"
                :glasgocoma_scale="{ ...glasgocoma_scale }"
                :obstetric_and_gynecologic_history="{
                  ...obstetric_and_gynecologic_history,
                }"
                :form_version="form_version"
                :current_medication="current_medication"
                :telemedicine="telemedicine"
                :pregnancy="pregnancy || []"
              />
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
    <!-- Place AFK dialog here, before closing fullscreen-div -->
    <div v-if="afkDialogVisible" class="afk-overlay">
      <div class="afk-dialog">
        <p>You have been inactive for some time.</p>
        <p>
          Ending call in <b>{{ afkCountdown }}</b> seconds...
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import "./css/index.css";
td {
  padding: 5px;
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
  .fullscreen-div {
    overflow: auto;
    position: relative;
  }

  .video-container,
  .form-container {
    width: 100%;
    flex: none;
    overflow: hidden;
  }

  .video-container {
    height: 60%;
  }

  .form-container {
    height: 40%;
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
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter-from,
.fade-leave-to {
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
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
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

/* AFK Dialog Styles */
.afk-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
}
.afk-dialog {
  background: #fff;
  padding: 30px 40px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 2px 16px rgba(0, 0, 0, 0.2);
}

@media screen and (max-width: 768px) {
  .reco-button {
    border-radius: 50% !important;
    width: 30px !important;
    height: 30px !important;
    background-color: rgba(81, 83, 85, 0.596) !important;
    border-color: transparent !important;

    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
  }
  .bi-chat-left-text {
    font-size: 12px !important;
  }
  .bi-prescription2 {
    font-size: 12px !important;
  }
  .prescription-button {
    border-radius: 50% !important;
    width: 30px !important;
    height: 30px !important;
    background-color: rgba(81, 83, 85, 0.596) !important;
    border-color: transparent !important;

    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
  }
  .bi-prescription {
    font-size: 12px !important;
  }
  .upward-button {
    border-radius: 50% !important;
    width: 30px !important;
    height: 30px !important;
    background-color: rgba(81, 83, 85, 0.596) !important;
    border-color: transparent !important;

    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
  }
  .bi-hospital {
    font-size: 12px !important;
  }
  .decline-button {
    border-radius: 50% !important;
    width: 30px !important;
    height: 30px !important;
    background-color: rgba(81, 83, 85, 0.596) !important;
    border-color: transparent !important;

    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
  }
  .bi-telephone-x-fill {
    font-size: 12px !important;
  }
  .camera-switch-button {
    border-radius: 50% !important;
    width: 30px !important;
    height: 30px !important;
    background-color: rgba(81, 83, 85, 0.596) !important;
    border-color: transparent !important;

    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
  }
  .bi-arrow-repeat {
    font-size: 12px !important;
  }
  .video-button {
    border-radius: 50% !important;
    width: 30px !important;
    height: 30px !important;
    background-color: rgba(81, 83, 85, 0.596) !important;
    border-color: transparent !important;

    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
  }
  .bi-camera-video-fill {
    font-size: 12px !important;
  }
  .mic-button {
    border-radius: 50% !important;
    width: 30px !important;
    height: 30px !important;
    background-color: rgba(81, 83, 85, 0.596) !important;
    border-color: transparent !important;

    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
  }

  .bi-mic-fill {
    font-size: 12px !important;
  }
  .localPlayerLayer {
    height: 120px !important;
    width: 90px !important;
    object-fit: scale-down !important;
  }

  .localPlayerDiv {
    min-height: 120px !important;
    min-width: 90px !important;
    max-height: 25vh !important;
    max-width: 30vw !important;
    overflow: hidden !important;
  }

  .localPlayerDiv video,
  .localPlayerDiv img {
    width: 100% !important;
    height: 100% !important;
    object-fit: scale-down !important;
  }
}

@media screen and (max-width: 480px) {
  .localPlayerLayer {
    height: 100px !important;
    width: 75px !important;
    object-fit: scale-down !important;
  }

  .localPlayerDiv {
    min-height: 100px !important;
    min-width: 70px !important;
    max-height: 20vh !important;
    max-width: 25vw !important;
    bottom: 60px !important;
    right: 5px !important;
    overflow: hidden !important;
  }

  .localPlayerDiv video,
  .localPlayerDiv img {
    width: 100% !important;
    height: 100% !important;
    object-fit: scale-down !important;
  }
}
</style>
