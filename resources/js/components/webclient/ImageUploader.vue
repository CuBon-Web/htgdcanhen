<template>
  <div>
    <input
      class="form-control"
      type="file"
      accept="image/*"
      @change="uploadToServer"
    />

    <div v-if="uploading" class="mt-2">
      <div class="progress">
        <div
          class="progress-bar progress-bar-striped progress-bar-animated"
          role="progressbar"
          :style="{ width: uploadProgress + '%' }"
        >
          {{ uploadProgress }}%
        </div>
      </div>
    </div>

    <div v-if="previewUrl" class="mt-2 position-relative">
      <img width="50" :src="previewUrl" alt="Preview" class="img-fluid rounded border" />
      <button
        type="button"
        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
        @click="removeImage"
      >
        Xóa
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "ImageUploader",
  props: {
    // Bạn truyền ảnh từ cha nếu muốn, hoặc null
    initialValue: String,
  },
  data() {
    return {
      previewUrl: this.initialValue || null,
      uploading: false,
      uploadProgress: 0,
    };
  },
  watch: {
    initialValue(newVal) {
      this.previewUrl = newVal;
    },
  },
  methods: {
    uploadToServer(event) {
      const file = event.target.files[0];
      if (!file || !file.type.startsWith("image/")) {
        alert("Vui lòng chọn một tệp hình ảnh.");
        return;
      }

      const formData = new FormData();
      formData.append("image", file);

      this.uploading = true;
      this.uploadProgress = 0;

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "/api/upload-image-khoahoc", true);

      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          this.uploadProgress = Math.round((e.loaded / e.total) * 100);
        }
      };

      xhr.onload = () => {
        this.uploading = false;
        try {
          const res = JSON.parse(xhr.responseText);
          if (res.url) {
            this.previewUrl = res.url;
            // Emit event truyền url ảnh ra ngoài component cha
            this.$emit('image-changed', res.url);
          } else {
            throw new Error("Phản hồi không chứa URL");
          }
        } catch (err) {
          console.error(err);
          alert("Tải ảnh thất bại.");
        }
      };

      xhr.onerror = () => {
        this.uploading = false;
        alert("Lỗi khi gửi yêu cầu đến máy chủ.");
      };

      xhr.send(formData);
    },
    removeImage() {
      this.previewUrl = null;
      // Emit event báo xóa ảnh
      this.$emit('image-changed', null);
    },
  },
};
</script>
