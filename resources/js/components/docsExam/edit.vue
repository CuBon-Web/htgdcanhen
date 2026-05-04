<template>
  <div>
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3"><h4 class="card-title">Sửa Đề</h4></div>
                <div class="col-md-6"></div>
                <div class="col-md-3">
                  </div>
              </div>
              
              <!-- <p class="card-description">Basic form elements</p> -->
              <form class="forms-sample">
                <div class="form-group">
                <label>Tên</label>
                  <vs-input
                    class="w-100"
                    v-model="objData.name"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                  <label>Audio: <span class="text-success font-weight-bold">{{ objData.audio }}(File Hiện Tại)</span></label>
                  <UploadPdf @increase-vu="choisefile"></UploadPdf>
                  
                </div>
                <div class="form-group">
                <label>Danh mục </label>
                <vs-select
                  class="selectExample"
                  v-model="objData.category"
                  placeholder="Danh mục"
                >
                <vs-select-item
                    value="0"
                    text="Không danh mục"
                  />
                  <vs-select-item
                    :value="item.id"
                    :text="item.name"
                    v-for="(item, index) in cate"
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
      showLang:{
        title:false
      },
      objData: {
        id:this.$route.params.id,
        name: "",
        category: "",
        audio:"",
        status: "",
      },
      lang:[],
      img: "",
      cate:[],
      errors:[]
    };
  },
  components: {
    TinyMce,UploadPdf
  },
  methods: {
    ...mapActions(["editDocsExamId","saveDocsExam", "loadings","listDocsCategory"]),
    nameImage(event) {
      this.objData.avatar = event;
    },
    saveEdit() {
      this.errors = [];
      if(this.objData.name == '') this.errors.push('Tên danh mục không được để trống');
      // if(this.objData.audio == '') this.errors.push('Chọn File Audio cho đề này');
      if(this.objData.category == "") this.errors.push('Chọn danh mục');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      } else {
        this.loadings(true);
        this.saveDocsExam(this.objData)
        .then(response => {
            this.loadings(false);
            this.$router.push({ name: "list_docs_exam" });
            this.$success("Sửa thành công");
            this.$route.push({ name: "list_docs_exam" });
          })
          .catch(error => {
            this.loadings(false);
            // this.$error('Sửa danh mục thất bại');
          });
      }
    },
    choisefile(e){
      this.objData.audio = e;
    },
    editDocsExamIds(){
      this.loadings(true);
      this.editDocsExamId(this.objData).then(response => {
        this.loadings(false);
        if(response.data == null){
          this.objData ={
            id:this.$route.params.id,
            name: "",
            category: "",
            status: "",
          }
        }else{
          this.objData = response.data;
        }
      }).catch(error => {
        console.log(12);
      });
    },
    changeLanguage(data){
      this.editDocsExamIds();
    }
  },
  mounted() {
    this.editDocsExamIds();
    this.listDocsCategory().then((response) => {
      this.loadings(false);
      this.cate = response.data;
    });
  }
};
</script>

<style>
</style>