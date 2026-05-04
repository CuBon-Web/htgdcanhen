@extends('crm_course.main.master')
@section('title')
Quản lý quà tặng
@endsection
@section('description')
Quản lý quà tặng
@endsection
@section('css_crm_course')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .reward-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        background: white;
    }
    .reward-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .reward-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #f0f0f0;
    }
    .reward-actions {
        display: flex;
        gap: 10px;
    }
    .btn-action {
        padding: 2px 10px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    }
    .btn-edit {
        color: #667eea;
        border: 1px solid #667eea;
    }
    .btn-edit:hover {
        background: #5568d3;
    }
    .btn-delete {
        background: #eb3349;
        color: white;
    }
    .btn-delete:hover {
        background: #d32f2f;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    .search-filter-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('content_crm_course')
<main class="main-content-wrap">
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        <h3>Quản lý quà tặng</h3>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li><a href="{{ route('gamelistAITrieuPhuToanHoc') }}">Game</a></li>
                        <li>Quản lý quà tặng</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-us-area">
        <div class="container-fluid">
            <!-- Search and Filter -->
            <div class="search-filter-box">
                <form method="GET" action="{{ route('game.reward.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, mô tả..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ ($filters['status'] ?? '') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="0" {{ ($filters['status'] ?? '') == '0' ? 'selected' : '' }}>Ngừng hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="{{ route('game.reward.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Thêm quà tặng mới
                        </a>
                    </div>
                </form>
            </div>

            <!-- Rewards List -->
            @if($rewards->count() > 0)
                <div class="row">
                    @foreach($rewards as $reward)
                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($reward->image)
                                        <img src="{{ $reward->image }}" alt="{{ $reward->name }}" class="reward-image" onerror="this.src='{{ asset('frontend/crm-course/images/default-reward.png') }}'">
                                    @else
                                        <div class="reward-image d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 32px;">
                                            🎁
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-2">{{ $reward->name }}</h5>
                                    <p class="text-muted small mb-2">
                                        {{ Str::limit($reward->description ?? 'Không có mô tả', 60) }}
                                    </p>
                                    <div class="mb-2">
                                        <span class="status-badge {{ $reward->status == 1 ? 'status-active' : 'status-inactive' }}">
                                            {{ $reward->status == 1 ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                                        </span>
                                    </div>
                                    <div class="reward-actions">
                                        <a href="{{ route('game.reward.edit', $reward->id) }}" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <button type="button" class="btn-action btn-delete" onclick="deleteReward({{ $reward->id }})">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $rewards->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-gift" style="font-size: 64px; color: #ccc;"></i>
                    </div>
                    <h4 class="text-muted">Chưa có quà tặng nào</h4>
                    <p class="text-muted">Hãy thêm quà tặng mới để bắt đầu!</p>
                    <a href="{{ route('game.reward.create') }}" class="btn btn-success mt-3">
                        <i class="fas fa-plus"></i> Thêm quà tặng mới
                    </a>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection

@section('js_crm_course')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    function deleteReward(id) {
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa quà tặng này?',
            text: 'Bạn sẽ không thể khôi phục lại sau khi xóa!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/tro-choi/qua-tang/${id}/xoa.html`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(data.message || 'Đã xảy ra lỗi khi xóa quà tặng');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Đã xảy ra lỗi khi xóa quà tặng');
                });
            }
        });
    }

    // Hiển thị thông báo từ session
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
</script>
@endsection

