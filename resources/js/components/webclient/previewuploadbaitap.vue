<template>
    <div>
      <div class="row" style="margin-right: 0;">
        <!-- Cột trái: Preview -->
        <div class="col-lg-6 col-12">
          <div class="preview-box">
            <div class="preview-header">
              <span class="preview-title">Định dạng câu hỏi</span>
              <div class="header-actions">
                <button class="btn-action" @click="saveExam">
                  <i class="fas fa-save"></i> Tạo bài tập
                </button>
                <button class="btn-action" @click="showScoreConfigModal = true">
                  <i class="fas fa-sliders-h"></i> Chia điểm
                </button>
                <!-- <button class="btn-action" @click="parseContent">
                  <i class="fas fa-sync-alt"></i> Cập nhật
                </button> -->
              </div>
            </div>
            <div class="preview-content editor-content content-height">
              <div v-for="(item_part, partIndex) in questions" :key="partIndex">
                <span class="preview-title" style="margin-bottom:10px;">{{ item_part.part }}: {{ item_part.part_title
                  }}</span>
                  <!-- <input type="text" v-if="examConfigProps !== null" v-model="item_part.id" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"> -->
                <br>
                <div class="question-block" v-for="(question, qIndex) in item_part.questions" :key="qIndex">
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
                      <template v-else>Tự luận</template>
                    </span>
                    <span class="toolbar-divider"></span>
                    <span class="toolbar-btn">
                      <i class="fas fa-microphone"></i>
                      <span class="audio-upload-label" @click="triggerAudioInput(qIndex, partIndex)">Audio</span>
                      <input type="file" accept="audio/*" :ref="'audioInput_' + partIndex + '_' + qIndex"
                        @change="onAudioChange($event, partIndex, qIndex)" style="display: none;">
                      
                    </span>
                    <div v-if="question.audio" style="margin: 6px 0;">
                      <audio :src="question.audio" controls style="height: 24px; vertical-align: middle;"></audio>
                    </div>
                    <!-- <span v-if="question.audio" @click.stop="playAudio(question.audio)" style="cursor: pointer; color: #4f46e5;">
                        <i class="fas fa-volume-up"></i>
                      </span> -->
                  </div>
  
                  <div class="question-text" v-html="renderMath(question.content)"></div>
  
                  
  
                  <div v-if="question.answers && question.answers.length" class="answers">
                    <div class="answer" v-for="(ans, aIndex) in question.answers" :key="aIndex"
                      :class="{ correct: question.correct_answer && ans.label.toUpperCase() === question.correct_answer }">
                      <template v-if="question.question_type === 'multiple_choice'">
                        <span v-html="renderMath(ans.label + '. ' + ans.content)"></span>
                      </template>
                      <template v-else-if="question.question_type === 'true_false_grouped'">
                        <span v-if="ans.is_correct === 1" style="color: #16a34a; font-size: 18px; margin-right: 4px;"><i
                            class="fas fa-check-circle"></i></span>
                        <span v-else style="color: #dc2626; font-size: 18px; margin-right: 4px;"><i
                            class="fas fa-times-circle"></i></span>
                        <span v-html="renderMath(ans.label + ') ' + ans.content)"></span>
                      </template>
                    </div>
                  </div>
                  <div v-if="question.explanation" class="explanation-block"
                    style="margin-top: 10px; background: #f8f9fa; border-left: 4px solid #4f46e5; padding: 10px 16px; border-radius: 6px;">
                    <div style="font-weight: bold; color: #4f46e5; margin-bottom: 4px;">Lời giải:</div>
                    <div v-html="question.explanation"></div>
                  </div>
                </div>
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
              <textarea ref="contentTextarea" v-model="rawContentCopy" class="content-textarea"
                placeholder="Nhập nội dung ở đây..." rows="25"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-mask" v-show="showScoreConfigModal">
        <div class="modal-wrapper">
          <div class="modal-container">
              <h3 style="margin-bottom: 18px;font-size: 18px;"><i class="fas fa-sliders-h"></i> Chia điểm</h3>
              <h3 style="font-size: 15px;">Cấu hình thang điểm cho câu hỏi đúng sai</h3>
              <small>Chức năng này cho phép Người dùng có thể cấu hình % số điểm mong muốn với từng câu trả lời đúng với
                loại câu hỏi đúng sai.</small>
              <div v-for="i in 4" :key="i"
                style="margin-bottom: 14px; display: flex; align-items: center; gap: 12px;justify-content: space-between;">
                <label style="width: 120px;">Trả lời đúng {{ i }} ý</label>
                <div class="inpit" style="    display: flex;align-items: center;"> 
                  <input type="number" v-model.number="trueFalseScorePercent[i]" min="0" max="100"
                    style="width: 60px; padding: 4px 8px; border-radius: 6px; border: 1px solid #e5e7eb;">
                  <div class="phantram">%</div>
                </div>
                 
              </div>
              <hr>
              <div v-for="(part, idx) in questions" :key="part.part"
                style="margin-bottom: 14px; display: flex; align-items: center; gap: 12px;    justify-content: space-between;">
                <label style="width: 320px;">{{ part.part }} ({{ part.part_title }})</label>
                <div class="inpit" style="    display: flex;align-items: center;"> 
                  <input type="number" v-model.number="partScores[part.part]" min="0"
                    style="width: 60px; padding: 4px 8px; border-radius: 6px; border: 1px solid #e5e7eb;">
                  <div class="phantram">đ</div>
                </div>
              </div>
              <div style="text-align: right; margin-top: 18px;display: flex ; justify-content: space-between;">
                <button class="btn-action"
                  style="background: #f3f4f6; color: #222; border: 1px solid #e5e7eb; margin-right: 8px;"
                  @click="showScoreConfigModal = false">Đóng</button>
                <button class="btn-action" @click="saveScoreConfig">Lưu</button>
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
                <b>Mô tả chức năng của component:</b><br>
                - <b>Xem trước bài tập</b>: Hiển thị cấu trúc đề, các phần, câu hỏi, đáp án, lời giải, điểm số.<br>
                - <b>Chỉnh sửa nội dung file gốc</b>: Cho phép sửa trực tiếp nội dung bài tập dạng text/raw, sau đó tự động
                parse lại sang cấu trúc câu hỏi.<br>
                - <b>Cấu hình thang điểm</b>: Chia điểm cho từng phần, cấu hình % điểm cho từng số ý đúng với phần
                đúng/sai nhóm.<br>
                - <b>Upload audio</b>: Gắn file audio cho từng câu hỏi.<br>
                - <b>Chèn công thức toán học</b>: Hỗ trợ nhập/chỉnh sửa công thức LaTeX với preview trực quan.<br>
                - <b>Bảo toàn điểm số</b>: Khi chỉnh sửa nội dung và parse lại, điểm số từng câu được giữ nguyên.<br>
                - <b>Xem lời giải</b>: Hiển thị lời giải chi tiết cho từng câu hỏi nếu có.<br>
                - <b>Phân biệt loại câu hỏi</b>: Tự động nhận diện trắc nghiệm, đúng/sai nhóm, tự luận.<br>
                - <b>Giao diện hiện đại, dễ dùng</b>: Modal, bảng điểm, preview, hướng dẫn đều trực quan.<br>
                <br>
                <b>Hướng dẫn nhập liệu:</b><br>
                <b>1. Câu hỏi trắc nghiệm:</b><br>
                - Mỗi câu bắt đầu bằng <b>Câu X.</b> (ví dụ: Câu 1.)<br>
                - Đáp án ghi theo dạng <b>A. ...</b>, <b>B. ...</b>, ...<br>
                - Đáp án đúng đánh dấu <b>*</b> trước ký tự (ví dụ: <b>*A. Đáp án đúng</b>)<br>
                <br>
                <b>2. Câu hỏi đúng/sai nhóm:</b><br>
                - Đáp án ghi dạng <b>a) ...</b>, <b>b) ...</b>, ...<br>
                - Đáp án đúng đánh dấu <b>*</b> trước ký tự (ví dụ: <b>*a) Đúng</b>)<br>
                <br>
                <b>3. Lời giải:</b><br>
                - Sau các đáp án, thêm dòng <b>Lời giải:</b> rồi nhập lời giải.<br>
                <br>
                <b>4. Phân chia phần (tùy chọn):</b><br>
                - Đầu mỗi phần ghi <b>PHẦN I. [Tên phần]</b> (ví dụ: PHẦN I. Trắc nghiệm)<br>
                - <b>Nếu không có phần:</b> Hệ thống sẽ tự động tạo "PHẦN I. Câu hỏi chung"<br>
                <br>
                <b>Ví dụ có phần:</b><br>
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
  
      <!-- Modal cấu hình thông tin bài tập -->
      <template v-if="showExamConfigModal">
        <div class="modal-mask" @click.self="showExamConfigModal = false">
          <div class="modal-wrapper">
            <div class="modal-container" style="max-width: 600px;">
              <h3 style="margin-bottom: 20px; color: #4f46e5; font-size: 18px;">
                <i class="fas fa-cog"></i> Cấu hình chung
              </h3>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                      Tên bài tập <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" v-model="examConfig.title" placeholder="Nhập tên bài tập..."
                      style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                      :class="{ 'border-red-500': examConfig.title === '' }">
                  </div>
                </div>
              </div>
              
  
              <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button class="btn-action" style="background: #f3f4f6; color: #222; border: 1px solid #e5e7eb;"
                  @click="showExamConfigModal = false">
                  Hủy
                </button>
                <button class="btn-action" :disabled="!isExamConfigValid || isSavingExam"
                  :style="{ opacity: isExamConfigValid && !isSavingExam ? 1 : 0.5, cursor: isExamConfigValid && !isSavingExam ? 'pointer' : 'not-allowed' }"
                  @click="saveExamWithConfig">
                  <i class="fas fa-save" v-if="!isSavingExam"></i>
                  <i class="fas fa-spinner fa-spin" v-else></i>
                  {{ isSavingExam ? 'Đang lưu...' : 'Tạo bài tập' }}
                </button>
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
  export default {
    name: "PreviewExam",
    props: {
      initialQuestions: {
        type: Array,
        required: true
      },
      rawContent: {
        type: String,
        default: ''
      },
      grade: {
        type: Array,
        default: function() { return []; }
      }
    },
    data: function() {
      return {
        questions: JSON.parse(JSON.stringify(this.initialQuestions)),
        rawContentCopy: this.rawContent,
        showScoreConfigModal: false,
        showGuideModal: false,
        showExamConfigModal: false,
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
        return this.examConfig.title.trim() !== '';
      }
    },
    watch: {
      rawContentCopy: {
        handler: "debouncedParseContent"
      }
    },
    mounted() {
      this.loadFontAwesome();
      // Nếu là sửa bài tập, có thể cần load subject theo grade
      if (this.examConfig && this.examConfig.grade) {
        this.changeGrade();
      }
      // Thêm sự kiện double click cho textarea để chỉnh sửa công thức
      this.$nextTick(() => {
        var textarea = this.$refs.contentTextarea;
        if (textarea) {
          textarea.addEventListener('dblclick', this.handleLatexDblClick);
        }
      });
    },
    beforeDestroy() {
      // Xóa sự kiện khi component bị hủy
      var textarea = this.$refs.contentTextarea;
      if (textarea) {
        textarea.removeEventListener('dblclick', this.handleLatexDblClick);
      }
    },
    methods: {
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
  
      insertMathFormula() {
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
          max-width: 800px;
          max-height: 90vh;
          overflow-y: auto;
        `;
  
        modalContent.innerHTML = `
          <h3 style="margin: 0 0 16px 0; color: #333; font-size: 18px;">
            <i class="fas fa-square-root-alt"></i> Chèn công thức toán học
          </h3>
          <div style=" margin-bottom: 20px;">
            <!-- Cột trái: Nhập công thức -->
            <div>
              <div>
              <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; min-height: 100px; background: #fafafa; display: flex; align-items: center; justify-content: center;">
                <div id="mathPreview" style="font-size: 15px; color: #333;">
                  <span style="color: #999; font-style: italic;">Nhập công thức để xem preview...</span>
                </div>
              </div>
              <div style="margin:12px 0; padding: 12px; background: #e0e7ff; border-radius: 8px; border-left: 4px solid #4f46e5;">
                <h5 style="margin: 0 0 8px 0; color: #3730a3; font-size: 13px;">
                  <i class="fas fa-info-circle"></i> Thông tin:
                </h5>
                <p style="margin: 0; color: #4c1d95; font-size: 12px;">
                  Công thức sẽ được hiển thị chính xác như trong MathType. Sử dụng LaTeX để tạo công thức toán học chuyên nghiệp.
                </p>
              </div>
            </div>
              <div class="math-keyboard" style="margin-bottom: 12px; display: flex; flex-wrap: wrap; gap: 6px;">
                <!-- Cơ bản -->
                <button type="button" class="math-key" data-symbol="+">+</button>
                <button type="button" class="math-key" data-symbol="-">-</button>
                <button type="button" class="math-key" data-symbol="\\times">×</button>
                <button type="button" class="math-key" data-symbol="\\div">÷</button>
                <button type="button" class="math-key" data-symbol="=">=</button>
                <button type="button" class="math-key" data-symbol="\\neq">≠</button>
                <button type="button" class="math-key" data-symbol="<">&lt;</button>
                <button type="button" class="math-key" data-symbol=">">&gt;</button>
                <button type="button" class="math-key" data-symbol="\\leq">≤</button>
                <button type="button" class="math-key" data-symbol="\\geq">≥</button>
                <button type="button" class="math-key" data-symbol="\\approx">≈</button>
                <button type="button" class="math-key" data-symbol="\\equiv">≡</button>
                <button type="button" class="math-key" data-symbol="\\to">→</button>
                <button type="button" class="math-key" data-symbol="\\leftarrow">←</button>
                <button type="button" class="math-key" data-symbol="\\leftrightarrow">↔</button>
                <button type="button" class="math-key" data-symbol="\\implies">⇒</button>
                <button type="button" class="math-key" data-symbol="\\iff">⇔</button>
                <button type="button" class="math-key" data-symbol="\\overrightarrow{}">\overrightarrow{}</button>
                <button type="button" class="math-key" data-symbol="\\widehat{}">x&#770;</button>
                <button type="button" class="math-key" data-symbol="\\sqrt{}">√</button>
                <button type="button" class="math-key" data-symbol="\\sqrt[n]{}">n√</button>
                <button type="button" class="math-key" data-symbol="\\frac{}{}">a/b</button>
                <button type="button" class="math-key" data-symbol="\\overline{}">¯</button>
                <!-- Tổng, tích phân, giới hạn -->
                <button type="button" class="math-key" data-symbol="\\sum_{i=1}^{n}{}">∑</button>
                <button type="button" class="math-key" data-symbol="\\prod_{i=1}^{n}{}">∏</button>
                <button type="button" class="math-key" data-symbol="\\int_{a}^{b}{}">∫</button>
                <button type="button" class="math-key" data-symbol="\\lim_{x \\to a}">lim</button>
                <!-- Ma trận, hệ phương trình -->
                <button type="button" class="math-key" data-symbol="\\begin{pmatrix} a & b \\\\ c & d \\end{pmatrix}">pmatrix</button>
                <button type="button" class="math-key" data-symbol="\\begin{bmatrix} a & b \\\\ c & d \\end{bmatrix}">bmatrix</button>
                <button type="button" class="math-key" data-symbol="\\begin{cases} x & x>0 \\\\ -x & x<0 \\end{cases}">cases</button>
                <!-- Hy Lạp -->
                <button type="button" class="math-key" data-symbol="\\alpha">α</button>
                <button type="button" class="math-key" data-symbol="\\beta">β</button>
                <button type="button" class="math-key" data-symbol="\\gamma">γ</button>
                <button type="button" class="math-key" data-symbol="\\delta">δ</button>
                <button type="button" class="math-key" data-symbol="\\epsilon">ε</button>
                <button type="button" class="math-key" data-symbol="\\theta">θ</button>
                <button type="button" class="math-key" data-symbol="\\lambda">λ</button>
                <button type="button" class="math-key" data-symbol="\\mu">μ</button>
                <button type="button" class="math-key" data-symbol="\\rho">ρ</button>
                <button type="button" class="math-key" data-symbol="\\sigma">σ</button>
                <button type="button" class="math-key" data-symbol="\\phi">φ</button>
                <button type="button" class="math-key" data-symbol="\\omega">ω</button>
                <!-- Hàm số -->
                <button type="button" class="math-key" data-symbol="\\sin">sin</button>
                <button type="button" class="math-key" data-symbol="\\cos">cos</button>
                <button type="button" class="math-key" data-symbol="\\tan">tan</button>
                <button type="button" class="math-key" data-symbol="\\cot">cot</button>
                <button type="button" class="math-key" data-symbol="\\log">log</button>
                <button type="button" class="math-key" data-symbol="\\ln">ln</button>
                <button type="button" class="math-key" data-symbol="\\exp">exp</button>
                <!-- Tập hợp, logic -->
                <button type="button" class="math-key" data-symbol="\\in">∈</button>
                <button type="button" class="math-key" data-symbol="\\notin">∉</button>
                <button type="button" class="math-key" data-symbol="\\subset">⊂</button>
                <button type="button" class="math-key" data-symbol="\\subseteq">⊆</button>
                <button type="button" class="math-key" data-symbol="\\supset">⊃</button>
                <button type="button" class="math-key" data-symbol="\\supseteq">⊇</button>
                <button type="button" class="math-key" data-symbol="\\cup">∪</button>
                <button type="button" class="math-key" data-symbol="\\cap">∩</button>
                <button type="button" class="math-key" data-symbol="\\emptyset">∅</button>
                <button type="button" class="math-key" data-symbol="\\forall">∀</button>
                <button type="button" class="math-key" data-symbol="\\exists">∃</button>
                <button type="button" class="math-key" data-symbol="\\neg">¬</button>
                <button type="button" class="math-key" data-symbol="\\wedge">∧</button>
                <button type="button" class="math-key" data-symbol="\\vee">∨</button>
                <button type="button" class="math-key" data-symbol="\\Rightarrow">⇒</button>
                <button type="button" class="math-key" data-symbol="\\Leftrightarrow">⇔</button>
                <!-- Dấu ngoặc -->
                <button type="button" class="math-key" data-symbol="(">(</button>
                <button type="button" class="math-key" data-symbol=")">)</button>
                <button type="button" class="math-key" data-symbol="[">[</button>
                <button type="button" class="math-key" data-symbol="]">]</button>
                <button type="button" class="math-key" data-symbol="{">{</button>
                <button type="button" class="math-key" data-symbol="}">}</button>
              </div>
              <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                  Công thức LaTeX:
                </label>
                <textarea 
                  id="mathFormulaInput"
                  placeholder="Ví dụ: \\frac{a}{b}, \\sqrt{x}, \\sum_{i=1}^{n} x_i"
                  style="width: 100%; height: 120px; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'Monaco', 'Menlo', monospace; font-size: 14px; resize: vertical;"
                ></textarea>
              </div>
            </div>\
            
          </div>
          <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <button id="cancelBtn" style="padding: 10px 20px; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 6px; cursor: pointer; font-size: 14px;">
              Hủy
            </button>
            <button id="insertBtn" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
              <i class="fas fa-plus"></i> Chèn công thức
            </button>
          </div>
        `;
  
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
  
        const input = modal.querySelector('#mathFormulaInput');
        const preview = modal.querySelector('#mathPreview');
        const cancelBtn = modal.querySelector('#cancelBtn');
        const insertBtn = modal.querySelector('#insertBtn');
        const formulaBtns = modal.querySelectorAll('.formula-btn');
        const mathKeys = modal.querySelectorAll('.math-key');
  
        input.focus();
  
        // Hàm cập nhật preview
        const updatePreview = function (formula) {
          if (formula.trim()) {
            preview.innerHTML = '\\(' + formula + '\\)';
            if (window.MathJax && window.MathJax.typesetPromise) {
              window.MathJax.typesetPromise([preview]).catch(function (err) {
                console.error('MathJax error:', err);
                preview.innerHTML = '<span style="color: #dc2626;">Lỗi: ' + formula + '</span>';
              });
            }
          } else {
            preview.innerHTML = '<span style="color: #999; font-style: italic;">Nhập công thức để xem preview...</span>';
          }
        };
  
        // Xử lý input change để cập nhật preview
        input.addEventListener('input', function (e) {
          updatePreview(e.target.value);
        });
  
        // Xử lý click vào các button công thức có sẵn
        formulaBtns.forEach(function (btn) {
          btn.addEventListener('click', function () {
            const formula = this.getAttribute('data-formula');
            input.value = formula;
            updatePreview(formula);
            input.focus();
          });
        });
  
        // Xử lý click vào các nút bàn phím toán học
        mathKeys.forEach(function (btn) {
          btn.addEventListener('click', function () {
            const symbol = this.getAttribute('data-symbol');
            const start = input.selectionStart;
            const end = input.selectionEnd;
            input.value = input.value.substring(0, start) + symbol + input.value.substring(end);
            input.focus();
            input.setSelectionRange(start + symbol.length, start + symbol.length);
            updatePreview(input.value);
          });
        });
  
        // Thêm CSS cho các button công thức và bàn phím
        const style = document.createElement('style');
        style.textContent = `
          .formula-btn, .math-key {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-family: monospace;
            transition: all 0.2s;
            text-align: left;
          }
          .formula-btn:hover, .math-key:hover {
            background: #e0e7ff;
            border-color: #4f46e5;
            color: #4f46e5;
          }
          .formula-btn:active, .math-key:active {
            transform: translateY(1px);
          }
        `;
        document.head.appendChild(style);
  
        const closeModal = function () {
          document.body.removeChild(modal);
          document.head.removeChild(style);
        };
  
        cancelBtn.onclick = closeModal;
        insertBtn.onclick = () => {
          const formula = input.value.trim();
          if (formula) {
            const mathText = `\\(${formula}\\)`;
            const textarea = this.$refs.contentTextarea;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            
            // Chèn công thức vào vị trí con trỏ
            const newValue = textarea.value.substring(0, start) + mathText + textarea.value.substring(end);
            this.rawContentCopy = newValue;
            
            // Đặt lại con trỏ sau công thức đã chèn
            this.$nextTick(() => {
              textarea.focus();
              textarea.setSelectionRange(start + mathText.length, start + mathText.length);
            });
          }
          closeModal();
        };
  
        // Đóng modal khi click bên ngoài
        modal.onclick = function (e) {
          if (e.target === modal) closeModal();
        };
  
        // Đóng modal khi nhấn Escape
        const handleEscape = function (e) {
          if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', handleEscape);
          }
        };
        document.addEventListener('keydown', handleEscape);
  
        // Enter để chèn công thức
        const handleEnter = function (e) {
          if (e.key === 'Enter' && e.ctrlKey) {
            insertBtn.click();
          }
        };
        input.addEventListener('keydown', handleEnter);
      },
      saveExam() {
        // Mở modal ngay lập tức để UX tốt hơn
        this.showExamConfigModal = true;
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
      saveExamWithConfig() {
        // Validate câu hỏi trước
        const validationResult = this.validateQuestions();
        if (!validationResult.isValid) {
          this.showErrorAlert(validationResult.message);
          return;
        }

        if (!this.isExamConfigValid) {
          alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
          return;
        }
        this.isSavingExam = true;
        const examData = {
          id: this.examConfig.id ?? null,
          title: this.examConfig.title,
          description: "",
          time: 0,
          grade: 0,
          subject: 0,
          pricing_type: 'free',
          price: 0,
          true_false_score_percent: this.trueFalseScorePercent,
          parts: this.questions,
          partScores: this.partScores
        };
        axios.post('/bai-tap/store-exam', examData)
          .then(response => {
            this.isSavingExam = false;
            if (response.data.success) {
              this.showSuccessAlert('Tạo bài tập thành công!');
              this.showExamConfigModal = false;
              window.location.href = '/bai-tap/danh-sach.html';
            } else {
              this.showErrorAlert('Có lỗi xảy ra: ' + response.data.error);
            }
          })
          .catch(error => {
            this.isSavingExam = false;
            this.showErrorAlert('Có lỗi xảy ra khi tạo bài tập!');
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
        // Lưu lại điểm số cũ theo part + question_no
        const scoreMap = {};
        this.questions.forEach(part => {
          part.questions.forEach(q => {
            if (q.question_no != null) {
              const key = part.part + '___' + q.question_no;
              scoreMap[key] = q.score;
            }
          });
        });
        console.log(this.questions);
        const normalized = this.normalizeText(this.rawContentCopy);
        axios
          .post("/api/parse-content", {
            rawContent: normalized,
            originParts: this.questions
          })
          .then(res => {
            
            // Gán lại điểm số cũ cho dữ liệu mới
            res.data.questions.forEach(part => {
  
              part.questions.forEach(q => {
                const key = part.part + '___' + q.question_no;
                if (q.question_no != null && scoreMap[key] != null) {
                  q.score = scoreMap[key];
                }
              });
            });
            this.questions = res.data.questions;
            console.log(this.questions);
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
      handleLatexDblClick: function (e) {
        var textarea = e.target;
        var value = textarea.value;
        var pos = textarea.selectionStart;
        // Tìm công thức gần vị trí con trỏ (\(...\), \[...\], $...$, $$...$$)
        var regex = /\\\((.+?)\\\)|\\\[(.+?)\\\]|\$\$(.+?)\$\$|\$(.+?)\$/gs;
        var match, found = false;
        while ((match = regex.exec(value)) !== null) {
          var start = match.index;
          var end = regex.lastIndex;
          if (pos >= start && pos <= end) {
            var latex = match[1] || match[2] || match[3] || match[4] || '';
            this.openMathModalForEdit(latex, start, end);
            found = true;
            break;
          }
        }
      },
      openMathModalForEdit: function (latex, start, end) {
        var self = this;
        var modal = document.createElement('div');
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
        var modalContent = document.createElement('div');
        modalContent.style.cssText = `
          background: white;
          padding: 24px;
          border-radius: 12px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.2);
          width: 90%;
          max-width: 800px;
          max-height: 90vh;
          overflow-y: auto;
        `;
  
        modalContent.innerHTML = `
          <div style="">
            <!-- Cột trái: Nhập công thức -->
            <div>
              <div>
              <h4 style="margin: 0 0 12px 0; color: #333; font-size: 15px;">
                <i class="fas fa-eye"></i> Xem trước (MathType)
              </h4>
              <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; min-height: 100px; background: #fafafa; display: flex; align-items: center; justify-content: center;">
                <div id="mathPreview" style="font-size: 15px; color: #333;">
                  <span style="color: #999; font-style: italic;">Nhập công thức để xem preview...</span>
                </div>
              </div>
              <div style="margin: 12px 0; padding: 12px; background: #e0e7ff; border-radius: 8px; border-left: 4px solid #4f46e5;">
                <h5 style="margin: 0 0 8px 0; color: #3730a3; font-size: 13px;">
                  <i class="fas fa-info-circle"></i> Thông tin:
                </h5>
                <p style="margin: 0; color: #4c1d95; font-size: 12px;">
                  Công thức sẽ được hiển thị chính xác như trong MathType. Sử dụng LaTeX để tạo công thức toán học chuyên nghiệp.
                </p>
              </div>
            </div>
              <h4 style="margin: 0 0 12px 0; color: #333; font-size: 15px;">
                <i class="fas fa-edit"></i> Nhập công thức LaTeX
              </h4>
              <div class="math-keyboard" style="margin-bottom: 12px; display: flex; flex-wrap: wrap; gap: 6px;">
                <!-- Cơ bản -->
                <button type="button" class="math-key" data-symbol="+">+</button>
                <button type="button" class="math-key" data-symbol="-">-</button>
                <button type="button" class="math-key" data-symbol="\\times">×</button>
                <button type="button" class="math-key" data-symbol="\\div">÷</button>
                <button type="button" class="math-key" data-symbol="=">=</button>
                <button type="button" class="math-key" data-symbol="\\neq">≠</button>
                <button type="button" class="math-key" data-symbol="<">&lt;</button>
                <button type="button" class="math-key" data-symbol=">">&gt;</button>
                <button type="button" class="math-key" data-symbol="\\leq">≤</button>
                <button type="button" class="math-key" data-symbol="\\geq">≥</button>
                <button type="button" class="math-key" data-symbol="\\approx">≈</button>
                <button type="button" class="math-key" data-symbol="\\equiv">≡</button>
                <button type="button" class="math-key" data-symbol="\\to">→</button>
                <button type="button" class="math-key" data-symbol="\\leftarrow">←</button>
                <button type="button" class="math-key" data-symbol="\\leftrightarrow">↔</button>
                <button type="button" class="math-key" data-symbol="\\implies">⇒</button>
                <button type="button" class="math-key" data-symbol="\\iff">⇔</button>
                <button type="button" class="math-key" data-symbol="\\overrightarrow{}">\overrightarrow{}</button>
                <button type="button" class="math-key" data-symbol="\\widehat{}">x&#770;</button>
                <button type="button" class="math-key" data-symbol="\\sqrt{}">√</button>
                <button type="button" class="math-key" data-symbol="\\sqrt[n]{}">n√</button>
                <button type="button" class="math-key" data-symbol="\\frac{}{}">a/b</button>
                <button type="button" class="math-key" data-symbol="\\overline{}">¯</button>
                <!-- Tổng, tích phân, giới hạn -->
                <button type="button" class="math-key" data-symbol="\\sum_{i=1}^{n}{}">∑</button>
                <button type="button" class="math-key" data-symbol="\\prod_{i=1}^{n}{}">∏</button>
                <button type="button" class="math-key" data-symbol="\\int_{a}^{b}{}">∫</button>
                <button type="button" class="math-key" data-symbol="\\lim_{x \\to a}">lim</button>
                <!-- Ma trận, hệ phương trình -->
                <button type="button" class="math-key" data-symbol="\\begin{pmatrix} a & b \\\\ c & d \\end{pmatrix}">pmatrix</button>
                <button type="button" class="math-key" data-symbol="\\begin{bmatrix} a & b \\\\ c & d \\end{bmatrix}">bmatrix</button>
                <button type="button" class="math-key" data-symbol="\\begin{cases} x & x>0 \\\\ -x & x<0 \\end{cases}">cases</button>
                <!-- Hy Lạp -->
                <button type="button" class="math-key" data-symbol="\\alpha">α</button>
                <button type="button" class="math-key" data-symbol="\\beta">β</button>
                <button type="button" class="math-key" data-symbol="\\gamma">γ</button>
                <button type="button" class="math-key" data-symbol="\\delta">δ</button>
                <button type="button" class="math-key" data-symbol="\\epsilon">ε</button>
                <button type="button" class="math-key" data-symbol="\\theta">θ</button>
                <button type="button" class="math-key" data-symbol="\\lambda">λ</button>
                <button type="button" class="math-key" data-symbol="\\mu">μ</button>
                <button type="button" class="math-key" data-symbol="\\rho">ρ</button>
                <button type="button" class="math-key" data-symbol="\\sigma">σ</button>
                <button type="button" class="math-key" data-symbol="\\phi">φ</button>
                <button type="button" class="math-key" data-symbol="\\omega">ω</button>
                <!-- Hàm số -->
                <button type="button" class="math-key" data-symbol="\\sin">sin</button>
                <button type="button" class="math-key" data-symbol="\\cos">cos</button>
                <button type="button" class="math-key" data-symbol="\\tan">tan</button>
                <button type="button" class="math-key" data-symbol="\\cot">cot</button>
                <button type="button" class="math-key" data-symbol="\\log">log</button>
                <button type="button" class="math-key" data-symbol="\\ln">ln</button>
                <button type="button" class="math-key" data-symbol="\\exp">exp</button>
                <!-- Tập hợp, logic -->
                <button type="button" class="math-key" data-symbol="\\in">∈</button>
                <button type="button" class="math-key" data-symbol="\\notin">∉</button>
                <button type="button" class="math-key" data-symbol="\\subset">⊂</button>
                <button type="button" class="math-key" data-symbol="\\subseteq">⊆</button>
                <button type="button" class="math-key" data-symbol="\\supset">⊃</button>
                <button type="button" class="math-key" data-symbol="\\supseteq">⊇</button>
                <button type="button" class="math-key" data-symbol="\\cup">∪</button>
                <button type="button" class="math-key" data-symbol="\\cap">∩</button>
                <button type="button" class="math-key" data-symbol="\\emptyset">∅</button>
                <button type="button" class="math-key" data-symbol="\\forall">∀</button>
                <button type="button" class="math-key" data-symbol="\\exists">∃</button>
                <button type="button" class="math-key" data-symbol="\\neg">¬</button>
                <button type="button" class="math-key" data-symbol="\\wedge">∧</button>
                <button type="button" class="math-key" data-symbol="\\vee">∨</button>
                <button type="button" class="math-key" data-symbol="\\Rightarrow">⇒</button>
                <button type="button" class="math-key" data-symbol="\\Leftrightarrow">⇔</button>
                <!-- Dấu ngoặc -->
                <button type="button" class="math-key" data-symbol="(">(</button>
                <button type="button" class="math-key" data-symbol=")">)</button>
                <button type="button" class="math-key" data-symbol="[">[</button>
                <button type="button" class="math-key" data-symbol="]">]</button>
                <button type="button" class="math-key" data-symbol="{">{</button>
                <button type="button" class="math-key" data-symbol="}">}</button>
              </div>
              <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                  Công thức LaTeX:
                </label>
                <textarea 
                  id="mathFormulaInput"
                  placeholder="Ví dụ: \\frac{a}{b}, \\sqrt{x}, \\sum_{i=1}^{n} x_i"
                  style="width: 100%; height: 120px; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'Monaco', 'Menlo', monospace; font-size: 14px; resize: vertical;"
                ></textarea>
              </div>
            </div>
            <!-- Cột phải: Preview MathType -->
            
          </div>
          <div style="display: flex; gap: 8px; justify-content: flex-end;">
            <button id="cancelBtn" style="padding: 10px 20px; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 6px; cursor: pointer; font-size: 14px;">
              Hủy
            </button>
            <button id="insertBtn" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
              <i class="fas fa-plus"></i> Chèn công thức
            </button>
          </div>
        `;
  
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
  
        const input = modal.querySelector('#mathFormulaInput');
        const preview = modal.querySelector('#mathPreview');
        const cancelBtn = modal.querySelector('#cancelBtn');
        const insertBtn = modal.querySelector('#insertBtn');
        const formulaBtns = modal.querySelectorAll('.formula-btn');
        const mathKeys = modal.querySelectorAll('.math-key');
  
        input.focus();
        input.value = latex; // Set the initial value for the input
        // Hàm cập nhật preview
        function updatePreview(formula) {
          if (formula.trim()) {
            preview.innerHTML = '\\(' + formula + '\\)';
            if (window.MathJax && window.MathJax.typesetPromise) {
              window.MathJax.typesetPromise([preview]).catch(function (err) {
                console.error('MathJax error:', err);
                preview.innerHTML = '<span style="color: #dc2626;">Lỗi: ' + formula + '</span>';
              });
            }
          } else {
            preview.innerHTML = '<span style="color: #999; font-style: italic;">Nhập công thức để xem preview...</span>';
          }
        }
        updatePreview(latex);
  
        // Xử lý input change để cập nhật preview
        input.addEventListener('input', function (e) {
          updatePreview(e.target.value);
        });
  
        // Xử lý click vào các button công thức có sẵn
        formulaBtns.forEach(function (btn) {
          btn.addEventListener('click', function () {
            const formula = this.getAttribute('data-formula');
            input.value = formula;
            updatePreview(formula);
            input.focus();
          });
        });
  
        // Xử lý click vào các nút bàn phím toán học
        mathKeys.forEach(function (btn) {
          btn.addEventListener('click', function () {
            const symbol = this.getAttribute('data-symbol');
            const start = input.selectionStart;
            const end = input.selectionEnd;
            input.value = input.value.substring(0, start) + symbol + input.value.substring(end);
            input.focus();
            input.setSelectionRange(start + symbol.length, start + symbol.length);
            updatePreview(input.value);
          });
        });
  
        // Thêm CSS cho các button công thức và bàn phím
        const style = document.createElement('style');
        style.textContent = `
          .formula-btn, .math-key {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-family: monospace;
            transition: all 0.2s;
            text-align: left;
          }
          .formula-btn:hover, .math-key:hover {
            background: #e0e7ff;
            border-color: #4f46e5;
            color: #4f46e5;
          }
          .formula-btn:active, .math-key:active {
            transform: translateY(1px);
          }
        `;
        document.head.appendChild(style);
  
        const closeModal = function () {
          document.body.removeChild(modal);
          document.head.removeChild(style);
        };
  
        cancelBtn.onclick = closeModal;
        insertBtn.onclick = function () {
          var formula = input.value.trim();
          if (formula) {
            var textarea = self.$refs.contentTextarea;
            // Thay thế value từ start đến end bằng công thức mới (giữ nguyên \(...\), \[...\], $...$, $$...$$)
            var before = textarea.value.substring(0, start);
            var after = textarea.value.substring(end);
            var old = textarea.value.substring(start, end);
            var newLatex;
            if (old.startsWith('$$')) {
              newLatex = '$$' + formula + '$$';
            } else if (old.startsWith('$')) {
              newLatex = '$' + formula + '$';
            } else if (old.startsWith('\\(')) {
              newLatex = '\\(' + formula + '\\)';
            } else if (old.startsWith('\\[')) {
              newLatex = '\\[' + formula + '\\]';
            } else {
              newLatex = formula;
            }
            self.rawContentCopy = before + newLatex + after;
            self.$nextTick(function () {
              textarea.focus();
              textarea.setSelectionRange(start + newLatex.length, start + newLatex.length);
            });
          }
          closeModal();
        };
  
        // Đóng modal khi click bên ngoài
        modal.onclick = function (e) {
          if (e.target === modal) closeModal();
        };
  
        // Đóng modal khi nhấn Escape
        const handleEscape = function (e) {
          if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', handleEscape);
          }
        };
        document.addEventListener('keydown', handleEscape);
  
        // Enter để chèn công thức
        const handleEnter = function (e) {
          if (e.key === 'Enter' && e.ctrlKey) {
            insertBtn.click();
          }
        };
        input.addEventListener('keydown', handleEnter);
      },
      saveScoreConfig() {
        // Chia đều điểm cho từng phần
        Object.keys(this.partScores).forEach(partKey => {
          const part = this.questions.find(p => p.part === partKey);
          if (part && part.questions.length > 0) {
            const perQuestion = this.partScores[partKey] / part.questions.length;
            part.questions.forEach(q => {
              q.score = Math.round(perQuestion * 100) / 100;
            });
          }
        });
        this.showScoreConfigModal = false;
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
      }
    },
    updated() {
      if (window.MathJax) {
        this.$nextTick(() => {
          window.MathJax.typesetPromise && window.MathJax.typesetPromise();
        });
      }
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
  
  .content-textarea {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 14px;
    line-height: 1.6;
    resize: vertical;
    background: #fff;
    outline: none;
  }
  
  .content-textarea:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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
  </style>
  