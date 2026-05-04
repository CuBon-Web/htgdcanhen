<template>
  <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Danh sách thành viên</h4>
              <div class="row">
                <div class="col-md-6">
                  <vs-input
                icon="search"
                placeholder="Tìm kiếm bằng email"
                v-model="keyword"
                @keyup="searchCategory()"
              />
                </div>
                <div class="col-md-6 text-right">
                  <vs-button   @click="exportExcel()">Xuất Excel</vs-button>
                </div>
              </div>

              </div>
              <vs-table max-items="20" pagination :data="list">
                <template slot="thead">
                  <vs-th>ID</vs-th>
                  <vs-th>Thông tin</vs-th>
                  <vs-th>Lớp học</vs-th>
                  <vs-th>Email</vs-th>
                  <vs-th>Điện thoại</vs-th>
                  <vs-th>Trạng thái</vs-th>
                  <vs-th>Action</vs-th>
                </template>
                <template slot-scope="{data}">
                  <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="indextr+1">{{tr.id}}</vs-td>
                    <vs-td :data="tr.name">{{tr.name}}</vs-td>
                    <vs-td :data="tr.note">{{tr.note}}</vs-td>
                    <vs-td :data="tr.email">{{tr.email}}</vs-td>
                    <vs-td :data="tr.phone">{{tr.phone}}</vs-td>
                    <vs-td :data="tr.status" v-if="tr.status == 0"><div class="active_acount">Đã Kích Hoạt</div></vs-td>
                    <vs-td :data="tr.status" v-if="tr.status == 1"><div class="none-active_acount">Chưa Kích Hoạt</div></vs-td>
                    <vs-td :data="tr.id">
                      <router-link :to="{name:'customerEdit',params:{id_customer:tr.id}}">
                        <vs-button
                          vs-type="gradient"
                          size="lagre"
                          color="success"
                          icon="edit"
                        ></vs-button>
                      </router-link>
                      <vs-button vs-type="gradient" size="lagre" color="red" icon="delete_forever" @click="confirmDestroy(tr.id)"></vs-button>
                    </vs-td>
                  </vs-tr>
                </template>
              </vs-table>
            </div>
          </div>
        </div>
      </div>
</template>


<script>
import { mapActions } from "vuex";
export default {
  data: () => ({
    keyword: null,
    malophoc: null,
    list: [],
    timer:0,
    id_item :''
  }),
  components: {
  },
  computed: {
    
  },
  methods: {
    ...mapActions(["listCustomer","destroyCustomer", "loadings"]),
    listCustomers() {
      this.loadings(true);
      this.listCustomer({ keyword: this.keyword })
      .then(response => {
          this.loadings(false);
          this.list = response.data;
          console.log(this.list.length);
        });
    },
    exportExcel(){
      window.open('/api/export-excel-customer', '_blank');
    },
    searchCategory() {
      if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
      }
      this.timer = setTimeout(() => {
          this.listCustomer({ keyword: this.keyword,malophoc:this.malophoc })
          .then(response => {
            this.list = response.data;
          });
      }, 800);
    },
    destroy(){
      this.loadings(true);
      this.destroyCustomer({id:this.id_item})
      .then(response => {
        this.listCustomers()
        this.loadings(false);
        this.$success('Xóa thành công');
      });
    },
    confirmDestroy(id){
      this.id_item = id;
      this.$vs.dialog({
        type:'confirm',
        color: 'danger',
        title: `Bạn có chắc chắn`,
        text: 'Xóa tài khoản và toàn bộ dữ liệu của thành viên này',
        accept:this.destroy
      })
    }
  },
  mounted() {
    this.listCustomers()
  }
};
</script>
<style>
.active_acount {
    background: #239d23;
    width: 109px;
    text-align: center;
    color: white;
    border-radius: 15px;
}
.none-active_acount {
    background: rgb(163, 5, 5);
    width: 109px;
    text-align: center;
    color: white;
    border-radius: 15px;
}
</style>