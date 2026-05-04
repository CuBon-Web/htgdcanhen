@extends('crm_course.main.master')
@section('title')
{{$setting->company}}
@endsection
@section('description')
{{$setting->webname}}
@endsection
@section('image')
@endsection
@section('css_crm_course')
<style>

.upload-box {
    background: #fff;
    border: 1px dashed #6c63ff;
    border-radius: 16px;
    padding: 40px 32px 24px 32px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.04);
    width: 100%;
    transition: box-shadow 0.2s;
    width: 100%;
}
.upload-box:hover {
    box-shadow: 0 4px 32px rgba(108,99,255,0.10);
    border-color: #4f46e5;
}
.upload-icon svg {
    display: block;
    margin: 0 auto;
}
.upload-text {
    font-size: 20px;
    color: #222;
}
.upload-desc {
    color: #555;
    font-size: 15px;
}
.upload-links a {
    color: #6c63ff;
    text-decoration: underline;
    font-size: 14px;
    margin: 0 2px;
}

@keyframes spinner-border {
  to { transform: rotate(360deg); }
}
.spinner-border {
  display: inline-block;
  width: 2.5rem;
  height: 2.5rem;
  vertical-align: text-bottom;
  border: .25em solid #e3e3e3;
  border-right-color: #6c63ff;
  border-radius: 50%;
  animation: spinner-border .75s linear infinite;
}

</style>
@endsection
@section('js_crm_course')
<script>
const dropArea = document.getElementById('drop-area');
const fileElem = document.getElementById('fileElem');
const uploadLabel = document.getElementById('upload-label');
const fileList = document.getElementById('file-list');
const uploadForm = document.getElementById('uploadForm');

// Click vào icon để mở chọn file
uploadLabel.addEventListener('click', () => fileElem.click());

// Kéo thả file
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
  dropArea.addEventListener(eventName, preventDefaults, false)
});
function preventDefaults (e) {
  e.preventDefault();
  e.stopPropagation();
}
['dragenter', 'dragover'].forEach(eventName => {
  dropArea.addEventListener(eventName, () => dropArea.classList.add('highlight'), false)
});
['dragleave', 'drop'].forEach(eventName => {
  dropArea.addEventListener(eventName, () => dropArea.classList.remove('highlight'), false)
});
dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
  let dt = e.dataTransfer;
  let files = dt.files;
  fileElem.files = files;
  handleFiles(files);
}

function handleFiles(files) {
  fileList.innerHTML = '';
  [...files].forEach(file => {
    const div = document.createElement('div');
    div.textContent = file.name + ' (' + Math.round(file.size/1024) + ' KB)';
    fileList.appendChild(div);
  });
  // Hiện spinner khi submit
  if(files.length > 0) {
    document.getElementById('upload-spinner').style.display = 'block';
    setTimeout(() => uploadForm.submit(), 500); // Cho phép hiển thị tên file trước khi submit
  }
}
</script>
@endsection
@section('content_crm_course')
<main class="main-content-wrap">
    <div class="content">
        
    <!-- Start Features Area -->
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        @if($type == 'dethi')
                        <h3>Nhập đề thi từ file</h3>
                        @elseif($type == 'game')
                        <h3>Nhập game từ file</h3>
                        @else
                        <h3>Nhập bài tập từ file</h3>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Trang chủ</li>
                        @if($type == 'dethi')
                        <li>Nhập đề thi từ file</li>
                        @elseif($type == 'game')
                        <li>Nhập game từ file</li>
                        @else
                        <li>Nhập bài tập từ file</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="form-layouts-area">
        <div class="container-fluid">
            <div class="upload-box text-center" id="drop-area">
                <form id="uploadForm" action="{{ route('PostuploadFile') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="{{$type}}">
                    <input type="hidden" name="folder_id" value="{{ $folderId ?? '' }}">
                    @csrf
                    <input type="file" id="fileElem" name="file" style="display:none" onchange="handleFiles(this.files)">
                    <div class="upload-icon mb-3" id="upload-label" style="cursor:pointer;">
                        <svg width="64" height="64" fill="none" viewBox="0 0 24 24"><path d="M12 16V4m0 0l-4 4m4-4l4 4M4 16.5A4.5 4.5 0 018.5 12H9m6 0h.5A4.5 4.5 0 0120 16.5v0A4.5 4.5 0 0115.5 21h-7A4.5 4.5 0 014 16.5v0z" stroke="#6c63ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="upload-text mb-2">
                        <strong>Chọn File hoặc kéo thả File vào đây</strong>
                    </div>
                    <div class="upload-desc mb-2">
                       Hệ thống sẽ tự động phân tích và khởi tạo các câu hỏi từ file được upload, bạn cần xem lại và chỉnh sửa trước khi khởi tạo
                    </div>
                    <div class="upload-links mb-2"> 
                        <a  href="/frontend/demau/de_thi_mau.docx">Tải Xuống đề mẫu Docx</a>
                    </div>
                    <div id="file-list" style="margin-top:16px;"></div>
                    <div id="upload-spinner" style="display:none; margin-top:16px;">
                        <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <div style="margin-top:8px;color:#6c63ff;">Đang tải lên, vui lòng chờ...</div>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>
    <!-- End Features Area -->
    <!-- Start Footer Area -->
    @include('crm_course.main.footer')
    <!-- End Footer Area -->
 </main>
@endsection