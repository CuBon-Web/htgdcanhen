<template>
  <!-- partial -->
  <div>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Nhóm Câu Hỏi (Nhóm câu hỏi có cùng nội dung cho phần writting )</h4>
              <router-link class="nav-link" :to="{name:'add_docs_question_group'}">
                <vs-button type="gradient" style="float:right;">Thêm mới</vs-button>
              </router-link>
              <div class="row">
                <div class="col-md-6">
                  <vs-select
                    class="selectExample"
                    placeholder="--Lọc Part theo đề--"
                    v-model="exam_id"
                    @change="findGroupExam()"
                    >
                    <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="item,index in listExam" />
                  </vs-select>
                </div>
              </div>
              <vs-table max-items="20" pagination :data="list">
                <template slot="thead">
                  <vs-th>ID</vs-th>
                  <vs-th>Tên</vs-th>
                  <vs-th>Đề Thi</vs-th>
                  <vs-th>Part</vs-th>
                  <vs-th>Hành động</vs-th>
                </template>
                <template slot-scope="{data}">
                  <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="tr.id">{{ tr.id }}</vs-td>
                    <vs-td :data="tr.name">{{ tr.name }}</vs-td>
                    <vs-td :data="tr.exam" v-if="tr.exam != null">{{ tr.exam.name }}</vs-td>
                    <vs-td :data="tr.exam" v-if="tr.exam == null">--Trống--</vs-td>
                    <vs-td :data="tr.part" v-if="tr.part != null">{{ tr.part.name }}</vs-td>
                    <vs-td :data="tr.part" v-if="tr.part == null">--Trống--</vs-td>
                    <vs-td :data="tr.id">
                      <router-link :to="{name:'edit_docs_question_group',params:{id:tr.id}}">
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
      <vs-popup style="width:100%;" title="Thêm mới danh mục" :active.sync="popupActivo">
        <ModalAdd @closePopup="closePop($event)" />
      </vs-popup>
  </div>
</template>


<script>
import ModalAdd from "../../components/layouts/modal/category/add";

import { mapActions } from "vuex";
export default {
  data: () => ({
    keyword: null,
    popupActivo: false,
    list: [],
    listExam:[],
    timer:0,
    exam_id:0,
    id_item :''
  }),
  components: {
    ModalAdd
  },
  computed: {
    
  },
  methods: {
    ...mapActions(["listDocsQuestionGroup","deleteDocsQuestionIdGroup", "loadings",'listDocsExam','findGroupQuestionDocs']),
    closePop(event) {
      this.listCategory();
      this.popupActivo = event;
    },
    findGroupExam(){
      this.findGroupQuestionDocs({exam_id:this.exam_id}).then((response) => {
        this.list = response.data;
      });
    },
    listExams() {
    this.loadings(true);
      this.listDocsExam({ keyword: this.keyword })
      .then(response => {
          this.loadings(false);
          this.listExam = response.data;
        });
    },
    listCategory() {
      this.loadings(true);
      this.listDocsQuestionGroup({ keyword: this.keyword })
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
          this.listDocsQuestionGroup({ keyword: this.keyword })
          .then(response => {
            this.list = response.data;
          });
      }, 800);
    },
    destroy(){
      this.loadings(true);
      this.deleteDocsQuestionIdGroup({id:this.id_item})
      .then(response => {
        this.listCategory()
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
        text: 'Xóa mục này',
        accept:this.destroy
      })
    }
  },
  mounted() {
    this.listCategory();
    this.listExams();
  }
};
</script>
<style>
</style>