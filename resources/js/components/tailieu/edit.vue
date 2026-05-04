<template>
  <div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <!-- Tên tài liệu -->
            <div class="form-group">
              <label>Tên tài liệu</label>
              <vs-input
                type="text"
                size="default"
                placeholder="Tên danh mục"
                class="w-100"
                v-model="objData.name"
              />
            </div>

            <!-- Mô tả ngắn -->
            <div class="form-group">
              <label>Mô tả ngắn</label>
              <vs-input
                type="text"
                size="default"
                placeholder="Mô tả ngắn"
                class="w-100"
                v-model="objData.description"
              />
            </div>

            <!-- Danh mục -->
            <div class="form-group">
              <label>Danh mục</label>
              <vs-select
                class="selectExample"
                v-model="objData.cate_id"
                placeholder="Danh mục"
              >
                <vs-select-item value="0" text="Chọn danh mục" />
                <vs-select-item
                  v-for="(item, index) in cate"
                  :key="index"
                  :value="item.id"
                  :text="item.name"
                />
              </vs-select>
            </div>

            <!-- Ảnh đại diện -->
            <div class="form-group">
              <label>Ảnh đại diện</label>
              <image-upload
                v-model="objData.image"
                type="avatar"
                :title="'Ảnh đại diện'"
              ></image-upload>
            </div>

            <!-- Chọn loại nội dung -->
            <div class="form-group mb-3 mt-3">
              <label class="mb-2">Chọn loại nội dung:</label>
              <select v-model="objData.contentType" class="form-control">
                <option value="test">Bài Test</option>
                <option value="document">Tài Liệu</option>
              </select>
            </div>

            <!-- Nếu là Bài Test -->
            <div v-if="objData.contentType === 'test'">
              <div
                v-for="(task, taskIndex) in objData.tasks"
                :key="taskIndex"
                class="task-block"
                draggable="true"
                @dragstart="onTaskDragStart(taskIndex)"
                @dragover.prevent
                @drop="onTaskDrop(taskIndex)"
              >
                <div class="form-group">
                  <label>
                    <b>Câu hỏi {{ taskIndex + 1 }}</b>:
                    <i
                      class="fas fa-trash text-danger"
                      @click="removeTask(taskIndex)"
                      style="cursor: pointer;"
                    ></i>
                  </label>
                </div>

                <div class="form-group">
                  <TinyMce v-model="task.title" />
                </div>

                <div class="form-group">
                  <label>Loại câu hỏi:</label>
                  <select v-model="task.type" class="form-control">
                    <option value="0">Tự Luận</option>
                    <option value="1">Trắc Nghiệm</option>
                  </select>
                </div>

                <!-- Nếu là Trắc Nghiệm -->
                <div
                  class="accordion"
                  v-if="task.type == 1"
                >
                  <div
                    v-for="(detail, detailIndex) in task.multiple_choice"
                    :key="detailIndex"
                    class="accordion-item detail-task-block"
                  >
                    <h2 class="accordion-header">
                      <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        :data-bs-target="'#collapse_' + taskIndex + '_' + detailIndex"
                        aria-expanded="false"
                      >
                        {{ detail.name }}
                      </button>
                    </h2>
                    <div
                      :id="'collapse_' + taskIndex + '_' + detailIndex"
                      class="accordion-collapse collapse"
                    >
                      <div class="accordion-body">
                        <div class="form-group">
                          <label>Nội dung {{ detail.name }}:</label>
                          <textarea
                            v-model="detail.title"
                            class="form-control"
                            placeholder="Nhập nội dung"
                          ></textarea>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Đáp án đúng:</label>
                    <select v-model="task.right_choice" class="form-control">
                      <option value="0">--Chọn--</option>
                      <option value="1">Đáp án A</option>
                      <option value="2">Đáp án B</option>
                      <option value="3">Đáp án C</option>
                      <option value="4">Đáp án D</option>
                    </select>
                  </div>
                </div>
                <hr />
              </div>

              <div class="form-group">
                <button
                  type="button"
                  class="btn btn-success"
                  @click="addTask"
                >
                  Thêm câu hỏi
                </button>
              </div>
            </div>

            <!-- Nếu là Tài liệu -->
            <div v-else-if="objData.contentType === 'document'">
              <div class="form-group">
                <label>Upload Tài liệu:</label>
                <p class="descs">Lưu ý: Tài liệu chỉ thành công khi hiển thị "Hoàn tất upload"</p>
                <input
                  type="file"
                  @change="uploadFile($event, null, null, 'document')"
                  class="form-control"
                  accept=".pdf,.doc,.docx"
                />
                <div
                  v-if="uploadProgress.document > 0 && uploadProgress.document < 100"
                  class="progress mt-2"
                >
                  <div
                    class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                    :style="{ width: uploadProgress.document + '%' }"
                  >
                    {{ uploadProgress.document }}%
                  </div>
                </div>
                <small
                  v-if="objData.documentFile && uploadProgress.document === 100"
                  class="text-success d-block mt-1"
                >
                  Hoàn tất: {{ getFileName(objData.documentFile) }}
                </small>
                <small
                  v-if="objData.documentFile"
                  class="text-success d-block mt-1"
                >
                  File hiện tại: {{ getFileName(objData.documentFile) }}
                </small>
              </div>
            </div>

            <!-- Trạng thái -->
            <div class="form-group">
              <label>Trạng thái</label>
              <vs-select v-model="objData.status">
                <vs-select-item value="1" text="Hiện" />
                <vs-select-item value="0" text="Ẩn" />
              </vs-select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Nút lưu -->
    <div class="row fixxed">
      <div class="col-12">
        <div class="saveButton">
          <vs-button color="primary" @click="saveCourseDocumentLists">
            Thêm mới
          </vs-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";

export default {
  name: "DocumentCate",
  components: { TinyMce },
  data() {
    return {
      errors: [],
      cate: [],
      objData: {
        name: "",
        image: "",
        description: "",
        cate_id: 0,
        contentType: "document",
        tasks: [this.defaultTask()],
        documentFile: null,
        status:1,
      },
      uploadProgress: { document: 0 },
      dragTaskIndex: null,
    };
  },
  methods: {
    ...mapActions([
      "saveDocumentList",
      "loadings",
      "listCourseDocumentList",
      "getInfoDocumentList",
    ]),

    saveCourseDocumentLists() {
      this.errors = [];
      if (!this.objData.name) this.errors.push("Tên không được để trống");
      if (!this.objData.description) this.errors.push("Mô tả không được để trống");

      if (this.errors.length) {
        this.errors.forEach((err) => this.$error(err));
        return;
      }

      this.loadings(true);
      this.saveDocumentList(this.objData)
        .then(() => {
          this.loadings(false);
          this.$router.push({ name: "listDocument" });
          this.$success("Thêm thành công");
        })
        .catch(() => {
          this.loadings(false);
          this.$error("Thêm thất bại");
        });
    },

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

      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          this.uploadProgress[type] = Math.round((e.loaded / e.total) * 100);
        }
      };

      xhr.onload = () => {
        try {
          const res = JSON.parse(xhr.responseText);
          if (xhr.status === 200 && res.url) {
            if (type === "document") this.objData.documentFile = res.url;
            this.uploadProgress[type] = 100;
          } else {
            alert("Upload thất bại: " + (res.message || "Không rõ lỗi."));
            this.uploadProgress[type] = 0;
          }
        } catch {
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
          { name: "Đáp Án A", title: "", index: 1 },
          { name: "Đáp Án B", title: "", index: 2 },
          { name: "Đáp Án C", title: "", index: 3 },
          { name: "Đáp Án D", title: "", index: 4 },
        ],
      };
    },

    addTask() {
      this.objData.tasks.push(this.defaultTask());
    },

    removeTask(index) {
      if (confirm("Bạn có chắc chắn muốn xóa câu hỏi này không?")) {
        this.objData.tasks.splice(index, 1);
      }
    },

    getFileName(url) {
      return url.split("/").pop().split("?")[0];
    },

    onTaskDragStart(index) {
      this.dragTaskIndex = index;
    },

    onTaskDrop(index) {
      if (this.dragTaskIndex === null || this.dragTaskIndex === index) return;
      const moved = this.objData.tasks.splice(this.dragTaskIndex, 1)[0];
      this.objData.tasks.splice(index, 0, moved);
      this.dragTaskIndex = null;
    },

    editById() {
      this.loadings(true);
      this.getInfoDocumentList({ id: this.$route.params.id })
        .then((res) => {
          this.loadings(false);
          this.objData = {
                ...this.objData, // Giữ nguyên các key mặc định
                ...res.data, // Ghi đè key từ API
            };
            if (res.data.type === "test" && res.data.test_json) {
                this.objData.tasks = JSON.parse(res.data.test_json);
            } else {
                this.objData.tasks = [this.defaultTask()];
            }
            this.objData.contentType = res.data.type;
            this.objData.documentFile = res.data.file;
        })
        .catch(() => {
          this.loadings(false);
          console.log("Không thể tải dữ liệu chi tiết.");
        });
    },
  },

  mounted() {
    this.editById();
    this.listCourseDocumentList({ keyword: this.keyword })
      .then((res) => {
        this.cate = res.data;
      })
      .catch(() => {
        console.log("Lỗi khi tải danh sách danh mục.");
      });
  },
};
</script>

<style scoped>
.task-block {
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #000000;
    background: #f9f9f9;
    border-radius: 0;
}

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
    font-size: 12px;
    padding: 8px;
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

.form-group input[type=file] {
    opacity: 1;
    position: relative;
    border: none;
}

.form-control,
.form-group .el-input__inner {
    padding: 6px 16px 6px 18px;
    background-color: transparent;
    border: 1px solid #E3E3E3;
    border-radius: 5px;
    color: #2c2c2c;
    height: 40px;
    line-height: normal;
    font-size: 13px;
}
</style>