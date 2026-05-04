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
<style>
    .courses-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }
    
    @media (max-width: 991px) {
        .courses-grid-container {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
    }
    
    @media (max-width: 575px) {
        .courses-grid-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }
    
    .courses-two__single {
        width: 100%;
        margin: 0;
    }
    
    /* Animation cho load more */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .courses-two__single.fade-in {
        animation: fadeInUp 0.5s ease forwards;
    }
</style>
@endsection
@section('content')
<div class="smart-exam-listing">
    <!-- Smart Header -->
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
        <!-- 1. Danh sách Danh mục -->
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
                <button class="show-more-btn" onclick="toggleShowMore('categories')">
                    <span id="categories-toggle-text">Xem thêm</span>
                    <i class="fas fa-chevron-down" id="categories-toggle-icon"></i>
                </button>
                    </div>
            @endif
                            </div>

        <!-- 2. Danh sách Loại khóa học -->
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
                <button class="show-more-btn" onclick="toggleShowMore('types')">
                    <span id="types-toggle-text">Xem thêm</span>
                    <i class="fas fa-chevron-down" id="types-toggle-icon"></i>
                </button>
            </div>
                        @endif
        </div>
                        @endif


        <!-- 3. Danh sách Nhóm khóa học và Khóa học -->
        <div class="content-area">
            <!-- Left: Course Types List -->
            <div style="width: 100%; grid-column: 1 / -1;">
                

                @if(isset($coursesByType) && count($coursesByType) > 0)
                    @foreach($coursesByType as $typeGroup)
                        <div class="exam-type-group" data-type="{{ $loop->index }}">
                            <div class="exam-type-header" onclick="toggleExamType({{ $loop->index }})">
                                <div class="exam-type-title">
                                    <i class="fas fa-folder-open"></i>
                                    <span>{{ ($typeGroup['type']->name) }}</span>
                                    <span class="exam-type-count">{{ $typeGroup['courses']->count() }} khóa học</span>
                    </div>
                                <i class="fas fa-chevron-down exam-type-toggle" id="toggle-{{ $loop->index }}"></i>
                </div>
                            <div class="exam-type-body" id="exam-body-{{ $loop->index }}" style="display: none;">
                                @if($typeGroup['courses']->count() > 0)
                                    <div class="courses-grid-container" id="exam-container-{{ $loop->index }}" data-type-id="{{ $typeGroup['type']->id }}" data-loaded="10">
                                    @foreach($typeGroup['courses'] as $course)
                                        <div class="courses-two__single">
                                            <div class="courses-two__img-box">
                                                <div class="courses-two__img">
                                                    <a href="{{ route('couseDetail', ['slug' => $course->slug]) }}">
                                                        <img src="{{ $course->images }}" alt="{{ $course->name }}">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="courses-two__content">
                                                <div class="courses-two__doller-and-review">
                                                    <div class="courses-two__doller">
                                                        @if ($course->price > 0)
                                                            @if($course->discount > 0)
                                                                <p>{{ number_format($course->price - ($course->price * $course->discount / 100), 0, ',', '.') }}đ
                                                                    <del>{{ number_format($course->price, 0, ',', '.') }}đ</del>
                                                                </p>
                                                            @else
                                                                <p>{{ number_format($course->price, 0, ',', '.') }}đ</p>
                                                            @endif
                                                        @else
                                                            <p>Miễn phí</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <h3 class="courses-two__title">
                                                    <a href="{{ route('couseDetail', ['slug' => $course->slug]) }}">{{ $course->name }}</a>
                                                </h3>
                                                <div class="courses-two__btn-and-client-box">
                                                    <div class="courses-two__btn-box">
                                                        <a href="{{ route('couseDetail', ['slug' => $course->slug]) }}" class="thm-btn-two">
                                                            <span>Chi tiết</span>
                                                            <i class="icon-angles-right"></i>
                                                        </a>
                                                    </div>
                                            <div class="courses-two__client-box">
                                                <div class="courses-two__client-img">
                                                            @if ($course->user_id == 0)
                                                                <img src="{{ url('frontend/images/user_icon.png') }}" alt="">
                                                    @else
                                                                <img src="{{ $course->customer && $course->customer->avatar ? url('uploads/images/' . $course->customer->avatar) : url('frontend/images/user_icon.png') }}"
                                                            alt="">
                                                    @endif
                                                </div>
                                                <div class="courses-two__client-content">
                                                            <h4>{{ $course->user_id == 0 ? 'Quản trị viên' : $course->customer->name }}</h4>
                                                            <p>Giáo viên</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <ul class="courses-two__meta list-unstyled">
                                                    <li>
                                                        <div class="icon">
                                                            <span class="icon-book"></span>
                                                        </div>
                                                        <p>{{ $course->ingredient ?? 0 }} Bài học</p>
                                                    </li>
                                                    <li>
                                                        <div class="icon">
                                                            <span class="icon-clock"></span>
                                                        </div>
                                                        <p>{{ $course->thickness ?? 0 }} Buổi học</p>
                                                    </li>
                                                    <li>
                                                        <div class="icon">
                                                            <span class="icon-book"></span>
                                            </div>
                                                        <p>Bài tập online</p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                    
                                    @if($typeGroup['count'] > 10)
                                    <div class="text-center" style="padding: 20px;">
                                        <button 
                                            class="btn-load-more" 
                                            data-index="{{ $loop->index }}"
                                            data-type-id="{{ $typeGroup['type']->id }}"
                                            data-total="{{ $typeGroup['count'] }}"
                                            data-loaded="10"
                                            onclick="loadMoreCourses({{ $loop->index }})">
                                            <i class="fas fa-chevron-down"></i> Xem thêm (còn {{ $typeGroup['count'] - 10 }} khóa học)
                                        </button>
                        </div>
                        @endif
                    @else
                                    <div style="text-align: center; padding: 30px 20px; color: var(--secondary-color);">
                                        <i class="fas fa-inbox" style="font-size: 36px; opacity: 0.5; margin-bottom: 10px;"></i>
                                        <p style="margin: 0;">Chưa có khóa học nào trong nhóm này</p>
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
                    </div>

<script>
// Toggle course type accordion
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

// Load more courses từ server (AJAX)
function loadMoreCourses(index) {
    const container = document.getElementById(`exam-container-${index}`);
    const button = document.querySelector(`button[data-index="${index}"]`);
    const typeId = button.getAttribute('data-type-id');
    const currentLoaded = parseInt(button.getAttribute('data-loaded'));
    const total = parseInt(button.getAttribute('data-total'));
    const perPage = 10;
    
    // Disable button
    button.classList.add('loading');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
    
    // Get filter parameters
    const categoryId = '{{ isset($selectedCategory) && $selectedCategory ? $selectedCategory->id : "" }}';
    const typeProductId = '{{ isset($selectedType) && $selectedType ? $selectedType->id : "" }}';
    
    // AJAX call to server
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
            // Render new courses
            data.courses.forEach(course => {
                const courseHTML = createCourseHTML(course);
                container.insertAdjacentHTML('beforeend', courseHTML);
            });
            
            // Update loaded count
            const newLoaded = currentLoaded + data.courses.length;
            button.setAttribute('data-loaded', newLoaded);
            
            // Check if there are more courses
            if (data.has_more) {
                button.classList.remove('loading');
                button.disabled = false;
                button.innerHTML = `<i class="fas fa-chevron-down"></i> Xem thêm (còn ${data.remaining} khóa học)`;
            } else {
                button.style.display = 'none';
            }
            
            // Add animation to new items
            const allItems = container.querySelectorAll('.courses-two__single');
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

// Create course HTML
function createCourseHTML(course) {
    const hasDiscount = course.discount > 0;
    const finalPrice = hasDiscount ? course.price - (course.price * course.discount / 100) : course.price;
    const priceFormatted = new Intl.NumberFormat('vi-VN').format(finalPrice) + 'đ';
    const originalPriceFormatted = new Intl.NumberFormat('vi-VN').format(course.price) + 'đ';
    
    // Teacher info
    const teacherAvatar = course.customer.avatar;
    const teacherName = course.customer.name;
    
    // Price display
    let priceDisplay = '';
    if (course.price > 0) {
        if (hasDiscount) {
            priceDisplay = `<p>${priceFormatted} <del>${originalPriceFormatted}</del></p>`;
        } else {
            priceDisplay = `<p>${priceFormatted}</p>`;
        }
    } else {
        priceDisplay = '<p>Miễn phí</p>';
    }
    
    // Button display
    const buttonHTML = `<a href="{{ url('/chi-tiet-khoa-hoc-online') }}/${course.slug}.html" class="thm-btn-two">
        <span>Chi tiết</span>
        <i class="icon-angles-right"></i>
    </a>`;
    
    return `
        <div class="courses-two__single">
            <div class="courses-two__img-box">
                <div class="courses-two__img">
                    <a href="{{ url('/chi-tiet-khoa-hoc-online') }}/${course.slug}.html">
                        <img src="${course.images}" alt="${course.name}">
                    </a>
                </div>
            </div>
            <div class="courses-two__content">
                <div class="courses-two__doller-and-review">
                    <div class="courses-two__doller">
                        ${priceDisplay}
                    </div>
                </div>
                <h3 class="courses-two__title">
                    <a href="{{ url('/chi-tiet-khoa-hoc-online') }}/${course.slug}.html">${course.name}</a>
                </h3>
                <div class="courses-two__btn-and-client-box">
                    <div class="courses-two__btn-box">
                        ${buttonHTML}
                    </div>
                    <div class="courses-two__client-box">
                        <div class="courses-two__client-img">
                            <img src="${teacherAvatar}" alt="${teacherName}">
                        </div>
                        <div class="courses-two__client-content">
                            <h4>${teacherName}</h4>
                            <p>Giáo viên</p>
                        </div>
                    </div>
                </div>
                <ul class="courses-two__meta list-unstyled">
                    <li>
                        <div class="icon">
                            <span class="icon-book"></span>
                        </div>
                        <p>${course.ingredient || 0} Bài học</p>
                    </li>
                    <li>
                        <div class="icon">
                            <span class="icon-clock"></span>
                        </div>
                        <p>${course.thickness || 0} Buổi học</p>
                    </li>
                    <li>
                        <div class="icon">
                            <span class="icon-book"></span>
                        </div>
                        <p>Bài tập online</p>
                    </li>
                </ul>
            </div>
        </div>
    `;
}

// Scroll Actions for Large Data Sets
function scrollActions(type, direction) {
    const grid = document.getElementById(`${type}-grid`);
    const scrollAmount = 200;
    
    if (direction === 'left') {
        grid.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        grid.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
    
    // Update scroll indicators after scroll
    setTimeout(() => updateScrollIndicators(type), 300);
}

// Update scroll indicators visibility
function updateScrollIndicators(type) {
    const grid = document.getElementById(`${type}-grid`);
    const leftIndicator = grid.parentElement.querySelector('.scroll-indicator.left');
    const rightIndicator = grid.parentElement.querySelector('.scroll-indicator.right');
    
    const scrollLeft = grid.scrollLeft;
    const maxScrollLeft = grid.scrollWidth - grid.clientWidth;
    
    // Show/hide left indicator
    if (scrollLeft > 0) {
        leftIndicator.classList.remove('hidden');
    } else {
        leftIndicator.classList.add('hidden');
    }
    
    // Show/hide right indicator
    if (scrollLeft < maxScrollLeft - 10) {
        rightIndicator.classList.remove('hidden');
    } else {
        rightIndicator.classList.add('hidden');
    }
}

// Toggle show more/less for actions
function toggleShowMore(type) {
    const grid = document.getElementById(`${type}-grid`);
    const toggleText = document.getElementById(`${type}-toggle-text`);
    const toggleIcon = document.getElementById(`${type}-toggle-icon`);
    
    if (grid.style.maxHeight === '200px' || !grid.style.maxHeight) {
        // Show all
        grid.style.maxHeight = 'none';
        toggleText.textContent = 'Thu gọn';
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
    } else {
        // Show limited
        grid.style.maxHeight = '200px';
        toggleText.textContent = 'Xem thêm';
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
    }
}

// Initialize scroll indicators on page load
function initializeScrollIndicators() {
    ['categories', 'types'].forEach(type => {
        const grid = document.getElementById(`${type}-grid`);
        if (grid) {
            updateScrollIndicators(type);
            
            // Update indicators on scroll
            grid.addEventListener('scroll', () => updateScrollIndicators(type));
            
            // Update indicators on resize
            window.addEventListener('resize', () => updateScrollIndicators(type));
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize scroll indicators
    initializeScrollIndicators();
    
    // Mở accordion đầu tiên mặc định
    const firstBody = document.getElementById('exam-body-0');
    const firstToggle = document.getElementById('toggle-0');
    if (firstBody) {
        firstBody.style.display = 'block';
        if (firstToggle) {
            firstToggle.classList.remove('collapsed');
        }
    }

    // Smooth scroll cho các link
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation to course items
    const courseItems = document.querySelectorAll('.courses-two__single');
    courseItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.05}s`;
        item.classList.add('fade-in');
    });
});
</script>
@endsection
