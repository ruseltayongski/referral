<template>
  <PrivacyNoticeModal
    ref="privacyModal"
    modal-id="telemedicinePrivacyNoticeModal"
    @accepted="onPrivacyAccepted"
  />
  <div class="modal fade" role="dialog" id="telemedicineFormModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body" style="padding: 0;">
          <!-- Header Section -->
          <div style="text-align: center; padding: 15px; border-bottom: 2px solid #ccc;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
              <img :src="logoPath" alt="DOH Logo" style="height: 60px;" @error="onLogoError">
              <div>
                <div style="font-size: 11px; font-weight: normal;">Republic of the Philippines</div>
                <div style="font-size: 11px; font-weight: bold;">DEPARTMENT OF HEALTH</div>
                <div style="font-size: 11px; font-weight: bold;">CENTRAL VISAYA'S CENTER FOR HEALTH DEVELOPMENT</div>
                <div style="font-size: 10px;">Osmeña Boulevard, Cebu City</div>
                <div style="font-size: 10px;">Regional Director's Office Tel. No. (032) 253-6355 Fax No. (032) 254-0109</div>
                <div style="font-size: 10px;">Official Website: http://www.ro7.doh.gov.ph Email Address: dohro7@gmail.com</div>
              </div>
            </div>
            <h4 style="color: #17a2b8; margin: 10px 0 5px 0; font-size: 18px; font-weight: bold;">Clinical Telemedicine Consultation</h4>
          </div>

          <form @submit.prevent="submitForm" style="padding: 20px;">
            <!-- Appointment Details -->
            <div class="row" style="margin-bottom: 15px;">
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">APPOINTMENT DATE/TIME</label>
                <div style="background: white; padding: 6px 10px; min-height: 30px;">{{ formData.appointment_datetime}}</div>
              </div>
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">NAME OF DOCTOR/CONSULTANT</label>
                <div style="background: white; padding: 6px 10px; min-height: 30px;">{{ formData.doctor_name }}</div>
              </div>
              <!-- <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">ADDRESS</label>
                <div style="background: white; padding: 6px 10px; min-height: 30px;">{{ formData.referred_to_address }}</div>
              </div> -->
            </div>

            <!-- Referred To Section -->
            <!-- <div class="row" style="margin-bottom: 15px;">
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">REFERRED TO <span style="color: red;">*</span></label>
                <select class="form-control input-sm" v-model="formData.referred_to" :disabled="appointmentFieldsLocked" required>
                  <option value="">Select facility...</option>
                  <option v-for="facility in facilities" :key="facility.id" :value="facility.id">
                    {{ facility.name }}
                  </option>
                </select>
              </div>
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">DEPARTMENT <span style="color: red;">*</span></label>
                <select class="form-control input-sm" v-model="formData.department" :disabled="appointmentFieldsLocked" required>
                  <option value="">Select option</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.description }}
                  </option>
                </select>
              </div>
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">ADDRESS</label>
                <input type="text" class="form-control input-sm" v-model="selectedFacilityAddress" :disabled="appointmentFieldsLocked" readonly>
              </div>
            </div> -->


            <!-- Patient Basic Information -->
            <div class="row" style="margin-bottom: 15px;">
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">DATE/TIME REFERRED (ReCo) <span style="color: red;">*</span></label>
                <input type="text" class="form-control input-sm" v-model="formData.date_time_referred" readonly>
              </div>
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">NAME OF PATIENT <span style="color: red;">*</span></label>
                <input type="text" class="form-control input-sm" v-model="formData.patient_name" readonly>
              </div>
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">ADDRESS</label>
                <input type="text" class="form-control input-sm" v-model="formData.patient_address" readonly>
              </div>
            </div>

            
            <!-- Demographics -->
            <div class="row" style="margin-bottom: 15px;">
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">AGE <span style="color: red;">*</span></label>
                <input type="text" class="form-control input-sm" v-model="formData.age" readonly>
              </div>
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">SEX <span style="color: red;">*</span></label>
                <select class="form-control input-sm" v-model="formData.sex" :disabled="appointmentFieldsLocked" readonly>
                  <option value="">Select option</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
              <div class="col-md-4">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">CIVIL STATUS <span style="color: red;">*</span></label>
                <select class="form-control input-sm" v-model="formData.civil_status" :disabled="appointmentFieldsLocked" readonly>
                  <option value="">Select option</option>
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Divorced">Divorced</option>
                  <option value="Separated">Separated</option>
                  <option value="Widowed">Widowed</option>
                </select>
              </div>
            </div>

            <!-- Case Summary -->
            <div class="row" style="margin-bottom: 15px;">
              <div class="col-md-12">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 3px;">
                  CHIEF COMPLAINTS <span style="font-style: italic; font-weight: normal;">(symptoms, duration, comorbidities, medicine taken, allergies etc.)</span> <span style="color: red;">*</span>
                </label>
                <textarea class="form-control" v-model="formData.case_summary" rows="5" style="resize: none; font-size: 12px;" required></textarea>
              </div>
            </div>

            <!-- File Attachments -->
            <div class="row" style="margin-bottom: 15px;">
              <div class="col-md-12">
                <label style="font-size: 11px; font-weight: bold; margin-bottom: 5px;">FILE ATTACHMENTS<span style="font-style: italic; font-weight: normal;"> ( Laboratory results, medical certificates, prescriptions, imaging results, wounds and injuries, and other relevant documents. )</span></label>
                <button type="button" v-if="uploadedFiles.length > 0" class="btn btn-md btn-danger" @click="removeAllFiles" style="margin-bottom: 10px;">Remove All Files</button>
                <br>
                <div class="file-upload-container" style="display: flex; flex-wrap: wrap; gap: 15px;">
                  <!-- Uploaded Files -->
                  <div v-for="(file, index) in uploadedFiles" :key="'file-' + index" class="col-md-3" style="flex: 0 0 auto;">
                    <div class="file-upload">
                      <div class="file-upload-content" style="position: relative; border: 2px solid #28a745; border-radius: 4px; padding: 10px;">
                        <img class="file-upload-image" :src="file.preview" style="width: 100%; height: 120px; object-fit: contain;" />
                        <div class="image-title-wrap" style="margin-top: 8px;">
                          <b><small class="image-title" style="display: block; word-wrap: break-word; font-size: 10px;" :title="file.name">{{ truncateFileName(file.name) }}</small></b>
                          <button type="button" class="btn btn-danger btn-xs" @click="removeFile(index)" style="margin-top: 5px;">
                            <i class="fa fa-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Upload Area -->
                  <div class="col-md-3" style="flex: 0 0 auto;">
                    <div class="file-upload">
                      <div class="text-center image-upload-wrap" style="cursor: pointer; padding: 20px; border: 2px dashed #ccc; border-radius: 4px;" @click="triggerFileInput()">
                        <input 
                          ref="fileInput"
                          type="file" 
                          @change="handleFileUpload" 
                          accept="image/*,application/pdf,.doc,.docx"
                          multiple
                          style="display: none;"
                        >
                        <img :src="addFileImagePath" style="width: 50%; height: 50%;">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="row">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-default" @click="closeModal" style="margin-right: 5px;">
                  <i class="fa fa-times"></i> Close
                </button>
                <button type="submit" class="btn btn-success">
                  <i class="fa fa-check"></i> {{ isSubmitting ? 'Submitting...' : 'Submit' }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div v-if="isSubmitting" class="loading-overlay" role="status" aria-live="polite">
    <div class="loading-card">
      <div class="loading-spinner"></div>
      <div class="loading-text">Submitting referral, please wait...</div>
    </div>
  </div>
</template>

<script>
import PrivacyNoticeModal from './PrivacyNoticeModal.vue';

export default {
  name: 'TelemedicineConsultationForm',

  components: {
    PrivacyNoticeModal
  },
  
  props: {
    user: {
      type: Object,
      default: () => ({})
    },
    appointmentData: {
      type: Object,
      default: () => ({})
    },
    patientData: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      formData: {
        date_time_referred: '',
        appointment_datetime: '',
        patient_name: '',
        patient_address: '',
        referred_to_address: '',
        referred_to: '',
        department: '',
        doctor_name: '',
        age: '',
        sex: '',
        civil_status: '',
        case_summary: '',
        opdSubId: ''
      },
      
      facilities: [],
      departments: [],
      
      // File uploads
      uploadedFiles: [],

      isSubmitting: false,

      privacyAccepted: false,
      requestOpenHandler: null,
      formModalShowHandler: null,
      formModalHiddenHandler: null
    };
  },

  computed: {
    appointmentFieldsLocked() {
      return !!(this.formData.referred_to || this.formData.department);
    },

    selectedFacilityAddress() {
      const facility = this.facilities.find(f => f.id === this.formData.referred_to);
      return facility ? facility.address : '';
    },
    
    logoPath() {
      // Try to get base URL from broadcasting_url input or use window origin
      const baseUrl = (typeof $ !== 'undefined' && $("#broadcasting_url").length) 
        ? $("#broadcasting_url").val() 
        : window.location.origin;
      return `${baseUrl}/resources/img/doh.png`;
    },

    addFileImagePath() {
      const baseUrl = (typeof $ !== 'undefined' && $("#broadcasting_url").length) 
        ? $("#broadcasting_url").val() 
        : window.location.origin;
      return `${baseUrl}/resources/img/add_file.png`;
    }
  },

  mounted() {
    this.initializeForm();
    this.setupPrivacyGuards();
  },

  beforeUnmount() {
    if (this.requestOpenHandler) {
      window.removeEventListener('telemedicine:request-open', this.requestOpenHandler);
    }

    if (typeof $ !== 'undefined') {
      $('#telemedicineFormModal').off('show.bs.modal.telemedPrivacy');
      $('#telemedicineFormModal').off('hidden.bs.modal.telemedPrivacy');
    }
  },

  watch: {
    appointmentData: {
      handler(newData) {
        if (newData && Object.keys(newData).length > 0) {
          // Populate form fields from appointment data
          if (newData.facility_id) {
            this.formData.referred_to = newData.facility_id;
          }
          if (newData.departmentId) {
            this.formData.department = newData.departmentId;
          }
          this.formData.appointment_datetime = this.formatAppointmentDateTime(newData);
          this.formData.doctor_name = newData.doctor_name || newData.createdBy || '';
        }
      },
      deep: true,
      immediate: true
    },
    'formData.referred_to': {
      handler() {
        this.formData.referred_to_address = this.selectedFacilityAddress;
      }
    },
    user: {
      handler(newUser) {
        if (newUser && Object.keys(newUser).length > 0) {
          this.populateUserData(newUser);
        }
      },
      deep: true,
      immediate: true
    },
    patientData: {
      handler(newData) {
        if (newData && Object.keys(newData).length > 0) {
          this.populatePatientData(newData);
        }
      },
      deep: true,
      immediate: true
    }
  },

  methods: {
    setupPrivacyGuards() {
      this.requestOpenHandler = () => {
        this.showPrivacyNotice();
      };
      window.addEventListener('telemedicine:request-open', this.requestOpenHandler);

      if (typeof $ !== 'undefined') {
        this.formModalShowHandler = (event) => {
          if (!this.privacyAccepted) {
            event.preventDefault();
            this.showPrivacyNotice();
          }
        };

        this.formModalHiddenHandler = () => {
          this.privacyAccepted = false;
        };

        $('#telemedicineFormModal').on('show.bs.modal.telemedPrivacy', this.formModalShowHandler);
        $('#telemedicineFormModal').on('hidden.bs.modal.telemedPrivacy', this.formModalHiddenHandler);
      }
    },

    showPrivacyNotice() {
      this.privacyAccepted = false;
      this.$refs.privacyModal.show();
    },

    onPrivacyAccepted() {
      this.privacyAccepted = true;
      this.$nextTick(() => {
        if (typeof $ !== 'undefined' && $('#telemedicineFormModal').length) {
          $('#telemedicineFormModal').modal('show');
        }
      });
    },

    initializeForm() {
      // Set date/time
      const now = new Date();
      const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      };
      this.formData.date_time_referred = now.toLocaleDateString('en-US', options);
      
      // Populate user data if available
      if (this.user && Object.keys(this.user).length > 0) {
        this.populateUserData(this.user);
      }
      
      // Load facilities and departments from backend
      this.loadFacilities();
      this.loadDepartments();
    },

    loadFacilities() {
      const baseUrl = (typeof $ !== 'undefined' && $("#broadcasting_url").length)
        ? $("#broadcasting_url").val()
        : window.location.origin;
      const url = `${baseUrl}/iMkiW5YcHA6D9Gd7BuTteeQPVx4a1UxK`;

      $.ajax({
        url: url,
        type: 'GET',
        success: (response) => {
          const list = Array.isArray(response)
            ? response
            : (response.data || response.facilities || []);
          this.facilities = list;
          this.formData.referred_to_address = this.selectedFacilityAddress;
        },
        error: (error) => {
          console.error('Failed to load facilities:', error);
        }
      });
    },

    formatAppointmentDateTime(appointment) {
      if (!appointment || !appointment.appointment_date) {
        return '';
      }
      
      try {
        // Parse the appointment date
        const dateStr = appointment.appointment_date;
        const timeFrom = appointment.appointedTime || '';
        const timeTo = appointment.appointedTimeTo || '';
        
        // Create a Date object from the date string
        const appointmentDate = new Date(dateStr);
        
        // Format the date like DATE/TIME REFERRED using toLocaleDateString
        const options = {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        };
        const formattedDate = appointmentDate.toLocaleDateString('en-US', options);
        
        // Add time if available
        if (timeFrom) {
          const formattedFrom = this.formatTime12Hour(timeFrom, dateStr);
          const formattedTo = timeTo ? this.formatTime12Hour(timeTo, dateStr) : '';
          const timeRange = formattedTo ? `${formattedFrom} - ${formattedTo}` : formattedFrom;
          return `${formattedDate}, ${timeRange}`;
        }
        
        return formattedDate;
      } catch (error) {
        console.error('Error formatting appointment date:', error);
        return appointment.appointment_date;
      }
    },

    formatTime12Hour(timeStr, dateStr) {
      if (!timeStr) {
        return '';
      }
      // If already formatted with AM/PM, return as-is
      if (/am|pm/i.test(timeStr)) {
        return timeStr;
      }
      const dateTime = dateStr ? `${dateStr}T${timeStr}` : `1970-01-01T${timeStr}`;
      const dt = new Date(dateTime);
      if (Number.isNaN(dt.getTime())) {
        return timeStr;
      }
      return dt.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    },

    loadDepartments() {
      const baseUrl = (typeof $ !== 'undefined' && $("#broadcasting_url").length)
        ? $("#broadcasting_url").val()
        : window.location.origin;
      const url = `${baseUrl}/XO2XFSiDX2PdHyLbq9WNHhA95vy3Fdld`;

      $.ajax({
        url: url,
        type: 'GET',
        success: (response) => {
          const list = Array.isArray(response)
            ? response
            : (response.data || response.departments || []);
          this.departments = list;
        },
        error: (error) => {
          console.error('Failed to load departments:', error);
        }
      });
    },

    populateUserData(user) {
      // Set patient name from user data
      if (user.fname || user.lname) {
        this.formData.patient_name = `${user.fname || ''} ${user.mname || ''} ${user.lname || ''}`.trim();
      }
      
      // Set patient address if available
      if (user.address) {
        this.formData.patient_address = user.address;
      }
      
      // Set age if available
      if (user.age) {
        this.formData.age = user.age;
      }
      
      // Set sex if available
      if (user.sex) {
        this.formData.sex = user.sex;
      }
      
      // Set civil status if available
      if (user.civil_status) {
        this.formData.civil_status = user.civil_status;
      }
    },

    populatePatientData(patient) {
      // Populate from database patient record
      // The API returns pre-formatted fields: patient_name, address, age
      if (patient.patient_name) {
        this.formData.patient_name = patient.patient_name;
      } else if (patient.fname || patient.lname) {
        this.formData.patient_name = `${patient.fname || ''} ${patient.mname || ''} ${patient.lname || ''}`.trim();
      }
      
      // Use pre-formatted address from API
      if (patient.address) {
        this.formData.patient_address = patient.address;
      }
      
      // Use pre-calculated age from API
      if (patient.age) {
        this.formData.age = patient.age.toString();
      } 
      
      // Set sex
      if (patient.sex) {
        this.formData.sex = patient.sex;
      }
      
      // Set civil status
      if (patient.civil_status) {
        this.formData.civil_status = patient.civil_status;
      }
    },

    submitForm() {
      const formData = new FormData();
      
      // Append all form fields
      Object.keys(this.formData).forEach(key => {
        formData.append(key, this.formData[key] || '');
      });

      // Ensure case summary is included
      if (typeof formData.set === 'function') {
        formData.set('case_summary', this.formData.case_summary || '');
      } else {
        formData.append('case_summary', this.formData.case_summary || '');
      }
  
      // return;
      // Append IDs and appointment context
      const patientId = (this.patientData && this.patientData.id) || (this.user && this.user.patient_id) || '';
      const patientCode = (this.patientData && (this.patientData.patient_code || this.patientData.code)) || (this.user && this.user.patient_code) || '';
      const referredFacilityId = this.appointmentData.facility_id || this.formData.referred_to || '';
      const referredDepartmentId = this.formData.department || this.appointmentData.departmentId || '';
      const referredMd = this.appointmentData.action_md || this.appointmentData.actionMd || 0;
      const referringMd = this.user.id || 0;
      const reasonReferral = -1;
      const referringFacility = (this.user && this.user.facility_id) || 0;
      const configId = parseInt(this.appointmentData.opdCategory) || 0;


      formData.append('reason_referral1', reasonReferral);
      formData.append('patient_id', patientId);
      formData.append('patient_code', patientCode);
      formData.append('patient_sex', this.formData.sex || '');
      formData.append('referring_md', referringMd);
      formData.append('referring_facility', referringFacility);
      formData.append('referred_facility', referredFacilityId);
      formData.append('referred_department', referredDepartmentId);
      formData.append('reffered_md', referredMd);
      formData.append('other_reason_referral', 'Telemedicine Patient Booked Appointment');
      formData.append('other_diagnosis', 'For Patient Consultation');
      formData.append('appointmentId', this.appointmentData.appointmentId || '');
      formData.append('telemedicine', 1);
      formData.append('configId', configId);
      formData.append('opdSubId', configId || 0);
      
      // console.log('Form data to submit:', this.patientData);
      // return;
      // Append files
      this.uploadedFiles.forEach(file => {
        formData.append('file_upload[]', file.file);
      });
      
      // Submit to backend
      const baseUrl = (typeof $ !== 'undefined' && $("#broadcasting_url").length)
        ? $("#broadcasting_url").val()
        : window.location.origin;
      const url = `${baseUrl}/api/doctor/refer/normal`;

      this.isSubmitting = true;

      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: (response) => {
          window.location.href = `${baseUrl}/doctor/referred?filterRef=1`;
        },
        error: (error) => {
          this.isSubmitting = false;
          console.error('Referral submit error:', error);
          Lobibox.notify('error', {
            msg: 'Failed to submit referral. Please try again.'
          });
        }
      });
    },

    closeModal() {
      $('#telemedicineFormModal').modal('hide');
    },

    onLogoError(event) {
      // Hide the image if it fails to load
      event.target.style.display = 'none';
    },

    triggerFileInput() {
      this.$refs.fileInput.click();
    },

    handleFileUpload(event) {
      const files = Array.from(event.target.files);
      
      files.forEach(file => {
        const reader = new FileReader();
        
        reader.onload = (e) => {
          const fileObj = {
            file: file,
            name: file.name,
            preview: this.getFilePreview(file, e.target.result)
          };
          
          this.uploadedFiles.push(fileObj);
        };
        
        reader.readAsDataURL(file);
      });
    },

    truncateFileName(fileName) {
      const maxLength = 12;
      return fileName.length > maxLength ? 
        fileName.substring(0, maxLength) + '...' : 
        fileName;
    },

    getFilePreview(file, dataUrl) {
      if (file.type.startsWith('image/')) {
        return dataUrl;
      }
      return this.getIconSrc(file.type);
    },

    getIconSrc(fileType) {
      const baseUrl = (typeof $ !== 'undefined' && $("#broadcasting_url").length)
        ? $("#broadcasting_url").val()
        : window.location.origin;

      if (fileType === 'application/pdf') {
        return `${baseUrl}/resources/img/pdf_icon.png`;
      } else if (fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        return `${baseUrl}/resources/img/document_icon.png`;
      } else if (fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        return `${baseUrl}/resources/img/sheet_icon.png`;
      } else {
        return `${baseUrl}/resources/img/file_icon.png`;
      }
    },

    removeFile(index) {
      this.uploadedFiles.splice(index, 1);
    },

    removeAllFiles() {
      this.uploadedFiles = [];
    }
  }
};
</script>

<style scoped>
.input-sm {
  height: 30px;
  font-size: 12px;
  padding: 5px 10px;
}

label {
  display: block;
  margin-bottom: 3px;
}

.file-upload-box {
  width: 120px;
  height: 120px;
  border: 2px dashed #ccc;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  cursor: pointer;
  position: relative;
  background-color: #f9f9f9;
  transition: all 0.3s;
}

.file-upload-box:hover {
  border-color: #28a745;
  background-color: #f0f9f4;
}

.file-upload-box.uploaded {
  border-style: solid;
  border-color: #28a745;
  background-color: #fff;
}

.modal-body {
  max-height: 100vh;
  overflow-y: auto;
}

.table {
  font-size: 12px;
}

.form-control:focus {
  border-color: #17a2b8;
  box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
}

.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 3000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.loading-card {
  width: 100%;
  max-width: 360px;
  background: #fff;
  border-radius: 8px;
  padding: 22px 20px;
  text-align: center;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
}

.loading-spinner {
  width: 44px;
  height: 44px;
  margin: 0 auto 12px;
  border: 4px solid #d9f0e3;
  border-top-color: #28a745;
  border-radius: 50%;
  animation: spin 0.9s linear infinite;
}

.loading-text {
  font-size: 14px;
  color: #2f2f2f;
  font-weight: 600;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
