@extends('crm_course.main.master')
@section('title')
    {{ $setting->company }}
@endsection
@section('description')
    {{ $setting->webname }}
@endsection
@section('image')
@endsection
@section('css_crm_course')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .ck-editor__editable {
            min-height: 150px;
            /* thay đổi chiều cao tại đây */
        }
    </style>
    <style>
        .wrap {
            width: 120px;
            margin: 0 auto;
            text-align: center;
            overflow: hidden;
        }

        label[for=upload] {
            width: 100%;
            display: inline-block;
            border: 1px solid #0a0a0a;
            color: #0c0c0c;
            font-weight: bold;
            /* background: #eee; */
            padding: 8px 4px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        label[for=upload]:hover {
            background: #ddd
        }

        label[for=upload] input {
            display: none
        }

        .thumb {
            position: relative;
            height: 230px;
            width: 100%;
            overflow: hidden;
            margin: 10px 0;
            cursor: pointer;
        }

        .thumb:before {
            content: "";
            display: block;
            position: absolute;
            width: 96%;
            height: 96%;
            border: 3px dashed #eee;
            z-index: 9;
            top: 1%;
            left: 1%;
            opacity: 0;
            transition: all 0.2s;
            pointer-events: none
        }

        .thumb:hover::before {
            opacity: 0.6
        }

        .thumb img {
            height: 100%;
            width: 100%;
            transition: all 0.4s;
            object-fit: cover;
            border: 1px solid black;
            border-radius: 10px;
        }

        .task {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
        }

        .detail-task {
            margin-left: 20px;
            padding-top: 10px;
            border-left: 3px solid #eee;
            padding-left: 10px;
            position: relative;
        }


        .add-detail {
            background-color: #dff0d8;
        }

        .add-task {
            background-color: #d9edf7;
        }

        .submit-btn {
            background-color: #f0ad4e;
            color: white;
        }

        .delete-task,
        .delete-detail {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #f2dede;
            color: red;
            border: none;
            padding: 5px 10px;
        }
    </style>
@endsection
@section('js_crm_course')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/child-component.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        let sidebarInstance = null;

        // function initStickySidebar() {
        //     const isMobile = window.innerWidth < 768;

        //     if (!isMobile) {
        //         // Chỉ khởi tạo nếu chưa có
        //         if (!sidebarInstance) {
        //             sidebarInstance = new StickySidebar('#sidebar', {
        //                 containerSelector: '.become-a-teacher__tab-box .row',
        //                 innerWrapperSelector: '.sidebar__inner',
        //                 topSpacing: 120,
        //                 bottomSpacing: 20,
        //                 resizeSensor: true
        //             });
        //         }
        //     } else {
        //         // Nếu đang ở mobile và đã khởi tạo -> hủy
        //         if (sidebarInstance) {
        //             sidebarInstance.destroy();
        //             sidebarInstance = null;
        //         }
        //     }
        // }

        // window.addEventListener('load', initStickySidebar);
        // window.addEventListener('resize', initStickySidebar);

        document.querySelectorAll('.ckeditor').forEach(function(el) {
            ClassicEditor
                .create(el, {
                    toolbar: [
                        'heading',
                        '|',
                        'bold', 'italic', 'underline', 'link',
                        '|',
                        'bulletedList', 'numberedList',
                        '|',
                        'undo', 'redo'
                    ]
                    // Không cấu hình simpleUpload hoặc ckfinder gì cả
                })
                .catch(error => {
                    console.error(error);
                });
        });

        function toggleFullScreen() {
            const editorWrapper = document.querySelector('.ck-editor');

            if (!document.fullscreenElement) {
                editorWrapper.requestFullscreen().catch(err => {
                    alert(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }
    </script>
    <script>
        function deleteCourse(){
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa khoá học này không?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/crm-course/xoa-khoa-hoc/' + {{ $course->id }};
                }
            });
        }
        function preview(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    console.log(reader.result)
                    var img = new Image;
                    img.onload = function() {
                        $('#img').attr({
                            'src': e.target.result,
                            'width': img.width
                        });
                    };
                    img.src = reader.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#upload").change(function() {
            $("#img").css({
                top: 0,
                left: 0
            });
            preview(this);
            $("#img").draggable({
                containment: 'parent',
                scroll: false
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, looking for form...');
            const form = document.querySelector('form');
            console.log('Form found:', form);
            
            if (form) {
                console.log('Adding submit event listener to form...');
                form.addEventListener('submit', function(e) {
                    console.log('Form submit event triggered!');
                    e.preventDefault();

                    const validations = [{
                            selector: 'input[name="name"]',
                            tabId: '#become-an-intructor',
                            message: 'Vui lòng nhập tên khóa học'
                        },
                        {
                            selector: 'select[name="category"]',
                            tabId: '#become-an-intructor',
                            message: 'Vui lòng chọn danh mục khóa học'
                        },
                        {
                            selector: 'textarea[name="description"]',
                            tabId: '#become-an-intructor',
                            message: 'Vui lòng nhập mô tả khóa học'
                        },
                        {
                            selector: 'textarea[name="content"]',
                            tabId: '#become-an-intructor',
                            message: 'Vui lòng nhập giới thiệu khóa học'
                        },
                        // {
                        //     selector: 'input[name="image"]',
                        //     tabId: '#become-an-intructor',
                        //     message: 'Vui lòng chọn ảnh đại diện khóa học'
                        // },
                        {
                            selector: 'input[name="so_buoi"]',
                            tabId: '#v-pills-settings',
                            message: 'Vui lòng nhập số buổi học'
                        },

                    ];
                    for (const v of validations) {
                        const input = this.querySelector(v.selector);
                        if (!input) continue;

                        let value = input.value.trim();

                        if ((input.tagName.toLowerCase() === 'select' && value === '') || (input.type === 'hidden' &&
                                value === '') || value === '') {
                            activateTab(v.tabId);

                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: v.message,
                                confirmButtonText: 'Đồng ý'
                            }).then(() => {
                                input.focus();
                            });

                            return false;
                        }
                    }
                    const tasksInput = this.querySelector('input[name="tasks_json"]');
                    let tasksValid = false;

                    try {
                        const tasks = JSON.parse(tasksInput.value);

                        tasksValid = tasks.some(task => {
                            const hasChuong = task.chuong && task.chuong.trim() !== '';
                            const hasValidDetail = Array.isArray(task.detail_task) && task.detail_task.some(dt => dt
                                .name && dt.name.trim() !== '');

                            return hasChuong && hasValidDetail;
                        });
                    } catch (err) {
                        tasksValid = false;
                    }

                    if (!tasksValid) {
                        activateTab('#v-pills-profile');
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Vui lòng tạo chương trình giảng dạy hợp lệ (tên chương và ít nhất 1 bài học)',
                            confirmButtonText: 'Đồng ý'
                        }).then(() => {
                            tasksInput.focus();
                            tasksInput.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        });
                        return;
                    }
                    console.log('Validation passed, submitting form...');
                    this.submit();
                });
                console.log('Submit event listener added successfully');
            } else {
                console.error('Form not found!');
            }
        });

        function activateTab(tabId) {
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-selected', 'false');
            });

            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.classList.remove('active', 'show');
            });

            // Find the correct tab button and activate it
            const tabBtn = document.querySelector(`[data-bs-target="${tabId}"]`);
            if (tabBtn) {
                tabBtn.classList.add('active');
                tabBtn.setAttribute('aria-selected', 'true');
            }

            // Show the target tab content
            const tabContent = document.querySelector(tabId);
            if (tabContent) {
                tabContent.classList.add('active', 'show');
            }
        }
    </script>
@endsection
@section('content_crm_course')
    <main class="main-content-wrap">
        <div class="content">
            <div class="page-title-area">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-sm-6">
                            <div class="page-title">
                                <h3>Tạo khóa học</h3>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-6">
                            <ul class="page-title-list">
                                <li>Trang chủ</li>
                                <li>Tạo khóa học</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Page Title Area -->

            <!-- Start Profile Area -->
            <div class="profile-area">
                <div class="container-fluid">
                    <form action="{{ route('postCourse') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="id_course" value="{{$course->id}}">
                            <div class="col-lg-3">
                                <div class="edit-profile-content card-box-style">
                                    <h3>Các bước thực hiện</h3>
                                    <div class="nav flex-column nav-pills step-course" id="v-pills-tab" role="tablist"
                                        aria-orientation="vertical">
                                        <button class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill"
                                            data-bs-target="#become-an-intructor"  data-tab="become-an-intructor" type="button" role="tab"
                                            aria-controls="become-an-intructor" aria-selected="true"><b>Bước 1:</b> Thông
                                            tin
                                            cơ bản</button>
                                        <button class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill"
                                            data-bs-target="#v-pills-profile" type="button" role="tab"
                                            aria-controls="v-pills-profile" data-tab="v-pills-profile" aria-selected="false"><b>Bước 2:</b> Chương
                                            trình giảng
                                            dạy</button>
                                        <button class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill"
                                            data-bs-target="#v-pills-messages" data-tab="v-pills-messages" type="button"
                                            role="tab" aria-controls="v-pills-messages" aria-selected="false"><b>Bước 3:</b> Câu hỏi
                                            thường gặp</button>
                                        <button class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                                            data-bs-target="#v-pills-settings" data-tab="v-pills-settings" type="button"
                                            role="tab" aria-controls="v-pills-settings" aria-selected="false"><b>Bước 4:</b> Thông
                                            tin
                                            khác</button>
                                    </div>
                                    <div class="save-update text-center">
                                        <button type="submit" class="btn btn-success me-2"><i class="bx bxs-save"></i> Lưu chỉnh sửa</button>
                                        <button type="button" class="btn btn-danger me-2" onclick="deleteCourse()"><i class="bx bxs-trash"></i> Xóa khóa học</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="become-an-intructor" role="tabpanel"
                                        aria-labelledby="become-an-intructor-tab" tabindex="0">
                                        <div class="edit-profile-content card-box-style">
                                            <h3>Thông tin cơ bản</h3>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Tên khóa học</label>
                                                        <input class="form-control" type="text" name="name" 
                                                            placeholder="Nhập tên khóa học..." value="{{ $course->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Danh mục khóa học</label>
                                                        <select class="form-control form-select" name="category"
                                                            tabindex="-1" aria-hidden="true">
                                                            <option value="">--Chọn--</option>
                                                            @foreach ($category as $item)
                                                                <option value="{{ $item->id }}" {{$course->category == $item->id ? 'selected' : ''}}> 
                                                                    {{ languageName($item->name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Giá thực bán <i style="font-size: 12px;color:green">(Miến
                                                                phí nếu bỏ trống)</i></label>
                                                        <input class="form-control"  value="{{$course->price}}" type="number" name="price"
                                                            value="0">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Giá khuyến mãi <i
                                                                style="font-size: 12px;color:green">(Giá khuyến mãi sẽ
                                                                lớn hơn giá thực bán)</i></label>
                                                        <input class="form-control"  value="{{$course->discount}}" type="number" name="discount"
                                                            value="0">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Mô tả ngắn cho khóa học</label>
                                                        <textarea name="description" placeholder="Nhập nội dung..." class="ckeditor">{!!$course->description!!}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Giới thiệu khóa học</label>
                                                        <textarea name="content" placeholder="Nhập nội dung..." class="ckeditor">{!!$course->content!!}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <label>Ảnh đại diện</label>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="thumb"><img id="img"
                                                                    src="{{ $course->images ?  $course->images : 'https://s.udemycdn.com/course/750x422/placeholder.jpg' }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <p>Tải hình ảnh khóa học của bạn lên đây. Hình ảnh phải đáp ứng
                                                                tiêu
                                                                chuẩn chất lượng hình ảnh khóa học của chúng tôi để được
                                                                chấp
                                                                nhận.
                                                                Hướng dẫn quan trọng: 750x422 pixel; .jpg, .jpeg,. gif hoặc
                                                                .png
                                                            </p>
                                                            <label for="upload">Click Upload Ảnh
                                                                <input type='file' value="{{$course->images}}" id="upload" name="image">
                                                            </label>
                                                        </div>

                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                        aria-labelledby="v-pills-profile-tab" tabindex="0">
                                        <div class="edit-profile-content card-box-style">
                                            <h3>Chương trình giảng dạy</h3>
                                            <p>Bạn có thể kéo thả để thay đổi thứ tự chương và bài giảng</p>
                                            <input type="hidden" name="tasks_json" id="tasks_json" />
                                            <div id="child-component-app">
                                                @php
                                                    $tasks = json_decode($course->size);
                                                @endphp
                                                <noi-dung-khoa-hoc :initial-tasks='@json($tasks)' :my-prop="{{ json_encode($test) }}"></noi-dung-khoa-hoc>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                        aria-labelledby="v-pills-messages-tab" tabindex="0">
                                        <div class="edit-profile-content card-box-style">
                                            <h3>Câu hỏi thường gặp</h3>
                                            <input type="hidden" name="faq_json" id="faq_json" />
                                            @php
                                                $cauhoithuonggap = json_decode($course->species);
                                            @endphp
                                            <div id="cau-hoi-thuong-gap">
                                                <cau-hoi-thuong-gap :initial-tasks='@json($cauhoithuonggap)'></cau-hoi-thuong-gap>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                        aria-labelledby="v-pills-settings-tab" tabindex="0">
                                        <div class="edit-profile-content card-box-style">
                                            <h3>Thông tin khác</h3>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Số buổi học</label>
                                                        <input class="form-control" type="text" name="so_buoi" value="{{$course->thickness}}"
                                                            placeholder="Vd: 12">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Số bài học</label>
                                                        <input class="form-control" type="text" name="so_bai_hoc" value="{{$course->ingredient}}"
                                                            placeholder="VD: 34">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Khóa học bao gồm</label>
                                                        <input type="hidden" name="ques_json" id="ques_json" />
                                                        @php
                                                        $khoahocbaogom = json_decode($course->preserve);
                                                    @endphp
                                                        <div id="khoa-hoc-bao-gom">
                                                            <khoa-hoc-bao-gom :initial-tasks='@json($khoahocbaogom)'></khoa-hoc-bao-gom>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>

                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- End Features Area -->
        <!-- Start Footer Area -->
        @include('crm_course.main.footer')
        <!-- End Footer Area -->
    </main>
@endsection
