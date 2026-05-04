@extends('crm_course.main.master')
@section('title')
Tạo game mới
@endsection
@section('description')
Tạo game mới
@endsection
@section('image')
@endsection
@section('css_crm_course')
<style>
    .background-image {
        background-image: url('https://storage.googleapis.com/azt_agents/html_content_agent/76552763/c86f96c3-481d-45d7-8d11-8b4a1a060d6a/millionaire_background.png/0');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 15px;
        padding-top: 70px;
    }


    #game-container { width: 100%; max-width: 1200px; background: rgba(0, 0, 0, 0.75); border-radius: 15px; padding: 20px; box-shadow: 0 0 20px rgba(0, 255, 255, 0.5); position: relative; }

    #main-menu {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 300px;
    }

    .header h1 {
        font-size: 2.5rem;
        color: #ffc107;
        text-shadow: 2px 2px 4px #000;
    }

    #teacher-controls .btn {
        margin-top: 5px;
    }

    #game-view,
    #results-view {
        display: none;
    }

    #export-html-content {
        font-family: monospace;
        font-size: 0.8rem;
        height: 300px;
    }

    .modal-content {
        background-color: #0a1950;
        color: white;
        border: 2px solid #00ffff;
    }

    .modal-body .form-label {
        color: #ffc107
    }

    .modal-body .form-control,
    .modal-body .form-select {
        background-color: rgba(10, 25, 80, .9);
        color: #fff;
        border: 1px solid #00ffff
    }

    .modal-body .form-control:focus {
        box-shadow: 0 0 10px #00ffff
    }

    .nav-tabs .nav-link {
        background-color: transparent;
        border-color: #00ffff;
        color: #00ffff
    }

    .nav-tabs .nav-link.active {
        background-color: #0a1950;
        color: #fff
    }

    #preview-image-q,
    #preview-image-s {
        max-width: 100px;
        max-height: 100px;
        margin-top: 10px;
        border: 1px solid #00ffff;
        display: none;
    }

    .game-stats {
        font-size: 1.5rem;
        text-align: right;
        display: flex;
        gap: 20px;
        align-items: center;
    }

    #timer-display {
        color: #ffc107;
        font-weight: 700
    }

    .help-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px
    }

    .help-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #ffc107
    }

    .help-item span {
        margin-top: 5px;
        font-size: .9rem
    }

    .help-btn {
        background-color: #007bff;
        border: 2px solid #00ffff;
        color: #fff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        transition: all .2s ease-in-out
    }

    .help-btn:hover:not(:disabled) {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 255, 255, .4)
    }

    .help-btn:disabled {
        opacity: .5;
        background-color: #333;
        border-color: #666;
        cursor: not-allowed
    }

    .help-btn:disabled .fas {
        text-decoration: line-through
    }

    .help-item.disabled span {
        color: #6c757d;
        text-decoration: line-through
    }

    #question-container {
        background: rgba(10, 25, 80, .8);
        border: 2px solid #00ffff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        min-height: 100px;
        font-size: 1.5rem
    }

    .answer-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px
    }

    .answer-btn {
        background: linear-gradient(145deg, #0a1f44, #1a3a6a);
        border: 2px solid #555;
        color: #fff;
        font-size: 1.2rem;
        border-radius: 10px;
        text-align: left;
        padding: 15px;
        transition: all .2s ease
    }

    .answer-btn .option-letter {
        color: #ffc107;
        font-weight: 700
    }

    .answer-btn:hover:not([disabled]) {
        border-color: #ffc107;
        transform: scale(1.02)
    }

    .answer-btn.correct {
        background: #28a745;
        border-color: #1c7430
    }

    .answer-btn.wrong {
        background: #dc3545;
        border-color: #b02a37
    }

    #prize-ladder {
        list-style-type: none;
        padding: 10px;
        background: rgba(0, 0, 0, .5);
        border-radius: 10px
    }

    #prize-ladder li {
        padding: 5px 10px;
        margin: 2px 0;
        border-radius: 5px;
        color: #ffc107;
        opacity: .6;
        transition: all .3s
    }

    #prize-ladder li.current {
        background: #ffc107;
        color: #000;
        opacity: 1;
        transform: scale(1.05);
        font-weight: 700
    }

    #prize-ladder li.passed {
        opacity: 1;
        color: #28a745
    }

    #prize-ladder li.failed {
        opacity: 1;
        text-decoration: line-through;
        color: #dc3545
    }

    #results-view h2 {
        color: #ffc107
    }

    #rewardModal .modal-content {
        background: linear-gradient(145deg, #1a0555, #3a2a7a);
        border: 3px solid #ffd700;
    }

    #reward-image-container {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        padding: 20px;
        width: 150px;
        height: 150px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #reward-image {
        max-width: 100px;
        max-height: 100px;
    }

    #final-rewards-summary {
        border: 3px dashed #ffd700;
        background-color: rgba(255, 215, 0, 0.1);
        padding: 20px;
        border-radius: 15px;
    }

    .reward-item {
        display: flex;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.3);
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .reward-item img {
        width: 60px;
        height: 60px;
        margin-right: 15px;
    }

    #play-restriction-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: none;
        justify-content: center;
        align-items: center;
        text-align: center;
        z-index: 2000;
        border-radius: 15px;
    }

    #play-restriction-overlay .content {
        max-width: 400px;
    }

    #play-restriction-overlay .fas {
        font-size: 4rem;
        color: #dc3545;
        margin-bottom: 20px;
    }

    #questionManagementModal .modal-body {
        max-height: 60vh;
        overflow-y: auto;
    }

    .question-list-item {
        background: rgba(10, 25, 80, 0.7);
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #00ffff44;
    }

    .question-list-item .question-text {
        flex-grow: 1;
        margin-right: 15px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    @media (max-width:768px) {
        .answer-options {
            grid-template-columns: 1fr
        }

        .header h1 {
            font-size: 1.8rem
        }

        #question-container {
            font-size: 1.2rem
        }
    }
    .modal-backdrop.show{
       display: none
    }
</style>
@endsection
@section('js_crm_course')
<script>
    window.MathJax = {
        tex: { inlineMath: [['$', '$'], ['\\(', '\\)']], displayMath: [['$$', '$$'], ['\\[', '\\]']] },
        svg: { fontCache: 'global' }
    };
</script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script id="game-data" type="application/json"><!-- GAME_DATA_PLACEHOLDER --></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- GLOBAL VARIABLES & CONSTANTS ---
        const TOTAL_TIME = 30 * 60;
        const REWARD_THRESHOLD = 0.7; // 70%
        const SAVE_EXPORT_URL = "/crm-course/game/save-export";
        // const SAVE_EXPORT_URL = "{{ route('game.save-export') }}";
        const SAVE_RESULT_URL = "/crm-course/game/save-result";
        const CHECK_RESULT_URL = "/crm-course/game/check-result";
        const CSRF_TOKEN = "{{ csrf_token() }}";
        let questions = [];
        let gameId = null;
        let editingQuestionIndex = null;
        let currentQuestionIndex, answeredQuestions, gameQuestions, timeLeft, gameTimerInterval, helpUsed, rewardShown, earnedRewards;

        const prizes = [
            { name: 'Chúc bạn may mắn lần sau!', image: 'https://storage.googleapis.com/azt_agents/html_content_agent/76552763/1f0fb0e0-9290-466c-afb8-01c3ea72253d/try_again.png/1?transparent=1' },
            { name: 'Một chai nước ngọt mát lạnh!', image: 'https://storage.googleapis.com/azt_agents/html_content_agent/76552763/1f0fb0e0-9290-466c-afb8-01c3ea72253d/soda.png/1?transparent=1' },
            { name: 'Một gói bim bim giòn tan!', image: 'https://storage.googleapis.com/azt_agents/html_content_agent/76552763/1f0fb0e0-9290-466c-afb8-01c3ea72253d/snacks.png/1?transparent=1' },
            { name: 'Một que kem ốc quế ngọt ngào!', image: 'https://storage.googleapis.com/azt_agents/html_content_agent/76552763/1f0fb0e0-9290-466c-afb8-01c3ea72253d/ice_cream.png/1?transparent=1' },
            { name: 'Một cái kẹo mút đủ màu sắc!', image: 'https://storage.googleapis.com/azt_agents/html_content_agent/76552763/1f0fb0e0-9290-466c-afb8-01c3ea72253d/lollipop.png/1?transparent=1' },
            { name: 'Một tách cafe để tỉnh táo học bài!', image: 'https://storage.googleapis.com/azt_agents/html_content_agent/76552763/1f0fb0e0-9290-466c-afb8-01c3ea72253d/coffee.png/1?transparent=1' },
            { name: 'Thêm 1 tiếng học chăm chỉ!', image: 'https://storage.googleapis.com/azt_agents/html_content_agent/76552763/1f0fb0e0-9290-466c-afb8-01c3ea72253d/study_time.png/1?transparent=1' }
        ];

        const specialPrize = {
            name: 'Một suất bánh ngọt',
            image: 'https://storage.googleapis.com/azt_agents/html_content_agent/76552763/1f0fb0e0-9290-466c-afb8-01c3ea72253d/cake.png/1?transparent=1'
        };


        // --- ELEMENT SELECTORS ---
        const mainMenu = document.getElementById('main-menu');
        const gameView = document.getElementById('game-view');
        const resultsView = document.getElementById('results-view');
        const teacherControls = document.getElementById('teacher-controls');
        const startBtn = document.getElementById('start-btn');
        const createQuestionModalEl = document.getElementById('createQuestionModal');
        const createQuestionModal = new bootstrap.Modal(createQuestionModalEl);
        const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
        const solutionModal = new bootstrap.Modal(document.getElementById('solution-modal'));
        const rewardModal = new bootstrap.Modal(document.getElementById('rewardModal'));

        // --- EVENT LISTENERS ---
        startBtn.addEventListener('click', startGame);
        document.getElementById('back-to-menu').addEventListener('click', showMenu);
        document.getElementById('play-again-btn').addEventListener('click', startGame);
        document.getElementById('menu-from-results-btn').addEventListener('click', showMenu);
        document.getElementById('review-answers-btn').addEventListener('click', showReviewView);
        document.getElementById('continue-btn').addEventListener('click', () => {
            solutionModal.hide();
            moveToNextQuestion();
        });
        document.getElementById('continue-game-btn').addEventListener('click', () => {
            rewardModal.hide();
            moveToNextQuestion();
        });
        document.getElementById('saveQuestionBtn').addEventListener('click', saveOrUpdateQuestion);
        document.getElementById('processImportBtn').addEventListener('click', processImport);
        document.getElementById('questionImageInput').addEventListener('change', (e) => previewImage(e, 'preview-image-q'));
        document.getElementById('solutionImageInput').addEventListener('change', (e) => previewImage(e, 'preview-image-s'));
        document.getElementById('save-for-student-btn').addEventListener('click', prepareGameForStudent);
        document.getElementById('copy-html-btn').addEventListener('click', copyHtmlToClipboard);
        document.getElementById('help-5050').addEventListener('click', useHelp5050);
        document.getElementById('help-teacher').addEventListener('click', useHelpTeacher);
        createQuestionModalEl.addEventListener('show.bs.modal', function () {
            if (editingQuestionIndex === null) {
                document.getElementById('createQuestionModalLabel').textContent = 'Tạo Câu Hỏi Mới';
                document.getElementById('saveQuestionBtn').textContent = 'Lưu Câu Hỏi';
                clearCreateForm();
            }
        });
        document.getElementById('create-question-btn').addEventListener('click', () => {
            editingQuestionIndex = null;
        });


        // --- INITIALIZATION ---
        window.onload = async function () {
            const dataEl = document.getElementById('game-data');
            const dataContent = dataEl.textContent.trim();

            // Student Mode Check
            if (dataContent && !dataContent.includes('PLACEHOLDER')) {
                try {
                    const parsedData = JSON.parse(dataContent);
                    questions = parsedData.questions;
                    gameId = parsedData.gameId;

                    let canPlay = true;
                    if (gameId) {
                        canPlay = await canStudentPlay(gameId);
                    }
                    if (!canPlay) {
                        document.getElementById('play-restriction-overlay').style.display = 'flex';
                        startBtn.style.display = 'none';
                        return;
                    }

                    if (teacherControls) teacherControls.style.display = 'none';
                    if (document.getElementById('play-again-btn')) document.getElementById('play-again-btn').style.display = 'none';
                    if (document.getElementById('menu-from-results-btn')) document.getElementById('menu-from-results-btn').style.display = 'none';
                    if (document.getElementById('back-to-menu')) document.getElementById('back-to-menu').style.display = 'none';

                } catch (e) {
                    console.error("Lỗi khi đọc dữ liệu game:", e);
                    startBtn.style.display = 'none';
                    return;
                }
            } else { // Teacher Mode
                teacherControls.style.display = 'block';
                startBtn.textContent = 'Chơi thử với bộ đề đã tạo';
            }

            startBtn.style.display = 'block';
            renderQuestionList();
            showMenu();
        };

        // --- VIEW MANAGEMENT ---
        function showView(viewToShow) { [mainMenu, gameView, resultsView].forEach(v => v.style.display = 'none'); viewToShow.style.display = 'block'; }
        function showMenu() { clearInterval(gameTimerInterval); showView(mainMenu); }

        // --- GAME LOGIC ---
        function startGame() {
            if (questions.length === 0) { alert(`Bạn cần tạo ít nhất 1 câu hỏi để bắt đầu.`); return; }
            gameQuestions = [...questions].sort(() => 0.5 - Math.random());
            currentQuestionIndex = 0;
            answeredQuestions = [];
            helpUsed = { '5050': false, 'teacher': false };
            rewardShown = false;
            earnedRewards = [];
            startTimer(); updateHelpButtonsState(); buildPrizeLadder(); displayQuestion(); showView(gameView);
        }

        function displayQuestion() {
            if (currentQuestionIndex >= gameQuestions.length) { showResults({ status: 'completed' }); return; }
            updateHelpButtonsState(); updatePrizeLadder(); updateScoreDisplay();
            const currentQuestion = gameQuestions[currentQuestionIndex];
            document.getElementById('question-text').innerHTML = `<strong>Câu ${currentQuestionIndex + 1}:</strong> ${currentQuestion.question}`;
            const answerOptions = document.getElementById('answer-options');
            answerOptions.innerHTML = '';
            currentQuestion.options.forEach((option, index) => {
                const button = document.createElement('button');
                button.className = 'answer-btn p-3';
                button.innerHTML = `<span class="option-letter">${String.fromCharCode(65 + index)}: </span> ${option}`;
                button.onclick = () => selectAnswer(index);
                answerOptions.appendChild(button);
            });
            if (window.MathJax) MathJax.typeset();
        }

        function selectAnswer(selectedIndex) {
            const buttons = Array.from(document.getElementById('answer-options').children);
            buttons.forEach(btn => btn.disabled = true);
            const currentQuestion = gameQuestions[currentQuestionIndex];
            const isCorrect = selectedIndex === currentQuestion.correct;

            answeredQuestions.push({
                question: currentQuestion.question,
                options: currentQuestion.options,
                correct: currentQuestion.correct,
                solution: currentQuestion.solution,
                selectedIndex: selectedIndex,
                isCorrect: isCorrect
            });

            buttons[currentQuestion.correct].classList.add('correct');

            const correctCount = answeredQuestions.filter(q => q.isCorrect).length;
            const percentage = gameQuestions.length > 0 ? (correctCount / gameQuestions.length) : 0;

            let shouldShowReward = !rewardShown && percentage >= REWARD_THRESHOLD;

            if (isCorrect) {
                document.getElementById('correct-sound').play();
                if (shouldShowReward) {
                    setTimeout(showRewardModal, 1000);
                } else {
                    setTimeout(moveToNextQuestion, 1500);
                }
            } else {
                document.getElementById('wrong-sound').play();
                buttons[selectedIndex].classList.add('wrong');
                document.getElementById('solution-modal-title').textContent = 'Tiếc quá, bạn trả lời sai rồi!';
                document.getElementById('solution-text').innerHTML = currentQuestion.solution;
                if (window.MathJax) MathJax.typesetPromise().then(() => setTimeout(() => solutionModal.show(), 1000));
                else setTimeout(() => solutionModal.show(), 1000);
            }
        }

        function moveToNextQuestion() { currentQuestionIndex++; displayQuestion(); }

        // --- REWARD LOGIC ---
        function showRewardModal() {
            rewardShown = true;
            document.getElementById('complete-sound').play();
            const randomPrize = prizes[Math.floor(Math.random() * prizes.length)];
            earnedRewards.push(randomPrize);
            document.getElementById('reward-image').src = randomPrize.image;
            document.getElementById('reward-name').textContent = randomPrize.name;
            rewardModal.show();
        }

        // --- RESULTS & REVIEW LOGIC ---
        function showResults({ status }) {
            clearInterval(gameTimerInterval);

            const correctCount = answeredQuestions.filter(q => q.isCorrect).length;
            const totalQuestions = gameQuestions.length;
            const percentage = totalQuestions > 0 ? (correctCount / totalQuestions) * 100 : 0;
            let title, message;

            if (status === 'completed') {
                title = "Hoàn Thành!";
                message = "Bạn đã hoàn thành lượt chơi. Hãy xem kết quả nhé.";
            } else {
                title = "Hết Giờ!";
                message = "Rất tiếc, bạn đã không hoàn thành kịp thời gian.";
            }

            document.getElementById('results-content').innerHTML = `<h2 class="mb-3">${title}</h2><p class="fs-4">${message}</p><h3>Kết quả: <span class="text-warning">${correctCount} / ${totalQuestions}</span> câu đúng</h3>`;

            if (percentage === 100 && totalQuestions > 0) {
                if (!earnedRewards.some(r => r.name === specialPrize.name)) {
                    earnedRewards.push(specialPrize);
                }
                document.getElementById('complete-sound').play();
            }

            const summaryContainer = document.getElementById('final-rewards-summary');
            if (earnedRewards.length > 0) {
                let summaryHtml = '<h3 class="text-warning mb-3"><i class="fas fa-trophy"></i> Tổng Hợp Phần Thưởng <i class="fas fa-trophy"></i></h3>';
                earnedRewards.forEach(reward => {
                    summaryHtml += `
                <div class="reward-item">
                    <img src="${reward.image}" alt="${reward.name}">
                    <h5 class="text-white mb-0">${reward.name}</h5>
                </div>
            `;
                });
                summaryContainer.innerHTML = summaryHtml;
            } else {
                summaryContainer.innerHTML = '<p class="text-muted fs-5 mt-4">Rất tiếc, bạn chưa nhận được phần thưởng nào. Cố gắng hơn ở lần sau nhé!</p>';
            }

            document.getElementById('results-summary-container').style.display = 'block';
            document.getElementById('review-container').style.display = 'none';

            const mark = totalQuestions > 0 ? (correctCount / totalQuestions) * 10 : 0;

            let rewardsHtml = '';
            if (earnedRewards.length > 0) {
                rewardsHtml += "<h5>Phần thưởng đạt được:</h5><ul>";
                earnedRewards.forEach(r => { rewardsHtml += `<li>${r.name}</li>`; });
                rewardsHtml += "</ul>";
            } else {
                rewardsHtml = "<p>Chưa đạt phần thưởng nào.</p>";
            }

            const html_view = `<div style='padding:15px; border-radius:10px; border: 1px solid #ccc; background-color:#f9f9f9; color: black;'><h4>Kết quả "Ai là triệu phú"</h4><p><b>Trạng thái:</b> ${title}</p><p><b>Kết quả:</b> ${correctCount}/${totalQuestions} câu đúng</p>${rewardsHtml}</div>`;

            if (gameId) { // Only send results in student mode
                window.parent.postMessage({
                    type: "azt-student-result",
                    mark: parseFloat(mark.toFixed(2)),
                    url: window.location.href,
                    result: {
                        correctCount,
                        total: totalQuestions,
                        status,
                        percentage: percentage.toFixed(2),
                        earnedRewards: earnedRewards,
                        answeredQuestions
                    },
                    html_view
                }, '*');

                sendResultToServer({
                    game_id: gameId,
                    correct_count: correctCount,
                    total_questions: totalQuestions,
                    percentage: parseFloat(percentage.toFixed(2)),
                    status,
                    earned_rewards: earnedRewards
                });
            }

            showView(resultsView);
        }


        function showReviewView() {
            document.getElementById('results-summary-container').style.display = 'none';
            document.getElementById('review-container').style.display = 'block';

            let reviewHtml = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-warning">Xem Lại Bài Làm</h2>
            <button id="back-to-results-btn" class="btn btn-secondary">Quay lại Kết quả</button>
        </div>`;

            gameQuestions.forEach((question, index) => {
                const answered = answeredQuestions[index];
                let resultHtml;

                if (answered) {
                    const optionsHtml = question.options.map((option, optIndex) => {
                        let itemClass = 'list-group-item';
                        const userChoiceIndicator = (optIndex === answered.selectedIndex)
                            ? `<i class="fas fa-arrow-left ms-2 text-info"></i> <span class="text-info">(Lựa chọn của bạn)</span>`
                            : '';
                        if (optIndex === question.correct) {
                            itemClass += ' list-group-item-success';
                        } else if (optIndex === answered.selectedIndex) {
                            itemClass += ' list-group-item-danger';
                        }
                        return `<li class="${itemClass}">${String.fromCharCode(65 + optIndex)}: ${option} ${userChoiceIndicator}</li>`;
                    }).join('');
                    resultHtml = `<ul class="list-group list-group-flush">${optionsHtml}</ul>`;
                } else {
                    const optionsHtml = question.options.map((option, optIndex) => {
                        const itemClass = (optIndex === question.correct) ? 'list-group-item list-group-item-success' : 'list-group-item';
                        const correctIndicator = (optIndex === question.correct) ? ' <i class="fas fa-check text-success"></i>' : '';
                        return `<li class="${itemClass}">${String.fromCharCode(65 + optIndex)}: ${option}${correctIndicator}</li>`;
                    }).join('');
                    resultHtml = `<div class="alert alert-secondary">Bạn đã không trả lời câu này.</div><ul class="list-group list-group-flush">${optionsHtml}</ul>`;
                }
                reviewHtml += `
            <div class="card bg-dark text-white mb-3" style="border: 1px solid #00ffff;">
                <div class="card-header"><strong>Câu ${index + 1}:</strong> ${question.question}</div>
                <div class="card-body">
                    ${resultHtml}
                    <div class="mt-3 p-3 rounded" style="background-color: rgba(10,25,80,.9);">
                        <h6><i class="fas fa-lightbulb text-warning me-2"></i>Hướng dẫn giải:</h6>
                        <div>${question.solution}</div>
                    </div>
                </div>
            </div>`;
            });

            const reviewContainer = document.getElementById('review-container');
            reviewContainer.innerHTML = reviewHtml;
            document.getElementById('back-to-results-btn').addEventListener('click', () => {
                document.getElementById('review-container').style.display = 'none';
                document.getElementById('results-summary-container').style.display = 'block';
            });

            if (window.MathJax) MathJax.typeset();
        }

        // --- LIFELINES ---
        function updateHelpButtonsState() { document.getElementById('help-5050').disabled = helpUsed['5050']; document.getElementById('help-item-5050').classList.toggle('disabled', helpUsed['5050']); document.getElementById('help-teacher').disabled = helpUsed['teacher']; document.getElementById('help-item-teacher').classList.toggle('disabled', helpUsed['teacher']); }
        function useHelp5050() { if (helpUsed['5050']) return; document.getElementById('lifeline-sound').play(); helpUsed['5050'] = true; updateHelpButtonsState(); const cq = gameQuestions[currentQuestionIndex]; let removed = 0; const btns = Array.from(document.getElementById('answer-options').children); while (removed < 2) { const randIdx = Math.floor(Math.random() * 4); if (randIdx !== cq.correct && btns[randIdx].style.visibility !== 'hidden') { btns[randIdx].style.visibility = 'hidden'; removed++ } } }
        function useHelpTeacher() { if (helpUsed.teacher) return; document.getElementById('lifeline-sound').play(); helpUsed.teacher = true; updateHelpButtonsState(); document.querySelectorAll('.answer-btn').forEach(btn => btn.disabled = true); document.getElementById('help-5050').disabled = true; const currentQuestion = gameQuestions[currentQuestionIndex]; selectAnswer(currentQuestion.correct); }

        // --- TIMER & OTHER FUNCTIONS ---
        function startTimer() { clearInterval(gameTimerInterval); const d = document.getElementById('game-duration-input'); timeLeft = d ? (parseInt(d.value, 10) || 30) * 60 : TOTAL_TIME; updateTimerDisplay(); gameTimerInterval = setInterval(() => { timeLeft--; updateTimerDisplay(); if (timeLeft <= 0) { showResults({ status: 'timeup' }) } }, 1000) }
        function updateTimerDisplay() { const minutes = Math.floor(timeLeft / 60); let seconds = timeLeft % 60; seconds = seconds < 10 ? '0' + seconds : seconds; document.getElementById('timer-display').innerHTML = `<i class="fas fa-clock"></i> ${minutes}:${seconds}` }
        function updateQuestionCounter() { if (document.getElementById('question-counter')) document.getElementById('question-counter').textContent = `Đã tạo: ${questions.length} câu hỏi`; }
        function buildPrizeLadder() { const prizeLadder = document.getElementById('prize-ladder'); prizeLadder.innerHTML = ''; for (let i = 1; i <= gameQuestions.length; i++) { const li = document.createElement('li'); li.textContent = `Câu ${i}`; li.dataset.level = i - 1; prizeLadder.appendChild(li) } }
        function updatePrizeLadder() { document.getElementById('prize-ladder').querySelectorAll('li').forEach(item => { const level = parseInt(item.dataset.level); item.className = ''; if (level < currentQuestionIndex) { const answered = answeredQuestions[level]; if (answered) item.classList.add(answered.isCorrect ? 'passed' : 'failed') } if (level === currentQuestionIndex) item.classList.add('current') }) }
        function updateScoreDisplay() { const correctCount = answeredQuestions.filter(q => q.isCorrect).length; document.getElementById('score-display').textContent = `Đúng: ${correctCount}/${gameQuestions.length}` }

        // --- QUESTION MANAGEMENT ---
        function saveOrUpdateQuestion() {
            let questionStr = document.getElementById('questionTextInput').value;
            let solutionStr = document.getElementById('solutionTextInput').value;
            const qImg = document.getElementById('preview-image-q');
            if (qImg.style.display !== 'none' && qImg.src.startsWith('data:')) {
                questionStr += `<br><img src="${qImg.src}" style="max-width:250px;height:auto;">`;
            }
            const sImg = document.getElementById('preview-image-s');
            if (sImg.style.display !== 'none' && sImg.src.startsWith('data:')) {
                solutionStr += `<br><img src="${sImg.src}" style="max-width:100%;height:auto;">`;
            }
            const newQuestion = {
                question: questionStr,
                options: [
                    document.getElementById('answerA').value,
                    document.getElementById('answerB').value,
                    document.getElementById('answerC').value,
                    document.getElementById('answerD').value,
                ],
                correct: parseInt(document.querySelector('input[name="correctAnswer"]:checked').value),
                solution: solutionStr
            };
            if (!newQuestion.question || newQuestion.options.some(o => !o)) {
                alert("Vui lòng điền đầy đủ câu hỏi và các đáp án.");
                return;
            }
            if (editingQuestionIndex !== null) {
                questions[editingQuestionIndex] = newQuestion;
            } else {
                questions.push(newQuestion);
            }

            editingQuestionIndex = null;
            updateQuestionCounter();
            renderQuestionList();
            createQuestionModal.hide();
            const feedback = document.getElementById('save-feedback');
            feedback.textContent = `Đã lưu!`;
            feedback.style.color = 'lightgreen';
            setTimeout(() => feedback.textContent = '', 3000)
        }
        function editQuestion(index) {
            editingQuestionIndex = index;
            const question = questions[index];

            const tempDiv = document.createElement('div');

            // Handle question text and image
            tempDiv.innerHTML = question.question;
            const qImgEl = tempDiv.querySelector('img');
            const qPreview = document.getElementById('preview-image-q');
            if (qImgEl) {
                document.getElementById('questionTextInput').value = tempDiv.textContent;
                qPreview.src = qImgEl.src;
                qPreview.style.display = 'block';
            } else {
                document.getElementById('questionTextInput').value = question.question;
                qPreview.style.display = 'none';
            }

            // Handle solution text and image
            tempDiv.innerHTML = question.solution;
            const sImgEl = tempDiv.querySelector('img');
            const sPreview = document.getElementById('preview-image-s');
            if (sImgEl) {
                document.getElementById('solutionTextInput').value = tempDiv.textContent;
                sPreview.src = sImgEl.src;
                sPreview.style.display = 'block';
            } else {
                document.getElementById('solutionTextInput').value = question.solution;
                sPreview.style.display = 'none';
            }

            document.getElementById('answerA').value = question.options[0];
            document.getElementById('answerB').value = question.options[1];
            document.getElementById('answerC').value = question.options[2];
            document.getElementById('answerD').value = question.options[3];
            document.querySelector(`input[name="correctAnswer"][value="${question.correct}"]`).checked = true;

            document.getElementById('createQuestionModalLabel').textContent = `Chỉnh sửa Câu hỏi ${index + 1}`;
            document.getElementById('saveQuestionBtn').textContent = 'Cập nhật';
            createQuestionModal.show();
        }

        function deleteQuestion(index) {
            if (confirm(`Bạn có chắc chắn muốn xóa câu hỏi ${index + 1}?`)) {
                questions.splice(index, 1);
                renderQuestionList();
                updateQuestionCounter();
            }
        }
        function renderQuestionList() {
            const listEl = document.getElementById('question-list');
            if (!listEl) return;
            listEl.innerHTML = '';
            if (questions.length === 0) {
                listEl.innerHTML = '<li class="text-muted p-3 text-center text-white">Chưa có câu hỏi nào được tạo.</li>';
                return;
            }
            questions.forEach((q, index) => {
                const li = document.createElement('li');
                li.className = 'question-list-item';

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = q.question;

                li.innerHTML = `
            <span class="question-text"><strong>Câu ${index + 1}:</strong> ${tempDiv.textContent}</span>
            <div>
                <button class="btn btn-sm btn-outline-warning me-2" onclick="editQuestion(${index})"><i class="fas fa-edit"></i> Sửa</button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteQuestion(${index})"><i class="fas fa-trash"></i> Xóa</button>
            </div>
        `;
                listEl.appendChild(li);
            });
        }
        function processImport() {
            const text = document.getElementById('importTextArea').value; try {
                const q = text.match(/\[CÂU HỎI\]([\s\S]*?)\[A\]/)[1].trim(); const a = text.match(/\[A\]([\s\S]*?)\[B\]/)[1].trim(); const b = text.match(/\[B\]([\s\S]*?)\[C\]/)[1].trim(); const c = text.match(/\[C\]([\s\S]*?)\[D\]/)[1].trim(); const d = text.match(/\[D\]([\s\S]*?)\[ĐÚNG\]/)[1].trim(); const correct = text.match(/\[ĐÚNG\]\s*(\w)/)[1].trim().toUpperCase(); const sol = text.match(/\[GIẢI\]([\s\S]*)/)[1].trim(); document.getElementById('questionTextInput').value = q; document.getElementById('answerA').value = a; document.getElementById('answerB').value = b; document.getElementById('answerC').value = c; document.getElementById('answerD').value = d; document.getElementById('solutionTextInput').value = sol; const correctIndex = ['A', 'B', 'C', 'D'].indexOf(correct); if (correctIndex !== -1) { document.querySelector(`input[name="correctAnswer"][value="${correctIndex}"]`).checked = true }
                bootstrap.Tab.getInstance(document.getElementById('manual-tab')).show(); alert('Đã nhập dữ liệu! Nhấn "Lưu Câu Hỏi" để hoàn tất.')
            } catch (e) { alert("Lỗi định dạng! Vui lòng kiểm tra lại văn bản.") }
        }
        function clearCreateForm() { document.getElementById('create-question-form').reset(); document.getElementById('preview-image-q').style.display = 'none'; document.getElementById('preview-image-s').style.display = 'none'; document.getElementById('importTextArea').value = '' }
        function previewImage(event, previewElemId) { const file = event.target.files[0]; if (!file) return; const reader = new FileReader(); const preview = document.getElementById(previewElemId); reader.onload = function () { preview.src = reader.result; preview.style.display = 'block' }; reader.readAsDataURL(file) }
        async function prepareGameForStudent() {
            if (questions.length === 0) { alert("Bạn phải tạo ít nhất một câu hỏi trước khi chuẩn bị mã game."); return }
            const saveButton = document.getElementById('save-for-student-btn');
            const saveFeedbackEl = document.getElementById('save-for-student-feedback');

            saveButton.disabled = true;
            saveButton.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo mã...`;
            if (saveFeedbackEl) {
                saveFeedbackEl.textContent = '';
                saveFeedbackEl.classList.remove('text-success', 'text-danger');
            }
            try {
                let pageHTML = `<!DOCTYPE html>\n${document.documentElement.outerHTML}`;

                const gameData = {
                    gameId: Date.now().toString(36) + Math.random().toString(36).substr(2),
                    gradeId: document.getElementById('grade-select').value,
                    subjectId: document.getElementById('subject-select').value,
                    gameName: document.getElementById('game-name-input').value,
                    gameDescription: document.getElementById('game-description-input').value,
                    questions: questions
                };

                const gameDataJSON = JSON.stringify(gameData);

                const finalHtml = pageHTML.replace('<!-- GAME_DATA_PLACEHOLDER -->', gameDataJSON);

                document.getElementById('export-html-content').value = finalHtml;

                await persistGameExport({
                    game_id: gameData.gameId,
                    teacher_id: {{ Auth::guard('customer')->user()->id }},
                    grade_id: gameData.gradeId,
                    subject_id: gameData.subjectId,
                    game_name: gameData.gameName,
                    game_description: gameData.gameDescription,
                    html: finalHtml,
                    questions_count: questions.length
                }); 

                if (saveFeedbackEl) {
                    saveFeedbackEl.textContent = 'Đã lưu mã game vào hệ thống.';
                    saveFeedbackEl.classList.add('text-success');
                }

                exportModal.show();

            } catch (error) {
                alert("Đã xảy ra lỗi trong quá trình tạo mã game.");
                console.error("Lỗi khi chuẩn bị game:", error);
                if (saveFeedbackEl) {
                    saveFeedbackEl.textContent = 'Không thể lưu mã game. Vui lòng thử lại.';
                    saveFeedbackEl.classList.add('text-danger');
                }
            } finally {
                saveButton.disabled = false;
                saveButton.innerHTML = `<i class="fas fa-code me-2"></i>Lấy Mã Game cho Học sinh`;
            }
        }
        async function persistGameExport(payload) {
            const response = await fetch(SAVE_EXPORT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Không thể lưu dữ liệu.');
            }

            return response.json();
        }
        async function sendResultToServer(payload) {
            if (!SAVE_RESULT_URL) return;
            try {
                await fetch(SAVE_RESULT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
            } catch (error) {
                console.warn('Không thể lưu kết quả học sinh:', error);
            }
        }
        async function canStudentPlay(gameId) {
            if (!CHECK_RESULT_URL) return true;
            try {
                const response = await fetch(CHECK_RESULT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ game_id: gameId })
                });
                if (!response.ok) return true;
                const data = await response.json();
                return data.can_play !== false;
            } catch (error) {
                console.warn('Không thể kiểm tra lượt chơi:', error);
                return true;
            }
        }
        function copyHtmlToClipboard() { 
            window.location.href = "/crm-course/danh-sach-game.html";
         }
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
                        <h3>Danh sách bài tập</h3>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Trang chủ</li>
                        <li>Danh sách bài tập</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="contact-list-area">
        <div class="container-fluid">
            <div class="background-image">
                <div id="game-container">
                    <div id="play-restriction-overlay">
                        <div class="content">
                            <i class="fas fa-hand-paper"></i>
                            <h2 class="text-warning">Lượt chơi đã kết thúc</h2>
                            <p class="fs-4">Bạn đã hoàn thành trò chơi này. Mỗi học sinh chỉ được chơi một lần.</p>
                        </div>
                    </div>
                    <!-- Main Menu View -->
                    <div id="main-menu">
                        <h1 class="header text-center mb-4 text-white">Ai là Triệu phú Toán học</h1>
                        <div id="teacher-controls">
                            <p class="lead text-center">Tạo bộ câu hỏi, sau đó nhấn "Lấy Mã Game" để gửi cho học sinh.</p>
                            <div class="mb-3 col-8 mx-auto time-setting-container">
                                <label for="game-duration-input" class="form-label text-warning">Thời gian làm bài (phút):</label>
                                <input type="number" class="form-control" id="game-duration-input" value="30" min="1"
                                    style="background-color: rgba(10,25,80,0.9); color: #fff; border: 1px solid #00ffff;">
                            </div>
                            <div class="mb-3 col-8 mx-auto">
                                <label for="grade-select" class="form-label text-warning">Lớp:</label>
                                <select class="form-select" id="grade-select">
                                    @foreach ($grades as $grade)
                                        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-8 mx-auto">
                                <label for="subject-select" class="form-label text-warning">Môn:</label>
                                <select class="form-select" id="subject-select">
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-8 mx-auto">
                                <label for="game-name-input" class="form-label text-warning">Tên Game:</label>
                                <input type="text" class="form-control" id="game-name-input" value="Ai là Triệu phú Toán học">
                            </div>
                            <div class="mb-3 col-8 mx-auto">
                                <label for="game-description-input" class="form-label text-warning">Mô tả Game:</label>
                                <input type="text" class="form-control" id="game-description-input" value="Ai là Triệu phú Toán học">
                            </div>
                            <div class="d-grid gap-2 col-8 mx-auto">
                                <button id="create-question-btn" class="btn btn-info text-white" data-bs-toggle="modal"
                                    data-bs-target="#createQuestionModal">Tạo Câu Hỏi Mới</button>
                                <button id="manage-questions-btn" class="btn btn-secondary text-white" data-bs-toggle="modal"
                                    data-bs-target="#questionManagementModal"><i class="fas fa-list-alt me-2"></i>Quản lý Câu
                                    hỏi</button>
                                <button id="save-for-student-btn" class="btn btn-success text-white"><i class="fas fa-code me-2"></i>Tạo Game</button>
                            </div>
                            <p id="question-counter" class="text-muted text-center mt-2">Đã tạo: 0 câu hỏi</p>
                            <p id="save-for-student-feedback" class="text-center mt-1"></p>
                        </div>
                        <button id="start-btn" class="btn btn-warning text-white btn-lg mb-3 px-5 py-3 mt-3">Bắt đầu trò chơi</button>
                    </div>
            
                    <!-- Game Play View -->
                    <div id="game-view" style="display: none;">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="header d-flex justify-content-between align-items-center mb-2">
                                        <h1>Ai là Triệu phú</h1>
                                        <div class="game-stats">
                                            <div id="score-display">Đúng: 0/0</div>
                                            <div id="timer-display"><i class="fas fa-clock"></i> 30:00</div>
                                        </div>
                                    </div>
                                    <div class="help-container">
                                        <div class="help-item" id="help-item-5050">
                                            <button id="help-5050" class="btn help-btn" title="Loại bỏ 2 phương án sai"><i
                                                    class="fas fa-divide"></i></button>
                                            <span>50/50</span>
                                        </div>
                                        <div class="help-item" id="help-item-teacher">
                                            <button id="help-teacher" class="btn help-btn" title="Giáo viên chọn đáp án đúng"><i
                                                    class="fas fa-user-tie"></i></button>
                                            <span>Hỏi Giáo viên</span>
                                        </div>
                                    </div>
                                    <div id="question-container"
                                        class="d-flex justify-content-center align-items-center text-center">
                                        <p id="question-text"></p>
                                    </div>
                                    <div id="answer-options" class="answer-options"></div>
                                    <div class="text-center mt-3"><button id="back-to-menu" class="btn btn-secondary mx-2">Về Menu
                                            chính</button></div>
                                </div>
                                <div class="col-lg-3">
                                    <h4 class="text-center mt-3 mt-lg-0 text-warning">Tiến độ</h4>
                                    <ul id="prize-ladder"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <!-- Results View -->
                    <div id="results-view" style="display: none;">
                        <div id="results-summary-container">
                            <div id="results-content" class="text-center"></div>
                            <div id="final-rewards-summary" class="text-center mt-4"></div>
                            <div id="results-actions" class="text-center mt-4">
                                <button id="play-again-btn" class="btn btn-warning btn-lg">Chơi lại</button>
                                <button id="menu-from-results-btn" class="btn btn-secondary btn-lg">Về Menu chính</button>
                                <button id="review-answers-btn" class="btn btn-info btn-lg">Xem Lại Bài Làm</button>
                            </div>
                        </div>
                        <div id="review-container" class="mt-4" style="display: none; text-align: left;"></div>
                    </div>
                </div>
            </div>
            
        
            <!-- Create/Edit Question Modal -->
            <div class="modal fade" id="createQuestionModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="createQuestionModalLabel">Tạo Câu Hỏi Mới</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation"><button class="nav-link active" id="manual-tab"
                                        data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">Nhập thủ
                                        công</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" id="import-tab"
                                        data-bs-toggle="tab" data-bs-target="#import" type="button" role="tab">Dán từ
                                        Word</button></li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active p-3" id="manual" role="tabpanel">
                                    <form id="create-question-form">
                                        <div class="mb-3"><label for="questionTextInput" class="form-label">Câu hỏi (dùng $...$
                                                cho công thức toán)</label><textarea class="form-control" id="questionTextInput"
                                                rows="3"></textarea></div>
                                        <div class="mb-3"><label for="questionImageInput" class="form-label">Ảnh cho câu hỏi
                                                (tùy chọn)</label><input class="form-control" type="file"
                                                id="questionImageInput" accept="image/*"><img id="preview-image-q" src="#"
                                                alt="Xem trước ảnh câu hỏi" /></div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3"><label for="answerA" class="form-label">Đáp án
                                                    A</label><input type="text" class="form-control" id="answerA"></div>
                                            <div class="col-md-6 mb-3"><label for="answerB" class="form-label">Đáp án
                                                    B</label><input type="text" class="form-control" id="answerB"></div>
                                            <div class="col-md-6 mb-3"><label for="answerC" class="form-label">Đáp án
                                                    C</label><input type="text" class="form-control" id="answerC"></div>
                                            <div class="col-md-6 mb-3"><label for="answerD" class="form-label">Đáp án
                                                    D</label><input type="text" class="form-control" id="answerD"></div>
                                        </div>
                                        <div class="mb-3"><label class="form-label">Đáp án đúng</label>
                                            <div>
                                                <div class="form-check form-check-inline"><input class="form-check-input"
                                                        type="radio" name="correctAnswer" id="correctA" value="0" checked><label
                                                        class="form-check-label" for="correctA">A</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input"
                                                        type="radio" name="correctAnswer" id="correctB" value="1"><label
                                                        class="form-check-label" for="correctB">B</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input"
                                                        type="radio" name="correctAnswer" id="correctC" value="2"><label
                                                        class="form-check-label" for="correctC">C</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input"
                                                        type="radio" name="correctAnswer" id="correctD" value="3"><label
                                                        class="form-check-label" for="correctD">D</label></div>
                                            </div>
                                        </div>
                                        <div class="mb-3"><label for="solutionTextInput" class="form-label">Hướng dẫn
                                                giải</label><textarea class="form-control" id="solutionTextInput"
                                                rows="3"></textarea></div>
                                        <div class="mb-3"><label for="solutionImageInput" class="form-label">Ảnh cho hướng dẫn
                                                (tùy chọn)</label><input class="form-control" type="file"
                                                id="solutionImageInput" accept="image/*"><img id="preview-image-s" src="#"
                                                alt="Xem trước ảnh giải" /></div>
                                    </form>
                                </div>
                                <div class="tab-pane fade p-3" id="import" role="tabpanel">
                                    <p>Dán nội dung từ Word vào đây theo định dạng mẫu.</p>
                                    <div class="alert alert-info"><strong>Định dạng mẫu:</strong><br>[CÂU HỎI] Nội
                                        dung...<br>[A] Nội dung...<br>[B] Nội dung...<br>[C] Nội dung...<br>[D] Nội
                                        dung...<br>[ĐÚNG] B<br>[GIẢI] Nội dung...</div><textarea class="form-control"
                                        id="importTextArea" rows="10"></textarea><button class="btn btn-primary mt-3"
                                        id="processImportBtn">Xử lý và Nhập</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="save-feedback" class="me-auto"></div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="button" class="btn btn-primary" id="saveQuestionBtn">Lưu Câu Hỏi</button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Question Management Modal -->
            <div class="modal fade" id="questionManagementModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white">Quản lý Câu hỏi</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul id="question-list" class="list-unstyled"></ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Export Modal -->
            <div class="modal fade" id="exportModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white">Tạo game thành công</h5><button type="button"
                                class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <textarea id="export-html-content" class="form-control mt-2" readonly></textarea>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-primary" id="copy-html-btn"><i
                                    class="fas fa-copy me-2"></i>Hoàn thành</button></div>
                    </div>
                </div>
            </div>
        
            <!-- Modals -->
            <div class="modal fade" id="solution-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="solution-modal-title">Tiếc quá, bạn trả lời sai rồi!</h5>
                        </div>
                        <div class="modal-body">
                            <h6>Hướng dẫn giải:</h6>
                            <p id="solution-text"></p>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-primary" id="continue-btn">Tiếp
                                tục</button></div>
                    </div>
                </div>
            </div>
        
            <!-- Reward Modal -->
            <div class="modal fade" id="rewardModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                        <div class="modal-header border-0">
                            <h5 class="modal-title w-100" id="rewardModalLabel" style="font-size: 2rem; color: #ffd700;">
                                <i class="fas fa-gift text-white"></i> Một Phần Quà Cho Bạn!
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p class="fs-5">Hãy tiếp tục cố gắng để nhận thêm những phần thưởng giá trị khác nhé!</p>
                            <div id="reward-image-container" class="my-4">
                                <img id="reward-image" src="" alt="Hình ảnh phần thưởng">
                            </div>
                            <h4 id="reward-name" class="text-warning"></h4>
                        </div>
                        <div class="modal-footer border-0 justify-content-center">
                            <button type="button" class="btn btn-warning btn-lg" id="continue-game-btn">Tiếp tục Chơi</button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Audio files -->
            <audio id="correct-sound" src="https://www.myinstants.com/media/sounds/correct-answer-sound-effect-19.mp3"
                preload="auto"></audio>
            <audio id="wrong-sound" src="https://www.myinstants.com/media/sounds/answer-wrong.mp3" preload="auto"></audio>
            <audio id="complete-sound" src="https://www.myinstants.com/media/sounds/kids_cheering.mp3" preload="auto"></audio>
            <audio id="lifeline-sound" src="https://www.myinstants.com/media/sounds/swoosh-14.mp3" preload="auto"></audio>
        </div>
    </div>
    <!-- End Features Area -->
    <!-- Start Footer Area -->
    @include('crm_course.main.footer')
    <!-- End Footer Area -->
 </main>

@endsection