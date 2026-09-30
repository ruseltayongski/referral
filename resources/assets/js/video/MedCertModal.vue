<template>
  <div class="modal fade" id="medCertModal" tabindex="-1" ref="modalEl">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Medical Certificate</h5>
          <button type="button" class="close" data-dismiss="modal" @click="hide">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <form @submit.prevent="submit">
            <div class="form-group position-relative">
              <label for="mcIcd10Search">ICD-10 Search</label>
              <input
                id="mcIcd10Search"
                v-model="icdKeyword"
                type="text"
                class="form-control"
                placeholder="Type a code or description (e.g. J45, Asthma)"
                autocomplete="off"
                @input="onIcdInput"
                @keydown.down.prevent="moveHighlight(1)"
                @keydown.up.prevent="moveHighlight(-1)"
                @keydown.enter.prevent="selectHighlighted"
              />

              <ul
                v-if="icdResults.length"
                class="list-group position-absolute w-100"
                style="z-index: 1060; max-height: 220px; overflow-y: auto;"
              >
                <li
                  v-for="(item, index) in icdResults"
                  :key="item.id"
                  class="list-group-item list-group-item-action"
                  :class="{ active: index === highlightedIndex }"
                  style="cursor: pointer;"
                  @click="selectIcd(item)"
                  @mouseover="highlightedIndex = index"
                >
                  <strong>{{ item.code }}</strong> — {{ item.description }}
                </li>
              </ul>

              <small v-if="icdSearching" class="text-muted">Searching...</small>
            </div>

            <div class="form-group">
              <label for="mcDiagnosis">Diagnosis</label>
              <textarea
                id="mcDiagnosis"
                v-model="form.diagnosis"
                class="form-control"
                rows="3"
                required
              ></textarea>
              <small class="form-text text-muted">
                Selected ICD-10 codes are appended here automatically — edit freely.
              </small>
            </div>

            <div class="form-group">
              <label for="mcRecommendation">Recommendation</label>
              <textarea
                id="mcRecommendation"
                v-model="form.recommendation"
                class="form-control"
                rows="4"
                required
              ></textarea>
            </div>

            <div v-if="error" class="alert alert-danger">{{ error }}</div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="hide">
            Cancel
          </button>
          <button type="button" class="btn btn-primary" :disabled="submitting" @click="submit">
            {{ submitting ? 'Generating...' : 'Generate Certificate' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MedCertModal',
  data() {
    return {
      patient_code: null,
      form: {
        diagnosis: '',
        recommendation: '',
      },
      submitting: false,
      error: null,

      // ICD-10 search state
      icdKeyword: '',
      icdResults: [],
      icdSearching: false,
      highlightedIndex: -1,
      icdDebounceTimer: null,
    };
  },
  methods: {
    show(referral_code) {
      this.patient_code = referral_code;
      this.form.diagnosis = '';
      this.form.recommendation = '';
      this.icdKeyword = '';
      this.icdResults = [];
      this.error = null;
      // jQuery/Bootstrap 4 modal, consistent with existing modals in the app
      window.$(this.$refs.modalEl).modal('show');
    },
    hide() {
      window.$(this.$refs.modalEl).modal('hide');
    },

    onIcdInput() {
      clearTimeout(this.icdDebounceTimer);
      const keyword = this.icdKeyword.trim();

      if (!keyword) {
        this.icdResults = [];
        return;
      }

      this.icdDebounceTimer = setTimeout(() => {
        this.icdSearching = true;
        axios
          .post(`/api/icd10/search/${encodeURIComponent(keyword)}`)
          .then((response) => {
            this.icdResults = response.data.icd || [];
            this.highlightedIndex = this.icdResults.length ? 0 : -1;
          })
          .catch(() => {
            this.icdResults = [];
          })
          .finally(() => {
            this.icdSearching = false;
          });
      }, 300); // debounce so it's not firing on every keystroke
    },

    moveHighlight(delta) {
      if (!this.icdResults.length) return;
      const next = this.highlightedIndex + delta;
      this.highlightedIndex = Math.max(0, Math.min(this.icdResults.length - 1, next));
    },

    selectHighlighted() {
      if (this.highlightedIndex >= 0 && this.icdResults[this.highlightedIndex]) {
        this.selectIcd(this.icdResults[this.highlightedIndex]);
      }
    },

    selectIcd(item) {
      const line = `${item.code} - ${item.description}`;
      this.form.diagnosis = this.form.diagnosis
        ? `${this.form.diagnosis}\n${line}`
        : line;

      // Reset search UI after selection
      this.icdKeyword = '';
      this.icdResults = [];
      this.highlightedIndex = -1;
    },

    submit() {
    if (!this.form.diagnosis.trim() || !this.form.recommendation.trim()) {
        this.error = 'Diagnosis and recommendation are both required.';
        return;
    }

    this.submitting = true;
    this.error = null;

    axios
        .post(`/api/submit/recommendation/${this.patient_code}`, {
        diagnosis: this.form.diagnosis,
        recommendation: this.form.recommendation,
        })
        .then((response) => {
        this.submitting = false;

        if (response.data.success) {
            this.hide();

            Lobibox.notify('success', {
            size: 'mini',
            rounded: true,
            delayIndicator: false,
            sound: false,
            msg: response.data.message || 'Medical certificate recorded successfully.',
            });

            this.$emit('generated', response.data.data);
        } else {
            this.error = response.data.message || 'Something went wrong.';

            Lobibox.notify('warning', {
            size: 'mini',
            rounded: true,
            delayIndicator: false,
            sound: false,
            msg: this.error,
            });
        }
        })
        .catch((err) => {
        this.submitting = false;
        this.error =
            err.response?.data?.message || 'Failed to generate medical certificate.';

        Lobibox.notify('error', {
            size: 'mini',
            rounded: true,
            delayIndicator: false,
            sound: false,
            msg: this.error,
        });
        });
    },
  },
};
</script>