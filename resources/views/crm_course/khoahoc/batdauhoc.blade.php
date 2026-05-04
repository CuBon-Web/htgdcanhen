@extends('crm_course.main.master')
@section('title')
    {{ $detail->name }}
@endsection
@section('description')
    {{ $detail->description }}
@endsection
@section('image')
    @php
        $noidungkhoahoc = json_decode($detail->size) ?? [];
        $faq = json_decode($detail->species) ?? [];
        $khoahoc = json_decode($detail->preserve) ?? [];
    @endphp
    {{ url('' . $detail->images) }}
@endsection
@section('css_crm_course')
    <script src="https://cdn.jsdelivr.net/npm/sticky-sidebar@3.3.1/dist/sticky-sidebar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.0/classic/ckeditor.js"></script>
    <style>
        .course-details__info-box {
            transition: all 0.3s ease-in-out;
            will-change: transform;
        }
        
        /* Loading overlay cho quiz */
        .quiz-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-radius: 8px;
        }
        
        .quiz-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Style cho button khi loading */
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn .fa-spinner {
            margin-right: 8px;
        }
    </style>
    <link rel="stylesheet" href="/frontend/css/batdauhoc.css">
    <link rel="stylesheet" href="/frontend/crm-course/css/starttest.css">
    <link rel="stylesheet" href="/frontend/crm-course/css/ketqua.css">
    <style>
        .tab-content {
            display: block !important;
        }
    </style>
@endsection
@section('js_crm_course')
    <script src="{{ asset('js/child-component.js') }}"></script>
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['$', '$'],
                    ['\\(', '\\)']
                ],
                displayMath: [
                    ['$$', '$$'],
                    ['\\[', '\\]']
                ]
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lắng nghe tất cả radio của multiple_choice
            document.querySelectorAll('.question-block input[type=radio]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    // Lấy id câu hỏi từ name, ví dụ: answers[123]
                    var match = this.name.match(/^answers\[(\d+)\]/);
                    if (match) {
                        var questionId = match[1];
                        var navBtn = document.getElementById('nav-btn-' + questionId);
                        if (navBtn) {
                            navBtn.classList.add('answered');
                        }
                    }
                });
            });

            // Xử lý true_false_grouped: nếu trả lời hết thì xanh, chưa hết thì vàng
            document.querySelectorAll('.question-block').forEach(function(block) {
                var tfRadios = block.querySelectorAll('input[type=radio][name^="answers"][name*="]["]');
                if (tfRadios.length > 0) {
                    tfRadios.forEach(function(radio) {
                        radio.addEventListener('change', function() {
                            // Lấy id câu hỏi từ name: answers[123][456]
                            var match = this.name.match(/^answers\[(\d+)\]/);
                            if (match) {
                                var questionId = match[1];
                                var navBtn = document.getElementById('nav-btn-' +
                                    questionId);
                                if (navBtn) {
                                    // Đếm số đáp án con
                                    var subNames = Array.from(block.querySelectorAll(
                                        'input[type=radio][name^="answers[' +
                                        questionId + ']"]'));
                                    var subIds = [...new Set(subNames.map(r => r.name))];
                                    var allAnswered = subIds.every(function(subName) {
                                        return block.querySelector('input[name="' +
                                            subName + '"]:checked');
                                    });
                                    var anyAnswered = subIds.some(function(subName) {
                                        return block.querySelector('input[name="' +
                                            subName + '"]:checked');
                                    });
                                    navBtn.classList.remove('answered', 'partial-answered');
                                    if (allAnswered) {
                                        navBtn.classList.add('answered');
                                    } else if (anyAnswered) {
                                        navBtn.classList.add('partial-answered');
                                    }
                                }
                            }
                        });
                    });
                }
                // Xử lý short_answer: khi nhập vào textarea thì active xanh
                var textarea = block.querySelector('textarea.form-control[name^="answers["]');
                if (textarea) {
                    textarea.addEventListener('input', function() {
                        var match = this.name.match(/^answers\[(\d+)\]/);
                        if (match) {
                            var questionId = match[1];
                            var navBtn = document.getElementById('nav-btn-' + questionId);
                            if (navBtn) {
                                if (this.value.trim() !== '') {
                                    navBtn.classList.add('answered');
                                } else {
                                    navBtn.classList.remove('answered');
                                }
                            }
                        }
                    });
                }
            });

        });
    </script>
    <script>
        document.querySelectorAll('.question-nav-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault(); // Ngăn đổi hash trên URL
                var href = btn.getAttribute('href');
                if (href && href.startsWith('#')) {
                    var target = document.querySelector(href);
                    if (target) {
                        // Cuộn mượt tới câu hỏi, offset để không bị che bởi header sticky
                        var yOffset = -110; // sticky header cao 110px
                        var y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                        window.scrollTo({
                            top: y,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    </script>
    <script>
        window.onbeforeunload = function(e) {
            // Hiển thị cảnh báo khi người dùng reload hoặc rời khỏi trang
            e = e || window.event;
            // For IE and Firefox prior to version 4
            if (e) {
                e.returnValue = 'Bạn có chắc chắn muốn thoát? Dữ liệu làm bài có thể bị mất!';
            }
            // For Safari, Chrome, Firefox 4+, Opera 12+ and others
            return 'Bạn có chắc chắn muốn thoát? Dữ liệu làm bài có thể bị mất!';
        };
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var submitBtn = document.getElementById('submit-exam');
            var modal = document.getElementById('submit-confirm-modal');
            var modalCancel = document.getElementById('modal-cancel-btn');
            var modalSubmit = document.getElementById('modal-submit-btn');
            var modalTimer = document.getElementById('modal-timer');
            var modalWarning = document.getElementById('modal-warning');
            var form = document.getElementById('exam-form');
            var timerDisplay = document.getElementById('timer');

            // Hàm đếm số câu chưa trả lời (ví dụ cho multiple_choice, true_false_grouped, short_answer)
            function countUnanswered() {
                var unanswered = 0;
                document.querySelectorAll('.question-block').forEach(function(block) {
                    var radios = block.querySelectorAll('input[type=radio]');
                    var textarea = block.querySelector('textarea');
                    if (radios.length > 0) {
                        var checked = Array.from(radios).some(r => r.checked);
                        if (!checked) unanswered++;
                    } else if (textarea) {
                        if (!textarea.value.trim()) unanswered++;
                    }
                });
                return unanswered;
            }

            // Hàm hiển thị modal xác nhận
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (modal) modal.style.display = 'flex';
                    // Hiển thị thời gian còn lại
                    if (modalTimer && timerDisplay) {
                        modalTimer.textContent = timerDisplay.textContent.length === 5 ?
                            '00:' + timerDisplay.textContent :
                            timerDisplay.textContent;
                    }
                    // Hiển thị cảnh báo nếu còn câu chưa trả lời
                    var unanswered = countUnanswered();
                    if (modalWarning) {
                        if (unanswered > 0) {
                            modalWarning.innerHTML = 'Cảnh báo: Bạn còn <b>' + unanswered +
                                '</b> câu hỏi chưa trả lời. Bạn có chắc muốn kết thúc bài thi?';
                        } else {
                            modalWarning.innerHTML = '';
                        }
                    }
                });
            }
            // Đóng modal khi bấm Hủy
            if (modalCancel) {
                modalCancel.addEventListener('click', function() {
                    if (modal) modal.style.display = 'none';
                });
            }
            // Nộp bài khi bấm Nộp bài trong modal
            if (modalSubmit) {
                modalSubmit.addEventListener('click', function() {
                    if (form) form.submit();
                });
            }
            // Đóng modal khi bấm ra ngoài
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) modal.style.display = 'none';
                });
            }
        });
    </script>
    <script>
        document.querySelectorAll('.answer-image-input').forEach(function(input) {
            input.addEventListener('change', function(e) {
                var previewList = document.getElementById('preview-list-' + input.id.replace(
                    'answer-image-', ''));
                previewList.innerHTML = '';
                var file = input.files[0];
                if (file && file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(ev) {
                        var wrap = document.createElement('div');
                        wrap.className = 'img-preview-wrap';
                        var img = document.createElement('img');
                        img.className = 'img-preview';
                        img.src = ev.target.result;
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'remove-img-btn';
                        btn.innerHTML = '&times;';
                        btn.onclick = function() {
                            input.value = '';
                            previewList.innerHTML = '';
                        };
                        wrap.appendChild(img);
                        wrap.appendChild(btn);
                        previewList.appendChild(wrap);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoContainer = document.querySelector('.video-container');
            const iframe = videoContainer.querySelector('iframe');
            const loadingOverlay = videoContainer.querySelector('.loading-overlay');

            // Fix video aspect ratio to prevent black bars
            function fixVideoAspectRatio() {
                const containerWidth = videoContainer.offsetWidth;
                const aspectRatio = 16 / 9; // Standard video aspect ratio
                const newHeight = containerWidth / aspectRatio;
                
                videoContainer.style.height = newHeight + 'px';
                videoContainer.style.paddingBottom = '0';
                
                // Ensure iframe fills container
                iframe.style.width = '100%';
                iframe.style.height = '100%';
                iframe.style.objectFit = 'cover';
            }

            // Call on load and resize
            fixVideoAspectRatio();
            window.addEventListener('resize', fixVideoAspectRatio);

            const lessons = document.querySelectorAll('.list-test li');
            const baitapContainer = document.querySelector('#home');

            // Khi click bài học (như trước đó)
            function loadBaiTap(taskId, studentId, courseId) {
                const container = document.querySelector('#home');
                container.innerHTML = '<p>Đang tải bài tập...</p>';
                axios.get(`/crm-course/baitap/${taskId}?student_id=${studentId}&course_id=${courseId}`)
                    .then(response => {
                        const html = response.data.html;
                        container.innerHTML = html;
                        if (window.MathJaxUtils && window.MathJaxUtils.renderMathJaxForElement) {
                            window.MathJaxUtils.renderMathJaxForElement(container);
                        } else if (window.MathJax && window.MathJax.typesetPromise) {
                            // Fallback nếu không có MathJaxUtils
                            setTimeout(() => {
                                window.MathJax.typesetPromise([container])
                                    .then(() => console.log('MathJax render thành công!'))
                                    .catch((err) => console.error('MathJax render lỗi:', err));
                            }, 100);
                        }
                        
                        // Đăng ký event listener cho modal ảnh sau khi HTML được render
                        initImageModal(container);
                        
                        // Nếu trả về là form làm bài (chưa làm)
                        if (response.data.status === 'not_done') {
                            // Submit form
                            $('#quizForm').on('submit', function(e) {
                                e.preventDefault();
                                
                                // Hiển thị loading
                                const submitBtn = $(this).find('button[type="submit"]');
                                const originalText = submitBtn.text();
                                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang nộp bài...');
                                
                                // Thêm overlay loading cho container
                                const loadingOverlay = $('<div class="quiz-loading-overlay"><div class="quiz-spinner"></div></div>');
                                $(container).css('position', 'relative');
                                container.appendChild(loadingOverlay[0]);
                                
                                let formData = new FormData(this);
                                formData.append('student_id', studentId);
                                formData.append('course_id', courseId);
                                formData.append('test_id', taskId);
                                $.ajax({
                                    url: '/bai-tap/nop-bai-thi',
                                    method: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                    success: function(res) {
                                        // Ẩn loading
                                        submitBtn.prop('disabled', false).text(originalText);
                                        $('.quiz-loading-overlay').remove();
                                        
                                        if (window.MathJaxUtils && window.MathJaxUtils.renderMathJaxForElement) {
                                            window.MathJaxUtils.renderMathJaxForElement(container);
                                        } else if (window.MathJax && window.MathJax.typesetPromise) {
                                            // Fallback nếu không có MathJaxUtils
                                            setTimeout(() => {
                                                window.MathJax.typesetPromise([container])
                                                    .then(() => console.log('MathJax render thành công!'))
                                                    .catch((err) => console.error('MathJax render lỗi:', err));
                                            }, 100);
                                        }
                                        const html = res.html;
                                        container.innerHTML = html;
                                    },
                                    error: function(err) {
                                        // Ẩn loading khi có lỗi
                                        submitBtn.prop('disabled', false).text(originalText);
                                        $('.quiz-loading-overlay').remove();
                                        
                                        alert('Lỗi khi nộp bài.');
                                        console.log(err);
                                    }
                                });
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        container.innerHTML = '<p>Lỗi khi tải bài tập.</p>';
                    });
            }



            function setLesson(li) {
                const videoUrl = li.getAttribute('data-video');
                const taskId = li.getAttribute('data-task-id');
                const studentId = li.getAttribute('data-student-id');
                const courseId = li.getAttribute('data-course-id');

                if (videoUrl && iframe.src !== videoUrl) {
                    loadingOverlay.style.display = 'flex';
                    iframe.src = videoUrl;
                }

                lessons.forEach(item => item.classList.remove('active'));
                li.classList.add('active');
                li.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Gọi load bài tập qua AJAX
                if (taskId) {
                    loadBaiTap(taskId, studentId, courseId);
                } else {
                    baitapContainer.innerHTML = '<p>Không có bài tập.</p>';
                }
            }

            // Khi load trang, tự động chọn bài đầu tiên
            const firstLi = lessons[0];
            if (firstLi && firstLi.getAttribute('data-video')) {
                setLesson(firstLi);
            }

            // Click bài học
            lessons.forEach(function(li) {
                li.addEventListener('click', function() {
                    setLesson(this);
                });
            });

            iframe.addEventListener('load', function() {
                loadingOverlay.style.display = 'none';
            });

            document.getElementById('nextLesson').addEventListener('click', function() {
                const activeLi = document.querySelector('.list-test li.active');
                const currentIndex = Array.from(lessons).indexOf(activeLi);
                if (currentIndex < lessons.length - 1) {
                    const nextLi = lessons[currentIndex + 1];
                    setLesson(nextLi);
                }
            });

            document.getElementById('prevLesson').addEventListener('click', function() {
                const activeLi = document.querySelector('.list-test li.active');
                const currentIndex = Array.from(lessons).indexOf(activeLi);
                if (currentIndex > 0) {
                    const prevLi = lessons[currentIndex - 1];
                    setLesson(prevLi);
                }
            });
        });
    </script>
    <script>
        let sidebarInstance = null;

        function initStickySidebar() {
            const isMobile = window.innerWidth < 768;
            const sidebar = document.querySelector('#sidebar');
            const container = document.querySelector('.testonline .row');

            if (!isMobile && sidebar && container) {
                // Hủy instance cũ nếu có
                if (sidebarInstance) {
                    sidebarInstance.destroy();
                    sidebarInstance = null;
                }

                // Khởi tạo mới
                try {
                    sidebarInstance = new StickySidebar('#sidebar', {
                        containerSelector: '.testonline .row',
                        innerWrapperSelector: '.sidebar__inner',
                        topSpacing: 0,
                        bottomSpacing: 20,
                        resizeSensor: true
                    });
                    console.log('StickySidebar đã được khởi tạo thành công');
                } catch (error) {
                    console.error('Lỗi khởi tạo StickySidebar:', error);
                }
            } else {
                // Nếu đang ở mobile hoặc không tìm thấy elements -> hủy
                if (sidebarInstance) {
                    sidebarInstance.destroy();
                    sidebarInstance = null;
                }
            }
        }

        // Đợi DOM load xong
        document.addEventListener('DOMContentLoaded', function() {
            // Đợi thêm một chút để đảm bảo tất cả elements đã render
            setTimeout(initStickySidebar, 500);
        });

        window.addEventListener('resize', function() {
            setTimeout(initStickySidebar, 100);
        });
    </script>
@endsection
@section('content_crm_course')
    <main class="main-content-wrap">
        <div class="content">
            <div class="blog-post-area ptb-100">
                <div class="container-fluid">
                    <div class="row testonline">
                        <div class="col-xl-9 col-lg-7 ">
                            <div class="lesson-container">
                                <div class="lesson-header">Khóa học: {{ $detail->name }}</div>
                                <div class="video-container">
                                    <div class="loading-overlay" style="display: none;">
                                        <div class="spinner"></div>
                                    </div>
                                    <iframe src=""
                                        allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;"
                                        allowfullscreen
                                        style="width: 100%; height: 100%; border: none; object-fit: cover;"></iframe>

                                </div>
                                <div class="lesson-navigation mt-3">
                                    <button id="prevLesson" class="btn btn-outline-secondary"><i
                                            class="fas fa-chevron-left"></i>
                                        Bài trước </button>
                                    <button id="nextLesson" class="btn btn-outline-primary">Tiếp theo <i
                                            class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                            <div class="sub-overview">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                            data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                            aria-selected="true">Bài
                                            Tập</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                            data-bs-target="#profile" type="button" role="tab" aria-controls="profile"
                                            aria-selected="false">Thông tin
                                            khóa học</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab"
                                            data-bs-target="#contact" type="button" role="tab" aria-controls="contact"
                                            aria-selected="false">Contact</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel"
                                        aria-labelledby="home-tab">...</div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        {!! $detail->content !!}
                                    </div>
                                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                        <div class="contact-info-wrap">
                                            <div class="single-contact-info">
                                                <h3>Liên Hệ</h3>
                    
                                                <ul class="address">
                                                    <li class="location">
                                                        <span>Địa chỉ</span>
                                                        : {{$setting->address1}}
                                                    </li>
                                                    <li>
                                                        <span>Phone</span>
                                                        <a href="tel:{{$setting->phone1}}">: {{$setting->phone1}}</a>
                                                    </li>
                                                    <li>
                                                        <span>Email</span>
                                                        <a href="mailto:{{$setting->email}}">{{$setting->email}}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-5">
                            <div id="sidebar">
                                <div class="accordion" id="accordionExample">
                                    @foreach ($noidungkhoahoc as $k => $item)
                                        @php

                                            $totalSeconds = 0;
                                            foreach ($item->detail_task as $key => $value) {
                                                [$minutes, $seconds] = explode(':', $value->time);
                                                $totalSeconds += $minutes * 60 + $seconds;
                                            }
                                            $totalMinutes = $totalSeconds / 60;
                                        @endphp
                                        <div class="accordion-item ">
                                            <h2 class="accordion-header " id="headingOne_{{ $k }}">
                                                <div class="accordion-button accordion-test {{ $k == 0 ? '' : 'collapsed' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne-{{ $k }}"
                                                    aria-expanded="{{ $k == 0 ? 'true' : 'false' }}"
                                                    aria-controls="collapseOne-{{ $k }}">
                                                    <p>{{ $item->chuong }}</p>
                                                    <p class="time">{{ round($totalMinutes, 2) }} Phút |
                                                        {{ count($item->detail_task) }} bài học</p>
                                                </div>

                                            </h2>
                                            <div id="collapseOne-{{ $k }}"
                                                class="accordion-collapse collapse {{ $k == 0 ? 'show' : '' }} "
                                                aria-labelledby="headingOne_{{ $k }}"
                                                data-bs-parent="#accordionExample">
                                                <ul class="list-test">
                                                    @foreach ($item->detail_task as $task)
                                                        <li data-video="{{ $task->video }}"
                                                            data-task-id="{{ $task->test_id }}"
                                                            data-student-id="{{ $profile->id }}"
                                                            data-course-id="{{ $detail->id }}">
                                                            <p class="">
                                                                <i class="fas fa-video"></i>
                                                                {{ $task->name }}
                                                            </p>
                                                            <p class="time"><span class="icon-clock"></span>
                                                                {{ $task->time }}
                                                            </p>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- End Features Area -->
        <!-- Start Footer Area -->
        @include('crm_course.main.footer')
        <!-- End Footer Area -->
    </main>
    
    <script>
        // Hàm khởi tạo modal ảnh đơn giản cho container được render động
        function initImageModal(container) {
            // Xử lý click vào ảnh câu hỏi để xem to hơn
            container.addEventListener('click', function(e) {
                if (e.target.closest('.question-image-item')) {
                    const imageItem = e.target.closest('.question-image-item');
                    const img = imageItem.querySelector('img');
                    const imageUrl = img.src;
                    
                    // Tạo modal để hiển thị ảnh to
                    const modal = document.createElement('div');
                    modal.className = 'image-modal';
                    modal.innerHTML = `
                        <div class="image-modal-content-baitap">
                            <span class="image-modal-close">&times;</span>
                            <img src="${imageUrl}" alt="Ảnh câu hỏi" class="image-modal-img">
                        </div>
                    `;
                    
                    // Thêm CSS cho modal
                    const style = document.createElement('style');
                    style.textContent = `
                        .image-modal {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0,0,0,0.9);
                            z-index: 9999;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 20px;
                        }
                        .image-modal-content-baitap {
                            position: relative;
                            max-width: 90%;
                            max-height: 90%;
                        }
                        .image-modal-close {
                            position: absolute;
                            top: -40px;
                            right: 0;
                            color: white;
                            font-size: 35px;
                            font-weight: bold;
                            cursor: pointer;
                            z-index: 10000;
                        }
                        .image-modal-close:hover {
                            color: #ddd;
                        }
                        .image-modal-img {
                            width: 100%!important;
                            height: auto!important;
                            max-height: 90vh!important;
                            object-fit: contain!important;
                            border-radius: 8px!important;
                            box-shadow: 0 4px 20px rgba(0,0,0,0.3)!important;
                        }
                        @media (max-width: 768px) {
                            .image-modal {
                                padding: 10px;
                            }
                            .image-modal-img {
                                max-width: 95%;
                                max-height: 85vh;
                            }
                        }
                    `;
                    
                    document.head.appendChild(style);
                    document.body.appendChild(modal);
                    
                    // Xử lý đóng modal
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal || e.target.classList.contains('image-modal-close')) {
                            closeModal();
                        }
                    });
                    
                    // Đóng modal bằng phím ESC
                    const escHandler = function(e) {
                        if (e.key === 'Escape') {
                            closeModal();
                            document.removeEventListener('keydown', escHandler);
                        }
                    };
                    document.addEventListener('keydown', escHandler);
                    
                    // Hàm đóng modal
                    function closeModal() {
                        if (document.body.contains(modal)) {
                            document.body.removeChild(modal);
                        }
                        if (document.head.contains(style)) {
                            document.head.removeChild(style);
                        }
                    }
                }
            });
        }
    </script>
@endsection
