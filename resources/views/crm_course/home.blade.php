@extends('crm_course.main.master')
@section('title')
{{$setting->company}}
@endsection
@section('description')
{{$setting->webname}}
@endsection
@section('image')
@endsection
@section('css_crm_course')
<style>
    /* Tối ưu badge cho mobile */
    .audience-content .badge {
        font-size: 12px;
        padding: 6px 10px;
        white-space: nowrap;
        display: inline-block;
        margin-bottom: 4px;
    }
    
    .audience-content h4 {
        margin-bottom: 8px;
        line-height: 1.4;
    }
    
    /* Mobile: hiển thị badge theo cột, mỗi badge một dòng */
    @media (max-width: 767.98px) {
        .audience-content .d-flex {
            flex-direction: column;
            gap: 6px !important;
        }
        
        .audience-content .badge {
            font-size: 11px;
            padding: 5px 8px;
            width: 100%;
            text-align: center;
            display: block;
        }
        
        .audience-content h4 {
            margin-bottom: 4px;
            width: 100%;
        }
        
        .audience-content h4:last-child {
            margin-bottom: 0;
        }
    }
    
    /* Tablet: hiển thị 2 cột */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .audience-content .d-flex {
            flex-wrap: wrap;
            gap: 8px !important;
        }
        
        .audience-content .badge {
            font-size: 11px;
            padding: 5px 9px;
            flex: 0 0 calc(50% - 4px);
        }
    }
    
    /* Desktop: hiển thị theo block (như hiện tại) */
    @media (min-width: 992px) {
        .audience-content .d-md-block {
            display: block !important;
        }
        
        .audience-content .d-md-block .badge {
            display: block;
            width: 100%;
            margin-bottom: 6px;
        }
    }
</style>
@endsection
@section('js_crm_course')
@endsection
@section('content_crm_course')
<main class="main-content-wrap">
    <!-- Start Features Area -->
    <div class="content">
      <div class="features-area">
         <div class="container-fluid">
            <div class="row justify-content-center">
                @if ($profile->type == 1) <!-- Giáo viên -->
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('khoiTaoDeThi')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Đề thi</h5>
                              <div class="d-flex  gap-2">
                              <h4> <span class="badge text-bg-success">{{ $dethi_tu_do ?? 0 }} Tự do </span></h4>
                                <h4> <span class="badge text-bg-primary">{{ $dethi_giao_lop ?? 0 }} Giao cho lớp </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $dethi_thoi_gian ?? 0 }} Thời gian giới hạn </span></h4>
                                <h4> <span class="badge text-bg-danger">{{ $dethi_chua_xuat_ban ?? 0 }} Chưa xuất bản </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('danhSachBaiTap')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Bài giảng</h5>
                                <div class="d-flex gap-2">
                              <h4> <span class="badge text-bg-success">{{ $baitap_tu_do ?? 0 }} Tự do </span></h4>
                                <h4> <span class="badge text-bg-primary">{{ $baitap_giao_lop ?? 0 }} Giao cho lớp </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $baitap_thoi_gian ?? 0 }} Thời gian giới hạn </span></h4>
                                <h4> <span class="badge text-bg-danger">{{ $baitap_chua_xuat_ban ?? 0 }} Chưa xuất bản </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('gamelistAll')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Trò chơi</h5>
                              <div class="d-flex gap-2">
                              <h4> <span class="badge text-bg-success">{{ $game_tu_do ?? 0 }} Tự do </span></h4>
                                <h4> <span class="badge text-bg-primary">{{ $game_giao_lop ?? 0 }} Giao cho lớp </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $game_thoi_gian ?? 0 }} Thời gian giới hạn </span></h4>
                                <h4> <span class="badge text-bg-danger">{{ $game_chua_xuat_ban ?? 0 }} Chưa xuất bản </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div>
                {{-- <div class="col-lg-4 col-md-6">
                    <a href="">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Học sinh của bạn</h5>
                              <h4>{{ $hoc_sinh_count ?? 0 }} <span>Số lượng</span></h4>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div> --}}
                @elseif ($profile->type == 3) <!-- Admin Edualpha -->
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('khoiTaoDeThi')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Đề thi</h5>
                              <div class="d-flex  gap-2">
                              <h4> <span class="badge text-bg-success">{{ $dethi_tu_do ?? 0 }} Tự do </span></h4>
                                <h4> <span class="badge text-bg-primary">{{ $dethi_giao_lop ?? 0 }} Giao cho lớp </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $dethi_thoi_gian ?? 0 }} Thời gian giới hạn </span></h4>
                                <h4> <span class="badge text-bg-danger">{{ $dethi_chua_xuat_ban ?? 0 }} Chưa xuất bản </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('danhSachBaiTap')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Bài giảng</h5>
                                <div class="d-flex gap-2">
                              <h4> <span class="badge text-bg-success">{{ $baitap_tu_do ?? 0 }} Tự do </span></h4>
                                <h4> <span class="badge text-bg-primary">{{ $baitap_giao_lop ?? 0 }} Giao cho lớp </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $baitap_thoi_gian ?? 0 }} Thời gian giới hạn </span></h4>
                                <h4> <span class="badge text-bg-danger">{{ $baitap_chua_xuat_ban ?? 0 }} Chưa xuất bản </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('gamelistAll')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Trò chơi</h5>
                              <div class="d-flex gap-2">
                              <h4> <span class="badge text-bg-success">{{ $game_tu_do ?? 0 }} Tự do </span></h4>
                                <h4> <span class="badge text-bg-primary">{{ $game_giao_lop ?? 0 }} Giao cho lớp </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $game_thoi_gian ?? 0 }} Thời gian giới hạn </span></h4>
                                <h4> <span class="badge text-bg-danger">{{ $game_chua_xuat_ban ?? 0 }} Chưa xuất bản </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div>
                {{-- <div class="col-lg-4 col-md-6">
                    <a href="">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Tất cả học sinh</h5>
                              <h4>{{ $hoc_sinh_count ?? 0 }} <span>Số lượng</span></h4>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div> --}}
                {{-- <div class="col-lg-4 col-md-6">
                    <a href="">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Tất cả giáo viên</h5>
                              <h4>{{ $giao_vien_count ?? 0 }} <span>Số lượng</span></h4>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                </div> --}}
                @else <!-- Học sinh -->
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('allDeThi')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Đề thi của bạn</h5>
                              <div class="d-flex gap-2">
                              <h4> <span class="badge text-bg-info">{{ $dethi_da_lam ?? 0 }} Đã làm </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $dethi_dang_lam ?? 0 }} Đang làm </span></h4>
                                <h4> <span class="badge text-bg-success">{{ $dethi_hoan_thanh ?? 0 }} Hoàn thành </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                 </div>
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('danhSachBaiTap')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Bài tập của bạn</h5>
                              <div class="d-flex gap-2">
                              <h4> <span class="badge text-bg-info">{{ $baitap_da_lam ?? 0 }} Đã làm </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $baitap_dang_lam ?? 0 }} Đang làm </span></h4>
                                <h4> <span class="badge text-bg-success">{{ $baitap_hoan_thanh ?? 0 }} Hoàn thành </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                 </div>
                <div class="col-lg-4 col-md-6">
                    <a href="{{route('gamelistAll')}}">
                       <div class="single-audience d-flex justify-content-between align-items-center">
                          <div class="audience-content">
                              <h5>Trò chơi của bạn</h5>
                              <div class="d-flex gap-2">
                              <h4> <span class="badge text-bg-info">{{ $game_da_lam ?? 0 }} Đã làm </span></h4>
                                <h4> <span class="badge text-bg-warning">{{ $game_dang_lam ?? 0 }} Đang làm </span></h4>
                                <h4> <span class="badge text-bg-success">{{ $game_hoan_thanh ?? 0 }} Hoàn thành </span></h4>
                              </div>
                          </div>
                          <div class="icon">
                              <img src="frontend/crm-course/images/course_white.svg" alt="white-profile-2user">
                          </div>
                      </div>
                    </a>
                 </div>
                @endif
               
            </div>
         </div>
      </div>
      
    </div>
    
    <!-- End Features Area -->
    <!-- Start Footer Area -->
    @include('crm_course.main.footer')
    <!-- End Footer Area -->
 </main>
@endsection