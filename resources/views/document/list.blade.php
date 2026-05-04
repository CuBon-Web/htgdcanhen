@extends('layouts.main.master')
@section('title')
    {{ $cate->name }}
@endsection
@section('description')
    {{ $cate->name }}
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('css')
    <style>
        .courses-three__content p {
            margin: 0;
            font-size: 14px;
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
                    <div class="col-lg-8">
                        <h1>{{ $cate->name }}</h1>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li><a href="">Đề thi</a></li>
                                <li><span>//</span></li>
                                <li>{{ $cate->name }}</li>
                            </ul>
                        </div>
                        <div class="desc mb-3">
                            {{ $cate->description }}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="page-header__img">
                            <img src="/frontend/images/breamcum.png" alt="">
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
        <section class="course-grid">
            <div class="container">
                <div class="course-grid__right">
                    <div class="course-grid__right-content-box">
                        <div class="row">
                            <!-- Courses Three Single Start -->
                            {{-- {{dd($listDethiwith)}} --}}
                            @foreach ($list as $taiLieu)
                                <div class="col-xl-4 wow fadeInLeft" data-wow-delay="100ms">
                                    <div class="courses-three__single">
                                        <div class="courses-three__img">
                                                <img src="{{ $taiLieu->image != null ? url($taiLieu->image) : url('frontend/images/document.jpg')}}"
                                                    alt="{{ $taiLieu->image != null ? url($taiLieu->image) : url('frontend/images/document.jpg')}}">
                                        </div>
                                        <div class="courses-three__content">
                                            <h3 class="courses-three__title"><a
                                                    href="">{{ $taiLieu->name }}</a></h3>
                                                    <div class="line_2">
                                                        {!! languageName($taiLieu->description) !!}
                                                    </div>

                                            @if ($taiLieu->price > 0)
                                                <div class="courses-three__price">
                                                    <span>Giá: {{ number_format($taiLieu->price, 0, ',', '.') }} VNĐ</span>
                                                </div>
                                            @else
                                                <div class="courses-three__price">
                                                    <span>Miễn phí</span>
                                                </div>
                                            @endif
                                            <div class="courses-three__btn-and-heart-box">
                                                <div class="courses-three__btn-box">
                                                    @if ($taiLieu->price == 0)
                                                        <a  href="{{url('upload/audio/'.$taiLieu->docs)}}" class="thm-btn-two">
                                                            <span>Tải xuống</span>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @else
                                                        <a  href="{{url('upload/audio/'.$taiLieu->pdf)}}" class="thm-btn-two">
                                                            <span>Xem trước</span>
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" data-id="{{ $taiLieu->id }}" data-slug="{{ $taiLieu->slug }}"
                                                            class="thm-btn-two themvaogiohangTailieu">
                                                            <span>Thêm giỏ hàng</span>
                                                            <i class="icon-angles-right"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="courses-two__client-box">
                                                    <div class="courses-two__client-img">
                                                        @if ($taiLieu->customer)
                                                            <img src="{{ $taiLieu->customer->avatar != null ? url('uploads/images/' . $taiLieu->customer->avatar) : url('frontend/images/user_icon.png') }}"
                                                                alt="">
                                                        @else
                                                            <img src="{{ url('frontend/images/user_icon.png') }}"
                                                                alt="">
                                                        @endif
                                                    </div>
                                                    <div class="courses-two__client-content">
                                                        <h4>{{ $taiLieu->customer ? $taiLieu->customer->name : 'Edu Alpha' }}
                                                        </h4>
                                                        <p> Giáo viên</p>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="courses-three__btn-and-heart-box">
                                                <div class="courses-three__btn-box">
                                                    @if ($taiLieu->price > 0)
                                                        <a href="javascript:void(0);" class="themvaogiohangTailieu thm-btn-two" data-id="{{ $taiLieu->id }}" data-slug="{{ $taiLieu->slug }}">
                                                            <span style="background-color: #647bff;">Thêm vào giỏ hàng</span>
                                                            <i class="fas fa-shopping-cart" style="background-color: #647bff;"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Courses Three Single End -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
@endsection
@section('js')
@endsection
