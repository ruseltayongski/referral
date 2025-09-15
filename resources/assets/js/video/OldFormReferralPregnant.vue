<script>
export default {
  props: {
    initialForm: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    initialFormBaby: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    file_path: {
      type: Array,
      required: false,
      default: () => [],
    },
    icd: {
      type: Array,
      required: false,
      default: () => [],
    },
    file_name: {
      type: Array,
      required: false,
      default: () => [],
    },
    patient_age: {
      type: String,
      required: false,
      default: "",
    },
  },
  data() {
    return {
      form: { ...this.initialForm },
      formBaby: { ...this.initialFormBaby },
      localFilePath: this.file_path, 
      localFileName: this.file_name,
    };
  },
  watch: {
    initialForm: {
      handler(newValue) {
        this.form = { ...newValue };
      },
      deep: true,
    },
    initialFormBaby: {
      handler(newValue) {
        this.formBaby = { ...newValue };
      },
      deep: true,
    },
    file_path: {
      handler(newValue) {
        this.localFilePath = [...newValue];
        console.log("File path updated:", this.localFilePath);
      },
      deep: true,
    },
    file_name: {
      handler(newValue) {
        this.localFileName = [...newValue];
        console.log("File name updated:", this.localFileName);
      },
      deep: true,
    },
  },
};
</script>

<template>
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
                </div>
              </div>
</template>

