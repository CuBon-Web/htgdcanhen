<template>
  <div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Tên giảng viên</label>
                  <vs-input type="text" size="default" placeholder="Tên giảng viên" class="w-100"
                    v-model="objData.name" />
                </div>
                <div class="form-group">
                  <label>Điểm số</label>
                  <vs-input type="text" size="default" placeholder="8.5" class="w-100" v-model="objData.uudai" />
                </div>
                <div class="form-group">
                  <label>Ảnh đại diện</label>
                  <image-upload v-model="objData.images" type="avatar" :title="'lich-khai-giang-'"></image-upload>
                </div>
                <div class="form-group">
                  <label>Mô tả giáo viên</label>
                  <div class="form-group">
                    <div v-for="(item, i) in objData.description" :key="i">
                      <div class="row">
                        <div class="col-10">
                          <vs-textarea v-model="objData.description[i].title" :placeholder="'Mô tả ' + i" />
                          <br />
                        </div>
                        <div class="col-2">
                          <a href="javascript:;" v-if="i != 0" @click="remoteAr(i, 'description')">
                            <img v-bind:src="'/media/' + joke.avatar" width="25" />
                          </a>
                        </div>
                      </div>
                    </div>

                    <el-button size="small" @click="addInput('description')">Thêm giá trị</el-button>
                  </div>

                  <!-- <vs-textarea v-model="objData.description" /> -->
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nội dung</label>
                  <TinyMce v-model="objData.content" />
                </div>

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

      </div>

    </div>
    <div class="row fixxed">
      <div class="col-12">
        <div class="saveButton">
          <vs-button color="primary" @click="addSolutions">Cập nhật</vs-button>
        </div>
      </div>
    </div>
    <!-- content-wrapper ends -->
  </div>
</template>


<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";
import { required } from "vuelidate/lib/validators";
import ImageMulti from "../_common/upload_image_multi";
export default {
  name: "editSolution",
  data() {
    return {
      joke: {
        avatar: "delete-sign--v2.png",
      },
      showLang: {
        title: false,
        content: false,
        description: false
      },
      joke: {
        avatar: "delete-sign--v2.png",
      },
      errors: [],
      cate: [],
      type_cate: [],
      objData: {
        name: "",
        detail: [
          {
            malop: "",
            khaigiang: "",
            lichhoc: "",
            uudiem: ""
          },
        ],
        uudai: "",
        content: "",
        description: [
          {
            title: ""
          }
        ],
        status: 1,
        images: "",
      },
      lang: []
    };
  },
  components: {
    TinyMce, ImageMulti
  },
  computed: {},
  watch: {},
  methods: {
    ...mapActions(["addSolution", "loadings", "detailSolution", "listLanguage"]),
    addSolutions() {
      this.errors = [];
      if (this.objData.name == '') this.errors.push('Tên không được để trống');
      if (this.objData.content == '') this.errors.push('Nội dung không được để trống');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      } else {
        this.loadings(true);
        this.addSolution(this.objData).then(response => {
          this.loadings(false);
          this.$router.push({ name: 'list_solution' });
          this.$success('Thành công');
        }).catch(error => {
          this.loadings(false);
          this.$error('Thất bại');
        })
      }
    },
    remoteAr(index, key) {
      if (key == 'description') {
        this.objData.description.splice(index, 1);
      }
    },
    addInput(key) {
      var oj = {};
      if (key == 'description') {
        oj.title = "";
        this.objData.description.push(oj);
      }
    },
    showSettingLangExist(value, name = "content") {
      if (value == "name") {
        this.showLang.title = !this.showLang.title
        this.lang.forEach((value, index) => {
          if (!this.objData.name[index] && value.code != this.objData.name[0].lang_code) {
            var oj = {};
            oj.lang_code = value.code;
            oj.content = ''
            this.objData.name.push(oj)
          }
        });
      }
      if (value == "content") {
        this.showLang.content = !this.showLang.content
        this.lang.forEach((value, index) => {
          if (!this.objData.content[index] && value.code != this.objData.content[0].lang_code) {
            var oj = {};
            oj.lang_code = value.code;
            oj.content = ''
            this.objData.content.push(oj)
          }
        });
      }
      if (value == "description") {
        this.showLang.description = !this.showLang.description
        this.lang.forEach((value, index) => {
          if (!this.objData.description[index] && value.code != this.objData.description[0].lang_code) {
            var oj = {};
            oj.lang_code = value.code;
            oj.content = ''
            this.objData.description.push(oj)
          }
        });
      }

    },
    editById() {
      this.loadings(true);
      this.detailSolution({ id: this.$route.params.id }).then(response => {
        this.loadings(false);
        this.objData = response.data;
         this.objData.description = JSON.parse(response.data.description);
      }).catch(error => {
        console.log(12);
      });
    },
    listLang() {
      this.listLanguage().then(response => {
        this.lang = response.data
      }).catch(error => {

      })
    },
    changeLanguage(data) {
      this.editById();
    }
  },
  mounted() {
    this.editById();
    this.listLang();
  }
};
</script>