<template>
  <div>
    <!-- Phần chọn loại nội dung -->
    <div class="form-group mb-3 mt-3">
      <label class="mb-2">Chọn loại nội dung:</label>
      <select v-model="contentType" class="form-control">
        <option value="test">Bài Test</option>
        <option value="document">Tài Liệu</option>
      </select>
    </div>

    <!-- Nếu là Bài Test -->
    <div v-if="contentType === 'test'">

      <div v-for="(task, taskIndex) in tasks" :key="taskIndex" class="task-block" draggable="true"
        @dragstart="onTaskDragStart(taskIndex)" @dragover.prevent @drop="onTaskDrop(taskIndex)">
        <div class="form-group">
          <label><b>Câu hỏi {{ taskIndex + 1 }}</b>:
            <i class="fas fa-trash text-danger" @click="removeTask(taskIndex)" style="cursor: pointer;"></i>
          </label>
        </div>

        <div class="form-group">
          <TinyMce v-model="task.title" />
        </div>

        <div class="form-group">
          <label>Loại câu hỏi:</label>
          <select v-model="task.type" class="form-control">
            <option value="0">Tự Luận</option>
            <option value="1">Trắc Nghiệm</option>
          </select>
        </div>

        <!-- Nếu là Trắc nghiệm -->
        <div class="accordion" id="accordionExample" v-if="task.type == 1">
          <div class="accordion-item detail-task-block" v-for="(detail, detailIndex) in task.multiple_choice"
            :key="detailIndex">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                :data-bs-target="'#collapseTwo_' + detailIndex + taskIndex" aria-expanded="false"
                :aria-controls="'collapseTwo_' + detailIndex + taskIndex">
                {{ detail.name }}
              </button>
            </h2>
            <div :id="'collapseTwo_' + detailIndex + taskIndex" class="accordion-collapse collapse"
              aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="form-group">
                  <label>Nội dung {{ detail.name }}:</label>
                  <textarea v-model="detail.title" class="form-control" placeholder="Nhập nội dung"></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Đáp án đúng:</label>
            <select v-model="task.right_choice" class="form-control">
              <option value="0">--Chọn--</option>
              <option value="1">Đáp Án A</option>
              <option value="2">Đáp Án B</option>
              <option value="3">Đáp Án C</option>
              <option value="4">Đáp Án D</option>
            </select>
          </div>
        </div>

        <hr />
      </div>

      <!-- Nút thêm câu hỏi -->
      <div class="form-group">
        <button type="button" class="btn btn-success" @click="addTask">Thêm câu hỏi</button>
      </div>
    </div>

    <!-- Nếu là Tài liệu -->
    <div v-else-if="contentType === 'document'">
      <!-- <div class="form-group">
        <label>Chọn file tài liệu:</label>
        <input type="file" @change="onFileChange" class="form-control" name="document_file">
      </div> -->

      <div class="form-group">
        <label>Upload Tài liệu:</label>
        <p class="descs">Lưu ý: Tài liệu upload thành công chỉ khi hiển thị nội dung "Hoàn tất upload"</p>
        <input type="file" @change="uploadFile($event, null, null, 'document')" class="form-control"
          accept=".pdf,.doc,.docx" />

        <div v-if="uploadProgress.document > 0 && uploadProgress.document < 100" class="progress mt-2">
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
            :style="{ width: uploadProgress.document + '%' }">
            {{ uploadProgress.document }}%
          </div>
        </div>

        <small v-if="documentFile && uploadProgress.document === 100" class="text-success d-block mt-1">
          Hoàn tất: {{ getFileName(documentFile) }}
        </small>
      </div>
    </div>

    <!-- Input ẩn để đẩy toàn bộ data sang blade -->
    <input type="hidden" name="tasks_json" id="tasks_json" :value="JSON.stringify(compiledData)" />
  </div>
</template>

<script>
import TinyMce from "../_common/tinymceclient";

export default {
  components: {
    TinyMce,
  },
  props: {
    initialTasks: {
      type: Array,
      default: () => ([]),
    },
    initialDocument: {
      type: [String, null], // có thể là chuỗi hoặc null
      default: null,
    },
    initialType: {
      type: [Number, String], // có thể là số hoặc chuỗi
      default: 0,
    },

  },
  data() {
    return {
      contentType: this.initialType ? this.initialType : 'document', // Mặc định là document
      uploadProgress: {
        document: 0,
      },
      tasks: this.initialTasks.length ? this.initialTasks : [this.defaultTask()],
      documentFile: this.initialDocument ? this.initialDocument : null,
      dragTaskIndex: null,
    };
  },
  computed: {
    compiledData() {
      return {
        contentType: this.contentType,
        tasks: this.tasks,
        documentFile: this.documentFile,
      };
    },
  },
  watch: {
    compiledData: {
      handler() {
        this.updateHiddenInput();
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    uploadFile(event, taskIndex, detailIndex, type) {
      const file = event.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append("file", file);
      formData.append("type", type);

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "/api/uploadcourse", true);
      xhr.setRequestHeader(
        "X-CSRF-TOKEN",
        document.querySelector('meta[name="csrf-token"]').getAttribute("content")
      );

      xhr.upload.onprogress = (event) => {
        if (event.lengthComputable) {
          const percent = Math.round((event.loaded / event.total) * 100);
          this.uploadProgress[type] = percent;
        }
      };

      xhr.onload = () => {
        try {
          const result = JSON.parse(xhr.responseText);
          if (xhr.status === 200 && result.url) {
            if (type === "document") {
              this.documentFile = result.url;
            }
            this.uploadProgress[type] = 100;
          } else {
            alert("Upload thất bại: " + (result.message || "Không rõ lỗi."));
            this.uploadProgress[type] = 0;
          }
        } catch (e) {
          alert("Lỗi khi phân tích phản hồi.");
          this.uploadProgress[type] = 0;
        }
      };

      xhr.onerror = () => {
        alert("Lỗi khi upload.");
        this.uploadProgress[type] = 0;
      };

      xhr.send(formData);
    },

    defaultTask() {
      return {
        title: "",
        image: "",
        type: 0,
        giai_thich: "",
        right_choice: 0,
        multiple_choice: [
          { name: "Đáp Án A", title: "", index: 1 },
          { name: "Đáp Án B", title: "", index: 2 },
          { name: "Đáp Án C", title: "", index: 3 },
          { name: "Đáp Án D", title: "", index: 4 },
        ],
      };
    },
    updateHiddenInput() {
      const hiddenInput = document.getElementById("tasks_json");
      if (hiddenInput) {
        hiddenInput.value = JSON.stringify(this.compiledData);
      }
    },
    addTask() {
      this.tasks.push(this.defaultTask());
    },
    removeTask(index) {
      if (confirm("Bạn có chắc chắn muốn xóa câu hỏi này không?")) {
        this.tasks.splice(index, 1);
      }
    },
    getFileName(url) {
      return url.split("/").pop().split("?")[0];
    },
    // Kéo thả
    onTaskDragStart(index) {
      this.dragTaskIndex = index;
    },
    onTaskDrop(index) {
      if (this.dragTaskIndex === null || this.dragTaskIndex === index) return;
      const moved = this.tasks.splice(this.dragTaskIndex, 1)[0];
      this.tasks.splice(index, 0, moved);
      this.dragTaskIndex = null;
    },
  },
  mounted() {
    // Nếu có file ban đầu, đặt uploadProgress = 100
    if (this.documentFile) {
      this.uploadProgress.document = 100;
    }
  },
};
</script>

<style scoped>
.detail-task-block {
  border: 1px solid #ddd;
  margin-bottom: 15px;
  border-radius: 6px;
  background-color: #fff;
}

.progress {
  height: 22px;
}

.progress-bar {
  font-size: 14px;
  line-height: 22px;
}

.accordion-button {
  border: 1px solid black;
  margin-top: 0;
  padding: 10px 17px;
  font-size: 14px;
}

.accordion-button i {
  font-size: 12px;
  padding: 0 6px;
  color: rgb(199, 9, 9);
}

.form-group {
  text-align: left;
  margin-bottom: 15px;
  font-size: 14px;
}

.form-group label {
  font-size: 15px;
}

.accordion-body {
  border: 1px solid black;
  border-top: none;
}

button.btn.btn-secondary.plusbaihoc {
  margin: 0;
  border: 1px solid #6d28d2;
  color: #6d28d2;
  background: white;
  font-size: 14px;
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
