<template>
  <div>
    <div v-for="(task, taskIndex) in tasks" :key="taskIndex" class="task-block"  draggable="true"
      @dragstart="onTaskDragStart(taskIndex)"
      @dragover.prevent
      @drop="onTaskDrop(taskIndex)">
      <div class="form-group">
        <label><b>Câu hỏi {{ taskIndex+1 }}</b>: {{ task.chuong }} <i class="bx bxs-trash text-danger" @click="removeTask(taskIndex)" style="cursor: pointer;"></i></label>
        <input type="text" v-model="task.chuong" class="form-control" placeholder="Tiêu đề câu hỏi" />
        <label>Câu trả lời</label>
        <div class="contact-three__input-box text-message-box">
            <textarea class="form-control" v-model="task.content" placeholder="Câu trả lời"></textarea>
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-12" style="text-align: left;">
        <button type="button" class="btn btn-secondary plusbaihoc" @click="addTask">
            <i class="bx bxs-plus-circle"></i> Thêm câu hỏi
        </button>
        </div>
    </div>
    <input type="hidden" name="faq_json" id="faq_json" :value="JSON.stringify(tasks)" />
  </div>
</template>

<script>
export default {
  props: {
    initialTasks: {
      type: Array,
      default: () => ([]),
    },
  },  
  data() {
    return {
      tasks: this.initialTasks.length ? this.initialTasks : [
        {
          chuong: "",
          content: "",
        },
      ],
    };
  },
  watch: {
    tasks: {
      handler(newVal) {
        document.getElementById("faq_json").value = JSON.stringify(newVal);
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    addTask() {
      this.tasks.push({
        chuong: "",
        content: ""
      });
    },
    // === Kéo thả chương ===
    onTaskDragStart(index) {
      this.dragTaskIndex = index;
    },
    onTaskDrop(index) {
      if (this.dragTaskIndex === null || this.dragTaskIndex === index) return;
      const moved = this.tasks.splice(this.dragTaskIndex, 1)[0];
      this.tasks.splice(index, 0, moved);
      this.dragTaskIndex = null;
    },
    removeTask(index) {
  if (confirm('Bạn có chắc chắn muốn xóa chương này không?')) {
    this.tasks.splice(index, 1);
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

.detail-task-block {
  border: 1px solid #ddd;
  margin-bottom: 15px;
  border-radius: 6px;
  background-color: #fff;
}

.progress {
  height: 22px;
}

.progress-bar {
  font-size: 14px;
  line-height: 22px;
}

.accordion-button {
  border: 1px solid black;
  margin-top: 0;
  padding: 10px 17px;
  font-size:14px;
}

.accordion-button i {
  font-size: 12px;
  padding: 0 6px;
  color: rgb(199, 9, 9);
}
.form-group {
    text-align: left;
    margin-bottom: 15px;
    font-size: 14px;
}
.form-group label {
    font-size: 15px;
}
.accordion-body {
    border: 1px solid black;
    border-top: none;
}
button.btn.btn-secondary.plusbaihoc {
  margin: 0;
    border: 1px solid #6d28d2;
    color: #6d28d2;
    background: white;
    font-size: 14px;
    padding: 8px 14px
}
select.form-control {
    margin: 5px 0;
}
.descs {
    font-size: 12px;
    font-style: italic;
    color: green;
    line-height: normal;
}
</style>
