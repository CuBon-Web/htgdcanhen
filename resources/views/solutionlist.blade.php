@extends('layouts.main.master')
@section('title')
Lịch khai giảng - Toeic Hà Nội
@endsection
@section('description')
Lịch khai giảng - Toeic Hà Nội
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
                        <h3 class="title">Lịch khai giảng</h3>
                    </div>
                    <nav class="edu-breadcrumb-nav">
                        <ol class="edu-breadcrumb d-flex justify-content-start liststyle">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                            <li class="separator"><i class="ri-arrow-drop-right-line"></i></li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch khai giảng</li>
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
<div class="edu-elements-area edu-section-gap bg-color-white">
    <div class="container">
       <div class="row g-5">
          <!-- Start Event List  -->
          <div class="col-lg-9 sal-animate" data-sal-delay="150" data-sal="slide-up" data-sal-duration="800">
            <h3 class="title" style="font-size: 20px; text-align: center; color: #00b58c; font-weight: 700;"><a href="{{route('detailSolution',['slug'=>$list[0]->slug])}}">{{$list[0]->name}}</a></h3>
            <div class="table-responsive">
                @if (count($list) > 0)
                <a href="{{route('detailSolution',['slug'=>$list[0]->slug])}}" >
                    <img src="{{$list[0]->images}}" alt="{{$list[0]->images}}" style="margin-bottom:20px;">
                 </a>
                @endif
              </div>
              <h3 class="title" style="font-size: 20px">Lịch Khai Giảng Khác: </h3>
            @foreach ($list as $key =>  $item)
            @if ($key > 0)
            @php
                $detaillkg = json_decode($item->detail);
            @endphp
            
            <div class="edu-event event-list radius-small">
                <div class="inner">
                <div class="content">
                    <div class="content-left">
                        <h5 class="title"><a href="{{route('detailSolution',['slug'=>$item->slug])}}">{{$item->name}}</a></h5>
                    </div>
                    <div class="read-more-btn">
                        <a class="edu-btn btn-dark" href="{{route('detailSolution',['slug'=>$item->slug])}}">Chi Tiết<i class="icon-arrow-right-line-right"></i></a>
                    </div>
                </div>
                </div>
            </div> <br>
            @endif
            @endforeach
             <div class="row">
                <div class="col-lg-12 mt--60">
                   <nav>
                      {{$list->links()}}
                   </nav>
                </div>
             </div>
          </div>
          <div class="col-lg-3">
            @include('layouts.main.sidebar')
          </div>
       </div>
       
    </div>
 </div>
@endsection