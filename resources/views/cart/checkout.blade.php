@extends('layouts.main.master')
@section('title')
Hoàn Tất Đăng Ký
@endsection
@section('description')
Hoàn Tất Đăng Ký Của Bạn
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
                        <h2>Đăng ký thành công</h2>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Giỏ hàng</li>
                                 <li><span>//</span></li>
                                <li>Đăng ký thành công</li>
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
                <div class="col-xl-9 col-lg-12">
                <p>Bạn đã đăng ký thành công</p>
                <p>Vui lòng thực hiện chuyển khoản với số tiền
                   <b> {{ number_format($total) }}₫ </b>
               đến một trong các tài khoản sau:</p>
               <p>Số tài khoản: <b></b></p>
               <p></p>
               <b>Bước 2:</b>
               <p>Chụp bill chuyển khoản gửi về chúng tôi qua Zalo: <a href="https://zalo.me/{{$setting->phone1}}">{{$setting->phone1}}</a> với nội dung "{{$bill}}"</p>
               <b>Bước 3:</b>
               <p>Đợi chúng tôi mở khóa khóa học cho bạn</p>
               <p>Cảm ơn bạn đã tin tưởng sử dụng hệ thống của chúng tôi..!</p>
            </div>
            <div class="col-xl-3 col-lg-12">
                <img class="w-100" src="{{$setting->popupimage}}" alt="">
            </div>
            </div>
        </div>
     </section>
@endsection

