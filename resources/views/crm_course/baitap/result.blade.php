<div class="sidebar-left">
    <div class="sidebar">
        <div class="drive-wrap">
            <!-- Score Display -->
            <div class="score-display">
                Điểm:
                {{ rtrim(rtrim(number_format($session->total_score, 2), '0'), '.') }}/{{ rtrim(rtrim(number_format($session->max_score, 2), '0'), '.') }}
            </div>


        </div>
    </div>
</div>

<div class="content-right">
    <div class="my-result-area">
        <div class="quick-access">
            <!-- Questions Content -->
            @foreach ($session->dethi->parts as $partIndex => $part)
                <div class="question-section">
                    <div class="question-title">
                        {{ $part->part }}. {{ $part->part_title }}
                    </div>

                    @foreach ($part->questions as $questionIndex => $question)
                        @if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false_grouped' || $question->question_type === 'fill_in_blank' || $question->question_type === 'short_answer')
                            <div class="question-item">
                                <div style="margin-bottom: 15px;">
                                    <strong>Câu {{ $question->question_no }}</strong> -
                                    {!! $question->content !!}
                                </div>

                                @if ($question->question_type === 'multiple_choice')
                                    <div class="options-container">
                                        @foreach ($question->answers as $answerIndex => $answer)
                                            @php
                                                $isCorrect = $answer->is_correct;
                                                $studentAnswer = $session->answers
                                                    ->where('question_id', $question->id)
                                                    ->first();
                                                $isSelected =
                                                    $studentAnswer &&
                                                    $studentAnswer->answer_choice ===
                                                        chr(65 + $answerIndex);
                                                $optionClass = '';
                                                if ($isCorrect) {
                                                    $optionClass = 'correct';
                                                } elseif ($isSelected && !$isCorrect) {
                                                    $optionClass = 'incorrect';
                                                }
                                            @endphp
                                            <div class="option {{ $optionClass }}">
                                                <span
                                                    class="option-label">{{ chr(65 + $answerIndex) }}.</span>
                                                <span>{!! $answer->content !!}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="answer-display">
                                        <span class="correct-answer">
                                            Đáp án đúng:
                                            @foreach ($question->answers as $answerIndex => $answer)
                                                @if ($answer->is_correct)
                                                    {{ chr(65 + $answerIndex) }}
                                                @endif
                                            @endforeach
                                        </span>
                                        <div class="answer-buttons">
                                            @foreach ($question->answers as $answerIndex => $answer)
                                                @php
                                                    $isCorrect = $answer->is_correct;
                                                    $studentAnswer = $session->answers
                                                        ->where('question_id', $question->id)
                                                        ->first();
                                                    $isSelected =
                                                        $studentAnswer &&
                                                        $studentAnswer->answer_choice ===
                                                            chr(65 + $answerIndex);
                                                    $btnClass = '';
                                                    if ($isCorrect) {
                                                        $btnClass = 'correct';
                                                    } elseif ($isSelected && !$isCorrect) {
                                                        $btnClass = 'incorrect';
                                                    }
                                                @endphp
                                                <div class="answer-btn {{ $btnClass }}">
                                                    {{ chr(65 + $answerIndex) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @elseif($question->question_type === 'fill_in_blank')
                                    @php
                                        $studentAnswer = $session->answers
                                            ->where('question_id', $question->id)
                                            ->first();
                                        $correctAnswer = $question->answers->where('is_correct', true)->first();
                                        $isCorrect = $studentAnswer && $correctAnswer && 
                                                    strtolower(trim($studentAnswer->answer_text)) === strtolower(trim($correctAnswer->content));
                                    @endphp
                                     <div class="fill-in-blank-answer">
                                        <div class="student-answer-section d-flex align-items-center">
                                            <strong>Trả lời của học sinh:</strong>
                                            <div class="student-answer {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                                {{ $studentAnswer ? $studentAnswer->answer_text : 'Không trả lời' }}
                                            </div>
                                        </div>
                                        <div class="correct-answer-section d-flex align-items-center gap-2">
                                            <strong>Đáp án đúng:</strong>
                                            <div class="student-answer">
                                                {{ $correctAnswer ? $correctAnswer->content : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($question->question_type === 'true_false_grouped')
                                   

                                    <div style="margin: 15px 0;">
                                        @foreach ($question->answers as $answerIndex => $answer)
                                            <div style="margin-bottom: 8px;">
                                                <strong>{{ chr(97 + $answerIndex) }})</strong>
                                                {!! $answer->content !!}
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="true-false-summary">
                                        @php
                                            $studentAnswer = $session->answers
                                                ->where('question_id', $question->id)
                                                ->first();
                                            $studentChoices = [];
                                            $wrongAnswers = [];

                                            if ($studentAnswer && $studentAnswer->answer_choice) {
                                                $studentChoices = json_decode(
                                                    $studentAnswer->answer_choice,
                                                    true,
                                                );
                                            }
                                            foreach (
                                                $question->answers
                                                as $answerIndex => $answer
                                            ) {
                                                $subQuestionId = $answer->id;
                                                $correctAnswer = $answer->is_correct; // Đáp án đúng từ dethi_answers
                                                $studentChoice = isset(
                                                    $studentChoices[$subQuestionId],
                                                )
                                                    ? $studentChoices[$subQuestionId]
                                                    : '2';
                                                $isSelected = $studentChoice === '1';
                                                $isNotChosen = $studentChoice === '2';

                                                // Chỉ tính sai nếu học sinh chọn sai (không tính câu không chọn)
                                                if (
                                                    !$isNotChosen &&
                                                    $isSelected != $correctAnswer
                                                ) {
                                                    $wrongAnswers[] = chr(97 + $answerIndex);
                                                }
                                            }
                                        @endphp

                                        <div class="summary-row">
                                            <span class="summary-label">Đáp án đúng:</span>
                                            <span class="summary-value">
                                                @foreach ($question->answers as $answerIndex => $answer)
                                                    @if ($answer->is_correct)
                                                        {{ chr(97 + $answerIndex) }}) Đúng
                                                    @else
                                                        {{ chr(97 + $answerIndex) }}) Sai
                                                    @endif
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </span>
                                        </div>

                                        <div class="summary-row">
                                            <span class="summary-label">Thí sinh chọn:</span>
                                            <span class="summary-value">
                                                @foreach ($question->answers as $answerIndex => $answer)
                                                    @php
                                                        $subQuestionId = $answer->id;
                                                        $studentChoice = isset(
                                                            $studentChoices[$subQuestionId],
                                                        )
                                                            ? $studentChoices[$subQuestionId]
                                                            : '2';
                                                        $isSelected = $studentChoice === '1';
                                                        $isNotChosen = $studentChoice === '2';
                                                    @endphp
                                                    {{ chr(97 + $answerIndex) }})
                                                    @if ($isNotChosen)
                                                        <span
                                                            style="color: #666; font-style: italic;">Không
                                                            chọn</span>
                                                    @else
                                                        {{ $isSelected ? 'Đúng' : 'Sai' }}
                                                    @endif
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </span>
                                        </div>

                                        @if (!empty($wrongAnswers))
                                            <div class="summary-row">
                                                <span class="summary-label">Câu làm sai:</span>
                                                <span
                                                    class="wrong-answers">{{ implode(') ', $wrongAnswers) }})</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="explanation-toggle">
                                    <i class="fas fa-chevron-down"></i> Giải thích
                                </div>
                                <div class="explanation-content" style="display: none;">
                                    <p>{{$question->explanation}}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="essay-questions-container">
            @foreach ($session->dethi->parts as $partIndex => $part)
                @foreach ($part->questions as $questionIndex => $question)
                    @if ($question->question_type === 'short_answer')
                        @php
                            $studentAnswer = $session->answers
                                ->where('question_id', $question->id)
                                ->first();
                        @endphp
                        <div class="essay-question-item">
                            <div class="essay-question-header">
                                <div class="question-info">
                                    <span class="question-number">Câu
                                        {{ $question->question_no }}</span>
                                    <span class="question-points">({{ $question->score ?? 2 }}
                                        điểm)</span>
                                </div>
                                {{-- <button class="btn btn-primary btn-sm grade-btn"
                                    data-question-id="{{ $question->id }}"
                                    data-exam-answer-id="{{ $studentAnswer->id ?? '' }}"
                                    data-max-score="{{ $question->score ?? 2 }}">
                                    Nhập điểm
                                </button> --}}
                            </div>

                            <div class="essay-question-content">
                                <div class="question-text">
                                    {!! $question->content !!}
                                </div>

                                <div class="student-answer-section">
                                    <h6>Bài làm của thí sinh:</h6>
                                    <div class="student-answer">
                                        @if ($studentAnswer && $studentAnswer->answer_text)
                                            {!! nl2br(e($studentAnswer->answer_text)) !!}
                                        @else
                                            <em style="color: #666;">Thí sinh không nhập thông
                                                tin.</em>
                                        @endif
                                    </div>

                                    @if ($studentAnswer && $studentAnswer->answer_image)
                                        <div class="student-image">
                                            <h6>Hình ảnh đính kèm:</h6>
                                            <img src="{{ asset('storage/' . $studentAnswer->answer_image) }}"
                                                alt="Bài làm của thí sinh"
                                                style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;">
                                        </div>
                                    @endif
                                </div>

                                <div class="grading-section">
                                    <div class="grade-info">
                                        @if ($studentAnswer && $studentAnswer->score !== null)
                                            <div class="current-grade">
                                                <strong>Điểm hiện tại:
                                                    {{ rtrim(rtrim(number_format($studentAnswer->score, 2), '0'), '.') }}/{{ $question->score ?? 2 }}</strong>
                                            </div>
                                        @endif
                                        {{-- @if ($studentAnswer && $studentAnswer->teacher_comment)
                                            <div class="teacher-comment">
                                                <strong>Nhận xét:</strong>
                                                {{ $studentAnswer->teacher_comment }}
                                            </div>
                                        @endif --}}
                                    </div>

                                    <div class="comment-section">
                                        <label for="comment-{{ $question->id }}">Nhận xét giáo viên
                                            {{ $question->question_no }}:</label>
                                        <textarea disabled id="comment-{{ $question->id }}" class="form-control" rows="3" placeholder="Nhập nhận xét">{{ $studentAnswer->teacher_comment ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
</div>
<script>
    // Sử dụng JavaScript thuần túy thay vì jQuery
    function initResultPage() {
        // Toggle explanation
        document.addEventListener('click', function(e) {
            if (e.target.closest('.explanation-toggle')) {
                var toggle = e.target.closest('.explanation-toggle');
                var explanationContent = toggle.nextElementSibling;
                var icon = toggle.querySelector('i');

                if (explanationContent.style.display === 'none' || explanationContent.style.display === '') {
                    explanationContent.style.display = 'block';
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                } else {
                    explanationContent.style.display = 'none';
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                }
            }
        });

        // Tab switching
        document.addEventListener('click', function(e) {
            if (e.target.closest('.tab')) {
                var clickedTab = e.target.closest('.tab');
                var tabType = clickedTab.getAttribute('data-tab');
                
                // Remove active class from all tabs
                document.querySelectorAll('.tab').forEach(function(tab) {
                    tab.classList.remove('active');
                });
                
                // Add active class to clicked tab
                clickedTab.classList.add('active');
                
                // Remove active class from all tab contents
                document.querySelectorAll('.tab-content').forEach(function(content) {
                    content.classList.remove('active');
                });
                
                // Add active class to corresponding content
                if (tabType === 'essay') {
                    document.getElementById('essay-content').classList.add('active');
                } else {
                    document.getElementById('multiple-choice-content').classList.add('active');
                }
            }
        });
    }

    // Chạy ngay khi script được load
    initResultPage();
</script>