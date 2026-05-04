@extends('layouts.main.master')
@section('title')
    THÀNH TÍNH VÀ CẢM NHẬN CỦA HỌC VIÊN
@endsection
@section('description')
    Những thành tích học tập cức kỳ tốt trong quá trình học tập tại Edu Alpha
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('css')
    <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/faq.css">
@endsection
@section('js')
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
                    <div class="col-lg-12">
                        <h2>THÀNH TÍCH VÀ CẢM NHẬN CỦA HỌC VIÊN </h2>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>THÀNH TÍCH VÀ CẢM NHẬN CỦA HỌC VIÊN </li>
                            </ul>
                        </div>
                        <div class="banner-one__thm-and-other-btn-box d-flex align-items-center" style="gap: 10px;">
                            <div class="banner-one__btn-box">
                                <a href="{{ $setting->facebook }}"  class="thm-btn"><span
                                        class="icon-angles-right"></span>Nhắn tin cho Edu Alpha </a>
                            </div>
                            <div class="banner-one__other-btn-box">
                                <a href="javascript:;" onclick="javascript:jump('section-contact-sales')"
                                    class="banner-one__other-btn-1"><span class="icon-thumbs-up"></span>Yêu cầu
                                    tư vấn</a>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </section>
    <section class="blog-two">
        <div class="container">
            <div class="section-title-two text-center sec-title-animation animation-style1">
                <h2 class="section-title-two__title title-animation">
                    Bảng Vàng Thành Tích<br>Của <span>Edu Alpha</span>
                </h2>
            </div>
            <div class="row">
                <!-- Blog Two Single Start -->
                @foreach ($thanhtich as $item)
                    <div class="col-xl-4 col-lg-4 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="event-one__single">
                            <a href="{{ $item->image }}" class="img-popup">
                                <div class="event-one__img">

                                    <img src="{{ $item->avatar }}" alt="">
                                    <div class="event-one__date">
                                        <p>{{ $item->status }}</p>
                                    </div>

                                </div>
                                <div class="event-one__content">
                                    <h4 class="event-one__title">
                                        {{ $item->name }}
                                    </h4>
                                    <div class="event-one__location">
                                        <div class="event-one__location-icon">
                                            <span class="icon-location"></span>
                                        </div>
                                        <p class="event-one__loation-text">{{ $item->name }}</p>
                                    </div>
                                </div>
                            </a>

                        </div>
                    </div>
                @endforeach

                <!-- Blog Two Single End -->
            </div>
        </div>
    </section>
    <section class="blog-two">
        <div class="container">
            <div class="section-title-two text-center sec-title-animation animation-style1">
                <h2 class="section-title-two__title title-animation">
                    Feedback trên <br> <span>Social Network</span>
                </h2>
            </div>
            <div class="row">
                <!-- Blog Two Single Start -->
                @foreach ($socical as $item)
                    <div class="col-xl-4 col-lg-4 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="blog-list__single">
                            <a href="{{ $item->link }}" target="_blnk">


                                <div class="blog-list__img-box">
                                    <div class="blog-list__img">
                                        <img src="{{ $item->image }}" alt="">
                                    </div>
                                    <div class="blog-list__date">
                                        <p>Facebook</p>
                                    </div>
                                </div>
                            </a>
                            <div class="blog-list__content">
                                <div class="blog-list__client-and-meta mb-3">
                                    <div class="blog-list__client-box">
                                        <div class="blog-list__client-img">
                                            <img src="{{ $item->avatar }}" alt="">
                                        </div>
                                        <div class="blog-list__client-content">
                                            <p>{{ $item->date }}</p>
                                            <h4>{{ $item->name }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <p class="blog-list__text">
                                    {{ $item->status }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Blog Two Single End -->
            </div>
        </div>
    </section>
    <section class="testimonial-two">
        <div class="testimonial-two__shape-1">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/testimonial-two-shape-1.png" alt="">
            <div class="testimonial-two__shape-icon-1">
                <img src="{{ env('AWS_R2_URL') }}/frontend/images/testimonial-two-shape-icon-1.png" alt=""
                    class="zoom-fade">
            </div>
            <div class="testimonial-two__shape-img-2">
                <img src="{{ env('AWS_R2_URL') }}/frontend/images/testimonial-two-shape-img-2.jpg" alt="">
            </div>
            <div class="testimonial-two__shape-img-3 img-bounce">
                <img src="{{ env('AWS_R2_URL') }}/frontend/images/testimonial-two-shape-img-3.jpg" alt="">
            </div>
        </div>
        <div class="testimonial-two__shape-2">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/testimonial-two-shape-2.png" alt="">
            <div class="testimonial-two__shape-icon-2 float-bob-y">
                <img src="{{ env('AWS_R2_URL') }}/frontend/images/why-choose-two-icon-1.png" alt="">
            </div>
            <div class="testimonial-two__shape-img-1 zoom-fade">
                <img src="{{ env('AWS_R2_URL') }}/frontend/images/testimonial-two-shape-img-1.jpg" alt="">
            </div>
        </div>
        <div class="testimonial-two__shape-3 float-bob-x">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/testimonial-two-shape-3.png" alt="">
        </div>
        <div class="testimonial-two__shape-4"></div>
        <div class="testimonial-two__shape-5"></div>
        <div class="container">
            <div class="section-title-two text-center sec-title-animation animation-style1">
                <h2 class="section-title-two__title title-animation">
                    Tình yêu của học viên với <br> <span>Edu Alpha</span>
                </h2>
            </div>
            <div class="testimonial-two__inner">
                <div class="testimonial-two__slider">
                    <div class="testimonial-two__main-content">
                        <div class="testimonial-two__carousel owl-carousel owl-theme">
                            <!-- Testimonial Item 1 -->
                            @foreach ($reviewcus as $item)
                                <div class="video-one__inner">
                                    <div class="video-one__shape-1"></div>
                                    <div class="video-one__shape-2 rotate-me"></div>
                                    <div class="video-one__img-box">
                                        <img src="{{ $item->avatar }}" alt="">
                                        <div class="video-one__video-link">
                                            <a href="{{ $item->content }}" class="video-popup">
                                                <div class="video-one__video-icon">
                                                    <span class="fa fa-play"></span>
                                                    <i class="ripple"></i>
                                                </div>
                                            </a>
                                        </div>
                                        <ul class="list-unstyled video-one__video-control">
                                            <li><a href="#"><span class="icon-screen-share"></span></a></li>
                                            <li><a href="#"><span class="icon-microphone"></span></a></li>
                                            <li><a href="#"><span class="icon-phone"></span></a></li>
                                            <li><a href="#"><span class="icon-video-slash"></span></a></li>
                                            <li><a href="#"><span class="icon-share-from"></span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="testimonial-two__thumb-outer-box">
                        <div class="testimonial-two__thumb-carousel owl-carousel owl-theme">
                            @foreach ($reviewcus as $item)
                                <div class="testimonial-two__thumb-item">
                                    <div class="testimonial-two__img-holder-box">
                                        <div class="testimonial-two__img-holder">
                                            <img src="{{ $item->avatar }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
