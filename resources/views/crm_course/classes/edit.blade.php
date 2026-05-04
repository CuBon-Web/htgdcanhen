@extends('crm_course.main.master')
@section('title', 'Sửa lớp học')

@section('content_crm_course')
<main class="main-content-wrap">
    <!-- Start Page Title Area -->
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        <h3>Sửa lớp học: {{ $class->class_name }}</h3>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Lớp học</li>
                        <li>Sửa lớp học: {{ $class->class_name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title Area -->

    <div class="form-layouts-area">
        <div class="container-fluid">
            <div class="card-box-style">
                <form action="{{ route('classes.update', $class->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                <div class="pb-24">
                    <div class="row">
                        <div class="col">
                            <label for="formGroupExampleInput" class="form-label">Tên lớp</label>
                            <input type="text" name="class_name" class="form-control" id="formGroupExampleInput" placeholder="VD: 10A1, Lớp Toán nâng cao, Lớp Tiếng Anh..." required value="{{ old('class_name', $class->class_name) }}">
                            @error('class_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="formGroupExampleInput" class="form-label">Mã lớp</label>
                            <input type="text" disabled class="form-control" value="{{ $class->class_code }}" readonly style="background-color: #f8f9fa;">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Học sinh dùng mã này để đăng ký vào lớp. Không thể thay đổi.
                            </small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="formGroupExampleInput" class="form-label">Năm học</label>
                            <input type="number" name="school_year" class="form-control" id="formGroupExampleInput" placeholder="VD: 2024" value="{{ old('school_year', $class->school_year) }}">
                            @error('school_year')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="formGroupExampleInput" class="form-label">Trạng thái</label>
                            <select name="is_active" class="form-control" id="formGroupExampleInput">
                                <option value="1" {{ old('is_active', $class->is_active) == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="0" {{ old('is_active', $class->is_active) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật lớp học
                            </button>
                        </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

@endsection

