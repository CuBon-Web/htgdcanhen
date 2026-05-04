<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bảng xếp hạng - {{ $game->title }}</title>
    <meta name="description" content="Bảng xếp hạng game {{ $game->title }}">
    <meta property="og:title" content="Bảng xếp hạng - {{ $game->title }}">
    <meta property="og:description" content="Bảng xếp hạng game {{ $game->title }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:type" content="website">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .ranking-container {
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ranking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
            max-width: 1200px;
            width: 100%;
        }
        .ranking-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .ranking-header h1 {
            color: #667eea;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .ranking-header p { color: #666; font-size: 15px; }
        .ranking-table { width: 100%; border-collapse: collapse; }
        .ranking-table thead { background: #f8f9fa; }
        .ranking-table th {
            padding: 12px;
            text-align: center;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            font-size: 14px;
        }
        .ranking-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }
        .ranking-table tbody tr:hover { background: #f8f9fa; }
        .rank-1 { background: rgba(255, 215, 0, 0.15) !important; font-weight: 600; }
        .rank-2 { background: rgba(192, 192, 192, 0.15) !important; font-weight: 600; }
        .rank-3 { background: rgba(205, 127, 50, 0.15) !important; font-weight: 600; }
        .rank-badge { font-size: 22px; font-weight: bold; }
        .rank-1 .rank-badge { color: #FFD700; }
        .rank-2 .rank-badge { color: #C0C0C0; }
        .rank-3 .rank-badge { color: #CD7F32; }
        .share-buttons { text-align: center; margin-top: 24px; padding-top: 16px; border-top: 2px solid #dee2e6; }
        .share-buttons .btn {
            margin: 5px;
            padding: 9px 14px;
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-facebook { background: #1877f2; }
        .btn-zalo { background: #0f8ee0; }
        .no-data { text-align: center; padding: 60px 20px; color: #999; }
        .no-data i { font-size: 64px; margin-bottom: 20px; color: #ddd; }
        @media (max-width: 768px) {
            .ranking-card { padding: 20px; }
            .ranking-header h1 { font-size: 24px; }
            .ranking-table th, .ranking-table td { padding: 10px; font-size: 13px; }
        }
    </style>
</head>
<body>
<div class="ranking-container">
    <div class="ranking-card">
        <div class="ranking-header">
            <h1><i class="fas fa-trophy"></i> Bảng xếp hạng - {{ $game->title }}</h1>
            <p>Tổng số học sinh: <strong>{{ $totalStudents }}</strong></p>
        </div>
        
        @if(count($results) > 0)
        <div class="table-responsive">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Hạng</th>
                        <th>Học sinh</th>
                        <th style="width: 140px;">Đúng/Tổng</th>
                        <th style="width: 140px;">Điểm (%)</th>
                        <th style="width: 180px;">Nộp lúc</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                    <tr class="rank-{{ $result['rank'] <= 3 ? $result['rank'] : '' }}">
                        <td>
                            @if($result['rank'] == 1)
                                <span class="rank-badge"><i class="fas fa-medal"></i></span>
                            @elseif($result['rank'] == 2)
                                <span class="rank-badge"><i class="fas fa-medal"></i></span>
                            @elseif($result['rank'] == 3)
                                <span class="rank-badge"><i class="fas fa-medal"></i></span>
                            @else
                                <strong>#{{ $result['rank'] }}</strong>
                            @endif
                        </td>
                        <td style="text-align: left; padding-left: 20px;">
                            <strong>{{ $result['student_name'] }}</strong>
                        </td>
                        <td>{{ $result['correct_count'] }}/{{ $result['total_questions'] }}</td>
                        <td><strong style="color: #667eea;">{{ $result['percentage'] }}%</strong></td>
                        <td>{{ $result['created_at'] ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="share-buttons">
            <p class="mb-3"><strong>Chia sẻ bảng xếp hạng:</strong></p>
            <button type="button" class="btn btn-facebook" onclick="shareOnFacebook()">
                <i class="fab fa-facebook-f"></i> Share Facebook
            </button>
            <button type="button" class="btn btn-zalo" onclick="shareOnZalo()">
                <i class="fas fa-comment-dots"></i> Share Zalo
            </button>
        </div>
        @else
        <div class="no-data">
            <i class="fas fa-inbox"></i>
            <h3>Chưa có dữ liệu</h3>
            <p>Chưa có học sinh nào hoàn thành game.</p>
        </div>
        @endif
    </div>
</div>

<script>
    const shareUrl = window.location.href;
    function shareOnFacebook() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
            const messengerDeepLink = `fb-messenger://share?link=${encodeURIComponent(shareUrl)}`;
            window.location.href = messengerDeepLink;
            setTimeout(() => {
                const sendDialogUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(shareUrl)}&redirect_uri=${encodeURIComponent(shareUrl)}`;
                window.location.href = sendDialogUrl;
            }, 1000);
        } else {
            const sendDialogUrl = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(shareUrl)}&redirect_uri=${encodeURIComponent(window.location.href)}`;
            window.open(sendDialogUrl, '_blank', 'width=600,height=400,noopener');
        }
    }
    function shareOnZalo() {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(shareUrl)
                .then(() => alert('Đã copy link. Vui lòng mở Zalo và dán vào tin nhắn.'))
                .catch(() => prompt('Copy link này và dán vào Zalo:', shareUrl));
        } else {
            prompt('Copy link này và dán vào Zalo:', shareUrl);
        }
    }
</script>
</body>
</html>

