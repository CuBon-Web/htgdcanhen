@extends('layouts.main.master')
@section('title')
{{languageName($cate->name)}}
@endsection
@section('description')
Tin tức nổi bật và mới nhất
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')
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
               <div class="col-lg-12 text-center">
                   <h1>{{languageName($cate->name)}}</h1>
                   <div class="thm-breadcrumb__box">
                       <ul class="thm-breadcrumb list-unstyled">
                           <li><a href="{{route('home')}}">Trang chủ</a></li>
                           <li><span>//</span></li>
                           <li>{{languageName($cate->name)}}</li>
                       </ul>
                   </div>
                   <div class="desc mb-3">
                       {{-- {!!languageName($detail_service->description)!!} --}}
                   </div>
                   
               </div>
           </div>
       </div>
   </div>
</section>
<section class="blog-page">
   <div class="container">
       <div class="row">
         @if (count($blog) > 0)
         @foreach ($blog as $item)
           <!--Blog Two Single Start -->
           <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="100ms">
               <div class="blog-two__single">
                  <a href="{{route('detailBlog',['slug'=>$item->slug])}}">
                     <div class="blog-two__img">
                        <img src="{{$item->image}}" alt="{{$item->slug}}">
                        <div class="blog-two__date">
                            <span class="icon-calendar"></span>
                            <p>{{date_format($item->created_at,'d/m/Y')}}</p>
                        </div>
                    </div>
                  </a>
                   
                   <div class="blog-two__content">
                       <h4 class="blog-two__title"><a href="{{route('detailBlog',['slug'=>$item->slug])}}">{{languageName($item->title)}}</a></h4>
                       <p class="blog-two__text">{{languageName($item->description)}}</p>
                   </div>
               </div>
           </div>
           <!--Blog Two Single End -->
           @endforeach
           @else 
           <div class="col-lg-12 col-md-12 col-12 sal-animate" data-wow-delay="100ms">
              <h5>Nội dung đang cập nhật</h5>
           </div>
           @endif
       </div>
       <div class="row">
           <div class="col-xl-12">
            {{$blog->links()}}
           </div>
       </div>
   </div>
</section>


@endsection