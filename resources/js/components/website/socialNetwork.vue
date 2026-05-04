<template>
    <div>
        <h3 class="page-title">Feedback Socical</h3>
        <div class="row">
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body" >
                <div class="row" v-for="(item, key) in objData" :key="item.id">
                  <div class="col-md-5">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Ảnh đại diện</label>
                          <image-upload type="avatar" v-model="item.avatar" :title="'avatar-'"></image-upload>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Ảnh bài viết</label>
                          <image-upload type="avatar" v-model="item.image" :title="'avatar-image-'"></image-upload>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-7">
                    <div class="form-group">
                      <label>Tên</label>
                      <label style="float: right;cursor: pointer" title="Xóa ảnh" v-if="key != 0" @click="removeObjPartner(item.id)">
                        <vs-icon icon="clear"></vs-icon>
                      </label>
                      <vs-input
                        type="text"
                        v-model="item.name"
                        size="default"
                        placeholder="Học viên"
                        class="w-100"
                      />
                    </div>
                    <div class="form-group">
                      <label>Ngày feedback</label>
                      <vs-input
                        type="text"
                        v-model="item.date"
                        size="default"
                        placeholder="01/2025"
                        class="w-100"
                      />
                    </div>
                    <div class="form-group">
                      <label>Link Socical</label>
                      <vs-input
                        type="text"
                        v-model="item.link"
                        size="default"
                        placeholder="..."
                        class="w-100"
                      />
                    </div>
                    <div class="form-group">
                      <label>Nội Dung</label>
                      <vs-textarea v-model="item.status" />

                    </div>
                  </div>
                  <hr style="border: 0.5px solid #04040426; width: 100%;">
                </div>
                <vs-button color="primary" @click="saveSocicalFeedbacks">Lưu</vs-button>
                <vs-button color="success" @click="addObjPartner">Thêm ảnh</vs-button>
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
  export default {
    name: "prize",
    data() {
      return {
        objData:[
          {
            name:"",
            avatar: "",
            image: "",
            status:"",
            link:"",
            date:"",
            type:1
          }
        ]
      };
    },
    components: {},
    computed: {},
    watch: {},
    methods: {
      ...mapActions(["saveSocicalFeedback", "loadings","listSocicalFeedback"]),
      saveSocicalFeedbacks(){
        this.loadings(true);
        this.saveSocicalFeedback({data:this.objData}).then(response => {
          this.loadings(false);
          this.$success('Thêm ảnh thành công');
        }).catch(error => {
          this.loadings(false);
          this.$error('Thêm ảnh thất bại');
        })
      },
      addObjPartner(){
        this.objData.push({
            name:"",
            image: "",
            avatar:"",
            status:"",
            link:"",
            date:"",
            type:1
          });
      },
      removeObjPartner(id){
        const index = this.objData.findIndex(obj => obj.id == id);
        if (index !== -1) {
            this.objData.splice(index, 1);
        }
      },
      listBanners(){
        this.loadings(true);
        this.listSocicalFeedback().then(response => {
          this.loadings(false);
          if(response.data.length > 0){
            this.objData = response.data
          }else{
            this.objData = [
              {
                name:"",
                image: "",
                avatar:"",
                status:"",
                link:"",
                date:"",
                type:1
              }
            ]
          }

        }).catch(error => {
          this.loadings(false);;
        })
      }
    },
    mounted() {
      this.listBanners();
    }
  };
  </script>
