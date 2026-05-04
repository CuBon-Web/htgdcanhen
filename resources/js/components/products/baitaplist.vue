<template>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title" >Danh sách bài tập</h4>

            <router-link class="nav-link" :to="{name:'createTest'}">
              <vs-button type="gradient" style="float:right;">Thêm mới</vs-button>
            </router-link>

            <vs-input icon="search" placeholder="Search" v-model="keyword" @keyup="searchProduct()"/>
            <vs-table stripe :data="list" max-items="30" pagination>
              <template slot="thead">
                <vs-th>Tên khóa học</vs-th>
                <vs-th>Người đăng</vs-th>
                <vs-th>Hành động</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td>{{ tr.name }}</vs-td>
                  <vs-td v-if="tr.user_id == 0">Quản trị viên</vs-td>
                  <vs-td v-if="tr.user_id != 0">{{ tr.customer.name }}</vs-td>
                  <vs-td >
                    <router-link :to="{name:'edittest',params:{id:tr.id}}">
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
    ...mapActions(['listTest','deleteId','loadings']),
    listProducts(){
      this.loadings(true);
      this.listTest({ keyword: this.keyword })
      .then(response => {
          this.loadings(false);
          this.list = response.data;
          console.log(response.data);
      }).catch(err => {
          this.loadings(false);
          this.list = err.data;
      });
    },
    searchProduct() {
      if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
      }
      this.timer = setTimeout(() => {
          this.listTest({ keyword: this.keyword })
          .then(response => {
            this.list = response.data;
          });
      }, 800);
    },
    destroy(){
      this.deleteId(this.objDel).then(response => {
        this.listTest();
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
  }
};
</script>