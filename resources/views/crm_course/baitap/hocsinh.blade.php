@extends('crm_course.main.master')
@section('title')
Danh sách đề thi
@endsection
@section('description')
Danh sách đề thi
@endsection
@section('image')
@endsection
@section('css_crm_course')
@endsection
@section('js_crm_course')
@endsection
@section('content_crm_course')
<main class="main-content-wrap">
    <!-- Start Features Area -->
    <div class="page-title-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-sm-6">
                    <div class="page-title">
                        <h3>Danh sách bài tập</h3>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Trang chủ</li>
                        <li>Danh sách bài tập</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="contact-list-area">
        <div class="container-fluid">
            {{-- Danh sách bài tập có thể làm --}}
            <div class="mb-4">
                <h5 class="fw-semibold mb-3">Bài tập có thể làm</h5>
                <div class="table-responsive" data-simplebar>
                    <table class="table align-middle mb-0">
                        <tbody>
                            <tr>
                                <td><b>Tên bài tập</b></td>
                                <td><b>Người tạo</b></td>
                                <td><b>Quyền truy cập</b></td>
                                <td><b>Thời gian</b></td>
                            </tr>
                            @forelse($allExercises as $exercise)
                            @php
                                $ownerLabel = '---';
                                if ($exercise->customer) {
                                    $ownerLabel = $exercise->customer->type == 3 ? 'Cánh Én' : $exercise->customer->name;
                                }
                                if($exercise->access_type == 'all'){
                                    $accessLabel = 'Tất cả mọi người';
                                }elseif($exercise->access_type == 'class'){
                                    $accessLabel = 'Theo lớp';
                                }else{
                                    $accessLabel = 'Theo thời gian';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <label class="form-check-label ms-2">
                                            <img width="30" src="/frontend/crm-course/images/i-exam-qualification-svgrepo-com.svg" alt="exercise">
                                        </label>
                                        <div class="info ms-2">
                                            <h4 class="mb-1">
                                                <a href="{{ route('detailDeThi',['id'=>$exercise->id]) }}">
                                                    {{ $exercise->title }}
                                                </a>
                                            </h4>
                                            @if($exercise->access_type == 'time_limited' && $exercise->end_time)
                                                <small class="text-muted">Hết hạn: {{ \Carbon\Carbon::parse($exercise->end_time)->format('d/m/Y H:i') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td> 
                                    @if($exercise->customer)
                                        @if ($exercise->customer->type == 1)
                                            <span class="badge bg-secondary ms-1 text-white">  {{ $exercise->customer->name }}</span>
                                        @elseif($exercise->customer->type == 3)
                                            <span class="badge bg-primary ms-1 text-white"> <i class="fas fa-crown"></i> Cánh Én</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary ms-1 text-white">  ---</span>
                                    @endif
                                
                                </td>
                                <td>
                                    @if($exercise->access_type == 'time_limited')

                                         @if ($exercise->start_time && $exercise->end_time)
                                            @php
                                                $timeResult = calculateExamTimeStatus($exercise->start_time, $exercise->end_time);
                                                $timeStatus = $timeResult['status'];
                                                $timeText = $timeResult['text'];
                                            @endphp
                                            @if ($timeStatus == 'not_started')
                                                <span class="badge bg-warning text-white">
                                                    <i class="fas fa-clock"></i> Bắt đầu sau {{ $timeText }}
                                                </span>
                                            @elseif ($timeStatus == 'active')
                                                <span class="badge bg-success text-white">
                                                    <i class="fas fa-clock"></i> Còn {{ $timeText }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white">
                                                    <i class="fas fa-times-circle"></i> Đã kết thúc
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-white">
                                                <i class="fas fa-clock"></i> Giới hạn thời gian
                                            </span>
                                        @endif
                                    @elseif($exercise->access_type == 'class')
                                        <span class="badge bg-info ms-1 text-white"> Theo lớp</span>
                                    @else
                                        <span class="badge bg-success ms-1 text-white"> Tất cả mọi người</span>
                                    @endif
                                </td>
                                <td>
                                    @if($exercise->time > 0)
                                        <span class="badge bg-warning ms-1 text-white"> {{ $exercise->time }} phút</span>
                                    @else
                                        <span class="badge bg-success ms-1 text-white"> Không giới hạn</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="text-muted">Chưa có bài tập nào khả dụng.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Kết quả bài tập đã làm --}}
            <div>
                <h5 class="fw-semibold mb-3">Kết quả bài tập đã làm</h5>
                <div class="table-responsive" data-simplebar>
                    <table class="table align-middle mb-0">
                        <tbody>
                            <tr>
                                <td><b>Tên bài tập</b></td>
                                <td><b>Điểm</b></td>
                                <td><b>Trạng thái</b></td>
                            </tr>
                            @forelse($data as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <label class="form-check-label ms-2">
                                            <img width="30" src="/frontend/crm-course/images/i-exam-qualification-svgrepo-com.svg" alt="user-2">
                                        </label>
                                        <div class="info ml-3">
                                            <h4><a href="{{route('resultDethi',['id'=>$item->id])}}">{{$item->dethi->title}}</a></h4>
                                       </div>
                                    </div>
                                </td>
                                <td>
                                    {{rtrim(rtrim(number_format($item->total_score, 2), '0'), '.')}}/{{rtrim(rtrim(number_format($item->max_score, 2), '0'), '.')}}
                                </td>
                                <td>
                                    @if($item->status == 2)
                                    <button class="btn btn-success  mb-1 text-white">Đã chấm</button>
                                    @else
                                    <button class="btn btn-danger mb-1 text-white">Chưa chấm</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="text-muted">Chưa có kết quả bài tập.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-3">
                        {{ $data->links() }}
                    </div>
                </div>
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