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
        $courseReviewList = json_decode($detail->hang_muc ?? '[]', true);
        if (!is_array($courseReviewList)) {
            $courseReviewList = [];
        }
        $courseReviewList = array_values(array_filter($courseReviewList, function ($review) {
            $hasContent = !empty($review['name']) || !empty($review['content']);
            $hasStar = !empty($review['star']);
            return $hasContent && $hasStar;
        }));
        $courseReviewCount = count($courseReviewList);
        $courseReviewAverage = 0;
        if ($courseReviewCount > 0) {
            $courseReviewTotal = 0;
            foreach ($courseReviewList as $review) {
                $courseReviewTotal += (float) ($review['star'] ?? 0);
            }
            $courseReviewAverage = round($courseReviewTotal / $courseReviewCount, 1);
        }
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
        if ($courseReviewCount > 0) {
            $courseSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $courseReviewAverage,
                'reviewCount' => (string) $courseReviewCount,
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }
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

        $faqPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $courseFaqEntities,
        ];
    @endphp
    <script type="application/ld+json">@json($courseSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
    @if (count($courseFaqEntities) > 0)
        <script type="application/ld+json">@json($faqPageSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
    @endif
@endsection
@section('css')
@endsection
@section('js')
<script>
(function () {
    var sidebar = document.getElementById('sidebar');
    var row = document.querySelector('.course-details--page .course-details__layout-row');
    if (!sidebar || !row) {
        return;
    }

    var col = sidebar.closest('.course-details__sidebar-col');
    if (!col) {
        return;
    }

    var spacer = document.createElement('div');
    spacer.className = 'course-details__sidebar-spacer';
    sidebar.insertAdjacentElement('afterend', spacer);

    var ticking = false;

    function isDesktop() {
        return window.innerWidth >= 992;
    }

    function getOffsetTop() {
        var stickyHeader = document.querySelector('.stricky-header.stricky-fixed');
        return stickyHeader ? stickyHeader.offsetHeight + 16 : 24;
    }

    function resetSidebar() {
        sidebar.classList.remove('is-fixed', 'is-bottom');
        sidebar.style.position = '';
        sidebar.style.top = '';
        sidebar.style.left = '';
        sidebar.style.width = '';
        sidebar.style.bottom = '';
        spacer.style.display = 'none';
        spacer.style.height = '0';
        col.style.minHeight = '';
    }

    function syncColHeight() {
        if (!isDesktop()) {
            col.style.minHeight = '';
            return;
        }
        col.style.minHeight = row.offsetHeight + 'px';
    }

    function updateSidebar() {
        if (!isDesktop()) {
            resetSidebar();
            return;
        }

        syncColHeight();

        var offsetTop = getOffsetTop();
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;
        var rowTop = row.getBoundingClientRect().top + scrollY;
        var sidebarHeight = sidebar.offsetHeight;
        var colRect = col.getBoundingClientRect();
        var stickStart = rowTop - offsetTop;
        var stickEnd = rowTop + row.offsetHeight - sidebarHeight - offsetTop;

        if (scrollY < stickStart) {
            resetSidebar();
            syncColHeight();
            return;
        }

        if (scrollY >= stickEnd) {
            sidebar.classList.remove('is-fixed');
            sidebar.classList.add('is-bottom');
            sidebar.style.position = 'absolute';
            sidebar.style.top = (row.offsetHeight - sidebarHeight) + 'px';
            sidebar.style.left = '0';
            sidebar.style.width = '100%';
            sidebar.style.bottom = 'auto';
            spacer.style.display = 'none';
            spacer.style.height = '0';
            return;
        }

        sidebar.classList.add('is-fixed');
        sidebar.classList.remove('is-bottom');
        sidebar.style.position = 'fixed';
        sidebar.style.top = offsetTop + 'px';
        sidebar.style.left = colRect.left + 'px';
        sidebar.style.width = colRect.width + 'px';
        sidebar.style.bottom = 'auto';
        spacer.style.display = 'block';
        spacer.style.height = sidebarHeight + 'px';
    }

    function scheduleUpdate() {
        if (ticking) {
            return;
        }
        ticking = true;
        window.requestAnimationFrame(function () {
            updateSidebar();
            ticking = false;
        });
    }

    window.addEventListener('scroll', scheduleUpdate, { passive: true });
    window.addEventListener('resize', scheduleUpdate);
    window.addEventListener('load', scheduleUpdate);

    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(scheduleUpdate);
    }

    scheduleUpdate();
})();
</script>
@endsection

@section('content')
    <section class="page-header page-header--course">
        <div class="page-header__bg"></div>
        <div class="container">
            <div class="page-header__inner">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <div class="page-header__content">
                            <div class="thm-breadcrumb__box">
                                <ul class="thm-breadcrumb list-unstyled">
                                    <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                    <li><span>/</span></li>
                                    <li><a href="{{ route('couseList') }}">Khóa học</a></li>
                                    <li><span>/</span></li>
                                    <li class="is-current">{{ $detail->name }}</li>
                                </ul>
                            </div>
                            <h1>{{ $detail->name }}</h1>
                            @if ($courseReviewCount > 0)
                                <div class="page-header__rating">
                                    <div class="page-header__stars">
                                        @for ($starIndex = 1; $starIndex <= 5; $starIndex++)
                                            <span class="icon-star {{ $starIndex > round($courseReviewAverage) ? 'course-review-star--muted' : '' }}"></span>
                                        @endfor
                                    </div>
                                    <strong>{{ number_format($courseReviewAverage, 1) }}</strong>
                                    <span>({{ $courseReviewCount }} đánh giá)</span>
                                </div>
                            @endif
                            <ul class="page-header__meta list-unstyled">
                                <li>
                                    <span class="icon-clock"></span>
                                    <span>{{ $detail->thickness ?: 0 }} buổi học</span>
                                </li>
                                <li>
                                    <span class="icon-book"></span>
                                    <span>{{ $detail->ingredient ?: 0 }} bài học</span>
                                </li>
                            </ul>
                            @if (trim(strip_tags($detail->description ?? '')))
                                <div class="page-header__desc">
                                    {{ \Illuminate\Support\Str::limit(trim(strip_tags($detail->description)), 180) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="page-header__media">
                            <img src="{{ $detail->images }}" alt="{{ $detail->name }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="course-details course-details--page">
        <div class="container">
            <div class="row align-items-start course-details__layout-row">
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
                                    <div class="accrodion-grp faq-one-accrodion" id="accordion-container" data-grp-name="faq-one-accrodion">
                                        @foreach ($noidungkhoahoc as $key => $item)
                                            <div class="accrodion{{ $key === 0 ? ' active' : '' }}" style="{{ $key >= 10 ? 'display:none' : '' }}">
                                                <div class="accrodion-title">
                                                    <div class="accrodion-title-box">
                                                        <div class="accrodion-title__count"></div>
                                                        <div class="accrodion-title-text">
                                                            <h4>{{ $item->chuong }}</h4>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $totalSeconds = 0;
                                                        foreach ($item->detail_task as $taskItem) {
                                                            $timeParts = explode(':', $taskItem->time ?? '0:0');
                                                            $minutes = (int) ($timeParts[0] ?? 0);
                                                            $seconds = (int) ($timeParts[1] ?? 0);
                                                            $totalSeconds += $minutes * 60 + $seconds;
                                                        }
                                                        $totalMinutes = $totalSeconds / 60;
                                                    @endphp
                                                    <ul class="accrodion-meta list-unstyled">
                                                        <li>
                                                            <p>
                                                                <span class="icon-book"></span>
                                                                {{ count($item->detail_task) }} Bài học
                                                            </p>
                                                        </li>
                                                        <li>
                                                            <p>
                                                                <span class="icon-clock"></span>
                                                                {{ round($totalMinutes) }} Phút
                                                            </p>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="accrodion-content" @if($key === 0) style="display:block" @endif>
                                                    <div class="inner">
                                                        <ul class="accrodion-content__points list-unstyled">
                                                            @foreach ($item->detail_task as $task)
                                                                <li>
                                                                    <p class="accrodion-content__points-text">
                                                                        <span class="fas fa-video"></span>{{ $task->name }}
                                                                    </p>
                                                                    @if ($task->status == 0)
                                                                        <div class="accrodion-content__points-btn">
                                                                            <a href="{{ $task->video }}" class="video-popup">Học thử</a>
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
                                        <button id="show-more-btn" class="showmore" type="button"
                                            style="{{ count($noidungkhoahoc) <= 10 ? 'display:none' : '' }}">
                                            Xem thêm chương học
                                        </button>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const showMoreBtn = document.getElementById('show-more-btn');
                                                const accrodions = document.querySelectorAll('#accordion-container .accrodion');
                                                let visibleCount = 10;

                                                if (!showMoreBtn) {
                                                    return;
                                                }

                                                showMoreBtn.addEventListener('click', function() {
                                                    accrodions.forEach((el, index) => {
                                                        if (index < visibleCount + 10) {
                                                            el.style.display = 'block';
                                                        }
                                                    });

                                                    visibleCount += 10;

                                                    if (visibleCount >= accrodions.length) {
                                                        showMoreBtn.style.display = 'none';
                                                    }
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="course-details__curriculam mb-5 course-reviews">
                                <div class="course-reviews__head">
                                    <h3 class="course-details__curriculam-title mb-0">Review học viên tại Cánh Én</h3>
                                    @if ($courseReviewCount > 0)
                                        <div class="course-reviews__summary">
                                            <span class="course-reviews__score">{{ number_format($courseReviewAverage, 1) }}</span>
                                            <div class="course-reviews__stars" aria-label="{{ number_format($courseReviewAverage, 1) }} trên 5 sao">
                                                @for ($starIndex = 1; $starIndex <= 5; $starIndex++)
                                                    <span class="icon-star {{ $starIndex > round($courseReviewAverage) ? 'course-review-star--muted' : '' }}"></span>
                                                @endfor
                                            </div>
                                            <span class="course-reviews__count">{{ $courseReviewCount }} đánh giá</span>
                                        </div>
                                    @endif
                                </div>

                                @if ($courseReviewCount > 0)
                                    <div class="course-reviews__list">
                                        @foreach ($courseReviewList as $review)
                                            @php
                                                $reviewAvatar = !empty($review['avatar'])
                                                    ? $review['avatar']
                                                    : url('frontend/images/user_icon.png');
                                                $reviewDateText = '';
                                                if (!empty($review['feedback_at'])) {
                                                    try {
                                                        $reviewDateText = \Carbon\Carbon::parse($review['feedback_at'])->format('d/m/Y H:i');
                                                    } catch (\Exception $exception) {
                                                        $reviewDateText = $review['feedback_at'];
                                                    }
                                                }
                                                $reviewStar = (int) ($review['star'] ?? 5);
                                            @endphp
                                            <article class="course-review-card">
                                                <div class="course-review-card__avatar">
                                                    <img src="{{ $reviewAvatar }}" alt="{{ $review['name'] ?? 'Học viên' }}">
                                                </div>
                                                <div class="course-review-card__body">
                                                    <div class="course-review-card__top">
                                                        <div class="course-review-card__info">
                                                            <h4 class="course-review-card__name">{{ $review['name'] ?? 'Học viên' }}</h4>
                                                            <div class="course-review-card__meta">
                                                                @if (!empty($review['class_name']))
                                                                    <span><i class="icon-book"></i>{{ $review['class_name'] }}</span>
                                                                @endif
                                                                @if (!empty($review['address']))
                                                                    <span><i class="icon-location"></i>{{ $review['address'] }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="course-review-card__rating">
                                                            <div class="course-review-card__stars">
                                                                @for ($starIndex = 1; $starIndex <= 5; $starIndex++)
                                                                    <span class="icon-star {{ $starIndex > $reviewStar ? 'course-review-star--muted' : '' }}"></span>
                                                                @endfor
                                                            </div>
                                                            @if ($reviewDateText)
                                                                <time class="course-review-card__date">{{ $reviewDateText }}</time>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if (!empty($review['content']))
                                                        <p class="course-review-card__content">{{ $review['content'] }}</p>
                                                    @endif
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="course-reviews__empty">
                                        Chưa có đánh giá nào cho khóa học này.
                                    </div>
                                @endif
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
                <div class="col-xl-4 col-lg-5 order-1 order-lg-2 course-details__sidebar-col">
                    <aside class="course-details__right course-details__sidebar" id="sidebar">
                        <div class="course-details__info-box course-details__info-box--modern">
                            

                            <div class="course-details__price-box">
                                @if ($detail->price > 0)
                                    <span class="course-details__price-current">{{ number_format($detail->price) }}đ</span>
                                    @if (!empty($detail->discount) && $detail->discount > $detail->price)
                                        <span class="course-details__price-old">{{ number_format($detail->discount) }}đ</span>
                                    @endif
                                @else
                                    <span class="course-details__price-current">Miễn phí</span>
                                @endif
                            </div>

                            <div class="course-details__info-list">
                                <h3 class="course-details__info-list-title">Khóa học bao gồm</h3>
                                <ul class="course-details__info-list-1 list-unstyled">
                                    <li>
                                        <p><i class="icon-clock"></i><span>Buổi học</span></p>
                                        <span>{{ $detail->thickness ?: 0 }}</span>
                                    </li>
                                    <li>
                                        <p><i class="icon-book"></i><span>Bài học</span></p>
                                        <span>{{ $detail->ingredient ?: 0 }}</span>
                                    </li>
                                    @foreach ($khoahoc as $item)
                                        <li>
                                            <p>
                                                @if (!empty($item->image))
                                                    <img src="{{ $item->image }}" alt="">
                                                @else
                                                    <i class="icon-book"></i>
                                                @endif
                                                <span>{{ $item->title }}</span>
                                            </p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="course-details__actions">
                                @php
                                    $isOwner = $profile && $profile->id == $detail->user_id;
                                    $canStudy = !$isOwner && isset($paymendCourse) && $paymendCourse == 1;
                                    $isPending = !$isOwner && isset($paymendCourse) && $paymendCourse == 0;
                                @endphp

                                @if ($isOwner || $canStudy)
                                    <a href="{{ route('startStudyCourse', ['id' => $detail->id]) }}" class="course-details__btn course-details__btn--primary thm-btn-two">
                                        <span>Bắt đầu học</span>
                                        <i class="icon-angles-right"></i>
                                    </a>
                                @elseif ($isPending)
                                    <a href="javascript:;" class="course-details__btn course-details__btn--pending thm-btn-two">
                                        <span>Đơn hàng đang được duyệt</span>
                                        <i class="icon-angles-right"></i>
                                    </a>
                                @else
                                    <div class="course-details__btn-group">
                                        <a data-id="{{ $detail->id }}" data-slug="{{ $detail->slug }}"
                                            href="javascript:void(0);" class="course-details__btn course-details__btn--cart themvaoGioHangKhoaHoc">
                                            <span>Thêm giỏ hàng</span>
                                            <i class="fas fa-shopping-cart"></i>
                                        </a>
                                        <a href="{{ route('dangkykhoahoc', ['id' => $detail->id, 'slug' => $detail->slug]) }}"
                                            class="course-details__btn course-details__btn--buy thm-btn">
                                            <span>Mua khóa học</span>
                                            <i class="icon-angles-right"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </aside>
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
