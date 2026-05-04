<template>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
                <h4 class="card-title" >Nhập Dữ Liệu Excel</h4>
                <div class="form-group">
                <div class="text-center">
                    <form @submit="submitForm" enctype="multipart/form-data">
                      <div class="input-group">
                        <div class="custom-file">
                          <input
                            type="file"
                            name="filename"
                            class="custom-file-input"
                            id="inputFileUpload"
                            v-on:change="onFileChange"
                          />
                          <label class="custom-file-label" for="inputFileUpload"
                            >Chọn File Excel</label
                          >
                        </div>
                      </div>
                      <br />
                      <button class="button" type="submit">Import</button>
                      <p class="text-success font-weight-bold">{{ filename }}</p>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    
  </template>
  <script>
  import { mapActions } from "vuex";
  export default {
   mounted() {
     console.log("Component successfully mounted.");
   },
   data() {
     return {
       filename: "",
       file: "",
       success: "",
     };
   },
   methods: {
     ...mapActions([
       "loadings"
     ]),
     onFileChange(e) {
       //console.log(e.target.files[0]);
       this.filename = "Selected File: " + e.target.files[0].name;
       this.file = e.target.files[0];
     },
     submitForm(e) {
       this.loadings(true);
       const isType = this.file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        if (!isType) {
          this.loadings(false);
            this.$message('Bạn nên tải các file excel có định dạng .xlxs', 'error');
        }else{
            e.preventDefault();
       let currentObj = this;
       const config = {
         headers: {
           "content-type": "multipart/form-data",
           "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]'),
         },
       };
       var ac = this;
       // form data
       let formData = new FormData();
       formData.append("file", this.file);
       // send upload request
       axios
         .post("/api/import_excel_docs", formData, config)
         .then(function (response) {
            ac.loadings(false);
            ac.$router.push({ name: "list_docs_question" });
            ac.$success('Import Thành Công');
         })
         .catch(function (error) {
           ac.loadings(false);
           ac.$errr('Thất Bại');
         });
        }
          
        // }
     },
   },
  };
  </script>
  <style>
.button{
    background: #13b76b;
    border: none;
    padding: 6px 21px;
    color: white;
    border-radius: 5px;
}
</style>