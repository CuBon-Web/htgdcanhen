@extends('layouts.main.master')
@section('title')
Thành tích học viên
@endsection
@section('description')
Thành tích học viên
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
                       <h3 class="title">Thành Tích Học Viên</h3>
                   </div>
                   <nav class="edu-breadcrumb-nav">
                       <ol class="edu-breadcrumb d-flex justify-content-start liststyle">
                           <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                           <li class="separator"><i class="ri-arrow-drop-right-line"></i></li>
                           <li class="breadcrumb-item active" aria-current="page">Thành Tích Học Viên</li>
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
<div class="edu-instructor-area edu-section-gap ">
    <div class="container eduvibe-animated-shape">
       <div class="row g-5  popup-gallery">
          @foreach ($reviewcus as $item)
              @if ($item->content != null )
                <div class="col-lg-3 col-6">
                   <div class="edu-gallery-grid-item grid-metro-item cat--1 cat--3">
                      <div class="edu-gallery-grid">
                         <div class="inner">
                            <div class="thumbnail">
                               <img class="w-100" src="{{$item->avatar}}" alt="Portfolio Images">
                               <a href="{{$item->content}}" class="video-play-btn with-animation position-to-top video-popup-activation btn-secondary-color size-80">
                                  <span class="play-icon"></span>
                               </a>
                            </div>
                         </div>
                      </div>
                      <div class="edu-feedback-title">
                         <h5 class="title" style="color: black"><a href="">{{$item->name}}</a></h5>
                         <span class="desc">{{$item->position}}</span>
                   </div>
                   </div>
                </div>
             @else 
             <div class="col-lg-3 col-6">
                <a href="{{$item->avatar}}" class="edu-gallery-grid-item grid-metro-item cat--1 cat--3">
                   <div class="edu-gallery-grid">
                      <div class="inner">
                         <div class="thumbnail">
                            <img class="w-100" src="{{$item->avatar}}" alt="Portfolio Images">
                         </div>
                      </div>
                   </div>
                   <div class="edu-feedback-title">
                      <h5 class="title" style="color: black"><a href="">{{$item->name}}</a></h5>
                      <span class="desc">{{$item->position}}</span>
                </div>
                </a>
             </div>
              @endif
          @endforeach
          <div class="col-12 text-center">
            {{$reviewcus->links()}}
          </div>
         
       </div>
    </div>
 </div>
@endsection