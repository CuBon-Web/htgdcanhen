<template>
  <div>
    <div
      class="task-block"
      v-for="(task, taskIndex) in tasks"
      :key="taskIndex"
      draggable="true"
      @dragstart="onTaskDragStart(taskIndex)"
      @dragover.prevent
      @drop="onTaskDrop(taskIndex)"
    >
      <div class="form-group">
        <label><b>Chương {{ taskIndex + 1 }}</b>: {{ task.chuong }}</label>
        <input type="text" v-model="task.chuong" class="form-control" placeholder="Nhập tên chương" />
      </div>

      <div class="accordion" id="accordionExample">
        <div
          class="accordion-item detail-task-block"
          v-for="(detail, detailIndex) in task.detail_task"
          :key="detailIndex"
          draggable="true"
          @dragstart="onDetailDragStart(taskIndex, detailIndex)"
          @dragover.prevent
          @drop="onDetailDrop(taskIndex, detailIndex)"
        >
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button">
              {{ detail.name }}
              <i class="fas fa-trash" @click="removeDetailTask(taskIndex, detailIndex)"></i>
            </button>
          </h2>
          <div class="accordion-body">
            <div class="form-group">
              <label>Tiêu đề bài học:</label>
              <input type="text" v-model="detail.name" class="form-control" />
            </div>
          </div>
        </div>
      </div>

      <button class="btn btn-secondary plusbaihoc" @click="addDetailTask(taskIndex)">
        <i class="fas fa-plus"></i> Thêm bài học
      </button>

      <hr />
    </div>

    <button class="btn btn-primary" @click="addTask">Thêm chương mới</button>
    <input type="hidden" name="tasks_json" id="tasks_json" :value="JSON.stringify(tasks)" />
  </div>
</template>

<script>
export default {
  data() {
    return {
      tasks: [
        {
          chuong: "",
          time: "",
          detail_task: [
            {
              name: "Bài học mới",
              time: "",
              icon: "",
              video: "",
              document: "",
              status: 0,
              test_id: 0,
              uploadProgress: {
                video: 0,
                document: 0,
              },
            },
          ],
        },
      ],
    };
  },
   watch: {
    tasks: {
      handler(newVal) {
        document.getElementById("tasks_json").value = JSON.stringify(newVal);
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    addTask() {
      this.tasks.push({
        chuong: "",
        time: "",
        detail_task: [
          {
            name: "Bài học mới",
            time: "",
            icon: "",
            video: "",
            document: "",
            status: 0,
            test_id: 0,
            uploadProgress: {
              video: 0,
              document: 0,
            },
          },
        ],
      });
    },
    addDetailTask(taskIndex) {
      this.tasks[taskIndex].detail_task.push({
        name: "Bài học mới",
        time: "",
        icon: "",
        video: "",
        document: "",
        status: 0,
        test_id: 0,
        uploadProgress: {
          video: 0,
          document: 0,
        },
      });
    },
    removeDetailTask(taskIndex, detailIndex) {
      this.tasks[taskIndex].detail_task.splice(detailIndex, 1);
    },
    uploadFile(event, taskIndex, detailIndex, type) {
      const file = event.target.files[0];
      if (!file) return;

      if (type === "video") {
        this.getVideoDuration(file)
          .then((duration) => {
            const formatted = this.formatDuration(duration);
            this.tasks[taskIndex].detail_task[detailIndex].time = formatted;
          })
          .catch(() => {
            this.tasks[taskIndex].detail_task[detailIndex].time = "";
          });
      }

      const formData = new FormData();
      formData.append("file", file);
      formData.append("type", type);

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "/api/uploadcourse", true);
      xhr.setRequestHeader(
        "X-CSRF-TOKEN",
        document.querySelector('meta[name="csrf-token"]').getAttribute("content")
      );

      xhr.upload.onprogress = (event) => {
        if (event.lengthComputable) {
          const percent = Math.round((event.loaded / event.total) * 100);
          this.tasks[taskIndex].detail_task[detailIndex].uploadProgress[type] = percent;
        }
      };

      xhr.onload = () => {
        const detail = this.tasks[taskIndex].detail_task[detailIndex];
        const result = JSON.parse(xhr.responseText);

        if (xhr.status === 200) {
          if (type === "video" && result.playback_url) {
            detail.video = result.playback_url;
            detail.uploadProgress.video = 100;
          } else if (type === "document" && result.url) {
            detail.document = result.url;
            detail.uploadProgress.document = 100;
          } else {
            alert("Upload thất bại.");
          }
        } else {
          alert("Upload lỗi.");
        }
      };

      xhr.onerror = () => {
        alert("Lỗi khi upload.");
      };

      xhr.send(formData);
    },
    getVideoDuration(file) {
      return new Promise((resolve, reject) => {
        const url = URL.createObjectURL(file);
        const video = document.createElement("video");
        video.preload = "metadata";

        video.onloadedmetadata = () => {
          URL.revokeObjectURL(url);
          resolve(video.duration);
        };

        video.onerror = () => {
          reject("Không thể đọc file video.");
        };

        video.src = url;
      });
    },
    formatDuration(seconds) {
      const h = Math.floor(seconds / 3600);
      const m = Math.floor((seconds % 3600) / 60);
      const s = Math.floor(seconds % 60);

      if (h > 0) {
        return `${h}:${m.toString().padStart(2, "0")}:${s.toString().padStart(2, "0")}`;
      } else {
        return `${m}:${s.toString().padStart(2, "0")}`;
      }
    },
    getFileName(url) {
      return url.split("/").pop();
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

    // === Kéo thả bài học ===
    onDetailDragStart(taskIndex, detailIndex) {
      this.dragDetail = { taskIndex, detailIndex };
    },
    onDetailDrop(taskIndex, detailIndex) {
      const { taskIndex: fromTask, detailIndex: fromDetail } = this.dragDetail;

      if (fromTask === null || fromDetail === null) return;

      const movedItem = this.tasks[fromTask].detail_task.splice(fromDetail, 1)[0];

      if (fromTask === taskIndex && fromDetail < detailIndex) {
        detailIndex--; // do bị shift vị trí
      }

      this.tasks[taskIndex].detail_task.splice(detailIndex, 0, movedItem);
      this.dragDetail = { taskIndex: null, detailIndex: null };
    },
  },
};
</script>

<style scoped>
.task-block {
  margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #000000;
    background: #f9f9f9;
    border-radius: 0;
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
}

.accordion-button i {
  font-size: 12px;
  padding: 0 6px;
  color: rgb(199, 9, 9);
}
.form-group {
    text-align: left;
    margin-bottom: 15px;
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
}
</style>
