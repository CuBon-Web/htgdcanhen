<template>
  <!-- partial -->
  <div>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Danh sách đề thi</h4>
              <div class="row">
                <div class="col-md-6">
                  <vs-input
                icon="search"
                placeholder="Search"
                v-model="keyword"
                @keyup="searchCategory()"
              />
                </div>
                <div class="col-md-6 text-right">
                  <vs-button   @click="changeStatuss()" v-if="checkedid.length > 0">Đổi Trạng Thái</vs-button>
                  <vs-button color="danger"  @click="confirmDestroyArrayId()" v-if="checkedid.length > 0"><i class="fa fa-trash"></i> Xóa</vs-button>
                </div>
              </div>
             
              <vs-table max-items="50" pagination :data="list">
                <template slot="thead">
                  <vs-th> <input type="checkbox" name="a" v-model="checkedid" @change="checkAll()"></vs-th>
                  <vs-th>ID</vs-th>
                  <vs-th>Tên</vs-th>
                  <vs-th>Giáo viên</vs-th>
                  <vs-th>Giá</vs-th>
                  <vs-th>Lượt mua</vs-th>
                  <vs-th>Số lần làm bài</vs-th>
                  <vs-th>Trạng thái</vs-th>
                </template>
                <template slot-scope="{data}">
                  <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="tr.id">
                      <input type="checkbox" name="a" :value="tr.id" v-model="checkedid">
                    </vs-td>
                    <vs-td :data="tr.id">{{tr.id}}</vs-td>
                    <vs-td :data="tr.name">{{ tr.title }}</vs-td>
                    <vs-td :data="tr.customer">{{ tr.customer.name }}</vs-td>
                    <vs-td :data="tr.price" v-if="tr.price == 0">Miễn phí</vs-td>
                    <vs-td :data="tr.price" v-else>{{ tr.price }} VNĐ</vs-td>
                    <vs-td :data="tr.bill_dethi">{{ tr.bill_dethi.length }}</vs-td>
                    <vs-td :data="tr.sessions">{{ tr.sessions.length }}</vs-td>
                    <vs-td :data="tr.status" v-if="tr.status == 0">
                      <span class="badge badge-warning">Đợi xét duyệt</span>
                    </vs-td>
                    <vs-td :data="tr.status" v-if="tr.status == 1">
                      <span class="badge badge-success">Đang hoạt động</span>
                    </vs-td>
                    <vs-td :data="tr.status" v-else>
                      <span class="badge badge-danger">Đã khóa</span>
                    </vs-td>
                  </vs-tr>
                </template>
              </vs-table>
            </div>
          </div>
        </div>
      </div>
      <vs-popup style="width:100%;" title="Đổi trạng thái" :active.sync="popupActivo">
        <ChangeStatus :array-id="checkedid" @closePopup="closePop($event)" />
      </vs-popup>
  </div>
</template>


<script>
import ChangeStatus from "../../components/layouts/modal/changeStatus.vue";
import { mapActions } from "vuex";
export default {
  data: () => ({
    keyword: null,
    popupActivo: false,
    list: [],
    timer:0,
    id_item :'',
    checkedid:[]
  }),
  components: {ChangeStatus
  },
  computed: {
    
  },
  methods: {
    ...mapActions(["listDethi","deleteDocsCategoryId", "loadings","changeStatusDethi","deleteDethiArrayId"]),
    closePop(event) {
      this.listCategory();
      this.popupActivo = event;
    },
    changeStatuss(){
      this.errors = [];
      if (this.checkedid.length > 0) {
        this.popupActivo = true;
        
      }else{
           this.errors.push("Lựa chọn đơn hàng");
          if (this.errors.length > 0) {
            this.errors.forEach((value, key) => {
              this.$error(value);
            });
            return;
          }
        }
    },
    listCategory() {
      this.loadings(true);
      this.listDethi({ keyword: this.keyword })
      .then(response => {
          this.loadings(false);
          this.list = response.data;
        });
    },
    searchCategory() {
      if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
      }
      this.timer = setTimeout(() => {
          this.listDethi({ keyword: this.keyword })
          .then(response => {
            this.list = response.data;
          });
      }, 800);
    },
    destroy(){
      this.loadings(true);
      this.deleteDocsCategoryId({id:this.id_item})
      .then(response => {
        this.listCategory()
        this.loadings(false);
        this.$success('Xóa danh mục thành công');
      });
    },
    destroyArrayId(){
      this.loadings(true);
      this.deleteDethiArrayId({id:this.id_item})
      .then(response => {
        this.listCategory()
        this.loadings(false);
        this.$success('Xóa danh mục thành công');
      });
    },
    confirmDestroyArrayId(){
      this.id_item = this.checkedid;
      this.$vs.dialog({
        type:'confirm',
        color: 'danger',
        title: `Bạn có chắc chắn`,
        text: 'Xóa các đề thi đã chọn, các bài thi đã làm của học viên sẽ bị xóa',

        accept:this.destroyArrayId
      })
    },
    confirmDestroy(id){
      this.id_item = id;
      this.$vs.dialog({
        type:'confirm',
        color: 'danger',
        title: `Bạn có chắc chắn`,
        text: 'Xóa danh mục này',
        accept:this.destroy
      })
    },
    checkAll(){
      if(this.checkedid.length == this.list.length){
        this.checkedid = [];
      }else{
        this.checkedid = this.list.map(item => item.id);
      }
    }
  },
  mounted() {
    this.listCategory()
  }
};
</script>
<style>
</style>