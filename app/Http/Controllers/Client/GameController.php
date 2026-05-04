<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\models\GameExport;
use App\models\GameResult;
use App\models\GameAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\models\Quiz\CategoryMain;
use App\models\Quiz\QuizCategory;
use App\models\dethi\Dethi;
use App\models\dethi\ExamSession;
use App\models\dethi\DethiQuestion;
use App\models\dethi\DethiAnswer;
use App\models\GameReward;
use App\models\GameRewardConfig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\models\dethi\DethiClass;
use App\models\SchoolClass;

class GameController extends Controller
{
    public function listAll(){
        return view('crm_course.game.listall');
    }
    public function create(Request $request)
    {
        return redirect()->route('tutaodethi', [
            'type' => 'game',
            'folder_id' => $request->query('folder_id')
        ]);
    }

    public function uploadFile(Request $request)
    {
        return redirect()->route('uploadFile', [
            'type' => 'game',
            'folder_id' => $request->query('folder_id')
        ]);
    }

    public function startGame(Request $request, $gameId)
    {
        $game = Dethi::with(['parts.questions.answers'])->where('type', 'game')->find($gameId);
        if (!$game) {
            return redirect()->route('gamelist')->with('error', 'Game không tồn tại');
        }
        
        $student = Auth::guard('customer')->user();
        $isAdmin = $student && $student->type == 3;
        
        $attemptQuery = GameAttempt::where('game_id', $game->id)->where('status', 'active');
        if ($student) {
            $attemptQuery->where('student_id', $student->id);
        } else {
            $attemptQuery->whereNull('student_id')
                ->where('ip_address', $request->ip());
        }

        if (!$isAdmin && $attemptQuery->exists()) {
            return redirect()->route('gamelistAITrieuPhuToanHoc')
                ->with('error', 'Bạn đang có một bài thi chưa nộp hoặc đã mở ở tab khác.');
        }

        if (!$isAdmin) {
            $hasPlayed = GameResult::where('game_id', $game->id)
                ->when($student, function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                }, function ($query) use ($request) {
                    $query->whereNull('student_id')
                          ->where('ip_address', $request->ip());
                })
                ->exists();
            
            if ($hasPlayed) {
                return redirect()->route('gamelistAITrieuPhuToanHoc')
                    ->with('error', 'Bạn đã hoàn thành game này và không thể chơi lại.');
            }
        }

        $attemptToken = (string) Str::uuid();
        $attempt = GameAttempt::create([
            'game_id' => $game->id,
            'student_id' => $student ? $student->id : null,
            'token' => $attemptToken,
            'ip_address' => $request->ip(),
            'status' => 'active',
            'started_at' => now(),
        ]);

        if ($request->hasSession()) {
            $request->session()->put('game_attempt_token_' . $game->id, $attemptToken);
        }
        
        // Lấy tất cả câu hỏi từ tất cả các parts và sắp xếp
        $allQuestions = collect();
        foreach ($game->parts as $part) {
            foreach ($part->questions as $question) {
                $allQuestions->push([
                    'id' => $question->id,
                    'content' => $question->content,
                    'question_type' => $question->question_type,
                    'explanation' => $question->explanation,
                    'answers' => $question->answers->map(function($answer) {
                        return [
                            'id' => $answer->id,
                            'label' => $answer->label,
                            'content' => $answer->content,
                            'is_correct' => $answer->is_correct,
                            'order' => $answer->order ?? 0,
                        ];
                    })->sortBy('order')->values(),
                ]);
            }
        }
        
        // Sắp xếp câu hỏi theo question_no nếu có
        $allQuestions = $allQuestions->sortBy(function($q) {
            $question = DethiQuestion::find($q['id']);
            return $question->question_no ?? 999;
        })->values();
        
        return view('crm_course.game.start-game', [
            'game' => $game,
            'totalQuestions' => $allQuestions->count(),
            'timeLimit' => $game->time ?? 0, // Thời gian làm bài (phút), 0 = không giới hạn
            'attemptToken' => $attemptToken,
        ]);
    }
    
    /**
     * API: Lấy câu hỏi theo index
     */
    public function getQuestion(Request $request, $gameId)
    {
        $game = Dethi::with(['parts.questions.answers'])->where('type', 'game')->find($gameId);
        if (!$game) {
            return response()->json(['success' => false, 'message' => 'Game không tồn tại'], 404);
        }

        $attemptValidation = $this->validateAttempt($request, $game->id);
        if ($attemptValidation !== true) {
            return $attemptValidation;
        }
        
        $questionIndex = $request->input('index', 0);
        
        // Lấy tất cả câu hỏi
        $allQuestions = collect();
        foreach ($game->parts as $part) {
            foreach ($part->questions as $question) {
                $allQuestions->push($question);
            }
        }
        
        // Sắp xếp theo question_no
        $allQuestions = $allQuestions->sortBy('question_no')->values();
        
        if ($questionIndex >= $allQuestions->count()) {
            return response()->json(['success' => false, 'message' => 'Không còn câu hỏi nào'], 404);
        }
        
        $question = $allQuestions[$questionIndex];
        $answers = $question->answers->sortBy('order')->values()->map(function($answer) {
            return [
                'id' => $answer->id,
                'label' => $answer->label,
                'content' => $answer->content,
                'is_correct' => false, // Không trả về đáp án đúng cho client
            ];
        });
        
        return response()->json([
            'success' => true,
            'question' => [
                'id' => $question->id,
                'content' => $question->content,
                'question_type' => $question->question_type,
                'explanation' => $question->explanation,
                'answers' => $answers,
            ],
            'current_index' => $questionIndex,
            'total_questions' => $allQuestions->count(),
        ]);
    }
    
    /**
     * API: Submit đáp án và lấy đáp án đúng
     */
    public function submitAnswer(Request $request, $gameId)
    {
        try {
            $validated = $request->validate([
                'question_id' => 'required|integer',
                'answer_id' => 'required|integer',
                'question_index' => 'required|integer',
            ]);
            
            $game = Dethi::where('type', 'game')->find($gameId);
            if (!$game) {
                return response()->json(['success' => false, 'message' => 'Game không tồn tại'], 404);
            }

            $attemptValidation = $this->validateAttempt($request, $game->id);
            if ($attemptValidation !== true) {
                return $attemptValidation;
            }

            $question = DethiQuestion::with('answers')->find($validated['question_id']);
            if (!$question) {
                return response()->json(['success' => false, 'message' => 'Câu hỏi không tồn tại'], 404);
            }
            
            $selectedAnswer = DethiAnswer::where('id', $validated['answer_id'])
                ->where('dethi_question_id', $question->id)
                ->first();
            
            if (!$selectedAnswer) {
                return response()->json(['success' => false, 'message' => 'Đáp án không hợp lệ'], 400);
            }
            
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            $isCorrect = (bool)$selectedAnswer->is_correct;
            
            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
                'correct_answer' => $correctAnswer ? [
                    'id' => $correctAnswer->id,
                    'label' => $correctAnswer->label,
                    'content' => $correctAnswer->content,
                ] : null,
                'selected_answer' => [
                    'id' => $selectedAnswer->id,
                    'label' => $selectedAnswer->label,
                    'content' => $selectedAnswer->content,
                ],
                'explanation' => $question->explanation ?? '',
                'has_next' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Game submitAnswer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Lấy đáp án đúng cho lifeline (không lưu kết quả)
     */
    public function getCorrectAnswer(Request $request, $gameId)
    {
        try {
            $validated = $request->validate([
                'question_id' => 'required|integer',
            ]);
            
            $game = Dethi::where('type', 'game')->find($gameId);
            if (!$game) {
                return response()->json(['success' => false, 'message' => 'Game không tồn tại'], 404);
            }

            $attemptValidation = $this->validateAttempt($request, $game->id);
            if ($attemptValidation !== true) {
                return $attemptValidation;
            }

            $question = DethiQuestion::with('answers')->find($validated['question_id']);
            if (!$question) {
                return response()->json(['success' => false, 'message' => 'Câu hỏi không tồn tại'], 404);
            }
            
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            
            if (!$correctAnswer) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đáp án đúng'], 404);
            }
            
            return response()->json([
                'success' => true,
                'correct_answer' => [
                    'id' => $correctAnswer->id,
                    'label' => $correctAnswer->label,
                    'content' => $correctAnswer->content,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Game getCorrectAnswer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Lưu kết quả game vào game_results
     */
    public function saveGameResult(Request $request, $gameId)
    {
        $validated = $request->validate([
            'correct_count' => 'required|integer|min:0',
            'total_questions' => 'required|integer|min:1',
        ]);
        
        $game = Dethi::where('type', 'game')->find($gameId);
        if (!$game) {
            return response()->json(['success' => false, 'message' => 'Game không tồn tại'], 404);
        }

        $attempt = $this->validateAttempt($request, $game->id, true);
        if ($attempt instanceof \Illuminate\Http\JsonResponse) {
            return $attempt;
        }
        
        $user = Auth::guard('customer')->user();
        $percentage = ($validated['correct_count'] / $validated['total_questions']) * 100;
        $percentage = round($percentage, 2);
        
        $isAdmin = $user && $user->type == 3;
        
        if (!$isAdmin) {
            $existingResult = GameResult::where('game_id', $game->id)
                ->when($user, function($query) use ($user) {
                    $query->where('student_id', $user->id);
                }, function($query) use ($request) {
                    $query->whereNull('student_id')
                          ->where('ip_address', $request->ip());
                })
                ->exists();
            
            if ($existingResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã hoàn thành game này. Không thể nộp lại kết quả.'
                ], 409);
            }
        }
        
        // Tính toán quà tặng
        $earnedRewards = $this->calculateRewards($gameId, $percentage);
        
        $result = GameResult::create([
            'game_id' => (string)$game->id,
            'student_id' => $user ? $user->id : null,
            'student_name' => $user ? $user->name : null,
            'student_email' => $user ? $user->email : null,
            'correct_count' => $validated['correct_count'],
            'total_questions' => $validated['total_questions'],
            'percentage' => $percentage,
            'earned_rewards' => $earnedRewards,
            'ip_address' => $request->ip(),
        ]);

        if ($attempt) {
            $attempt->update([
                'status' => 'completed',
                'ended_at' => now(),
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->forget('game_attempt_token_' . $game->id);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Đã lưu kết quả game',
            'result' => [
                'id' => $result->id,
                'correct_count' => $result->correct_count,
                'total_questions' => $result->total_questions,
                'percentage' => $result->percentage,
            ],
            'rewards' => $earnedRewards,
        ]);
    }
    
    /**
     * Tính toán quà tặng dựa trên phần trăm điểm
     * Logic: Chỉ trao quà từ config có % điểm cao nhất mà học sinh đạt được (không tích lũy)
     */
    private function calculateRewards($gameId, $percentage)
    {
        // Lấy tất cả configs của game, sắp xếp theo priority (cao -> thấp), sau đó theo min_percentage (cao -> thấp)
        $configs = GameRewardConfig::with('reward')
            ->where('game_id', $gameId)
            ->orderByDesc('priority')
            ->orderByDesc('min_percentage')
            ->get();
        
        if ($configs->isEmpty()) {
            return [];
        }
        
        $earnedRewards = [];
        
        // Tìm các configs phù hợp với phần trăm điểm
        $matchingConfigs = $configs->filter(function($config) use ($percentage) {
            return $config->matchesPercentage($percentage) && 
                   $config->reward && 
                   $config->reward->status == 1;
        });
        
        if ($matchingConfigs->isEmpty()) {
            return [];
        }
        
        // Lấy config có priority cao nhất, nếu bằng nhau thì lấy min_percentage cao nhất
        // Điều này đảm bảo: 100% sẽ nhận quà từ config 100% (priority cao), không phải config 70%
        $topConfig = $matchingConfigs->first();
        $reward = $topConfig->reward;
        
        if (!$reward) {
            return [];
        }
        
        // Lấy tất cả rewards có thể random (từ cùng game và cùng điều kiện %)
        $availableRewards = GameReward::whereHas('configs', function($query) use ($gameId, $topConfig) {
            $query->where('game_id', $gameId)
                  ->where('min_percentage', $topConfig->min_percentage)
                  ->where(function($q) use ($topConfig) {
                      if ($topConfig->max_percentage !== null) {
                          $q->where('max_percentage', $topConfig->max_percentage);
                      } else {
                          $q->whereNull('max_percentage');
                      }
                  });
        })->where('status', 1)->get();
        
        if ($availableRewards->isEmpty()) {
            // Nếu không có reward nào khác, dùng reward từ config
            $availableRewards = collect([$reward]);
        }
        
        // Random số lượng quà tặng theo quantity trong config
        $quantity = $topConfig->quantity;
        $selectedRewards = $availableRewards->random(min($quantity, $availableRewards->count()));
        
        // Nếu quantity > số lượng rewards có sẵn, có thể random lại (có thể trùng)
        if ($quantity > $selectedRewards->count()) {
            $additionalCount = $quantity - $selectedRewards->count();
            $additionalRewards = $availableRewards->random($additionalCount);
            $selectedRewards = $selectedRewards->merge($additionalRewards);
        }
        
        // Format quà tặng để lưu vào database
        $rewardCounts = [];
        foreach ($selectedRewards as $selectedReward) {
            $key = $selectedReward->id;
            if (!isset($rewardCounts[$key])) {
                $rewardCounts[$key] = [
                    'id' => $selectedReward->id,
                    'name' => $selectedReward->name,
                    'description' => $selectedReward->description,
                    'image' => $selectedReward->image,
                    'quantity' => 0,
                ];
            }
            $rewardCounts[$key]['quantity']++;
        }
        
        $earnedRewards = array_values($rewardCounts);
        
        return $earnedRewards;
    }

    private function validateAttempt(Request $request, $gameId, $returnAttempt = false)
    {
        $token = $request->input('attempt_token') ?? $request->header('X-Attempt-Token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên thi không hợp lệ (thiếu mã attempt).'
            ], 403);
        }

        $attempt = GameAttempt::where('game_id', $gameId)
            ->where('token', $token)
            ->first();

        if (!$attempt || $attempt->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Phiên thi đã kết thúc hoặc không tồn tại.'
            ], 403);
        }

        if ($returnAttempt) {
            return $attempt;
        }

        return true;
    }

    private function getAssignedGameIdsForStudent($student)
    {
        if (!$student) {
            return [];
        }

        // Lấy class_codes từ JSON
        $classCodes = $student->class_codes ?? [];
        if (empty($classCodes)) {
            return [];
        }

        // Lấy tất cả các lớp của học sinh
        $studentClasses = \App\models\SchoolClass::whereIn('class_code', $classCodes)->get();
        if ($studentClasses->isEmpty()) {
            return [];
        }

        // Lấy tất cả class_id của học sinh
        $classIds = $studentClasses->pluck('id')->toArray();

        return DethiClass::whereIn('class_id', $classIds)
            ->pluck('dethi_id')
            ->toArray();
    }

    public function myResult($gameId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để xem kết quả.'
            ], 401);
        }

        $result = GameResult::where('game_id', $gameId)
            ->where('student_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa có kết quả cho game này.'
            ]);
        }

        return response()->json([
            'success' => true,
            'result' => [
                'game_title' => optional($result->game)->title ?? 'Game',
                'correct_count' => $result->correct_count,
                'total_questions' => $result->total_questions,
                'percentage' => $result->percentage,
                'created_at' => $result->created_at ? $result->created_at->format('d/m/Y H:i') : null,
                'earned_rewards' => $result->earned_rewards,
            ],
        ]);
    }
    public function restart($id)
    {
        $profile = Auth::guard('customer')->user();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.'
            ], 401);
        }

        $gameQuery = Dethi::where('type', 'game')->where('id', $id);
        if ($profile->type != 3) {
            $gameQuery->where('created_by', $profile->id);
        }

        $game = $gameQuery->first();

        if (!$game) {
            return response()->json([
                'success' => false,
                'message' => 'Game không tồn tại hoặc bạn không có quyền.'
            ], 404);
        }

        GameResult::where('game_id', $game->id)->delete();
        GameAttempt::where('game_id', $game->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ kết quả và bảng xếp hạng của game.'
        ]);
    }
    public function listAITrieuPhuToanHoc(Request $request)
    {
        $profile = Auth::guard('customer')->user();
        $grades = CategoryMain::where('status', 1)->orderBy('id')->get();
        $subjects = QuizCategory::where('status', 1)->orderBy('cate_id')->orderBy('id')->get();

        $filters = $request->only(['name', 'grade_id', 'subject_id']);

        if ($profile->type == 0) {
        $gamesQuery = Dethi::with(['gradeCategory', 'subjectCategory'])
                ->where('type', 'game')
                ->where('status', 1);

            $assignedGameIds = $this->getAssignedGameIdsForStudent($profile);

            $gamesQuery->where(function ($query) use ($assignedGameIds) {
                $query->where(function ($freeQuery) {
                    $freeQuery->where('pricing_type', 'free')
                        ->where(function ($sub) {
                            $sub->whereNull('access_type')
                                ->orWhere('access_type', 'all');
                        });
                });

                if (!empty($assignedGameIds)) {
                    $query->orWhereIn('id', $assignedGameIds);
                }
            });
        } else {
            $gamesQuery = Dethi::with(['gradeCategory', 'subjectCategory', 'game_results', 'customer'])
            ->where('type', 'game');

        if ($profile->type != 3) {
            $gamesQuery->where('created_by', $profile->id);
            }
        }

        if ($request->filled('name')) {
            $gamesQuery->where('title', 'like', '%' . trim($request->name) . '%');
        }

        if ($request->filled('grade_id')) {
            $gamesQuery->where('grade', $request->grade_id);
        }

        if ($request->filled('subject_id')) {
            $gamesQuery->where('subject', $request->subject_id);
        }

        $games = $gamesQuery->orderByDesc('updated_at')->get();

        return view('crm_course.game.list', [
            'games' => $games,
            'grades' => $grades,
            'subjects' => $subjects,
            'filters' => $filters,
            'profile' => $profile,
        ]);
    }
    public function delete($id)
    {
        $profile = Auth::guard('customer')->user();
        $gameQuery = Dethi::where('type', 'game')->where('id', $id);
        if ($profile->type != 3) {
            $gameQuery->where('created_by', $profile->id);
        }
        $game = $gameQuery->first();
        if (!$game) {
            return response()->json(['success' => false, 'message' => 'Game không tồn tại hoặc bạn không có quyền.']);
        }
        $game->delete();
        return response()->json(['success' => true, 'message' => 'Game đã được xóa']);
    }
    public function saveExport(Request $request)
    {
        $teacher = Auth::guard('customer')->user();
        if (!$teacher) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập để lưu mã game.',
            ], 401);
        }

        $validated = $request->validate([
            'game_id' => ['required', 'string', 'max:255'],
            'html' => ['required', 'string'],
            'questions_count' => ['nullable', 'integer', 'min:0'],
            'grade_id' => ['nullable', 'integer', 'min:1'],
            'subject_id' => ['nullable', 'integer', 'min:1'],
            'game_name' => ['nullable', 'string', 'max:255'],
            'game_description' => ['nullable', 'string', 'max:255'],
        ]);

        $gameExport = GameExport::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'game_id' => $validated['game_id'],
            ],
            [
                'html' => $validated['html'],
                'questions_count' => $validated['questions_count'] ?? 0,
                'grade_id' => $validated['grade_id'] ?? null,
                'subject_id' => $validated['subject_id'] ?? null,
                'game_name' => $validated['game_name'] ?? null,
                'game_description' => $validated['game_description'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Đã lưu mã game vào hệ thống!',
            'data' => $gameExport->only(['id', 'game_id']),
        ]);
    }

    public function saveResult(Request $request)
    {
        $validated = $request->validate([
            'game_id' => ['required', 'string', 'max:255'],
            'correct_count' => ['required', 'integer', 'min:0'],
            'total_questions' => ['required', 'integer', 'min:1'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
            'earned_rewards' => ['nullable', 'array'],
            'student_name' => ['nullable', 'string', 'max:255'],
            'student_email' => ['nullable', 'string', 'email', 'max:255'],
        ]);

        $gameExport = GameExport::where('game_id', $validated['game_id'])->first();

        $result = GameResult::create([
            'game_export_id' => $gameExport ? $gameExport->id : null,
            'game_id' => $validated['game_id'],
            'student_id' => Auth::guard('customer')->check() ? Auth::guard('customer')->id() : null,
            'student_name' => $validated['student_name'] ?? null,
            'student_email' => $validated['student_email'] ?? null,
            'correct_count' => $validated['correct_count'],
            'total_questions' => $validated['total_questions'],
            'percentage' => $validated['percentage'],
            'status' => $validated['status'] ?? null,
            'earned_rewards' => $validated['earned_rewards'] ?? null,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Đã lưu kết quả học sinh.',
            'data' => $result->only(['id', 'correct_count', 'total_questions']),
        ], 201);
    }

    public function checkResult(Request $request)
    {
        $validated = $request->validate([
            'game_id' => ['required', 'string', 'max:255'],
        ]);

        $studentId = Auth::guard('customer')->id();
        if (!$studentId) {
            return response()->json(['can_play' => true]);
        }

        $hasPlayed = GameResult::where('game_id', $validated['game_id'])
            ->where('student_id', $studentId)
            ->exists();

        return response()->json([
            'can_play' => !$hasPlayed,
        ]);
    }

    public function ranking($gameId)
    {
        $game = Dethi::where('type', 'game')->findOrFail($gameId);

        // Lấy tất cả kết quả
        $allResults = GameResult::where('game_id', $game->id)->get();
        
        // Tạo mảng kết quả với thời gian làm bài
        $resultsWithTime = [];
        foreach ($allResults as $result) {
            $studentName = $result->student_name
                ?? ($result->student_email ?? ('Học sinh #' . ($result->student_id ?? 'N/A')));
            
            // Tìm attempt tương ứng (cùng game_id, student_id, và thời gian gần nhất với created_at của result)
            $timeSeconds = 0;
            if ($result->student_id) {
                $resultCreatedAt = $result->created_at;
                
                // Tìm attempt có ended_at gần nhất với created_at của result (trong khoảng 10 phút)
                $matchingAttempt = GameAttempt::where('game_id', $game->id)
                    ->where('student_id', $result->student_id)
                    ->where('status', 'completed')
                    ->whereNotNull('started_at')
                    ->whereNotNull('ended_at')
                    ->whereBetween('ended_at', [
                        $resultCreatedAt->copy()->subMinutes(10),
                        $resultCreatedAt->copy()->addMinutes(10)
                    ])
                    ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, ended_at, ?))', [$resultCreatedAt])
                    ->first();
                
                // Nếu không tìm thấy trong khoảng 10 phút, tìm attempt gần nhất
                if (!$matchingAttempt) {
                    $matchingAttempt = GameAttempt::where('game_id', $game->id)
                        ->where('student_id', $result->student_id)
                        ->where('status', 'completed')
                        ->whereNotNull('started_at')
                        ->whereNotNull('ended_at')
                        ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, ended_at, ?))', [$resultCreatedAt])
                        ->first();
                }
                
                if ($matchingAttempt && $matchingAttempt->started_at && $matchingAttempt->ended_at) {
                    $timeSeconds = $matchingAttempt->ended_at->diffInSeconds($matchingAttempt->started_at);
                }
            }
            
            $resultsWithTime[] = [
                'result' => $result,
                'student_name' => $studentName,
                'time_seconds' => $timeSeconds,
                'percentage' => $result->percentage ?? 0,
            ];
        }
        
        // Sắp xếp: percentage giảm dần, nếu bằng nhau thì time_seconds tăng dần (ngắn hơn đứng trước)
        usort($resultsWithTime, function($a, $b) {
            // So sánh percentage
            if ($b['percentage'] != $a['percentage']) {
                return $b['percentage'] <=> $a['percentage'];
            }
            
            // Nếu percentage bằng nhau, so sánh thời gian làm bài (ngắn hơn đứng trước)
            return $a['time_seconds'] <=> $b['time_seconds'];
        });
        
        // Lấy top 50 và format kết quả
        $results = collect($resultsWithTime)
            ->take(50)
            ->map(function ($item, $index) {
                $result = $item['result'];
                return [
                    'rank' => $index + 1,
                    'student_name' => $item['student_name'],
                    'correct_count' => $result->correct_count ?? 0,
                    'total_questions' => $result->total_questions ?? 0,
                    'percentage' => number_format($item['percentage'], 2),
                    'created_at' => $result->created_at ? $result->created_at->format('d/m/Y H:i') : null,
                    'earned_rewards' => $result->earned_rewards,
                ];
            });

        return response()->json([
            'game' => [
                'id' => $game->id,
                'name' => $game->title,
            ],
            'results' => $results,
        ]);
    }

    /**
     * Trang chia sẻ bảng xếp hạng công khai cho từng game (không cần đăng nhập)
     */
    public function publicGameRanking($gameId)
    {
        $game = Dethi::where('type', 'game')->findOrFail($gameId);

        $allResults = GameResult::where('game_id', $game->id)->get();

        $resultsWithTime = [];
        foreach ($allResults as $result) {
            $studentName = $result->student_name
                ?? ($result->student_email ?? ('Học sinh #' . ($result->student_id ?? 'N/A')));

            $timeSeconds = 0;
            if ($result->student_id) {
                $resultCreatedAt = $result->created_at;
                $matchingAttempt = GameAttempt::where('game_id', $game->id)
                    ->where('student_id', $result->student_id)
                    ->where('status', 'completed')
                    ->whereNotNull('started_at')
                    ->whereNotNull('ended_at')
                    ->whereBetween('ended_at', [
                        $resultCreatedAt->copy()->subMinutes(10),
                        $resultCreatedAt->copy()->addMinutes(10)
                    ])
                    ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, ended_at, ?))', [$resultCreatedAt])
                    ->first();

                if (!$matchingAttempt) {
                    $matchingAttempt = GameAttempt::where('game_id', $game->id)
                        ->where('student_id', $result->student_id)
                        ->where('status', 'completed')
                        ->whereNotNull('started_at')
                        ->whereNotNull('ended_at')
                        ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, ended_at, ?))', [$resultCreatedAt])
                        ->first();
                }

                if ($matchingAttempt && $matchingAttempt->started_at && $matchingAttempt->ended_at) {
                    $timeSeconds = $matchingAttempt->ended_at->diffInSeconds($matchingAttempt->started_at);
                }
            }

            $resultsWithTime[] = [
                'result' => $result,
                'student_name' => $studentName,
                'time_seconds' => $timeSeconds,
                'percentage' => $result->percentage ?? 0,
            ];
        }

        usort($resultsWithTime, function($a, $b) {
            if ($b['percentage'] != $a['percentage']) {
                return $b['percentage'] <=> $a['percentage'];
            }
            return $a['time_seconds'] <=> $b['time_seconds'];
        });

        $results = collect($resultsWithTime)
            ->take(50)
            ->map(function ($item, $index) {
                $result = $item['result'];
                return [
                    'rank' => $index + 1,
                    'student_name' => $item['student_name'],
                    'correct_count' => $result->correct_count ?? 0,
                    'total_questions' => $result->total_questions ?? 0,
                    'percentage' => number_format($item['percentage'], 2),
                    'created_at' => $result->created_at ? $result->created_at->format('d/m/Y H:i') : null,
                    'earned_rewards' => $result->earned_rewards,
                ];
            });

        return view('crm_course.game.public-game-ranking', [
            'game' => $game,
            'results' => $results,
            'totalStudents' => $results->count(),
        ]);
    }
    
    /**
     * Danh sách quà tặng
     */
    public function rewardIndex(Request $request)
    {
        $profile = Auth::guard('customer')->user();
        
        $rewardsQuery = GameReward::with('creator');
        
        // Nếu không phải super admin, chỉ hiển thị quà tặng của mình
        if ($profile->type != 3) {
            $rewardsQuery->where('created_by', $profile->id);
        }
        
        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $rewardsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Lọc theo status
        if ($request->filled('status')) {
            $rewardsQuery->where('status', $request->status);
        }
        
        $rewards = $rewardsQuery->orderByDesc('created_at')->paginate(20);
        
        return view('crm_course.game.reward.index', [
            'rewards' => $rewards,
            'filters' => $request->only(['search', 'status']),
        ]);
    }
    
    /**
     * Form thêm quà tặng
     */
    public function rewardCreate()
    {
        return view('crm_course.game.reward.create');
    }
    
    /**
     * Lưu quà tặng mới
     */
    public function rewardStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|integer|in:0,1',
        ]);
        
        $user = Auth::guard('customer')->user();
        
        // Upload image nếu có
        $imagePath = null;

        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filename =
                Str::uuid() . "." . $file->getClientOriginalExtension();
            $path = public_path("reward");

            $file->move($path, $filename);
            $imagePath = "/reward/" . $filename;
        }

        $reward = GameReward::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath ?? null,
            'created_by' => $user->id,
            'status' => $validated['status'],
        ]);
        
        return redirect()->route('game.reward.index')
            ->with('success', 'Đã thêm quà tặng thành công!');
    }
    
    /**
     * Form sửa quà tặng
     */
    public function rewardEdit($id)
    {
        $profile = Auth::guard('customer')->user();
        
        $reward = GameReward::findOrFail($id);
        
        // Kiểm tra quyền
        if ($profile->type != 3 && $reward->created_by != $profile->id) {
            return redirect()->route('game.reward.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa quà tặng này!');
        }
        
        return view('crm_course.game.reward.edit', [
            'reward' => $reward,
        ]);
    }
    
    /**
     * Cập nhật quà tặng
     */
    public function rewardUpdate(Request $request, $id)
    {
        $profile = Auth::guard('customer')->user();
        
        $reward = GameReward::findOrFail($id);
        
        // Kiểm tra quyền
        if ($profile->type != 3 && $reward->created_by != $profile->id) {
            return redirect()->route('game.reward.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa quà tặng này!');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|integer|in:0,1',
        ]);
            
        // Upload image mới nếu có
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filename =
                Str::uuid() . "." . $file->getClientOriginalExtension();
            $path = public_path("reward");

            $file->move($path, $filename);
            $validated['image'] = "/reward/" . $filename;
        } else {
            unset($validated['image']);
        }
        
        $reward->update($validated);
        
        return redirect()->route('game.reward.index')
            ->with('success', 'Đã cập nhật quà tặng thành công!');
    }
    
    /**
     * Xóa quà tặng
     */
    public function rewardDelete($id)
    {
        $profile = Auth::guard('customer')->user();
        
        $reward = GameReward::findOrFail($id);
        
        // Kiểm tra quyền
        if ($profile->type != 3 && $reward->created_by != $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa quà tặng này!'
            ], 403);
        }
        
        // Kiểm tra xem quà tặng có đang được sử dụng trong config không
        $configsCount = GameRewardConfig::where('reward_id', $id)->count();
        if ($configsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa quà tặng này vì đang được sử dụng trong cấu hình phần thưởng!'
            ], 400);
        }
        
        // Xóa image
        if ($reward->image && Storage::disk('public')->exists($reward->image)) {
            Storage::disk('public')->delete($reward->image);
        }
        
        $reward->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa quà tặng thành công!'
        ]);
    }
    
    /**
     * Danh sách cấu hình phần thưởng cho game
     */
    public function rewardConfigIndex($gameId)
    {
        $profile = Auth::guard('customer')->user();
        
        $game = Dethi::where('type', 'game')->findOrFail($gameId);
        
        // Kiểm tra quyền
        if ($profile->type != 3 && $game->created_by != $profile->id) {
            return redirect()->route('gamelistAITrieuPhuToanHoc')
                ->with('error', 'Bạn không có quyền truy cập!');
        }
        
        $configs = GameRewardConfig::with('reward')
            ->where('game_id', $gameId)
            ->orderByDesc('priority')
            ->orderByDesc('min_percentage')
            ->get();
        
        $rewards = GameReward::where('status', 1)->orderBy('name')->get();
        
        return view('crm_course.game.reward.config', [
            'game' => $game,
            'configs' => $configs,
            'rewards' => $rewards,
        ]);
    }
    
    /**
     * API: Lấy dữ liệu cấu hình phần thưởng (cho popup)
     */
    public function rewardConfigData($gameId)
    {
        $profile = Auth::guard('customer')->user();
        
        $game = Dethi::where('type', 'game')->findOrFail($gameId);
        
        // Kiểm tra quyền
        if ($profile->type != 3 && $game->created_by != $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập!'
            ], 403);
        }
        
        $configs = GameRewardConfig::with('reward')
            ->where('game_id', $gameId)
            ->orderByDesc('priority')
            ->orderByDesc('min_percentage')
            ->get()
            ->map(function($config) {
                return [
                    'id' => $config->id,
                    'reward_id' => $config->reward_id,
                    'reward_name' => $config->reward->name,
                    'reward_description' => $config->reward->description,
                    'min_percentage' => $config->min_percentage,
                    'max_percentage' => $config->max_percentage,
                    'quantity' => $config->quantity,
                    'priority' => $config->priority,
                ];
            });
        
        $rewards = GameReward::where('status', 1)->orderBy('name')->get()->map(function($reward) {
            return [
                'id' => $reward->id,
                'name' => $reward->name,
                'description' => $reward->description,
            ];
        });
        
        return response()->json([
            'success' => true,
            'game' => [
                'id' => $game->id,
                'title' => $game->title,
            ],
            'configs' => $configs,
            'rewards' => $rewards,
        ]);
    }
    
    /**
     * Lưu cấu hình phần thưởng
     */
    public function rewardConfigStore(Request $request, $gameId)
    {
        $profile = Auth::guard('customer')->user();
        
        $game = Dethi::where('type', 'game')->findOrFail($gameId);
        
        // Kiểm tra quyền
        if ($profile->type != 3 && $game->created_by != $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!'
            ], 403);
        }
        
        $validated = $request->validate([
            'reward_id' => 'required|exists:game_rewards,id',
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100|gte:min_percentage',
            'quantity' => 'required|integer|min:1|max:10',
            'priority' => 'nullable|integer|min:0',
        ]);
        
        $config = GameRewardConfig::create([
            'game_id' => $gameId,
            'reward_id' => $validated['reward_id'],
            'min_percentage' => $validated['min_percentage'],
            'max_percentage' => $validated['max_percentage'] ?? null,
            'quantity' => $validated['quantity'],
            'priority' => $validated['priority'] ?? 0,
        ]);
        
        $config->load('reward');
        
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm cấu hình phần thưởng thành công!',
            'config' => [
                'id' => $config->id,
                'reward_name' => $config->reward->name,
                'min_percentage' => $config->min_percentage,
                'max_percentage' => $config->max_percentage,
                'quantity' => $config->quantity,
                'priority' => $config->priority,
            ],
        ]);
    }
    
    /**
     * Xóa cấu hình phần thưởng
     */
    public function rewardConfigDelete($id)
    {
        $profile = Auth::guard('customer')->user();
        
        $config = GameRewardConfig::with('game')->findOrFail($id);
        
        // Kiểm tra quyền
        if ($profile->type != 3 && $config->game->created_by != $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa cấu hình này!'
            ], 403);
        }
        
        $config->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa cấu hình phần thưởng thành công!'
        ]);
    }
    
    /**
     * Bảng xếp hạng tổng quát - tất cả game
     * Super admin: xem tất cả học sinh
     * Giáo viên: xem học sinh mà giáo viên quản lý
     */
    public function overallRanking()
    {
        $profile = Auth::guard('customer')->user();
        
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để xem bảng xếp hạng.'
            ], 401);
        }
        
        // Lấy danh sách student_id cần lọc
        $studentIds = null;
        
        if ($profile->type == 1) {
            // Giáo viên: chỉ xem học sinh của mình
            $teacherClasses = \App\models\SchoolClass::where('homeroom_teacher_id', $profile->id)
                ->pluck('class_code')
                ->toArray();
            
            if (empty($teacherClasses)) {
                return response()->json([
                    'success' => true,
                    'results' => [],
                    'message' => 'Bạn chưa có lớp học nào.'
                ]);
            }
            
            // Lấy tất cả học sinh thuộc các lớp này
            $studentIds = \App\Customer::where('type', 0)
                ->where(function($query) use ($teacherClasses) {
                    foreach ($teacherClasses as $classCode) {
                        $query->orWhereJsonContains('class_code', $classCode);
                    }
                })
                ->pluck('id')
                ->toArray();
            
            if (empty($studentIds)) {
                return response()->json([
                    'success' => true,
                    'results' => [],
                    'message' => 'Chưa có học sinh nào trong lớp của bạn.'
                ]);
            }
        } elseif ($profile->type != 3) {
            // Nếu không phải super admin và không phải giáo viên, không có quyền
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem bảng xếp hạng này.'
            ], 403);
        }
        
        // Lấy tất cả kết quả game
        $resultsQuery = GameResult::whereNotNull('student_id');
        
        if ($studentIds !== null) {
            $resultsQuery->whereIn('student_id', $studentIds);
        }
        
        $allResults = $resultsQuery->get();
        
        // Lấy tất cả attempts để tính thời gian làm bài
        $attemptsQuery = GameAttempt::whereNotNull('student_id')
            ->where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('ended_at');
        
        if ($studentIds !== null) {
            $attemptsQuery->whereIn('student_id', $studentIds);
        }
        
        $allAttempts = $attemptsQuery->get();
        
        // Nhóm theo student_id và tính điểm trung bình
        $studentStats = [];
        
        foreach ($allResults as $result) {
            $studentId = $result->student_id;
            
            if (!isset($studentStats[$studentId])) {
                $student = \App\Customer::find($studentId);
                $studentStats[$studentId] = [
                    'student_id' => $studentId,
                    'student_name' => $result->student_name ?? ($student ? $student->name : 'Học sinh #' . $studentId),
                    'student_email' => $result->student_email ?? ($student ? $student->email : null),
                    'total_games' => 0,
                    'total_percentage' => 0,
                    'average_percentage' => 0,
                    'total_correct' => 0,
                    'total_questions' => 0,
                    'total_time_seconds' => 0, // Tổng thời gian làm bài (giây)
                ];
            }
            
            $studentStats[$studentId]['total_games']++;
            $studentStats[$studentId]['total_percentage'] += $result->percentage ?? 0;
            $studentStats[$studentId]['total_correct'] += $result->correct_count ?? 0;
            $studentStats[$studentId]['total_questions'] += $result->total_questions ?? 0;
        }
        
        // Tính tổng thời gian làm bài từ attempts
        foreach ($allAttempts as $attempt) {
            $studentId = $attempt->student_id;
            
            if (isset($studentStats[$studentId]) && $attempt->started_at && $attempt->ended_at) {
                $timeDiff = $attempt->ended_at->diffInSeconds($attempt->started_at);
                $studentStats[$studentId]['total_time_seconds'] += $timeDiff;
            }
        }
        
        // Tính điểm trung bình
        foreach ($studentStats as &$stat) {
            if ($stat['total_games'] > 0) {
                $stat['average_percentage'] = round($stat['total_percentage'] / $stat['total_games'], 2);
            }
        }
        
        // Sắp xếp theo điểm trung bình giảm dần, nếu bằng nhau thì theo thời gian làm bài tăng dần (ít hơn đứng trước)
        usort($studentStats, function($a, $b) {
            // So sánh điểm trung bình
            if ($b['average_percentage'] != $a['average_percentage']) {
                return $b['average_percentage'] <=> $a['average_percentage'];
            }
            
            // Nếu điểm bằng nhau, so sánh thời gian làm bài (ít hơn đứng trước)
            return $a['total_time_seconds'] <=> $b['total_time_seconds'];
        });
        
        // Thêm rank
        $rankedResults = [];
        foreach ($studentStats as $index => $stat) {
            $rankedResults[] = [
                'rank' => $index + 1,
                'student_id' => $stat['student_id'],
                'student_name' => $stat['student_name'],
                'student_email' => $stat['student_email'],
                'total_games' => $stat['total_games'],
                'average_percentage' => $stat['average_percentage'],
                'total_correct' => $stat['total_correct'],
                'total_questions' => $stat['total_questions'],
            ];
        }
        
        return response()->json([
            'success' => true,
            'results' => $rankedResults,
            'is_teacher' => $profile->type == 1,
            'is_super_admin' => $profile->type == 3,
        ]);
    }
    
    /**
     * Hiển thị bảng xếp hạng công khai để chia sẻ (không cần đăng nhập)
     */
    public function shareRanking()
    {
        // Lấy tất cả kết quả game (công khai, không lọc theo giáo viên)
        $allResults = GameResult::whereNotNull('student_id')->get();
        
        // Lấy tất cả attempts để tính thời gian làm bài
        $allAttempts = GameAttempt::whereNotNull('student_id')
            ->where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('ended_at')
            ->get();
        
        // Nhóm theo student_id và tính điểm trung bình
        $studentStats = [];
        
        foreach ($allResults as $result) {
            $studentId = $result->student_id;
            
            if (!isset($studentStats[$studentId])) {
                $student = \App\Customer::find($studentId);
                $studentStats[$studentId] = [
                    'student_id' => $studentId,
                    'student_name' => $result->student_name ?? ($student ? $student->name : 'Học sinh #' . $studentId),
                    'total_games' => 0,
                    'total_percentage' => 0,
                    'average_percentage' => 0,
                    'total_correct' => 0,
                    'total_questions' => 0,
                ];
            }
            
            $studentStats[$studentId]['total_games']++;
            $studentStats[$studentId]['total_percentage'] += $result->percentage ?? 0;
            $studentStats[$studentId]['total_correct'] += $result->correct_count ?? 0;
            $studentStats[$studentId]['total_questions'] += $result->total_questions ?? 0;
        }
        
        // Tính điểm trung bình
        foreach ($studentStats as &$stat) {
            if ($stat['total_games'] > 0) {
                $stat['average_percentage'] = round($stat['total_percentage'] / $stat['total_games'], 2);
            }
        }
        
        // Sắp xếp theo điểm trung bình giảm dần
        usort($studentStats, function($a, $b) {
            return $b['average_percentage'] <=> $a['average_percentage'];
        });
        
        // Thêm rank
        $rankedResults = [];
        foreach ($studentStats as $index => $stat) {
            $rankedResults[] = [
                'rank' => $index + 1,
                'student_id' => $stat['student_id'],
                'student_name' => $stat['student_name'],
                'total_games' => $stat['total_games'],
                'average_percentage' => $stat['average_percentage'],
                'total_correct' => $stat['total_correct'],
                'total_questions' => $stat['total_questions'],
            ];
        }
        
        return view('crm_course.game.share-ranking', [
            'results' => $rankedResults,
            'totalStudents' => count($rankedResults),
        ]);
    }
    
    /**
     * Xóa tất cả kết quả game của một học sinh
     * Chỉ giáo viên và super admin mới có quyền
     */
    public function deleteStudentResults($studentId)
    {
        $profile = Auth::guard('customer')->user();
        
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.'
            ], 401);
        }
        
        // Kiểm tra quyền
        if ($profile->type == 1) {
            // Giáo viên: chỉ xóa được học sinh của mình
            $teacherClasses = \App\models\SchoolClass::where('homeroom_teacher_id', $profile->id)
                ->pluck('class_code')
                ->toArray();
            
            if (empty($teacherClasses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa kết quả này.'
                ], 403);
            }
            
            $student = \App\Customer::find($studentId);
            if (!$student || $student->type != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Học sinh không tồn tại.'
                ], 404);
            }
            
            // Kiểm tra học sinh có thuộc lớp của giáo viên không
            $studentClassCodes = $student->class_code ?? [];
            $hasAccess = false;
            foreach ($teacherClasses as $classCode) {
                if (in_array($classCode, $studentClassCodes)) {
                    $hasAccess = true;
                    break;
                }
            }
            
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa kết quả của học sinh này.'
                ], 403);
            }
        } elseif ($profile->type != 3) {
            // Chỉ super admin và giáo viên mới có quyền
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }
        
        // Xóa tất cả kết quả game của học sinh
        $deletedCount = GameResult::where('student_id', $studentId)->delete();
        
        // Xóa tất cả attempts của học sinh
        GameAttempt::where('student_id', $studentId)->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$deletedCount} kết quả game của học sinh. Học sinh có thể bắt đầu làm lại.",
            'deleted_count' => $deletedCount,
        ]);
    }
    
    /**
     * Xóa kết quả game của nhiều học sinh cùng lúc
     * Chỉ giáo viên và super admin mới có quyền
     */
    public function deleteMultipleStudentResults(Request $request)
    {
        $profile = Auth::guard('customer')->user();
        
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.'
            ], 401);
        }
        
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|integer|exists:customer,id',
        ]);
        
        $studentIds = $validated['student_ids'];
        
        // Kiểm tra quyền
        if ($profile->type == 1) {
            // Giáo viên: chỉ xóa được học sinh của mình
            $teacherClasses = \App\models\SchoolClass::where('homeroom_teacher_id', $profile->id)
                ->pluck('class_code')
                ->toArray();
            
            if (empty($teacherClasses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa kết quả này.'
                ], 403);
            }
            
            // Lấy danh sách học sinh của giáo viên
            $teacherStudentIds = \App\Customer::where('type', 0)
                ->where(function($query) use ($teacherClasses) {
                    foreach ($teacherClasses as $classCode) {
                        $query->orWhereJsonContains('class_code', $classCode);
                    }
                })
                ->pluck('id')
                ->toArray();
            
            // Kiểm tra tất cả student_ids có thuộc lớp của giáo viên không
            $unauthorizedIds = array_diff($studentIds, $teacherStudentIds);
            if (!empty($unauthorizedIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa kết quả của một số học sinh được chọn.'
                ], 403);
            }
        } elseif ($profile->type != 3) {
            // Chỉ super admin và giáo viên mới có quyền
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }
        
        // Xóa tất cả kết quả game của các học sinh
        $deletedResultsCount = GameResult::whereIn('student_id', $studentIds)->delete();
        
        // Xóa tất cả attempts của các học sinh
        $deletedAttemptsCount = GameAttempt::whereIn('student_id', $studentIds)->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Đã xóa kết quả của {$deletedResultsCount} lượt chơi từ " . count($studentIds) . " học sinh. Học sinh có thể bắt đầu làm lại.",
            'deleted_results_count' => $deletedResultsCount,
            'deleted_attempts_count' => $deletedAttemptsCount,
            'deleted_students_count' => count($studentIds),
        ]);
    }
}
