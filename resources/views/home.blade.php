@extends('layouts.main.master')
@section('title')
    {{ $setting->company }}
@endsection
@section('description')
    {{ $setting->webname }}
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('css')
<link rel="stylesheet" href="/frontend/css/banner.css">
@endsection
@section('js')
@endsection
@section('content')
    <!-- Banner Two Start -->
    <section class="banner-two">
        <div class="banner-two__shape-bg"
            style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/banner-two-shape-bg.png);"></div>
        <div class="banner-two__shape-box float-bob-y">
            <div class="banner-two__shape-1"></div>
        </div>
        <div class="banner-two__shape-2 img-bounce">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-shape-2.png" alt="">
        </div>
        <div class="banner-two__shape-3">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-shape-3.png" alt="">
        </div>
        <div class="banner-two__shape-4 shapemover">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-shape-4.png" alt="">
        </div>
        <div class="banner-two__shape-5 float-bob-y">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-shape-5.png" alt="">
        </div>
        <div class="banner-two__shape-6 rotate-me">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-shape-6.png" alt="">
        </div>
        <div class="banner-two__shape-7 float-bob-y"></div>
        <div class="banner-two__shape-8 float-bob-x"></div>
        <div class="banner-two__edu-icon shapemover">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-edu-icon.png" alt="">
        </div>
        <div class="banner-two__book-icon img-bounce">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-book-icon.png" alt="">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="banner-two__left">
                        <p class="banner-two__sub-title"># Về chúng tôi</p>
                        <h2 class="banner-two__title">{!! isset($banner[0]->title) ? $banner[0]->title : '' !!}
                        </h2>
                        <div class="banner-two__text">{!! isset($banner[0]->description) ? $banner[0]->description : '' !!}
                        </div>
                        <div class="banner-two__btn-and-success-student-box">
                            <div class="banner-two__btn-box">
                                <a href="{{ isset($banner[0]->link) ? $banner[0]->link : '' }}" class="thm-btn-two">
                                    <span>Thông tin chi tiết</span>
                                    <i class="icon-angles-right"></i>
                                </a>
                            </div>
                            <div class="banner-two__success-student">
                                <ul class="list-unstyled banner-two__success-student-list">
                                    <li>
                                        <div class="banner-two__success-student-img">
                                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-success-student-1-1.jpg"
                                                alt="">
                                        </div>
                                    </li>
                                    <li>
                                        <div class="banner-two__success-student-img">
                                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-success-student-1-2.jpg"
                                                alt="">
                                        </div>
                                    </li>
                                    <li>
                                        <div class="banner-two__success-student-img">
                                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-success-student-1-3.jpg"
                                                alt="">
                                        </div>
                                    </li>
                                </ul>
                                <div class="banner-two__success-student-content">
                                    <div class="banner-two__success-student-count-box">
                                        <p class="odometer" data-count="2000">00</p>
                                        <span>+</span>
                                    </div>
                                    <p class="banner-two__success-student-text">Học sinh - Giáo viên</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="banner-two__right">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="banner-two__content-one">
                                    <div class="banner-two__content-one-img">
                                        <img src="{{ isset($banner[0]->image) ? $banner[0]->image : '' }}" alt="">
                                    </div>
                                    <div class="banner-two__course-discount d-lg-block d-none">
                                        <div class="banner-two__course-discount-img">
                                            <img src="{{ isset($banner[1]->image) ? $banner[1]->image : '' }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 ">
                                <div class="banner-two__content-two">
                                    <div class="banner-two__content-two-img d-lg-block d-none">
                                        <img src="{{ isset($banner[2]->image) ? $banner[2]->image : '' }}" alt="">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner Two End -->
    <section class="courses-three">
        <div class="container">
            <div class="section-title-two text-center sec-title-animation animation-style1">
                <div class="section-title-two__tagline-box">
                    <div class="section-title-two__tagline-shape">
                        <img src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-two-shape-1.png" alt="">
                    </div>
                    <span class="section-title-two__tagline">Best Selling</span>
                </div>
                <h2 class="section-title-two__title">Khóa học nổi bật
                </h2>
            </div>
            <div class="row">
                @foreach ($khoahoc as $key => $item)
                    <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="courses-two__single">
                        <div class="courses-two__img-box">
                            <div class="courses-two__img">
                                <a href="{{route('couseDetail',['slug'=>$item->slug])}}">
                                    <img src="{{$item->images}}"
                                        alt="{{$item->images}}">
                                </a>
                            </div>
                            {{-- <div class="courses-two__heart">
                                <a href=""><span
                                        class="icon-heart"></span></a>
                            </div> --}}
                        </div>
                        <div class="courses-two__content">
                            <div class="courses-two__doller-and-review">
                                <div class="courses-two__doller">
                                    @if ($item->price > 0)
                                        <p>{{number_format($item->price)}}đ <del>{{number_format($item->discount)}}đ</del></p> 
                                    @else 
                                        <p>Miễn phí</p> 
                                    @endif
                                    
                                </div>
                            </div>
                            <h3 class="courses-two__title">
                                <a href="{{route('couseDetail',['slug'=>$item->slug])}}">{{$item->name}}</a>
                            </h3>
                            <div class="courses-two__btn-and-client-box">
                                <div class="courses-two__btn-box">
                                    <a href="{{route('couseDetail',['slug'=>$item->slug])}}" class="thm-btn-two">
                                        <span>Xem chi tiết</span>
                                        <i class="icon-angles-right"></i>
                                    </a>
                                </div>
                                <div class="courses-two__client-box">
                                    <div class="courses-two__client-img">
                                        @if ($item->user_id == 0)
                                            <img src="{{url('frontend/images/user_icon.png')}}" alt="">
                                        @else 
                                             <img src="{{$item->customer->avatar ? url('uploads/images/'.$item->customer->avatar) : url('frontend/images/user_icon.png')}}" alt="">
                                        @endif
                                    </div>
                                    <div class="courses-two__client-content">
                                        <h4>{{$item->user_id == 0 ? 'Quản trị viên' : $item->customer->name}}</h4>
                                        <p>Giáo viên</p>
                                    </div>
                                </div>
                            </div>
                            <ul class="courses-two__meta list-unstyled">
                                
                                <li>
                                    <div class="icon">
                                        <span class="icon-book"></span>
                                    </div>
                                    <p>{{$item->ingredient}} Bài học</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-clock"></span>
                                    </div>
                                    <p>{{$item->thickness}} Buổi học</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-book"></span>
                                    </div>
                                    <p>Bài tập online</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
                
            </div>



        </div>
    </section>

    <!--About Two Start -->
    <section class="blog-two">
        <div class="container">
            <div class="section-title-two text-center sec-title-animation animation-style1">
                <div class="section-title-two__tagline-box">
                    <div class="section-title-two__tagline-shape">
                        <img src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-two-shape-1.png" alt="">
                    </div>
                    <span class="section-title-two__tagline">Feedback</span>
                </div>
                <h2 class="section-title-two__title title-animation">
                    Bảng Vàng Thành Tích Tại Cánh Én
                </h2>
            </div>
            <div class="row">
                <!-- Blog Two Single Start -->
                @foreach ($thanhtich as $item)
                    <div class="col-xl-4 col-lg-4 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="event-one__single">
                            <a href="{{ $item->image }}" class="img-popup">
                                <div class="event-one__img">
                                    <img src="{{ $item->avatar }}" alt="{{ $item->avatar }}">
                                </div>
                                <div class="event-one__content">
                                    <h4 class="event-one__title">
                                        {{ $item->name }}
                                    </h4>
                                    <div class="event-one__location">
                                        <div class="event-one__location-icon">
                                            <span class="icon-location"></span>
                                        </div>
                                        <p class="event-one__loation-text">{{ $item->link }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
                <div class="col-12 text-center">
                    <a href="{{ route('diemReview') }}" class="viewmore-form__btn">Xem Thêm</a>
                </div>
                <!-- Blog Two Single End -->
            </div>
        </div>
    </section>
   
     <!--Team Two Start -->
     <section class="team-two">
        <div class="team-two__shape-1 float-bob-y">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/team-two-shape-1.png" alt="">
        </div>
        <div class="team-two__shape-2 float-bob-x">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/team-two-shape-2.png" alt="">
        </div>
        <div class="container">
            <div class="section-title-two text-left sec-title-animation animation-style2">
                <div class="section-title-two__tagline-box">
                    <div class="section-title-two__tagline-shape">
                        <img src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-two-shape-2.png" alt="">
                    </div>
                    <span class="section-title-two__tagline">Our Team</span>
                </div>
                <h2 class="section-title-two__title title-animation">Đội ngũ giáo viên tại Cánh Én
                </h2>
            </div>
            <div class="team-two__carousel owl-theme owl-carousel">
                @foreach ($founder as $item)
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
                                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/team-one-content-shape-1.png" alt="">
                                        </div>
                                        <div class="team-one__content-shape-2">
                                            <img src="{{ env('AWS_R2_URL') }}/frontend/images/team-one-content-shape-2.png" alt="">
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
                    <!--Team Two Single End-->
                @endforeach


            </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-12 text-center">
                    <a href="{{ route('listTeacher') }}" class="viewmore-form__btn"
                        style="    background: #eeeeee;color: #970a17;">Xem Thêm</a>
                </div>
            </div>
        </div>
    </section>
    <!--Team Two End -->
    {{-- <section class="about-two">
        <div class="about-two__bg-shape"
            style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/about-two-bg-shape.png);"></div>
        <div class="container">
            <div class="row">
                <div class="col-xl-5">
                    <div class="about-two__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                        <div class="about-two__img-box">
                            <div class="about-two__img">
                                <img src="{{ $setting->fax }}" alt="">
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
                            <h2 class="section-title-two__title title-animation">ĐIỂM <span>KHÁC BIỆT</span> CỦA CHÚNG TÔI
                            </h2>
                        </div>
                        <p class="about-two__text">{{ $setting->iframe_map }}</p>
                        <ul class="about-two__points-list list-unstyled">
                            @foreach ($khacbiet as $item)
                                <li>
                                    <div class="about-two__icon">
                                        <img src="{{ $item->image }}" alt="">
                                    </div>
                                    <div class="about-two__content">
                                        <h3>{{ $item->name }}</h3>
                                        <p>{{ $item->description }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!--About Two End -->
    <!--Courses Two Start -->

    <!--Courses Two End -->
    <!--Why Choose Two Start -->
    {{-- <section class="why-choose-two">
        <div class="why-choose-two__shape-6"></div>
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="why-choose-two__left">
                        <div class="section-title-two text-left sec-title-animation animation-style2">
                            <div class="section-title-two__tagline-box">
                                <div class="section-title-two__tagline-shape">
                                    <img src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-two-shape-1.png"
                                        alt="">
                                </div>
                                <span class="section-title-two__tagline">Why Choose Us</span>
                            </div>
                            <h2 class="section-title-two__title title-animation">Tại sao nên chọn Cánh Én
                            </h2>
                        </div>
                        <div class="why-choose-two__left-content-box">
                            <div class="accordion" id="accordionExample2">
                                @foreach ($bannerAds as $key => $item)
                                    <div class="accordion-item">
                                        <h3 class="accordion-header position-relative"
                                            id="headingOne-{{ $key }}"> <button
                                                class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne-{{ $key }}" aria-expanded="false"
                                                aria-controls="collapseOne-{{ $key }}"> {{ $item->name }}
                                            </button> <img class="position-absolute" src="{{ $item->image }}"
                                                alt="{{ $item->name }}"> </h3>
                                        <div id="collapseOne-{{ $key }}" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne-{{ $key }}"
                                            data-bs-parent="#accordionExample2" style="">
                                            <div class="accordion-body">
                                                {!! $item->content !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="course-details__video-link wow slideInRight" data-wow-delay="100ms"
                        data-wow-duration="2500ms" style="padding: 179px 0 178px;">
                        <div class="course-details__video-link-bg"
                            style="background-image: url('{{ $setting->linkpopup }}');">
                        </div>
                        <a href="{{ $setting->footer_content }}" class="video-popup">
                            <div class="course-details__video-icon">
                                <span class="icon-play"></span>
                                <i class="ripple"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!--Why Choose Two End -->
    <!-- Counter Two Start -->

    <!-- Courses Three End -->
    <section class="feature-cards">
        <div class="container">
            <div class="feature-cards__wrap">
                <ul class="list-unstyled feature-cards__list">
                    <li>
                        <div class="feature-cards__single">
                            <div class="feature-cards__icon-wrap">
                                <div class="feature-cards__icon">
                                    <span class="icon-screen-share"></span>
                                </div>
                            </div>
                            <p class="feature-cards__text">100% giáo viên chất lượng</p>
                        </div>
                    </li>
                    <li>
                        <div class="feature-cards__single">
                            <div class="feature-cards__icon-wrap">
                                <div class="feature-cards__icon">
                                    <span class="icon-user-plus"></span>
                                </div>
                            </div>
                            <p class="feature-cards__text">500 học sinh ưu tú</p>
                        </div>
                    </li>
                    <li>
                        <div class="feature-cards__single">
                            <div class="feature-cards__icon-wrap">
                                <div class="feature-cards__icon">
                                    <span class="icon-stamp"></span>
                                </div>
                            </div>
                            <p class="feature-cards__text">100 giải thưởng</p>
                        </div>
                    </li>
                    <li>
                        <div class="feature-cards__single">
                            <div class="feature-cards__icon-wrap">
                                <div class="feature-cards__icon">
                                    <span class="icon-book"></span>
                                </div>
                            </div>
                            <p class="feature-cards__text">50 chương trình học chất lượng</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <style>
        .feature-cards {
            margin: 36px 0 50px;
        }

        .feature-cards .feature-cards__wrap {
            padding: 0;
        }

        .feature-cards .feature-cards__list {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            width: 100%;
            padding-top: 36px;
        }

        .feature-cards .feature-cards__list li {
            margin: 0;
        }

        .feature-cards .feature-cards__single {
            position: relative;
            min-height: 150px;
            background: linear-gradient(180deg, #ffd45f 0%, #f4bc2f 100%);
            padding: 76px 18px 20px;
            text-align: center;
            border-radius: 14px;
            border: 1px solid #e8ae1b;
            box-shadow: 0 8px 18px rgba(244, 188, 47, 0.28);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .feature-cards .feature-cards__single:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 28px rgba(244, 188, 47, 0.4);
        }

    

        .feature-cards .feature-cards__icon-wrap {
            position: absolute;
            top: -34px;
            left: 50%;
            transform: translateX(-50%);
            width: 102px;
            height: 102px;
            background: #fff;
            border: 1px solid #ececec;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.14);
            z-index: 3;
        }

        .feature-cards .feature-cards__icon {
            width: 82px;
            height: 82px;
            border: 2px solid #f3c142;
            border-radius: 50%;
            color: #efb112;
            font-size: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.25s ease;
        }

        .feature-cards .feature-cards__single:hover .feature-cards__icon {
            transform: scale(1.06);
        }

        .feature-cards .feature-cards__icon span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .feature-cards .feature-cards__text {
            margin: 0;
            color: #1d1d1d;
            font-size: 19px;
            line-height: 1.34;
            font-weight: 700;
            letter-spacing: -0.005em;
        }

        @media (max-width: 1199px) {
            .feature-cards .feature-cards__list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .feature-cards .feature-cards__text {
                font-size: 20px;
            }
        }

        @media (max-width: 767px) {
            .feature-cards .feature-cards__list {
                grid-template-columns: 1fr;
            }

            .feature-cards .feature-cards__text {
                font-size: 20px;
            }
        }
    </style>

    <!-- Testimonial Two Start -->
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
                    Tình yêu của học viên với Cánh Én
                </h2>
            </div>
            <div class="testimonial-two__inner">
                <div class="testimonial-two__slider">
                    <div class="testimonial-two__main-content">
                        <div class="testimonial-two__carousel owl-carousel owl-theme">
                            <!-- Testimonial Item 1 -->
                            @foreach ($reviewcus as $item)
                                <div class="video-one__inner">
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
            <div class="row">
                <div class="col-12 text-center">
                    <a href="{{ route('diemReview') }}" class="viewmore-form__btn">Xem Thêm</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonial Two End -->

    <!-- Blog Two Start -->
    <section class="blog-two">
        <div class="container">
            <div class="section-title-two text-center sec-title-animation animation-style1">
                <div class="section-title-two__tagline-box">
                    <div class="section-title-two__tagline-shape">
                        <img src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-two-shape-1.png" alt="">
                    </div>
                    <span class="section-title-two__tagline">Our Insight</span>
                </div>
                <h2 class="section-title-two__title title-animation">
                    Tin tức và sự kiện
                </h2>
            </div>
            <div class="row">
                @foreach ($hotnews as $item)
                    <!-- Blog Two Single Start -->
                    <div class="col-xl-4 col-lg-4 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="blog-two__single">
                            <a href="{{ route('detailBlog', ['slug' => $item->slug]) }}">
                                <div class="blog-two__img">
                                    <img src="{{ $item->image }}" alt="">
                                    <div class="blog-two__date">
                                        <span class="icon-calendar"></span>
                                        <p>{{ date_format($item->created_at, 'd/m/Y') }}</p>
                                    </div>
                                </div>
                            </a>

                            <div class="blog-two__content">
                                <h4 class="blog-two__title">
                                    <a
                                        href="{{ route('detailBlog', ['slug' => $item->slug]) }}">{{ languageName($item->title) }}</a>
                                </h4>
                                <p class="blog-two__text line_3">{{ languageName($item->description) }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Blog Two Single End -->
                @endforeach
            </div>
        </div>
    </section>
    <!-- Blog Two End -->
@endsection
