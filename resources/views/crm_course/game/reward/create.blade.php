@extends('crm_course.main.master')
@section('title')
Thêm quà tặng mới
@endsection
@section('description')
Thêm quà tặng mới
@endsection
@section('css_crm_course')
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .image-preview {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px dashed #ddd;
        display: none;
        margin-top: 10px;
    }
    .image-preview.show {
        display: block;
    }
    .preview-placeholder {
        width: 200px;
        height: 200px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #999;
        font-size: 48px;
    }
</style>
@endsection

@section('content_crm_course')
<main class="main-content-wrap">
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        <h3>Thêm quà tặng mới</h3>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li><a href="{{ route('gamelistAITrieuPhuToanHoc') }}">Game</a></li>
                        <li><a href="{{ route('game.reward.index') }}">Quản lý quà tặng</a></li>
                        <li>Thêm mới</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-us-area">
        <div class="container-fluid">
            <div class="form-container">
                <form action="{{ route('game.reward.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên quà tặng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Ngừng hoạt động</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Hình ảnh</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Định dạng: JPG, PNG, GIF. Tối đa 2MB</small>
                                
                                <div id="image-preview-container" class="mt-3">
                                    <div class="preview-placeholder" id="preview-placeholder">
                                        🎁
                                    </div>
                                    <img id="image-preview" class="image-preview" alt="Preview">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu quà tặng
                            </button>
                            <a href="{{ route('game.reward.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
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
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('preview-placeholder');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.add('show');
                placeholder.style.display = 'none';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.remove('show');
            placeholder.style.display = 'flex';
        }
    }
</script>
@endsection

