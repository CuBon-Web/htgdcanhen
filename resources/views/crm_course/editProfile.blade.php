@extends('crm_course.main.master')
@section('title')
Chỉnh sửa trang cá nhân
@endsection
@section('description')
Chỉnh sửa trang cá nhân
@endsection
@section('image')
@endsection
@section('css_crm_course')
<style>
    .wrap {
        width: 120px;
        margin: 0 auto;
        text-align: center;
        overflow: hidden;
    }

    label[for=upload] {
        display: inline-block;
        border: 3px solid #06b28c;
        color: #06b28c;
        font-weight: bold;
        /* background:#eee; */
        padding: 8px 4px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
    }

    label[for=upload]:hover {
        background: #ddd
    }

    label[for=upload] input {
        display: none
    }

    .thumb {
        position: relative;
        height: 120px;
        width: 120px;
        overflow: hidden;
        margin: 10px 0;
        cursor: pointer;
        ;
        border-radius: 50%;
    }

    .thumb:before {
        content: "";
        display: block;
        position: absolute;
        width: 96%;
        height: 96%;
        border: 3px dashed #eee;
        z-index: 9;
        top: 1%;
        left: 1%;
        opacity: 0;
        transition: all 0.2s;
        pointer-events: none
    }

    .thumb:hover::before {
        opacity: 0.6
    }

    .thumb img {
        height: 100%;
        width: 100%;
        transition: all 0.4s;
        object-fit: cover;
    }

    .instructor-profile-left {
        background: #f5f5f5;
        border-radius: 5px;
        padding: 22px 9px;
    }

    .instructor-profile-left .inner .thumbnail {
        max-width: 230px;
        max-height: 230px;
        margin: 0 auto 30px;
    }

    .instructor-profile-left .inner .thumbnail img {
        border-radius: 100%;
        width: 100px;
        height: 100px;
    }

    .instructor-profile-left .inner .content .title {
        font-weight: 700;
        font-size: 24px;
        line-height: 36px;
        margin-bottom: 2px;
    }

    .instructor-profile-left .inner .content .subtitle {
        font-weight: 600;
        line-height: 26px;
        color: var(--color-primary);
        display: block;
        margin-bottom: 25px;
    }
</style>
@endsection
@section('js_crm_course')
<script>
    function preview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image;
                img.onload = function() {
                    $('#img').attr({
                        'src': e.target.result,
                        'width': img.width
                    });
                };
                img.src = reader.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#upload").change(function() {
        $("#img").css({
            top: 0,
            left: 0
        });
        preview(this);
        
    });
</script>
@endsection
@section('content_crm_course')
<main class="main-content-wrap">
    <!-- Start Features Area -->
    <div class="content">
        <div class="page-title-area">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-sm-6">
                        <div class="page-title">
                            <h3>Chỉnh sửa trang cá nhân</h3>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6">
                        <ul class="page-title-list">
                            <li>Trang chủ</li>
                            <li>Chỉnh sửa trang cá nhân</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="edit-profile-content card-box-style">
                            
                            <form action="{{route('postchinhSuaTrangCaNhan')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="wrap">
                                    <div class="thumb"><img id="img"
                                            src="{{ $profile->avatar ? url('uploads/images/' . $profile->avatar) : url('frontend/images/user_icon.png') }}" />
                                    </div>
                                    <label for="upload">Đổi Avatar
                                        <input type='file' id="upload" name="avatar">
                                    </label>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Họ Tên</label>
                                            <input type="text" class="form-control"  name="name"
                                            value="{{ old('name') ? old('name') : $profile->name }}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Số điện thoại</label>
                                            <input type="text" class="form-control" name="phone"
                                            value="{{ old('phone') ? old('phone') : $profile->phone }}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email"
                                            value="{{ old('email') ? old('email') : $profile->email }}">
                                        </div>
                                    </div>
                                    @if($profile->type == 0)
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Mã lớp</label>
                                            <input type="text" class="form-control" name="class_code" 
                                                value="{{ old('class_code', implode(', ', $profile->class_codes ?? [])) }}"
                                                placeholder="Nhập mã lớp (VD: ABC123, XYZ456)"
                                                style="text-transform: uppercase;">
                                            @error('class_code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Nhập mã lớp do giáo viên cung cấp. Có thể nhập nhiều mã lớp, phân cách bằng dấu phẩy hoặc khoảng trắng. Để trống nếu không tham gia lớp nào.
                                                @if(!empty($profile->class_codes))
                                                    <br><strong>Các lớp hiện tại:</strong> 
                                                    @foreach($profile->schoolClasses as $class)
                                                        <span class="badge bg-primary">{{ $class->class_name }}</span>
                                                    @endforeach
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="save-update">
                                        <button class="btn btn-primary me-2" type="submit">Cập nhật</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="edit-profile-content card-box-style">
                            <h3>Đổi mật khẩu</h3>
                            <form action="{{route('postChangePassword')}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Mật khẩu cũ</label>
                                            <input type="password" class="form-control" value="{{old('password')}}" name="password" required>
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Mật khẩu mới</label>
                                            <input type="password" class="form-control" value="{{old('new_password')}}" name="new_password" required>
                                            @error('new_password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Xác nhận mật khẩu</label>
                                            <input type="password" class="form-control" value="{{old('confirm_password')}}" name="confirm_password" required>
                                            @error('confirm_password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="save-update">
                                        <button class="btn btn-primary me-2" type="submit">Cập nhật</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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