<template>
  <div>
    <div class="row" style="margin-right: 0;">
      <!-- Cột trái: Preview -->
      <div class="col-lg-6 col-12">
        <div class="preview-box">
          <div class="preview-header">
            <span class="preview-title" v-if="type == 'dethi'">Định dạng câu hỏi</span>
            <span class="preview-title" v-if="type == 'baitap'">Định dạng bài tập</span>
            <div class="header-actions">
              <button class="btn-action" @click="saveExam" v-if="type === 'dethi' || type === 'game'">
                <i class="fas fa-save"></i> Tạo đề thi
              </button>
              <button class="btn-action" @click="saveExam" v-if="type === 'baitap'">
                <i class="fas fa-save"></i> Tạo bài tập
              </button>
              <button class="btn-action" @click="showScoreConfigModal = true" v-if="type !== 'game'">
                <i class="fas fa-sliders-h"></i> Chia điểm
              </button>
              <!-- <button class="btn-action" @click="parseContent">
                <i class="fas fa-sync-alt"></i> Cập nhật
              </button> -->
            </div>
          </div>
          <div class="preview-content editor-content content-height">
            <div v-for="(item_part, partIndex) in questions" :key="partIndex">
              <span class="preview-title" style="margin-bottom:10px;">{{ item_part.part }}: {{ item_part.part_title}}</span>
              <!-- <input type="text" v-if="examConfigProps !== null" v-model="item_part.id" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"> -->
              <br>
              <div class="question-block" 
                   :id="'question-' + partIndex + '-' + qIndex"
                   :data-question-no="question.question_no"
                   :data-part-index="partIndex"
                   :data-part-title="item_part.part"
                   v-for="(question, qIndex) in item_part.questions" 
                   :key="qIndex">
                <div class="question-header">
                  <span class="question-number">Câu {{ question.question_no }}.</span>
                  <template v-if="type !== 'game'">
                    <span class="toolbar-divider"></span>
                    <span class="toolbar-btn">
                      Điểm:
                      <input type="number" step="0.01" min="0" v-model.number="question.score"
                        style="width: 50px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 2px 4px;">
                    </span>
                  </template>
                  <span class="toolbar-divider"></span>
                  <span class="toolbar-btn">
                    <template v-if="question.question_type === 'multiple_choice'">Trắc nghiệm</template>
                    <template v-else-if="question.question_type === 'true_false_grouped'">Đúng/Sai</template>
                    <template v-else-if="question.question_type === 'fill_in_blank'">Nhập Đáp Án</template>
                    <template v-else>Tự luận</template>
                  </span>
                  <span class="toolbar-divider"></span>
                  <span class="toolbar-btn">
                    <i class="fas fa-microphone"></i>
                    <span class="audio-upload-label" @click="triggerAudioInput(partIndex, qIndex)">Audio</span>
                  </span>
                  <span class="toolbar-divider"></span>
                  <span class="toolbar-btn">
                    <i class="fas fa-image"></i>
                    <span class="audio-upload-label" @click="triggerImageInput(partIndex, qIndex)">Ảnh</span>
                  </span>
                </div>

                <!-- Media Content Section - Chỉ hiển thị audio, ảnh sẽ hiển thị inline trong content dưới dạng markdown -->
                <div class="media-content-section" v-if="question.audio">
                  <!-- Audio Section -->
                  <div v-if="question.audio" class="media-item audio-section">
                    <div class="media-header">
                      <i class="fas fa-volume-up"></i>
                      <span>Audio câu hỏi</span>
                    </div>
                    <audio :src="question.audio" controls class="audio-player"></audio>
                  </div>
                </div>


                <!-- <span v-if="question.audio" @click.stop="playAudio(question.audio)" style="cursor: pointer; color: #4f46e5;">
                      <i class="fas fa-volume-up"></i>
                    </span> -->

                <div class="question-text" v-html="renderContentWithImages(question)"></div>



                <div v-if="question.answers && question.answers.length" class="answers">
                  <div class="answer" v-for="(ans, aIndex) in question.answers" :key="aIndex"
                    :class="{ correct: question.correct_answer && ans.label.toUpperCase() === question.correct_answer }">
                    <template v-if="question.question_type === 'multiple_choice'">
                      <span v-html="renderContentWithMarkdownImages(ans.label + '. ' + ans.content)"></span>
                    </template>
                    <template v-else-if="question.question_type === 'true_false_grouped'">
                      <span v-if="ans.is_correct === 1" style="color: #16a34a; font-size: 18px; margin-right: 4px;"><i
                          class="fas fa-check-circle"></i></span>
                      <span v-else style="color: #dc2626; font-size: 18px; margin-right: 4px;"><i
                          class="fas fa-times-circle"></i></span>
                      <span v-html="renderContentWithMarkdownImages(ans.label + ') ' + ans.content)"></span>
                    </template>
                    <template v-else-if="question.question_type === 'fill_in_blank'">
                      <span style="color: #16a34a; font-size: 18px; margin-right: 4px;"><i
                          class="fas fa-check-circle"></i></span>
                      <span v-html="renderContentWithMarkdownImages(ans.content)"></span>
                    </template>
                    <template v-else>
                      <!-- short_answer hoặc các loại khác -->
                      <span v-html="renderContentWithMarkdownImages(ans.content || ans.label)"></span>
                    </template>
                  </div>
                </div>
                <div v-if="question.explanation" class="explanation-block"
                  style="margin-top: 10px; background: #f8f9fa; border-left: 4px solid #4f46e5; padding: 10px 16px; border-radius: 6px;">
                  <div style="font-weight: bold; color: #4f46e5; margin-bottom: 4px;">Lời giải:</div>
                  <div v-html="renderContentWithMarkdownImages(question.explanation)"></div>
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
              <button class="btn-action" @click="showGuideModal = true">
                <i class="fas fa-question-circle"></i> Hướng dẫn nhập liệu
              </button>

              <!-- <button class="btn-action" @click="parseContent">
                <i class="fas fa-sync-alt"></i> Cập nhật
              </button> -->
            </div>
          </div>
          <div class="preview-content editor-content">
            <div class="monaco-toolbar">
              <div class="toolbar-left">
                <button @click="insertMathFormula" class="toolbar-btn toolbar-btn-primary" title="Chèn công thức toán học (Ctrl+M)">
                  <i class="fas fa-square-root-alt"></i> Công thức
                </button>
                <button @click="formatCode" class="toolbar-btn" title="Format Code (Shift+Alt+F)">
                  <i class="fas fa-code"></i> Format
                </button>
                <button @click="findAndReplace" class="toolbar-btn" title="Find & Replace (Ctrl+H)">
                  <i class="fas fa-search"></i> Find
                </button>
                <button @click="selectFromPersonalBank" class="toolbar-btn toolbar-btn-primary" title="Chọn từ ngân hàng cá nhân">
                  <i class="fas fa-user-plus"></i> Chọn từ ngân hàng cá nhân
                </button>
                <button @click="redirectToRouteLink('/de-thi/upload-file-docx.html')" class="toolbar-btn toolbar-btn-primary" title="Tải file word">
                  <i class="fas fa-file-word"></i> Tải file word
                </button>
              </div>
              
              <div class="toolbar-right">
                <button @click="toggleTheme" class="toolbar-btn" title="Toggle Theme">
                  <i class="fas fa-adjust"></i> Theme
                </button>
                <button @click="toggleWordWrap" class="toolbar-btn" title="Toggle Word Wrap">
                  <i class="fas fa-text-width"></i> Wrap
                </button>
                <!-- <button @click="toggleMinimap" class="toolbar-btn" title="Toggle Minimap">
                  <i class="fas fa-th-large"></i> Map
                </button> -->
              </div>
            </div>
            <div ref="monacoEditor" class="monaco-editor-container"></div> 
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

    <!-- Modal ngân hàng câu hỏi -->
    <template v-if="showQuestionBankModal">
      <div class="modal-mask" @click.self="showQuestionBankModal = false">
        <div class="modal-wrapper">
          <div class="modal-container" style="max-width: 1200px; max-height: 90vh; overflow-y: auto;">
            <h3 style="margin-bottom: 20px; color: #4f46e5; font-size: 18px;">
              <i class="fas fa-database"></i> Chọn từ ngân hàng cá nhân
            </h3>
            
            <div class="row">
              <!-- Cột trái: Folders và Đề thi -->
              <div class="col-md-4" style="border-right: 1px solid #e5e7eb; padding-right: 16px;">
                <h4 style="font-size: 15px; margin-bottom: 12px; color: #333;">Thư mục</h4>
                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                  <div 
                    @click="selectedFolderId = null" 
                    :class="['question-bank-item', { active: selectedFolderId === null }]"
                    style="padding: 8px 12px; cursor: pointer; border-radius: 6px; margin-bottom: 4px;">
                    <i class="fas fa-home"></i> Thư mục gốc
                  </div>
                  <!-- Hiển thị cây thư mục đầy đủ -->
                  <div v-for="folder in flattenedFolders" :key="'folder-' + folder.id"
                    :style="{ 
                      paddingLeft: (folder.depth * 20) + 'px',
                      marginBottom: '4px',
                      display: folder.visible !== false ? 'block' : 'none'
                    }">
                    <div 
                      :class="['folder-tree-item-content', { active: selectedFolderId === folder.id }]"
                      :style="{ 
                        border: '1px solid #e5e7eb',
                        padding: '8px 12px',
                        cursor: 'pointer',
                        borderRadius: '6px',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px',
                        transition: 'all 0.2s'
                      }">
                      <span v-if="folder.hasChildren || !loadedFolderIds.includes(folder.id)"
                        @click.stop="toggleFolder(folder.id)"
                        class="folder-toggle"
                        style="width: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; color: #666; cursor: pointer; flex-shrink: 0;">
                        <i v-if="loadingFolderIds.includes(folder.id)" class="fas fa-spinner fa-spin"></i>
                        <i v-else :class="folder.isExpanded ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                      </span>
                      <span v-else style="width: 20px; display: inline-block; flex-shrink: 0;"></span>
                      <i class="fas fa-folder" style="color: #4f46e5; font-size: 14px; flex-shrink: 0;"></i>
                      
                      <span 
                        @click.stop="selectFolder(folder.id)"
                        style="flex: 1; user-select: none; cursor: pointer; min-width: 0;">
                        {{ folder.name }}
                      </span>
                      
                    </div>
                  </div>
                </div>

                <h4 style="font-size: 15px; margin-bottom: 12px; color: #333;">Đề thi</h4>
                <div style="max-height: 400px; overflow-y: auto;">
                  <div v-if="filteredExams.length === 0" style="padding: 20px; text-align: center; color: #999;">
                    Không có đề thi nào
                  </div>
                  <div v-for="exam in filteredExams" :key="exam.id"
                    @click="selectExam(exam.id)"
                    :class="['question-bank-item', { active: selectedExamId === exam.id }]"
                    style="padding: 10px 12px; cursor: pointer; border-radius: 6px; margin-bottom: 8px;">
                    <div style="font-weight: 600; margin-bottom: 4px;">{{ exam.title }}</div>
                    <div style="font-size: 12px; color: #666;">
                      {{ exam.folder ? exam.folder.name : 'Thư mục gốc' }}
                      <span v-if="exam.customer && exam.customer.type == 3" style="margin-left: 8px; color: #4f46e5;">
                        · {{ exam.customer.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cột phải: Câu hỏi -->
              <div class="col-md-8" style="padding-left: 16px;">
                <div v-if="!selectedExamId" style="padding: 40px; text-align: center; color: #999;">
                  <i class="fas fa-file-alt" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                  Vui lòng chọn một đề thi để xem câu hỏi
                </div>
                
                <div v-else>
                  <div v-if="isLoadingQuestions" style="padding: 40px; text-align: center;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #4f46e5;"></i>
                    <div style="margin-top: 12px; color: #666;">Đang tải câu hỏi...</div>
                  </div>

                  <div v-else-if="selectedExamQuestions.length === 0" style="padding: 40px; text-align: center; color: #999;">
                    Đề thi này không có câu hỏi
                  </div>

                  <div v-else style="max-height: 800px; overflow-y: auto;">
                    <div v-for="(part, partIndex) in selectedExamQuestions" :key="partIndex" style="margin-bottom: 24px;">
                      <div style="font-weight: 600; font-size: 14px; color: #4f46e5; margin-bottom: 12px; padding: 8px; background: #f0f0ff; border-radius: 6px;">
                        {{ part.part }}: {{ part.part_title }}
                      </div>
                      
                      <div v-for="(question, qIndex) in part.questions" :key="question.id" 
                        :class="['question-item', { selected: selectedQuestionIds.includes(question.id) }]"
                        @click="toggleQuestionSelection(question.id)"
                        style="padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                          <input type="checkbox" 
                            :checked="selectedQuestionIds.includes(question.id)"
                            @click.stop="toggleQuestionSelection(question.id)"
                            style="margin-top: 4px;">
                          <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 8px;">
                              Câu {{ question.question_no }}. 
                              <span style="font-size: 12px; color: #666; margin-left: 8px;">
                                {{ getQuestionTypeLabel(question.question_type) }} · {{ question.score }} điểm
                              </span>
                            </div>
                            <div style="font-size: 13px; color: #444; margin-bottom: 8px;" v-html="question.content.substring(0, 150) + (question.content.length > 150 ? '...' : '')"></div>
                            <div v-if="question.answers && question.answers.length > 0" style="font-size: 12px; color: #666;">
                              {{ question.answers.length }} đáp án
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
              <div style="color: #666; font-size: 14px;">
                Đã chọn: <strong>{{ selectedQuestionIds.length }}</strong> câu hỏi
              </div>
              <div>
                <button class="btn-action" 
                  :disabled="selectedQuestionIds.length === 0"
                  @click="insertSelectedQuestions">
                  <i class="fas fa-plus"></i> Chèn vào đề thi ({{ selectedQuestionIds.length }})
                </button>
              </div>
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
              <i class="fas fa-cog"></i> Cấu hình chung d
            </h3>
            <div class="row">
              <div class="col-md-6" v-if="type === 'dethi' ">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Tên đề thi <span style="color: #dc2626;">*</span>
                  </label>
                  <input type="text" v-model="examConfig.title" placeholder="Nhập tên đề thi..."
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                    :class="{ 'border-red-500': examConfig.title === '' }">
                </div>
              </div>
              <div class="col-md-6" v-if="type === 'game'">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Tên game <span style="color: #dc2626;">*</span>
                  </label>
                  <input type="text" v-model="examConfig.title" placeholder="Nhập tên đề thi..."
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                    :class="{ 'border-red-500': examConfig.title === '' }">
                </div>
              </div>
              <div class="col-md-12" v-if="type === 'baitap'">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Tên bài tập <span style="color: #dc2626;">*</span>
                  </label>
                  <input type="text" v-model="examConfig.title" placeholder="Nhập tên bài tập..."
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                    :class="{ 'border-red-500': examConfig.title === '' }">
                </div>
              </div>
              <div class="col-md-6" v-if="type === 'dethi' || type === 'game'">
                <div class="form-group" style="margin-bottom: 16px;">
                  <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                    Thời gian làm bài(phút) <small>Bỏ trống nếu không có giới hạn thời gian</small>
                  </label>
                  <input type="text" v-model="examConfig.time" placeholder="Nhập thời gian làm bài..."
                    style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                    :class="{ 'border-red-500': examConfig.time === '' }">
                </div>
              </div>
              <div class="col-md-4" v-if="type === 'dethi' || type === 'game'">
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
              <div class="col-md-4" v-if="type === 'dethi' || type === 'game'">
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
              <div class="col-md-4" v-if="type === 'dethi' || type === 'game'">
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






            <div class="form-group" style="margin-bottom: 16px;" v-if="type === 'dethi' || type === 'game'">
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

            <div class="form-group" style="margin-bottom: 20px;" v-if="type === 'dethi' || type === 'game'">
              <label style="display: block; margin-bottom: 8px;  color: #333;">
                Mô tả ngắn
              </label>
              <textarea v-model="examConfig.description" placeholder="Nhập mô tả ngắn..." rows="4"
                style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
            </div>

            <!-- Cấu hình kiểm soát truy cập -->
            <div v-if="type === 'dethi'" style="border-top: 2px solid #e5e7eb; padding-top: 20px; margin-bottom: 20px;">
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
                  style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; min-height: 100px;overflow-x: auto;">
                  <option v-for="cls in schoolClasses" :key="cls.id" :value="cls.id">
                    {{ cls.class_name }}
                  </option>
                </select>
                <small style="color: #6b7280; display: block; margin-top: 8px;">
                  <i class="fas fa-info-circle"></i> Giữ <kbd>Ctrl</kbd> (Windows) hoặc <kbd>Cmd</kbd> (Mac) để chọn nhiều lớp
                </small>
                <small style="color: #6b7280; display: block; margin-top: 4px;">
                  💡 Nếu chưa có lớp trong danh sách, vui lòng vào <a href="/quan-ly-lop-hoc"  style="color: #4f46e5;">Quản lý lớp học</a> để thêm mới
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
                {{ isSavingExam ? 'Đang lưu...' : 'Tạo' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Hidden input files for image and audio upload -->
    <input type="file" ref="hiddenImageInput" accept="image/*" multiple @change="onImageChange" style="display: none;">
    <input type="file" ref="hiddenAudioInput" accept="audio/*" @change="onAudioChange" style="display: none;">
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
      default: function () { return []; }
    },
    type: {
      type: String,
      default: 'dethi'
    },
    currentFolderId: {
      type: [String, Number, null],
      default: null
    }
  },
  data: function () {
    return {
      questions: JSON.parse(JSON.stringify(this.initialQuestions)),
      rawContentCopy: this.rawContent,
      codeMirrorEditor: null,
      showScoreConfigModal: false,
      showGuideModal: false,
      showExamConfigModal: false,
      trueFalseScorePercent: { 1: 10, 2: 25, 3: 50, 4: 100 },
      partScores: {}, // { 'PHẦN I': 5, ... }
      examConfig: {
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
      currentImagePartIndex: null,
      currentImageQIndex: null,
      currentAudioPartIndex: null,
      currentAudioQIndex: null,
      // Ngân hàng câu hỏi
      showQuestionBankModal: false,
      questionBankFolders: [], // Giữ nguyên cấu trúc tree
      questionBankExams: [],
      selectedFolderId: null,
      selectedExamId: null,
      selectedExamQuestions: [],
      selectedQuestionIds: [],
      isLoadingQuestions: false,
      expandedFolderIds: [], // Track các folder đang được mở rộng
      loadedFolderIds: [], // Track các folder đã load children từ API
      loadingFolderIds: [] // Track các folder đang loading
    };
  },
  computed: {
    isExamConfigValid() {
      if (this.type === 'dethi' || this.type === 'game') {
        return this.examConfig.title.trim() !== '' &&
          this.examConfig.grade !== '' &&
          this.examConfig.subject !== '' &&
          this.examConfig.cate_type_id !== '' &&
          this.examConfig.pricing_type !== '' &&
          (this.examConfig.pricing_type === 'free' ||
            (this.examConfig.pricing_type === 'paid' && this.examConfig.price > 0));
      } else {
        return this.examConfig.title.trim() !== '';
      }
    },
    filteredExams() {
      if (this.selectedFolderId === null) {
        // Hiển thị đề thi không có folder (folder_id = null)
        return this.questionBankExams.filter(exam => !exam.folder_id);
      } else {
        // Hiển thị đề thi trong folder đã chọn
        return this.questionBankExams.filter(exam => exam.folder_id === this.selectedFolderId);
      }
    },
    flattenedFolders() {
      const result = [];
      
      const flatten = (folders, depth = 0, parentExpanded = true) => {
        if (!folders || folders.length === 0) return;
        
        folders.forEach(folder => {
          const hasChildren = folder.children && folder.children.length > 0;
          const isExpanded = this.expandedFolderIds.includes(folder.id);
          
          // Chỉ thêm folder vào danh sách nếu parent đã được expand
          // (Với depth = 0, parentExpanded luôn là true để hiển thị root folders)
          if (parentExpanded || depth === 0) {
            result.push({
              id: folder.id,
              name: folder.name,
              depth: depth,
              hasChildren: hasChildren,
              owner: folder.owner,
              isExpanded: isExpanded,
              visible: true,
              children: folder.children || []
            });
            
            // Nếu folder có children và đang được expand, flatten children đệ quy
            // Truyền isExpanded của folder hiện tại làm parentExpanded cho children
            if (hasChildren && isExpanded && folder.children && folder.children.length > 0) {
              flatten(folder.children, depth + 1, isExpanded);
            }
          }
        });
      };
      
      // Bắt đầu với root folders (depth = 0, parentExpanded = true)
      flatten(this.questionBankFolders, 0, true);
      return result;
    }
  },
  watch: {
    rawContentCopy: {
      handler: "debouncedParseContent"
    },
    rawContent: {
      handler(newVal) {
        if (this.codeMirrorEditor && this.codeMirrorEditor.getValue() !== newVal) {
          this.codeMirrorEditor.setValue(newVal);
        }
      }
    }
  },
  mounted() {
    this.initCodeMirror();
    this.loadFontAwesome();
    this.loadSchoolClasses();
    // Nếu là sửa đề thi, có thể cần load subject theo grade
    if (this.examConfig && this.examConfig.grade) {
      this.changeGrade();
    }

    // Xử lý dữ liệu ảnh từ database (chuyển từ trường 'image' sang 'images')
    this.processImageData();
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
              'Ctrl-S': () => { this.$emit('save'); },
              'Cmd-S': () => { this.$emit('save'); },
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

          // Thêm event listener cho click để scroll đến câu hỏi tương ứng
          // CodeMirror không hỗ trợ event 'click' trực tiếp, nên dùng DOM event listener
          const wrapper = this.codeMirrorEditor.getWrapperElement();
          console.log('📌 Adding click listener to CodeMirror wrapper:', wrapper);
          
          const handleWrapperClick = (event) => {
            console.log('🖱️ DOM click event on wrapper detected', event);
            // Chỉ xử lý nếu click vào editor content (không phải vào scrollbar hoặc gutter)
            const editorDisplay = wrapper.querySelector('.CodeMirror-lines');
            const clickedTarget = event.target;
            
            console.log('🎯 Click target:', clickedTarget);
            console.log('📄 Editor display:', editorDisplay);
            
            // Kiểm tra xem click có trong editor content không
            if (editorDisplay && (editorDisplay.contains(clickedTarget) || clickedTarget === wrapper)) {
              console.log('✅ Click is in editor content, processing...');
              this.handleEditorClick(this.codeMirrorEditor, event);
            } else {
              console.log('⏭️ Click is outside editor content, skipping');
            }
          };
          
          wrapper.addEventListener('click', handleWrapperClick);
          console.log('✅ Click listener added successfully');
        }
      });
    },

    redirectToRouteLink(route) {
      window.location.href = route;
    },

    destroyCodeMirror() {
      if (this.codeMirrorEditor) {
        this.codeMirrorEditor.toTextArea();
        this.codeMirrorEditor = null;
      }
    },

    // Methods tiện ích cho CodeMirror
    formatCode() {
      // CodeMirror không có built-in format, có thể thêm thư viện js-beautify nếu cần
      alert('Format code feature - cần thêm js-beautify để sử dụng');
    },

    toggleTheme() {
      if (this.codeMirrorEditor) {
        const currentTheme = this.codeMirrorEditor.getOption('theme');
        const newTheme = currentTheme === 'default' ? 'monokai' : 'default';
        this.codeMirrorEditor.setOption('theme', newTheme);
      }
    },

    toggleWordWrap() {
      if (this.codeMirrorEditor) {
        const currentWrap = this.codeMirrorEditor.getOption('lineWrapping');
        this.codeMirrorEditor.setOption('lineWrapping', !currentWrap);
      }
    },

    toggleMinimap() {
      // CodeMirror không có minimap
      alert('CodeMirror không hỗ trợ minimap');
    },

    goToLine(lineNumber) {
      if (this.codeMirrorEditor && lineNumber > 0) {
        this.codeMirrorEditor.setCursor(lineNumber - 1, 0);
        this.codeMirrorEditor.focus();
      }
    },

    findAndReplace() {
      if (this.codeMirrorEditor) {
        this.codeMirrorEditor.execCommand('replace');
      }
    },

    // Ngân hàng câu hỏi
    async selectFromPersonalBank() {
      this.showQuestionBankModal = true;
      this.selectedFolderId = null;
      this.selectedExamId = null;
      this.selectedExamQuestions = [];
      this.selectedQuestionIds = [];
      try {
        // Đường dẫn đúng theo routes/web.php (nhóm 'de-thi' KHÔNG nằm trong 'crm-course')
        const response = await axios.get('/de-thi/question-bank/data');
        if (response.data.success) {
          // Giữ nguyên cấu trúc tree, không làm phẳng
          this.questionBankFolders = response.data.folders;
          console.log(this.questionBankFolders);
          this.questionBankExams = response.data.exams;
          // Mặc định collapse tất cả folders (không expand)
          this.expandedFolderIds = [];
          this.loadedFolderIds = [];
          this.loadingFolderIds = [];
          
          // Đánh dấu các folder đã có children từ API ban đầu
          this.markInitialFoldersAsLoaded(this.questionBankFolders);
        }
      } catch (error) {
        console.error('Error loading question bank:', error);
        alert('Không thể tải dữ liệu ngân hàng câu hỏi. Vui lòng thử lại.');
      }
    },

    getAllFolderIds(folders, ids = []) {
      folders.forEach(folder => {
        ids.push(folder.id);
        if (folder.children && folder.children.length > 0) {
          this.getAllFolderIds(folder.children, ids);
        }
      });
      return ids;
    },

    async toggleFolder(folderId) {
      const index = this.expandedFolderIds.indexOf(folderId);
      if (index > -1) {
        // Collapse: xóa khỏi danh sách expanded
        this.expandedFolderIds.splice(index, 1);
        // Force update để computed property được tính lại
        this.$forceUpdate();
      } else {
        // Expand: thêm vào danh sách expanded
        this.expandedFolderIds.push(folderId);
        
        // Nếu chưa load children, gọi API để kiểm tra và load
        if (!this.loadedFolderIds.includes(folderId) && !this.loadingFolderIds.includes(folderId)) {
          await this.loadFolderChildren(folderId);
        } else {
          // Đã có children hoặc đã load, chỉ cần force update sau một tick
          this.$nextTick(() => {
            this.$forceUpdate();
          });
        }
      }
    },

    async loadFolderChildren(folderId) {
      // Tránh load trùng lặp
      if (this.loadingFolderIds.includes(folderId)) {
        return;
      }
      
      this.loadingFolderIds.push(folderId);
      
      try {
        const response = await axios.get(`/de-thi/question-bank/folder/${folderId}/children`);
        if (response.data.success) {
          const children = response.data.children || [];
          
          // Tìm folder trong questionBankFolders và thêm children vào
          // (Gán cả khi children.length === 0 để đánh dấu đã kiểm tra)
          this.addChildrenToFolder(this.questionBankFolders, folderId, children);
          
          // Đánh dấu đã load (kể cả khi không có children)
          if (!this.loadedFolderIds.includes(folderId)) {
            this.loadedFolderIds.push(folderId);
          }
          
          // Force Vue update để computed property được tính lại
          this.$forceUpdate();
        }
      } catch (error) {
        console.error('Error loading folder children:', error);
        // Remove from expanded nếu có lỗi
        const expandedIndex = this.expandedFolderIds.indexOf(folderId);
        if (expandedIndex > -1) {
          this.expandedFolderIds.splice(expandedIndex, 1);
        }
        alert('Không thể tải folder con. Vui lòng thử lại.');
      } finally {
        // Remove khỏi loading list
        const loadingIndex = this.loadingFolderIds.indexOf(folderId);
        if (loadingIndex > -1) {
          this.loadingFolderIds.splice(loadingIndex, 1);
        }
      }
    },

    addChildrenToFolder(folders, folderId, children) {
      for (let folder of folders) {
        if (folder.id === folderId) {
          // Tìm folder đúng, thêm children vào - sử dụng Vue.set để đảm bảo reactivity
          this.$set(folder, 'children', children);
          return true;
        }
        // Đệ quy tìm trong children nếu có
        if (folder.children && folder.children.length > 0) {
          if (this.addChildrenToFolder(folder.children, folderId, children)) {
            return true;
          }
        }
      }
      return false;
    },

    markInitialFoldersAsLoaded(folders) {
      folders.forEach(folder => {
        // Nếu folder có children từ API ban đầu, đánh dấu là đã load
        if (folder.children && folder.children.length > 0) {
          this.loadedFolderIds.push(folder.id);
          // Đệ quy cho children
          this.markInitialFoldersAsLoaded(folder.children);
        }
      });
    },

    isFolderExpanded(folderId) {
      return this.expandedFolderIds.includes(folderId);
    },


    async selectFolder(folderId) {
      // Chọn folder
      this.selectedFolderId = folderId;
      this.selectedExamId = null;
      this.selectedExamQuestions = [];
      this.selectedQuestionIds = [];
      
      // Tìm folder trong cây
      const folder = this.findFolderById(this.questionBankFolders, folderId);
      if (folder) {
        const hasChildren = folder.children && folder.children.length > 0;
        const isExpanded = this.expandedFolderIds.includes(folderId);
        const isLoaded = this.loadedFolderIds.includes(folderId);
        
        // Nếu chưa load children, gọi API để kiểm tra và load
        if (!isLoaded && !this.loadingFolderIds.includes(folderId)) {
          await this.loadFolderChildren(folderId);
          // Sau khi load xong, tự động expand nếu có children
          const updatedFolder = this.findFolderById(this.questionBankFolders, folderId);
          if (updatedFolder && updatedFolder.children && updatedFolder.children.length > 0) {
            if (!this.expandedFolderIds.includes(folderId)) {
              this.expandedFolderIds.push(folderId);
              this.$nextTick(() => {
                this.$forceUpdate();
              });
            }
          }
        }
        // Nếu đã có children nhưng chưa expand, tự động expand
        else if (hasChildren && !isExpanded) {
          this.expandedFolderIds.push(folderId);
          this.$nextTick(() => {
            this.$forceUpdate();
          });
        }
      }
    },

    findFolderById(folders, folderId) {
      for (let folder of folders) {
        if (folder.id === folderId) {
          return folder;
        }
        if (folder.children && folder.children.length > 0) {
          const found = this.findFolderById(folder.children, folderId);
          if (found) return found;
        }
      }
      return null;
    },

    async selectExam(examId) {
      this.selectedExamId = examId;
      this.selectedExamQuestions = [];
      this.selectedQuestionIds = [];
      this.isLoadingQuestions = true;

      try {
        // Đường dẫn đúng theo routes/web.php (nhóm 'de-thi' KHÔNG nằm trong 'crm-course')
        const response = await axios.get(`/de-thi/question-bank/exam/${examId}/questions`);
        if (response.data.success) {
          const rawQuestions = response.data.questions || [];
          if (this.type === 'game') {
            const filteredParts = rawQuestions
              .map(part => {
                const filteredQuestions = (part.questions || []).filter(q => q.question_type === 'multiple_choice');
                return {
                  ...part,
                  questions: filteredQuestions
                };
              })
              .filter(part => part.questions && part.questions.length > 0);

            this.selectedExamQuestions = filteredParts;
          } else {
            this.selectedExamQuestions = rawQuestions;
          }
        } else {
          alert('Không thể tải câu hỏi. Vui lòng thử lại.');
        }
      } catch (error) {
        console.error('Error loading questions:', error);
        alert('Không thể tải câu hỏi. Vui lòng thử lại.');
      } finally {
        this.isLoadingQuestions = false;
      }
    },

    toggleQuestionSelection(questionId) {
      const index = this.selectedQuestionIds.indexOf(questionId);
      if (index > -1) {
        this.selectedQuestionIds.splice(index, 1);
      } else {
        this.selectedQuestionIds.push(questionId);
      }
    },

    getQuestionTypeLabel(type) {
      const labels = {
        'multiple_choice': 'Trắc nghiệm',
        'true_false_grouped': 'Đúng/Sai',
        'fill_in_blank': 'Điền vào chỗ trống',
        'short_answer': 'Tự luận'
      };
      return labels[type] || 'Khác';
    },

    insertSelectedQuestions() {
      if (this.selectedQuestionIds.length === 0) {
        alert('Vui lòng chọn ít nhất một câu hỏi');
        return;
      }

      // Tìm các câu hỏi đã chọn
      const selectedQuestions = [];
      this.selectedExamQuestions.forEach(part => {
        part.questions.forEach(question => {
          if (this.selectedQuestionIds.includes(question.id)) {
            selectedQuestions.push({
              ...question,
              part: part.part,
              part_title: part.part_title
            });
          }
        });
      });

      // Chuyển đổi câu hỏi sang format text để chèn vào editor
      let textToInsert = '\n\n';
      
      // Chèn trực tiếp các câu hỏi đã chọn, bỏ qua tiêu đề phần
      selectedQuestions.forEach(question => {
        // Thêm tiền tố [DT] cho câu hỏi điền vào chỗ trống
        const contentPrefix = question.question_type === 'fill_in_blank' ? '[DT] ' : '';
        textToInsert += `Câu ${question.question_no}. ${contentPrefix}${question.content}\n`;
        
        // Thêm đáp án
        if (question.answers && question.answers.length > 0) {
          question.answers.forEach(answer => {
            const prefix = answer.is_correct ? '*' : '';
            if (question.question_type === 'multiple_choice') {
              textToInsert += `${prefix}${answer.label}. ${answer.content}\n`;
            } else if (question.question_type === 'true_false_grouped') {
              textToInsert += `${prefix}${answer.label}) ${answer.content}\n`;
            } else if (question.question_type === 'fill_in_blank') {
              textToInsert += `Answer: ${answer.content}\n`;
            }
          });
        }
        
        // Thêm lời giải
        if (question.explanation) {
          textToInsert += `Lời giải: ${question.explanation}\n`;
        }
        
        textToInsert += '\n';
      });

      // Chèn vào editor
      if (this.codeMirrorEditor) {
        const cursor = this.codeMirrorEditor.getCursor();
        const doc = this.codeMirrorEditor.getDoc();
        doc.replaceRange(textToInsert, cursor);
        
        // Trigger change event để parse lại
        this.rawContentCopy = this.codeMirrorEditor.getValue();
        this.$nextTick(() => {
          this.parseContent();
        });
      }

      // Đóng modal
      this.showQuestionBankModal = false;
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

    // Insert new Math formula at cursor position
    insertMathFormula() {
      if (!this.codeMirrorEditor) {
        console.error('CodeMirror editor is not initialized');
        this.$message.error('Editor chưa sẵn sàng, vui lòng thử lại');
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
        this.$message.error('Có lỗi xảy ra: ' + error.message);
      }
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

    changeGrade() {
      axios.post('/de-thi/filter-subject', { grade: this.examConfig.grade })
        .then(response => {

          this.subject = response.data;
        })
        .catch(error => {

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
    loadFontAwesome() {
      // Load Font Awesome nếu chưa có
      if (!document.querySelector('link[href*="font-awesome"]')) {
        const fontAwesome = document.createElement('link');
        fontAwesome.rel = 'stylesheet';
        fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
        document.head.appendChild(fontAwesome);
      }
    },

    /* COMMENTED OUT - DUPLICATE OLD METHOD USING TEXTAREA
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
    END OF COMMENTED OUT DUPLICATE METHOD */
    saveExam() {
      // Mở modal ngay lập tức để UX tốt hơn
      this.showExamConfigModal = true;
    },
    validateQuestions() {
      const requireScore = this.type !== 'game';
      for (let partIndex = 0; partIndex < this.questions.length; partIndex++) {
        const part = this.questions[partIndex];
        for (let questionIndex = 0; questionIndex < part.questions.length; questionIndex++) {
          const question = part.questions[questionIndex];

          // Kiểm tra điểm số (chỉ áp dụng cho đề thi/bài tập)
          if (requireScore) {
            if (!question.score || question.score <= 0) {
              return {
                isValid: false,
                message: `Vui lòng nhập điểm cho câu ${question.question_no} trong ${part.part}`
              };
            }
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
    loadSchoolClasses() {
      axios.get('/api/school-classes/active')
        .then(response => {
          this.schoolClasses = response.data;
        })
        .catch(error => {
          console.error('Error loading school classes:', error);
        });
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
      if (!validationResult.isValid && this.type === 'dethi') {
        this.showErrorAlert(validationResult.message);
        return;
      }

      if (!this.isExamConfigValid && this.type === 'dethi') {
        alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        return;
      }

      // Validate thời gian nếu chọn time_limited
      if (this.examConfig.access_type === 'time_limited') {
        if (!this.examConfig.start_time || !this.examConfig.end_time) {
          alert('Vui lòng chọn thời gian bắt đầu và kết thúc!');
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

      this.isSavingExam = true;
      // Nếu là game, set điểm mặc định cho từng câu để hệ thống không phụ thuộc vào điểm
      if (this.type === 'game') {
        this.questions.forEach(part => {
          part.questions.forEach(q => {
            q.score = 1;
          });
        });
      }
      const examData = {
        id: this.examConfig.id ?? null,
        type: this.type,
        title: this.examConfig.title,
        description: this.examConfig.description,
        time: this.examConfig.time,
        grade: this.examConfig.grade,
        subject: this.examConfig.subject,
        cate_type_id: this.examConfig.cate_type_id,
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
        class_ids: this.examConfig.access_type === 'class' ? this.examConfig.classes : [],
        folder_id: this.currentFolderId
      };
    
      axios.post('/de-thi/store-exam', examData)
        .then(response => {
          this.isSavingExam = false;
          if (response.data.success) {
            this.showSuccessAlert('Tạo đề thi thành công!');
            this.showExamConfigModal = false;
            if (this.type === 'dethi') {
              const folderQuery = this.currentFolderId ? `?folder_id=${this.currentFolderId}` : '?folder_id=root';
              window.location.href = '/de-thi/danh-sach.html' + folderQuery;
            } else {
              const folderQuery = this.currentFolderId ? `?folder_id=${this.currentFolderId}` : '?folder_id=root';
              window.location.href = '/bai-tap/danh-sach.html' + folderQuery;
            }
          } else {
            this.showErrorAlert('Có lỗi xảy ra: ' + response.data.error);
          }
        })
        .catch(error => {
          this.isSavingExam = false;
          this.showErrorAlert('Có lỗi xảy ra khi tạo đề thi!');
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
    triggerImageInput(partIndex, qIndex) {
      // Lưu thông tin câu hỏi hiện tại để sử dụng trong onImageChange
      this.currentImagePartIndex = partIndex;
      this.currentImageQIndex = qIndex;

      // Sử dụng input file ẩn duy nhất
      if (this.$refs.hiddenImageInput) {
        this.$refs.hiddenImageInput.click();
      } else {
        console.warn('Hidden image input not found');
      }
    },
    onImageChange(event) {
      const files = event.target.files;
      if (files && files.length > 0) {
        // Sử dụng thông tin câu hỏi đã lưu
        const partIndex = this.currentImagePartIndex;
        const qIndex = this.currentImageQIndex;

        if (partIndex === undefined || qIndex === undefined) {
          console.error('Part index or question index not set');
          return;
        }

        // Khởi tạo mảng images nếu chưa có
        if (!this.questions[partIndex].questions[qIndex].images) {
          this.$set(this.questions[partIndex].questions[qIndex], 'images', []);
        }

        // Xử lý từng file
        Array.from(files).forEach(file => {

          // Kiểm tra kích thước file (tối đa 5MB)
          if (file.size > 5 * 1024 * 1024) {
            alert(`File ${file.name} quá lớn. Kích thước tối đa là 5MB.`);
            return;
          }

          // Kiểm tra loại file
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
          if (!allowedTypes.includes(file.type)) {
            alert(`File ${file.name} không phải là ảnh hợp lệ. Chỉ chấp nhận JPEG, PNG, GIF, WebP.`);
            return;
          }

          // Upload ảnh lên server
          const formData = new FormData();
          formData.append('image', file);

          axios.post('/api/upload-question-image', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          }).then(response => {
            if (response.data.success) {
              const imageData = {
                url: response.data.url,
                name: file.name,
                size: file.size,
                type: file.type,
                uploaded_at: new Date().toISOString(),
                server_filename: response.data.filename
              };

              // Sử dụng Vue.set để đảm bảo reactivity
              if (!this.questions[partIndex].questions[qIndex].images) {
                this.$set(this.questions[partIndex].questions[qIndex], 'images', []);
              }

              this.questions[partIndex].questions[qIndex].images.push(imageData);

              // Force update để đảm bảo Vue re-render
              this.$set(this.questions[partIndex].questions[qIndex], 'images', [...this.questions[partIndex].questions[qIndex].images]);

              // Force Vue to re-render the entire component
              this.$forceUpdate();

              // Alternative approach: Update the entire questions array
              const updatedQuestions = [...this.questions];
              this.questions = updatedQuestions;
            } else {
              alert(`Lỗi upload ảnh ${file.name}: ${response.data.error}`);
            }
          }).catch(error => {
            console.error('Upload error:', error);
            alert(`Lỗi upload ảnh ${file.name}. Vui lòng thử lại.`);
          });
        });
      }

      // Reset input để có thể chọn lại file
      event.target.value = '';
    },



    parseContent() {
      // Lưu lại điểm số cũ theo part + question_no
      const scoreMap = {};
      // Lưu lại ảnh cũ theo part + question_no
      const imagesMap = {};
      // Lưu lại audio cũ theo part + question_no
      const audioMap = {};
      
      this.questions.forEach(part => {
        part.questions.forEach(q => {
          if (q.question_no != null) {
            const key = part.part + '___' + q.question_no;
            scoreMap[key] = q.score;
            
            // Lưu ảnh
            if (q.images && q.images.length > 0) {
              imagesMap[key] = q.images;
              console.log(`📸 Đã lưu ${q.images.length} ảnh cho câu ${q.question_no} trong ${part.part}`);
            }
            
            // Lưu audio
            if (q.audio) {
              audioMap[key] = q.audio;
            }
          }
        });
      });
      
      console.log('💾 Images Map:', imagesMap);
      console.log('💾 Score Map:', scoreMap);
      console.log('💾 Audio Map:', audioMap);
      
      const normalized = this.normalizeText(this.rawContentCopy);
      axios
        .post("/api/parse-content", {
          rawContent: normalized,
          originParts: this.questions
        })
        .then(res => {
          // Gán lại điểm số, ảnh, audio cũ cho dữ liệu mới
          res.data.questions.forEach(part => {
            part.questions.forEach(q => {
              const key = part.part + '___' + q.question_no;
              
              // Gán lại điểm số
              if (q.question_no != null && scoreMap[key] != null) {
                q.score = scoreMap[key];
              }
              
              // Gán lại ảnh cũ nếu có
              if (q.question_no != null && imagesMap[key] != null) {
                q.images = imagesMap[key];
                console.log(`✅ Đã phục hồi ${q.images.length} ảnh cho câu ${q.question_no} trong ${part.part}`);
              } else {
                // Đảm bảo không có ảnh cũ thì set null
                q.images = null;
              }
              
              // Gán lại audio cũ nếu có
              if (q.question_no != null && audioMap[key] != null) {
                q.audio = audioMap[key];
              }
            });
          });
          
          console.log('🎉 Parse xong! Questions với ảnh:', res.data.questions);
          
          this.questions = res.data.questions;
          
          // Force update để Vue re-render
          this.$forceUpdate();
          
          // Re-render MathJax nếu có
          this.$nextTick(() => {
            if (window.MathJax && window.MathJax.typesetPromise) {
              window.MathJax.typesetPromise();
            }
          });
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
    
    // Convert line breaks (\n) thành <br> tags, nhưng tránh convert trong LaTeX formulas
    convertLineBreaksToHtml(content) {
      if (!content) return '';
      
      // Tách content thành các phần: LaTeX formulas và text thường
      // Pattern để match LaTeX: \(...\), \[...\], $...$, $$...$$
      const latexPatterns = [
        /\\\(([^\\]+|\\[^\)])*\\\)/g,  // \(...\)
        /\\\[([^\\]+|\\[^\]]+)*\\\]/g,  // \[...\]
        /\$\$([^$]+|\\[^$]+)*\$\$/g,    // $$...$$
        /\$([^$\n]+)\$/g                 // $...$ (inline, không có newline)
      ];
      
      // Tìm tất cả các LaTeX formulas và thay thế tạm thời bằng placeholders
      const placeholders = [];
      let placeholderIndex = 0;
      let processedContent = content;
      
      latexPatterns.forEach(pattern => {
        processedContent = processedContent.replace(pattern, (match) => {
          const placeholder = `__LATEX_PLACEHOLDER_${placeholderIndex}__`;
          placeholders[placeholderIndex] = match;
          placeholderIndex++;
          return placeholder;
        });
      });
      
      // Convert \n thành <br> trong phần text (không phải LaTeX)
      processedContent = processedContent.replace(/\n/g, '<br>');
      
      // Khôi phục lại các LaTeX formulas
      placeholders.forEach((latex, index) => {
        processedContent = processedContent.replace(`__LATEX_PLACEHOLDER_${index}__`, latex);
      });
      
      return processedContent;
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
    
    // Convert img_key về URL đầy đủ
    convertImageKeyToUrl(imgKey) {
      // Tìm trong images array của question nếu có
      // Hoặc reconstruct URL từ img_key
      // Format mặc định: /exam_images/{imgKey}.{extension}
      // Nhưng extension không biết, nên cần tìm trong images array
      return null;
    },
    
    // Render content với images inline - convert [img:key] thành HTML img tags
    renderContentWithImages(question) {
      let content = question.content || '';
      
      // Pattern để match cả markdown cũ và format mới: ![alt](url) hoặc [img:key]
      const oldImagePattern = /!\[([^\]]*)\]\(([^)]+)\)/g;
      const newImagePattern = /\[img:([^\]]+)\]/g;
      
      // Xử lý format cũ (backward compatibility)
      content = content.replace(oldImagePattern, (match, alt, url) => {
        const normalizedUrl = url.replace(/['"]/g, '').trim().replace(/^\/+/, '/');
        const safeAlt = (alt || 'Image').replace(/"/g, '&quot;');
        return `<img src="${normalizedUrl}" alt="${safeAlt}" style="max-width: 100%; height: auto; margin: 10px 0; border-radius: 8px; display: block;" />`;
      });
      
      // Xử lý format mới [img:key]
      content = content.replace(newImagePattern, (match, imgKey) => {
        // Tìm URL từ images array hoặc reconstruct từ img_key
        let imageUrl = null;
        
        // Thử tìm trong images array
        if (question.images && question.images.length > 0) {
          const foundImage = question.images.find(img => {
            const url = img.url || img;
            const key = this.extractImageKeyFromUrl(url);
            return key === imgKey;
          });
          
          if (foundImage) {
            imageUrl = foundImage.url || foundImage;
          }
        }
        
        // Nếu không tìm thấy, thử reconstruct URL (giả định extension là png, jpg, jpeg, gif, webp)
        if (!imageUrl) {
          // Thử các extension phổ biến
          const extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
          // Không thể biết extension chính xác, nên cần lưu mapping
          // Tạm thời dùng format /exam_images/{key}.png
          imageUrl = `/exam_images/${imgKey}.png`;
        }
        
        const safeAlt = 'Image';
        return `<img src="${imageUrl}" alt="${safeAlt}" style="max-width: 100%; height: auto; margin: 10px 0; border-radius: 8px; display: block;" />`;
      });
      
      // Convert line breaks (\n) thành <br> tags - QUAN TRỌNG để hiển thị xuống dòng từ Word
      // Nhưng cần tránh convert \n trong LaTeX formulas
      content = this.convertLineBreaksToHtml(content);
      
      // Render MathJax sau khi đã convert images và line breaks
      content = this.renderMath(content);
      
      return content;
    },
    
    // Method chung để convert markdown images thành HTML img tags (dùng cho answers, explanation, v.v.)
    renderContentWithMarkdownImages(content) {
      if (!content) return '';
      
      // Pattern để match cả markdown cũ và format mới
      const oldImagePattern = /!\[([^\]]*)\]\(([^)]+)\)/g;
      const newImagePattern = /\[img:([^\]]+)\]/g;
      
      // Xử lý format cũ (backward compatibility)
      let renderedContent = content.replace(oldImagePattern, (match, alt, url) => {
        const normalizedUrl = url.replace(/['"]/g, '').trim().replace(/^\/+/, '/');
        const safeAlt = (alt || 'Image').replace(/"/g, '&quot;');
        return `<img src="${normalizedUrl}" alt="${safeAlt}" style="max-width: 100%; height: auto; margin: 10px 0; border-radius: 8px; display: block;" />`;
      });
      
      // Xử lý format mới [img:key] - cần tìm URL từ rawContentCopy hoặc images array
      renderedContent = renderedContent.replace(newImagePattern, (match, imgKey) => {
        // Tìm URL từ rawContentCopy bằng cách tìm markdown cũ có cùng key
        let imageUrl = null;
        
        // Tìm trong rawContentCopy để lấy URL đầy đủ
        if (this.rawContentCopy) {
          const urlPattern = new RegExp(`!\\[[^\\]]*\\]\\(([^)]*${imgKey.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}[^)]*)\\)`, 'i');
          const urlMatch = this.rawContentCopy.match(urlPattern);
          if (urlMatch && urlMatch[1]) {
            imageUrl = urlMatch[1].trim();
          }
        }
        
        // Nếu không tìm thấy, thử reconstruct URL
        if (!imageUrl) {
          imageUrl = `/exam_images/${imgKey}.png`; // Default extension
        }
        
        const safeAlt = 'Image';
        return `<img src="${imageUrl}" alt="${safeAlt}" style="max-width: 100%; height: auto; margin: 10px 0; border-radius: 8px; display: block;" />`;
      });
      
      // Convert line breaks (\n) thành <br> tags - QUAN TRỌNG để hiển thị xuống dòng từ Word
      renderedContent = this.convertLineBreaksToHtml(renderedContent);
      
      // Render MathJax sau khi đã convert images và line breaks
      renderedContent = this.renderMath(renderedContent);
      
      return renderedContent;
    },
    
    saveScoreConfig() {
      console.log(this.questions);
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

    // Xử lý paste ảnh và LaTeX vào editor
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

      if (hasImage && imageFile) {
        // Xử lý paste ảnh (giữ nguyên logic cũ)
        this.handleImagePaste(cm, event, imageFile);
        return;
      }

      // Không có ảnh, kiểm tra text có chứa LaTeX không
      const pastedText = clipboardData.getData('text/plain');
      if (pastedText) {
        // Kiểm tra xem text có chứa LaTeX commands không
        const latexPattern = /\\[a-zA-Z@]+|\\[^a-zA-Z@\s]/;
        if (latexPattern.test(pastedText)) {
          // Text chứa LaTeX, xử lý để bọc từng công thức LaTeX riêng lẻ
          const processedText = this.wrapLatexFormulas(pastedText);
          
          // Nếu có thay đổi, thay thế paste mặc định
          if (processedText !== pastedText) {
            event.preventDefault();
            event.stopPropagation();
            
            const cursor = cm.getCursor();
            cm.replaceRange(processedText, cursor);
            
            // Di chuyển cursor sau text đã paste
            const finalCursor = {
              line: cursor.line,
              ch: cursor.ch + processedText.length
            };
            cm.setCursor(finalCursor);
            cm.focus();
            
            // Cập nhật rawContentCopy
            this.rawContentCopy = cm.getValue();
            return;
          }
        }
      }

      // Không có ảnh và không phải LaTeX, cho phép paste bình thường
      return;
    },

    // Hàm bọc từng công thức LaTeX riêng lẻ bằng \( và \)
    wrapLatexFormulas(text) {
      // Tìm và bọc từng công thức LaTeX chưa được bọc
      // Pattern: tìm các đoạn bắt đầu bằng \ (không phải \() và kéo dài cho đến khi gặp khoảng trắng hoặc ký tự không phải LaTeX
      
      let result = '';
      let i = 0;
      
      while (i < text.length) {
        // Bỏ qua nếu đã có \( \) rồi
        if (text.substring(i, i + 2) === '\\(') {
          // Tìm đến \)
          let j = i + 2;
          while (j < text.length) {
            if (text.substring(j, j + 2) === '\\)') {
              result += text.substring(i, j + 2);
              i = j + 2;
              break;
            }
            j++;
          }
          if (j >= text.length) {
            result += text[i];
            i++;
          }
          continue;
        }
        
        // Tìm công thức LaTeX bắt đầu từ \
        if (text[i] === '\\' && (i === 0 || text[i - 1] !== '\\')) {
          let start = i;
          let j = i + 1;
          let braceCount = 0;
          let parenCount = 0;
          let foundEnd = false;
          
          // Đọc lệnh LaTeX đầu tiên (ví dụ: \vec, \left, etc.)
          while (j < text.length && text[j].match(/[a-zA-Z@]/)) {
            j++;
          }
          
          // Tiếp tục đọc cho đến khi gặp khoảng trắng hoặc ký tự không phải LaTeX
          while (j < text.length && !foundEnd) {
            const char = text[j];
            
            // Đếm dấu ngoặc
            if (char === '{') braceCount++;
            else if (char === '}') braceCount--;
            else if (char === '(') parenCount++;
            else if (char === ')') parenCount--;
            
            // Kiểm tra ký tự có phải LaTeX không
            const isLatexChar = char.match(/[\\a-zA-Z0-9@{}()\[\]+=\-*\/^_|&%#$!?~`'":;.,<>]/);
            
            // Kết thúc công thức khi:
            // 1. Gặp khoảng trắng và tất cả dấu ngoặc đã đóng
            // 2. Gặp ký tự không phải LaTeX (nhưng giữ lại dấu chấm, phẩy ở cuối)
            if (braceCount === 0 && parenCount === 0) {
              if (char === ' ' || char === '\n' || char === '\t') {
                // Kiểm tra ký tự trước đó
                if (j > start) {
                  const prevChar = text[j - 1];
                  // Nếu ký tự trước là ký tự LaTeX hợp lệ, thì đây là kết thúc công thức
                  if (prevChar.match(/[a-zA-Z0-9})]/)) {
                    foundEnd = true;
                    break;
                  }
                }
              } else if (!isLatexChar) {
                // Ký tự không phải LaTeX
                foundEnd = true;
                break;
              }
            }
            
            j++;
            
            // Giới hạn độ dài
            if (j - start > 500) break;
          }
          
          if (j > start) {
            // Lấy công thức
            let formulaEnd = j;
            
            // Nếu ký tự cuối là dấu chấm, phẩy, thì giữ lại
            if (formulaEnd < text.length) {
              const nextChar = text[formulaEnd];
              if (nextChar === '.' || nextChar === ',' || nextChar === ';' || nextChar === ':') {
                formulaEnd++;
              }
            }
            
            const formula = text.substring(start, formulaEnd);
            if (formula && !formula.startsWith('\\(')) {
              result += `\\(${formula}\\)`;
              i = formulaEnd;
              continue;
            }
          }
        }
        
        result += text[i];
        i++;
      }
      
      return result;
    },

    // Xử lý paste ảnh (tách riêng để code gọn hơn)
    async handleImagePaste(cm, event, imageFile) {

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

    // Xử lý click trong editor để scroll đến câu hỏi tương ứng
    handleEditorClick(cm, event) {
      try {
        console.log('🔍 Editor click detected');
        const cursor = cm.getCursor();
        const lineNumber = cursor.line;
        const lineContent = cm.getLine(lineNumber);
        
        console.log('📍 Cursor position:', { line: lineNumber, ch: cursor.ch });
        console.log('📄 Line content:', lineContent);
        
        // Tìm phần (PHẦN X) và câu hỏi (Câu X.) trong các dòng gần đó
        let partTitle = null;
        let questionNo = null;
        let partIndex = null;
        
        // Tìm ngược lên để tìm phần và câu hỏi (tối đa 100 dòng để đảm bảo tìm được phần)
        console.log('🔍 Searching backwards for part and question pattern...');
        for (let i = lineNumber; i >= Math.max(0, lineNumber - 100); i--) {
          const line = cm.getLine(i);
          
          // Tìm pattern "PHẦN X" hoặc "Phần X" (có thể là số La Mã hoặc số thường)
          if (!partTitle) {
            const partMatch = line.match(/PHẦN\s+([IVX\d]+)[\s:\.]/i) || 
                            line.match(/Phần\s+([IVX\d]+)[\s:\.]/i) ||
                            line.match(/PHẦN\s+([A-Z\d]+)/i);
            if (partMatch) {
              partTitle = partMatch[0].trim().replace(/[:\s\.]+$/, '');
              console.log(`✅ Found part at line ${i}:`, partTitle);
            }
          }
          
          // Tìm pattern "Câu X."
          if (!questionNo) {
            const questionMatch = line.match(/Câu\s+(\d+)\./i);
            if (questionMatch) {
              questionNo = parseInt(questionMatch[1]);
              console.log(`✅ Found question at line ${i}:`, questionNo);
            }
          }
          
          // Nếu đã tìm thấy cả phần và câu hỏi, dừng lại
          if (partTitle && questionNo) {
            break;
          }
        }
        
        if (questionNo) {
          console.log('🎯 Target:', { partTitle, questionNo });
          
          // Tìm element tương ứng trong preview - dùng setTimeout để đảm bảo DOM đã render
          setTimeout(() => {
            let questionElement = null;
            
            // Nếu có partTitle, tìm câu hỏi trong phần đó
            if (partTitle) {
              // Tìm tất cả các phần có title
              const allPartElements = document.querySelectorAll('[data-part-title]');
              console.log('📋 All parts found:', Array.from(allPartElements).map(el => ({
                title: el.getAttribute('data-part-title'),
                index: el.getAttribute('data-part-index')
              })));
              
              // Tìm phần khớp với partTitle (so sánh phần đầu của title)
              for (const partEl of allPartElements) {
                const partElTitle = partEl.getAttribute('data-part-title');
                // So sánh phần (ví dụ: "PHẦN I", "PHẦN 1", "Phần I")
                const partElTitleNormalized = partElTitle ? partElTitle.toUpperCase().replace(/\s+/g, ' ') : '';
                const partTitleNormalized = partTitle.toUpperCase().replace(/\s+/g, ' ');
                
                // Kiểm tra xem có khớp không (ít nhất 6 ký tự đầu)
                if (partElTitleNormalized.includes(partTitleNormalized.substring(0, 6)) || 
                    partTitleNormalized.includes(partElTitleNormalized.substring(0, 6))) {
                  partIndex = partEl.getAttribute('data-part-index');
                  console.log('✅ Found matching part:', partElTitle, 'at index:', partIndex);
                  
                  // Tìm câu hỏi trong phần này
                  questionElement = document.querySelector(`[data-question-no="${questionNo}"][data-part-index="${partIndex}"]`);
                  
                  if (questionElement) {
                    console.log('✅ Found question in matching part');
                    break;
                  }
                }
              }
            }
            
            // Nếu không tìm thấy với partTitle, thử tìm theo question_no và partIndex
            if (!questionElement && partIndex !== null) {
              questionElement = document.querySelector(`[data-question-no="${questionNo}"][data-part-index="${partIndex}"]`);
            }
            
            // Fallback: tìm câu hỏi đầu tiên có số câu khớp (nếu không tìm thấy phần)
            if (!questionElement) {
              questionElement = document.querySelector(`[data-question-no="${questionNo}"]`);
              console.log('⚠️ Using fallback: found first question with number:', questionNo);
            }
            
            console.log('🔍 Question element found:', questionElement);
            
            if (questionElement) {
              // Tìm preview container có scroll - thử nhiều selector
              let previewContainer = questionElement.closest('.preview-content');
              if (!previewContainer) {
                previewContainer = questionElement.closest('.content-height');
              }
              if (!previewContainer) {
                // Tìm container có class chứa preview
                const previewBox = questionElement.closest('.preview-box');
                if (previewBox) {
                  previewContainer = previewBox.querySelector('.preview-content, .content-height');
                }
              }
              
              console.log('📦 Preview container found:', previewContainer);
              
              if (previewContainer) {
                // Kiểm tra xem container có scroll được không
                const canScroll = previewContainer.scrollHeight > previewContainer.clientHeight;
                console.log('📊 Container scroll info:', {
                  scrollHeight: previewContainer.scrollHeight,
                  clientHeight: previewContainer.clientHeight,
                  scrollTop: previewContainer.scrollTop,
                  canScroll: canScroll,
                  computedStyle: window.getComputedStyle(previewContainer).overflowY
                });
                
                // Tính toán vị trí scroll trong container
                const containerRect = previewContainer.getBoundingClientRect();
                const elementRect = questionElement.getBoundingClientRect();
                
                console.log('📐 Rects:', {
                  container: {
                    top: containerRect.top,
                    height: containerRect.height,
                    scrollTop: previewContainer.scrollTop
                  },
                  element: {
                    top: elementRect.top,
                    height: elementRect.height
                  }
                });
                
                // Tính offset cần scroll - đưa element vào giữa container
                const relativeTop = elementRect.top - containerRect.top;
                const scrollTop = previewContainer.scrollTop + relativeTop - (containerRect.height / 2) + (elementRect.height / 2);
                
                console.log('📜 Scrolling to:', scrollTop, '(current scrollTop:', previewContainer.scrollTop + ')');
                
                // Scroll trong container
                previewContainer.scrollTo({
                  top: Math.max(0, scrollTop),
                  behavior: 'smooth'
                });
                
                // Kiểm tra lại sau khi scroll
                setTimeout(() => {
                  console.log('📊 After scroll - scrollTop:', previewContainer.scrollTop);
                }, 100);
                
                console.log('✅ Scroll command executed');
              } else {
                console.warn('⚠️ Preview container not found, using fallback');
                // Fallback: scroll toàn bộ page nếu không tìm thấy container
                questionElement.scrollIntoView({
                  behavior: 'smooth',
                  block: 'center'
                });
              }
              
              // Highlight tạm thời để người dùng dễ nhận biết
              questionElement.style.transition = 'background-color 0.3s';
              questionElement.style.backgroundColor = '#fff3cd';
              setTimeout(() => {
                questionElement.style.backgroundColor = '';
              }, 1500);
            } else {
              console.error('❌ Question element not found for question:', questionNo);
              // Debug: list all question elements
              const allQuestions = document.querySelectorAll('[data-question-no]');
              console.log('📋 All available questions:', Array.from(allQuestions).map(el => ({
                no: el.getAttribute('data-question-no'),
                element: el,
                visible: el.offsetParent !== null
              })));
            }
          }, 100);
        } else {
          console.log('❌ No question number found near cursor');
        }
      } catch (error) {
        console.error('❌ Error in handleEditorClick:', error);
      }
    },

    editImage(partIndex, qIndex, imgIndex) {
      // Tạo modal để sửa ảnh
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
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
      `;

      const currentImage = this.questions[partIndex].questions[qIndex].images[imgIndex];

      modalContent.innerHTML = `
        <h3 style="margin: 0 0 16px 0; color: #333; font-size: 18px;">
          <i class="fas fa-edit"></i> Sửa ảnh câu hỏi
        </h3>
        <div style="margin-bottom: 20px;">
          <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
            Ảnh hiện tại:
          </label>
          <img src="${currentImage.url || currentImage}" alt="Current Image" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid #e5e7eb;">
        </div>
        <div style="margin-bottom: 20px;">
          <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
            Thay thế bằng ảnh mới:
          </label>
          <input type="file" id="newImageInput" accept="image/*" style="width: 100%; padding: 8px; border: 2px solid #e5e7eb; border-radius: 6px;">
        </div>
        <div style="display: flex; gap: 8px; justify-content: flex-end;">
          <button id="cancelBtn" style="padding: 10px 20px; border: 1px solid #d1d5db; background: #f9fafb; border-radius: 6px; cursor: pointer; font-size: 14px;">
            Hủy
          </button>
          <button id="saveBtn" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            <i class="fas fa-save"></i> Lưu thay đổi
          </button>
        </div>
      `;

      modal.appendChild(modalContent);
      document.body.appendChild(modal);

      const newImageInput = modal.querySelector('#newImageInput');
      const cancelBtn = modal.querySelector('#cancelBtn');
      const saveBtn = modal.querySelector('#saveBtn');

      const closeModal = () => {
        document.body.removeChild(modal);
      };

      cancelBtn.onclick = closeModal;

      saveBtn.onclick = () => {
        const file = newImageInput.files[0];
        if (file) {
          // Kiểm tra kích thước file
          if (file.size > 5 * 1024 * 1024) {
            alert('File quá lớn. Kích thước tối đa là 5MB.');
            return;
          }

          // Kiểm tra loại file
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
          if (!allowedTypes.includes(file.type)) {
            alert('File không phải là ảnh hợp lệ. Chỉ chấp nhận JPEG, PNG, GIF, WebP.');
            return;
          }

          // Upload ảnh mới lên server
          const formData = new FormData();
          formData.append('image', file);

          axios.post('/api/upload-question-image', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          }).then(response => {
            if (response.data.success) {
              const newImageData = {
                url: response.data.url,
                name: file.name,
                size: file.size,
                type: file.type,
                uploaded_at: new Date().toISOString(),
                server_filename: response.data.filename
              };
              // Cập nhật ảnh trong dữ liệu
              this.questions[partIndex].questions[qIndex].images[imgIndex] = newImageData;

              // Force Vue to re-render by updating the images array
              this.$set(this.questions[partIndex].questions[qIndex], 'images', [...this.questions[partIndex].questions[qIndex].images]);

              // Force component update
              this.$forceUpdate();

              // Alternative approach: Update the entire questions array
              const updatedQuestions = [...this.questions];
              this.questions = updatedQuestions;

              // Thêm một delay nhỏ để đảm bảo Vue đã cập nhật
              setTimeout(() => {
                // Kiểm tra xem ảnh đã được cập nhật chưa
                const updatedImage = this.questions[partIndex].questions[qIndex].images[imgIndex];
                if (updatedImage && updatedImage.url === response.data.url) {
                  console.log('Image updated successfully:', updatedImage);
                } else {
                  console.warn('Image update may not have been applied correctly');
                }
              }, 100);

              // Thêm một delay nhỏ để đảm bảo Vue đã cập nhật
              setTimeout(() => {
                // Kiểm tra xem ảnh đã được cập nhật chưa
                const updatedImage = this.questions[partIndex].questions[qIndex].images[imgIndex];
                if (updatedImage && updatedImage.url === response.data.url) {
                  console.log('Image updated successfully:', updatedImage);
                } else {
                  console.warn('Image update may not have been applied correctly');
                }
              }, 100);

              closeModal();
            } else {
              alert(`Lỗi upload ảnh: ${response.data.error}`);
            }
          }).catch(error => {
            console.error('Upload error:', error);
            alert('Lỗi upload ảnh. Vui lòng thử lại.');
          });
        } else {
          alert('Vui lòng chọn ảnh mới!');
        }
      };

      modal.onclick = (e) => {
        if (e.target === modal) closeModal();
      };
    },

    deleteImage(partIndex, qIndex, imgIndex) {
      if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
        const imageToDelete = this.questions[partIndex].questions[qIndex].images[imgIndex];

        // Xóa ảnh khỏi mảng
        this.questions[partIndex].questions[qIndex].images.splice(imgIndex, 1);

        // Nếu không còn ảnh nào, set về null
        if (this.questions[partIndex].questions[qIndex].images.length === 0) {
          this.$set(this.questions[partIndex].questions[qIndex], 'images', null);
        } else {
          // Force Vue to re-render by updating the images array
          this.$set(this.questions[partIndex].questions[qIndex], 'images', [...this.questions[partIndex].questions[qIndex].images]);
        }

        // Force component update
        this.$forceUpdate();

        // Alternative approach: Update the entire questions array
        const updatedQuestions = [...this.questions];
        this.questions = updatedQuestions;

        // Thêm một delay nhỏ để đảm bảo Vue đã cập nhật
        setTimeout(() => {
          // Kiểm tra xem ảnh đã được xóa chưa
          const currentImages = this.questions[partIndex].questions[qIndex].images;
          if (currentImages === null || currentImages.length === 0) {
            console.log('Image deleted successfully');
          } else {
            console.log('Images after deletion:', currentImages);
          }
        }, 100);

        // TODO: Có thể thêm API để xóa file trên server nếu cần
        // Nếu muốn xóa file trên server, có thể gọi API:
        // axios.post('/api/delete-question-image', { filename: imageToDelete.server_filename });
      }
    },

    processImageData() {
      // Xử lý dữ liệu ảnh từ database
      // Chuyển từ trường 'image' (JSON) sang 'images' (array) để tương thích với giao diện
      this.questions.forEach(part => {
        part.questions.forEach(question => {
          if (question.image && typeof question.image === 'string') {
            try {
              const imageData = JSON.parse(question.image);
              question.images = Array.isArray(imageData) ? imageData : [imageData];
              delete question.image; // Xóa trường cũ
            } catch (e) {
              console.warn('Invalid image data for question:', question.question_no, e);
              question.images = null;
            }
          } else if (question.image && Array.isArray(question.image)) {
            // Nếu đã là array, chỉ cần đổi tên
            question.images = question.image;
            delete question.image;
          } else {
            // Không làm gì nếu không có trường image;
            // TRÁNH ghi đè các ảnh đã được server map vào question.images (từ Word import)
          }
        });
      });
    },


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
.monaco-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #f8f9fa;
  border: 1px solid #e2e8f0;
  border-bottom: none;
  border-radius: 8px 8px 0 0;
  gap: 16px;
}

.toolbar-left,
.toolbar-right {
  display: flex;
  gap: 8px;
  align-items: center;
}

.toolbar-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.toolbar-btn:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toolbar-btn:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.toolbar-btn i {
  font-size: 12px;
  opacity: 0.8;
}

.toolbar-btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: transparent;
  font-weight: 600;
}

.toolbar-btn-primary:hover {
  background: linear-gradient(135deg, #5568d3 0%, #6a3f91 100%);
  border-color: transparent;
  color: white;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.toolbar-btn-primary i {
  opacity: 1;
  color: white;
}

.monaco-editor-container {
  height: 600px;
  width: 100%;
  border: 1px solid #e2e8f0;
  border-top: none;
  border-radius: 0 0 8px 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.monaco-editor-container:hover {
  border-color: #cbd5e0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* CodeMirror Custom Styles */
.monaco-editor-container >>> .CodeMirror {
  height: 600px;
  font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
  font-size: 14px;
  line-height: 1.5;
}

.monaco-editor-container >>> .CodeMirror-scroll {
  min-height: 600px;
}

.monaco-editor-container >>> .CodeMirror-gutters {
  border-right: 1px solid #ddd;
  background-color: #f7f7f7;
}
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

.content-height {
  max-height: calc(100vh - 200px);
  overflow-y: auto;
  overflow-x: hidden;
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
  min-width: 950px;
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

.images-section {
  border-left: 4px solid #10b981;
}

.audio-player {
  width: 100%;
  height: 40px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.no-media-message {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px;
  background: #f3f4f6;
  border: 1px dashed #d1d5db;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-style: italic;
}

.no-media-message i {
  color: #9ca3af;
  font-size: 16px;
}

.question-image {
  max-width: 100%;
  height: auto;
  border-radius: 4px;
  display: block;
}

/* Image Gallery Styles */
.image-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
  margin-top: 8px;
}

.image-item {
  position: relative;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
  background: #fff;
  transition: all 0.2s ease;
}

.image-item:hover {
  border-color: #4f46e5;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
}

.image-item img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  display: block;
  border-radius: 4px;
}



.image-actions {
  position: absolute;
  top: 8px;
  right: 8px;
  display: flex;
  gap: 4px;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.image-item:hover .image-actions {
  opacity: 1;
}

.btn-edit-image,
.btn-delete-image {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  transition: all 0.2s ease;
}

.btn-edit-image {
  background: #4f46e5;
  color: white;
}

.btn-edit-image:hover {
  background: #3730a3;
  transform: scale(1.1);
}

.btn-delete-image {
  background: #dc2626;
  color: white;
}

.btn-delete-image:hover {
  background: #b91c1c;
  transform: scale(1.1);
}

@media (max-width: 768px) {
  .monaco-toolbar {
    flex-direction: column;
    gap: 12px;
    padding: 16px;
  }

  .toolbar-left,
  .toolbar-right {
    width: 100%;
    justify-content: center;
    flex-wrap: wrap;
  }

  .toolbar-btn {
    flex: 1;
    min-width: 0;
    font-size: 12px;
    padding: 10px 8px;
  }

  .toolbar-btn span {
    display: none;
  }

  .monaco-editor-container {
    height: 500px;
  }

  .image-gallery {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 8px;
  }

  .image-item img {
    height: 120px;
  }

  .image-actions {
    opacity: 1;
  }
}

/* Question Bank Modal Styles */
.question-bank-item {
  background: #fff;
  border: 1px solid #e5e7eb;
  transition: all 0.2s;
}

.question-bank-item:hover {
  background: #f8f9fa;
  border-color: #4f46e5;
}

.question-bank-item.active {
  background: #eef2ff;
  border-color: #4f46e5;
  color: #4f46e5;
  font-weight: 600;
}

.question-item {
  transition: all 0.2s;
}

.question-item:hover {
  border-color: #4f46e5;
  background: #f8f9fa;
}

.question-item.selected {
  border-color: #4f46e5;
  background: #eef2ff;
}
</style>

