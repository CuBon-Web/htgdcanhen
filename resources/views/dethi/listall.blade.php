@extends('layouts.main.master')
@section('title')
    Danh sách đề thi
@endsection
@section('description')
    Danh sách đề thi
@endsection
@section('image')
    {{ asset('frontend/images/breamcum.png') }}
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('frontend/css/listall.css') }}">
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
                <a href="{{ route('allDeThi') }}">Đề thi</a>
                @if(isset($selectedGrade))
                    <span class="separator">›</span>
                    <span class="current-path">{{ $selectedGrade->name }}</span>
                @elseif(isset($selectedSubject))
                    <span class="separator">›</span>
                    <span class="current-path">{{ $selectedSubject->name }}</span>
                @elseif(isset($selectedExamType))
                    <span class="separator">›</span>
                    <span class="current-path">{{ $selectedExamType->name }}</span>
                @else
                    <span class="separator">›</span>
                    <span class="current-path">Tất cả đề thi</span>
                @endif
            </nav>
        </div>
    </div>

    <div class="main-container">
        <!-- 1. Danh sách Khối lớp -->
        <div class="quick-actions">
            <div class="quick-actions-title">
                <i class="fas fa-graduation-cap"></i> Khối lớp
                <span class="count-badge">{{ $grades->count() }}</span>
            </div>

            <div class="actions-container">
                <div class="scroll-indicator left hidden" onclick="scrollActions('grades', 'left')">
                    <i class="fas fa-chevron-left"></i>
                </div>
                
                <div class="actions-grid" id="grades-grid">
                @foreach($grades as $grade)
                    <a href="{{ route('listCategorymainDethi', ['id' => $grade->id]) }}" 
                       class="action-btn {{ isset($selectedGrade) && $selectedGrade->id == $grade->id ? 'active' : '' }}">
                    <div class="action-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="action-label">{{ $grade->name }}</div>
                </a>
                @endforeach
            </div>
                
                <div class="scroll-indicator right hidden" onclick="scrollActions('grades', 'right')">
                    <i class="fas fa-chevron-right"></i>
        </div>
            </div>

            @if($grades->count() > 6)
            <div class="show-more-toggle">
                <button class="show-more-btn" onclick="toggleShowMore('grades')">
                    <span id="grades-toggle-text">Xem thêm</span>
                    <i class="fas fa-chevron-down" id="grades-toggle-icon"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- 2. Danh sách Môn học -->
        @if($subjects->count() > 0)
        <div class="quick-actions">
            <div class="quick-actions-title">
                <i class="fas fa-book"></i> Môn học
                <span class="count-badge">{{ $subjects->count() }}</span>
                @if(isset($selectedGrade))
                    <small style="font-size: 12px; font-weight: normal; opacity: 0.8;">({{ $selectedGrade->name }})</small>
                @endif
            </div>

            <div class="actions-container">
                <div class="scroll-indicator left hidden" onclick="scrollActions('subjects', 'left')">
                    <i class="fas fa-chevron-left"></i>
                </div>
                
                <div class="actions-grid" id="subjects-grid">
                @foreach($subjects as $subject)
                    <a href="{{ route('listCategoryDethi', ['id' => $subject->id]) }}" 
                       class="action-btn {{ isset($selectedSubject) && $selectedSubject->id == $subject->id ? 'active' : '' }}">
                    <div class="action-icon">
                            <i class="fas fa-book-open"></i>
                    </div>
                    <div class="action-label">{{ $subject->name }}</div>
                </a>
                @endforeach
            </div>
                
                <div class="scroll-indicator right hidden" onclick="scrollActions('subjects', 'right')">
                    <i class="fas fa-chevron-right"></i>
                </div>
        </div>

            @if($subjects->count() > 6)
            <div class="show-more-toggle">
                <button class="show-more-btn" onclick="toggleShowMore('subjects')">
                    <span id="subjects-toggle-text">Xem thêm</span>
                    <i class="fas fa-chevron-down" id="subjects-toggle-icon"></i>
                </button>
            </div>
                        @endif
        </div>
                        @endif


        <!-- 4. Danh sách Bộ đề và Đề thi -->
        <div class="content-area">
            <!-- Left: Exam Types List -->
            <div style="width: 100%; grid-column: 1 / -1;">
                

                @if(isset($examsByType) && count($examsByType) > 0)
                    @foreach($examsByType as $typeGroup)
                        <div class="exam-type-group" data-type="{{ $loop->index }}">
                            <div class="exam-type-header" onclick="toggleExamType({{ $loop->index }})">
                                <div class="exam-type-title">
                                    <i class="fas fa-folder-open"></i>
                                    <span>{{ $typeGroup['type']->name }}</span>
                                    <span class="exam-type-count">{{ $typeGroup['exams']->count() }} đề thi</span>
                    </div>
                                <i class="fas fa-chevron-down exam-type-toggle" id="toggle-{{ $loop->index }}"></i>
                </div>
                            <div class="exam-type-body" id="exam-body-{{ $loop->index }}" style="display: none;">
                                @if($typeGroup['exams']->count() > 0)
                                    <div class="exam-items-container" id="exam-container-{{ $loop->index }}" data-type-id="{{ $typeGroup['type']->id }}" data-loaded="10">
                                    @foreach($typeGroup['exams'] as $exam)
                            <div class="exam-item-smart">
                                <div class="exam-icon-smart">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="exam-content-smart">
                                    <div class="exam-title-smart">
                                        {{ $exam->title }}
                                        @if($exam->pricing_type == 'paid')
                                            <span class="badge badge-warning ml-2">
                                                <i class="fas fa-crown"></i> Trả phí
                                            </span>
                                        @else
                                            <span class="badge badge-success ml-2">
                                                <i class="fas fa-gift"></i> Miễn phí
                                            </span>
                                        @endif
                                    </div>
                                    <div class="exam-meta-smart">
                                        <div class="exam-meta-item-smart">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $exam->time }} phút</span>
                                        </div>
                                        <div class="exam-meta-item-smart">
                                            <i class="fas fa-question-circle"></i>
                                            <span>{{ $exam->parts->sum(function($part) { return $part->questions->count(); }) }} câu</span>
                                        </div>
                                        <div class="exam-meta-item-smart">
                                            <i class="fas fa-graduation-cap"></i>
                                            <span>{{ $exam->gradeCategory ? $exam->gradeCategory->name : 'N/A' }}</span>
                                        </div>
                                        <div class="exam-meta-item-smart">
                                            <i class="fas fa-book"></i>
                                            <span>{{ $exam->subjectCategory ? $exam->subjectCategory->name : 'N/A' }}</span>
                                        </div>
                                        @if($exam->pricing_type == 'paid' && $exam->price > 0)
                                        <div class="exam-meta-item-smart">
                                            <i class="fas fa-tag"></i>
                                            <span class="text-danger font-weight-bold">{{ number_format($exam->price, 0, ',', '.') }}đ</span>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Teacher Info -->
                                            <div class="exam-teacher-info">
                                                @if($exam->customer)
                                                    <img class="exam-teacher-avatar" 
                                                         src="{{ $exam->customer->avatar != null ? url('uploads/images/' . $exam->customer->avatar) : url('frontend/images/user_icon.png') }}" 
                                                         alt="{{ $exam->customer->name }}">
                                                    <div>
                                                        <div class="exam-teacher-name">{{ $exam->customer->name }}</div>
                                                        <div class="exam-teacher-role">Giáo viên</div>
                                                    </div>
                                                @else
                                                    <img class="exam-teacher-avatar" 
                                                         src="{{ url('frontend/images/user_icon.png') }}" 
                                                         alt="Admin">
                                                    <div>
                                                        <div class="exam-teacher-name">Quản trị viên</div>
                                                        <div class="exam-teacher-role">Giáo viên</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @if($exam->pricing_type == 'paid')
                                            <a href="javascript:void(0)" class="exam-button-smart themvaogiohangDethi" style="background: #ffc107;" data-id="{{ $exam->id }}">
                                        <i class="fas fa-shopping-cart"></i>
                                                Thêm giỏ hàng
                                    </a>
                                @else
                                    <a href="{{ route('detailDeThi', ['id' => $exam->id]) }}" class="exam-button-smart">
                                        <i class="fas fa-play"></i>
                                        Thi ngay
                                    </a>
                                @endif
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
                                            onclick="loadMoreExams({{ $loop->index }})">
                                            <i class="fas fa-chevron-down"></i> Xem thêm (còn {{ $typeGroup['count'] - 10 }} đề thi)
                                        </button>
                        </div>
                        @endif
                    @else
                                    <div style="text-align: center; padding: 30px 20px; color: var(--secondary-color);">
                                        <i class="fas fa-inbox" style="font-size: 36px; opacity: 0.5; margin-bottom: 10px;"></i>
                                        <p style="margin: 0;">Chưa có đề thi nào trong bộ đề này</p>
                        </div>
                    @endif
                </div>
            </div>
                            @endforeach
                @else
                    <div class="no-exam-type">
                        <i class="fas fa-inbox"></i>
                        <h4>Chưa có đề thi nào</h4>
                        <p>Hãy quay lại sau để xem các đề thi mới</p>
                        </div>
                @endif
                    </div>

                        </div>

                        </div>
                    </div>

<script>
// Toggle exam type accordion
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

// Load more exams từ server (AJAX)
function loadMoreExams(index) {
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
    const gradeId = '{{ $selectedGrade ? $selectedGrade->id : "" }}';
    const subjectId = '{{ $selectedSubject ? $selectedSubject->id : "" }}';
    
    // AJAX call to server
    fetch('{{ route("loadMoreExams") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type_id: typeId,
            offset: currentLoaded,
            per_page: 10,
            grade_id: gradeId,
            subject_id: subjectId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.exams.length > 0) {
            // Render new exams
            data.exams.forEach(exam => {
                const examHTML = createExamHTML(exam);
                container.insertAdjacentHTML('beforeend', examHTML);
            });
            
            // Update loaded count
            const newLoaded = currentLoaded + data.exams.length;
            button.setAttribute('data-loaded', newLoaded);
            
            // Check if there are more exams
            if (data.has_more) {
                button.classList.remove('loading');
                button.disabled = false;
                button.innerHTML = `<i class="fas fa-chevron-down"></i> Xem thêm (còn ${data.remaining} đề thi)`;
            } else {
                button.style.display = 'none';
            }
            
            // Add animation to new items
            const allItems = container.querySelectorAll('.exam-item-smart');
            allItems.forEach((item, idx) => {
                if (idx >= currentLoaded) {
                    item.style.opacity = '0';
                    item.style.animation = 'fadeInUp 0.5s ease forwards';
                }
            });
        } else {
            button.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error loading exams:', error);
        button.classList.remove('loading');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-exclamation-circle"></i> Lỗi! Thử lại';
    });
}

// Create exam HTML
function createExamHTML(exam) {
    const isPaid = exam.pricing_type === 'paid';
    const price = exam.price ? new Intl.NumberFormat('vi-VN').format(exam.price) + 'đ' : '';
    const questionCount = exam.parts ? exam.parts.reduce((sum, part) => sum + (part.questions ? part.questions.length : 0), 0) : 0;
    
    // Teacher info
    const teacherAvatar = exam.customer && exam.customer.avatar 
        ? '{{ url("uploads/images") }}/' + exam.customer.avatar 
        : '{{ url("frontend/images/user_icon.png") }}';
    const teacherName = exam.customer ? exam.customer.name : 'Quản trị viên';
    
    return `
        <div class="exam-item-smart">
            <div class="exam-icon-smart">
                            <i class="fas fa-file-alt"></i>
                        </div>
            <div class="exam-content-smart">
                <div class="exam-title-smart">
                    ${exam.title}
                    ${isPaid ? 
                        '<span class="badge badge-warning ml-2"><i class="fas fa-crown"></i> Trả phí</span>' : 
                        '<span class="badge badge-success ml-2"><i class="fas fa-gift"></i> Miễn phí</span>'
                    }
                        </div>
                <div class="exam-meta-smart">
                    <div class="exam-meta-item-smart">
                        <i class="fas fa-clock"></i>
                        <span>${exam.time} phút</span>
                    </div>
                    <div class="exam-meta-item-smart">
                        <i class="fas fa-question-circle"></i>
                        <span>${questionCount} câu</span>
                </div>
                    <div class="exam-meta-item-smart">
                        <i class="fas fa-graduation-cap"></i>
                        <span>${exam.grade_category ? exam.grade_category.name : 'N/A'}</span>
            </div>
                    <div class="exam-meta-item-smart">
                        <i class="fas fa-book"></i>
                        <span>${exam.subject_category ? exam.subject_category.name : 'N/A'}</span>
                    </div>
                    ${isPaid && exam.price > 0 ? `
                    <div class="exam-meta-item-smart">
                        <i class="fas fa-tag"></i>
                        <span class="text-danger font-weight-bold">${price}</span>
                    </div>
                    ` : ''}
        </div>

                <!-- Teacher Info -->
                <div class="exam-teacher-info">
                    <img class="exam-teacher-avatar" 
                         src="${teacherAvatar}" 
                         alt="${teacherName}">
                    <div>
                        <div class="exam-teacher-name">${teacherName}</div>
                        <div class="exam-teacher-role">Giáo viên</div>
    </div>
</div>
            </div>
            ${isPaid ? 
                `<a href="javascript:void(0)" class="exam-button-smart themvaogiohangDethi" style="background: #ffc107;" data-id="${exam.id}">
                    <i class="fas fa-shopping-cart"></i>
                    Thêm giỏ hàng
                </a>` : 
                `<a href="{{ url('/de-thi/chi-tiet-de-thi') }}/${exam.id}.html" class="exam-button-smart">
                    <i class="fas fa-play"></i>
                    Thi ngay
                </a>`
            }
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
    ['grades', 'subjects'].forEach(type => {
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

    // Event delegation cho button "Thêm giỏ hàng" (cho cả items được load động)
    $(document).on('click', '.themvaogiohangDethi', function() {
        var id = $(this).data('id');
        $.ajax({
            url: "/de-thi/them-vao-gio-hang",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: { dethi_id: id },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Đã thêm vào giỏ hàng!');
                    // Cập nhật số lượng giỏ hàng nếu có
                    if (response.cart_count) {
                        $('.cart-count').text(response.cart_count);
                    }
                } else {
                    toastr.error(response.message || 'Có lỗi xảy ra!');
                }
            },
            error: function() {
                toastr.error('Có lỗi xảy ra, vui lòng thử lại!');
            }
        });
    });

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

    // Add loading animation to exam items
    const examItems = document.querySelectorAll('.exam-item-smart');
    examItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.05}s`;
        item.style.animation = 'fadeInUp 0.5s ease forwards';
    });
});

// CSS Animation
const style = document.createElement('style');
style.textContent = `
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
`;
document.head.appendChild(style);
</script>
@endsection
