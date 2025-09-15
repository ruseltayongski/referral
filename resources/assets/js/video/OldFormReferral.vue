<script>
export default {
  props: {
    initialForm: {
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
  <table class="table table-striped formTable">
    <tbody>
      <tr>
        <td colspan="12">
          Name of Referring Facility:
          <span class="forDetails">
            {{ form.referring_name }}
          </span>
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
          <span class="dateReferred">
            {{ form.time_referred }}
          </span>
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
          <span class="forDetails">
            {{ form.patient_address }}
          </span>
        </td>
        <td colspan="6">
          Status:
          <span class="forDetails">
            {{ form.patient_status }}
          </span>
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
          Case Summary (pertinent Hx/PE, including meds, labs, course etc.):
          <br /><span class="caseforDetails">{{ form.case_summary }}</span>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          Summary of ReCo (pls. refer to ReCo Guide in Referring Patients
          Checklist):<br /><span class="recoSummary">
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
          Name of referred MD/HCW-Mobile Contact # (ReCo):
          <br /><span class="mdHcw">
            {{ form.md_referred }}
          </span>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
/* Add your component styles here */
</style>
