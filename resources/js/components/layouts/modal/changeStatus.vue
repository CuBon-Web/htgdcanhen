<template>
    <div class="card-body">
      <form class="forms-sample" enctype="multipart/form-data">
        <div class="form-group">
        <label>Thay đổi trạng thái</label>
        <vs-select
            class="selectExample"
            v-model="status"
            placeholder="Thay đổi trạng thái"
            >
            <vs-select-item value="1" text="Kích hoạt" />
            <vs-select-item
                value="1"
                text="Kích hoạt"
            />
            <vs-select-item
                value="2"
                text="Khóa"
            />
            </vs-select>
        </div>
        <div class="form-group">
          <vs-button
            color="success"
            type="gradient"
            class="mr-left-45"
            @click="handleSubmit()"
          >Cập nhật</vs-button>
        </div>
      </form>
    </div>
  </template>
  
  <script>
  import { required, email, minLength, sameAs } from "vuelidate/lib/validators";
  import { mapActions } from "vuex";
  export default {
    data() {
      return {
        status:0
      };
    },
    props: ['array-id'],
    validations: {
    },
    methods: {
      ...mapActions(["loadings","changeStatusDethi"]),
      handleSubmit() {
        this.loadings(true);
        this.changeStatusDethi({data:this.arrayId,status:this.status} ).then(response => {
            this.loadings(false);
            this.$emit("closePopup", false);
            this.$success('Cập nhật thành công');
        }).catch(error => {
            this.loadings(false);
            this.$error('Cập nhật thất bại');
        })
      }
    }
  };
  </script>