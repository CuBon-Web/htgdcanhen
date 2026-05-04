@extends('layouts.main.master')
@section('title')
    {{ languageName($danhmuc->name) }}
@endsection
@section('description')
    {{ $danhmuc->description }}
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('css')
    <link rel="stylesheet" href="/frontend/css/faq.css">
@endsection
@section('js')
@endsection
@section('content')
    @php
        $vade = json_decode($danhmuc->vande);
        $help = json_decode($danhmuc->help);
    @endphp
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
                        <h2>{{ languageName($danhmuc->name) }}</h2>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li><a href="{{ route('couseList') }}">Khóa học</a></li>
                                <li><span>//</span></li>
                                <li>{{ languageName($danhmuc->name) }}</li>
                            </ul>
                        </div>
                        <div class="desc mb-3">
                            {!! $danhmuc->content !!}
                        </div>
                        <div class="banner-one__thm-and-other-btn-box d-flex">
                            <div class="banner-one__btn-box">
                                <a href="{{ $setting->facebook }}"  class="thm-btn"><span
                                        class="icon-angles-right"></span>Nhắn tin cho Edu Alpha </a>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-4">
                        <div class="page-header__img">
                            <img src="{{ $danhmuc->avatar }}" alt="">
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
    <section class="why-choose-one">
        <div class="why-choose-one__shape-6 float-bob-x">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/why-choose-one-shape-6.png" alt="">
        </div>
        <div class="why-choose-one__shape-7 float-bob-y">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/why-choose-one-shape-7.png" alt="">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="why-choose-one__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                        <div class="why-choose-one__img-box">
                            <div class="why-choose-one__img">
                                <img src="{{ $danhmuc->imagehome }}" alt="">
                            </div>
                            <div class="why-choose-one__shape-1 float-bob-y">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/why-choose-one-shape-1.png"
                                    alt="">
                            </div>
                            <div class="why-choose-one__shape-2 float-bob-x">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-edu-icon.png" alt="">
                            </div>
                            <div class="why-choose-one__shape-3 float-bob-y">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/why-choose-one-shape-3.png"
                                    alt="">
                            </div>
                            <div class="why-choose-one__shape-4">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/why-choose-one-shape-4.png"
                                    alt="">
                            </div>
                            <div class="why-choose-one__shape-5 img-bounce">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/why-choose-one-shape-5.png"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="why-choose-one__right">
                        <div class="section-title text-left sec-title-animation animation-style2">
                            <h2 class="section-title__title title-animation">Bạn Đang Gặp Phải
                                <span>Vấn Đề <img src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-shape-1.png"
                                        alt=""></span> Sau
                            </h2>
                        </div>
                        <p class="why-choose-one__text">Đây không phải là tất cả các vấn đề bạn gặp phải, nhưng nó sẽ là các
                            vấn đề thương thấy khi bạn học và ôn luyện</p>
                        <div class="why-choose-one__points-box">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                    <ul class="why-choose-one__points-list list-unstyled">
                                        @foreach ($vade as $item)
                                            <li>
                                                <div class="why-choose-one__points-icon-inner">
                                                    <div class="why-choose-one__points-icon">
                                                        <img src="{{ $item->image }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="why-choose-one__points-content">
                                                    <h3>{{ $item->title }}</h3>
                                                    <p>{{ $item->content }}</p>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="category-one">
        <div class="category-one__bg-shape"
            style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/category-one-bg-shape.png);"></div>
        <div class="category-one__shape-1">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/category-one-shape-1.png" alt="">
        </div>
        <div class="category-one__shape-2">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/category-one-shape-2.png" alt="">
        </div>
        <div class="category-one__shape-3">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/category-one-shape-3.png" alt="">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-7" style="margin: auto">
                    <div class="category-one__left">
                        <div class="section-title text-left sec-title-animation animation-style2">
                            <h2 class="section-title__title title-animation">Edu Alpha <span> Sẽ Giúp Bạn <img
                                        src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-shape-2.png"
                                        alt=""></span></h2>
                        </div>
                        @if (count($help) > 0)
                            <div class="faq-page__left">
                                <div class="accrodion-grp faq-one-accrodion" data-grp-name="faq-one-accrodion-1">

                                    @foreach ($help as $key => $item)
                                        <div class="accrodion  {{ $key == 0 ? 'active' : '' }}">
                                            <div class="accrodion-title">
                                                <h4>{{ $item->title }}</h4>
                                            </div>
                                            <div class="accrodion-content">
                                                <div class="inner">
                                                    <p>{{ $item->content }}</p>
                                                </div><!-- /.inner -->
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
                <div class="col-xl-6 col-lg-5 wow slideInRight" data-wow-delay="100ms" data-wow-duration="2500ms">
                    <div class="category-one__right">
                        <div class="category-one__img">
                            <img src="{{ $danhmuc->imagehelp }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="courses-one">
        <div class="container">
            <div class="section-title text-center sec-title-animation animation-style1">
                <h2 class="section-title__title title-animation">Các Khóa Học
                    <span>{{ languageName($danhmuc->name) }} <img
                            src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-shape-1.png"
                            alt=""></span>
                </h2>
            </div>
            <div class="courses-one__carousel owl-theme owl-carousel">
                <!--Courses One Single Start-->
                @foreach ($list as $item)
                    <div class="item">
                        <div class="courses-one__single">
                            <a href="{{ route('couseDetail', ['slug' => $item->slug]) }}">
                                <div class="courses-one__img-box">
                                    <div class="courses-one__img">
                                        <img src="{{ $item->images }}" alt="">
                                    </div>
                                </div>
                            </a>
                            <div class="courses-one__content">
                                <div class="courses-one__tag-and-meta">
                                    <ul class="courses-one__meta list-unstyled">
                                        <li>
                                            <div class="icon">
                                                <span class="icon-book"></span>
                                            </div>
                                            <p>{{ $item->ingredient }} Bài học</p>
                                        </li>
                                        <li>
                                            <div class="icon">
                                                <span class="icon-clock"></span>
                                            </div>
                                            <p>{{ $item->thickness }} Buổi học</p>
                                        </li>
                                    </ul>
                                </div>
                                <h3 class="courses-one__title"><a
                                        href="{{ route('couseDetail', ['slug' => $item->slug]) }}">{{ $item->name }}</a>
                                </h3>
                                <div class="line_2">{!! $item->description !!}</div>
                                <div class="courses-one__ratting-and-heart-box">

                                </div>
                                <div class="courses-one__btn-and-doller-box">
                                    <div class="courses-two__client-box">
                                        <div class="courses-two__client-img">
                                            @if ($item->user_id == 0)
                                                <img src="{{ url('frontend/images/user_icon.png') }}" alt="">
                                            @else
                                                @php
                                                    $teacher = \DB::table('customer')
                                                        ->where('id', $item->user_id)
                                                        ->first(['name', 'avatar']);
                                                @endphp
                                                <img src="{{ $item->customer->avatar ? url('uploads/images/' . $teacher->avatar) : url('frontend/images/user_icon.png') }}"
                                                    alt="">
                                            @endif
                                        </div>
                                        <div class="courses-two__client-content">
                                            <h4>{{ $item->user_id == 0 ? 'Quản trị viên' : $teacher->name }}</h4>
                                            <p>Giáo viên</p>
                                        </div>
                                    </div>
                                    <div class="courses-one__btn-box">
                                        <a href="{{ route('couseDetail', ['slug' => $item->slug]) }}"
                                            class="courses-one__btn thm-btn"><span class="icon-angles-right"></span>Chi
                                            tiết</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


@endsection
