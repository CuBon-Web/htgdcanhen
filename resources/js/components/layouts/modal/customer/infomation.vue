<template>
  <div class="card-body">
    <form class="forms-sample" enctype="multipart/form-data">
      <div class="form-group">
        <vs-input
          class="w-100"
          v-model="objData.name"
          font-size="40px"
          label-placeholder="Họ và tên"
        />
      </div> <br>
      <div class="form-group">
        <vs-input
          class="w-100"
          v-model="objData.phone"
          font-size="40px"
          label-placeholder="Số điện thoại"
        />
      </div> <br>
      <div class="form-group">
        <vs-input
          class="w-100"
          v-model="objData.email"
          font-size="40px"
          label-placeholder="Email"
        />
      </div> <br>
      <div class="form-group"> 
        <label>Lớp học</label>
       <vs-textarea  label-placeholder="Lớp học" v-model="objData.note"/>
      </div> <br>
      <div class="form-group">
        <vs-button
          color="success"
          type="gradient"
          class="mr-left-45"
          @click="handleSubmit()"
        >Lưu lại</vs-button>
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
      objData: {
        id:this.customer.id,
        name: this.customer.name,
        phone: this.customer.phone,
        note:this.customer.note,
        email:this.customer.email
      },
      submitted: false
    };
  },
  props:['customer'],
  validations: {
 
  },
  methods: {
    ...mapActions(["EditProfile", "loadings"]),
    handleSubmit() {
      this.EditProfile(this.objData)
          .then(response => {
            this.$success('Sửa thành công');
            this.$emit("closePopup", false);
          })
          .catch(error => {
            this.$error(Object.values(error.response.data.errors)[0][0]);
            this.$emit("closePopup", false);
          });
    },
  },
  mounted(){
  }
};
</script>