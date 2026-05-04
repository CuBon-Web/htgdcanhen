@extends('layouts.main.master')
@section('title')
    Trang cá nhân
@endsection
@section('description')
    Trang cá nhân
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('js')
    <script>
        function preview(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    console.log(reader.result)
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
            $("#img").draggable({
                containment: 'parent',
                scroll: false
            });
        });
    </script>
@endsection
@section('css')
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
@section('content')
    <section class="page-header">
        <div class="page-header__bg"
            style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/page-header-bg-shape.png);">
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
                    <div class="col-lg-12 text-center">
                        <h1>Chỉnh sửa trang cá nhân</h1>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Chỉnh sửa thông tin tài khoản</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="blog-details">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-3">
                    <div class="instructor-profile-left">
                        <div class="inner">
                            <div class="thumbnail">
                                <img src="{{ $profile->avatar ? url('uploads/images/' . $profile->avatar) : url('frontend/images/user_icon.png') }}"
                                    alt="none-avatar">
                            </div>
                            <div class="content">
                                <h5 class="title">{{ $profile->name }}</h5>
                                <span class="subtitle">Thành Viên</span>
                                <div class="contact-with-info">
                                    <p><i class="icon-mail-line-2"></i> <a href="#">{{ $profile->email }}</a></p>
                                    <p><i class="icon-Headphone"></i> <a href="">{{ $profile->phone }}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-9">
                    <form class="contact-three__form" action="{{ route('postShowProfile') }}" enctype="multipart/form-data"
                        method="post">
                        @csrf
                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="wrap">
                                    <div class="thumb"><img id="img"
                                            src="{{ $profile->avatar ? url('uploads/images/' . $profile->avatar) : url('frontend/images/user_icon.png') }}" />
                                    </div>
                                    <label for="upload">Đổi Avatar
                                        <input type='file' id="upload" name="avatar">
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12">
                                <h4 class="contact-three__input-title">Họ Tên</h4>
                                <div class="contact-three__input-box">
                                    <input type="text" placeholder="Full Name" name="name"
                                        value="{{ old('name') ? old('name') : $profile->name }}" required>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12">
                                <h4 class="contact-three__input-title">Số điện thoại</h4>
                                <div class="contact-three__input-box">
                                    <input type="text" placeholder="Phone" name="phone"
                                        value="{{ old('phone') ? old('phone') : $profile->phone }}" required>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12">
                                <h4 class="contact-three__input-title">Email</h4>
                                <div class="contact-three__input-box">
                                    <input type="email" placeholder="Email" name="email"
                                        value="{{ old('email') ? old('email') : $profile->email }}">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="contact-three__btn-box">
                                    <button type="submit" class="thm-btn-two contact-three__btn"><span>Lưu thông
                                            tin</span><i class="icon-angles-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
@endsection
