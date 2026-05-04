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
    <link rel="stylesheet" href="/frontend/crm-course/css/starttest.css">
@endsection
@section('js_crm_course')
    <script src="{{ asset('js/child-component.js') }}"></script>

    <!-- MathJax CDN for LaTeX rendering -->
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
                // Xử lý fill_in_blank: khi nhập vào input text thì active xanh
                var fillInput = block.querySelector('input[type="text"].form-control[name^="answers["]');
                if (fillInput && !fillInput.name.includes('][')) {
                    fillInput.addEventListener('input', function() {
                        var match = this.name.match(/^answers\[(\d+)\]$/);
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
        (function() {
            var timerDisplay = document.getElementById('timer');
            var minutes = parseInt(timerDisplay.textContent) || 40; // fallback 40 nếu lỗi
            var totalSeconds = minutes * 60;
            var interval = setInterval(function() {
                if (totalSeconds <= 0) {
                    clearInterval(interval);
                    timerDisplay.textContent = '00:00';
                    showTimeoutModal();
                    return;
                }
                totalSeconds--;
                var min = Math.floor(totalSeconds / 60);
                var sec = totalSeconds % 60;
                timerDisplay.textContent = (min < 10 ? '0' : '') + min + ':' + (sec < 10 ? '0' : '') + sec;
            }, 1000);

            function showTimeoutModal() {
                var modal = document.getElementById('timeout-modal');
                var countdown = document.getElementById('auto-submit-countdown');
                var submitBtn = document.getElementById('timeout-submit-btn');
                var form = document.getElementById('exam-form');
                var autoSubmitTime = 20;
                if (modal) modal.style.display = 'flex';
                if (countdown) countdown.textContent = autoSubmitTime;
                var autoSubmitInterval = setInterval(function() {
                    autoSubmitTime--;
                    if (countdown) countdown.textContent = autoSubmitTime;
                    if (autoSubmitTime <= 0) {
                        clearInterval(autoSubmitInterval);
                        if (form) form.submit();
                    }
                }, 1000);
                // Nếu bấm nộp bài thì submit luôn
                if (submitBtn) {
                    submitBtn.onclick = function() {
                        clearInterval(autoSubmitInterval);
                        if (form) form.submit();
                    };
                }
            }
        })();
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
            // Toggle lời giải cho bài tập
            document.querySelectorAll('.solution-toggle').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var targetId = btn.getAttribute('data-target');
                    var content = document.getElementById(targetId);
                    if (!content) return;
                    content.classList.toggle('show');
                    btn.classList.toggle('active');
                    btn.innerHTML = content.classList.contains('show')
                        ? '<i class="fa-regular fa-eye-slash"></i> Ẩn lời giải'
                        : '<i class="fa-regular fa-lightbulb"></i> Xem lời giải';
                });
            });

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
@endsection
@php
    // Helper function to render markdown images [img:key] to HTML img tags
    function renderMarkdownImages($content, $imagesArray = null) {
        if (!$content) return '';
        
        // Pattern để match format mới: [img:key]
        $newImagePattern = '/\[img:([^\]]+)\]/';
        
        // Xử lý format mới [img:key]
        $renderedContent = preg_replace_callback($newImagePattern, function($matches) use ($imagesArray) {
            $imgKey = $matches[1];
            $imageUrl = null;
            
            // Tìm URL từ images array nếu có
            if ($imagesArray && is_array($imagesArray)) {
                foreach ($imagesArray as $img) {
                    $url = is_array($img) ? ($img['url'] ?? $img) : $img;
                    // Extract key từ URL (filename không có extension)
                    $filename = basename($url);
                    $key = pathinfo($filename, PATHINFO_FILENAME);
                    if ($key === $imgKey) {
                        $imageUrl = $url;
                        break;
                    }
                }
            }
            
            // Nếu không tìm thấy, reconstruct URL từ img_key
            if (!$imageUrl) {
                $imageUrl = "/exam_images/{$imgKey}.png";
            }
            
            // Normalize URL
            $imageUrl = ltrim($imageUrl, '/');
            if (!str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/')) {
                $imageUrl = '/' . $imageUrl;
            }
            
            return '<img src="' . htmlspecialchars($imageUrl) . '" alt="Image" style="max-width: 100%; height: auto; border-radius: 8px; display: block;" class="question-markdown-image" />';
        }, $content);
        
        // Pattern để match format cũ: ![alt](url) (backward compatibility)
        $oldImagePattern = '/!\[([^\]]*)\]\(([^)]+)\)/';
        $renderedContent = preg_replace_callback($oldImagePattern, function($matches) {
            $alt = $matches[1] ?: 'Image';
            $url = trim($matches[2], '"\'');
            $url = ltrim($url, '/');
            if (!str_starts_with($url, 'http') && !str_starts_with($url, '/')) {
                $url = '/' . $url;
            }
            return '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" style="max-width: 100%; height: auto;border-radius: 8px; display: block;" class="question-markdown-image" />';
        }, $renderedContent);
        
        return $renderedContent;
    }
@endphp

@section('content_crm_course')
    <main class="main-content-wrap">
        <div class="contact-list-area">
            <div class="container">
                <div class="exam-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <strong>Thí sinh:</strong> {{ $user->name ?? 'Tên thí sinh' }}
                    </div>
                    <div>
                        <i class="fa-regular fa-clock" style="color:#3182ce;font-size:18px;margin-right:4px;"></i> <span
                            id="timer">{{ $dethi->time }}</span>
                    </div>
                    <button class="btn btn-primary" id="submit-exam" type="button"><i class="fa-regular fa-paper-plane"></i>
                        Nộp bài</button>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <form id="exam-form" method="POST" action="{{ route('submitTest') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="dethi_id" value="{{ $dethi->id }}">
                            <input type="hidden" name="teacher_id" value="{{ $dethi->created_by }}">
                            <input type="hidden" name="student_id" value="{{ $profile->id }}">
                            {{-- Thay thế vòng lặp cũ bằng đoạn sau --}}
                            @foreach ($dethi->parts as $partIndex => $part)
                                <div class="exam-part mb-5">
                                    <h4 class="mb-3">{{ $part['part'] }}. {{ $part['part_title'] }}</h4>
                                    @foreach ($part['questions'] as $index => $question)
                                        <div class="question-block mb-4"
                                            id="question-{{ $partIndex + 1 }}-{{ $question->question_no ?? ($index + 1) }}">
                                            <div class="mb-2">
                                                <strong>Câu {{ $question->question_no ?? ($index + 1) }}:</strong> 
                                                @php
                                                    $images = json_decode($question->image, true);
                                                @endphp
                                                {!! renderMarkdownImages($question->content, $images) !!}
                                            </div>
                                            @if($question->audio)
                                                <div class="question-audio-container">
                                                    <audio src="{{$question->audio}}" controls class="audio-player"></audio>
                                                </div>
                                            @endif
                                            @if ($question->question_type == 'multiple_choice')
                                                @foreach ($question->answers as $key => $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="answers[{{ $question->id }}]"
                                                            value="{{ $option->label }}"
                                                            id="q{{ $question->id }}_{{ $key }}">
                                                        <label class="form-check-label"
                                                            for="q{{ $question->id }}_{{ $key }}">
                                                            {!! renderMarkdownImages($option->label . '. ' . $option->content, $images) !!}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @elseif($question->question_type == 'true_false_grouped')
                                                @foreach ($question->answers as $key => $option)
                                                    <div class="true-false-block">
                                                        <div class="true-false-row">
                                                            <div class="true-false-statement">{!! renderMarkdownImages($option->label . ') ' . $option->content, $images) !!}</div>
                                                            <div class="true-false-options">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="answers[{{ $question->id }}][{{ $option->id }}]"
                                                                        value="1"
                                                                        id="q{{ $question->id }}_{{ $option->id }}_true">
                                                                    <label class="form-check-label"
                                                                        for="q{{ $question->id }}_{{ $option->id }}_true">Đúng</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="answers[{{ $question->id }}][{{ $option->id }}]"
                                                                        value="0"
                                                                        id="q{{ $question->id }}_{{ $option->id }}_false">
                                                                    <label class="form-check-label"
                                                                        for="q{{ $question->id }}_{{ $option->id }}_false">Sai</label>
                                                                </div>
                                                                <div class="form-check form-check-inline d-none">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="answers[{{ $question->id }}][{{ $option->id }}]"
                                                                        value="2"
                                                                        id="q{{ $question->id }}_{{ $option->id }}_none"
                                                                        checked>
                                                                    <label class="form-check-label"
                                                                        for="q{{ $question->id }}_{{ $option->id }}_none">Không
                                                                        chọn</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @elseif($question->question_type == 'fill_in_blank')
                                                @if($question->answers && count($question->answers) > 0)
                                                    @php
                                                        $correctAnswer = $question->answers->first();
                                                    @endphp
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control" name="answers[{{ $question->id }}]" 
                                                               placeholder="Nhập câu trả lời của bạn..." 
                                                               style="max-width: 300px;">
                                                        @if($correctAnswer && $correctAnswer->content)
                                                            <small class="text-muted d-block mt-1">
                                                                Gợi ý: {!! renderMarkdownImages($correctAnswer->content, $images) !!}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control" name="answers[{{ $question->id }}]" 
                                                               placeholder="Nhập câu trả lời của bạn..." 
                                                               style="max-width: 300px;">
                                                    </div>
                                                @endif
                                            @elseif($question->question_type == 'short_answer')
                                                <div class="mb-3">
                                                    <textarea class="form-control" name="answers[{{ $question->id }}][text]" rows="4"
                                                        placeholder="Nhập câu trả lời của bạn..."></textarea>
                                                    <div class="mt-2">
                                                        <label style="font-size:13px;">Tải ảnh bài tập tự luận:
                                                        </label>
                                                        <input type="file" accept="image/*"
                                                            name="answers[{{ $question->id }}][image]"
                                                            id="answer-image-{{ $question->id }}"
                                                            class="answer-image-input" style="display:none;">
                                                        <label for="answer-image-{{ $question->id }}"
                                                            class="custom-upload-label">
                                                            <i class="fa fa-paperclip"></i> <span>Tải ảnh</span>
                                                        </label>
                                                        <div class="image-preview-list"
                                                            id="preview-list-{{ $question->id }}"
                                                            style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($dethi->type === 'baitap' && !empty($question->explanation))
                                                <div class="solution-dropdown mt-2">
                                                    <button type="button"
                                                        class="btn btn-outline-info btn-sm solution-toggle"
                                                        data-target="solution-{{ $question->id }}">
                                                        <i class="fa-regular fa-lightbulb"></i> Xem lời giải
                                                    </button>
                                                    <div id="solution-{{ $question->id }}" class="solution-content">
                                                        {!! renderMarkdownImages($question->explanation, $images) !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <input type="hidden" name="actual_time" id="actual_time" value="0">
                            
                            <script>
                            // Khởi tạo thời gian làm bài
                            let startTime = Date.now();
                            let actualTime = 0;
                            
                            // Cập nhật thời gian mỗi giây
                            const timeInterval = setInterval(function() {
                                const currentTime = Date.now();
                                actualTime = Math.floor((currentTime - startTime) / 1000); // Thời gian tính bằng giây
                                
                                // Cập nhật giá trị vào input
                                document.getElementById('actual_time').value = actualTime;
                                
                                
                            }, 1000);
                            </script>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <div class="question-nav mb-4">
                            <h5>Danh sách câu hỏi</h5>
                            @foreach ($dethi->parts as $partIndex => $part)
                                <div class="mb-2"><strong>{{ $part['part'] }}</strong></div>
                                <div class="d-flex flex-wrap mb-2">
                                    @foreach ($part['questions'] as $index => $question)
                                        <a href="#question-{{ $partIndex + 1 }}-{{ $question->question_no ?? ($index + 1) }}"
                                            class="btn btn-outline-secondary m-1 question-nav-btn"
                                            id="nav-btn-{{ $question->id }}">
                                            {{ $question->question_no ?? ($index + 1) }}
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('crm_course.main.footer')
        <div id="submit-confirm-modal"
            style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.2);align-items:center;justify-content:center;">
            <div
                style="background:#fff;padding:32px 24px;border-radius:10px;max-width:80vw;min-width:320px;text-align:left;box-shadow:0 2px 16px rgba(0,0,0,0.15);">
                <div style="display:flex;align-items:center;margin-bottom:10px;">
                    <i class="fa fa-check-circle" style="color:#4caf50;font-size:22px;margin-right:8px"></i>
                    <span style="font-weight:600;font-size:17px;">Bạn có chắc chắn muốn nộp bài ?</span>
                </div>
                <div style="margin-bottom:8px;">
                    Thời gian làm bài của bạn còn: <span id="modal-timer"
                        style="color:#e53e3e;font-weight:600;">00:00:00</span>
                </div>
                <div id="modal-warning" style="color:#e53e3e;font-size:15px;margin-bottom:8px;">
                    <!-- Cảnh báo sẽ được cập nhật bằng JS -->
                </div>
                <div style="font-size:13px;color:#444;margin-bottom:16px;">
                    Khi xác nhận nhấn nộp bài, bạn sẽ không thể sửa lại bài thi của mình. Hãy chắc chắn bạn đã xem lại tất
                    cả các đáp án. Chúc bạn may mắn!
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;">
                    <button id="modal-cancel-btn" class="btn btn-light" style="min-width:80px;">Hủy</button>
                    <button id="modal-submit-btn" class="btn btn-primary" style="min-width:100px;"><i
                            class="fa-regular fa-paper-plane"></i> Nộp bài</button>
                </div>
            </div>
        </div>
        <div id="timeout-modal"
            style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;">
            <div
                style="background:#fff;padding:32px 24px;border-radius:10px;max-width:90vw;text-align:center;box-shadow:0 2px 16px rgba(0,0,0,0.15);">
                <h4 style="color:#e53e3e;margin-bottom:16px;">Hết giờ làm bài!</h4>
                <p>Bạn vui lòng bấm <b>Nộp bài</b> để gửi kết quả.<br>
                    Nếu không bấm, bài sẽ tự động nộp sau <span id="auto-submit-countdown">20</span> giây.</p>
                <button id="timeout-submit-btn" class="btn btn-primary" style="margin-top:16px;">Nộp bài</button>
            </div>
        </div>
    </main>
@endsection

<script>
    document.querySelectorAll('input[type="file"][id^="answer-image-"]').forEach(function(input) {
        input.addEventListener('change', function(e) {
            var file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(ev) {
                    let preview = input.parentNode.querySelector('.img-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'img-preview';
                        preview.style.maxWidth = '120px';
                        preview.style.marginTop = '6px';
                        input.parentNode.appendChild(preview);
                    }
                    preview.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<style>
    .image-preview-list img {
        max-width: 90px;
        max-height: 90px;
        border-radius: 6px;
        border: 1px solid #ddd;
        object-fit: cover;
        margin-bottom: 4px;
    }

    .image-preview-list .img-preview-wrap {
        position: relative;
        display: inline-block;
    }

    .image-preview-list .remove-img-btn {
        position: absolute;
        top: -7px;
        right: -7px;
        background: #e53e3e;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
    }

    .solution-dropdown {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
    }

    .solution-content {
        display: none;
        margin-top: 8px;
        padding: 10px 12px;
        background: #fff;
        border-radius: 6px;
        border: 1px dashed #cbd5e1;
        color: #334155;
    }

    .solution-content.show {
        display: block;
    }

    .solution-toggle.active {
        color: #0ea5e9;
        border-color: #0ea5e9;
        background: #e0f2fe;
    }
</style>

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

<style>
    .question-markdown-image {
        cursor: zoom-in;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 2px solid transparent;
    }
    .question-markdown-image:hover {
        transform: scale(1.03);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        border-color: #4f46e5;
    }
    
    /* Modal styles - chỉ thêm một lần */
    .image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.95);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.2s ease-out;
    }
    
    .image-modal-content {
        position: relative;
        max-width: 95%;
        max-height: 95%;
        animation: zoomIn 0.3s ease-out;
    }
    
    .image-modal-close {
        position: absolute;
        top: -50px;
        right: 0;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
        background: rgba(0,0,0,0.5);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    
    .image-modal-close:hover {
        background: rgba(255,255,255,0.2);
    }
    
    .image-modal-img {
        width: 100%;
        height: auto;
        max-height: 95vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    @media (max-width: 768px) {
        .image-modal {
            padding: 10px;
        }
        .image-modal-img {
            max-width: 98%;
            max-height: 90vh;
        }
        .image-modal-close {
            top: -40px;
            font-size: 35px;
            width: 40px;
            height: 40px;
        }
    }
</style>

<script>
    // Xử lý click vào ảnh markdown để zoom to
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý ảnh markdown (class .question-markdown-image)
        document.querySelectorAll('.question-markdown-image').forEach(function(img) {
            img.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                showImageModal(this.src);
            });
        });
        
        // Xử lý ảnh từ question-image-item (nếu còn sử dụng)
        document.querySelectorAll('.question-image-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const img = this.querySelector('img');
                if (img) {
                    showImageModal(img.src);
                }
            });
        });
    });
    
    function showImageModal(imageUrl) {
        
        // Tạo modal để hiển thị ảnh to
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        modal.innerHTML = `
            <div class="image-modal-content">
                <span class="image-modal-close" title="Đóng (ESC)">&times;</span>
                <img src="${imageUrl}" alt="Ảnh câu hỏi" class="image-modal-img">
            </div>
        `;
        
        // Thêm modal vào body
        document.body.appendChild(modal);
        
        // Ngăn scroll body khi modal mở
        document.body.style.overflow = 'hidden';
        
        // Xử lý đóng modal
        const closeModal = function() {
            if (document.body.contains(modal)) {
                modal.style.animation = 'fadeOut 0.2s ease-out';
                setTimeout(function() {
                    document.body.removeChild(modal);
                    document.body.style.overflow = '';
                }, 200);
            }
        };
        
        // Click vào modal background hoặc nút đóng
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.classList.contains('image-modal-close')) {
                closeModal();
            }
        });
        
        // Ngăn click vào ảnh đóng modal
        modal.querySelector('.image-modal-img').addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Đóng modal bằng phím ESC
        const escHandler = function(e) {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);
        
        // Focus vào modal để có thể đóng bằng ESC ngay lập tức
        modal.focus();
    }
</script>
