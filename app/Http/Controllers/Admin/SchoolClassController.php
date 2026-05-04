<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\models\SchoolClass;
use Auth;   
class SchoolClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index()
    {   
        
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
        // Chỉ hiển thị lớp mà giáo viên đã tạo
        $classes = SchoolClass::where('homeroom_teacher_id', $profile->id)
            ->with('homeroomTeacher')
            ->orderBy('id', 'desc')
            ->paginate(20);
        
        // Tính số học sinh cho mỗi lớp
        foreach ($classes as $class) {
            $class->students_count = $class->students_count; // Sử dụng accessor
        }
        
        return view('crm_course.classes.index', compact('classes'));
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
    }

    /**
     * Show the form for creating a new class.
     */
    public function create()
    {
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
        return view('crm_course.classes.create');
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(Request $request)
    {
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
         $validated = $request->validate([
            'class_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('school_classes', 'class_name')->where(function ($query) use ($profile) {
                    return $query->where('homeroom_teacher_id', $profile->id);
                })
            ],
            'school_year' => 'nullable|integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'class_name.unique' => 'Bạn đã tạo lớp với tên này rồi. Vui lòng chọn tên khác.'
        ]);

        // Tự động generate class_code
        $validated['class_code'] = $this->generateClassCode();
        
        // Tự động gán giáo viên chủ nhiệm là người tạo
        $validated['homeroom_teacher_id'] = $profile->id;

        SchoolClass::create($validated);

        return redirect()->route('classes.index')->with('success', 'Tạo lớp học thành công!');
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
    }
    
    /**
     * Generate unique class code
     */
    private function generateClassCode()
    {
        do {
            // Tạo mã 6 ký tự ngẫu nhiên (chữ + số)
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (SchoolClass::where('class_code', $code)->exists());
        
        return $code;
    }

    /**
     * Display the specified class.
     */
    public function show($id)
    {
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
        $class = SchoolClass::where('id', $id)
            ->where('homeroom_teacher_id', $profile->id)
            ->firstOrFail();
        
        // Load students manually
        $class->students = $class->getStudents();
        return view('crm_course.classes.show', compact('class'));
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit($id)
    {
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
        $class = SchoolClass::where('id', $id)
            ->where('homeroom_teacher_id', $profile->id)
            ->firstOrFail();
        return view('crm_course.classes.edit', compact('class'));
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
    }

    /**
     * Update the specified class in storage.
     */
    public function update(Request $request, $id)
    {
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
        $class = SchoolClass::findOrFail($id);
        
        // Kiểm tra quyền: chỉ giáo viên tạo lớp mới được sửa
        if($class->homeroom_teacher_id != $profile->id){
            return redirect()->back()->with('error','Bạn không có quyền sửa lớp này');
        }

         $validated = $request->validate([
            'class_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('school_classes', 'class_name')->where(function ($query) use ($profile) {
                    return $query->where('homeroom_teacher_id', $profile->id);
                })->ignore($id)
            ],
            'school_year' => 'nullable|integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'class_name.unique' => 'Bạn đã có lớp với tên này rồi. Vui lòng chọn tên khác.'
        ]);

        $class->update($validated);

        return redirect()->route('classes.index')->with('success', 'Cập nhật lớp học thành công!');
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy($id)
    {
        $profile = Auth::guard("customer")->user();
        if($profile->type == 1 || $profile->type == 3){
        $class = SchoolClass::where('id', $id)
            ->where('homeroom_teacher_id', $profile->id)
            ->firstOrFail();
        
        // Kiểm tra xem còn học sinh trong lớp không
        if ($class->students_count > 0) {
            return redirect()->back()->with('error', 'Không thể xóa lớp còn học sinh!');
        }

        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Xóa lớp học thành công!');
        }else{
            return redirect()->back()->with('error','Bạn không có quyền truy cập');
        }
    }

    /**
     * Get all active classes (API for dropdown) - chỉ lớp của giáo viên đã tạo
     */
    public function getActive()
    {
        $profile = Auth::guard("customer")->user();
        
        if($profile && ($profile->type == 1 || $profile->type == 3)){
            // Chỉ trả về lớp mà giáo viên đã tạo
            $classes = SchoolClass::where('is_active', true)
                ->where('homeroom_teacher_id', $profile->id)
                ->orderBy('class_name')
                ->get(['id', 'class_name']);
        } else {
            $classes = [];
        }
        
        return response()->json($classes);
    }
}
