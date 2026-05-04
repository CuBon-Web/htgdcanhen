@extends('layouts.main.master')
@section('title')
    Đăng khóa học
@endsection
@section('description')
@endsection
@section('image')
@endsection
@section('css')
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

        input {
            margin: 5px 0;
            display: block;
            width: 100%;
            padding: 5px;
        }

        button {
            margin-top: 8px;
            padding: 6px 12px;
            cursor: pointer;
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
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/child-component.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sticky-sidebar@3.3.1/dist/sticky-sidebar.min.js"></script>
    <script>
        let sidebarInstance = null;

        function initStickySidebar() {
            const isMobile = window.innerWidth < 768;

            if (!isMobile) {
                // Chỉ khởi tạo nếu chưa có
                if (!sidebarInstance) {
                    sidebarInstance = new StickySidebar('#sidebar', {
                        containerSelector: '.become-a-teacher__tab-box .row',
                        innerWrapperSelector: '.sidebar__inner',
                        topSpacing: 120,
                        bottomSpacing: 20,
                        resizeSensor: true
                    });
                }
            } else {
                // Nếu đang ở mobile và đã khởi tạo -> hủy
                if (sidebarInstance) {
                    sidebarInstance.destroy();
                    sidebarInstance = null;
                }
            }
        }

        window.addEventListener('load', initStickySidebar);
        window.addEventListener('resize', initStickySidebar);

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
        document.querySelector('form').addEventListener('submit', function(e) {
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
                {
                    selector: 'input[name="image"]',
                    tabId: '#become-an-intructor',
                    message: 'Vui lòng chọn ảnh đại diện khóa học'
                },
                {
                    selector: 'input[name="so_buoi"]',
                    tabId: '#start-courses',
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
                activateTab('#intructor-rules');
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
            this.submit();
        });

        function activateTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active-btn', 'active');
            });

            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active', 'show');
                tab.style.display = 'none';
            });

            const tabBtn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
            if (tabBtn) tabBtn.classList.add('active-btn', 'active');

            const tabContent = document.querySelector(tabId);
            if (tabContent) {
                tabContent.classList.add('active-tab');
                tabContent.style.display = 'block';
            }
        }
    </script>
@endsection

@section('content')
    <section class="become-a-teacher">
        <div class="container">
            {{-- <div class="become-a-teacher__top">
                <div class="section-title-two text-center sec-title-animation animation-style1">
                    <h2 class="section-title-two__title title-animation">Khởi tạo khóa học của bạn
                    </h2>
                </div>
            </div> --}}

            <form action="{{ route('postCourse') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="number" value="0" name="id_course" hidden>
                <div class="become-a-teacher__tab-box tabs-box">
                    <div class="row">
                        <div class="col-xl-3" id="sidebar">
                            <ul class="tab-buttons clearfix list-unstyled">
                                <li data-tab="#become-an-intructor" class="tab-btn active-btn"><span><b>Bước 1:</b> Thông
                                        tin
                                        cơ bản</span></li>
                                <li data-tab="#intructor-rules" class="tab-btn"><span><b>Bước 2:</b> Chương trình giảng
                                        dạy</span></li>
                                <li data-tab="#faq" class="tab-btn"><span><b>Bước 3:</b> Câu hỏi thường gặp</span>
                                </li>
                                <li data-tab="#start-courses" class="tab-btn"><span><b>Bước 4:</b> Thông tin khác</span>
                                </li>
                            </ul>
                            <div class="contact-three__btn-box">
                                <button type="submit" class="thm-btn-two contact-three__btn"><span>Đăng khóa học</span></button>
                            </div>
                        </div>
                        <div class="col-xl-9">
                            <div class="tabs-content">
                                <!--tab-->
                                <div class="tab active-tab" id="become-an-intructor">
                                    <div class="become-a-teacher__right">
                                        <div class="section-title text-left sec-title-animation animation-style2"
                                            style="margin-bottom: 10px;">
                                            <h3 class=" title-animation">Thông tin cơ bản của khóa học

                                            </h3>
                                        </div>
                                        <p class="why-choose-one__text" style="margin-bottom: 20px;">Thông tin này sẽ
                                            hiển thị khái quát chung cho khóa học của bạn</p>
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6">
                                                <h4 class="contact-three__input-title">Tên khóa học*</h4>
                                                <div class="contact-three__input-box">
                                                    <input type="text" name="name" placeholder="Nhập tên khóa học...">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6">
                                                <h4 class="contact-three__input-title">Danh mục khóa học</h4>
                                                <div class="contact-three__input-box">
                                                    <select name="category" id="">
                                                        <option value="">--Chọn--</option>
                                                        @foreach ($category as $item)
                                                             <option value="{{$item->id}}">{{languageName($item->name)}}</option>
                                                        @endforeach
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6">
                                                <h4 class="contact-three__input-title">Giá thực bán <i style="font-size: 12px;color:green">(Miến phí nếu bỏ trống)</i></h4>
                                                <div class="contact-three__input-box">
                                                    <input type="number" name="price" value="0">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6">
                                                <h4 class="contact-three__input-title">Giá khuyến mãi <i style="font-size: 12px;color:green">(Giá khuyến mãi sẽ lớn hơn giá thực bán)</i></h4>
                                                <div class="contact-three__input-box">
                                                    <input type="number" name="discount" value="0">
                                                </div>
                                            </div>
                                            <div class="col-xl-12">
                                                <h4 class="contact-three__input-title">Mô tả ngắn cho khóa học</h4>
                                                <div class="contact-three__input-box text-message-box">
                                                    <textarea name="description" placeholder="Nhập nội dung..." class="ckeditor"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-12">
                                                <h4 class="contact-three__input-title">Giới thiệu khóa học</h4>
                                                <div class="contact-three__input-box text-message-box">
                                                    <textarea name="content" placeholder="Nhập nội dung..." class="ckeditor"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-12">
                                                <h4 class="contact-three__input-title">Ảnh đại diện</h4>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="thumb"><img id="img"
                                                                src="{{ $profile->avatar ? url('uploads/images/' . $profile->avatar) : 'https://s.udemycdn.com/course/750x422/placeholder.jpg' }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <p>Tải hình ảnh khóa học của bạn lên đây. Hình ảnh phải đáp ứng tiêu
                                                            chuẩn chất lượng hình ảnh khóa học của chúng tôi để được chấp
                                                            nhận.
                                                            Hướng dẫn quan trọng: 750x422 pixel; .jpg, .jpeg,. gif hoặc .png
                                                        </p>
                                                        <label for="upload">Click Upload Ảnh
                                                            <input type='file' id="upload" name="image">
                                                        </label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="result"></div>
                                    </div>
                                </div>
                                <!--tab-->
                                <!--tab-->
                                <div class="tab" id="intructor-rules">
                                    <div class="become-a-teacher__content">
                                        <div class="section-title text-left sec-title-animation animation-style2"
                                            style="margin-bottom: 10px;">
                                            <h3 class=" title-animation">Tạo chương trình giảng dạy

                                            </h3>
                                        </div>
                                        <p class="why-choose-one__text" style="margin-bottom: 20px;">Bạn có thể tạo các
                                            bài học trong khóa học, đăng tải các tài liệu, video và kéo thả để
                                            thay đổi vị trí các chương và bài học</p>
                                        <input type="hidden" name="tasks_json" id="tasks_json" />
                                        <div id="child-component-app">
                                            <noi-dung-khoa-hoc :my-prop="{{ json_encode( $test) }}"></noi-dung-khoa-hoc>
                                        </div>
                                    </div>
                                </div>
                                <!--tab-->
                                <!--tab-->
                                <div class="tab" id="faq">
                                    <div class="become-a-teacher__right">
                                        <div class="section-title text-left sec-title-animation animation-style2"
                                            style="margin-bottom: 10px;">
                                            <h3 class=" title-animation">Câu hỏi thường gặp</h3>
                                        </div>
                                        <input type="hidden" name="faq_json" id="faq_json" />
                                        <div id="cau-hoi-thuong-gap">
                                            <cau-hoi-thuong-gap></cau-hoi-thuong-gap>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab" id="start-courses">
                                    <div class="become-a-teacher__right">
                                        <div class="section-title text-left sec-title-animation animation-style2"
                                            style="margin-bottom: 10px;">
                                            <h3 class=" title-animation">Thông tin khác</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6">
                                                <h4 class="contact-three__input-title">Số buổi học</h4>
                                                <div class="contact-three__input-box">
                                                    <input type="text" name="so_buoi" placeholder="Vd: 12">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6">
                                                <h4 class="contact-three__input-title">Số bài học</h4>
                                                <div class="contact-three__input-box">
                                                    <input type="text" name="so_bai_hoc" placeholder="VD: 34">
                                                </div>
                                            </div>
                                            <div class="col-xl-12">
                                                 <h4 class="contact-three__input-title">Khóa học bao gồm</h4>
                                                <input type="hidden" name="ques_json" id="ques_json" />
                                                <div id="khoa-hoc-bao-gom">
                                                    <khoa-hoc-bao-gom></khoa-hoc-bao-gom>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="result"></div>
                                    </div>
                                </div>
                                <!--tab-->
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
