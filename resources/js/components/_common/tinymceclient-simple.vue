<template>
  <div class="tinymce-container">
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
  name: "tinymce-simple",
  components: {
    editor: Editor
  },
  props: {
    value: {
      type: String,
      default: ''
    },
    height: {
      type: Number,
      default: 400
    }
  },
  data() {
    return {
      init: {
        paste_data_images: true,
        plugins: [
          "advlist autolink lists link image charmap print preview",
          "searchreplace wordcount visualblocks code fullscreen",
          "media table textcolor",
        ],
        toolbar: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image | forecolor backcolor | code",
        image_advtab: true,
        height: this.height,
        menubar: false,
        statusbar: false,
        branding: false,
        elementpath: false,
        icons: false, // Tắt icons để tránh lỗi
        images_upload_handler: this.images_upload_handler,
        content_style: `
          body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            font-size: 14px; 
            line-height: 1.6; 
            color: #333;
          }
          .math { 
            font-family: 'Times New Roman', serif; 
            font-size: 16px; 
            margin: 4px 0; 
          }
        `,
        setup: (editor) => {
          editor.on("keyup change input init", () => this.renderMathJax(editor));
          
          // Thêm button tùy chỉnh cho công thức toán học
          editor.ui.registry.addButton('custom_math', {
            text: '∑',
            tooltip: 'Chèn công thức toán học',
            onAction: () => {
              this.insertMathFormula(editor);
            }
          });
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
    this.loadFontAwesome();
    this.loadMathJax();
  },
  watch: {
    content() {
      this.$nextTick(() => this.renderMathJax());
    }
  },
  methods: {
    loadFontAwesome() {
      // Load Font Awesome nếu chưa có
      if (!document.querySelector('link[href*="font-awesome"]')) {
        const fontAwesome = document.createElement('link');
        fontAwesome.rel = 'stylesheet';
        fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
        document.head.appendChild(fontAwesome);
      }

      // Load custom TinyMCE CSS
      if (!document.querySelector('link[href*="tinymce-custom"]')) {
        const customCSS = document.createElement('link');
        customCSS.rel = 'stylesheet';
        customCSS.href = '/css/tinymce-custom.css';
        document.head.appendChild(customCSS);
      }
    },
    
    insertMathFormula(editor) {
      // Tạo modal đẹp hơn thay vì dùng prompt
      const modal = document.createElement('div');
      modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
      `;

      const modalContent = document.createElement('div');
      modalContent.style.cssText = `
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        width: 90%;
        max-width: 500px;
      `;

      modalContent.innerHTML = `
        <h3 style="margin: 0 0 16px 0; color: #333; font-size: 18px;">
          <i class="fas fa-square-root-alt"></i> Chèn công thức toán học
        </h3>
        <p style="margin: 0 0 16px 0; color: #666; font-size: 14px;">
          Nhập công thức LaTeX (ví dụ: \\frac{a}{b}, \\sqrt{x}, \\sum_{i=1}^{n})
        </p>
        <textarea 
          id="mathFormulaInput"
          placeholder="\\frac{a}{b}"
          style="width: 100%; height: 80px; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-family: monospace; font-size: 14px; resize: vertical;"
        ></textarea>
        <div style="margin-top: 16px; display: flex; gap: 8px; justify-content: flex-end;">
          <button id="cancelBtn" style="padding: 8px 16px; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 6px; cursor: pointer;">
            Hủy
          </button>
          <button id="insertBtn" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Chèn
          </button>
        </div>
      `;

      modal.appendChild(modalContent);
      document.body.appendChild(modal);

      const input = modal.querySelector('#mathFormulaInput');
      const cancelBtn = modal.querySelector('#cancelBtn');
      const insertBtn = modal.querySelector('#insertBtn');

      input.focus();

      const closeModal = () => {
        document.body.removeChild(modal);
      };

      cancelBtn.onclick = closeModal;
      insertBtn.onclick = () => {
        const formula = input.value.trim();
        if (formula) {
          const mathHtml = `<span class="math">\\(${formula}\\)</span>`;
          editor.insertContent(mathHtml);
          
          // Render MathJax
          this.$nextTick(() => {
            this.renderMathJax(editor);
          });
        }
        closeModal();
      };

      // Đóng modal khi click bên ngoài
      modal.onclick = (e) => {
        if (e.target === modal) closeModal();
      };

      // Đóng modal khi nhấn Escape
      const handleEscape = (e) => {
        if (e.key === 'Escape') {
          closeModal();
          document.removeEventListener('keydown', handleEscape);
        }
      };
      document.addEventListener('keydown', handleEscape);
    },

    images_upload_handler: function(blobInfo, success, failure) {
      var xhr, formData;
      xhr = new XMLHttpRequest();
      xhr.withCredentials = false;
      xhr.open("POST", "/api/upload-image", true);
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

<style scoped>
.tinymce-container {
  border-radius: 8px;
  overflow: hidden;
}

/* TinyMCE Custom Styles */
.tinymce-container >>> .tox-tinymce {
  border: none !important;
  border-radius: 8px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
}

.tinymce-container >>> .tox .tox-toolbar {
  background: #f8f9fa !important;
  border-bottom: 1px solid #e5e7eb !important;
  padding: 8px 12px !important;
}

.tinymce-container >>> .tox .tox-toolbar__group {
  padding: 0 4px !important;
}

.tinymce-container >>> .tox .tox-tbtn {
  background: transparent !important;
  border: 1px solid transparent !important;
  border-radius: 6px !important;
  margin: 0 2px !important;
  transition: all 0.2s ease !important;
}

.tinymce-container >>> .tox .tox-tbtn:hover {
  background: #e0e7ff !important;
  border-color: #4f46e5 !important;
  transform: translateY(-1px) !important;
}

.tinymce-container >>> .tox .tox-tbtn--enabled {
  background: #4f46e5 !important;
  color: #fff !important;
  border-color: #4f46e5 !important;
}

.tinymce-container >>> .tox .tox-tbtn--enabled:hover {
  background: #3730a3 !important;
  border-color: #3730a3 !important;
}

.tinymce-container >>> .tox .tox-edit-area {
  border: none !important;
}

.tinymce-container >>> .tox .tox-edit-area__iframe {
  background: #fff !important;
}

.tinymce-container >>> .tox .tox-editor-container {
  border-radius: 8px !important;
}

/* Custom button for math formulas */
.tinymce-container >>> .tox .tox-tbtn[aria-label="Chèn công thức toán học"] {
  background: #10b981 !important;
  color: #fff !important;
  border-color: #10b981 !important;
  font-weight: bold !important;
}

.tinymce-container >>> .tox .tox-tbtn[aria-label="Chèn công thức toán học"]:hover {
  background: #059669 !important;
  border-color: #059669 !important;
}

/* Content styling inside editor */
.tinymce-container >>> .tox .tox-edit-area iframe {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
  font-size: 14px !important;
  line-height: 1.6 !important;
}

/* Math formula styling */
.math {
  font-family: 'Times New Roman', serif !important;
  font-size: 16px !important;
  margin: 4px 0 !important;
}

/* Responsive design */
@media (max-width: 768px) {
  .tinymce-container >>> .tox .tox-toolbar {
    padding: 6px 8px !important;
  }
  
  .tinymce-container >>> .tox .tox-tbtn {
    margin: 0 1px !important;
    padding: 4px 6px !important;
  }
  
  .tinymce-container >>> .tox .tox-toolbar__group {
    padding: 0 2px !important;
  }
}
</style>