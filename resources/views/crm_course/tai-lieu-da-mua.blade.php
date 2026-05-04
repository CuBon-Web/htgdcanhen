@extends('crm_course.main.master')
@section('title')
Danh sách tài liệu đã mua
@endsection
@section('description')
Danh sách tài liệu đã mua
@endsection
@section('image')
@endsection
@section('css_crm_course')
@endsection
@section('js_crm_course')
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
                            <h3>Danh sách tài liệu đã mua</h3>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6">
                        <ul class="page-title-list">
                            <li>Trang chủ</li>
                            <li>Danh sách tài liệu đã mua</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="contact-list-area">
            <div class="container-fluid">
                <div class="form-file-upload d-lg-flex justify-content-between align-items-center">
                    <form class="search-form">
                        <input type="text" class="form-control" placeholder="Search Files">
                        <img src="/frontend/crm-course/images/search-normal.svg" alt="search-normal">
                    </form>
                </div>
                @if ($document->count() > 0)
                    <table class="table align-middle mb-0">
                        <tbody>
                            <tr>
                                <td>
                                <b>Tên tài liệu</b>
                                </td>
                                <td>
                                    <b>Giá</b>
                                </td>
                                <td>
                                    <b>Trạng thái</b>
                                </td>
                            </tr>
                            @foreach($document as $item)
                            {{-- {{dd($item)}} --}}
                            <tr>
                                <td>
                                    <div class=" d-flex align-items-center">
                                        <label class="form-check-label ms-2">
                                            <img width="30" src="/frontend/crm-course/images/open-file.svg" alt="user-2">
                                        </label>
                                        <div class="info ml-3">
                                            <h4><a href="">{{ $item->document->name ?? 'Đề thi đã bị xóa'}}</a></h4>
                                    </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->document ? $item->document->price === 0 : 0)
                                    <span >Miễn phí</span>
                                    @else
                                    <span>{{number_format($item->document ? $item->document->price : 0,0,',','.')}} VNĐ</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 0)
                                    <button class="btn btn-warning mb-1 text-white" >Chờ Thanh Toán</button>
                                    @elseif($item->status == 1)
                                    <a  href="{{$item->document->docs == null ? $item->document->pdf : $item->document->docs}}" class="btn btn-success  mb-1 text-white">  <i class="fas fa-download"></i> Tải xuống ngay</a>
                                    @else
                                    <button class="btn btn-success mb-1 text-white">Đã kết thúc</button>
                                    @endif
                                </td>
                                <td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else 
                    <div class="alert alert-warning">
                        <p>Không có tài liệu nào</p>
                    </div>
                    @endif
            </div>
        </div>
    </div>
    <!-- End Features Area -->
    <!-- Start Footer Area -->
    @include('crm_course.main.footer')
    <!-- End Footer Area -->
    <div class="modal fade" id="deleteDethiModal" tabindex="-1" aria-labelledby="deleteDethiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteDethiModalLabel">Xác nhận xoá đề thi</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Bạn có chắc chắn muốn xoá đề thi này? Tất cả dữ liệu học sinh làm bài và các file liên quan sẽ bị xoá vĩnh viễn!</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
              <button type="button" class="btn btn-danger" onclick="confirmDeleteDethi()">Xoá vĩnh viễn</button>
            </div>
          </div>
        </div>
      </div>
 </main>

@endsection