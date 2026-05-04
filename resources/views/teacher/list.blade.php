@extends('layouts.main.master')
@section('title')
    Đội ngũ giáo viên tại Edu Alpha
@endsection
@section('description')
    Là những giáo viên giỏi kiến thức và giỏi truyền đạt. Rất tận tâm với học viên, đi dạy vì cái tâm và luôn khát khao cải
    tiến việc học ở Việt Nam.
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
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
                        <h2>Đội ngũ giáo viên tại Edu Alpha </h2>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Đội ngũ giáo viên</li>
                            </ul>
                        </div>
                        <div class="desc mb-3">
                            <p>Là những giáo viên giỏi kiến thức và giỏi truyền đạt. Rất tận tâm với học viên, đi dạy vì cái
                                tâm và luôn khát khao cải tiến việc học ở Việt Nam.</p>
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
                    <div class="col-lg-4">
                        <div class="page-header__img">
                            <img src="/uploads/images/danh-muc-152671144.png" alt="">
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
    <section class="team-page">
        <div class="container">
            <div class="row">
                @foreach ($list as $item)
                    @php
                        $mota = json_decode($item->description);
                    @endphp
                    <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="team-one__single">
                            <div class="team-one__img-box">
                                <a href="{{ route('detailTeacher', ['slug' => $item->slug]) }}">
                                    <div class="team-one__img">
                                        <img src="{{ $item->images }}" alt="Team Member 1">
                                    </div>
                                    <div class="team-one__content">
                                        <div class="team-one__single-bg-shape"
                                            style="background-image: url(/frontend/images/team-one-single-bg-shape.png);">
                                        </div>
                                        <div class="team-one__content-shape-1">
                                            <img src="/frontend/images/team-one-content-shape-1.png" alt="">
                                        </div>
                                        <div class="team-one__content-shape-2">
                                            <img src="/frontend/images/team-one-content-shape-2.png" alt="">
                                        </div>
                                        <h3 class="team-one__title"><a
                                                href=https://laravel-fistudy.unicktheme.com/instructor-details>{{ $item->name }}</a>
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
                @endforeach
                <!-- Team One Single Start -->

                <!-- Add more team member blocks as needed -->
            </div>
        </div>
    </section>
@endsection
