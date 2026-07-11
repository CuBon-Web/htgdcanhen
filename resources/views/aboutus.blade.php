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
        <div class="page-header__bg"></div>
        <div class="container">
            <div class="page-header__inner">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>/</span></li>
                                <li>Về chúng tôi</li>
                            </ul>
                        </div>
                        <h1>Về chúng tôi</h1>
                        @if (!empty($setting->webname))
                            <div class="desc">{{ $setting->webname }}</div>
                        @endif
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
        <div class="container">
            <div class="section-title-two section-title-two--with-link sec-title-animation animation-style1">
                <div class="section-title-two__heading">
                    <div class="section-title-two__tagline-box">
                        <span class="section-title-two__tagline">Phụ huynh và học sinh nói gì về cánh én</span>
                    </div>
                    <h2 class="section-title-two__title">Những phản hồi chân thực</h2>
                </div>
                <a href="{{ route('diemReview') }}" class="section-view-all">
                    Xem tất cả
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
            <div class="testimonial-two__inner">
                <div class="testimonial-two__slider">
                    <div class="testimonial-two__main-content">
                        <div class="testimonial-two__carousel owl-carousel owl-theme">
                            @foreach ($reviewcus as $item)
                                <div class="testimonial-two__item">
                                    <article class="testimonial-two__card">
                                        <div class="testimonial-two__quote-icon">
                                            <i class="fas fa-quote-left" aria-hidden="true"></i>
                                        </div>
                                        <p class="testimonial-two__card-text">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($item->content ?? ''), 160) }}
                                        </p>
                                        <div class="testimonial-two__card-footer">
                                            <div class="testimonial-two__person">
                                                <div class="testimonial-two__avatar">
                                                    <img src="{{ $item->avatar }}" alt="{{ $item->name ?? 'Học viên' }}">
                                                </div>
                                                <div class="testimonial-two__meta">
                                                    <h4 class="testimonial-two__name">{{ $item->name ?? 'Học viên Cánh Én' }}</h4>
                                                    <p class="testimonial-two__role">{{ $item->class_name ?? 'Phụ huynh học sinh lớp 5' }}</p>
                                                </div>
                                            </div>
                                            <div class="testimonial-two__stars" aria-label="{{ (int) ($item->star ?? 5) }} sao">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                @endfor
                                            </div>
                                        </div>
                                    </article>
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
                                            <img src="{{ $item->avatar }}" alt="{{ $item->name ?? 'Học viên' }}">
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
