<template>
  <div>
    <editor :init="init" v-model="content"></editor>
  </div>
</template>

<script>
import config from '../../../config';
import tinymce from "tinymce/tinymce";
import "tinymce/themes/silver";
import "tinymce/plugins/advlist";
import "tinymce/plugins/autolink";
import "tinymce/plugins/lists";
import "tinymce/plugins/link";
import "tinymce/plugins/image";
import "tinymce/plugins/charmap";
import "tinymce/plugins/print";
import "tinymce/plugins/preview";
import "tinymce/plugins/searchreplace";
import "tinymce/plugins/wordcount";
import "tinymce/plugins/code";
import "tinymce/plugins/fullscreen";
import "tinymce/plugins/media";
import "tinymce/plugins/table";
import "tinymce/plugins/textcolor";
import "tinymce/plugins/visualblocks";
import "tinymce/skins/ui/oxide/skin.min.css";
import Editor from "@tinymce/tinymce-vue";

export default {
  name: "tinymce-mathjax",
  components: {
    editor: Editor
  },
  props: {
    value: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      init: {
        paste_data_images: true,
        plugins: [
         "advlist autolink lists link image charmap print preview hr anchor pagebreak",
          "searchreplace wordcount visualblocks visualchars code fullscreen",
          "insertdatetime media nonbreaking save table contextmenu directionality",
          "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image | code | tiny_mce_wiris_formulaEditor tiny_mce_wiris_formulaEditorChemistry",
         toolbar2: "print preview media | forecolor backcolor emoticons",
        image_advtab: true,
        height: 400,
        images_upload_handler: this.images_upload_handler,
        setup: (editor) => {
          editor.on("keyup change input init", () => this.renderMathJax(editor));
        },
        external_plugins: {
          "tiny_mce_wiris": "https://www.wiris.net/demo/plugins/tiny_mce/plugin.js"
        }
      }
    };
  },
  computed: {
    content: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit('input', value);
      }
    }
  },
  mounted() {
    this.loadMathJax();
  },
  watch: {
    content() {
      this.$nextTick(() => this.renderMathJax());
    }
  },
  methods: {
    images_upload_handler: function(blobInfo, success, failure) {
      var xhr, formData;
      xhr = new XMLHttpRequest();
      xhr.withCredentials = false;
      xhr.open("POST", __ENV__.link + 'api/upload-image');
      xhr.onload = function() {
        var json;

        if (xhr.status != 200) {
          failure("HTTP Error: " + xhr.status);
          return;
        }
        
        json = JSON.parse(xhr.responseText);
        success(json.path);
      };
      formData = new FormData();
      formData.append("img", blobInfo.blob(), blobInfo.filename());
      xhr.send(formData);
    },
    loadMathJax() {
      if (!window.MathJax) {
        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js";
        script.async = true;
        script.onload = () => console.log("MathJax đã tải xong");
        document.head.appendChild(script);
      }
    },
    renderMathJax(editor = null) {
      this.$nextTick(() => {
        if (window.MathJax) {
          const content = editor ? editor.getBody() : document.querySelector(".tox-editor-container");
          if (content) {
            window.MathJax.typesetPromise([content])
              .then(() => console.log("MathJax render thành công!"))
              .catch((err) => console.error("MathJax render lỗi:", err));
          }
        }
      });
    }
  }
};
</script>

<style>
</style>
