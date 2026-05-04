<template>
  <div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label>Tên danh mục</label>
              <vs-input
                type="text"
                size="default"
                placeholder="Tên danh mục"
                class="w-100"
                v-model="objData.name"
              />
            </div>
            <div class="form-group">
              <label>Mô tả ngắn</label>
              <vs-input
                type="text"
                size="default"
                placeholder="Mô tả ngắn"
                class="w-100"
                v-model="objData.description"
              />
            </div>
            <div class="form-group">
              <label>Trạng thái</label>
              <vs-select v-model="objData.status">
                <vs-select-item value="1" text="Hiện" />
                <vs-select-item value="0" text="Ẩn" />
              </vs-select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row fixxed">
      <div class="col-12">
        <div class="saveButton">
          <vs-button color="primary" @click="saveCourseDocumentLists">Thêm mới</vs-button>
        </div>
      </div>
    </div>
    <!-- content-wrapper ends -->
  </div>
</template>


<script>
import { mapActions } from "vuex";
import TinyMce from "../_common/tinymce";
export default {
  name: "documentcate",
  data() {
    return {
      errors: [],
      cate: [],
      lang: [],
      showLang: {
        title: false,
        content: false,
        description: false,
      },
      objData: {
        name: "",
        description: "",
        status: 1,
      },
    };
  },
  components: {
    TinyMce,
  },
  computed: {},
  watch: {},
  methods: {
    ...mapActions([
      "saveCourseDocumentList",
      "loadings",
    ]),

    saveCourseDocumentLists() {
      this.errors = [];
      if (this.objData.name == "")
        this.errors.push("Tên không được để trống");
      if (this.objData.description == "")
        this.errors.push("Mô tả không được để trống");
      if (this.errors.length > 0) {
        this.errors.forEach((value, key) => {
          this.$error(value);
        });
        return;
      } else {
        this.loadings(true);
        this.saveCourseDocumentList(this.objData)
          .then((response) => {
            this.loadings(false);
            this.$router.push({ name: "listDocumentCate" });
            this.$success("Thêm thành công");
          })
          .catch((error) => {
            this.loadings(false);
            this.$error("Thêm thất bại");
          });
      }
    },

  },
  mounted() {
  },
};
</script>