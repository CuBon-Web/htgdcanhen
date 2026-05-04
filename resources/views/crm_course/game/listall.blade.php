@extends('crm_course.main.master')
@section('title')
Danh sách game
@endsection
@section('description')
Danh sách game
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
                        <h3>Danh sách game</h3>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                    <ul class="page-title-list">
                        <li>Trang chủ</li>
                        <li>Danh sách game</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="contact-list-area">
        <div class="container-fluid">
            <div class="card-box-style">
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-md-6">
                        <div class="single-blog">
                            <div class="blog-img">
                                <a href="{{route('gamelistAITrieuPhuToanHoc')}}">
                                    <img src="/frontend/crm-course/images/ai-la-trieu-phu.png" alt="blog-1">
                                </a>
                            </div>

                            <div class="blog-content">
                                <h3>
                                    <a href="{{route('gamelistAITrieuPhuToanHoc')}}">Ai là triệu phú toán học</a>
                                </h3>

                                <ul class="d-flex justify-content-between">
                                    <li class="admin">
                                        <a href="{{route('gamelistAITrieuPhuToanHoc')}}"><img width="35" src="{{$setting->logo}}" alt="admin-1"></a>
                                        <span>By</span>
                                        <a href="{{route('gamelistAITrieuPhuToanHoc')}}">Edu Alpha</a>
                                    </li>
                                    <li>
                                        <a href="{{route('gamelistAITrieuPhuToanHoc')}}" class="read-more">
                                           Chơi Ngay
                                            <i class="ri-arrow-right-line"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Start Footer Area -->
    @include('crm_course.main.footer')
    <!-- End Footer Area -->

 </main>

@endsection