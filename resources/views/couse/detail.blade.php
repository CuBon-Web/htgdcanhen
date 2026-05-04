@extends('layouts.main.master')
@section('title')
    {{ $detail->name }}
@endsection
@section('description')
    {{ $detail->description }}
@endsection
@section('image')
    @php
        $noidungkhoahoc = json_decode($detail->size);
        $faq = json_decode($detail->species);
        $khoahoc = json_decode($detail->preserve);
    @endphp
    {{ url('' . $detail->images) }}
@endsection
@section('og_type', 'course')
@section('schema')
    @php
        $noidungSchema = json_decode($detail->size) ?? [];
        $faqSchema = json_decode($detail->species) ?? [];
        $courseTitle = trim($detail->name ?? '');
        $courseDescription = \Illuminate\Support\Str::limit(trim(strip_tags($detail->description ?? '')), 160, '');
        $courseImage = url('' . ($detail->images ?? 'img/logo.png'));
        $courseUrl = request()->fullUrl();
        $courseProvider = $setting->webname ?? $setting->company ?? config('app.name');
        $courseLessons = 0;
        foreach ($noidungSchema as $chapter) {
            if (!empty($chapter->detail_task) && is_array($chapter->detail_task)) {
                $courseLessons += count($chapter->detail_task);
            }
        }
        $coursePrice = isset($detail->price) ? (float) $detail->price : 0;
        $courseFaqEntities = [];
        foreach ($faqSchema as $item) {
            if (!empty($item->chuong) && !empty($item->content)) {
                $courseFaqEntities[] = [
                    '@type' => 'Question',
                    'name' => trim($item->chuong),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => trim(strip_tags($item->content)),
                    ],
                ];
            }
        }

        $courseSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $courseTitle,
            'description' => $courseDescription,
            'image' => [$courseImage],
            'url' => $courseUrl,
            'provider' => [
                '@type' => 'Organization',
                'name' => $courseProvider,
                'sameAs' => url('/'),
            ],
            'numberOfCredits' => (string) $courseLessons,
            'inLanguage' => 'vi-VN',
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'VND',
                'price' => $coursePrice,
                'availability' => 'https://schema.org/InStock',
                'url' => $courseUrl,
            ],
        ];
    @endphp
    <script type="application/ld+json">@json($courseSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
    @if (count($courseFaqEntities) > 0)
        <script type="application/ld+json">
            @json(
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => $courseFaqEntities,
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            )
        </script>
    @endif
@endsection
@section('css')
    <style>
        .course-details__info-box {
            transition: all 0.3s ease-in-out;
            will-change: transform;
        }
    </style>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sticky-sidebar@3.3.1/dist/sticky-sidebar.min.js"></script>
    <script>
        let sidebarInstance = null;

        function initStickySidebar() {
            const isMobile = window.innerWidth < 768;

            if (!isMobile) {
                // Chỉ khởi tạo nếu chưa có
                if (!sidebarInstance) {
                    sidebarInstance = new StickySidebar('#sidebar', {
                        containerSelector: '.course-details .row',
                        innerWrapperSelector: '.sidebar__inner',
                        topSpacing: 120,
                        bottomSpacing: 20,
                        resizeSensor: true
                    });
                }
            } else {
                // Nếu đang ở mobile và đã khởi tạo -> hủy
                if (sidebarInstance) {
                    sidebarInstance.destroy();
                    sidebarInstance = null;
                }
            }
        }

        window.addEventListener('load', initStickySidebar);
        window.addEventListener('resize', initStickySidebar);
    </script>
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
                        <h1>{{ $detail->name }}</h1>
                        <ul class="course-details__ratting list-unstyled">
                            <li>
                                <span class="icon-star"></span>
                            </li>
                            <li>
                                <span class="icon-star"></span>
                            </li>
                            <li>
                                <span class="icon-star"></span>
                            </li>
                            <li>
                                <span class="icon-star"></span>
                            </li>
                            <li>
                                <span class="icon-star"></span>
                            </li>
                        </ul>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li><a href="{{ route('couseList') }}">Khóa học</a></li>
                                <li><span>//</span></li>
                                <li>{{ $detail->name }}</li>
                            </ul>
                        </div>
                        <div class="desc mb-3">
                            {!! $detail->description !!}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="page-header__img">
                            {{-- <img src="{{$danhmuc->avatar}}" alt=""> --}}
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
    <section class="course-details">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-7 order-2 order-lg-1">
                    <div class="course-details__left">
                        <div class="course-details__content">
                            <div class="course-details__main-tab-box tabs-box mb-5">
                                <div class="course-details__overview">
                                    <h3 class="course-details__overview-title mb-3">Bạn sẽ nhận được gì từ khóa học?</h3>
                                    <div class="content-khoahoc">
                                        {!! $detail->content !!}
                                    </div>
                                </div>
                            </div>
                            <div class="course-details__curriculam mb-5">
                                <h3 class="course-details__curriculam-title mb-4">Nội dung chương trình học</h3>
                                <div class="course-details__curriculam-faq">
                                    <div class="accrodion-grp" id="accordion-container" data-grp-name="faq-one-accrodion">
                                        @foreach ($noidungkhoahoc as $key => $item)
                                            <div class="accrodion " style="{{ $key >= 11 ? 'display:none' : '' }}">
                                                <div class="accrodion-title">
                                                    <div class="accrodion-title-box">
                                                        <div class="accrodion-title__count"></div>
                                                        <div class="accrodion-title-text">
                                                            <h4>{{ $item->chuong }}</h4>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $totalSeconds = 0;
                                                        foreach ($item->detail_task as $key => $value) {
                                                            [$minutes, $seconds] = explode(':', $value->time);
                                                            $totalSeconds += $minutes * 60 + $seconds;
                                                        }
                                                        $totalMinutes = $totalSeconds / 60;
                                                    @endphp
                                                    <ul class="accrodion-meta list-unstyled">
                                                        <li>
                                                            <p><span
                                                                    class="icon-book"></span>{{ count($item->detail_task) }}
                                                                Bài học
                                                            </p>
                                                        </li>
                                                        <li>
                                                            <p><span
                                                                    class="icon-clock"></span>{{ round($totalMinutes, 2) }}
                                                                Phút</p>
                                                        </li>
                                                    </ul>

                                                </div>
                                                <div class="accrodion-content">

                                                    <div class="inner">
                                                        <ul class="accrodion-content__points list-unstyled">

                                                            @foreach ($item->detail_task as $task)
                                                                <li>
                                                                    <p class="accrodion-content__points-text">
                                                                        <span
                                                                            class="fas fa-video"></span>{{ $task->name }}
                                                                    </p>
                                                                    @if ($task->status == 0)
                                                                        <div class="accrodion-content__points-btn">
                                                                            <a href="{{ $task->video }}"
                                                                                class="video-popup">Học thử</a>
                                                                        </div>
                                                                    @endif
                                                                    <div class="accrodion-content__icon">
                                                                        <span class="far fa-lock-alt"></span>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                </div>
                                            </div>
                                        @endforeach
                                        <button id="show-more-btn" class="showmore"
                                            style="{{ count($noidungkhoahoc) < 10 ? 'display:none' : '' }}">Xem thêm 10 bài
                                            học</button>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const showMoreBtn = document.getElementById('show-more-btn');
                                                const accrodions = document.querySelectorAll('#accordion-container .accrodion');
                                                let visibleCount = 10;

                                                showMoreBtn.addEventListener('click', function() {
                                                    let shown = 0;

                                                    accrodions.forEach((el, index) => {
                                                        if (index < visibleCount + 10) {
                                                            el.style.display = 'block';
                                                            shown++;
                                                        }
                                                    });

                                                    visibleCount += 10;

                                                    if (visibleCount >= accrodions.length) {
                                                        showMoreBtn.style.display = 'none';
                                                    }
                                                });
                                            });
                                        </script>
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="course-details__curriculam mb-5">
                                <h3 class="course-details__curriculam-title mb-4">Review học viên tại Edu Alpha</h3>
                                <ul class="comment-one__single-list list-unstyled">
                                    @foreach ($socical as $item)
                                        <li>
                                            <div class="comment-one__single">

                                                <div class="comment-one__image-box">
                                                    <div class="comment-one__image">
                                                        <a href="{{ $item->link }}" >
                                                            <img src="{{ $item->avatar }}" alt="">
                                                        </a>
                                                    </div>
                                                </div>
                                                <a href="{{ $item->link }}" >
                                                    <div class="comment-one__content">
                                                        <div class="comment-one__name-box">
                                                            <h4>{{ $item->name }} <span>{{ $item->date }}</span>
                                                            </h4>
                                                        </div>
                                                        <p style="color: black">{{ $item->status }}</p>
                                                    </div>
                                                </a>

                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="course-details__curriculam mb-5">
                                <h3 class="course-details__curriculam-title mb-4">Khóa học khác</h3>
                                <div class="courses-one__carousel_lq owl-theme owl-carousel">
                                    <!--Courses One Single Start-->
                                    @foreach ($productlq as $item)
                                        @php

                                            $noidungkhoahoc = json_decode($item->size);
                                            // $baihoc = 0;
                                            // foreach ($noidungkhoahoc as $key => $value) {
                                            // foreach ($noidungkhoahoc[$key]->detail_task as $i) {
                                            // $baihoc = $baihoc + 1;
                                            // }
                                            // }
                                        @endphp
                                        {{-- <div class="item">
                                        <div class="courses-one__single">
                                            <a href="{{route('couseDetail',['slug'=>$item->slug])}}">
                                                <div class="courses-one__img-box">
                                                    <div class="courses-one__img">
                                                        <img src="{{$item->images}}" alt="">
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
                                                            <p>{{$baihoc}} Lessons</p>
                                                        </li>
                                                        <li>
                                                            <div class="icon">
                                                                <span class="icon-clock"></span>
                                                            </div>
                                                            <p>120h 45min</p>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <h3 class="courses-one__title"><a href="{{route('couseDetail',['slug'=>$item->slug])}}">{{($item->name)}}</a></h3>
                                                        <p class="line_2">{{($item->description)}}</p>
                                                <div class="courses-one__ratting-and-heart-box">

                                                </div>
                                                <div class="courses-one__btn-and-doller-box">
                                                    <div class="courses-one__ratting-box">
                                                        <ul class="courses-one__ratting list-unstyled">
                                                            <li>
                                                                <span class="icon-star"></span>
                                                            </li>
                                                            <li>
                                                                <span class="icon-star"></span>
                                                            </li>
                                                            <li>
                                                                <span class="icon-star"></span>
                                                            </li>
                                                            <li>
                                                                <span class="icon-star"></span>
                                                            </li>
                                                            <li>
                                                                <span class="icon-star"></span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="courses-one__btn-box">
                                                        <a href="{{route('couseDetail',['slug'=>$item->slug])}}" class="courses-one__btn thm-btn"><span
                                                                class="icon-angles-right"></span>Chi tiết</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    @endforeach
                                </div>
                            </div>
                            <div class="course-details__curriculam">
                                <h3 class="course-details__curriculam-title mb-4">Câu hỏi thường gặp</h3>
                                <div class="faq-page__left">
                                    <div class="accrodion-grp faq-one-accrodion" data-grp-name="faq-one-accrodion-1">
                                        @foreach ($faq as $item)
                                            <div class="accrodion">
                                                <div class="accrodion-title">
                                                    <h4>{{ $item->chuong }}</h4>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5 order-1 order-lg-2">
                    <div class="course-details__right sticky-sidebar" id="sidebar">
                        <div class="course-details__info-box">
                            <div class="course-details__video-link">
                                <div class="course-details__video-link-bg"
                                    style="background-image: url('{{ $detail->images }}');">
                                </div>
                            </div>
                            <div class="course-details__info-list">
                                <h3 class="course-details__info-list-title">Khóa học bao gồm:</h3>
                                <ul class="course-details__info-list-1 list-unstyled">
                                    <li>
                                        <p><i class="icon-book"></i>Buổi học</p>
                                        <span>{{ $detail->thickness }}</span>
                                    </li>
                                    <li>
                                        <p><i class="icon-book"></i>Bài học</p>
                                        <span>{{ $detail->ingredient }}</span>
                                    </li>
                                    @foreach ($khoahoc as $item)
                                        <li>
                                            <p><img width="15" src="{{ $item->image }}"
                                                    alt="">{{ $item->title }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="course-details__doller-and-btn-box">
                                <div class="course-details__doller-btn-box">
                                    @php
                                        $isOwner = $profile && $profile->id == $detail->user_id;
                                        $canStudy = !$isOwner && isset($paymendCourse) && $paymendCourse == 1;
                                        $isPending = !$isOwner && isset($paymendCourse) && $paymendCourse == 0;
                                    @endphp


                                    @if ($isOwner || $canStudy)
                                        <a href="{{ route('startStudyCourse', ['id' => $detail->id]) }}" 
                                            class="thm-btn-two">
                                            <span>Bắt đầu học</span>
                                            <i class="icon-angles-right"></i>
                                        </a>
                                    @elseif ($isOwner || $canStudy)
                                        <a href="javascript:;" class="thm-btn-two">
                                            <span>Đơn hàng của bạn đang được duyệt</span>
                                            <i class="icon-angles-right"></i>
                                        </a>
                                    @else
                                        <div class="d-flex gap-2 justify-content-between">
                                            <a data-id="{{ $detail->id }}" data-slug="{{ $detail->slug }}"
                                                href="javascript:void(0);" class="themvaoGioHangKhoaHoc thm-btn"
                                                style="background-color: #647bff;">
                                                <span>Thêm vào giỏ hàng</span>
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                            <a href="{{ route('dangkykhoahoc', ['id' => $detail->id, 'slug' => $detail->slug]) }}"
                                                class="thm-btn">
                                                <span>Mua Khóa Học</span>
                                                <i class="icon-angles-right"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            $('.themvaoGioHangKhoaHoc').click(function() {
                var id = $(this).data('id');
                var slug = $(this).data('slug');
                $.ajax({
                    url: "/khoa-hoc-online/them-vao-gio-hang",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        product_id: id,
                        slug: slug
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.cart-count').html(response.count);
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(response) {
                        toastr.error(response.message);
                    }
                });
            });
        });
    </script>
@endsection
@section('js')
@endsection
