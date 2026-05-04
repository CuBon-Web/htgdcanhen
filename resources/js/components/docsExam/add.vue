<template>
  <div>
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3"><h4 class="card-title">Thêm mới danh mục</h4></div>
                <div class="col-md-6"></div>
                <div class="col-md-3">
                  </div>
              </div>
              <!-- <p class="card-description">Basic form elements</p> -->
              <form class="forms-sample">
                <div class="form-group">
                  <vs-input
                    class="w-100"
                    v-model="objData.name"
                    font-size="40px"
                    label-placeholder="Tên danh mục"
                  />
                </div>
                <div class="form-group">
                  <label>Audio</label>
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
      objData: {
        name: "",
        category:"",
        status: 1,
        audio:""
      },
      cate:[],
      errors:[]
    };
  },
components: {
    TinyMce,UploadPdf
  },
  methods: {
    ...mapActions(["saveDocsExam", "loadings","listDocsCategory"]),
    choisefile(e){
      this.objData.audio = e;
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
            this.$success("Thêm đề thành công");
            this.$route.push({ name: "list_docs_exam" });
          })
          .catch(error => {
            this.loadings(false);
          });
      }
    },

  },
  mounted() {
    this.loadings(true);
    this.listDocsCategory().then((response) => {
      this.loadings(false);
      this.cate = response.data;
    });
  }
};
</script>

<style>
</style>