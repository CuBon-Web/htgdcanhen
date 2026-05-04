@extends('layouts.main.master')
@section('title')
Tự liệu tại TOEIC HÀ NỘI
@endsection
@section('description')
Tự liệu tại TOEIC HÀ NỘI
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')

@endsection
@section('js')
@endsection
@section('content')
<div class="eduvibe-home-four-service edu-service-area edu-section-gap bg-color-white position-relative border-bottom-1">
    <div class="container eduvibe-animated-shape">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title text-center sal-animate" data-sal-delay="150" data-sal="slide-up" data-sal-duration="800">
                    <span class="pre-title">TOEIC HÀ NỘI</span>
                    <h3 class="title">MATERIALS</h3>
                </div>
            </div>
        </div>
        <div class="row eduvibe-about-one-service g-5 mt--20">
            <div class="col-lg-9 col-md-9 col-12">
                <div class="row">

                    <!-- Start Service Grid  -->
                    <div class="col-lg-4 col-md-6 col-12 sal-animate" data-sal-delay="150" data-sal="slide-up" data-sal-duration="800">
                        <div class="service-card service-card-3 text-left shape-bg-1 bg-grey">
                            <div class="inner">
                                <div class="icon">
                                    <a href="{{route('listCategorymaindocs')}}">
                                        <i class="icon-book-mark-fill-solid"></i>
                                    </a>
                                </div>
                                <div class="content">
                                    <h6 class="title"><a href="{{route('listCategorymaindocs')}}">Học Liệu Online</a></h6>
                                    <p class="description">Toàn bộ các học liệu chất lượng được TOEIC HÀ NỘI số hóa, chuẩn hóa cho học viên.</p>
                                    <div class="read-more-btn">
                                        <a class="btn-transparent sm-size heading-color" href="{{route('listCategorymaindocs')}}">Bắt đầu<i class="icon-arrow-right-line-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Service Grid  -->

                    <!-- Start Service Grid  -->
                    <div class="col-lg-4 col-md-6 col-12 sal-animate" data-sal-delay="200" data-sal="slide-up" data-sal-duration="800">
                        <div class="service-card service-card-3 text-left shape-bg-2 bg-grey">
                            <div class="inner">
                                <div class="icon">
                                    <a href="{{route('listCategorymain')}}">
                                        <i class="icon-student-read"></i>
                                    </a>
                                </div>
                                <div class="content">
                                    <h6 class="title"><a href="{{route('listCategorymain')}}">Luyện Đề Online</a></h6>
                                    <p class="description">Đánh giá trình độ của bạn với hàng trăm đề thi ở mọi cấp độ</p>
                                    <div class="read-more-btn">
                                        <a class="btn-transparent sm-size heading-color" href="{{route('listCategorymain')}}">Bắt đầu<i class="icon-arrow-right-line-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Service Grid  -->

                    <!-- Start Service Grid  -->
                    <div class="col-lg-4 col-md-6 col-12 sal-animate" data-sal-delay="250" data-sal="slide-up" data-sal-duration="800">
                        <div class="service-card service-card-3 text-left shape-bg-3 bg-grey">
                            <div class="inner">
                                <div class="icon">
                                    <a href="{{route('couseList')}}">
                                        <i class="icon-Hand---Book"></i>
                                    </a>
                                </div>
                                <div class="content">
                                    <h6 class="title"><a href="{{route('couseList')}}">Khóa Học Online</a></h6>
                                    <p class="description">TOEIC HÀ NỘI  xây dựng hệ thống khóa học online tiện lợi, sát với nội dung ôn tập.</p>
                                    <div class="read-more-btn">
                                        <a class="btn-transparent sm-size heading-color" href="{{route('couseList')}}">Bắt đầu<i class="icon-arrow-right-line-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Service Grid  -->
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="edu-blog-widget widget-latest-post">
                    <div class="inner">
                       
                       <div class="content latest-post-list">
                        <div class="widget-content">

                            <div class="read-more-btn mt--15">
                                <a class="edu-btn w-100 text-center" href="{{route('listCategorymain')}}"><i class="icon-draft-line"></i> Luyện Đề Online</a>
                            </div>
                            <div class="read-more-btn mt--15">
                                <a class="edu-btn w-100 text-center" href="{{route('couseList')}}"><i class="icon-award-line"></i> Khóa Học Online</a>
                            </div>
                            <div class="read-more-btn mt--15">
                                <a class="edu-btn w-100 text-center" href="{{route('listCategorymaindocs')}}"><i class="icon-book-mark-fill-solid"></i> Học Liệu Online</a>
                            </div>
                        </div>
                        @if ($profile != "")
                        <h5 class="widget-title mt--25">Học liệu gần đây</h5>
                        {{-- {{dd($hoclieuganday)}} --}}
                        @foreach ($hoclieuganday as $item)
                        {{-- @php
                            dd($item);
                        @endphp --}}
                        <div class="latest-post">
                            <div class="post-content">
                                <ul class="blog-meta">
                                    <li>{{date_format($item->created_at,'d/m/Y')}}</li>
                                </ul>
                                <h6 class="title"><a href="{{route('detailResult',['id'=>$item->id])}}">{{$item->exam->name}}</a></h6>
                                @if ($item->type == 'fulltest')
                                            <div>
                                                <span class="result-badge-full">Full test</span>
                                            </div>
                                            @else 
                                            <div>
                                                @php
                                                    $partlt = json_decode($item->part_luyen_tap);
                                                @endphp
                                                <span class="result-badge-practice">Luyện tập</span>

                                                {{-- @foreach ($partlt as $t)
                                                    @php
                                                        $pa = \DB::table('quiz_part')->where('id',$t)->select('name')->first();
                                                    @endphp
                                                <span class="result-badge-practice">{{$pa->name}}</span>
                                                @endforeach --}}
                                            </div>
                                            @endif
                            </div>
                        </div>
                        @endforeach
                         <h5 class="widget-title mt--25">Đề Thi gần đây</h5>
                         @foreach ($dethiganday as $item)
                         {{-- @php
                             dd($item);
                         @endphp --}}
                         <div class="latest-post">
                             <div class="post-content">
                                 <ul class="blog-meta">
                                     <li>{{date_format($item->created_at,'d/m/Y')}}</li>
                                 </ul>
                                 <h6 class="title"><a href="{{route('detailResult',['id'=>$item->id])}}">{{$item->exam->name}}</a></h6>
                                 @if ($item->type == 'fulltest')
                                             <div>
                                                 <span class="result-badge-full">Full test</span>
                                             </div>
                                             @else 
                                             <div>
                                                 @php
                                                     $partlt = json_decode($item->part_luyen_tap);
                                                 @endphp
                                                 <span class="result-badge-practice">Luyện tập</span>
 
                                                 {{-- @foreach ($partlt as $t)
                                                     @php
                                                         $pa = \DB::table('quiz_part')->where('id',$t)->select('name')->first();
                                                     @endphp
                                                 <span class="result-badge-practice">{{$pa->name}}</span>
                                                 @endforeach --}}
                                             </div>
                                             @endif
                             </div>
                         </div>
                         @endforeach
                        @endif
                          <!-- Start Single Post  -->
                          
                       </div>
                    </div>
                </div>
            </div>
           
        </div>

        <div class="shape-dot-wrapper shape-wrapper d-xl-block d-none">
            <div class="shape-image shape-image-1">
                <img src="{{url('frontend/images/shape-29.png')}}" alt="Shape Thumb">
            </div>
            <div class="shape-image shape-image-2">
                <img src="{{url('frontend/images/shape-03-06.png')}}" alt="Shape Thumb">
            </div>
            <div class="shape-image shape-image-3">
                <img src="{{url('frontend/images/shape-02-06.png')}}" alt="Shape Thumb">
            </div>
            <div class="shape-image shape-image-4">
                <img src="{{url('frontend/images/shape-19-02.png')}}" alt="Shape Thumb">
            </div>
        </div>
    </div>
</div>
@endsection