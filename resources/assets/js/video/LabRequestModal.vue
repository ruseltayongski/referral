<script>
import { getLaboratories, saveLabRequest } from "./api/index";
import axios from "axios";

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
    activity_id: Number,
    requested_by: Number,
    code: String,
  },
  mounted() {
    this.__getLaboratories().then(() => {
      this.fetchLabRequest(this.activity_id);
    });
  },
  methods: {
    searchLaboratories() {
      const term = this.searchTerm.trim().toLowerCase();
      if (!term) {
        this.laboratories = this.laboratoriesHolder;
        return;
      }

      this.laboratories = Object.fromEntries(
        Object.entries(this.laboratoriesHolder).filter(
          ([code, desc]) =>
            code.toLowerCase().includes(term) ||
            desc.toLowerCase().includes(term)
        )
      );
    },

    async __getLaboratories() {
      try {
        const response = await getLaboratories();
        this.laboratoriesHolder = { ...response.data };
        this.laboratories = { ...response.data };
        this.checkedLaboratories = [];
        this.isOtherSelected = false;
        this.otherLabRequest = "";
      } catch (error) {
        console.error("Error loading laboratories:", error);
      }
    },

    async __saveLaboratories(params) {
      try {
        await saveLabRequest(params);
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
        const labRequests = response.data || [];

        // Reset selections
        this.checkedLaboratories = [];
        this.isOtherSelected = false;
        this.otherLabRequest = "";

        // Populate checkboxes
        if (Array.isArray(labRequests)) {
          labRequests.forEach((request) => {
            if (request.laboratory_code) {
              this.checkedLaboratories.push(request.laboratory_code);
            }
            if (request.others && request.others.trim() !== "") {
              this.isOtherSelected = true;
              this.otherLabRequest = request.others.trim();
            }
          });
        }

        // ✅ Ensure checkboxes visually update
        this.$nextTick(() => {
          this.$forceUpdate();
        });

        // console.log("Checked Labs:", this.checkedLaboratories);
      } catch (error) {
        console.error("Error fetching lab requests:", error);
      }
    },

    submitForm() {
      const otherRequest =
        this.isOtherSelected && this.otherLabRequest.trim()
          ? this.otherLabRequest.trim()
          : null;

      if (this.checkedLaboratories.length === 0 && !otherRequest) {
        Lobibox.alert("error", {
          msg: "Please select a laboratory to request",
        });
        return;
      }

      const params = {
        activity_id: this.activity_id,
        requested_by: this.requested_by,
        laboratory_code: this.checkedLaboratories,
        laboratory_others: otherRequest,
      };

      this.__saveLaboratories(params);
      $("#labRequestModal").modal("hide");
      Lobibox.notify("success", {
        title: "Laboratory Request",
        msg: "Laboratory successfully requested",
        img: `${this.baseUrl}/resources/img/ro7.png`,
      });
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
          <h5 class="modal-title fs-5">
            <i class="bi bi-prescription2"></i> Lab Request
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="input-group mb-3">
            <input
              type="text"
              class="form-control"
              placeholder="Search Laboratory..."
              v-model="searchTerm"
              @keyup="searchLaboratories"
            />
          </div>

          <div id="modal-body-scroll">
            <div
              class="form-check laboratory-check"
              v-for="(desc, code) in laboratories"
              :key="getCheckboxKey(code)"
            >
              <input
                class="form-check-input"
                type="checkbox"
                v-model="checkedLaboratories"
                :value="code"
                :id="getCheckboxKey(code)"
                :name="`lab_${activity_id}_${code}`"
              />
              <label class="form-check-label" :for="getCheckboxKey(code)">
                {{ desc }}
              </label>
            </div>

            <div class="form-check laboratory-check mt-2">
              <input
                class="form-check-input"
                type="checkbox"
                v-model="isOtherSelected"
              />
              <label class="form-check-label">Others (Specify)</label>
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
          <button class="btn btn-success btn-sm" @click="submitForm">
            <i class="bi bi-save"></i> Submit
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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
