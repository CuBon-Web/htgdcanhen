<template>
  <div>
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3"><h4 class="card-title">Thêm mới nhóm câu hỏi</h4></div>
                <div class="col-md-6"></div>
                <div class="col-md-3">
                  </div>
              </div>
              <!-- <p class="card-description">Basic form elements</p> -->
              <form class="forms-sample">
                <div class="form-group">
                  <label>Tên nhóm </label>
                  <vs-input
                    class="w-100"
                    v-model="objData.name"
                    font-size="40px"
                  />
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
                    @change="findQuestionPart()"
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
                  <label>Nhóm câu hỏi</label>
                  <vs-select
                    placeholder="Chọn các câu hỏi trong nhóm này"
                    multiple
                    autocomplete
                    class="selectExample"
                    v-model="objData.group_question"
                    :disabled=" questionOfPart.length == 0"
                    >
                    <vs-select-item v-if="item.name!=null" :key="index" :value="item.id" :text="'Câu hỏi ' + item.numerical_order +' - '+ item.name" v-for="(item,index) in questionOfPart" />
                    <vs-select-item v-if="item.name==null" :key="index" :value="item.id" :text="'Câu hỏi ' + item.numerical_order" v-for="(item,index) in questionOfPart" />
                  </vs-select>
                </div>
                <div class="form-group">
                  <label>Audio</label>
                  <UploadPdf @increase-vu="choisefile"></UploadPdf>
                </div>
                <div class="form-group">
                  <label>Ảnh nhóm câu hỏi</label>
                  <ImageMulti v-model="objData.image" :title="'question-group-'"/> 

                  <!-- <image-upload
                    v-model="objData.image"
                    type="avatar"
                    :title="'question-group-'"
                  ></image-upload> -->
                </div>
                <div class="form-group">
                <label>Nội dung nhóm câu hỏi</label>
                <TinyMce
                  v-model="objData.content"
                />
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
import ImageMulti from "../_common/upload_image_multi";
export default {
  data() {
    return {
      objData: {
        name: "",
        group_question:[],
        image:[],
        content:"",
        audio:"",
        part_id:0,
        exam_id:0,
        status: 1,
      },
      part:[],
      exam:[],
      questionOfPart:[],
      errors:[]
    };
  },
components: {
    TinyMce,UploadPdf,ImageMulti
  },
  methods: {
    ...mapActions(["saveDocsQuestionGroup","listDocsExam", "loadings","findPartExamDocs","findQuestionPartsDocs"]),
    saveEdit() {
      this.errors = [];
      if(this.objData.part_id == 0) this.errors.push('Chọn part của đề thsi');
      if(this.objData.exam_id == 0) this.errors.push('Chọn đề thi');
      // if(this.objData.image == "" || this.objData.content == "") this.errors.push('Hãy nhập ảnh, nội dung cho group');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      } else {
        this.loadings(true);
        this.saveDocsQuestionGroup(this.objData)
        .then(response => {
            this.loadings(false);
            this.$router.push({ name: "list_docs_question_group" });
            this.$success("Thêm thành công");
            this.$route.push({ name: "list_docs_question_group" });
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
      this.findPartExamDocs(this.objData.exam_id).then((response) => {
        this.part = response.data;
      });
    },
    findQuestionPart(){
    this.objData.group_question = [];
      this.findQuestionPartsDocs(this.objData).then((response) => {
        this.questionOfPart = response.data;
      });
    }
  },
  mounted() {
    this.loadings(true);
    this.listDocsExam().then((response) => {
      this.loadings(false);
      this.exam = response.data;
    });
  }
};
</script>

<style>
</style>