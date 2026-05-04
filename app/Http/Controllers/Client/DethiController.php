<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\Font;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\models\dethi\Dethi;
use App\models\dethi\DethiPart;
use App\models\dethi\DethiQuestion;
use App\models\dethi\ExamAnswer;
use App\models\dethi\ExamSession;
use App\models\dethi\ExamFolder;
use App\models\dethi\DethiAnswer;
use App\models\dethi\DethiClass;
use App\models\SchoolClass;
use App\models\Quiz\QuizCategory;
use App\models\Quiz\CategoryMain;
use App\models\Quiz\TypeCategory;
use App\models\Bill\BillDethi;
use App\Exports\ScoreExport;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DethiController extends Controller
{
    public function allDeThi(Request $request){
        // Lấy tất cả các cấp học (CategoryMain)
        $grades = CategoryMain::where('status', 1)
            ->withCount(['category' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy tất cả các môn học (QuizCategory) - không group, trả về collection
        $subjects = QuizCategory::where('status', 1)
            ->orderBy('cate_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy tất cả loại đề (TypeCategory - Bộ đề) với thông tin đầy đủ
        $allExamTypes = TypeCategory::where('status', 1)
            ->with('cate')  // Load môn học
            ->orderBy('cate_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy tất cả đề thi
        $allExams = Dethi::with(['gradeCategory', 'subjectCategory', 'parts.questions', 'sessions', 'typeCategory', 'customer'])
            ->where('type', 'dethi')
            ->where('status', 1)
            ->where('access_type', 'all')
            ->orderBy('id', 'DESC')
            ->get();
        
        // Hiển thị TẤT CẢ bộ đề (TypeCategory) kể cả không có đề thi
        $examsByType = [];
        // dd($allExamTypes);
        foreach ($allExamTypes as $examType) {
            $examsInType = $allExams->filter(function($exam) use ($examType) {
                // Filter đề thi theo cate_type_id
                return $exam->cate_type_id == $examType->id;
            });
            
            // Luôn hiển thị bộ đề, chỉ lấy 10 đề đầu tiên
            $examsByType[] = [
                'type' => $examType,
                'exams' => $examsInType->take(10),
                'count' => $examsInType->count(), // Tổng số thực tế
                'total' => $examsInType->count()
            ];
        }
        
        // Đề thi không thuộc bộ đề nào sẽ nhóm vào "Đề thi khác"
        $unmatchedExams = $allExams->filter(function($exam) {
            // Đề thi không có cate_type_id hoặc cate_type_id = null/0
            return !$exam->cate_type_id || $exam->cate_type_id == 0;
        });
        
        if ($unmatchedExams->count() > 0) {
            $examsByType[] = [
                'type' => (object)['name' => 'Đề thi khác', 'id' => 0],
                'exams' => $unmatchedExams->take(10),
                'count' => $unmatchedExams->count(), // Tổng số thực tế
                'total' => $unmatchedExams->count()
            ];
        }
        
        // Group examTypes theo môn học để hiển thị filter
        $examTypes = $allExamTypes->groupBy('cate_id');
        
        // Thống kê số lượng đề thi
        $totalExams = $allExams->count();
        
        // Khởi tạo các biến selected để tránh lỗi undefined
        $selectedGrade = null;
        $selectedSubject = null;
        $selectedExamType = null;
        
        return view('dethi.listall', compact(
            'examsByType', 'grades', 'subjects', 'examTypes', 'totalExams', 
            'selectedGrade', 'selectedSubject', 'selectedExamType'
        ));
    }
    
    public function loadMoreExams(Request $request)
    {
        $typeId = $request->input('type_id');
        $offset = $request->input('offset', 0);
        $perPage = $request->input('per_page', 10);
        $gradeId = $request->input('grade_id');
        $subjectId = $request->input('subject_id');
        
        // Khởi tạo query
        $query = Dethi::with(['gradeCategory', 'subjectCategory', 'parts.questions', 'typeCategory', 'customer'])
            ->where('type', 'dethi')
            ->where('status', 1)
            ->where('access_type', 'all')
            ->orderBy('id', 'DESC');
        
        // Áp dụng filter nếu có
        if ($gradeId) {
            $query->where('grade', $gradeId);
        }
        if ($subjectId) {
            $query->where('subject', $subjectId);
        }
        
        // Lấy tất cả đề thi thỏa điều kiện
        $allExams = $query->get();
        
        // Filter theo cate_type_id
        if ($typeId && $typeId != 0) {
            $allExams = $allExams->filter(function($exam) use ($typeId) {
                return $exam->cate_type_id == $typeId;
            });
        } elseif ($typeId == 0) {
            // Đề thi khác - không có cate_type_id
            $allExams = $allExams->filter(function($exam) {
                return !$exam->cate_type_id || $exam->cate_type_id == 0;
            });
        }
        
        // Phân trang
        $exams = $allExams->skip($offset)->take($perPage)->values();
        $hasMore = $allExams->count() > ($offset + $perPage);
        $remaining = max(0, $allExams->count() - ($offset + $perPage));
        
        return response()->json([
            'success' => true,
            'exams' => $exams,
            'has_more' => $hasMore,
            'remaining' => $remaining,
            'total' => $allExams->count()
        ]);
    }
    
    public function parseContent(Request $request)
    {
        $text = $request->input('rawContent');
        $originParts = $request->input('originParts');
        $parsedQuestions = $this->parseTextContent($text,$originParts);

        return response()->json([
            'questions' => $parsedQuestions
        ]);
    }
    public function parseContentQuestion(Request $request)
    {
        $id = $request->input('id');
        $text = $request->input('rawContent');
        $answerIds = $request->input('answerIds');
        $parsedQuestions = $this->parseTextContentForSingleQuestion($text,$answerIds);
        return response()->json([
            'question' => $parsedQuestions,
            'id' => $id
        ]);
    }
    public function index(Request $request){
        $profile = Auth::guard("customer")->user();
        $folderTree = collect();
        $folderOptions = [];
        $selectedFolderKey = 'root';
        $activeFolder = null;
        $childFolders = collect();
        $folderBreadcrumb = collect();
        $currentFolderId = null;
        $folderType = ExamFolder::TYPE_EXAM;
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type == 1 || $isSuperAdmin){
            // Giáo viên (type 1) hoặc Super Admin (type 3)
            // Giáo viên sẽ thấy folders của mình + của super admin
           
            $includeSuperAdminFolders = $profile->type == 1;
            $folderTree = ExamFolder::treeForOwner($profile->id, $folderType, $isSuperAdmin, $includeSuperAdminFolders);
            $folderOptions = $this->flattenFolderOptions($folderTree);
            
            // Tạo folderOptions riêng cho việc move đề thi (chỉ folders của giáo viên)
            $moveFolderOptions = [];
            if (!$isSuperAdmin) {
                // Chỉ lấy folders của giáo viên cho việc move
                $teacherFolderTree = ExamFolder::treeForOwner($profile->id, $folderType, false, false);
                $moveFolderOptions = $this->flattenFolderOptions($teacherFolderTree);
            } else {
                // Super admin có thể move vào bất kỳ folder nào
                $moveFolderOptions = $folderOptions;
            }
            
            $folderParam = $request->input('folder_id');

            // Lấy danh sách super admin IDs
            $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();

            // Lấy query cơ bản với eager loading
            $query = Dethi::with(['allowedClasses.schoolClass', 'folder', 'customer']);
            
            // Sắp xếp: super admin (type 3) lên đầu, sau đó mới đến giáo viên
            // Sử dụng leftJoin để sắp xếp theo type của customer
            $query = $query->leftJoin('customer', 'dethi.created_by', '=', 'customer.id')
                ->where('dethi.type','dethi')  // Chỉ định rõ dethi.type để tránh ambiguous
                ->select('dethi.*')
                ->orderByRaw('CASE WHEN customer.type = 3 THEN 0 ELSE 1 END')
                ->orderBy('dethi.id','DESC');

            // Nếu là giáo viên, lấy đề thi của chính họ + của super admin
            if (!$isSuperAdmin) {
                $query->where(function($q) use ($profile, $superAdminIds) {
                    $q->where('dethi.created_by', $profile->id);
                    if (!empty($superAdminIds)) {
                        $q->orWhereIn('dethi.created_by', $superAdminIds);
                    }
                });
            }

            if(empty($folderParam) || $folderParam === 'root'){
                $selectedFolderKey = 'root';
                $query->whereNull('dethi.folder_id');
            }elseif(is_numeric($folderParam)){
                $folderQuery = ExamFolder::ofType($folderType);
                if ($isSuperAdmin) {
                    // Super admin có thể xem tất cả
                } elseif ($includeSuperAdminFolders) {
                    // Giáo viên: có thể xem folder của mình hoặc của super admin
                    $folderQuery->where(function($q) use ($profile, $superAdminIds) {
                        $q->where('owner_id', $profile->id);
                        if (!empty($superAdminIds)) {
                            $q->orWhereIn('owner_id', $superAdminIds);
                        }
                    });
                } else {
                    $folderQuery->ownedBy($profile->id);
                }
                $activeFolder = $folderQuery->with('owner')->find($folderParam);
                
                if($activeFolder){
                    $selectedFolderKey = (string) $activeFolder->id;
                    $currentFolderId = $activeFolder->id;
                    $query->where('dethi.folder_id', $activeFolder->id);
                    $folderBreadcrumb = $this->buildFolderBreadcrumb($activeFolder);
                }else{
                    $query->whereNull('dethi.folder_id');
                }
            }else{
                $query->whereNull('dethi.folder_id');
            }

            // Giáo viên/Super Admin: hiển thị đề thi
            $data = $query->paginate(20);

            $childFoldersQuery = ExamFolder::query()
                ->leftJoin('customer', 'exam_folders.owner_id', '=', 'customer.id')
                ->where('exam_folders.type', $folderType)  // Chỉ định rõ exam_folders.type sau khi join
                ->where('exam_folders.parent_id', $activeFolder ? $activeFolder->id : null)
                ->select('exam_folders.*');
            
            if ($isSuperAdmin) {
                // Super admin thấy tất cả
                $childFoldersQuery->with('owner');
            } elseif ($includeSuperAdminFolders) {
                // Giáo viên: thấy folders của mình + của super admin
                $childFoldersQuery->where(function($q) use ($profile, $superAdminIds) {
                    $q->where('exam_folders.owner_id', $profile->id);
                    if (!empty($superAdminIds)) {
                        $q->orWhereIn('exam_folders.owner_id', $superAdminIds);
                    }
                });
                $childFoldersQuery->with('owner');
            } else {
                $childFoldersQuery->where('exam_folders.owner_id', $profile->id);
            }
            
            $childFolders = $childFoldersQuery
                // Sắp xếp: super admin (type 3) lên đầu, sau đó mới đến giáo viên
                ->orderByRaw('CASE WHEN customer.type = 3 THEN 0 ELSE 1 END')
                ->orderBy('exam_folders.position')
                ->orderBy('exam_folders.name')
                ->get();
            
            // Kiểm tra nếu giáo viên đang xem folder của super admin
            $hideActionButtons = false;
            if (!$isSuperAdmin && $activeFolder && $activeFolder->owner && $activeFolder->owner->type == 3) {
                $hideActionButtons = true;
            }
            // dd($hideActionButtons);
            // Lấy danh sách lớp để cấu hình quyền làm bài
            $schoolClasses = $profile->type == 3
                ? SchoolClass::active()->orderBy('class_name')->get()
                : SchoolClass::active()
                    ->where(function($q) use ($profile) {
                        $q->where('homeroom_teacher_id', $profile->id)
                          ->orWhereNull('homeroom_teacher_id');
                    })
                    ->orderBy('class_name')
                    ->get();
        }else{
            // Học sinh: hiển thị các loại đề thi theo yêu cầu + đề thi đã mua
            $classCodes = $profile->class_codes ?? [];
            $classIds = [];
            $teacherIds = [];
            
            // Lấy thông tin lớp và giáo viên của học sinh
            if(!empty($classCodes)){
                $studentClasses = \App\models\SchoolClass::whereIn('class_code', $classCodes)->get();
                
                if($studentClasses->isNotEmpty()){
                    // Lấy tất cả class_id của học sinh
                    $classIds = $studentClasses->pluck('id')->toArray();
                    
                    // Lấy các giáo viên chủ nhiệm của các lớp học sinh thuộc về
                    $teacherIds = $studentClasses->whereNotNull('homeroom_teacher_id')
                        ->pluck('homeroom_teacher_id')
                        ->unique()
                        ->toArray();
                }
            }
            
            // Lấy super admin IDs
            $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
            
            // Query để lấy các đề thi học sinh có thể xem (theo access_type)
            $examsQuery = Dethi::with('customer')
                ->where('type', 'dethi')
                ->where('status', 1) // Chỉ lấy đề thi đã kích hoạt
                ->where(function($query) use ($superAdminIds, $teacherIds, $classIds) {
                    // 1. Đề dành cho "tất cả mọi người" của super admin
                    $query->where(function($q) use ($superAdminIds) {
                        $q->where('access_type', 'all')
                          ->whereIn('created_by', $superAdminIds);
                    });
                    
                    // 2. Đề dành cho "tất cả mọi người" của giáo viên quản lý lớp của học sinh
                    if (!empty($teacherIds)) {
                        $query->orWhere(function($q) use ($teacherIds) {
                            $q->where('access_type', 'all')
                              ->whereIn('created_by', $teacherIds);
                        });
                    }
                    
                    // 3. Đề của giáo viên giao cho lớp (thông qua DethiClass)
                    if (!empty($classIds)) {
                        $assignedExamIds = \App\models\dethi\DethiClass::whereIn('class_id', $classIds)
                            ->pluck('dethi_id')
                            ->unique()
                            ->toArray();
                        
                        if (!empty($assignedExamIds)) {
                            $query->orWhere(function($q) use ($assignedExamIds) {
                                $q->whereIn('id', $assignedExamIds)
                                  ->where('access_type', 'class');
                            });
                        }
                    }
                    
                    // 4. Đề của giáo viên giao theo thời gian (time_limited và chưa hết hạn)
                    $now = now();
                    $query->orWhere(function($q) use ($now, $superAdminIds, $teacherIds) {
                        $q->where('access_type', 'time_limited')
                          ->where(function($timeQ) use ($now) {
                              $timeQ->whereNull('end_time')
                                    ->orWhere('end_time', '>=', $now);
                          });
                        
                        // Chỉ lấy của super admin hoặc giáo viên quản lý lớp
                        if (!empty($superAdminIds) || !empty($teacherIds)) {
                            $q->where(function($ownerQ) use ($superAdminIds, $teacherIds) {
                                if (!empty($superAdminIds)) {
                                    $ownerQ->whereIn('created_by', $superAdminIds);
                                }
                                if (!empty($teacherIds)) {
                                    if (!empty($superAdminIds)) {
                                        $ownerQ->orWhereIn('created_by', $teacherIds);
                                    } else {
                                        $ownerQ->whereIn('created_by', $teacherIds);
                                    }
                                }
                            });
                        }
                    });
                });
            
            // Lấy tất cả đề thi có thể xem theo access_type
            $accessibleExams = $examsQuery->get();
            
            // Lấy các đề thi đã mua (bao gồm cả chưa thanh toán)
            $purchasedExams = BillDethi::with('dethi.customer')
                ->where('student_id', $profile->id)
                ->get();
            
            // Merge và loại bỏ trùng lặp
            $allExams = collect();
            
            // Thêm đề thi đã mua (bao gồm cả chưa thanh toán)
            foreach($purchasedExams as $bill){
                if($bill->dethi){
                    $allExams->push([
                        'dethi' => $bill->dethi,
                        'status' => $bill->status, // 0: chưa thanh toán, 1: đã thanh toán
                        'type' => 'purchased',
                        'bill_id' => $bill->id
                    ]);
                }
            }
            
            // Thêm đề thi có thể xem theo access_type (nếu chưa có trong danh sách mua)
            $purchasedExamIds = $purchasedExams->pluck('dethi_id')->toArray();
            foreach($accessibleExams as $exam){
                if(!in_array($exam->id, $purchasedExamIds)){
                    // Xác định loại đề thi
                    $examType = 'accessible';
                    $examStatus = 4; // 4: đề thi có thể truy cập (theo access_type)
                    
                    // Kiểm tra xem đề thi có phải là miễn phí không
                    if($exam->pricing_type == 'free' && $exam->access_type == 'all'){
                        $examType = 'free';
                        $examStatus = 3; // 3: đề thi miễn phí công khai
                    } elseif($exam->access_type == 'class'){
                        $examType = 'assigned';
                        $examStatus = 2; // 2: đề thi được giao từ lớp
                    }
                    
                    $allExams->push([
                        'dethi' => $exam,
                        'status' => $examStatus,
                        'type' => $examType,
                        'bill_id' => null
                    ]);
                }
            }
            
            // Sắp xếp: đề chưa thanh toán -> đề được giao -> đề miễn phí -> đề có thể truy cập -> đề đã thanh toán
            $sortedExams = $allExams->sortBy(function($item){
                if($item['status'] == 0) return 0; // Chưa thanh toán (cần thanh toán)
                if($item['status'] == 2) return 1; // Được giao (từ lớp)
                if($item['status'] == 3) return 2; // Miễn phí công khai
                if($item['status'] == 4) return 3; // Có thể truy cập (theo access_type)
                return 4; // Đã thanh toán
            })->values();
            
            // Tạo pagination từ collection
            $perPage = 20;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $sortedExams->slice(($currentPage - 1) * $perPage, $perPage)->all();
            
            $data = new LengthAwarePaginator(
                $currentItems,
                $sortedExams->count(),
                $perPage,
                $currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
            $hideActionButtons = false; // Học sinh không có các nút này
        }

        // Thông tin session cho chức năng Cut/Paste và Copy/Paste
        $cutFolders = session('cut_folders', []);
        $cutDethis = session('cut_dethis', []);
        $hasCutSession = !empty($cutFolders) || !empty($cutDethis);
        
        $copyFolders = session('copy_folders', []);
        $copyDethis = session('copy_dethis', []);
        $hasCopySession = !empty($copyFolders) || !empty($copyDethis);
        
        // Kiểm tra action_type để biết đang là copy hay cut
        $actionType = session('action_type', 'cut');
        $hasAnySession = $hasCutSession || $hasCopySession;

        return view('crm_course.dethi.index',[
            'data' => $data,
            'profile' => $profile,
            'folderTree' => $folderTree,
            'folderOptions' => $folderOptions,
            'moveFolderOptions' => $moveFolderOptions ?? $folderOptions, // Options cho việc move đề thi
            'selectedFolderKey' => $selectedFolderKey,
            'activeFolder' => $activeFolder,
            'childFolders' => $childFolders,
            'folderBreadcrumb' => $folderBreadcrumb,
            'currentFolderId' => $currentFolderId,
            'hideActionButtons' => $hideActionButtons ?? false,
            'schoolClasses' => $schoolClasses ?? collect(),
            'hasCutSession' => $hasCutSession,
            'cutFoldersCount' => is_array($cutFolders) ? count($cutFolders) : 0,
            'cutDethisCount' => is_array($cutDethis) ? count($cutDethis) : 0,
            'hasCopySession' => $hasCopySession,
            'copyFoldersCount' => is_array($copyFolders) ? count($copyFolders) : 0,
            'copyDethisCount' => is_array($copyDethis) ? count($copyDethis) : 0,
            'actionType' => $actionType,
            'hasAnySession' => $hasAnySession,
        ]);
    }

    protected function flattenFolderOptions($folders, string $prefix = '')
    {
        $options = [];
        foreach ($folders as $folder) {
            $options[] = [
                'id' => $folder->id,
                'label' => $prefix . $folder->name,
            ];

            $children = $folder->children ?? collect();
            if ($children->count() > 0) {
                $options = array_merge($options, $this->flattenFolderOptions($children, $prefix . '-- '));
            }
        }

        return $options;
    }

    protected function buildFolderBreadcrumb(ExamFolder $folder)
    {
        $segments = collect();
        $current = $folder;
        while ($current) {
            $segments->push($current);
            $current = $current->parent;
        }

        return $segments->reverse()->values();
    }

    protected function resolveFolderIdForOwner($folderId, $ownerId, string $type = ExamFolder::TYPE_EXAM)
    {
        if (empty($folderId) || $folderId === 'root') {
            return null;
        }

        $folder = ExamFolder::ownedBy($ownerId)
            ->ofType($type)
            ->find($folderId);

        return $folder ? $folder->id : null;
    }

    /**
     * API: Lấy folders và đề thi cho ngân hàng câu hỏi
     */
    public function getQuestionBankData(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $folderType = ExamFolder::TYPE_EXAM;
        $isSuperAdmin = $profile->type == 3;
        $isTeacher = $profile->type == 1;
        
        if (!$isSuperAdmin && !$isTeacher) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập'
            ], 403);
        }

        // Lấy danh sách super admin IDs
        $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
        
        // Lấy folders
        $includeSuperAdminFolders = $isTeacher;
        $folderTree = ExamFolder::treeForOwner($profile->id, $folderType, $isSuperAdmin, $includeSuperAdminFolders);
        
        // Lấy đề thi
        $query = Dethi::with(['folder', 'customer', 'parts'])
            ->where('type', 'dethi');
        
        if (!$isSuperAdmin) {
            $query->where(function($q) use ($profile, $superAdminIds) {
                $q->where('created_by', $profile->id);
                if (!empty($superAdminIds)) {
                    $q->orWhereIn('created_by', $superAdminIds);
                }
            });
        }
        
        // Sắp xếp: super admin lên đầu
        if (!empty($superAdminIds)) {
            $exams = $query->orderByRaw('CASE WHEN created_by IN (' . implode(',', $superAdminIds) . ') THEN 0 ELSE 1 END')
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $exams = $query->orderBy('id', 'DESC')->get();
        }

        return response()->json([
            'success' => true,
            'folders' => $folderTree,
            'exams' => $exams
        ]);
    }

    /**
     * API: Lấy folder con của một folder cụ thể
     */
    public function getFolderChildren(Request $request, $folderId)
    {
        $profile = Auth::guard("customer")->user();
        $folderType = ExamFolder::TYPE_EXAM;
        $isSuperAdmin = $profile->type == 3;
        $isTeacher = $profile->type == 1;
        
        if (!$isSuperAdmin && !$isTeacher) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập'
            ], 403);
        }

        // Lấy danh sách super admin IDs
        $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();

        // Tìm folder
        $folder = ExamFolder::find($folderId);
        if (!$folder || $folder->type !== $folderType) {
            return response()->json([
                'success' => false,
                'message' => 'Folder không tồn tại'
            ], 404);
        }

        // Kiểm tra quyền truy cập folder
        if (!$isSuperAdmin && $folder->owner_id != $profile->id && !in_array($folder->owner_id, $superAdminIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập folder này'
            ], 403);
        }

        // Lấy children với quyền tương ứng
        $query = ExamFolder::where('parent_id', $folderId)
            ->where('type', $folderType);

        if ($isSuperAdmin) {
            $query->with('owner');
        } else {
            // Giáo viên: thấy của mình + của super admin
            $query->where(function($q) use ($profile, $superAdminIds) {
                $q->where('owner_id', $profile->id);
                if (!empty($superAdminIds)) {
                    $q->orWhereIn('owner_id', $superAdminIds);
                }
            });
            $query->with('owner');
        }

        $children = $query->orderBy('position')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'children' => $children
        ]);
    }

    /**
     * API: Lấy câu hỏi từ một đề thi
     */
    public function getExamQuestions(Request $request, $examId)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        $isTeacher = $profile->type == 1;
        
        if (!$isSuperAdmin && !$isTeacher) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập'
            ], 403);
        }

        // Lấy danh sách super admin IDs
        $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
        
        $query = Dethi::with(['parts.questions.answers'])
            ->where('id', $examId)
            ->where('type', 'dethi');
        
        // Kiểm tra quyền truy cập
        if (!$isSuperAdmin) {
            $query->where(function($q) use ($profile, $superAdminIds) {
                $q->where('created_by', $profile->id);
                if (!empty($superAdminIds)) {
                    $q->orWhereIn('created_by', $superAdminIds);
                }
            });
        }
        
        $exam = $query->first();
        
        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Đề thi không tồn tại hoặc bạn không có quyền truy cập'
            ], 404);
        }

        // Format dữ liệu câu hỏi
        $questions = [];
        foreach ($exam->parts as $part) {
            $partData = [
                'part' => $part->part,
                'part_title' => $part->part_title,
                'questions' => []
            ];
            
            foreach ($part->questions as $question) {
                $questionData = [
                    'id' => $question->id,
                    'question_no' => $question->question_no,
                    'content' => $question->content,
                    'question_type' => $question->question_type,
                    'score' => $question->score,
                    'explanation' => $question->explanation,
                    'audio' => $question->audio,
                    'images' => $question->image ? json_decode($question->image, true) : null,
                    'answers' => []
                ];
                
                foreach ($question->answers as $answer) {
                    $questionData['answers'][] = [
                        'id' => $answer->id,
                        'label' => $answer->label,
                        'content' => $answer->content,
                        'is_correct' => $answer->is_correct,
                    ];
                }
                
                $partData['questions'][] = $questionData;
            }
            
            if (!empty($partData['questions'])) {
                $questions[] = $partData;
            }
        }

        return response()->json([
            'success' => true,
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
            ],
            'questions' => $questions
        ]);
    }

    public function moveToFolder(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type != 1 && !$isSuperAdmin){
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $data = $request->validate([
            'dethi_id' => ['required','integer','exists:dethi,id'],
            'folder_id' => ['nullable','integer','exists:exam_folders,id'],
        ]);

        $examQuery = Dethi::where('id',$data['dethi_id']);
        if (!$isSuperAdmin) {
            // Giáo viên chỉ có thể move đề thi của chính họ
            $examQuery->where('created_by',$profile->id);
        }
        $exam = $examQuery->firstOrFail();
        
        // Kiểm tra thêm: giáo viên không thể move đề thi của super admin hoặc giáo viên khác
        if (!$isSuperAdmin && $exam->created_by != $profile->id) {
            abort(403, 'Bạn chỉ có thể di chuyển đề thi của chính mình.');
        }

        $folderId = $data['folder_id'] ?? null;
        if ($folderId) {
            $folderQuery = ExamFolder::where('id',$folderId)
                ->ofType(ExamFolder::TYPE_EXAM);
            if (!$isSuperAdmin) {
                // Giáo viên chỉ có thể move vào folder của chính họ
                $folderQuery->where('owner_id',$profile->id);
            }
            $folder = $folderQuery->firstOrFail();
            
            // Kiểm tra thêm: giáo viên không thể move vào folder của super admin hoặc giáo viên khác
            if (!$isSuperAdmin && $folder->owner_id != $profile->id) {
                abort(403, 'Bạn chỉ có thể di chuyển đề thi vào thư mục của chính mình.');
            }
        } else {
            $folder = null;
        }

        $exam->folder_id = $folder ? $folder->id : null;
        $exam->save();

        return redirect()->back()->with('success','Đã cập nhật thư mục cho đề thi.');
    }
    public function detail($id){

        $profile = Auth::guard("customer")->user();
        if($profile->type == 1){
            $data = Dethi::where(['id'=>$id,'created_by'=>$profile->id])->first();
            $sessions = ExamSession::with('student')->where('dethi_id',$id)->orderBy('status','ASC')->paginate(16);
        }elseif($profile->type == 3){
            $data = Dethi::where(['id'=>$id])->first();
            $sessions = ExamSession::with('student')->where('dethi_id',$id)->orderBy('status','ASC')->paginate(16);
        }
        else{
            // Kiểm tra xem $id là bill_id hay dethi_id
            $dethi_id = $id;
            
            // Thử tìm bill trước
            $billdethi = BillDethi::where('id', $id)->first();
            if($billdethi){
                if($billdethi->status == 0){
                return redirect()->back()->with('error','Bạn chưa thanh toán hoặc chưa được kiểm tra đơn hàng này');
                }else{
                    $dethi_id = $billdethi->dethi_id;
                    $data = Dethi::where(['id'=>$dethi_id])->first();
                    $sessions = ExamSession::with('student')->where(['dethi_id'=>$dethi_id,'student_id'=>$profile->id])->orderBy('status','ASC')->paginate(16);
                }
            } else {
                // Không phải bill_id, kiểm tra xem có phải đề thi được giao không
                $dethi = Dethi::find($id);
                if(!$dethi){
                    return redirect()->back()->with('error','Đề thi không tồn tại');
                }
                
                // Kiểm tra quyền truy cập (bỏ qua nếu là đề thi miễn phí công khai)
                if($dethi->access_type == 'class'){
                    $classCodes = $profile->class_codes ?? [];
                    if(empty($classCodes)){
                        return redirect()->back()->with('error','Bạn cần tham gia lớp để làm đề thi này');
                    }
                    
                    // Lấy các lớp của học sinh
                    $studentClasses = \App\models\SchoolClass::whereIn('class_code', $classCodes)->get();
                    if($studentClasses->isEmpty()){
                        return redirect()->back()->with('error','Lớp học của bạn không tồn tại');
                    }
                    
                    // Kiểm tra xem học sinh có thuộc lớp nào được giao đề thi không
                    $classIds = $studentClasses->pluck('id')->toArray();
                    $isAssigned = \App\models\dethi\DethiClass::where('dethi_id', $id)
                        ->whereIn('class_id', $classIds)
                        ->exists();
                    if(!$isAssigned){
                        return redirect()->back()->with('error','Bạn không có quyền truy cập đề thi này');
                    }
                } elseif($dethi->pricing_type == 'paid'){
                    // Nếu là đề thi thu phí, kiểm tra xem đã thanh toán chưa
                    return redirect()->back()->with('error','Bạn cần mua đề thi này để làm bài');
                }
            }
            
            $data = Dethi::where(['id'=>$dethi_id])->first();
            $sessions = ExamSession::with('student')->where(['dethi_id'=>$dethi_id,'student_id'=>$profile->id])->orderBy('status','ASC')->paginate(16);
        }
        if($data){
            return view('crm_course.dethi.detail',compact('data','sessions'));
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập đề thi này hoặc đề thi đã bị xóa');
        }
    }
    public function chamdiem($id){
        $profile = Auth::guard("customer")->user();
        $session = ExamSession::with([
            'dethi.parts.questions.answers',
            'answers'
        ])->where(['id'=>$id,'teacher_id'=>$profile->id])->first();
        if($profile->type == 3){
            $session = ExamSession::with([
                'dethi.parts.questions.answers',
                'answers'
            ])->where(['id'=>$id])->first();
        }
        if($session){
            $multipleChoiceQuestions = 0;
        $trueFalseQuestions = 0;
        $essayQuestions = 0;
        $fillInBlankQuestions = 0;
        $correctMultipleChoice = 0;
        $correctTrueFalse = 0;
        $correctFillInBlank = 0;
        $totalMultipleChoiceScore = 0;
        $totalTrueFalseScore = 0;
        $totalFillInBlankScore = 0;

        foreach ($session->dethi->parts as $part) {
            foreach ($part->questions as $question) {
                if ($question->question_type === 'multiple_choice') {
                    $multipleChoiceQuestions++;
                    // Kiểm tra câu trả lời đúng từ exam_answers
                    $studentAnswer = $session->answers->where('question_id', $question->id)->first();
                    if ($studentAnswer) {
                        if ($studentAnswer->is_correct === 1) {
                            $correctMultipleChoice++;
                            $totalMultipleChoiceScore += $studentAnswer->score ?? 1;
                        }
                    }
                } elseif ($question->question_type === 'true_false_grouped') {
                    $trueFalseQuestions++;
                    // Lấy câu trả lời của học sinh cho câu hỏi này
                    $studentAnswer = $session->answers->where('question_id', $question->id)->first();

                    if ($studentAnswer && $studentAnswer->answer_choice) {
                        // Parse JSON answer_choice của học sinh: {"5":"0","6":"1","7":"0","8":"0"}
                        $studentChoices = json_decode($studentAnswer->answer_choice, true);

                        // Lấy đáp án đúng từ bảng dethi_answers
                        $correctAnswers = $question->answers->pluck('is_correct', 'id')->toArray();

                        // Đếm số câu đúng
                        $correctCount = 0;
                        if (is_array($studentChoices)) {
                            foreach ($studentChoices as $subQuestionId => $studentChoice) {
                                // Bỏ qua câu không chọn (giá trị "2")
                                if ($studentChoice === "2") {
                                    continue;
                                }

                                $correctAnswer = $correctAnswers[$subQuestionId] ?? 0;
                                if ($studentChoice === (string)$correctAnswer) {
                                    $correctCount++;
                                }
                            }
                        }
                        //dd($correctCount);
                        // Tính điểm dựa trên cấu hình trueFalseScorePercent
                        $trueFalseScorePercent = json_decode($session->dethi->true_false_score_percent ?? '{"1":10,"2":25,"3":50,"4":100}', true);
                        if (isset($trueFalseScorePercent[$correctCount]) && $trueFalseScorePercent[$correctCount] > 0) {
                            $correctTrueFalse++;
                            $scorePercent = $trueFalseScorePercent[$correctCount];
                            $questionScore = $question->score ?? 1;
                            $totalTrueFalseScore += ($questionScore * $scorePercent / 100);
                        }
                    }
                } elseif ($question->question_type === 'fill_in_blank') {
                    $fillInBlankQuestions++;
                    // Kiểm tra câu trả lời đúng từ exam_answers
                    $studentAnswer = $session->answers->where('question_id', $question->id)->first();
                    if ($studentAnswer) {
                        if ($studentAnswer->is_correct === 1) {
                            $correctFillInBlank++;
                            $totalFillInBlankScore += $studentAnswer->score ?? 1;
                        }
                    }
                } elseif (in_array($question->question_type, ['essay', 'short_answer'])) {
                    $essayQuestions++;
                }
            }
        }
        return view('crm_course.dethi.chamdiem', [
            'session' => $session,
            'multiple_choice_questions' => $multipleChoiceQuestions, //số câu trắc nghiệm
            'true_false_questions' => $trueFalseQuestions, //số câu đúng/sai nhóm
            'essay_questions' => $essayQuestions, //số câu tự luận
            'fill_in_blank_questions' => $fillInBlankQuestions, //số câu điền vào chỗ trống
            'correct_multiple_choice' => $correctMultipleChoice, //số câu trắc nghiệm đúng
            'correct_true_false' => $correctTrueFalse, //số câu đúng/sai nhóm đúng
            'correct_fill_in_blank' => $correctFillInBlank, //số câu điền vào chỗ trống đúng
            'total_multiple_choice_score' => $totalMultipleChoiceScore, //điểm tổng câu trắc nghiệm
            'total_true_false_score' => $totalTrueFalseScore, //điểm tổng câu đúng/sai nhóm
            'total_fill_in_blank_score' => $totalFillInBlankScore //điểm tổng câu điền vào chỗ trống
        ]);
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập đề thi nàys');
        }
        // Tính toán thống kê

    }

    /**
     * Trang công khai hiển thị bảng điểm (dùng cho chia sẻ)
     */
    public function publicScoreTable($dethiId)
    {
        $dethi = Dethi::findOrFail($dethiId);
        $sessions = ExamSession::with('student')
            ->where('dethi_id', $dethiId)
            ->whereNotNull('finished_at')
            ->get();

        // Sắp xếp: điểm giảm dần, nếu bằng nhau thì thời gian tăng dần, rồi theo finished_at
        $sorted = $sessions->sort(function($a, $b) {
            $scoreA = $a->total_score ?? 0;
            $scoreB = $b->total_score ?? 0;
            if ($scoreA != $scoreB) return $scoreB <=> $scoreA;
            $timeA = $a->actual_time ?? 0;
            $timeB = $b->actual_time ?? 0;
            if ($timeA != $timeB) return $timeA <=> $timeB;
            return ($a->finished_at ?? now()) <=> ($b->finished_at ?? now());
        })->values();

        $results = $sorted->take(100)->values()->map(function($session, $index) {
            return [
                'rank' => $index + 1,
                'student_name' => $session->student->name ?? 'Học sinh',
                'score' => $session->total_score ?? 0,
                'max_score' => $session->max_score ?? 10,
                'time_minutes' => $session->actual_time ?? 0,
                'finished_at' => $session->finished_at ? $session->finished_at->format('d/m/Y H:i') : '',
            ];
        });

        return view('crm_course.dethi.public-score-table', [
            'dethi' => $dethi,
            'results' => $results,
            'totalStudents' => $results->count(),
        ]);
    }
    public function edit($id)
    {
        $profile = Auth::guard("customer")->user();
        // 1. Lấy đề thi
        if($profile->type == 1){
            $dethi = Dethi::where(['id' => $id, 'created_by' => $profile->id])->first();
        }elseif($profile->type == 3){
            $dethi = Dethi::where(['id' => $id])->first();
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập đề thi này');
        }
        if (!$dethi) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập đề thi này');
        }

        // 2. Lấy các phần của đề thi
        $parts = DethiPart::where('dethi_id', $dethi->id)->get();

        // 3. Lấy câu hỏi và đáp án cho từng phần
        $partsData = [];
        foreach ($parts as $index => $part) {
            $questions = DethiQuestion::where('dethi_part_id', $part->id)
                ->orderBy('question_no')
                ->get();

            $questionsData = [];
            foreach ($questions as $question) {
                $answers = DethiAnswer::where('dethi_question_id', $question->id)
                    ->orderBy('label')
                    ->get();

                $answersData = [];
                foreach ($answers as $answer) {
                    $answersData[] = [
                        'id' => $answer->id,
                        'label' => $answer->label,
                        'content' => $answer->content,
                        'is_correct' => $answer->is_correct,
                        'order' => $answer->order,
                    ];
                }

                // Xác định đáp án đúng cho multiple_choice
                $correct_answer = null;
                if ($question->question_type === 'multiple_choice') {
                    foreach ($answersData as $ans) {
                        if (!empty($ans['is_correct'])) {
                            $correct_answer = $ans['label'];
                            break;
                        }
                    }
                }

                $questionsData[] = [
                    'id' => $question->id,
                    'question_no' => $question->question_no,
                    'content' => $question->content,
                    'question_type' => $question->question_type,
                    'score' => $question->score,
                    'audio' => $question->audio,
                    'explanation' => $question->explanation,
                    'image' => $question->image,
                    'answers' => $answersData,
                    'correct_answer' => $correct_answer,
                ];
            }

            $partsData[] = [
                'id' => $part->id,
                'part' => 'PHẦN ' . ($index + 1),
                'part_title' => $part->part_title,
                'questions' => $questionsData,
            ];
        }
        // 4. Export ra text chuẩn để truyền vào rawContent
        $rawContent = $this->exportExamToText($partsData);

        // 5. Lấy danh sách khối học (grade)
        $grade = CategoryMain::where('status',1)->get(['id','name']);
        
        // Lấy danh sách lớp đã chọn (nếu có)
        $selectedClasses = \App\models\dethi\DethiClass::where('dethi_id', $dethi->id)
            ->pluck('class_id')
            ->toArray();
        
        $examConfig = [
            'id' => $dethi->id,
            'title' => $dethi->title,
            'grade' => $dethi->grade,
            'subject' => $dethi->subject,
            'cate_type_id' => $dethi->cate_type_id,
            'pricing_type' => $dethi->pricing_type,
            'price' => $dethi->price,
            'description' => $dethi->description,
            'time' => $dethi->time,
            // Thêm các trường kiểm soát truy cập
            'xemdapan' => (bool)$dethi->xemdapan,
            'access_type' => $dethi->access_type ?? 'all',
            'start_time' => $dethi->start_time ? \Carbon\Carbon::parse($dethi->start_time)->format('Y-m-d\TH:i') : '',
            'end_time' => $dethi->end_time ? \Carbon\Carbon::parse($dethi->end_time)->format('Y-m-d\TH:i') : '',
            'classes' => $selectedClasses,
        ];
        $trueFalseScorePercent = json_decode($dethi->true_false_score_percent,true);
        $partScores = json_decode($dethi->part_scores,true);
        // 6. Trả về view preview-file-upload để chỉnh sửa lại
        return view('crm_course.dethi.edit', [
            'questions' => $partsData,
            'rawContent' => $rawContent,
            'grade' => $grade,
            'examConfig' => $examConfig,
            'trueFalseScorePercent' => $trueFalseScorePercent,
            'partScores' => $partScores,
        ]);
    }

    // Hàm export dữ liệu đề thi thành text chuẩn
    private function exportExamToText($parts)
    {
        $lines = [];
        foreach ($parts as $part) {
            $lines[] = $part['part'] . '. ' . $part['part_title'];
            foreach ($part['questions'] as $q) {
                $lines[] = 'Câu ' . $q['question_no'] . '. ' . $q['content'];
                if (!empty($q['answers'])) {
                    foreach ($q['answers'] as $ans) {
                        if ($q['question_type'] === 'multiple_choice') {
                            $prefix = (isset($q['correct_answer']) && strtoupper($ans['label']) == strtoupper($q['correct_answer'])) ? '*' : '';
                            $lines[] = $prefix . $ans['label'] . '. ' . $ans['content'];
                        } elseif ($q['question_type'] === 'true_false_grouped') {
                            $prefix = (!empty($ans['is_correct'])) ? '*' : '';
                            $lines[] = $prefix . $ans['label'] . ') ' . $ans['content'];
                        } elseif ($q['question_type'] === 'fill_in_blank') {
                            $lines[] = 'Answer: ' . $ans['content'];
                        }
                    }
                }
                if (!empty($q['explanation'])) {
                    $lines[] = 'Lời giải: ' . $q['explanation'];
                }
                $lines[] = '';
            }
            $lines[] = '';
        }
        return implode(PHP_EOL, $lines);
    }
    public function tutaodethi(Request $request){
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
            $grade = CategoryMain::where('status',1)->get(['id','name']);
            $questions = [];
            $rawContent = '';
            $type = $request->input('type', 'dethi');
            $folderType = $type === 'baitap' ? ExamFolder::TYPE_EXERCISE : ExamFolder::TYPE_EXAM;
            $folderId = $this->resolveFolderIdForOwner($request->query('folder_id'), $profile->id, $folderType);

            return view('crm_course.dethi.tutaodethi', [
                'questions' => $questions,
                'rawContent' => $rawContent,
                'grade' => $grade,
                'folderId' => $folderId,
                'type' => $type,
            ]);
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy tạo đề thi');
        }

    }
    public function uploadFile(Request $request){
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
            $type = $request->input('type', 'dethi');
            $folderId = $this->resolveFolderIdForOwner($request->query('folder_id'), $profile->id, ExamFolder::TYPE_EXAM);
            return view('crm_course.dethi.upload-file',compact('type','folderId'));
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy tạo đề thi');
        }
    }
    public function PostuploadFile(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
        $type = $request->input('type');
        // 1️⃣ Kiểm tra file hợp lệ
        if (!$request->hasFile('file')) {
            return back()->withErrors(['file' => 'Vui lòng chọn tệp Word.']);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return back()->withErrors(['file' => 'Tệp tải lên không hợp lệ.']);
        }

        if ($file->getClientOriginalExtension() !== 'docx') {
            return back()->withErrors(['file' => 'Chỉ chấp nhận file Word (.docx)']);
        }

        try {
            // 2️⃣ Dùng Pandoc để chuyển file Word sang HTML
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('word_uploads', $filename);

            $inputPath = storage_path('app/' . $path);
            $outputPath = storage_path('app/converted/' . pathinfo($filename, PATHINFO_FILENAME) . '.html');
            $mediaDir = storage_path('app/converted/media');

            if (!file_exists(storage_path('app/converted'))) {
                mkdir(storage_path('app/converted'), 0777, true);
            }

            if (!file_exists($mediaDir)) {
                mkdir($mediaDir, 0777, true);
            }

            // Thử extract ảnh với nhiều cách
            $extractDir = storage_path('app/converted');
            
            $process = new Process([
                'pandoc',
                $inputPath,
                '-f', 'docx',
                '-t', 'html',
                '--mathjax',  // Sử dụng MathJax thay vì tự chuyển đổi LaTeX
                '--wrap=none',  // Không wrap text
                '--no-highlight',  // Tắt highlight syntax
                '--preserve-tabs',  // Giữ lại tabs (có thể giúp preserve formatting)
                '--extract-media=' . $extractDir,  // Extract ảnh từ Word (dùng = thay vì space)
                '-o', $outputPath
            ]);
            
            $process->run();

            if (!$process->isSuccessful()) {
                return back()->withErrors(['file' => 'Lỗi Pandoc: ' . $process->getErrorOutput()]);
            }


            $fullHtml = file_get_contents($outputPath);
            // Xoá file tạm
            @unlink($inputPath);
            @unlink($outputPath); // Bạn có thể giữ file HTML để sử dụng sau này

            // Đảm bảo HTML có meta charset UTF-8
            if (strpos($fullHtml, 'charset=') === false) {
                $fullHtml = preg_replace(
                    '/<head>/i',
                    '<head><meta charset="UTF-8">',
                    $fullHtml,
                    1
                );
            }

            // 3️⃣ Làm sạch HTML: chỉ lấy nội dung trong <body>
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $fullHtml);
            $body = $dom->getElementsByTagName('body')->item(0);

            $cleanHtml = '';
            foreach ($body->childNodes as $child) {
                $cleanHtml .= $dom->saveHTML($child);
            }
            
            // Xử lý HTML để merge các <p> liên tiếp của cùng một câu hỏi
            $cleanHtml = $this->mergeConsecutiveParagraphsInHtml($cleanHtml);
            
            // 4️⃣ Xử lý ảnh từ HTML và upload lên server
            $imageMapping = $this->extractAndUploadImages($cleanHtml, storage_path('app/converted'));
            
            // 5️⃣ Convert HTML img tags thành markdown syntax và build rawContent
            $rawContent = $this->convertHtmlToRawWithMarkdownImages($cleanHtml, $imageMapping);

            // 6️⃣ Parse bằng text parser để chuẩn hóa và gắn ảnh vào câu hỏi
            $parsedQuestions = $this->parseTextContent($rawContent, null, $imageMapping);
            
            // 7️⃣ Map ảnh vào từng câu hỏi dựa trên vị trí (để đảm bảo images array được set cho backward compatibility)
            $parsedQuestions = $this->mapImagesToQuestions($parsedQuestions, $imageMapping, $cleanHtml);
            
            $grade = CategoryMain::where('status',1)->get(['id','name']);

            // 8️⃣ Xóa các file tạm
            @unlink($inputPath);
            @unlink($outputPath);
            // Xóa thư mục media sau khi đã upload
            $this->deleteDirectory($mediaDir);

            // 9️⃣ Trả về view preview
            $type = $request->input('type', 'dethi');
            $folderType = $type === 'baitap' ? ExamFolder::TYPE_EXERCISE : ExamFolder::TYPE_EXAM;
            $folderId = $this->resolveFolderIdForOwner($request->input('folder_id'), $profile->id, $folderType);

            return view('crm_course.dethi.preview-file-upload', [
                'questions' => $parsedQuestions,
                'rawContent' => $rawContent,
                'grade' => $grade,
                'type' => $type,
                'folderId' => $folderId,
            ]);
            } catch (\Exception $e) {
                return back()->withErrors(['file' => 'Lỗi xử lý file: ' . $e->getMessage()]);
            }
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy tạo đề thi');
        }
    }
    private function parseHtmlContent($html)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//body//*');

        $sections = [];
        $currentPart = null;
        $currentPartTitle = null;
        $currentQuestion = null;
        $currentType = null;
        $pendingAnswers = [];
        $skipAppending = false;
        $parsedFirstQuestionFromPart = false;
        $collectingExplanation = false;
        $explanationLines = [];
            $hasParts = false; // Flag để kiểm tra có phần hay không
            $answersParsedInQuestionLine = false; // NEW: Flag to avoid duplicate answers

        foreach ($nodes as $node) {
            $text = trim(preg_replace('/\s+/', ' ', $node->textContent));
            if ($text === '') continue;

                // Kiểm tra xem có phần hay không
                if (preg_match('/^PHẦN\s+([IVXLCDM1-9]+)\./iu', $text)) {
                    $hasParts = true;
                }

            if (preg_match('/^Lời giải[:：]?/ui', $text)) {
                $collectingExplanation = true;
                $explanationLines = [];
                $after = trim(preg_replace('/^Lời giải[:：]?/ui', '', $text));
                if ($after !== '') $explanationLines[] = $after;
                continue;
            }
            if ($collectingExplanation && (preg_match('/^PHẦN\s+([IVXLCDM1-9]+)\./iu', $text) || preg_match('/^Câu\s+\d+\./ui', $text))) {
                $collectingExplanation = false;
                if ($currentQuestion && count($explanationLines)) {
                    $currentQuestion['explanation'] = implode("\n", $explanationLines);
                }
            }
            if ($collectingExplanation) {
                if (trim($text) !== '') $explanationLines[] = $text;
                continue;
            }

            // ==== BẮT PHẦN ====
            if (preg_match('/^PHẦN\s+([IVXLCDM1-9]+)\.(.*)$/iu', $text, $m)) {
                if ($currentQuestion && $currentPart) {
                    if ($pendingAnswers) $currentQuestion['answers'] = $pendingAnswers;
                    if (!$currentType) $currentType = 'short_answer';
                    $currentQuestion['question_type'] = $currentType;
                    if ($collectingExplanation && count($explanationLines)) {
                        $currentQuestion['explanation'] = implode("\n", $explanationLines);
                        $collectingExplanation = false;
                        $explanationLines = [];
                    }
                    $sections[$currentPart]['questions'][] = $currentQuestion;
                }

                $currentQuestion = null;
                $pendingAnswers = [];
                $currentType = null;
                $skipAppending = false;
                $parsedFirstQuestionFromPart = false;
                    $answersParsedInQuestionLine = false;

                $roman = strtoupper($m[1]);
                $currentPart = 'PHẦN ' . $roman;
                $rest = trim($m[2]);

                if (preg_match('/^(.*?)\s+(Câu\s+\d+\..*)$/ui', $rest, $m2)) {
                    $currentPartTitle = trim($m2[1]);
                    $rest = $m2[2];
                } else {
                    $currentPartTitle = $rest;
                    $rest = null;
                }

                $sections[$currentPart] = [
                    'part' => $currentPart,
                    'part_title' => $currentPartTitle,
                    'questions' => []
                ];

                if ($rest && preg_match('/^Câu\s+(\d+)\.\s*(.+)$/ui', $rest, $m3)) {
                    $contentRaw = trim($m3[2]);
                    // 🛠 Xoá "Câu X." nếu còn sót
                    $contentRaw = preg_replace('/^Câu\s+\d+\.\s*/ui', '', $contentRaw);

                    $questionContent = preg_split('/A\.\s+/', $contentRaw)[0];

                    $currentQuestion = [
                        'question_no' => (int)$m3[1],
                        'content' => trim($questionContent),
                        'answers' => [],
                        'correct_answer' => null,
                        'score' => null,
                        'audio' => null
                    ];

                    if (preg_match_all('/([A-D])\.\s*([^A-D]+)(?=\s+[A-D]\.|$)/u', $contentRaw, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $pendingAnswers[] = [
                                'label' => $match[1],
                                'content' => trim($match[2])
                            ];
                        }
                        $currentType = 'multiple_choice';
                            $answersParsedInQuestionLine = true;
                        } else {
                            $answersParsedInQuestionLine = false;
                    }

                    $parsedFirstQuestionFromPart = true;
                    $skipAppending = true;
                }
                continue;
            }

            // ==== CÂU HỎI ====
            if (preg_match('/^Câu\s+(\d+)\.\s*(.+)$/ui', $text, $m)) {
                    // Nếu không có phần, tạo phần mặc định
                    if (!$hasParts && !$currentPart) {
                        $currentPart = 'PHẦN I';
                        $currentPartTitle = 'Câu hỏi chung';
                        $sections[$currentPart] = [
                            'part' => $currentPart,
                            'part_title' => $currentPartTitle,
                            'questions' => []
                        ];
                    }

                if ($parsedFirstQuestionFromPart && isset($currentQuestion['question_no']) && (int)$m[1] === $currentQuestion['question_no']) {
                    $parsedFirstQuestionFromPart = false;
                    continue; // Bỏ qua nếu đã xử lý trong dòng PHẦN
                }

                    if ($currentQuestion && $currentPart) {
                    if ($pendingAnswers) $currentQuestion['answers'] = $pendingAnswers;
                    if (!$currentType) $currentType = 'short_answer';
                    $currentQuestion['question_type'] = $currentType;
                    if ($collectingExplanation && count($explanationLines)) {
                        $currentQuestion['explanation'] = implode("\n", $explanationLines);
                        $collectingExplanation = false;
                        $explanationLines = [];
                    }
                    $sections[$currentPart]['questions'][] = $currentQuestion;
                }

                $pendingAnswers = [];
                $currentType = null;

                $contentRaw = trim($m[2]);
                // 🛠 Xoá "Câu X." nếu còn sót
                $contentRaw = preg_replace('/^Câu\s+\d+\.\s*/ui', '', $contentRaw);

                $questionContent = preg_split('/A\.\s+/', $contentRaw)[0];

                $currentQuestion = [
                    'question_no' => (int)$m[1],
                    'content' => trim($questionContent),
                    'answers' => [],
                    'correct_answer' => null,
                    'score' => null,
                    'audio' => null
                ];

                // if (preg_match_all('/([A-D])\.\s*([^A-D]+)(?=\s+[A-D]\.|$)/u', $contentRaw, $matches, PREG_SET_ORDER)) {
                //     foreach ($matches as $match) {
                //         $pendingAnswers[] = [
                //             'label' => $match[1],
                //             'content' => trim($match[2])
                //         ];
                //     }
                //     $currentType = 'multiple_choice';
                // }
                continue;
            }

            if ($skipAppending) {
                $skipAppending = false;
                continue;
            }

            // ==== ĐÁP ÁN Multiple Choice (1 dòng) ====
                if ($answersParsedInQuestionLine && preg_match('/^[*]?[A-D]\./u', $text)) {
                    // Nếu đã parse đáp án ở dòng câu hỏi, và node này là đáp án, thì bỏ qua
                    continue;
                }
            if (preg_match_all('/([*]?)([A-D])\.\s*([^A-D]+)(?=\s+[A-D]\.|$)/u', $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $isCorrect = $match[1] === '*';
                    $label = $match[2];
                    $content = trim($match[3]);
                    if ($isCorrect && $currentQuestion) {
                        $currentQuestion['correct_answer'] = $label;
                    }
                    $pendingAnswers[] = [
                        'label' => $label,
                        'content' => $content
                    ];
                }
                $currentType = 'multiple_choice';
                continue;
            }

            // ==== ĐÁP ÁN Multiple Choice (nhiều dòng) ====
            if (preg_match('/^([*]?)([A-D])\.\s*(.+)$/u', $text, $m)) {
                $isCorrect = $m[1] === '*';
                $label = $m[2];
                $content = $m[3];
                if ($isCorrect && $currentQuestion) {
                    $currentQuestion['correct_answer'] = $label;
                }
                $pendingAnswers[] = [
                    'label' => $label,
                    'content' => $content
                ];
                $currentType = 'multiple_choice';
                continue;
            }

            // ==== ĐÁP ÁN đúng/sai dạng nhóm ====
            if (preg_match('/^([*]?)([a-d])\)\s*(.+)$/u', $text, $m)) {
                $isCorrect = $m[1] === '*';
                $label = $m[2];
                $content = $m[3];

                $pendingAnswers[] = [
                    'label' => $label,
                    'content' => $content,
                    'is_correct' => $isCorrect ? 1 : 0
                ];
                $currentType = 'true_false_grouped';
                $lastAnswerIndex = count($pendingAnswers) - 1;
                continue;
            }

            // ==== Chọn đáp án đúng ====
            if (preg_match('/Chọn\s*\|\s*([A-Da-d])/', $text, $m)) {
                if ($currentQuestion) {
                    $currentQuestion['correct_answer'] = strtoupper($m[1]);
                }
                continue;
            }

            // ==== Nội dung bổ sung ====
            // Append nội dung tiếp theo vào câu hỏi, giữ lại xuống dòng
            if ($currentQuestion && !$currentType) {
                // Nếu content đã có nội dung, thêm xuống dòng trước khi append
                if (!empty(trim($currentQuestion['content']))) {
                    $currentQuestion['content'] .= "\n" . $text;
                } else {
                    $currentQuestion['content'] .= $text;
                }
            }
        }

        // ==== Lưu câu cuối ====
        if ($currentQuestion && $currentPart) {
            if ($pendingAnswers) {
                $currentQuestion['answers'] = $pendingAnswers;
            }
            if (!$currentType) $currentType = 'short_answer';
            $currentQuestion['question_type'] = $currentType;
            if ($collectingExplanation && count($explanationLines)) {
                $currentQuestion['explanation'] = implode("\n", $explanationLines);
                $collectingExplanation = false;
                $explanationLines = [];
            }
            $sections[$currentPart]['questions'][] = $currentQuestion;
        }

        // ==== Gán question_type cho từng phần ====
        // foreach ($sections as &$section) {
        //     $first = $section['questions'][0] ?? null;
        //     if ($first) {
        //         $section['question_type'] = $first['question_type'];
        //     }
        // }
        return array_values($sections);
    }

    private function parseTextContent($text, $originParts = null, $imageMapping = null)
    {
        // Giải mã các HTML entities về ký tự gốc
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Nếu là HTML, chuyển <br>, </p>, </div>, </li>, </h1-6> thành \n, rồi loại bỏ thẻ còn lại
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = preg_replace('/<\/(p|div|li|h[1-6])>/i', "\n", $text);
        $text = strip_tags($text);

        // Tách text thành các dòng và loại bỏ dòng trống
        $lines = array_filter(explode("\n", $text), function($line) {
            return trim($line) !== '';
        });

        $sections = [];
        $currentPart = null;
        $currentPartTitle = null;
        $currentQuestion = null;
        $currentType = null;
        $pendingAnswers = [];
        $skipAppending = false;
        $parsedFirstQuestionFromPart = false;
        $collectingExplanation = false;
        $explanationLines = [];
        $hasParts = false; // Flag để kiểm tra có phần hay không

        // Kiểm tra xem có phần hay không
        foreach ($lines as $line) {
            $lineText = trim(preg_replace('/\s+/', ' ', $line));
            if (preg_match('/^PHẦN\s+([IVXLCDM1-9]+)\./iu', $lineText)) {
                $hasParts = true;
                break;
            }
        }

        foreach ($lines as $line) {
            $text = trim(preg_replace('/\s+/', ' ', $line));
            if ($text === '') continue;

            if (preg_match('/^Lời giải[:：]?/ui', $text)) {
                $collectingExplanation = true;
                $explanationLines = [];
                $after = trim(preg_replace('/^Lời giải[:：]?/ui', '', $text));
                if ($after !== '') $explanationLines[] = $after;
                continue;
            }
            if ($collectingExplanation && (preg_match('/^PHẦN\s+([IVXLCDM1-9]+)\./iu', $text) || preg_match('/^Câu\s+\d+\./ui', $text))) {
                $collectingExplanation = false;
                if ($currentQuestion && count($explanationLines)) {
                    $currentQuestion['explanation'] = implode("\n", $explanationLines);
                }
            }
            if ($collectingExplanation) {
                if (trim($text) !== '') $explanationLines[] = $text;
                continue;
            }

            // ==== BẮT PHẦN ====
            if (preg_match('/^PHẦN\s+([IVXLCDM1-9]+)\.(.*)$/iu', $text, $m)) {
                if ($currentQuestion && $currentPart) {
                    if ($pendingAnswers) $currentQuestion['answers'] = $pendingAnswers;
                    if (!$currentType) $currentType = 'short_answer';
                    $currentQuestion['question_type'] = $currentType;
                    if ($collectingExplanation && count($explanationLines)) {
                        $currentQuestion['explanation'] = implode("\n", $explanationLines);
                        $collectingExplanation = false;
                        $explanationLines = [];
                    }
                    $sections[$currentPart]['questions'][] = $currentQuestion;
                }

                $currentQuestion = null;
                $pendingAnswers = [];
                $currentType = null;
                $skipAppending = false;
                $parsedFirstQuestionFromPart = false;

                $roman = strtoupper($m[1]);
                $currentPart = 'PHẦN ' . $roman;
                $rest = trim($m[2]);

                if (preg_match('/^(.*?)\s+(Câu\s+\d+\..*)$/ui', $rest, $m2)) {
                    $currentPartTitle = trim($m2[1]);
                    $rest = $m2[2];
                } else {
                    $currentPartTitle = $rest;
                    $rest = null;
                }

                $sections[$currentPart] = [
                    'part' => $currentPart,
                    'part_title' => $currentPartTitle,
                    'questions' => []
                ];

                if ($rest && preg_match('/^Câu\s+(\d+)\.\s*(.+)$/ui', $rest, $m3)) {
                    $contentRaw = trim($m3[2]);
                    $contentRaw = preg_replace('/^Câu\s+\d+\.\s*/ui', '', $contentRaw);
                    $questionContent = preg_split('/A\.\s+/', $contentRaw)[0];
                    $questionContent = preg_replace('/Câu\s*\d+\./ui', '', $questionContent);
                    $questionContent = trim($questionContent);
                    
                    // Extract markdown images từ content (hỗ trợ cả [img:key] và ![alt](url))
                    // Không loại bỏ syntax khỏi content - giữ nguyên để render đúng vị trí
                    $images = $this->extractMarkdownImagesFromContent($questionContent, $this->currentImageMapping ?? null);
                    
                    $currentQuestion = [
                        'question_no' => (int)$m3[1],
                        'content' => $questionContent,
                        'answers' => [],
                        'correct_answer' => null,
                        'score' => null,
                        'audio' => null,
                        'images' => $images
                    ];
                    if (preg_match_all('/([A-D])\.\s*([^A-D]+)(?=\s+[A-D]\.|$)/u', $contentRaw, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $pendingAnswers[] = [
                                'label' => $match[1],
                                'content' => trim($match[2])
                            ];
                        }
                        $currentType = 'multiple_choice';
                    }
                    $parsedFirstQuestionFromPart = true;
                    $skipAppending = true;
                }
                continue;
            }

            // ==== CÂU HỎI ====
            if (preg_match('/^Câu\s+(\d+)\.\s*(.+)$/ui', $text, $m)) {
                // Nếu không có phần, tạo phần mặc định
                if (!$hasParts && !$currentPart) {
                    $currentPart = 'PHẦN I';
                    $currentPartTitle = 'Câu hỏi chung';
                    $sections[$currentPart] = [
                        'part' => $currentPart,
                        'part_title' => $currentPartTitle,
                        'questions' => []
                    ];
                }

                if ($parsedFirstQuestionFromPart && isset($currentQuestion['question_no']) && (int)$m[1] === $currentQuestion['question_no']) {
                    $parsedFirstQuestionFromPart = false;
                    continue; // Bỏ qua nếu đã xử lý trong dòng PHẦN
                }

                if ($currentQuestion && $currentPart) {
                    if ($pendingAnswers) $currentQuestion['answers'] = $pendingAnswers;
                    if (!$currentType) $currentType = 'short_answer';
                    $currentQuestion['question_type'] = $currentType;
                    if ($collectingExplanation && count($explanationLines)) {
                        $currentQuestion['explanation'] = implode("\n", $explanationLines);
                        $collectingExplanation = false;
                        $explanationLines = [];
                    }
                    $sections[$currentPart]['questions'][] = $currentQuestion;
                }

                $pendingAnswers = [];
                $currentType = null;

                $contentRaw = trim($m[2]);
                $contentRaw = preg_replace('/^Câu\s+\d+\.\s*/ui', '', $contentRaw);
                $questionContent = preg_split('/A\.\s+/', $contentRaw)[0];
                $questionContent = preg_replace('/Câu\s*\d+\./ui', '', $questionContent);
                $questionContent = trim($questionContent);

                // Kiểm tra xem có phải câu hỏi fill_in_blank không
                if (preg_match('/\[DT\]/i', $questionContent)) {
                    $currentType = 'fill_in_blank';
                    // Loại bỏ [DT] khỏi nội dung câu hỏi
                    $questionContent = preg_replace('/\[DT\]/i', '', $questionContent);
                    $questionContent = trim($questionContent);
                }

                // Extract markdown images từ content (hỗ trợ cả [img:key] và ![alt](url))
                // Không loại bỏ syntax khỏi content - giữ nguyên để render đúng vị trí
                $images = $this->extractMarkdownImagesFromContent($questionContent, $imageMapping);

                $currentQuestion = [
                    'question_no' => (int)$m[1],
                    'content' => $questionContent,
                    'answers' => [],
                    'correct_answer' => null,
                    'score' => null,
                    'audio' => null,
                    'images' => $images
                ];

                // Pattern đã được cập nhật để match content có chứa [img:key]
                if (preg_match_all('/([A-D])\.\s*((?:[^A-D]|\[img:[^\]]+\])+?)(?=\s+[A-D]\.|$)/u', $contentRaw, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $pendingAnswers[] = [
                            'label' => $match[1],
                            'content' => trim($match[2])
                        ];
                    }
                    $currentType = 'multiple_choice';
                }
                continue;
            }

            if ($skipAppending) {
                $skipAppending = false;
                continue;
            }

            // ==== ĐÁP ÁN Multiple Choice (1 dòng) ====
            // Pattern đã được cập nhật để match content có chứa [img:key] hoặc các ký tự đặc biệt
            if (preg_match_all('/([*]?)([A-D])\.\s*((?:[^A-D]|\[img:[^\]]+\])+?)(?=\s+[A-D]\.|$)/u', $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $isCorrect = $match[1] === '*';
                    $label = $match[2];
                    $content = trim($match[3]);
                    if ($isCorrect && $currentQuestion) {
                        $currentQuestion['correct_answer'] = $label;
                    }
                    $pendingAnswers[] = [
                        'label' => $label,
                        'content' => $content
                    ];
                }
                $currentType = 'multiple_choice';
                continue;
            }

            // ==== ĐÁP ÁN Multiple Choice (nhiều dòng) ====
            if (preg_match('/^([*]?)([A-D])\.\s*(.+)$/u', $text, $m)) {
                $isCorrect = $m[1] === '*';
                $label = $m[2];
                $content = $m[3];
                if ($isCorrect && $currentQuestion) {
                    $currentQuestion['correct_answer'] = $label;
                }
                $pendingAnswers[] = [
                    'label' => $label,
                    'content' => $content
                ];
                $currentType = 'multiple_choice';
                continue;
            }

            // ==== ĐÁP ÁN đúng/sai dạng nhóm ====
            if (preg_match('/^([*]?)([a-d])\)\s*(.+)$/u', $text, $m)) {
                $isCorrect = $m[1] === '*';
                $label = $m[2];
                $content = $m[3];

                $pendingAnswers[] = [
                    'label' => $label,
                    'content' => $content,
                    'is_correct' => $isCorrect ? 1 : 0
                ];
                $currentType = 'true_false_grouped';
                $lastAnswerIndex = count($pendingAnswers) - 1;
                continue;
            }

            // ==== ĐÁP ÁN fill_in_blank ====
            if ($currentType === 'fill_in_blank' && !empty(trim($text)) && !preg_match('/^[A-D]\./u', $text) && !preg_match('/^[a-d]\)/u', $text)) {
                // Nếu đang xử lý câu hỏi fill_in_blank và dòng này không phải là đáp án multiple choice hoặc true/false
                // thì coi như đây là đáp án fill_in_blank

                $answerContent = trim($text);
                // Debug: Log trước khi xử lý
                \Log::info("Fill in blank answer before processing: " . $answerContent);

                // Nếu có prefix "Answer:" hoặc "answer:" thì loại bỏ nó
                if (stripos($answerContent, 'Answer:') !== false) {
                    // Sử dụng stripos để không phân biệt hoa thường
                    $answerContent = trim(substr($answerContent, stripos($answerContent, 'Answer:') + strlen('Answer:')));
                    \Log::info("Fill in blank answer after processing (Answer): " . $answerContent);
                } else {
                    \Log::info("No 'Answer:' prefix found in: " . $answerContent);
                }
                $pendingAnswers[] = [
                    'label' => 'answer',
                    'content' => $answerContent,
                    'is_correct' => 1
                ];
                $currentQuestion['correct_answer'] = $answerContent;
                continue;
            }

            // Nếu là dòng tiếp theo, không phải đáp án mới, nối vào đáp án trước đó
            if ($currentType === 'true_false_grouped' && isset($lastAnswerIndex)) {
                // Không phải dòng bắt đầu bằng a) [..]
                if (!preg_match('/^([a-d])\)\s*(.+)$/u', $text)) {
                    $pendingAnswers[$lastAnswerIndex]['content'] .= ' ' . $text;
                    continue;
                }
            }

            // ==== Chọn đáp án đúng ====
            if (preg_match('/Chọn\s*\|\s*([A-Da-d])/', $text, $m)) {
                if ($currentQuestion) {
                    $currentQuestion['correct_answer'] = strtoupper($m[1]);
                }
                continue;
            }

            // ==== Nội dung bổ sung ====
            // Append nội dung tiếp theo vào câu hỏi, giữ lại xuống dòng
            if ($currentQuestion && !$currentType) {
                // Nếu content đã có nội dung, thêm xuống dòng trước khi append
                if (!empty(trim($currentQuestion['content']))) {
                    $currentQuestion['content'] .= "\n" . $text;
                } else {
                    $currentQuestion['content'] .= $text;
                }
            }
        }

        // ==== Lưu câu cuối ====
        if ($currentQuestion && $currentPart) {
            if ($pendingAnswers) {
                $currentQuestion['answers'] = $pendingAnswers;
            }
            if (!$currentType) $currentType = 'short_answer';
            $currentQuestion['question_type'] = $currentType;
            if ($collectingExplanation && count($explanationLines)) {
                $currentQuestion['explanation'] = implode("\n", $explanationLines);
                $collectingExplanation = false;
                $explanationLines = [];
            }
            $sections[$currentPart]['questions'][] = $currentQuestion;
        }

        // Nếu có $originParts (chế độ edit), map id vào từng part/question

        if ($originParts) {
            foreach ($sections as $i => &$section) {
                // Tìm part gốc theo điều kiện cũ
                $originPart = null;
                foreach ($originParts as $op) {
                    if (
                        mb_strtoupper(trim($op['part'])) === mb_strtoupper(trim($section['part'])) &&
                        mb_strtolower(trim($op['part_title'])) === mb_strtolower(trim($section['part_title']))
                    ) {
                        $originPart = $op;
                        break;
                    }
                }
                // Nếu không tìm thấy theo tên, thử map theo vị trí
                if (!$originPart && isset($originParts[$i])) {
                    $originPart = $originParts[$i];
                }
                if ($originPart) {
                    $section['id'] = $originPart['id'] ?? null;
                    // Map id cho question
                    foreach ($section['questions'] as $qi => &$q) {
                        foreach ($originPart['questions'] as $oq) {
                            if (
                                $oq['question_no'] == $q['question_no'] &&
                                mb_strtolower(trim($oq['content'])) == mb_strtolower(trim($q['content']))
                            ) {
                                $q['id'] = $oq['id'] ?? null;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return array_values($sections);
    }

    private function parseTextContentForSingleQuestion($text, $answerIds = [])
    {
        // dd($answerIds);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/<br\s*\/?\>/i', "\n", $text);
        $text = preg_replace('/<\/p>|<\/div>|<\/li>|<\/h[1-6]>/i', "\n", $text);
        $text = strip_tags($text);
        $lines = array_filter(explode("\n", $text), function($line) {
            return trim($line) !== '';
        });
        $question = [
            'question_no' => null,
            'content' => '',
            'answers' => [],
            'correct_answer' => null,
            'score' => null,
            'audio' => null,
            'explanation' => null
        ];
        $collectingExplanation = false;
        $explanationLines = [];
        $answerIndex = 0;
        foreach ($lines as $line) {
            $text = trim($line);
            if ($text === '') continue;
            // Lời giải
            if (preg_match('/^Lời giải[:：]?/ui', $text)) {
                $collectingExplanation = true;
                $after = trim(preg_replace('/^Lời giải[:：]?/ui', '', $text));
                if ($after !== '') $explanationLines[] = $after;
                continue;
            }
            if ($collectingExplanation) {
                $explanationLines[] = $text;
                continue;
            }
            // Câu hỏi
            if (preg_match('/^Câu\s+(\d+)\.\s*(.+)$/ui', $text, $m)) {
                $question['question_no'] = (int)$m[1];
                $questionContent = trim($m[2]);

                // Kiểm tra xem có phải câu hỏi fill_in_blank không
                if (preg_match('/\[DT\]/i', $questionContent)) {
                    $question['question_type'] = 'fill_in_blank';
                    // Loại bỏ [DT] khỏi nội dung câu hỏi
                    $questionContent = preg_replace('/\[DT\]/i', '', $questionContent);
                    $question['content'] = trim($questionContent);
                } else {
                    $question['content'] = $questionContent;
                }
                continue;
            }
            // Đáp án trắc nghiệm
            if (preg_match('/^([*]?)([A-D])\.\s*(.+)$/u', $text, $m)) {
                $isCorrect = $m[1] === '*';
                $label = $m[2];
                $content = $m[3];
                $id = $answerIds[$answerIndex] ?? null; // lấy id theo thứ tự
                $question['answers'][] = [
                    'id' => $id,
                    'label' => $label,
                    'content' => $content,
                    'is_correct' => $isCorrect ? 1 : 0
                ];
                if ($isCorrect) {
                    $question['correct_answer'] = $label;
                }
                $question['question_type'] = 'multiple_choice';
                $answerIndex++;
                continue;
            }
            // Đáp án đúng/sai nhóm
            if (preg_match('/^([*]?)([a-d])\)\s*(.+)$/u', $text, $m)) {
                $isCorrect = $m[1] === '*';
                $label = $m[2];
                $content = $m[3];
                $id = $answerIds[$answerIndex] ?? null;
                $question['answers'][] = [
                    'id' => $id,
                    'label' => $label,
                    'content' => $content,
                    'is_correct' => $isCorrect ? 1 : 0
                ];
                $question['question_type'] = 'true_false_grouped';
                $answerIndex++; // Đảm bảo id được lấy đúng thứ tự
                continue;
            }
            // Đáp án fill_in_blank
            if ($question['question_type'] === 'fill_in_blank' && !empty(trim($text)) && !preg_match('/^[A-D]\./u', $text) && !preg_match('/^[a-d]\)/u', $text)) {
                $id = $answerIds[$answerIndex] ?? null;
                $answerContent = trim($text);

                // Nếu có prefix "Answer:" hoặc "answer:" thì loại bỏ nó
                if (stripos($answerContent, 'Answer:') !== false) {
                    // Sử dụng stripos để không phân biệt hoa thường
                    $answerContent = trim(substr($answerContent, stripos($answerContent, 'Answer:') + strlen('Answer:')));
                    \Log::info("Fill in blank answer after processing (Answer): " . $answerContent);
                } else {
                    \Log::info("No 'Answer:' prefix found in: " . $answerContent);
                }

                $question['answers'][] = [
                    'id' => $id,
                    'label' => 'answer',
                    'content' => $answerContent,
                    'is_correct' => 1
                ];
                $question['correct_answer'] = $answerContent;
                $answerIndex++;
                continue;
            }
        }
        if (count($explanationLines)) {
            $question['explanation'] = implode("\n", $explanationLines);
        }
        if (empty($question['question_type'])) {
            $question['question_type'] = 'short_answer';
        }
        return $question;
    }

    public function exportExamToTextQuestion($question)
    {
        // Lấy đáp án của câu hỏi
        $answers = \App\models\dethi\DethiAnswer::where('dethi_question_id', $question->id)
            ->orderBy('label')
            ->get();

        $lines = [];
        // Dòng câu hỏi
        if ($question->question_type === 'fill_in_blank') {
            $lines[] = 'Câu ' . $question->question_no . '. [DT] ' . $question->content;
        } else {
            $lines[] = 'Câu ' . $question->question_no . '. ' . $question->content;
        }

        // Đáp án
        if ($answers && count($answers)) {
            foreach ($answers as $ans) {
                if ($question->question_type === 'multiple_choice') {
                    $prefix = (!empty($ans->is_correct)) ? '*' : '';
                    $lines[] = $prefix . $ans->label . '. ' . $ans->content;
                } elseif ($question->question_type === 'true_false_grouped') {
                    $prefix = (!empty($ans->is_correct)) ? '*' : '';
                    $lines[] = $prefix . $ans->label . ') ' . $ans->content;
                } elseif ($question->question_type === 'fill_in_blank') {
                    $lines[] = 'Answer: ' . $ans->content;
                }
            }
        }
        // Lời giải
        if (!empty($question->explanation)) {
            $lines[] = 'Lời giải: ' . $question->explanation;
        }
        return implode("\n", $lines);
    }
    public function editQuestion($id)
    {
        $question = DethiQuestion::with('answers')->find($id);
        $rawContent = $this->exportExamToTextQuestion($question);

        return view('crm_course.dethi.editquestion', [
            'question' => $question,
            'exam_id' => $question->de_thi_id,
            'rawContent' => $rawContent
        ]);
    }
    public function storeQuestion(Request $request)
    {
        $data = $request->all();
        $id = $data['question']['id'];
        $question = DethiQuestion::find($id);
        $question->update([
            'question_no' => $data['question']['question_no'],
            'content' => $data['question']['content'],
            'question_type' => $data['question']['question_type'],
            'score' => $data['question']['score'] ?? null,
            'audio' => $data['question']['audio'] ?? null,
            'explanation' => $data['question']['explanation'] ?? null,
        ]);
        if (!empty($data['question']['answers'])) {
            foreach ($data['question']['answers'] as $answerData) {
                $answer = DethiAnswer::find($answerData['id']);
                $answer->update([
                    'label' => $answerData['label'],
                    'content' => $answerData['content'],
                    'is_correct' => isset($answerData['is_correct'])
                                        ? (bool)$answerData['is_correct']
                                        : (
                                            (isset($questionData['correct_answer']) && strtoupper($answerData['label']) == strtoupper($questionData['correct_answer']))
                                                ? true : false
                                        ),
                ]);
            }
        }
        return response()->json(['success' => true]);
    }
    public function storeExam(Request $request)
    {
        $data = $request->all();
        \DB::beginTransaction();
        try {
            $profile = Auth::guard("customer")->user();
            $examType = $data['type'] ?? 'dethi';
            $folderType = $examType === 'baitap' ? ExamFolder::TYPE_EXERCISE : ExamFolder::TYPE_EXAM;
            $folderId = $this->resolveFolderIdForOwner($request->input('folder_id'), $profile->id, $folderType);
            $data['folder_id'] = $folderId;
            
            // 1. Lưu đề thi
            if (!empty($data['id'])) {
                // UPDATE
                $dethi = Dethi::where(['id'=>$data['id'],'created_by'=>$profile->id])->first();
                if($profile->type == 3){
                    $dethi = Dethi::where(['id'=>$data['id']])->first();
                }
                if (!$dethi) {
                    return redirect()->back()->with('error', 'Bạn không có quyền sửa đề thi này');
                }
                $dethi->update([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'grade' => $data['grade'],
                    'time' => $data['time'] ?? 0,
                    'subject' => $data['subject'],
                    'cate_type_id' => $data['cate_type_id'],
                    'pricing_type' => $data['pricing_type'],
                    'price' => $data['pricing_type'] === 'paid' ? ($data['price'] ?? 0) : 0,
                    'created_by' => $profile->id,
                    'part_scores' => json_encode($data['partScores']) ?? null,
                    'true_false_score_percent' => json_encode($data['true_false_score_percent']) ?? null,
                    // 'type' => $data['type'],
                    'course_id' => $data['course_id'] ?? 0,
                    // Thêm các trường kiểm soát truy cập
                    'xemdapan' => $data['xemdapan'] ?? 0,
                    'access_type' => $data['access_type'] ?? 'all',
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'folder_id' => $folderId ?? $dethi->folder_id,
                ]);

                // Cập nhật danh sách lớp
                if ($data['access_type'] === 'class' && !empty($data['class_ids'])) {
                    // Xóa các lớp cũ
                    \App\models\dethi\DethiClass::where('dethi_id', $dethi->id)->delete();
                    
                    // Thêm lớp mới
                    foreach ($data['class_ids'] as $classId) {
                        if (!empty($classId)) {
                            \App\models\dethi\DethiClass::create([
                                'dethi_id' => $dethi->id,
                                'class_id' => $classId
                            ]);
                        }
                    }
                } elseif ($data['access_type'] !== 'class') {
                    // Nếu không phải theo lớp, xóa hết
                    \App\models\dethi\DethiClass::where('dethi_id', $dethi->id)->delete();
                }

                foreach($data['parts'] as $partData){
                    $part = DethiPart::find($partData['id']);
                    $part->part = $partData['part'] ?? null;
                    $part->part_title = $partData['part_title'] ?? null;
                    $part->save();
                    foreach ($partData['questions'] as $questionData) {
                        $question = DethiQuestion::find($questionData['id']);
                        $question->update([
                            'score' => $questionData['score'] ?? null,
                            'image' => isset($questionData['images']) ? json_encode($questionData['images']) : null,
                            'audio' => $questionData['audio'] ?? null,
                        ]);
                    }
                }

            } else {
                // CREATE
                $dethi = Dethi::create([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'grade' => $data['grade'] ?? 0,
                    'time' => $data['time'] ?? 0,
                    'subject' => $data['subject'] ?? 0,
                    'cate_type_id' => $data['cate_type_id'],
                    'pricing_type' => $data['pricing_type'],
                    'price' => $data['pricing_type'] === 'paid' ? ($data['price'] ?? 0) : 0,
                    'created_by' => $profile->id,
                    'status' => $data['type'] == 'dethi' ? 0 : 1,
                    'true_false_score_percent' => json_encode($data['true_false_score_percent']) ?? null,
                    'part_scores' => json_encode($data['partScores']) ?? null,
                    'type' => $data['type'],
                    'course_id' => $data['course_id'] ?? 0,
                    // Thêm các trường kiểm soát truy cập
                    'xemdapan' => $data['xemdapan'] ?? 0,
                    'access_type' => $data['access_type'] ?? 'all',
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'folder_id' => $folderId,
                    ]);

                // Thêm danh sách lớp (nếu có)
                if ($data['access_type'] === 'class' && !empty($data['class_ids'])) {
                    foreach ($data['class_ids'] as $classId) {
                        if (!empty($classId)) {
                            \App\models\dethi\DethiClass::create([
                                'dethi_id' => $dethi->id,
                                'class_id' => $classId
                            ]);
                        }
                    }
                }
                foreach ($data['parts'] as $partData) {
                    $part = DethiPart::create([
                        'part' => $partData['part'] ?? null,
                        'part_title' => $partData['part_title'] ?? null,
                        'dethi_id' => $dethi->id,
                    ]);
                    foreach ($partData['questions'] as $questionData) {
                        $question = DethiQuestion::create([
                            'question_no' => $questionData['question_no'],
                            'content' => $questionData['content'],
                            'question_type' => $questionData['question_type'],
                            'score' => $questionData['score'] ?? null,
                            'audio' => $questionData['audio'] ?? null,
                            'image' => isset($questionData['images']) ? json_encode($questionData['images']) : null,
                            'explanation' => $questionData['explanation'] ?? null,
                            'dethi_part_id' => $part->id,
                            'de_thi_id' => $dethi->id,
                        ]);
                        if (!empty($questionData['answers'])) {
                            foreach ($questionData['answers'] as $answerData) {
                                DethiAnswer::create([
                                    'label' => $answerData['label'],
                                    'content' => $answerData['content'],
                                    'is_correct' => isset($answerData['is_correct'])
                                        ? (bool)$answerData['is_correct']
                                        : (
                                            (isset($questionData['correct_answer']) && strtoupper($answerData['label']) == strtoupper($questionData['correct_answer']))
                                                ? true : false
                                        ),
                                    'dethi_question_id' => $question->id,
                                    'de_thi_id' => $dethi->id,
                                ]);
                            }
                        }
                    }
                }
            }
            \DB::commit();
            return response()->json(['success' => true, 'dethi_id' => $dethi->id]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function filterSubject(Request $request)
    {
        $grade = $request->grade;
        $subject = QuizCategory::where('cate_id',$grade)->get(['id','name']);
        return response()->json($subject);
    }
    public function filterType(Request $request)
    {
        $subject = $request->subject;
        $type = TypeCategory::where('cate_id',$subject)->get(['id','name']);
        return response()->json($type);
    }
    public function deleteQuestion(Request $request)
    {
        $id = $request->id;

        \DB::beginTransaction();
        try {
            $question = DethiQuestion::find($id);
            $answers = DethiAnswer::where('dethi_question_id', $id)->get();
            if($answers){
                foreach ($answers as $answer) {
                    $answer->delete();
                }
            }
            $question->delete();
            \DB::commit();
        return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function startTest($id)
    {
        $dethi = Dethi::with(['parts.questions.answers', 'allowedClasses'])->where('id', $id)->first();
        $profile = Auth::guard("customer")->user();

        // 1. Nếu là người tạo đề, cho phép làm bài luôn
        if($dethi->created_by == $profile->id){
            return $this->allowStartTest($dethi, $profile);
        }

        // 2. Kiểm tra đề thi đã kích hoạt chưa
        if($dethi->status == 0){
            return redirect()->route('home')->with('error', 'Đề thi chưa được kích hoạt');
        }

        // 3. Kiểm tra quyền truy cập theo access_type
        if ($dethi->access_type === 'class') {
            // Đề thi được giao cho lớp - kiểm tra tất cả các lớp của học sinh
            $studentClassCodes = $profile->class_codes ?? [];
            
            if (!$dethi->canStudentAccessMultiple($studentClassCodes)) {
                $allowedClasses = $dethi->allowedClasses()
                    ->with('schoolClass')
                    ->get()
                    ->pluck('schoolClass.class_name')
                    ->implode(', ');
                return redirect()->route('home')->with('error', 'Đề thi này chỉ dành cho lớp: ' . $allowedClasses);
            }
            // Trong lớp → cho làm luôn
            return $this->allowStartTest($dethi, $profile);
            
        } elseif ($dethi->access_type === 'time_limited') {
            $timeResult = calculateExamTimeStatus($dethi->start_time, $dethi->end_time);
            $timeStatus = $timeResult['status'];
            $timeText = $timeResult['text'];
            if($timeStatus == 'not_started'){
                return back()->with('error', "Đề thi chỉ làm được từ {$timeText}");
            }elseif($timeStatus == 'expired'){
                return back()->with('error', "Đề thi đã hết hạn");
            }
        }
        
        // 4. Kiểm tra pricing_type (thanh toán)
        if($dethi->pricing_type == 'free'){
            // Miễn phí → cho làm luôn
            return $this->allowStartTest($dethi, $profile);
        }

        // Mất phí → kiểm tra thanh toán
        $billdethi = BillDethi::where(['dethi_id'=>$id,'student_id'=>$profile->id])->first();
        
        if(!$billdethi){
            return redirect()->route('home')->with('error', 'Bạn cần mua đề thi này để làm bài. Vui lòng thanh toán.');
        }
        
        if($billdethi->status == 0){
            return redirect()->route('home')->with('error', 'Bạn chưa thanh toán hoặc chưa được kiểm tra đơn hàng này');
        }
        
        // Đã thanh toán → cho làm
        return $this->allowStartTest($dethi, $profile);
    }

    /**
     * Helper: Cho phép làm bài và shuffle câu hỏi
     */
    private function allowStartTest($dethi, $profile)
    {
        // Sắp xếp câu hỏi theo question_no để đảm bảo thứ tự đúng
        if ($dethi && $dethi->parts) {
            $dethi->setRelation('parts', $dethi->parts->map(function ($part) {
                if ($part->relationLoaded('questions') && $part->questions) {
                    $sortedQuestions = $part->questions->sortBy('question_no')->values();
                    $part->setRelation('questions', $sortedQuestions);
                }
                return $part;
            }));
        }

        return view('crm_course.dethi.starttest', [
            'dethi' => $dethi, 'user' => $profile
        ]);
    }

    
    public function downloadScoreTable(Request $request, $id)
    {
        try {
            // Lấy thông tin đề thi
            $dethi = Dethi::with('parts.questions.answers')->findOrFail($id);

            // Kiểm tra quyền truy cập
            $profile = Auth::guard("customer")->user();
            if (!$profile) {
                return redirect()->back()->with('error', 'Bạn cần đăng nhập để thực hiện chức năng này.');
            }

            // Kiểm tra xem user có quyền xem kết quả không
            // Cho phép giáo viên (người tạo đề thi) hoặc admin xem kết quả
            if ($profile->id !== $dethi->created_by && $profile->role !== 'admin') {
                $billdethi = BillDethi::where(['dethi_id' => $id, 'student_id' => $profile->id])->first();
                if (!$billdethi) {
                    return redirect()->back()->with('error', 'Bạn không có quyền truy cập kết quả này.');
                }
            }

            // Lấy danh sách session IDs được chọn (nếu có)
            $selectedSessions = $request->input('sessions');
            if ($selectedSessions) {
                $selectedSessions = json_decode($selectedSessions, true);
            }

            // Lấy kết quả thi
            $scoresQuery = ExamSession::with(['student', 'teacher', 'answers', 'dethi.parts.questions'])
                ->where('dethi_id', $id)
                ->where('status', '>=', 1); // Chỉ lấy những bài đã hoàn thành

            // Nếu có danh sách được chọn, lọc theo danh sách đó
            if ($selectedSessions && is_array($selectedSessions) && count($selectedSessions) > 0) {
                $scoresQuery->whereIn('id', $selectedSessions);
            }

            $scores = $scoresQuery->orderBy('total_score', 'desc')
                ->orderBy('finished_at', 'desc')
                ->get();

            if ($scores->isEmpty()) {
                return redirect()->back()->with('error', 'Không có dữ liệu để xuất.');
            }
            // Tạo tên file
            $filename = 'ket_qua_thi_' . Str::slug($dethi->title) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Export Excel
            return Excel::download(new ScoreExport($scores, $dethi), $filename);

        } catch (\Exception $e) {
            \Log::error('Lỗi khi xuất Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xuất file Excel. Vui lòng thử lại.');
        }
    }

    public function submitTest(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $request->validate([
                'dethi_id' => 'required|exists:dethi,id',
                'answers' => 'required|array',
                'student_id' => 'required|exists:customer,id',
                'teacher_id' => 'nullable|exists:customer,id',
                'actual_time' => 'nullable|integer',
            ]);

            $dethi_id = $request->dethi_id;
            $answers = $request->answers;
            $student_id = $request->student_id;
            $teacher_id = $request->teacher_id;
            $actual_time = $request->actual_time;

            // Lấy thông tin đề thi và cấu hình chấm điểm
            $dethi = Dethi::with(['parts.questions.answers'])->findOrFail($dethi_id);
            $partScores = json_decode($dethi->part_scores, true) ?? [];
            $trueFalseScorePercent = json_decode($dethi->true_false_score_percent, true) ?? [];

            // 1. Tạo session làm bài
            $session = ExamSession::create([
                'dethi_id' => $dethi_id,
                'student_id' => $student_id,
                'teacher_id' => $teacher_id,
                'actual_time' => $actual_time,
                'status' => 1, // 1 = đã hoàn thành
                'finished_at' => now(),
            ]);

            $totalScore = 0;
            $correctAnswers = 0;
            $totalQuestions = 0;
            $partResults = [];
            // 2. Xử lý từng câu trả lời và chấm điểm
            foreach ($answers as $question_id => $answer) {
                $question = DethiQuestion::with('answers')->find($question_id);
                if (!$question) {
                    throw new \Exception("Câu hỏi ID {$question_id} không tồn tại");
                }
                $totalQuestions++;

                $answer_text = null;
                $answer_image = null;
                $answer_choice = null;
                $is_correct = false;
                $score = 0;
                $questionScore = $question->score ?? 1; // Mặc định 1 điểm nếu không có

                // Xử lý câu trả lời theo loại câu hỏi
                switch ($question->question_type) {
                    case 'multiple_choice':
                        // Trắc nghiệm: answer có thể là string (label A, B, C, D) hoặc null
                        if (is_null($answer)) {
                            $answer_choice = null;
                            $is_correct = false;
                            $score = 0;
                        } elseif (is_string($answer)) {
                            $answer_choice = $answer;
                            // Kiểm tra đáp án đúng
                            $correctAnswer = $question->answers->where('is_correct', true)->first();
                            if ($correctAnswer && strtoupper($correctAnswer->label) === strtoupper($answer)) {
                                $is_correct = true;
                                $score = $questionScore;
                                $correctAnswers++;
                            } else {
                                $is_correct = false;
                                $score = 0;
                            }
                        } else {
                            throw new \Exception("Câu trả lời trắc nghiệm phải là string hoặc null, nhận được: " . gettype($answer));
                        }
                        break;

                    case 'true_false_grouped':
                        // Đúng/Sai nhóm: answer có thể là mảng [option_id => 1/0/2] hoặc null
                        if (is_null($answer)) {
                            $answer_choice = null;
                            $is_correct = false;
                            $score = 0;
                        } elseif (is_array($answer)) {
                            // Đảm bảo tất cả các câu hỏi con đều có giá trị
                            $completeAnswer = [];
                            foreach ($question->answers as $correctAnswer) {
                                // Nếu không có giá trị được gửi, mặc định là 2 (không chọn)
                                $completeAnswer[$correctAnswer->id] = $answer[$correctAnswer->id] ?? 2;
                            }

                            // Lưu đáp án vào CSDL
                            $answer_choice = json_encode($completeAnswer, JSON_UNESCAPED_UNICODE);

                            // Tính điểm cho câu hỏi đúng/sai nhóm
                            $answeredCount = 0;    // Số câu đã trả lời
                            $correctCount = 0;     // Số câu trả lời đúng
                            $totalSubQuestions = count($question->answers);

                            foreach ($question->answers as $correctAnswer) {
                                $studentAnswer = $completeAnswer[$correctAnswer->id];

                                // Bỏ qua câu không chọn (giá trị 2)
                                if ($studentAnswer == 2) {
                                    continue;
                                }

                                $answeredCount++;

                                // So sánh đáp án của học sinh với đáp án đúng
                                $studentAnswerBool = (bool)$studentAnswer;
                                $correctAnswerBool = (bool)$correctAnswer->is_correct;

                                if ($studentAnswerBool === $correctAnswerBool) {
                                    $correctCount++;
                                }
                            }

                            // Tính điểm dựa trên cấu hình trueFalseScorePercent
                            if ($answeredCount > 0) {
                                // Tìm phần trăm điểm tương ứng với số câu đúng
                                $scorePercent = 0;
                                if (isset($trueFalseScorePercent[$correctCount])) {
                                    $scorePercent = $trueFalseScorePercent[$correctCount];
                                }

                                // Tính điểm thực tế
                                $score = $questionScore * ($scorePercent / 100);
                                // Kiểm tra xem có đúng hết không
                                if ($correctCount == $answeredCount && $answeredCount == $totalSubQuestions) {
                                    $is_correct = true;
                                    $correctAnswers++;
                                }
                            } else {
                                // Không có câu nào được trả lời
                                $score = 0;
                            }
                        } else {
                            throw new \Exception("Câu trả lời đúng/sai nhóm phải là array hoặc null, nhận được: " . gettype($answer));
                        }
                        break;

                    case 'fill_in_blank':
                        // Điền vào chỗ trống: answer có thể là string, array hoặc null
                        if (is_null($answer)) {
                            $answer_text = '';
                            $is_correct = false;
                            $score = 0;
                        } elseif (is_string($answer) || is_array($answer)) {
                            $answer_text = is_array($answer) ? ($answer['text'] ?? '') : $answer;
                            // Kiểm tra đáp án đúng
                            $correctAnswer = $question->answers->where('is_correct', true)->first();
                            if ($correctAnswer && !empty($answer_text) && strtolower(trim($correctAnswer->content)) === strtolower(trim($answer_text))) {
                                $is_correct = true;
                                $score = $questionScore;
                                $correctAnswers++;
                            } else {
                                $is_correct = false;
                                $score = 0;
                            }
                        } else {
                            throw new \Exception("Câu trả lời điền vào chỗ trống phải là string, array hoặc null, nhận được: " . gettype($answer));
                        }
                        break;

                    case 'short_answer':
                        // Tự luận: answer có thể là array ['text', 'image'] hoặc null
                        if (is_null($answer)) {
                            $answer_text = '';
                            $answer_image = null;
                            $score = 0;
                        } elseif (is_array($answer)) {
                            $answer_text = $answer['text'] ?? null;
                            if (isset($answer['image']) && $request->hasFile("answers.$question_id.image")) {
                                $file = $request->file("answers.$question_id.image");
                                $extension = $file->getClientOriginalExtension();
                                $nameImg = uniqid() . '.' . $extension;

                                // Di chuyển ảnh đến thư mục public/uploads/images
                                $file->move(public_path('/uploads/exam_answers'), $nameImg);
                                $answer_image = $nameImg;
                            }
                            // Tự luận cần chấm thủ công, không tính điểm tự động
                            $score = 0;
                        } else {
                            throw new \Exception("Câu trả lời tự luận phải là array hoặc null, nhận được: " . gettype($answer));
                        }
                        break;
                    default:
                        throw new \Exception("Loại câu hỏi không được hỗ trợ: " . $question->question_type);
                }

                // Lưu câu trả lời
                ExamAnswer::create([
                    'exam_session_id' => $session->id,
                    'question_id' => $question_id,
                    'answer_text' => $answer_text,
                    'answer_image' => $answer_image,
                    'answer_choice' => $answer_choice,
                    'is_correct' => $is_correct,
                    'score' => $score,
                ]);

                $totalScore += $score;

                // Thống kê theo phần
                $partId = $question->dethi_part_id;
                if (!isset($partResults[$partId])) {
                    $partResults[$partId] = [
                        'total_questions' => 0,
                        'correct_answers' => 0,
                        'score' => 0,
                        'max_score' => 0
                    ];
                }
                $partResults[$partId]['total_questions']++;
                $partResults[$partId]['max_score'] += $questionScore;
                if ($is_correct) {
                    $partResults[$partId]['correct_answers']++;
                }
                $partResults[$partId]['score'] += $score;
            }

            // 3. Tính điểm tổng và cập nhật session
            $maxPossibleScore = $dethi->parts->sum(function($part) {
                return $part->questions->sum('score') ?: $part->questions->count();
            });

            $percentage = $maxPossibleScore > 0 ? ($totalScore / $maxPossibleScore) * 100 : 0;
            // Cập nhật session với kết quả
            $session->update([
                'total_score' => $totalScore,//điểm tổng
                'max_score' => $maxPossibleScore,//điểm tối đa
                'percentage' => $percentage,//phần trăm điểm
                'correct_answers' => $correctAnswers,//số câu đúng
                'total_questions' => $totalQuestions,//số câu hỏi
                'part_results' => json_encode($partResults),//kết quả từng phần
            ]);
            \DB::commit();
            return redirect()->route('resultDethi', ['id' => $session->id]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    public function result($id)
    {
        $session = ExamSession::with([
            'dethi.parts.questions.answers',
            'answers'
        ])->findOrFail($id);
        // Tính toán thống kê
        $multipleChoiceQuestions = 0;
        $trueFalseQuestions = 0;
        $essayQuestions = 0;
        $fillInBlankQuestions = 0;
        $correctMultipleChoice = 0;
        $correctTrueFalse = 0;
        $correctFillInBlank = 0;
        $totalMultipleChoiceScore = 0;
        $totalTrueFalseScore = 0;
        $totalFillInBlankScore = 0;

        foreach ($session->dethi->parts as $part) {
            foreach ($part->questions as $question) {
                if ($question->question_type === 'multiple_choice') {
                    $multipleChoiceQuestions++;
                    // Kiểm tra câu trả lời đúng từ exam_answers
                    $studentAnswer = $session->answers->where('question_id', $question->id)->first();
                    if ($studentAnswer) {
                        if ($studentAnswer->is_correct === 1) {
                            $correctMultipleChoice++;
                            $totalMultipleChoiceScore += $studentAnswer->score ?? 1;
                        }
                    }
                } elseif ($question->question_type === 'true_false_grouped') {
                    $trueFalseQuestions++;
                    // Lấy câu trả lời của học sinh cho câu hỏi này
                    $studentAnswer = $session->answers->where('question_id', $question->id)->first();

                    if ($studentAnswer && $studentAnswer->answer_choice) {
                        // Parse JSON answer_choice của học sinh: {"5":"0","6":"1","7":"0","8":"0"}
                        $studentChoices = json_decode($studentAnswer->answer_choice, true);

                        // Lấy đáp án đúng từ bảng dethi_answers
                        $correctAnswers = $question->answers->pluck('is_correct', 'id')->toArray();

                        // Đếm số câu đúng
                        $correctCount = 0;
                        if (is_array($studentChoices)) {
                            foreach ($studentChoices as $subQuestionId => $studentChoice) {
                                // Bỏ qua câu không chọn (giá trị "2")
                                if ($studentChoice === "2") {
                                    continue;
                                }

                                $correctAnswer = $correctAnswers[$subQuestionId] ?? 0;
                                if ($studentChoice === (string)$correctAnswer) {
                                    $correctCount++;
                                }
                            }
                        }
                        //dd($correctCount);
                        // Tính điểm dựa trên cấu hình trueFalseScorePercent
                        $trueFalseScorePercent = json_decode($session->dethi->true_false_score_percent ?? '{"1":10,"2":25,"3":50,"4":100}', true);
                        if (isset($trueFalseScorePercent[$correctCount]) && $trueFalseScorePercent[$correctCount] > 0) {
                            $correctTrueFalse++;
                            $scorePercent = $trueFalseScorePercent[$correctCount];
                            $questionScore = $question->score ?? 1;
                            $totalTrueFalseScore += ($questionScore * $scorePercent / 100);
                        }
                    }
                } elseif ($question->question_type === 'fill_in_blank') {
                    $fillInBlankQuestions++;
                    // Kiểm tra câu trả lời đúng từ exam_answers
                    $studentAnswer = $session->answers->where('question_id', $question->id)->first();
                    if ($studentAnswer) {
                        if ($studentAnswer->is_correct === 1) {
                            $correctFillInBlank++;
                            $totalFillInBlankScore += $studentAnswer->score ?? 1;
                        }
                    }
                } elseif (in_array($question->question_type, ['essay', 'short_answer'])) {
                    $essayQuestions++;
                }
            }
        }
        // Kiểm tra có được phép xem đáp án không
        $canViewAnswer = $session->dethi->xemdapan ?? false;

        return view('crm_course.dethi.result', [
            'session' => $session,
            'multiple_choice_questions' => $multipleChoiceQuestions, //số câu trắc nghiệm
            'true_false_questions' => $trueFalseQuestions, //số câu đúng/sai nhóm
            'essay_questions' => $essayQuestions, //số câu tự luận
            'fill_in_blank_questions' => $fillInBlankQuestions, //số câu điền vào chỗ trống
            'correct_multiple_choice' => $correctMultipleChoice, //số câu trắc nghiệm đúng
            'correct_true_false' => $correctTrueFalse, //số câu đúng/sai nhóm đúng
            'correct_fill_in_blank' => $correctFillInBlank, //số câu điền vào chỗ trống đúng
            'total_multiple_choice_score' => $totalMultipleChoiceScore, //điểm tổng câu trắc nghiệm
            'total_true_false_score' => $totalTrueFalseScore, //điểm tổng câu đúng/sai nhóm
            'total_fill_in_blank_score' => $totalFillInBlankScore, //điểm tổng câu điền vào chỗ trống
            'canViewAnswer' => $canViewAnswer //cho phép xem đáp án
        ]);
    }

    public function getExamResult($session_id)
    {
        try {
            $session = ExamSession::with([
                'dethi.parts.questions.answers',
                'student',
                'teacher',
                'answers.question.answers'
            ])->findOrFail($session_id);

            $result = [
                'session_id' => $session->id,
                'dethi_title' => $session->dethi->title,
                'student_name' => $session->student->name ?? 'N/A',
                'teacher_name' => $session->teacher->name ?? 'N/A',
                'actual_time' => $session->actual_time,
                'finished_at' => $session->finished_at,
                'total_score' => $session->total_score,
                'max_score' => $session->max_score,
                'percentage' => $session->percentage,
                'correct_answers' => $session->correct_answers,
                'total_questions' => $session->total_questions,
                'part_results' => json_decode($session->part_results, true),
                'answers' => $session->answers->map(function($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'question_content' => $answer->question->content ?? 'N/A',
                        'question_type' => $answer->question->question_type ?? 'N/A',
                        'student_answer' => $answer->answer_text ?: $answer->answer_choice,
                        'correct_answer' => $answer->question->answers->where('is_correct', true)->first()->label ?? 'N/A',
                        'is_correct' => $answer->is_correct,
                        'score' => $answer->score,
                        'teacher_comment' => $answer->teacher_comment,
                        'graded_at' => $answer->graded_at,
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function gradeEssayQuestionsBulk(Request $request)
    {
        \DB::beginTransaction();
        try {

            $teacher_id = Auth::guard("customer")->user()->id;
            $session = null;
            foreach ($request->essay_data as $item) {
                $examAnswer = ExamAnswer::findOrFail($item['exam_answer_id']);
                // Lưu session để cập nhật tổng điểm sau cùng
                if (!$session) {
                    $session = $examAnswer->session;
                }
                $examAnswer->update([
                    'score' => $item['score'],
                    'is_correct' => $item['score'] > 0,
                    'graded_by' => $teacher_id,
                    'graded_at' => now(),
                    'teacher_comment' => $item['teacher_comment'],
                ]);
            }
            if ($session) {
                $totalScore = $session->answers->sum('score');
                $maxScore = $session->max_score;
                $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
                $session->update([
                    'total_score' => $totalScore,
                    'percentage' => $percentage,
                    'status' => 2,
                ]);
            }
            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Lưu điểm thành công!',
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa toàn bộ đề thi, các part, câu hỏi, đáp án, session, answer, và file vật lý liên quan
     */
    public function destroyDeThi($id)
    {
        \DB::beginTransaction();
        try {
            $dethi = Dethi::with(['parts.questions.answers', 'sessions.answers'])->findOrFail($id);
            // Xóa file audio của từng câu hỏi (nếu có)
            foreach ($dethi->parts as $part) {
                foreach ($part->questions as $question) {
                    // Xóa file audio nếu có
                    if ($question->audio) {
                        // Lấy đường dẫn file từ URL
                        $filePath = str_replace('/audio/', 'audio/', $question->audio);
                        // Xóa file từ thư mục public/audio
                        if (file_exists(public_path($filePath))) {
                            unlink(public_path($filePath));
                        }
                    }
                    // Xóa file ảnh nếu có
                    if ($question->image) {
                        $images = json_decode($question->image, true);
                        if (is_array($images)) {
                            foreach ($images as $image) {
                                if (isset($image['url'])) {
                                    // Lấy đường dẫn file từ URL
                                    $filePath = str_replace('/exam_images/', 'exam_images/', $image['url']);
                                    // Xóa file từ thư mục public/exam_images
                                    if (file_exists(public_path($filePath))) {
                                        unlink(public_path($filePath));
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // Xóa file ảnh của các bài làm (exam_answers)
            foreach ($dethi->sessions as $session) {
                foreach ($session->answers as $answer) {
                    if ($answer->answer_image) {
                        Storage::disk('public')->delete($answer->answer_image);
                    }
                }
            }
            // Xóa các session và answer
            foreach ($dethi->sessions as $session) {
                foreach ($session->answers as $answer) {
                    $answer->delete();
                }
                $session->delete();
            }
            // Xóa các câu hỏi, đáp án, part
            foreach ($dethi->parts as $part) {
                foreach ($part->questions as $question) {
                    foreach ($question->answers as $ans) {
                        $ans->delete();
                    }
                    $question->delete();
                }
                $part->delete();
            }
            // Xóa đề thi
            $dethi->delete();
            \DB::commit();
            return redirect()->route('khoiTaoDeThi')->with('success', 'Đã xóa đề thi thành công');
        } catch (\Exception $e) {
            \DB::rollBack();
           return back()->with('error', 'Đã xóa đề thi không thành công');
        }
    }
    public function listCategorymainDethi($id)
    {
        // Lấy thông tin khối lớp
        $selectedGrade = CategoryMain::where('id', $id)->first();
        if (!$selectedGrade) {
            return redirect()->route('allDeThi')->with('error', 'Không tìm thấy khối lớp');
        }

        // Load tất cả khối lớp (để hiển thị grid)
        $grades = CategoryMain::where('status', 1)
            ->withCount(['category' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'ASC')
            ->get();
        
        // Chỉ lấy các môn học thuộc khối lớp được chọn
        $subjects = QuizCategory::where('status', 1)
            ->where('cate_id', $id)  // Filter theo khối lớp
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy IDs của các môn học thuộc khối lớp này
        $subjectIds = $subjects->pluck('id')->toArray();
        
        // Chỉ lấy các loại đề thuộc các môn học của khối lớp này
        $examTypes = TypeCategory::where('status', 1)
            ->whereIn('cate_id', $subjectIds)  // Filter theo môn học của khối lớp
            ->orderBy('cate_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->groupBy('cate_id');

        // Lấy tất cả loại đề (TypeCategory - Bộ đề) thuộc các môn học của khối lớp này
        $allExamTypes = TypeCategory::where('status', 1)
            ->whereIn('cate_id', $subjectIds)
            ->with('cate')
            ->orderBy('cate_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy đề thi theo khối lớp
        $allExams = Dethi::with(['gradeCategory', 'subjectCategory', 'parts.questions', 'sessions', 'typeCategory', 'customer'])
            ->where('type', 'dethi')
            ->where('status', 1)
            ->where('access_type', 'all')
            ->where('grade', $id)
            ->orderBy('id', 'DESC')
            ->get();

        // Hiển thị TẤT CẢ bộ đề (TypeCategory) kể cả không có đề thi
        $examsByType = [];
        foreach ($allExamTypes as $examType) {
            $examsInType = $allExams->filter(function($exam) use ($examType) {
                // Filter đề thi theo cate_type_id
                return $exam->cate_type_id == $examType->id;
            });
            
            // Luôn hiển thị bộ đề, chỉ lấy 10 đề đầu tiên
            $examsByType[] = [
                'type' => $examType,
                'exams' => $examsInType->take(10),
                'count' => $examsInType->count(), // Tổng số thực tế
                'total' => $examsInType->count()
            ];
        }
        
        // Đề thi không thuộc bộ đề nào sẽ nhóm vào "Đề thi khác"
        $unmatchedExams = $allExams->filter(function($exam) {
            // Đề thi không có cate_type_id hoặc cate_type_id = null/0
            return !$exam->cate_type_id || $exam->cate_type_id == 0;
        });
        
        if ($unmatchedExams->count() > 0) {
            $examsByType[] = [
                'type' => (object)['name' => 'Đề thi khác', 'id' => 0],
                'exams' => $unmatchedExams->take(10),
                'count' => $unmatchedExams->count(), // Tổng số thực tế
                'total' => $unmatchedExams->count()
            ];
        }

        $totalExams = $allExams->count();

        // Khởi tạo các biến selected khác để tránh lỗi undefined
        $selectedSubject = null;
        $selectedExamType = null;

        return view('dethi.listall', compact(
            'examsByType', 'grades', 'subjects', 'examTypes', 'totalExams', 
            'selectedGrade', 'selectedSubject', 'selectedExamType'
        ));
    }

    public function listCategoryDethi($id)
    {
        // Lấy thông tin môn học
        $selectedSubject = QuizCategory::where('id', $id)->first();
        if (!$selectedSubject) {
            return redirect()->route('allDeThi')->with('error', 'Không tìm thấy môn học');
        }

        // Lấy khối lớp của môn học này
        $selectedGrade = CategoryMain::where('id', $selectedSubject->cate_id)->first();

        // Load tất cả khối lớp (để hiển thị grid)
        $grades = CategoryMain::where('status', 1)
            ->withCount(['category' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'ASC')
            ->get();
        
        // Chỉ lấy các môn học thuộc khối lớp của môn học được chọn
        $subjects = QuizCategory::where('status', 1)
            ->where('cate_id', $selectedSubject->cate_id)  // Filter theo khối lớp
            ->orderBy('id', 'ASC')
            ->get();
        
        // Chỉ lấy các loại đề thuộc môn học được chọn
        $examTypes = TypeCategory::where('status', 1)
            ->where('cate_id', $id)  // Filter theo môn học
            ->orderBy('id', 'ASC')
            ->get()
            ->groupBy('cate_id');

        // Lấy tất cả loại đề (TypeCategory - Bộ đề) thuộc môn học này
        $allExamTypes = TypeCategory::where('status', 1)
            ->where('cate_id', $id)
            ->with('cate')
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy đề thi theo môn học
        $allExams = Dethi::with(['gradeCategory', 'subjectCategory', 'parts.questions', 'sessions', 'typeCategory', 'customer'])
            ->where('type', 'dethi')
            ->where('status', 1)
            ->where('access_type', 'all')
            ->where('subject', $id)
            ->orderBy('id', 'DESC')
            ->get();

        // Hiển thị TẤT CẢ bộ đề (TypeCategory) kể cả không có đề thi
        $examsByType = [];
        foreach ($allExamTypes as $examType) {
            $examsInType = $allExams->filter(function($exam) use ($examType) {
                // Filter đề thi theo cate_type_id
                return $exam->cate_type_id == $examType->id;
            });
            
            // Luôn hiển thị bộ đề, chỉ lấy 10 đề đầu tiên
            $examsByType[] = [
                'type' => $examType,
                'exams' => $examsInType->take(10),
                'count' => $examsInType->count(), // Tổng số thực tế
                'total' => $examsInType->count()
            ];
        }
        
        // Đề thi không thuộc bộ đề nào sẽ nhóm vào "Đề thi khác"
        $unmatchedExams = $allExams->filter(function($exam) {
            // Đề thi không có cate_type_id hoặc cate_type_id = null/0
            return !$exam->cate_type_id || $exam->cate_type_id == 0;
        });
        
        if ($unmatchedExams->count() > 0) {
            $examsByType[] = [
                'type' => (object)['name' => 'Đề thi khác', 'id' => 0],
                'exams' => $unmatchedExams->take(10),
                'count' => $unmatchedExams->count(), // Tổng số thực tế
                'total' => $unmatchedExams->count()
            ];
        }

        $totalExams = $allExams->count();

        // Khởi tạo các biến selected khác để tránh lỗi undefined
        $selectedExamType = null;

        return view('dethi.listall', compact(
            'examsByType', 'grades', 'subjects', 'examTypes', 'totalExams', 
            'selectedGrade', 'selectedSubject', 'selectedExamType'
        ));
    }

    public function listBodDethi($id)
    {
        // Lấy thông tin loại đề
        $selectedExamType = TypeCategory::where('id', $id)->first();
        if (!$selectedExamType) {
            return redirect()->route('allDeThi')->with('error', 'Không tìm thấy loại đề');
        }

        // Lấy môn học của loại đề này
        $selectedSubject = QuizCategory::where('id', $selectedExamType->cate_id)->first();
        
        // Lấy khối lớp của môn học này
        $selectedGrade = null;
        if ($selectedSubject) {
            $selectedGrade = CategoryMain::where('id', $selectedSubject->cate_id)->first();
        }

        // Load tất cả khối lớp (để hiển thị grid)
        $grades = CategoryMain::where('status', 1)
            ->withCount(['category' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'ASC')
            ->get();
        
        // Chỉ lấy các môn học thuộc khối lớp của loại đề này
        $subjects = collect();
        if ($selectedSubject) {
            $subjects = QuizCategory::where('status', 1)
                ->where('cate_id', $selectedSubject->cate_id)  // Filter theo khối lớp
                ->orderBy('id', 'ASC')
                ->get();
        }
        
        // Chỉ lấy các loại đề thuộc môn học của loại đề được chọn
        $examTypes = collect();
        if ($selectedSubject) {
            $examTypes = TypeCategory::where('status', 1)
                ->where('cate_id', $selectedSubject->id)  // Filter theo môn học
                ->orderBy('id', 'ASC')
                ->get()
                ->groupBy('cate_id');
        }

        // Lấy đề thi theo loại đề - filter theo cate_type_id
        $allExams = Dethi::with(['gradeCategory', 'subjectCategory', 'parts.questions', 'sessions', 'typeCategory', 'customer'])
            ->where('type', 'dethi')
            ->where('status', 1)
            ->where('access_type', 'all')
            ->where('cate_type_id', $id)
            ->orderBy('id', 'DESC')
            ->get();

        // Chỉ hiển thị bộ đề được chọn
        $examsByType = [];
        if ($allExams->count() > 0) {
            $examsByType[] = [
                'type' => $selectedExamType,
                'exams' => $allExams->take(10),
                'count' => $allExams->count(),
                'total' => $allExams->count()
            ];
        }

        $totalExams = $allExams->count();

        return view('dethi.listall', compact(
            'examsByType', 'grades', 'subjects', 'examTypes', 'totalExams', 
            'selectedGrade', 'selectedSubject', 'selectedExamType'
        ));
    }
    public function dangkyMuaDeThi($id)
    {
        $dethi = Dethi::findOrFail($id);
        $profile = Auth::guard("customer")->user();
        $existBill = BillDethi::where([
            "student_id" => $profile->id,
            "dethi_id" => $dethi->id,
        ])->first();
        if ($existBill) {
            if($existBill->status == 0){
                return view("dethi.dangkymuathanhcong",['dethi' => $dethi,'bill' => $existBill])->with("error", "Bạn đã đăng ký đề thi này");
            }else{
                return redirect()->route('khoiTaoDeThi')->with("success", "Đơn hàng của bạn đã được kiểm tra và đã thanh toán");
            }
        }else{
            $bill_id = rand();
            $bill = new BillDethi();
            $bill->bill_id = $bill_id;
            $bill->student_id = $profile->id;
            $bill->dethi_id = $dethi->id;
            $bill->status = 0;
            $bill->price = $dethi->price;
            $bill->save();
            return view("dethi.dangkymuathanhcong",['dethi' => $dethi,'bill' => $bill])->with("success", "Bạn đã đăng ký đề thi này");
        }

    }

    public function themvaoGioHangDethi(Request $request)
    {
        $dethi = Dethi::where([
            "id" => $request->dethi_id,
        ])->first();

        $cart = session()->get('cart', []);

        if(isset($cart[$request->dethi_id]) && $cart[$request->dethi_id]['type'] == 'dethi') {
            $cart[$request->dethi_id]['quantity'] = $cart[$request->dethi_id]['quantity'] + 1;
        } else {
            $cart[$request->dethi_id] = [
                "idpro" => $request->dethi_id,
                "name" => $dethi->title,
                "quantity" => 1,
                "price" => $dethi->price,
                "discount" => 0,
                "image" => null,
                "type" => 'dethi'
            ];
        }

        session()->put('cart', $cart);

        $countCart = 0;
        foreach($cart as $item){
            $countCart += $item['quantity'];
        }
        return response()->json(['data' => $cart, 'message' => 'Thêm vào giỏ hàng thành công', 'success' => true, 'count' => $countCart]);
    }

    /**
     * Extract images from HTML and upload to server
     * @param string $html - HTML content from Pandoc
     * @param string $baseDir - Base directory where media files are extracted
     * @return array - Array mapping image positions to uploaded URLs
     */
    private function extractAndUploadImages($html, $baseDir)
    {
        $imageMapping = [];
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        
        $images = $dom->getElementsByTagName('img');
        $imageIndex = 0;

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            
            // ✅ XỬ LÝ BASE64 IMAGES
            if (strpos($src, 'data:image') === 0) {
                if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $src, $matches)) {
                    $extension = $matches[1];
                    $imageData = base64_decode($matches[2]);
                    
                    $tempFile = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
                    file_put_contents($tempFile, $imageData);
                    
                    $uploadedImageData = $this->uploadImageToPublic($tempFile);
                    @unlink($tempFile);
                    
                    if ($uploadedImageData) {
                        $imageMapping[$imageIndex] = $uploadedImageData;
                        $imageMapping[$imageIndex]['original_src'] = 'base64';
                        $imageMapping[$imageIndex]['position'] = $imageIndex;
                        $imageIndex++;
                    }
                }
                continue;
            }
            
            // Bỏ qua URL external
            if (strpos($src, 'http') === 0) {
                continue;
            }

            // ✅ XỬ LÝ FILE IMAGES - Thử nhiều đường dẫn
            $possiblePaths = [
                $baseDir . '/' . $src,
                $baseDir . '/media/' . basename($src),
                dirname($baseDir) . '/' . $src,
            ];
            
            $imagePath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $imagePath = $path;
                    break;
                }
            }
            
            if ($imagePath) {
                $uploadedImageData = $this->uploadImageToPublic($imagePath);
                
                if ($uploadedImageData) {
                    $imageMapping[$imageIndex] = $uploadedImageData;
                    $imageMapping[$imageIndex]['original_src'] = $src;
                    $imageMapping[$imageIndex]['position'] = $imageIndex;
                    $imageIndex++;
                }
            }
        }
        return $imageMapping;
    }

    /**
     * Upload image to public directory
     * @param string $sourcePath - Path to source image file
     * @return string|null - URL of uploaded image or null on failure
     */
    private function uploadImageToPublic($sourcePath)
    {
        try {
            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $originalName = pathinfo($sourcePath, PATHINFO_BASENAME);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $destinationPath = public_path('exam_images');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $destination = $destinationPath . '/' . $filename;
            
            if (copy($sourcePath, $destination)) {
                // Lấy thông tin file
                $fileSize = filesize($destination);
                $mimeType = mime_content_type($destination);
                
                return [
                    'url' => '/exam_images/' . $filename,
                    'name' => $originalName,
                    'size' => $fileSize,
                    'type' => $mimeType,
                    'server_filename' => $filename,
                    'uploaded_at' => now()->toISOString()
                ];
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Error uploading image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Map images to questions based on their position in HTML
     * @param array $questions - Parsed questions array
     * @param array $imageMapping - Image URLs mapped by position
     * @param string $html - Original HTML content
     * @return array - Questions with images attached
     */
    private function mapImagesToQuestions($questions, $imageMapping, $html)
    {
        if (empty($imageMapping)) {
            return $questions;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        
        // Tạo một array để lưu vị trí của từng câu hỏi trong HTML
        $questionPositions = [];
        $currentImageIndex = 0;

        // Duyệt qua tất cả các node để tìm câu hỏi và ảnh
        $xpath = new \DOMXPath($dom);
        $allNodes = $xpath->query('//body//*');
        
        $currentQuestionPart = null;
        $currentQuestionNo = null;
        
        foreach ($allNodes as $node) {
            $text = trim($node->textContent);
            
            // Kiểm tra xem có phải là phần không
            if (preg_match('/^PHẦN\s+([IVXLCDM1-9]+)\./iu', $text, $m)) {
                $currentQuestionPart = 'PHẦN ' . strtoupper($m[1]);
            }
            
            // Kiểm tra xem có phải là câu hỏi không
            if (preg_match('/^Câu\s+(\d+)\./ui', $text, $m)) {
                $currentQuestionNo = (int)$m[1];
                
                // Nếu chưa có phần, tạo phần mặc định
                if (!$currentQuestionPart) {
                    $currentQuestionPart = 'PHẦN I';
                }
                
                if (!isset($questionPositions[$currentQuestionPart])) {
                    $questionPositions[$currentQuestionPart] = [];
                }
                
                $questionPositions[$currentQuestionPart][$currentQuestionNo] = [
                    'images' => []
                ];
            }
            
            // Kiểm tra xem có ảnh không
            if ($node->nodeName === 'img' && $currentQuestionPart && $currentQuestionNo) {
                if (isset($imageMapping[$currentImageIndex])) {
                    $questionPositions[$currentQuestionPart][$currentQuestionNo]['images'][] = $imageMapping[$currentImageIndex];
                    $currentImageIndex++;
                }
            }
        }

        // Gắn ảnh vào câu hỏi tương ứng
        foreach ($questions as &$part) {
            $partKey = $part['part'];
            
            if (isset($questionPositions[$partKey])) {
                foreach ($part['questions'] as &$question) {
                    $qNo = $question['question_no'];
                    
                    if (isset($questionPositions[$partKey][$qNo]['images']) && 
                        !empty($questionPositions[$partKey][$qNo]['images'])) {
                        $question['images'] = $questionPositions[$partKey][$qNo]['images'];
                    }
                }
            }
        }

        return $questions;
    }

    /**
     * Recursively delete a directory
     * @param string $dir - Directory path to delete
     */
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }

    public function bulkDelete(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type != 1 && !$isSuperAdmin){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $folderIds = $request->input('folder_ids', []);
        $dethiIds = $request->input('dethi_ids', []);

        if (empty($folderIds) && empty($dethiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục nào được chọn để xóa.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Xóa folders (bao gồm đệ quy xóa folder con và đề thi trong folder)
            if (!empty($folderIds)) {
                foreach ($folderIds as $folderId) {
                    $this->deleteFolderRecursive($folderId, $profile->id, $isSuperAdmin);
                }
            }

            // Xóa đề thi
            if (!empty($dethiIds)) {
                foreach ($dethiIds as $dethiId) {
                    $this->deleteDethi($dethiId, $profile->id, $isSuperAdmin);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xuất bản đề thi (chuyển status = 1)
     */
    public function bulkPublish(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type != 1 && !$isSuperAdmin){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $dethiIds = $request->input('dethi_ids', []);

        if (empty($dethiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có đề thi nào được chọn để xuất bản.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            foreach ($dethiIds as $dethiId) {
                $dethiQuery = Dethi::where('id', $dethiId)
                    ->where('type', 'dethi');
                
                // Giáo viên chỉ có thể xuất bản đề thi của chính họ
                if (!$isSuperAdmin) {
                    $dethiQuery->where('created_by', $profile->id);
                }
                
                $dethi = $dethiQuery->first();
                
                if ($dethi) {
                    $dethi->status = 1;
                    $dethi->save();
                    $updatedCount++;
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Đã xuất bản {$updatedCount} đề thi thành công."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk publish error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xuất bản: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kết thúc đề thi (chuyển status = 0)
     */
    public function bulkFinish(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type != 1 && !$isSuperAdmin){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $dethiIds = $request->input('dethi_ids', []);

        if (empty($dethiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có đề thi nào được chọn để kết thúc.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            foreach ($dethiIds as $dethiId) {
                $dethiQuery = Dethi::where('id', $dethiId)
                    ->where('type', 'dethi');
                
                // Giáo viên chỉ có thể kết thúc đề thi của chính họ
                if (!$isSuperAdmin) {
                    $dethiQuery->where('created_by', $profile->id);
                }
                
                $dethi = $dethiQuery->first();
                
                if ($dethi) {
                    $dethi->status = 0;
                    $dethi->save();
                    $updatedCount++;
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Đã kết thúc {$updatedCount} đề thi thành công."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk finish error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kết thúc: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật đối tượng làm bài (access_type) cho nhiều đề thi
     */
    public function bulkUpdateAccess(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;

        if($profile->type != 1 && !$isSuperAdmin){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $data = $request->validate([
            'dethi_ids' => ['required','array'],
            'dethi_ids.*' => ['integer','exists:dethi,id'],
            'access_type' => ['required','in:all,class,time_limited'],
            'classes' => ['nullable','array'],
            'classes.*' => ['integer','exists:school_classes,id'],
            'start_time' => ['nullable','date'],
            'end_time' => ['nullable','date','after_or_equal:start_time'],
        ]);

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            $classIds = $data['access_type'] === 'class' ? ($data['classes'] ?? []) : [];

            foreach ($data['dethi_ids'] as $id) {
                $dethiQuery = Dethi::where('id', $id)
                    ->where('type', 'dethi');

                if (!$isSuperAdmin) {
                    $dethiQuery->where('created_by', $profile->id);
                }

                $exam = $dethiQuery->first();
                if (!$exam) {
                    continue;
                }

                $exam->access_type = $data['access_type'];
                $exam->start_time = $data['access_type'] === 'time_limited' ? $data['start_time'] : null;
                $exam->end_time = $data['access_type'] === 'time_limited' ? $data['end_time'] : null;
                $exam->save();

                // Cập nhật danh sách lớp
                if ($data['access_type'] === 'class') {
                    DethiClass::where('dethi_id', $exam->id)->delete();
                    $insert = [];
                    foreach ($classIds as $classId) {
                        $insert[] = [
                            'dethi_id' => $exam->id,
                            'class_id' => $classId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (!empty($insert)) {
                        DethiClass::insert($insert);
                    }
                } else {
                    DethiClass::where('dethi_id', $exam->id)->delete();
                }

                $updatedCount++;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật đối tượng cho {$updatedCount} đề thi.",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk access update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge các thẻ <p> liên tiếp không có pattern đặc biệt (Câu X., A., B., Lời giải, etc.)
     * để giữ lại xuống dòng trong cùng một câu hỏi
     * Xử lý trực tiếp trên HTML string để đơn giản và hiệu quả hơn
     */
    private function mergeConsecutiveParagraphsInHtml($html)
    {
        // Tách HTML thành các thẻ <p> riêng biệt
        $parts = preg_split('/(<p[^>]*>.*?<\/p>)/is', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
        
        $result = '';
        $previousText = '';
        $previousTag = '';
        
        foreach ($parts as $part) {
            $content = $part[0];
            $offset = $part[1];
            
            // Nếu là thẻ <p>
            if (preg_match('/^<p[^>]*>(.*?)<\/p>$/is', $content, $match)) {
                $text = trim(strip_tags($content));
                
                // Kiểm tra pattern đặc biệt
                $hasSpecialPattern = preg_match('/^(PHẦN\s+[IVXLCDM1-9]+|Câu\s+\d+|[*]?[A-D]\.|[*]?[a-d]\)|Lời giải)/iu', $text);
                
                // Nếu cả 2 thẻ <p> liên tiếp đều không có pattern, merge bằng \n
                if ($previousText !== '' && !$hasSpecialPattern) {
                    $previousHasPattern = preg_match('/^(PHẦN\s+[IVXLCDM1-9]+|Câu\s+\d+|[*]?[A-D]\.|[*]?[a-d]\)|Lời giải)/iu', $previousText);
                    
                    if (!$previousHasPattern) {
                        // Thay </p> của thẻ trước bằng \n và loại bỏ <p> của thẻ hiện tại
                        $result = preg_replace('/<\/p>$/i', "\n", $result);
                        $content = preg_replace('/^<p[^>]*>/i', '', $content);
                    }
                }
                
                $result .= $content;
                $previousText = $text;
                $previousTag = $content;
            } else {
                // Phần không phải thẻ <p>
                $result .= $content;
            }
        }
        
        return $result;
    }
    
    /**
     * Merge các thẻ <p> liên tiếp không có pattern đặc biệt (Câu X., A., B., Lời giải, etc.)
     * để giữ lại xuống dòng trong cùng một câu hỏi
     * @deprecated Sử dụng mergeConsecutiveParagraphsInHtml thay thế
     */
    private function mergeConsecutiveParagraphs($body, $dom)
    {
        $mergedHtml = '';
        $previousNode = null;
        $previousText = '';
        
        foreach ($body->childNodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                $mergedHtml .= $dom->saveHTML($node);
                continue;
            }
            
            $nodeText = trim(strip_tags($dom->saveHTML($node)));
            
            // Kiểm tra xem node này có pattern đặc biệt không
            $hasSpecialPattern = preg_match('/^(PHẦN\s+[IVXLCDM1-9]+|Câu\s+\d+|[*]?[A-D]\.|[*]?[a-d]\)|Lời giải)/iu', $nodeText);
            
            // Nếu node trước không có pattern đặc biệt và node hiện tại cũng không có
            // thì merge chúng lại (thay </p><p> bằng space)
            if ($previousNode && !$hasSpecialPattern) {
                $previousHasPattern = preg_match('/^(PHẦN\s+[IVXLCDM1-9]+|Câu\s+\d+|[*]?[A-D]\.|[*]?[a-d]\)|Lời giải)/iu', $previousText);
                
                if (!$previousHasPattern && $node->nodeName === 'p' && $previousNode->nodeName === 'p') {
                    // Merge: thay </p> của node trước và <p> của node hiện tại bằng \n để giữ xuống dòng
                    $previousHtml = $dom->saveHTML($previousNode);
                    $currentHtml = $dom->saveHTML($node);
                    
                    // Loại bỏ thẻ đóng </p> của previous và thẻ mở <p> của current, thay bằng \n
                    $previousHtml = preg_replace('/<\/p>\s*$/i', '', $previousHtml);
                    $currentHtml = preg_replace('/^<p[^>]*>/i', "\n", $currentHtml);
                    
                    // Thay thế previous node trong mergedHtml
                    $mergedHtml = preg_replace('/' . preg_quote($dom->saveHTML($previousNode), '/') . '$/', $previousHtml, $mergedHtml);
                    $mergedHtml .= $currentHtml;
                    
                    $previousNode = $node;
                    $previousText = $nodeText;
                    continue;
                }
            }
            
            // Nếu có pattern đặc biệt hoặc không phải <p>, giữ nguyên
            $mergedHtml .= $dom->saveHTML($node);
            $previousNode = $node;
            $previousText = $nodeText;
        }
        
        return $mergedHtml;
    }
    
    /**
     * Convert HTML thành rawContent với markdown images tại đúng vị trí
     * @param string $html - HTML content từ Pandoc
     * @param array $imageMapping - Image URLs mapped by position (index)
     * @return string - Raw content với markdown image syntax tại đúng vị trí
     */
    private function convertHtmlToRawWithMarkdownImages($html, $imageMapping)
    {
        if (empty($imageMapping)) {
            // Nếu không có ảnh, chỉ cần strip tags và normalize
            $rawContent = strip_tags($html);
            // Normalize whitespace: nhiều spaces thành 1 space, giữ lại newlines
            $rawContent = preg_replace('/[ \t]+/', ' ', $rawContent);
            return $rawContent;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        
        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            return strip_tags($html);
        }
        
        $imageIndex = 0;
        
        // Thay thế tất cả img tags bằng markdown trước
        $xpath = new \DOMXPath($dom);
        $images = $xpath->query('//img');
        
        foreach ($images as $img) {
            if (isset($imageMapping[$imageIndex])) {
                $url = $imageMapping[$imageIndex]['url'] ?? '';
                if ($url) {
                    // Extract img_key từ filename (bỏ extension)
                    $imgKey = $this->extractImageKeyFromUrl($url);
                    // Tạo text node với markdown image syntax ngắn gọn
                    // Thêm space trước và sau để giữ inline với text, không thêm newline
                    $markdownText = $dom->createTextNode(" [img:{$imgKey}] ");
                    $img->parentNode->replaceChild($markdownText, $img);
                }
                $imageIndex++;
            }
        }
        
        // Lấy HTML sau khi đã thay thế img tags
        $htmlWithMarkdown = '';
        foreach ($body->childNodes as $child) {
            $htmlWithMarkdown .= $dom->saveHTML($child);
        }
        
        // Convert các block elements và line breaks thành newlines TRƯỚC KHI strip_tags
        // Điều này đảm bảo giữ lại xuống dòng từ Word
        $htmlWithMarkdown = preg_replace('/<br\s*\/?>/i', "\n", $htmlWithMarkdown);
        $htmlWithMarkdown = preg_replace('/<\/(p|div|li|h[1-6])>/i', "\n", $htmlWithMarkdown);
        // Convert các block elements mở thành newline (để tách các đoạn)
        $htmlWithMarkdown = preg_replace('/<(p|div|li|h[1-6])([^>]*)>/i', "\n", $htmlWithMarkdown);
        
        // Strip tags để lấy text thuần
        $rawContent = strip_tags($htmlWithMarkdown);
        
        // Normalize whitespace: nhiều spaces thành 1 space, giữ lại newlines
        $rawContent = preg_replace('/[ \t]+/', ' ', $rawContent);
        
        // Loại bỏ nhiều dòng trống liên tiếp (hơn 2 dòng trống)
        $rawContent = preg_replace('/\n{3,}/', "\n\n", $rawContent);
        
        // Loại bỏ dòng trống ở đầu và cuối
        $rawContent = trim($rawContent);
        
        return $rawContent;
    }
    
    /**
     * Build raw content từ DOM nodes, thay thế img tags bằng markdown
     * Giữ nguyên cấu trúc gốc, không thêm dòng trống không cần thiết
     */
    private function buildRawContentFromHtml($node, $imageMapping, &$imageIndex, &$lines)
    {
        if ($node->nodeName === 'img') {
            // Tìm ảnh đã upload trong imageMapping
            if (isset($imageMapping[$imageIndex])) {
                $alt = $node->getAttribute('alt') ?: 'image';
                $url = $imageMapping[$imageIndex]['url'] ?? '';
                if ($url) {
                    // Thêm markdown image syntax trên cùng dòng với text trước đó (nếu có)
                    // hoặc trên dòng mới nếu là block element
                    $lines[] = "![{$alt}]({$url})";
                }
                $imageIndex++;
            }
        } elseif ($node->nodeName === '#text') {
            // Text node - giữ nguyên text, chỉ normalize whitespace
            $text = $node->textContent;
            // Normalize multiple spaces thành single space
            $text = preg_replace('/[ \t]+/', ' ', $text);
            $text = trim($text);
            
            if (!empty($text)) {
                // Nếu dòng cuối cùng không rỗng và không phải là markdown image, append vào dòng đó
                if (!empty($lines) && end($lines) !== '' && !preg_match('/^!\[/', end($lines))) {
                    $lastIndex = count($lines) - 1;
                    $lines[$lastIndex] .= ' ' . $text;
                } else {
                    $lines[] = $text;
                }
            }
        } elseif (in_array($node->nodeName, ['p', 'div'])) {
            // Block elements - chỉ thêm newline sau, không thêm trước
            $childLines = [];
            
            foreach ($node->childNodes as $child) {
                $this->buildRawContentFromHtml($child, $imageMapping, $imageIndex, $childLines);
            }
            
            if (!empty($childLines)) {
                // Merge child lines vào lines chính
                $lines = array_merge($lines, $childLines);
                // Chỉ thêm newline sau nếu dòng cuối không rỗng
                if (end($lines) !== '') {
                    $lines[] = '';
                }
            }
        } elseif (in_array($node->nodeName, ['li'])) {
            // List items - giữ nguyên, không thêm newline thừa
            $childLines = [];
            
            foreach ($node->childNodes as $child) {
                $this->buildRawContentFromHtml($child, $imageMapping, $imageIndex, $childLines);
            }
            
            if (!empty($childLines)) {
                $lines = array_merge($lines, $childLines);
            }
        } elseif (in_array($node->nodeName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
            // Headings - thêm newline sau
            $childLines = [];
            
            foreach ($node->childNodes as $child) {
                $this->buildRawContentFromHtml($child, $imageMapping, $imageIndex, $childLines);
            }
            
            if (!empty($childLines)) {
                // Thêm newline trước heading (trừ dòng đầu tiên)
                if (!empty($lines) && end($lines) !== '') {
                    $lines[] = '';
                }
                $lines = array_merge($lines, $childLines);
                // Thêm newline sau heading
                if (end($lines) !== '') {
                    $lines[] = '';
                }
            }
        } elseif ($node->nodeName === 'br') {
            // Line break - chỉ thêm newline nếu dòng hiện tại không rỗng
            if (!empty($lines) && end($lines) !== '') {
                $lines[] = '';
            }
        } else {
            // Các node khác (span, strong, em, etc.) - chỉ duyệt children, không thêm newline
            foreach ($node->childNodes as $child) {
                $this->buildRawContentFromHtml($child, $imageMapping, $imageIndex, $lines);
            }
        }
    }

    /**
     * Extract img_key từ URL (filename không có extension)
     */
    private function extractImageKeyFromUrl($url)
    {
        if (empty($url)) {
            return '';
        }
        // Lấy filename từ URL, ví dụ: /exam_images/08df36e3-1eb1-4b8a-b3ed-965b8679ecfc.png
        $filename = basename($url);
        // Bỏ extension
        $imgKey = pathinfo($filename, PATHINFO_FILENAME);
        return $imgKey;
    }

    /**
     * Extract markdown images từ content (hỗ trợ cả format cũ và mới)
     * @param string $content - Content có thể chứa: ![alt](url) hoặc [img:key]
     * @return array - Mảng các object ảnh với url
     */
    /**
     * Extract markdown images từ content (hỗ trợ cả format cũ và mới)
     * @param string $content - Content có thể chứa: ![alt](url) hoặc [img:key]
     * @param array $imageMapping - Image mapping từ import Word (optional)
     * @return array - Mảng các object ảnh với url
     */
    private function extractMarkdownImagesFromContent($content, $imageMapping = null)
    {
        $images = [];
        
        // Tạo map img_key -> URL từ imageMapping nếu có
        $keyToUrlMap = [];
        if ($imageMapping && is_array($imageMapping)) {
            foreach ($imageMapping as $imgData) {
                if (isset($imgData['url'])) {
                    $imgKey = $this->extractImageKeyFromUrl($imgData['url']);
                    $keyToUrlMap[$imgKey] = $imgData['url'];
                }
            }
        }
        
        // Pattern để match format mới: [img:key]
        $newPattern = '/\[img:([^\]]+)\]/';
        
        if (preg_match_all($newPattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $imgKey = $match[1];
                // Tìm URL từ imageMapping nếu có
                if (isset($keyToUrlMap[$imgKey])) {
                    $url = $keyToUrlMap[$imgKey];
                } else {
                    // Reconstruct URL từ img_key (giả định extension là png)
                    $url = "/exam_images/{$imgKey}.png";
                }
                $images[] = [
                    'url' => $url,
                    'name' => $imgKey,
                    'type' => 'image',
                ];
            }
        }
        
        // Pattern để match format cũ: ![alt text](url) (backward compatibility)
        $oldPattern = '/!\[([^\]]*)\]\(([^)]+)\)/';
        
        if (preg_match_all($oldPattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $alt = $match[1];
                $url = trim($match[2]);
                
                // Nếu URL có title trong quotes, loại bỏ phần title
                if (preg_match('/^([^\s]+)\s+["\']/', $url, $urlMatch)) {
                    $url = $urlMatch[1];
                } elseif (preg_match('/^["\']([^"\']+)["\']/', $url, $urlMatch)) {
                    $url = $urlMatch[1];
                }
                
                // Chỉ thêm ảnh nếu URL hợp lệ (bắt đầu bằng / hoặc http)
                if (preg_match('/^(\/|https?:\/\/)/', $url)) {
                    $images[] = [
                        'url' => $url,
                        'name' => $alt ?: basename($url),
                        'type' => 'image',
                    ];
                }
            }
        }
        
        return $images;
    }

    /**
     * Xóa folder đệ quy (bao gồm folder con và đề thi trong folder)
     */
    private function deleteFolderRecursive($folderId, $userId, $isSuperAdmin)
    {
        $folderQuery = ExamFolder::where('id', $folderId)
            ->ofType(ExamFolder::TYPE_EXAM);
        
        if (!$isSuperAdmin) {
            $folderQuery->where('owner_id', $userId);
        }
        
        $folder = $folderQuery->first();
        
        if (!$folder) {
            return; // Folder không tồn tại hoặc không có quyền
        }

        // Xóa đệ quy các folder con
        $childFolders = ExamFolder::where('parent_id', $folder->id)
            ->ofType(ExamFolder::TYPE_EXAM)
            ->get();
        
        foreach ($childFolders as $childFolder) {
            $this->deleteFolderRecursive($childFolder->id, $userId, $isSuperAdmin);
        }

        // Xóa tất cả đề thi trong folder này
        $exams = Dethi::where('folder_id', $folder->id)->get();
        foreach ($exams as $exam) {
            $this->deleteDethi($exam->id, $userId, $isSuperAdmin);
        }

        // Xóa folder
        $folder->delete();
    }

    /**
     * Xóa đề thi và tất cả dữ liệu liên quan
     */
    private function deleteDethi($dethiId, $userId, $isSuperAdmin)
    {
        $dethiQuery = Dethi::with(['parts.questions.answers', 'sessions.answers'])
            ->where('id', $dethiId);
        
        if (!$isSuperAdmin) {
            $dethiQuery->where('created_by', $userId);
        }
        
        $dethi = $dethiQuery->first();
        
        if (!$dethi) {
            return; // Đề thi không tồn tại hoặc không có quyền
        }

        // Xóa file audio của từng câu hỏi (nếu có)
        foreach ($dethi->parts as $part) {
            foreach ($part->questions as $question) {
                if ($question->audio) {
                    Storage::disk('public')->delete($question->audio);
                }
            }
        }

        // Xóa file ảnh của các bài làm (exam_answers)
        foreach ($dethi->sessions as $session) {
            foreach ($session->answers as $answer) {
                if ($answer->answer_image) {
                    Storage::disk('public')->delete($answer->answer_image);
                }
            }
        }

        // Xóa các session và answer
        foreach ($dethi->sessions as $session) {
            foreach ($session->answers as $answer) {
                $answer->delete();
            }
            $session->delete();
        }

        // Xóa các câu hỏi, đáp án, part
        foreach ($dethi->parts as $part) {
            foreach ($part->questions as $question) {
                foreach ($question->answers as $ans) {
                    $ans->delete();
                }
                $question->delete();
            }
            $part->delete();
        }

        // Xóa đề thi
        $dethi->delete();
    }

    /**
     * Copy folders và đề thi vào folder đích
     */
    public function bulkCopy(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type != 1 && !$isSuperAdmin){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $data = $request->validate([
            'folder_ids' => ['nullable', 'array'],
            'folder_ids.*' => ['integer', 'exists:exam_folders,id'],
            'dethi_ids' => ['nullable', 'array'],
            'dethi_ids.*' => ['integer', 'exists:dethi,id'],
            'target_folder_id' => ['nullable', 'integer', 'exists:exam_folders,id'],
        ]);

        $folderIds = $data['folder_ids'] ?? [];
        $dethiIds = $data['dethi_ids'] ?? [];
        $targetFolderId = $data['target_folder_id'] ?? null;

        if (empty($folderIds) && empty($dethiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục nào được chọn để copy.'
            ], 400);
        }

        // Kiểm tra target folder
        if ($targetFolderId) {
            $targetFolderQuery = ExamFolder::where('id', $targetFolderId)
                ->ofType(ExamFolder::TYPE_EXAM);
            if (!$isSuperAdmin) {
                $targetFolderQuery->where('owner_id', $profile->id);
            }
            $targetFolder = $targetFolderQuery->first();
            if (!$targetFolder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thư mục đích không tồn tại hoặc bạn không có quyền truy cập.'
                ], 403);
            }

            // Kiểm tra không được copy vào chính nó hoặc folder con
            foreach ($folderIds as $folderId) {
                if ($folderId == $targetFolderId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể copy thư mục vào chính nó.'
                    ], 400);
                }
                // Kiểm tra target folder có phải là con của folder này không
                if ($this->isDescendantFolder($targetFolderId, $folderId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể copy thư mục vào thư mục con của nó.'
                    ], 400);
                }
            }
        }

        DB::beginTransaction();
        try {
            $copiedFolders = 0;
            $copiedDethis = 0;

            // Copy folders (đệ quy)
            if (!empty($folderIds)) {
                foreach ($folderIds as $folderId) {
                    $copied = $this->copyFolderRecursive($folderId, $targetFolderId, $profile->id, $isSuperAdmin);
                    if ($copied) {
                        $copiedFolders++;
                    }
                }
            }

            // Copy đề thi
            if (!empty($dethiIds)) {
                foreach ($dethiIds as $dethiId) {
                    $copied = $this->copyDethi($dethiId, $targetFolderId, $profile->id, $isSuperAdmin);
                    if ($copied) {
                        $copiedDethis++;
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Đã copy thành công: {$copiedFolders} thư mục, {$copiedDethis} đề thi."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk copy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi copy: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lưu danh sách folders và đề thi để cut (paste sau)
     */
    public function bulkCut(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type != 1 && !$isSuperAdmin){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $data = $request->validate([
            'folder_ids' => ['nullable', 'array'],
            'folder_ids.*' => ['integer', 'exists:exam_folders,id'],
            'dethi_ids' => ['nullable', 'array'],
            'dethi_ids.*' => ['integer', 'exists:dethi,id'],
        ]);

        $folderIds = $data['folder_ids'] ?? [];
        $dethiIds = $data['dethi_ids'] ?? [];

        if (empty($folderIds) && empty($dethiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục nào được chọn để cut.'
            ], 400);
        }

        // Kiểm tra quyền truy cập
        if (!empty($folderIds)) {
            foreach ($folderIds as $folderId) {
                $folderQuery = ExamFolder::where('id', $folderId)
                    ->ofType(ExamFolder::TYPE_EXAM);
                if (!$isSuperAdmin) {
                    $folderQuery->where('owner_id', $profile->id);
                }
                $folder = $folderQuery->first();
                if (!$folder) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền cut một số thư mục đã chọn.'
                    ], 403);
                }
            }
        }

        if (!empty($dethiIds)) {
            foreach ($dethiIds as $dethiId) {
                $dethiQuery = Dethi::where('id', $dethiId)
                    ->where('type', 'dethi');
                if (!$isSuperAdmin) {
                    $dethiQuery->where('created_by', $profile->id);
                }
                $dethi = $dethiQuery->first();
                if (!$dethi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền cut một số đề thi đã chọn.'
                    ], 403);
                }
            }
        }

        // Lưu vào session
        session([
            'cut_folders' => $folderIds,
            'cut_dethis' => $dethiIds,
            'cut_user_id' => $profile->id,
            'action_type' => 'cut' // Đánh dấu là cut
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu danh sách để cut. Vui lòng chọn thư mục đích và nhấn Paste.',
            'count' => [
                'folders' => count($folderIds),
                'dethis' => count($dethiIds)
            ]
        ]);
    }

    /**
     * Lưu danh sách folders và đề thi để copy (paste sau)
     */
    public function bulkCopySession(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type != 1 && !$isSuperAdmin){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $data = $request->validate([
            'folder_ids' => ['nullable', 'array'],
            'folder_ids.*' => ['integer', 'exists:exam_folders,id'],
            'dethi_ids' => ['nullable', 'array'],
            'dethi_ids.*' => ['integer', 'exists:dethi,id'],
        ]);

        $folderIds = $data['folder_ids'] ?? [];
        $dethiIds = $data['dethi_ids'] ?? [];

        if (empty($folderIds) && empty($dethiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục nào được chọn để copy.'
            ], 400);
        }

        // Kiểm tra quyền truy cập (cho copy, giáo viên có thể copy đề thi của super admin)
        if (!empty($folderIds)) {
            foreach ($folderIds as $folderId) {
                $folderQuery = ExamFolder::where('id', $folderId)
                    ->ofType(ExamFolder::TYPE_EXAM);
                if (!$isSuperAdmin) {
                    $folderQuery->where('owner_id', $profile->id);
                }
                $folder = $folderQuery->first();
                if (!$folder) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền copy một số thư mục đã chọn.'
                    ], 403);
                }
            }
        }

        if (!empty($dethiIds)) {
            foreach ($dethiIds as $dethiId) {
                $dethiQuery = Dethi::where('id', $dethiId)
                    ->where('type', 'dethi');
                if (!$isSuperAdmin) {
                    // Giáo viên có thể copy đề thi của mình hoặc của super admin
                    $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
                    $dethiQuery->where(function($q) use ($profile, $superAdminIds) {
                        $q->where('created_by', $profile->id);
                        if (!empty($superAdminIds)) {
                            $q->orWhereIn('created_by', $superAdminIds);
                        }
                    });
                }
                $dethi = $dethiQuery->first();
                if (!$dethi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền copy một số đề thi đã chọn.'
                    ], 403);
                }
            }
        }

        // Lưu vào session
        session([
            'copy_folders' => $folderIds,
            'copy_dethis' => $dethiIds,
            'copy_user_id' => $profile->id,
            'action_type' => 'copy' // Đánh dấu là copy
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã copy. Vui lòng chọn thư mục đích và nhấn Paste.',
            'count' => [
                'folders' => count($folderIds),
                'dethis' => count($dethiIds)
            ]
        ]);
    }

    /**
     * Paste (move) folders và đề thi đã cut vào folder đích
     */
    public function bulkPaste(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;
        
        if($profile->type != 1 && !$isSuperAdmin){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $data = $request->validate([
            'target_folder_id' => ['nullable', 'integer', 'exists:exam_folders,id'],
        ]);

        $targetFolderId = $data['target_folder_id'] ?? null;

        // Lấy từ session (kiểm tra cả cut và copy)
        $actionType = session('action_type', 'cut'); // Mặc định là cut
        $folderIds = [];
        $dethiIds = [];
        $userId = null;

        if ($actionType === 'copy') {
            $folderIds = session('copy_folders', []);
            $dethiIds = session('copy_dethis', []);
            $userId = session('copy_user_id');
        } else {
            $folderIds = session('cut_folders', []);
            $dethiIds = session('cut_dethis', []);
            $userId = session('cut_user_id');
        }

        // Kiểm tra session
        if ($userId != $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Session đã hết hạn hoặc không hợp lệ.'
            ], 400);
        }

        if (empty($folderIds) && empty($dethiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục nào để paste. Vui lòng chọn và copy/cut lại.'
            ], 400);
        }

        // Kiểm tra target folder
        if ($targetFolderId) {
            $targetFolderQuery = ExamFolder::where('id', $targetFolderId)
                ->ofType(ExamFolder::TYPE_EXAM);
            if (!$isSuperAdmin) {
                $targetFolderQuery->where('owner_id', $profile->id);
            }
            $targetFolder = $targetFolderQuery->first();
            if (!$targetFolder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thư mục đích không tồn tại hoặc bạn không có quyền truy cập.'
                ], 403);
            }

            // Kiểm tra không được paste vào chính nó hoặc folder con
            if (in_array($targetFolderId, $folderIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể paste thư mục vào chính nó.'
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            if ($actionType === 'copy') {
                // Copy folders và đề thi
                $copiedFolders = 0;
                $copiedDethis = 0;

                if (!empty($folderIds)) {
                    foreach ($folderIds as $folderId) {
                        $copied = $this->copyFolderRecursive($folderId, $targetFolderId, $profile->id, $isSuperAdmin);
                        if ($copied) {
                            $copiedFolders++;
                        }
                    }
                }

                if (!empty($dethiIds)) {
                    foreach ($dethiIds as $dethiId) {
                        $copied = $this->copyDethi($dethiId, $targetFolderId, $profile->id, $isSuperAdmin);
                        if ($copied) {
                            $copiedDethis++;
                        }
                    }
                }

                // Xóa session copy
                session()->forget(['copy_folders', 'copy_dethis', 'copy_user_id', 'action_type']);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Đã paste thành công: {$copiedFolders} thư mục, {$copiedDethis} đề thi."
                ]);
            } else {
                // Move folders và đề thi (cut) - giữ nguyên tên
                $movedFolders = 0;
                $movedDethis = 0;

                if (!empty($folderIds)) {
                    foreach ($folderIds as $folderId) {
                        $moved = $this->moveFolder($folderId, $targetFolderId, $profile->id, $isSuperAdmin);
                        if ($moved) {
                            $movedFolders++;
                        }
                    }
                }

                if (!empty($dethiIds)) {
                    foreach ($dethiIds as $dethiId) {
                        $moved = $this->moveDethi($dethiId, $targetFolderId, $profile->id, $isSuperAdmin);
                        if ($moved) {
                            $movedDethis++;
                        }
                    }
                }

                // Xóa session cut
                session()->forget(['cut_folders', 'cut_dethis', 'cut_user_id', 'action_type']);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Đã paste thành công: {$movedFolders} thư mục, {$movedDethis} đề thi."
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk paste error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi paste: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra xem folder có phải là con của folder khác không
     */
    private function isDescendantFolder($folderId, $ancestorId)
    {
        $current = ExamFolder::find($folderId);
        if (!$current) {
            return false;
        }

        while ($current->parent_id) {
            if ($current->parent_id == $ancestorId) {
                return true;
            }
            $current = ExamFolder::find($current->parent_id);
            if (!$current) {
                break;
            }
        }

        return false;
    }

    /**
     * Copy folder đệ quy (bao gồm folder con và đề thi)
     */
    private function copyFolderRecursive($folderId, $targetFolderId, $userId, $isSuperAdmin)
    {
        $folderQuery = ExamFolder::where('id', $folderId)
            ->ofType(ExamFolder::TYPE_EXAM);
        
        if (!$isSuperAdmin) {
            $folderQuery->where('owner_id', $userId);
        }
        
        $folder = $folderQuery->first();
        
        if (!$folder) {
            return false;
        }

        // Tạo folder mới
        $newFolder = $folder->replicate();
        $newFolder->parent_id = $targetFolderId;
        $newFolder->owner_id = $userId; // Folder mới thuộc về user hiện tại

        // Đảm bảo slug mới không trùng (để boot() tự generate lại)
        $newFolder->slug = null;

        // Đảm bảo không trùng name trong cùng parent/type (do unique(parent_id, name, type))
        $baseName = $folder->name;
        $name = $baseName;
        $index = 1;

        while (
            ExamFolder::where('parent_id', $targetFolderId)
                ->where('type', ExamFolder::TYPE_EXAM)
                ->where('name', $name)
                ->exists()
        ) {
            $suffix = $index === 1 ? ' - Copy' : " - Copy {$index}";
            $name = $baseName . $suffix;
            $index++;
        }

        $newFolder->name = $name;

        $newFolder->save();

        // Copy các đề thi trong folder
        $exams = Dethi::where('folder_id', $folder->id)->get();
        foreach ($exams as $exam) {
            $this->copyDethi($exam->id, $newFolder->id, $userId, $isSuperAdmin);
        }

        // Copy đệ quy các folder con
        $childFolders = ExamFolder::where('parent_id', $folder->id)
            ->ofType(ExamFolder::TYPE_EXAM)
            ->get();
        
        foreach ($childFolders as $childFolder) {
            $this->copyFolderRecursive($childFolder->id, $newFolder->id, $userId, $isSuperAdmin);
        }

        return true;
    }

    /**
     * Copy đề thi (clone toàn bộ parts, questions, answers)
     */
    private function copyDethi($dethiId, $targetFolderId, $userId, $isSuperAdmin)
    {
        $dethiQuery = Dethi::with(['parts.questions.answers', 'allowedClasses'])
            ->where('id', $dethiId)
            ->where('type', 'dethi');
        
        if (!$isSuperAdmin) {
            // Giáo viên có thể copy đề thi của mình hoặc của super admin
            $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
            $dethiQuery->where(function($q) use ($userId, $superAdminIds) {
                $q->where('created_by', $userId);
                if (!empty($superAdminIds)) {
                    $q->orWhereIn('created_by', $superAdminIds);
                }
            });
        }
        
        $dethi = $dethiQuery->first();
        
        if (!$dethi) {
            return false;
        }

        // Clone đề thi
        $newDethi = $dethi->replicate();
        $newDethi->created_by = $userId; // Đề thi mới thuộc về user hiện tại
        $newDethi->folder_id = $targetFolderId;
        $newDethi->status = 0; // Mặc định là draft

        // Đổi tên đề thi mới: thêm hậu tố \" - Copy\" và đảm bảo không trùng trong cùng folder
        $baseTitle = $dethi->title;
        $title = $baseTitle . ' - Copy';
        $index = 2;

        while (
            Dethi::where('folder_id', $targetFolderId)
                ->where('type', 'dethi')
                ->where('title', $title)
                ->exists()
        ) {
            $title = $baseTitle . " - Copy {$index}";
            $index++;
        }

        $newDethi->title = $title;
        $newDethi->save();

        // Clone các parts
        foreach ($dethi->parts as $originalPart) {
            $newPart = $originalPart->replicate();
            $newPart->dethi_id = $newDethi->id;
            $newPart->save();

            // Clone các questions
            foreach ($originalPart->questions as $originalQuestion) {
                $newQuestion = $originalQuestion->replicate();
                $newQuestion->de_thi_id = $newDethi->id;
                $newQuestion->dethi_part_id = $newPart->id;
                $newQuestion->save();

                // Clone các answers
                foreach ($originalQuestion->answers as $originalAnswer) {
                    $newAnswer = $originalAnswer->replicate();
                    $newAnswer->dethi_question_id = $newQuestion->id;
                    $newAnswer->de_thi_id = $newDethi->id;
                    $newAnswer->save();
                }
            }
        }

        // Không copy allowedClasses - để user tự cấu hình lại

        return true;
    }

    /**
     * Move folder (chỉ đổi parent_id)
     */
    private function moveFolder($folderId, $targetFolderId, $userId, $isSuperAdmin)
    {
        $folderQuery = ExamFolder::where('id', $folderId)
            ->ofType(ExamFolder::TYPE_EXAM);
        
        if (!$isSuperAdmin) {
            $folderQuery->where('owner_id', $userId);
        }
        
        $folder = $folderQuery->first();
        
        if (!$folder) {
            return false;
        }

        // Kiểm tra không được move vào chính nó
        if ($targetFolderId == $folderId) {
            return false;
        }

        // Kiểm tra không được move vào folder con
        if ($targetFolderId && $this->isDescendantFolder($targetFolderId, $folderId)) {
            return false;
        }

        // Nếu đổi parent_id và trong thư mục đích đã có folder trùng tên + type,
        // thì tự động đổi tên để tránh lỗi unique (parent_id, name, type)
        if ($folder->parent_id !== $targetFolderId) {
            $baseName = $folder->name;
            $name = $baseName;
            $index = 1;

            while (
                ExamFolder::where('parent_id', $targetFolderId)
                    ->where('type', ExamFolder::TYPE_EXAM)
                    ->where('name', $name)
                    ->where('id', '!=', $folder->id)
                    ->exists()
            ) {
                $suffix = $index === 1 ? ' - Copy' : " - Copy {$index}";
                $name = $baseName . $suffix;
                $index++;
            }

            $folder->name = $name;
        }

        $folder->parent_id = $targetFolderId;
        $folder->save();

        return true;
    }

    /**
     * Move đề thi (chỉ đổi folder_id)
     */
    private function moveDethi($dethiId, $targetFolderId, $userId, $isSuperAdmin)
    {
        $dethiQuery = Dethi::where('id', $dethiId)
            ->where('type', 'dethi');
        
        if (!$isSuperAdmin) {
            $dethiQuery->where('created_by', $userId);
        }
        
        $dethi = $dethiQuery->first();
        
        if (!$dethi) {
            return false;
        }

        $dethi->folder_id = $targetFolderId;
        $dethi->save();

        return true;
    }

    /**
     * Clone đề thi từ super admin về thư mục gốc của giáo viên
     */
    public function cloneExam($id)
    {
        $profile = Auth::guard("customer")->user();
        
        // Chỉ giáo viên mới có thể clone
        if ($profile->type != 1) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này');
        }

        // Lấy đề thi gốc với đầy đủ relationships
        $originalExam = Dethi::with(['parts.questions.answers', 'allowedClasses'])
            ->where('type', 'dethi')
            ->find($id);

        if (!$originalExam) {
            return redirect()->back()->with('error', 'Đề thi không tồn tại');
        }

        // Kiểm tra xem đề thi này có thuộc về super admin không
        $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
        if (!in_array($originalExam->created_by, $superAdminIds)) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể tải đề thi từ super admin');
        }

        try {
            DB::beginTransaction();

            // Clone đề thi
            $newExam = $originalExam->replicate();
            $newExam->created_by = $profile->id;
            $newExam->folder_id = null; // Đặt vào thư mục gốc
            $newExam->status = 0; // Có thể đặt status mặc định là draft
            $newExam->save();

            // Clone các parts
            $partMapping = []; // Lưu mapping giữa old_part_id và new_part_id
            foreach ($originalExam->parts as $originalPart) {
                $newPart = $originalPart->replicate();
                $newPart->dethi_id = $newExam->id;
                $newPart->save();
                $partMapping[$originalPart->id] = $newPart->id;

                // Clone các questions
                $questionMapping = []; // Lưu mapping giữa old_question_id và new_question_id
                foreach ($originalPart->questions as $originalQuestion) {
                    $newQuestion = $originalQuestion->replicate();
                    $newQuestion->de_thi_id = $newExam->id;
                    $newQuestion->dethi_part_id = $newPart->id;
                    $newQuestion->save();
                    $questionMapping[$originalQuestion->id] = $newQuestion->id;

                    // Clone các answers
                    foreach ($originalQuestion->answers as $originalAnswer) {
                        $newAnswer = $originalAnswer->replicate();
                        $newAnswer->dethi_question_id = $newQuestion->id;
                        $newAnswer->de_thi_id = $newExam->id;
                        $newAnswer->save();
                    }
                }
            }

            // Clone allowed classes (nếu có) - giáo viên có thể giữ lại hoặc xóa
            // Ở đây tôi sẽ không clone để giáo viên tự cấu hình lại
            // foreach ($originalExam->allowedClasses as $allowedClass) {
            //     // Kiểm tra xem lớp này có thuộc về giáo viên không trước khi clone
            // }

            DB::commit();

            return redirect()->route('khoiTaoDeThi')->with('success', 'Đã tải đề thi thành công về thư mục gốc của bạn');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Clone exam error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải đề thi. Vui lòng thử lại.');
        }
    }

    /**
     * Lấy nội dung đề thi để hiển thị trong popup
     */
    public function getExamContent($id)
    {
        $profile = Auth::guard("customer")->user();
        
        // Chỉ giáo viên mới có thể xem nội dung đề thi từ super admin
        if ($profile->type != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập'
            ], 403);
        }

        // Kiểm tra đề thi có tồn tại và thuộc về super admin không
        $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
        $exam = Dethi::with(['parts.questions.answers', 'customer'])
            ->where('type', 'dethi')
            ->find($id);

        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Đề thi không tồn tại'
            ], 404);
        }

        // Kiểm tra đề thi có thuộc về super admin không
        if (!in_array($exam->created_by, $superAdminIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể xem đề thi từ super admin'
            ], 403);
        }

        // Format dữ liệu để trả về
        $partsData = [];
        foreach ($exam->parts as $index => $part) {
            $questionsData = [];
            foreach ($part->questions as $question) {
                $answersData = [];
                foreach ($question->answers as $answer) {
                    $answersData[] = [
                        'label' => $answer->label,
                        'content' => $answer->content,
                        'is_correct' => $answer->is_correct,
                    ];
                }

                // Xác định tất cả đáp án đúng
                $correct_answers = [];
                foreach ($question->answers as $ans) {
                    if (!empty($ans->is_correct)) {
                        $correct_answers[] = $ans->label;
                    }
                }
                // Giữ lại backward compatibility với correct_answer (đáp án đầu tiên nếu có)
                $correct_answer = !empty($correct_answers) ? $correct_answers[0] : null;

                $questionsData[] = [
                    'question_no' => $question->question_no,
                    'content' => $question->content,
                    'question_type' => $question->question_type,
                    'score' => $question->score,
                    'audio' => $question->audio,
                    'explanation' => $question->explanation,
                    'image' => $question->image,
                    'answers' => $answersData,
                    'correct_answer' => $correct_answer, // Giữ lại cho backward compatibility
                    'correct_answers' => $correct_answers, // Tất cả đáp án đúng
                ];
            }

            $partsData[] = [
                'part' => 'PHẦN ' . ($index + 1),
                'part_title' => $part->part_title,
                'questions' => $questionsData,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $exam->title,
                'time' => $exam->time,
                'description' => $exam->description,
                'parts' => $partsData,
            ]
        ]);
    }

    /**
     * Export đề thi ra file Word có đáp án đúng và lời giải
     * Loại 2: Câu hỏi + Đáp án + Đáp án đúng (đánh dấu) + Lời giải
     */
    public function exportWordWithAnswer($id)
    {
        try {
            $dethi = Dethi::with(['parts.questions.answers'])
                ->where('type', 'dethi')
                ->findOrFail($id);

            return $this->generateWordDocument($dethi, true);
        } catch (\Exception $e) {
            Log::error('Export Word with answer error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xuất file Word: ' . $e->getMessage());
        }
    }

    /**
     * Export đề thi ra file Word chỉ có câu hỏi và đáp án
     * Loại 1: Chỉ xuất Câu hỏi + Đáp án (không đánh dấu đáp án đúng, không có lời giải)
     */
    public function exportWordWithoutAnswer($id)
    {
        try {
            $dethi = Dethi::with(['parts.questions.answers'])
                ->where('type', 'dethi')
                ->findOrFail($id);

            return $this->generateWordDocument($dethi, false);
        } catch (\Exception $e) {
            Log::error('Export Word without answer error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xuất file Word: ' . $e->getMessage());
        }
    }

    /**
     * Tạo file Word từ dữ liệu đề thi
     * Sử dụng Pandoc để convert HTML (có MathML) sang Word để đảm bảo công thức toán học đúng định dạng
     */
    private function generateWordDocument($dethi, $includeAnswers = true)
    {
        try {
            // Tạo HTML content với MathML
            $htmlContent = $this->generateHtmlContent($dethi, $includeAnswers);
            
            // Convert HTML sang Word bằng Pandoc
            // includeAnswers = true: Câu hỏi + Đáp án + Đáp án đúng + Lời giải
            // includeAnswers = false: Chỉ Câu hỏi + Đáp án
            $filename = 'De_thi_' . Str::slug($dethi->title) . '_' . ($includeAnswers ? 'co_dap_an_dung_va_loi_giai' : 'chi_de_thi_va_dap_an') . '.docx';
            $tempDir = storage_path('app/temp/export');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $tempHtmlFile = $tempDir . '/' . uniqid('export_') . '.html';
            $tempWordFile = $tempDir . '/' . uniqid('export_') . '.docx';

            // Lưu HTML tạm
            file_put_contents($tempHtmlFile, $htmlContent);

            // Sử dụng Pandoc để convert HTML sang Word với MathML
            $process = new Process([
                'pandoc',
                $tempHtmlFile,
                '-f', 'html',
                '-t', 'docx',
                '--mathml',  // Giữ MathML trong Word
                '--wrap=none',
                '-o', $tempWordFile
            ]);

            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \Exception('Pandoc conversion failed: ' . $process->getErrorOutput());
            }

            if (!file_exists($tempWordFile)) {
                throw new \Exception('Word file was not created');
            }

            // Xóa file HTML tạm
            @unlink($tempHtmlFile);

            // Download file
            return response()->download($tempWordFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Generate Word document error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tạo HTML content từ dữ liệu đề thi với MathML
     */
    private function generateHtmlContent($dethi, $includeAnswers = true)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 2cm;
        }
        h1 {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .question {
            margin-bottom: 15px;
        }
        .question-number {
            font-weight: bold;
        }
        .answer {
            margin-left: 20px;
            margin-bottom: 5px;
        }
        .answer-correct {
            font-weight: bold;
        }
        .explanation {
            margin-top: 10px;
            font-style: italic;
            margin-left: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
        }
    </style>
</head>
<body>';

        // Tiêu đề đề thi
        $html .= '<h1>' . htmlspecialchars($dethi->title) . '</h1>';

        // Thông tin đề thi
        if ($dethi->description) {
            $html .= '<p>' . nl2br(htmlspecialchars($dethi->description)) . '</p>';
        }

        if ($dethi->time) {
            $html .= '<p><strong>Thời gian:</strong> ' . $dethi->time . ' phút</p>';
        }

        $html .= '<hr>';

        // Duyệt qua các phần
        $questionNumber = 1;
        foreach ($dethi->parts as $partIndex => $part) {
            // Tiêu đề phần
            if ($part->part_title) {
                $html .= '<h2>PHẦN ' . ($partIndex + 1) . ': ' . htmlspecialchars($part->part_title) . '</h2>';
            }

            // Duyệt qua các câu hỏi
            foreach ($part->questions as $question) {
                $html .= '<div class="question">';
                
                // Số câu hỏi và nội dung (ảnh sẽ được chèn vào đúng vị trí markdown trong nội dung)
                $html .= '<p><span class="question-number">Câu ' . $questionNumber . '.</span> ';
                $html .= $this->processContentWithMathForHtml($question->content, $question);
                $html .= '</p>';

                // Đáp án - luôn hiển thị
                if (!empty($question->answers)) {
                    foreach ($question->answers as $answer) {
                        // Chỉ đánh dấu đáp án đúng khi includeAnswers = true
                        $answerClass = ($includeAnswers && $answer->is_correct) ? 'answer answer-correct' : 'answer';
                        $html .= '<div class="' . $answerClass . '">';
                        
                        // Xử lý prefix theo loại câu hỏi khi includeAnswers = true
                        $prefix = '';
                        if ($includeAnswers) {
                            if ($question->question_type === 'multiple_choice') {
                                // Multiple choice: thêm * trước đáp án đúng
                                if ($answer->is_correct) {
                                    $prefix = '<strong>*</strong> ';
                                }
                            } elseif ($question->question_type === 'true_false_grouped') {
                                // True/False: thêm [Đúng] hoặc [Sai]
                                if ($answer->is_correct) {
                                    $prefix = '<strong>[Đúng]</strong> ';
                                } else {
                                    $prefix = '<strong>[Sai]</strong> ';
                                }
                            }
                        }
                        
                        $html .= $prefix;
                        $html .= '<strong>' . htmlspecialchars($answer->label) . '.</strong> ';
                        $html .= $this->processContentWithMathForHtml($answer->content, $question);
                        $html .= '</div>';
                    }
                }

                // Lời giải - chỉ hiển thị khi includeAnswers = true
                if ($includeAnswers && $question->explanation) {
                    $html .= '<div class="explanation">';
                    $html .= '<strong>Lời giải:</strong> ';
                    $html .= $this->processContentWithMathForHtml($question->explanation, $question);
                    $html .= '</div>';
                }

                $html .= '</div>';
                $questionNumber++;
            }

            $html .= '<br>';
        }

        $html .= '</body>
</html>';

        return $html;
    }

    /**
     * Xử lý nội dung có công thức LaTeX và markdown images, chuyển sang MathML cho HTML
     */
    private function processContentWithMathForHtml($content, $question = null)
    {
        if (empty($content)) {
            return '';
        }

        $processedContent = $content;
        $mathReplacements = [];
        
        // Bước 1: Xử lý markdown images [img:key] trước (trước khi escape HTML)
        $processedContent = $this->processMarkdownImages($processedContent, $question);
        
        // Bước 2: Tìm và thay thế LaTeX trước khi escape HTML
        // Hỗ trợ: \(...\), \[...\], $$...$$, $...$
        // Sử dụng non-greedy và xử lý nested parentheses
        
        // Pattern 1: \(...\) - cần match cả nested parentheses
        $processedContent = preg_replace_callback(
            '/\\\\\(((?:[^\\\\]|\\\\.)*?)\\\\\)/s',
            function($matches) use (&$mathReplacements) {
                $latex = $matches[1];
                // Unescape các ký tự đặc biệt đã được escape
                $latex = str_replace(['\\(', '\\)'], ['(', ')'], $latex);
                $mathml = $this->convertLatexToMathML($latex);
                $placeholder = '___MATHML_' . count($mathReplacements) . '___';
                $mathReplacements[$placeholder] = $mathml ? $mathml : '<math xmlns="http://www.w3.org/1998/Math/MathML"><mi>' . htmlspecialchars($latex) . '</mi></math>';
                return $placeholder;
            },
            $processedContent
        );

        // Pattern 2: \[...\]
        $processedContent = preg_replace_callback(
            '/\\\\\[((?:[^\\\\]|\\\\.)*?)\\\\\]/s',
            function($matches) use (&$mathReplacements) {
                $latex = $matches[1];
                $mathml = $this->convertLatexToMathML($latex);
                $placeholder = '___MATHML_' . count($mathReplacements) . '___';
                $mathReplacements[$placeholder] = $mathml ? $mathml : '<math xmlns="http://www.w3.org/1998/Math/MathML"><mi>' . htmlspecialchars($latex) . '</mi></math>';
                return $placeholder;
            },
            $processedContent
        );

        // Pattern 3: $$...$$
        $processedContent = preg_replace_callback(
            '/\$\$((?:[^$]|\\$)*?)\$\$/s',
            function($matches) use (&$mathReplacements) {
                $latex = $matches[1];
                $mathml = $this->convertLatexToMathML($latex);
                $placeholder = '___MATHML_' . count($mathReplacements) . '___';
                $mathReplacements[$placeholder] = $mathml ? $mathml : '<math xmlns="http://www.w3.org/1998/Math/MathML"><mi>' . htmlspecialchars($latex) . '</mi></math>';
                return $placeholder;
            },
            $processedContent
        );

        // Pattern 4: $...$ (inline math, cẩn thận với $ trong text)
        // Chỉ match nếu không phải là $$ (đã xử lý ở trên)
        $processedContent = preg_replace_callback(
            '/(?<!\$)\$(?!\$)((?:[^$]|\\$)+?)\$(?!\$)/',
            function($matches) use (&$mathReplacements) {
                $latex = $matches[1];
                $mathml = $this->convertLatexToMathML($latex);
                $placeholder = '___MATHML_' . count($mathReplacements) . '___';
                $mathReplacements[$placeholder] = $mathml ? $mathml : '<math xmlns="http://www.w3.org/1998/Math/MathML"><mi>' . htmlspecialchars($latex) . '</mi></math>';
                return $placeholder;
            },
            $processedContent
        );

        // Escape HTML (trừ MathML placeholders và img tags đã được tạo)
        // Tách nội dung thành các phần để escape an toàn
        $parts = preg_split('/(___MATHML_\d+___|<img[^>]*>)/', $processedContent, -1, PREG_SPLIT_DELIM_CAPTURE);
        $processedContent = '';
        foreach ($parts as $part) {
            if (preg_match('/^___MATHML_\d+___$/', $part) || preg_match('/^<img[^>]*>$/', $part)) {
                // Giữ nguyên MathML placeholder và img tags
                $processedContent .= $part;
            } else {
                // Escape HTML cho phần còn lại
                $processedContent .= htmlspecialchars($part, ENT_QUOTES, 'UTF-8');
            }
        }

        // Thay thế placeholders bằng MathML thực tế
        foreach ($mathReplacements as $placeholder => $mathml) {
            $processedContent = str_replace($placeholder, $mathml, $processedContent);
        }

        // Xử lý các thẻ HTML khác (br, etc.)
        $processedContent = $this->processHtmlTags($processedContent);

        return $processedContent;
    }

    /**
     * Xử lý markdown images [img:key] trong nội dung
     */
    private function processMarkdownImages($content, $question = null)
    {
        if (empty($content)) {
            return $content;
        }

        // Tạo map từ img_key sang URL từ question->image
        $keyToUrlMap = [];
        if ($question && $question->image) {
            $images = json_decode($question->image, true);
            if (is_array($images)) {
                foreach ($images as $imageItem) {
                    $imgKey = null;
                    $imgUrl = null;
                    
                    if (is_array($imageItem) && isset($imageItem['url'])) {
                        $imgUrl = $imageItem['url'];
                        // Extract key từ URL hoặc từ server_filename
                        if (isset($imageItem['server_filename'])) {
                            $imgKey = pathinfo($imageItem['server_filename'], PATHINFO_FILENAME);
                        } else {
                            $imgKey = $this->extractImageKeyFromUrl($imgUrl);
                        }
                    } elseif (is_string($imageItem)) {
                        $imgUrl = $imageItem;
                        $imgKey = $this->extractImageKeyFromUrl($imgUrl);
                    }
                    
                    if (!empty($imgKey) && $imgUrl) {
                        $keyToUrlMap[$imgKey] = $imgUrl;
                    }
                }
            } elseif (is_string($question->image)) {
                $imgKey = $this->extractImageKeyFromUrl($question->image);
                if (!empty($imgKey)) {
                    $keyToUrlMap[$imgKey] = $question->image;
                }
            }
        }

        // Pattern để match format mới: [img:key]
        $newPattern = '/\[img:([^\]]+)\]/';
        $processedContent = preg_replace_callback($newPattern, function($matches) use ($keyToUrlMap) {
            $imgKey = $matches[1];
            $imgUrl = null;
            
            // Tìm URL từ map
            if (isset($keyToUrlMap[$imgKey])) {
                $imgUrl = $keyToUrlMap[$imgKey];
            } else {
                // Fallback: reconstruct URL từ img_key
                $imgUrl = "/exam_images/{$imgKey}.png";
            }
            
            return $this->getImageHtml($imgUrl);
        }, $content);

        // Pattern để match format cũ: ![alt](url) (backward compatibility)
        $oldPattern = '/!\[([^\]]*)\]\(([^)]+)\)/';
        $processedContent = preg_replace_callback($oldPattern, function($matches) {
            $alt = $matches[1] ?: 'Image';
            $url = trim($matches[2], '"\'');
            return $this->getImageHtml($url);
        }, $processedContent);

        return $processedContent;
    }

    /**
     * Xử lý các thẻ HTML trong nội dung (img, br, etc.)
     */
    private function processHtmlTags($content)
    {
        // Xử lý [img:key] thành img tag (trước khi escape)
        // Note: Đã được xử lý trong generateHtmlContent, không cần xử lý lại ở đây
        
        // Cho phép một số thẻ HTML an toàn (bao gồm math tag)
        $allowedTags = '<p><br><strong><em><u><img><span><div><math><mi><mo><mn><mfrac><msup><msub><mover><munder><mroot><msqrt><mtable><mtr><mtd><mrow><mtext>';
        
        // Không dùng strip_tags vì sẽ mất MathML, chỉ cần đảm bảo an toàn
        // MathML đã được tạo từ LaTeX nên an toàn
        return $content;
    }

    /**
     * Lấy HTML cho ảnh
     */
    private function getImageHtml($imageUrl)
    {
        // Kiểm tra nếu là array thì lấy phần tử đầu tiên hoặc return rỗng
        if (is_array($imageUrl)) {
            if (empty($imageUrl)) {
                return '';
            }
            $imageUrl = is_string($imageUrl[0]) ? $imageUrl[0] : (string)$imageUrl[0];
        }

        // Đảm bảo là string
        if (!is_string($imageUrl)) {
            $imageUrl = (string)$imageUrl;
        }

        // Xử lý URL ảnh
        if (!empty($imageUrl) && strpos($imageUrl, 'http') === 0) {
            $src = $imageUrl;
        } else {
            $src = url($imageUrl);
        }

        return '<img src="' . htmlspecialchars($src) . '" alt="Question Image" style="max-width: 100%; height: auto; margin: 10px 0;" />';
    }

    /**
     * Chuyển đổi LaTeX sang MathML bằng Pandoc
     */
    private function convertLatexToMathML($latex)
    {
        try {
            // Làm sạch LaTeX
            $latex = trim($latex);
            if (empty($latex)) {
                return null;
            }

            // Escape các ký tự đặc biệt trong LaTeX để tránh lỗi
            // Giữ nguyên các ký tự LaTeX hợp lệ như \infty, \frac, etc.
            $latex = $this->sanitizeLatex($latex);

            // Tạo file tạm chứa LaTeX
            $tempDir = storage_path('app/temp/math');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $tempLatexFile = $tempDir . '/' . uniqid('latex_') . '.tex';
            $tempMathMLFile = $tempDir . '/' . uniqid('mathml_') . '.html';

            // Tạo file LaTeX với inline math
            $latexContent = '\documentclass{article}
\usepackage{amsmath}
\usepackage{amssymb}
\usepackage{amsfonts}
\begin{document}
$' . $latex . '$
\end{document}';
            file_put_contents($tempLatexFile, $latexContent);

            // Sử dụng Pandoc để convert LaTeX sang HTML với MathML
            $process = new Process([
                'pandoc',
                $tempLatexFile,
                '-f', 'latex',
                '-t', 'html',
                '--mathml',  // Output MathML
                '--wrap=none',
                '-o', $tempMathMLFile
            ]);

            $process->setTimeout(15);
            $process->run();

            if ($process->isSuccessful() && file_exists($tempMathMLFile)) {
                $html = file_get_contents($tempMathMLFile);
                
                // Extract MathML từ HTML - tìm tất cả các thẻ math
                if (preg_match('/<math[^>]*>.*?<\/math>/s', $html, $matches)) {
                    $mathml = $matches[0];
                    
                    // Xóa file tạm
                    @unlink($tempLatexFile);
                    @unlink($tempMathMLFile);

                    return $mathml;
                }
            }

            // Nếu không thành công với inline, thử display math
            $latexContent = '\documentclass{article}
\usepackage{amsmath}
\usepackage{amssymb}
\usepackage{amsfonts}
\begin{document}
\[ ' . $latex . ' \]
\end{document}';
            file_put_contents($tempLatexFile, $latexContent);

            $process = new Process([
                'pandoc',
                $tempLatexFile,
                '-f', 'latex',
                '-t', 'html',
                '--mathml',
                '--wrap=none',
                '-o', $tempMathMLFile
            ]);

            $process->setTimeout(15);
            $process->run();

            if ($process->isSuccessful() && file_exists($tempMathMLFile)) {
                $html = file_get_contents($tempMathMLFile);
                
                if (preg_match('/<math[^>]*>.*?<\/math>/s', $html, $matches)) {
                    $mathml = $matches[0];
                    
                    @unlink($tempLatexFile);
                    @unlink($tempMathMLFile);

                    return $mathml;
                }
            }

            // Xóa file tạm nếu lỗi
            @unlink($tempLatexFile);
            @unlink($tempMathMLFile);

            // Fallback: tạo MathML đơn giản từ LaTeX
            return $this->createSimpleMathML($latex);
        } catch (\Exception $e) {
            Log::error('Convert LaTeX to MathML error: ' . $e->getMessage() . ' | LaTeX: ' . $latex);
            // Fallback
            return $this->createSimpleMathML($latex);
        }
    }

    /**
     * Làm sạch LaTeX trước khi convert
     */
    private function sanitizeLatex($latex)
    {
        // Loại bỏ các ký tự không hợp lệ nhưng giữ lại các ký tự LaTeX hợp lệ
        // Không cần escape quá nhiều vì Pandoc sẽ xử lý
        return $latex;
    }

    /**
     * Tạo MathML đơn giản từ LaTeX (fallback)
     */
    private function createSimpleMathML($latex)
    {
        // Escape HTML trong LaTeX
        $escapedLatex = htmlspecialchars($latex, ENT_QUOTES, 'UTF-8');
        
        // Tạo MathML đơn giản
        return '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mi>' . $escapedLatex . '</mi></mrow></math>';
    }

}
