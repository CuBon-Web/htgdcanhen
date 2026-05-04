@extends('layouts.main.master')
@section('title')
    {{ $detail->name }} | Đội ngũ giáo viên tại Edu Alpha
@endsection
@section('description')
    Là những giáo viên giỏi kiến thức và giỏi truyền đạt. Rất tận tâm với học viên, đi dạy vì cái tâm và luôn khát khao cải
    tiến việc học ở Việt Nam.
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('css')
@endsection
@section('js')
@endsection
@section('content')
    <section class="page-header">
        <div class="page-header__bg"
            style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/page-header-bg-shape.png);">
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
                    <div class="col-lg-12">
                        <h2>{{ $detail->name }}</h2>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Đội ngũ giáo viên</li>
                                <li><span>//</span></li>
                                <li>{{ $detail->name }}</li>
                            </ul>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </section>
    <section class="team-details">
        <div class="container">
            <div class="row">
                <div class="col-xl-5">
                    <div class="team-details__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                        <div class="team-details__img">
                            <img src="{{ $detail->images }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-xl-7">
                    <div class="team-details__right">
                        <div class="team-details__name-and-ratting-box">
                            <div class="team-details__name-box">
                                <h3 class="team-details__name">{{ $detail->name }}</h3>
                                <p class="team-details__sub-title">Giáo viên tại Edu Alpha</p>
                            </div>
                        </div>
                        @php
                            $des = json_decode($detail->description);
                        @endphp
                        <ul class="team-details__meta list-unstyled">
                            @foreach ($des as $item)
                                <li>
                                    <div class="icon">
                                        <span class="fas fa-check"></span>
                                    </div>
                                    <div class="content">
                                        <span>{{ $item->title }}</span>
                                    </div>
                                </li>
                            @endforeach

                        </ul>
                        <h3 class="team-details__title">Thông tin giáo viên</h3>
                        {!! $detail->content !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
