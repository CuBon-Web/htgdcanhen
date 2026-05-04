@extends('layouts.main.master')
@section('title')
Khóa học của DAYTOEICHANO
@endsection
@section('description')
Khóa học của DAYTOEICHANO
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')
@endsection
@section('js')
@endsection
@section('content')
<div class="edu-breadcrumb-area breadcrumb-style-1 ptb--30 ptb_md--20 ptb_sm--20 bg-image">
   <div class="container eduvibe-animated-shape">
       <div class="row">
           <div class="col-lg-12">
               <div class="breadcrumb-inner text-start">
                   <div class="page-title">
                       <h3 class="title">Tất Cả Khóa Học</h3>
                   </div>
                   <nav class="edu-breadcrumb-nav">
                       <ol class="edu-breadcrumb d-flex justify-content-start liststyle">
                           <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                           <li class="separator"><i class="ri-arrow-drop-right-line"></i></li>
                           <li class="breadcrumb-item active" aria-current="page">Tất Cả Khóa Học</li>
                       </ol>
                   </nav>
               </div>
           </div>
       </div>

       <div class="shape-dot-wrapper shape-wrapper d-xl-block d-none">
           <div class="shape-dot-wrapper shape-wrapper d-xl-block d-none">
               <div class="shape-image shape-image-1">
                   <img src="{{url('frontend/images/shape-11-07.png')}}" alt="Shape Thumb">
               </div>
               <div class="shape-image shape-image-2">
                   <img src="{{url('frontend/images/shape-01-02.png')}}" alt="Shape Thumb">
               </div>
               <div class="shape-image shape-image-3">
                   <img src="{{url('frontend/images/shape-03.png')}}" alt="Shape Thumb">
               </div>
               <div class="shape-image shape-image-4">
                   <img src="{{url('frontend/images/shape-13-12.png')}}" alt="Shape Thumb">
               </div>
               <div class="shape-image shape-image-5">
                   <img src="{{url('frontend/images/shape-36.png')}}" alt="Shape Thumb">
               </div>
               <div class="shape-image shape-image-6">
                   <img src="{{url('frontend/images/shape-05-07.png')}}" alt="Shape Thumb">
               </div>
           </div>
       </div>
   </div>
</div>
<div class="edu-course-area edu-section-gap bg-color-white">
   <div class="container">
       <div class="row g-5 mt--10">
         @foreach ($list as $key => $item)
             <!-- Start Single Card  -->
            <div class="col-12 col-sm-6 col-lg-4 sal-animate" data-sal-delay="150" data-sal="slide-up" data-sal-duration="800">
               <div class="edu-card card-type-3 radius-small">
                  <div class="inner">
                     <div class="thumbnail">
                        <a href="{{route('serviceDetail',['slug'=>$item->slug])}}">
                        <img class="w-100" src="{{$item->image}}" alt="Course Meta">
                        </a>
                        <div class="top-position status-group left-bottom">
                           <span class="eduvibe-status status-03">Khóa Học</span>
                        </div>
                     </div>
                     <div class="content">
                        <div class="card-top">
                           <div class="author-meta">
                              <div class="author-thumb">
                                 <a href="#">
                                 <img src="{{url('frontend/images/avthoang.png')}}" alt="Author Images">
                                 <span class="author-title">TOEIC HÀ NỘI</span>
                                 </a>
                              </div>
                           </div>
                           <div class="rating">
                              <i class="icon-Star" style="font-size: 10px;"></i>
                              <i class="icon-Star" style="font-size: 10px;"></i>
                              <i class="icon-Star" style="font-size: 10px;"></i>
                              <i class="icon-Star" style="font-size: 10px;"></i>
                              <i class="icon-Star" style="font-size: 10px;"></i>
                           </div>
                        </div>
                        <h6 class="title"><a href="{{route('serviceDetail',['slug'=>$item->slug])}}">{{$item->name}}</a>
                        </h6>
                     </div>
                  </div>
               </div>
            </div>
        <!-- End Single Card  -->
         @endforeach
       </div>
       <div class="row">
           <div class="col-lg-12 mt--60">
               <nav>
                   {{$list->links()}}
               </nav>
           </div>
       </div>
   </div>
</div>
@endsection