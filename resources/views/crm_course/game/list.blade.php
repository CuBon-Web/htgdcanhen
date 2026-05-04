@extends('crm_course.main.master')
@section('title')
Danh sách game
@endsection
@section('description')
Danh sách game
@endsection
@section('image')
@endsection
@section('css_crm_course')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .ranking-badge {
        cursor: pointer;
    }

/* Share inline buttons */
.share-inline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(0,0,0,0.05);
    border-radius: 999px;
    padding: 4px 10px;
}
.share-label {
    font-size: 12px;
    font-weight: 600;
    color: #555;
}
.share-btn {
    border: none;
    border-radius: 50%;
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #fff;
    font-size: 14px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.share-btn:focus { outline: none; }
.share-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.share-fb { background: #1877f2; }
.share-zalo { background: #0f8ee0; font-weight: 700; font-size: 12px; padding: 0 6px; width: auto; border-radius: 14px; }
    .ranking-modal .rank-1 td {
        background: rgba(255, 215, 0, 0.2);
        font-weight: 600;
    }
    .ranking-modal .rank-2 td {
        background: rgba(192, 192, 192, 0.2);
        font-weight: 600;
    }
    .ranking-modal .rank-3 td {
        background: rgba(205, 127, 50, 0.2);
        font-weight: 600;
    }
    .reward-config-modal .config-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .reward-config-modal .badge-percentage {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        background: #667eea;
        color: white;
    }
    #studentResultModal .modal-content {
        border-radius: 20px;
    }
    #studentResultModal .bg-gradient-primary {
        background: linear-gradient(135deg, #4338ca, #c026d3);
    }
    .student-result-wrapper {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .result-score-card {
        background: linear-gradient(135deg, #eef2ff, #ede9fe);
        border-radius: 16px;
        padding: 18px;
        border: 1px solid rgba(99,102,241,0.3);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }
    .result-score {
        font-size: 42px;
        font-weight: 700;
        color: #312e81;
        line-height: 1;
    }
    .result-score small {
        display: block;
        font-size: 14px;
        color: #6b7280;
        margin-top: 4px;
        font-weight: 500;
    }
    .result-meta {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-weight: 500;
        color: #4b5563;
    }
    .result-meta span {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .result-meta span i {
        color: #6366f1;
    }
    .reward-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
    }
    .reward-card {
        border: 1px solid #ede9fe;
        border-radius: 14px;
        padding: 14px;
        background: #fff;
        box-shadow: 0 8px 16px rgba(99,102,241,0.08);
        text-align: center;
    }
    .reward-card img,
    .reward-card .reward-avatar {
        width: 70px;
        height: 70px;
        border-radius: 14px;
        object-fit: cover;
        margin-bottom: 10px;
        border: 1px solid #e5e7eb;
    }
    .reward-card .reward-avatar {
        background: linear-gradient(135deg, #a78bfa, #c084fc);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }
    .reward-card h6 {
        font-size: 15px;
        margin-bottom: 6px;
        color: #1f2937;
        font-weight: 600;
    }
    .reward-card .badge {
        font-size: 12px;
        border-radius: 999px;
        padding: 4px 10px;
    }
    #studentResultModal .modal-content {
        border-radius: 18px;
    }
    #studentResultModal .bg-gradient-primary {
        background: linear-gradient(135deg, #4f46e5, #9333ea);
    }
    #studentResultModal .result-score-display {
        display: flex;
        align-items: baseline;
        gap: 10px;
        font-weight: 600;
        font-size: 28px;
        color: #1e1b4b;
        margin-bottom: 15px;
    }
    #studentResultModal .result-score-display .score {
        font-size: 36px;
        color: #2563eb;
    }
    #studentResultModal .result-summary {
        background: #f8fafc;
        border-radius: 14px;
        padding: 20px;
        border: 1px solid #e2e8f0;
    }
</style>
@endsection
@section('js_crm_course')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    const rankingUrlTemplate = "/tro-choi/game/" + ':id' + "/ranking";
    const studentResultUrlTemplate = "/tro-choi/game/" + ':id' + "/my-result";
    const startGameUrlTemplate = "{{ route('startGame', ['id' => ':id']) }}";
    const rankingShareUrlTemplate = `${window.location.origin}/tro-choi/game/:id/ranking-view`;
    let latestOverallResults = [];
    let latestGameRankingResults = [];
    let currentGameShareUrl = '';

    function copyLink(link) {
        navigator.clipboard.writeText(link);
        toastr.success('Đã copy link', 'Thành công');
    }

    async function viewResult(gameId) {
        const modalEl = document.getElementById('studentResultModal');
        const modalContent = document.getElementById('student-result-content');
        const modalTitle = document.getElementById('studentResultModalLabel');
        modalContent.innerHTML = '<div class="text-center text-muted py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        modalTitle.textContent = 'Kết quả của tôi';
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        try {
            const response = await fetch(studentResultUrlTemplate.replace(':id', gameId));
            if (!response.ok) {
                throw new Error('Không thể tải kết quả của bạn.');
            }

            const data = await response.json();
            if (!data.success || !data.result) {
                modalContent.innerHTML = '<div class="alert alert-warning">Bạn chưa có kết quả cho game này.</div>';
                return;
            }

            const result = data.result;
            let rewardsHtml = '';
            if (result.earned_rewards && result.earned_rewards.length > 0) {
                rewardsHtml = `
                    <div class="mt-2">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="mb-0 text-success"><i class="fa fa-gift me-2"></i>Phần thưởng bạn nhận được</h6>
                            <span class="badge bg-light text-dark">${result.earned_rewards.length} phần thưởng</span>
                        </div>
                        <div class="reward-grid">
                            ${result.earned_rewards.map(reward => `
                                <div class="reward-card">
                                    ${reward.image ? `<img src="${reward.image}" alt="${reward.name}" onerror="this.style.display='none'">` : `<div class="reward-avatar">🎁</div>`}
                                    <h6>${reward.name}</h6>
                                    ${reward.quantity > 1 ? `<span class="badge bg-primary">x${reward.quantity}</span>` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            } else {
                rewardsHtml = '<div class="alert alert-info mt-3 mb-0"><i class="fa fa-info-circle me-2"></i>Bạn chưa nhận được phần thưởng nào.</div>';
            }

            modalContent.innerHTML = `
                <div class="student-result-wrapper">
                    <div class="result-score-card">
                        <div>
                            <div class="result-score">${result.correct_count}/${result.total_questions}</div>
                            <small>Đúng / Tổng câu</small>
                        </div>
                        <div class="result-meta">
                            <span><i class="fa fa-gamepad"></i>${result.game_title || 'Game'}</span>
                            <span><i class="fa fa-clock"></i>${result.created_at || 'Chưa cập nhật'}</span>
                            <span class="badge bg-success">${result.percentage}%</span>
                        </div>
                    </div>
                    ${rewardsHtml}
                </div>
            `;
        } catch (error) {
            console.error('Error:', error);
            modalContent.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
        }
    }
    function deleteGame(id) {
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa game này?',
            text: 'Bạn sẽ không thể khôi phục lại sau khi xóa!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/tro-choi/game/delete/" + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Xóa game thành công', 'Thành công');
                            window.location.reload();
                        } else {
                            toastr.error('Xóa game thất bại', 'Lỗi');
                        }
                    },
                    error: function(response) {
                        toastr.error('Xóa game thất bại', 'Lỗi');
                    }
                });
            }
        });
    }
    function restartGame(gameId) {
        Swal.fire({
            title: 'Bạn có chắc chắn muốn thi lại game này?',
            text: 'Kết quả của tất cả học sinh sẽ bị xóa!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Thi lại',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/tro-choi/game/restart/" + gameId,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        toastr.success('Thi lại game thành công', 'Thành công');
                        window.location.reload();
                    },
                    error: function(response) {
                        toastr.error('Thi lại game thất bại', 'Lỗi');
                    }
                });
            } else {
                toastr.error('Thi lại game thất bại', 'Lỗi');
            }
        });
    }
    async function showRanking(gameId, gameName) {
        const modalEl = document.getElementById('rankingModal');
        const rankingTitle = document.getElementById('rankingModalLabel');
        const rankingBody = document.getElementById('ranking-table-body');
        const rankingSummary = document.getElementById('ranking-summary');
        rankingTitle.textContent = `Bảng xếp hạng - ${gameName || 'Game'}`;
        rankingBody.innerHTML = `<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`;
        rankingSummary.textContent = '';
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        try {
            const response = await fetch(rankingUrlTemplate.replace(':id', gameId));
            if (!response.ok) {
                throw new Error('Không thể tải bảng xếp hạng.');
            }
            const data = await response.json();
            const results = data.results || [];
            latestGameRankingResults = results;
            currentGameShareUrl = rankingShareUrlTemplate.replace(':id', gameId);
            if (results.length === 0) {
                rankingBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">Chưa có học sinh nào hoàn thành game này.</td></tr>`;
                return;
            }
            rankingBody.innerHTML = results.map(result => {
                let rankDisplay = result.rank;
                if (result.rank === 1) {
                    rankDisplay = '<i class="fa fa-medal text-warning fs-5"></i>';
                } else if (result.rank === 2) {
                    rankDisplay = '<i class="fa fa-medal text-muted fs-5"></i>';
                } else if (result.rank === 3) {
                    rankDisplay = '<i class="fa fa-medal text-brown fs-5" style="color:#cd7f32;"></i>';
                }
                const rowClass = result.rank <= 3 ? `rank-${result.rank}` : '';
                const rewardsHtml = (result.earned_rewards && result.earned_rewards.length > 0)
                    ? result.earned_rewards.map(reward => `
                        <span class="badge bg-light text-dark border me-1 mb-1">
                            ${reward.name}
                            ${reward.quantity > 1 ? `<span class="text-primary ms-1">x${reward.quantity}</span>` : ''}
                        </span>
                    `).join('')
                    : '<span class="text-muted small">-</span>';
                return `
                    <tr class="${rowClass}">
                        <td class="text-center fw-bold">${rankDisplay}</td>
                        <td>${result.student_name}</td>
                        <td class="text-center">${result.correct_count}/${result.total_questions}</td>
                        <td class="text-center">${result.percentage}%</td>
                        <td class="text-center">${rewardsHtml}</td>
                        <td class="text-center text-muted">${result.created_at ?? ''}</td>
                    </tr>
                `;
            }).join('');
            rankingSummary.textContent = `Tổng số lượt: ${results.length}`;
        } catch (error) {
            rankingBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">${error.message}</td></tr>`;
        }
    }

    // Reward Config Modal
    let currentGameId = null;
    let currentGameTitle = null;
    
    // Hàm reload danh sách cấu hình
    async function reloadRewardConfigs() {
        if (!currentGameId) return;
        
        const configsList = document.getElementById('reward-configs-list');
        configsList.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        
        try {
            const response = await fetch(`/tro-choi/game/${currentGameId}/cau-hinh-phan-thuong/data.html`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (!response.ok) {
                throw new Error('Không thể tải dữ liệu cấu hình.');
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Đã xảy ra lỗi');
            }
            
            // Hiển thị danh sách cấu hình
            if (data.configs.length === 0) {
                configsList.innerHTML = '<div class="text-center py-4 text-muted">Chưa có cấu hình phần thưởng nào. Hãy thêm cấu hình ở trên!</div>';
            } else {
                configsList.innerHTML = data.configs.map(config => {
                    const percentageRange = config.max_percentage 
                        ? `${parseFloat(config.min_percentage).toFixed(2)}% - ${parseFloat(config.max_percentage).toFixed(2)}%`
                        : `≥ ${parseFloat(config.min_percentage).toFixed(2)}%`;
                    
                    return `
                        <div class="config-item" data-id="${config.id}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <strong>${config.reward_name}</strong>
                                    <span class="badge-percentage">${percentageRange}</span>
                                    <span class="badge bg-info">Số lượng: ${config.quantity}</span>
                                    ${config.priority > 0 ? `<span class="badge bg-warning">Ưu tiên: ${config.priority}</span>` : '<span class="badge bg-secondary">Ưu tiên: 0</span>'}
                                </div>
                                <small class="text-muted">${config.reward_description || 'Không có mô tả'}</small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteRewardConfig(${config.id})">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        } catch (error) {
            console.error('Error:', error);
            configsList.innerHTML = `<div class="text-center py-4 text-danger">${error.message}</div>`;
        }
    }
    
    async function showRewardConfig(gameId, gameTitle) {
        currentGameId = gameId;
        currentGameTitle = gameTitle;
        const modalEl = document.getElementById('rewardConfigModal');
        const modalLabel = document.getElementById('rewardConfigModalLabel');
        const rewardSelect = document.getElementById('config-reward-id');
        
        modalLabel.textContent = `Cấu hình phần thưởng: ${gameTitle}`;
        rewardSelect.innerHTML = '<option value="">-- Chọn quà tặng --</option>';
        
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        
        try {
            const response = await fetch(`/tro-choi/game/${gameId}/cau-hinh-phan-thuong/data.html`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (!response.ok) {
                throw new Error('Không thể tải dữ liệu cấu hình.');
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Đã xảy ra lỗi');
            }
            
            // Load danh sách quà tặng vào select
            data.rewards.forEach(reward => {
                const option = document.createElement('option');
                option.value = reward.id;
                option.textContent = reward.name;
                rewardSelect.appendChild(option);
            });
            
            // Load danh sách cấu hình
            await reloadRewardConfigs();
        } catch (error) {
            console.error('Error:', error);
            const configsList = document.getElementById('reward-configs-list');
            configsList.innerHTML = `<div class="text-center py-4 text-danger">${error.message}</div>`;
        }
    }
    
    // Submit form thêm cấu hình (sẽ được gán sau khi modal được tạo)
    document.addEventListener('DOMContentLoaded', function() {
        const configForm = document.getElementById('reward-config-form');
        if (configForm) {
            configForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                if (!currentGameId) return;
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang lưu...';
                
                try {
                    const response = await fetch(`/tro-choi/game/${currentGameId}/cau-hinh-phan-thuong/luu.html`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        toastr.success(data.message);
                        this.reset();
                        // Reload danh sách cấu hình
                        await reloadRewardConfigs();
                    } else {
                        toastr.error(data.message || 'Đã xảy ra lỗi khi thêm cấu hình');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    toastr.error('Đã xảy ra lỗi khi thêm cấu hình');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }
    });
    
    // Xóa cấu hình
    function deleteRewardConfig(id) {
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
                        const configItem = document.querySelector(`.config-item[data-id="${id}"]`);
                        if (configItem) {
                            configItem.remove();
                        }
                        // Reload danh sách cấu hình
                        reloadRewardConfigs();
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

    // Hiển thị bảng xếp hạng tổng quát
    async function showOverallRanking() {
        const modalEl = document.getElementById('overallRankingModal');
        const rankingBody = document.getElementById('overall-ranking-table-body');
        const rankingSummary = document.getElementById('overall-ranking-summary');
        const selectAllCheckbox = document.getElementById('select-all-students');
        const deleteSelectedBtn = document.getElementById('delete-selected-btn');
        
        // Reset
        rankingBody.innerHTML = `<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`;
        rankingSummary.textContent = '';
        selectAllCheckbox.checked = false;
        deleteSelectedBtn.style.display = 'none';
        document.getElementById('selected-count').textContent = '0';
        
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        
        try {
            const response = await fetch('/tro-choi/bang-xep-hang-tong-quat', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Không thể tải bảng xếp hạng.');
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Đã xảy ra lỗi');
            }
            
            const results = data.results || [];
            const isTeacher = data.is_teacher || false;
            const canDelete = isTeacher || data.is_super_admin;
            
            if (results.length === 0) {
                rankingBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Chưa có học sinh nào hoàn thành game.</td></tr>`;
                rankingSummary.textContent = '';
                return;
            }
            latestOverallResults = results;
            rankingBody.innerHTML = results.map(result => {
                let rankDisplay = result.rank;
                if (result.rank === 1) {
                    rankDisplay = '<i class="fa fa-medal text-warning fs-5"></i>';
                } else if (result.rank === 2) {
                    rankDisplay = '<i class="fa fa-medal text-muted fs-5"></i>';
                } else if (result.rank === 3) {
                    rankDisplay = '<i class="fa fa-medal text-brown fs-5" style="color:#cd7f32;"></i>';
                }
                
                const rowClass = result.rank <= 3 ? `rank-${result.rank}` : '';
                const checkbox = canDelete 
                    ? `<input type="checkbox" class="student-checkbox" value="${result.student_id}" onchange="updateSelectedCount()" data-student-name="${result.student_name.replace(/"/g, '&quot;')}">`
                    : '';
                const deleteButton = canDelete 
                    ? `<button class="btn btn-sm btn-danger" onclick="deleteStudentResults(${result.student_id}, '${result.student_name.replace(/'/g, "\\'")}')">
                        <i class="fa fa-trash"></i> Xóa
                       </button>`
                    : '';
                
                return `
                    <tr class="${rowClass}">
                        <td class="text-center">${checkbox}</td>
                        <td class="text-center fw-bold">${rankDisplay}</td>
                        <td>${result.student_name}</td>
                        <td class="text-center">${result.total_games}</td>
                        <td class="text-center fw-bold">${result.average_percentage}%</td>
                        <td class="text-center">${result.total_correct}/${result.total_questions}</td>
                        <td class="text-center">${deleteButton}</td>
                    </tr>
                `;
            }).join('');
            
            rankingSummary.textContent = `Tổng số học sinh: ${results.length}`;
            
            // Hiển thị nút xóa nếu có quyền
            if (canDelete) {
                deleteSelectedBtn.style.display = 'inline-block';
            }
        } catch (error) {
            console.error('Error:', error);
            rankingBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">${error.message}</td></tr>`;
        }
    }

    function buildRankingSummary() {
        // Sử dụng link tĩnh chia sẻ bảng xếp hạng công khai
        const shareUrl = `${window.location.origin}/tro-choi/bang-xep-hang-game.html`;
        const results = latestOverallResults || [];
        if (!results.length) {
            toastr.warning('Chưa có dữ liệu bảng xếp hạng để chia sẻ.');
            return null;
        }
        const topText = results.slice(0, 5).map((r, idx) => {
            return `${idx + 1}. ${r.student_name} - ${r.average_percentage}% (${r.total_correct}/${r.total_questions})`;
        }).join('\n');
        return { summary: `Bảng xếp hạng game:\n${topText}\nXem thêm: ${shareUrl}`, shareUrl };
    }

    function buildGameRankingSummary() {
        const shareUrl = currentGameShareUrl || window.location.href;
        const results = latestGameRankingResults || [];
        if (!results.length) {
            toastr.warning('Chưa có dữ liệu bảng xếp hạng để chia sẻ.');
            return null;
        }
        const topText = results.slice(0, 5).map((r, idx) => {
            return `${idx + 1}. ${r.student_name} - ${r.percentage || r.average_percentage || 0}% (${r.correct_count || r.total_correct}/${r.total_questions || 0})`;
        }).join('\n');
        return { summary: `Bảng xếp hạng game:\n${topText}\nXem thêm: ${shareUrl}`, shareUrl };
    }
    
    function shareFacebookRanking() {
        const data = buildRankingSummary();
        if (!data) return;
        
        // Chia sẻ vào Messenger sử dụng Facebook Send Dialog
        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        
        if (isMobile) {
            // Mobile: thử deep link để mở Messenger app
            const messengerDeepLink = `fb-messenger://share?link=${encodeURIComponent(data.shareUrl)}`;
            window.location.href = messengerDeepLink;
            
            // Fallback sau 1s nếu app không mở
            setTimeout(() => {
                const sendDialogUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(data.shareUrl)}&redirect_uri=${encodeURIComponent(data.shareUrl)}`;
                window.location.href = sendDialogUrl;
            }, 1000);
        } else {
            // Desktop: dùng Facebook Send Dialog
            const sendDialogUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(data.shareUrl)}&redirect_uri=${encodeURIComponent(window.location.href)}`;
            window.open(sendDialogUrl, '_blank', 'width=600,height=400,noopener');
        }
    }

    function shareFacebookGameRanking() {
        const data = buildGameRankingSummary();
        if (!data) return;
        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        if (isMobile) {
            const messengerDeepLink = `fb-messenger://share?link=${encodeURIComponent(data.shareUrl)}`;
            window.location.href = messengerDeepLink;
            setTimeout(() => {
                const sendDialogUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(data.shareUrl)}&redirect_uri=${encodeURIComponent(data.shareUrl)}`;
                window.location.href = sendDialogUrl;
            }, 1000);
        } else {
            const sendDialogUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(data.shareUrl)}&redirect_uri=${encodeURIComponent(window.location.href)}`;
            window.open(sendDialogUrl, '_blank', 'width=600,height=400,noopener');
        }
    }
    
    
    function shareZaloRanking() {
        const data = buildRankingSummary();
        if (!data) return;
        // Chỉ copy link và thông báo cho người dùng tự mở Zalo, dán thủ công
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(data.shareUrl)
                .then(() => toastr.success('Đã copy link. Vui lòng mở Zalo và dán vào tin nhắn.'))
                .catch(() => prompt('Copy link này và dán vào Zalo:', data.shareUrl));
        } else {
            prompt('Copy link này và dán vào Zalo:', data.shareUrl);
        }
    }

    function shareZaloGameRanking() {
        const data = buildGameRankingSummary();
        if (!data) return;
        // Chỉ copy link và thông báo
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(data.shareUrl)
                .then(() => toastr.success('Đã copy link. Vui lòng mở Zalo và dán vào tin nhắn.'))
                .catch(() => prompt('Copy link này và dán vào Zalo:', data.shareUrl));
        } else {
            prompt('Copy link này và dán vào Zalo:', data.shareUrl);
        }
    }

    // Chọn/bỏ chọn tất cả
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('select-all-students');
        const checkboxes = document.querySelectorAll('.student-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        
        updateSelectedCount();
    }

    // Cập nhật số lượng đã chọn
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        const selectedCount = checkboxes.length;
        const selectAllCheckbox = document.getElementById('select-all-students');
        const deleteSelectedBtn = document.getElementById('delete-selected-btn');
        
        document.getElementById('selected-count').textContent = selectedCount;
        
        // Cập nhật trạng thái "Chọn tất cả"
        const allCheckboxes = document.querySelectorAll('.student-checkbox');
        if (allCheckboxes.length > 0) {
            selectAllCheckbox.checked = selectedCount === allCheckboxes.length;
            selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < allCheckboxes.length;
        }
        
        // Hiển thị/ẩn nút xóa
        if (selectedCount > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    // Xóa nhiều học sinh đã chọn
    function deleteSelectedStudents() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        const selectedIds = Array.from(checkboxes).map(cb => cb.value);
        const selectedNames = Array.from(checkboxes).map(cb => cb.getAttribute('data-student-name'));
        
        if (selectedIds.length === 0) {
            toastr.warning('Vui lòng chọn ít nhất một học sinh để xóa.');
            return;
        }
        
        const namesList = selectedNames.slice(0, 3).join(', ');
        const moreText = selectedNames.length > 3 ? ` và ${selectedNames.length - 3} học sinh khác` : '';
        
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa dữ liệu?',
            html: `Tất cả kết quả game của <strong>${selectedIds.length} học sinh</strong> sẽ bị xóa:<br><strong>${namesList}${moreText}</strong><br><br>Học sinh có thể bắt đầu làm lại từ đầu.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/tro-choi/xoa-nhieu-ket-qua-hoc-sinh', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_ids: selectedIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        // Đóng popup
                        const modalEl = document.getElementById('overallRankingModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    } else {
                        toastr.error(data.message || 'Đã xảy ra lỗi khi xóa dữ liệu');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Đã xảy ra lỗi khi xóa dữ liệu');
                });
            }
        });
    }

    // Xóa dữ liệu của học sinh
    function deleteStudentResults(studentId, studentName) {
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa dữ liệu?',
            html: `Tất cả kết quả game của học sinh <strong>${studentName}</strong> sẽ bị xóa.<br>Học sinh có thể bắt đầu làm lại từ đầu.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/tro-choi/xoa-ket-qua-hoc-sinh/${studentId}`, {
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
                        // Đóng popup
                        const modalEl = document.getElementById('overallRankingModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    } else {
                        toastr.error(data.message || 'Đã xảy ra lỗi khi xóa dữ liệu');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Đã xảy ra lỗi khi xóa dữ liệu');
                });
            }
        });
    }

    // Expose functions used by inline onclick
    window.showRanking = showRanking;
    window.copyLink = copyLink;
    window.restartGame = restartGame;
    window.deleteGame = deleteGame;
    window.viewResult = viewResult;
    window.showRewardConfig = showRewardConfig;
    window.shareFacebookGameRanking = shareFacebookGameRanking;
    window.shareZaloGameRanking = shareZaloGameRanking;
</script>
@endsection
@section('content_crm_course')
<main class="main-content-wrap">
    <!-- Start Features Area -->
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        <h3>Danh sách game Ai là triệu phú toán học</h3>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Trang chủ</li>
                        <li>Danh sách game Ai là triệu phú toán học</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="contact-list-area">
        <div class="container-fluid">
            <div class="form-file-upload">
                <form class="row g-2 align-items-end" method="GET" action="{{ route('gamelistAITrieuPhuToanHoc') }}">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label text-muted mb-1">Tên bài game</label>
                        <div class="position-relative">
                            <input type="text" name="name" class="form-control" placeholder="Nhập tên bài game"
                                value="{{ $filters['name'] ?? '' }}">
                            <img src="/frontend/crm-course/images/search-normal.svg" alt="search-normal"
                                class="position-absolute top-50 end-0 translate-middle-y me-3" style="width:18px;">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-muted mb-1">Lớp</label>
                        <select name="grade_id" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ isset($filters['grade_id']) && $filters['grade_id']==$grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-muted mb-1">Môn</label>
                        <select name="subject_id" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ isset($filters['subject_id']) && $filters['subject_id']==$subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        <a href="{{ route('gamelistAITrieuPhuToanHoc') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
               
                    <ul class="create-upload d-lg-flex mt-3">
                        @if($profile->type == 1 || $profile->type == 3)
                        <li class="mb-3 mt-3 mt-lg-0">
                            <a href="{{ route('tutaodethi', ['type' => 'game']) }}" class="upload-btn upload create-folder">
                                Tạo game
                                <img src="/frontend/crm-course/images/folder-plus-svgrepo-com.svg" alt="add-circle">
                            </a>
                        </li>
                        <li class="mb-3 mt-3 mt-lg-0">
                            <a href="{{ route('uploadFile', ['type' => 'game']) }}" class="upload-btn upload">
                                Nhập game từ file
                                <img src="/frontend/crm-course/images/export.svg" alt="export">
                            </a>
                        </li>
                        @endif
                        <li class="mb-3 mt-3 mt-lg-0">
                            <a href="javascript:void(0)" onclick="showOverallRanking()" class="upload-btn upload">
                               Bảng xếp hạng tổng quát
                                <img src="/frontend/crm-course/images/rating-svgrepo-com.svg" alt="export">
                            </a>
                        </li>
                    </ul>
                
            </div>
            <div class="table-responsive" data-simplebar>
                <table class="table align-middle mb-0">
                    <tbody>
                        <tr>
                            <td>
                               <b>Tên game</b>
                            </td>
                            <td>
                                <b>Lớp</b>
                            </td>
                            <td>
                                <b>Môn</b>
                            </td>
                            <td>
                                <b>Số lượt chơi</b>
                            </td>
                            @if($profile->type == 0)
                            <td>
                                <b>Tình trạng</b>
                            </td>
                            @endif
                            <td>
                                <b>Hành động</b>
                            </td>
                        </tr>
                        @forelse($games as $item)
                        <tr>
                            <td>
                                <div class=" d-flex align-items-center">
                                    <label class="form-check-label ms-2">
                                        <img width="30" src="/frontend/crm-course/images/11-game-s-svgrepo-com.svg" alt="user-2">
                                    </label>
                                    <div class="info ml-3">
                                        <h4>
                                            <a href="{{ route('editDeThi',['id'=>$item->id]) }}">{{ $item->title }}</a>
                                            <button type="button" class="badge rounded-pill text-bg-success ranking-badge border-0" onclick='showRanking({{ $item->id }}, @json($item->title))'> <i class="fa fa-trophy text-white"></i> Xem xếp hạng</button>
                                        </h4>
                                        @if($profile->type == 3 && $item->customer)
                                            <div class="text-muted small">
                                                @if($item->customer->type == 1)
                                                    <i class="fas fa-user"></i> Giáo viên: {{ $item->customer->name }}
                                                @elseif($item->customer->type == 3)
                                                    <i class="fas fa-crown"></i> Cánh Én
                                                @endif
                                            </div>
                                        @endif
                                   </div>
                                </div>
                            </td>
                            <td>
                                {{$item->gradeCategory->name ?? 'N/A'}}
                            </td>
                            <td>
                                {{$item->subjectCategory->name ?? 'N/A'}}
                            </td>
                            <td>
                                {{count($item->game_results) ?? 0}}
                            </td>
                            @if($profile->type == 0)
                            <td>
                                @if (isset($item->game_results))
                                    <span class="badge bg-success">Đã chơi</span>
                                @else
                                    <span class="badge bg-danger">Chưa chơi</span>  
                                @endif
                            </td>
                            @endif
                            <td>
                                <a href="javascript:void(0)" class="btn btn-primary" onclick="copyLink('{{route('startGame', ['id' => $item->id])}}')" data-toggle="tooltip" data-placement="top" title="Copy link game"> <i class="fa fa-copy text-white"></i> </a>
                                @if($profile->type == 1 || $profile->type == 3)
                                <a href="javascript:void(0)" class="btn btn-info" onclick="showRewardConfig({{$item->id}}, '{{$item->title}}')" data-toggle="tooltip" data-placement="top" title="Cấu hình phần thưởng"> <i class="fa fa-gift text-white"></i> </a>
                                <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteGame({{$item->id}})" data-toggle="tooltip" data-placement="top" title="Xóa game" > <i class="fa fa-trash text-white"></i> </a>
                                <a href="javascript:void(0)" class="btn btn-warning" onclick="restartGame({{$item->id}})" data-toggle="tooltip" data-placement="top" title="Thi Lại"> <i class="fa fa-refresh text-white"></i> </a>
                                @else 
                                <a href="javascript:void(0)" class="btn btn-warning" onclick="viewResult({{$item->id}})" data-toggle="tooltip" data-placement="top" title="Xem kết quả"> <i class="fa fa-eye text-white"></i> </a>
                                <a  href="{{route('startGame', ['id' => $item->id])}}" class="btn btn-success"  data-toggle="tooltip" data-placement="top" title="Chơi ngay"> <i class="fa fa-play text-white"></i> </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có game nào được tạo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Ranking Modal -->
    <div class="modal fade ranking-modal" id="rankingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rankingModalLabel">Bảng xếp hạng</h5>
                    <div class="share-inline">
                        <span class="share-label">Share:</span>
                        <button type="button" class="share-btn share-fb" onclick="shareFacebookGameRanking()">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button type="button" class="share-btn share-zalo" onclick="shareZaloGameRanking()">
                            Zalo
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Học sinh</th>
                                    <th class="text-center">Đúng/Tổng</th>
                                    <th class="text-center">Điểm (%)</th>
                                    <th class="text-center">Phần thưởng</th>
                                    <th class="text-center">Nộp lúc</th>
                                </tr>
                            </thead>
                            <tbody id="ranking-table-body">
                            </tbody>
                        </table>
                    </div>
                    <p id="ranking-summary" class="mt-3 text-muted"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Result Modal -->
    <div class="modal fade" id="studentResultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 bg-gradient-primary text-white">
                    <h5 class="modal-title d-flex align-items-center gap-2 text-white" id="studentResultModalLabel">
                        <i class="fa fa-award"></i> Kết quả của tôi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <div id="student-result-content">
                        <div class="text-center text-muted py-4">
                            Đang tải dữ liệu...
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reward Config Modal -->
    <div class="modal fade reward-config-modal" id="rewardConfigModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rewardConfigModalLabel">Cấu hình phần thưởng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hướng dẫn -->
                    <div class="alert alert-info mb-3">
                        <small>
                            <strong>Ví dụ:</strong> 70% → 1 quà (Min=70, Max=99.99, Qty=1) | 
                            100% → 2 quà (Min=100, Max=trống, Qty=2, Priority=10)
                        </small>
                    </div>

                    <!-- Form thêm cấu hình -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-plus-circle"></i> Thêm cấu hình mới</h6>
                        </div>
                        <div class="card-body">
                            <form id="reward-config-form">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Quà tặng <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-sm" id="config-reward-id" name="reward_id" required>
                                            <option value="">-- Chọn quà tặng --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">% Min <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-sm" id="config-min-percentage" 
                                               name="min_percentage" min="0" max="100" step="0.01" required placeholder="70">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">% Max</label>
                                        <input type="number" class="form-control form-control-sm" id="config-max-percentage" 
                                               name="max_percentage" min="0" max="100" step="0.01" placeholder="99.99">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-sm" id="config-quantity" 
                                               name="quantity" min="1" max="10" value="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ưu tiên</label>
                                        <input type="number" class="form-control form-control-sm" id="config-priority" 
                                               name="priority" min="0" value="0">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save"></i> Thêm cấu hình
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Danh sách cấu hình -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-list"></i> Danh sách cấu hình</h6>
                        </div>
                        <div class="card-body">
                            <div id="reward-configs-list">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Ranking Modal -->
    <div class="modal fade ranking-modal" id="overallRankingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="overallRankingModalLabel">Bảng xếp hạng tổng quát</h5>
                    <div class="share-inline">
                        <span class="share-label">Share:</span>
                        <button type="button" class="share-btn share-fb" onclick="shareFacebookRanking()">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button type="button" class="share-btn share-zalo" onclick="shareZaloRanking()">
                            Zalo
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <input type="checkbox" id="select-all-students" onchange="toggleSelectAll()">
                                    </th>
                                    <th class="text-center">#</th>
                                    <th>Học sinh</th>
                                    <th class="text-center">Số game đã chơi</th>
                                    <th class="text-center">Điểm trung bình (%)</th>
                                    <th class="text-center">Tổng đúng/Tổng câu</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="overall-ranking-table-body">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p id="overall-ranking-summary" class="mt-3 text-muted"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete-selected-btn" onclick="deleteSelectedStudents()" style="display: none;">
                        <i class="fa fa-trash"></i> Xóa đã chọn (<span id="selected-count">0</span>)
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Features Area -->
    <!-- Start Footer Area -->
    @include('crm_course.main.footer')
    <!-- End Footer Area -->

 </main>

@endsection