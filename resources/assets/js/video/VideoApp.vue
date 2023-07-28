<script>
    import axios from 'axios';
    import { Transition } from 'vue';

    import AgoraRTC from "agora-rtc-sdk-ng"
    export default {
        name: 'RecoApp',
        components: {
        },
        data() {
            return {
                ringingPhoneUrl: $("#broadcasting_url").val()+"/public/ringing.mp3",
                baseUrl: $("#broadcasting_url").val(),
                doctorUrl: $("#broadcasting_url").val()+"/resources/img/video/Doctor5.png",
                doctorUrl1: $("#broadcasting_url").val()+"/resources/img/video/Doctor6.png",
                declineUrl: $("#broadcasting_url").val()+"/resources/img/video/decline.png",
                videoCallUrl: $("#broadcasting_url").val()+"/resources/img/video/videocall.png",
                micUrl: $("#broadcasting_url").val()+"/resources/img/video/mic.png",
                dohLogoUrl: $("#broadcasting_url").val()+"/resources/img/video/doh-logo.png",
                tracking_id: this.getUrlVars()["id"],
                referral_code: this.getUrlVars()["code"],
                referring_md: this.getUrlVars()["referring_md"],
                activity_id: this.getUrlVars()["activity_id"],
                options: {
                    // Pass your App ID here.
                    appId: '1e264d7f57994a64b4ceb42f80188d06',
                    // Set the channel name.
                    channel: this.getUrlVars()["code"],
                    // Pass your temp token here.
                    token: null,
                    // Set the user ID.
                    uid: 0,
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
                    remoteUid: null
                },
                showDiv: false,
                prescription: "",
                prescriptionSubmitted: false
            }
        },
        mounted() {
            axios
                .get(`${this.baseUrl}/doctor/referral/video/normal/form/${this.tracking_id}`)
                .then((res) => {
                    const response = res.data;
                    console.log("testing");
                    console.log(response);
                    this.form = response.form;
                    if(response.age_type === "y")
                        this.patient_age = response.patient_age + " Years Old";
                    else if(response.age_type === "m")
                        this.patient_age =  response.patient_age + " Months Old";

                    this.icd = response.icd;
                    console.log("testing\n"+this.icd);

                    this.file_path = response.file_path;
                    this.file_name = response.file_name;

                    console.log(response)
                })
                .catch((error) => {
                    console.log(error);
                });

            this.hideDivAfterTimeout();
            window.addEventListener('click', this.showDivAgain);
        },
        beforeUnmount() {
            //this.clearTimeout();
            window.removeEventListener('click', this.showDivAgain);
        },
        props : ["user"],
        created() {
            let self = this
            $(document).ready(function() {
                console.log( "ready!" );
                self.ringingPhoneFunc();
            });
            this.startBasicCall();
        },
        methods: {
            async startBasicCall()
            {
                // Create an instance of the Agora Engine
                const agoraEngine = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
                // Dynamically create a container in the form of a DIV element to play the remote video track.
                const remotePlayerContainer = document.createElement("div");
                // Dynamically create a container in the form of a DIV element to play the local video track.
                const localPlayerContainer = document.createElement('div');
                // Specify the ID of the DIV container. You can use the uid of the local user.
                localPlayerContainer.id = this.options.uid;
                // Set the textContent property of the local video container to the local user id.
                /*localPlayerContainer.textContent = "Local user " + this.options.uid;
                // Set the local video container size.
                localPlayerContainer.style.width = "640px";
                localPlayerContainer.style.height = "480px";
                localPlayerContainer.style.padding = "15px 5px 5px 5px";
                // Set the remote video container size.
                remotePlayerContainer.style.width = "640px";
                remotePlayerContainer.style.height = "480px";
                remotePlayerContainer.style.padding = "15px 5px 5px 5px";*/
                // Listen for the "user-published" event to retrieve a AgoraRTCRemoteUser object.
                let self =  this
                agoraEngine.on("user-published", async (user, mediaType) =>
                {
                    // Subscribe to the remote user when the SDK triggers the "user-published" event.
                    await agoraEngine.subscribe(user, mediaType);
                    console.log("subscribe success");
                    // Subscribe and play the remote video in the container If the remote user publishes a video track.
                    if (mediaType == "video")
                    {
                        console.log("remote")
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
                        $(".remotePlayerDiv").html(remotePlayerContainer)
                        $(".remotePlayerDiv").removeAttr("style").css("display", "unset");
                        $(remotePlayerContainer).addClass("remotePlayerLayer");
                        // Play the remote video track.
                        self.channelParameters.remoteVideoTrack.play(remotePlayerContainer);
                    }
                    // Subscribe and play the remote audio track If the remote user publishes the audio track only.
                    if (mediaType == "audio")
                    {
                        // Get the RemoteAudioTrack object in the AgoraRTCRemoteUser object.
                        self.channelParameters.remoteAudioTrack = user.audioTrack;
                        // Play the remote audio track. No need to pass any DOM element.
                        self.channelParameters.remoteAudioTrack.play();
                    }
                    // Listen for the "user-unpublished" event.
                    agoraEngine.on("user-unpublished", user =>
                    {
                        console.log(user.uid+ "has left the channel");
                    });
                });
                window.onload = function ()
                {
                    self.joinVideo(agoraEngine,self.channelParameters,localPlayerContainer,self)
                }
            },
            getUrlVars()
            {
                var vars = [], hash;
                var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                for(var i = 0; i < hashes.length; i++)
                {
                    hash = hashes[i].split('=');
                    vars.push(hash[0]);
                    vars[hash[0]] = hash[1];
                }
                return vars;
            },
            async joinVideo(agoraEngine,channelParameters,localPlayerContainer,self) {
                console.log("local")
                // Join a channel.
                await agoraEngine.join(self.options.appId, self.options.channel, self.options.token, self.options.uid);
                // Create a local audio track from the audio sampled by a microphone.
                channelParameters.localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
                // Create a local video track from the video captured by a camera.
                channelParameters.localVideoTrack = await AgoraRTC.createCameraVideoTrack();
                // Append the local video container to the page body.
                document.body.append(localPlayerContainer);
                $(".localPlayerDiv").html(localPlayerContainer)
                $(localPlayerContainer).addClass("localPlayerLayer")
                // Publish the local audio and video tracks in the channel.
                await agoraEngine.publish([channelParameters.localAudioTrack, channelParameters.localVideoTrack]);
                // Play the local video track.
                channelParameters.localVideoTrack.play(localPlayerContainer);
                console.log("publish success!");
            },
            leaveChannel() {
                if(confirm("Are you sure you want to leave this channel?")) {
                    window.top.close();
                }
            },
            videoStreamingOnAndOff() {
                this.videoStreaming = this.videoStreaming ? false : true
                this.channelParameters.localVideoTrack.setEnabled(this.videoStreaming);
            },
            audioStreamingOnAnddOff() {
                this.audioStreaming = this.audioStreaming ? false : true
                this.channelParameters.localAudioTrack.setEnabled(this.audioStreaming);
            },
            hideDivAfterTimeout() {
                setTimeout(() => {
                    $(".iconCall").removeClass("fade-in");
                    this.showDiv = false;
                }, 10000);
            },
            showDivAgain() {
                this.showDiv = true;
                this.hideDivAfterTimeout();
            },
            clearTimeout() {
                // Clear the timeout if the component is about to be unmounted
                // to prevent memory leaks
                clearTimeout(this.timeoutId);
            },
            async ringingPhoneFunc() {
                await this.$refs.ringingPhone.play();
                let self = this;
                setTimeout(function() {
                    console.log("pause");
                    self.$refs.ringingPhone.pause();
                },60000);
            },
            submitPrescription() {
                if(this.prescription) {
                    const updatePrescription = {
                        code : this.referral_code,
                        prescription: this.prescription,
                        form_type: "normal",
                        activity_id: this.activity_id
                    }
                    axios.post(`${this.baseUrl}/api/video/prescription/update`, updatePrescription).then(response => {
                        console.log(response)
                        if(response.data === 'success') {
                            this.prescriptionSubmitted = true
                            Lobibox.alert("success",
                            {
                                msg: "Successfully submitted prescription!"
                            });
                        } else {
                            Lobibox.alert("error",
                            {
                                msg: "Error in server!"
                            });
                        }
                    });
                } else {
                    Lobibox.alert("error",
                    {
                        msg: "No prescription inputted!"
                    });
                }
            },
            generatePrescription() {
                const getPrescription = {
                    code : this.referral_code,
                    form_type : "normal",
                    activity_id : this.activity_id
                }
                axios.post(`${this.baseUrl}/api/video/prescription/check`, getPrescription).then((response) => {
                    console.log(response)
                    if(response.data === 'success') {
                        window.open(`${this.baseUrl}/doctor/print/prescription/${this.tracking_id}/${this.activity_id}`, '_blank');
                    } else {
                        Lobibox.alert("error",
                        {
                            msg: "No added prescription!"
                        });
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            endorseUpward() {
                let self = this
                Lobibox.confirm({
                    msg: "Do you want to endorse this patient for an upward level of referral?",
                    callback: function ($this, type, ev) {
                        if(type == 'yes') {
                            const endorseUpward = {
                                code : self.referral_code,
                                form_type: "normal"
                            }
                            axios.post(`${self.baseUrl}/api/video/upward`, endorseUpward).then(response => {
                                console.log(response.status)
                                if(response.data === 'success') {
                                    Lobibox.alert("success",
                                        {
                                            msg: "Successfully endorse the patient for upward referral!"
                                        });
                                } else {
                                    Lobibox.alert("error",
                                        {
                                            msg: "Error in server!"
                                        });
                                }
                            });
                        }
                    }
                });
            }
        },
    }
</script>
<template>
    <audio ref="ringingPhone" :src="ringingPhoneUrl" loop></audio>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="mainPic">
                    <div class="remotePlayerDiv">
                        <div id="calling">
                            <h3>Calling...</h3>
                        </div>
                        <img :src="doctorUrl" class="img-fluid" alt="Image1">
                    </div>
                    <Transition name="fade">
                    <div class="iconCall position-absolute fade-in" v-if="showDiv">
                        <button class="btn btn-success btn-lg mic-button" :class="{ 'mic-button-slash': !audioStreaming }" @click="audioStreamingOnAnddOff" type="button"><i class="bi-mic-fill"></i></button>&nbsp;
                        <button class="btn btn-success btn-lg video-button" :class="{ 'video-button-slash': !videoStreaming }" @click="videoStreamingOnAndOff" type="button"><i class="bi-camera-video-fill"></i></button>&nbsp;
                        <button class="btn btn-danger btn-lg decline-button" @click="leaveChannel" type="button"><i class="bi-telephone-x-fill"></i></button>&nbsp;
                        <button class="btn btn-warning btn-lg decline-button" @click="endorseUpward" type="button" v-if="referring_md == 'no'"><i class="bi-hospital"></i></button>
                    </div>
                    </Transition>
                    <div class="localPlayerDiv">
                        <img :src="doctorUrl1" id="local-image" class="img2" alt="Image2">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="telemedForm">
                <div class="row-fluid">
                    <div>
                        <img :src="dohLogoUrl" alt="Image3" class="dohLogo">
                    </div>
                    <div class="formHeader">
                        <p>Republic of the Philippines</p>
                        <p>DEPARTMENT OF HEALTH</p>
                        <p><b>CENTRAL VISAYAS CENTER for HEALTH DEVELOPMENT</b></p>
                        <p>Osmeña Boulevard Sambag II, Cebu City, 6000 Philippines</p>
                        <p>Regional Director's Office Tel. No. (032) 253-6355 Fax No. (032) 254-0109</p>
                        <p>Official Website: <span style="color: blue;">http://www.ro7.doh.gov.ph</span> Email Address: dohro7@gmail.com</p>
                    </div>
                    <div class="clinical">
                        <span style="color: #4CAF50;"><b>CLINICAL REFERRAL FORM</b></span>
                    </div>
                    <div class="tableForm">
                        <table class="table table-striped formTable">
                            <tr>
                                <td colspan="12">Name of Referring Facility: <span class="forDetails"> {{ form.referring_name }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Facility Contact #: <span class="forDetails"> {{ form.referring_contact }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Address: <span class="forDetails"> {{ form.referring_address }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="6">Referred to: <span class="forDetails"> {{ form.referred_name }} </span></td>
                                <td colspan="6">Department: <span class="forDetails"> {{ form.department }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Address: <span class="forDetails"> {{ form.referred_address }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="6">Date/Time Referred (ReCo): <span class="dateReferred"> {{ form.time_referred }} </span></td>
                                <td colspan="6">Date/Time Transferred:<span class="forDetails"> {{ form.time_transferred}} </span></td>
                            </tr>
                            <tr>
                                <td colspan="4">Name of Patient: <span class="forDetails"> {{ form.patient_name }} </span></td>
                                <td colspan="4">Age: <span class="forDetails"> {{ patient_age }} </span></td>
                                <td colspan="4">Sex: <span class="forDetails"> {{ form.patient_sex }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="6">Address: <span class="forDetails"> {{ form.patient_address }} </span></td>
                                <td colspan="6">Status: <span class="forDetails"> {{ form.patient_status }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="6">Philhealth status: <span class="forDetails"> {{ form.phic_status }} </span></td>
                                <td colspan="6">Philhealth #: <span class="forDetails"> {{ form.phic_id }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Covid Number: <span class="forDetails"> {{ form.covid_number }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Clinical Status: <span class="forDetails"> {{ form.refer_clinical_status }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Surviellance Category: <span class="forDetails"> {{ form.refer_sur_category }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Case Summary (pertinent Hx/PE, including meds, labs, course etc.): <br><span class="caseforDetails">{{ form.case_summary }}</span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist):<br><span class="recoSummary"> {{ form.reco_summary }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">ICD-10 Code and Description:
                                    <li v-for="i in icd">
                                        <span class="caseforDetails">{{ i.code }} - {{ i.description }}</span>
                                    </li>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="12">Reason for referral: <span class="forDetails"> {{ form.other_reason_referral }} </span></td>
                            </tr>
                            <tr v-if="file_path">
                                <td colspan="12">
                                    <span v-if="file_path.length > 1">File Attachments: </span>
                                    <span v-else>File Attachment: </span>
                                    <span v-for="(path, index) in file_path" :key="index">
                                            <a :href="path" :key="index" id="file_download" class="reason" target="_blank" download>{{ file_name[index] }}</a>
                                            <span v-if="index + 1 !== file_path.length">,&nbsp;</span>
                                        </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="12">Name of Referring MD/HCW: <span class="forDetails"> {{ form.md_referring }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Contact # of Referring MD/HCW: <span class="forDetails"> {{ form.referring_md_contact }} </span></td>
                            </tr>
                            <tr>
                                <td colspan="12">Name of referred MD/HCW-Mobile Contact # (ReCo): <br><span class="mdHcw"> {{ form.md_referred }} </span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
                <div v-if="referring_md == 'yes'">
                    <button class="btn btn-success btn-md btn-block" type="button" @click="generatePrescription()"><i class="bi bi-prescription"></i> Generate Prescription</button>
                </div>
                <div v-else>
                    <div class="row prescription" >
                        <div class="col">
                            <textarea class="form-control textArea" id="FormControlTextarea" v-model="prescription" rows="4"></textarea>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-success btn-md btn-block" type="button" @click="submitPrescription()" v-if="prescriptionSubmitted"><i class="bi bi-prescription"></i> Update Prescription</button>
                        <button class="btn btn-success btn-md btn-block" type="button" @click="submitPrescription()" v-else><i class="bi bi-prescription"></i> Submit Prescription</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .fade-enter,
    .fade-leave-to {
        animation: fadeOut 2s;
    }
    .fade-in {
        animation: fadeIn 2s;
    }
    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
    @keyframes fadeOut {
        0% { opacity: 1; }
        100% { opacity: 0; }
    }
    .container-fluid {
        border: 4px outset green;
        /*height: 978px;*/
        object-fit: cover;
    }
    #calling {
        display: flex;
        position: absolute;
        text-align: center;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
    .mainPic {
        position: relative;
        border: 2px outset transparent;
        height: 100%;
        width: 100%;
    }
    .img-fluid {
        border: 3px outset transparent;
        width: 100%;
    }
    .img2 {
        border-radius: 30px;
    }
    .iconCall {
        border: 1px outset transparent;
        width: 100%;
        bottom: 20px;
        text-align: center;
        opacity: 1;
        transition: opacity 0.5s ease-in-out;
    }
    .iconCall.hidden {
        display: none;
        opacity: 0;
    }
    .mic-button {
        border-radius: 50%;
    }
    .video-button {
        border-radius: 50%;
    }
    .decline-button {
        border-radius: 50%;
        border: 0;
    }

    .telemedForm {
        position: relative;
        border: 2px outset black;
        margin-top: 5px;
        height: 790px;
        padding: 0;
        font-size: 14px;
        font-family: Calibri;
    }
    .dohLogo {
        position: relative;
        border: 1px outset transparent;
        top: 10px;
        left: 10px;
        z-index: 2;
        height: 72px;
        width: 76px;
    }
    .formHeader {
        position: absolute;
        top: 15px;
        left: 95px;
        border: 1px outset transparent;
        text-align: center;
        line-height: .1px;
        font-size: 13px;
    }
    .clinical {
        position: relative;
        text-align: center;
        margin-top: 28px;
        border: 1px outset transparent;
        font-size: 18px;
        font-family: Calibri;
    }
    .tableForm {
        position: relative;
        border: 1px outset transparent;
        height: 655px;
        width: auto;
        text-align: left;
        line-height: 1.2;
        font-size: 14px;
        font-family: Calibri;

        display: flex;
        flex-direction: column;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .remotePlayerLayer {
        height: 966px;
    }
    .remotePlayerDiv {
        height: 960px;
        width: 100%;
    }
    .localPlayerLayer {
        height: 300px;
        width: 250px;
    }
    .localPlayerLayer div{
        border-radius: 10px;
    }
    .localPlayerDiv {
        position: absolute;
        right: 20px;
        bottom: 10px;
        border: 2px outset green;
        border-radius: 11px;
    }
    .prescription {
        position: relative;
        border: 2px outset transparent;
        margin-top: 5px;
        font-family: Calibri;
    }
    .textArea {
        border: 1px outset black;
    }
    .btn {
        position: relative;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .forDetails {
        color: #E18E0B;
    }
    .caseforDetails {
        color: #E18E0B;
        line-height: 1.2;
        white-space: pre-line;
    }
    .dateReferred {
        color: #E18E0B;
    }
    .recoSummary {
        color: #E18E0B;
        line-height: 1.2;
        white-space: pre-wrap;
    }
    .mdHcw {
        color: #E18E0B;
        line-height: 1.2;
    }
    tr:nth-child(odd) {
        background-color: #f2f2f2;
        border: 1px outset transparent;
    }
    tr:nth-child(even) {
        background-color: white;
        border: 1px outset transparent;
    }
    .mic-button:hover {
        background-color: rgba(2, 133, 221, 0.911);
        box-shadow: 0 0.5rem 1rem rgba(2, 133, 221, 0.911);
    }
    .mic-button-slash:before, .mic-button-slash:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        height: 2px;
        background-color: #FF0000;
    }
    .mic-button-slash:before {
        transform: rotate(-45deg);
        padding: 2px;
    }
    .mic-button-slash:after {
        transform: rotate(-45deg);
    }
    .video-button:hover {
        background-color: rgba(2, 133, 221, 0.911);
        box-shadow: 0 0.5rem 1rem rgba(2, 133, 221, 0.911);
    }
    .video-button-slash:before, .video-button-slash:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        height: 2px;
        background-color: #FF0000;
    }
    .video-button-slash:before {
        transform: rotate(-45deg);
        padding: 2px;
    }
    .video-button-slash:after {
        transform: rotate(-45deg);
    }
    .decline-button:hover {
        background-color: rgba(2, 133, 221, 0.911);
        box-shadow: 0 0.5rem 1rem rgba(2, 133, 221, 0.911);
    }


    /*MOBILE VIEWPORT*/
    @media only screen and (min-width: 280px) and (max-width: 280px) and (min-height: 653px) and (max-height: 653px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 32px;
            width: 36px;
        }
        .formHeader {
            position: absolute;
            top: 6px;
            left: 15px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 6.5px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 60px;
            border: 1px outset transparent;
            font-size: 11px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 120px;
            width: 100px;
        }
        .img-fluid {
            position: relative;
            height: 28vh;
            width: 100%;
        }
        .remotePlayerLayer {
            height: 601px;
        }
        .remotePlayerDiv {
            height: 649px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .img2 {
            height: 120px;
            width: 100px;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 653px) and (max-width: 653px) and (min-height: 280px) and (max-height: 280px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 15px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            top: 15px;
            left: 25px;
            border: 1px outset transparent;
            text-align: center;
        }
        .localPlayerDiv {
            top: 120px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 228px;
        }
        .remotePlayerDiv {
            height: 276px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 320px) and (max-width: 320px) and (min-height: 480px) and (max-height: 480px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 9px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 7px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 11px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 114px;
            width: 90px;
        }
        .img-fluid {
            position: relative;
            height: 44vh;
            width: 100%;
        }
        .img2 {
            height: 114px;
            width: 90px;
        }
        .remotePlayerLayer {
            height: 428px;
        }
        .remotePlayerDiv {
            height: 476px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 480px) and (max-width: 480px) and (min-height: 320px) and (max-height: 320px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 62px;
            width: 66px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 35px;
            border: 1px outset transparent;
            font-size: 14px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 114px;
            width: 90px;
        }
        .img-fluid {
            position: relative;
            height: 94vh;
            width: 100%;
        }
        .img2 {
            height: 114px;
            width: 90px;
        }
        .remotePlayerLayer {
            height: 268px;
        }
        .remotePlayerDiv {
            height: 316px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }

    @media only screen and (min-width: 320px) and (max-width: 320px) and (min-height: 568px) and (max-height: 568px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 9px;
            height: 646px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 7px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 11px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 114px;
            width: 90px;
        }
        .img-fluid {
            position: relative;
            height: 38vh;
            width: 100%;
        }
        .img2 {
            height: 114px;
            width: 90px;
        }
        .remotePlayerLayer {
            height: 516px;
        }
        .remotePlayerDiv {
            height: 565px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 568px) and (max-width: 568px) and (min-height: 320px) and (max-height: 320px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 645px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 62px;
            width: 66px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 11px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 35px;
            border: 1px outset transparent;
            font-size: 12px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 114px;
            width: 90px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 114px;
            width: 90px;
        }
        .remotePlayerLayer {
            height: 268px;
        }
        .remotePlayerDiv {
            height: 316px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }

    @media only screen and (min-width: 320px) and (max-width: 320px) and (min-height: 640px) and (max-height: 640px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 9px;
            height: 646px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 7px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 11px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 114px;
            width: 90px;
        }
        .img-fluid {
            position: relative;
            height: 34vh;
            width: 100%;
        }
        .img2 {
            height: 114px;
            width: 90px;
        }
        .remotePlayerLayer {
            height: 588px;
        }
        .remotePlayerDiv {
            height: 636px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 640px) and (max-width: 640px) and (min-height: 320px) and (max-height: 320px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 5px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .localPlayerDiv {
            top: 150px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 267px;
        }
        .remotePlayerDiv {
            height: 315px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 360px) and (max-width: 360px) and (min-height: 540px) and (max-height: 540px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 9px;
            height: 646px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 8.5px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 11px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 114px;
            width: 90px;
        }
        .img-fluid {
            position: relative;
            height: 43vh;
            width: 100%;
        }
        .img2 {
            height: 114px;
            width: 90px;
        }
        .remotePlayerLayer {
            height: 488px;
        }
        .remotePlayerDiv {
            height: 536px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 540px) and (max-width: 540px) and (min-height: 360px) and (max-height: 360px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 645px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 62px;
            width: 66px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 11px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 35px;
            border: 1px outset transparent;
            font-size: 12px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 114px;
            width: 90px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 114px;
            width: 90px;
        }
        .remotePlayerLayer {
            height: 308px;
        }
        .remotePlayerDiv {
            height: 356px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }

    @media only screen and (min-width: 360px) and (max-width: 360px) and (min-height: 600px) and (max-height: 640px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            top: 11px;
            left: 3px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 9px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 36vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 590px;
        }
        .remotePlayerDiv {
            height: 638px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 600px) and (max-width: 640px) and (min-height: 360px) and (max-height: 360px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 195px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 308px;
        }
        .remotePlayerDiv {
            height: 356px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 360px) and (max-width: 360px) and (min-height: 641px) and (max-height: 740px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 8px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 34vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 688px;
        }
        .remotePlayerDiv {
            height: 736px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 641px) and (max-width: 740px) and (min-height: 360px) and (max-height: 360px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 125px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 195px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 308px;
        }
        .remotePlayerDiv {
            height: 356px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 360px) and (max-width: 360px) and (min-height: 741px) and (max-height: 760px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            top: 11px;
            left: 3px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 9px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 708px;
        }
        .remotePlayerDiv {
            height: 756px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 741px) and (max-width: 760px) and (min-height: 360px) and (max-height: 360px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 140px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 195px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 308px;
        }
        .remotePlayerDiv {
            height: 356px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 360px) and (max-width: 360px) and (min-height: 761px) and (max-height: 780px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            top: 11px;
            left: 3px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 9px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 728px;
        }
        .remotePlayerDiv {
            height: 776px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 761px) and (max-width: 780px) and (min-height: 360px) and (max-height: 360px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 140px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 195px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 308px;
        }
        .remotePlayerDiv {
            height: 356px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 360px) and (max-width: 360px) and (min-height: 781px) and (max-height: 800px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 2%;
            right: 2%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 30vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 747px;
        }
        .remotePlayerDiv {
            height: 796px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 781px) and (max-width: 800px) and (min-height: 360px) and (max-height: 360px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 175px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 200px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 308px;
        }
        .remotePlayerDiv {
            height: 356px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 360px) and (max-width: 360px) and (min-height: 801px) and (max-height: 840px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 2%;
            right: 2%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 30vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 787px;
        }
        .remotePlayerDiv {
            height: 836px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 801px) and (max-width: 840px) and (min-height: 360px) and (max-height: 360px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 175px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 200px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 308px;
        }
        .remotePlayerDiv {
            height: 356px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 375px) and (max-width: 375px) and (min-height: 640px) and (max-height: 667px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 38vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 614px;
        }
        .remotePlayerDiv {
            height: 662px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 640px) and (max-width: 667px) and (min-height: 375px) and (max-height: 375px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .localPlayerDiv {
            top: 210px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 323px;
        }
        .remotePlayerDiv {
            height: 371px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 375px) and (max-width: 375px) and (min-height: 720px) and (max-height: 812px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            top: 11px;
            left: 11px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 9px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 40px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 762px;
        }
        .remotePlayerDiv {
            height: 810px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 720px) and (max-width: 812px) and (min-height: 375px) and (max-height: 375px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 18px;
            height: 639px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 92px;
            width: 96px;
        }
        .formHeader {
            position: absolute;
            top: 15px;
            left: 120px;
            border: 1px outset transparent;
            text-align: center;
            line-height: 1px;
            font-size: 16px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 15px;
            border: 1px outset transparent;
            font-size: 24px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 174px;
            width: 150px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 174px;
            width: 150px;
        }
        .remotePlayerLayer {
            height: 325px;
        }
        .remotePlayerDiv {
            height: 372px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 384px) and (max-width: 384px) and (min-height: 640px) and (max-height: 640px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 40vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 587px;
        }
        .remotePlayerDiv {
            height: 635px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 640px) and (max-width: 640px) and (min-height: 384px) and (max-height: 384px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 5px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 331px;
        }
        .remotePlayerDiv {
            height: 380px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 384px) and (max-width: 385px) and (min-height: 854px) and (max-height: 854px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 802px;
        }
        .remotePlayerDiv {
            height: 850px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 854px) and (max-width: 854px) and (min-height: 384px) and (max-height: 385px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 175px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 333px;
        }
        .remotePlayerDiv {
            height: 380px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 390px) and (max-width: 390px) and (min-height: 695px) and (max-height: 695px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 38vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 643px;
        }
        .remotePlayerDiv {
            height: 690px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 695px) and (max-width: 695px) and (min-height: 390px) and (max-height: 390px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 338px;
        }
        .remotePlayerDiv {
            height: 386px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 390px) and (max-width: 390px) and (min-height: 844px) and (max-height: 844px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 791px;
        }
        .remotePlayerDiv {
            height: 840px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 844px) and (max-width: 844px) and (min-height: 390px) and (max-height: 390px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 175px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 338px;
        }
        .remotePlayerDiv {
            height: 386px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 392px) and (max-width: 392px) and (min-height: 800px) and (max-height: 800px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 33vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 748px;
        }
        .remotePlayerDiv {
            height: 796px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 800px) and (max-width: 800px) and (min-height: 392px) and (max-height: 392px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 340px;
        }
        .remotePlayerDiv {
            height: 388px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 393px) and (max-width: 393px) and (min-height: 786px) and (max-height: 786px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 33vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 734px;
        }
        .remotePlayerDiv {
            height: 782px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 786px) and (max-width: 786px) and (min-height: 393px) and (max-height: 393px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 341px;
        }
        .remotePlayerDiv {
            height: 389px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 393px) and (max-width: 393px) and (min-height: 808px) and (max-height: 808px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 33vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 756px;
        }
        .remotePlayerDiv {
            height: 804px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 808px) and (max-width: 808px) and (min-height: 393px) and (max-height: 393px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 341px;
        }
        .remotePlayerDiv {
            height: 389px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 393px) and (max-width: 393px) and (min-height: 816px) and (max-height: 817px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 33vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 764px;
        }
        .remotePlayerDiv {
            height: 812px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 816px) and (max-width: 817px) and (min-height: 393px) and (max-height: 393px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 341px;
        }
        .remotePlayerDiv {
            height: 389px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 393px) and (max-width: 393px) and (min-height: 830px) and (max-height: 830px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 33vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 778px;
        }
        .remotePlayerDiv {
            height: 826px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 830px) and (max-width: 830px) and (min-height: 393px) and (max-height: 393px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 341px;
        }
        .remotePlayerDiv {
            height: 389px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 393px) and (max-width: 393px) and (min-height: 873px) and (max-height: 873px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 31vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 820px;
        }
        .remotePlayerDiv {
            height: 868px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 873px) and (max-width: 873px) and (min-height: 393px) and (max-height: 393px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 341px;
        }
        .remotePlayerDiv {
            height: 389px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 393px) and (max-width: 393px) and (min-height: 851px) and (max-height: 852px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 10px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }

        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }

        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .remotePlayerLayer {
            height: 799px;
        }
        .remotePlayerDiv {
            height: 847px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
        .btn {
            margin-bottom: 10px;
        }
    }
    @media only screen and (min-width: 851px) and (max-width: 852px) and (min-height: 393px) and (max-height: 393px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 230px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 341px;
        }
        .remotePlayerDiv {
            height: 389px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 400px) and (max-width: 400px) and (min-height: 880px) and (max-height: 880px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 40px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 828px;
        }
        .remotePlayerDiv {
            height: 876px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 880px) and (max-width: 880px) and (min-height: 400px) and (max-height: 400px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 235px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 348px;
        }
        .remotePlayerDiv {
            height: 396px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 411px) and (max-width: 412px) and (min-height: 731px) and (max-height: 732px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 38vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 679px;
        }
        .remotePlayerDiv {
            height: 727px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 731px) and (max-width: 732px) and (min-height: 411px) and (max-height: 412px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 360px;
        }
        .remotePlayerDiv {
            height: 408px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 411px) and (max-width: 411px) and (min-height: 960px) and (max-height: 960px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 30vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 60px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 907px;
        }
        .remotePlayerDiv {
            height: 955px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 960px) and (max-width: 960px) and (min-height: 411px) and (max-height: 411px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 358px;
        }
        .remotePlayerDiv {
            height: 406px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 412px) and (max-width: 412px) and (min-height: 823px) and (max-height: 847px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 34vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 794px;
        }
        .remotePlayerDiv {
            height: 843px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 823px) and (max-width: 847px) and (min-height: 412px) and (max-height: 412px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 360px;
        }
        .remotePlayerDiv {
            height: 408px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 412px) and (max-width: 412px) and (min-height: 848px) and (max-height: 892px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 840px;
        }
        .remotePlayerDiv {
            height: 887px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 848px) and (max-width: 892px) and (min-height: 412px) and (max-height: 412px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 362px;
        }
        .remotePlayerDiv {
            height: 409px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 412px) and (max-width: 412px) and (min-height: 906px) and (max-height: 915px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 863px;
        }
        .remotePlayerDiv {
            height: 911px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 906px) and (max-width: 915px) and (min-height: 412px) and (max-height: 412px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 220px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 360px;
        }
        .remotePlayerDiv {
            height: 408px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 412px) and (max-width: 412px) and (min-height: 916px) and (max-height: 919px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 866px;
        }
        .remotePlayerDiv {
            height: 914px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 916px) and (max-width: 919px) and (min-height: 412px) and (max-height: 412px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 360px;
        }
        .remotePlayerDiv {
            height: 408px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 412px) and (max-width: 412px) and (min-height: 1004px) and (max-height: 1004px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 28vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 936px;
        }
        .remotePlayerDiv {
            height: 984px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 1004px) and (max-width: 1004px) and (min-height: 412px) and (max-height: 412px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            top: 340px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 9px;
            height: 273px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .telemedForm {
            height: 395px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 8px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 12px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 140px;
            width: 114px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 250px;
            right: 0px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 140px;
            width: 114px;
        }
        .remotePlayerLayer {
            height: 350px;
        }
        .remotePlayerDiv {
            height: 400px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 414px) and (max-width: 414px) and (min-height: 736px) and (max-height: 736px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 38vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 684px;
        }
        .remotePlayerDiv {
            height: 732px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 736px) and (max-width: 736px) and (min-height: 414px) and (max-height: 414px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 362px;
        }
        .remotePlayerDiv {
            height: 410px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 414px) and (max-width: 414px) and (min-height: 896px) and (max-height: 896px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 843px;
        }
        .remotePlayerDiv {
            height: 891px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 896px) and (max-width: 896px) and (min-height: 414px) and (max-height: 414px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 200px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 362px;
        }
        .remotePlayerDiv {
            height: 410px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 428px) and (max-width: 428px) and (min-height: 926px) and (max-height: 926px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 860px;
        }
        .remotePlayerDiv {
            height: 907px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 926px) and (max-width: 926px) and (min-height: 428px) and (max-height: 428px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 370px;
        }
        .remotePlayerDiv {
            height: 418px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 430px) and (max-width: 430px) and (min-height: 932px) and (max-height: 932px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 32vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 866px;
        }
        .remotePlayerDiv {
            height: 914px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 932px) and (max-width: 932px) and (min-height: 430px) and (max-height: 430px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 250px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 372px;
        }
        .remotePlayerDiv {
            height: 420px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 432px) and (max-width: 432px) and (min-height: 768px) and (max-height: 768px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 37vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 704px;
        }
        .remotePlayerDiv {
            height: 752px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 768px) and (max-width: 768px) and (min-height: 432px) and (max-height: 432px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 15px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 260px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 374px;
        }
        .remotePlayerDiv {
            height: 422px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 540px) and (max-width: 540px) and (min-height: 960px) and (max-height: 960px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 40vh;
            width: 100%;
        }
        .img2 {
            height: 140px;
            width: 120px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 140px;
            width: 120px;
        }
        .remotePlayerLayer {
            height: 893px;
        }
        .remotePlayerDiv {
            height: 941px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 960px) and (max-width: 962px) and (min-height: 540px) and (max-height: 540px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 370px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 480px;
        }
        .remotePlayerDiv {
            height: 528px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    /*
    @media only screen and (min-width: 480px) and (max-width: 480px) and (min-height: 853px) and (max-height: 853px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 12px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 11px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 38vh;
            width: 100%;
        }
        .img2 {
            height: 140px;
            width: 120px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 30px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 140px;
            width: 120px;
        }
        .remotePlayerLayer {
            height: 788px;
        }
        .remotePlayerDiv {
            height: 836px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 853px) and (max-width: 853px) and (min-height: 480px) and (max-height: 480px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 15px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 310px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 421px;
        }
        .remotePlayerDiv {
            height: 469px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    */

    @media only screen and (min-width: 768px) and (max-width: 768px) and (min-height: 1076px) and (max-height: 1076px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 18px;
            height: 648px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 92px;
            width: 96px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 16px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 5px;
            border: 1px outset transparent;
            font-size: 24px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 50vh;
            width: 100%;
        }
        .img2 {
            height: 174px;
            width: 150px
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 174px;
            width: 150px
        }
        .remotePlayerLayer {
            height: 1008px;
        }
        .remotePlayerDiv {
            height: 1056px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 1076px) and (max-width: 1076px) and (min-height: 768px) and (max-height: 768px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 10px;
            height: 456px;
        }
        .telemedForm {
            height: 582px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 14px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 610px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 66vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 713px;
        }
        .remotePlayerDiv {
            height: 408px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 200px;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 884px) and (max-width: 884px) and (min-height: 1104px) and (max-height: 1104px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            left: 200px;
            border: 1px outset transparent;
        }
        .localPlayerDiv {
            top: 880px;
            right: 5px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .img-fluid {
            position: relative;
            height: 58vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 1051px;
        }
        .remotePlayerDiv {
            height: 1100px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }



    /*TAB VIEWPORT*/
    @media only screen and (min-width: 540px) and (max-width: 540px) and (min-height: 720px) and (max-height: 720px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            font-size: 11px;
            height: 640px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            top: 11px;
            left: 95px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 52vh;
            width: 100%;
        }
        .img2 {
            height: 140px;
            width: 120px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 140px;
            width: 120px;
        }
        .remotePlayerLayer {
            height: 668px;
        }
        .remotePlayerDiv {
            height: 716px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 720px) and (max-width: 720px) and (min-height: 540px) and (max-height: 540px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
       .formHeader {
            position: absolute;
            top: 11px;
            left: 125px;
            border: 1px outset transparent;
            text-align: center;
        }
        .localPlayerDiv {
            top: 370px;
            right: 20px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 95vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 488px;
        }
        .remotePlayerDiv {
            height: 536px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 600px) and (max-width: 601px) and (min-height: 960px) and (max-height: 962px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .telemedForm {
            height: 766px;
        }
        .tableForm {
            height: 630px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 62px;
            width: 66px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 42vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 910px;
        }
        .remotePlayerDiv {
            height: 958px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 960px) and (max-width: 962px) and (min-height: 600px) and (max-height: 601px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .localPlayerDiv {
            top: 430px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 100vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 550px;
        }
        .remotePlayerDiv {
            height: 597px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 768px) and (max-width: 768px) and (min-height: 1024px) and (max-height: 1024px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 18px;
            height: 635px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 92px;
            width: 96px;
        }
        .formHeader {
            position: absolute;
            top: 18px;
            left: 100px;
            border: 1px outset transparent;
            text-align: center;
            line-height: 1px;
            font-size: 16px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 18px;
            border: 1px outset transparent;
            font-size: 24px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 55vh;
            width: 100%;
        }
        .img2 {
            height: 174px;
            width: 150px
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 174px;
            width: 150px
        }
        .remotePlayerLayer {
            height: 972px;
        }
        .remotePlayerDiv {
            height: 1020px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 1024px) and (max-width: 1024px) and (min-height: 768px) and (max-height: 768px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            top: 665px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 11px;
            height: 669px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 20px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 8px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 12px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 65vh;
            width: 100%;
        }
        .img2 {
            height: 140px;
            width: 114px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 600px;
            right: 0px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 140px;
            width: 114px;
        }
        .remotePlayerLayer {
            height: 690px;
        }
        .remotePlayerDiv {
            height: 740px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 800px) and (max-width: 800px) and (min-height: 1280px) and (max-height: 1280px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 18px;
            height: 639px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 92px;
            width: 96px;
        }
        .formHeader {
            position: absolute;
            top: 15px;
            left: 120px;
            border: 1px outset transparent;
            text-align: center;
            line-height: 1px;
            font-size: 16px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 15px;
            border: 1px outset transparent;
            font-size: 24px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 45vh;
            width: 100%;
        }
        .img2 {
            height: 174px;
            width: 150px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 174px;
            width: 150px;
        }
        .remotePlayerLayer {
            height: 1228px;
        }
        .remotePlayerDiv {
            height: 1276px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 1280px) and (max-width: 1280px) and (min-height: 800px) and (max-height: 800px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 180px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 12px;
            height: 667px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 56px;
            width: 60px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 25px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-size: 15px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 80vh;
            width: 100%;
        }
        .img2 {
            height: 170px;
            width: 144px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 520px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 170px;
            width: 144px;
        }
        .remotePlayerLayer {
            height: 740px;
        }
        .remotePlayerDiv {
            height: 800px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 810px) and (max-width: 810px) and (min-height: 1080px) and (max-height: 1080px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 18px;
            height: 650px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 72px;
            width: 76px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 25px;
            border: 1px outset transparent;
            font-size: 24px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 50vh;
            width: 100%;
        }
        .img2 {
            height: 174px;
            width: 150px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 174px;
            width: 150px;
        }
        .remotePlayerLayer {
            height: 1028px;
        }
        .remotePlayerDiv {
            height: 1076px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 1080px) and (max-width: 1080px) and (min-height: 810px) and (max-height: 810px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 170px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 10px;
            height: 667px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 14px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 645px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 62vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 790px;
        }
        .remotePlayerDiv {
            height: 408px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 200px;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 820px) and (max-width: 820px) and (min-height: 1180px) and (max-height: 1180px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 18px;
            height: 639px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 92px;
            width: 96px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 16px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 15px;
            border: 1px outset transparent;
            font-size: 24px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 50vh;
            width: 100%;
        }
        .img2 {
            height: 174px;
            width: 150px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 174px;
            width: 150px;
        }
        .remotePlayerLayer {
            height: 1128px;
        }
        .remotePlayerDiv {
            height: 1176px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 1180px) and (max-width: 1180px) and (min-height: 820px) and (max-height: 820px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 170px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 12px;
            height: 665px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 9.5px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 14px;
            font-family: Calibri;
        }
        .localPlayerDiv {
            top: 655px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            height: 70z`vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .remotePlayerLayer {
            height: 790px;
        }
        .remotePlayerDiv {
            height: 408px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 200px;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 912px) and (max-width: 912px) and (min-height: 1368px) and (max-height: 1368px){
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 18px;
            height: 642px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 92px;
            width: 96px;
        }
        .formHeader {
            position: absolute;
            top: 15px;
            left: 175px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 16px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 13px;
            border: 1px outset transparent;
            font-size: 24px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 50vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 0;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px
        }
        .remotePlayerLayer {
            height: 1316px;
        }
        .remotePlayerDiv {
            height: 1364px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    @media only screen and (min-width: 1368px) and (max-width: 1368px) and (min-height: 912px) and (max-height: 912px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            top: 810px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 12px;
            height: 663px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 30px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 11px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 16px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 70vh;
            width: 100%;
        }
        .img2 {
            height: 180px;
            width: 154px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 700px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 180px;
            width: 154px;
        }
        .remotePlayerLayer {
            height: 850px;
        }
        .remotePlayerDiv {
            height: 900px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1024px) and (max-width: 1024px) and (min-height: 600px) and (max-height: 600px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            top: 525px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 8px;
            height: 299px;
        }
        .telemedForm {
            height: 421px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 42px;
            width: 46px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 20px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            font-size: 8px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 55px;
            border: 1px outset transparent;
            font-size: 12px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 80vh;
            width: 100%;
        }
        .img2 {
            height: 140px;
            width: 114px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 430px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 140px;
            width: 114px;
        }
        .remotePlayerLayer {
            height: 546px;
        }
        .remotePlayerDiv {
            height: 590px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }




    /*DESKTOP VIEWPORT*/
    @media only screen and (min-width: 1920px) and (max-width: 1920px) and (min-height: 1080px) and (max-height: 1080px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 18px;
            height: 760px;
        }
        .telemedForm {
            height: 900px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 72px;
            width: 76px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 25px;
            border: 1px outset transparent;
            font-size: 24px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 82vh;
            width: 100%;
        }
        .img2 {
            height: 274px;
            width: 250px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 274px;
            width: 250px;
        }
        .remotePlayerLayer {
            height: 1028px;
        }
        .remotePlayerDiv {
            height: 1070px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1680px) and (max-width: 1680px) and (min-height: 1050px) and (max-height: 1050px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 745px;
        }
        .telemedForm {
            height: 870px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 76vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 996px;
        }
        .remotePlayerDiv {
            height: 1044px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1664px) and (max-width: 1664px) and (min-height: 1110px) and (max-height: 1110px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 803px;
        }
        .telemedForm {
            height: 931px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 72vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 1050px;
        }
        .remotePlayerDiv {
            height: 1100px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1620px) and (max-width: 1620px) and (min-height: 1080px) and (max-height: 1080px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 773px;
        }
        .telemedForm {
            height: 901px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 72vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 1020px;
        }
        .remotePlayerDiv {
            height: 1072px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1600px) and (max-width: 1600px) and (min-height: 1024px) and (max-height: 1024px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 720px;
        }
        .telemedForm {
            height: 845px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 76vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 970px;
        }
        .remotePlayerDiv {
            height: 1010px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1600px) and (max-width: 1600px) and (min-height: 900px) and (max-height: 900px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 593px;
        }
        .telemedForm {
            height: 721px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 82vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 810px;
        }
        .remotePlayerDiv {
            height: 859px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1536px) and (max-width: 1536px) and (min-height: 901px) and (max-height: 960px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 652px;
        }
        .telemedForm {
            height: 781px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 76vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 890px;
        }
        .remotePlayerDiv {
            height: 940px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1536px) and (max-width: 1536px) and (min-height: 864px) and (max-height: 864px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 557px;
        }
        .telemedForm {
            height: 685px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 82vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 810px;
        }
        .remotePlayerDiv {
            height: 859px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1500px) and (max-width: 1504px) and (min-height: 1000px) and (max-height: 1003px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 695px;
        }
        .telemedForm {
            height: 824px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 72vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 940px;
        }
        .remotePlayerDiv {
            height: 995px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1440px) and (max-width: 1440px) and (min-height: 901px) and (max-height: 960px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 647px;
        }
        .telemedForm {
            height: 781px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 12px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 72vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 900px;
        }
        .remotePlayerDiv {
            height: 950px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1440px) and (max-width: 1440px) and (min-height: 900px) and (max-height: 900px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 590px;
        }
        .telemedForm {
            height: 723px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 12px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 78vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 847px;
        }
        .remotePlayerDiv {
            height: 860px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1368px) and (max-width: 1368px) and (min-height: 912px) and (max-height: 912px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 606px;
        }
        .telemedForm {
            height: 733px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 11px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-family: Calibri;
            font-size: 16px;
        }
        .img-fluid {
            position: relative;
            height: 74vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 850px;
        }
        .remotePlayerDiv {
            height: 900px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1360px) and (max-width: 1366px) and (min-height: 768px)  and (max-height: 768px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            height: 462px;
            font-size: 11px;
        }
        .telemedForm {
            height: 590px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-0%, -50%);
            font-size: 11px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-family: Calibri;
            font-size: 16px;
        }
        .img-fluid {
            position: relative;
            height: 84vh;
            width: 100%;
        }
        .img2 {
            height: 204px;
            width: 180px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 20px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 204px;
            width: 180px;
        }
        .remotePlayerLayer {
            height: 715px;
        }
        .remotePlayerDiv {
            height: 760px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1280px) and (max-width: 1280px) and (min-height: 1024px) and (max-height: 1024px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 11px;
            height: 721px;
        }
        .telemedForm{
            height: 845px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 56px;
            width: 60px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 25px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-size: 13px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 60vh;
            width: 100%;
        }
        .img2 {
            height: 200px;
            width: 174px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 10px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 200px;
            width: 174px;
        }
        .remotePlayerLayer {
            height: 960px;
        }
        .remotePlayerDiv {
            height: 1010px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1280px) and (max-width: 1280px) and (min-height: 960px) and (max-height: 960px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 11px;
            height: 657px;
        }
        .telemedForm{
            height: 781px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 56px;
            width: 60px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 25px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-size: 13px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 63vh;
            width: 100%;
        }
        .img2 {
            height: 200px;
            width: 174px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 10px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 200px;
            width: 174px;
        }
        .remotePlayerLayer {
            height: 906px;
        }
        .remotePlayerDiv {
            height: 950px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1280px) and (max-width: 1280px) and (min-height: 920px) and (max-height: 920px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 11px;
            height: 617px;
        }
        .telemedForm{
            height: 741px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 56px;
            width: 60px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 25px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-size: 13px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 64vh;
            width: 100%;
        }
        .img2 {
            height: 200px;
            width: 174px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 10px;
            bottom: 15px;
        }
        .localPlayerLayer{
            height: 200px;
            width: 174px;
        }
        .remotePlayerLayer {
            height: 866px;
        }
        .remotePlayerDiv {
            height: 910px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1280px) and (max-width: 1280px) and (min-height: 800px) and (max-height: 800px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 11px;
            height: 498px;
        }
        .telemedForm{
            height: 621px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 56px;
            width: 60px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 35px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-size: 13px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 78vh;
            width: 100%;
        }
        .img2 {
            height: 170px;
            width: 144px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 10px;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 170px;
            width: 144px;
        }
        .remotePlayerLayer {
            height: 740px;
        }
        .remotePlayerDiv {
            height: 790px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1280px) and (max-width: 1280px) and (min-height: 768px) and (max-height: 768px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 11px;
            height: 467px;
        }
        .telemedForm{
            height: 589px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 56px;
            width: 60px;
        }
        .formHeader {
            position: absolute;
            top: 10px;
            left: 35px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            font-size: 10px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 40px;
            border: 1px outset transparent;
            font-size: 13px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 79vh;
            width: 100%;
        }
        .img2 {
            height: 170px;
            width: 144px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: auto;
            right: 10px;
            bottom: 20px;
        }
        .localPlayerLayer{
            height: 170px;
            width: 144px;
        }
        .remotePlayerLayer {
            height: 714px;
        }
        .remotePlayerDiv {
            height: 760px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1280px) and (max-width: 1280px) and (min-height: 720px) and (max-height: 720px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 11px;
            height: 420px;
        }
        .telemedForm{
            height: 542px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 13px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 84vh;
            width: 100%;
        }
        .img2 {
            height: 170px;
            width: 144px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 530px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 170px;
            width: 144px;
        }
        .remotePlayerLayer {
           height: 667px;
        }
        .remotePlayerDiv {
            height: 715px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 1152px) and (max-width: 1152px) and (min-height: 864px) and (max-height: 864px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 10px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 11px;
            height: 562px;
        }
        .telemedForm{
            height: 685px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 52px;
            width: 56px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 9px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 45px;
            border: 1px outset transparent;
            font-size: 13px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 64vh;
            width: 100%;
        }
        .img2 {
            height: 170px;
            width: 144px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 675px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 170px;
            width: 144px;
        }
        .remotePlayerLayer {
            height: 810px;
        }
        .remotePlayerDiv {
            height: 850px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }

    @media only screen and (min-width: 800px) and (max-width: 800px) and (min-height: 600px) and (max-height: 600px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 15px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .tableForm {
            font-size: 15px;
            height: 555px;
        }
        .telemedForm{
            height: 685px;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 15px;
            left: 15px;
            z-index: 2;
            height: 72px;
            width: 76px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
            font-size: 13px;
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 25px;
            border: 1px outset transparent;
            font-size: 18px;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 90vh;
            width: 100%;
        }
        .img2 {
            height: 170px;
            width: 144px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 400px;
            right: 10px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 170px;
            width: 144px;
        }
        .remotePlayerLayer {
            height: 547px;
        }
        .remotePlayerDiv {
            height: 596px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }


    /*X-Small devices (portrait phones, less than 576px)*/
   /* @media only screen and (min-width: 541px) and (max-width: 575.98px){
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 5px;
            left: 5px;
            z-index: 2;
            height: 62px;
            width: 66px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 1%;
            right: 1%;
            transform: translate(-1%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 35px;
            border: 1px outset transparent;
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 52vh;
            width: 100%;
        }
        .img2 {
             height: 134px;
             width: 110px;
         }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 670px;
        }
        .remotePlayerDiv {
            height: 720px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }

    }*/
    /*------------------------------------------------------------------------------------*/

    /*Small devices (landscape phones, less than 768px)*/
    /*@media only screen and (min-width: 576px) and (max-width: 767.98px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 62px;
            width: 66px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 35px;
            border: 1px outset transparent;
            !*font-size: 15px;*!
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 52vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 670px;
        }
        .remotePlayerDiv {
            height: 720px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }

    }*/
    /*------------------------------------------------------------------------------------*/

    /*Medium devices (tablets, less than 992px)*/
   /* @media only screen and (min-width: 769px) and (max-width: 991.98px) {
        .col-lg-8 {
            background-color: black;
        }
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
            height: auto;
        }
        .dohLogo {
            position: relative;
            border: 1px outset transparent;
            top: 10px;
            left: 10px;
            z-index: 2;
            height: 62px;
            width: 66px;
        }
        .formHeader {
            position: absolute;
            border: 1px outset transparent;
            text-align: center;
            line-height: .1px;
            top: 60px;
            left: 5%;
            right: 5%;
            transform: translate(-0%, -50%);
        }
        .clinical {
            position: relative;
            text-align: center;
            margin-top: 35px;
            border: 1px outset transparent;
            !*font-size: 15px;*!
            font-family: Calibri;
        }
        .img-fluid {
            position: relative;
            height: 52vh;
            width: 100%;
        }
        .img2 {
            height: 134px;
            width: 110px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            right: 0;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 134px;
            width: 110px;
        }
        .remotePlayerLayer {
            height: 670px;
        }
        .remotePlayerDiv {
            height: 720px;
            width: 100%;
            border: 2px outset transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }

    }*/
    /*------------------------------------------------------------------------------------*/
    /*Large devices (desktops, less than 1200px)*/
   /* @media (max-width: 1199.98px) {

    }*/
    /*------------------------------------------------------------------------------------*/
    /*X-Large devices (large desktops, less than 1400px)*/
   /* @media (max-width: 1399.98px) {

    }*/
    /*------------------------------------------------------------------------------------*/

</style>
