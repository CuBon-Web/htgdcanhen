@extends('crm_course.main.master')
@section('title')
    Tự tạo đề thi
@endsection
@section('description')
    Tự tạo đề thi
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
            <div id="preview-file-upload" >
                <preview-file-upload
                    :initial-questions='@json($questions)'
                    :raw-content='@json($rawContent)'
                    :grade='@json($grade)'
                    :type='@json($type ?? "dethi")'
                    :current-folder-id='@json($folderId)'
                ></preview-file-upload>
            </div>
        </div>
        @include('crm_course.main.footer')
    </main>
@endsection
