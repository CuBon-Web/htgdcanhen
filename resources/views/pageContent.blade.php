@extends('layouts.main.master')
@section('title')
{{$pagecontentdetail->title}}
@endsection
@section('description')
{{$pagecontentdetail->title}}
@endsection
@section('image')
{{url(''.$banner[0]->image)}}
@endsection
@section('css')
@endsection
@section('js')
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
                    <h1>{{$pagecontentdetail->title}}</h1>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="{{route('home')}}">Trang chủ</a></li>
                            <li><span>//</span></li>
                            <li><a href="">Chính sách</a></li>
                            <li><span>//</span></li>
                            <li>{{$pagecontentdetail->title}}</li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
 </section>
 <section class="blog-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="blog-details__left">
                    <div class="blog-details__content">
                        {!!($pagecontentdetail->content)!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection