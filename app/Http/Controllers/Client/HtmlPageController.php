<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\models\HtmlPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HtmlPageController extends Controller
{
    protected function authorizeTeacherOrAdmin($profile)
    {
        if (!$profile || !in_array($profile->type, [1, 3])) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }
    }

    protected function ensureOwner($profile, HtmlPage $htmlPage)
    {
        if ($profile->type == 3) {
            return;
        }

        if ($htmlPage->created_by !== $profile->id) {
            abort(403, 'Bạn chỉ có thể thao tác trên trang của mình.');
        }
    }

    protected function makeUniqueSlug($baseSlug, $ignoreId = null)
    {
        $slug = $baseSlug ?: 'trang-html';
        $counter = 1;

        while (
            HtmlPage::where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function index()
    {
        $profile = Auth::guard('customer')->user();
        $this->authorizeTeacherOrAdmin($profile);

        $pages = HtmlPage::query()
            ->when($profile->type == 1, function ($query) use ($profile) {
                $query->where('created_by', $profile->id);
            })
            ->orderByDesc('id')
            ->paginate(15);

        return view('crm_course.html_page.index', [
            'pages' => $pages,
            'profile' => $profile,
        ]);
    }

    public function create()
    {
        $profile = Auth::guard('customer')->user();
        $this->authorizeTeacherOrAdmin($profile);

        return view('crm_course.html_page.create', [
            'profile' => $profile,
        ]);
    }

    public function store(Request $request)
    {
        $profile = Auth::guard('customer')->user();
        $this->authorizeTeacherOrAdmin($profile);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content_html' => 'required|string',
            'status' => 'nullable|boolean',
        ]);

        $baseSlug = to_slug($data['slug'] ?? $data['title']);
        $slug = $this->makeUniqueSlug($baseSlug);

        $page = HtmlPage::create([
            'title' => $data['title'],
            'slug' => $slug,
            'content_html' => $data['content_html'],
            'status' => $data['status'] ?? 1,
            'created_by' => $profile->id,
            'updated_by' => $profile->id,
        ]);

        return redirect()
            ->route('html-pages.index')
            ->with('success', 'Đã tạo trang HTML thành công.');
    }

    public function edit(HtmlPage $htmlPage)
    {
        $profile = Auth::guard('customer')->user();
        $this->authorizeTeacherOrAdmin($profile);
        $this->ensureOwner($profile, $htmlPage);

        return view('crm_course.html_page.edit', [
            'page' => $htmlPage,
            'profile' => $profile,
        ]);
    }

    public function update(Request $request, HtmlPage $htmlPage)
    {
        $profile = Auth::guard('customer')->user();
        $this->authorizeTeacherOrAdmin($profile);
        $this->ensureOwner($profile, $htmlPage);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content_html' => 'required|string',
            'status' => 'nullable|boolean',
        ]);

        $baseSlug = to_slug($data['slug'] ?? $data['title']);
        $slug = $this->makeUniqueSlug($baseSlug, $htmlPage->id);

        $htmlPage->update([
            'title' => $data['title'],
            'slug' => $slug,
            'content_html' => $data['content_html'],
            'status' => $data['status'] ?? 1,
            'updated_by' => $profile->id,
        ]);

        return redirect()
            ->route('html-pages.index')
            ->with('success', 'Đã cập nhật trang HTML.');
    }

    public function destroy(HtmlPage $htmlPage)
    {
        $profile = Auth::guard('customer')->user();
        $this->authorizeTeacherOrAdmin($profile);
        $this->ensureOwner($profile, $htmlPage);

        $htmlPage->delete();

        return redirect()
            ->route('html-pages.index')
            ->with('success', 'Đã xóa trang HTML.');
    }

    public function showPublic($slug)
    {
        $page = HtmlPage::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $content = $page->content_html;

        // Nếu lưu đường dẫn/URL file HTML, chuyển hướng hoặc trả file
        if (filter_var($content, FILTER_VALIDATE_URL)) {
            return redirect()->away($content);
        }

        // Nếu là đường dẫn tương đối trong public/storage
        if (Str::startsWith($content, ['/','storage','uploads'])) {
            $relativePath = ltrim($content, '/');
            $fullPath = public_path($relativePath);
            if (file_exists($fullPath)) {
                return response()->file($fullPath);
            }
        }

        // Mặc định: trả nguyên nội dung HTML đã lưu
        return response($content, 200)
            ->header('Content-Type', 'text/html');
    }
}

