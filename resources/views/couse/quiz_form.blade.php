@if (count($baiTapData) === 0)
    <p>Không có bài tập.</p>
@else
    <form id="quizForm">
        @foreach ($baiTapData as $idx => $question)
            <div class="quiz-container">
                <div class="question-title d-flex">
                    Câu hỏi {{ $idx + 1 }}: &nbsp;{!! $question['title'] !!}
                </div>
                @if ($question['type'] == 0)
                    <textarea name="tuluan[{{ $idx }}]" placeholder="Nhập nội dung..." class="ckeditor"></textarea>
                @else
                    @foreach ($question['multiple_choice'] as $choiceIdx => $choice)
                        @php
                            $inputId = "question{$idx}_answer{$choiceIdx}";
                            $choiceName = "answer[{$idx}]";
                        @endphp
                        <div class="answer-option">
                            <input type="radio" id="{{ $inputId }}" name="{{ $choiceName }}"
                                value="{{ $choice['index'] }}">
                            <label class="answer-label" for="{{ $inputId }}">
                                {{ $choice['name'] }}. {{ $choice['title'] }}
                            </label>
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
        <button type="submit" class="submit-btn">Nộp bài</button>
    </form>
@endif
