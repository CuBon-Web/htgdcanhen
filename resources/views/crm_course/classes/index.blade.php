@extends('crm_course.main.master')
@section('title', 'Quản lý lớp học')
@section('css_crm_course')

@endsection

@section('content_crm_course')
<main class="main-content-wrap">
    <div class="content">
        <!-- Start Features Area -->
        <div class="page-title-area">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-sm-6">
                        <div class="page-title">
                            <h3>Danh sách lớp học</h3>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6">
                        <ul class="page-title-list">
                            <li>Trang chủ</li>
                            <li>Danh sách lớp học</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="contact-list-area">
            <div class="container-fluid">
                <div class="form-file-upload d-lg-flex justify-content-between align-items-center">
                    @if ($profile->type == 1 || $profile->type == 3)

                    <ul class="create-upload d-lg-flex">
                        <li class="mb-3 mt-3 mt-lg-0 mr-lg-3">
                            <a href="{{route('classes.create')}}" class="upload-btn">
                                Thêm lớp mới
                                <img src="/frontend/crm-course/images/add-circle.svg" alt="add-circle">
                            </a>
                        </li>
                    </ul>

                    @endif
                </div>
                <div class="table-responsive" data-simplebar>
                    @if ($classes->count() > 0)
                    <table class="table align-middle mb-0">
                        <tbody>
                            <tr>
                                <td>
                                <b>Tên lớp học</b>
                                </td>
                                @if ($profile->type == 3)
                                <td>
                                    <b>Giáo viên chủ nhiệm</b>
                                </td>
                                @endif
                                <td>
                                    <b>Số học sinh</b>
                                </td>
                                <td>
                                    <b>Mã lớp</b>
                                </td>
                                <td>
                                    <b>Năm học</b>
                                </td>
                                <td>
                                    <b>Hành động</b>
                                </td>
                            </tr>
                            @foreach($classes as $item)
                            <tr>
                                <td>
                                    {{$item->class_name}}
                                </td>
                                @if ($profile->type == 3)
                                <td>
                                    {{$item->homeroomTeacher->name}}
                                    @if($item->homeroomTeacher->type == 1)
                                        <span class="badge bg-primary">Giáo viên</span>
                                    @elseif($item->homeroomTeacher->type == 3)
                                        <span class="badge bg-secondary">Cánh Én</span>
                                    @endif
                                </td>
                                @endif
                                
                                <td>
                                    {{$item->students_count}}
                                </td>
                                <td>
                                    {{$item->class_code}}
                                </td>
                                <td>
                                    {{$item->school_year}}
                                </td>
                                <td>
                                    <ul class="d-flex justify-content-betweens">
                                        <li>
                                            <a href="{{ route('classes.show', $item->id) }}">
                                                <i class="bx bx-show-alt"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('classes.edit', $item->id) }}">
                                                <i class="bx bxs-edit-alt"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" onclick="event.preventDefault(); if (confirm('Bạn có chắc muốn xóa lớp này?')) { document.getElementById('delete-form-{{ $item->id }}').submit(); }">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->id }}" action="{{ route('classes.destroy', $item->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else 
                    <div class="alert alert-warning">
                        <p>Không có lớp học nào</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

@endsection

