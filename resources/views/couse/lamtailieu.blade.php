@extends('layouts.main.master')
@section('title')
Danh sách tài liệu
@endsection
@section('description')
Danh sách tài liệu
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')

<link rel="stylesheet" href="/frontend/css/batdauhoc.css">
@endsection
@section('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.0/classic/ckeditor.js"></script>
    <script>
        document.querySelectorAll('.ckeditor').forEach(function(el) {
                    ClassicEditor.create(el, {
                        toolbar: [ 'heading', '|', 'bold', 'italic', 'underline', 'link', '|', 'bulletedList', 'numberedList', '|', 'undo', 'redo' ]
                    }).catch(error => console.error(error));
                });
    </script>
@endsection
@section('content')

<section class="become-a-teacher" style="padding: 50px 0 120px;">
        <div class="container">
            <div class="become-a-teacher__top">
                <div class="section-title-two text-center sec-title-animation animation-style1">
                    <h2 class="section-title-two__title title-animation">Bắt đầu làm bài
                    </h2>
                </div>
            </div>
            <form  action="{{route('postlamTaiLieu')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="text" name="tailieu_id" hidden value="{{$baitap->id}}">
                    @foreach ($baiTapData as $idx => $question)
                        <div class="quiz-container">
                            <div class="question-title d-flex">
                                Câu hỏi {{ $idx + 1 }}: &nbsp;{!! $question->title !!}
                            </div>
                            @if ($question->type == 0)
                                <textarea name="tuluan[{{ $idx }}]" placeholder="Nhập nội dung..." class="ckeditor"></textarea>
                            @else
                                @foreach ($question->multiple_choice as $choiceIdx => $choice)
                                {{-- {{dd($choice)}} --}}
                                    @php
                                        $inputId = "question{$idx}_answer{$choiceIdx}";
                                        $choiceName = "answer[{$idx}]";
                                    @endphp
                                    <div class="answer-option">
                                        <input type="radio" id="{{ $inputId }}" name="{{ $choiceName }}"
                                            value="{{ $choice->index }}">
                                        <label class="answer-label" for="{{ $inputId }}">
                                            {{ $choice->name }}. {{ $choice->title }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                    <button type="submit" class="submit-btn">Nộp bài</button>
                </form>
        </div>
    </section>
@endsection