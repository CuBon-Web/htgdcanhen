<template>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title" >Danh sách khóa học</h4>

            <router-link class="nav-link" :to="{name:'createProduct'}">
              <vs-button type="gradient" style="float:right;">Thêm mới</vs-button>
            </router-link>
            <div class="row">
                <div class="col-md-4 col-12">
                  <vs-input class="w-100" placeholder="Tên khóa học" v-model="keyword" @keyup="searchProduct()"/>
                </div>
                <div class="col-md-4 col-12">
                  <vs-select
                    class="selectExample"
                    placeholder="--Giáo viên--"
                    v-model="keyCustomer"
                    @change="searchProduct()"
                    >
                    <vs-select-item value="" text="--Tất cả--" />

                      <vs-select-item 
                        v-for="item in customer"
                        v-if="item.type == 1"
                        :key="item.id"
                        :value="item.id"
                        :text="item.name"
                      />
                     
                  </vs-select>
                  
                </div>
                <div class="col-md-4 col-12">
                  <vs-select
                    class="selectExample"
                    placeholder="--Trạng Thái Khóa Học--"
                    v-model="status"
                    @change="searchProduct()"
                    >
                    <vs-select-item value="" text="--Chọn--" />

                      <vs-select-item
                        value="1"
                        text="Đang hoạt động"
                      />

                      <vs-select-item
                        value="0"
                        text="Cần xét duyệt"
                      />
                      <vs-select-item
                        value="2"
                        text="Ngưng hoạt động"
                      />
                  </vs-select>
                  
                </div>
              </div>
            
            <vs-table stripe :data="list" max-items="30" pagination>
              <template slot="thead">
                <vs-th>Ảnh khóa học</vs-th>
                <vs-th>Tên khóa học</vs-th>
                <vs-th>Danh mục</vs-th>
                <vs-th>Người đăng</vs-th>
                <vs-th>Trạng thái</vs-th>
                <vs-th>Hành động</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td ><vs-avatar size="large" :src="tr.images"/></vs-td>
                  <vs-td>{{ tr.name }}</vs-td>
                  <vs-td v-if="tr.cate != null">{{JSON.parse(tr.cate.name)[0].content}}</vs-td>
                  <vs-td v-if="tr.cate == null">--Trống---</vs-td>
                  <vs-td v-if="tr.user_id == 0">Quản trị viên</vs-td>
                  <vs-td v-if="tr.user_id != 0">{{ tr.customer.name }}</vs-td>
                  <vs-td v-if="tr.status == 1"><vs-button color="success" type="border">Đang hoạt động</vs-button></vs-td>
                  <vs-td v-if="tr.status == 0"><vs-button color="danger" type="border">Cần xét duyệt</vs-button></vs-td>  
                  <vs-td v-if="tr.status == 2"><vs-button color="danger" type="border">Ngưng hoạt động</vs-button></vs-td>  
                  <vs-td>
                    <router-link :to="{name:'edit_product',params:{id:tr.id}}">
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
import Swal from "sweetalert2";
import { mapActions } from "vuex";
export default {
  data() {
    return {
      list:[],
      customer:[],
      status:"",
      keyCustomer:0,
      keyword:"",
      objDel:{
        id_item:"",
        slug:"",
      }
      
    };
  },
  components: {},
  computed: {},
  watch: {},
  methods: {
    ...mapActions(['listProduct','deleteId','loadings','listCustomer']),
    listProducts(){
      this.loadings(true);
      this.listProduct({ keyword: this.keyword })
      .then(response => {
          this.loadings(false);
          this.list = response.data;
          console.log(response.data);
      }).catch(err => {
          this.loadings(false);
          this.list = err.data;
      });
    },
    listCustomers(){
      this.listCustomer({ keyword: this.customer })
      .then(response => {
        this.customer = response.data;
      });
    },
    searchProduct() {
      if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
      }
      this.timer = setTimeout(() => {
          this.listProduct({ keyword: this.keyword,keyCustomer:this.keyCustomer,status:this.status })
          .then(response => {
            this.list = response.data;
          });
      }, 800);
    },
    destroy(){
      this.deleteId(this.objDel).then(response => {
        this.listProducts();
        this.$success('xóa thành công');
      });
    },
    confirmDestroy(id,slug){
      this.objDel.id_item = id;
      this.$vs.dialog({
        type:'confirm',
        color: 'danger',
        title: `Bạn có chắc chắn`,
        text: 'Xóa khóa học này',
        accept:this.destroy
      })
    }
  },
  mounted() {
    this.listProducts();
    this.listCustomers();
  }
};
</script>