<template>
  <div>
        <h3 class="page-title">Mã đơn hàng: #{{bill.bill_id}}</h3>
      <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <label>Chi tiết đơn hàng</label>
              <vs-table stripe :data="bill">
              <template slot="thead">
                <vs-th>Tên tài liệu</vs-th>
                <vs-th>Giá bán</vs-th>
              </template>
              <template>
                <vs-tr >
                  <vs-td>{{bill.document.name}}</vs-td>
                  <vs-td>{{formatNumber(bill.document.price)}}đ</vs-td>
                  <vs-td v-if="bill.document.price == 0">Miễn phí</vs-td>
                </vs-tr>
              </template>
            </vs-table>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <h4>Xác nhận thanh toán</h4>
                  </div>
                </div>
                <div class="col-md-6">
                  <vs-button color="success" type="border" @click="changePaymented()" v-if="bill.status == 0">Đã thanh toán</vs-button>
                  <vs-button color="danger" type="border" @click="changeUnPaymented()" v-if="bill.status == 1">Chưa thanh toán</vs-button>
                </div>
              </div>
            </div>
          </div>
          
        </div>
        <div class="col-md-4 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="form-group">
                <label>Trạng thái đơn hàng</label> <br>
                <vs-chip color="primary" v-if="bill.status == 1">
                  Đã thanh toán
                </vs-chip>
                <vs-chip color="danger" v-if="bill.status == 4">
                  Đã hủy
                </vs-chip>
                <vs-chip color="success" v-if="bill.status == 0">
                  Đợi kiểm tra
                </vs-chip>
                <vs-chip color="success" v-if="bill.status == 3">
                  Hoàn tất
                </vs-chip>
              </div>
              <div class="form-group ">
                <table class="table">
                    <tbody>
                      <tr>
                        <td>Tên khách hàng</td>
                        <td>{{bill.customer.name}}</td>
                      </tr>
                      <tr>
                        <td>Phone </td>
                        <td>{{bill.customer.phone}}</td>
                      </tr>
                      <tr>
                        <td>Email </td>
                        <td>{{bill.customer.email}}</td>
                      </tr>
                      <tr>
                        <td>Ghi chú </td>
                        <td>{{bill.note}}</td>
                      </tr>
                    </tbody>
                  </table>
              </div>
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
import Multiselect from 'vue-multiselect'
export default {
  name: "billsDocumentDetail",
  data() {
    return {
      popupActivo:false,
      customer:[],
      submitted: false,
      products:[],
      qty:"",
      bill:{
          bill_id:this.$route.params.bill_id,
          customer_id:'',
          product_id:0,
          status:0,
        }
    };
  },
  validations: {
    
  },
  components: {
    Multiselect
  },
  computed: {
    
  },
  watch: {},
  methods: {
    ...mapActions(["loadings","detailBillDocument","changeDocumentStatus"]),
    closePop(event) {
      this.popupActivo = event;
    },
    changeUnPaymented(){
      this.loadings(true);
      this.changeDocumentStatus({'status':0,'bill_id':this.$route.params.bill_id}).then(response => {
        this.loadings(false);
        this.detailBills();
        this.$success('Đã chuyển về cần kiểm tra');
        this.$router.push({ name: "billDocumentPaymented" });
      })
    },
    changePaymented(){
      this.loadings(true);
      this.changeDocumentStatus({'status':1,'bill_id':this.$route.params.bill_id}).then(response => {
        this.loadings(false);
        this.detailBills();
        this.$success('Xác nhận thành công');
        this.$router.push({ name: "billDocumentDraft" });
      })
    },
    detailBills(){
      this.detailBillDocument(this.bill.bill_id).then(response => {
        this.bill = response.data;
        
      })
    },

    customLabel ({ name }) {
      return `${name}`
    },
    formatNumber(num) {
       return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },

 

    saveBill(){
      this.bill.total_money = this.total_bill;
      console.log(this.bill);
    }
  },
  mounted() {
      this.detailBills();
  }
};
</script>
<style scoped>
.el-select-dropdown__item{
    height: auto!important;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>