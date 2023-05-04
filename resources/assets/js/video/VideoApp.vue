
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
                doctorUrl: $("#broadcasting_url").val()+"/resources/img/video/Doctor.jpg",
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
                    channel: '',
                    // Pass your temp token here.
                    token: null,
                    // Set the user ID.
                    uid: 0,
                },
                form: {}
            }
        },

        mounted() {
            axios
                .get(`${this.baseUrl}/doctor/referral/video/normal/form/${this.tracking_id}`)
                .then((res) => {
                    const response = res.data;
                    this.form = response.form
                    console.log(response.form)
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        props : ["user"],
        created() {
            console.log(this.user)
            this.startBasicCall();
            // Remove the video stream from the container.
        },
        methods: {
            async startBasicCall()
            {
                /*let options =
                    {
                        // Pass your App ID here.
                        appId: 'da7a671355bc4560bb7b8a53bd7b2a96',
                        // Set the channel name.
                        channel: 'rusel',
                        // Pass your temp token here.
                        token: null,
                        // Set the user ID.
                        uid: 0,
                    };*/

                let channelParameters =
                    {
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
                    };

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
                        channelParameters.remoteVideoTrack = user.videoTrack;
                        // Retrieve the remote audio track.
                        channelParameters.remoteAudioTrack = user.audioTrack;
                        // Save the remote user id for reuse.
                        channelParameters.remoteUid = user.uid.toString();
                        // Specify the ID of the DIV container. You can use the uid of the remote user.
                        remotePlayerContainer.id = user.uid.toString();
                        channelParameters.remoteUid = user.uid.toString();
                        /*remotePlayerContainer.textContent = "Remote user " + user.uid.toString();*/
                        // Append the remote container to the page body.
                        document.body.append(remotePlayerContainer);
                        $(".divImage1").html(remotePlayerContainer)
                        $(remotePlayerContainer).addClass("image1")
                        // Play the remote video track.
                        channelParameters.remoteVideoTrack.play(remotePlayerContainer);
                    }
                    // Subscribe and play the remote audio track If the remote user publishes the audio track only.
                    if (mediaType == "audio")
                    {
                        // Get the RemoteAudioTrack object in the AgoraRTCRemoteUser object.
                        channelParameters.remoteAudioTrack = user.audioTrack;
                        // Play the remote audio track. No need to pass any DOM element.
                        channelParameters.remoteAudioTrack.play();
                    }
                    // Listen for the "user-unpublished" event.
                    agoraEngine.on("user-unpublished", user =>
                    {
                        console.log(user.uid+ "has left the channel");
                    });
                });
                let self =  this
                window.onload = function ()
                {
                    // Listen to the Join button click event.
                    document.getElementById("join").onclick = async function ()
                    {
                        console.log("local")
                        // Join a channel.
                        await agoraEngine.join(self.options.appId, self.options.channel, self.options.token, self.options.uid);
                        // Create a local audio track from the audio sampled by a microphone.
                        channelParameters.localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
                        // Create a local video track from the video captured by a camera.
                        channelParameters.localVideoTrack = await AgoraRTC.createCameraVideoTrack();
                        // Append the local video container to the page body.
                        document.body.append(localPlayerContainer);
                        $(".divImage2").html(localPlayerContainer)
                        $(localPlayerContainer).addClass("image2")
                        // Publish the local audio and video tracks in the channel.
                        await agoraEngine.publish([channelParameters.localAudioTrack, channelParameters.localVideoTrack]);
                        // Play the local video track.
                        channelParameters.localVideoTrack.play(localPlayerContainer);
                        console.log("publish success!");
                    }
                    // Listen to the Leave button click event.
                    document.getElementById('leave').onclick = async function ()
                    {
                        // Destroy the local audio and video tracks.
                        channelParameters.localAudioTrack.close();
                        channelParameters.localVideoTrack.close();
                        // Remove the containers you created for the local video and remote video.
                        this.removeVideoDiv(remotePlayerContainer.id);
                        this.removeVideoDiv(localPlayerContainer.id);
                        // Leave the channel
                        await agoraEngine.leave();
                        console.log("You left the channel");
                        // Refresh the page for reuse
                        window.location.reload();
                    }
                }
            },
            removeVideoDiv(elementId)
            {
                console.log("Removing "+ elementId+"Div");
                let Div = document.getElementById(elementId);
                if (Div)
                {
                    Div.remove();
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
            }
        }

    }

</script>

<template>

    <div class="container">
        <div class="myDiv">
            <div class="divImage1">
                <img :src="doctorUrl" alt="Image 1" class="image1">
            </div>
            <div class="divImage2">
                <img :src="doctorUrl1" class="image2" alt="Image 2">
            </div>
            <button class="decline-button" onclick="alert('Hello, world!')"><img :src="declineUrl" alt="Button Image"></button>
            <button class="video-button" type="button" ><img :src="videoCallUrl" alt="Button Image"></button>
            <button class="mic-button" type="button" ><img :src="micUrl" alt="Button Image"></button>
        </div>

        <div class="myDiv2">
            <img :src="dohLogoUrl" alt="Image 3" class="doh-logo">
            <div class="myDiv4">
                <p>Republic of the Philippines</p>
                <p>DEPARTMENT OF HEALTH</p>
                <p><b>CENTRAL VISAYAS CENTER for HEALTH DEVELOPMENT</b></p>
                <p>Osmeña Boulevard Sambag II, Cebu City, 6000 Philippines</p>
                <p>Regional Director's Office Tel. No. (032) 253-6355 Fax No. (032) 254-0109</p>
                <p>Official Website: <span style="color: blue;">http://www.ro7.doh.gov.ph</span> Email Address: dohro7@gmail.com</p>
            </div>

            <div class="myDiv5">
                <p><span style="color: #4CAF50;"><b>CLINICAL REFERRAL FORM</b></span></p>
            </div>

            <!--div-- class="myDiv6">
                <div class="box"><p>Name of Referring Facility: <span style="color: #E18E0B;"> {{ form.referring_name }} </span></p></div>
                <div class="box"><p>Facility Contact #: <span style="color: #E18E0B;"> {{ form.referring_contact }} </span></p></div>
                <div class="box"><p>Address: <span style="color: #E18E0B;"> {{ form.referring_address }} </span></p></div>
                <div class="box"><p>Referred to: <span style="color: #E18E0B;"> {{ form.referred_name }} </span></p></div>
                <div class="box"><p>Address: <span style="color: #E18E0B;"> {{ form.referred_address }} </span></p></div>
                <div class="box"><p>Date/Time Referred (ReCo): <span style="color: #E18E0B;"> {{ form.time_referred }} </span></p></div>
                <div class="box"><p>Name of Patient: <span style="color: #E18E0B;"> {{ form.patient_name }} </span></p></div>
                <div class="box"><p>Address: <span style="color: #E18E0B;"> {{ form.patient_address }} </span></p></div>
                <div class="box"><p>Philhealth status: <span style="color: #E18E0B;"> {{ form.phic_status }} </span></p></div>
                <div class="box"><p>Covid Number: <span style="color: #E18E0B;"> {{ form.covid_number }} </span></p></div>
                <div class="box"><p>Clinical Status: <span style="color: #E18E0B;"> {{ form.refer_clinical_status }} </span></p></div>
                <div class="box"><p>Surviellance Category: <span style="color: #E18E0B;"> {{  }} </span></p></div>

                <div class="deptbox"><p>Department: <span style="color: #E18E0B;"> {{ form.department }} </span></p></div>
                <div class="transbox"><p>Date/Time Transferred: <span style="color: #E18E0B;"> {{ form.time_transferred}} </span></p></div>
                <div class="agebox"><p>Age: <span style="color: #E18E0B;"></span></p></div>
                <div class="sexbox"><p>Sex: <span style="color: #E18E0B;"> {{ form.patient_sex }} </span></p></div>
                <div class="statusbox"><p>Status: <span style="color: #E18E0B;"> {{ form.patient_status }} </span></p></div>
                <div class="philbox"><p>Philhealth #: <span style="color: #E18E0B;"> {{ form.phic_id }} </span></p></div>

                <div class="divbox1"><p>Case Summary (pertinent Hx/PE, including meds, labs, course etc.): <br><span style="color: #E18E0B; line-height: 2;"> {{ form.case_summary }} </span></p>
                    <div class="divbox2"><p>Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist): <br><span style="color: #E18E0B; line-height: 2;"> {{ form.reco_summary }} </span></p></div>
                    <div class="boxDiv">
                        <div class="divbox3"><p>Other Diagnoses: <span style="color: #E18E0B;"> {{ form.other_diagnoses }} </span></p></div>
                        <div class="divbox3"><p>Reason for referral: <span style="color: #E18E0B;"> {{ form.other_reason_referral }} </span></p></div>
                        <div class="divbox3"><p>File Attachment: <span style="color: #E18E0B;"> {{ form.phic_id }} </span></p></div>
                        <div class="divbox3"><p>Name of Referring MD/HCW: <span style="color: #E18E0B;"> {{ form.md_referring }} </span></p></div>
                        <div class="divbox3"><p>Contact # of Referring MD/HCW: <span style="color: #E18E0B;"> {{ form.referring_md_contact }} </span></p></div>
                        <div class="divbox3"><p>Name of referred MD/HCW-Mobile Contact # (ReCo): <span style="color: #E18E0B;"> {{ form.phic_id }} </span></p></div>
                    </div>
                </div>

            </div-->

            <div class="myDiv6">
                <table>
                    <tr>
                        <td>Name of Referring Facility: <span style="color: #E18E0B;"> {{ form.referring_name }} </span></td>
                    </tr>
                    <tr>
                        <td>Facility Contact #: <span style="color: #E18E0B;"> {{ form.referring_contact }} </span></td>
                    </tr>
                    <tr>
                        <td>Address: <span style="color: #E18E0B;"> {{ form.referring_address }} </span></td>
                    </tr>
                    <tr>
                        <td>Referred to: <span style="color: #E18E0B;"> {{ form.referred_name }} </span></td>
                    </tr>
                    <tr>
                        <td>Address: <span style="color: #E18E0B;"> {{ form.referred_address }} </span></td>
                    </tr>
                    <tr>
                        <td>Date/Time Referred (ReCo): <span style="color: #E18E0B;"> {{ form.time_referred }} </span></td>
                    </tr>
                    <tr>
                        <td>Name of Patient: <span style="color: #E18E0B;"> {{ form.patient_name }} </span></td>
                    </tr>
                    <tr>
                        <td>Address: <span style="color: #E18E0B;"> {{ form.patient_address }} </span></td>
                    </tr>
                    <tr>
                        <td>Philhealth status: <span style="color: #E18E0B;"> {{ form.phic_status }} </span></td>
                    </tr>
                    <tr>
                        <td>Covid Number: <span style="color: #E18E0B;"> {{ form.covid_number }} </span></td>
                    </tr>
                    <tr>
                        <td>Clinical Status: <span style="color: #E18E0B;"> {{ form.refer_clinical_status }} </span></td>
                    </tr>
                    <tr>
                        <td>Surviellance Category: <span style="color: #E18E0B;"> {{  }} </span></td>
                    </tr>

                    <tr>
                        <td>Case Summary (pertinent Hx/PE, including meds, labs, course etc.): <br><span style="color: #E18E0B;"> {{ form.case_summary }} </span></td>
                    </tr>
                    <tr>
                        <td>Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist): <br><span style="color: #E18E0B;"> {{ form.reco_summary }} </span></td>
                    </tr>
                    <tr>
                        <td>ICD-10 Code and Description: <span style="color: #E18E0B;"> {{ form.other_diagnoses }} </span></td>
                    </tr>
                    <tr>
                        <td>Reason for referral: <span style="color: #E18E0B;"> {{ form.other_reason_referral }} </span></td>
                    </tr>
                    <tr>
                        <td>File Attachment: <span style="color: #E18E0B;"> {{ form.phic_id }} </span></td>
                    </tr>
                    <tr>
                        <td>Name of Referring MD/HCW: <span style="color: #E18E0B;"> {{ form.md_referring }} </span></td>
                    </tr>
                    <tr>
                        <td>Contact # of Referring MD/HCW: <span style="color: #E18E0B;"> {{ form.referring_md_contact }} </span></td>
                    </tr>
                    <tr>
                        <td>Name of referred MD/HCW-Mobile Contact # (ReCo): <span style="color: #E18E0B;"> {{ form.phic_id }} </span></td>
                    </tr>


                    <!--div class="deptbox"><td style="background-color: white;">Department: <span style="color: #E18E0B;">{{ form.department }}</span></td></div-->
                    <div class="transbox"><td style="background-color: white;">Date/Time Transferred: <span style="color: #E18E0B;">{{ form.time_transferred}}</span></td></div>
                    <div class="agebox"><td style="background-color: #f2f2f2;">Age: <span style="color: #E18E0B;"></span></td></div>
                    <div class="sexbox"><td style="background-color: #f2f2f2;">Sex: <span style="color: #E18E0B;"> {{ form.patient_sex }} </span></td></div>
                    <div class="statusbox"><td style="background-color: white;">Status: <span style="color: #E18E0B;"> {{ form.patient_status }} </span></td></div>
                    <div class="philbox"><td style="background-color: #f2f2f2;">Philhealth #: <span style="color: #E18E0B;"> {{ form.phic_id }} </span></td></div>
                </table>
            </div>


        </div>

        <div class="myDiv3">
            <input type="text" id="myTextbox" name="myTextbox" placeholder="Input Prescription" v-model="options.channel">
        </div>
        <button class="submit-button" onclick="alert('Hello, world!')">SUBMIT</button>
    </div>
    <h2 class="left-align">Get started with Voice Calling</h2>
    <div class="row">
        <div>
            <button type="button" id="join">Join</button>
            <button type="button" id="leave">Leave</button>
        </div>
    </div>
    <br>
    <div id="message"></div>


</template>

<style>

    .container {
        /*position: relative;
        margin: 0 auto;*/
        width: 1900px;
        border: 5px outset green;
        height: 950px;
    }

    .image1 {
        position: relative;
        top: 1;
        left: 1;
        z-index: 1;
        height: 948px;
        width: 1200px;
    }

    .divImage2 {
        position: absolute;
        top: 670px;
        left: 915px;
        z-index: 1;
        border: 4px outset	green;
        border-radius: 25px;
        width: 270px;
        height: 260px;
    }
    .image2 {
        position: absolute;
        /*top: .5px;
        left: .5px;*/
        z-index: 2;
        /*transform: rotate(360deg);*/
        /*border: 4px outset	green;*/
        border-radius: 23px;
        width: 270px;
        height: 260px;
    }

    .decline-button {
        position: absolute;
        top: 750px;
        left: 657px;
        z-index: 2;
        /*background-color: red;
        border: none;
        color: white;
        padding: 1.2rem 1.4rem;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 100%;
        transition: background-color 0.3s ease-in-out;*/

        border: none;
        background-color: transparent;
        cursor: pointer;
        transition: transform 0.3s ease-in-out;
    }

    .decline-button img {
        display: block;
        width: 40%;
        height: auto;
    }

    /*.decline-button:hover {
          transform: scale(1.1);
    }*/

    .video-button {
        position: absolute;
        top: 750px;
        left: 580px;
        z-index: 2;
        border: none;
        background-color: transparent;
        cursor: pointer;
        transition: transform 0.3s ease-in-out;
    }

    .video-button img {
        display: block;
        width: 40%;
        height: auto;
    }

    /*.video-button:hover {
          transform: scale(1.1);
    }*/

    .mic-button {
        position: absolute;
        top: 750px;
        left: 500px;
        z-index: 2;
        border: none;
        background-color: transparent;
        cursor: pointer;
        transition: transform 0.3s ease-in-out;
    }

    .mic-button img {
        display: block;
        width: 40%;
        height: auto;
    }

    /*.video-button:hover {
          transform: scale(1.1);
    }*/

    /*Main Video Call*/
    .myDiv {
        position: absolute;
        top: 13px;
        left: 13px;
        border: 1px outset transparent;
        height: 948px;
        width:  1200px;
    }

    /*FORM*/
    .myDiv2 {
        position: absolute;
        top: 25px;
        left: 1230px;
        border: 2px outset black;
        height: 807px;
        width:  660px;
    }

    /*Prescription Form*/
    .myDiv3 {
        position: absolute;
        top: 840px;
        left: 1230px;
        border: 2px outset black;
        height: 70px;
        width:  660px;
    }

    /*SUBMIT BUTTON*/
    .submit-button {
        position: absolute;
        top: 915px;
        left: 1230px;
        z-index: 2;
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 5px;
        width:  660px;
        font-weight: bold;
    }

    .doh-logo {
        position: relative;
        top: 10px;
        left: 10px;
        z-index: 2;
        height: 72px;
        width: 76px;
    }

    /*Heading Form*/
    .myDiv4 {
        position: absolute;
        top: 0px;
        left: 10px;
        border: 1px outset transparent;
        height: 90px;
        width:  640px;
        text-align: center;
        line-height: .0;
        font-size: 13px;
    }

    /*Clinical Referral Form*/
    .myDiv5 {
        position: absolute;
        top: 82px;
        left: 10px;
        border: 1px outset transparent;
        height: 30px;
        width:  640px;
        text-align: center;
        line-height: .0;
        font-size: 23px;
        font-family: Calibri;
    }

    /*Patients Details*/
    .myDiv6 {
        position: absolute;
        top: 125px;
        left: 10px;
        border: 1px outset transparent;
        height: 679px;
        width:  640px;
        text-align: left;
        /*line-height: .0;*/
        font-weight: bold;
        font-size: 14px;
        font-family: Calibri;

        display: flex; /* Optional: use flexbox to align items */
        flex-direction: column;
        /*justify-content: top; /* Optional: distribute items evenly */

        overflow-y: auto;
        overflow-x: hidden;
    }

    tr:nth-child(odd) {
        background-color: #f2f2f2;
        border: 1px outset transparent;
        height: 30px;
        width:  640px;
    }

    tr:nth-child(even) {
        background-color: white;
        border: 1px outset transparent;
        height: 30px;
        width:  640px;
    }




    .box:nth-child(odd) {
        background-color: #f2f2f2;
        border: 1px outset transparent;
        height: 30px;
        width:  640px;
    }

    .box:nth-child(even) {
        background-color: white;
        border: 1px outset transparent;
        height: 30px;
        width:  640px;
    }

    .divbox1 {
        background-color: #f2f2f2;
        position: absolute;
        top: 383px;
        border: 1px outset red;
        /*height: 50px;*/
        height: auto;
        width:  640px;
        text-align: left;
        line-height: .0;
        font-weight: bold;
        font-size: 14px;

    }

    .divbox2 {
        background-color: white;
        position: absolute;
        top: 434px;
        border: 1px outset transparent;
        height: 50px;
        width:  640px;
        text-align: left;
        line-height: 0;
        font-weight: bold;
        font-size: 14px;
    }

    .boxDiv {
        /*background-color: white;*/
        position: absolute;
        top: 485px;
        border: 1px outset transparent;
        height: 166px;
        width:  640px;
        text-align: left;
        line-height: .0;
        font-weight: bold;
        font-size: 14px;

        display: flex; /* Optional: use flexbox to align items */
        flex-direction: column;
        justify-content: top; /* Optional: distribute items evenly */
    }

    .divbox3:nth-child(odd) {
        background-color: #f2f2f2;
        border: 1px outset orchid;
        height: 30px;
        width:  640px;
    }

    .deptbox {
        position: absolute;
        top: 96px;
        left: 320px;
        border: 1px outset transparent;
        height: 30px;
        width:  320px;


    }

    .transbox {
        position: absolute;
        top: 160px;
        left: 320px;
        border: 1px outset transparent;
        height: 30px;
        width:  320px;
    }

    .agebox {
        position: absolute;
        top: 192px;
        left: 390px;
        border: 1px outset transparent;
        height: 30px;
        width:  131px;
    }

    .sexbox {
        position: absolute;
        top: 192px;
        left: 522px;
        border: 1px outset transparent;
        height: 30px;
        width:  118px;
    }

    .statusbox {
        position: absolute;
        top: 224px;
        left: 390px;
        border: 1px outset transparent;
        height: 30px;
        width:  250px;
    }

    .philbox {
        position: absolute;
        top: 256px;
        left: 320px;
        border: 1px outset transparent;
        height: 30px;
        width:  320px;
    }

    /*.textbox-container {
        position: absolute;
          background-color: red;
          border: 1px solid red;
          padding: 10px;
          border-radius: 1px;

          top: 1px;
        left: 160px;
          height: 30px;
        width:  250px;
    }*/

    input[type="text"] {
        position: absolute;
        padding: 5px;
        font-size: 14px;
        border: 1px solid transparent;
        border-radius: 0px;
        box-sizing: border-box;
        background-color: transparent;
        font-weight: bold;
        font-family: Calibri;

        top: 0px;
        left: 0px;
        height: 69px;
        width:  659px;
    }


    .mobile-view {
        display: none;
        visibility: hidden;
    }

    @media only screen and (max-width: 720px) {
        .file-upload {
            background-color: #ffffff;
            width: 300px;
            margin: 0 auto;
            padding: 20px;
        }

        .web-view {
            display: none;
            visibility: hidden;
        }

        .mobile-view {
            display: block;
            visibility: visible;
        }
    }

    #telemedicine {
        border-color:#00a65a;
        border: none;
        padding: 7px;
    }
    #telemedicine:hover {
        background-color: lightgreen;
    }


















</style>
