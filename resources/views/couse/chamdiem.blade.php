@extends('layouts.main.master')
@section('title')
    Trang cá nhân
@endsection
@section('description')
    Trang cá nhân
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('css')
    <style>
        .quiz-card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgb(0 0 0 / 76%);
            background: #ffffff;
            padding: 15px;
        }

        .question {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 1rem;
            align-items: center;
        }

        .question p {
            margin: 0;
        }

        .option {
            border: 2px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 5px 14px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0.5rem;
            font-size: 14px;
        }

        .option:hover {
            background-color: #f1f3f5;
            border-color: #0d6efd;
        }

        .option.active {
            background-color: #e7f1ff;
            border-color: #0d6efd;
        }

        .result {
            font-weight: 600;
            margin-top: 1rem;
            display: none;
        }

        .result.correct {
            display: block;
            color: #198754;
            /* xanh */
        }

        .result.incorrect {
            display: block;
            color: #dc3545;
            /* đỏ */
        }
    </style>
@endsection
@section('content')
    <section class="become-a-teacher">
        <div class="container">

            <div class="become-a-teacher__bottom">
                <div class="row">

                    <div class="col-xl-12">
                        <div class="become-a-teacher__right">
                            <div class="section-title-two text-left sec-title-animation animation-style1">
                                <div class="section-title-two__tagline-box">
                                    <div class="section-title-two__tagline-shape">
                                        <img src="https://laravel-fistudy.unicktheme.com/assets/images/shapes/section-title-two-shape-1.png"
                                            alt="">
                                    </div>
                                    <span class="section-title-two__tagline">Chấm điểm</span>
                                </div>
                            </div>
                            <form class="contact-three__form" action="{{route('postteacherChamdiem')}}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="text" hidden name="idkey" value="{{$ketqua->id}}">
                                <div class="row">
                                    @foreach ($ketqua->quizDetail as $key => $item)
                                        @php
                                            $arrques = json_decode($item->multiple_choice);
                                            $choices = ['A', 'B', 'C', 'D'];
                                            $answerLetter =
                                                $item->user_answer !== null && isset($choices[$item->user_answer])
                                                    ? $choices[$item->user_answer]
                                                    : '-';
                                            $rightLetter =
                                                $item->right_choice !== null && isset($choices[$item->right_choice])
                                                    ? $choices[$item->right_choice]
                                                    : '-';
                                        @endphp
                                        <div class="col-xl-{{ $item->essay_text ? '12' : '6' }} col-lg-6">
                                            <div class="quiz-card mb-3">
                                                <div class="question d-flex">
                                                    Câu hỏi {{ $key + 1 }} : {!! $item->question_title !!}
                                                </div>

                                                @if ($item->essay_text)
                                                    <i>Đáp án tự luận:</i> {!! $item->essay_text !!}
                                                @else
                                                    <div class="options">
                                                        @foreach ($arrques as $ques)
                                                            <div class="option">{{ $ques->name }}: {{ $ques->title }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <p class="mb-0"><strong>Đáp án của HS:</strong> {{ $answerLetter }}
                                                    </p>
                                                    <p class="mb-0"><strong>Kết quả:</strong> {!! $item->is_correct ? '✅ Đúng' : '❌ Sai' !!}
                                                    </p>
                                                    <p class="mb-0"><strong>Đáp án đúng:</strong> {{ $rightLetter }}</p>
                                                @endif

                                                <h4 class="contact-three__input-title">Nhận xét của giáo viên</h4>
                                                <div class="contact-three__input-box text-message-box">
                                                    <textarea name="feedback[{{ $item->id }}]" placeholder="Viết nhận xét..." >{{ old('feedback_general', $item->teacher_note ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-xl-12 col-lg-6">
                                            <div class="quiz-card mb-3">
                                                <div class="question d-flex">
                                                   Nhận xét tổng quan cho toàn bộ bài làm
                                                </div>
                                                <div class="contact-three__input-box text-message-box">
                                                    <textarea name="feedback_general" placeholder="Viết nhận xét..." required>{{ old('feedback_general', $ketqua->teacher_note ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="contact-three__btn-box">
                                        <button type="submit" class="thm-btn-two contact-three__btn">
                                            <span>Hoàn Thành</span><i class="icon-angles-right"></i>
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
