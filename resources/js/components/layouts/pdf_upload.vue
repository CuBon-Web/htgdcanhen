<template>
  <div class="form-group">
    <div class="text-center">
      <form @change="submitForm" enctype="multipart/form-data">
        <div class="input-group">
          <div class="custom-file">
            <input
              type="file"
              name="filename"
              class="custom-file-input"
              id="inputFileUpload"
              accept=".pdf,.doc,.docx"
              @change="onFileChange"
            />
            <label class="custom-file-label" for="inputFileUpload">
              Chọn File
            </label>
          </div>
        </div>
        <p v-if="selectedFileName" class="text-success font-weight-bold mb-2">
          Đã chọn: {{ selectedFileName }}
        </p>
        <p v-if="currentFileName" class="text-info font-weight-bold mb-0">
          File hiện tại:
          <a
            :href="'/upload/audio/' + value"
            target="_blank"
            rel="noopener noreferrer"
          >
            {{ currentFileName }}
          </a>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
  props: {
    value: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      selectedFileName: "",
      file: null,
      success: "",
    };
  },
  computed: {
    currentFileName() {
      if (!this.value) return "";
      const sanitized = this.value.split("?")[0];
      return sanitized.substring(sanitized.lastIndexOf("/") + 1);
    },
  },
  methods: {
    ...mapActions(["loadings"]),
    onFileChange(e) {
      const selected = e.target.files[0];
      if (!selected) return;
      this.selectedFileName = selected.name;
      this.file = selected;
    },
    submitForm(e) {
      if (!this.file) return;

      this.loadings(true);
      const allowedTypes = [
        "application/pdf",
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
      ];

      if (!allowedTypes.includes(this.file.type)) {
        this.loadings(false);
        this.$message(
          "Bạn nên tải các file Docs có định dạng PDF, DOC, DOCX",
          "error"
        );
        return;
      }

      e.preventDefault();
      const currentObj = this;
      const config = {
        headers: {
          "content-type": "multipart/form-data",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
      };

      const formData = new FormData();
      formData.append("file", this.file);

      axios
        .post("/api/store_file", formData, config)
        .then(function (response) {
          currentObj.success = response.data.success;
          currentObj.$emit("increase-vu", response.data.path.title);
          currentObj.selectedFileName = "";
          currentObj.file = null;
          currentObj.loadings(false);
        })
        .catch(function () {
          currentObj.loadings(false);
        });
    },
  },
};
</script>
