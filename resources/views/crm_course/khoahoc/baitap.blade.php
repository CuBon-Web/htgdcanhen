
<form id="quizForm">
            @csrf
            <input type="hidden" name="dethi_id" value="{{ $dethi->id }}">
            <input type="hidden" name="teacher_id" value="{{ $dethi->created_by }}">
            <input type="hidden" name="student_id" value="{{ $profile->id }}"> 
            <input type="hidden" name="course_id" value="{{ $courseId }}">
            {{-- Thay thế vòng lặp cũ bằng đoạn sau --}}
            @foreach($dethi->parts as $partIndex => $part)
                <div class="exam-part mb-5">
                    <h4 class="mb-3">{{ $part['part'] }}. {{ $part['part_title'] }}</h4>
                    @foreach($part['questions'] as $index => $question)
                        <div class="question-block mb-4" id="question-{{ $partIndex+1 }}-{{ $index+1 }}">
                            <div class="mb-2">
                                <strong>Câu {{ $question->question_no }}:</strong> {!! $question->content !!}
                            </div>
                            @php
                                $images = json_decode($question->image, true);
                            @endphp
                            @if($images)
                                <div class="question-images-container">
                                    @foreach($images as $image)
                                        <div class="question-image-item">
                                            <img src="{{$image['url']}}" alt="Ảnh câu hỏi" class="img-fluid">
                                            <div class="image-overlay">
                                                <i class="fas fa-search-plus"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if($question->audio)
                                <div class="question-audio-container">
                                    <audio src="{{$question->audio}}" controls class="audio-player"></audio>
                                </div>
                            @endif
                            @if($question->question_type == 'multiple_choice')
                                @foreach($question->answers as $key => $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $option->label }}" id="q{{ $question->id }}_{{ $key }}">
                                        <label class="form-check-label" for="q{{ $question->id }}_{{ $key }}">
                                            {{$option->label}}. {{ $option->content }}
                                        </label>
                                    </div>
                                @endforeach
                            @elseif($question->question_type == 'true_false_grouped')
                                @foreach($question->answers as $key => $option)
                                    <div class="true-false-block">
                                        <div class="true-false-row">
                                            <div class="true-false-statement">{{ $option->label }} ) {{ $option->content }}</div>
                                            <div class="true-false-options">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="answers[{{ $question->id }}][{{ $option->id }}]"
                                                           value="1" id="q{{ $question->id }}_{{ $option->id }}_true">
                                                    <label class="form-check-label" for="q{{ $question->id }}_{{ $option->id }}_true">Đúng</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                           name="answers[{{ $question->id }}][{{ $option->id }}]"
                                                           value="0" id="q{{ $question->id }}_{{ $option->id }}_false">
                                                    <label class="form-check-label" for="q{{ $question->id }}_{{ $option->id }}_false">Sai</label>
                                                </div>
                                                <div class="form-check form-check-inline d-none">
                                                    <input class="form-check-input" type="radio"
                                                           name="answers[{{ $question->id }}][{{ $option->id }}]"
                                                           value="2" id="q{{ $question->id }}_{{ $option->id }}_none" checked>
                                                    <label class="form-check-label" for="q{{ $question->id }}_{{ $option->id }}_none">Không chọn</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($question->question_type == 'fill_in_blank')
                                <div class="mb-3">
                                    <textarea class="form-control"
                                              name="answers[{{ $question->id }}][text]"
                                              rows="2"
                                              placeholder="Nhập câu trả lời của bạn..."></textarea>
                                </div>
                            @elseif($question->question_type == 'short_answer')
                                <div class="mb-3">
                                    <textarea class="form-control"
                                              name="answers[{{ $question->id }}][text]"
                                              rows="4"
                                              placeholder="Nhập câu trả lời của bạn..."></textarea>
                                    <div class="mt-2">
                                        <label style="font-size:13px;">Tải ảnh bài tập tự luận: </label>
                                        <input type="file" accept="image/*" name="answers[{{ $question->id }}][image]" id="answer-image-{{ $question->id }}" class="answer-image-input" style="display:none;">
                                        <label for="answer-image-{{ $question->id }}" class="custom-upload-label">
                                            <i class="fa fa-paperclip"></i> <span>Tải ảnh</span>
                                        </label>
                                        <div class="image-preview-list" id="preview-list-{{ $question->id }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px;"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
            <button class="btn btn-primary" type="submit"><i class="fa-regular fa-paper-plane"></i>
                Nộp bài</button>
        </form>


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
