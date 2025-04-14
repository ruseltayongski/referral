<template>
  <div v-if="visible" class="custom-modal-backdrop">
    <div class="modal-dialog modal-lg custom-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Document Viewer</h5>
          <button type="button" class="close" @click="closeModal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="embed-responsive embed-responsive-1by1">
            <iframe v-if="pdfUrl" :src="pdfUrl" class="embed-responsive-item" style="height: 75vh;"></iframe>
            <div v-else class="text-center p-5">
              <p>No document to display</p>
            </div>
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
      visible: false
    };
  },
  methods: {
    openModal() {
      this.visible = true;
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
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.custom-modal {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  width: 90%;
  max-width: 800px;
}
</style>
