<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\models\dethi\ExamFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExamFolderController extends Controller
{
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $folderType = $this->resolveFolderType($request);
        $isSuperAdmin = $customer->type == 3;
        $isTeacher = $customer->type == 1;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:exam_folders,id'],
        ]);

        if (!empty($data['parent_id']) && !$this->folderBelongsToCustomer($data['parent_id'], $customer->id, $folderType, $isSuperAdmin, $isTeacher, false)) {
            throw ValidationException::withMessages([
                'parent_id' => 'Bạn không có quyền trên thư mục này.',
            ]);
        }

        // Tính position: super admin sẽ có position riêng, giáo viên cũng có position riêng của mình
        $positionQuery = ExamFolder::where('type', $folderType)
            ->where('parent_id', $data['parent_id'] ?? null);
        
        if (!$isSuperAdmin) {
            $positionQuery->where('owner_id', $customer->id);
        }
        
        $nextPosition = $positionQuery->max('position') ?? 0;

        $folder = ExamFolder::create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
            'owner_id' => $customer->id,
            'position' => $nextPosition + 1,
            'type' => $folderType,
        ]);

        return $request->expectsJson()
            ? response()->json(['success' => true, 'folder' => $folder])
            : redirect()->back()->with('success', 'Tạo thư mục thành công.');
    }

    public function update(Request $request, ExamFolder $folder)
    {
        $customer = Auth::guard('customer')->user();
        $folderType = $this->resolveFolderType($request) ?? $folder->type;
        $isSuperAdmin = $customer->type == 3;
        $isTeacher = $customer->type == 1;
        $this->authorizeOwner($folder, $customer->id, $folderType, $isSuperAdmin, $isTeacher);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:exam_folders,id'],
        ]);

        if (array_key_exists('parent_id', $data)) {
            $parentId = $data['parent_id'];
            if ($parentId === $folder->id) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Không thể chọn chính thư mục hiện tại.',
                ]);
            }

            if ($parentId && !$this->folderBelongsToCustomer($parentId, $customer->id, $folderType, $isSuperAdmin, $isTeacher, false)) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Bạn không có quyền trên thư mục đích.',
                ]);
            }

            if ($parentId && $this->isDescendant($parentId, $folder->id)) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Không thể di chuyển vào thư mục con của chính nó.',
                ]);
            }
        }

        $folder->fill($data);
        $folder->save();

        return $request->expectsJson()
            ? response()->json(['success' => true, 'folder' => $folder->fresh()])
            : redirect()->back()->with('success', 'Cập nhật thư mục thành công.');
    }

    public function destroy(Request $request, ExamFolder $folder)
    {
        $customer = Auth::guard('customer')->user();
        $folderType = $this->resolveFolderType($request) ?? $folder->type;
        $isSuperAdmin = $customer->type == 3;
        $isTeacher = $customer->type == 1;
        $this->authorizeOwner($folder, $customer->id, $folderType, $isSuperAdmin, $isTeacher);

        if ($folder->children()->exists() || $folder->exams()->exists()) {
            throw ValidationException::withMessages([
                'folder' => 'Vui lòng di chuyển hoặc xóa toàn bộ thư mục con và đề thi trước.',
            ]);
        }

        $folder->delete();

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : redirect()->back()->with('success', 'Đã xóa thư mục.');
    }

    public function tree(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $type = $this->resolveFolderType($request);
        $isSuperAdmin = $customer->type == 3;
        // Giáo viên (type 1) cũng nên thấy folders của super admin
        $includeSuperAdminFolders = $customer->type == 1;
        $tree = ExamFolder::treeForOwner($customer->id, $type, $isSuperAdmin, $includeSuperAdminFolders);

        return response()->json([
            'success' => true,
            'tree' => $tree,
        ]);
    }

    protected function folderBelongsToCustomer(int $folderId, int $customerId, string $type, bool $isSuperAdmin = false, bool $isTeacher = false, bool $allowTeacherAccessToSuperAdmin = true): bool
    {
        if ($isSuperAdmin) {
            return ExamFolder::where('id', $folderId)
                ->where('type', $type)
                ->whereNull('deleted_at')
                ->exists();
        }
        
        $query = ExamFolder::where('id', $folderId)
            ->where('type', $type)
            ->whereNull('deleted_at');
        
        if ($isTeacher) {
            if (!$allowTeacherAccessToSuperAdmin) {
                return $query->where('owner_id', $customerId)->exists();
            }

            $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
            return $query->where(function($q) use ($customerId, $superAdminIds) {
                $q->where('owner_id', $customerId);
                if (!empty($superAdminIds)) {
                    $q->orWhereIn('owner_id', $superAdminIds);
                }
            })->exists();
        }
        
        return $query->where('owner_id', $customerId)->exists();
    }

    protected function authorizeOwner(ExamFolder $folder, int $customerId, ?string $expectedType = null, bool $isSuperAdmin = false, bool $allowViewOnly = false): void
    {
        $isTeacher = !$isSuperAdmin && $allowViewOnly;
        
        if ($isSuperAdmin) {
            // Super admin có thể làm mọi thứ
        } elseif ($isTeacher) {
            // Giáo viên chỉ có thể sửa/xóa folders của chính họ, không thể sửa/xóa folders của super admin
            $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
            if (in_array($folder->owner_id, $superAdminIds)) {
                abort(403, 'Bạn chỉ có thể xem thư mục này, không thể chỉnh sửa hoặc xóa.');
            }
            if ($folder->owner_id !== $customerId) {
                abort(403, 'Bạn không có quyền truy cập thư mục này.');
            }
        } else {
            // Chỉ có thể sửa/xóa của chính họ
            if ($folder->owner_id !== $customerId) {
                abort(403, 'Bạn không có quyền truy cập thư mục này.');
            }
        }

        if ($expectedType && $folder->type !== $expectedType) {
            abort(403, 'Bạn không có quyền truy cập thư mục này.');
        }
    }

    protected function isDescendant(int $targetFolderId, int $sourceFolderId): bool
    {
        $current = ExamFolder::find($targetFolderId);

        while ($current) {
            if ($current->id === $sourceFolderId) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }

    protected function resolveFolderType(Request $request): string
    {
        // Nếu URL thuộc nhóm /bai-tap thì mặc định type = exercise
        $route = $request->route();
        $prefix = $route ? $route->getPrefix() : '';
        if ($prefix && strpos($prefix, 'bai-tap') !== false) {
            return ExamFolder::TYPE_EXERCISE;
        }

        $type = $request->input('type', ExamFolder::TYPE_EXAM);
        $allowed = [ExamFolder::TYPE_EXAM, ExamFolder::TYPE_EXERCISE];
        return in_array($type, $allowed, true) ? $type : ExamFolder::TYPE_EXAM;
    }
}

