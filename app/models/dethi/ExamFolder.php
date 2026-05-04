<?php

namespace App\models\dethi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ExamFolder extends Model
{
    use SoftDeletes;

    public const TYPE_EXAM = 'exam';
    public const TYPE_EXERCISE = 'exercise';

    protected $table = 'exam_folders';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $folder): void {
            if (empty($folder->slug)) {
                $folder->slug = self::generateUniqueSlug($folder->name ?? Str::random(8));
            }

            if (empty($folder->type)) {
                $folder->type = self::TYPE_EXAM;
            }
        });
    }

    protected static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $index = 1;

        while (self::where('slug', $slug)->withTrashed()->exists()) {
            $slug = "{$base}-{$index}";
            $index++;
        }

        return $slug;
    }

    public function scopeOwnedBy($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Lấy folders của owner hoặc của super admins
     */
    public function scopeOwnedByOrSuperAdmin($query, int $ownerId)
    {
        // Lấy danh sách super admin IDs (type == 3)
        $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
        
        return $query->where(function($q) use ($ownerId, $superAdminIds) {
            $q->where('owner_id', $ownerId);
            if (!empty($superAdminIds)) {
                $q->orWhereIn('owner_id', $superAdminIds);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('type', $this->type)
            ->orderBy('position')
            ->orderBy('name');
    }

    public function exams()
    {
        return $this->hasMany(Dethi::class, 'folder_id');
    }

    public function owner()
    {
        return $this->belongsTo(\App\Customer::class, 'owner_id');
    }

    public static function treeForOwner(int $ownerId, string $type = self::TYPE_EXAM, bool $isSuperAdmin = false, bool $includeSuperAdminFolders = false)
    {
        // Lấy super admin IDs một lần nếu cần
        $superAdminIds = [];
        if ($includeSuperAdminFolders || $isSuperAdmin) {
            $superAdminIds = \App\Customer::where('type', 3)->pluck('id')->toArray();
        }
        
        $query = self::ofType($type)
            ->whereNull('parent_id');
        
        if ($isSuperAdmin) {
            // Super admin thấy tất cả
            $query->with('owner');
        } elseif ($includeSuperAdminFolders) {
            // Giáo viên: thấy của mình + của super admin
            $query->where(function($q) use ($ownerId, $superAdminIds) {
                $q->where('owner_id', $ownerId);
                if (!empty($superAdminIds)) {
                    $q->orWhereIn('owner_id', $superAdminIds);
                }
            });
            $query->with('owner');
        } else {
            // Chỉ của chính họ
            $query->ownedBy($ownerId);
        }
        
        return $query->with([
                'children' => function ($query) use ($ownerId, $type, $isSuperAdmin, $includeSuperAdminFolders, $superAdminIds) {
                    $query->ofType($type);
                    if ($isSuperAdmin) {
                        $query->with('owner');
                    } elseif ($includeSuperAdminFolders) {
                        $query->where(function($q) use ($ownerId, $superAdminIds) {
                            $q->where('owner_id', $ownerId);
                            if (!empty($superAdminIds)) {
                                $q->orWhereIn('owner_id', $superAdminIds);
                            }
                        });
                        $query->with('owner');
                    } else {
                        $query->ownedBy($ownerId);
                    }
                    // Load children của children với cùng logic
                    $query->with(['children' => function($q) use ($ownerId, $type, $isSuperAdmin, $includeSuperAdminFolders, $superAdminIds) {
                        $q->ofType($type);
                        if ($isSuperAdmin) {
                            $q->with('owner');
                        } elseif ($includeSuperAdminFolders) {
                            $q->where(function($subQ) use ($ownerId, $superAdminIds) {
                                $subQ->where('owner_id', $ownerId);
                                if (!empty($superAdminIds)) {
                                    $subQ->orWhereIn('owner_id', $superAdminIds);
                                }
                            });
                            $q->with('owner');
                        } else {
                            $q->ownedBy($ownerId);
                        }
                    }]);
                },
            ])
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }
}

