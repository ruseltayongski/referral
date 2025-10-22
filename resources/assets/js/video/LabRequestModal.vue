<script>
import { getLaboratories, saveLabRequest } from "./api/index";
// import axios from 'axios';
export default {
  data() {
    return {
      baseUrl: $("#broadcasting_url").val(),
      searchTerm: "",
      laboratories: [],
      laboratoriesHolder: [],
      checkedLaboratories: [],
      isOtherSelected: false,
      otherLabRequest: "",
    };
  },
  props: {
    activity_id: {
      type: Number,
    },
    requested_by: {
      type: Number,
    },
    code: {
      type: String,
    },
  },
  mounted() {
    // console.log(this.activity_id)
    this.__getLaboratories();
    // if (this.code) {
    this.fetchLabRequest(this.requested_by);
    // }
  },
  watch: {
    laboratories: {
      handler(newVal, oldVal) {
        // Reset checked laboratories when laboratories data changes
        // This prevents stale references from causing issues
        if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
          this.checkedLaboratories = [];
          this.isOtherSelected = false;
          this.otherLabRequest = "";
        }
      },
      deep: true,
    },
  },
  methods: {
    searchLaboratories() {
      const searchTermLower = this.searchTerm.toLowerCase();
      const searchTermRegex = new RegExp(
        searchTermLower.replace(/[-/\\^$*+?.()|[\]{}]/g, "\\$&"),
        "i"
      );
      if (searchTermLower) {
        this.laboratories = Object.entries(this.laboratoriesHolder)
          .filter(([laboratoryCode, laboratoryDescription]) => {
            return (
              searchTermRegex.test(laboratoryCode.toLowerCase()) ||
              searchTermRegex.test(laboratoryDescription.toLowerCase())
            );
          })
          .reduce((filtered, [laboratoryCode, laboratoryDescription]) => {
            filtered[laboratoryCode] = laboratoryDescription;
            return filtered;
          }, {});
      } else {
        this.laboratories = this.laboratoriesHolder;
      }
    },
    submitForm() {
      let selectedLabs = [...this.checkedLaboratories];
      let otherRequest = null;
      if (this.isOtherSelected && this.otherLabRequest.trim() !== "") {
        //  selectedLabs.push(this.otherLabRequest.trim());
        otherRequest = this.otherLabRequest.trim();
      }

      if (this.checkedLaboratories.length > 0 || selectedLabs.length > 0) {
        const params = {
          activity_id: this.activity_id,
          requested_by: this.requested_by,
          laboratory_code: this.checkedLaboratories,
          laboratory_others: otherRequest, // <-- send null if none
          // laboratory_others: selectedLabs
        };
        this.__saveLaboratories(params);
        $("#labRequestModal").modal("hide");
        Lobibox.notify("success", {
          title: "Laboratory Request",
          msg: "Laboratory successfully request",
          img: this.baseUrl + "/resources/img/ro7.png",
        });
        return;
      }
      Lobibox.alert("error", {
        msg: "Please select a laboratory to request",
      });
    },
    async __getLaboratories() {
      try {
        const response = await getLaboratories();
        // Ensure we have clean object references
        this.laboratoriesHolder = { ...response.data };
        this.laboratories = { ...response.data };

        // Reset checkbox state to prevent any lingering issues
        this.checkedLaboratories = [];
        this.isOtherSelected = false;
        this.otherLabRequest = "";

        // Force Vue to re-render the checkboxes
        this.$nextTick(() => {
          this.$forceUpdate();
        });
      } catch (error) {
        console.error("Error loading laboratories:", error);
      }
      // const response = await getLaboratories()
      // this.laboratories = response.data;
      // this.laboratoriesHolder = response.data;
    },
    async __saveLaboratories(params) {
      try {
        const response = await saveLabRequest(params);
        // console.log(response.data)
      } catch (error) {
        console.error("Error saving laboratories:", error);
      }
    },
    getCheckboxKey(laboratoryCode) {
      return `checkbox_${this.activity_id}_${laboratoryCode}`;
    },
    async fetchLabRequest(code) {
      try {
        const response = await axios.get(
          `${this.baseUrl}/api/video/labresults/${code}`
        );
        const labRequests = response.data.labrequests;
        console.log("LabRequestData:", response.data);
        console.log("user code:", this.requested_by);
        // if (labRequests && labRequests.length > 0) {
        //     // Reset existing selections
        //     this.checkedLaboratories = [];
        //     this.isOtherSelected = false;
        //     this.otherLabRequest = '';

        //     // Process each lab request
        //     labRequests.forEach(request => {
        //         if (request.laboratory_code) {
        //             this.checkedLaboratories.push(request.laboratory_code);
        //         }
        //         if (request.others) {
        //             this.isOtherSelected = true;
        //             this.otherLabRequest = request.others;
        //         }
        //     });
        // }
      } catch (error) {
        console.error("Error fetching lab requests:", error);
      }
    },
  },
};
</script>

<template>
  <div
    class="modal fade"
    id="labRequestModal"
    data-backdrop="static"
    data-keyboard="false"
    aria-labelledby="prescriptionModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fs-5" id="prescriptionModalLabel">
            <i class="bi bi-prescription2"></i> Lab Request
          </h5>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="input-group mb-3">
            <input
              type="text"
              class="form-control"
              placeholder="Search Laboratory..."
              aria-label="Search..."
              aria-describedby="searchButton"
              v-model="searchTerm"
              @keyup="searchLaboratories"
            />
            <!-- <button class="btn btn-secondary" type="button" id="laboratorySearch" @click="searchLaboratories">Search</button> -->
          </div>
          <div id="modal-body-scroll">
            <!-- <div class="form-check laboratory-check" v-for="(laboratoryDescription, laboratoryCode) in laboratories" :key="getCheckboxKey(laboratoryCode)" >
                            <input class="form-check-input" type="checkbox" v-model="checkedLaboratories" :value="laboratoryCode">
                            <label class="form-check-label" :for="getCheckboxKey(laboratoryCode)"  :name="`lab_${activity_id}_${laboratoryCode}`">
                                {{ `${laboratoryDescription}` }}
                            </label>
                        </div> -->

            <div
              class="form-check laboratory-check"
              v-for="(laboratoryDescription, laboratoryCode) in laboratories"
              :key="getCheckboxKey(laboratoryCode)"
            >
              <input
                class="form-check-input"
                type="checkbox"
                v-model="checkedLaboratories"
                :value="laboratoryCode"
                :id="getCheckboxKey(laboratoryCode)"
                :name="`lab_${activity_id}_${laboratoryCode}`"
              />
              <label
                class="form-check-label"
                :for="getCheckboxKey(laboratoryCode)"
              >
                {{ laboratoryDescription }}
              </label>
            </div>

            <!-- "Others" Option -->
            <div class="form-check laboratory-check">
              <input
                class="form-check-input"
                type="checkbox"
                v-model="isOtherSelected"
              />
              <label class="form-check-label"> Others (Specify) </label>
            </div>

            <div v-if="isOtherSelected" class="mt-2">
              <input
                type="text"
                class="form-control"
                v-model="otherLabRequest"
                placeholder="Enter specific lab request..."
              />
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button
            class="btn btn-success btn-sm"
            type="button"
            @click="submitForm"
          >
            <i class="bi bi-save"></i> Submit
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
#laboratorySearch {
  margin-top: -1px;
}

.laboratory-check {
  padding: 5px;
  margin-left: 20px;
}

.form-check-label {
  margin-left: 10px;
}

.form-check-input {
  cursor: pointer;
  transform: scale(1.5);
}

#modal-body-scroll {
  max-height: 65vh;
  overflow-y: auto;
}
</style>
