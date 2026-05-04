@extends('layouts.main.master')
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
@section('css')
    <style>
        .course-details__info-box {
            transition: all 0.3s ease-in-out;
            will-change: transform;
        }
    </style>
    <link rel="stylesheet" href="/frontend/css/batdauhoc.css">
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sticky-sidebar@3.3.1/dist/sticky-sidebar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.0/classic/ckeditor.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoContainer = document.querySelector('.video-container');
            const iframe = videoContainer.querySelector('iframe');
            const loadingOverlay = videoContainer.querySelector('.loading-overlay');

            const lessons = document.querySelectorAll('.list-test li');
            const baitapContainer = document.querySelector('#home');

            // Khi click bài học (như trước đó)
            function loadBaiTap(taskId, studentId, courseId) {
                const container = document.querySelector('#home');
                container.innerHTML = '<p>Đang tải bài tập...</p>';

                axios.get(`/baitap/${taskId}?student_id=${studentId}&course_id=${courseId}`)
                    .then(response => {
                        const html = response.data.html;
                        container.innerHTML = html;

                        // Nếu trả về là form làm bài (chưa làm)
                        if (response.data.status === 'not_done') {
                            // Khởi tạo CKEditor
                            document.querySelectorAll('.ckeditor').forEach(function(el) {
                                ClassicEditor.create(el, {
                                    toolbar: ['heading', '|', 'bold', 'italic', 'underline',
                                        'link', '|', 'bulletedList', 'numberedList', '|',
                                        'undo', 'redo'
                                    ]
                                }).catch(error => console.error(error));
                            });

                            // Submit form
                            $('#quizForm').on('submit', function(e) {
                                e.preventDefault();
                                let formData = new FormData(this);
                                formData.append('student_id', studentId);
                                formData.append('course_id', courseId);
                                formData.append('test_id', taskId);
                                $.ajax({
                                    url: '/submit-quiz',
                                    method: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                            'content')
                                    },
                                    success: function(res) {
                                        alert('Nộp bài thành công!');
                                        console.log(res);
                                    },
                                    error: function(err) {
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

            if (!isMobile) {
                // Chỉ khởi tạo nếu chưa có
                if (!sidebarInstance) {
                    sidebarInstance = new StickySidebar('#sidebar', {
                        containerSelector: '.test-course-details .row',
                        innerWrapperSelector: '.sidebar__inner',
                        topSpacing: 0,
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
    </script>
@endsection

@section('content')
    <section class="test-course-details">
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
                                allowfullscreen></iframe>

                        </div>
                        <div class="lesson-navigation mt-3">
                            <button id="prevLesson" class="btn btn-outline-secondary"><i class="fas fa-chevron-left"></i>
                                Bài trước </button>
                            <button id="nextLesson" class="btn btn-outline-primary">Tiếp theo <i
                                    class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="sub-overview">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                                    type="button" role="tab" aria-controls="home" aria-selected="true">Bài
                                    Tập</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">Thông tin
                                    khóa học</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                                    type="button" role="tab" aria-controls="contact"
                                    aria-selected="false">Contact</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">...</div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...
                            </div>
                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...
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
                                                <li data-video="{{ $task->video }}" data-task-id="{{ $task->test_id }}"
                                                    data-student-id="{{ $profile->id }}"
                                                    data-course-id="{{ $detail->id }}">
                                                    <p class="">
                                                        <i class="fas fa-video"></i>
                                                        {{ $task->name }}
                                                    </p>
                                                    <p class="time"><span class="icon-clock"></span> {{ $task->time }}
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
    </section>
@endsection
