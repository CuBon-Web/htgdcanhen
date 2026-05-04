@extends('crm_course.main.master')
@section('title', 'Quản lý trang HTML')

@section('content_crm_course')
<main class="main-content-wrap">
    <div class="content">
        <div class="page-title-area">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-sm-6">
                        <div class="page-title">
                            <h3>Danh sách trang HTML</h3>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <ul class="page-title-list">
                            <li>Trang chủ</li>
                            <li>Trang HTML</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-list-area">
            <div class="container-fluid">

                <div class="form-file-upload d-lg-flex justify-content-between align-items-center">
                    <ul class="create-upload d-lg-flex">
                        <li class="mb-3 mt-3 mt-lg-0 mr-lg-3">
                            <a href="{{ route('html-pages.create') }}" class="upload-btn">
                                Thêm trang mới
                                <img src="/frontend/crm-course/images/add-circle.svg" alt="add-circle">
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="table-responsive" data-simplebar>
                    @if ($pages->count() > 0)
                        <table class="table align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td><b>Tiêu đề</b></td>
                                    <td><b>Trạng thái</b></td>
                                    <td><b>Link xem</b></td>
                                    <td><b>Hành động</b></td>
                                </tr>
                                @foreach($pages as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td>
                                            @if($item->status)
                                                <span class="badge bg-success">Hiển thị</span>
                                            @else
                                                <span class="badge bg-secondary">Ẩn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('html-pages.public', $item->slug) }}" >
                                                {{ route('html-pages.public', $item->slug) }}
                                            </a>
                                        </td>
                                        <td>
                                            <ul class="d-flex justify-content-betweens">
                                                <li>
                                                    <a href="{{ route('html-pages.edit', $item->id) }}">
                                                        <i class="bx bxs-edit-alt"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="event.preventDefault(); if(confirm('Xóa trang này?')) document.getElementById('delete-{{ $item->id }}').submit();">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                    <form id="delete-{{ $item->id }}" action="{{ route('html-pages.destroy', $item->id) }}" method="POST" style="display:none;">
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

                        <div class="mt-3">
                            {{ $pages->links() }}
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <p>Chưa có trang HTML nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

