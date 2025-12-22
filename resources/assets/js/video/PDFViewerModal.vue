<template>
  <div v-if="visible" class="custom-modal-backdrop">
    <div class="custom-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"> {{ modalTitle }}</h5>
          <button type="button" class="close" @click="closeModal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <iframe
            v-if="pdfUrl"
            :src="pdfUrl"
            class="responsive-iframe"
            allowfullscreen
          ></iframe>
          <div v-else class="text-center p-5">
            <p>No document to display</p>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
          <a v-if="pdfUrl" :href="pdfUrl" target="_blank" class="btn btn-primary">Open in New Tab</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PDFViewerModal',
  props: {
    pdfUrl: String
  },
  data() {
    return {
      visible: false,
      modalTitle: 'PDF VIEWER',
    };
  },
  methods: {
    openModal() {
      this.visible = true;

      if(this.pdfUrl.includes("prescription")){
        this.modalTitle = "PRESCRIPTION";
      }else {
        this.modalTitle = "LAB REQUEST";
      }
    },
    closeModal() {
      this.visible = false;
      this.$emit('modalClosed');
    }
  }
};
</script>

<style scoped>
.custom-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  z-index: 1050;
  box-sizing: border-box;
}

.custom-modal {
  width: 100%;
  max-width: 800px;
  height: 90vh;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal-content {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.modal-header,
.modal-footer {
  flex-shrink: 0;
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
}

.modal-footer {
  border-top: 1px solid #dee2e6;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

.modal-body {
  flex: 1;
  padding: 0;
}

/* Fixed height for iframe, takes full modal body */
.responsive-iframe {
  width: 100%;
  height: 100%;
  border: none;
}

/* Responsive tweaks */
@media (max-width: 768px) {
  .custom-modal {
    max-width: 100%;
    height: 95vh;
  }
}
</style>
