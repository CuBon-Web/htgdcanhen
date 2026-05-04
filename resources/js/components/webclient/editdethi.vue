<template>
  <div>
    <div class="row" style="margin: auto;">
      <!-- Cột trái: Preview -->
      <div class="col-lg-2 col-12"></div>
      <div class="col-lg-8 col-12">
        <div class="preview-box">
          <div class="preview-header">
            <span class="preview-title">Chỉnh sửa: {{ examConfigProps.title }}</span>
            <div class="header-actions">
              <button class="btn-action" @click="saveExam">
                <i class="fas fa-save"></i> Lưu chỉnh sửa
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
              <span class="preview-title" style="display: block;">
                {{ item_part.part }}:
                <input type="text" v-model="item_part.part_title"
                  style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 2px 8px; font-size: 15px; width: 60%; margin-left: 6px;" />
              </span>
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
                    <template v-else-if="question.question_type === 'fill_in_blank'">Nhập đáp án</template>
                    <template v-else>Tự luận</template>
                  </span>
                  <span class="toolbar-divider"></span>
                  <span class="toolbar-btn">
                    <i class="fas fa-microphone"></i>
                    <span class="audio-upload-label" @click="triggerAudioInput(partIndex, qIndex)">Audio</span>
                  </span>
                  <span class="toolbar-divider"></span>
                  <span class="toolbar-btn" title="Sửa câu hỏi" @click="editQuestion(question.id)">
                    <i class="fas fa-edit"></i> Sửa nội dung
                  </span>
                  <span class="toolbar-btn" title="Xóa câu hỏi" @click="deleteQuestion(question.id)">
                    <i class="fas fa-trash-alt" style="color: #dc2626;"></i>
                  </span>
                </div>

                <!-- Audio Section -->
                <div v-if="question.audio" class="media-content-section">
                  <div class="media-item audio-section">
                    <div class="media-header">
                      <i class="fas fa-volume-up"></i>
                      <span>Audio câu hỏi</span>
                    </div>
                    <audio :src="question.audio" controls class="audio-player"></audio>
                  </div>
                </div>
                <div class="question-text" v-html="renderContentWithMarkdownImages(question.content, question)"></div>

                <div v-if="question.answers && question.answers.length" class="answers">
                  <div class="answer" v-for="(ans, aIndex) in question.answers" :key="aIndex"
                    :class="{ correct: question.correct_answer && ans.label.toUpperCase() === question.correct_answer }"
                    @click="question.question_type === 'multiple_choice' && setCorrectAnswer(partIndex, qIndex, ans.label)"
                    :style="question.question_type === 'multiple_choice' ? {
                      cursor: 'pointer',
                      fontWeight: question.correct_answer === ans.label.toUpperCase() ? 'bold' : 'normal',
                      color: question.correct_answer === ans.label.toUpperCase() ? '#fff' : '#222',
                      background: question.correct_answer === ans.label.toUpperCase() ? '#4f46e5' : '#f8f9fa',
                      borderRadius: '6px',
                      width: '100%',
                      padding: '2px 8px',
                      display: 'inline-block',
                      marginBottom: '6px'
                    } : {}">
                    <template v-if="question.question_type === 'multiple_choice'">
                      <span v-html="renderContentWithMarkdownImages(ans.label + '. ' + ans.content, question)"></span>
                    </template>
                    <template v-else-if="question.question_type === 'true_false_grouped'">
                      <span v-html="renderContentWithMarkdownImages(ans.label + ') ' + ans.content, question)"></span>
                      <span style="margin-left: 12px;">
                        <button @click="setTrueFalseCorrectAnswer(partIndex, qIndex, aIndex, 1)" :style="{
                          background: ans.is_correct === 1 ? '#16a34a' : '#f3f4f6',
                          color: ans.is_correct === 1 ? '#fff' : '#222',
                          border: '1px solid #4f46e5',
                          borderRadius: '4px',
                          padding: '2px 10px',
                          marginRight: '6px',
                          cursor: 'pointer',
                          fontWeight: ans.is_correct === 1 ? 'bold' : 'normal'
                        }">Đúng</button>
                        <button @click="setTrueFalseCorrectAnswer(partIndex, qIndex, aIndex, 0)" :style="{
                          background: ans.is_correct === 0 ? '#dc2626' : '#f3f4f6',
                          color: ans.is_correct === 0 ? '#fff' : '#222',
                          border: '1px solid #4f46e5',
                          borderRadius: '4px',
                          padding: '2px 10px',
                          cursor: 'pointer',
                          fontWeight: ans.is_correct === 0 ? 'bold' : 'normal'
                        }">Sai</button>
                      </span>
                    </template>
                    <template v-else-if="question.question_type === 'fill_in_blank'">
                      <span style="color: #16a34a; font-size: 18px; margin-right: 4px;"><i
                          class="fas fa-check-circle"></i></span>
                      <span v-html="renderContentWithMarkdownImages(ans.content, question)"></span>
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
        </div>
      </div>


    </div>
    <template v-if="showScoreConfigModal">
      <div class="modal-mask">
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
                <input type="number" v-model.number="partScores['PHẦN ' + toRoman(idx + 1)]" min="0"
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
    </template>

    <!-- Modal cấu hình thông tin đề thi -->
    <template v-if="showExamConfigModal">
      <div class="modal-mask" @click.self="showExamConfigModal = false">
        <div class="modal-wrapper">
          <div class="modal-container" style="max-width: 600px; max-height: 90vh; overflow-y: auto;">
            <h3 style="margin-bottom: 20px; color: #4f46e5; font-size: 18px;">
              <i class="fas fa-cog"></i> Cấu hình chung
            </h3>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Tên đề thi <span style="color: #dc2626;">*</span>
                  </label>
                  <input type="text" v-model="examConfig.title" placeholder="Nhập tên đề thi..."
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                    :class="{ 'border-red-500': examConfig.title === '' }">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Thời gian làm bài(phút) <small>Bỏ trống nếu không có giới hạn thời gian</small>
                  </label>
                  <input type="text" v-model="examConfig.time" placeholder="Nhập thời gian làm bài..."
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                    :class="{ 'border-red-500': examConfig.time === '' }">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Khối học <span style="color: #dc2626;">*</span>
                  </label>
                  <select v-model="examConfig.grade" @change="changeGrade"
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                    <option value="">Chọn khối học</option>
                    <option :value="item.id" v-for="item in grade" :key="item.id">{{ item.name }}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Môn học <span style="color: #dc2626;">*</span>
                  </label>
                  <select v-model="examConfig.subject" @change="changeSubject"
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                    <option value="">Chọn môn học</option>
                    <option :value="item.id" v-for="item in subject" :key="item.id">{{ item.name }}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Bộ đề <span style="color: #dc2626;">*</span>
                  </label>
                  <select v-model="examConfig.cate_type_id"
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                    <option value="">Chọn bộ đề</option>
                    <option :value="item.id" v-for="item in cate_type" :key="item.id">{{ item.name }}</option>
                  </select>
                </div>
              </div>
            </div>






            <div class="form-group" style="margin-bottom: 16px;">
              <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                Cấu hình giá <span style="color: #dc2626;">*</span>
              </label>
              <div style="display: flex; gap: 12px; align-items: center;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                  <input type="radio" v-model="examConfig.pricing_type" value="free" style="margin: 0;">
                  <span>Miễn phí</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                  <input type="radio" v-model="examConfig.pricing_type" value="paid" style="margin: 0;">
                  <span>Thu phí</span>
                </label>
              </div>

              <div v-if="examConfig.pricing_type === 'paid'" style="margin-top: 12px;">
                <input type="number" v-model.number="examConfig.price" placeholder="Nhập giá (VNĐ)" min="0"
                  style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
              <label style="display: block; margin-bottom: 8px;  color: #333;">
                Mô tả ngắn về đề thi
              </label>
              <textarea v-model="examConfig.description" placeholder="Nhập mô tả ngắn về đề thi..." rows="4"
                style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
            </div>

            <!-- Cấu hình kiểm soát truy cập -->
            <div style="border-top: 2px solid #e5e7eb; padding-top: 20px; margin-bottom: 20px;">
              <h4 style="margin-bottom: 16px; color: #4f46e5; font-size: 16px;">
                <i class="fas fa-shield-alt"></i> Kiểm soát truy cập
              </h4>

              <!-- Cho phép xem đáp án -->
              <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                  <input type="checkbox" v-model="examConfig.xemdapan" style="width: 18px; height: 18px; cursor: pointer;">
                  <span style="font-weight: 600; color: #333;">Không cho phép học sinh xem đáp án sau khi làm bài</span>
                </label>
                <small style="color: #6b7280; display: block; margin-top: 4px; margin-left: 26px;">
                  Nếu bật, học sinh không thể xem đáp án sau khi làm bài
                </small>
              </div>

              <!-- Đối tượng được làm bài -->
              <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                  Đối tượng được làm bài
                </label>
                <select v-model="examConfig.access_type" 
                  style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                  <option value="all">Tự do (Tất cả học sinh)</option>
                  <option value="class">Theo lớp</option>
                  <option value="time_limited">Giới hạn thời gian</option>
                </select>
              </div>

              <!-- Chọn lớp (hiển thị khi access_type = 'class') -->
              <div v-if="examConfig.access_type === 'class'" class="form-group" style="margin-bottom: 16px; background: #f9fafb; border-radius: 8px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                  <i class="fas fa-users"></i> Các lớp được phép làm bài
                </label>
                <select v-model="examConfig.classes" multiple
                  style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; min-height: 100px; overflow-x: auto;">
                  <option v-for="cls in schoolClasses" :key="cls.id" :value="cls.id">
                    {{ cls.class_name }}
                  </option>
                </select>
                <small style="color: #6b7280; display: block; margin-top: 8px;">
                  <i class="fas fa-info-circle"></i> Giữ <kbd>Ctrl</kbd> (Windows) hoặc <kbd>Cmd</kbd> (Mac) để chọn nhiều lớp
                </small>
                <small style="color: #6b7280; display: block; margin-top: 4px;">
                  💡 Nếu chưa có lớp trong danh sách, vui lòng vào <a href="/quan-ly-lop-hoc" target="_blank" style="color: #4f46e5;">Quản lý lớp học</a> để thêm mới
                </small>
              </div>

              <!-- Giới hạn thời gian (hiển thị khi access_type = 'time_limited') -->
              <div v-if="examConfig.access_type === 'time_limited'" class="form-group" style="margin-bottom: 16px; padding: 16px; background: #f9fafb; border-radius: 8px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                  <i class="fas fa-clock"></i> Thời gian cho phép làm bài
                </label>
                <div class="row">
                  <div class="col-md-6">
                    <label style="display: block; margin-bottom: 4px; font-size: 13px; color: #6b7280;">Bắt đầu</label>
                    <input type="datetime-local" v-model="examConfig.start_time"
                      style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
                  </div>
                  <div class="col-md-6">
                    <label style="display: block; margin-bottom: 4px; font-size: 13px; color: #6b7280;">Kết thúc</label>
                    <input type="datetime-local" v-model="examConfig.end_time"
                      style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
                  </div>
                </div>
                <small style="color: #6b7280; display: block; margin-top: 8px;">
                  Học sinh chỉ có thể làm bài trong khoảng thời gian này
                </small>
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
                {{ isSavingExam ? 'Đang lưu...' : 'Đồng ý sửa' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Hidden input file for audio upload -->
    <input type="file" ref="hiddenAudioInput" accept="audio/*" @change="onAudioChange" style="display: none;">
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
    grade: {
      type: Array,
      default: function () { return []; }
    },

    trueFalseScorePercentProps: {
      type: Object,
      default: null
    },
    examConfigProps: {
      type: Object,
      default: null
    },
    partScoresProps: {
      type: Object,
      default: null
    }
  },
  data: function () {
    return {
      questions: JSON.parse(JSON.stringify(this.initialQuestions)),
      showScoreConfigModal: false,
      showGuideModal: false,
      showExamConfigModal: false,
      trueFalseScorePercent: this.trueFalseScorePercentProps ? Object.assign({}, this.trueFalseScorePercentProps) : { 1: 10, 2: 25, 3: 50, 4: 100 },
      partScores: this.partScoresProps ? Object.assign({}, this.partScoresProps) : {}, // { 'PHẦN I': 5, ... }
      examConfig: this.examConfigProps ? Object.assign({}, this.examConfigProps) : {
        title: '',
        grade: '',
        subject: '',
        cate_type_id: '',
        pricing_type: 'free',
        price: 0,
        description: '',
        time: 0,
        xemdapan: false,
        access_type: 'all',
        start_time: '',
        end_time: '',
        classes: [] // Mảng class_id (dùng cho multiple select)
      },
      isSavingExam: false,
      listSubject: [],
      subject: [],
      cate_type: [],
      schoolClasses: [],
      currentAudioPartIndex: null,
      currentAudioQIndex: null
    };
  },
  computed: {
    isExamConfigValid() {
      return this.examConfig.title.trim() !== '' &&
        this.examConfig.grade !== '' &&
        this.examConfig.subject !== '' &&
        this.examConfig.cate_type_id !== '' &&
        this.examConfig.pricing_type !== '' &&
        (this.examConfig.pricing_type === 'free' ||
          (this.examConfig.pricing_type === 'paid' && this.examConfig.price > 0));
    }
  },
  watch: {
    partScoresProps: {
      handler(newVal) {
        this.partScores = newVal ? Object.assign({}, newVal) : {};
      },
      deep: true
    },
    initialQuestions: {
      handler(newVal) {
        console.log('initialQuestions changed:', newVal);
        this.questions = JSON.parse(JSON.stringify(newVal));
        this.processImageData();
        console.log('Questions after processing:', this.questions);
      },
      immediate: true,
      deep: true
    }
  },
  mounted() {
    console.log('Initial questions:', this.questions);
    this.loadFontAwesome();
    this.loadSchoolClasses();
    // Nếu là sửa đề thi, có thể cần load subject theo grade
    if (this.examConfig && this.examConfig.grade) {
      this.changeGrade();
      this.changeSubject();
    }
    // Xử lý dữ liệu ảnh từ database (chuyển từ trường 'image' sang 'images')
    this.processImageData();
    // Thêm sự kiện double click cho textarea để chỉnh sửa công thức
    this.$nextTick(() => {
      var textarea = this.$refs.contentTextarea;
      if (textarea) {
        textarea.addEventListener('dblclick', this.handleLatexDblClick);
      }
    });
    // Thêm event listener cho click vào markdown images để zoom
    this.$nextTick(() => {
      this.attachImageClickHandlers();
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
    toRoman(num) {
      const roman = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X"];
      return roman[num] || num;
    },
    loadSchoolClasses() {
      axios.get('/api/school-classes/active')
        .then(response => {
          this.schoolClasses = response.data;
        })
        .catch(error => {
          console.error('Error loading school classes:', error);
        });
    },
    changeGrade() {
      axios.post('/de-thi/filter-subject', { grade: this.examConfig.grade })
        .then(response => {

          this.subject = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    changeSubject() {
      axios.post('/de-thi/filter-type', { subject: this.examConfig.subject })
        .then(response => {

          this.cate_type = response.data;
        })
        .catch(error => {

        });
    },
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
    loadFontAwesome() {
      // Load Font Awesome nếu chưa có
      if (!document.querySelector('link[href*="font-awesome"]')) {
        const fontAwesome = document.createElement('link');
        fontAwesome.rel = 'stylesheet';
        fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
        document.head.appendChild(fontAwesome);
      }
    },


    saveExam() {

      // Validate trước khi mở modal
      const validationResult = this.validateQuestions();
      if (!validationResult.isValid) {
        this.showErrorAlert(validationResult.message);
        return;
      }
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
      if (!this.isExamConfigValid) {
        alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        return;
      }

      // Validate thời gian nếu chọn time_limited
      if (this.examConfig.access_type === 'time_limited') {
        if (!this.examConfig.start_time || !this.examConfig.end_time) {
          alert('Vui lòng nhập đầy đủ thời gian bắt đầu và kết thúc!');
          return;
        }
        if (new Date(this.examConfig.start_time) >= new Date(this.examConfig.end_time)) {
          alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
          return;
        }
      }

      // Validate lớp học nếu chọn class
      if (this.examConfig.access_type === 'class') {
        if (!this.examConfig.classes || this.examConfig.classes.length === 0) {
          alert('Vui lòng chọn ít nhất một lớp học!');
          return;
        }
      }

      // Chuẩn bị dữ liệu câu hỏi trước khi gửi
      if (!this.prepareQuestionsData()) {
        return; // Dừng nếu có lỗi validation
      }

      this.isSavingExam = true;
      const examData = {
        id: this.examConfig.id ?? null,
        title: this.examConfig.title,
        description: this.examConfig.description,
        time: this.examConfig.time,
        grade: this.examConfig.grade,
        cate_type_id: this.examConfig.cate_type_id,
        subject: this.examConfig.subject,
        pricing_type: this.examConfig.pricing_type,
        price: this.examConfig.pricing_type === 'paid' ? this.examConfig.price : 0,
        true_false_score_percent: this.trueFalseScorePercent,
        parts: this.questions,
        partScores: this.partScores,
        // Thêm các trường kiểm soát truy cập
        xemdapan: this.examConfig.xemdapan ? 1 : 0,
        access_type: this.examConfig.access_type,
        start_time: this.examConfig.access_type === 'time_limited' ? this.examConfig.start_time : null,
        end_time: this.examConfig.access_type === 'time_limited' ? this.examConfig.end_time : null,
        class_ids: this.examConfig.access_type === 'class' ? this.examConfig.classes : []
      };
      axios.post('/de-thi/store-exam', examData)
        .then(response => {
          this.isSavingExam = false;
          if (response.data.success) {
            this.showSuccessAlert('Tạo đề thi thành công!');
            this.showExamConfigModal = false;
            window.location.reload();
          } else {
            this.showErrorAlert('Có lỗi xảy ra: ' + response.data.error);
          }
        })
        .catch(error => {
          this.isSavingExam = false;
          this.showErrorAlert('Có lỗi xảy ra khi tạo đề thi!');
        });
    },

    saveScoreConfig() {
      // Chia đều điểm cho từng phần, tự động so sánh PHẦN 1 <-> PHẦN I
      function romanize(num) {
        if (!+num) return '';
        const digits = String(+num).split('');
        const key = [
          '', 'C', 'CC', 'CCC', 'CD', 'D', 'DC', 'DCC', 'DCCC', 'CM',
          '', 'X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC',
          '', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX'
        ];
        let roman = '', i = 3;
        while (i--) roman = (key[+digits.pop() + (i * 10)] || '') + roman;
        return Array(+digits.join('') + 1).join('M') + roman;
      }
      Object.keys(this.partScores).forEach(partKey => {
        // partKey là 'PHẦN I', 'PHẦN II', ...
        this.questions.forEach(part => {
          // part.part có thể là 'PHẦN 1', 'PHẦN 2', ...
          let matchKey = part.part;
          // Nếu part.part là PHẦN 1, PHẦN 2... thì chuyển sang PHẦN I, PHẦN II...
          const match = matchKey.match(/^PHẦN\s+(\d+)$/);
          if (match) {
            matchKey = 'PHẦN ' + romanize(match[1]);
          }
          if (matchKey === partKey && part.questions.length > 0) {
            const perQuestion = this.partScores[partKey] / part.questions.length;
            part.questions.forEach(q => {
              q.score = Math.round(perQuestion * 100) / 100;
            });
          }
        });
      });
      this.showScoreConfigModal = false;
    },
    playAudio(audioUrl) {
      const audios = document.querySelectorAll('audio[src="' + audioUrl + '"]');
      if (audios.length) audios[0].play();
    },
    onAudioChange(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Sử dụng thông tin câu hỏi đã lưu
      const partIndex = this.currentAudioPartIndex;
      const qIndex = this.currentAudioQIndex;

      if (partIndex === undefined || qIndex === undefined) {
        console.error('Part index or question index not set for audio');
        return;
      }

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
    triggerAudioInput(partIndex, qIndex) {
      // Lưu thông tin câu hỏi hiện tại để sử dụng trong onAudioChange
      this.currentAudioPartIndex = partIndex;
      this.currentAudioQIndex = qIndex;

      // Sử dụng input file ẩn duy nhất
      if (this.$refs.hiddenAudioInput) {
        this.$refs.hiddenAudioInput.click();
      } else {
        console.warn('Hidden audio input not found');
      }
    },
    setCorrectAnswer(partIndex, qIndex, label) {
      this.questions[partIndex].questions[qIndex].correct_answer = label.toUpperCase();
    },
    setTrueFalseCorrectAnswer(partIndex, qIndex, aIndex, value) {
      // Toggle đáp án đúng/sai cho đáp án đúng/sai nhóm
      const answers = this.questions[partIndex].questions[qIndex].answers;
      answers[aIndex].is_correct = value;
    },
    editQuestion(id) {
      window.location.href = '/de-thi/sua-cau-hoi/' + id + '.html';
    },
    confirmDelete(id) {
      Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: 'Câu hỏi sẽ bị xóa vĩnh viễn!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
      }).then((result) => {
        if (result.isConfirmed) {
          // Gọi ajax xóa
          axios.post('/de-thi/delete-question', { id: id }).then(res => {
            if (res.data.success) {
              this.showSuccessAlert('Xóa câu hỏi thành công!');
              window.location.reload();
            } else {
              this.showErrorAlert('Có lỗi xảy ra: ' + res.data.error);
            }
          });
        }
      });
    },
    deleteQuestion(id) {
      this.confirmDelete(id);
    },


    processImageData() {

      // Xử lý dữ liệu ảnh từ database
      // Chuyển từ trường 'image' (JSON) sang 'images' (array) để tương thích với giao diện
      this.questions.forEach((part, partIndex) => {
        part.questions.forEach((question, qIndex) => {
          console.log(`Question ${question.question_no}:`, {
            hasImage: !!question.image,
            hasImages: !!question.images,
            imageType: typeof question.image,
            imageValue: question.image,
            imagesValue: question.images,
            questionKeys: Object.keys(question)
          });

          // Xử lý trường hợp có trường 'image' (JSON string hoặc array)
          if (question.image) {
            if (typeof question.image === 'string') {
              try {
                const imageData = JSON.parse(question.image);
                question.images = Array.isArray(imageData) ? imageData : [imageData];
                console.log(`Parsed image data for question ${question.question_no}:`, question.images);
                delete question.image; // Xóa trường cũ
              } catch (e) {
                console.warn('Invalid image data for question:', question.question_no, e);
                question.images = null;
              }
            } else if (Array.isArray(question.image)) {
              // Nếu đã là array, chỉ cần đổi tên
              question.images = question.image;
              console.log(`Converted image array for question ${question.question_no}:`, question.images);
              delete question.image;
            } else {
              // Nếu là object hoặc kiểu dữ liệu khác
              question.images = [question.image];
              console.log(`Converted image object for question ${question.question_no}:`, question.images);
              delete question.image;
            }
          } else if (question.images) {
            // Nếu đã có trường 'images', giữ nguyên
            console.log(`Question ${question.question_no} already has images:`, question.images);
          } else {
            // Không có ảnh
            question.images = null;
            console.log(`Question ${question.question_no} has no images`);
          }

          // Đảm bảo images là array hoặc null
          if (question.images && !Array.isArray(question.images)) {
            question.images = [question.images];
          }

          // Xử lý trường question_type - chuyển từ 'type' nếu cần
          if (question.type && !question.question_type) {
            question.question_type = question.type;
            delete question.type;
          }

          // Đảm bảo có trường question_type
          if (!question.question_type) {
            // Xác định loại câu hỏi dựa trên cấu trúc dữ liệu
            if (question.answers && question.answers.length > 0) {
              if (question.correct_answer) {
                question.question_type = 'multiple_choice';
              } else if (question.answers.some(ans => ans.hasOwnProperty('is_correct'))) {
                question.question_type = 'true_false_grouped';
              } else {
                question.question_type = 'fill_in_blank';
              }
            } else {
              question.question_type = 'essay'; // Tự luận
            }
          }

          // Log kết quả cuối cùng
          console.log(`Final result for question ${question.question_no}:`, {
            images: question.images,
            imagesType: typeof question.images,
            isArray: Array.isArray(question.images),
            question_type: question.question_type
          });
        });
      });

      console.log('Final questions after processing:', this.questions);
    },

    prepareQuestionsData() {
      // Chuẩn bị dữ liệu câu hỏi trước khi gửi đi
      this.questions.forEach((part, partIndex) => {
        part.questions.forEach((question, qIndex) => {
          // Đảm bảo có trường question_type
          if (!question.question_type) {
            if (question.type) {
              question.question_type = question.type;
              delete question.type;
            } else {
              // Xác định loại câu hỏi dựa trên cấu trúc dữ liệu
              if (question.answers && question.answers.length > 0) {
                if (question.correct_answer) {
                  question.question_type = 'multiple_choice';
                } else if (question.answers.some(ans => ans.hasOwnProperty('is_correct'))) {
                  question.question_type = 'true_false_grouped';
                } else {
                  question.question_type = 'fill_in_blank';
                }
              } else {
                question.question_type = 'essay'; // Tự luận
              }
            }
          }

          // Đảm bảo có trường images (array hoặc null)
          if (!question.hasOwnProperty('images')) {
            if (question.image) {
              if (typeof question.image === 'string') {
                try {
                  const imageData = JSON.parse(question.image);
                  question.images = Array.isArray(imageData) ? imageData : [imageData];
                } catch (e) {
                  question.images = null;
                }
              } else if (Array.isArray(question.image)) {
                question.images = question.image;
              } else {
                question.images = [question.image];
              }
              delete question.image;
            } else {
              question.images = null;
            }
          }

          // Đảm bảo images là array hoặc null
          if (question.images && !Array.isArray(question.images)) {
            question.images = [question.images];
          }

          // Đảm bảo có các trường cần thiết khác
          if (!question.hasOwnProperty('score')) {
            question.score = 0;
          }

          if (!question.hasOwnProperty('content')) {
            question.content = '';
          }

          if (!question.hasOwnProperty('answers')) {
            question.answers = [];
          }

          console.log(`Prepared question ${question.question_no}:`, {
            question_type: question.question_type,
            images: question.images,
            score: question.score,
            hasContent: !!question.content,
            answersCount: question.answers ? question.answers.length : 0
          });
        });
      });

      console.log('Questions prepared for saving:', this.questions);

      // Kiểm tra dữ liệu trước khi gửi
      return this.validateQuestionsData();
    },

    validateQuestionsData() {
      let isValid = true;
      const errors = [];

      this.questions.forEach((part, partIndex) => {
        part.questions.forEach((question, qIndex) => {
          if (!question.question_type) {
            errors.push(`Câu ${question.question_no}: Thiếu trường question_type`);
            isValid = false;
          }

          if (!question.hasOwnProperty('score')) {
            errors.push(`Câu ${question.question_no}: Thiếu trường score`);
            isValid = false;
          }

          if (!question.hasOwnProperty('content')) {
            errors.push(`Câu ${question.question_no}: Thiếu trường content`);
            isValid = false;
          }

          if (!question.hasOwnProperty('answers')) {
            errors.push(`Câu ${question.question_no}: Thiếu trường answers`);
            isValid = false;
          }

          if (!question.hasOwnProperty('images')) {
            errors.push(`Câu ${question.question_no}: Thiếu trường images`);
            isValid = false;
          }
        });
      });

      if (!isValid) {
        console.error('Data validation errors:', errors);
        alert('Có lỗi trong dữ liệu câu hỏi: ' + errors.join(', '));
        return false;
      }

      console.log('Data validation passed');
      return true;
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

/* Media Content Section Styles */
.media-content-section {
  margin: 16px 0;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.media-item {
  margin-bottom: 16px;
  padding: 12px;
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.media-item:last-child {
  margin-bottom: 0;
}

.media-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
  font-weight: 600;
  color: #374151;
  font-size: 14px;
}

.media-header i {
  color: #4f46e5;
  font-size: 16px;
}

.audio-section {
  border-left: 4px solid #4f46e5;
}

.audio-player {
  width: 100%;
  height: 40px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
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