@extends('layouts.main.master')
@section('title')
{{languageName($danhmuc->name)}}
@endsection
@section('description')
{{($danhmuc->description)}}
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')
<link rel="stylesheet" href="/frontend/css/faq.css">
@endsection
@section('js')
@endsection
@section('content')


<section class="courses-one">
    <div class="container">
        <div class="section-title text-center sec-title-animation animation-style1">
            <h2 class="section-title__title title-animation">Các Khóa Học
                <span>{{languageName($danhmuc->name)}} <img src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-shape-1.png" alt=""></span></h2>
        </div>
        <div class="row">
            @foreach ($list as $item)
            <div class="col-lg-4 mt-3">
                <div class="item">
                    <div class="courses-one__single">
                        <a href="{{route('couseDetail',['slug'=>$item->slug])}}">
                            <div class="courses-one__img-box">
                                <div class="courses-one__img">
                                    <img src="{{$item->images}}" alt="">
                                </div>
                            </div>
                        </a>
                        <div class="courses-one__content">
                            <div class="courses-one__tag-and-meta">
                                <ul class="courses-one__meta list-unstyled">
                                     <li>
                                        <div class="icon">
                                            <span class="icon-book"></span>
                                        </div>
                                        <p>{{$item->ingredient}} Bài học</p>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <span class="icon-clock"></span>
                                        </div>
                                        <p>{{$item->thickness}} Buổi học</p>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="courses-one__title"><a href="{{route('couseDetail',['slug'=>$item->slug])}}">{{($item->name)}}</a></h3>
                                    <div class="line_2">{!!($item->description)!!}</div>
                            <div class="courses-one__ratting-and-heart-box">
                                
                            </div>
                            <div class="courses-one__btn-and-doller-box">
                                <div class="courses-two__client-box">
                                    <div class="courses-two__client-img">
                                        @if ($item->user_id == 0)
                                            <img src="{{url('frontend/images/user_icon.png')}}" alt="">
                                        @else 
                                        @php
                                            $teacher = \DB::table('customer')->where('id',$item->user_id)->first(['name','avatar']);
                                        @endphp
                                                <img src="{{$item->customer->avatar ? url('uploads/images/'.$teacher->avatar) : url('frontend/images/user_icon.png')}}" alt="">
                                        @endif
                                    </div>
                                    <div class="courses-two__client-content">
                                        <h4>{{$item->user_id == 0 ? 'Quản trị viên' : $teacher->name}}</h4>
                                        <p>Giáo viên</p>
                                    </div>
                                </div>
                                <div class="courses-one__btn-box">
                                    <a href="{{route('couseDetail',['slug'=>$item->slug])}}" class="courses-one__btn thm-btn"><span
                                            class="icon-angles-right"></span>Chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            {{$list->links()}}
        </div>

        
    </div>
</section>


@endsection