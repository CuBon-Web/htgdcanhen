<template>
  <div>
    <div
      v-for="(task, taskIndex) in tasks"
      :key="taskIndex"
      class="task-block"
      :class="{ 'drag-over': dragOverIndex === taskIndex }"
      draggable="true"
      @dragstart="onTaskDragStart(taskIndex)"
      @dragenter="onTaskDragEnter(taskIndex)"
      @dragleave="onTaskDragEnter(null)"
      @dragover.prevent
      @drop="onTaskDrop(taskIndex)"
    >
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label>
              <b>Tiêu đề {{ taskIndex + 1 }}</b>: {{ task.title }}
              <i
                class="bx bxs-trash text-danger"
                @click="removeTask(taskIndex)"
                style="cursor: pointer;"
              ></i>
            </label>
            <input
              type="text"
              v-model="task.title"
              class="form-control"
              placeholder="Tiêu đề"
            />
          </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
            <label>Icon</label>
             <ImageUploader :initialValue="task.image" @image-changed="val => task.image = val" />
            </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12" style="text-align: left;">
        <button type="button" class="btn btn-secondary plusbaihoc" @click="addTask">
          <i class="bx bxs-plus-circle"></i> Thêm nội dung
        </button>
      </div>
    </div>

    <input type="hidden" name="ques_json" id="ques_json" :value="JSON.stringify(tasks)" />
  </div>
</template>

<script>
import ImageUploader from "../webclient/ImageUploader.vue";

export default {
   props: {
    initialTasks: {
      type: Array,
      default: () => ([]),
    },
  }, 
  components: {
    ImageUploader,
  },
  data() {
    return {
      tasks: this.initialTasks.length ? this.initialTasks : [
        {
          title: "Sách giáo trình màu miễn phí",
          image: "",
        },
        {
          title: "Hỗ trợ sau giờ học từ giáo viên",
          image: "",
        },
        {
          title: "Chăm sóc tận tình, báo cáo tiến độ thường xuyên",
          image: "",
        },
      ],
      dragTaskIndex: null,
      dragOverIndex: null,
    };
  },
  watch: {
    tasks: {
      handler() {
        this.updateHiddenInput();
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    addTask() {
      this.tasks.push({ title: "", image: "" });
    },
    removeTask(index) {
      if (confirm("Bạn có chắc chắn muốn xóa chương này không?")) {
        this.tasks.splice(index, 1);
      }
    },
    onTaskDragStart(index) {
      this.dragTaskIndex = index;
    },
    onTaskDragEnter(index) {
      this.dragOverIndex = index;
    },
    onTaskDrop(index) {
      if (this.dragTaskIndex === null || this.dragTaskIndex === index) return;
      const moved = this.tasks.splice(this.dragTaskIndex, 1)[0];
      this.tasks.splice(index, 0, moved);
      this.dragTaskIndex = null;
      this.dragOverIndex = null;
      this.updateHiddenInput();
    },
    updateHiddenInput() {
      const el = document.getElementById("ques_json");
      if (el) {
        el.value = JSON.stringify(this.tasks);
      }
    },
  },
};
</script>

<style scoped>
.task-block {
  margin-bottom: 20px;
    padding: 10px;
    background: #f8faff;
    border-radius: 5px;
    border: 1px solid #94b0f3;
}

.task-block.drag-over {
  background-color: #f0f0ff;
  border: 2px dashed #6d28d2;
}

.form-group {
  text-align: left;
  margin-bottom: 15px;
  font-size: 14px;
}
.form-group label {
  font-size: 15px;
}
button.btn.btn-secondary.plusbaihoc {
  margin: 0;
    border: 1px solid #6d28d2;
    color: #6d28d2;
    background: white;
    font-size: 14px;
    padding: 8px 14px
}
</style>
