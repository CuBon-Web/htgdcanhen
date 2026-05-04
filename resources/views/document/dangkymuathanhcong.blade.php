@extends('layouts.main.master')
@section('title')
Đăng ký mua tài liệu thành công
@endsection
@section('description')
Đăng ký mua tài liệu thành công
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
                        <h2>Đăng ký mua tài liệu thành công</h2>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Tài liệu</li>
                                 <li><span>//</span></li>
                                <li>Đăng ký mua thành công</li>
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
                <p>Bạn đã đăng ký thành công tài liệu <b>{{$document->name}}</b></p>
                <b>Bước 1:</b>
                <p>Vui lòng thực hiện chuyển khoản với số tiền  
                   <b> {{$document->price}}₫ </b>
               đến một trong các tài khoản sau:</p>
               <p>Số tài khoản: <b></b></p>
               <p>Nội dung chuyển khoản: <b>{{$bill->bill_id}}</b></p>
               <b>Bước 2:</b>
               <p>Chụp bill chuyển khoản gửi về chúng tôi qua Zalo: <a  href="https://zalo.me/{{$setting->phone1}}">{{$setting->phone1}}</a></p>
               <b>Bước 3:</b>
               <p>Đợi chúng tôi mở khóa đề thi cho bạn hoặc theo dõi qua đường dẫn: <a  href="{{route('documentListDamua')}}">Bấm để xem</a></p>
               <p>Cảm ơn bạn đã tin tưởng sử dụng hệ thống của chúng tôi..!</p>
            </div>
            <div class="col-xl-3 col-lg-12">
                <img class="w-100" src="{{$setting->popupimage}}" alt="">
            </div>
            </div>
        </div>
     </section>
@endsection

