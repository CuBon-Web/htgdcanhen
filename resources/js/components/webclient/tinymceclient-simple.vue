<template>
  <div>
    <textarea :id="elementId"></textarea>
  </div>
</template>

<script>
let idSeed = 0;
export default {
  name: 'TinyMCEClientSimple',
  props: {
    value: String, // v-model
    init: {
      type: Object,
      default: () => ({})
    },
    height: {
      type: [Number, String],
      default: 400
    }
  },
  data() {
    return {
      editor: null,
      elementId: 'tinymce_' + (++idSeed)
    };
  },
  mounted() {
    // Load TinyMCE CDN if not loaded
    if (!window.tinymce) {
      const script = document.createElement('script');
      script.src = 'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js';
      script.referrerPolicy = 'origin';
      script.onload = this.initEditor;
      document.head.appendChild(script);
    } else {
      this.initEditor();
    }
  },
  beforeDestroy() {
    if (window.tinymce && this.editor) {
      window.tinymce.remove(this.editor);
    }
  },
  methods: {
    initEditor() {
      const self = this;
      // Loại bỏ các option icons, icons_url, skin_url, base_url nếu có trong init
      const cleanInit = { ...this.init };
      delete cleanInit.icons;
      delete cleanInit.icons_url;
      delete cleanInit.skin_url;
      delete cleanInit.base_url;
      cleanInit.icons = 'thin'; // ép TinyMCE dùng icon CDN
      window.tinymce.init({
        target: this.$el.querySelector('textarea'),
        height: this.height,
        ...cleanInit,
        setup(editor) {
          self.editor = editor;
          editor.on('init', () => {
            editor.setContent(self.value || '');
          });
          editor.on('Change KeyUp Undo Redo', () => {
            const content = editor.getContent();
            self.$emit('input', content);
            self.$emit('update:value', content);
          });
          // Bắt sự kiện double click vào công thức LaTeX
          editor.on('DblClick', function(e) {
            const selection = editor.selection;
            const content = editor.getContent({ format: 'text' });
            const selectedText = selection.getContent({ format: 'text' }).trim();
            // Regex tìm công thức LaTeX
            const regex = /\\\((.+?)\\\)|\\\[(.+?)\\\]|\$\$(.+?)\$\$|\$(.+?)\$/gs;
            let found = false;
            let match;
            // Ưu tiên: Nếu selection text là công thức LaTeX
            while ((match = regex.exec(content)) !== null) {
              const latex = match[1] || match[2] || match[3] || match[4] || '';
              if (selectedText && latex && selectedText === latex) {
                self.$emit('latex-dblclick', { latex, start: match.index, end: regex.lastIndex });
                found = true;
                break;
              }
            }
            // Nếu không, tìm công thức gần vị trí con trỏ
            if (!found) {
              let caretOffset = 0;
              const rng = selection.getRng();
              if (rng.startContainer && rng.startContainer.textContent) {
                caretOffset = content.indexOf(selectedText);
              }
              regex.lastIndex = 0;
              while ((match = regex.exec(content)) !== null) {
                if (caretOffset >= match.index && caretOffset <= regex.lastIndex) {
                  const latex = match[1] || match[2] || match[3] || match[4] || '';
                  self.$emit('latex-dblclick', { latex, start: match.index, end: regex.lastIndex });
                  break;
                }
              }
            }
          });
        }
      });
    }
  },
  watch: {
    value(val) {
      if (this.editor && this.editor.getContent() !== val) {
        this.editor.setContent(val || '');
      }
    }
  }
};
</script> 