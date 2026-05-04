<template>
  <div>
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <h4 class="card-title">Sửa lớp học</h4>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-3">
                  <vs-select
                    class="selectExample"
                    v-model="objData.language"
                    placeholder="Ngôn ngữ lớp học"
                    @change="changeLanguage($event)"
                  >
                    <vs-select-item
                      :key="index"
                      :value="item.code"
                      :text="item.name"
                      v-for="item,index in lang"
                    />
                  </vs-select>
                </div>
              </div>

              <!-- <p class="card-description">Basic form elements</p> -->
              <form class="forms-sample">
                <div class="form-group">
                <label for="exampleInputName1">Tên lớp học</label>
                  <vs-input
                    class="w-100"
                    v-model="objData.name[0].content"
                    :class="{ 'is-invalid': submitted && $v.objData.name.$error }"
                    font-size="40px"
                  />
                </div>
                <div class="form-group">
                <label for="exampleInputName1">Cấp học</label>
                  <vs-select class="selectExample" v-model="objData.cate_id" placeholder="Danh mục">
                  <vs-select-item
                    :value="0"
                    text="Cấp học"
                  />
                  <vs-select-item
                    :value="item.id"
                    :text="JSON.parse(item.name)[0].content"
                    v-for="(item,index) in categoryList"
                    :key="'f'+index"
                  />
                </vs-select>
                </div>
                <div class="form-group">
                  <label for="exampleInputName1">Nội dung</label>
                  <TinyMce v-model="objData.content" />
                </div>
                <div class="form-group">
                  <label for="exampleInputName1">Trạng thái</label>
                  <vs-select v-model="objData.status"
                  >
                    <vs-select-item  value="1" text="Hiện" />
                    <vs-select-item  value="0" text="Ẩn" />
                  </vs-select>
                </div>
                <vs-button
                  color="success"
                  type="gradient"
                  class="mr-left-45"
                  @click="saveEdit()"
                >Lưu lại</vs-button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <!-- content-wrapper ends -->
  </div>
</template>

<script>
import { mapActions } from "vuex";
import { required } from "vuelidate/lib/validators";
import TinyMce from "../_common/tinymce";
export default {
  data() {
    return {
      objData: {
        language: this.$route.params.language,
        id:this.$route.params.id,
        name: [
          {
            lang_code:'vi',
            content:''
          }
        ],
        content: "",
        avatar: "",
        status: "",
        cate_id:"",
      },
      categoryList:[],
      lang: [],
      img: "",
      submitted: false
    };
  },
  components: {
    TinyMce,
  },
  validations: {
    objData: {
      name: { required },
      cate_id: { required }
    }
  },
  methods: {
    ...mapActions([
      "getInfoTypeCate",
      "saveTypeCate",
      "listLanguage",
      "loadings","listCate"
    ]),
    nameImage(event) {
      this.objData.avatar = event;
    },
    listCategory() {
      this.loadings(true);
      this.listCate()
      .then(response => {
          this.loadings(false);
          this.categoryList = response.data;
        });
    },
    saveEdit() {
      this.errors = [];
      if(this.objData.name[0].content == '') this.errors.push('Tên lớp học không được để trống');
      if(this.objData.cate_id == '') this.errors.push('Danh mục cha không được để trống');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      } else {
        this.loadings(true);
        this.saveTypeCate(this.objData)
          .then(response => {
            this.$router.push({ name: "list_type_cate" });
            this.$success("Thêm lớp học thành công");
            this.$router.push({ name: "list_type_cate" });
          })
          .catch(error => {
            this.loadings(false);
            this.$error("Sửa lớp học thất bại");
          });
      }
    },
    listLang() {
      this.listLanguage()
        .then(response => {
          this.loadings(false);
          this.lang = response.data;
        })
        .catch(error => {});
    },
    getInfoCates() {
      this.loadings(true);
      this.getInfoTypeCate(this.objData)
        .then(response => {
          this.loadings(false);
          if (response.data == null) {
            this.objData = {
              id: this.$route.params.id,
              name: "",
              path: "",
              avatar: "",
              status: ""
            };
          } else {
            this.objData = response.data;
             this.objData.name = JSON.parse(response.data.name);
          }
        })
        .catch(error => {
          console.log(12);
        });
    },
    changeLanguage(data) {
      this.getInfoCates();
    }
  },
  mounted() {
    this.loadings(true);
    this.getInfoCates();
    this.listLang();
    this.listCategory();
  }
};
</script>

<style>
</style>