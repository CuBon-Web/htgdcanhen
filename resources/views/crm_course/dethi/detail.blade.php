@extends('crm_course.main.master')
@section('title')
@if($data->type == 'dethi')
Danh sách đề thi
@else
Danh sách bài tập
@endif
@endsection
@section('description')
@if($data->type == 'dethi')
Danh sách đề thi
@else
Danh sách bài tập
@endif
@endsection
@section('image')
@endsection
@section('css_crm_course')
<link rel="stylesheet" href="/frontend/crm-course/css/dethi.css">
<style>
    .badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 500;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 12px;
    }
    
    .badge-primary {
        background-color: #007bff;
        color: white;
    }
    
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
    
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
    
    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .badge-light {
        background-color: #f8f9fa;
        color: #212529;
    }
    
    .badge-dark {
        background-color: #343a40;
        color: white;
    }
    
    /* Cải thiện giao diện cho detail items */
    .detail-item {
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .detail-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .detail-label {
        font-weight: 500;
        color: #6c757d;
        font-size: 13px;
    }
    
    .detail-value {
        font-weight: 600;
        color: #495057;
        font-size: 13px;
    }
    
    /* Hiệu ứng hover cho badges */
    .badge {
        transition: all 0.2s ease;
    }
    
    .badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Responsive cho mobile */
    @media (max-width: 768px) {
        .detail-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
        
        .detail-value {
            align-self: flex-end;
        }
    }

    /* Style for checkbox on student card */
    .student-card {
        position: relative;
    }

    .student-checkbox {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .student-card-wrapper {
        position: relative;
    }

    .student-card-link {
        display: block;
        text-decoration: none;
        color: inherit;
    }

    .session-item.hidden {
        display: none !important;
    }
</style>
@endsection
@section('js_crm_course')
@endsection
@section('content_crm_course')
<main class="main-content-wrap">
    <!-- Start Page Title Area -->
   
    <!-- End Page Title Area -->

    <!-- Start Dy Devices Area -->
    <div class="my-devices-area mt-20">
        <div class="container-fluid">
            <div class="sidebar-left">
                <div class="sidebar">
                    <div class="drive-wrap">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Thông tin:</h4>
                            @if($data->type == 'dethi' || $data->type == 'baitap')
                            @if ($profile->type == 1 || $profile->type == 3 )
                            <button type="button" class="btn btn-outline-success" onclick="copyLink()"><img src="/frontend/crm-course/images/document-copy.svg" alt="document-copy"> Copy Link</button>
                            @else
                            <a href="{{route('startTest',['id'=>$data->id])}}"  class="btn btn-outline-success"><img src="/frontend/crm-course/images/document-copy.svg" alt="document-copy"> Làm Bài</a>
                            @endif
                            @endif
                        </div>
                        <ul class="drive-list-wrap">
                            <li>
                                <a href="">
                                    <img src="https://templates.envytheme.com/joxi/default/assets/images/icon/folder-2.svg" alt="folder-2">
                                    {{$data->title}}
                                </a>
                               
                            </li>
                            <li>
                                <a href="">
                                    <img src="https://templates.envytheme.com/joxi/default/assets/images/icon/clock.svg" alt="clock">
                                    Ngày tạo: {{$data->created_at}}
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <img src="https://templates.envytheme.com/joxi/default/assets/images/icon/user-octagon.svg" alt="star">
                                    Người tạo: {{$data->customer->name ?? 'Vô danh'}}
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <img src="https://templates.envytheme.com/joxi/default/assets/images/icon/save-2.svg" alt="trash">
                                    Số lượt làm: {{$data->sessions->count()}}
                                </a>
                            </li>
                            {{-- @if($data->type == 'baitap')
                            <li>
                                <a href="">
                                    <img src="https://templates.envytheme.com/joxi/default/assets/images/icon/save-2.svg" alt="trash">
                                    Khóa học: {{ $data->course_id ? $data->course->name : 'Chưa trong khóa học' }}
                                </a>
                            </li>
                            @endif --}}
                        </ul>
                        @if ($profile->type == 1 || $profile->type == 3 )
                        <h4 class="mb-3">Hành động:</h4>
                        <ul class="drive-list-wrap">
                            <li>
                                <a href="{{route('editDeThi',['id'=>$data->id])}}">
                                    <i class="fas fa-edit"></i>
                                    @if($data->type == 'dethi')
                                    Sửa đề thi
                                    @elseif($data->type == 'baitap')
                                    Sửa bài tập
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="showDeleteModal(event)" style="color: rgb(187, 7, 7);">
                                    <i style="color: rgb(187, 7, 7);" class="fas fa-trash"></i>
                                    @if($data->type == 'dethi')
                                    Xóa đề thi
                                    @elseif($data->type == 'baitap')
                                    Xóa bài tập
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{route('exportWordWithAnswer',['id'=>$data->id])}}" >
                                    <i class="fas fa-file-word"></i>
                                    Tải xuống file word có lời giải
                                </a>
                            </li>
                            <li>
                                <a href="{{route('exportWordWithoutAnswer',['id'=>$data->id])}}" >
                                    <i class="fas fa-file-word"></i>
                                    Tải xuống file word không lời giải
                                </a>
                            </li>
                        </ul>
                        @endif
                        
                    </div>
                </div>
            </div>

            <div class="content-right">
                <div class="my-file-area">
                    <!-- Filter Section -->
                    @if ($profile->type == 1 || $profile->type == 3)
                    <div class="filter-section mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label" style="font-weight: 600; font-size: 13px;">Tìm theo tên</label>
                                <input type="text" id="filter-name" class="form-control" placeholder="Nhập tên học sinh...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-weight: 600; font-size: 13px;">Điểm từ</label>
                                <input type="number" id="filter-score-min" class="form-control" placeholder="0" min="0" max="10" step="0.1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-weight: 600; font-size: 13px;">Điểm đến</label>
                                <input type="number" id="filter-score-max" class="form-control" placeholder="10" min="0" max="10" step="0.1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-weight: 600; font-size: 13px;">Thời gian (phút)</label>
                                <input type="number" id="filter-time" class="form-control" placeholder="Thời gian tối đa">
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button class="btn btn-primary" onclick="applyFilter()" style="height: 38px;">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                                <button class="btn btn-secondary" onclick="resetFilter()" style="height: 38px;">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-file-upload d-flex justify-content-between align-items-center">
                        @if ($profile->type == 1 || $profile->type == 3)
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                                <label class="form-check-label" for="select-all" style="font-weight: 600;">
                                    Chọn tất cả
                                </label>
                            </div>
                            <span id="selected-count" class="badge bg-info" style="display: none; font-size: 13px; padding: 6px 12px;">0 học sinh được chọn</span>
                        </div>
                        @else
                        <form class="search-form">
                            <input type="text" class="form-control" placeholder="Tìm kiếm học sinh">
                            <img src="/frontend/crm-course/images/search-normal.svg" alt="search-normal">
                        </form>
                        @endif
                        <ul class="create-upload d-flex">
                            <li>
                                <button class="second upload-btn upload" onclick="downloadScoreTable()">
                                    Tải bảng điểm
                                    <img src="/frontend/crm-course/images/export.svg" alt="export">
                                </button>
                            </li>
                            <li>
                                <button class="second upload-btn upload" onclick="shareScoreFacebook()">
                                    Share Facebook
                                </button>
                            </li>
                            <li>
                                <button class="second upload-btn upload" onclick="shareScoreZalo()">
                                    Share Zalo
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="quick-access">
                        @if ($profile->type == 1 || $profile->type == 3 )
                        <h3>Danh sách học sinh đã làm</h3>
                        @else
                        <h3>Danh sách lần làm đề</h3>
                        @endif

                        <div class="row" id="sessions-container">
                            @foreach($sessions as $session)
                            <div class="col-xxl-3 col-lg-6 col-md-6 mb-4 session-item" 
                                data-session-id="{{$session->id}}" 
                                data-student-name="{{$session->student->name}}" 
                                data-score="{{$session->total_score}}" 
                                data-time="{{$session->actual_time}}">
                                <div class="student-card-wrapper">
                                    @if ($profile->type == 1 || $profile->type == 3)
                                    <input type="checkbox" class="student-checkbox" data-session-id="{{$session->id}}" onchange="updateSelectedCount()" onclick="event.stopPropagation();">
                                    @endif
                                    <a href="{{$profile->type == 1 || $profile->type == 3 ? route('chamdiem',['id'=>$session->id]) : route('resultDethi',['id'=>$session->id])}}" class="student-card-link">
                                    <div class="student-card">
                                        <div class="student-header">
                                            <div class="student-avatar">
                                                @if ($session->student->avatar)
                                                    <img src="{{asset('/uploads/images/'.$session->student->avatar)}}" alt="avatar">
                                                @else
                                                    <span class="avatar-initials">{{ substr($session->student->name, 0, 2) }}</span>
                                                @endif
                                            </div>
                                            <div class="student-info">
                                                <h5 class="student-name">{{ $session->student->name }}</h5>
                                                @if ($session->status == 1)
                                                    <span class="status-badge">Chưa chấm tự luận</span>
                                                @elseif ($session->status == 2)
                                                    <span class="status-success">Điểm: {{$session->total_score}}/{{$session->max_score}}</span>
                                                @else
                                                    <span class="status-badge">Đã chấm tự luận</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="submission-details">
                                            <div class="detail-item">
                                                <span class="detail-label">Làm lần thứ:</span>
                                                <span class="detail-value">
                                                    @php
                                                        // Xác định thứ tự làm bài dựa vào student_id và finished_at
                                                        $userAttempts = $sessions->where('student_id', $session->student_id)
                                                            ->where('dethi_id', $data->id)
                                                            ->sortBy('finished_at')
                                                            ->values();
                                                        
                                                        $currentIndex = $userAttempts->search(function($item) use ($session) {
                                                            return $item->id === $session->id;
                                                        });
                                                        
                                                        $attemptNumber = $currentIndex !== false ? $currentIndex + 1 : 1;
                                                    @endphp
                                                    <span class="badge badge-primary">Lần {{ $attemptNumber }}</span>
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Thời gian làm bài:</span>
                                                <span class="detail-value">
                                                    @if ($session->actual_time )
                                                    @php
                                                        $result = formatTime($session->actual_time);
                                                    @endphp
                                                    {{ $result }}
                                                    @else
                                                    N/A
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Số câu đúng:</span>
                                                <span class="detail-value">{{ $session->correct_answers }}/{{$session->total_questions}} câu</span>
                                            </div>
                                            
                                            <div class="detail-item">
                                                <span class="detail-label">Tổng số lần làm:</span>
                                                <span class="detail-value">
                                                    @php
                                                        $totalAttempts = $sessions->where('student_id', $session->student_id)
                                                            ->where('dethi_id', $data->id)
                                                            ->count();
                                                    @endphp
                                                    <span class="badge badge-info">{{ $totalAttempts }} lần</span>
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Thời gian nộp bài:</span>
                                                <span class="detail-value">{{ $session->finished_at ? $session->finished_at->format('H:i - d/m/Y') : 'N/A' }}</span>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="comments-section">
                                            <span class="comments-label">Nhận xét:</span>
                                            <button class="edit-comment-btn">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            <div class="col-12">
                                {{$sessions->links()}}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Dy Devices Area -->

    <div class="flex-grow-1"></div>

    <!-- Start Footer Area -->
    @include('crm_course.main.footer')
    <!-- End Footer Area -->

    <!-- Modal xác nhận xoá đề thi -->
    <div class="modal fade" id="deleteDethiModal" tabindex="-1" aria-labelledby="deleteDethiModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteDethiModalLabel">Xác nhận xoá đề thi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Bạn có chắc chắn muốn xoá đề thi này? Tất cả dữ liệu học sinh làm bài và các file liên quan sẽ bị xoá vĩnh viễn!</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
            <button type="button" class="btn btn-danger" onclick="confirmDeleteDethi()">Xoá vĩnh viễn</button>
          </div>
        </div>
      </div>
    </div>
</main>
<script>
    function copyLink() {
        var link = "{{route('startTest',['id'=>$data->id])}}";
        navigator.clipboard.writeText(link);
        alert('Đã copy link');
    }

    function showDeleteModal(e) {
        e.preventDefault();
        var modal = new bootstrap.Modal(document.getElementById('deleteDethiModal'));
        modal.show();
    }

    function confirmDeleteDethi() {
        window.location.href = "{{route('destroyDeThi',['id'=>$data->id])}}";
    }

    function shareScoreFacebook() {
        const shareUrl = `${window.location.origin}/tro-choi/de-thi/{{$data->id}}/score-view`;
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
            const messengerDeepLink = `fb-messenger://share?link=${encodeURIComponent(shareUrl)}`;
            window.location.href = messengerDeepLink;
            setTimeout(() => {
                const sendDialogUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(shareUrl)}&redirect_uri=${encodeURIComponent(shareUrl)}`;
                window.location.href = sendDialogUrl;
            }, 800);
        } else {
            const sendDialogUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(shareUrl)}&redirect_uri=${encodeURIComponent(window.location.href)}`;
            window.open(sendDialogUrl, '_blank', 'width=600,height=400,noopener');
        }
    }

    function shareScoreZalo() {
        const shareUrl = `${window.location.origin}/tro-choi/de-thi/{{$data->id}}/score-view`;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(shareUrl)
                .then(() => alert('Đã copy link. Vui lòng mở Zalo và dán vào tin nhắn.'))
                .catch(() => prompt('Copy link này và dán vào Zalo:', shareUrl));
        } else {
            prompt('Copy link này và dán vào Zalo:', shareUrl);
        }
    }

    function downloadScoreTable() {
        @if ($profile->type == 1 || $profile->type == 3)
        // Get selected session IDs
        var selectedSessions = [];
        var checkboxes = document.querySelectorAll('.student-checkbox:checked');
        checkboxes.forEach(function(checkbox) {
            selectedSessions.push(checkbox.getAttribute('data-session-id'));
        });

        if (selectedSessions.length === 0) {
            alert('Vui lòng chọn ít nhất một học sinh để tải bảng điểm.');
            return;
        }

        // Create a form and submit
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = "/de-thi/download-score-table/{{$data->id}}";
        
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        var sessionsInput = document.createElement('input');
        sessionsInput.type = 'hidden';
        sessionsInput.name = 'sessions';
        sessionsInput.value = JSON.stringify(selectedSessions);
        form.appendChild(sessionsInput);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        @else
        // window.location.href = "/de-thi/download-score-table/{{$data->id}}";
        @endif
    }

    function toggleSelectAll(checkbox) {
        var studentCheckboxes = document.querySelectorAll('.student-checkbox');
        studentCheckboxes.forEach(function(cb) {
            cb.checked = checkbox.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        var selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
        var badge = document.getElementById('selected-count');
        
        if (selectedCount > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = selectedCount + ' học sinh được chọn';
        } else {
            badge.style.display = 'none';
        }

        // Update select all checkbox
        var totalCheckboxes = document.querySelectorAll('.student-checkbox').length;
        var selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.checked = (selectedCount === totalCheckboxes && totalCheckboxes > 0);
    }

    function applyFilter() {
        var filterName = document.getElementById('filter-name').value.toLowerCase().trim();
        var filterScoreMin = parseFloat(document.getElementById('filter-score-min').value) || 0;
        var filterScoreMax = parseFloat(document.getElementById('filter-score-max').value) || 10;
        var filterTime = parseFloat(document.getElementById('filter-time').value) || Infinity;

        // Convert filter time from minutes to seconds
        var filterTimeSeconds = filterTime * 60;

        var sessionItems = document.querySelectorAll('.session-item');
        
        sessionItems.forEach(function(item) {
            var studentName = item.getAttribute('data-student-name').toLowerCase();
            var score = parseFloat(item.getAttribute('data-score')) || 0;
            var time = parseFloat(item.getAttribute('data-time')) || 0; // time is in seconds

            var matchName = !filterName || studentName.includes(filterName);
            var matchScore = score >= filterScoreMin && score <= filterScoreMax;
            var matchTime = filterTime === Infinity || time <= filterTimeSeconds;

            if (matchName && matchScore && matchTime) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });

        // Update pagination visibility
        var visibleItems = document.querySelectorAll('.session-item:not(.hidden)').length;
        if (visibleItems === 0) {
            // Show message if no results
            if (!document.getElementById('no-results-message')) {
                var message = document.createElement('div');
                message.id = 'no-results-message';
                message.className = 'col-12 text-center p-4';
                message.innerHTML = '<p class="text-muted">Không tìm thấy kết quả nào phù hợp với bộ lọc.</p>';
                document.getElementById('sessions-container').appendChild(message);
            }
        } else {
            var noResultsMsg = document.getElementById('no-results-message');
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }

    function resetFilter() {
        document.getElementById('filter-name').value = '';
        document.getElementById('filter-score-min').value = '';
        document.getElementById('filter-score-max').value = '';
        document.getElementById('filter-time').value = '';

        var sessionItems = document.querySelectorAll('.session-item');
        sessionItems.forEach(function(item) {
            item.classList.remove('hidden');
        });

        var noResultsMsg = document.getElementById('no-results-message');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }
</script>
@endsection
