<template>
  <div>
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <h4 class="card-title">Sửa cấp học khóa học</h4>
              </div>
              <div class="col-md-6"></div>
              <div class="col-md-3">
              </div>
            </div>
            <!-- <p class="card-description">Basic form elements</p> -->
            <form class="forms-sample">
              <div class="form-group">
                <vs-input class="w-100" v-model="objData.name[0].content" font-size="40px"
                  label-placeholder="Tên cấp học" />
                <el-button size="small" @click="showSettingLangExist('name')">Đa ngôn ngữ</el-button>
                <div class="dropLanguage" v-if="showLang.title == true">
                  <div class="form-group" v-for="item, index in lang" :key="index">
                    <label v-if="index != 0">{{ item.name }}</label>
                    <input v-if="index != 0" type="text" size="default" placeholder="Tên khóa học"
                      class="w-100 inputlang" v-model="objData.name[index].content" />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Ảnh header (Kích thước: 450x380)</label>
                    <image-upload v-model="objData.avatar" type="avatar" :title="'danh-muc'"></image-upload>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <label>Mô tả trong header</label>
                    <TinyMce v-model="objData.content" />
                  </div>
                </div>
              </div>


              <div class="form-group">
                <div class="row">
                  <div class="col-4">
                    <label>Ảnh đại diện vấn đề (Kích thước: 360x460)</label>
                    <image-upload v-model="objData.imagehome" type="avatar" :title="'service-'"></image-upload>
                  </div>
                  <div class="col-8">
                    <label>Vấn đề gặp phải</label>
                    <div v-for="(item, index) in objData.vande" :key="index">
                      <div class="row">
                        <div class="col-11">
                          <div class="row">
                            <div class="col-9">
                              <vs-input type="text" size="default" :placeholder="'Tiêu đề ' + index" class="w-100"
                                v-model="objData.vande[index].title" /> <br>
                              <vs-textarea :placeholder="'Nội dung ' + index" v-model="objData.vande[index].content" />
                            </div>
                            <div class="col-3">
                              <image-upload v-model="objData.vande[index].image" type="avatar"
                                :title="'service-sub-'"></image-upload>
                            </div>
                          </div>
                          <br />
                        </div>
                        <div class="col-1">
                          <a href="javascript:;" v-if="index != 0" @click="remoteAr(index, 'vande')">
                            <img v-bind:src="'/media/' + joke.avatar" width="25" />
                          </a>
                        </div>
                      </div>
                    </div>

                    <el-button size="small" @click="addInput('vande')">Thêm giá trị</el-button>
                  </div>
                </div>

              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-4">
                    <label>Ảnh đại diện phần sẽ giúp bạn</label>
                    <image-upload v-model="objData.imagehelp" type="avatar" :title="'service-'"></image-upload>
                  </div>
                  <div class="col-8">
                    <label>Nội dung phần sẽ giúp bạn</label>
                    <div v-for="(item, index) in objData.help" :key="index">
                      <div class="row">
                        <div class="col-11">
                          <div class="row">
                            <div class="col-9">
                              <vs-input type="text" size="default" :placeholder="'Tiêu đề ' + index" class="w-100"
                                v-model="objData.help[index].title" /> <br>
                              <vs-textarea :placeholder="'Nội dung ' + index" v-model="objData.help[index].content" />
                            </div>
                            <div class="col-3">
                              <image-upload v-model="objData.help[index].image" type="avatar"
                                :title="'service-sub-'"></image-upload>
                            </div>
                          </div>
                          <br />
                        </div>
                        <div class="col-1">
                          <a href="javascript:;" v-if="index != 0" @click="remoteAr(index, 'help')">
                            <img v-bind:src="'/media/' + joke.avatar" width="25" />
                          </a>
                        </div>
                      </div>
                    </div>

                    <el-button size="small" @click="addInput('help')">Thêm giá trị</el-button>
                  </div>
                </div>

              </div>
              <div class="form-group">
                <label for="exampleInputName1">Trạng thái</label>
                <vs-select v-model="objData.status">
                  <vs-select-item value="1" text="Hiện" />
                  <vs-select-item value="0" text="Ẩn" />
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
export default {
  data() {
    return {
      joke: {
        avatar: "delete-sign--v2.png",
      },
      showLang:{
        title:false
      },
      objData: {
        id:this.$route.params.id,
        name: [
          {
            lang_code:'vi',
            content:''
          }
        ],
        content: "",
        avatar: "",
        imagehome: "",
        status: 1,
        imagehelp: "",
        vande: [
          {
            title: "",
            image: "",
            content: ""
          },
        ],
        help: [
          {
            title: "",
            image: "",
            content: ""
          },
        ],
      },
      lang:[],
      img: "",
      errors:[]
    };
  },
  components: {
    TinyMce,
  },
  methods: {
    ...mapActions(["getInfoCate","saveCategory","listLanguage", "loadings"]),
    nameImage(event) {
      this.objData.avatar = event;
    },
    addInput(key) {
      var oj = {};
      if (key == 'vande') {
        oj.title = "";
        oj.image = "";
        oj.content = "";
        this.objData.vande.push(oj);
      }
      if (key == 'help') {
        oj.title = "";
        oj.image = "";
        oj.content = "";
        this.objData.help.push(oj);
      }

    },
    remoteAr(index, key) {
      if (key == 'vande') {
        this.objData.vande.splice(index, 1);
      }
      if (key == 'help') {
        this.objData.help.splice(index, 1);
      }
    },
    showSettingLangExist(value){
        this.showLang.title = !this.showLang.title
          this.lang.forEach((value, index) => {
              if(!this.objData.name[index] && value.code != this.objData.name[0].lang_code){
                  var oj = {};
                  oj.lang_code = value.code;
                  oj.content = ''
                  this.objData.name.push(oj)
              }
          });
    },
    saveEdit() {
      this.errors = [];
      if(this.objData.name[0].content == '') this.errors.push('Tên cấp học không được để trống');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      } else {
        this.loadings(true);
        this.saveCategory(this.objData)
        .then(response => {
            this.loadings(false);
            this.$router.push({ name: "list_category" });
            this.$success("Sửa cấp học thành công");
            this.$route.push({ name: "list_category" });
          })
          .catch(error => {
            this.loadings(false);
            // this.$error('Sửa cấp học thất bại');
          });
      }
    },
    listLang(){
      this.listLanguage().then(response => {
        this.loadings(false);
        this.lang  = response.data
      }).catch(error => {

      })
    },
    getInfoCates(){
      this.loadings(true);
      this.getInfoCate(this.objData).then(response => {
        this.loadings(false);
        if(response.data == null){
          this.objData ={
            id:this.$route.params.id,
            name: "",
            path: "",
            avatar: "",
            status: "",
          }
        }else{
          this.objData = response.data;
          this.objData.name = JSON.parse(response.data.name);
          this.objData.vande = JSON.parse(response.data.vande);
          this.objData.help = JSON.parse(response.data.help);
        }
      }).catch(error => {
        console.log(12);
      });
    },
    changeLanguage(data){
      this.getInfoCates();
    }
  },
  mounted() {
    this.loadings(true);
    this.getInfoCates();
    this.listLang();
  }
};
</script>

<style>
</style>