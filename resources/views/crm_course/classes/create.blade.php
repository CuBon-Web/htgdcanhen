@extends('crm_course.main.master')
@section('title', 'Thêm lớp học mới')

@section('content_crm_course')
<main class="main-content-wrap">
    <!-- Start Page Title Area -->
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        <h3>Thêm lớp học mới</h3>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Lớp học</li>
                        <li>Thêm lớp học mới</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title Area -->

    <div class="form-layouts-area">
        <div class="container-fluid">
            <div class="card-box-style">

                <form action="{{ route('classes.store') }}" method="POST">
                    @csrf
                <div class="pb-24">
                    <div class="row">
                        <div class="col">
                            <label for="formGroupExampleInput" class="form-label">Tên lớp</label>
                            <input type="text" name="class_name" class="form-control" id="formGroupExampleInput" placeholder="VD: 10A1, Lớp Toán nâng cao, Lớp Tiếng Anh..." required value="{{ old('class_name') }}">
                            @error('class_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Mã lớp</strong> sẽ được tự động tạo ngẫu nhiên (6 ký tự). 
                                Học sinh sẽ dùng mã này để đăng ký vào lớp.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="formGroupExampleInput" class="form-label">Năm học</label>
                            <input type="number" name="school_year" class="form-control" id="formGroupExampleInput" placeholder="VD: 2024" value="{{ old('school_year', date('Y')) }}">
                            @error('school_year')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="formGroupExampleInput" class="form-label">Trạng thái</label>
                            <select name="is_active" class="form-control" id="formGroupExampleInput">
                                <option value="1" >Đang hoạt động</option>
                                <option value="0" >Không hoạt động</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Thêm lớp học
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

