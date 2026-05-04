@extends('crm_course.main.master')
@section('title', 'Cập nhật trang HTML')

@section('content_crm_course')
<main class="main-content-wrap">
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        <h3>Cập nhật trang HTML</h3>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Trang HTML</li>
                        <li>Cập nhật</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="form-layouts-area">
        <div class="container-fluid">
            <div class="card-box-style">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('html-pages.update', $page->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="pb-24">
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Tiêu đề *</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-6">
                                <label class="form-label">Slug (tùy chọn)</label>
                                <input type="text" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}" placeholder="Nếu bỏ trống sẽ tự tạo từ tiêu đề">
                                @error('slug')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status', $page->status) == 1 ? 'selected' : '' }}>Hiển thị</option>
                                    <option value="0" {{ old('status', $page->status) == 0 ? 'selected' : '' }}>Ẩn</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="content_html" id="content_html_hidden" value="{{ old('content_html', $page->content_html) }}">

                        <div class="row mt-3">
                            <div class="col-lg-6">
                                <label class="form-label">Tải file HTML</label>
                                <input type="file" id="htmlFile" accept=".html,.htm,text/html" class="form-control">
                                <small class="text-muted">Chọn file .html, nội dung sẽ được lưu và xem trước bên dưới</small>
                                @error('content_html')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col">
                                <button type="button" id="btnPreview" class="btn btn-outline-primary mb-3">
                                    Xem preview (render như file HTML)
                                </button>
                                <div class="card">
                                    <div class="card-body p-0">
                                        <iframe id="previewFrame" style="width: 100%; min-height: 500px; border: none;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cập nhật
                                </button>
                                <a href="{{ route('html-pages.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                                <a href="{{ route('html-pages.public', $page->slug) }}" class="btn btn-link" >Xem trang</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('js_crm_course')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hiddenField = document.getElementById('content_html_hidden');
            const frame = document.getElementById('previewFrame');
            const btnPreview = document.getElementById('btnPreview');
            const fileInput = document.getElementById('htmlFile');

            // Preview: render như mở file HTML
            const renderPreview = () => {
                if (!frame) return;
                const html = hiddenField ? hiddenField.value : '';
                frame.srcdoc = html || '<p><em>Chưa có nội dung HTML</em></p>';
            };

            if (btnPreview) {
                btnPreview.addEventListener('click', renderPreview);
            }

            // Render ngay lần đầu nếu có dữ liệu sẵn có
            renderPreview();

            // Nạp nội dung từ file HTML vào CKEditor
            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files ? e.target.files[0] : null;
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        const html = evt.target?.result || '';
                        if (hiddenField) {
                            hiddenField.value = html;
                        }
                        renderPreview();
                    };
                    reader.readAsText(file, 'UTF-8');
                });
            }
        });
    </script>
@endsection

