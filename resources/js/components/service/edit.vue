<template>
  <div>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            
            <div class="form-group">
              <label>Tiêu đề</label>
              <vs-input
                type="text"
                size="default"
                placeholder="Tên bài viết"
                class="w-100"
                v-model="objData.name"
              />
            </div>
            <div class="form-group">
                <label>Mô tả ngắn</label>
                <vs-input
                  type="text"
                  size="default"
                  placeholder="Mô tả"
                  class="w-100"
                  v-model="objData.description[0].content"
                />
              </div>
              <div class="form-group">
              <label>Ảnh header (png)</label>
              <image-upload
                v-model="objData.image"
                type="avatar"
                :title="'service-'"
              ></image-upload>
            </div>
            
            <div class="form-group br pd-1">
                <h3>Benefit</h3>
                <div class="row">
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>Ảnh một</label>
                      <image-upload
                        v-model="objData.content.image_1"
                        type="avatar"
                        :title="'service-'"
                      ></image-upload>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>Ảnh hai</label>
                      <image-upload
                        v-model="objData.content.image_2"
                        type="avatar"
                        :title="'service-'"
                      ></image-upload>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <vs-input
                      type="text"
                      size="default"
                      placeholder="Tiêu đề"
                      class="w-100"
                      v-model="objData.content.title"
                    /> <br>
                    <vs-textarea v-model="objData.content.description" placeholder="Mô tả" />
                   
                  </div>
                </div>
                <div v-for="(item, i) in objData.content.object" :key="i">
                <div class="row">
                  <div class="col-2">
                    <div class="form-group">
                      <label>icon</label>
                      <image-upload-small v-model="objData.content.object[i].image" type="avatar" :title="'danh-muc'"
                      class="w-100"></image-upload-small>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <label>Nội dung</label>
                      <vs-input type="text" size="default" :placeholder="'Tiêu đề ' + i" class="w-100"
                      v-model="objData.content.object[i].name" /> <br>
                    <vs-textarea v-model="objData.content.object[i].content" :placeholder="'Nội dung ' + i" />
                    </div>
                    
                    <br />
                  </div>
                  <div class="col-2">
                    <a href="javascript:;" v-if="i != 0" @click="remoteDetailTaskr(index, keys)">
                      <img v-bind:src="'/media/' + joke.avatar" width="25" />
                    </a>
                  </div>
                </div>
              </div>
              <el-button size="small" @click="addDetailTask('content', index)">Thêm giá trị</el-button>
              </div>
              <div class="form-group br pd-1">
                <h3>Quote</h3>
                <div class="row">
                  <div class="col-lg-12">
                    <vs-input
                      type="text"
                      size="default"
                      placeholder="Tiêu đề"
                      class="w-100"
                      v-model="objData.otherser.title"
                    />
                   
                  </div>
                </div>
                <div v-for="(item, i) in objData.otherser.object" :key="i">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Ảnh</label>
                      <image-upload
                        v-model="objData.otherser.object[i].image"
                        type="avatar"
                        :title="'service-'"
                      ></image-upload>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group">
                      <label>Nội dung</label>
                      <vs-input type="text" size="default" :placeholder="'Tiêu đề ' + i" class="w-100"
                      v-model="objData.otherser.object[i].name" /> <br>
                      <TinyMce v-model="objData.otherser.object[i].content" />
                    <!-- <vs-textarea v-model="objData.otherser.object[i].content" :placeholder="'Nội dung ' + i" /> -->
                    </div>
                    
                    <br />
                  </div>
                  <div class="col-2">
                    <a href="javascript:;" v-if="i != 0" @click="remoteDetailTaskr2(index, keys)">
                      <img v-bind:src="'/media/' + joke.avatar" width="25" />
                    </a>
                  </div>
                </div>
              </div>
              <el-button size="small" @click="addDetailTask('otherser', index)">Thêm giá trị</el-button>
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
      <div class="row fixxed">
        <div class="col-12">
          <div class="saveButton">
            <vs-button color="primary" @click="addServices">Cập nhật</vs-button>
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
export default {
  name: "editService",
  data() {
    return {
      joke: {
        avatar: "delete-sign--v2.png",
      },
      showLang:{
        title:false,
        content:false,
        description:false
      },
      options1:[],
      errors:[],
      cate:[],
      type_cate:[],
      objData: {
        id: this.$route.params.id,
        name: "",
        content:{
            title: "",
            description: "",
            image_1:"",
            image_2:"",
            object: [
              {
                name: "",
                image: "",
                content: ""
              }
            ]
          },
        description: [
          {
            lang_code: "vi",
            content: "",
          },
        ],
        status: 1,
        image: "",
        cate_id: 0,
        otherser:{
            title: "",
            object: [
              {
                name: "",
                image: "",
                content: ""
              }
            ]
          },
      },
      lang:[],
      listSer:[]
    };
  },
  components: {
    TinyMce
  },
  computed: {},
  watch: {},
  methods: {
    ...mapActions(["addService", "loadings","detailService","listLanguage","listCateService","listService"]),
    addServices(){
      this.errors = [];
      if(this.objData.name == '') this.errors.push('Tên không được để trống');
      if(this.objData.description[0].content == '') this.errors.push('Mô tả không được để trống');
      if(this.objData.image == '') this.errors.push('Vui lòng chọn ảnh');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      }else{
        this.loadings(true);
        this.addService(this.objData).then(response => {
          this.loadings(false);
          this.$router.push({name:'listService'});
          this.$success('Thêm tin tức thành công');
        }).catch(error => {
          this.loadings(false);
          this.$error('Thêm tin tức thất bại');
        })
      }
    },
    addDetailTask(key, index) {
      var oj = {};
      if (key == 'content') {
        oj.name = "";
        oj.image = "";
        oj.content = "";
        this.objData.content.object.push(oj);
      }
      if (key == 'otherser') {
        oj.name = "";
        oj.image = "";
        oj.content ="";
        this.objData.otherser.object.push(oj);
      }
    },
    remoteDetailTaskr(index, keytaskdetail) {
      this.objData.content.object.splice(keytaskdetail, 1);
    },
    remoteDetailTaskr2(index, keytaskdetail) {
      this.objData.otherser.object.splice(keytaskdetail, 1);
    },
    listServices(){
      this.listService({ keyword: this.keyword })
      .then(response => {
          this.loadings(false);
          this.options1 = response.data;
      }).catch(err => {
          this.loadings(false);
          this.options1 = err.data;
      });
    },
    editById() {
      this.loadings(true);
      this.detailService(this.objData).then(response => {
        this.loadings(false);
        this.objData = response.data;
           this.objData.content = JSON.parse(response.data.content);
          this.objData.description = JSON.parse(response.data.description);
          this.objData.otherser = JSON.parse(response.data.otherser);
          this.objData.name = JSON.parse(response.data.name);
      }).catch(error => {
        console.log(12);
      });
    },
    listLang(){
      this.listLanguage().then(response => {
        this.lang  = response.data
      }).catch(error => {

      })
    },
    changeLanguage(data){
      this.editById();
    }
  },
  mounted() {
     this.listCateService().then((response) => {
      this.loadings(false);
      this.cate = response.data;
    });
    this.editById();
    this.listLang();
    this.listServices();
  }
};
</script>