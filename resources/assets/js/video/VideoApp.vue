<script>
import axios from "axios";
import { Transition } from "vue";
import AgoraRTC from "agora-rtc-sdk-ng";
import PrescriptionModal from "./PrescriptionModal.vue";
import LabRequestModal from "./LabRequestModal.vue";
import FeedbackModal from "./FeedbackModal.vue";

let baseUrlfeedback = `referral/doctor/vue/feedback`;
let doctorFeedback = `referral/doctor/feedback`
export default {
  name: "RecoApp",
  components: {
    PrescriptionModal,
    LabRequestModal,
    FeedbackModal,
  },
  data() {
    return {
      //start video in minutes
      callMinutes: 0,
      callTimer: null,
      isLeavingChannel: false,
      //feedback
      feedbackUrl: baseUrlfeedback,
      doctorfeedback: doctorFeedback,
      feedbackModalVisible: false,
      currentCode: null,
      ImageUrl:'',
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
      reco_count : $("#reco_count_val").val(),
    };
  },
  mounted() {
    //for minutes timer
    this.startCallTimer();
    window.addEventListener('beforeunload', this.stopCallTimer);
    axios
      .get(
        `${this.baseUrl}/doctor/referral/video/normal/form/${this.tracking_id}`
      )
      .then((res) => {
        const response = res.data;
        console.log(response);
        this.form = response.form;
        if (response.age_type === "y")
          this.patient_age = response.patient_age + " Years Old";
        else if (response.age_type === "m")
          this.patient_age = response.patient_age + " Months Old";

        this.icd = response.icd;
        console.log("testing\n" + this.icd);

        this.file_path = response.file_path;
        this.file_name = response.file_name;

        console.log(response);
      })
      .catch((error) => {
        console.log(error);
      });

    //this.hideDivAfterTimeout();
    window.addEventListener("click", this.showDivAgain);
  },

  beforeUnmount() {
    window.removeEventListener("click", this.showDivAgain);
  },
  props: ["user"],
  created() {
    let self = this;
    $(document).ready(function () {
      self.ringingPhoneFunc();
    });
    this.startBasicCall();
    Echo.join('reco')
      .listen('SocketReco', (event) => {
          $("#reco_count"+event.payload.code).html(event.payload.feedback_count);
          axios.get($("#broadcasting_url").val()+'/activity/check/'+event.payload.code+'/'+this.user.facility_id).then(response => {
              if(response.data && event.payload.sender_facility !== this.user.facility_id && $("#archived_reco_page").val() !== 'true') {
                  this.reco_count++
                  $("#reco_count").html(this.reco_count)
                  this.appendReco(event.payload.code, event.payload.name_sender, event.payload.facility_sender, event.payload.date_now, event.payload.message)
                  try {
                      let objDiv = document.getElementById(event.payload.code);
                      objDiv.scrollTop = objDiv.scrollHeight;
                      if (!objDiv.scrollTop)
                          this.notifyReco(event.payload.code, event.payload.feedback_count, event.payload.redirect_track)
                  } catch(err){
                      console.log("modal not open");
                      this.notifyReco(event.payload.code, event.payload.feedback_count, event.payload.redirect_track)
                  }
              }
          });
      });
  },
  methods: {
    startCallTimer() {
      let startTime = localStorage.getItem('callStartTime');
    
        if (startTime) {
          this.callMinutes = Math.floor((Date.now() - startTime) / 60000);
        } else {
          // Store the start time if not already set
          localStorage.setItem('callStartTime', Date.now());
        }

      this.callTimer = setInterval(() => {
        this.callMinutes++;
          console.log("Current call duration:", this.callMinutes);
      }, 60000);
console.log("referring call duration:", this.referring_md);
    },
    notifyReco(code, feedback_count, redirect_track) {
        let content = '<button class=\'btn btn-xs btn-info\' onclick=\'viewReco($(this))\' data-toggle=\'modal\'\n' +
            '                               data-target=\'#feedbackModal\'\n' +
            '                               data-code="'+code+'" ' +
            '                               >\n' +
            '                           <i class=\'fa fa-comments\'></i> ReCo <span class=\'badge bg-blue\' id="reco_count'+code+'">'+feedback_count+'</span>\n' +
            '                       </button><a href="'+redirect_track+'" class=\'btn btn-xs btn-warning\' target=\'_blank\'>\n' +
            '                                                <i class=\'fa fa-stethoscope\'></i> Track\n' +
            '                                            </a>';
        Lobibox.notify("success", {
            title: code,
            size: 'normal',
            delay: false,
            closeOnClick: false,
            img: $("#broadcasting_url").val()+"/resources/img/ro7.png",
            msg: content
        });
        
    },
    appendReco(code, name_sender, facility_sender, date_now, msg) {
        let picture_sender = $("#broadcasting_url").val()+"/resources/img/receiver.png";
        let message = msg.replace(/^\<p\>/,"").replace(/\<\/p\>$/,"");
        $(".reco-body"+code).append('<div class=\'direct-chat-msg left\'>\n' +
            '                    <div class=\'direct-chat-info clearfix\'>\n' +
            '                    <span class="direct-chat-name text-info pull-left">'+facility_sender+'</span><br>'+
            '                    <span class=\'direct-chat-name pull-left\'>'+name_sender+'</span>\n' +
            '                    <span class=\'direct-chat-timestamp pull-right\'>'+date_now+'</span>\n' +
            '                    </div>\n' +
            '                    <img class=\'direct-chat-img\' title=\'\' src="'+picture_sender+'" alt=\'Message User Image\'>\n' +
            '                    <div class=\'direct-chat-text\'>\n' +
            '                    '+message+'\n' +
            '                    </div>\n' +
            '                    </div>')
    },
    async startBasicCall() {
      // Create an instance of the Agora Engine
      const agoraEngine = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
      // Dynamically create a container in the form of a DIV element to play the remote video track.
      const remotePlayerContainer = document.createElement("div");
      // Dynamically create a container in the form of a DIV element to play the local video track.
      const localPlayerContainer = document.createElement("div");
      // Specify the ID of the DIV container. You can use the uid of the local user.
      localPlayerContainer.id = this.options.uid;
      // Listen for the "user-published" event to retrieve a AgoraRTCRemoteUser object.
      let self = this;
      agoraEngine.on("user-published", async (user, mediaType) => {
        // Subscribe to the remote user when the SDK triggers the "user-published" event.
        await agoraEngine.subscribe(user, mediaType);
        console.log("subscribe success");
        // Subscribe and play the remote video in the container If the remote user publishes a video track.
        if (mediaType == "video") {
          console.log("remote");
          // Retrieve the remote video track.
          self.channelParameters.remoteVideoTrack = user.videoTrack;
          // Retrieve the remote audio track.
          self.channelParameters.remoteAudioTrack = user.audioTrack;
          // Save the remote user id for reuse.
          self.channelParameters.remoteUid = user.uid.toString();
          // Specify the ID of the DIV container. You can use the uid of the remote user.
          remotePlayerContainer.id = user.uid.toString();
          self.channelParameters.remoteUid = user.uid.toString();
          /*remotePlayerContainer.textContent = "Remote user " + user.uid.toString();*/
          // Append the remote container to the page body.
          self.$refs.ringingPhone.pause();
          document.body.append(remotePlayerContainer);
          $(".remotePlayerDiv").html(remotePlayerContainer);
          $(".remotePlayerDiv").removeAttr("style").css("display", "unset");
          $(remotePlayerContainer).addClass("remotePlayerLayer");
          // Play the remote video track.
          self.channelParameters.remoteVideoTrack.play(remotePlayerContainer);
        }
        // Subscribe and play the remote audio track If the remote user publishes the audio track only.
        if (mediaType == "audio") {
          // Get the RemoteAudioTrack object in the AgoraRTCRemoteUser object.
          self.channelParameters.remoteAudioTrack = user.audioTrack;
          // Play the remote audio track. No need to pass any DOM element.
          self.channelParameters.remoteAudioTrack.play();
        }
        // Listen for the "user-unpublished" event.
        agoraEngine.on("user-unpublished", (user) => {
          console.log(user.uid + "has left the channel");
        });
      });
      window.onload = function () {
        self.joinVideo(
          agoraEngine,
          self.channelParameters,
          localPlayerContainer,
          self
        );
      };
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

      if (this.callMinutes > 0) {
        try {
          // Calculate final duration before sending
          const finalDuration = Math.floor((Date.now() - localStorage.getItem('callStartTime')) / 60000);
          
          const response = await axios.post(`${this.baseUrl}/save-call-duration`, {
            call_duration: finalDuration,
            tracking_id: this.tracking_id,
            referral_code: this.referral_code
          });
          
          console.log("Call duration saved:", response.data);
          localStorage.removeItem('callStartTime'); // Clean up
          return true;
        } catch (error) {
          console.error("Error saving call duration:", error);
          return false;
        }
      }
      return false;
    },

    async leaveChannel() {
      if (confirm("Are you sure you want to leave this channel?")) {
        // Wait for duration to be sent before closing
        if(this.referring_md == 'yes'){
          clearInterval(this.callTimer); // Stop the timer
          await this.sendCallDuration();

          // Give more time for the request to complete
          setTimeout(() => {
            window.top.close();
          }, 2000);
        }
          window.top.close();

      }
    },
    stopCallTimer() {
      if(this.referring_md == 'yes'){
        if (this.callTimer) {
            clearInterval(this.callTimer);
        }

        this.sendCallDuration();
        localStorage.removeItem('callStartTime');
      }
    },
    beforeDestroy() {
      clearInterval(this.callTimer);
      // Remove sendCallDuration from here since it's handled in leaveChannel
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
        console.log("pause");
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
      console.log(getPrescription);

      axios
        .post(`${this.baseUrl}/api/video/prescription/check`, getPrescription)
        .then((response) => {
          console.log(response);
          if (response.data.status === "success") {
            const prescriptions =
              response.data.prescriptions[0].prescribed_activity_id;

            console.log("Prescriptions:", prescriptions);

            const prescribedActivityId = prescriptions;
            window.open(
              `${this.baseUrl}/doctor/print/prescription/${this.tracking_id}/${prescribedActivityId}`,
              "_blank"
            );
          } else {
            Lobibox.alert("error", {
              msg: "No added prescription!",
            });
          }
        })
        .catch((error) => {
          console.log(error);
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
            window.open(pdfUrl, "_blank"); // Opens the PDF in a new tab
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
                console.log(response.status);
                console.log('data Upward:',response.data)
                console.log("endorseUpward:", endorseUpward);
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
    labRequest() {
      console.log("lab request");
    },
  },
};
</script>

<template>
  <audio ref="ringingPhone" :src="ringingPhoneUrl" loop></audio>
  <div class="fullscreen-div">
    <div class="row">
      <div class="col-lg-7">
        <div class="mainPic">
          <div class="remotePlayerDiv">
            <div id="calling">
              <h3>Calling...</h3>
            </div>
            <img :src="doctorUrl" class="img-fluid" alt="Image1" />
          </div>
          <Transition name="fade">
            <div class="tooltip-container">
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
                    Reco
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
            </div>
          </Transition>
          <div class="localPlayerDiv">
            <img :src="doctorUrl1" id="local-image" class="img2" alt="Image2" />
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="telemedForm">
          <div class="row-fluid">
            <div style="height: 10px;">
              <img :src="dohLogoUrl" alt="Image3" class="dohLogo" />
            </div>
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
              <span style="color: #4caf50"><b>CLINICAL REFERRAL FORM</b></span>
            </div>
            <div class="tableForm">
              <table class="table table-striped formTable">
                <tbody>
                    <tr>
                    <td colspan="12">
                        Name of Referring Facility:
                        <span class="forDetails"> {{ form.referring_name }} </span>
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
                        Address:
                        <span class="forDetails">
                        {{ form.referring_address }}
                        </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="6">
                        Referred to:
                        <span class="forDetails"> {{ form.referred_name }} </span>
                    </td>
                    <td colspan="6">
                        Department:
                        <span class="forDetails"> {{ form.department }} </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="12">
                        Address:
                        <span class="forDetails">
                        {{ form.referred_address }}
                        </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="6">
                        Date/Time Referred (ReCo):
                        <span class="dateReferred"> {{ form.time_referred }} </span>
                    </td>
                    <td colspan="6">
                        Date/Time Transferred:<span class="forDetails">
                        {{ form.time_transferred }}
                        </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="4">
                        Name of Patient:
                        <span class="forDetails"> {{ form.patient_name }} </span>
                    </td>
                    <td colspan="4">
                        Age: <span class="forDetails"> {{ patient_age }} </span>
                    </td>
                    <td colspan="4">
                        Sex:
                        <span class="forDetails"> {{ form.patient_sex }} </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="6">
                        Address:
                        <span class="forDetails"> {{ form.patient_address }} </span>
                    </td>
                    <td colspan="6">
                        Status:
                        <span class="forDetails"> {{ form.patient_status }} </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="6">
                        Philhealth status:
                        <span class="forDetails"> {{ form.phic_status }} </span>
                    </td>
                    <td colspan="6">
                        Philhealth #:
                        <span class="forDetails"> {{ form.phic_id }} </span>
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
                    <tr>
                    <td colspan="12">
                        Case Summary (pertinent Hx/PE, including meds, labs, course
                        etc.): <br /><span class="caseforDetails">{{
                        form.case_summary
                        }}</span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="12">
                        Summary of ReCo (pls. refer to ReCo Guide in Referring
                        Patients Checklist):<br /><span class="recoSummary">
                        {{ form.reco_summary }}
                        </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="12">
                        ICD-10 Code and Description: 
                        <li v-for="i in icd">
                        <span class="caseforDetails"
                            >{{ i.code }} - {{ i.description }}</span
                        >
                        </li>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="12">
                        Reason for referral:
                        <span class="forDetails">
                        {{ form.reason }}
                        </span>
                    </td>
                    </tr>
                    <tr v-if="file_path">
                    <td colspan="12">
                        <span v-if="file_path.length > 1">File Attachments: </span>
                        <span v-else>File Attachment: </span>
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
                        <span v-if="index + 1 !== file_path.length">,&nbsp;</span>
                        </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="12">
                        Name of Referring MD/HCW:
                        <span class="forDetails"> {{ form.md_referring }} </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="12">
                        Contact # of Referring MD/HCW:
                        <span class="forDetails">
                        {{ form.referring_md_contact }}
                        </span>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="12">
                        Name of referred MD/HCW-Mobile Contact # (ReCo): <br /><span
                        class="mdHcw"
                        >
                        {{ form.md_referred }}
                        </span>
                    </td>
                    </tr>
                </tbody>
              </table>
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
      @refresh="refreshMessages"
      @close-modal="closeFeedbackModal"
    />

  </div>
</template>

<style scoped>
@import "./css/index.css";
.fullscreen-div {
    width: 100vw;
    height: 100vh;
    overflow: hidden; /* Prevent scrolling */
    position: fixed; /* Keep it fixed in the viewport */
    top: 0;
    left: 0;
}
body {
    margin: 0;
    padding: 0;
    overflow: hidden;
}
.remotePlayerDiv, .localPlayerDiv {
    max-height: 100%;
    overflow: hidden;
}
</style>
