<template>
    <div>
        <div class="form-group">
            <label>Tên bài test* <i class="sub_name">(Gợi ý: bạn nên đặt tên kèm với tên khóa học để dễ dàng
                    lựa chọn khi đăng khóa học vd:Test bài 1 - Khóa học toán)</i></label>
            <vs-input type="text" size="default" placeholder="Tên sản phẩm" class="w-100" v-model="objData.name" />
        </div>

        <div v-for="(task, taskIndex) in objData.tasks" :key="taskIndex" class="task-block" draggable="true"
            @dragstart="onTaskDragStart(taskIndex)" @dragover.prevent @drop="onTaskDrop(taskIndex)">
            <div class="form-group">
                <label><b>Câu hỏi {{ taskIndex + 1 }}</b>: <i class="fas fa-trash text-danger"
                        @click="removeTask(taskIndex)" style="cursor: pointer;"></i></label>
            </div>
            <div class="form-group">
                <TinyMce v-model="task.title" />
            </div>
            <div class="form-group">
                <label>Loại câu hỏi</label>
                <vs-select v-model="task.type">
                    <vs-select-item value="0" text="Tự Luận" />
                    <vs-select-item value="1" text="Trắc Nghiệm" />
                </vs-select>
            </div>
            <div class="accordion" id="accordionExample" v-if="task.type == 1">
                <div class="accordion-item detail-task-block" v-for="(detail, detailIndex) in task.multiple_choice"
                    :key="detailIndex">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            :data-bs-target="'#collapseTwo_' + detailIndex + taskIndex" aria-expanded="false"
                            :aria-controls="'collapseTwo_' + detailIndex + taskIndex">
                            {{ detail.name }}
                        </button>
                    </h2>
                    <div :id="'collapseTwo_' + detailIndex + taskIndex" class="accordion-collapse collapse"
                        aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="form-group">
                                <label>Nội dung {{ detail.name }}:</label>
                                <textarea v-model="detail.title" class="form-control" placeholder="Nhập nội dung"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Đáp án đúng:</label>
                    <vs-select v-model="task.right_choice">
                        <vs-select-item value="1" text="Đáp án A" />
                        <vs-select-item value="2" text="Đáp án B" />
                        <vs-select-item value="3" text="Đáp án C" />
                        <vs-select-item value="4" text="Đáp án D" />
                    </vs-select>
                </div>
            </div>
            <hr />
        </div>

        <div class="row">
            <div class="col-12" style="text-align: left;">
                <button type="button" class="btn btn-secondary plusbaihoc" @click="addTask">
                    <i class="fas fa-plus"></i> Thêm câu hỏi
                </button>
            </div>
        </div>

        <div class="row fixxed">
            <div class="col-12">
                <div class="saveButton">
                    <vs-button color="primary" @click="saveBaitap">Cập nhật</vs-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TinyMce from "../_common/tinymceclient";
import { mapActions } from "vuex";

export default {
    components: {
        TinyMce,
    },
    data() {
        return {
            errors: [],
            objData: {
                name: '',
                tasks: [],
            },
            dragTaskIndex: null,
        };
    },
    methods: {
        ...mapActions(["saveTest", "loadings", "editIdTest"]),

        saveBaitap() {
            this.errors = [];
            if (this.objData.name === '') this.errors.push('Tên không được để trống');
            this.objData.tasks.forEach((task, taskIndex) => {
                    if (task.type == 0) {
                        // Tự luận: phải có title
                        if (!task.title || task.title.trim() === '') {
                            this.errors.push(`Câu hỏi ${taskIndex + 1} (Tự luận) chưa nhập nội dung`);
                        }
                    } else if (task.type == 1) {
                        // Trắc nghiệm: phải có title
                        if (!task.title || task.title.trim() === '') {
                            this.errors.push(`Câu hỏi ${taskIndex + 1} (Trắc nghiệm) chưa nhập nội dung`);
                        }
                        // Trắc nghiệm: validate tất cả các đáp án
                        task.multiple_choice.forEach((choice, choiceIndex) => {
                            if (!choice.title || choice.title.trim() === '') {
                                this.errors.push(`Câu hỏi ${taskIndex + 1} - Đáp án ${choice.name} chưa nhập nội dung`);
                            }
                        });
                    }
                });
            if (this.errors.length > 0) {
                this.errors.forEach(value => {
                    this.$error(value);
                });
                return;
            }

            this.loadings(true);
            this.saveTest(this.objData)
                .then(() => {
                    this.loadings(false);
                    this.$router.push({ name: "listTest" });
                    this.$success("Cập nhật thành công");
                })
                .catch(() => {
                    this.loadings(false);
                    this.$error("Thêm thất bại");
                });
        },

        addTask() {
            const newTask = {
                title: "",
                image: "",
                type: 0,
                giai_thich: "",
                right_choice: 0,
                multiple_choice: [
                    { name: "Đáp Án A", title: "", index: 1 },
                    { name: "Đáp Án B", title: "", index: 2 },
                    { name: "Đáp Án C", title: "", index: 3 },
                    { name: "Đáp Án D", title: "", index: 4 },
                ],
            };
            // Thêm task mới bằng spread để đảm bảo reactive
            this.objData.tasks = [...this.objData.tasks, newTask];
        },

        editById() {
            this.loadings(true);
            this.editIdTest({ id: this.$route.params.id })
                .then(response => {
                    this.loadings(false);
                    // Đảm bảo dữ liệu reactive
                    this.objData = {
                        id: response.data.id || '',
                        name: response.data.name || '',
                        tasks: JSON.parse(response.data.test_json) || [],
                    };
                })
                .catch(() => {
                    this.loadings(false);
                    this.$error("Lỗi khi lấy dữ liệu test");
                });
        },

        // Kéo thả task
        onTaskDragStart(index) {
            this.dragTaskIndex = index;
        },
        onTaskDrop(index) {
            if (this.dragTaskIndex === null || this.dragTaskIndex === index) return;
            const movedTask = this.objData.tasks.splice(this.dragTaskIndex, 1)[0];
            this.objData.tasks.splice(index, 0, movedTask);
            this.dragTaskIndex = null;
        },

        removeTask(index) {
            if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này không?')) {
                this.objData.tasks.splice(index, 1);
            }
        },
    },
    mounted() {
        this.editById();
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
