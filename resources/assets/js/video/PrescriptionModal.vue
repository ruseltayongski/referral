
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
                            <div class="circle4">4</div>
                            <div class="circle5">5</div>
                        </div>
                        <div class="row row-presLabel">
                            <div class="col col-presLabel">
                                <div class="col-Label">
                                    <span class="underline">Paracetamol</span>
                                    <span class="underline">(Biogesic)</span>
                                    <span class="underline">500mg</span>
                                    <span class="underline">#30</span>
                                    <span class="underline">Tablet</span>
                                </div>
                            </div>
                        </div>
                        <div class="row row-circle">
                            <div class="circle6">6</div>
                            <div class="circle7">7</div>
                        </div>
                        <div class="row row-presLabel">
                            <div class="col col-presLabel">
                                <div class="col-Label">Sig:
                                    <span class="underline">1 Tab</span> for
                                    <span class="underline">Every 4 hours</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row prescription">
                        <div class="col">
                            <label for="generic_name">1.) Generic Name:</label>
                            <input type="text" v-model="generic_name" class="form-control form-control-sm" >
                        </div>
                    </div>
                    <div class="row prescription">
                        <div class="col">
                            <label for="brandname">2.) Brand Name:</label>
                            <input type="text" v-model="brandname" class="form-control form-control-sm">
                        </div>
                        <div class="col">
                            <label for="dosage">3.) Dosage:</label>
                            <input type="text" v-model="dosage" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row prescription">
                        <div class="col">
                            <label for="quantity">4.) Quantity:</label>
                            <input type="number" v-model="quantity"  class="form-control form-control-sm">
                        </div>
                        <div class="col">
                            <label for="formulation">5.) Formulation:</label>
                            <input type="text" v-model="formulation" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row prescription">
                        <div class="col">
                            <label for="frequency">6.) Frequency:</label>
                            <input type="text" v-model="frequency" class="form-control form-control-sm">
                        </div>
                        <div class="col">
                            <label for="duration">7.) Duration:</label>
                            <input type="text" v-model="duration" class="form-control form-control-sm">
                        </div>
                    </div>



                        <!-- Duplicated Prescription Fields -->
                        <div v-for="(prescription, index) in prescriptions" :key="index" style="border: solid 1px black; margin-top: 15px; padding: 5px;">
                            <div class="row prescription">
                                <div class="col">
                                    <label :for="'generic_name_'">1.) Generic Name:</label>
                                    <input type="text" :v-model="'generic_name_' + index" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row prescription">
                                <div class="col">
                                    <label :for="'brandname'">2.) Brand Name:</label>
                                    <input type="text" :v-model="'brandname' + index" class="form-control form-control-sm">
                                </div>
                                <div class="col">
                                    <label :for="'dosage'">3.) Dosage:</label>
                                    <input type="text" :v-model="'dosage' + index" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row prescription">
                                <div class="col">
                                    <label :for="'quantity'">4.) Quantity:</label>
                                    <input type="text" :v-model="'quantity' + index" class="form-control form-control-sm">
                                </div>
                                <div class="col">
                                    <label :for="'formulation'">5.) Formulation:</label>
                                    <input type="text" :v-model="'formulation' + index" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row prescription">
                                <div class="col">
                                    <label :for="'frequency'">6.) Frequency:</label>
                                    <input type="text" :v-model="'quantity' + index" class="form-control form-control-sm">
                                </div>
                                <div class="col">
                                    <label :for="'duration'">7.) Duration:</label>
                                    <input type="text" :v-model="'formulation' + index" class="form-control form-control-sm">
                                </div>
                            </div>

                        </div>

                </div>







                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" type="button" @click="addPrescriptionField">Add Prescription Field</button>

                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-success btn-sm" type="button" @click="submitPrescription()" v-if="prescriptionSubmitted"><i class="bi bi-prescription"></i> Update Prescription</button>
                    <button class="btn btn-success btn-sm" type="button" @click="submitPrescription()" v-else><i class="bi bi-prescription"></i> Submit Prescription</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        data() {
            return {
                prescriptionSubmitted: false,
                generic_name: "",
                brandname: "",
                dosage: "",
                quantity: "",
                formulation: "",
                frequency: "",
                duration: "",


                prescriptions: [] // Store dynamic prescriptions

            };
        },
        props: {
            activity_id: {
                type: Number
            },
            baseUrl: {
                type: String
            },
            code: {
                type: String
            },
            form_type: {
                type: String
            }
        },
        created() {
            console.log(this.activity_id, this.baseUrl, this.code)
        },
        methods: {
            submitPrescription() {
                if(!this.generic_name) {
                    Lobibox.alert("error",
                        {
                            msg: "Please input generic name"
                        });
                }
                else if(!this.brandname) {
                    Lobibox.alert("error",
                        {
                            msg: "Please input brand name"
                        });
                }
                else if(!this.dosage) {
                    Lobibox.alert("error",
                        {
                            msg: "Please input dosage"
                        });
                }
                else if(!this.quantity) {
                    Lobibox.alert("error",
                        {
                            msg: "Please input quantity"
                        });
                }
                else if(!this.formulation) {
                    Lobibox.alert("error",
                        {
                            msg: "Please input formulation"
                        });
                }
                else if(!this.frequency) {
                    Lobibox.alert("error",
                        {
                            msg: "Please input frequency"
                        });
                }
                else if(!this.duration) {
                    Lobibox.alert("error",
                        {
                            msg: "Please input duration"
                        });
                }
                else {
                    const updatePrescription = {
                        code : this.code,
                        generic_name: this.generic_name,
                        dosage: this.dosage,
                        formulation: this.formulation,
                        brandname: this.brandname,
                        frequency: this.frequency,
                        duration: this.duration,
                        quantity: this.quantity,
                        form_type: this.form_type,
                        activity_id: this.activity_id
                    }
                    axios.post(`${this.baseUrl}/api/video/prescription/update`, updatePrescription).then(response => {
                        console.log(response)
                        if(response.data === 'success') {
                            $("#prescriptionModal").modal('hide');
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
                }
            },



            addPrescriptionField() {
                // Add a new prescription field
                this.prescriptions.push({
                    generic_name: '',
                    brandname: '',
                    dosage: '',
                    quantity: '',
                    formulation: '',
                    frequency: '',
                    duration: ''
                });
            },



        },
    };
</script>

<style scoped>
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
    label {
        margin-bottom: 2px;
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
        margin-left: 28%;
    }
    .circle2 {
        margin-left: 13%;
    }
    .circle3 {
        margin-left: 9%;
    }
    .circle4 {
        margin-left: 5%;
    }
    .circle5 {
        margin-left: 5%;
    }
    .circle6 {
        margin-top: 2%;
        margin-left: 39%;
    }
    .circle7 {
        margin-top: 2%;
        margin-left: 15%;
    }
</style>