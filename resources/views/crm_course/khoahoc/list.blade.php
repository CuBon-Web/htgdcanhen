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
    
         /* ===== EMPTY COURSE STATE ===== */
         .not-course {
             background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
             border-radius: 20px;
             padding: 25px 29px;
             text-align: center;
             box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
             position: relative;
             overflow: hidden;
             border: 1px solid rgba(255, 255, 255, 0.1);
         }

         .not-course::before {
             content: '';
             position: absolute;
             top: -50%;
             left: -50%;
             width: 200%;
             height: 200%;
             background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
             animation: float 6s ease-in-out infinite;
         }

         @keyframes float {
             0%, 100% { transform: translateY(0px) rotate(0deg); }
             50% { transform: translateY(-20px) rotate(180deg); }
         }

         .not-course .content {
             position: relative;
             z-index: 2;
             min-height: auto;
         }

         .not-course h3 {
             color: #ffffff;
             font-size: 18px;
             font-weight: 600;
             margin-bottom: 25px;
             text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
             letter-spacing: 0.5px;
         }

         .not-course .btn-primary {
            background: linear-gradient(188deg, #ff6b6b, #ee5a24);
            border: none;
            border-radius: 50px;
            padding: 5px 35px;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
        }

         .not-course .btn-primary::before {
             content: '';
             position: absolute;
             top: 0;
             left: -100%;
             width: 100%;
             height: 100%;
             background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
             transition: left 0.5s;
         }

         .not-course .btn-primary:hover {
             transform: translateY(-3px);
             box-shadow: 0 15px 35px rgba(255, 107, 107, 0.4);
             color: #ffffff;
             text-decoration: none;
         }

         .not-course .btn-primary:hover::before {
             left: 100%;
         }

         .not-course .btn-primary:active {
             transform: translateY(-1px);
         }

         /* Responsive design */
         @media (max-width: 768px) {
  

             .not-course h3 {
                 font-size: 22px;
                 margin-bottom: 20px;
             }

             .not-course .btn-primary {
                 padding: 12px 25px;
                 font-size: 14px;
             }
         }

         @media (max-width: 480px) {
          
             .not-course h3 {
                 font-size: 18px;
                 margin-bottom: 15px;
             }

             .not-course .btn-primary {
                 padding: 10px 20px;
                 font-size: 13px;
             }
         }
</style>
@endsection
@section('js_crm_course')
@endsection
@section('content_crm_course')
<main class="main-content-wrap">
    <div class="content">
    <div class="blog-post-area ptb-100">
        <div class="container-fluid">
            @if($profile->type == 1 || $profile->type == 3)
            <div class="card-box-style">
                <div class="others-title d-lg-flex d-block justify-content-between align-items-center">
                    <h3>Khóa học của bạn</h3>
                    <ul class="create-upload d-flex">
                        <li>
                            <a href="{{route('postCouse')}}" class="upload-btn">
                                Tạo khóa học
                                <img src="/frontend/crm-course/images/add-circle.svg" alt="add-circle">
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="row justify-content-center">
                    @if ($product->count() == 0)
                    <div class="col-12">
                        <div class="not-course">
                            <div class="content">
                                <h3>Bạn chưa tạo khóa học nào</h3>
                                <a href="{{route('postCouse')}}" class="btn btn-primary">Tạo khóa học</a> 
                            </div>
                        </div>
                    </div>
                @else
                    @foreach ($product as $item)
                    <div class="col-xl-3 col-md-6">
                        <div class="single-blog">
                            <div class="blog-img">
                                <a href="{{ route('editCouse', ['id' => $item->id]) }}">
                                    <img src="{{$item->images}}" alt="blog-1">
                                </a>
                                <span class="{{ $item->status == 0 ? 'pending date' : 'success date' }} ">{{ $item->status == 0 ? 'Đang xét duyệt' : 'Đang hoạt động' }}</span>
                            </div>

                            <div class="blog-content">
                                <h3>
                                    <a href="{{ route('editCouse', ['id' => $item->id]) }}">{{ $item->name }}</a>
                                </h3>
                                <ul class="d-flex justify-content-between">
                                    <li class="admin">
                                        <a href="{{ route('editCouse', ['id' => $item->id]) }}"><i class="bx bx-book-reader"></i> {{ $item->ingredient }} Bài học</a>
                                    </li>
                                    <li class="admin">
                                        <a href="{{ route('editCouse', ['id' => $item->id]) }}"><i class="bx bx-layer-plus"></i>{{ $item->thickness }} Buổi học</a>
                                    </li>
                                    <li>
                                        @if ($item->price == 0)
                                            <p>Miễn phí</p>
                                        @else
                                            <p>{{ number_format($item->price) }}đ
                                                <del>{{ number_format($item->discount) }}đ</del>
                                            </p>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            @endif
            <div class="card-box-style">
                <div class="others-title d-lg-flex d-block justify-content-between align-items-center">
                    <h3 class="mb-3">Khóa học bạn đã mua</h3>
                </div>
                <div class="row justify-content-center">
                    @if ($courser->count() == 0)
                        <div class="col-12">
                            <div class="not-course">
                                <div class="content">
                                    <h3>Bạn chưa mua khóa học nào</h3>
                                    <a href="{{route('home')}}" class="btn btn-primary">Mua khóa học</a> 
                                </div>
                            </div>
                        </div>
                    @else
                    @foreach ($courser as $item)
                    <div class="col-xl-3 col-md-6">
                        <div class="single-blog">
                            <div class="blog-img">
                                <a  href="{{ route('startStudyCourse',['id' => $item->product->id]) }}">
                                    <img src="{{  $item->product->images }}" alt="blog-1">
                                </a>
                            </div>
                            <div class="blog-content">
                                <h3>
                                    <a  href="{{ route('startStudyCourse',['id' => $item->product->id]) }}">{{ $item->product->name }}</a>
                                </h3>
                                <ul class="d-flex justify-content-between">
                                    <li class="admin">
                                        <a  href="{{ route('startStudyCourse',['id' => $item->product->id]) }}"><i class="bx bx-book-reader"></i> {{ $item->ingredient }} Bài học</a>
                                    </li>
                                    <li class="admin">
                                        <a  href="{{ route('startStudyCourse',['id' => $item->product->id]) }}"><i class="bx bx-layer-plus"></i>{{ $item->thickness }} Buổi học</a>
                                    </li>
                                    <li>
                                        
                                        @if ($item->product->price == 0)
                                            <p>Miễn phí</p>
                                        @else
                                            <p>{{ number_format($item->product->price) }}đ
                                                <del>{{ number_format($item->product->discount) }}đ</del>
                                            </p>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
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