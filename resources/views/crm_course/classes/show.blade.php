@extends('crm_course.main.master')
@section('title', 'Chi tiết lớp học')

@section('css_crm_course')
<style>
    .contact-info-wrap .d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .contact-info-wrap .d-flex h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .contact-info-wrap .btn-warning {
        background: #fbbf24;
        border: none;
        color: #1f2937;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }

    .contact-info-wrap .btn-warning:hover {
        background: #f59e0b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
    }

    .contact-info-wrap .btn-warning i {
        margin-right: 5px;
    }
</style>
@endsection

@section('content_crm_course')
<main class="main-content-wrap">
    <!-- Start Page Title Area -->
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        <h3>Chi tiết lớp học: {{ $class->class_name }}</h3>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Lớp học</li>
                        <li>Chi tiết lớp học</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title Area -->

    <!-- Start Contact Us Area -->
    <div class="contact-us-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4">
                    <div class="contact-info-wrap">
                        <div class="single-contact-info">
                            <div class="d-flex">
                                <h3>Thông tin lớp học</h3>
                                <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                            </div>
                           

                            <ul class="address">
                                <li class="location">
                                    <span>Tên lớp</span>
                                    : {{ $class->class_name }}
                                </li>
                                <li>
                                    <span>Mã lớp</span>
                                    : {{ $class->class_code }}
                                </li>
                                <li>
                                    <span>GVCN</span>
                                    : {{ $class->homeroom_teacher }}
                                </li>
                                <li>
                                    <span>Năm học</span>
                                    : {{ $class->school_year }}
                                </li>
                                <li>
                                    <span>Trạng thái</span>
                                    : {{ $class->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                </li>
                                <li>
                                    <span>Số học sinh</span>
                                    : {{ $class->students->count() }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="contact-form">
                        <h3>Danh sách học sinh</h3>
                        <div class="table-responsive" data-simplebar>
                            @if ($class->students->count() > 0)
                            <table class="table align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <td>
                                        <b>Tên học sinh</b>
                                        </td>
                                        <td>
                                            <b>Email</b>
                                        </td>
                                        <td>
                                            <b>Số điện thoại</b>
                                        </td>
                                        <td>
                                            <b>Ngày đăng ký</b>
                                        </td>
                                    </tr>
                                    @foreach($class->students as $item)
                                    <tr>
                                        <td>
                                            {{$item->name}}
                                        </td>
                                        <td>
                                            {{$item->email}}
                                        </td>
                                        <td>
                                            {{$item->phone}}
                                        </td>
                                        <td>
                                            {{$item->created_at->format('d/m/Y')}}
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
        </div>
    </div>
</main>
@endsection

