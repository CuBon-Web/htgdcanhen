<template>
  <!-- partial -->
  <div>
      <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Câu Hỏi</h4>
              <router-link class="nav-link" :to="{name:'add_docs_question'}">
                <vs-button type="gradient" style="float:right;">Thêm mới</vs-button>
              </router-link>
              <div class="row">
                <div class="col-md-5">
                  <vs-select
                    class="selectExample"
                    placeholder="--Đề Thi--"
                    v-model="fillter.exam_id"
                    @change="findQuestionPartExample()"
                    >
                    <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="item,index in listExam" />
                  </vs-select>
                </div>
                <div class="col-md-5">
                  <vs-select
                    class="selectExample"
                    placeholder="--Đề Thi--"
                    v-model="fillter.part_id"
                    :disabled=" listPart.length == 0"
                    @change="findQuestionPartExample()"
                    >
                    <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="item,index in listPart" />
                  </vs-select>
                 
                </div>
                <div class="col-md-2">
                  <router-link class="mb-0 font-weight-light" :to="{name:'questionImportDocs'}">
                    <vs-button color="warning" >Nhập Excel</vs-button>
                  </router-link>
                </div>
              </div>
              <vs-table max-items="20" pagination :data="list">
                <template slot="thead">
                  <vs-th>Câu Số</vs-th>
                  <vs-th>Đề Thi</vs-th>
                  <vs-th>Part</vs-th>
                  <vs-th>Loại câu hỏi</vs-th>
                  <vs-th>Hành động</vs-th>
                </template>
                <template slot-scope="{data}">
                  <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="tr.numerical_order">{{ tr.numerical_order }}</vs-td>
                    <vs-td :data="tr.exam" v-if="tr.exam != null">{{ tr.exam.name }}</vs-td>
                    <vs-td :data="tr.exam" v-if="tr.exam == null">--Trống--</vs-td>
                    <vs-td :data="tr.part" v-if="tr.part != null">{{ tr.part.name }}</vs-td>
                    <vs-td :data="tr.part" v-if="tr.part == null">--Trống--</vs-td>
                    <vs-td :data="tr.type" v-if="tr.type == 1">Trắc nghiệm thông thường</vs-td>
                    <vs-td :data="tr.type" v-if="tr.type == 2">Dịch câu - Điền từ</vs-td>
                    <vs-td :data="tr.id">
                      <router-link :to="{name:'edit_docs_question',params:{id:tr.id}}">
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
    fillter:{
      exam_id:0,
      part_id:0,
    },
    listExam:[],
    listPart:[],
    timer:0,
    id_item :''
  }),
  components: {
    ModalAdd
  },
  computed: {
  },
  methods: {
    ...mapActions(["listDocsQuestion","deleteDocsQuestionId", "loadings",'listDocsExam','findPartExamDocs','findQuestionPartExamDocs']),
    closePop(event) {
      this.listCategory();
      this.popupActivo = event;
    },
    findQuestionPartExample(){
      this.findPartExamDocs(this.fillter.exam_id).then((response) => {
        this.listPart = response.data;
      });
      this.findQuestionPartExamDocs(this.fillter).then((response) => {
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
      this.listDocsQuestion({ keyword: this.keyword })
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
          this.listDocsQuestion({ keyword: this.keyword })
          .then(response => {
            this.list = response.data;
          });
      }, 800);
    },
    destroy(){
      this.loadings(true);
      this.deleteDocsQuestionId({id:this.id_item})
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