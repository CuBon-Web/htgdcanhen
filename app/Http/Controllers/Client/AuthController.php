<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Customer;
use App\User;
use Auth,Validator;
use App\Notifications\CustomerRigisterNotify;
use App\Providers\RouteServiceProvider;
use App\Notifications\testNoti;
use App\models\Bill\BillDetail;
use App\models\Bill\Bill;

use App\models\BillCourse;
use App\models\product\Product;
use App\models\dethi\Dethi;
use App\models\Bill\BillDethi;
use App\models\dethi\ExamSession;
use Illuminate\Support\Facades\Hash;
use App\models\BillDocument;
use App\models\SchoolClass;

class AuthController extends Controller
{
    public function profile() {
        $data['profile'] = Auth::guard('customer')->user();
        // khóa học đã mua
        if($data['profile']->type == 1){
         
            // Số lượng đề thi theo trạng thái
            $data['dethi_tu_do'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','dethi')
                ->where('access_type','all')
                ->where('status',1)
                ->count();
            $data['dethi_giao_lop'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','dethi')
                ->where('access_type','class')
                ->where('status',1)
                ->count();
            $data['dethi_thoi_gian'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','dethi')
                ->where('access_type','time_limited')
                ->where('status',1)
                ->count();
            $data['dethi_chua_xuat_ban'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','dethi')
                ->where('status',0)
                ->count();
            $data['baitap_tu_do'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','baitap')
                ->where('access_type','all')
                ->where('status',1)
                ->count();
            $data['baitap_giao_lop'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','baitap')
                ->where('access_type','class')
                ->where('status',1)
                ->count();
            $data['baitap_thoi_gian'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','baitap')
                ->where('access_type','time_limited')
                ->where('status',1)
                ->count();
            $data['baitap_chua_xuat_ban'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','baitap')
                ->where('status',0)
                ->count();
            $data['game_tu_do'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','game')
                ->where('access_type','all')
                ->where('status',1)
                ->count();
            $data['game_giao_lop'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','game')
                ->where('access_type','class')
                ->where('status',1)
                ->count();
            $data['game_thoi_gian'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','game')
                ->where('access_type','time_limited')
                ->where('status',1)
                ->count();
            $data['game_chua_xuat_ban'] = Dethi::where('created_by',$data['profile']->id)
                ->where('type','game')
                ->where('status',0)
                ->count();
            
            // Lấy số lượng học sinh của giáo viên
            // Lấy tất cả các lớp mà giáo viên là chủ nhiệm
            $teacherClasses = SchoolClass::where('homeroom_teacher_id', $data['profile']->id)
                ->where('is_active', true)
                ->get();
            
            if($teacherClasses->isNotEmpty()){
                // Lấy tất cả class_code của các lớp
                $classCodes = $teacherClasses->pluck('class_code')->filter()->toArray();
                
                if(!empty($classCodes)){
                    // Đếm số lượng học sinh có class_code chứa bất kỳ mã lớp nào
                    $data['hoc_sinh_count'] = Customer::where('type', '=', 0) // Loại trừ giáo viên
                        ->where(function($query) use ($classCodes) {
                            foreach($classCodes as $classCode) {
                                $query->orWhereJsonContains('class_code', $classCode);
                            }
                        })
                        ->distinct()
                        ->count('id');
                } else {
                    $data['hoc_sinh_count'] = 0;
                }
            } else {
                $data['hoc_sinh_count'] = 0;
            }
           
        }else if($data['profile']->type == 3){
            $data['dethi_tu_do'] = Dethi::where('type','dethi')
                ->where('access_type','all')
                ->where('status',1)
                ->count();
            $data['dethi_giao_lop'] = Dethi::where('type','dethi')
                ->where('access_type','class')
                ->where('status',1)
                ->count();
            $data['dethi_thoi_gian'] = Dethi::where('type','dethi')
                ->where('access_type','time_limited')
                ->where('status',1)
                ->count();
            $data['dethi_chua_xuat_ban'] = Dethi::where('type','dethi')
                ->where('status',0)
                ->count();
            $data['baitap_tu_do'] = Dethi::where('type','baitap')
                ->where('access_type','all')
                ->where('status',1)
                ->count();
            $data['baitap_giao_lop'] = Dethi::where('type','baitap')
                ->where('access_type','class')
                ->where('status',1)
                ->count();
            $data['baitap_thoi_gian'] = Dethi::where('type','baitap')
                ->where('access_type','time_limited')
                ->where('status',1)
                ->count();
            $data['baitap_chua_xuat_ban'] = Dethi::where('type','baitap')
                ->where('status',0)
                ->count();
            $data['game_tu_do'] = Dethi::where('type','game')
                ->where('access_type','all')
                ->where('status',1)
                ->count();
            $data['game_giao_lop'] = Dethi::where('type','game')
                ->where('access_type','class')
                ->where('status',1)
                ->count();
            $data['game_thoi_gian'] = Dethi::where('type','game')
                ->where('access_type','time_limited')
                ->where('status',1)
                ->count();
            $data['game_chua_xuat_ban'] = Dethi::where('type','game')
                ->where('status',0)
                ->count();
            $data['hoc_sinh_count'] = Customer::where('type','=',0)->count();
            $data['giao_vien_count'] = Customer::where('type','=',1)->count();
        }
        else{
            // Học sinh: Thống kê đề thi, bài tập, game đã làm và chưa làm
            $studentId = $data['profile']->id;
            
            // Thống kê đề thi (dethi)
            // Đã làm: có session với status >= 1 (đã hoàn thành hoặc đang làm)
            $data['dethi_da_lam'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'dethi');
                })
                ->where('status', '>=', 1)
                ->distinct('dethi_id')
                ->count('dethi_id');
            
            // Đang làm: có session với status = 0 hoặc 1 (chưa hoàn thành)
            $data['dethi_dang_lam'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'dethi');
                })
                ->where('status', 0)
                ->distinct('dethi_id')
                ->count('dethi_id');
            
            // Đã hoàn thành: có session với status = 2 (hoàn thành)
            $data['dethi_hoan_thanh'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'dethi');
                })
                ->where('status', 2)
                ->distinct('dethi_id')
                ->count('dethi_id');
            
            // Thống kê bài tập (baitap)
            // Đã làm: có session với status >= 1
            $data['baitap_da_lam'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'baitap');
                })
                ->where('status', '>=', 1)
                ->distinct('dethi_id')
                ->count('dethi_id');
            
            // Đang làm: có session với status = 0
            $data['baitap_dang_lam'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'baitap');
                })
                ->where('status', 0)
                ->distinct('dethi_id')
                ->count('dethi_id');
            
            // Đã hoàn thành: có session với status = 2
            $data['baitap_hoan_thanh'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'baitap');
                })
                ->where('status', 2)
                ->distinct('dethi_id')
                ->count('dethi_id');
            
            // Thống kê game
            // Đã làm: có session với status >= 1
            $data['game_da_lam'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'game');
                })
                ->where('status', '>=', 1)
                ->distinct('dethi_id')
                ->count('dethi_id');
            
            // Đang làm: có session với status = 0
            $data['game_dang_lam'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'game');
                })
                ->where('status', 0)
                ->distinct('dethi_id')
                ->count('dethi_id');
            
            // Đã hoàn thành: có session với status = 2
            $data['game_hoan_thanh'] = ExamSession::where('student_id', $studentId)
                ->whereHas('dethi', function($q) {
                    $q->where('type', 'game');
                })
                ->where('status', 2)
                ->distinct('dethi_id')
                ->count('dethi_id');
        }
        $data['product'] = Product::where('user_id',$data['profile']->id)->orderBy('id',"DESC")->get();
         return view('crm_course.home',$data);
    }
    public function documentList(){
        $data['profile'] = Auth::guard('customer')->user();
        $data['document'] = BillDocument::with(['document'])->where('customer_id',$data['profile']->id)->orderBy('id','DESC')->get();
        return view('crm_course.tai-lieu-da-mua',$data);
    }
    public function editProfile(){
        return view('crm_course.editProfile');
    }
    public function postEditProfile(Request $request){
        $data['profile'] = Auth::guard('customer')->user();
    }
    public function postChangePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:new_password',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $profile = Auth::guard('customer')->user();
        if(Hash::check($request->password, $profile->password)){
            $profile->password = bcrypt($request->new_password);
            $profile->save();
            return back()->with('success', 'Cập nhật thành công')->withInput();
        }else{
            return back()->with('error', 'Mật khẩu cũ không đúng')->withInput();
        }
    }
    public function postShowProfile(Request $request) {
        $profile = Auth::guard('customer')->user();
        $data = Customer::findOrFail($profile->id);
        
        // Xử lý nhiều mã lớp nếu có
        if($request->filled('class_code')){
            $classCodes = $this->parseClassCodes($request->class_code);
            $validClassCodes = [];
            $invalidCodes = [];
            
            foreach($classCodes as $classCode){
                $class = \App\models\SchoolClass::where('class_code', $classCode)
                    ->where('is_active', true)
                    ->first();
                
                if($class){
                    $validClassCodes[] = $classCode;
                } else {
                    $invalidCodes[] = $classCode;
                }
            }
            
            if(!empty($invalidCodes)){
                return back()
                    ->withErrors(['class_code' => 'Một số mã lớp không tồn tại hoặc đã ngừng hoạt động: ' . implode(', ', $invalidCodes)])
                    ->withInput();
            }
            
            // Lưu class_code dưới dạng JSON
            $data->class_codes = $validClassCodes;
        } else {
            // Nếu để trống thì xóa tất cả lớp
            $data->class_codes = [];
        }

        // Xử lý avatar nếu có upload
        if ($request->hasFile('avatar')) {
            $imgAvatar = $request->file('avatar');
            $extension = $imgAvatar->getClientOriginalExtension();
            $nameImg = uniqid() . '.' . $extension;

            // Di chuyển ảnh đến thư mục public/uploads/images
            $imgAvatar->move(public_path('/uploads/images'), $nameImg);

            // Xoá ảnh cũ nếu có (tuỳ chọn)
                if ($data->avatar && file_exists(public_path('/uploads/images/' . $data->avatar))) {
                    unlink(public_path('/uploads/images/' . $data->avatar));
                }

                $data->avatar = $nameImg;
            }

            $data->email = $request->email;
            $data->name = $request->name;
            $data->phone = $request->phone;
            $data->note = $request->note;
            $data->status = 0;
            $data->save();

            return back()->with('success', 'Cập nhật thành công')->withInput();
    }
    public function myCouseGiaoVien(){
        $profile = Auth::guard('customer')->user();
        $data['product'] = Product::where('user_id',$profile->id)->orderBy('id',"DESC")->get();
        $data['courser'] = BillCourse::with(['product'])->where('customer_id',$profile->id)->orderBy('id',"DESC")->get();
        return view('crm_course.khoahoc.list',$data);
    }
    public function myCouse(){
        $profile = Auth::guard('customer')->user();
        $data['courser'] = BillCourse::with(['product'])->where('customer_id',$profile->id)->get();
        return view('auth.myCouse',$data);
    }





    public function login()
    {
        return view('auth.login');
    }
    public function postLogin(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'phone' => 'required',
                'password' => 'required',
            ], [
                'phone.required' => 'Số điện thoại hoặc email không được để trống',
                'password.required' => 'Mật khẩu không được để trống',
            ]);
            
            $login = $request->phone;
            
            // Kiểm tra xem input là email hay số điện thoại
            $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
            
            // Tìm customer theo email hoặc số điện thoại
            if($isEmail) {
                $cus = Customer::where('email', $login)->first();
            } else {
                $cus = Customer::where('phone', $login)->first();
            }
            
            if($cus != null){
                // Kiểm tra status
                if($cus->status == 0){
                    // Kiểm tra password có tồn tại và không null không
                    if(empty($cus->password) || $cus->password === null){
                        return back()->with('error', 'Tài khoản chưa được thiết lập mật khẩu. Vui lòng liên hệ quản trị viên.')->withInput();
                    }
                    
                    // Kiểm tra password thủ công vì Auth::attempt() mặc định chỉ tìm theo email
                    if(Hash::check($request->password, $cus->password)){
                        // Đăng nhập thủ công
                        Auth::guard('customer')->login($cus);
                        $request->session()->regenerate();
                        return redirect()->route('profile')->with('success', 'Đăng nhập thành công');
                    }
                    else{
                        return back()->with('error', 'Đăng nhập thất bại vui lòng kiểm tra thông tin')->withInput();
                    }
                }
                else{
                    return view('auth.openAcount');
                }
            }
            else{
                return back()->with('error', 'Vui lòng đăng ký hoặc kiểm tra lại thông tin trước khi đăng nhập')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi đăng nhập. Vui lòng thử lại.')->withInput();
        }

    }
    public function register()
    {
        return view('auth.register');
    }
    public function postRegister(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'email' => 'email|unique:customer',
                'password' => 'required|min:8',
                'password_confirm' => 'required|min:8|same:password',
                'name' => 'required',
                'phone' => 'required|unique:customer',
                'class_code' => 'nullable|string|max:200' // Tăng max để cho phép nhiều mã lớp
            ]);
            if ($validator->fails()) {
                return back()
                            ->withErrors($validator)
                            ->withInput();
            }
            
            // Xử lý nhiều mã lớp nếu có
            $validClassCodes = [];
            if($request->filled('class_code')){
                $classCodes = $this->parseClassCodes($request->class_code);
                $invalidCodes = [];
                
                foreach($classCodes as $classCode){
                    $class = \App\models\SchoolClass::where('class_code', $classCode)
                        ->where('is_active', true)
                        ->first();
                    
                    if($class){
                        $validClassCodes[] = $classCode;
                    } else {
                        $invalidCodes[] = $classCode;
                    }
                }
                
                if(!empty($invalidCodes)){
                    return back()
                        ->withErrors(['class_code' => 'Một số mã lớp không tồn tại hoặc đã ngừng hoạt động: ' . implode(', ', $invalidCodes)])
                        ->withInput();
                }
            }
            
            $data = new Customer();
            $data->email = $request->email;
            $data->password = bcrypt($request->password);
            $data->name = $request->name;
            $data->phone = $request->phone;
            $data->note = $request->note;
            $data->status = 0; // Đặt status = 0 để có thể đăng nhập ngay
            $data->type = $request->type;
            // Lưu class_code dưới dạng JSON
            if(!empty($validClassCodes)){
                $data->class_codes = $validClassCodes;
            }
            $data->save();
            if($data){
                // Tự động đăng nhập sau khi đăng ký thành công
                $credentials = $request->only('email', 'password');
                if(Auth::guard('customer')->attempt($credentials)){
                    $request->session()->regenerate();
                    return redirect()->intended('/')->with('success', 'Đăng ký và đăng nhập thành công');
                } else {
                    return redirect()->route('login')->with('success', 'Đăng ký tài khoản thành công, vui lòng đăng nhập');
                }
            }
    }
    public function postRegisterGiaovien(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:customer',
                'password' => 'required|min:8',
                'password_confirm' => 'required|min:8|same:password',
                'name' => 'required',
                'phone' => 'required'
            ]);
            if ($validator->fails()) {
                return back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $data = new Customer();
            $data->email = $request->email;
            $data->password = bcrypt($request->password);
            $data->name = $request->name;
            $data->phone = $request->phone;
            $data->note = $request->note;
            $data->caphoc = $request->caphoc;
            $data->bomon = $request->bomon;
            $data->status = 0; // Đặt status = 0 để có thể đăng nhập ngay
            $data->type = $request->type;
            $data->save();
            if($data){
                // Tự động đăng nhập sau khi đăng ký thành công
                $credentials = $request->only('email', 'password');
                if(Auth::guard('customer')->attempt($credentials)){
                    $request->session()->regenerate();
                    return redirect()->intended('/')->with('success', 'Đăng ký và đăng nhập thành công');
                } else {
                    return redirect()->route('login')->with('success', 'Đăng ký tài khoản thành công, vui lòng đăng nhập');
                }
            }
    }
    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect('/');
    }

    /**
     * Parse nhiều mã lớp từ string (hỗ trợ phân cách bằng dấu phẩy, khoảng trắng, hoặc xuống dòng)
     */
    private function parseClassCodes($classCodeString)
    {
        // Loại bỏ khoảng trắng thừa và chuyển thành mảng
        $codes = preg_split('/[,\s\n\r]+/', trim($classCodeString));
        
        // Loại bỏ các phần tử rỗng và chuyển thành chữ hoa
        $codes = array_filter(array_map(function($code) {
            return strtoupper(trim($code));
        }, $codes));
        
        // Loại bỏ các mã trùng lặp
        return array_unique(array_values($codes));
    }
    public function accoungOrder(){
        $profile = Auth::guard('customer')->user();
        $data['bill'] = Bill::where('code_customer',$profile->id)->get();
        return view('auth.account-order',$data);
    }
    public function accoungOrderDetail($billid){
        $data['bill'] = Bill::where('code_bill',$billid)->first();
        $data['billdetail'] = BillDetail::where('code_bill',$billid)->get();
        return view('auth.account-order-detail',$data);
    }

    // Quên mật khẩu
    public function forgotPassword(){
        return view('auth.forgotPassword');
    }
    public function postForgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:customer',
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
        ],[
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'email.exists' => 'Email không tồn tại',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải ít nhất 8 ký tự',
            'confirm_password.required' => 'Mật khẩu không được để trống',
            'confirm_password.min' => 'Mật khẩu phải ít nhất 8 ký tự',
            'confirm_password.same' => 'Mật khẩu không khớp',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = Customer::where('email',$request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();
        return redirect()->route('login')->with('success', 'Thay đổi mật khẩu thành công');
    }
    /**
     * Lấy danh sách học sinh
     */
    public function listStudent(Request $request){
        $profile = Auth::guard("customer")->user();
        
        // Kiểm tra quyền truy cập
        if($profile->type != 1 && $profile->type != 3){
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
        
        // Khởi tạo query base
        if($profile->type == 1){
            // Teacher: Load học sinh thuộc các lớp mà giáo viên là chủ nhiệm
            $teacherClasses = SchoolClass::where('homeroom_teacher_id', $profile->id)
                ->where('is_active', true)
                ->get();
            
            if($teacherClasses->isNotEmpty()){
                // Lấy tất cả class_code của các lớp
                $classCodes = $teacherClasses->pluck('class_code')->filter()->toArray();
                
                if(!empty($classCodes)){
                    // Tìm học sinh có class_code chứa bất kỳ mã lớp nào
                    $query = Customer::where('type', 0)
                        ->where(function($q) use ($classCodes) {
                            foreach($classCodes as $classCode) {
                                $q->orWhereJsonContains('class_code', $classCode);
                            }
                        })
                        ->distinct();
                } else {
                    // Không có lớp nào, trả về query rỗng
                    $query = Customer::where('type', 0)->where('id', 0);
                }
            } else {
                // Không có lớp nào, trả về query rỗng
                $query = Customer::where('type', 0)->where('id', 0);
            }
        } else {
            // Super Admin: Load tất cả học sinh
            $query = Customer::where('type', 0);
            
            // Filter theo giáo viên (chỉ admin mới có)
            // Lấy học sinh từ các lớp mà giáo viên đó là chủ nhiệm
            if ($request->filled('teacher_id')) {
                $teacherClasses = SchoolClass::where('homeroom_teacher_id', $request->teacher_id)
                    ->where('is_active', true)
                    ->get();
                
                if($teacherClasses->isNotEmpty()){
                    // Lấy tất cả class_code của các lớp
                    $classCodes = $teacherClasses->pluck('class_code')->filter()->toArray();
                    
                    if(!empty($classCodes)){
                        // Tìm học sinh có class_code chứa bất kỳ mã lớp nào
                        $query->where(function($q) use ($classCodes) {
                            foreach($classCodes as $classCode) {
                                $q->orWhereJsonContains('class_code', $classCode);
                            }
                        })
                        ->distinct();
                    } else {
                        // Không có lớp nào, trả về query rỗng
                        $query->where('id', 0);
                    }
                } else {
                    // Không có lớp nào, trả về query rỗng
                    $query->where('id', 0);
                }
            }
        }
        
        // Áp dụng các filter chung
        $this->applyBasicFilters($query, $request);
        $this->applySubscriptionFilter($query, $request);
        $this->applyExerciseStatusFilter($query, $request);
        $this->applyHomeworkStatusFilter($query, $request);
        
        // Lấy dữ liệu với phân trang
        $data = $query->orderBy('id', 'DESC')->paginate(20);
        
        return view('crm_course.hocsinh.list', compact('data'));
    }
    
    /**
     * Áp dụng filter cơ bản (name, email, phone, status)
     */
    private function applyBasicFilters($query, Request $request)
    {
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    }
    
    /**
     * Áp dụng filter theo trạng thái subscription
     */
    private function applySubscriptionFilter($query, Request $request)
    {
        if (!$request->filled('subscription_status')) {
            return;
        }
        
        $subscriptionStatus = $request->subscription_status;
        
        switch ($subscriptionStatus) {
            case 'no_subscription':
                $query->whereDoesntHave('subscriptions');
                break;
                
            case 'active':
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'active')->where('expires_at', '>', now());
                });
                break;
                
            case 'expired':
                $query->whereHas('subscriptions', function($q) {
                    $q->where('expires_at', '<=', now());
                });
                break;
                
            case 'cancelled':
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'cancelled');
                });
                break;
        }
    }
    
    /**
     * Áp dụng filter theo trạng thái bài tập (exercise_status)
     */
    private function applyExerciseStatusFilter($query, Request $request)
    {
        if (!$request->filled('exercise_status')) {
            return;
        }
        
        $exerciseStatus = $request->exercise_status;
        $status = ($exerciseStatus == '1') ? 2 : 1; // 1 = chưa làm, 2 = đã làm
        
        $query->whereHas('examSessions', function($q) use ($status) {
            $q->where('status', $status)
              ->where(function($subQ) {
                  $subQ->where('type', 'baitap')
                       ->orWhereHas('exam', function($examQ) {
                           $examQ->where('type', 'baitap');
                       });
              });
        });
    }
    
    /**
     * Áp dụng filter theo trạng thái bài tập về nhà (homework_status)
     */
    private function applyHomeworkStatusFilter($query, Request $request)
    {
        if (!$request->filled('homework_status')) {
            return;
        }
        
        $homeworkStatus = $request->homework_status;
        $status = ($homeworkStatus == '1') ? 2 : 1; // 1 = chưa làm, 2 = đã làm
        
        $query->whereHas('examSessions', function($q) use ($status) {
            $q->where('status', $status)
              ->where(function($subQ) {
                  $subQ->where('type', 'homework')
                       ->orWhereHas('exam', function($examQ) {
                           $examQ->where('type', 'homework');
                       });
              });
        });
    }

    public function studentDetail($id)
    {
        $profile = Auth::guard("customer")->user();
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem thông tin học sinh');
        }

        $student = Customer::with('belong_teacher')
            ->where('type', 0)
            ->findOrFail($id);

        if ($profile->type == 1) {
            // Kiểm tra xem học sinh có thuộc lớp nào mà giáo viên là chủ nhiệm không
            $teacherClasses = SchoolClass::where('homeroom_teacher_id', $profile->id)
                ->where('is_active', true)
                ->pluck('class_code')
                ->filter()
                ->toArray();
            
            if(empty($teacherClasses)){
                return redirect()->route('listStudent')->with('error', 'Bạn không có lớp nào được quản lý');
            }
            
            // Lấy class_code của học sinh
            $studentClassCodes = $student->class_code ?? [];
            if(!is_array($studentClassCodes)){
                $studentClassCodes = [];
            }
            
            // Kiểm tra xem có class_code nào trùng không
            $hasAccess = false;
            foreach($teacherClasses as $classCode){
                if(in_array($classCode, $studentClassCodes)){
                    $hasAccess = true;
                    break;
                }
            }
            
            if(!$hasAccess){
                return redirect()->route('listStudent')->with('error', 'Bạn không thể xem học sinh này');
            }
        }

        if ($profile->type == 0 && $profile->id !== $student->id) {
            return redirect()->route('listStudent')->with('error', 'Bạn không thể xem học sinh này');
        }
        return view('crm_course.hocsinh.detail', [
            'student' => $student,
        ]);
    }
    public function listTeacher(Request $request){
        $profile = Auth::guard("customer")->user();
        
        if($profile->type == 3){
            // Super Admin: Load tất cả trung tâm với filter
            $query = Customer::with('students')->where('type', 1);
            
            // Apply filters
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            
            if ($request->filled('email')) {
                $query->where('email', 'like', '%' . $request->email . '%');
            }
            
            if ($request->filled('phone')) {
                $query->where('phone', 'like', '%' . $request->phone . '%');
            }
            
            if ($request->filled('address')) {
                $query->where('address', 'like', '%' . $request->address . '%');
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('student_count')) {
                $studentCount = $request->student_count;
                if ($studentCount === '0') {
                    $query->whereDoesntHave('students');
                } elseif ($studentCount === '1-10') {
                    $query->withCount('students')->having('students_count', '>=', 1)->having('students_count', '<=', 10);
                } elseif ($studentCount === '11-50') {
                    $query->withCount('students')->having('students_count', '>=', 11)->having('students_count', '<=', 50);
                } elseif ($studentCount === '51-100') {
                    $query->withCount('students')->having('students_count', '>=', 51)->having('students_count', '<=', 100);
                } elseif ($studentCount === '100+') {
                    $query->withCount('students')->having('students_count', '>', 100);
                }
            } else {
                // Always load students count for display
                $query->withCount('students');
            }
            
            $data = $query->orderBy('id', 'DESC')->paginate(20);
            return view('crm_course.trungtam.list', compact('data'));
        } else {
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
    }

    /**
     * Đổi mật khẩu cho học sinh
     */
    public function bulkChangePassword(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        
        // Check permission - only admin can bulk change password
        if($profile->type != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện chức năng này'
            ], 403);
        }

        $studentIds = $request->input('student_ids', []);
        $newPassword = $request->input('new_password');

        // Validation
        if(empty($studentIds) || !is_array($studentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một học sinh'
            ], 400);
        }

        if(empty($newPassword) || strlen($newPassword) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu phải có ít nhất 6 ký tự'
            ], 400);
        }

        try {
            $updatedCount = 0;
            $failedStudents = [];

            foreach($studentIds as $studentId) {
                $student = Customer::where('id', $studentId)
                    ->where('type', 0) // Only students
                    ->first();

                if($student) {
                    $student->password = bcrypt($newPassword);
                    if($student->save()) {
                        $updatedCount++;
                    } else {
                        $failedStudents[] = $student->name;
                    }
                }
            }

            if($updatedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Đã đổi mật khẩu thành công cho {$updatedCount} học sinh",
                    'updated_count' => $updatedCount,
                    'failed_students' => $failedStudents
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể đổi mật khẩu cho bất kỳ học sinh nào'
                ], 500);
            }

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa học sinh
     */
    public function bulkDeleteStudent(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        
        // Check permission - only admin can bulk delete
        if($profile->type != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện chức năng này'
            ], 403);
        }

        $studentIds = $request->input('student_ids', []);

        // Validation
        if(empty($studentIds) || !is_array($studentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một học sinh'
            ], 400);
        }

        try {
            $deletedCount = 0;
            $failedStudents = [];

            foreach($studentIds as $studentId) {
                $student = Customer::where('id', $studentId)
                    ->where('type', 0) // Only students
                    ->first();

                if($student) {
                    
                    
                    // Delete exam sessions
                    ExamSessionV2::where('customer_id', $student->id)->delete();

                    if($student->delete()) {
                        $deletedCount++;
                    } else {
                        $failedStudents[] = $student->name;
                    }
                }
            }

            if($deletedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Đã xóa thành công {$deletedCount} học sinh",
                    'deleted_count' => $deletedCount,
                    'failed_students' => $failedStudents
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa bất kỳ học sinh nào'
                ], 500);
            }

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hạn chế truy cập cho học sinh
     */
    public function bulkRestrictAccess(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        
        // Check permission - only admin can bulk restrict access
        if($profile->type != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện chức năng này'
            ], 403);
        }

        $studentIds = $request->input('student_ids', []);
        $reason = $request->input('reason');
        $note = $request->input('note');

        // Validation
        if(empty($studentIds) || !is_array($studentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một học sinh'
            ], 400);
        }


        try {
            $restrictedCount = 0;
            $failedStudents = [];

            foreach($studentIds as $studentId) {
                $student = Customer::where('id', $studentId)
                    ->where('type', 0) // Only students
                    ->first();

                if($student) {
                    // Set status to restricted (using status field)
                    // Status: 0 = active, 1 = inactive, 2 = suspended, 3 = restricted
                    $student->status = 3;
                    
                    // Store restriction info in a JSON field or notes field
                    $restrictionData = [
                        'reason' => $reason,
                        'note' => $note,
                        'restricted_at' => now()->toDateTimeString(),
                        'restricted_by' => $profile->id
                    ];
                    
                    // Try to save in notes field or create a simple concatenated string
                    $student->note = json_encode($restrictionData);
                    
                    if($student->save()) {
                        $restrictedCount++;
                    } else {
                        $failedStudents[] = $student->name;
                    }
                }
            }

            if($restrictedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Đã hạn chế truy cập thành công cho {$restrictedCount} học sinh",
                    'restricted_count' => $restrictedCount,
                    'failed_students' => $failedStudents
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hạn chế truy cập cho bất kỳ học sinh nào'
                ], 500);
            }

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription plans for dropdowns
     */
 

    /**
     * Hạn chế gói dịch vụ cho học sinh
     */
    public function bulkRestrictSubscription(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        
        // Check permission - only admin can bulk restrict subscription
        if($profile->type != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện chức năng này'
            ], 403);
        }

        $studentIds = $request->input('student_ids', []);
        $reason = $request->input('reason');
        $note = $request->input('note');

        // Validation
        if(empty($studentIds) || !is_array($studentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một học sinh'
            ], 400);
        }

        if(empty($reason)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn lý do hạn chế gói dịch vụ'
            ], 400);
        }

        try {
            $restrictedCount = 0;
            $failedStudents = [];

            foreach($studentIds as $studentId) {
                $student = Customer::where('id', $studentId)
                    ->where('type', 0) // Only students
                    ->first();

                if($student) {
                    // Cancel all active subscriptions
                    $activeSubscriptions = $student->subscriptions()
                        ->where('status', 'active')
                        ->where('expires_at', '>', now())
                        ->get();

                    foreach($activeSubscriptions as $subscription) {
                        $subscription->status = 'cancelled';
                        $subscription->cancelled_at = now();
                        $subscription->notes = 'Hạn chế gói dịch vụ - ' . $reason . ($note ? ' - ' . $note : '');
                        $subscription->save();
                    }

                    $restrictedCount++;
                }
            }

            if($restrictedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Đã hạn chế gói dịch vụ thành công cho {$restrictedCount} học sinh",
                    'restricted_count' => $restrictedCount,
                    'failed_students' => $failedStudents
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hạn chế gói dịch vụ cho bất kỳ học sinh nào'
                ], 500);
            }

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }


 
    private function getSessionTotalTime(ExamSessionV2 $session): int
    {
        $timeTracking = $session->time_tracking;
        if (is_array($timeTracking) && isset($timeTracking['total_time'])) {
            return (int) $timeTracking['total_time'];
        }

        return $session->getDuration() ?? 0;
    }

    private function formatDuration(?int $seconds): string
    {
        if (!$seconds || $seconds <= 0) {
            return '0s';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        if ($minutes > 0) {
            $parts[] = $minutes . 'm';
        }
        $parts[] = $secs . 's';

        return implode(' ', $parts);
    }

    /**
     * Tính Band Score theo bảng IELTS dựa trên số câu trả lời đúng
     */
    private function calculateBandScore($correctAnswers)
    {
        if ($correctAnswers >= 39) {
            return 9.0;
        } elseif ($correctAnswers >= 37) {
            return 8.5;
        } elseif ($correctAnswers >= 35) {
            return 8.0;
        } elseif ($correctAnswers >= 33) {
            return 7.5;
        } elseif ($correctAnswers >= 30) {
            return 7.0;
        } elseif ($correctAnswers >= 27) {
            return 6.5;
        } elseif ($correctAnswers >= 23) {
            return 6.0;
        } elseif ($correctAnswers >= 20) {
            return 5.5;
        } elseif ($correctAnswers >= 16) {
            return 5.0;
        } elseif ($correctAnswers >= 13) {
            return 4.5;
        } elseif ($correctAnswers >= 10) {
            return 4.0;
        } elseif ($correctAnswers >= 7) {
            return 3.5;
        } elseif ($correctAnswers >= 5) {
            return 3.0;
        } elseif ($correctAnswers >= 3) {
            return 2.5;
        } else {
            return 0.0;
        }
    }
}
