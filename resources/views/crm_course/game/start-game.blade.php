@extends('crm_course.main.master')
@section('title')
{{ $game->title }} - Game
@endsection
@section('description')
{{ $game->title }} - Game
@endsection
@section('css_crm_course')
<style>
    .game-container {
        min-height: 100vh;
        background-image: url(https://storage.googleapis.com/azt_agents/html_content_agent/76552763/c86f96c3-481d-45d7-8d11-8b4a1a060d6a/millionaire_background.png/0);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .game-card {
        background: rgba(0, 0, 0, 0.75);
        max-width: 1200px;
        width: 100%;
        padding: 20px;
        position: relative;
    }
    
    .game-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .game-header-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 1;
    }
    
    .game-title {
        font-size: 32px;
        font-weight: bold;
        color: #FFD700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    .lifelines-container {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .lifeline-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(0, 50, 100, 0.8);
        border: 2px solid #4A9EFF;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 17px;
    }
    
    .lifeline-btn:hover:not(:disabled) {
        transform: scale(1.1);
        box-shadow: 0 0 15px rgba(74, 158, 255, 0.6);
    }
    
    .lifeline-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: rgba(50, 50, 50, 0.8);
    }
    
    .lifeline-btn.used {
        opacity: 0.5;
        background: rgba(50, 50, 50, 0.8);
        border-color: #666;
    }
    
    .lifeline-label {
        font-size: 12px;
        margin-top: 5px;
        color: #FFD700;
    }
    
    .game-header-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 15px;
    }
    
    .status-display {
        display: flex;
        align-items: center;
        gap: 15px;
        color: #FFD700;
        font-size: 18px;
        font-weight: 600;
    }
    
    .correct-count {
        color: #FFD700;
    }
    
    .timer-container {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #FFD700;
    }
    
    .timer-display {
        color: #FFD700;
        font-size: 18px;
        font-weight: bold;
        font-family: 'Courier New', monospace;
        min-width: 60px;
    }
    
    .timer-display.warning {
        animation: pulse 1s infinite;
        color: #FFA500;
    }
    
    .timer-display.danger {
        animation: pulse 0.5s infinite;
        color: #FF4444;
    }
    
    .progress-section {
        background: rgba(0, 50, 100, 0.6);
        border: 2px solid #4A9EFF;
        border-radius: 10px;
        padding: 15px;
        position: sticky;
        top: 20px;
        margin-bottom: 20px;
    }
    
    @media (max-width: 991px) {
        .progress-section {
            position: relative;
            top: 0;
            margin-top: 20px;
        }
    }
    
    .progress-title {
        color: #FFD700;
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
        text-align: center;
    }
    
    .progress-items {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .progress-item {
        padding: 8px 12px;
        border-radius: 5px;
        text-align: center;
        font-size: 14px;
        color: white;
    }
    
    .progress-item.active {
        background: #FFD700;
        color: #000;
        font-weight: bold;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .question-content {
        background:rgba(10,25,80,.8);
        border: 3px solid #4A9EFF;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        align-items: center;
        font-size: 19px;
        line-height: 1.6;
        color: white;
        text-align: center;
    }
    
    .answers-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    @media (max-width: 768px) {
        .answers-container {
            grid-template-columns: 1fr;
        }
        .game-header {
            flex-direction: column;
        }
        .game-header-right {
            align-items: flex-start;
            width: 100%;
        }
        .question-content {
            padding: 15px;
            font-size: 16px;
        }
    }
    
    .answer-btn {    background: linear-gradient(145deg,#0a1f44,#1a3a6a);
    border: 3px solid #4A9EFF;
    border-radius: 12px;
    padding: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
    position: relative;
    overflow: hidden;
    color: white;
    }
    
    .answer-btn:hover:not(:disabled) {
        transform: translateY(-3px);
        box-shadow: 0 0 20px rgba(74, 158, 255, 0.6);
        border-color: #FFD700;
    }
    
    .answer-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .answer-btn.correct {
        background: rgba(0, 150, 0, 0.8);
        border-color: #00FF00;
        animation: correctPulse 0.5s ease;
    }
    
    .answer-btn.incorrect {
        background: rgba(150, 0, 0, 0.8);
        border-color: #FF4444;
        animation: incorrectShake 0.5s ease;
    }
    
    .answer-label {
        display: inline-block;
        color: #FFD700;
        font-weight: bold;
        margin-right: 15px;
        font-size: 24px;
    }
    
    .back-to-menu-btn {
        background: #666;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: block;
        margin: 30px auto 0;
    }
    
    .back-to-menu-btn:hover {
        background: #777;
        transform: translateY(-2px);
    }
    
    .result-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    
    .result-modal {
        background: white;
        border-radius: 20px;
        padding: 40px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        animation: modalSlideIn 0.3s ease;
    }
    
    .result-title {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
    }
    
    .result-score {
        font-size: 48px;
        font-weight: bold;
        color: #667eea;
        margin: 20px 0;
    }
    
    .result-info {
        font-size: 18px;
        color: #666;
        margin-bottom: 30px;
    }
    
    .result-section {
        margin-top: 30px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 25px;
        color: #fff;
        backdrop-filter: blur(6px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    }
    
    .result-section h2 {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .result-section .result-score-inline {
        font-size: 40px;
        font-weight: 700;
        color: #ffeb3b;
        margin: 10px 0;
    }
    
    .result-section .result-info-inline {
        font-size: 18px;
        margin-bottom: 20px;
    }
    
    .rewards-container {
        margin: 30px 0;
        padding: 20px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-radius: 15px;
        text-align: center;
    }
    
    .rewards-title {
        font-size: 24px;
        font-weight: bold;
        color: white;
        margin-bottom: 20px;
    }
    
    .rewards-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
    }
    
    .reward-item {
        background: white;
        border-radius: 12px;
        padding: 15px;
        min-width: 150px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        animation: rewardSlideIn 0.5s ease;
    }
    
    .reward-item:nth-child(1) { animation-delay: 0.1s; }
    .reward-item:nth-child(2) { animation-delay: 0.2s; }
    .reward-item:nth-child(3) { animation-delay: 0.3s; }
    .reward-item:nth-child(4) { animation-delay: 0.4s; }
    
    @keyframes rewardSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .reward-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin: 0 auto 10px;
        display: block;
    }
    
    .reward-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .reward-quantity {
        font-size: 14px;
        color: #667eea;
        font-weight: bold;
    }
    
    .no-rewards {
        color: white;
        font-size: 16px;
        font-style: italic;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    @keyframes correctPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    @keyframes incorrectShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .explanation-box {
        background: rgba(0, 50, 100, 0.8);
        border: 2px solid #4A9EFF;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
        display: none;
        color: white;
    }
    
    .explanation-box strong {
        color: #FFD700;
    }
    
    .explanation-box.show {
        display: block;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endsection
@section('content_crm_course')
<div class="game-container">
    <div class="game-card">
        <div class="game-header">
            <div class="game-header-left">
                <h1 class="game-title">Ai là Triệu phú</h1>
                <div class="lifelines-container">
                    <button class="lifeline-btn" id="lifeline-5050" title="50/50">
                        <i class="fas fa-percent"></i>
                    </button>
                    <button class="lifeline-btn" id="lifeline-teacher" title="Hỏi Giáo viên">
                        <i class="fas fa-user-graduate"></i>
                    </button>
                </div>
            </div>
            <div class="game-header-right">
                <div class="status-display">
                    <span class="correct-count">Đúng: <span id="correct-count-display">0</span>/<span id="total-questions-display">{{ $totalQuestions }}</span></span>
                    @if($timeLimit > 0)
                    <div class="timer-container" id="timer-container">
                        <span>⏱️</span>
                        <span class="timer-display" id="timer-display">--:--</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-9 col-md-8">
                <div class="question-content" id="question-content">
                    <div class="loading-spinner"></div>
                </div>
                
                <div class="answers-container" id="answers-container">
                    <!-- Answers will be loaded here -->
                </div>
                
                <div class="explanation-box" id="explanation-box">
                    <strong>Giải thích:</strong>
                    <p id="explanation-text"></p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-4">
                <div class="progress-section">
                    <div class="progress-title">Tiến độ</div>
                    <div class="progress-items" id="progress-items">
                        @for($i = 1; $i <= $totalQuestions; $i++)
                        <div class="progress-item {{ $i == 1 ? 'active' : '' }}" data-question="{{ $i }}">Câu {{ $i }}</div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="result-section" id="result-section" style="display: none;">
        <h2>Kết quả của bạn</h2>
        <div class="result-score-inline" id="result-inline-score">0/0</div>
        <div class="result-info-inline" id="result-inline-info"></div>
        <div class="rewards-container" id="inline-rewards-container" style="display: none;">
            <div class="rewards-title">🎁 Phần thưởng nhận được</div>
            <div class="rewards-list" id="inline-rewards-list"></div>
        </div>
        <button class="btn-primary-custom mt-3" onclick="window.location.href='{{ route('gamelistAITrieuPhuToanHoc') }}'">
            Quay lại danh sách game
        </button>
    </div>
</div>

<!-- Result Modal -->
<div class="result-overlay" id="result-overlay">
    <div class="result-modal">
        <h2 class="result-title">Kết quả</h2>
        <div class="result-score" id="result-score">0/0</div>
        <div class="result-info" id="result-info"></div>
        
        <!-- Rewards Section -->
        <div class="rewards-container" id="rewards-container" style="display: none;">
            <div class="rewards-title">🎁 Phần thưởng của bạn</div>
            <div class="rewards-list" id="rewards-list"></div>
        </div>
        
        <button class="btn-primary-custom" onclick="window.location.href='{{ route('gamelistAITrieuPhuToanHoc') }}'">
            Quay lại danh sách
        </button>
    </div>
</div>
@endsection

@section('js_crm_course')
<!-- MathJax CDN for LaTeX rendering -->
<script>
    window.MathJax = {
        tex: {
            inlineMath: [
                ['$', '$'],
                ['\\(', '\\)']
            ],
            displayMath: [
                ['$$', '$$'],
                ['\\[', '\\]']
            ]
        },
        options: {
            skipHtmlTags: ['script', 'style', 'textarea', 'pre', 'code'],
            ignoreHtmlClass: 'no-mathjax'
        }
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script>
    const gameId = {{ $game->id }};
    const timeLimit = {{ $timeLimit }}; // Thời gian làm bài (phút), 0 = không giới hạn
    const attemptToken = @json($attemptToken);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentQuestionIndex = 0;
    let totalQuestions = {{ $totalQuestions }};
    let correctCount = 0;
    let currentQuestion = null;
    let isAnswerSubmitted = false;
    let timeRemaining = 0; // Thời gian còn lại (giây)
    let timerInterval = null;
    let gameFinished = false;
    let autoSubmitSent = false;
    let lifeline5050Used = false;
    let lifelineTeacherUsed = false;
    
    // Function to render MathJax
    function renderMathJax(element) {
        if (window.MathJax && window.MathJax.typesetPromise) {
            return MathJax.typesetPromise([element]).catch(function (err) {
                console.warn('MathJax rendering error:', err);
            });
        } else if (window.MathJax && window.MathJax.Hub) {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, element]);
        }
    }
    
    // Format thời gian (giây) thành MM:SS
    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }
    
    // Khởi tạo timer
    function initTimer() {
        if (timeLimit <= 0) {
            return; // Không có giới hạn thời gian
        }
        
        timeRemaining = timeLimit * 60; // Chuyển phút sang giây
        updateTimerDisplay();
        
        timerInterval = setInterval(() => {
            if (gameFinished) {
                clearInterval(timerInterval);
                return;
            }
            
            timeRemaining--;
            updateTimerDisplay();
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                timeExpired();
            }
        }, 1000);
    }
    
    // Cập nhật hiển thị timer
    function updateTimerDisplay() {
        const timerDisplay = document.getElementById('timer-display');
        if (!timerDisplay) return;
        
        timerDisplay.textContent = formatTime(timeRemaining);
        
        // Thay đổi màu sắc khi thời gian sắp hết
        timerDisplay.classList.remove('warning', 'danger');
        if (timeRemaining <= 60) {
            timerDisplay.classList.add('danger');
        } else if (timeRemaining <= 300) { // 5 phút
            timerDisplay.classList.add('warning');
        }
    }
    
    // Xử lý khi hết thời gian
    function timeExpired() {
        if (gameFinished) return;
        
        gameFinished = true;
        clearInterval(timerInterval);
        
        // Disable tất cả buttons
        const allButtons = document.querySelectorAll('.answer-btn');
        allButtons.forEach(btn => btn.disabled = true);
        
        // Cập nhật timer display
        const timerDisplay = document.getElementById('timer-display');
        if (timerDisplay) {
            timerDisplay.textContent = '00:00';
            timerDisplay.classList.add('danger');
        }
        
        // Nếu đang chờ kết quả của câu hỏi, đợi một chút rồi mới nộp bài
        if (isAnswerSubmitted) {
            // Đang chờ kết quả, đợi 1 giây rồi nộp bài
            setTimeout(() => {
                finishGame();
            }, 1000);
        } else {
            // Hiển thị thông báo và nộp bài ngay
            alert('Hết thời gian làm bài! Hệ thống sẽ tự động nộp bài của bạn.');
            finishGame();
        }
    }
    
    // Dừng timer
    function stopTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }
    
    // Load first question
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo timer nếu có giới hạn thời gian
        if (timeLimit > 0) {
            initTimer();
        }
        
        window.addEventListener('beforeunload', handleBeforeUnload);
        
        // Lifeline button handlers
        const lifeline5050 = document.getElementById('lifeline-5050');
        const lifelineTeacher = document.getElementById('lifeline-teacher');
        
        if (lifeline5050) {
            lifeline5050.addEventListener('click', function() {
                useLifeline5050();
            });
        }
        
        if (lifelineTeacher) {
            lifelineTeacher.addEventListener('click', function() {
                useLifelineTeacher();
            });
        }
        
        // Wait for MathJax to be ready
        if (window.MathJax && window.MathJax.startup) {
            MathJax.startup.promise.then(() => {
                loadQuestion(0);
            });
        } else {
            loadQuestion(0);
        }
    });
    
    // Hàm xử lý lifeline 50/50: Loại bỏ 2 đáp án sai
    function useLifeline5050() {
        if (lifeline5050Used || isAnswerSubmitted || gameFinished || !currentQuestion) {
            return;
        }
        
        lifeline5050Used = true;
        const lifelineBtn = document.getElementById('lifeline-5050');
        if (lifelineBtn) {
            lifelineBtn.disabled = true;
            lifelineBtn.classList.add('used');
        }
        
        // Lấy đáp án đúng từ server
        fetch(`/tro-choi/game/${gameId}/get-correct-answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                question_id: currentQuestion.id,
                attempt_token: attemptToken
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.correct_answer) {
                const correctLabel = data.correct_answer.label;
                const allButtons = document.querySelectorAll('.answer-btn');
                const wrongAnswers = [];
                
                // Tìm tất cả đáp án sai
                allButtons.forEach(btn => {
                    const label = btn.querySelector('.answer-label');
                    if (label && label.textContent.trim().replace(':', '') !== correctLabel) {
                        wrongAnswers.push(btn);
                    }
                });
                
                // Loại bỏ 2 đáp án sai ngẫu nhiên
                if (wrongAnswers.length >= 2) {
                    // Shuffle array để random
                    for (let i = wrongAnswers.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [wrongAnswers[i], wrongAnswers[j]] = [wrongAnswers[j], wrongAnswers[i]];
                    }
                    
                    // Ẩn 2 đáp án sai đầu tiên với hiệu ứng
                    wrongAnswers.slice(0, 2).forEach((btn, index) => {
                        // Thêm hiệu ứng fade out trước khi ẩn
                        btn.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        btn.style.opacity = '0';
                        btn.style.transform = 'scale(0.8)';
                        
                        setTimeout(() => {
                            btn.style.display = 'none';
                        }, 500);
                    });
                } else if (wrongAnswers.length === 1) {
                    // Nếu chỉ có 1 đáp án sai, ẩn nó luôn
                    wrongAnswers[0].style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    wrongAnswers[0].style.opacity = '0';
                    wrongAnswers[0].style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        wrongAnswers[0].style.display = 'none';
                    }, 500);
                }
            } else {
                alert('Không thể lấy đáp án đúng. Vui lòng thử lại.');
                lifeline5050Used = false;
                if (lifelineBtn) {
                    lifelineBtn.disabled = false;
                    lifelineBtn.classList.remove('used');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi sử dụng trợ giúp 50/50.');
            lifeline5050Used = false;
            const lifelineBtn = document.getElementById('lifeline-5050');
            if (lifelineBtn) {
                lifelineBtn.disabled = false;
                lifelineBtn.classList.remove('used');
            }
        });
    }
    
    // Hàm xử lý lifeline Teacher: Tự động chọn đáp án đúng
    function useLifelineTeacher() {
        if (lifelineTeacherUsed || isAnswerSubmitted || gameFinished || !currentQuestion) {
            return;
        }
        
        lifelineTeacherUsed = true;
        const lifelineBtn = document.getElementById('lifeline-teacher');
        if (lifelineBtn) {
            lifelineBtn.disabled = true;
            lifelineBtn.classList.add('used');
        }
        
        // Lấy đáp án đúng từ server
        fetch(`/tro-choi/game/${gameId}/get-correct-answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                question_id: currentQuestion.id,
                attempt_token: attemptToken
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.correct_answer) {
                const correctAnswerId = data.correct_answer.id;
                const allButtons = document.querySelectorAll('.answer-btn');
                
                // Tìm và tự động chọn đáp án đúng
                allButtons.forEach(btn => {
                    const answerId = parseInt(btn.getAttribute('data-answer-id'));
                    if (answerId === correctAnswerId) {
                        // Tự động chọn đáp án đúng sau 500ms để người dùng thấy hiệu ứng
                        setTimeout(() => {
                            selectAnswer(correctAnswerId, btn);
                        }, 500);
                        return;
                    }
                });
            } else {
                alert('Không thể lấy đáp án đúng. Vui lòng thử lại.');
                lifelineTeacherUsed = false;
                if (lifelineBtn) {
                    lifelineBtn.disabled = false;
                    lifelineBtn.classList.remove('used');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi sử dụng trợ giúp Hỏi Giáo viên.');
            lifelineTeacherUsed = false;
            const lifelineBtn = document.getElementById('lifeline-teacher');
            if (lifelineBtn) {
                lifelineBtn.disabled = false;
                lifelineBtn.classList.remove('used');
            }
        });
    }
    
    function loadQuestion(index) {
        currentQuestionIndex = index;
        isAnswerSubmitted = false;
        
        // Update progress items
        const progressItems = document.querySelectorAll('.progress-item');
        progressItems.forEach((item, i) => {
            if (i === index) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
        
        // Show loading
        document.getElementById('question-content').innerHTML = '<div class="loading-spinner"></div>';
        document.getElementById('answers-container').innerHTML = '';
        document.getElementById('explanation-box').classList.remove('show');
        
        // Fetch question
        const questionUrl = `/tro-choi/game/${gameId}/question?index=${index}&attempt_token=${encodeURIComponent(attemptToken)}`;
        fetch(questionUrl, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentQuestion = data.question;
                displayQuestion(data.question);
            } else {
                alert('Không thể tải câu hỏi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi tải câu hỏi');
        });
    }
    
    function displayQuestion(question) {
        // Display question content with HTML support
        const questionContent = document.getElementById('question-content');
        questionContent.innerHTML = `Câu ${currentQuestionIndex + 1}: ${question.content}`;
        
        // Render MathJax for question
        renderMathJax(questionContent).then(() => {
            // Display answers after MathJax is rendered
            const answersContainer = document.getElementById('answers-container');
            answersContainer.innerHTML = '';
            
            question.answers.forEach((answer, index) => {
                const answerBtn = document.createElement('button');
                answerBtn.className = 'answer-btn';
                answerBtn.setAttribute('data-label', answer.label);
                answerBtn.setAttribute('data-content', answer.content);
                answerBtn.setAttribute('data-answer-id', answer.id);
                answerBtn.style.display = ''; // Đảm bảo hiển thị lại khi load câu hỏi mới
                answerBtn.innerHTML = `
                    <span class="answer-label">${answer.label}:</span>
                    <span>${answer.content}</span>
                `;
                answerBtn.onclick = () => selectAnswer(answer.id, answerBtn);
                answersContainer.appendChild(answerBtn);
                
                // Render MathJax for each answer button
                renderMathJax(answerBtn);
            });
        });
    }
    
    function selectAnswer(answerId, buttonElement) {
        if (isAnswerSubmitted || gameFinished) return;
        
        isAnswerSubmitted = true;
        
        // Disable all buttons
        const allButtons = document.querySelectorAll('.answer-btn');
        allButtons.forEach(btn => btn.disabled = true);
        
        // Show loading on selected button
        buttonElement.innerHTML = '<div class="loading-spinner"></div>';
        
        // Submit answer
        fetch(`/tro-choi/game/${gameId}/submit-answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                question_id: currentQuestion.id,
                answer_id: answerId,
                question_index: currentQuestionIndex,
                attempt_token: attemptToken
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                handleAnswerResult(data, buttonElement, allButtons);
            } else {
                // Restore button content
                const label = buttonElement.getAttribute('data-label') || '';
                const content = buttonElement.getAttribute('data-content') || '';
                if (label && content) {
                    buttonElement.innerHTML = `
                        <span class="answer-label">${label}:</span>
                        <span>${content}</span>
                    `;
                    renderMathJax(buttonElement);
                }
                alert('Đã xảy ra lỗi: ' + (data.message || 'Lỗi không xác định'));
                // Re-enable buttons
                allButtons.forEach(btn => btn.disabled = false);
                isAnswerSubmitted = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Restore button content
            const label = buttonElement.getAttribute('data-label') || '';
            const content = buttonElement.getAttribute('data-content') || '';
            if (label && content) {
                buttonElement.innerHTML = `
                    <span class="answer-label">${label}:</span>
                    <span>${content}</span>
                `;
                renderMathJax(buttonElement);
            }
            alert('Đã xảy ra lỗi khi gửi đáp án. Vui lòng thử lại.');
            // Re-enable buttons
            allButtons.forEach(btn => btn.disabled = false);
            isAnswerSubmitted = false;
        });
    }
    
    function handleAnswerResult(data, selectedButton, allButtons) {
        // Mark correct answer
        if (data.correct_answer && data.correct_answer.label) {
            allButtons.forEach(btn => {
                const label = btn.querySelector('.answer-label');
                if (label && label.textContent.trim().replace(':', '') === data.correct_answer.label) {
                    btn.classList.add('correct');
                }
            });
        }
        
        // Mark selected answer
        if (data.is_correct) {
            selectedButton.classList.add('correct');
            correctCount++;
            // Update correct count display
            const correctCountDisplay = document.getElementById('correct-count-display');
            if (correctCountDisplay) {
                correctCountDisplay.textContent = correctCount;
            }
        } else {
            selectedButton.classList.add('incorrect');
        }
        
        // Restore button content (if it was replaced by loading spinner)
        const selectedLabel = selectedButton.getAttribute('data-label') || '';
        const selectedContent = selectedButton.getAttribute('data-content') || '';
        if (selectedLabel && selectedContent && !selectedButton.querySelector('.answer-label')) {
            selectedButton.innerHTML = `
                <span class="answer-label">${selectedLabel}:</span>
                <span>${selectedContent}</span>
            `;
            renderMathJax(selectedButton);
        }
        
        // Show explanation if available
        if (data.explanation) {
            const explanationText = document.getElementById('explanation-text');
            explanationText.innerHTML = data.explanation;
            document.getElementById('explanation-box').classList.add('show');
            // Render MathJax for explanation
            renderMathJax(explanationText);
        }
        
        // Move to next question after 2 seconds
        setTimeout(() => {
            // Kiểm tra xem có hết thời gian không
            if (gameFinished) {
                finishGame();
                return;
            }
            
            if (currentQuestionIndex + 1 < totalQuestions) {
                loadQuestion(currentQuestionIndex + 1);
            } else {
                // Game finished
                finishGame();
            }
        }, 2000);
    }
    
    function finishGame() {
        // Dừng timer
        stopTimer();
        gameFinished = true;
        autoSubmitSent = true;
        window.removeEventListener('beforeunload', handleBeforeUnload);
        
        // Save result
        fetch(`/tro-choi/game/${gameId}/save-result`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                correct_count: correctCount,
                total_questions: totalQuestions,
                attempt_token: attemptToken
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showResult(data.rewards || []);
            } else {
                alert('Đã xảy ra lỗi khi lưu kết quả: ' + data.message);
                showResult([]); // Still show result even if save fails
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showResult([]); // Still show result even if save fails
        });
    }
    
    function handleBeforeUnload(event) {
        if (gameFinished || autoSubmitSent) {
            return;
        }
        
        autoSubmitSent = true;
        
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('correct_count', correctCount);
        formData.append('total_questions', totalQuestions);
        formData.append('auto_submit', '1');
        formData.append('attempt_token', attemptToken);
        
        const url = `/tro-choi/game/${gameId}/save-result`;
        
        if (navigator.sendBeacon) {
            navigator.sendBeacon(url, formData);
        } else {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url, false);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.send(new URLSearchParams({
                correct_count: correctCount,
                total_questions: totalQuestions,
                auto_submit: 1,
                attempt_token: attemptToken,
            }));
        }
    }
    
    function showResult(rewards = []) {
        const percentage = Math.round((correctCount / totalQuestions) * 100);
        document.getElementById('result-score').textContent = `${correctCount}/${totalQuestions}`;
        document.getElementById('result-info').textContent = 
            `Bạn đã trả lời đúng ${correctCount} trên tổng số ${totalQuestions} câu hỏi (${percentage}%)`;
        
        // Hiển thị quà tặng nếu có (modal + inline)
        displayRewards(rewards);
        updateInlineResultSection(percentage, rewards);
        
        document.getElementById('result-overlay').style.display = 'flex';
    }
    
    function updateInlineResultSection(percentage, rewards) {
        const section = document.getElementById('result-section');
        const inlineScore = document.getElementById('result-inline-score');
        const inlineInfo = document.getElementById('result-inline-info');
        
        if (!section || !inlineScore || !inlineInfo) return;
        
        inlineScore.textContent = `${correctCount}/${totalQuestions}`;
        inlineInfo.textContent = `Bạn đã trả lời đúng ${correctCount}/${totalQuestions} câu hỏi (${percentage}%)`;
        renderRewards(rewards, 'inline-rewards-container', 'inline-rewards-list');
        
        section.style.display = 'block';
        section.scrollIntoView({ behavior: 'smooth' });
    }
    
    function displayRewards(rewards) {
        renderRewards(rewards, 'rewards-container', 'rewards-list');
    }
    
    function renderRewards(rewards, containerId, listId) {
        const rewardsContainer = document.getElementById(containerId);
        const rewardsList = document.getElementById(listId);
        
        if (!rewardsContainer || !rewardsList) return;
        
        if (!rewards || rewards.length === 0) {
            rewardsContainer.style.display = 'none';
            rewardsList.innerHTML = '';
            return;
        }
        
        rewardsContainer.style.display = 'block';
        rewardsList.innerHTML = '';
        
        rewards.forEach(reward => {
            const rewardItem = document.createElement('div');
            rewardItem.className = 'reward-item';
            
            let imageHtml = '';
            if (reward.image) {
                imageHtml = `<img src="${reward.image}" alt="${reward.name}" class="reward-image" onerror="this.style.display='none'">`;
            } else {
                imageHtml = `<div class="reward-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px;">🎁</div>`;
            }
            
            rewardItem.innerHTML = `
                ${imageHtml}
                <div class="reward-name">${reward.name}</div>
                ${reward.quantity > 1 ? `<div class="reward-quantity">x${reward.quantity}</div>` : ''}
            `;
            
            rewardsList.appendChild(rewardItem);
        });
    }
</script>
@endsection

