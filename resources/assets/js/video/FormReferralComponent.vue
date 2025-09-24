<script>
export default {
  props: {
    initialForm: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    past_medical_history: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    personal_and_social_history: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    pertinent_laboratory: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    review_of_system: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    nutritional_status: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    latest_vital_signs: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    glasgocoma_scale: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    obstetric_and_gynecologic_history: {
      type: Object,
      required: false,
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
    pregnancy: {
      type: Array,
      required: false,
      default: () => [],
    },
    patient_age: {
      type: String,
      required: false,
      default: "",
    },
    form_version: {
      type: String,
      required: false,
      default: "",
    },
    current_medication: {
      type: String,
      required: false,
      default: "",
    },
    telemedicine: {
      type: Number,
      required: false,
      default: null,
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
  computed: {
    filteredPregnancy() {
      return this.pregnancy.filter(
        (record) =>
          record.pregnancy_order !== null ||
          record.pregnancy_year !== null ||
          record.pregnancy_gestation_completed !== null ||
          record.pregnancy_outcome !== null ||
          record.pregnancy_place_of_birth !== null ||
          record.pregnancy_sex !== null ||
          record.pregnancy_birth_weight !== null ||
          record.pregnancy_present_status !== null ||
          record.pregnancy_complication !== null
      );
    },
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return "";

      // make sure it's valid ISO format (replace space with T)
      const date = new Date(dateStr.replace(" ", "T"));

      return date
        .toLocaleString("en-US", {
          year: "numeric",
          month: "long",
          day: "numeric",
          hour: "numeric",
          minute: "2-digit",
          hour12: true,
        })
        .replace(",", ""); // remove the extra comma before time
    },
    isEmpty(value) {
      if (value === null || value === undefined) return true;
      if (typeof value === "string" && value.trim() === "") return true;
      if (Array.isArray(value) && value.length === 0) return true;
      if (typeof value === "object" && Object.keys(value).length === 0)
        return true;
      return false;
    },
  },
};
</script>

<template>
  <table class="table table-striped formTable">
    <tbody>
      <tr v-if="form.referring_name != null">
        <td colspan="12">
          Name of Referring Facility:
          <span class="forDetails">
            {{ form.referring_name }}
          </span>
        </td>
      </tr>
      <tr v-if="form.referring_contact != null">
        <td colspan="12">
          Facility Contact #:
          <span class="forDetails">
            {{ form.referring_contact }}
          </span>
        </td>
      </tr>
      <tr v-if="form.referring_address != null">
        <td colspan="12">
          Address:
          <span class="forDetails">
            {{ form.referring_address }}
          </span>
        </td>
      </tr>
      <tr v-if="form.referred_name != null || form.department != null">
        <td colspan="6" v-if="form.referred_name != null">
          Referred to:
          <span class="forDetails"> {{ form.referred_name }} </span>
        </td>
        <td colspan="6" v-if="form.department != null">
          Department:
          <span class="forDetails"> {{ form.department }} </span>
        </td>
      </tr>
      <tr v-if="form.referred_address != null">
        <td colspan="12">
          Address:
          <span class="forDetails">
            {{ form.referred_address }}
          </span>
        </td>
      </tr>
      <tr v-if="form.time_referred != null || form.time_transferred != null">
        <td colspan="6" v-if="form.time_referred != null">
          Date/Time Referred (ReCo):
          <span class="dateReferred">
            {{ form.time_referred }}
          </span>
        </td>
        <td colspan="6" v-if="form.time_transferred != null">
          Date/Time Transferred:<span class="forDetails">
            {{ form.time_transferred }}
          </span>
        </td>
      </tr>
      <tr
        v-if="
          form.patient_name != null ||
          form.patient_sex != null ||
          patient_age != null
        "
      >
        <td colspan="4" v-if="form.patient_name != null">
          Name of Patient:
          <span class="forDetails"> {{ form.patient_name }} </span>
        </td>
        <td colspan="4" v-if="patient_age != null">
          Age: <span class="forDetails"> {{ patient_age }} </span>
        </td>
        <td colspan="4" v-if="form.patient_sex != null">
          Sex:
          <span class="forDetails"> {{ form.patient_sex }} </span>
        </td>
      </tr>
      <tr v-if="form.patient_address != null || form.patient_status != null">
        <td colspan="6" v-if="form.patient_address != null">
          Address:
          <span class="forDetails">
            {{ form.patient_address }}
          </span>
        </td>
        <td colspan="6" v-if="form.patient_status != null">
          Status:
          <span class="forDetails">
            {{ form.patient_status }}
          </span>
        </td>
      </tr>
      <tr v-if="form.phic_status != null || form.phic_id != null">
        <td colspan="6" v-if="form.phic_status != null">
          Philhealth status:
          <span class="forDetails"> {{ form.phic_status }} </span>
        </td>
        <td colspan="6" v-if="form.phic_id != null">
          Philhealth #:
          <span class="forDetails"> {{ form.phic_id }} </span>
        </td>
      </tr>
      <tr v-if="form.covid_number != null">
        <td colspan="12" v-if="form.covid_number != null">
          Covid Number:
          <span class="forDetails"> {{ form.covid_number }} </span>
        </td>
      </tr>
      <tr v-if="form.refer_clinical_status != null">
        <td colspan="12">
          Clinical Status:
          <span class="forDetails">
            {{ form.refer_clinical_status }}
          </span>
        </td>
      </tr>
      <tr v-if="form.refer_sur_category != null">
        <td colspan="12">
          Surviellance Category:
          <span class="forDetails">
            {{ form.refer_sur_category }}
          </span>
        </td>
      </tr>
      <tr v-if="form.case_summary != null && form_version != 'version2'">
        <td colspan="12">
          Case Summary (pertinent Hx/PE, including meds, labs, course etc.):
          <br /><span class="caseforDetails">{{ form.case_summary }}</span>
        </td>
      </tr>
      <tr v-if="form.reco_summary != null && form_version != 'version2'">
        <td colspan="12">
          Summary of ReCo (pls. refer to ReCo Guide in Referring Patients
          Checklist):<br /><span class="recoSummary">
            {{ form.reco_summary }}
          </span>
        </td>
      </tr>
      <tr v-if="icd != null && form_version != 'version2'">
        <td colspan="12">
          ICD-10 Code and Description:
          <li v-for="i in icd">
            <span class="caseforDetails"
              >{{ i.code }} - {{ i.description }}</span
            >
          </li>
        </td>
      </tr>
      <tr v-if="form.reason != null && form_version != 'version2'">
        <td colspan="12">
          Reason for referral:
          <span class="forDetails">
            {{ form.reason }}
          </span>
        </td>
      </tr>
      <tr v-if="file_path && form_version != 'version2'">
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

  <table
    class="table table-striped formTable"
    v-if="form_version == 'version2'"
  >
    <tbody>
      <tr
        class="bg-gray text-center"
        v-if="!isEmpty(form.case_summary) && !isEmpty(form.reco_summary)"
      >
        <td colspan="12"><b> History of Present Illness </b></td>
      </tr>
      <tr v-if="form.case_summary != null">
        <td colspan="12">
          Case Summary (pertinent Hx/PE, including meds, labs, course etc.):
          <br /><span class="caseforDetails">{{ form.case_summary }}</span>
        </td>
      </tr>
      <tr v-if="form.reco_summary != null">
        <td colspan="12">
          Summary of ReCo (pls. refer to ReCo Guide in Referring Patients
          Checklist):<br /><span class="recoSummary">
            {{ form.reco_summary }}
          </span>
        </td>
      </tr>
      <tr
        class="bg-gray text-center"
        v-if="!isEmpty(icd) && !isEmpty(form.other_diagnoses)"
      >
        <td colspan="12"><b> Diagnosis </b></td>
      </tr>
      <tr v-if="icd != null">
        <td colspan="12">
          ICD-10 Code and Description:
          <li v-for="i in icd">
            <span class="caseforDetails"
              >{{ i.code }} - {{ i.description }}</span
            >
          </li>
        </td>
      </tr>
      <tr class="padded-row" v-if="form.other_diagnoses != ''">
        <td colspan="12">
          Other Diagnosis:
          <span class="forDetails">{{ form.other_diagnoses }}</span>
        </td>
      </tr>
      <tr
        class="bg-gray text-center"
        v-if="
          !isEmpty(past_medical_history.allergies) &&
          !isEmpty(past_medical_history.allergy_drugs_cause) &&
          !isEmpty(past_medical_history.allergy_food_cause) &&
          !isEmpty(past_medical_history.commordities_asthma_year) &&
          !isEmpty(past_medical_history.commordities_cancer) &&
          !isEmpty(past_medical_history.commordities_diabetes_year) &&
          !isEmpty(past_medical_history.commordities_hyper_year) &&
          !isEmpty(past_medical_history.commordities_others) &&
          !isEmpty(past_medical_history.heredo_asthma_side) &&
          !isEmpty(past_medical_history.heredo_cancer_side) &&
          !isEmpty(past_medical_history.heredo_diab_side) &&
          !isEmpty(past_medical_history.heredo_hyper_side) &&
          !isEmpty(past_medical_history.heredo_kidney_side) &&
          !isEmpty(past_medical_history.heredo_thyroid_side) &&
          !isEmpty(past_medical_history.heredo_others)
        "
      >
        <td colspan="12"><b> Past Medical History </b></td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(past_medical_history.allergies) ||
          !isEmpty(past_medical_history.allergy_drugs_cause) ||
          !isEmpty(past_medical_history.allergy_food_cause)
        "
      >
        <td colspan="12">
          Allergies:
          <span class="forDetails">{{ past_medical_history.allergies }}</span>
          Drugs cause:
          <span class="forDetails"
            >{{ past_medical_history.allergy_drugs_cause }} &nbsp;</span
          >
          Food cause:
          <span class="forDetails"
            >{{ past_medical_history.allergy_food_cause }} &nbsp;</span
          >
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(past_medical_history.commordities_asthma_year) ||
          !isEmpty(past_medical_history.commordities_cancer) ||
          !isEmpty(past_medical_history.commordities_diabetes_year) ||
          !isEmpty(past_medical_history.commordities_hyper_year) ||
          !isEmpty(past_medical_history.commordities_others)
        "
      >
        <td colspan="12">
          <br />Commorbidities &nbsp;
          <!-- <span class="forDetails">{{past_medical_history.commordities?.replace("Select All,", "")}}&nbsp;</span> -->
          Asthma:
          <span class="forDetails"
            >{{ past_medical_history.commordities_asthma_year }} &nbsp;</span
          >
          Cancer:
          <span class="forDetails"
            >{{ past_medical_history.commordities_cancer }} &nbsp;</span
          >
          Diabetes:
          <span class="forDetails"
            >{{ past_medical_history.commordities_diabetes_year }} &nbsp;</span
          >
          Hypertension:
          <span class="forDetails"
            >{{ past_medical_history.commordities_hyper_year }} &nbsp;</span
          >
          Others:
          <span class="forDetails"
            >{{ past_medical_history.commordities_others }} &nbsp;</span
          >
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(past_medical_history.heredo_asthma_side) ||
          !isEmpty(past_medical_history.heredo_cancer_side) ||
          !isEmpty(past_medical_history.heredo_diab_side) ||
          !isEmpty(past_medical_history.heredo_hyper_side) ||
          !isEmpty(past_medical_history.heredo_kidney_side) ||
          !isEmpty(past_medical_history.heredo_thyroid_side) ||
          !isEmpty(past_medical_history.heredo_others)
        "
      >
        <td colspan="12">
          <br />Heredofamilial Diseases &nbsp;
          <!-- <span class="forDetails">{{ past_medical_history.heredofamilial_diseases }} &nbsp;</span> -->
          Asthma:
          <span class="forDetails"
            >{{ past_medical_history.heredo_asthma_side }} &nbsp;</span
          >
          Cancer:
          <span class="forDetails"
            >{{ past_medical_history.heredo_cancer_side }} &nbsp;</span
          >
          Diabetes:
          <span class="forDetails"
            >{{ past_medical_history.heredo_diab_side }} &nbsp;</span
          >
          Hypertension:
          <span class="forDetails"
            >{{ past_medical_history.heredo_hyper_side }} &nbsp;</span
          >
          Kidney Disease:
          <span class="forDetails"
            >{{ past_medical_history.heredo_kidney_side }} &nbsp;</span
          >
          Thyroid Disease:
          <span class="forDetails"
            >{{ past_medical_history.heredo_thyroid_side }} &nbsp;</span
          >
          Others:
          <span class="forDetails"
            >{{ past_medical_history.heredo_others }} &nbsp;</span
          >
        </td>
      </tr>
      <tr
        class="bg-gray text-center padded-row"
        v-if="
          !isEmpty(personal_and_social_history.alcohol_drinking) ||
          !isEmpty(personal_and_social_history.alcohol_bottles_per_day) ||
          !isEmpty(personal_and_social_history.alcohol_drinking_quit_year) ||
          !isEmpty(personal_and_social_history.alcohol_liquor_type) ||
          !isEmpty(personal_and_social_history.smoking) ||
          !isEmpty(personal_and_social_history.smoking_sticks_per_day) ||
          !isEmpty(personal_and_social_history.smoking_quit_year) ||
          !isEmpty(personal_and_social_history.smoking_remarks) ||
          !isEmpty(personal_and_social_history.illicit_drugs) ||
          !isEmpty(personal_and_social_history.illicit_drugs_quit_year) ||
          !isEmpty(personal_and_social_history.illicit_drugs_taken)
        "
      >
        <td colspan="12"><b> Personal and Social History </b></td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(personal_and_social_history.alcohol_drinking) ||
          !isEmpty(personal_and_social_history.alcohol_bottles_per_day) ||
          !isEmpty(personal_and_social_history.alcohol_drinking_quit_year) ||
          !isEmpty(personal_and_social_history.alcohol_liquor_type)
        "
      >
        <td colspan="12">
          Alcohol:
          <span class="forDetails"
            >{{ personal_and_social_history.alcohol_drinking }} &nbsp;</span
          >
          Per day:
          <span class="forDetails"
            >{{
              personal_and_social_history.alcohol_bottles_per_day
            }}
            &nbsp;</span
          >
          Year quitted:
          <span class="forDetails"
            >{{
              personal_and_social_history.alcohol_drinking_quit_year
            }}
            &nbsp;</span
          >
          Type of liquor:
          <span class="forDetails"
            >{{ personal_and_social_history.alcohol_liquor_type }} &nbsp;</span
          >
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(personal_and_social_history.smoking) ||
          !isEmpty(personal_and_social_history.smoking_sticks_per_day) ||
          !isEmpty(personal_and_social_history.smoking_quit_year) ||
          !isEmpty(personal_and_social_history.smoking_remarks)
        "
      >
        <td colspan="12">
          <br />Smoking:
          <span class="forDetails"
            >{{ personal_and_social_history.smoking }} &nbsp;</span
          >
          Sticks per day:
          <span class="forDetails"
            >{{
              personal_and_social_history.smoking_sticks_per_day
            }}
            &nbsp;</span
          >
          Year quitted:
          <span class="forDetails"
            >{{ personal_and_social_history.smoking_quit_year }} &nbsp;</span
          >
          Remarks:
          <span class="forDetails"
            >{{ personal_and_social_history.smoking_remarks }} &nbsp;</span
          >
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(personal_and_social_history.illicit_drugs) ||
          !isEmpty(personal_and_social_history.illicit_drugs_quit_year) ||
          !isEmpty(personal_and_social_history.illicit_drugs_taken)
        "
      >
        <td colspan="12">
          <br />Illicit Drugs:
          <span class="forDetails"
            >{{ personal_and_social_history.illicit_drugs }} &nbsp;</span
          >
          Year quitted:
          <span class="forDetails"
            >{{
              personal_and_social_history.illicit_drugs_quit_year
            }}
            &nbsp;</span
          >
          Illicit drugs taken:
          <span class="forDetails"
            >{{ personal_and_social_history.illicit_drugs_taken }} &nbsp;</span
          >
        </td>
      </tr>
      <tr
        class="bg-gray text-center padded-row"
        v-if="!isEmpty(current_medication)"
      >
        <td colspan="12"><b> Current Medications </b></td>
      </tr>
      <tr class="padded-row" v-if="!isEmpty(current_medication)">
        <td colspan="12">
          <span class="forDetails">
            {{ current_medication }}
          </span>
        </td>
      </tr>
      <tr
        class="bg-gray text-center padded-row"
        v-if="
          !isEmpty(pertinent_laboratory.pertinent_laboratory_and_procedures) &&
          !isEmpty(pertinent_laboratory.lab_procedure_other)
        "
      >
        <td colspan="12">
          <b> Pertinent Laboratory and Other Ancillary Procedures </b>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(pertinent_laboratory.pertinent_laboratory_and_procedures) ||
          !isEmpty(pertinent_laboratory.lab_procedure_other)
        "
      >
        <td colspan="12">
          Laboratory:
          <span class="forDetails">
            {{ pertinent_laboratory.pertinent_laboratory_and_procedures }}&nbsp;
          </span>
          Other:
          <span class="forDetails">
            {{ pertinent_laboratory.lab_procedure_other }}
          </span>
        </td>
      </tr>
      <tr class="padded-row" v-if="file_path && form_version == 'version2'">
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
      <tr
        class="bg-gray text-center"
        v-if="
          !isEmpty(review_of_system.skin) &&
          !isEmpty(review_of_system.skin_others) &&
          !isEmpty(review_of_system.head) &&
          !isEmpty(review_of_system.head_others) &&
          !isEmpty(review_of_system.eyes) &&
          !isEmpty(review_of_system.eyes_others) &&
          !isEmpty(review_of_system.ears) &&
          !isEmpty(review_of_system.ears_others) &&
          !isEmpty(review_of_system.nose_or_sinuses) &&
          !isEmpty(review_of_system.nose_others) &&
          !isEmpty(review_of_system.mouth_or_throat) &&
          !isEmpty(review_of_system.mouth_others) &&
          !isEmpty(review_of_system.neck) &&
          !isEmpty(review_of_system.neck_others) &&
          !isEmpty(review_of_system.breast) &&
          !isEmpty(review_of_system.breast_others) &&
          !isEmpty(review_of_system.respiratory_or_cardiac) &&
          !isEmpty(review_of_system.respiratory_others) &&
          !isEmpty(review_of_system.gastrointestinal) &&
          !isEmpty(review_of_system.gastrointestinal_others) &&
          !isEmpty(review_of_system.urinary) &&
          !isEmpty(review_of_system.urinary_others) &&
          !isEmpty(review_of_system.peripheral_vascular) &&
          !isEmpty(review_of_system.peripheral_vascular_others) &&
          !isEmpty(review_of_system.musculoskeletal) &&
          !isEmpty(review_of_system.musculoskeletal_others) &&
          !isEmpty(review_of_system.neurologic) &&
          !isEmpty(review_of_system.neurologic_others) &&
          !isEmpty(review_of_system.hematologic) &&
          !isEmpty(review_of_system.hematologic_others) &&
          !isEmpty(review_of_system.endocrine) &&
          !isEmpty(review_of_system.endocrine_others) &&
          !isEmpty(review_of_system.psychiatric) &&
          !isEmpty(review_of_system.psychiatric_others)
        "
      >
        <td colspan="12"><b> Review of Systems </b></td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.skin) ||
          !isEmpty(review_of_system.skin_others)
        "
      >
        <td colspan="12">
          Skin:
          <span class="forDetails"
            >{{
              review_of_system.skin
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.skin_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.head) ||
          !isEmpty(review_of_system.head_others)
        "
      >
        <td colspan="12">
          Head:
          <span class="forDetails"
            >{{
              review_of_system.head
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.head_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.eyes) ||
          !isEmpty(review_of_system.eyes_others)
        "
      >
        <td colspan="12">
          Eyes:
          <span class="forDetails"
            >{{
              review_of_system.eyes
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.eyes_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.ears) ||
          !isEmpty(review_of_system.ears_others)
        "
      >
        <td colspan="12">
          Ears:
          <span class="forDetails"
            >{{
              review_of_system.ears
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.ears_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.nose_or_sinuses) ||
          !isEmpty(review_of_system.nose_others)
        "
      >
        <td colspan="12">
          Nose/Sinuses:
          <span class="forDetails"
            >{{
              review_of_system.nose_or_sinuses
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.nose_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.mouth_or_throat) ||
          !isEmpty(review_of_system.mouth_others)
        "
      >
        <td colspan="12">
          Mouth/Throat:
          <span class="forDetails"
            >{{
              review_of_system.mouth_or_throat
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.mouth_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.neck) ||
          !isEmpty(review_of_system.neck_others)
        "
      >
        <td colspan="12">
          Neck:
          <span class="forDetails"
            >{{
              review_of_system.neck
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.neck_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.breast) ||
          !isEmpty(review_of_system.breast_others)
        "
      >
        <td colspan="12">
          Breast:
          <span class="forDetails"
            >{{
              review_of_system.breast
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.breast_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.respiratory_or_cardiac) ||
          !isEmpty(review_of_system.respiratory_others)
        "
      >
        <td colspan="12">
          Respiratory/Cardiac:
          <span class="forDetails"
            >{{
              review_of_system.respiratory_or_cardiac
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            review_of_system.respiratory_others
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.gastrointestinal) ||
          !isEmpty(review_of_system.gastrointestinal_others)
        "
      >
        <td colspan="12">
          Gastrointestinal:
          <span class="forDetails"
            >{{
              review_of_system.gastrointestinal
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            review_of_system.gastrointestinal_others
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.urinary) ||
          !isEmpty(review_of_system.urinary_others)
        "
      >
        <td colspan="12">
          Urinary:
          <span class="forDetails"
            >{{
              review_of_system.urinary
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{ review_of_system.urinary_others }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.peripheral_vascular) ||
          !isEmpty(review_of_system.peripheral_vascular_others)
        "
      >
        <td colspan="12">
          Peripheral Vascular:
          <span class="forDetails"
            >{{
              review_of_system.peripheral_vascular
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            review_of_system.peripheral_vascular_others
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.musculoskeletal) ||
          !isEmpty(review_of_system.musculoskeletal_others)
        "
      >
        <td colspan="12">
          Musculoskeletal:
          <span class="forDetails"
            >{{
              review_of_system.musculoskeletal
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            review_of_system.musculoskeletal_others
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.neurologic) ||
          !isEmpty(review_of_system.neurologic_others)
        "
      >
        <td colspan="12">
          Neurologic:
          <span class="forDetails"
            >{{
              review_of_system.neurologic
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            review_of_system.neurologic_others
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.hematologic) ||
          !isEmpty(review_of_system.hematologic_others)
        "
      >
        <td colspan="12">
          Hematologic:
          <span class="forDetails"
            >{{
              review_of_system.hematologic
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            review_of_system.hematologic_others
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.endocrine) ||
          !isEmpty(review_of_system.endocrine_others)
        "
      >
        <td colspan="12">
          Endocrine:
          <span class="forDetails"
            >{{
              review_of_system.endocrine
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            review_of_system.endocrine_others
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(review_of_system.psychiatric) ||
          !isEmpty(review_of_system.psychiatric_others)
        "
      >
        <td colspan="12">
          Psychiatric:
          <span class="forDetails"
            >{{
              review_of_system.psychiatric
                ?.replace("Select All,", "")
                ?.replace(",Others", "")
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            review_of_system.psychiatric_others
          }}</span>
        </td>
      </tr>
      <tr
        class="bg-gray text-center"
        v-if="
          !isEmpty(nutritional_status.diet) &&
          !isEmpty(nutritional_status.specify_diets)
        "
      >
        <td colspan="12"><b> Nutritional Status </b></td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(nutritional_status.diet) ||
          !isEmpty(nutritional_status.specify_diets)
        "
      >
        <td colspan="12">
          Diet:
          <span class="forDetails">{{ nutritional_status.diet }}&nbsp;</span>
          Specify Diets:
          <span class="forDetails">{{ nutritional_status.specify_diets }}</span>
        </td>
      </tr>
      <tr
        class="bg-gray text-center"
        v-if="
          !isEmpty(latest_vital_signs.temperature) &&
          !isEmpty(latest_vital_signs.pulse_rate) &&
          !isEmpty(latest_vital_signs.respiratory_rate) &&
          !isEmpty(latest_vital_signs.blood_pressure) &&
          !isEmpty(latest_vital_signs.oxygen_saturation)
        "
      >
        <td colspan="12">
          <b>Latest Vital Signs</b>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(latest_vital_signs.temperature) ||
          !isEmpty(latest_vital_signs.pulse_rate) ||
          !isEmpty(latest_vital_signs.respiratory_rate)
        "
      >
        <td colspan="12">
          Temperature:
          <span class="forDetails"
            >{{ latest_vital_signs.temperature }}&nbsp;</span
          >
          Pulse Rate/Heart Rate:
          <span class="forDetails"
            >{{ latest_vital_signs.pulse_rate }}&nbsp;</span
          >
          Respiratory Rate:
          <span class="forDetails">{{
            latest_vital_signs.respiratory_rate
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(latest_vital_signs.blood_pressure) ||
          !isEmpty(latest_vital_signs.oxygen_saturation)
        "
      >
        <td colspan="12">
          Blood Pressure:
          <span class="forDetails"
            >{{ latest_vital_signs.blood_pressure }}&nbsp;</span
          >
          O2 Saturation:
          <span class="forDetails">{{
            latest_vital_signs.oxygen_saturation
          }}</span>
        </td>
      </tr>
      <tr
        class="bg-gray text-center"
        v-if="
          !isEmpty(glasgocoma_scale.pupil_size_chart) &&
          !isEmpty(glasgocoma_scale.motor_response) &&
          !isEmpty(glasgocoma_scale.verbal_response) &&
          !isEmpty(glasgocoma_scale.eye_response) &&
          !isEmpty(glasgocoma_scale.gsc_score) &&
          glasgocoma_scale.gsc_score != '0'
        "
      >
        <td colspan="12">
          <b>Glasgow Coma Scale</b>
        </td>
      </tr>
      <tr class="padded-row" v-if="!isEmpty(glasgocoma_scale.pupil_size_chart)">
        <td colspan="12">
          <table class="table table-bordered glasgow-table">
            <thead>
              <tr>
                <th
                  v-for="i in 10"
                  :key="i"
                  :class="{
                    highlight: Number(glasgocoma_scale?.pupil_size_chart) === i,
                  }"
                >
                  <b>{{ i }}</b>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td
                  v-for="i in 10"
                  :key="i"
                  :class="{
                    highlight: Number(glasgocoma_scale?.pupil_size_chart) === i,
                  }"
                >
                  <span
                    class="glasgow-dot"
                    :style="{
                      height: i * 4 + 2 + 'px',
                      width: i * 4 + 2 + 'px',
                    }"
                  >
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(glasgocoma_scale.pupil_size_chart) ||
          !isEmpty(glasgocoma_scale.motor_response)
        "
      >
        <td colspan="12">
          Pupil Size Chart:
          <span class="forDetails"
            >{{ glasgocoma_scale.pupil_size_chart }}&nbsp;&nbsp;</span
          >
          Motor Response:
          <span class="forDetails">{{ glasgocoma_scale.motor_response }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(glasgocoma_scale.verbal_response) ||
          !isEmpty(glasgocoma_scale.eye_response)
        "
      >
        <td colspan="12">
          Verbal Response:
          <span class="forDetails"
            >{{ glasgocoma_scale.verbal_response }}&nbsp;&nbsp;</span
          >
          Eye Response:
          <span class="forDetails">{{ glasgocoma_scale.eye_response }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(glasgocoma_scale.gsc_score) &&
          glasgocoma_scale.gsc_score != '0'
        "
      >
        <td colspan="12">
          GCS Response:
          <span class="forDetails">{{ glasgocoma_scale.gsc_score }}</span>
        </td>
      </tr>
      <tr class="bg-gray text-center">
        <td colspan="12">
          <b>Reason for Referral</b>
        </td>
      </tr>
      <tr v-if="!isEmpty(form.reason)">
        <td colspan="12">
          Reason for referral:
          <span class="forDetails">
            {{ form.reason }}
          </span>
        </td>
      </tr>
      <tr class="padded-row" v-if="!isEmpty(form.other_reason_referral)">
        <td colspan="12">
          Other Reason for Referral:
          <span class="forDetails">{{ form.other_reason_referral }}</span>
        </td>
      </tr>
      <tr
        class="bg-gray text-center"
        v-if="
          !isEmpty(obstetric_and_gynecologic_history.menarche) ||
          !isEmpty(obstetric_and_gynecologic_history.menopause) ||
          !isEmpty(obstetric_and_gynecologic_history.menstrual_cycle) ||
          !isEmpty(
            obstetric_and_gynecologic_history.menstrual_cycle_duration
          ) ||
          !isEmpty(
            obstetric_and_gynecologic_history.menstrual_cycle_padsperday
          ) ||
          !isEmpty(
            obstetric_and_gynecologic_history.menstrual_cycle_medication
          ) ||
          !isEmpty(obstetric_and_gynecologic_history.contraceptive_history) ||
          (!isEmpty(obstetric_and_gynecologic_history.contraceptive_others) &&
            !isEmpty(obstetric_and_gynecologic_history.parity_g)) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_ft) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_a) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_lnmp) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_p) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_pt) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_l) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_edc) ||
          !isEmpty(obstetric_and_gynecologic_history.aog_lnmp) ||
          !isEmpty(obstetric_and_gynecologic_history.aog_eutz) ||
          !isEmpty(obstetric_and_gynecologic_history.prenatal_history)
        "
      >
        <td colspan="12">
          <b>Obstetric and Gynecologic History</b>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(obstetric_and_gynecologic_history.menarche) ||
          !isEmpty(obstetric_and_gynecologic_history.menopause)
        "
      >
        <td colspan="12">
          Menarche:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.menarche }}&nbsp;</span
          >
          Menopause:
          <span class="forDetails">{{
            obstetric_and_gynecologic_history.menopause
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(obstetric_and_gynecologic_history.menstrual_cycle) ||
          !isEmpty(obstetric_and_gynecologic_history.menstrual_cycle_duration)
        "
      >
        <td colspan="12">
          Menstrual Cycle:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.menstrual_cycle }}&nbsp;</span
          >
          Menstrual Duration:
          <span class="forDetails">{{
            obstetric_and_gynecologic_history.menstrual_cycle_duration
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(
            obstetric_and_gynecologic_history.menstrual_cycle_padsperday
          ) ||
          !isEmpty(obstetric_and_gynecologic_history.menstrual_cycle_medication)
        "
      >
        <td colspan="12">
          Menstrual Pads per Day:
          <span class="forDetails"
            >{{
              obstetric_and_gynecologic_history.menstrual_cycle_padsperday
            }}&nbsp;</span
          >
          Menstrual Medication:
          <span class="forDetails">{{
            obstetric_and_gynecologic_history.menstrual_cycle_medication
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(obstetric_and_gynecologic_history.contraceptive_history) ||
          !isEmpty(obstetric_and_gynecologic_history.contraceptive_others)
        "
      >
        <td colspan="12">
          Contraceptive:
          <span class="forDetails"
            >{{
              obstetric_and_gynecologic_history.contraceptive_history
            }}&nbsp;</span
          >
          Others:
          <span class="forDetails">{{
            obstetric_and_gynecologic_history.contraceptive_others
          }}</span>
        </td>
      </tr>
      <tr class="padded-row">
        <td
          colspan="12"
          v-if="
            !isEmpty(obstetric_and_gynecologic_history.parity_g) ||
            !isEmpty(obstetric_and_gynecologic_history.parity_ft) ||
            !isEmpty(obstetric_and_gynecologic_history.parity_a) ||
            !isEmpty(obstetric_and_gynecologic_history.parity_lnmp)
          "
        >
          Parity
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(obstetric_and_gynecologic_history.parity_g) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_ft) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_a) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_lnmp)
        "
      >
        <td colspan="12">
          G:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.parity_g }}&nbsp;</span
          >
          FT:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.parity_ft }}&nbsp;</span
          >
          A:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.parity_a }}&nbsp;</span
          >
          LMP:
          <span class="forDetails">{{
            formatDate(obstetric_and_gynecologic_history.parity_lnmp)
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(obstetric_and_gynecologic_history.parity_p) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_pt) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_l) ||
          !isEmpty(obstetric_and_gynecologic_history.parity_edc)
        "
      >
        <td colspan="12">
          P:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.parity_p }}&nbsp;</span
          >
          PT:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.parity_pt }}&nbsp;</span
          >
          L:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.parity_l }}&nbsp;</span
          >
          EDC:
          <span class="forDetails">{{
            formatDate(obstetric_and_gynecologic_history.parity_edc)
          }}</span>
        </td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(obstetric_and_gynecologic_history.aog_lnmp) ||
          !isEmpty(obstetric_and_gynecologic_history.aog_eutz) ||
          !isEmpty(obstetric_and_gynecologic_history.prenatal_history)
        "
      >
        <td colspan="12">AOG</td>
      </tr>
      <tr
        class="padded-row"
        v-if="
          !isEmpty(obstetric_and_gynecologic_history.aog_lnmp) ||
          !isEmpty(obstetric_and_gynecologic_history.aog_eutz) ||
          !isEmpty(obstetric_and_gynecologic_history.prenatal_history)
        "
      >
        <td colspan="12">
          LMP:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.aog_lnmp }}&nbsp;</span
          >
          UTZ:
          <span class="forDetails"
            >{{ obstetric_and_gynecologic_history.aog_eutz }}&nbsp;</span
          >
          Prenatal History:
          <span class="forDetails">{{
            obstetric_and_gynecologic_history.prenatal_history
          }}</span>
        </td>
      </tr>
    </tbody>
  </table>
  <div
    v-if="
      form.patient_sex === 'Female' &&
      filteredPregnancy.length > 0 &&
      form_version != 'version1'
    "
    class="row"
  >
    <div class="col-sm-12">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr style="font-size: 10pt">
              <th class="text-center" style="width: 50%">Pregnancy Order</th>
              <th class="text-center" style="width: 20%">Year of Birth</th>
              <th class="text-center">Gestation Completed</th>
              <th class="text-center">Pregnancy Outcome</th>
              <th class="text-center">Place of Birth</th>
              <th class="text-center">Biological Sex</th>
              <th class="text-center" style="width: 50%">Birth Weight</th>
              <th class="text-center">Present Status</th>
              <th class="text-center">Complication(s)</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(record, index) in filteredPregnancy" :key="index">
              <td>{{ record.pregnancy_order }}</td>
              <td>{{ record.pregnancy_year }}</td>
              <td>{{ record.pregnancy_gestation_completed }}</td>
              <td>{{ record.pregnancy_outcome }}</td>
              <td>{{ record.pregnancy_place_of_birth }}</td>
              <td>{{ record.pregnancy_sex }}</td>
              <td>{{ record.pregnancy_birth_weight }}</td>
              <td>{{ record.pregnancy_present_status }}</td>
              <td>{{ record.pregnancy_complication }}</td>
            </tr>
            <tr v-if="filteredPregnancy.length === 0">
              <td colspan="9" class="text-center">No data available.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add your component styles here */
.glasgow-table {
  border: 1px solid lightgrey;
  width: 100%;
}
.glasgow-table th.highlight,
.glasgow-table td.highlight {
  border: 2px solid orange !important;
  background-color: #fffbda !important; /* Light red background */
}

.glasgow-dot {
  background-color: #494646;
  border-radius: 50%;
  display: inline-block;
}
#glasgow_table_1,
tr td:nth-child(1) {
  width: 35%;
}
#glasgow_table_2 tr td:nth-child(2) {
  width: 35%;
}
</style>
