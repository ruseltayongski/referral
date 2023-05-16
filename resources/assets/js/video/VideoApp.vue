<script>
    import axios from 'axios';

    import AgoraRTC from "agora-rtc-sdk-ng"
    export default {
        name: 'RecoApp',
        components: {
        },
        data() {
            return {
                baseUrl: $("#broadcasting_url").val(),
                doctorUrl: $("#broadcasting_url").val()+"/resources/img/video/Doctor5.png",
                doctorUrl1: $("#broadcasting_url").val()+"/resources/img/video/Doctor1.png",
                declineUrl: $("#broadcasting_url").val()+"/resources/img/video/decline.png",
                videoCallUrl: $("#broadcasting_url").val()+"/resources/img/video/videocall.png",
                micUrl: $("#broadcasting_url").val()+"/resources/img/video/mic.png",
                dohLogoUrl: $("#broadcasting_url").val()+"/resources/img/video/doh-logo.png",
                tracking_id: this.getUrlVars()["id"],
                options: {
                    // Pass your App ID here.
                    appId: 'da7a671355bc4560bb7b8a53bd7b2a96',
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
                }
            }
        },
        mounted() {
            axios
                .get(`${this.baseUrl}/doctor/referral/video/normal/form/${this.tracking_id}`)
                .then((res) => {
                    const response = res.data;
                    this.form = response.form
                    if(response.ageType === "y")
                        this.patient_age = response.age + " Years Old"
                    else if(response.ageType === "m")
                        this.patient_age =  response.age + " Months Old"

                    if(count(response.file_path) > 1)
                        this.file_path = "File Attachments:" + response.file_path
                    else
                        this.file_path = "File Attachment:" + response.file_path

                    /*for(i = 0; i < count(response.file_path); i++)
                        this.file_path = response.file_path*/

                    /*if(i + 1 != count(response.file_path))*/
                            /*,&nbsp*/


                    console.log(response)
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        props : ["user"],
        created() {
            console.log(this.user)
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
                        document.body.append(remotePlayerContainer);
                        $(".remotePlayerDiv").html(remotePlayerContainer)
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
            formatTextWithLineBreaks(text) {
                return text
            }
        },


    }
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8" style="padding: 0;">
                <div class="mainPic">
                    <div class="remotePlayerDiv">
                        <img :src="doctorUrl" class="img-fluid" alt="Image1">
                    </div>
                    <div class="iconCall position-absolute">
                        <button class="btn btn-success btn-lg mic-button" :class="{ 'mic-button-slash': !audioStreaming }" @click="audioStreamingOnAnddOff" type="button"><i class="bi-mic-fill"></i></button>&nbsp;
                        <button class="btn btn-success btn-lg video-button" :class="{ 'video-button-slash': !videoStreaming }" @click="videoStreamingOnAndOff" type="button"><i class="bi-camera-video-fill"></i></button>&nbsp;
                        <button class="btn btn-danger  btn-lg decline-button" @click="leaveChannel" type="button"><i class="bi-telephone-x-fill"></i></button>
                    </div>
                    <div class="localPlayerDiv">
                        <img :src="doctorUrl1" id="local-image" class="img2" alt="Image2">
                    </div>
                </div>
            </div>
            <div class="col-lg-4" style="padding: 0;">
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
                                    <td colspan="12">ICD-10 Code and Description: <br><span class="forDetails"> {{ form.other_diagnoses }}</span></td>
                                </tr>
                                <tr>
                                    <td colspan="12">Reason for referral: <span class="forDetails"> {{ form.other_reason_referral }} </span></td>
                                </tr>
                                <tr>
                                    <td colspan="12">File Attachments:
                                        <a :href="file_path" id="file_download" class="reason" target="_blank" style="font-size: 12pt;" download>{{ file_name }}</a>
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
                <div class="row prescription">
                    <div class="col">
                        <textarea class="form-control textArea" id="FormControlTextarea" rows="4"></textarea>
                    </div>
                </div>
                <div>
                    <button class="btn btn-success btn-md btn-block" type="button" onclick="alert('Successfuly Submit')">Submit</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .container-fluid {
        border: 4px outset green;
        height: auto;
    }

    .mainPic {
        position: relative;
        border: 2px outset transparent;
        height: 100%;
        width: 100%;
    }

    .remotePlayerLayer {
        height: 960px;
    }

    .remotePlayerDiv {
        height: 960px;
        width: 100%;
        border: 2px outset transparent;
    }

    .localPlayerLayer {
        height: 300px;
        width: 250px;
    }

    .localPlayerLayer div{
        border-radius: 30px;
    }

    .localPlayerDiv {
        position: absolute;
        right: 20px;
        bottom: 20px;
        border: 2px outset green;
        border-radius: 32px;
    }

    .img-fluid {
        border: 3px outset transparent;
        width: 100%;
        height: 963px;
    }
    .img2 {
        border-radius: 30px;
    }
    .iconCall {
        border: 1px outset transparent;
        width: 100%;
        bottom: 220px;
        text-align: center;
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
        height: 797px;
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
        left: 105px;
        border: 1px outset transparent;
        text-align: center;
        line-height: .0;
        font-size: 13px;
    }
    .clinical {
        position: relative;
        text-align: center;
        margin-top: 25px;
        border: 1px outset transparent;
        font-size: 20px;
        font-family: Calibri;
    }
    .tableForm {
        position: relative;
        border: 1px outset transparent;
        height: 664px;
        width: auto;
        text-align: left;
        line-height: 1;
        font-weight: bold;
        font-size: 14px;
        font-family: Calibri;

        display: flex;
        flex-direction: column;
        overflow-y: auto;
        overflow-x: hidden;
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
        background-color: #FF0000; /* set the color of the lines */
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
        background-color: #FF0000; /* set the color of the lines */
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
    /*------------------------------------------------------------------------------------*/
    /*X-Small devices (portrait phones, less than 576px)*/
    @media (max-width: 575.98px) {
        .iconCall {
            bottom: 20px;
        }
        .container-fluid {
            border: 1px outset green;
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
            top: 15px;
            left: 25px;
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

        .tableForm {
            font-size: 10px;
        }
        .localPlayerDiv {
            top: 20px;
            bottom: auto;
        }
        .localPlayerLayer{
            height: 150px;
            width: 110px;
        }
        .img-fluid {
            position: relative;
            border: 1px outset transparent;
            height: 100%;
            width: auto;
        }
        .remotePlayerLayer {
            height: 860px;
        }

        .remotePlayerDiv {
            height: 860px;
            width: 100%;
            border: 2px outset transparent;
            background-color:red;
        }
        .img2 {
            height: 150px;
            width: 110px;
        }
        .remotePlayerLayer div video {
            object-fit: contain !important;
        }
    }
    /*Small devices (landscape phones, less than 768px)*/
    @media (max-width: 767.98px) {

    }
    /*Medium devices (tablets, less than 992px)*/
    @media (max-width: 991.98px) {

    }
    /*Large devices (desktops, less than 1200px)*/
    @media (max-width: 1199.98px) {

    }
    /*X-Large devices (large desktops, less than 1400px)*/
    @media (max-width: 1399.98px) {

    }

    @media (max-width: 321px) {
        .remotePlayerDiv {
            height: 600px;
            background-color:blue;
        }
        .remotePlayerLayer {
            height: 600px;
        }
    }
</style>
