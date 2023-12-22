
<template>
    <div class="modal fade" id="prescriptionModal" data-backdrop="static" data-keyboard="false" aria-labelledby="prescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="prescriptionModalLabel"><i class="bi bi-prescription"></i> Prescription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="container">Ex.
                        <div class="row row-circle">
                            <div class="circle1">1</div>
                            <div class="circle2">2</div>
                            <div class="circle3">3</div>
                            <div class="circle7">7</div>
                        </div>
                        <div class="row row-presLabel">
                            <div class="col col-presLabel">
                                <div class="col-Label">
                                    <span class="underline">Ascorbic Acid</span>
                                    <span class="underline">500mg</span>
                                    <span class="underline">tablet</span>
                                    <span class="underline">#30</span>
                                </div>
                            </div>
                        </div>
                        <!--<div class="row row-presLabel">
                            <div class="col col-presLabel">
                                <div class="circle1">1</div>
                                <div class="col-Label">
                                    <span class="underline">Ascorbic Acid</span>
                                </div>
                            </div>
                            <div class="col col-presLabel">
                                <div class="circle2">2</div>
                                <div class="col-Label">
                                    <span class="underline">500mg</span>
                                </div>
                            </div>
                            <div class="col col-presLabel">
                                <div class="circle3">3</div>
                                <div class="col-Label">
                                    <span class="underline">tablet</span>
                                </div>
                            </div>
                            <div class="col col-presLabel">
                                <div class="circle7">7</div>
                                <div class="col-Label">
                                    <span class="underline">#30</span>
                                </div>
                            </div>
                        </div>-->



                        <div class="row row-presLabel">
                            <div class="col col-presLabel">
                                <div class="circle4">4</div>
                                <div class="col-Label">
                                    <span class="underline">(Brand Name)</span>
                                </div>
                            </div>
                        </div>



                        <div class="row row-circle">
                            <div class="circle5">5</div>
                            <div class="circle6">6</div>
                        </div>
                        <div class="row row-presLabel">
                            <div class="col col-presLabel">
                                <div class="col-Label">Sig:
                                    <span class="underline">Once a day</span> for
                                    <span class="underline">7 Days</span>
                                </div>
                            </div>
                        </div>
                    </div>





                    <div class="row prescription">
                        <div class="col">
                            <label for="genericName">1.) Generic Name:</label>
                            <input type="text" v-model="prescription" class="form-control" >
                        </div>
                    </div>
                    <div class="row prescription">
                        <div class="col">
                            <label for="dosage">2.) Dosage:</label>
                            <input type="text" v-model="dosage" class="form-control">
                        </div>
                        <div class="col">
                            <label for="formulation">3.) Formulation:</label>
                            <input type="text" v-model="formulation" class="form-control">
                        </div>
                    </div>
                    <div class="row prescription">
                        <div class="col">
                            <label for="brandName">4.) Brand Name:</label>
                            <input type="text" v-model="brandName" class="form-control">
                        </div>
                        <div class="col">
                            <label for="frequency">5.) Frequency:</label>
                            <input type="text" v-model="frequency" class="form-control">
                        </div>
                    </div>
                    <div class="row prescription">
                        <div class="col">
                            <label for="brandName">6.) Duration:</label>
                            <input type="text" v-model="duration" class="form-control">
                        </div>
                        <div class="col">
                            <label for="quantity">7.) Quantity:</label>
                            <input type="number" v-model="quantity"  class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-success btn-sm" type="button" @click="submitPrescription()" v-if="prescriptionSubmitted"><i class="bi bi-prescription"></i> Update Prescription</button>
                    <button class="btn btn-success btn-sm" type="button" @click="submitPrescription()" v-else><i class="bi bi-prescription"></i> Submit Prescription</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                prescription: "",
                dosage: "",
                formulation: "",
                brandName: "",
                frequency: "",
                duration: "",
                quantity: "",
            };
        },
        methods: {
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
        },
    };
</script>

<style>
    .container {
        border: solid 1px lightgray;
        padding-bottom: 12px;
    }
    .row-presLabel {
        display: flex;
    }

    .col-presLabel {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .row-presLabel, .col-presLabel{
        font-size: 13px;
    }

    .col-Label {
        font-style: italic;
    }
    .underline {
        text-decoration: underline;
        margin: 5px;
    }
    .row-circle {
        display: flex;

    }
    .circle1, .circle2, .circle3, .circle4, .circle5, .circle6, .circle7 {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        border: solid 1px lightgrey;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: bold;
    }
    .circle1 {
        margin-left: 35%;
    }
    .circle2 {
        margin-left: 12%;
    }
    .circle3 {
        margin-left: 6%;
    }
    .circle7 {
        margin-left: 5%;
    }
    .circle4{
        margin-top: 1%;
    }
    .circle5 {
        margin-top: 1%;
        margin-left: 44%;
    }
    .circle6 {
        margin-top: 1%;
        margin-left: 14%;
    }





</style>
