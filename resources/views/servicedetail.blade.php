@extends('layouts.main.master')
@section('title')
    {{ $detail_service->name }}
@endsection
@section('description')
    {{ languageName($detail_service->description) }}
@endsection
@section('image')
    {{ url('' . $detail_service->image) }}
    @php
        $benefit = json_decode($detail_service->content);
        $quote = json_decode($detail_service->otherser);
    @endphp
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
                    <div class="col-lg-8">
                        <h1>{{ $detail_service->name }}</h1>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li><a href="">Điểm khác biệt</a></li>
                                <li><span>//</span></li>
                                <li>{{ $detail_service->name }}</li>
                            </ul>
                        </div>
                        <div class="desc mb-3">
                            {!! languageName($detail_service->description) !!}
                        </div>
                        <div class="banner-one__thm-and-other-btn-box d-flex justify-content-between">
                            <div class="banner-one__btn-box">
                                <a href="{{ $setting->facebook }}"  class="thm-btn"><span
                                        class="icon-angles-right"></span>Nhắn tin cho Edu Alpha </a>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-4">
                        <div class="page-header__img">
                            <img src="{{ $detail_service->image }}" alt="">
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
    <section class="about-one">
        <div class="about-one__shape-1">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/about-one-shape-1.png" alt="">
        </div>
        <div class="about-one__shape-2">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/about-one-shape-2.png" alt="">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xl-6 wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                    <div class="about-one__left">
                        <div class="about-one__left-shape-1 rotate-me"></div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 d-md-block d-none ">
                                <div class="about-one__img-box">
                                    <div class="about-one__img">
                                        @if ($benefit->image_1 != '')
                                            <img src="{{ $benefit->image_1 }}" alt="">
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="about-one__img-box-2">
                                    <div class="about-one__img-2">
                                        @if ($benefit->image_2 != '')
                                            <img src="{{ $benefit->image_2 }}" alt="">
                                        @endif
                                    </div>
                                    <div class="about-one__img-shape-1 float-bob-y">
                                        <img src="{{ env('AWS_R2_URL') }}/images/about-one-img-shape-1.png" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="about-one__right">
                        <div class="section-title text-left sec-title-animation animation-style2">
                            <div class="section-title__tagline-box">
                                <div class="section-title__tagline-shape"></div>
                                <span class="section-title__tagline">Benefit</span>
                            </div>
                            <h2 class="section-title__title title-animation">{{ $benefit->title }}</h2>
                        </div>
                        <p class="about-one__text">{{ $benefit->description }}</p>
                        <ul class="about-one__mission-and-vision list-unstyled">
                            @if ($benefit->object[0]->name != '')
                                @foreach ($benefit->object as $obj)
                                    <li>
                                        <div class="about-one__icon-and-title">
                                            <div class="about-one__icon">
                                                <img width="40" src="{{ $obj->image }}" alt="">
                                            </div>
                                            <h3>{{ $obj->name }}</h3>
                                        </div>
                                        <p class="about-one__mission-and-vision-text">{{ $obj->content }}</p>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                        <div class="about-one__btn-and-live-class">
                            <div class="about-one__btn-box">
                                <a href="{{ $setting->facebook }}" class="about-one__btn thm-btn"><span
                                        class="icon-angles-right"></span>Nhắn tin Edu Alpha</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="about-one">
        <div class="about-one__shape-1">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/about-one-shape-1.png" alt="">
        </div>
        <div class="about-one__shape-2">
            <img src="{{ env('AWS_R2_URL') }}/frontend/images/about-one-shape-2.png" alt="">
        </div>
        <div class="container">
            <div class="section-title text-center sec-title-animation animation-style2">
                <div class="section-title__tagline-box">
                    <div class="section-title__tagline-shape"></div>
                    <span class="section-title__tagline">Quote</span>
                </div>
                <h2 class="section-title__title title-animation">{{ $quote->title }}</h2>
            </div>
            @if ($quote->object[0]->name != '')
                @foreach ($quote->object as $key => $bg)
                    @if ($key % 2 == 0)
                        <div class="row">
                            <div class="col-xl-6 wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                                <div class="about-one__left">
                                    <div class="about-one__img-box">
                                        <div class="about-one__img">
                                            <img src="{{ $bg->image }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6" style="margin: auto">
                                <div class="about-one__right">
                                    <div class="section-title text-left sec-title-animation animation-style2">
                                        <h2 class="section-title__title title-animation">{{ $bg->name }}</h2>
                                    </div>
                                    <p class="about-one__text">{!! $bg->content !!}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-xl-6" style="margin: auto">
                                <div class="about-one__right">
                                    <div class="section-title text-left sec-title-animation animation-style2">
                                        <h2 class="section-title__title title-animation">{{ $bg->name }}</h2>
                                    </div>
                                    <p class="about-one__text">{!! $bg->content !!}</p>
                                </div>
                            </div>
                            <div class="col-xl-6 wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                                <div class="about-one__left">
                                    <div class="about-one__img-box">
                                        <div class="about-one__img">
                                            <img src="{{ $bg->image }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </section>

@endsection
