<template>
  <div>
    <div class="row">
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            
            <div class="form-group">
              <label>Tên tài liệu</label>
              <vs-input
                type="text"
                size="default"
                placeholder="Tên bài viết"
                class="w-100"
                v-model="objData.name"
              />
            </div>
            <div class="form-group">
                <label>Nội dung</label>
                <TinyMce
                  v-model="objData.content[0].content"
                />
                <el-button size="small" @click="showSettingLangExist('content')">Đa ngôn ngữ</el-button>
                 <div class="dropLanguage" v-if="showLang.content == true">
                    <div class="form-group" v-for="item,index in lang" :key="index">
                        <label v-if="index != 0">{{item.name}}</label>
                        <TinyMce v-if="index != 0" v-model="objData.content[index].content" />
                    </div>
                </div>
              </div>
              <div class="form-group">
                <label>Mô tả ngắn</label>
                <TinyMce
                  v-model="objData.description[0].content"
                />
                <el-button size="small" @click="showSettingLangExist('description')">Đa ngôn ngữ</el-button>
                 <div class="dropLanguage" v-if="showLang.description == true">
                    <div class="form-group" v-for="item,index in lang" :key="index">
                        <label v-if="index != 0">{{item.name}}</label>
                        <TinyMce v-if="index != 0" v-model="objData.description[index].content" />
                    </div>
                </div>
              </div>
              <div class="form-group">
              <label>Ảnh đại diện</label>
              <image-upload
                v-model="objData.image"
                type="avatar"
                :title="'document-'"
              ></image-upload>
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
                <vs-select-item value="1" text="Hiện" />
                <vs-select-item value="0" text="Ẩn" />
              </vs-select>
            </div>
            <div class="form-group">
                  <vs-input
                    class="w-100"
                    v-model="objData.order_id"
                    font-size="40px"
                    label-placeholder="Thứ tự hiển thị"
                  />
                </div>
            <div class="form-group">
                <label>Danh mục</label>
                <vs-select
                  class="selectExample"
                  v-model="objData.cate_id"
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
              <label>File Xem Thử</label>
              <PdfUpload :value="objData.pdf" @increase-vu="choisefilePdf" />
              
            </div>
            <div class="form-group">
              <label>File Bán</label>
              <PdfUpload :value="objData.docs" @increase-vu="choisefileDocs" />
            </div>
            <div class="form-group ">
                <label>Giá </label>
                <vs-input
                  type="number"
                  size="default"
                  icon="all_inclusive"
                  class="w-100"
                  v-model="objData.price"
                />
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row fixxed">
      <div class="col-12">
        <div class="saveButton">
          <vs-button color="primary" @click="addDocuments">Thêm mới</vs-button>
        </div>
      </div>
    </div>
    <!-- content-wrapper ends -->
  </div>
</template>


<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";
import PdfUpload from "../layouts/pdf_upload.vue";
export default {
  name: "product",
  data() {
    return {
      errors: [],
      cate: [],
      lang: [],
      showLang: {
        title: false,
        content: false,
        description: false,
      },
      objData: {
        name: "",
        pdf:"",
        order_id:0,
        docs:"",
        price:0,
        content: [
          {
            lang_code: "vi",
            content: "",
          },
        ],
        description: [
          {
            lang_code: "vi",
            content: "",
          },
        ],
        status: 1,
        home_status: 0,
        image: "",
        cate_id: 0
      },
    };
  },
  components: {
    TinyMce,
    PdfUpload
  },
  computed: {},
  watch: {},
  methods: {
    ...mapActions([ 
      "addDocument",
      "loadings",
      "listLanguage",
      "listCateDocument"
    ]),
    listLang() {
      this.listLanguage()
        .then((response) => {
          this.loadings(false);
          this.lang = response.data;
        })
        .catch((error) => {});
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
    addDocuments() {
      this.errors = [];
      if(this.objData.name == '') this.errors.push('Tên không được để trống');
      if(this.objData.order_id == 0) this.errors.push('Nhập thứ tự hiển thị');
      if(this.objData.pdf == '') this.errors.push('File PDF không được để trống');
      if(this.objData.docs == '') this.errors.push('File DOC không được để trống');
      // if(this.objData.content[0].content == '') this.errors.push('Nội dung không được để trống');
      if(this.objData.description[0].content == '') this.errors.push('Mô tả không được để trống');
      if(this.objData.cate_id == 0) this.errors.push('Chọn danh mục');
      if (this.objData.image == "") this.errors.push("Vui lòng chọn ảnh");
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value);
        });
        return;
      } else {
        this.loadings(true);
        this.addDocument(this.objData)
          .then((response) => {
            this.loadings(false);
            this.$router.push({ name: "listDocument" });
            this.$success("Thêm thành công");
          })
          .catch((error) => {
            this.loadings(false);
            this.$error("Thêm thất bại");
          });
      }
    },
    choisefileDocs(e){
      this.objData.docs = e;
    },
    choisefilePdf(e){
      this.objData.pdf = e;
    },
  },
  mounted() {
     this.listCateDocument().then((response) => {
      this.loadings(false);
      this.cate = response.data;
    });
    this.listLang();
  },
};
</script>