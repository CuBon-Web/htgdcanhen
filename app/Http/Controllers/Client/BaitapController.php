<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\models\dethi\Dethi;
use App\models\dethi\DethiPart;
use App\models\dethi\DethiQuestion;
use App\models\dethi\ExamAnswer;
use App\models\dethi\ExamSession;
use App\models\dethi\DethiAnswer;
use App\models\dethi\DethiClass;
use App\models\dethi\ExamFolder;
use App\models\Quiz\QuizCategory;
use App\models\Quiz\CategoryMain;
use App\models\SchoolClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Auth;
use Illuminate\Support\Facades\Storage;

class BaitapController extends Controller
{
    public function index(Request $request){
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
            $folderType = ExamFolder::TYPE_EXERCISE;
            $isSuperAdmin = $profile->type == 3;
            // Giáo viên (type 1) cần thấy thêm folder/bài tập của super admin
            $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
            $teacherOwnerIds = $isSuperAdmin ? null : array_merge([$profile->id], $superAdminIds);

            $includeSuperAdminFolders = $isSuperAdmin || $profile->type == 1;
            $folderTree = ExamFolder::treeForOwner(
                $profile->id,
                $folderType,
                $isSuperAdmin,
                $includeSuperAdminFolders
            );
            $folderOptions = $this->flattenFolderOptions($folderTree);
            $folderParam = $request->input('folder_id');
            $selectedFolderKey = 'root';
            $activeFolder = null;
            $folderBreadcrumb = collect();
            $childFolders = collect();
            $currentFolderId = null;
            
            $query = Dethi::with(['sessions', 'course', 'folder'])
                ->where('type','baitap')
                ->orderBy('id','DESC');

            if ($isSuperAdmin) {
                // super admin xem tất cả
            } else {
                $query->whereIn('created_by', $teacherOwnerIds);
            }

            if(empty($folderParam) || $folderParam === 'root'){
                $selectedFolderKey = 'root';
                $query->whereNull('folder_id');
            }elseif(is_numeric($folderParam)){
                $activeFolder = ExamFolder::ofType($folderType)
                    ->when(!$isSuperAdmin, function($q) use ($profile) {
                        $q->ownedByOrSuperAdmin($profile->id);
                    })
                    ->find($folderParam);
                if($activeFolder){
                    $selectedFolderKey = (string) $activeFolder->id;
                    $currentFolderId = $activeFolder->id;
                    $query->where('folder_id', $activeFolder->id);
                    $folderBreadcrumb = $this->buildFolderBreadcrumb($activeFolder);
                }else{
                    $query->whereNull('folder_id');
                }
            }else{
                $query->whereNull('folder_id');
            }

            $data = $query->paginate(20);

            $childFoldersQuery = ExamFolder::ofType($folderType)
                ->where('parent_id', $activeFolder ? $activeFolder->id : null);

            if ($isSuperAdmin) {
                $childFoldersQuery->orderBy('position')->orderBy('name');
            } else {
                // Giáo viên: lấy folder của mình + super admin
                $childFoldersQuery->ownedByOrSuperAdmin($profile->id)
                    ->orderBy('position')
                    ->orderBy('name');
            }

            $childFolders = $childFoldersQuery->get();
            $hideActionButtons = false;
            if (!$isSuperAdmin && $activeFolder && $activeFolder->owner && $activeFolder->owner->type == 3) {
                $hideActionButtons = true;
            }
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

            // Thông tin session cho chức năng Cut/Copy/Paste bài tập
            $cutFolders = session('exercise_cut_folders', []);
            $cutExercises = session('exercise_cut_baitaps', []);
            $copyFolders = session('exercise_copy_folders', []);
            $copyExercises = session('exercise_copy_baitaps', []);
            $hasCutSession = !empty($cutFolders) || !empty($cutExercises);
            $hasCopySession = !empty($copyFolders) || !empty($copyExercises);
            $actionType = session('exercise_action_type', 'cut');
            $hasAnySession = $hasCutSession || $hasCopySession;

            return view('crm_course.baitap.giaovien',[
                'data' => $data,
                'folderTree' => $folderTree,
                'folderOptions' => $folderOptions,
                'selectedFolderKey' => $selectedFolderKey,
                'activeFolder' => $activeFolder,
                'childFolders' => $childFolders,
                'folderBreadcrumb' => $folderBreadcrumb,
                'currentFolderId' => $currentFolderId,
                'profile' => $profile,
                'schoolClasses' => $schoolClasses,
                'hideActionButtons' => $hideActionButtons,
                'hasCutSession' => $hasCutSession,
                'cutFoldersCount' => is_array($cutFolders) ? count($cutFolders) : 0,
                'cutExercisesCount' => is_array($cutExercises) ? count($cutExercises) : 0,
                'hasCopySession' => $hasCopySession,
                'copyFoldersCount' => is_array($copyFolders) ? count($copyFolders) : 0,
                'copyExercisesCount' => is_array($copyExercises) ? count($copyExercises) : 0,
                'actionType' => $actionType,
                'hasAnySession' => $hasAnySession,
            ]); 
        }else{
            // Học sinh: hiển thị các loại bài tập theo yêu cầu
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
            
            // Query để lấy các bài tập học sinh có thể xem
            $exercisesQuery = Dethi::with('customer')
                ->where('type', 'baitap')
                ->where('status', 1) // Chỉ lấy bài tập đã kích hoạt
                ->where(function($query) use ($superAdminIds, $teacherIds, $classIds) {
                    // 1. Bài dành cho "tất cả mọi người" của super admin
                    $query->where(function($q) use ($superAdminIds) {
                        $q->where('access_type', 'all')
                          ->whereIn('created_by', $superAdminIds);
                    });
                    
                    // 2. Bài dành cho "tất cả mọi người" của giáo viên quản lý lớp của học sinh
                    if (!empty($teacherIds)) {
                        $query->orWhere(function($q) use ($teacherIds) {
                            $q->where('access_type', 'all')
                              ->whereIn('created_by', $teacherIds);
                        });
                    }
                    
                    // 3. Đề của giáo viên giao cho lớp (thông qua DethiClass)
                    if (!empty($classIds)) {
                        $assignedExerciseIds = \App\models\dethi\DethiClass::whereIn('class_id', $classIds)
                            ->pluck('dethi_id')
                            ->unique()
                            ->toArray();
                        
                        if (!empty($assignedExerciseIds)) {
                            $query->orWhere(function($q) use ($assignedExerciseIds) {
                                $q->whereIn('id', $assignedExerciseIds)
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
            
            // Lấy tất cả bài tập có thể xem
            $allExercises = $exercisesQuery->get();
            // Lấy các bài tập đã làm (ExamSession) để hiển thị kết quả
            $completedSessions = ExamSession::where('student_id', $profile->id)
                ->with('dethi.customer')
                ->orderBy('id', 'DESC')
                ->paginate(10);
            
            return view('crm_course.baitap.hocsinh', [
                'data' => $completedSessions,
                'allExercises' => $allExercises,
                'profile' => $profile
            ]); 
        }
    }
    public function uploadFile(Request $request){
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
            $type = 'baitap';
            $folderId = $this->resolveFolderIdForOwner($request->query('folder_id'), $profile->id, ExamFolder::TYPE_EXERCISE);
            return view('crm_course.dethi.upload-file',compact('type','folderId'));
        }else{
            return redirect()->back()->with('error','Chỉ giáo viên mới có quyền truy cập');
        }
    }
    public function moveToFolder(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        if($profile->type != 1){
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $data = $request->validate([
            'dethi_id' => ['required','integer','exists:dethi,id'],
            'folder_id' => ['nullable','integer','exists:exam_folders,id'],
        ]);

        $exercise = Dethi::where('id',$data['dethi_id'])
            ->where('created_by',$profile->id)
            ->where('type','baitap')
            ->firstOrFail();

        $folderId = $data['folder_id'] ?? null;
        if ($folderId) {
            $folder = ExamFolder::ownedBy($profile->id)
                ->ofType(ExamFolder::TYPE_EXERCISE)
                ->findOrFail($folderId);
        } else {
            $folder = null;
        }

        $exercise->folder_id = $folder ? $folder->id : null;
        $exercise->save();

        return redirect()->back()->with('success','Đã cập nhật thư mục cho bài tập.');
    }

    public function bulkDelete(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        if (!in_array($profile->type, [1,3])) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $data = $request->validate([
            'dethi_ids' => ['required','array'],
            'dethi_ids.*' => ['integer','exists:dethi,id'],
        ]);

        $query = Dethi::whereIn('id', $data['dethi_ids'])
            ->where('type', 'baitap');

        if ($profile->type == 1) {
            $query->where('created_by', $profile->id);
        }

        $deleted = $query->delete();

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
        ]);
    }

    public function bulkPublish(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        if (!in_array($profile->type, [1,3])) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $data = $request->validate([
            'dethi_ids' => ['required','array'],
            'dethi_ids.*' => ['integer','exists:dethi,id'],
        ]);

        $query = Dethi::whereIn('id', $data['dethi_ids'])
            ->where('type', 'baitap');

        if ($profile->type == 1) {
            $query->where('created_by', $profile->id);
        }

        $updated = $query->update(['status' => 1]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }

    public function bulkFinish(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        if (!in_array($profile->type, [1,3])) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $data = $request->validate([
            'dethi_ids' => ['required','array'],
            'dethi_ids.*' => ['integer','exists:dethi,id'],
        ]);

        $query = Dethi::whereIn('id', $data['dethi_ids'])
            ->where('type', 'baitap');

        if ($profile->type == 1) {
            $query->where('created_by', $profile->id);
        }

        $updated = $query->update(['status' => 0]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }

    /**
     * Lưu danh sách folders và bài tập để copy (paste sau)
     */
    public function bulkCopySession(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;

        if (!in_array($profile->type, [1,3])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $data = $request->validate([
            'folder_ids' => ['nullable','array'],
            'folder_ids.*' => ['integer','exists:exam_folders,id'],
            'dethi_ids' => ['nullable','array'],
            'dethi_ids.*' => ['integer','exists:dethi,id'],
        ]);

        $folderIds = $data['folder_ids'] ?? [];
        $exerciseIds = $data['dethi_ids'] ?? [];

        if (empty($folderIds) && empty($exerciseIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục nào được chọn để copy.'
            ], 400);
        }

        // Kiểm tra quyền truy cập folder (chỉ folder bài tập)
        if (!empty($folderIds)) {
            foreach ($folderIds as $folderId) {
                $folderQuery = ExamFolder::where('id', $folderId)
                    ->ofType(ExamFolder::TYPE_EXERCISE);
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

        // Kiểm tra quyền truy cập bài tập
        if (!empty($exerciseIds)) {
            foreach ($exerciseIds as $exerciseId) {
                $exerciseQuery = Dethi::where('id', $exerciseId)
                    ->where('type', 'baitap');
                if (!$isSuperAdmin) {
                    // Giáo viên có thể copy bài tập của mình hoặc của super admin
                    $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
                    $exerciseQuery->where(function($q) use ($profile, $superAdminIds) {
                        $q->where('created_by', $profile->id);
                        if (!empty($superAdminIds)) {
                            $q->orWhereIn('created_by', $superAdminIds);
                        }
                    });
                }
                $exercise = $exerciseQuery->first();
                if (!$exercise) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền copy một số bài tập đã chọn.'
                    ], 403);
                }
            }
        }

        // Lưu vào session
        session([
            'exercise_copy_folders' => $folderIds,
            'exercise_copy_baitaps' => $exerciseIds,
            'exercise_copy_user_id' => $profile->id,
            'exercise_action_type' => 'copy', // Đánh dấu là copy
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã copy. Vui lòng chọn thư mục đích và nhấn Paste.',
            'count' => [
                'folders' => count($folderIds),
                'exercises' => count($exerciseIds),
            ]
        ]);
    }

    /**
     * Lưu danh sách folders và bài tập để cut (paste sau)
     */
    public function bulkCut(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;

        if (!in_array($profile->type, [1,3])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $data = $request->validate([
            'folder_ids' => ['nullable','array'],
            'folder_ids.*' => ['integer','exists:exam_folders,id'],
            'dethi_ids' => ['nullable','array'],
            'dethi_ids.*' => ['integer','exists:dethi,id'],
        ]);

        $folderIds = $data['folder_ids'] ?? [];
        $exerciseIds = $data['dethi_ids'] ?? [];

        if (empty($folderIds) && empty($exerciseIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục nào được chọn để cut.'
            ], 400);
        }

        // Kiểm tra quyền truy cập folder
        if (!empty($folderIds)) {
            foreach ($folderIds as $folderId) {
                $folderQuery = ExamFolder::where('id', $folderId)
                    ->ofType(ExamFolder::TYPE_EXERCISE);
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

        // Kiểm tra quyền truy cập bài tập
        if (!empty($exerciseIds)) {
            foreach ($exerciseIds as $exerciseId) {
                $exerciseQuery = Dethi::where('id', $exerciseId)
                    ->where('type', 'baitap');
                if (!$isSuperAdmin) {
                    $exerciseQuery->where('created_by', $profile->id);
                }
                $exercise = $exerciseQuery->first();
                if (!$exercise) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền cut một số bài tập đã chọn.'
                    ], 403);
                }
            }
        }

        // Lưu vào session
        session([
            'exercise_cut_folders' => $folderIds,
            'exercise_cut_baitaps' => $exerciseIds,
            'exercise_cut_user_id' => $profile->id,
            'exercise_action_type' => 'cut', // Đánh dấu là cut
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu danh sách để cut. Vui lòng chọn thư mục đích và nhấn Paste.',
            'count' => [
                'folders' => count($folderIds),
                'exercises' => count($exerciseIds),
            ]
        ]);
    }

    /**
     * Paste (copy hoặc move) folders và bài tập vào folder đích
     */
    public function bulkPaste(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $isSuperAdmin = $profile->type == 3;

        if (!in_array($profile->type, [1,3])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $data = $request->validate([
            'target_folder_id' => ['nullable','integer','exists:exam_folders,id'],
        ]);

        $targetFolderId = $data['target_folder_id'] ?? null;

        // Lấy từ session (kiểm tra cả cut và copy)
        $actionType = session('exercise_action_type', 'cut'); // Mặc định là cut
        $folderIds = [];
        $exerciseIds = [];
        $userId = null;

        if ($actionType === 'copy') {
            $folderIds = session('exercise_copy_folders', []);
            $exerciseIds = session('exercise_copy_baitaps', []);
            $userId = session('exercise_copy_user_id');
        } else {
            $folderIds = session('exercise_cut_folders', []);
            $exerciseIds = session('exercise_cut_baitaps', []);
            $userId = session('exercise_cut_user_id');
        }

        // Kiểm tra session
        if ($userId != $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Session đã hết hạn hoặc không hợp lệ.'
            ], 400);
        }

        if (empty($folderIds) && empty($exerciseIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục nào để paste. Vui lòng chọn và copy/cut lại.'
            ], 400);
        }

        // Kiểm tra target folder
        if ($targetFolderId) {
            $targetFolderQuery = ExamFolder::where('id', $targetFolderId)
                ->ofType(ExamFolder::TYPE_EXERCISE);
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

            // Kiểm tra không được paste vào chính nó
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
                // Copy folders và bài tập
                $copiedFolders = 0;
                $copiedExercises = 0;

                if (!empty($folderIds)) {
                    foreach ($folderIds as $folderId) {
                        $copied = $this->copyExerciseFolderRecursive($folderId, $targetFolderId, $profile->id, $isSuperAdmin);
                        if ($copied) {
                            $copiedFolders++;
                        }
                    }
                }

                if (!empty($exerciseIds)) {
                    foreach ($exerciseIds as $exerciseId) {
                        $copied = $this->copyExercise($exerciseId, $targetFolderId, $profile->id, $isSuperAdmin);
                        if ($copied) {
                            $copiedExercises++;
                        }
                    }
                }

                // Xóa session copy
                session()->forget([
                    'exercise_copy_folders',
                    'exercise_copy_baitaps',
                    'exercise_copy_user_id',
                    'exercise_action_type',
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Đã paste thành công: {$copiedFolders} thư mục, {$copiedExercises} bài tập."
                ]);
            } else {
                // Move folders và bài tập (cut)
                $movedFolders = 0;
                $movedExercises = 0;

                if (!empty($folderIds)) {
                    foreach ($folderIds as $folderId) {
                        $moved = $this->moveExerciseFolder($folderId, $targetFolderId, $profile->id, $isSuperAdmin);
                        if ($moved) {
                            $movedFolders++;
                        }
                    }
                }

                if (!empty($exerciseIds)) {
                    foreach ($exerciseIds as $exerciseId) {
                        $moved = $this->moveExercise($exerciseId, $targetFolderId, $profile->id, $isSuperAdmin);
                        if ($moved) {
                            $movedExercises++;
                        }
                    }
                }

                // Xóa session cut
                session()->forget([
                    'exercise_cut_folders',
                    'exercise_cut_baitaps',
                    'exercise_cut_user_id',
                    'exercise_action_type',
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Đã paste thành công: {$movedFolders} thư mục, {$movedExercises} bài tập."
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk exercise paste error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi paste: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra xem folder bài tập có phải là con của folder khác không
     */
    private function isDescendantExerciseFolder($folderId, $ancestorId)
    {
        $current = ExamFolder::where('id', $folderId)
            ->ofType(ExamFolder::TYPE_EXERCISE)
            ->first();

        if (!$current) {
            return false;
        }

        while ($current->parent_id) {
            if ($current->parent_id == $ancestorId) {
                return true;
            }
            $current = ExamFolder::where('id', $current->parent_id)
                ->ofType(ExamFolder::TYPE_EXERCISE)
                ->first();
            if (!$current) {
                break;
            }
        }

        return false;
    }

    /**
     * Copy folder bài tập đệ quy (bao gồm folder con và bài tập)
     */
    private function copyExerciseFolderRecursive($folderId, $targetFolderId, $userId, $isSuperAdmin)
    {
        $folderQuery = ExamFolder::where('id', $folderId)
            ->ofType(ExamFolder::TYPE_EXERCISE);

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
                ->where('type', ExamFolder::TYPE_EXERCISE)
                ->where('name', $name)
                ->exists()
        ) {
            $suffix = $index === 1 ? ' - Copy' : " - Copy {$index}";
            $name = $baseName . $suffix;
            $index++;
        }

        $newFolder->name = $name;
        $newFolder->save();

        // Copy các bài tập trong folder
        $exercises = Dethi::where('folder_id', $folder->id)
            ->where('type','baitap')
            ->get();
        foreach ($exercises as $exercise) {
            $this->copyExercise($exercise->id, $newFolder->id, $userId, $isSuperAdmin);
        }

        // Copy đệ quy các folder con
        $childFolders = ExamFolder::where('parent_id', $folder->id)
            ->ofType(ExamFolder::TYPE_EXERCISE)
            ->get();

        foreach ($childFolders as $childFolder) {
            $this->copyExerciseFolderRecursive($childFolder->id, $newFolder->id, $userId, $isSuperAdmin);
        }

        return true;
    }

    /**
     * Copy bài tập (clone toàn bộ parts, questions, answers)
     */
    private function copyExercise($exerciseId, $targetFolderId, $userId, $isSuperAdmin)
    {
        $exerciseQuery = Dethi::with(['parts.questions.answers'])
            ->where('id', $exerciseId)
            ->where('type', 'baitap');

        if (!$isSuperAdmin) {
            // Giáo viên có thể copy bài tập của mình hoặc của super admin
            $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
            $exerciseQuery->where(function($q) use ($userId, $superAdminIds) {
                $q->where('created_by', $userId);
                if (!empty($superAdminIds)) {
                    $q->orWhereIn('created_by', $superAdminIds);
                }
            });
        }

        $exercise = $exerciseQuery->first();

        if (!$exercise) {
            return false;
        }

        // Clone bài tập
        $newExercise = $exercise->replicate();
        $newExercise->created_by = $userId; // Bài tập mới thuộc về user hiện tại
        $newExercise->folder_id = $targetFolderId;
        $newExercise->status = 0; // Mặc định là draft

        // Đổi tên bài tập mới: thêm hậu tố " - Copy" và đảm bảo không trùng trong cùng folder
        $baseTitle = $exercise->title;
        $title = $baseTitle . ' - Copy';
        $index = 2;

        while (
            Dethi::where('folder_id', $targetFolderId)
                ->where('type', 'baitap')
                ->where('title', $title)
                ->exists()
        ) {
            $title = $baseTitle . " - Copy {$index}";
            $index++;
        }

        $newExercise->title = $title;
        $newExercise->save();

        // Clone các parts
        foreach ($exercise->parts as $originalPart) {
            $newPart = $originalPart->replicate();
            $newPart->dethi_id = $newExercise->id;
            $newPart->save();

            // Clone các questions
            foreach ($originalPart->questions as $originalQuestion) {
                $newQuestion = $originalQuestion->replicate();
                $newQuestion->de_thi_id = $newExercise->id;
                $newQuestion->dethi_part_id = $newPart->id;
                $newQuestion->save();

                // Clone các answers
                foreach ($originalQuestion->answers as $originalAnswer) {
                    $newAnswer = $originalAnswer->replicate();
                    $newAnswer->dethi_question_id = $newQuestion->id;
                    $newAnswer->de_thi_id = $newExercise->id;
                    $newAnswer->save();
                }
            }
        }

        return true;
    }

    /**
     * Move folder bài tập (chỉ đổi parent_id, xử lý trùng tên)
     */
    private function moveExerciseFolder($folderId, $targetFolderId, $userId, $isSuperAdmin)
    {
        $folderQuery = ExamFolder::where('id', $folderId)
            ->ofType(ExamFolder::TYPE_EXERCISE);

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
        if ($targetFolderId && $this->isDescendantExerciseFolder($targetFolderId, $folderId)) {
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
                    ->where('type', ExamFolder::TYPE_EXERCISE)
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
     * Move bài tập (chỉ đổi folder_id)
     */
    private function moveExercise($exerciseId, $targetFolderId, $userId, $isSuperAdmin)
    {
        $exerciseQuery = Dethi::where('id', $exerciseId)
            ->where('type', 'baitap');

        if (!$isSuperAdmin) {
            $exerciseQuery->where('created_by', $userId);
        }

        $exercise = $exerciseQuery->first();

        if (!$exercise) {
            return false;
        }

        $exercise->folder_id = $targetFolderId;
        $exercise->save();

        return true;
    }

    /**
     * Bulk update access type / target audience for exercises
     */
    public function bulkUpdateAccess(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        if (!in_array($profile->type, [1,3])) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
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

        $query = Dethi::whereIn('id', $data['dethi_ids'])
            ->where('type', 'baitap');

        if ($profile->type == 1) {
            $query->where('created_by', $profile->id);
        }

        $exams = $query->get();
        if ($exams->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài tập hợp lệ.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($exams as $exam) {
                $exam->access_type = $data['access_type'];
                $exam->start_time = $data['access_type'] === 'time_limited' ? $data['start_time'] : null;
                $exam->end_time = $data['access_type'] === 'time_limited' ? $data['end_time'] : null;
                $exam->save();

                // Update allowed classes
                if ($data['access_type'] === 'class') {
                    $classIds = $data['classes'] ?? [];
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
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'updated' => $exams->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getBaiTap($task_id, Request $request)
    {
        $profile = Auth::guard("customer")->user();
        $dethi = Dethi::with(['parts.questions.answers', 'allowedClasses'])->where('id', $task_id)->first();
        
        if(!$dethi){
            return response()->json([
                "html" => "Bài tập không tồn tại",
                "status" => "error"
            ], 404);
        }
        
        // Kiểm tra quyền truy cập nếu là học sinh
        if($profile->type == 0){
            // 1. Nếu là người tạo bài tập, cho phép xem luôn
            if($dethi->created_by == $profile->id){
                // Cho phép
            }
            // 2. Kiểm tra bài tập đã kích hoạt chưa
            elseif($dethi->status == 0){
                return response()->json([
                    "html" => "Bài tập chưa được kích hoạt",
                    "status" => "error"
                ], 403);
            }
            // 3. Kiểm tra quyền truy cập theo access_type
            elseif($dethi->access_type === 'class'){
                $studentClassCodes = $profile->class_codes ?? [];
                
                if(!$dethi->canStudentAccessMultiple($studentClassCodes)){
                    $allowedClasses = $dethi->allowedClasses()
                        ->with('schoolClass')
                        ->get()
                        ->pluck('schoolClass.class_name')
                        ->implode(', ');
                    return response()->json([
                        "html" => "Bài tập này chỉ dành cho lớp: " . $allowedClasses,
                        "status" => "error"
                    ], 403);
                }
            }
            // 4. Kiểm tra time_limited nếu có
            elseif($dethi->access_type === 'time_limited'){
                if(!$dethi->isWithinTimeLimit()){
                    $startTime = $dethi->start_time ? $dethi->start_time->format('H:i d/m/Y') : 'N/A';
                    $endTime = $dethi->end_time ? $dethi->end_time->format('H:i d/m/Y') : 'N/A';
                    return response()->json([
                        "html" => "Bài tập chỉ làm được từ {$startTime} đến {$endTime}",
                        "status" => "error"
                    ], 403);
                }
            }
        }
        
        $studentId = $request->query("student_id");
        $courseId = $request->query("course_id");

        $result = ExamSession::where("student_id", $studentId)
            ->where("dethi_id", $task_id)
            ->first();
        if ($result) {
            $session = $result;
            $multipleChoiceQuestions = 0;
            $trueFalseQuestions = 0;
            $essayQuestions = 0;
            $correctMultipleChoice = 0;
            $correctTrueFalse = 0;
            $totalMultipleChoiceScore = 0;
            $totalTrueFalseScore = 0;
            foreach ($result->dethi->parts as $part) {
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
                    } elseif (in_array($question->question_type, ['essay', 'short_answer'])) {
                        $essayQuestions++;
                    }
                }
            }
            $view = view("crm_course.baitap.result", [
                'session' => $session,
                'multiple_choice_questions' => $multipleChoiceQuestions, //số câu trắc nghiệm
                'true_false_questions' => $trueFalseQuestions, //số câu đúng/sai nhóm
                'essay_questions' => $essayQuestions, //số câu tự luận
                'correct_multiple_choice' => $correctMultipleChoice, //số câu trắc nghiệm đúng
                'correct_true_false' => $correctTrueFalse, //số câu đúng/sai nhóm đúng
                'total_multiple_choice_score' => $totalMultipleChoiceScore, //điểm tổng câu trắc nghiệm
                'total_true_false_score' => $totalTrueFalseScore //điểm tổng câu đúng/sai nhóm
            ])->render();
            return response()->json([
                "html" => $view,
                "status" => "done"
            ]);
        } else {
            $view = view("crm_course.khoahoc.baitap", compact("dethi","courseId"))->render();
            return response()->json([
                "html" => $view,
                "status" => "not_done"
            ]);
        }

        // return response()->json($baiTapData);
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

    protected function resolveFolderIdForOwner($folderId, $ownerId, string $type)
    {
        if (empty($folderId) || $folderId === 'root') {
            return null;
        }

        $folder = ExamFolder::ownedBy($ownerId)
            ->ofType($type)
            ->find($folderId);

        return $folder ? $folder->id : null;
    }
    public function submitBaitap(Request $request)
    {
        \DB::beginTransaction();
        try {
            $profile = Auth::guard("customer")->user();
            $dethi_id = $request->dethi_id;
            $answers = $request->answers;
            $student_id = $request->student_id;
            $teacher_id = $request->teacher_id;
            $course_id = $request->course_id;
            
            // Lấy thông tin đề thi và cấu hình chấm điểm
            $dethi = Dethi::with(['parts.questions.answers', 'allowedClasses'])->findOrFail($dethi_id);
            
            // Kiểm tra quyền truy cập nếu là học sinh
            if($profile->type == 0){
                // 1. Nếu là người tạo bài tập, cho phép submit luôn
                if($dethi->created_by == $profile->id){
                    // Cho phép
                }
                // 2. Kiểm tra bài tập đã kích hoạt chưa
                elseif($dethi->status == 0){
                    throw new \Exception('Bài tập chưa được kích hoạt');
                }
                // 3. Kiểm tra quyền truy cập theo access_type
                elseif($dethi->access_type === 'class'){
                    $studentClassCodes = $profile->class_codes ?? [];
                    
                    if(!$dethi->canStudentAccessMultiple($studentClassCodes)){
                        $allowedClasses = $dethi->allowedClasses()
                            ->with('schoolClass')
                            ->get()
                            ->pluck('schoolClass.class_name')
                            ->implode(', ');
                        throw new \Exception('Bài tập này chỉ dành cho lớp: ' . $allowedClasses);
                    }
                }
                // 4. Kiểm tra time_limited nếu có
                elseif($dethi->access_type === 'time_limited'){
                    if(!$dethi->isWithinTimeLimit()){
                        $startTime = $dethi->start_time ? $dethi->start_time->format('H:i d/m/Y') : 'N/A';
                        $endTime = $dethi->end_time ? $dethi->end_time->format('H:i d/m/Y') : 'N/A';
                        throw new \Exception("Bài tập chỉ làm được từ {$startTime} đến {$endTime}");
                    }
                }
            }
            $partScores = json_decode($dethi->part_scores, true) ?? [];
            $trueFalseScorePercent = json_decode($dethi->true_false_score_percent, true) ?? [];

            // 1. Tạo session làm bài
            $session = ExamSession::create([
                'dethi_id' => $dethi_id,
                'student_id' => $student_id,
                'teacher_id' => $teacher_id,
                'course_id' => $course_id,
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
                        // Trắc nghiệm: answer là string (label A, B, C, D)
                        if (is_string($answer)) {
                            $answer_choice = $answer;
                            // Kiểm tra đáp án đúng
                            $correctAnswer = $question->answers->where('is_correct', true)->first();
                            if ($correctAnswer && strtoupper($correctAnswer->label) === strtoupper($answer)) {
                                $is_correct = true;
                                $score = $questionScore;
                                $correctAnswers++;
                            }
                        } else {
                            throw new \Exception("Câu trả lời trắc nghiệm phải là string, nhận được: " . gettype($answer));
                        }
                        break;

                    case 'true_false_grouped':
                        // Đúng/Sai nhóm: answer là mảng [option_id => 1/0/2]
                        if (is_array($answer)) {
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
                            throw new \Exception("Câu trả lời đúng/sai nhóm phải là array, nhận được: " . gettype($answer));
                        }
                        break;
                        case 'fill_in_blank':
                            // Điền vào chỗ trống: answer là string
                            if (is_string($answer['text'])) {
                                $answer_text = $answer['text'];
                                // Kiểm tra đáp án đúng
                                $correctAnswer = $question->answers->where('is_correct', true)->first();
                                if ($correctAnswer && strtolower(trim($correctAnswer->content)) === strtolower(trim($answer['text']))) {
                                    $is_correct = true;
                                    $score = $questionScore;
                                    $correctAnswers++;
                                }
                            } else {
                                throw new \Exception("Câu trả lời điền vào chỗ trống phải là string, nhận được: " . gettype($answer));
                            }
                            break;
                    case 'short_answer':
                        // Tự luận: answer['text'], answer['image']
                        if (is_array($answer)) {
                            $answer_text = $answer['text'] ?? null;
                            if (isset($answer['image']) && $request->hasFile("answers.$question_id.image")) {
                                $file = $request->file("answers.$question_id.image");
                                $path = $file->store('exam_answers', 'public');
                                $answer_image = $path;
                            }
                            // Tự luận cần chấm thủ công, không tính điểm tự động
                            $score = 0;
                        } else {
                            throw new \Exception("Câu trả lời tự luận phải là array, nhận được: " . gettype($answer));
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
            if($session->id){
            \DB::commit();
            $result = ExamSession::with([
                'dethi.parts.questions.answers',
                'answers'
            ])->findOrFail($session->id);
            // Tính toán thống kê
            $multipleChoiceQuestions = 0;
            $trueFalseQuestions = 0;
            $essayQuestions = 0;
            $correctMultipleChoice = 0;
            $correctTrueFalse = 0;
            $totalMultipleChoiceScore = 0;
            $totalTrueFalseScore = 0;
            
            foreach ($result->dethi->parts as $part) {
                foreach ($part->questions as $question) {
                    if ($question->question_type === 'multiple_choice') {
                        $multipleChoiceQuestions++;
                        // Kiểm tra câu trả lời đúng từ exam_answers
                        $studentAnswer = $result->answers->where('question_id', $question->id)->first();
                        if ($studentAnswer) {
                            if ($studentAnswer->is_correct === 1) {
                                $correctMultipleChoice++;
                                $totalMultipleChoiceScore += $studentAnswer->score ?? 1;
                            }
                        }
                    } elseif ($question->question_type === 'true_false_grouped') {
                        $trueFalseQuestions++;
                        // Lấy câu trả lời của học sinh cho câu hỏi này
                        $studentAnswer = $result->answers->where('question_id', $question->id)->first();
                        
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
                            $trueFalseScorePercent = json_decode($result->dethi->true_false_score_percent ?? '{"1":10,"2":25,"3":50,"4":100}', true);
                            if (isset($trueFalseScorePercent[$correctCount]) && $trueFalseScorePercent[$correctCount] > 0) {
                                $correctTrueFalse++;
                                $scorePercent = $trueFalseScorePercent[$correctCount];
                                $questionScore = $question->score ?? 1;
                                $totalTrueFalseScore += ($questionScore * $scorePercent / 100);
                            }
                        }
                    } elseif (in_array($question->question_type, ['essay', 'short_answer'])) {
                        $essayQuestions++;
                    }
                }
            }
            $view = view('crm_course.baitap.result', [
                'session' => $result,
                'multiple_choice_questions' => $multipleChoiceQuestions, //số câu trắc nghiệm
                'true_false_questions' => $trueFalseQuestions, //số câu đúng/sai nhóm
                'essay_questions' => $essayQuestions, //số câu tự luận
                'correct_multiple_choice' => $correctMultipleChoice, //số câu trắc nghiệm đúng
                'correct_true_false' => $correctTrueFalse, //số câu đúng/sai nhóm đúng
                'total_multiple_choice_score' => $totalMultipleChoiceScore, //điểm tổng câu trắc nghiệm
                'total_true_false_score' => $totalTrueFalseScore //điểm tổng câu đúng/sai nhóm
            ])->render();
             return response()->json([
                "html" => $view,
                "status" => "done"
            ]);
            }

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

    public function cloneBaitap($id)
    {
        $profile = Auth::guard("customer")->user();
        
        // Chỉ giáo viên mới có thể clone
        if ($profile->type != 1) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này');
        }

        // Lấy bài tập gốc với đầy đủ relationships
        $originalBaitap = Dethi::with(['parts.questions.answers', 'allowedClasses'])
            ->where('type', 'baitap')
            ->find($id);

        if (!$originalBaitap) {
            return redirect()->back()->with('error', 'Bài tập không tồn tại');
        }

        // Kiểm tra xem bài tập này có thuộc về super admin không
        $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
        if (!in_array($originalBaitap->created_by, $superAdminIds)) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể tải bài tập từ super admin');
        }

        try {
            DB::beginTransaction();

            // Clone bài tập
            $newBaitap = $originalBaitap->replicate();
            $newBaitap->created_by = $profile->id;
            $newBaitap->folder_id = null; // Đặt vào thư mục gốc
            $newBaitap->status = 0; // Có thể đặt status mặc định là draft
            $newBaitap->save();

            // Clone các parts
            $partMapping = []; // Lưu mapping giữa old_part_id và new_part_id
            foreach ($originalBaitap->parts as $originalPart) {
                $newPart = $originalPart->replicate();
                $newPart->dethi_id = $newBaitap->id;
                $newPart->save();
                $partMapping[$originalPart->id] = $newPart->id;

                // Clone các questions
                $questionMapping = []; // Lưu mapping giữa old_question_id và new_question_id
                foreach ($originalPart->questions as $originalQuestion) {
                    $newQuestion = $originalQuestion->replicate();
                    $newQuestion->de_thi_id = $newBaitap->id;
                    $newQuestion->dethi_part_id = $newPart->id;
                    $newQuestion->save();
                    $questionMapping[$originalQuestion->id] = $newQuestion->id;

                    // Clone các answers
                    foreach ($originalQuestion->answers as $originalAnswer) {
                        $newAnswer = $originalAnswer->replicate();
                        $newAnswer->dethi_question_id = $newQuestion->id;
                        $newAnswer->de_thi_id = $newBaitap->id;
                        $newAnswer->save();
                    }
                }
            }

            // Clone allowed classes (nếu có) - giáo viên có thể giữ lại hoặc xóa
            // Ở đây tôi sẽ không clone để giáo viên tự cấu hình lại
            // foreach ($originalBaitap->allowedClasses as $allowedClass) {
            //     // Kiểm tra xem lớp này có thuộc về giáo viên không trước khi clone
            // }

            DB::commit();

            return redirect()->route('danhSachBaiTap')->with('success', 'Đã tải bài tập thành công về thư mục gốc của bạn');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Clone baitap error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải bài tập. Vui lòng thử lại.');
        }
    }

    /**
     * Lấy nội dung bài tập để hiển thị trong popup (chỉ cho giáo viên, từ super admin)
     */
    public function getExerciseContent($id)
    {
        $profile = Auth::guard("customer")->user();
        
        // Chỉ giáo viên mới có thể xem nội dung bài tập từ super admin
        if ($profile->type != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập'
            ], 403);
        }

        // Kiểm tra bài tập có tồn tại và thuộc về super admin không
        $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
        $exercise = Dethi::with(['parts.questions.answers', 'customer'])
            ->where('type', 'baitap')
            ->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Bài tập không tồn tại'
            ], 404);
        }

        if (!in_array($exercise->created_by, $superAdminIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể xem bài tập từ super admin'
            ], 403);
        }

        // Format dữ liệu để trả về
        $partsData = [];
        foreach ($exercise->parts as $index => $part) {
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
                    'correct_answer' => $correct_answer,
                    'correct_answers' => $correct_answers,
                ];
            }

            $partsData[] = [
                'part' => 'PHẦN ' . ($index + 1),
                'part_title' => $part->part_title,
                'questions' => $questionsData,
            ];
        }

        $responseData = [
            'title' => $exercise->title,
            'description' => $exercise->description,
            'time' => $exercise->time,
            'access_type' => $exercise->access_type,
            'parts' => $partsData,
            'owner' => $exercise->customer ? [
                'name' => $exercise->customer->name,
                'type' => $exercise->customer->type,
            ] : null
        ];

        return response()->json([
            'success' => true,
            'data' => $responseData
        ]);
    }
}
