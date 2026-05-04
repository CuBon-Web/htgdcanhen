<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\models\product\Category;
use App\models\product\Product;
use App\models\BillCourse;
use App\models\website\AlbumAffter;
use Str;
use App\models\Course\quizResultCourseDetails;
use App\Customer;
use App\models\dethi\Dethi;
use App\models\product\TypeProduct;
use App\models\product\TypeProductTwo;
use File,Http;
class CouseController extends Controller
{

    public function startStudyCourse($id)
    {
        $data["detail"] = Product::where("id", $id)->first();
        return view("crm_course.khoahoc.batdauhoc", $data);
    }
    public function deleteCouse($id)
    {
        $query = Product::find($id);
        $khoaHoc = json_decode($query->size, true);
        foreach ($khoaHoc as $item) {
            foreach($item['detail_task'] as $key){
                if($key['video']){
                    $this->deleteVideoCloudflare($key['video']);
                }
                if($key['document']){
                    $file = public_path($key['document']);
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        }
        if ($query->images) {
            $image = public_path($query->images);
            if (file_exists($image)) {
                unlink($image);
            }
        }
        $query->delete();
        return redirect()->route("myCouseGiaoVien")->with("success", "Xóa khóa học thành công");
    }
    public function deleteVideoCloudflare($uid)
    {
        if (!$uid) {
            return response()->json(['error' => 'Thiếu uid'], 400);
        }

        // Kiểm tra nếu $uid là URL iframe, trích xuất video ID
        if (strpos($uid, 'https://iframe.videodelivery.net/') === 0) {
            // Trích xuất video ID từ URL iframe
            $videoId = basename($uid);
            $uid = $videoId;
        }

        // Kiểm tra nếu $uid vẫn không hợp lệ
        if (empty($uid) || strlen($uid) < 10) {
            return response()->json(['error' => 'UID video không hợp lệ'], 400);
        }

        try {
            $url = "https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_STREAM_ACCOUNT_ID') . "/stream/" . $uid;
            $response = Http::withToken(env('CLOUDFLARE_STREAM_TOKEN'))
                ->delete($url);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Video đã được xóa thành công',
                    'video_id' => $uid
                ]);
            } else {
                return response()->json([
                    'error' => 'Không thể xóa video',
                    'status_code' => $response->status(),
                    'response' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi khi xóa video: ' . $e->getMessage()
            ], 500);
        }
    }
    public function postCouse()
    {

        $profile = Auth::guard("customer")->user();
        if ($profile->type == 1 || $profile->type == 3) {
            $data["category"] = Category::where("status", 1)->get();
            $profile = Auth::guard("customer")->user();
            $data["test"] = Dethi::where(['created_by' => $profile->id,'type' => 'baitap'])->get();
            return view("crm_course.khoahoc.add", $data);
        } else {
            return back()->with(
                "error",
                "Chỉ tài khoản giáo viên mới có quyền truy cập"
            );
        }
    }
    public function editCouse($id)
    {
        $data["category"] = Category::where("status", 1)->get();
        $profile = Auth::guard("customer")->user();
        $data["test"] = Dethi::where(['created_by' => $profile->id,'type' => 'baitap'])->get();
        $data["course"] = Product::where("id", $id)->first();
        return view("crm_course.khoahoc.edit", $data);
    }
    public function postCourse(Request $request)
    {
        $cat = Category::where("id", $request->category)->first("slug");
        $profile = Auth::guard("customer")->user();

        $id = $request->id_course;

        if ($id == 0) {
            $query = new Product();
            $query->name = $request->name;
            $query->user_id = $profile->id;
            $query->slug = to_slug($request->name);
            $query->price = $request->price != null ? $request->price : 0;
            $query->discount =
                $request->discount != null ? $request->discount : 0;

            if ($request->hasFile("image")) {
                $file = $request->file("image");

                $filename =
                    Str::uuid() . "." . $file->getClientOriginalExtension();
                $path = public_path("khoahocbaogom");

                $file->move($path, $filename);
                $query->images = "/khoahocbaogom/" . $filename;
            }

            $query->price = $request->price != null ? $request->price : 0;
            $query->discount =
                $request->discount != null ? $request->discount : 0;

            $query->description = $request->description;
            $query->content = $request->content;
            $query->size = $request->tasks_json; //Nội dung khóa học


            $query->category = $request->category;
            $query->cate_slug = $cat ? $cat->slug : "";
            $query->preserve = $request->ques_json; // Khóa học bao gồm
            // $query->tags = json_encode($request->tags);
            $query->ingredient = $request->so_bai_hoc; // số bài học
            $query->thickness = $request->so_buoi; // số buổi học
            $query->species = $request->faq_json; // câu hỏi thường gặp
            $query->status = 0;
            $query->home_status = 0;
            $query->save();
            if($query){
            $tasks = json_decode($request->tasks_json, true);
                foreach ($tasks as $task) {
                    foreach($task['detail_task'] as $key){
                        $dethi = Dethi::where("id", $key['test_id'])->first();
                        if($dethi){
                            $dethi->course_id = $query->id;
                            $dethi->save();
                        }
                    }
                }
            }
        } else {
            $query = Product::find($id);
            $query->name = $request->name;
            $query->user_id = $profile->id;
            $query->slug = to_slug($request->name);
            $query->price = $request->price != null ? $request->price : 0;
            $query->discount =
                $request->discount != null ? $request->discount : 0;

            if ($request->hasFile("image")) {
                $file = $request->file("image");

                $filename =
                    Str::uuid() . "." . $file->getClientOriginalExtension();
                $path = public_path("khoahocbaogom");

                $file->move($path, $filename);
                $query->images = "/khoahocbaogom/" . $filename;
            }

            $query->price = $request->price != null ? $request->price : 0;
            $query->discount =
                $request->discount != null ? $request->discount : 0;

            $query->description = $request->description;
            $query->content = $request->content;
            $query->size = $request->tasks_json; //Nội dung khóa học
            $tasks = json_decode($request->tasks_json, true);
            foreach ($tasks as $task) {
                foreach($task['detail_task'] as $key){
                    $dethi = Dethi::where("id", $key['test_id'])->first();
                    if($dethi){
                        $dethi->course_id = $query->id;
                        $dethi->save();
                    }
                }
            }
            $query->category = $request->category;
            $query->cate_slug = $cat ? $cat->slug : "";
            $query->preserve = $request->ques_json; // Khóa học bao gồm
            // $query->tags = json_encode($request->tags);
            $query->ingredient = $request->so_bai_hoc; // số bài học
            $query->thickness = $request->so_buoi; // số buổi học
            $query->species = $request->faq_json; // câu hỏi thường gặp
            $query->save();
        }

        if ($query) {
            return redirect()
                ->route("profile")
                ->with("success", "Thành công. Đợi xét duyệt");
        }
    }
    public function couseList()
    {
        // Lấy tất cả các danh mục khóa học (Category) - tương đương grades
        $categories = Category::where('status', 1)
            ->withCount(['product' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy tất cả các loại khóa học (TypeProduct) - tương đương subjects
        $types = TypeProduct::where('status', 1)
            ->orderBy('cate_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy tất cả loại chi tiết (TypeProductTwo - Nhóm khóa học) với thông tin đầy đủ
        $allCourseTypes = TypeProductTwo::where('status', 1)
            ->with('cate')  // Load danh mục
            ->orderBy('cate_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy tất cả khóa học
        $allCourses = Product::with(['cate', 'typecate', 'customer'])
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();
        
        // Hiển thị TẤT CẢ nhóm khóa học (TypeProductTwo) kể cả không có khóa học
        $coursesByType = [];
        foreach ($allCourseTypes as $courseType) {
            $coursesInType = $allCourses->filter(function($course) use ($courseType) {
                // Filter khóa học theo type_two
                return $course->type_two == $courseType->id;
            });
            
            // Luôn hiển thị nhóm khóa học, chỉ lấy 10 khóa đầu tiên
            $coursesByType[] = [
                'type' => $courseType,
                'courses' => $coursesInType->take(10),
                'count' => $coursesInType->count(), // Tổng số thực tế
                'total' => $coursesInType->count()
            ];
        }
        
        // Khóa học không thuộc nhóm nào sẽ nhóm vào "Danh sách khóa học"
        $unmatchedCourses = $allCourses->filter(function($course) {
            // Khóa học không có type_two hoặc type_two = null/0
            return !$course->type_two || $course->type_two == 0;
        });
        
        if ($unmatchedCourses->count() > 0) {
            $coursesByType[] = [
                'type' => (object)['name' => 'Danh sách khóa học', 'id' => 0],
                'courses' => $unmatchedCourses->take(10),
                'count' => $unmatchedCourses->count(), // Tổng số thực tế
                'total' => $unmatchedCourses->count()
            ];
        }
        
        // Group courseTypes theo danh mục để hiển thị filter
        $courseTypes = $allCourseTypes->groupBy('cate_id');
        
        // Thống kê số lượng khóa học
        $totalCourses = $allCourses->count();
        
        // Khởi tạo các biến selected để tránh lỗi undefined
        $selectedCategory = null;
        $selectedType = null;
        $selectedCourseType = null;
        
        return view('couse.listAll', compact(
            'coursesByType', 'categories', 'types', 'courseTypes', 'totalCourses', 
            'selectedCategory', 'selectedType', 'selectedCourseType'
        ));
    }
    public function loadMoreCourses(Request $request)
    {
        $typeId = $request->input('type_id');
        $offset = $request->input('offset', 0);
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->input('category_id');
        $typeProductId = $request->input('type_product_id');
        
        // Khởi tạo query
        $query = Product::with(['cate', 'typecate', 'customer'])
            ->where('status', 1)
            ->orderBy('id', 'DESC');
        
        // Áp dụng filter nếu có
        if ($categoryId) {
            $query->where('category', $categoryId);
        }
        if ($typeProductId) {
            $query->where('type_cate', $typeProductId);
        }
        
        // Filter theo type_two (nhóm khóa học)
        if ($typeId && $typeId != 0) {
            $query->where('type_two', $typeId);
        } else if ($typeId == 0) {
            // "Danh sách khóa học" - courses không có type_two
            $query->where(function($q) {
                $q->whereNull('type_two')->orWhere('type_two', 0);
            });
        }
        
        // Get total
        $total = $query->count();
        
        // Get paginated results
        $courses = $query->skip($offset)->take($perPage)->get();
        
        // Format response
        $coursesData = $courses->map(function($course) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'slug' => $course->slug,
                'description' => $course->description,
                'images' => $course->images,
                'price' => $course->price,
                'discount' => $course->discount,
                'ingredient' => $course->ingredient, // số bài học
                'thickness' => $course->thickness, // số buổi học
                'category' => $course->cate ? languageName($course->cate->name) : 'N/A',
                'type_cate' => $course->typecate ? languageName($course->typecate->name) : 'N/A',
                'customer' => [
                    'name' => $course->customer ? $course->customer->name : 'Quản trị viên',
                    'avatar' => $course->customer && $course->customer->avatar ? url('uploads/images/' . $course->customer->avatar) : url('frontend/images/user_icon.png')
                ]
            ];
        });
        
        return response()->json([
            'success' => true,
            'courses' => $coursesData,
            'has_more' => ($offset + $perPage) < $total,
            'remaining' => max(0, $total - ($offset + $perPage)),
            'total' => $total
        ]);
    }

    public function listCategoryMainCourse($id)
    {
        // Lấy thông tin danh mục
        $selectedCategory = Category::where('id', $id)->first();
        if (!$selectedCategory) {
            return redirect()->route('couseList')->with('error', 'Không tìm thấy danh mục');
        }

        // Load tất cả danh mục (để hiển thị grid)
        $categories = Category::where('status', 1)
            ->withCount(['product' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'ASC')
            ->get();
        
        // Chỉ lấy các loại khóa học thuộc danh mục được chọn
        $types = TypeProduct::where('status', 1)
            ->where('cate_id', $id)  // Filter theo danh mục
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy IDs của các loại khóa học thuộc danh mục này
        $typeIds = $types->pluck('id')->toArray();
        
        // Chỉ lấy các nhóm khóa học thuộc các loại của danh mục này
        $courseTypes = TypeProductTwo::where('status', 1)
            ->where('cate_id', $id)  // Filter theo danh mục
            ->orderBy('id', 'ASC')
            ->get()
            ->groupBy('cate_id');

        // Lấy tất cả nhóm khóa học (TypeProductTwo) thuộc danh mục này
        $allCourseTypes = TypeProductTwo::where('status', 1)
            ->where('cate_id', $id)
            ->with('cate')
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy khóa học theo danh mục
        $allCourses = Product::with(['cate', 'typecate', 'customer'])
            ->where('status', 1)
            ->where('category', $id)
            ->orderBy('id', 'DESC')
            ->get();

        // Hiển thị TẤT CẢ nhóm khóa học (TypeProductTwo) kể cả không có khóa học
        $coursesByType = [];
        foreach ($allCourseTypes as $courseType) {
            $coursesInType = $allCourses->filter(function($course) use ($courseType) {
                // Filter khóa học theo type_two
                return $course->type_two == $courseType->id;
            });
            
            // Luôn hiển thị nhóm khóa học, chỉ lấy 10 khóa đầu tiên
            $coursesByType[] = [
                'type' => $courseType,
                'courses' => $coursesInType->take(10),
                'count' => $coursesInType->count(), // Tổng số thực tế
                'total' => $coursesInType->count()
            ];
        }
        
        // Khóa học không thuộc nhóm nào sẽ nhóm vào "Danh sách khóa học"
        $unmatchedCourses = $allCourses->filter(function($course) {
            // Khóa học không có type_two hoặc type_two = null/0
            return !$course->type_two || $course->type_two == 0;
        });
        
        if ($unmatchedCourses->count() > 0) {
            $coursesByType[] = [
                'type' => (object)['name' => 'Danh sách khóa học', 'id' => 0],
                'courses' => $unmatchedCourses->take(10),
                'count' => $unmatchedCourses->count(), // Tổng số thực tế
                'total' => $unmatchedCourses->count()
            ];
        }
        
        // Thống kê số lượng khóa học
        $totalCourses = $allCourses->count();
        
        // Khởi tạo các biến selected
        $selectedType = null;
        $selectedCourseType = null;
        
        return view('couse.listAll', compact(
            'coursesByType', 'categories', 'types', 'courseTypes', 'totalCourses', 
            'selectedCategory', 'selectedType', 'selectedCourseType'
        ));
    }

    public function listTypeCourse($id)
    {
        // Lấy thông tin loại khóa học
        $selectedType = TypeProduct::where('id', $id)->first();
        if (!$selectedType) {
            return redirect()->route('couseList')->with('error', 'Không tìm thấy loại khóa học');
        }

        // Lấy danh mục của loại khóa học này
        $selectedCategory = Category::where('id', $selectedType->cate_id)->first();

        // Load tất cả danh mục (để hiển thị grid)
        $categories = Category::where('status', 1)
            ->withCount(['product' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('id', 'ASC')
            ->get();
        
        // Chỉ lấy các loại khóa học thuộc danh mục của loại được chọn
        $types = TypeProduct::where('status', 1)
            ->where('cate_id', $selectedType->cate_id)  // Filter theo danh mục
            ->orderBy('id', 'ASC')
            ->get();
        
        // Chỉ lấy các nhóm khóa học thuộc loại được chọn
        $courseTypes = TypeProductTwo::where('status', 1)
            ->where('type_id', $id)  // Filter theo loại
            ->orderBy('id', 'ASC')
            ->get()
            ->groupBy('type_id');

        // Lấy tất cả nhóm khóa học (TypeProductTwo) thuộc loại này
        $allCourseTypes = TypeProductTwo::where('status', 1)
            ->where('type_id', $id)
            ->with('cate')
            ->orderBy('id', 'ASC')
            ->get();
        
        // Lấy khóa học theo loại
        $allCourses = Product::with(['cate', 'typecate', 'customer'])
            ->where('status', 1)
            ->where('type_cate', $id)
            ->orderBy('id', 'DESC')
            ->get();

        // Hiển thị TẤT CẢ nhóm khóa học (TypeProductTwo) kể cả không có khóa học
        $coursesByType = [];
        foreach ($allCourseTypes as $courseType) {
            $coursesInType = $allCourses->filter(function($course) use ($courseType) {
                // Filter khóa học theo type_two
                return $course->type_two == $courseType->id;
            });
            
            // Luôn hiển thị nhóm khóa học, chỉ lấy 10 khóa đầu tiên
            $coursesByType[] = [
                'type' => $courseType,
                'courses' => $coursesInType->take(10),
                'count' => $coursesInType->count(), // Tổng số thực tế
                'total' => $coursesInType->count()
            ];
        }
        
        // Khóa học không thuộc nhóm nào sẽ nhóm vào "Danh sách khóa học"
        $unmatchedCourses = $allCourses->filter(function($course) {
            // Khóa học không có type_two hoặc type_two = null/0
            return !$course->type_two || $course->type_two == 0;
        });
        
        if ($unmatchedCourses->count() > 0) {
            $coursesByType[] = [
                'type' => (object)['name' => 'Danh sách khóa học', 'id' => 0],
                'courses' => $unmatchedCourses->take(10),
                'count' => $unmatchedCourses->count(), // Tổng số thực tế
                'total' => $unmatchedCourses->count()
            ];
        }
        
        // Thống kê số lượng khóa học
        $totalCourses = $allCourses->count();
        
        // Khởi tạo biến selected
        $selectedCourseType = null;
        
        return view('couse.listAll', compact(
            'coursesByType', 'categories', 'types', 'courseTypes', 'totalCourses', 
            'selectedCategory', 'selectedType', 'selectedCourseType'
        ));
    }

    public function couseListCate($cate_slug)
    {
        $data["danhmuc"] = Category::where("slug", $cate_slug)->first();
        $data["list"] = Product::where(['status'=> 1,'cate_slug'=>$cate_slug])->get();
        return view("couse.listCate", $data);
    }
    public function couseListType($cate_slug,$type_slug)
    {
        $data["danhmuc"] = TypeProduct::where("slug", $type_slug)->first();
        $data["list"] = Product::where(['status'=> 1,'cate_slug'=>$cate_slug,'type_slug'=>$type_slug])->paginate(12);
        return view("couse.list", $data);
    }
    public function couseListTypeTwo($cate_slug,$type_slug,$type_two_slug)
    {
        $data["danhmuc"] = TypeProductTwo::where("slug", $type_two_slug)->first();
        $data["list"] = Product::where(['status'=> 1,'cate_slug'=>$cate_slug,'type_slug'=>$type_slug,'type_two_slug'=>$type_two_slug])->paginate(12);
        return view("couse.list", $data);
    }
    public function couseDetail($slug)
    {
        $data["socical"] = AlbumAffter::where("type", 1)
            ->orderBy("id", "DESC")
            ->get();
        $data["detail"] = Product::where("slug", $slug)
            ->orderBy("id", "DESC")
            ->first();
        $data["productlq"] = Product::where(
            "category",
            $data["detail"]->category
        )->get([
            "id",
            "name",
            "images",
            "size",
            "price",
            "slug",
            "cate_slug",
            "type_slug",
            "description",
        ]);
        $profile = Auth::guard("customer")->user();
        if ($profile) {
            $billsUser = BillCourse::where([
                "customer_id" => $profile->id,
                "product_id" => $data["detail"]->id,
            ])->first();
            if($billsUser){
                $data["paymendCourse"] = $billsUser && $billsUser->status != 0 ? 1 : 0;
            }else{
                $data["paymendCourse"] = null;
            }
        }
        return view("couse.detail", $data);
    }
    public function dangkykhoahoc($couserid, $couserslug)
    {
        $product = Product::where([
            "id" => $couserid,
            "slug" => $couserslug,
        ])->first();
        if (Auth::guard("customer")->user() != null) {
            $profile = Auth::guard("customer")->user();
        } else {
            $profile = "";
        }
        $existBill = BillCourse::where([
            "customer_id" => $profile->id,
            "product_id" => $product->id,
        ])->first();
        if ($existBill) {
            return back()->with("error", "Bạn đã đăng ký khóa học này");
        } else {
            $course = new BillCourse();
            $course->bill_id = rand();
            $course->customer_id = $profile->id;
            $course->product_id = $couserid;
            $course->status = 0;
            $course->save();
            if ($course) {
                return view("cart.courseSuccess", compact("course", "product"));
            }
        }
    }

    public function themvaoGioHangKhoaHoc(Request $request)
    {
        $product = Product::where([
            "id" => $request->product_id,
            "slug" => $request->slug,
        ])->first();

        $cart = session()->get('cart', []);

        if(isset($cart[$request->product_id]) && $cart[$request->product_id]['type'] == 'product') {
            $cart[$request->product_id]['quantity'] = $cart[$request->product_id]['quantity'] + 1;
        } else {
            $cart[$request->product_id] = [
                "idpro" => $request->product_id,
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "discount" => $product->discount,
                "image" => $product->images,
                "type" => 'product'
            ];
        }

        session()->put('cart', $cart);

        $countCart = 0;
        foreach($cart as $item){
            $countCart += $item['quantity'];
        }
        return response()->json(['data' => $cart, 'message' => 'Thêm vào giỏ hàng thành công', 'success' => true, 'count' => $countCart]);
    }
}
