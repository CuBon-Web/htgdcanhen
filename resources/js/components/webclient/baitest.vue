<template>
    <div>
        <div v-for="(task, taskIndex) in tasks" :key="taskIndex" class="task-block" draggable="true"
            @dragstart="onTaskDragStart(taskIndex)" @dragover.prevent @drop="onTaskDrop(taskIndex)">
            <div class="form-group">
                <label><b>Câu hỏi {{ taskIndex + 1 }}</b>: <i class="fas fa-trash text-danger"
                        @click="removeTask(taskIndex)" style="cursor: pointer;"></i></label>
            </div>
            <div class="form-group">
                <textarea name="title" id="title" v-model="task.title" class="form-control" placeholder="Nhập nội dung"></textarea>
                <!-- <TinyMce v-model="task.title" /> -->
            </div>

            <div class="form-group">
                <label>Loại câu hỏi:</label>
                <select v-model="task.type" class="form-control">
                    <option value="0">Tự Luận</option>
                    <option value="1">Trắc Nghiệm</option>
                </select>
            </div>

            <div class="accordion" id="accordionExample" v-if="task.type == 1">
                <div class="accordion-item detail-task-block" v-for="(detail, detailIndex) in task.multiple_choice"
                    :key="detailIndex">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            :data-bs-target="'#collapseTwo_' + detailIndex + taskIndex" aria-expanded="false"
                            :aria-controls="'#collapseTwo_' + detailIndex + taskIndex">
                            {{ detail.name }}
                        </button>
                    </h2>
                    <div :id="'collapseTwo_' + detailIndex + taskIndex" class="accordion-collapse collapse"
                        aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="form-group">
                                        <label>Nội dung {{ detail.name }}:</label>
                                        <textarea type="text" v-model="detail.title" class="form-control"
                                            placeholder="Nhập nội dung"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Đáp án đúng:</label>
                    <select v-model="task.right_choice" class="form-control">
                        <option value="0">--Chọn--</option>
                        <option value="1">Đáp án A</option>
                        <option value="2">Đáp án B</option>
                        <option value="3">Đáp án C</option>
                        <option value="4">Đáp án D</option>
                    </select>
                </div>
            </div>

            <!-- <div class="row">
                <div class="col-12" style="    text-align: left;">
                    <button type="button" class="btn btn-secondary plusbaihoc" @click="addDetailTask(taskIndex)">
                        <i class="fas fa-plus"></i> Thêm bài học
                    </button>
                </div>
            </div> -->


            <hr />
        </div>
        <div class="row">
            <div class="col-12" style="    text-align: left;">
                <button type="button" class="btn btn-secondary plusbaihoc" @click="addTask">
                    <i class="fas fa-plus"></i> Thêm câu hỏi
                </button>
            </div>
        </div>

        <input type="hidden" name="tasks_json" id="tasks_json" :value="JSON.stringify(tasks)" />
    </div>
</template>

<script>
import TinyMce from "../_common/tinymceclient";
export default {
    created() {
    },
    components: {
        TinyMce,
    },
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
                    title: "",
                    image: "",
                    type: 0,
                    giai_thich: "",
                    right_choice: 0,
                    multiple_choice: [
                        {
                            name: "Đáp Án A",
                            title: "",
                            index: 1
                        },
                        {
                            name: "Đáp Án B",
                            title: "",
                            index: 2
                        },
                        {
                            name: "Đáp Án C",
                            title: "",
                            index: 3
                        },
                        {
                            name: "Đáp Án D",
                            title: "",
                            index: 4
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
                title: "",
                image: "",
                type: 0,
                giai_thich: "",
                right_choice: 0,
                multiple_choice: [
                    {
                        name: "Đáp Án A",
                        title: "",
                        index: 1
                    },
                    {
                        name: "Đáp Án B",
                        title: "",
                        index: 2
                    },
                    {
                        name: "Đáp Án C",
                        title: "",
                        index: 3
                    },
                    {
                        name: "Đáp Án D",
                        title: "",
                        index: 4
                    },
                ],
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

        // === Kéo thả bài học ===

        removeTask(index) {
            if (confirm('Bạn có chắc chắn muốn xóa chương này không?')) {
                this.tasks.splice(index, 1);
            }
        },
    }
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
    padding: 10px 50px;
    font-size: 14px;
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
