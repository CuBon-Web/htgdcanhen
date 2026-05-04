<template>
  <div>
    <div v-for="(task, taskIndex) in tasks" :key="taskIndex" class="task-block" draggable="true"
      @dragstart="onTaskDragStart(taskIndex)" @dragover.prevent @drop="onTaskDrop(taskIndex)">
      <div class="form-group">
        <label><b>{{ taskIndex + 1 }}</b>: {{ task.chuong }} <i class="bx bxs-trash text-danger"
            @click="removeTask(taskIndex)" style="cursor: pointer;"></i></label>
        <input type="text" v-model="task.chuong" class="form-control" placeholder="Nhập tên chương" />
      </div>

      <div class="accordion" id="accordionExample">

        <div class="accordion-item detail-task-block" v-for="(detail, detailIndex) in task.detail_task"
          :key="detailIndex" draggable="true" @dragstart="onDetailDragStart(taskIndex, detailIndex)" @dragover.prevent
          @drop="onDetailDrop(taskIndex, detailIndex)">
          <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              :data-bs-target="'#collapseTwo_' + detailIndex + taskIndex" aria-expanded="false"
              :aria-controls="'#collapseTwo_' + detailIndex + taskIndex">
              {{ detail.name }} <i class="bx bxs-trash" v-if="task.detail_task.length > 1"
                @click="removeDetailTask(taskIndex, detailIndex)"></i>
            </button>
          </h2>
          <div :id="'collapseTwo_' + detailIndex + taskIndex" class="accordion-collapse collapse"
            aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
            <div class="accordion-body">
              <!-- Overlay loading khi đang upload video -->

              <div class="row" :class="{ 'disabled-content': detail.isUploadingVideo }">
                <div class="col-xl-6 col-lg-6">
                  <div class="form-group">
                    <label>Tiêu đề bài học:</label>
                    <input type="text" v-model="detail.name" class="form-control" placeholder="Tên bài học"
                      :disabled="detail.isUploadingVideo" />
                  </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                  <div class="form-group">
                    <label>Chọn bài test:</label>
                    <select v-model="detail.test_id" class="form-control" :disabled="detail.isUploadingVideo">
                      <option value="0">--Chọn--</option>
                      <option :value="index.id" v-for="(index, it) in myProp">{{ index.title }}</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row" :class="{ 'disabled-content': detail.isUploadingVideo }">
                <div class="col-xl-6 col-lg-6">
                  <div class="form-group">
                    <label>Thời lượng video:</label>
                    <input type="text" v-model="detail.time" class="form-control" placeholder="00:00" readonly />
                  </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                  <div class="form-group">
                    <label>Trạng thái:</label>
                    <select v-model="detail.status" class="form-control" :disabled="detail.isUploadingVideo">
                      <option value="1">Mất Phí</option>
                      <option value="0">Học thử</option>
                    </select>
                  </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                  <div class="form-group">
                    <label>Upload Video:</label>
                    <p class="descs">Lưu ý: Video upload thành công chỉ khi hiển thị nội dung "Hoàn tất upload"
                    </p>
                    <input type="file" @change="uploadFile($event, taskIndex, detailIndex, 'video')"
                      class="form-control" accept="video/*" :disabled="detail.isUploadingVideo" />

                    <!-- Spinner khi đang upload -->
                    <div v-if="detail.isUploadingVideo" class="upload-spinner mt-2">
                      <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                          <span class="visually-hidden">Đang upload...</span>
                        </div>
                        <span class="text-primary">Đang upload video...</span>
                      </div>
                    </div>

                    <div v-if="detail.uploadProgress.video > 0 && detail.uploadProgress.video < 100"
                      class="progress mt-2">
                      <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar"
                        :style="{ width: detail.uploadProgress.video + '%' }">
                        {{ detail.uploadProgress.video }}%
                      </div>
                    </div>
                    <small v-if="detail.video && detail.uploadProgress.video === 100" class="text-success d-block mt-1">
                      Hoàn tất upload video - <a :href="detail.video" target="_blank">Xem Video</a>
                    </small>
                  </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                  <div class="form-group">
                    <label>Upload Tài liệu:</label>
                    <p class="descs">Lưu ý: Tài liệu upload thành công chỉ khi hiển thị nội dung "Hoàn tất
                      upload"</p>
                    <input type="file" @change="uploadFile($event, taskIndex, detailIndex, 'document')"
                      class="form-control" accept=".pdf,.doc,.docx" :disabled="detail.isUploadingVideo" />
                    <div v-if="detail.uploadProgress.document > 0 && detail.uploadProgress.document < 100"
                      class="progress mt-2">
                      <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                        :style="{ width: detail.uploadProgress.document + '%' }">
                        {{ detail.uploadProgress.document }}%
                      </div>
                    </div>
                    <small v-if="detail.document && detail.uploadProgress.document === 100"
                      class="text-success d-block mt-1">
                      Hoàn tất: {{ getFileName(detail.document) }}
                    </small>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12" style="    text-align: left;">
          <button type="button" class="btn btn-secondary plusbaihoc" @click="addDetailTask(taskIndex)">
            <i class="bx bxs-plus-circle"></i> Thêm bài học
          </button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12" style="    text-align: left;">
        <button type="button" class="btn btn-secondary plusbaihoc" @click="addTask">
          <i class="bx bxs-plus-circle"></i> Thêm chương mới
        </button>
      </div>
    </div>

    <input type="hidden" name="tasks_json" id="tasks_json" :value="JSON.stringify(tasks)" />
  </div>
</template>

<script>
import * as tus from 'tus-js-client';
export default {
  props: {
    initialTasks: {
      type: Array,
      default: () => ([]),
    },
    myProp: {
      type: Array, // Tuỳ theo kiểu dữ liệu
      default: () => (null),
    },
  },
  data() {
    return {
      tasks: this.initialTasks.length ? this.initialTasks : [
        {
          chuong: "",
          time: "",
          detail_task: [
            {
              name: "Bài học mới",
              time: "",
              icon: "",
              video: "",
              document: "",
              status: 1,
              test_id: 0,
              uploadProgress: {
                video: 0,
                document: 0,
              },
              isUploadingVideo: false,
            },
          ],
        },
      ],
    };
  },
  watch: {
    tasks: {
      handler(newVal) {
        document.getElementById("tasks_json").value = JSON.stringify(newVal);
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    addTask() {
      this.tasks.push({
        chuong: "",
        time: "",
        detail_task: [
          {
            name: "Bài học mới",
            time: "",
            icon: "",
            video: "",
            document: "",
            status: 1,
            test_id: 0,
            uploadProgress: {
              video: 0,
              document: 0,
            },
            isUploadingVideo: false,
          },
        ],
      });
    },
    addDetailTask(taskIndex) {
      this.tasks[taskIndex].detail_task.push({
        name: "Bài học mới",
        time: "",
        icon: "",
        video: "",
        document: "",
        status: 1,
        test_id: 0,
        uploadProgress: {
          video: 0,
          document: 0,
        },
        isUploadingVideo: false,
      });
    },
    removeDetailTask(taskIndex, detailIndex) {
      if (confirm('Bạn có chắc chắn muốn xóa bài học này không?')) {
        this.tasks[taskIndex].detail_task.splice(detailIndex, 1);
      }
    },
    async uploadFile(event, taskIndex, detailIndex, type) {
      const file = event.target.files[0];
      if (!file) return;

      const detail = this.tasks[taskIndex].detail_task[detailIndex];

      // Xử lý video
      if (type === "video") {
        if (!file.type.startsWith('video/')) {
          alert('Vui lòng chọn file video hợp lệ');
          return;
        }

        detail.isUploadingVideo = true;
        detail.uploadProgress.video = 0;

        try {
          const duration = await this.getVideoDuration(file);
          detail.time = this.formatDuration(duration);
        } catch (err) {
          detail.time = "";
        }
      }

      // Chuẩn bị form gửi lên server (để lấy upload URL nếu là video)
      const formData = new FormData();
      formData.append("type", type);

      if (type === "video") {
        formData.append("filename", file.name);
        formData.append("filesize", file.size);
        formData.append("filetype", file.type);
      } else {
        formData.append("file", file);
      }

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "/api/uploadcourse", true);
      const csrfEl = document.querySelector('meta[name="csrf-token"]');
      if (csrfEl && csrfEl.content) {
        xhr.setRequestHeader("X-CSRF-TOKEN", csrfEl.content);
      }

      // Theo dõi tiến trình upload tài liệu
      if (type === "document") {
        xhr.upload.onprogress = (event) => {
          if (event.lengthComputable) {
            const percent = Math.round((event.loaded / event.total) * 100);
            detail.uploadProgress.document = percent;
          }
        };
      }

      // Khi upload lên server hoàn tất
      xhr.onload = async () => {
        let result;
        try {
          result = JSON.parse(xhr.responseText || '{}');
        } catch (e) {
          alert("Lỗi xử lý phản hồi từ server.");
          if (type === "video") {
            detail.isUploadingVideo = false;
            detail.uploadProgress.video = 0;
          }
          return;
        }

        if (xhr.status === 200) {
          if (type === "video" && result.uid && result.uploadURL) {
            try {
              const method = result.uploadMethod || (file.size > 200 * 1024 * 1024 ? 'tus' : 'direct');

              if (method === 'tus') {
                const upload = new tus.Upload(file, {
                  uploadUrl: result.uploadURL,
                  chunkSize: 5 * 1024 * 1024,
                  retryDelays: [0, 1000, 3000, 5000],
                  storeFingerprintForResuming: false,
                  removeFingerprintOnSuccess: true,
                  metadata: {
                    filename: file.name,
                    filetype: file.type
                  },
                  onProgress: (bytesUploaded, bytesTotal) => {
                    const percent = Math.floor((bytesUploaded / bytesTotal) * 100);
                    detail.uploadProgress.video = percent;
                  },
                  onSuccess: () => {
                    detail.video = `https://iframe.videodelivery.net/${result.uid}`;
                    detail.uploadProgress.video = 100;
                    detail.isUploadingVideo = false;
                  },
                  onError: (error) => {
                    alert("Upload thất bại: " + (error?.message || 'TUS error'));
                    detail.uploadProgress.video = 0;
                    detail.isUploadingVideo = false;
                  }
                });
                upload.start();
              } else {
                const directRes = await this.uploadToCloudflareDirect(result.uploadURL, file, detail);
                if (directRes.success) {
                  detail.video = `https://iframe.videodelivery.net/${result.uid}`;
                  detail.uploadProgress.video = 100;
                } else {
                  throw new Error(directRes.error || 'Direct upload thất bại');
                }
              }
            } catch (error) {
              alert('Upload video thất bại: ' + error.message);
              detail.uploadProgress.video = 0;
              detail.isUploadingVideo = false;
            }

          } else if (type === "document" && result.url) {
            detail.document = result.url;
            detail.uploadProgress.document = 100;
          } else {
            alert("Upload thất bại.");
          }

        } else if (xhr.status === 413) {
          alert("File quá lớn. Vui lòng chọn file nhỏ hơn 500MB.");
        } else {
          alert("Upload lỗi.");
        }

        if (type === "video") {
          detail.isUploadingVideo = false;
        }
      };

      xhr.onerror = () => {
        alert("Lỗi khi upload.");
        if (type === "video") {
          detail.isUploadingVideo = false;
          detail.uploadProgress.video = 0;
        }
      };

      xhr.send(formData);
    },

    // Direct upload helper (giữ cho video nhỏ <= 200MB)
    async uploadToCloudflareDirect(uploadURL, file, detail) {
      return new Promise((resolve) => {
        const xhr = new XMLHttpRequest();
        xhr.timeout = 300000; // 5 phút

        xhr.upload.onprogress = (event) => {
          if (event.lengthComputable) {
            const percent = Math.round((event.loaded / event.total) * 100);
            detail.uploadProgress.video = percent;
          }
        };

        xhr.onload = () => {
          if (xhr.status === 200) {
            resolve({ success: true });
          } else if (xhr.status === 413) {
            resolve({ success: false, error: 'File quá lớn cho direct upload (<200MB)' });
          } else {
            resolve({ success: false, error: `HTTP ${xhr.status} - ${xhr.statusText}` });
          }
        };

        xhr.onerror = () => resolve({ success: false, error: 'Network error khi upload lên Cloudflare' });
        xhr.ontimeout = () => resolve({ success: false, error: 'Upload timeout' });

        const formData = new FormData();
        formData.append('file', file);
        xhr.open('POST', uploadURL);
        // Không tự set Content-Type để trình duyệt tự thêm boundary
        xhr.send(formData);
      });
    },





    getVideoDuration(file) {
      return new Promise((resolve, reject) => {
        const url = URL.createObjectURL(file);
        const video = document.createElement("video");
        video.preload = "metadata";

        video.onloadedmetadata = () => {
          URL.revokeObjectURL(url);
          resolve(video.duration);
        };

        video.onerror = () => {
          reject("Không thể đọc file video.");
        };

        video.src = url;
      });
    },
    formatDuration(seconds) {
      const h = Math.floor(seconds / 3600);
      const m = Math.floor((seconds % 3600) / 60);
      const s = Math.floor(seconds % 60);

      if (h > 0) {
        return `${h}:${m.toString().padStart(2, "0")}:${s.toString().padStart(2, "0")}`;
      } else {
        return `${m}:${s.toString().padStart(2, "0")}`;
      }
    },
    getFileName(url) {
      return url.split("/").pop();
    },
    // === Kéo thả chương ===
    onTaskDragStart(index) {
      this.dragTaskIndex = index;
    },
    onTaskDrop(index) {
      if (this.dragTaskIndex === null || this.dragTaskIndex === index) return;
      const moved = this.tasks.splice(this.dragTaskIndex, 1)[0];
      this.tasks.splice(index, 0, moved);
      this.dragTaskIndex = null;
    },

    // === Kéo thả bài học ===
    onDetailDragStart(taskIndex, detailIndex) {
      this.dragDetail = { taskIndex, detailIndex };
    },
    onDetailDrop(taskIndex, detailIndex) {
      const { taskIndex: fromTask, detailIndex: fromDetail } = this.dragDetail;

      if (fromTask === null || fromDetail === null) return;

      const movedItem = this.tasks[fromTask].detail_task.splice(fromDetail, 1)[0];

      if (fromTask === taskIndex && fromDetail < detailIndex) {
        detailIndex--; // do bị shift vị trí
      }

      this.tasks[taskIndex].detail_task.splice(detailIndex, 0, movedItem);
      this.dragDetail = { taskIndex: null, detailIndex: null };
    },
    removeTask(index) {
      if (confirm('Bạn có chắc chắn muốn xóa chương này không?')) {
        this.tasks.splice(index, 1);
      }
    },
  },
  mounted() {
    console.log(this.myProp);
  },
};
</script>

<style scoped>
.task-block {
  margin-bottom: 20px;
  padding: 10px;
  background: #f8faff;
  border-radius: 5px;
  border: 1px solid #94b0f3;
}

.detail-task-block {
  margin-bottom: 15px;
  border-radius: 6px;
  background-color: #fff;
  position: relative;
}

.progress {
  height: 22px;
}

.progress-bar {
  font-size: 14px;
  line-height: 22px;
}

/* Overlay loading styles */
.upload-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  border-radius: 6px;
}

.spinner-container {
  text-align: center;
}

.spinner-container p {
  margin: 0;
  color: #6c757d;
  font-size: 14px;
}

/* Disabled content styles */
.disabled-content {
  opacity: 0.6;
  pointer-events: none;
}

.disabled-content input,
.disabled-content select {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

/* Upload spinner styles */
.upload-spinner {
  padding: 10px;
  background: #f8f9fa;
  border-radius: 4px;
  border: 1px solid #dee2e6;
}

.form-group {
  text-align: left;
  margin-bottom: 15px;
  font-size: 14px;
}

.form-group label {
  font-size: 15px;
}

button.btn.btn-secondary.plusbaihoc {
  margin: 0;
  border: 1px solid #6d28d2;
  color: #6d28d2;
  background: white;
  font-size: 14px;
  padding: 8px 14px
}

select.form-control {
  margin: 5px 0;
}

.descs {
  font-size: 12px;
  font-style: italic;
  color: green;
  line-height: normal;
}
</style>
