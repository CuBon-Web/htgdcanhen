<div class="team-details__have-question mb-4">
                                <div class="team-details__have-question-icon">
                                    <span class="fas fa-check"></span>
                                </div>
                                <div class="team-details__have-question-content">
                                    <p>Bạn đã hoàn thành bài tập này.</p>
                                    <h5>Điểm trắc nghiệm: {{ $score }}</h5>
                                </div>
                            </div>
<ul class="course-details__overview-points list-unstyled">
@foreach ($details as $key => $detail)

    <li>
        
            {{-- {!! $detail->is_correct ? '✅ Đúng' : '❌ Sai' !!} --}}
            @if ($detail->is_correct)
                 <div class="course-details__overview-points-icon" style="background-color: green;">
                    <span class="fas fa-check" ></span>
                </div>
            @else 
            <div class="course-details__overview-points-icon">
                <span class="fas fa-times"></span>
                </div>
            @endif
            
        
        <div class="course-details__overview-points-content">
            <h5>Câu hỏi {{$key+1}} : {!! $detail->question_title !!}</h5>
            
        @if ($detail->essay_text)
            <p><strong>Bài tự luận:</strong></p>
             {!! $detail->essay_text !!}
        @else
        @php
            $choices = ['A', 'B', 'C', 'D'];
            $answerLetter = $detail->user_answer !== null ? $choices[$detail->user_answer] : '-';
            $rightLetter = $detail->right_choice !== null ? $choices[$detail->right_choice] : '-';
        @endphp
            <p><strong>Đáp án của bạn:</strong> {{ $answerLetter }}</p>
            <p><strong>Kết quả:</strong> {!! $detail->is_correct ? '✅ Đúng' : '❌ Sai' !!}</p>
            <p><strong>Đáp án đúng:</strong> {{ $rightLetter }}</p>
        @endif
        @if ($detail->teacher_note)
            <div class="comment-one__single">
            <div class="comment-one__content">
                <div class="comment-one__name-box">
                    <h4>{{$teacher != null ? $teacher->name : 'Quản trị viên'}} <span>Nhận xét của giáo viên</span>
                    </h4>
                </div>
                <p>{{$detail->teacher_note}}</p>
            </div>
        </div>
        @endif
        
        </div>
        
    </li>
@endforeach
</ul>
@if ($nhanxettonquan)
<div class="comment-one__single" style="    border: 2px solid #0aac39;">
           
            <div class="comment-one__content">
                <div class="comment-one__name-box">
                    <h4>{{$teacher != null ? $teacher->name : 'Quản trị viên'}} <span>Nhận xét tổng quan phần làm bài</span>
                    </h4>
                </div>
                <p>{{$nhanxettonquan}}</p>
            </div>
        </div>
        @endif