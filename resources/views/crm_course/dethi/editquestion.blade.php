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

@endsection
@section('js_crm_course')
<script src="{{ asset('js/child-component.js') }}"></script>

<!-- MathJax CDN for LaTeX rendering -->
<script>
  window.MathJax = {
    tex: {
      inlineMath: [['$', '$'], ['\\(', '\\)']],
      displayMath: [['$$', '$$'], ['\\[', '\\]']]
    }
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
@endsection
@section('content_crm_course')
    <main class="main-content-wrap">
        <div class="dethi-preview-area">
            <div id="edit-question" >
                <edit-question :raw-content='@json($rawContent)' :initial-question='@json($question)' :exam-id='@json($exam_id)'></edit-question>
            </div>
        </div>
        @include('crm_course.main.footer')
    </main>
@endsection
