@extends('layouts.main.master')
@section('title')
Đăng nhập Toán Edu Alpha
@endsection
@section('description')
Đăng nhập Toán Edu Alpha
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')
<style>
    input[type="password"]{
        padding-right: 40px !important;
        position: relative;
    }
    .eye-icon{
        position: absolute;
        right: 20px;
        top: 22px;
    }
</style>
@endsection
@section('content')
<section class="page-header">
    <div class="page-header__bg" style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/page-header-bg-shape.png);">
    </div>
    <div class="page-header__shape-4">
        <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-4.png" alt="">
    </div>
    <div class="page-header__shape-5">
        <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-5.png" alt="">
    </div>
    <div class="container">
        <div class="page-header__inner">
            <div class="row">
                <div class="col-lg-8">
                    <h1>Đăng nhập</h1>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="{{route('home')}}">Trang chủ</a></li>
                            <li><span>//</span></li>
                            <li>Đăng nhập</li>
                        </ul>
                    </div>
                    <div class="desc mb-3">
                        Đăng nhập ngay để bắt đầu trải nghiệm học và luyện thi hiệu quả cùng hàng trăm ngàn học viên mỗi ngày
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="page-header__img">
                        <img src="/uploads/images/danh-muc-797808265.png" alt="">
                        <div class="page-header__shape-1">
                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-1.png" alt="">
                        </div>
                        <div class="page-header__shape-2">
                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-book-icon.png" alt="">
                        </div>
                        <div class="page-header__shape-3">
                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-3.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="login-one">
    <div class="container">
        <div class="login-one__form">
            <form action="{{route('postlogin')}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-xl-12">
                        <div class="form-group">
                            <div class="input-box">
                                <label for="phone" class="form-label">Số điện thoại hoặc email:</label>
                                <input type="text" name="phone" placeholder="Số điện thoại hoặc email" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="form-group">
                            <div class="input-box">
                                <label for="password" class="form-label">Mật khẩu:</label>
                                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                                <span class="eye-icon" onclick="togglePassword('password')">
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="form-group">
                            <button class="thm-btn" type="submit">Đăng nhập</button>
                        </div>
                    </div>
                    <div class="remember-forget">
                        <div class="checked-box1">
                            <input type="checkbox" name="saveMyInfo" id="saveinfo" checked="">
                            <label for="saveinfo">
                                <span></span>
                                Lưu tài khoản
                            </label>
                        </div>
                        <div>
                            <a href="{{route('forgotPassword')}}">Quên mật khẩu?</a>
                        </div>
                    </div>
                    <div class="create-account text-center">
                        <p>Bạn chưa có tài khoản? <a href="{{route('register')}}">Đăng ký ngay</a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
@section('js')
<script>
    function togglePassword(id){
        var password = document.getElementById(id);
        if(password.type == 'password'){
            password.type = 'text';
            password.nextElementSibling.firstElementChild.classList.remove('fa-eye-slash');
            password.nextElementSibling.firstElementChild.classList.add('fa-eye');
        }else{
            password.type = 'password';
            password.nextElementSibling.firstElementChild.classList.remove('fa-eye');
            password.nextElementSibling.firstElementChild.classList.add('fa-eye-slash');
        }
    }
</script>
@endsection
