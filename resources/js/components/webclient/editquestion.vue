<template>
    <div>
      <div class="row" style="margin-right: 0;">
        <!-- Cột trái: Preview -->
        <div class="col-lg-6 col-12">
          <div class="preview-box">
            <div class="preview-header">
              <span class="preview-title">Chỉnh sửa câu hỏi</span>
              <div class="header-actions">
                <button class="btn-action" @click="saveQuestion">
                  <i class="fas fa-save"></i> Lưu chỉnh sửa
                </button>
                <!-- <button class="btn-action" @click="parseContent">
                  <i class="fas fa-sync-alt"></i> Cập nhật
                </button> -->
              </div>
            </div>
            <div class="preview-content editor-content content-height">
                  <div class="question-header">
                    <span class="question-number">Câu {{ question.question_no }}.</span>
                    <span class="toolbar-divider"></span>
                    <span class="toolbar-btn">
                      Điểm:
                      <input type="number" step="0.01" min="0" v-model.number="question.score"
                        style="width: 50px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 2px 4px;">
                    </span>
                    <span class="toolbar-divider"></span>
                    <span class="toolbar-btn">
                      <template v-if="question.question_type === 'multiple_choice'">Trắc nghiệm</template>
                      <template v-else-if="question.question_type === 'true_false_grouped'">Đúng/Sai</template>
                      <template v-else-if="question.question_type === 'fill_in_blank'">Nhập đáp án</template>
                      <template v-else>Tự luận</template>
                    </span>
                    <span class="toolbar-divider"></span>
                    <span class="toolbar-btn">
                      <i class="fas fa-microphone"></i>
                      <!-- <span class="audio-upload-label" @click="triggerAudioInput(qIndex, partIndex)">Audio</span>
                      <input type="file" accept="audio/*" :ref="'audioInput_' + partIndex + '_' + qIndex"
                        @change="onAudioChange($event, partIndex, qIndex)" style="display: none;"> -->
                      
                    </span>
                    <div v-if="question.audio" style="margin: 6px 0;">
                      <audio :src="question.audio" controls style="height: 24px; vertical-align: middle;"></audio>
                    </div>
                    <!-- <span v-if="question.audio" @click.stop="playAudio(question.audio)" style="cursor: pointer; color: #4f46e5;">
                        <i class="fas fa-volume-up"></i>
                      </span> -->
                  </div>
  
                  <div class="question-text" v-html="renderContentWithMarkdownImages(question.content, question)"></div>
  
                  
  
                  <div v-if="question.answers && question.answers.length" class="answers">
                    <div class="answer" v-for="(ans, aIndex) in question.answers" :key="aIndex"
                      :class="{ correct: question.correct_answer && ans.label.toUpperCase() === question.correct_answer }">
                      <template v-if="question.question_type === 'multiple_choice'">
                        <span v-if="ans.is_correct === 1" style="color: #16a34a; font-size: 18px; margin-right: 4px;"><i
                            class="fas fa-check-circle"></i></span>
                      <span v-html="renderContentWithMarkdownImages(ans.label + '. ' + ans.content, question)"></span>
                    </template>
                    <template v-else-if="question.question_type === 'true_false_grouped'">
                      <span v-if="ans.is_correct === 1" style="color: #16a34a; font-size: 18px; margin-right: 4px;"><i
                          class="fas fa-check-circle"></i></span>
                      <span v-else style="color: #dc2626; font-size: 18px; margin-right: 4px;"><i
                          class="fas fa-times-circle"></i></span>
                      <span v-html="renderContentWithMarkdownImages(ans.label + ') ' + ans.content, question)"></span>
                    </template>
                    <template v-else-if="question.question_type === 'fill_in_blank'">
                      <span style="color: #16a34a; font-size: 18px; margin-right: 4px;"><i
                          class="fas fa-check-circle"></i></span>
                      <span v-html="renderContentWithMarkdownImages(ans.content, question)"></span>
                    </template>
                    <template v-else>
                      <!-- short_answer hoặc các loại khác -->
                      <span v-html="renderContentWithMarkdownImages(ans.content || ans.label, question)"></span>
                    </template>
                    </div>
                  </div>
                  <div v-if="question.explanation" class="explanation-block"
                    style="margin-top: 10px; background: #f8f9fa; border-left: 4px solid #4f46e5; padding: 10px 16px; border-radius: 6px;">
                    <div style="font-weight: bold; color: #4f46e5; margin-bottom: 4px;">Lời giải:</div>
                    <div v-html="renderContentWithMarkdownImages(question.explanation, question)"></div>
                  </div>
            </div>
          </div>
        </div>
  
        <!-- Cột phải: Sửa nội dung gốc -->
        <div class="col-lg-6 col-12">
          <div class="preview-box">
            <div class="preview-header">
              <span class="preview-title">Chỉnh sửa nội dung file gốc</span>
              <div class="header-actions">
                <button class="btn-action" @click="insertMathFormula">
                  <i class="fas fa-square-root-alt"></i> Công thức
                </button>
                <button class="btn-action" @click="showGuideModal = true">
                  <i class="fas fa-question-circle"></i> Hướng dẫn nhập liệu
                </button>
                <!-- <button class="btn-action" @click="parseContent">
                  <i class="fas fa-sync-alt"></i> Cập nhật
                </button> -->
              </div>
            </div>
            <div class="preview-content editor-content">
              <div ref="monacoEditor" class="monaco-editor-container"></div>
            </div>
          </div>
        </div>
      </div>
      <template v-if="showGuideModal">
        <div class="modal-mask" @click.self="showGuideModal = false">
        <div class="modal-wrapper">
          <div class="modal-container" style="max-width: 540px;">
            <h3 style="margin-bottom: 12px; color: #4f46e5; font-size: 18px;"><i class="fas fa-info-circle"></i> Hướng
              dẫn & Mô tả chức năng</h3>
            <div
              style="font-size: 15px; color: #222; line-height: 1.7; max-height: 60vh; overflow-y: auto; padding-right: 6px;">
              <b style="color: #4f46e5; font-size: 16px;">📋 Mô tả chức năng:</b><br>
              <ul style="margin: 8px 0; padding-left: 20px;">
                <li><b>Xem trước đề thi</b>: Hiển thị cấu trúc đề, các phần, câu hỏi, đáp án, lời giải, điểm số với preview trực quan.</li>
                <li><b>Chỉnh sửa nội dung</b>: Sửa trực tiếp nội dung đề thi dạng text/raw, tự động parse lại sang cấu trúc câu hỏi.</li>
                <li><b>Cấu hình thang điểm</b>: Chia điểm cho từng phần, cấu hình % điểm cho từng số ý đúng với phần đúng/sai nhóm.</li>
                <li><b>Paste ảnh trực tiếp</b>: Dán ảnh (Ctrl+V) vào bất kỳ vị trí nào trong editor, ảnh sẽ tự động được lưu và hiển thị.</li>
                <li><b>Chèn công thức toán học</b>: Hỗ trợ nhập/chỉnh sửa công thức LaTeX với preview trực quan (Ctrl+M hoặc nút √x).</li>
                <li><b>Upload audio</b>: Gắn file audio cho từng câu hỏi thông qua nút Audio trong toolbar.</li>
                <li><b>Bảo toàn điểm số</b>: Khi chỉnh sửa và parse lại, điểm số từng câu được giữ nguyên.</li>
                <li><b>Điều hướng nhanh</b>: Click vào bất kỳ vị trí nào trong editor, preview sẽ tự động scroll đến câu hỏi tương ứng.</li>
              </ul>
              <br>
              
              <b style="color: #4f46e5; font-size: 16px;">📝 Hướng dẫn nhập liệu:</b><br><br>
              
              <b>1️⃣ Câu hỏi trắc nghiệm (Multiple Choice):</b><br>
              <ul style="margin: 5px 0 15px 20px;">
                <li>Bắt đầu bằng <b>Câu X.</b> (ví dụ: Câu 1., Câu 2.)</li>
                <li>Đáp án ghi theo dạng <b>A. ...</b>, <b>B. ...</b>, <b>C. ...</b>, <b>D. ...</b></li>
                <li>Đáp án đúng đánh dấu <b>*</b> trước ký tự (ví dụ: <b>*A. Đáp án đúng</b>)</li>
              </ul>
              
              <b>2️⃣ Câu hỏi đúng/sai nhóm (True/False Grouped):</b><br>
              <ul style="margin: 5px 0 15px 20px;">
                <li>Bắt đầu bằng <b>Câu X.</b> như bình thường</li>
                <li>Đáp án ghi dạng <b>a) ...</b>, <b>b) ...</b>, <b>c) ...</b>, <b>d) ...</b> (chữ thường, có dấu ngoặc đơn)</li>
                <li>Đáp án đúng đánh dấu <b>*</b> trước ký tự (ví dụ: <b>*a) Đúng</b>)</li>
                <li>Có thể có nhiều đáp án đúng trong một câu hỏi</li>
              </ul>
              
              <b>3️⃣ Câu hỏi điền vào chỗ trống (Fill in the Blank):</b><br>
              <ul style="margin: 5px 0 15px 20px;">
                <li>Bắt đầu bằng <b>Câu X.</b></li>
                <li>Trong nội dung câu hỏi, đánh dấu vị trí cần điền bằng <b>[DT]</b></li>
                <li>Đáp án ghi trên dòng riêng sau câu hỏi (không cần prefix A/B/C/D)</li>
                <li>Ví dụ: "Tổng của 2 và 3 là [DT]." → Đáp án: "5"</li>
              </ul>
              
              <b>4️⃣ Câu hỏi tự luận (Short Answer/Essay):</b><br>
              <ul style="margin: 5px 0 15px 20px;">
                <li>Bắt đầu bằng <b>Câu X.</b></li>
                <li>Không có đáp án A/B/C/D hoặc a)/b)/c)/d)</li>
                <li>Học sinh sẽ tự nhập câu trả lời</li>
              </ul>
              
              <b>5️⃣ Lời giải:</b><br>
              <ul style="margin: 5px 0 15px 20px;">
                <li>Sau các đáp án, thêm dòng <b>Lời giải:</b> hoặc <b>Lời giải</b> rồi nhập nội dung</li>
                <li>Lời giải có thể chứa công thức toán học (LaTeX), ảnh, và text</li>
              </ul>
              
              <b>6️⃣ Phân chia phần (tùy chọn):</b><br>
              <ul style="margin: 5px 0 15px 20px;">
                <li>Đầu mỗi phần ghi <b>PHẦN X. [Tên phần]</b> (ví dụ: PHẦN I. Trắc nghiệm, PHẦN II. Tự luận)</li>
                <li>Có thể dùng số La Mã (I, II, III) hoặc số thường (1, 2, 3)</li>
                <li><b>Nếu không có phần:</b> Hệ thống tự động tạo "PHẦN I. Câu hỏi chung"</li>
              </ul>
              
              <b>7️⃣ Chèn ảnh:</b><br>
              <ul style="margin: 5px 0 15px 20px;">
                <li><b>Cách 1:</b> Copy ảnh (Ctrl+C) và dán trực tiếp vào editor (Ctrl+V) - ảnh sẽ tự động được lưu và hiển thị dưới dạng <code>[img:key]</code></li>
                <li><b>Cách 2:</b> Click vào nút "Ảnh" trong toolbar của câu hỏi để upload ảnh</li>
                <li>Ảnh có thể chèn vào câu hỏi, đáp án, hoặc lời giải</li>
              </ul>
              
              <b>8️⃣ Chèn công thức toán học:</b><br>
              <ul style="margin: 5px 0 15px 20px;">
                <li>Click vào nút <b>√x Công thức</b> hoặc nhấn <b>Ctrl+M</b></li>
                <li>Nhập công thức LaTeX (ví dụ: <code>\frac{a}{b}</code>, <code>x^2 + y^2 = z^2</code>)</li>
                <li>Hệ thống hỗ trợ cả công thức inline và block</li>
              </ul>
              <br>
              <b style="color: #4f46e5; font-size: 16px;">💡 Ví dụ đầy đủ:</b><br><br>
              
              <pre style="background:#f3f4f6; border-radius:6px; padding:10px; font-size:14px;">PHẦN I. Trắc nghiệm
Câu 1. Nội dung câu hỏi?
*A. Đáp án đúng
B. Đáp án sai
C. Đáp án sai
D. Đáp án sai
Lời giải: Đây là lời giải cho câu hỏi 1.

PHẦN II. Đúng/Sai nhóm
Câu 2. Nội dung câu hỏi?
*a) Đúng
b) Sai
c) Sai
Lời giải: Đây là lời giải cho câu hỏi 2.</pre>

              <b>Ví dụ không có phần:</b><br>
              <pre style="background:#f3f4f6; border-radius:6px; padding:10px; font-size:14px;">Câu 1. Tính giá trị của biểu thức: 2 + 3 × 4
*A. 14
B. 20
C. 24
D. 26
Lời giải: Thực hiện phép nhân trước: 3 × 4 = 12, sau đó cộng: 2 + 12 = 14.

Câu 2. Cho các phát biểu sau:
*a) Số 0 là số tự nhiên
b) Số âm không phải là số tự nhiên
c) Tập hợp số tự nhiên có vô hạn phần tử
Lời giải: Cả ba phát biểu đều đúng.</pre>
            </div>
            <div style="text-align: right; margin-top: 18px;">
              <button class="btn-action" style="background: #f3f4f6; color: #222; border: 1px solid #e5e7eb;"
                @click="showGuideModal = false">Đóng</button>
            </div>
          </div>
        </div>
      </div>
      </template>

    </div>
  </template>
  
  <script>
  import axios from "axios";
  import debounce from "lodash/debounce";
  import CodeMirror from 'codemirror';
  import 'codemirror/lib/codemirror.css';
  import 'codemirror/theme/monokai.css';
  import 'codemirror/mode/htmlmixed/htmlmixed.js';
  import 'codemirror/addon/edit/closebrackets.js';
  import 'codemirror/addon/edit/closetag.js';
  import 'codemirror/addon/edit/matchbrackets.js';
  import 'codemirror/addon/selection/active-line.js';
  import 'codemirror/addon/fold/foldcode.js';
  import 'codemirror/addon/fold/foldgutter.js';
  import 'codemirror/addon/fold/foldgutter.css';
  import 'codemirror/addon/fold/xml-fold.js';
  import 'codemirror/addon/search/search.js';
  import 'codemirror/addon/search/searchcursor.js';
  import 'codemirror/addon/dialog/dialog.js';
  import 'codemirror/addon/dialog/dialog.css';
  export default {
    name: "PreviewExam",
    props: {
      initialQuestion: {
        type: Object,
        required: true
      },
      rawContent: {
        type: String,
        default: ''
      },
      grade: {
        type: Array,
        default: function() { return []; }
      },
      examId: {
        type: Number,
        default: null
      }
    },
    data: function() {
      return {
        question: this.initialQuestion,
        examIdProps: this.examId,
        rawContentCopy: this.rawContent,
        codeMirrorEditor: null,
        showGuideModal: false,
        trueFalseScorePercent: { 1: 10, 2: 25, 3: 50, 4: 100 },
        partScores: {}, // { 'PHẦN I': 5, ... }
        examConfig: {
          title: '',
          grade: '',
          subject: '',
          pricing_type: 'free',
          price: 0,
          description: '',
          time: 0
        },
        isSavingExam: false,
        listSubject: [],
        subject: []
      };
    },
    computed: {
      isExamConfigValid() {
        return this.examConfig.title.trim() !== '' &&
          this.examConfig.grade !== '' &&
          this.examConfig.subject !== '' &&
          this.examConfig.pricing_type !== '' &&
          (this.examConfig.pricing_type === 'free' ||
            (this.examConfig.pricing_type === 'paid' && this.examConfig.price > 0));
      }
    },
    watch: {
      rawContentCopy: {
        handler(newVal) {
          // Sync với CodeMirror nếu có
          if (this.codeMirrorEditor && this.codeMirrorEditor.getValue() !== newVal) {
            this.codeMirrorEditor.setValue(newVal);
          }
          // Trigger parse
          this.debouncedParseContent();
        }
      }
    },
    mounted() {
        console.log(this.question);
      this.initCodeMirror();
      this.loadFontAwesome();
      // Nếu là sửa đề thi, có thể cần load subject theo grade
      if (this.examConfig && this.examConfig.grade) {
        this.changeGrade();
      }
      // Attach image click handlers
      this.$nextTick(() => {
        this.attachImageClickHandlers();
      });
    },
    beforeDestroy() {
      this.destroyCodeMirror();
    },
    methods: {
      initCodeMirror() {
        this.$nextTick(() => {
          if (this.$refs.monacoEditor && !this.codeMirrorEditor) {
            this.codeMirrorEditor = CodeMirror(this.$refs.monacoEditor, {
              value: this.rawContentCopy,
              mode: 'htmlmixed',
              theme: 'default',
              lineNumbers: true,
              lineWrapping: true,
              autoCloseBrackets: true,
              autoCloseTags: true,
              matchBrackets: true,
              styleActiveLine: true,
              foldGutter: true,
              gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
              tabSize: 2,
              indentUnit: 2,
              indentWithTabs: false,
              extraKeys: {
                'Ctrl-S': () => { this.saveQuestion(); },
                'Cmd-S': () => { this.saveQuestion(); },
                'Ctrl-F': 'findPersistent',
                'Cmd-F': 'findPersistent',
                'Ctrl-H': 'replace',
                'Cmd-Alt-F': 'replace',
                'Ctrl-M': () => { this.insertMathFormula(); },
                'Cmd-M': () => { this.insertMathFormula(); }
              }
            });

            // Lắng nghe thay đổi nội dung
            this.codeMirrorEditor.on('change', debounce(() => {
              this.rawContentCopy = this.codeMirrorEditor.getValue();
            }, 300));

            // Auto refresh khi hiển thị
            setTimeout(() => {
              if (this.codeMirrorEditor) {
                this.codeMirrorEditor.refresh();
              }
            }, 100);

            // Thêm event double-click cho LaTeX editing
            this.codeMirrorEditor.on('dblclick', (cm, event) => {
              this.handleCodeMirrorLatexDblClick(cm, event);
            });

            // Thêm event listener cho paste ảnh
            this.codeMirrorEditor.on('paste', (cm, event) => {
              this.handlePasteImage(cm, event);
            });
          }
        });
      },

      destroyCodeMirror() {
        if (this.codeMirrorEditor) {
          this.codeMirrorEditor.toTextArea();
          this.codeMirrorEditor = null;
        }
      },

      // Xử lý paste ảnh vào editor
      async handlePasteImage(cm, event) {
        const clipboardData = event.clipboardData || window.clipboardData;
        if (!clipboardData) return;

        // Kiểm tra xem có file ảnh trong clipboard không
        const items = clipboardData.items;
        if (!items) return;

        let hasImage = false;
        let imageFile = null;

        // Tìm file ảnh trong clipboard
        for (let i = 0; i < items.length; i++) {
          if (items[i].type.indexOf('image') !== -1) {
            imageFile = items[i].getAsFile();
            hasImage = true;
            break;
          }
        }

        if (!hasImage || !imageFile) {
          // Không có ảnh, cho phép paste bình thường
          return;
        }

        // Ngăn chặn paste mặc định
        event.preventDefault();
        event.stopPropagation();

        // Kiểm tra kích thước file (tối đa 5MB)
        if (imageFile.size > 5 * 1024 * 1024) {
          alert('File ảnh quá lớn. Kích thước tối đa là 5MB.');
          return;
        }

        // Kiểm tra loại file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(imageFile.type)) {
          alert('File không phải là ảnh hợp lệ. Chỉ chấp nhận JPEG, PNG, GIF, WebP.');
          return;
        }

        // Lấy vị trí cursor hiện tại
        const cursor = cm.getCursor();
        const loadingText = '[img:loading...]';
        
        // Chèn loading text vào vị trí cursor
        cm.replaceRange(loadingText, cursor);
        
        // Lưu vị trí bắt đầu của loading text để sau này thay thế
        const loadingStartPos = cursor;
        const loadingEndPos = {
          line: cursor.line,
          ch: cursor.ch + loadingText.length
        };

        try {
          // Upload ảnh lên server
          const formData = new FormData();
          formData.append('image', imageFile);

          const response = await axios.post('/api/upload-question-image', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });

          if (response.data.success) {
            const imageUrl = response.data.url;
            // Extract img_key từ filename (bỏ extension)
            const imgKey = this.extractImageKeyFromUrl(imageUrl);
            const imageMarkdown = `[img:${imgKey}]`;
            
            // Thay thế loading text bằng markdown image
            cm.replaceRange(imageMarkdown, loadingStartPos, loadingEndPos);
            
            // Di chuyển cursor sau ảnh
            const finalCursor = {
              line: loadingStartPos.line,
              ch: loadingStartPos.ch + imageMarkdown.length
            };
            cm.setCursor(finalCursor);
            cm.focus();

            // Cập nhật rawContentCopy để trigger parse
            this.rawContentCopy = cm.getValue();
            
            // Trigger parse để cập nhật preview với ảnh mới
            this.$nextTick(() => {
              this.parseContent();
            });
          } else {
            // Xóa loading text nếu upload thất bại
            cm.replaceRange('', loadingStartPos, loadingEndPos);
            alert(`Lỗi upload ảnh: ${response.data.error || 'Vui lòng thử lại.'}`);
          }
        } catch (error) {
          console.error('Upload error:', error);
          // Xóa loading text nếu upload thất bại
          cm.replaceRange('', loadingStartPos, loadingEndPos);
          alert('Lỗi upload ảnh. Vui lòng thử lại.');
        }
      },

      // Handle double-click in CodeMirror for LaTeX editing
      handleCodeMirrorLatexDblClick(cm, event) {
        const pos = cm.coordsChar({ left: event.clientX, top: event.clientY });
        const line = cm.getLine(pos.line);
        const fullText = cm.getValue();
        
        // Tìm vị trí trong toàn bộ text
        let charPos = 0;
        for (let i = 0; i < pos.line; i++) {
          charPos += cm.getLine(i).length + 1; // +1 for newline
        }
        charPos += pos.ch;

        // Tìm công thức LaTeX gần vị trí click
        const regex = /\\\((.+?)\\\)|\\\[(.+?)\\\]|\$\$(.+?)\$\$|\$(.+?)\$/gs;
        let match;
        let found = null;

        while ((match = regex.exec(fullText)) !== null) {
          const start = match.index;
          const end = match.index + match[0].length;
          
          if (charPos >= start && charPos <= end) {
            found = {
              full: match[0],
              formula: match[1] || match[2] || match[3] || match[4],
              start: start,
              end: end,
              type: match[0].startsWith('\\(') ? 'inline' : 
                    match[0].startsWith('\\[') ? 'block' :
                    match[0].startsWith('$$') ? 'block-dollar' : 'inline-dollar'
            };
            break;
          }
        }

        if (found) {
          this.showMathFormulaEditor(found, cm);
        }
      },

      changeGrade() {
        axios.post('/de-thi/filter-subject', {grade: this.examConfig.grade})
          .then(response => {
  
            this.subject = response.data;
          })
          .catch(error => {
            console.log(error);
          });
      },
      loadFontAwesome() {
        // Load Font Awesome nếu chưa có
        if (!document.querySelector('link[href*="font-awesome"]')) {
          const fontAwesome = document.createElement('link');
          fontAwesome.rel = 'stylesheet';
          fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
          document.head.appendChild(fontAwesome);
        }
      },
  
      // Insert new Math formula at cursor position
      insertMathFormula() {
        if (!this.codeMirrorEditor) {
          console.error('CodeMirror editor is not initialized');
          alert('Editor chưa sẵn sàng, vui lòng thử lại');
          return;
        }

        try {
          const cursor = this.codeMirrorEditor.getCursor();
          const latexData = {
            formula: '',
            start: this.codeMirrorEditor.indexFromPos(cursor),
            end: this.codeMirrorEditor.indexFromPos(cursor),
            type: 'inline'
          };

          this.showMathFormulaEditor(latexData, this.codeMirrorEditor, true);
        } catch (error) {
          console.error('Error in insertMathFormula:', error);
          alert('Có lỗi xảy ra: ' + error.message);
        }
      },
      validateQuestions() {
        for (let partIndex = 0; partIndex < this.questions.length; partIndex++) {
          const part = this.questions[partIndex];
          for (let questionIndex = 0; questionIndex < part.questions.length; questionIndex++) {
            const question = part.questions[questionIndex];
  
            // Kiểm tra điểm số
            if (!question.score || question.score <= 0) {
              return {
                isValid: false,
                message: `Vui lòng nhập điểm cho câu ${question.question_no} trong ${part.part}`
              };
            }
  
            // Kiểm tra đáp án đúng cho câu hỏi trắc nghiệm
            if (question.question_type === 'multiple_choice') {
              if (!question.correct_answer) {
                return {
                  isValid: false,
                  message: `Vui lòng chọn đáp án đúng cho câu ${question.question_no} trong ${part.part}`
                };
              }
  
              // Kiểm tra có ít nhất 2 đáp án
              if (!question.answers || question.answers.length < 2) {
                return {
                  isValid: false,
                  message: `Câu ${question.question_no} trong ${part.part} phải có ít nhất 2 đáp án`
                };
              }
            }
  
  
          }
        }
  
        return { isValid: true, message: '' };
      },
      showErrorAlert(message) {
        // Tạo modal alert lỗi đẹp
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
          animation: fadeIn 0.3s ease;
        `;
  
        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
          background: white;
          padding: 24px;
          border-radius: 12px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.2);
          max-width: 400px;
          width: 90%;
          text-align: center;
          animation: slideIn 0.3s ease;
        `;
  
        modalContent.innerHTML = `
          <div style="margin-bottom: 16px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #dc2626;"></i>
          </div>
          <h3 style="margin: 0 0 12px 0; color: #dc2626; font-size: 18px; font-weight: 600;">
            Lỗi
          </h3>
          <p style="margin: 0 0 20px 0; color: #374151; font-size: 14px; line-height: 1.5;">
            ${message}
          </p>
          <button id="closeBtn" style="
            padding: 10px 24px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
          " onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
            Đóng
          </button>
        `;
  
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
  
        // Thêm CSS animations
        const style = document.createElement('style');
        style.textContent = `
          @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
          }
          @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
          }
        `;
        document.head.appendChild(style);
  
        const closeModal = () => {
          document.body.removeChild(modal);
          document.head.removeChild(style);
        };
  
        modal.querySelector('#closeBtn').onclick = closeModal;
        modal.onclick = (e) => {
          if (e.target === modal) closeModal();
        };
  
        // Tự động đóng sau 5 giây
        setTimeout(closeModal, 5000);
      },
      showSuccessAlert(message) {
        // Tạo modal alert thành công đẹp
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
          animation: fadeIn 0.3s ease;
        `;
  
        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
          background: white;
          padding: 24px;
          border-radius: 12px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.2);
          max-width: 400px;
          width: 90%;
          text-align: center;
          animation: slideIn 0.3s ease;
        `;
  
        modalContent.innerHTML = `
          <div style="margin-bottom: 16px;">
            <i class="fas fa-check-circle" style="font-size: 48px; color: #16a34a;"></i>
          </div>
          <h3 style="margin: 0 0 12px 0; color: #16a34a; font-size: 18px; font-weight: 600;">
            Thành công
          </h3>
          <p style="margin: 0 0 20px 0; color: #374151; font-size: 14px; line-height: 1.5;">
            ${message}
          </p>
          <button id="closeBtn" style="
            padding: 10px 24px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
          " onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            Đóng
          </button>
        `;
  
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
  
        // Thêm CSS animations
        const style = document.createElement('style');
        style.textContent = `
          @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
          }
          @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
          }
        `;
        document.head.appendChild(style);
  
        const closeModal = () => {
          document.body.removeChild(modal);
          document.head.removeChild(style);
        };
  
        modal.querySelector('#closeBtn').onclick = closeModal;
        modal.onclick = (e) => {
          if (e.target === modal) closeModal();
        };
  
        // Tự động đóng sau 3 giây
        setTimeout(closeModal, 3000);
      },
      saveQuestion() {
        this.isSavingExam = true;
        console.log(this.question);
        axios.post('/de-thi/sua-cau-hoi.html', {question: this.question})
          .then(response => {
            this.isSavingExam = false;
            if (response.data.success) {
              this.showSuccessAlert('Lưu câu hỏi thành công!');
              window.location.href = '/de-thi/sua-de-thi/'+this.examIdProps+'.html';
            } else {
              this.showErrorAlert('Có lỗi xảy ra: ' + response.data.error);
            }
          })
          .catch(error => {
            this.isSavingExam = false;
            this.showErrorAlert('Có lỗi xảy ra khi lưu câu hỏi!');
          });
      },
      normalizeText(text) {
        return text
          .normalize('NFC') // chuẩn hóa Unicode tổ hợp
          .replace(/\u00A0/g, ' ') // non-breaking space -> space
          .replace(/\r\n|\r/g, '\n') // CRLF, CR -> LF
          .replace(/[\t\v\f]/g, '') // tab, vertical tab, form feed -> remove
          .replace(/ +/g, ' '); // nhiều space -> 1 space
      },
  
      parseContent() {
        const normalized = this.normalizeText(this.rawContentCopy);
        const answerIds = this.question.answers.map(answer => answer.id);
        axios
          .post("/api/parse-content-question", {
            rawContent: normalized,
            id: this.question.id,
            answerIds: answerIds
          })
          .then(res => {
           
            this.question = res.data.question;
            this.question.id = res.data.id;
          })
          .catch(err => {
            console.error("Lỗi khi parse:", err);
          });
      },
  
      debouncedParseContent: debounce(function () {
        this.parseContent();
      }, 500),
  
      renderMath(content) {
        return content;
      },
      
      // Extract img_key từ URL (filename không có extension)
      extractImageKeyFromUrl(url) {
        if (!url) return '';
        // Lấy filename từ URL, ví dụ: /exam_images/08df36e3-1eb1-4b8a-b3ed-965b8679ecfc.png
        const filename = url.split('/').pop() || '';
        // Bỏ extension
        const imgKey = filename.replace(/\.[^.]+$/, '');
        return imgKey;
      },
      
      // Render content với images inline - convert [img:key] thành HTML img tags
      renderContentWithMarkdownImages(content, question = null) {
        if (!content) return '';
        
        // Pattern để match cả markdown cũ và format mới
        const oldImagePattern = /!\[([^\]]*)\]\(([^)]+)\)/g;
        const newImagePattern = /\[img:([^\]]+)\]/g;
        
        // Xử lý format cũ (backward compatibility)
        let renderedContent = content.replace(oldImagePattern, (match, alt, url) => {
          const normalizedUrl = url.replace(/['"]/g, '').trim().replace(/^\/+/, '/');
          const safeAlt = (alt || 'Image').replace(/"/g, '&quot;');
          return `<img src="${normalizedUrl}" alt="${safeAlt}" style="max-width: 100%; height: auto; border-radius: 8px; display: block;" class="question-markdown-image" />`;
        });
        
        // Xử lý format mới [img:key]
        renderedContent = renderedContent.replace(newImagePattern, (match, imgKey) => {
          let imageUrl = null;
          
          // Tìm URL từ images array của question nếu có
          if (question && question.images && question.images.length > 0) {
            const foundImage = question.images.find(img => {
              const url = img.url || img;
              const key = this.extractImageKeyFromUrl(url);
              return key === imgKey;
            });
            
            if (foundImage) {
              imageUrl = foundImage.url || foundImage;
            }
          }
          
          // Nếu không tìm thấy, reconstruct URL từ img_key
          if (!imageUrl) {
            imageUrl = `/exam_images/${imgKey}.png`; // Default extension
          }
          
          const safeAlt = 'Image';
          return `<img src="${imageUrl}" alt="${safeAlt}" style="max-width: 100%; height: auto; border-radius: 8px; display: block;" class="question-markdown-image" />`;
        });
        
        // Render MathJax sau khi đã convert images
        renderedContent = this.renderMath(renderedContent);
        
        return renderedContent;
      },
      // Show Math Formula editor modal
      showMathFormulaEditor(latexData, cm, isNewFormula = false) {
        const modal = document.createElement('div');
        modal.style.cssText = `
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.7);
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 10000;
        `;

        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
          background: white;
          padding: 32px;
          border-radius: 12px;
          width: 90%;
          max-width: 600px;
          box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        `;

        const title = document.createElement('h3');
        title.textContent = isNewFormula ? 'Chèn công thức LaTeX' : 'Chỉnh sửa công thức LaTeX';
        title.style.cssText = 'margin: 0 0 20px 0; color: #1f2937; font-size: 20px; font-weight: 600;';

        // Type selector
        const typeContainer = document.createElement('div');
        typeContainer.style.cssText = 'margin-bottom: 16px;';

        const typeLabel = document.createElement('label');
        typeLabel.textContent = 'Loại công thức:';
        typeLabel.style.cssText = 'display: block; margin-bottom: 8px; font-weight: 500; color: #374151;';

        const typeSelect = document.createElement('select');
        typeSelect.style.cssText = `
          width: 100%;
          padding: 10px 12px;
          border: 2px solid #e5e7eb;
          border-radius: 8px;
          font-size: 14px;
          margin-bottom: 16px;
          cursor: pointer;
        `;

        typeSelect.innerHTML = `
          <option value="inline">Inline \\( ... \\)</option>
          <option value="block">Block \\[ ... \\]</option>
          <option value="inline-dollar">Inline $ ... $</option>
          <option value="block-dollar">Block $$ ... $$</option>
        `;
        typeSelect.value = latexData.type;

        typeSelect.addEventListener('change', (e) => {
          latexData.type = e.target.value;
        });

        typeContainer.appendChild(typeLabel);
        typeContainer.appendChild(typeSelect);

        // Category filter tabs
        const categoryTabsContainer = document.createElement('div');
        categoryTabsContainer.style.cssText = 'margin-bottom: 12px;';

        const categoryLabel = document.createElement('label');
        categoryLabel.textContent = 'Môn học:';
        categoryLabel.style.cssText = 'display: block; margin-bottom: 8px; font-weight: 500; color: #374151;';

        const categoryTabs = document.createElement('div');
        categoryTabs.style.cssText = `
          display: flex;
          gap: 8px;
          margin-bottom: 12px;
        `;

        let selectedCategory = 'all';
        const categories = [
          { value: 'all', label: 'Tất cả', color: '#6366f1' },
          { value: 'math', label: 'Toán học', color: '#3b82f6' },
          { value: 'physics', label: 'Vật lý', color: '#8b5cf6' },
          { value: 'chemistry', label: 'Hóa học', color: '#10b981' }
        ];

        // Quick templates
        const templatesLabel = document.createElement('label');
        templatesLabel.textContent = 'Công thức mẫu:';
        templatesLabel.style.cssText = 'display: block; margin-bottom: 8px; font-weight: 500; color: #374151;';

        const templatesContainer = document.createElement('div');
        templatesContainer.style.cssText = `
          display: flex;
          gap: 8px;
          flex-wrap: wrap;
          margin-bottom: 16px;
          max-height: 300px;
          overflow-y: auto;
          padding: 8px;
          border: 1px solid #e5e7eb;
          border-radius: 8px;
          background: #f9fafb;
        `;

        const templates = [
          // Toán học cơ bản
          { label: 'Phân số', formula: '\\frac{a}{b}', category: 'math' },
          { label: 'Căn bậc hai', formula: '\\sqrt{x}', category: 'math' },
          { label: 'Căn bậc n', formula: '\\sqrt[n]{x}', category: 'math' },
          { label: 'Lũy thừa', formula: 'x^{n}', category: 'math' },
          { label: 'Chỉ số dưới', formula: 'x_{i}', category: 'math' },
          { label: 'Giá trị tuyệt đối', formula: '|x|', category: 'math' },
          
          // Tích phân & Đạo hàm
          { label: 'Tích phân', formula: '\\int_{a}^{b} f(x) dx', category: 'math' },
          { label: 'Tích phân kép', formula: '\\iint_{D} f(x,y) dxdy', category: 'math' },
          { label: 'Đạo hàm', formula: '\\frac{df}{dx}', category: 'math' },
          { label: 'Đạo hàm riêng', formula: '\\frac{\\partial f}{\\partial x}', category: 'math' },
          
          // Tổng & Tích
          { label: 'Tổng', formula: '\\sum_{i=1}^{n} x_i', category: 'math' },
          { label: 'Tích', formula: '\\prod_{i=1}^{n} x_i', category: 'math' },
          { label: 'Giới hạn', formula: '\\lim_{x \\to \\infty} f(x)', category: 'math' },
          
          // Ma trận
          { label: 'Ma trận 2x2', formula: '\\begin{pmatrix} a & b \\\\ c & d \\end{pmatrix}', category: 'math' },
          { label: 'Ma trận 3x3', formula: '\\begin{pmatrix} a & b & c \\\\ d & e & f \\\\ g & h & i \\end{pmatrix}', category: 'math' },
          { label: 'Định thức', formula: '\\begin{vmatrix} a & b \\\\ c & d \\end{vmatrix}', category: 'math' },
          
          // Hệ phương trình
          { label: 'Hệ PT', formula: '\\begin{cases} x + y = 5 \\\\ x - y = 1 \\end{cases}', category: 'math' },
          
          // Hình học
          { label: 'Góc', formula: '\\angle ABC', category: 'math' },
          { label: 'Tam giác', formula: '\\triangle ABC', category: 'math' },
          { label: 'Vuông góc', formula: 'AB \\perp CD', category: 'math' },
          { label: 'Song song', formula: 'AB \\parallel CD', category: 'math' },
          { label: 'Vector', formula: '\\vec{AB}', category: 'math' },
          { label: 'Độ dài vector', formula: '|\\vec{v}|', category: 'math' },
          
          // Tập hợp
          { label: 'Thuộc', formula: 'x \\in A', category: 'math' },
          { label: 'Không thuộc', formula: 'x \\notin A', category: 'math' },
          { label: 'Hợp', formula: 'A \\cup B', category: 'math' },
          { label: 'Giao', formula: 'A \\cap B', category: 'math' },
          { label: 'Tập con', formula: 'A \\subset B', category: 'math' },
          { label: 'Tập rỗng', formula: '\\emptyset', category: 'math' },
          
          // Vật lý - Cơ học
          { label: 'Vận tốc', formula: 'v = \\frac{s}{t}', category: 'physics' },
          { label: 'Gia tốc', formula: 'a = \\frac{\\Delta v}{\\Delta t}', category: 'physics' },
          { label: 'Lực', formula: 'F = ma', category: 'physics' },
          { label: 'Động lượng', formula: 'p = mv', category: 'physics' },
          { label: 'Công', formula: 'A = F \\cdot s \\cdot \\cos\\alpha', category: 'physics' },
          { label: 'Động năng', formula: 'E_k = \\frac{1}{2}mv^2', category: 'physics' },
          { label: 'Thế năng', formula: 'E_p = mgh', category: 'physics' },
          { label: 'Định luật vạn vật hấp dẫn', formula: 'F = G\\frac{m_1 m_2}{r^2}', category: 'physics' },
          
          // Vật lý - Điện học
          { label: 'Định luật Ohm', formula: 'U = IR', category: 'physics' },
          { label: 'Công suất', formula: 'P = UI', category: 'physics' },
          { label: 'Điện trở tương đương nối tiếp', formula: 'R_{td} = R_1 + R_2 + R_3', category: 'physics' },
          { label: 'Điện trở song song', formula: '\\frac{1}{R_{td}} = \\frac{1}{R_1} + \\frac{1}{R_2}', category: 'physics' },
          { label: 'Năng lượng điện', formula: 'W = UIt', category: 'physics' },
          { label: 'Lực Lorentz', formula: 'F = qvB\\sin\\alpha', category: 'physics' },
          
          // Vật lý - Quang học
          { label: 'Định luật khúc xạ', formula: '\\frac{\\sin i}{\\sin r} = n', category: 'physics' },
          { label: 'Tiêu cự thấu kính', formula: '\\frac{1}{f} = \\frac{1}{d} + \\frac{1}{d\'}', category: 'physics' },
          { label: 'Số phóng đại', formula: 'k = \\frac{h\'}{h} = \\frac{d\'}{d}', category: 'physics' },
          
          // Vật lý - Nhiệt học
          { label: 'Nhiệt lượng', formula: 'Q = mc\\Delta t', category: 'physics' },
          { label: 'Phương trình trạng thái', formula: '\\frac{pV}{T} = const', category: 'physics' },
          
          // Vật lý - Dao động sóng
          { label: 'Chu kỳ', formula: 'T = \\frac{1}{f}', category: 'physics' },
          { label: 'Tần số góc', formula: '\\omega = 2\\pi f', category: 'physics' },
          { label: 'Phương trình dao động', formula: 'x = A\\cos(\\omega t + \\varphi)', category: 'physics' },
          { label: 'Vận tốc dao động', formula: 'v = -A\\omega\\sin(\\omega t + \\varphi)', category: 'physics' },
          { label: 'Bước sóng', formula: '\\lambda = \\frac{v}{f}', category: 'physics' },
          
          // Hóa học - Cơ bản
          { label: 'Phân tử H₂O', formula: 'H_2O', category: 'chemistry' },
          { label: 'Ion', formula: 'SO_4^{2-}', category: 'chemistry' },
          { label: 'Phương trình hóa học', formula: '2H_2 + O_2 \\rightarrow 2H_2O', category: 'chemistry' },
          { label: 'Cân bằng hóa học', formula: 'A + B \\rightleftharpoons C + D', category: 'chemistry' },
          
          // Hóa học - Nhiệt động
          { label: 'Entalpy', formula: '\\Delta H = H_{sp} - H_{cd}', category: 'chemistry' },
          { label: 'Entropy', formula: '\\Delta S = S_{sp} - S_{cd}', category: 'chemistry' },
          { label: 'Năng lượng Gibbs', formula: '\\Delta G = \\Delta H - T\\Delta S', category: 'chemistry' },
          
          // Hóa học - Nồng độ
          { label: 'Nồng độ mol', formula: 'C_M = \\frac{n}{V}', category: 'chemistry' },
          { label: 'Nồng độ %', formula: 'C\\% = \\frac{m_{ct}}{m_{dd}} \\times 100\\%', category: 'chemistry' },
          { label: 'Số mol', formula: 'n = \\frac{m}{M}', category: 'chemistry' },
          
          // Hóa học - pH
          { label: 'pH', formula: 'pH = -\\log[H^+]', category: 'chemistry' },
          { label: 'pOH', formula: 'pOH = -\\log[OH^-]', category: 'chemistry' },
          { label: 'pH + pOH', formula: 'pH + pOH = 14', category: 'chemistry' },
          
          // Hóa học - Cân bằng
          { label: 'Hằng số cân bằng Kc', formula: 'K_c = \\frac{[C]^c[D]^d}{[A]^a[B]^b}', category: 'chemistry' },
          { label: 'Hằng số phân ly Ka', formula: 'K_a = \\frac{[H^+][A^-]}{[HA]}', category: 'chemistry' },
          
          // Hóa học - Điện hóa
          { label: 'Điện thế chuẩn', formula: 'E^0_{cell} = E^0_{cathode} - E^0_{anode}', category: 'chemistry' },
          { label: 'Định luật Faraday', formula: 'm = \\frac{AIt}{nF}', category: 'chemistry' }
        ];

        // Function to render template buttons based on selected category
        const renderTemplates = (category) => {
          templatesContainer.innerHTML = '';
          const filteredTemplates = category === 'all' 
            ? templates 
            : templates.filter(t => t.category === category);

          if (filteredTemplates.length === 0) {
            const noDataMsg = document.createElement('div');
            noDataMsg.textContent = 'Không có công thức nào';
            noDataMsg.style.cssText = 'color: #9ca3af; font-style: italic; padding: 20px; text-align: center; width: 100%;';
            templatesContainer.appendChild(noDataMsg);
            return;
          }

          filteredTemplates.forEach(template => {
            const btn = document.createElement('button');
            btn.textContent = template.label;
            btn.type = 'button';
            btn.style.cssText = `
              padding: 6px 12px;
              background: #fff;
              border: 1px solid #d1d5db;
              border-radius: 6px;
              font-size: 12px;
              cursor: pointer;
              transition: all 0.2s;
              white-space: nowrap;
            `;

            btn.addEventListener('mouseenter', () => {
              btn.style.background = '#e0e7ff';
              btn.style.borderColor = '#6366f1';
              btn.style.color = '#4f46e5';
            });

            btn.addEventListener('mouseleave', () => {
              btn.style.background = '#fff';
              btn.style.borderColor = '#d1d5db';
              btn.style.color = '#000';
            });

            btn.addEventListener('click', () => {
              input.value = template.formula;
              updatePreview(template.formula);
              input.focus();
            });

            templatesContainer.appendChild(btn);
          });
        };

        // Create category tab buttons
        categories.forEach(cat => {
          const tabBtn = document.createElement('button');
          tabBtn.textContent = cat.label;
          tabBtn.type = 'button';
          tabBtn.style.cssText = `
            padding: 8px 16px;
            background: ${cat.value === selectedCategory ? cat.color : '#f3f4f6'};
            color: ${cat.value === selectedCategory ? '#fff' : '#374151'};
            border: 2px solid ${cat.value === selectedCategory ? cat.color : '#e5e7eb'};
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
          `;

          tabBtn.addEventListener('click', () => {
            selectedCategory = cat.value;
            // Update all tab buttons
            categoryTabs.querySelectorAll('button').forEach((btn, idx) => {
              const category = categories[idx];
              btn.style.background = category.value === selectedCategory ? category.color : '#f3f4f6';
              btn.style.color = category.value === selectedCategory ? '#fff' : '#374151';
              btn.style.borderColor = category.value === selectedCategory ? category.color : '#e5e7eb';
            });
            renderTemplates(selectedCategory);
          });

          categoryTabs.appendChild(tabBtn);
        });

        categoryTabsContainer.appendChild(categoryLabel);
        categoryTabsContainer.appendChild(categoryTabs);

        // Initial render
        renderTemplates(selectedCategory);

        const inputLabel = document.createElement('label');
        inputLabel.textContent = 'Công thức LaTeX:';
        inputLabel.style.cssText = 'display: block; margin-bottom: 8px; font-weight: 500; color: #374151;';

        const input = document.createElement('input');
        input.type = 'text';
        input.value = latexData.formula;
        input.style.cssText = `
          width: 100%;
          padding: 12px 16px;
          border: 2px solid #e5e7eb;
          border-radius: 8px;
          font-size: 14px;
          font-family: 'Consolas', 'Courier New', monospace;
          margin-bottom: 16px;
          transition: all 0.2s;
        `;

        const preview = document.createElement('div');
        preview.style.cssText = `
          padding: 20px;
          background: #f9fafb;
          border: 1px solid #e5e7eb;
          border-radius: 8px;
          min-height: 80px;
          font-size: 16px;
          margin-bottom: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
        `;

        const updatePreview = (formula) => {
          if (formula.trim()) {
            preview.innerHTML = '\\(' + formula + '\\)';
            if (window.MathJax && window.MathJax.typesetPromise) {
              window.MathJax.typesetPromise([preview]).catch((err) => {
                console.error('MathJax error:', err);
                preview.innerHTML = '<span style="color: #dc2626;">Lỗi: ' + formula + '</span>';
              });
            }
          } else {
            preview.innerHTML = '<span style="color: #999; font-style: italic;">Nhập công thức để xem preview...</span>';
          }
        };

        updatePreview(latexData.formula);

        input.addEventListener('input', (e) => {
          updatePreview(e.target.value);
        });

        input.addEventListener('focus', () => {
          input.style.borderColor = '#3b82f6';
          input.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)';
        });

        input.addEventListener('blur', () => {
          input.style.borderColor = '#e5e7eb';
          input.style.boxShadow = 'none';
        });

        const btnContainer = document.createElement('div');
        btnContainer.style.cssText = 'display: flex; gap: 12px; justify-content: flex-end;';

        const btnSave = document.createElement('button');
        btnSave.textContent = 'Lưu';
        btnSave.style.cssText = `
          padding: 10px 24px;
          background: #3b82f6;
          color: white;
          border: none;
          border-radius: 8px;
          font-size: 14px;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.2s;
        `;

        const btnCancel = document.createElement('button');
        btnCancel.textContent = 'Hủy';
        btnCancel.style.cssText = `
          padding: 10px 24px;
          background: #6b7280;
          color: white;
          border: none;
          border-radius: 8px;
          font-size: 14px;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.2s;
        `;

        btnSave.addEventListener('mouseenter', () => {
          btnSave.style.background = '#2563eb';
          btnSave.style.transform = 'translateY(-1px)';
        });

        btnSave.addEventListener('mouseleave', () => {
          btnSave.style.background = '#3b82f6';
          btnSave.style.transform = 'translateY(0)';
        });

        btnCancel.addEventListener('mouseenter', () => {
          btnCancel.style.background = '#4b5563';
          btnCancel.style.transform = 'translateY(-1px)';
        });

        btnCancel.addEventListener('mouseleave', () => {
          btnCancel.style.background = '#6b7280';
          btnCancel.style.transform = 'translateY(0)';
        });

        btnSave.addEventListener('click', () => {
          const newFormula = input.value;
          let newLatex = '';
          
          // Giữ nguyên format của công thức gốc
          if (latexData.type === 'inline') {
            newLatex = '\\(' + newFormula + '\\)';
          } else if (latexData.type === 'block') {
            newLatex = '\\[' + newFormula + '\\]';
          } else if (latexData.type === 'block-dollar') {
            newLatex = '$$' + newFormula + '$$';
          } else {
            newLatex = '$' + newFormula + '$';
          }

          if (isNewFormula) {
            // Insert at cursor position
            const cursor = cm.posFromIndex(latexData.start);
            cm.replaceRange(newLatex, cursor);
            // Move cursor after inserted formula
            const newCursor = cm.posFromIndex(latexData.start + newLatex.length);
            cm.setCursor(newCursor);
          } else {
            // Replace existing formula
            const startPos = cm.posFromIndex(latexData.start);
            const endPos = cm.posFromIndex(latexData.end);
            cm.replaceRange(newLatex, startPos, endPos);
          }

          // Update rawContentCopy để trigger parse
          this.rawContentCopy = cm.getValue();

          cm.focus();
          document.body.removeChild(modal);
        });

        btnCancel.addEventListener('click', () => {
          document.body.removeChild(modal);
        });

        // ESC to close
        modal.addEventListener('click', (e) => {
          if (e.target === modal) {
            document.body.removeChild(modal);
          }
        });

        document.addEventListener('keydown', function escHandler(e) {
          if (e.key === 'Escape') {
            if (document.body.contains(modal)) {
              document.body.removeChild(modal);
            }
            document.removeEventListener('keydown', escHandler);
          }
        });

        btnContainer.appendChild(btnCancel);
        btnContainer.appendChild(btnSave);

        modalContent.appendChild(title);
        modalContent.appendChild(typeContainer);
        modalContent.appendChild(categoryTabsContainer);
        modalContent.appendChild(templatesLabel);
        modalContent.appendChild(templatesContainer);
        modalContent.appendChild(inputLabel);
        modalContent.appendChild(input);
        modalContent.appendChild(preview);
        modalContent.appendChild(btnContainer);
        modal.appendChild(modalContent);
        document.body.appendChild(modal);

        // Focus input
        setTimeout(() => {
          input.focus();
          input.select();
        }, 100);
      },
      playAudio(audioUrl) {
        const audios = document.querySelectorAll('audio[src="' + audioUrl + '"]');
        if (audios.length) audios[0].play();
      },
      onAudioChange(event, partIndex, qIndex) {
        const file = event.target.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('audio', file);
        // Gọi API upload
        axios.post('/api/upload-audio', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        }).then(res => {
          const url = res.data.url;
          this.questions[partIndex].questions[qIndex].audio = url;
        }).catch(err => {
          alert('Lỗi upload audio!');
        });
      },
      triggerAudioInput(qIndex, partIndex) {
        this.$refs['audioInput_' + partIndex + '_' + qIndex][0].click();
      },
      
      // Attach click handlers cho markdown images
      attachImageClickHandlers() {
        // Sử dụng MutationObserver để theo dõi khi DOM thay đổi (khi Vue render)
        const observer = new MutationObserver(() => {
          this.attachImageClickListeners();
        });
        
        // Bắt đầu observe
        const container = this.$el;
        if (container) {
          observer.observe(container, {
            childList: true,
            subtree: true
          });
          
          // Attach ngay lập tức
          this.$nextTick(() => {
            this.attachImageClickListeners();
          });
        }
      },
      
      // Attach click listeners cho các markdown images
      attachImageClickListeners() {
        const images = this.$el.querySelectorAll('.question-markdown-image');
        images.forEach(img => {
          // Kiểm tra xem đã có listener chưa
          if (!img.dataset.hasClickListener) {
            img.dataset.hasClickListener = 'true';
            img.addEventListener('click', (e) => {
              e.preventDefault();
              e.stopPropagation();
              this.showImageModal(img.src);
            });
          }
        });
      },
      
      // Hiển thị modal zoom ảnh
      showImageModal(imageUrl) {
        // Tạo modal để hiển thị ảnh to
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        modal.innerHTML = `
          <div class="image-modal-content">
            <span class="image-modal-close" title="Đóng (ESC)">&times;</span>
            <img src="${imageUrl}" alt="Ảnh câu hỏi" class="image-modal-img">
          </div>
        `;
        
        // Thêm modal vào body
        document.body.appendChild(modal);
        
        // Ngăn scroll body khi modal mở
        document.body.style.overflow = 'hidden';
        
        // Xử lý đóng modal
        const closeModal = function() {
          if (document.body.contains(modal)) {
            modal.style.animation = 'fadeOut 0.2s ease-out';
            setTimeout(function() {
              document.body.removeChild(modal);
              document.body.style.overflow = '';
            }, 200);
          }
        };
        
        // Click vào modal background hoặc nút đóng
        modal.addEventListener('click', function(e) {
          if (e.target === modal || e.target.classList.contains('image-modal-close')) {
            closeModal();
          }
        });
        
        // Ngăn click vào ảnh đóng modal
        modal.querySelector('.image-modal-img').addEventListener('click', function(e) {
          e.stopPropagation();
        });
        
        // Đóng modal bằng phím ESC
        const escHandler = function(e) {
          if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', escHandler);
          }
        };
        document.addEventListener('keydown', escHandler);
        
        // Focus vào modal để có thể đóng bằng ESC ngay lập tức
        modal.focus();
      }
    },
    updated() {
        
      if (window.MathJax) {
        this.$nextTick(() => {
          window.MathJax.typesetPromise && window.MathJax.typesetPromise();
        });
      }
      // Re-attach image click listeners khi component updated
      this.$nextTick(() => {
        this.attachImageClickListeners();
      });
    }
  };
  </script>
  
  <style scoped>
  .dethi-preview-area {
    padding: 32px 0;
    background: #f8f9fa;
  }
  
  .preview-box {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
    border: 1px solid #ececec;
    margin-bottom: 24px;
    overflow: hidden;
    min-height: 480px;
    display: flex;
    flex-direction: column;
  }
  
  .preview-header {
    background: #f5f6fa;
    border-bottom: 1px solid #ececec;
    padding: 8px;
    border-radius: 16px 16px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .header-actions {
    display: flex;
    gap: 8px;
  }
  
  .preview-title {
    font-size: 16px;
    color: #333;
    font-weight: 600;
  }
  
  .question-block {
    margin-bottom: 17px;
      border-bottom: 1px solid #f0f0f0;
      padding-bottom: 7px;
  }
  
  .question-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
  }
  
  .question-number {
    font-weight: 600;
    color: #4f46e5;
    margin-right: 8px;
    font-size: 13px;
    background: #eef2ff;
    padding: 4px 8px;
    border-radius: 4px;
    border: 1px solid #c7d2fe;
  }
  
  .toolbar-divider {
    width: 1px;
    height: 16px;
    background-color: #d1d5db;
    margin: 0 4px;
  }
  
  .toolbar-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f8f9fa;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 12px;
    color: #6b7280;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  .toolbar-btn:hover {
    background: #f1f5f9;
    border-color: #d1d5db;
    color: #374151;
  }
  
  .toolbar-btn input[type="number"] {
    width: 45px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 2px 4px;
    text-align: center;
    font-size: 11px;
    background: #fff;
    outline: none;
  }
  
  .toolbar-btn input[type="number"]:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
  }
  
  .toolbar-btn input[type="file"] {
    display: none;
    /* Hide the default file input */
  }
  
  .toolbar-btn i {
    font-size: 12px;
    color: #6b7280;
  }
  
  .toolbar-btn span[onclick] {
    transition: all 0.2s ease;
  }
  
  .toolbar-btn span[onclick]:hover {
    color: #3730a3 !important;
    transform: scale(1.1);
  }
  
  .btn-action {
    background: #4f46e5;
    border: 1px solid #4f46e5;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 13px;
    color: #fff;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  
  .btn-action:hover {
    background: #3730a3;
    border-color: #3730a3;
    transform: translateY(-1px);
  }
  
  .btn-action i {
    font-size: 12px;
  }
  
  .answers {
    margin: 12px 0;
  }
  
  .answer {
    background: #f8f9fa;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 12px;
    margin-bottom: 6px;
    font-size: 15px;
    color: #222;
  }
  
  .answer.correct {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
    font-weight: bold;
  }
  
  .explain {
    font-size: 14px;
    color: #eab308;
    margin-top: 6px;
  }
  
  .editor-content {
    padding: 16px;
    min-height: 650px;
  }
  
  .monaco-editor-container {
    height: 600px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
  }
  
  .monaco-editor-container .CodeMirror {
    height: 100%;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 14px;
    line-height: 1.6;
  }
  
  .monaco-editor-container .CodeMirror:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
  }
  
  .monaco-editor-container .CodeMirror-scroll {
    min-height: 600px;
  }
  
  .monaco-editor-container .CodeMirror-gutters {
    border-right: 1px solid #ddd;
    background-color: #f7f7f7;
  }
  
  .audio-upload-label {
    color: #4f46e5;
    cursor: pointer;
    font-weight: 500;
    margin-left: 4px;
    margin-right: 4px;
    transition: color 0.2s;
  }
  
  .audio-upload-label:hover {
    color: #3730a3;
    text-decoration: underline;
  }
  
  .math-key {
    font-family: 'Noto Sans', 'Arial', 'Segoe UI Symbol', 'DejaVu Sans', 'Symbola', sans-serif !important;
  }
  
  @media (max-width: 991px) {
    .col-lg-6 {
      flex: 0 0 100%;
      max-width: 100%;
    }
    
    .preview-header {
      flex-direction: column;
      gap: 12px;
      align-items: flex-start;
    }
    
    .header-actions {
      width: 100%;
      justify-content: flex-end;
    }
  }
  
  .modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(40, 48, 80, 0.18);
    backdrop-filter: blur(2.5px) brightness(0.98);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: modal-fade-in 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  @keyframes modal-fade-in {
    from {
      opacity: 0;
    }
  
    to {
      opacity: 1;
    }
  }
  
  .modal-wrapper {
    width: 100%;
    max-width: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .modal-container {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(60, 60, 100, 0.18), 0 1.5px 6px rgba(60, 60, 100, 0.10);
    padding: 28px 24px 20px 24px;
    min-width: 820px;
    max-width: 98vw;
    min-height: 80px;
    animation: modal-pop-in 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  @keyframes modal-pop-in {
    from {
      transform: scale(0.97) translateY(20px);
      opacity: 0.7;
    }
  
    to {
      transform: scale(1) translateY(0);
      opacity: 1;
    }
  }
  
  @media (max-width: 600px) {
    .modal-container {
      padding: 14px 2vw 12px 2vw;
      min-width: unset;
      max-width: 99vw;
    }
  }
  
  .score-modal {
    max-width: 420px;
    padding: 24px 18px 18px 18px;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.13);
  }
  
  .score-modal-title {
    font-size: 19px;
    font-weight: 700;
    color: #3730a3;
    margin-bottom: 18px;
    text-align: center;
  }
  
  .score-modal-section {
    margin-bottom: 18px;
  }
  
  .score-modal-subtitle {
    font-size: 15px;
    font-weight: 600;
    color: #4f46e5;
    margin-bottom: 8px;
    margin-top: 8px;
  }
  
  .score-modal-group {
    background: #f8f9fa;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px 12px 8px 12px;
    margin-bottom: 10px;
  }
  
  .score-modal-part-label {
    font-size: 14px;
    font-weight: 600;
    color: #222;
    margin-bottom: 6px;
  }
  
  .score-modal-part-title {
    color: #6b7280;
    font-weight: 400;
    font-size: 13px;
  }
  
  .score-modal-tf-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
  }
  
  .score-modal-tf-label {
    width: 110px;
    font-size: 13px;
    color: #444;
  }
  
  .score-modal-tf-input {
    width: 54px;
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
    font-size: 13px;
    text-align: right;
  }
  
  .score-modal-tf-percent {
    font-size: 13px;
    color: #666;
  }
  
  .score-modal-score-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 2px;
  }
  
  .score-modal-score-label {
    width: 70px;
    font-size: 13px;
    color: #444;
  }
  
  .score-modal-score-input {
    width: 54px;
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
    font-size: 13px;
    text-align: right;
  }
  
  .score-modal-score-unit {
    font-size: 13px;
    color: #666;
  }
  
  .score-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 18px;
  }
  
  .score-modal-cancel {
    background: #f3f4f6;
    color: #222;
    border: 1px solid #e5e7eb;
  }
  
  .score-modal-save {
    background: #4f46e5;
    color: #fff;
    border: 1px solid #4f46e5;
  }
  
  @media (max-width: 600px) {
    .score-modal {
      max-width: 98vw;
      padding: 12px 4vw 12px 4vw;
    }
  
    .score-modal-group {
      padding: 8px 4vw 6px 4vw;
    }
  
    .score-modal-title {
      font-size: 16px;
    }
  }

/* Markdown image styles */
.question-markdown-image {
  cursor: zoom-in;
  transition: transform 0.2s, box-shadow 0.2s;
  border: 2px solid transparent;
  margin: 8px 0;
}

.question-markdown-image:hover {
  transform: scale(1.02);
  box-shadow: 0 6px 16px rgba(0,0,0,0.2);
  border-color: #4f46e5;
}

/* Modal styles for zoomed image */
.image-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.95);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  animation: fadeIn 0.2s ease-out;
}

.image-modal-content {
  position: relative;
  max-width: 95%;
  max-height: 95%;
  animation: zoomIn 0.3s ease-out;
}

.image-modal-close {
  position: absolute;
  top: -50px;
  right: 0;
  color: white;
  font-size: 40px;
  font-weight: bold;
  cursor: pointer;
  z-index: 10000;
  background: rgba(0,0,0,0.5);
  width: 45px;
  height: 45px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.image-modal-close:hover {
  background: rgba(255,255,255,0.2);
}

.image-modal-img {
  width: 100%;
  height: auto;
  max-height: 95vh;
  object-fit: contain;
  border-radius: 8px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.5);
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes zoomIn {
  from {
    transform: scale(0.8);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .image-modal {
    padding: 10px;
  }
  .image-modal-img {
    max-width: 98%;
    max-height: 90vh;
  }
  .image-modal-close {
    top: -40px;
    font-size: 35px;
    width: 40px;
    height: 40px;
  }
}
</style>
  