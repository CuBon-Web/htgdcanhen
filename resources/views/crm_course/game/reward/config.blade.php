@extends('crm_course.main.master')
@section('title')
Cấu hình phần thưởng - {{ $game->title }}
@endsection
@section('description')
Cấu hình phần thưởng
@endsection
@section('css_crm_course')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .config-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    .config-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .form-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    .percentage-range {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .percentage-range input {
        flex: 1;
    }
    .config-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .config-info {
        flex-grow: 1;
    }
    .config-actions {
        display: flex;
        gap: 10px;
    }
    .badge-percentage {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        background: #667eea;
        color: white;
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
                        <h3>Cấu hình phần thưởng: {{ $game->title }}</h3>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li><a href="{{ route('gamelistAITrieuPhuToanHoc') }}">Game</a></li>
                        <li>Cấu hình phần thưởng</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-us-area">
        <div class="container-fluid">
            <!-- Hướng dẫn -->
            <div class="alert alert-info mb-4">
                <h5><i class="fas fa-info-circle"></i> Hướng dẫn cấu hình:</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mt-3">Ví dụ: 70% → 1 phần quà, 100% → 2 phần quà</h6>
                        <ol>
                            <li><strong>Cấu hình 1 (70%):</strong>
                                <ul>
                                    <li>Quà tặng: Chọn quà tặng bạn muốn</li>
                                    <li>% điểm tối thiểu: <code>70</code></li>
                                    <li>% điểm tối đa: <code>99.99</code></li>
                                    <li>Số lượng quà: <code>1</code></li>
                                    <li>Độ ưu tiên: <code>0</code></li>
                                </ul>
                            </li>
                            <li><strong>Cấu hình 2 (100%):</strong>
                                <ul>
                                    <li>Quà tặng: Chọn quà tặng bạn muốn</li>
                                    <li>% điểm tối thiểu: <code>100</code></li>
                                    <li>% điểm tối đa: <code>(để trống)</code></li>
                                    <li>Số lượng quà: <code>2</code></li>
                                    <li>Độ ưu tiên: <code>10</code> (cao hơn để ưu tiên)</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mt-3">Lưu ý quan trọng:</h6>
                        <ul>
                            <li>Hệ thống sẽ trao quà từ <strong>config có % điểm cao nhất</strong> mà học sinh đạt được</li>
                            <li><strong>Không tích lũy:</strong> Nếu đạt 100%, chỉ nhận quà từ config 100%, không nhận thêm quà từ config 70%</li>
                            <li><strong>Priority:</strong> Số càng cao = ưu tiên càng cao. Nếu học sinh đạt nhiều mức, sẽ chọn config có priority cao nhất</li>
                            <li><strong>Max %:</strong> Để trống = không giới hạn trên (áp dụng từ min% trở lên)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form thêm cấu hình mới -->
            <div class="form-container">
                <h4 class="mb-4"><i class="fas fa-plus-circle"></i> Thêm cấu hình phần thưởng mới</h4>
                <form id="config-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="reward_id" class="form-label">Quà tặng <span class="text-danger">*</span></label>
                                <select class="form-control" id="reward_id" name="reward_id" required>
                                    <option value="">-- Chọn quà tặng --</option>
                                    @foreach($rewards as $reward)
                                        <option value="{{ $reward->id }}">{{ $reward->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="min_percentage" class="form-label">% điểm tối thiểu <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="min_percentage" name="min_percentage" 
                                       min="0" max="100" step="0.01" required placeholder="VD: 70">
                                <small class="form-text text-muted">VD: 70 = từ 70% trở lên</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="max_percentage" class="form-label">% điểm tối đa</label>
                                <input type="number" class="form-control" id="max_percentage" name="max_percentage" 
                                       min="0" max="100" step="0.01" placeholder="Để trống = không giới hạn">
                                <small class="form-text text-muted">VD: 99.99 = đến 99.99%</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Số lượng quà <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       min="1" max="10" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Độ ưu tiên</label>
                                <input type="number" class="form-control" id="priority" name="priority" 
                                       min="0" value="0" placeholder="Cao hơn = ưu tiên hơn">
                                <small class="form-text text-muted">VD: 100% đặt priority = 10</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Thêm cấu hình
                            </button>
                            <a href="{{ route('gamelistAITrieuPhuToanHoc') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Danh sách cấu hình hiện có -->
            <div class="form-container">
                <h4 class="mb-4"><i class="fas fa-list"></i> Danh sách cấu hình phần thưởng</h4>
                
                @if($configs->count() > 0)
                    <div id="configs-list">
                        @foreach($configs as $config)
                        <div class="config-item" data-id="{{ $config->id }}">
                            <div class="config-info">
                                <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
                                    <strong>{{ $config->reward->name }}</strong>
                                    <span class="badge-percentage">
                                        @if($config->max_percentage)
                                            {{ number_format($config->min_percentage, 2) }}% - {{ number_format($config->max_percentage, 2) }}%
                                        @else
                                            ≥ {{ number_format($config->min_percentage, 2) }}%
                                        @endif
                                    </span>
                                    <span class="badge bg-info">Số lượng: {{ $config->quantity }} phần quà</span>
                                    @if($config->priority > 0)
                                        <span class="badge bg-warning">Ưu tiên: {{ $config->priority }}</span>
                                    @else
                                        <span class="badge bg-secondary">Ưu tiên: 0</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $config->reward->description ?? 'Không có mô tả' }}</small>
                            </div>
                            <div class="config-actions">
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfig({{ $config->id }})">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-cog" style="font-size: 64px; color: #ccc;"></i>
                        </div>
                        <h5 class="text-muted">Chưa có cấu hình phần thưởng nào</h5>
                        <p class="text-muted">Hãy thêm cấu hình phần thưởng ở trên để bắt đầu!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@section('js_crm_course')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    const gameId = {{ $game->id }};
    
    // Submit form thêm cấu hình
    document.getElementById('config-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`/tro-choi/game/${gameId}/cau-hinh-phan-thuong/luu.html`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                // Reset form
                document.getElementById('config-form').reset();
                // Reload page để hiển thị config mới
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                toastr.error(data.message || 'Đã xảy ra lỗi khi thêm cấu hình');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Đã xảy ra lỗi khi thêm cấu hình');
        });
    });
    
    // Xóa cấu hình
    function deleteConfig(id) {
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa cấu hình này?',
            text: 'Bạn sẽ không thể khôi phục lại sau khi xóa!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/tro-choi/game/cau-hinh-phan-thuong/${id}/xoa.html`, {
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
                        // Xóa element khỏi DOM
                        document.querySelector(`.config-item[data-id="${id}"]`).remove();
                        // Nếu không còn config nào, reload page
                        if (document.querySelectorAll('.config-item').length === 0) {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                        toastr.error(data.message || 'Đã xảy ra lỗi khi xóa cấu hình');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Đã xảy ra lỗi khi xóa cấu hình');
                });
            }
        });
    }
    
    // Validate max_percentage >= min_percentage
    document.getElementById('max_percentage').addEventListener('blur', function() {
        const min = parseFloat(document.getElementById('min_percentage').value);
        const max = parseFloat(this.value);
        
        if (max && max < min) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Phần trăm tối đa phải lớn hơn hoặc bằng phần trăm tối thiểu!'
            });
            this.value = '';
        }
    });
</script>
@endsection

