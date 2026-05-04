@extends('layouts.main.master')
@section('title')
    Tải khoản cần được kích hoạt
@endsection
@section('description')
    Đăng ký thành công, liên hệ để được kích hoạt tài khoản
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('css')
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
                    <div class="col-lg-8">
                        <h1>Tài khoản cần được kích hoạt</h1>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Yêu cầu kích hoạt</li>
                            </ul>
                        </div>
                        <div class="desc mb-3">
                            Tài khoản của bạn đã được đăng ký, vui lòng liên hệ với chúng tôi để được kích và sử dụng các
                            chức năng trên web
                        </div>
                        <div class="banner-one__thm-and-other-btn-box">
                            <div class="banner-one__btn-box">
                                <a href="{{ $setting->facebook }}"  class="thm-btn"><span
                                        class="icon-angles-right"></span>Nhắn tin cho fanpage của Edu Alpha</a>
                            </div>
                            <div class="banner-one__other-btn-box">
                                <a href="tel:{{ $setting->phone1 }}" class="banner-one__other-btn-1"><span
                                        class="icon-phone"></span>Gọi trực tiếp</a>
                            </div>
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
@endsection
