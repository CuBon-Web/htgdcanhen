<template>
  <div>
    <div class="row">
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label>Tên khóa học</label>
              <vs-input type="text" size="default" placeholder="Tên sản phẩm" class="w-100" v-model="objData.name" />
            </div>
            <div class="form-group">
              <label>Tổng quan khóa học</label>
              <TinyMce v-model="objData.content" />
            </div>
            <div class="form-group">
              <label>Mô tả ngắn</label>
              <TinyMce v-model="objData.description" />
            </div>
            <div class="form-group">
              <label>Chươn trình giảng dạy</label>
              <i>(Bạn có thể kéo thả để thay đổi vị trí chương học hoặc bài học)</i>
              <div v-for="(task, taskIndex) in objData.size" :key="taskIndex" class="task-block" draggable="true"
                @dragstart="onTaskDragStart(taskIndex)" @dragover.prevent @drop="onTaskDrop(taskIndex)">
                <div class="form-group">
                  <label><b>Chương {{ taskIndex + 1 }}</b>: {{ task.chuong }} <i class="fas fa-trash text-danger"
                      @click="removeTask(taskIndex)" style="cursor: pointer;"></i></label>
                  <input type="text" v-model="task.chuong" class="form-control" placeholder="Nhập tên chương"
                    style="background: #0fa3342e; border: 1px solid #06a306;" />
                </div>

                <div class="accordion" id="accordionExample">

                  <div class="accordion-item detail-task-block" v-for="(detail, detailIndex) in task.detail_task"
                    :key="detailIndex" draggable="true" @dragstart="onDetailDragStart(taskIndex, detailIndex)"
                    @dragover.prevent @drop="onDetailDrop(taskIndex, detailIndex)">
                    <h2 class="accordion-header" id="headingTwo">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        :data-bs-target="'#collapseTwo_' + detailIndex + taskIndex" aria-expanded="false"
                        :aria-controls="'#collapseTwo_' + detailIndex + taskIndex">
                        {{ detail.name }} <i class="fas fa-trash" v-if="task.detail_task.length > 1"
                          @click="removeDetailTask(taskIndex, detailIndex)"></i>
                      </button>
                    </h2>
                    <div :id="'collapseTwo_' + detailIndex + taskIndex" class="accordion-collapse collapse"
                      aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                      <div class="accordion-body">
                        <div class="row">
                          <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                              <label>Tiêu đề bài học:</label>
                              <input type="text" v-model="detail.name" class="form-control" placeholder="Tên bài học" />
                            </div>
                          </div>
                          <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                              <label>Chọn bài test:</label>
                              <vs-select class="selectExample" v-model="detail.test_id" placeholder="Danh mục" autocomplete>
                                  <vs-select-item value="0" text="--Chọn--" />
                                  <vs-select-item :value="item.id" :text="item.name" v-for="(item, index) in listTests"
                                    :key="'f' + index" />
                                </vs-select>
                              <!-- <select v-model="detail.test_id" class="form-control">
                                <option value="0" v-for="(index, item) this.listTest">--Chọn--</option>
                                <option value="1">Học thử</option>
                              </select> -->
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                              <label>Thời lượng video:</label>
                              <input type="text" v-model="detail.time" class="form-control" placeholder="00:00"
                                readonly />
                            </div>
                          </div>
                          <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                              <label>Trạng thái:</label>
                              <select v-model="detail.status" class="form-control">
                                <option value="1">Mất Phí</option>
                                <option value="0">Học thử</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                              <label>Upload Video:</label>
                              <p class="descs">Lưu ý: Video upload thành công chỉ khi hiển thị nội dung "Hoàn
                                tất upload"</p>
                              <input type="file" @change="uploadFile($event, taskIndex, detailIndex, 'video')"
                                class="form-control" accept="video/*" />
                              <div v-if="detail.uploadProgress.video > 0 && detail.uploadProgress.video < 100"
                                class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                  role="progressbar" :style="{ width: detail.uploadProgress.video + '%' }">
                                  {{ detail.uploadProgress.video }}%
                                </div>
                              </div>
                              <small v-if="detail.video && detail.uploadProgress.video === 100"
                                class="text-success d-block mt-1">
                                Hoàn tất upload video - <a :href="detail.video" target="_blank">Xem Video</a>

                              </small>
                            </div>
                          </div>
                          <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                              <label>Upload Tài liệu:</label>
                              <p class="descs">Lưu ý: Tài liệu upload thành công chỉ khi hiển thị nội dung
                                "Hoàn tất upload"</p>
                              <input type="file" @change="uploadFile($event, taskIndex, detailIndex, 'document')"
                                class="form-control" accept=".pdf,.doc,.docx" />
                              <div v-if="detail.uploadProgress.document > 0 && detail.uploadProgress.document < 100"
                                class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                  role="progressbar" :style="{ width: detail.uploadProgress.document + '%' }">
                                  {{ detail.uploadProgress.document }}%
                                </div>
                              </div>
                              <small v-if="detail.document && detail.uploadProgress.document === 100"
                                class="text-success d-block mt-1">
                                Hoàn tất upload tài liệu - <a :href="detail.document" target="_blank">Xem tài
                                  liệu</a>
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
                      <i class="fas fa-plus"></i> Thêm bài học
                    </button>
                  </div>
                </div>


                <hr />
              </div>
              <div class="row">
                <div class="col-12" style="    text-align: left;">
                  <button type="button" class="btn btn-secondary plusbaihoc" @click="addTask">
                    <i class="fas fa-plus"></i> Thêm chương mới
                  </button>
                </div>
              </div>

            </div>
            <div class="form-group">
              <label>Ảnh đại diện</label>
              <image-upload v-model="objData.images" type="avatar" :title="'khoa-hoc-'"></image-upload>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label>Trạng thái</label>
              <vs-select v-model="objData.status">
                <vs-select-item value="1" text="Đang hoạt động" />
                <vs-select-item value="2" text="Ngưng hoạt động" />
              </vs-select>
            </div>
            <div class="form-group">
                <label>Cấp học</label>
                <vs-select
                  class="selectExample"
                  v-model="objData.category"
                  placeholder="Danh mục"
                  @change="findCategoryType()"
                >
                <vs-select-item
                    value="0"
                    text="Không cấp học"
                  />
                  <vs-select-item
                    :value="item.id"
                    :text="JSON.parse(item.name)[0].content"
                    v-for="(item, index) in cate"
                    :key="'f' + index"
                  />
                </vs-select>
              </div>
              <div class="form-group">
                <label>Lớp học</label>
                <vs-select
                  class="selectExample"
                  v-model="objData.type_cate"
                  placeholder="Lớp học"
                  :disabled=" type_cate.length == 0"
                  @change="findCategoryTypeTwo()"
                >
                  <vs-select-item
                    :value="item.id"
                    :text="JSON.parse(item.name)[0].content"
                    v-for="(item, index) in type_cate"
                    :key="'v' + index"
                  />
                </vs-select>
              </div>

            <div class="form-group">
              <label>Hiển thị trang chủ</label>
              <vs-select v-model="objData.home_status">
                <vs-select-item value="1" text="Có" />
                <vs-select-item value="0" text="Không" />
              </vs-select>
            </div>
            <div class="form-group">
              <label>Số buổi học</label>
              <vs-input type="text" size="default" placeholder="Số buổi học" class="w-100"
                v-model="objData.thickness" />
            </div>
            <div class="form-group">
              <label>Số bài học</label>
              <vs-input type="text" size="default" placeholder="Số bài học" class="w-100"
                v-model="objData.ingredient" />
            </div>
            <div class="form-group">
              <label>Khóa học bao gồm</label>
              <div v-for="(item, i) in objData.preserve" :key="i">
                <div class="row">
                  <div class="col-2">
                    <image-upload-small v-model="objData.preserve[i].image" type="avatar" :title="'khoa-hoc-bao-gom-'"
                      class="w-100"></image-upload-small>
                  </div>
                  <div class="col-7">
                    <vs-input type="text" size="default" :placeholder="'Tiêu đề ' + i" class="w-100"
                      v-model="objData.preserve[i].title" />
                    <br />
                  </div>
                  <div class="col-3">
                    <a href="javascript:;" v-if="i != 0" @click="remoteAr(i, 'preserve')">
                      <img v-bind:src="'/media/' + joke.avatar" width="25" />
                    </a>
                  </div>
                </div>
              </div>

              <el-button size="small" @click="addInput('preserve')">Thêm giá trị</el-button>
            </div>
            <div class="form-group">
              <label>Câu hỏi thường gặp</label>
              <div v-for="(item, i) in objData.species" :key="i">
                <div class="row">
                  <div class="col-9">
                    <vs-input type="text" size="default" :placeholder="'Câu hỏi ' + i" class="w-100"
                      v-model="objData.species[i].chuong" />
                    <vs-textarea v-model="objData.species[i].content" :placeholder="'Trả lời ' + i" />
                    <br />
                  </div>
                  <div class="col-3">
                    <a href="javascript:;" v-if="i != 0" @click="remoteAr(i, 'species')">
                      <img v-bind:src="'/media/' + joke.avatar" width="25" />
                    </a>
                  </div>
                </div>
              </div>

              <el-button size="small" @click="addInput('species')">Thêm giá trị</el-button>
            </div>
            <div class="form-group">
              <label>Giá thực bán (Miễn phí nếu bỏ trống)</label>
              <vs-input type="number" size="default" class="w-100" v-model="objData.price" />
            </div>
            <div class="form-group">
              <label>Giá chưa khuyến mãi(bỏ trống nếu bạn không giảm giá)</label>
              <vs-input type="number" size="default" class="w-100" v-model="objData.discount" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row fixxed">
      <div class="col-12">
        <div class="saveButton">
          <vs-button color="primary" @click="saveProducts">Thêm mới</vs-button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";
import ImageMulti from "../_common/upload_image_multi";
import "tinymce/icons/default/icons.min.js";
import InputTag from "vue-input-tag";
export default {
  name: "product",
  data() {
    return {
      cate: [],
      joke: {
        avatar: "delete-sign--v2.png",
      },
      type_cate: [],
      tags: [],
      checkBox1: {
        roleid: []
      },
      linhtinh: [],
listTests:[],
      type_two: [],
      showLang: {
        title: false,
        content: false,
        description: false,
      },
      variantstatus: false,
      lang: [],
      errors: [],
      cateservice: [],
      lungtung2: [],
      objData: {
        lang: "",
        name: "",
        size: [
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
              },
            ],
          },
        ],
        tags: [],
        price: 0,
        discount: 0,
        preserve: [
          {
            image: "",
            title: "Sách giáo trình màu miễn phí"
          },
          {
            image: "",
            title: "Hỗ trợ sau giờ học từ giáo viên"
          }, {
            image: "",
            title: "Chăm sóc tận tình, báo cáo tiến độ thường xuyên"
          },
        ],
        ingredient: '',
        images: "",
        qty: "",
        description: "",
        content: "",
        category: 0,
        status: 1,
        qty: 100,
        discountStatus: 0,
        type_cate: 0,
        type_two: 0,
        species: [
          {
            chuong: "",
            content: "",
          }
        ],
        origin: "",
        thickness: "",
        hang_muc: "",
        service_id: 0,
        lungtung: [],
        status_variant: 0,
        home_status: 0
      },
    };
  },
  components: {
    TinyMce,
    ImageMulti,
    InputTag,
  },
  computed: {},
  watch: {
  },
  methods: {
    ...mapActions([
      "editId",
      "saveProduct",
      "listCate",
      "loadings",
      "listLanguage",
      "findTypeCate",
      "findTypeCateTwo",
      "listCateService",
      "listVariantValue",
      "listVariantSku",
      "listTest"
    ]),

    addTask() {
      this.objData.size.push({
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
          },
        ],
      });
    },
    addDetailTask(taskIndex) {
      this.objData.size[taskIndex].detail_task.push({
        name: "Bài học mới",
        time: "",
        icon: "",
        video: "",
        document: "",
        status: 0,
        test_id: 0,
        uploadProgress: {
          video: 0,
          document: 0,
        },
      });
    },
    removeDetailTask(taskIndex, detailIndex) {
      if (confirm('Bạn có chắc chắn muốn xóa bài học này không?')) {
        this.objData.size[taskIndex].detail_task.splice(detailIndex, 1);
      }
    },
    uploadFile(event, taskIndex, detailIndex, type) {
      const file = event.target.files[0];
      if (!file) return;

      if (type === "video") {
        this.getVideoDuration(file)
          .then((duration) => {
            const formatted = this.formatDuration(duration);
            this.objData.size[taskIndex].detail_task[detailIndex].time = formatted;
          })
          .catch(() => {
            this.objData.size[taskIndex].detail_task[detailIndex].time = "";
          });
      }

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
          this.objData.size[taskIndex].detail_task[detailIndex].uploadProgress[type] = percent;
        }
      };

      xhr.onload = () => {
        const detail = this.objData.size[taskIndex].detail_task[detailIndex];
        const result = JSON.parse(xhr.responseText);

        if (xhr.status === 200) {
          if (type === "video" && result.playback_url) {
            detail.video = result.playback_url;
            detail.uploadProgress.video = 100;
          } else if (type === "document" && result.url) {
            detail.document = result.url;
            detail.uploadProgress.document = 100;
          } else {
            alert("Upload thất bại.");
          }
        } else {
          alert("Upload lỗi.");
        }
      };

      xhr.onerror = () => {
        alert("Lỗi khi upload.");
      };

      xhr.send(formData);
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
      const moved = this.objData.size.splice(this.dragTaskIndex, 1)[0];
      this.objData.size.splice(index, 0, moved);
      this.dragTaskIndex = null;
    },

    // === Kéo thả bài học ===
    onDetailDragStart(taskIndex, detailIndex) {
      this.dragDetail = { taskIndex, detailIndex };
    },
    onDetailDrop(taskIndex, detailIndex) {
      const { taskIndex: fromTask, detailIndex: fromDetail } = this.dragDetail;

      if (fromTask === null || fromDetail === null) return;

      const movedItem = this.objData.size[fromTask].detail_task.splice(fromDetail, 1)[0];

      if (fromTask === taskIndex && fromDetail < detailIndex) {
        detailIndex--; // do bị shift vị trí
      }

      this.objData.size[taskIndex].detail_task.splice(detailIndex, 0, movedItem);
      this.dragDetail = { taskIndex: null, detailIndex: null };
    },
    removeTask(index) {
      if (confirm('Bạn có chắc chắn muốn xóa chương này không?')) {
        this.objData.size.splice(index, 1);
      }
    },
    saveProducts() {
      this.errors = [];
      if (this.objData.name == '') this.errors.push('Tên không được để trống');
      if (this.objData.content == '') this.errors.push('Nội dung không được để trống');
      if (this.objData.description == '') this.errors.push('Mô tả không được để trống');
      if (this.objData.images == '') this.errors.push('Vui lòng chọn ảnh');
      if (this.objData.category == 0) this.errors.push('Chọn danh mục sản phẩm');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value);
        });
        return;
      } else {
        this.loadings(true);
        // this.objData.lungtung = this.lungtung2;
        this.saveProduct(this.objData)
          .then((response) => {
            this.loadings(false);
            this.$router.push({ name: "listProduct" });
            this.$success("Thêm sản phẩm thành công");
            this.$route.push({ name: "listProduct" });
          })
          .catch((error) => {
            this.loadings(false);
            // this.$vs.notify({
            //   title: "Thất bại",
            //   text: "Thất bại",
            //   color: "danger",
            //   position: "top-right"
            // });
          });
      }
    },

    findCategoryType() {
      this.findTypeCate(this.objData.category).then((response) => {
        this.type_cate = response.data;
      });
    },

    findCategoryTypeTwo() {
      this.findTypeCateTwo(this.objData.type_cate).then((response) => {
        this.type_two = response.data;
      });
    },
    remoteAr(index, key) {

      if (key == 'preserve') {
        this.objData.preserve.splice(index, 1);
      }
      if (key == 'species') {
        this.objData.species.splice(index, 1);
      }
    },

    addInput(key) {
      var oj = {};


      if (key == 'preserve') {
        oj.image = "";
        oj.title = "";
        this.objData.preserve.push(oj);
      }
      if (key == 'species') {
        oj.chuong = "";
        oj.content = "";
        this.objData.species.push(oj);
      }
    },
    showSettingLangExist(value, name = "content") {
      if (value == "content") {
        this.showLang.content = !this.showLang.content;
        this.lang.forEach((value, index) => {
          if (
            !this.objData.content[index] &&
            value.code != this.objData.content[0].lang_code
          ) {
            var oj = {};
            oj.lang_code = value.code;
            oj.content = "";
            this.objData.content.push(oj);
          }
        });
      }
      if (value == "description") {
        this.showLang.description = !this.showLang.description;
        this.lang.forEach((value, index) => {
          if (
            !this.objData.description[index] &&
            value.code != this.objData.description[0].lang_code
          ) {
            var oj = {};
            oj.lang_code = value.code;
            oj.content = "";
            this.objData.description.push(oj);
          }
        });
      }
    },
    listLang() {
      this.listLanguage()
        .then((response) => {
          this.loadings(false);
          this.lang = response.data;
        })
        .catch((error) => { });
    },
    editById() {
      this.loadings(true);
      this.editId({ id: this.$route.params.id }).then(response => {
        this.loadings(false);
        console.log(JSON.parse(response.data.size));
        this.objData = response.data;
        this.objData.preserve = JSON.parse(response.data.preserve);
        this.objData.size = JSON.parse(response.data.size);
        this.objData.species = JSON.parse(response.data.species);
      }).catch(error => {
        console.log(12);
      });
    },
  },
  mounted() {
    this.loadings(true);
    this.editById();
    this.listCate().then((response) => {
      this.loadings(false);
      this.cate = response.data;
    });
     this.listTest().then((response) => {
      this.listTests = response.data;
    });
    this.listCateService().then((response) => {
      this.loadings(false);
      this.cateservice = response.data;
    });
    this.listLang();
  },
};
</script>
<style scoped>
.centerx li {
  list-style: none !important;
}

.centerx,
.con-notifications,
.con-notifications-position {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
}

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