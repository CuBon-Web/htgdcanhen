@extends('layouts.main.master')
@section('title')
Khóa Học Của Tôi
@endsection
@section('description')
Khóa Học Của Tôi
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')
@endsection
@section('content')
<div class="edu-breadcrumb-area breadcrumb-style-1 ptb--30 ptb_md--20 ptb_sm--20 bg-image">
   <div class="container eduvibe-animated-shape">
      <div class="row">
         <div class="col-lg-12">
            <div class="breadcrumb-inner text-start">
               <div class="page-title">
                  <h3 class="title">Khóa Học Của Tôi</h3>
               </div>
               <nav class="edu-breadcrumb-nav">
                  <ol class="edu-breadcrumb d-flex justify-content-start liststyle">
                     <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                     <li class="separator"><i class="ri-arrow-drop-right-line"></i></li>
                     <li class="breadcrumb-item"><a href="{{route('home')}}">Khóa Học Của Tôi</a></li>
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
<div class="edu-instructor-profile-area edu-section-gap bg-color-white">
   <div class="container">
      <div class="row g-5">
         <div class="col-lg-3">
            <div class="instructor-profile-left">
               <div class="inner">
                  <div class="thumbnail">
                     <img src="{{$profile->avatar ? url('uploads/images/'.$profile->avatar) : url('frontend/images/user_icon.png')}}" alt="none-avatar">
                  </div>
                  <div class="content">
                     <h5 class="title">{{$profile->name}}</h5>
                     <span class="subtitle">Thành Viên</span>
                     <div class="contact-with-info">
                        <p><i class="icon-mail-line-2"></i> <a href="#">{{$profile->email}}</a></p>
                        <p><i class="icon-Headphone"></i> <a href="">{{$profile->phone}}</a></p>
                     </div>
                     <div class="contact-btn">
                        <a class="edu-btn" href="{{route('showProfile')}}">Chỉnh sửa thông tin<i class="icon-arrow-right-line-right"></i></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-lg-9">
            <div class="instructor-profile-right">
               <div class="inner">
                  <div class="section-title text-start">
                     <span class="pre-title">Profile</span>
                     <h3 class="title">Hello, {{$profile->name}}</h3>
                  </div>
                  <div class="edu-skill-style mt--25">
                     <div class="section-title text-start mb--30">
                        <h3 class="title_t">Khóa Học Của Tôi</h3>
                     </div>
                     <div class="rbt-progress-style-1 row g-5">
                        {{-- {{dd($courser)}} --}}
                           @foreach ($courser as $item)
                           @php
                                $img = json_decode($item->product->images);
                           @endphp
                           <div class="col-12 col-sm-6 col-lg-6 sal-animate" data-sal-delay="150" data-sal="slide-up" data-sal-duration="800">
                              <div class="edu-card card-type-1 radius-small">
                                 <div class="inner">
                                    <div class="thumbnail">
                                       <a href="{{route('couseDetail',['slug'=>$item->product->slug])}}">
                                       <img class="w-100" src="{{$img[0]}}" alt="Course Meta">
                                       </a>
                                       @if (checkBillCourse($profile->id,$item->product->id) == true)
                                       <div class="top-position status-group left-top">
                                          <span class="eduvibe-status status-01" style="background: #107718 !important;color:white;">Đã Thanh Toán</span>
                                       </div>
                                       @else 
                                       <div class="top-position status-group left-top">
                                            <span class="eduvibe-status status-01 bg-secondary-color">Chưa Thanh Toán</span>
                                        </div>
                                       @endif
                                    </div>
                                    <div class="content">
                                       <div class="card-top">
                                          <div class="author-meta">
                                             <div class="author-thumb">
                                                <a href="{{route('couseDetail',['slug'=>$item->product->slug])}}" tabindex="0">
                                                <img src="{{url('frontend/images/avthoang.png')}}" alt="Toán Edu Alpha">
                                                <span class="author-title">Toán Edu Alpha</span>
                                                </a>
                                             </div>
                                          </div>
                                       </div>
                                       <h4 class="title"><a href="{{route('couseDetail',['slug'=>$item->product->slug])}}">{{$item->product->name}}</a>
                                       </h4>
                                       <div class="edu-rating rating-default">
                                          <div class="rating">
                                             <i class="icon-Star" style="font-size: 12px;"></i>
                                             <i class="icon-Star" style="font-size: 12px;"></i>
                                             <i class="icon-Star" style="font-size: 12px;"></i>
                                             <i class="icon-Star" style="font-size: 12px;"></i>
                                             <i class="icon-Star" style="font-size: 12px;"></i>
                                          </div>
                                       </div>
                                       <div class="card-bottom">
                                          <div class="price-list price-style-03">
                                             @if ($item->product->price > 0 && $item->product->discount > 0)
                                             @if ($item->product->status_variant == 1)
                                             <div class="price current-price">{{get_price_variant($item->product->id)}}₫</div>
                                             <div class="price old-price">{{number_format($item->product->price)}}₫</div>
                                             @else 
                                             <div class="price current-price">{{number_format($item->product->discount)}}₫</div>
                                             <div class="price old-price">{{number_format($item->product->price)}}₫</div>
                                             @endif
                                             @elseif($item->product->price == 0 && $item->product->discount > 0)
                                             <div class="price current-price">{{number_format($item->product->discount)}}₫</div>
                                             @else
                                             <div class="price current-price">Miễn phí</div>
                                             @endif
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           @endforeach
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection