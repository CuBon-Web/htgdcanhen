<template>
  <div>
      <h3 class="page-title">Thành Tích</h3>
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
                        <label>Ảnh bảng điểm</label>
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
                      placeholder="Tên học viên"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Địa chỉ</label>
                    <vs-input
                      type="text"
                      v-model="item.link"
                      size="default"
                      placeholder="Địa chỉ"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Điểm</label>
                    <vs-input
                      type="text"
                      v-model="item.status"
                      size="default"
                      placeholder="8.5"
                      class="w-100"
                    />
                  </div>
                </div>
                <hr style="border: 0.5px solid #04040426; width: 100%;">
              </div>
              <vs-button color="primary" @click="saveAlbumAffters">Lưu</vs-button>
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
          type:0,
          id:0
        }
      ]
    };
  },
  components: {},
  computed: {},
  watch: {},
  methods: {
    ...mapActions(["saveAlbumAffter", "loadings","listAlbumAffter"]),
    saveAlbumAffters(){
      this.loadings(true);
      this.saveAlbumAffter({data:this.objData}).then(response => {
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
          type:0,
          id:0
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
      this.listAlbumAffter().then(response => {
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
              type:0,
              id:0
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
