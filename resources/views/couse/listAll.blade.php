@extends('layouts.main.master')
@section('title')
    Khóa học online
@endsection
@section('description')
    Khóa học online luyện thi Toán Thầy Tựu
@endsection
@section('image')
    {{ asset('frontend/images/breamcum.png') }}
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('frontend/css/listall.css') }}">
@endsection
@section('content')
<div class="smart-exam-listing">
    <div class="smart-header">
        <div class="main-container">
            <nav class="breadcrumb-smart">
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
                <span class="separator">›</span>
                <a href="{{ route('couseList') }}">Khóa học</a>
                @if(isset($selectedCategory))
                    <span class="separator">›</span>
                    <span class="current-path">{{ languageName($selectedCategory->name) }}</span>
                @elseif(isset($selectedType))
                    <span class="separator">›</span>
                    <span class="current-path">{{ languageName($selectedType->name) }}</span>
                @elseif(isset($selectedCourseType))
                    <span class="separator">›</span>
                    <span class="current-path">{{ languageName($selectedCourseType->name) }}</span>
                @else
                    <span class="separator">›</span>
                    <span class="current-path">Tất cả khóa học</span>
                @endif
            </nav>
        </div>
    </div>

    <div class="main-container">
        <div class="quick-actions">
            <div class="quick-actions-title">
                <i class="fas fa-folder-open"></i> Danh mục khóa học
                <span class="count-badge">{{ $categories->count() }}</span>
            </div>

            <div class="actions-container">
                <div class="scroll-indicator left hidden" onclick="scrollActions('categories', 'left')">
                    <i class="fas fa-chevron-left"></i>
                </div>

                <div class="actions-grid" id="categories-grid">
                    @foreach($categories as $category)
                        <a href="{{ route('listCategoryMainCourse', ['id' => $category->id]) }}"
                           class="action-btn {{ isset($selectedCategory) && $selectedCategory->id == $category->id ? 'active' : '' }}">
                            <div class="action-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div class="action-label">{{ languageName($category->name) }}</div>
                        </a>
                    @endforeach
                </div>

                <div class="scroll-indicator right hidden" onclick="scrollActions('categories', 'right')">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            @if($categories->count() > 6)
            <div class="show-more-toggle">
                <button class="show-more-btn" type="button" onclick="toggleShowMore('categories')">
                    <span id="categories-toggle-text">Xem thêm</span>
                    <i class="fas fa-chevron-down" id="categories-toggle-icon"></i>
                </button>
            </div>
            @endif
        </div>

        @if($types->count() > 0)
        <div class="quick-actions">
            <div class="quick-actions-title">
                <i class="fas fa-layer-group"></i> Loại khóa học
                <span class="count-badge">{{ $types->count() }}</span>
            </div>

            <div class="actions-container">
                <div class="scroll-indicator left hidden" onclick="scrollActions('types', 'left')">
                    <i class="fas fa-chevron-left"></i>
                </div>

                <div class="actions-grid" id="types-grid">
                    @foreach($types as $type)
                        <a href="{{ route('listTypeCourse', ['id' => $type->id]) }}"
                           class="action-btn {{ isset($selectedType) && $selectedType->id == $type->id ? 'active' : '' }}">
                            <div class="action-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="action-label">{{ languageName($type->name) }}</div>
                        </a>
                    @endforeach
                </div>

                <div class="scroll-indicator right hidden" onclick="scrollActions('types', 'right')">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            @if($types->count() > 6)
            <div class="show-more-toggle">
                <button class="show-more-btn" type="button" onclick="toggleShowMore('types')">
                    <span id="types-toggle-text">Xem thêm</span>
                    <i class="fas fa-chevron-down" id="types-toggle-icon"></i>
                </button>
            </div>
            @endif
        </div>
        @endif

        <div class="content-area">
            @if(isset($coursesByType) && count($coursesByType) > 0)
                @foreach($coursesByType as $typeGroup)
                    @php
                        $groupName = is_object($typeGroup['type']->name)
                            ? languageName($typeGroup['type']->name)
                            : $typeGroup['type']->name;
                    @endphp
                    <div class="exam-type-group" data-type="{{ $loop->index }}">
                        <div class="exam-type-header" onclick="toggleExamType({{ $loop->index }})">
                            <div class="exam-type-title">
                                <i class="fas fa-folder-open"></i>
                                <span>{{ $groupName }}</span>
                                <span class="exam-type-count">{{ $typeGroup['courses']->count() }} khóa học</span>
                            </div>
                            <i class="fas fa-chevron-down exam-type-toggle" id="toggle-{{ $loop->index }}"></i>
                        </div>
                        <div class="exam-type-body" id="exam-body-{{ $loop->index }}" style="display: none;">
                            @if($typeGroup['courses']->count() > 0)
                                <div class="courses-grid-container" id="exam-container-{{ $loop->index }}" data-type-id="{{ $typeGroup['type']->id }}" data-loaded="10">
                                    @foreach($typeGroup['courses'] as $course)
                                        <article class="course-card">
                                            <div class="course-card__media">
                                                <a href="{{ route('couseDetail', ['slug' => $course->slug]) }}">
                                                    <img src="{{ $course->images }}" alt="{{ $course->name }}">
                                                </a>
                                                <span class="course-card__badge">{{ $groupName }}</span>
                                            </div>
                                            <div class="course-card__body">
                                                <h3 class="course-card__title">
                                                    <a href="{{ route('couseDetail', ['slug' => $course->slug]) }}">{{ $course->name }}</a>
                                                </h3>
                                                <p class="course-card__desc">
                                                    {{ \Illuminate\Support\Str::limit(trim(strip_tags($course->description ?? '')), 90) }}
                                                </p>
                                                <div class="course-card__divider"></div>
                                                <ul class="course-card__meta list-unstyled">
                                                    <li>
                                                        <span class="icon-clock"></span>
                                                        <span>{{ $course->thickness ?: 0 }} buổi học</span>
                                                    </li>
                                                    <li>
                                                        <span class="icon-location"></span>
                                                        <span>Online hoặc offline</span>
                                                    </li>
                                                </ul>
                                                <div class="course-card__footer">
                                                    <div class="course-card__price">
                                                        @if ($course->price > 0)
                                                            <strong>{{ number_format($course->price, 0, ',', '.') }}đ</strong>
                                                            @if(($course->discount ?? 0) > $course->price)
                                                                <del>{{ number_format($course->discount, 0, ',', '.') }}đ</del>
                                                            @endif
                                                        @else
                                                            <strong>Miễn phí</strong>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('couseDetail', ['slug' => $course->slug]) }}" class="course-card__btn">Đăng ký</a>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>

                                @if($typeGroup['count'] > 10)
                                <div class="text-center" style="padding: 12px 0 4px;">
                                    <button
                                        class="btn-load-more"
                                        type="button"
                                        data-index="{{ $loop->index }}"
                                        data-type-id="{{ $typeGroup['type']->id }}"
                                        data-total="{{ $typeGroup['count'] }}"
                                        data-loaded="10"
                                        data-group-name="{{ $groupName }}"
                                        onclick="loadMoreCourses({{ $loop->index }})">
                                        <i class="fas fa-chevron-down"></i> Xem thêm (còn {{ $typeGroup['count'] - 10 }} khóa học)
                                    </button>
                                </div>
                                @endif
                            @else
                                <div class="empty-group">
                                    <i class="fas fa-inbox"></i>
                                    <p>Chưa có khóa học nào trong nhóm này</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-exam-type">
                    <i class="fas fa-inbox"></i>
                    <h4>Chưa có khóa học nào</h4>
                    <p>Hãy quay lại sau để xem các khóa học mới</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleExamType(index) {
    const body = document.getElementById(`exam-body-${index}`);
    const toggle = document.getElementById(`toggle-${index}`);

    if (body.style.display === 'none' || body.style.display === '') {
        body.style.display = 'block';
        toggle.classList.remove('collapsed');
    } else {
        body.style.display = 'none';
        toggle.classList.add('collapsed');
    }
}

function loadMoreCourses(index) {
    const container = document.getElementById(`exam-container-${index}`);
    const button = document.querySelector(`button[data-index="${index}"]`);
    const typeId = button.getAttribute('data-type-id');
    const groupName = button.getAttribute('data-group-name') || 'Khóa học';
    const currentLoaded = parseInt(button.getAttribute('data-loaded'));

    button.classList.add('loading');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';

    const categoryId = '{{ isset($selectedCategory) && $selectedCategory ? $selectedCategory->id : "" }}';
    const typeProductId = '{{ isset($selectedType) && $selectedType ? $selectedType->id : "" }}';

    fetch('{{ route("loadMoreCourses") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type_id: typeId,
            offset: currentLoaded,
            per_page: 10,
            category_id: categoryId,
            type_product_id: typeProductId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.courses.length > 0) {
            data.courses.forEach(course => {
                container.insertAdjacentHTML('beforeend', createCourseHTML(course, groupName));
            });

            const newLoaded = currentLoaded + data.courses.length;
            button.setAttribute('data-loaded', newLoaded);

            if (data.has_more) {
                button.classList.remove('loading');
                button.disabled = false;
                button.innerHTML = `<i class="fas fa-chevron-down"></i> Xem thêm (còn ${data.remaining} khóa học)`;
            } else {
                button.style.display = 'none';
            }

            const allItems = container.querySelectorAll('.course-card');
            allItems.forEach((item, idx) => {
                if (idx >= currentLoaded) {
                    item.classList.add('fade-in');
                }
            });
        } else {
            button.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error loading courses:', error);
        button.classList.remove('loading');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-exclamation-circle"></i> Lỗi! Thử lại';
    });
}

function createCourseHTML(course, groupName) {
    const sellPrice = Number(course.price) || 0;
    const oldPrice = Number(course.discount) || 0;
    const priceFormatted = new Intl.NumberFormat('vi-VN').format(sellPrice) + 'đ';
    const oldPriceFormatted = new Intl.NumberFormat('vi-VN').format(oldPrice) + 'đ';
    const detailUrl = `{{ url('/chi-tiet-khoa-hoc-online') }}/${course.slug}.html`;
    const desc = (course.description || '').replace(/<[^>]*>/g, '').trim().slice(0, 90);

    let priceDisplay = '';
    if (sellPrice > 0) {
        priceDisplay = `<strong>${priceFormatted}</strong>`;
        if (oldPrice > sellPrice) {
            priceDisplay += `<del>${oldPriceFormatted}</del>`;
        }
    } else {
        priceDisplay = '<strong>Miễn phí</strong>';
    }

    return `
        <article class="course-card fade-in">
            <div class="course-card__media">
                <a href="${detailUrl}">
                    <img src="${course.images}" alt="${course.name}">
                </a>
                <span class="course-card__badge">${groupName}</span>
            </div>
            <div class="course-card__body">
                <h3 class="course-card__title">
                    <a href="${detailUrl}">${course.name}</a>
                </h3>
                <p class="course-card__desc">${desc}</p>
                <div class="course-card__divider"></div>
                <ul class="course-card__meta list-unstyled">
                    <li>
                        <span class="icon-clock"></span>
                        <span>${course.thickness || 0} buổi học</span>
                    </li>
                    <li>
                        <span class="icon-location"></span>
                        <span>Online hoặc offline</span>
                    </li>
                </ul>
                <div class="course-card__footer">
                    <div class="course-card__price">${priceDisplay}</div>
                    <a href="${detailUrl}" class="course-card__btn">Đăng ký</a>
                </div>
            </div>
        </article>
    `;
}

function scrollActions(type, direction) {
    const grid = document.getElementById(`${type}-grid`);
    const scrollAmount = 200;

    if (direction === 'left') {
        grid.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        grid.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }

    setTimeout(() => updateScrollIndicators(type), 300);
}

function updateScrollIndicators(type) {
    const grid = document.getElementById(`${type}-grid`);
    if (!grid) return;

    const leftIndicator = grid.parentElement.querySelector('.scroll-indicator.left');
    const rightIndicator = grid.parentElement.querySelector('.scroll-indicator.right');
    const scrollLeft = grid.scrollLeft;
    const maxScrollLeft = grid.scrollWidth - grid.clientWidth;

    if (leftIndicator) {
        leftIndicator.classList.toggle('hidden', scrollLeft <= 0);
    }
    if (rightIndicator) {
        rightIndicator.classList.toggle('hidden', scrollLeft >= maxScrollLeft - 10);
    }
}

function toggleShowMore(type) {
    const grid = document.getElementById(`${type}-grid`);
    const toggleText = document.getElementById(`${type}-toggle-text`);
    const toggleIcon = document.getElementById(`${type}-toggle-icon`);

    if (grid.style.maxHeight === '210px' || !grid.style.maxHeight) {
        grid.style.maxHeight = 'none';
        toggleText.textContent = 'Thu gọn';
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
    } else {
        grid.style.maxHeight = '210px';
        toggleText.textContent = 'Xem thêm';
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
    }
}

function initializeScrollIndicators() {
    ['categories', 'types'].forEach(type => {
        const grid = document.getElementById(`${type}-grid`);
        if (grid) {
            updateScrollIndicators(type);
            grid.addEventListener('scroll', () => updateScrollIndicators(type));
            window.addEventListener('resize', () => updateScrollIndicators(type));
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initializeScrollIndicators();

    const firstBody = document.getElementById('exam-body-0');
    const firstToggle = document.getElementById('toggle-0');
    if (firstBody) {
        firstBody.style.display = 'block';
        if (firstToggle) {
            firstToggle.classList.remove('collapsed');
        }
    }

    document.querySelectorAll('.course-card').forEach((item, index) => {
        item.style.animationDelay = `${index * 0.04}s`;
        item.classList.add('fade-in');
    });
});
</script>
@endsection
