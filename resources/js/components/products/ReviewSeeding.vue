<template>
  <div class="form-group review-seed-wrap">
    <div class="review-seed-head">
      <div class="review-seed-head__left">
        <label class="review-seed-title">Seeding Reviews</label>
        <span class="review-seed-badge">{{ reviewCount }} đánh giá</span>
        <span class="review-seed-badge review-seed-badge--rating">TB {{ reviewAverage }} ★</span>
      </div>
      <div class="review-seed-head__actions">
        <div class="review-seed-template">
          <button
            type="button"
            class="btn btn-light btn-sm review-seed-btn-outline"
            @click="showQuickTemplate = !showQuickTemplate"
          >
            <i class="fas fa-plus"></i> Mẫu nhanh
          </button>
          <div v-if="showQuickTemplate" class="review-seed-template__menu">
            <button
              v-for="tpl in reviewQuickTemplates"
              :key="tpl.id"
              type="button"
              @click="applyQuickTemplate(tpl)"
            >
              {{ tpl.label }}
            </button>
          </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm review-seed-btn-add" @click="addReview">
          <i class="fas fa-plus"></i> Thêm đánh giá
        </button>
      </div>
    </div>
    <p class="review-seed-desc">
      Tạo đánh giá seeding độc lập cho khóa học: tên học sinh, lớp học, sao, nội dung, ảnh đại diện và thời gian.
    </p>

    <div v-if="!reviews.length" class="review-seed-empty">
      Chưa có đánh giá. Bấm <strong>Mẫu nhanh</strong> hoặc <strong>Thêm đánh giá</strong> để bắt đầu.
    </div>

    <div class="review-seed-list">
      <div
        v-for="(item, i) in reviews"
        :key="'review-' + i"
        class="review-seed-card"
        :class="{ 'is-open': expandedReviewIndex === i }"
      >
        <div class="review-seed-card__header" @click="toggleReviewExpand(i)">
          <span class="review-seed-card__index">#{{ i + 1 }}</span>
          <strong class="review-seed-card__name">{{ item.name || 'Chưa có tên' }}</strong>
          <span class="review-seed-card__stars">
            <i
              v-for="n in 5"
              :key="'head-star-' + i + '-' + n"
              class="fas fa-star"
              :class="{ active: n <= Number(item.star) }"
            ></i>
          </span>
          <span class="review-seed-card__time">{{ formatReviewDate(item.feedback_at) }}</span>
          <div class="review-seed-card__tools" @click.stop>
            <button type="button" class="review-seed-tool" title="Nhân bản" @click="duplicateReview(i)">
              <i class="far fa-copy"></i>
            </button>
            <button type="button" class="review-seed-tool review-seed-tool--danger" title="Xóa" @click="removeReview(i)">
              <i class="fas fa-trash"></i>
            </button>
            <button type="button" class="review-seed-tool" title="Mở rộng" @click="toggleReviewExpand(i)">
              <i class="fas" :class="expandedReviewIndex === i ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
          </div>
        </div>

        <div v-show="expandedReviewIndex === i" class="review-seed-card__body">
          <div class="row">
            <div class="col-3">
              <label class="review-seed-label">Ảnh đại diện</label>
              <image-upload-small
                v-model="reviews[i].avatar"
                type="avatar"
                :title="'review-' + i + '-'"
                class="w-100"
              ></image-upload-small>
            </div>
            <div class="col-9">
              <div class="row">
                <div class="col-6">
                  <label class="review-seed-label">Tên khách hàng</label>
                  <vs-input
                    type="text"
                    size="default"
                    placeholder="Họ tên học sinh / phụ huynh"
                    class="w-100"
                    v-model="reviews[i].name"
                  />
                </div>
                <div class="col-6">
                  <label class="review-seed-label">Lớp học</label>
                  <vs-input
                    type="text"
                    size="default"
                    placeholder="VD: Lớp 4A"
                    class="w-100"
                    v-model="reviews[i].class_name"
                  />
                </div>
                <div class="col-6">
                  <label class="review-seed-label">Địa chỉ</label>
                  <vs-input
                    type="text"
                    size="default"
                    placeholder="VD: Hà Nội"
                    class="w-100"
                    v-model="reviews[i].address"
                  />
                </div>
                <div class="col-6">
                  <label class="review-seed-label">Thời gian feedback</label>
                  <input
                    type="datetime-local"
                    class="form-control review-seed-datetime"
                    v-model="reviews[i].feedback_at"
                  />
                </div>
                <div class="col-12">
                  <label class="review-seed-label">Đánh giá sao</label>
                  <div class="review-seed-star-picker">
                    <button
                      v-for="n in 5"
                      :key="'pick-star-' + i + '-' + n"
                      type="button"
                      class="review-seed-star-btn"
                      @click="setReviewStar(i, n)"
                    >
                      <i class="fas fa-star" :class="{ active: n <= Number(reviews[i].star) }"></i>
                    </button>
                    <span class="review-seed-star-text">{{ reviews[i].star }}/5</span>
                  </div>
                </div>
                <div class="col-12">
                  <label class="review-seed-label">Nội dung đánh giá</label>
                  <vs-textarea
                    v-model="reviews[i].content"
                    placeholder="Nội dung feedback..."
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ReviewSeeding",
  props: {
    value: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      expandedReviewIndex: null,
      showQuickTemplate: false,
      reviewQuickTemplates: [
        {
          id: "standard-3",
          label: "Bộ 3 đánh giá tiêu chuẩn",
          items: [
            {
              star: 5,
              name: "Nguyễn Văn An",
              class_name: "Lớp 4A",
              address: "Hà Nội",
              content: "Con học rất tiến bộ sau 2 tháng, giáo viên nhiệt tình và tận tâm theo sát từng em.",
              avatar: "",
            },
            {
              star: 5,
              name: "Trần Minh Khôi",
              class_name: "Lớp 5B",
              address: "TP. Hồ Chí Minh",
              content: "Chương trình toán tư duy rất hay, con tự tin hơn khi giải các bài toán khó.",
              avatar: "",
            },
            {
              star: 4,
              name: "Lê Thu Hà",
              class_name: "Lớp 3C",
              address: "Đà Nẵng",
              content: "Lớp học sinh động, con rất thích đến trung tâm mỗi tuần. Mong có thêm buổi luyện đề.",
              avatar: "",
            },
          ],
        },
        {
          id: "diverse-5",
          label: "Bộ 5 đánh giá đa dạng",
          items: [
            {
              star: 5,
              name: "Phạm Gia Bảo",
              class_name: "Lớp 6A",
              address: "Hải Phòng",
              content: "Giáo viên giảng dễ hiểu, con tiến bộ rõ rệt về tư duy logic và tính cẩn thận.",
              avatar: "",
            },
            {
              star: 5,
              name: "Hoàng Minh Châu",
              class_name: "Lớp 2B",
              address: "Bắc Ninh",
              content: "Con mới học được 1 tháng đã thích toán hơn, cảm ơn thầy cô Cánh Én rất nhiều!",
              avatar: "",
            },
            {
              star: 4,
              name: "Vũ Ngọc Linh",
              class_name: "Lớp 7C",
              address: "Cần Thơ",
              content: "Chương trình bài bản, tài liệu phong phú. Con học online vẫn theo kịp tiến độ.",
              avatar: "",
            },
            {
              star: 5,
              name: "Đỗ Quang Huy",
              class_name: "Lớp 8A",
              address: "Thanh Hóa",
              content: "Điểm toán tăng từ 7 lên 9 sau một học kỳ. Gia đình rất hài lòng với kết quả.",
              avatar: "",
            },
            {
              star: 4,
              name: "Bùi Thảo My",
              class_name: "Lớp 5C",
              address: "Nghệ An",
              content: "Trung tâm chăm sóc học sinh tốt, báo cáo tiến độ thường xuyên cho phụ huynh.",
              avatar: "",
            },
          ],
        },
        {
          id: "positive-2",
          label: "Bộ 2 đánh giá ngắn gọn",
          items: [
            {
              star: 5,
              name: "Nguyễn Thị Mai",
              class_name: "Lớp 4B",
              address: "Hà Nội",
              content: "Học phí hợp lý, chất lượng giảng dạy tốt. Con học vui và có tiến bộ.",
              avatar: "",
            },
            {
              star: 5,
              name: "Trịnh Văn Đức",
              class_name: "Lớp 9A",
              address: "TP. Hồ Chí Minh",
              content: "Luyện thi hiệu quả, đề bài sát với chương trình trên lớp. Rất đáng học!",
              avatar: "",
            },
          ],
        },
      ],
    };
  },
  computed: {
    reviews: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
    reviewCount() {
      return (this.reviews || []).filter((item) => item.name || item.content).length;
    },
    reviewAverage() {
      const items = (this.reviews || []).filter((item) => Number(item.star) > 0);
      if (!items.length) {
        return "0.0";
      }
      const total = items.reduce((sum, item) => sum + Number(item.star || 0), 0);
      return (total / items.length).toFixed(1);
    },
  },
  mounted() {
    document.addEventListener("click", this.handleDocumentClick);
  },
  beforeDestroy() {
    document.removeEventListener("click", this.handleDocumentClick);
  },
  methods: {
    handleDocumentClick(event) {
      if (!this.$el.contains(event.target)) {
        this.showQuickTemplate = false;
      }
    },
    createEmptyReview() {
      return {
        star: 5,
        name: "",
        class_name: "",
        address: "",
        content: "",
        avatar: "",
        feedback_at: this.randomFeedbackDate(),
      };
    },
    randomFeedbackDate() {
      const now = new Date();
      const daysAgo = Math.floor(Math.random() * 90) + 1;
      const date = new Date(now.getTime() - daysAgo * 24 * 60 * 60 * 1000);
      date.setHours(Math.floor(Math.random() * 10) + 8, Math.floor(Math.random() * 60), 0, 0);
      const pad = (num) => String(num).padStart(2, "0");
      return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    },
    formatReviewDate(value) {
      if (!value) {
        return "Chưa có thời gian";
      }
      const date = new Date(value);
      if (isNaN(date.getTime())) {
        return value;
      }
      const pad = (num) => String(num).padStart(2, "0");
      return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
    },
    emitReviews(nextReviews) {
      this.$emit("input", nextReviews);
    },
    addReview() {
      const nextReviews = [...this.reviews, this.createEmptyReview()];
      this.emitReviews(nextReviews);
      this.expandedReviewIndex = nextReviews.length - 1;
      this.showQuickTemplate = false;
    },
    removeReview(index) {
      if (!confirm("Bạn có chắc chắn muốn xóa đánh giá này?")) {
        return;
      }
      const nextReviews = [...this.reviews];
      nextReviews.splice(index, 1);
      this.emitReviews(nextReviews);
      if (this.expandedReviewIndex === index) {
        this.expandedReviewIndex = null;
      } else if (this.expandedReviewIndex > index) {
        this.expandedReviewIndex -= 1;
      }
    },
    duplicateReview(index) {
      const source = this.reviews[index];
      const clone = {
        ...source,
        name: source.name ? `${source.name} (bản sao)` : "",
        feedback_at: this.randomFeedbackDate(),
      };
      const nextReviews = [...this.reviews];
      nextReviews.splice(index + 1, 0, clone);
      this.emitReviews(nextReviews);
      this.expandedReviewIndex = index + 1;
    },
    toggleReviewExpand(index) {
      this.expandedReviewIndex = this.expandedReviewIndex === index ? null : index;
    },
    setReviewStar(index, star) {
      const nextReviews = [...this.reviews];
      nextReviews[index] = {
        ...nextReviews[index],
        star,
      };
      this.emitReviews(nextReviews);
    },
    applyQuickTemplate(template) {
      const items = template.items.map((item, itemIndex) => ({
        ...item,
        avatar: item.avatar || "",
        feedback_at: item.feedback_at || this.randomFeedbackDate(),
        star: Number(item.star) || 5,
      }));
      const startIndex = this.reviews.length;
      const nextReviews = [...this.reviews, ...items];
      this.emitReviews(nextReviews);
      this.expandedReviewIndex = startIndex;
      this.showQuickTemplate = false;
      if (this.$success) {
        this.$success(`Đã thêm ${items.length} đánh giá từ mẫu "${template.label}"`);
      }
    },
  },
};
</script>

<style scoped>
.review-seed-wrap {
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 14px;
  background: #fff;
}

.review-seed-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 8px;
}

.review-seed-head__left {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

.review-seed-title {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
}

.review-seed-badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 10px;
  border-radius: 999px;
  background: #f3f4f6;
  color: #4b5563;
  font-size: 12px;
  font-weight: 600;
}

.review-seed-badge--rating {
  background: #fff7ed;
  color: #ea580c;
}

.review-seed-head__actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.review-seed-template {
  position: relative;
}

.review-seed-btn-outline {
  border: 1px solid #d1d5db;
  color: #374151;
  background: #fff;
}

.review-seed-btn-add {
  background: #2563eb;
  border-color: #2563eb;
}

.review-seed-template__menu {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  z-index: 20;
  min-width: 220px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.review-seed-template__menu button {
  display: block;
  width: 100%;
  padding: 10px 14px;
  border: none;
  background: #fff;
  text-align: left;
  font-size: 13px;
  color: #111827;
  cursor: pointer;
}

.review-seed-template__menu button:hover {
  background: #f9fafb;
}

.review-seed-desc {
  margin: 0 0 12px;
  font-size: 12px;
  color: #6b7280;
  line-height: 1.5;
}

.review-seed-empty {
  padding: 14px;
  margin-bottom: 10px;
  border: 1px dashed #d1d5db;
  border-radius: 8px;
  font-size: 13px;
  color: #6b7280;
  background: #f9fafb;
}

.review-seed-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.review-seed-card {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
  background: #fff;
}

.review-seed-card.is-open {
  border-color: #bfdbfe;
  box-shadow: 0 0 0 1px #dbeafe;
}

.review-seed-card__header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  cursor: pointer;
  background: #f9fafb;
}

.review-seed-card__index {
  font-size: 12px;
  font-weight: 700;
  color: #6b7280;
  min-width: 24px;
}

.review-seed-card__name {
  font-size: 14px;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 120px;
}

.review-seed-card__stars {
  display: inline-flex;
  gap: 2px;
}

.review-seed-card__stars .fa-star {
  font-size: 11px;
  color: #d1d5db;
}

.review-seed-card__stars .fa-star.active {
  color: #f59e0b;
}

.review-seed-card__time {
  margin-left: auto;
  font-size: 11px;
  color: #9ca3af;
  white-space: nowrap;
}

.review-seed-card__tools {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.review-seed-tool {
  width: 28px;
  height: 28px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: #fff;
  color: #6b7280;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 12px;
}

.review-seed-tool--danger {
  color: #dc2626;
  border-color: #fecaca;
  background: #fef2f2;
}

.review-seed-card__body {
  padding: 14px;
  border-top: 1px solid #e5e7eb;
}

.review-seed-label {
  display: block;
  margin-bottom: 4px;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
}

.review-seed-datetime {
  height: 40px;
  font-size: 13px;
}

.review-seed-star-picker {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-bottom: 8px;
}

.review-seed-star-btn {
  border: none;
  background: transparent;
  padding: 0;
  cursor: pointer;
}

.review-seed-star-btn .fa-star {
  font-size: 22px;
  color: #d1d5db;
}

.review-seed-star-btn .fa-star.active {
  color: #f59e0b;
}

.review-seed-star-text {
  margin-left: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #ea580c;
}
</style>
