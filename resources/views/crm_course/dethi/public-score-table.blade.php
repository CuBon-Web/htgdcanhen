<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bảng điểm - {{ $dethi->title ?? 'Đề thi' }}</title>
    <meta name="description" content="Bảng điểm đề thi {{ $dethi->title ?? '' }}">
    <meta property="og:title" content="Bảng điểm - {{ $dethi->title ?? 'Đề thi' }}">
    <meta property="og:description" content="Bảng điểm đề thi {{ $dethi->title ?? '' }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:type" content="website">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
        }
        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }
        .card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            padding: 24px;
            max-width: 1100px;
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header h1 {
            margin: 0 0 8px;
            color: #4f46e5;
            font-size: 28px;
        }
        .header p { margin: 0; color: #555; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead { background: #f8f9fa; }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        tbody tr:hover { background: #f8fafc; }
        .rank-1 { background: rgba(255, 215, 0, 0.15); font-weight: 600; }
        .rank-2 { background: rgba(192, 192, 192, 0.15); font-weight: 600; }
        .rank-3 { background: rgba(205, 127, 50, 0.15); font-weight: 600; }
        .rank-badge { font-size: 22px; }
        .rank-1 .rank-badge { color: #FFD700; }
        .rank-2 .rank-badge { color: #C0C0C0; }
        .rank-3 .rank-badge { color: #CD7F32; }
        .share {
            text-align: center;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 2px solid #e5e7eb;
        }
        .share .btn {
            margin: 5px;
            padding: 9px 14px;
            border: none;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-fb { background: #1877f2; }
        .btn-zalo { background: #0f8ee0; }
        .no-data { text-align: center; color: #888; padding: 40px 10px; }
        @media (max-width: 768px) {
            .card { padding: 18px; }
            th, td { padding: 10px 8px; font-size: 13px; }
            .header h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1><i class="fas fa-list-ol"></i> Bảng điểm - {{ $dethi->title ?? 'Đề thi' }}</h1>
                <p>Tổng số học sinh: <strong>{{ $totalStudents }}</strong></p>
            </div>

            @if(count($results) > 0)
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 200px;">Họ Tên</th>
                            <th style="width: 120px;">Điểm</th>
                            <th style="width: 140px;">Thời gian (phút)</th>
                            <th style="width: 160px;">Nộp lúc</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $row)
                        <tr class="rank-{{ $row['rank'] <= 3 ? $row['rank'] : '' }}">
                            <td style="text-align: left; padding-left: 18px;">
                                <strong>{{ $row['student_name'] }}</strong>
                            </td>
                            <td><strong style="color:#4f46e5">{{ $row['score'] }}/{{ $row['max_score'] }}</strong></td>
                            <td>{{ $row['time_minutes'] }}</td>
                            <td>{{ $row['finished_at'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="share">
                <p><strong>Chia sẻ bảng điểm:</strong></p>
                <button class="btn btn-fb" onclick="shareFacebook()"><i class="fab fa-facebook-f"></i> Share Facebook</button>
                <button class="btn btn-zalo" onclick="shareZalo()"><i class="fas fa-comment-dots"></i> Share Zalo</button>
            </div>
            @else
            <div class="no-data">
                <i class="fas fa-inbox"></i>
                <h3>Chưa có dữ liệu</h3>
                <p>Chưa có học sinh nào hoàn thành đề thi này.</p>
            </div>
            @endif
        </div>
    </div>

    <script>
        const shareUrl = window.location.href;
        function shareFacebook() {
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
        function shareZalo() {
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

