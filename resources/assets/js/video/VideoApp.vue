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
                    channel: this.getUrlVars()["code"],
                    // Pass your temp token here.
                    token: null,
                    // Set the user ID.
                    uid: 0,
                },

                form: {},



                numberOfColumns: 3,
                cellsToMerge: 2,

            };
        },

        computed: {
            spanValue() {
                return this.numberOfColumns - this.cellsToMerge + 1;
            },

            formattedText() {
                return this.form.case_summary.replace(/\n/g, '<br>')
            },
        },

        mounted() {
            axios
                .get(`${this.baseUrl}/doctor/referral/video/normal/form/${this.tracking_id}`)
                .then((res) => {
                    const response = res.data;
                    this.form = response.form
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
                    self.joinVideo(agoraEngine,channelParameters,localPlayerContainer,self)
                    // Listen to the Join button click event.
                    /*document.getElementById("join").onclick = async function ()
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
                    }*/
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
                $(".divImage2").html(localPlayerContainer)
                $(localPlayerContainer).addClass("image2")
                // Publish the local audio and video tracks in the channel.
                await agoraEngine.publish([channelParameters.localAudioTrack, channelParameters.localVideoTrack]);
                // Play the local video track.
                channelParameters.localVideoTrack.play(localPlayerContainer);
                console.log("publish success!");
            }
        }
    }
</script>


<template>

    <div class="container">
        <img :src="doctorUrl" alt="Image 1" class="image1">
        <div class="divImage2">
            <img :src="doctorUrl1" class="image2" alt="Image 2">
        </div>
        <button class="decline-button" onclick="alert('Hello, world!')"><img :src="declineUrl" alt="Button Image"></button>
        <button class="video-button" type="button" ><img :src="videoCallUrl" alt="Button Image"></button>
        <button class="mic-button" type="button" ><img :src="micUrl" alt="Button Image"></button>
    </div>

    <div class="myDiv2">
        <img :src="dohLogoUrl" alt="Image 3" class="dohLogo">
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
        <table class="myTable">
            <tr>
                <td colspan="6">Name of Referring Facility: <span class="forDetails"> {{ form.referring_name }} </span></td>
            </tr>
            <tr>
                <td colspan="6">Facility Contact #: <span class="forDetails"> {{ form.referring_contact }} </span></td>
            </tr>
            <tr>
                <td colspan="6">Address: <span class="forDetails"> {{ form.referring_address }} </span></td>
            </tr>
            <tr>
                <td :colspan="spanValue">Referred to: <span class="forDetails"> {{ form.referred_name }} </span></td>
                <td :colspan="spanValue">Department: <span class="forDetails"> {{ form.department }} </span></td>
            </tr>
            <tr>
                <td>Address: <span class="forDetails"> {{ form.referred_address }} </span></td>
            </tr>
            <tr>
                <td>Date/Time Referred (ReCo): <span class="forDetails"> {{ form.time_referred }} </span></td>
                <td>Date/Time Transferred:<span class="forDetails"> {{ form.time_transferred}} </span></td>
            </tr>
            <tr>
                <td>Name of Patient: <span class="forDetails"> {{ form.patient_name }} </span></td>
                <td>Age: <span class="forDetails"> {{  }} </span></td>
                <td>Sex: <span class="forDetails"> {{ form.patient_sex }} </span></td>
            </tr>
            <tr>
                <td>Address: <span class="forDetails"> {{ form.patient_address }} </span></td>
                <td>Status: <span class="forDetails"> {{ form.patient_status }} </span></td>
            </tr>
            <tr>
                <td>Philhealth status: <span class="forDetails"> {{ form.phic_status }} </span></td>
                <td>Philhealth #: <span class="forDetails"> {{ form.phic_id }} </span></td>
            </tr>
            <tr>
                <td>Covid Number: <span class="forDetails"> {{ form.covid_number }} </span></td>
            </tr>
            <tr>
                <td>Clinical Status: <span class="forDetails"> {{ form.refer_clinical_status }} </span></td>
            </tr>
            <tr>
                <td>Surviellance Category: <span class="forDetails"> {{  }} </span></td>
            </tr>

            <tr>
                <td>Case Summary (pertinent Hx/PE, including meds, labs, course etc.): <br><span class="forDetails"> {{ form.case_summary }} </span></td>
            </tr>
            <tr>
                <td>Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist): <br><span class="forDetails"> {{ form.reco_summary }} </span></td>
            </tr>
            <tr>
                <td>ICD-10 Code and Description: <span class="forDetails"> {{ form.other_diagnoses }} </span></td>
            </tr>
            <tr>
                <td>Reason for referral: <span class="forDetails"> {{ form.other_reason_referral }} </span></td>
            </tr>
            <tr>
                <td>File Attachment: <span class="forDetails"> {{ form.phic_id }} </span></td>
            </tr>
            <tr>
                <td>Name of Referring MD/HCW: <span class="forDetails"> {{ form.md_referring }} </span></td>
            </tr>
            <tr>
                <td>Contact # of Referring MD/HCW: <span class="forDetails"> {{ form.referring_md_contact }} </span></td>
            </tr>
            <tr>
                <td>Name of referred MD/HCW-Mobile Contact # (ReCo): <span class="forDetails"> {{ form.phic_id }} </span></td>
            </tr>
        </table>
    </div>

    <div class="myDiv6">
        <div class="myDiv3">
            <input type="text" id="myTextbox" name="myTextbox" placeholder="Input Prescription" v-model="options.channel">
        </div>
        <button class="submit-button" onclick="alert('Hello, world!')">SUBMIT</button>
    </div>

    <!--/div-->
    <!--h2 class="left-align">Get started with Voice Calling</h2>
    <div class="row">
        <div>
            <button type="button" id="join">Join</button>
            <button type="button" id="leave">Leave</button>
        </div>
    </div>
    <br>
    <div id="message"></div-->

</template>

<style>

    /*Main Video Call*/
    .container {
        /*position: relative;
        margin: 0 auto;   width: 1900px;
        width: 1200px;*/
        border: 4px outset green;
        margin: 5px;
        height: 950px;
    }

    .image1 {
        position: relative;
        top: 0;
        left: 0;
        z-index: 1;
        height: 948px;
        width: 1200px;
    }

    .dohLogo {
        position: relative;
        top: 10px;
        left: 10px;
        z-index: 2;
        height: 72px;
        width: 76px;
        border: 1px outset transparent;
    }

    /*Form Header*/
    .myDiv4 {
        position: absolute;
        top: 0;
        left: 10px;
        border: 1px outset transparent;
        height: 90px;
        width:  640px;
        text-align: center;
        line-height: .0;
        font-size: 14px;
    }

    /*Clinical Referral*/
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
        z-index: 2;
        border-radius: 23px;
        width: 270px;
        height: 260px;
    }

    .decline-button {
        position: absolute;
        top: 750px;
        left: 657px;
        z-index: 2;
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
        border: 2px outset black;
        height: 70px;
        width:  660px;
        top: 842px;
        left: 0;
    }

    .myDiv6 {
        position: absolute;
        top: 0;
        left: 1230px;
        border: 2px outset transparent;
        height: 964px;
        width:  660px;
    }

    /*SUBMIT BUTTON*/
    .submit-button {
        position: absolute;
        top: 919px;
        left: 0;
        z-index: 1;
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;

        cursor: pointer;
        border-radius: 5px;
        width:  660px;
        font-weight: bold;
    }

    /*Patients Details*/
    .myTable {
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

    .forDetails {
        color: #E18E0B;
    }

    tr:nth-child(odd) {
        background-color: #f2f2f2;
        border: 1px outset transparent;
        /*height: 30px;
        width:  640px;*/
        height: auto;
        width:  auto;
    }

    tr:nth-child(even) {
        background-color: white;
        border: 1px outset transparent;
        /*height: 30px;
        width:  640px;*/
        height: auto;
        width:  auto;
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

    /* For iPhone SE*/
    @media (min-width: 375px) and (max-width: 667px){

        .container {
            position: absolute;
            width: 98%;
            height: 99%;
            top: 1px;
            left: 1px;
            border: 2px outset green;
            margin: 0;
        }
        .image1 {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
            top: 0;
            left: 0;
        }

        .divImage2 {
            position: absolute;
            z-index: 1;
            width: 35%;
            height: 35%;
            left: 230px;
            top: 420px;
            border: 2px outset	green;
            border-radius: 25px;
        }

        .image2 {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
            top: 0;
            right: 0;
        }

        .dohLogo {
            position: relative;
           /* top: 10px;
            left: 10px;
            z-index: 2;*/
            height: 42px;
            width: 46px;
        }

        .myDiv2 {
            position: absolute; /*FORM BORDER*/
            width: 98%;
            top: 670px;
            left: 1px;
            border: 2px outset black;
            height: auto;
        }

        .myDiv4 {
            position: absolute; /*FORM HEADER*/
            top: 10px;
            width: 100%;
            line-height: .2;
            text-align: center;
            font-size: 10px;
        }

        .myDiv5 {
            position: relative; /*CLINICAL REFERRAL*/
            width: 100%;
            top: 35px;
            left: 10px;
            border: 1px outset transparent;
            text-align: center;
            line-height: .0;
            font-size: 18px;
            font-family: Calibri;
        }

        .myTable {
            position: relative;
            width: 98%;
            height: auto;
            top: 40px;
            left: 5px;
            border: 1px outset transparent;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            font-family: Calibri;

            display: flex; /* Optional: use flexbox to align items */
            flex-direction: column;
            /*justify-content: top; /* Optional: distribute items evenly */

            overflow-y: auto;
            overflow-x: hidden;

        }

        .decline-button {
            position: absolute;
            top: 600px;
            left: 170px;
            border: none;
            background-color: transparent;
            cursor: pointer;
            /*transition: transform 0.3s ease-in-out;*/
        }

        .decline-button img {
            display: block;
            width: 30%;
            height: auto;
        }

        .video-button {
            position: absolute;
            top: 600px;
            left: 120px;
            z-index: 1;
            border: none;
            background-color: transparent;
            cursor: pointer;
            /*transition: transform 0.3s ease-in-out;*/
        }

        .video-button img {
            display: block;
            width: 30%;
            height: auto;
        }

        .mic-button {
            position: absolute;
            top: 600px;
            left: 70px;
            z-index: 1;
            border: none;
            background-color: transparent;
            cursor: pointer;
            /*transition: transform 0.3s ease-in-out;*/
        }

        .mic-button img {
            display: block;
            width: 30%;
            height: auto;
        }


        /*.myDiv6 {
            position: relative;
            top: 2200px;
            width: auto;
            height: 50px;
            left: 2px;
            border: 2px outset red;
        }
        .myDiv3 {
            position: absolute;
            width: 50%;
            left: 0;
        }
        .submit-button {
            position: absolute;
            width: 50%;
            left: 0;
        }*/

    }



</style>
