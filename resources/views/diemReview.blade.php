@extends('layouts.main.master')
@section('title')
    THÀNH TÍNH VÀ CẢM NHẬN CỦA HỌC VIÊN
@endsection
@section('description')
    Những thành tích học tập cức kỳ tốt trong quá trình học tập tại Cánh Én
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
                                        class="icon-angles-right"></span>Nhắn tin cho Cánh Én </a>
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
                    Bảng Vàng Thành Tích<br>Của <span>Cánh Én</span>
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
