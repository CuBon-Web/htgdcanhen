@extends('layouts.main.master')
@section('title')
    {{ $setting->company }}
@endsection
@section('description')
    {{ $setting->webname }}
@endsection
@section('css')
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
                    <div class="col-lg-12 text-center">
                        <h1>{{ $setting->company }}</h1>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Về chúng tôi</li>
                            </ul>
                        </div>
                        <div class="desc mb-3">
                            {{ $setting->webname }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="about-two">
        <div class="about-two__bg-shape"
            style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/about-two-bg-shape.png);"></div>
        <div class="container">
            <div class="row">
                <div class="col-xl-5">
                    <div class="about-two__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                        <div class="about-two__img-box">
                            <div class="about-two__img">
                                <img src="{{ $gioithieu->image }}" alt="">
                            </div>
                            <div class="about-two__shape-1">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/about-two-shape-1.png" alt="">
                            </div>
                            <div class="about-two__shape-2">
                                <img src="{{ env('AWS_R2_URL') }}/frontend//images/about-two-shape-2.png" alt="">
                            </div>
                            <div class="about-two__shape-3 rotate-me">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/about-two-shape-3.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-7" style="margin: auto">
                    <div class="about-two__right">
                        <div class="section-title-two text-left sec-title-animation animation-style2">
                            <h2 class="section-title-two__title title-animation">Chúng tôi <span>là ai?</span> </h2>
                        </div>
                        {!! $gioithieu->content !!}

                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="team-page">
        <div class="container">
            <div class="section-title-two text-left sec-title-animation animation-style2">
                <div class="section-title-two__tagline-box">
                    <div class="section-title-two__tagline-shape">
                        <img src="https://laravel-fistudy.unicktheme.com/assets/images/shapes/section-title-two-shape-2.png"
                            alt="">
                    </div>
                    <span class="section-title-two__tagline">Our Team</span>
                </div>
                <h2 class="section-title-two__title title-animation">Đội ngũ giáo viên tại <span>Cánh Én</span>
                </h2>
            </div>
            <div class="row">
                @foreach ($founder as $item)
                    <!-- Team One Single Start -->
                    <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="100ms">
                        @php
                        $mota = json_decode($item->description);
                    @endphp
                    <!--Team Two Single Start-->
                    <div class="item">
                        <div class="team-one__single">
                            <div class="team-one__img-box">
                                <a href="{{ route('detailTeacher', ['slug' => $item->slug]) }}">
                                    <div class="team-one__img">
                                        <img src="{{ $item->images }}" alt="Team Member 1">
                                    </div>
                                    <div class="team-one__content">
                                        <div class="team-one__single-bg-shape"
                                            style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/team-one-single-bg-shape.png);">
                                        </div>
                                        <div class="team-one__content-shape-1">
                                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/team-one-content-shape-1.png"
                                                alt="">
                                        </div>
                                        <div class="team-one__content-shape-2">
                                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/team-one-content-shape-2.png"
                                                alt="">
                                        </div>
                                        <h3 class="team-one__title"><a
                                                href="{{ route('detailTeacher', ['slug' => $item->slug]) }}">{{ $item->name }}</a>
                                        </h3>
                                        <ul class="listiteme">
                                            @foreach ($mota as $key => $item)
                                                @if ($key < 3)
                                                    <li>
                                                        <span>
                                                            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 512 512"><!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                <path fill="currentColor"
                                                                    d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z">
                                                                </path>
                                                            </svg>
                                                        </span>
                                                        <span>{{ $item->title }}</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </a>

                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- Team One Single End -->
                @endforeach
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
