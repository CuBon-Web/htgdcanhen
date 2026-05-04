<template>
  <div>
      <h3 class="page-title">Quản lý Founder</h3>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body" >
              <div class="row" v-for="(item, key) in objData">
                <div class="col-md-3">
                  <div class="form-group">
                    <image-upload type="avatar" v-model="item.image" :title="'thanh-vien-'"></image-upload>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tên</label>
                    <vs-input
                      type="text"
                      v-model="item.name"
                      size="default"
                      placeholder="Tên Founder"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Chức Vụ</label>
                    <vs-input
                      type="text"
                      v-model="item.position"
                      size="default"
                      placeholder="Mô tả founder"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Điểm số</label>
                    <vs-input
                      type="text"
                      v-model="item.poins"
                      size="default"
                      placeholder="8.5"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Mô tả</label>
                    <div class="form-group">
                    <label>Câu hỏi thường gặp</label>
                    <div v-for="(item, i) in item.description" :key="i">
                      <div class="row">
                        <div class="col-10">
                          <vs-textarea v-model="item.description[i].title" :placeholder="'Mô tả ' + i" />
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

                    <vs-input
                      type="text"
                      v-model="item.position"
                      size="default"
                      placeholder="Mô tả founder"
                      class="w-100"
                    />
                  </div>
                  <div class="form-group">
                    <label>Trạng thái</label>
                    <vs-select v-model="item.status"
                  >
                      <vs-select-item  value="1" text="Hiện" />
                      <vs-select-item  value="0" text="Ẩn" />
                    </vs-select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Bài viết</label>
                    
                    <label style="float: right;cursor: pointer" title="Xóa founder" v-if="key != 0" @click="removeObjBanner(key)">
                      <vs-icon icon="clear"></vs-icon>
                    </label>
                    <TinyMce v-model="item.content" />
                    
                  </div>
                  
                  
                </div>
                <hr style="border: 0.5px solid #04040426; width: 100%;">
              </div>
              <vs-button color="primary" @click="saveFounders">Lưu</vs-button>
              <vs-button color="success" @click="addObjBanner">Thêm Founder</vs-button>
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
  name: "product",
  data() {
    return {
      joke: {
        avatar: "delete-sign--v2.png",
      },
      objData:[
        {
          image: "",
          status:1,
          name:"",
          position:"",
          poins:"8.5",
          content:"",
          description: [
            {
              title: ""
            }
          ]
        }
      ] 
    };
  },
  components: {TinyMce,},
  computed: {},
  watch: {},
  methods: {
    ...mapActions(["saveFounder", "loadings","listFounder"]),
    saveFounders(){
      this.loadings(true);
      this.saveFounder({data:this.objData}).then(response => {
        this.loadings(false);
        this.$success('Sửa Founder thành công');
      }).catch(error => {
        this.loadings(false);
        this.$error('Sửa Founder thất bại');
      })
    },
    addObjBanner(){
      this.objData.push({
          image: "",
          status:1,
          name:"",
          position:"",
        });
    },
    removeObjBanner(i){
      this.objData.splice(i, 1);
    },
    listFounders(){
      this.loadings(true);
      this.listFounder().then(response => {
        this.loadings(false);
        this.objData = response.data
      }).catch(error => {
        this.loadings(false);;
      })
    }
  },
  mounted() {
    this.listFounders();
  }
};
</script>