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
<style>
    .feature-cards {
        background: #fdf8ee;
        padding: 25px 0;
        margin: 0;
    }

    .feature-cards .feature-cards__wrap {
        padding: 0;
    }

    .feature-cards .feature-cards__list {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0;
        width: 100%;
        margin: 0;
    }

    .feature-cards .feature-cards__item {
        margin: 0;
        padding: 18px 28px;
        border-right: 1px solid #e6dfd3;
    }

    .feature-cards .feature-cards__item:last-child {
        border-right: none;
    }

    .feature-cards .feature-cards__single {
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: auto;
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 0;
        text-align: left;
    }

    .feature-cards .feature-cards__icon-wrap {
        position: relative;
        top: auto;
        left: auto;
        transform: none;
        flex-shrink: 0;
        width: 56px;
        height: 56px;
        padding: 10px;
        background: #fff;
        border-radius: 14px;
        border: 1px solid #f3ece0;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .feature-cards .feature-cards__icon-wrap img {
        /* width: 100%;
        height: 100%; */
        max-width: 56px;
        max-height: 56px;
        object-fit: contain;
        display: block;
    }

    .feature-cards .feature-cards__content {
        flex: 1;
        min-width: 0;
    }

    .feature-cards .feature-cards__title {
        margin: 0 0 4px;
        font-size: 22px;
        line-height: 1.2;
        font-weight: 700;
        color: #1f1f1f;
    }

    .feature-cards .feature-cards__desc {
        margin: 0;
        font-size: 14px;
        line-height: 1.45;
        font-weight: 400;
        color: #5a5a5a;
    }

    @media only screen and (max-width: 1199px) {
        .feature-cards .feature-cards__list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .feature-cards .feature-cards__item {
            padding: 0;
            border: none !important;
        }

        .feature-cards .feature-cards__single {
            height: 100%;
            padding: 16px;
            background: #fff;
            border-radius: 14px;
            border: 1px solid #efe6d6;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }
    }

    @media only screen and (max-width: 767px) {
        .feature-cards {
            padding: 18px 0 22px;
        }

        .feature-cards .feature-cards__list {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .feature-cards .feature-cards__item {
            padding: 0;
            border: none !important;
        }

        .feature-cards .feature-cards__single {
            gap: 14px;
            padding: 14px 16px;
            border-radius: 12px;
        }

        .feature-cards .feature-cards__icon-wrap {
            width: 48px;
            height: 48px;
            padding: 8px;
            border-radius: 12px;
        }

        .feature-cards .feature-cards__icon-wrap img {
            max-width: 30px;
            max-height: 30px;
        }

        .feature-cards .feature-cards__title {
            font-size: 18px;
            margin-bottom: 3px;
        }

        .feature-cards .feature-cards__desc {
            font-size: 13px;
            line-height: 1.4;
            color: #666;
        }
    }

    @media only screen and (max-width: 400px) {
        .feature-cards .feature-cards__single {
            padding: 12px 14px;
            gap: 12px;
        }

        .feature-cards .feature-cards__icon-wrap {
            width: 44px;
            height: 44px;
            padding: 7px;
        }

        .feature-cards .feature-cards__icon-wrap img {
            max-width: 28px;
            max-height: 28px;
        }

        .feature-cards .feature-cards__title {
            font-size: 17px;
        }

        .feature-cards .feature-cards__desc {
            font-size: 12px;
        }
    }



    .team-two--modern .team-two__shape-1,
    .team-two--modern .team-two__shape-2 {
        display: none;
    }

    .team-two--modern .team-two__carousel-wrap {
        position: relative;
        padding: 0 8px;
    }

    .team-two--modern .teacher-card {
        display: flex;
        align-items: stretch;
        min-height: 168px;
        background: #fff;
        border: 1px solid #ebe8e4;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        transition: box-shadow 0.25s ease, transform 0.25s ease;
    }

    .team-two--modern .teacher-card:hover {
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .team-two--modern .teacher-card__media {
        flex: 0 0 38%;
        max-width: 38%;
        background: #f9fafb;
    }

    .team-two--modern .teacher-card__media a {
        display: block;
        height: 100%;
    }

    .team-two--modern .teacher-card__media img {
        width: 100%;
        height: 100%;
        min-height: 168px;
        object-fit: cover;
        display: block;
    }

    .team-two--modern .teacher-card__body {
        flex: 1;
        min-width: 0;
        padding: 16px 16px 14px;
        display: flex;
        flex-direction: column;
    }

    .team-two--modern .teacher-card__name {
        margin: 0 0 8px;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.3;
    }

    .team-two--modern .teacher-card__name a {
        color: var(--fistudy-primary, #f46f01);
        text-decoration: none;
    }

    .team-two--modern .teacher-card__name a:hover {
        color: #d96200;
    }

    .team-two--modern .teacher-card__line {
        margin: 0 0 4px;
        font-size: 13px;
        line-height: 1.45;
        color: #4b5563;
    }

    .team-two--modern .teacher-card__social {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: auto;
        padding-top: 10px;
    }

    .team-two--modern .teacher-card__social a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #2563eb;
        color: #fff !important;
        font-size: 13px;
        text-decoration: none;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .team-two--modern .teacher-card__social a:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .team-two--modern .team-two__carousel .owl-nav {
        position: absolute;
        top: 50%;
        right: -4px;
        left: auto;
        transform: translateY(-50%);
        margin: 0;
        width: auto;
    }

    .team-two--modern .team-two__carousel .owl-nav button {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        background: var(--fistudy-primary, #f46f01) !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 4px 14px rgba(244, 111, 1, 0.35);
        font-size: 16px !important;
        line-height: 40px !important;
        margin: 0 !important;
    }

    .team-two--modern .team-two__carousel .owl-nav button.owl-prev {
        display: none !important;
    }

    .team-two--modern .team-two__carousel .owl-nav button:hover {
        background: #d96200 !important;
    }

    .team-two--modern .team-two__more {
        margin-top: 24px;
    }

    .team-two--modern .team-two__more .viewmore-form__btn {
        background: #fff;
        color: #3d2620;
        border: 1px solid #e5e7eb;
        padding: 10px 22px;
        border-radius: 8px;
        font-weight: 600;
    }

    .team-two--modern .team-two__more .viewmore-form__btn:hover {
        background: var(--fistudy-primary, #f46f01);
        color: #fff;
        border-color: var(--fistudy-primary, #f46f01);
    }

    @media (max-width: 767px) {
        .team-two--modern {
            padding: 36px 0 44px;
        }

        .team-two--modern .teacher-card {
            min-height: 150px;
        }

        .team-two--modern .teacher-card__media img {
            min-height: 150px;
        }

        .team-two--modern .teacher-card__name {
            font-size: 16px;
        }

        .team-two--modern .teacher-card__line {
            font-size: 12px;
        }
    }

    .home-stats {
        padding: 0 0 56px;
        background: #fff;
    }

    .home-stats__bar {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        background: #fef9f2;
        border-radius: 15px;
        padding: 28px 12px;
    }

    .home-stats__item {
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 4px 20px;
        min-width: 0;
    }

    .home-stats__item:not(:last-child)::after {
        content: "";
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 58px;
        background: #e6dfd3;
    }

    .home-stats__icon {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        font-size: 40px;
        line-height: 1;
        color: var(--fistudy-primary, #f46f01);
    }

    .home-stats__icon .fas {
        font-size: 36px;
    }

    .home-stats__content {
        min-width: 0;
    }

    .home-stats__number {
        margin: 0 0 2px;
        font-size: 32px;
        line-height: 1.15;
        font-weight: 700;
        color: var(--fistudy-primary, #f46f01);
        letter-spacing: -0.02em;
    }

    .home-stats__label {
        margin: 0;
        font-size: 14px;
        line-height: 1.35;
        font-weight: 400;
        color: #2b2b2b;
    }

    @media only screen and (max-width: 1199px) {
        .home-stats__bar {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0;
            padding: 8px 0;
        }

        .home-stats__item {
            flex: none;
            justify-content: flex-start;
            padding: 20px 22px;
        }

        .home-stats__item:nth-child(odd)::after {
            display: block;
        }

        .home-stats__item:nth-child(even)::after {
            display: none;
        }

        .home-stats__item:nth-child(-n+2) {
            border-bottom: 1px solid #e6dfd3;
        }
    }

    @media only screen and (max-width: 767px) {
        .home-stats {
            padding: 0 0 36px;
        }

        .home-stats__bar {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            border-radius: 14px;
            padding: 6px 0;
            gap: 0;
        }

        .home-stats__item {
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 10px;
            padding: 16px 14px;
            min-height: 112px;
        }

        .home-stats__item:nth-child(odd) {
            border-right: 1px solid #e6dfd3;
        }

        .home-stats__item:nth-child(-n+2) {
            border-bottom: 1px solid #e6dfd3;
        }

        .home-stats__item::after {
            display: none !important;
        }

        .home-stats__icon {
            width: 36px;
            height: 36px;
            font-size: 28px;
        }

        .home-stats__icon .fas {
            font-size: 26px;
        }

        .home-stats__number {
            font-size: 24px;
            margin-bottom: 2px;
        }

        .home-stats__label {
            font-size: 12px;
            line-height: 1.35;
            color: #4a4a4a;
        }
    }

    @media only screen and (max-width: 400px) {
        .home-stats__item {
            padding: 14px 12px;
            min-height: 104px;
            gap: 8px;
        }

        .home-stats__number {
            font-size: 22px;
        }

        .home-stats__label {
            font-size: 11px;
        }
    }

    .testimonial-two {
        position: relative;
        padding: 16px 0 58px;
        background: #fff;
    }

    .testimonial-two .container {
        position: relative;
    }

    .testimonial-two .section-title-two {
        margin-bottom: 30px;
    }

    .testimonial-two__shape-1,
    .testimonial-two__shape-2,
    .testimonial-two__shape-3,
    .testimonial-two__shape-4,
    .testimonial-two__shape-5 {
        display: none !important;
    }

    .testimonial-two__inner,
    .testimonial-two__slider,
    .testimonial-two__main-content,
    .testimonial-two__carousel {
        position: relative;
    }

    .testimonial-two__carousel .owl-stage {
        display: flex;
    }

    .testimonial-two__carousel .owl-item {
        height: auto;
    }

    .testimonial-two__card {
        height: 100%;
        background: #fef9f2;
        border: 1px solid #f1e6d8;
        border-radius: 12px;
        padding: 20px 20px 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .testimonial-two__quote-icon {
        font-size: 18px;
        line-height: 1;
        color: #f2a84a;
    }

    .testimonial-two__card-text {
        margin: 0;
        font-size: 14px;
        line-height: 1.55;
        font-weight: 500;
        color: #4a525f;
        min-height: 88px;
    }

    .testimonial-two__card-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .testimonial-two__person {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .testimonial-two__avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .testimonial-two__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .testimonial-two__meta {
        min-width: 0;
    }

    .testimonial-two__name {
        margin: 0;
        font-size: 14px;
        line-height: 1.2;
        font-weight: 700;
        color: #222833;
    }

    .testimonial-two__role {
        margin: 2px 0 0;
        font-size: 11px;
        line-height: 1.3;
        color: #7b8491;
    }

    .testimonial-two__stars {
        display: flex;
        align-items: center;
        gap: 3px;
        flex-shrink: 0;
        color: #f7931a;
        font-size: 11px;
    }

    .testimonial-two__thumb-outer-box {
        display: none !important;
    }

    .testimonial-two .owl-nav {
        position: absolute;
        top: 50%;
        left: -24px;
        right: -24px;
        margin: 0;
        pointer-events: none;
    }

    .testimonial-two .owl-nav button.owl-prev,
    .testimonial-two .owl-nav button.owl-next {
        pointer-events: auto;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        border: 1px solid #f1dfca !important;
        background: #fff7eb !important;
        color: #f0a138 !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        font-size: 14px !important;
        box-shadow: none !important;
    }

    .testimonial-two .owl-nav button.owl-prev {
        left: 0;
    }

    .testimonial-two .owl-nav button.owl-next {
        right: 0;
    }

    .testimonial-two .owl-nav button.owl-prev:hover,
    .testimonial-two .owl-nav button.owl-next:hover {
        background: #f7a73a !important;
        color: #fff !important;
        border-color: #f7a73a !important;
    }

    @media only screen and (max-width: 1199px) {
        .testimonial-two {
            padding-bottom: 44px;
        }

        .testimonial-two .owl-nav {
            left: -12px;
            right: -12px;
        }
    }

    @media only screen and (max-width: 767px) {
        .testimonial-two .section-title-two {
            margin-bottom: 20px;
        }

        .testimonial-two__card {
            padding: 16px;
            border-radius: 10px;
        }

        .testimonial-two__card-text {
            min-height: auto;
            font-size: 13px;
        }

        .testimonial-two .owl-nav {
            left: -4px;
            right: -4px;
        }

        .testimonial-two .owl-nav button.owl-prev,
        .testimonial-two .owl-nav button.owl-next {
            width: 34px !important;
            height: 34px !important;
            font-size: 12px !important;
        }
    }
</style>
@endsection
@section('js')
@endsection
@section('content')
    <!-- Banner Two Start -->
    <section class="banner-two">
        <div class="banner-two__layout">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-6 col-lg-6">
                        <div class="banner-two__left">
                        <p class="banner-two__sub-title">Hệ thống giáo dục Cánh Én</p>
                        <h2 class="banner-two__title">{!! isset($banner[0]->title) ? $banner[0]->title : '' !!}</h2>
                        <div class="banner-two__text">{!! isset($banner[0]->description) ? $banner[0]->description : '' !!}</div>
                        <div class="banner-two__btn-box">
                            <a href="#consultRegisterModal" class="thm-btn-two banner-two__btn-primary" data-bs-toggle="modal" data-bs-target="#consultRegisterModal" data-toggle="modal" data-target="#consultRegisterModal">
                                <span>Đăng ký tư vấn miễn phí</span>
                                <i class="icon-play"></i>
                            </a>
                            <a href="{{ route('couseList') }}" class="banner-two__btn-outline">
                                <span>Xem khóa học</span>
                                <i class="icon-play"></i>
                            </a>
                        </div>
                        <div class="banner-two__success-student">
                            <ul class="list-unstyled banner-two__success-student-list">
                                <li>
                                    <div class="banner-two__success-student-img">
                                        <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-success-student-1-1.jpg" alt="">
                                    </div>
                                </li>
                                <li>
                                    <div class="banner-two__success-student-img">
                                        <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-success-student-1-2.jpg" alt="">
                                    </div>
                                </li>
                                <li>
                                    <div class="banner-two__success-student-img">
                                        <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-success-student-1-3.jpg" alt="">
                                    </div>
                                </li>
                            </ul>
                            <div class="banner-two__success-student-content">
                                <p class="banner-two__success-student-text">
                                    <span class="banner-two__success-student-count">
                                        <span class="odometer" data-count="2000">00</span>+
                                    </span>
                                    học sinh <br> đã và đang đồng hành cùng Cánh Én
                                </p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="banner-two__right">
                <div class="banner-two__hero-image">
                    <img src="{{ isset($banner[0]->image) ? $banner[0]->image : '' }}" alt="{{ $setting->company ?? 'Cánh Én' }}">
                    <span class="banner-two__deco-star banner-two__deco-star--1" aria-hidden="true"></span>
                    <span class="banner-two__deco-star banner-two__deco-star--2" aria-hidden="true"></span>
                    <span class="banner-two__deco-swirl" aria-hidden="true"></span>
                </div>
            </div>
        </div>
    </section>
    <section class="feature-cards">
        <div class="container">
            <div class="feature-cards__wrap">
                <ul class="list-unstyled feature-cards__list">
                    <li class="feature-cards__item">
                        <div class="feature-cards__single">
                            <div class="feature-cards__icon-wrap">
                               <img src="/frontend/images/icon1.png" alt="">
                            </div>
                            <div class="feature-cards__content">
                                <h3 class="feature-cards__title">Hiểu</h3>
                                <p class="feature-cards__desc">Nắm chắc kiến thức nền tảng</p>
                            </div>
                        </div>
                    </li>
                    <li class="feature-cards__item">
                        <div class="feature-cards__single">
                            <div class="feature-cards__icon-wrap">
                                <img src="/frontend/images/icon2.png" alt="">
                            </div>
                            <div class="feature-cards__content">
                                <h3 class="feature-cards__title">Sâu</h3>
                                <p class="feature-cards__desc">Đào sâu bản chất, phát triển tư duy</p>
                            </div>
                        </div>
                    </li>
                    <li class="feature-cards__item">
                        <div class="feature-cards__single">
                            <div class="feature-cards__icon-wrap">
                                <img src="/frontend/images/icon3.png" alt="">
                            </div>
                            <div class="feature-cards__content">
                                <h3 class="feature-cards__title">Vững</h3>
                                <p class="feature-cards__desc">Rèn luyện kỹ năng, vận dụng linh hoạt</p>
                            </div>
                        </div>
                    </li>
                    <li class="feature-cards__item">
                        <div class="feature-cards__single">
                            <div class="feature-cards__icon-wrap">
                                <img src="/frontend/images/icon4.png" alt="">
                            </div>
                            <div class="feature-cards__content">
                                <h3 class="feature-cards__title">Yêu</h3>
                                <p class="feature-cards__desc">Nuôi dưỡng niềm say mê, tinh thần tự học</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <!-- Banner Two End -->
    <section class="courses-three">
        <div class="container">
            <div class="section-title-two section-title-two--with-link sec-title-animation animation-style1">
                <div class="section-title-two__heading">
                    <div class="section-title-two__tagline-box">
                        <span class="section-title-two__tagline">KHÓA HỌC NỔI BẬT</span>
                    </div>
                    <h2 class="section-title-two__title">Các khóa học được yêu thích</h2>
                </div>
                <a href="{{ route('couseList') }}" class="section-view-all">
                    Xem tất cả khóa học
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
            <div class="row">
                @foreach ($khoahoc as $key => $item)
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <article class="course-card">
                            <div class="course-card__media">
                                <a href="{{ route('couseDetail', ['slug' => $item->slug]) }}">
                                    <img src="{{ $item->images }}" alt="{{ $item->name }}">
                                </a>
                                <span class="course-card__badge">
                                    {{ languageName($item->cate->name) ?? languageName($item->typecate)->name ?? 'Khóa học' }}
                                </span>
                            </div>
                            <div class="course-card__body">
                                <h3 class="course-card__title">
                                    <a href="{{ route('couseDetail', ['slug' => $item->slug]) }}">{{ $item->name }}</a>
                                </h3>
                                @php
                                    $courseReviews = json_decode($item->hang_muc ?? '[]', true);
                                    if (!is_array($courseReviews)) {
                                        $courseReviews = [];
                                    }
                                    $courseReviewCount = 0;
                                    $courseReviewTotal = 0;
                                    foreach ($courseReviews as $courseReview) {
                                        $reviewStar = isset($courseReview['star']) ? (float) $courseReview['star'] : 0;
                                        $hasReviewContent = !empty($courseReview['name']) || !empty($courseReview['content']);
                                        if ($reviewStar > 0 && $hasReviewContent) {
                                            $courseReviewCount++;
                                            $courseReviewTotal += $reviewStar;
                                        }
                                    }
                                    $courseReviewAverage = $courseReviewCount > 0
                                        ? number_format($courseReviewTotal / $courseReviewCount, 1)
                                        : null;
                                @endphp
                                @if ($courseReviewCount > 0)
                                <div class="course-card__rating">
                                    <span class="icon-star"></span>
                                    <strong>{{ $courseReviewAverage }}</strong>
                                    <span class="course-card__reviews">({{ $courseReviewCount }} đánh giá)</span>
                                </div>
                                @endif
                                <p class="course-card__desc">
                                    {{ \Illuminate\Support\Str::limit(trim(strip_tags($item->description ?? '')), 90) }}
                                </p>
                                <div class="course-card__divider"></div>
                                <ul class="course-card__meta list-unstyled">
                                    <li>
                                        <span class="icon-clock"></span>
                                        <span>{{ $item->thickness ?: 0 }} buổi học</span>
                                    </li>
                                    <li>
                                        <span class="icon-location"></span>
                                        <span>Online hoặc offline</span>
                                    </li>
                                </ul>
                                <div class="course-card__footer">
                                    <div class="course-card__price">
                                        @if ($item->price > 0)
                                            <strong>{{ number_format($item->price, 0, ',', '.') }}đ</strong>
                                            @if (($item->discount ?? 0) > $item->price)
                                                <del>{{ number_format($item->discount, 0, ',', '.') }}đ</del>
                                            @endif
                                        @else
                                            <strong>Miễn phí</strong>
                                        @endif
                                    </div>
                                    <a href="{{ route('couseDetail', ['slug' => $item->slug]) }}" class="course-card__btn">Đăng ký</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
                
            </div>



        </div>
    </section>


     <!--Team Two Start -->
     <section class="team-two team-two--modern">
        <div class="container">
            <div class="section-title-two section-title-two--with-link sec-title-animation animation-style1">
                <div class="section-title-two__heading">
                    <div class="section-title-two__tagline-box">
                        <span class="section-title-two__tagline">Đội ngũ giáo viên</span>
                    </div>
                    <h2 class="section-title-two__title">Giáo viên giàu kinh nghiệm, <br> tận tâm và chuyên môn cao</h2>
                </div>
                <a href="{{ route('listTeacher') }}" class="section-view-all">
                    Xem tất cả
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
            <div class="team-two__carousel-wrap">
                <div class="team-two__carousel owl-theme owl-carousel">
                    @foreach ($founder as $teacher)
                        @php
                            $teacherMeta = json_decode($teacher->description) ?? [];
                            $teacherSubject = $teacherMeta[0]->title ?? 'Giáo viên tại Cánh Én';
                            $teacherExperience = $teacherMeta[1]->title ?? null;
                            $teacherSchool = $teacherMeta[2]->title ?? null;
                        @endphp
                        <div class="item">
                            <article class="teacher-card">
                                <div class="teacher-card__media">
                                    <a href="{{ route('detailTeacher', ['slug' => $teacher->slug]) }}">
                                        <img src="{{ $teacher->images }}" alt="{{ $teacher->name }}">
                                    </a>
                                </div>
                                <div class="teacher-card__body">
                                    <h3 class="teacher-card__name">
                                        <a href="{{ route('detailTeacher', ['slug' => $teacher->slug]) }}">{{ $teacher->name }}</a>
                                    </h3>
                                    <p class="teacher-card__line">{{ $teacherSubject }}</p>
                                    @if ($teacherExperience)
                                        <p class="teacher-card__line">{{ $teacherExperience }}</p>
                                    @endif
                                    @if ($teacherSchool)
                                        <p class="teacher-card__line">{{ $teacherSchool }}</p>
                                    @endif
                                    <div class="teacher-card__social">
                                        <a href="{{ route('detailTeacher', ['slug' => $teacher->slug]) }}" title="Facebook" aria-label="Facebook">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                        <a href="{{ route('detailTeacher', ['slug' => $teacher->slug]) }}" title="LinkedIn" aria-label="LinkedIn">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                        <a href="{{ route('lienHe') }}" title="Liên hệ" aria-label="Liên hệ">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
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
  


    <!-- Testimonial Two Start -->
    
    <!-- Testimonial Two End -->
    <section class="home-stats">
        <div class="container">
            <div class="home-stats__bar">
                <div class="home-stats__item">
                    <span class="home-stats__icon" aria-hidden="true">
                        <i class="icon-graduation-cap"></i>
                    </span>
                    <div class="home-stats__content">
                        <p class="home-stats__number">2.000+</p>
                        <p class="home-stats__label">Học sinh đã đồng hành</p>
                    </div>
                </div>
                <div class="home-stats__item">
                    <span class="home-stats__icon" aria-hidden="true">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="home-stats__content">
                        <p class="home-stats__number">120+</p>
                        <p class="home-stats__label">Giáo viên chất lượng</p>
                    </div>
                </div>
                <div class="home-stats__item">
                    <span class="home-stats__icon" aria-hidden="true">
                        <i class="fas fa-trophy"></i>
                    </span>
                    <div class="home-stats__content">
                        <p class="home-stats__number">500+</p>
                        <p class="home-stats__label">Giải thưởng đạt được</p>
                    </div>
                </div>
                <div class="home-stats__item">
                    <span class="home-stats__icon" aria-hidden="true">
                        <i class="fas fa-book-open"></i>
                    </span>
                    <div class="home-stats__content">
                        <p class="home-stats__number">50+</p>
                        <p class="home-stats__label">Chương trình học chất lượng</p>
                    </div>
                </div>
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
