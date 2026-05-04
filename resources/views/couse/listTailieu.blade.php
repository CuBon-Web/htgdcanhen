@extends('layouts.main.master')
@section('title')
Danh sách tài liệu
@endsection
@section('description')
Danh sách tài liệu
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')
<style>
    .courses-three__content p {
    margin: 0;
    font-size: 14px;
}
</style>
@endsection
@section('content')
<section class="page-header">
   <div class="page-header__bg" style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/page-header-bg-shape.png);">
   </div>
   <div class="page-header__shape-4">
       <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-4.png" alt="">
   </div>
   <div class="page-header__shape-5">
       <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-5.png" alt="">
   </div>
   <div class="container">
       <div class="page-header__inner">
           <div class="row">
               <div class="col-lg-8">
                   <h1>{{$cate->name}}</h1>
                   <div class="thm-breadcrumb__box">
                       <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="{{route('home')}}">Trang chủ</a></li>
                            <li><span>//</span></li>
                            <li><a href="">Tài Liệu</a></li>
                            <li><span>//</span></li>
                            <li>{{$cate->name}}</li>
                       </ul>
                   </div>
                   <div class="desc mb-3">
                      Hãy bắt đầu cùng chúng tôi rèn luyện ngay bây giờ
                   </div>
               </div>
               <div class="col-lg-4">
                   <div class="page-header__img">
                       <img src="/frontend/images/breamcum.png" alt="">
                       <div class="page-header__shape-1">
                           <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-1.png" alt="">
                       </div>
                       <div class="page-header__shape-2">
                           <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-book-icon.png" alt="">
                       </div>
                       <div class="page-header__shape-3">
                           <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-3.png" alt="">
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
</section>
<section class="course-grid">
   <div class="container">
       <div class="row">
         <div class="col-xl-12 col-lg-12">
            <div class="course-grid__right">
                <div class="course-grid__right-content-box">
                    <div class="row">
                <!-- Courses Three Single Start -->
                @foreach ($list as $item)
                    <div class="col-xl-6 wow fadeInLeft" data-wow-delay="100ms">
                    <div class="courses-three__single">
                        <div class="courses-three__img">
                            @if ($item->type == 'test')
                                <img src="{{$item->image ? $item->image : url('frontend/images/testonline.png')}}" alt="">
                            @else 
                                 <img src="{{$item->image ? $item->image : url('frontend/images/document.jpg')}}" alt="">
                            @endif
                            
                        </div>
                        <div class="courses-three__content">
                            <h3 class="courses-three__title"><a href="">{{$item->name}}</a></h3>
                            <p>{{$item->description}}</p>
                            <div class="courses-three__btn-and-heart-box">
                                <div class="courses-three__btn-box">
                                     @if ($item->type == 'test')
                                        <a href="{{route('lamTailieu',['slug'=>$item->slug])}}"  class="thm-btn-two">
                                            <span>Làm bài</span>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @else 
                                        <a href="{{$item->file}}"  class="thm-btn-two">
                                            <span>Tải xuống</span>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                                <div class="courses-two__client-box">
                                    <div class="courses-two__client-img">
                                        @if ($item->customer)
                                        <img src="{{$item->customer->avatar != null ? url('uploads/images/'.$item->customer->avatar) : url('frontend/images/user_icon.png')}}" alt="">
                                            
                                        @else 
                                            <img src="{{url('frontend/images/user_icon.png')}}" alt="">
                                        @endif
                                        
                                    </div>
                                    <div class="courses-two__client-content">
                                        <h4>{{$item->customer ? $item->customer->name : 'Quản trị viên'}}</h4>
                                        <p> Giáo viên</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- Courses Three Single End -->
            </div>
                </div>
            </div>
        </div>
           <div class="col-xl-4 col-lg-5">
               <div class="course-grid__left">
                   <div class="course-grid__sidebar">
                    
                   </div>
               </div>
           </div>
       </div>
   </div>
</section>
@endsection