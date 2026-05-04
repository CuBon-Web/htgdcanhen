<template>
    <!-- partial -->
    <div>
        <div class="row">
            <div class="col-md-3"></div>
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Đổi mật khẩu</h4>
                <p class="card-description">Đổi mật khẩu Admin của bạn, Lưu ý ghi nhớ mật khẩu. Nếu quên mật khẩu hãy thông báo với Developer để reset về mật khẩu mặc định là 123456</p>
               
                
                <form class="forms-sample">
                <div class="form-group">
                  <vs-input
                    class="w-100"
                    v-model="objData.newpass"
                    font-size="40px"
                    label-placeholder="Mật khẩu mới"
                  />
                </div>
              </form>
              <div class="saveButton">
            <vs-button color="primary" @click="saveEdit()">Cập nhật</vs-button>
          </div>
              </div>
            </div>
          </div>
          <div class="col-md-3"></div>
        </div>
    </div>
  </template>
  
  
  <script>
  import ModalAdd from "../../components/layouts/modal/category/add";
  
  import { mapActions } from "vuex";
  export default {
    data: () => ({
      keyword: null,
      popupActivo: false,
      objData:{
        newpass:""
      }
      
    }),
    components: {
      ModalAdd
    },
    computed: {
      
    },
    methods: {
      ...mapActions(["changePass", "loadings","destroyToken"]),
      closePop(event) {
        this.listCategory();
        this.popupActivo = event;
      },
      saveEdit() {
      this.errors = [];
      if(this.objData.newpass == '') this.errors.push('Nhập mật khẩu mới');
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value)
        })
        return;
      } else {
        this.loadings(true);
        this.changePass(this.objData)
        .then(response => {
            this.loadings(false);
            this.destroyToken().then(response => {
                this.loadings(false)
                this.$router.push({ name: "login" })
                this.$error('Vui lòng đăng nhập lại');
            }).catch(error => {
                this.loadings(false);
                this.$router.push({ name: "login" })
                this.$error('Đăng nhập thất bại');
            })
          })
          .catch(error => {
            this.loadings(false);
          });
      }
    },
    },
    mounted() {
    }
  };
  </script>
  <style>
  </style>