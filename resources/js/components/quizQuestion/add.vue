<template>
  <div>
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3"><h4 class="card-title">Thêm mới câu hỏi</h4></div>
                <div class="col-md-6"></div>
                <div class="col-md-3">
                  </div>
              </div>
              <!-- <p class="card-description">Basic form elements</p> -->
              <form class="forms-sample">
                <div class="form-group">
                  <label>Tiêu đề (VD:Câu hỏi WHERE) </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.title_review"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Câu hỏi </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.name"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Dịch Câu hỏi </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.sub_name"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Audio</label>
                  <UploadPdf @increase-vu="choisefile"></UploadPdf>
                </div>
                <div class="form-group">
                  <label>Đáp Án A </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.option_1"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Đáp Án B </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.option_2"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Đáp Án C </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.option_3"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Đáp Án D </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.option_4"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Giải thích</label>
                  <TinyMce
                  v-model="objData.sub_explain"
                />
                  <!-- <vs-input
                    class="w-100"
                    v-model="objData.sub_explain"
                    font-size="40px"
                  /> -->
                </div>
                <div class="form-group">
                  <label>Dịch Đáp Án A </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.sub_option_1"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Dịch Đáp Án B </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.sub_option_2"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Dịch Đáp Án C </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.sub_option_3"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Dịch Đáp Án D </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.sub_option_4"
                    font-size="40px"
                  />
                </div>
                
                <div class="form-group">
                  <label>Đáp Án Đúng </label>
                  <vs-select class="selectExample" v-model="objData.ans" placeholder="Chọn đáp án" >
                    <vs-select-item value="option_1" text="Đáp Án A" />
                    <vs-select-item value="option_2" text="Đáp Án B" />
                    <vs-select-item value="option_3" text="Đáp Án C" />
                    <vs-select-item value="option_4" text="Đáp Án D" />
                  </vs-select>
                </div>
                <div class="form-group">
                  <label>Điểm số cho câu hỏi này </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.poins"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Ảnh câu hỏi</label>
                  <image-upload
                    v-model="objData.image"
                    type="avatar"
                    :title="'question-'"
                  ></image-upload>
                </div>
                <div class="form-group">
                  <label>Đề Thi </label>
                  <vs-select
                    class="selectExample"
                    v-model="objData.exam_id"
                    placeholder="Danh mục"
                    @change="findPartExample()"
                  >
                  <vs-select-item
                      value="0"
                      text="Không danh mục"
                    />
                    <vs-select-item
                      :value="item.id"
                      :text="item.name"
                      v-for="(item, index) in exam"
                      :key="'f' + index"
                    />
                  </vs-select>
                </div>
                <div class="form-group">
                  <label>Part </label>
                  <vs-select
                    class="selectExample"
                    v-model="objData.part_id"
                    placeholder="Chọn"
                    :disabled=" part.length == 0"
                  >
                  <vs-select-item
                      value="0"
                      text="Part"
                    />
                    <vs-select-item
                      :value="item.id"
                      :text="item.name"
                      v-for="(item, index) in part"
                      :key="'f' + index"
                    />
                  </vs-select>
                </div>
                <div class="form-group">
                  <label for="exampleInputName1">Trạng thái</label>
                  <vs-select v-model="objData.status"
                  >
                      <vs-select-item  value="1" text="Hiện" />
                      <vs-select-item  value="0" text="Ẩn" />
                    </vs-select>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="row fixxed">
        <div class="col-12">
          <div class="saveButton">
            <vs-button color="primary" @click="saveEdit()">Cập nhật</vs-button>
          </div>
        </div>
      </div>
    <!-- content-wrapper ends -->
  </div>
</template>

<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";
import UploadPdf from "../layouts/pdf_upload.vue"
export default {
  data() {
    return {
      objData: {
        name: "",
        sub_name:"",
        sub_explain:"",
        audio:"",
        title_review:"",
        option_1:"",
        option_2:"",
        option_3:"",
        option_4:"",
        sub_option_1:"",
        sub_option_2:"",
        sub_option_3:"",
        sub_option_4:"",
        image:"",
        ans:"",
        poins:4.95,
        part_id:0,
        exam_id:0,
        status: 1,
      },
      part:[],
      exam:[],
      errors:[]
    };
  },
components: {
    TinyMce,UploadPdf
  },
  methods: {
    ...mapActions(["saveQuizQuestion","listQuizExam", "loadings","findPartExam"]),
    saveEdit() {
      this.errors = [];
      
      if(this.objData.part_id == 0) this.errors.push('Chọn part của đề thi');
      if(this.objData.exam_id == 0) this.errors.push('Chọn đề thi');
      if(this.objData.ans == "") this.errors.push('Chọn Đáp Án Đúng');
      if(this.objData.option_2 == "") this.errors.push('Điền Đáp Án B');
      if(this.objData.option_1 == "") this.errors.push('Điền Đáp Án A');
      // if(this.objData.title_review == 0) this.errors.push('Nhập tiêu đề câu hỏi');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      } else {
        this.loadings(true);
        this.saveQuizQuestion(this.objData)
        .then(response => {
            this.loadings(false);
            this.$router.push({ name: "list_quiz_question" });
            this.$success("Thêm câu hỏi thành công");
            this.$route.push({ name: "list_quiz_question" });
          })
          .catch(error => {
            this.loadings(false);
          });
      }
    },
    choisefile(e){
      this.objData.audio = e;
    },
    findPartExample(){
      this.findPartExam(this.objData.exam_id).then((response) => {
        this.part = response.data;
      });
    }
  },
  mounted() {
    this.loadings(true);
    this.listQuizExam().then((response) => {
      this.loadings(false);
      this.exam = response.data;
    });
  }
};
</script>

<style>
</style>