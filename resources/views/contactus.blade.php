@extends('layouts.main.master')
@section('title')
Liên hệ với chúng tôi
@endsection
@section('description')
Liên hệ với chúng tôi
@endsection
@section('image')
{{url(''.$setting->logo)}}
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
                   <h1>Liên hệ với chúng tôi</h1>
                   <div class="thm-breadcrumb__box">
                       <ul class="thm-breadcrumb list-unstyled">
                           <li><a href="{{route('home')}}">Trang chủ</a></li>
                           <li><span>//</span></li>
                           <li>Về chúng tôi</li>
                       </ul>
                   </div>
               </div>
           </div>
       </div>
   </div>
</section>
<section class="contact-two">
   <div class="container">
       <ul class="row list-unstyled">
           <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="100ms">
               <div class="contact-two__single">
                   <div class="contact-two__icon">
                       <img src="{{ env('AWS_R2_URL') }}/frontend/images/contact-two-icon-1.png" alt="">
                   </div>
                   <h3 class="contact-two__title">Địa chỉ</h3>
                   <p>{{$setting->address1}}</p>
               </div>
           </li>
           <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="200ms">
               <div class="contact-two__single">
                   <div class="contact-two__icon">
                       <img src="{{ env('AWS_R2_URL') }}/frontend/images/contact-two-icon-2.png" alt="">
                   </div>
                   <h3 class="contact-two__title">Hotline</h3>
                   <p><a href="tel:{{$setting->phone1}}">{{$setting->phone1}}</a></p>
               </div>
           </li>
           <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInRight" data-wow-delay="300ms">
               <div class="contact-two__single">
                   <div class="contact-two__icon">
                       <img src="{{ env('AWS_R2_URL') }}/frontend/images/contact-two-icon-3.png" alt="">
                   </div>
                   <h3 class="contact-two__title">Email</h3>
                   <p><a href="mailto:{{$setting->email}}">{{$setting->email}}</a></p>
               </div>
           </li>
           <li class="col-xl-3 col-lg-6 col-md-6 wow fadeInRight" data-wow-delay="400ms">
               <div class="contact-two__single">
                   <div class="contact-two__icon">
                       <img src="{{ env('AWS_R2_URL') }}/frontend/images/contact-two-icon-4.png" alt="">
                   </div>
                   <h3 class="contact-two__title">Class Schedule</h3>
                   <p>10:00 AM - 6:00 PM<br> Monday - Friday</p>
               </div>
           </li>
       </ul>
   </div>
</section>
<section class="contact-three">
   <div class="container">
       <div class="row">
           <div class="col-xl-6 col-lg-6">
               <div class="contact-three__left">
                   <div class="contact-three__img">
                       <img src="{{ env('AWS_R2_URL') }}/frontend/images/contact-three-img-1.jpg" alt="">
                   </div>
               </div>
           </div>
           <div class="col-xl-6 col-lg-6">
               <div class="contact-three__right">
                   <div class="section-title-two text-left sec-title-animation animation-style1">
                       <div class="section-title-two__tagline-box">
                           <div class="section-title-two__tagline-shape">
                               <img src="{{ env('AWS_R2_URL') }}/frontend/images/section-title-two-shape-1.png" alt="">
                           </div>
                           <span class="section-title-two__tagline">Get in Touch</span>
                       </div>
                       <h2 class="section-title-two__title title-animation">Chúng tôi ở đây để giúp đỡ và sẵn sàng lắng nghe bạn</h2>
                   </div>
                   <form class="contact-form-validated contact-three__form"  >
                         <div class="row">
                           <div class="col-xl-6 col-lg-6">
                               <h4 class="contact-three__input-title">Họ Tên</h4>
                               <div class="contact-three__input-box">
                                   <input type="text" name="name" placeholder="John Doe" required="">
                               </div>
                           </div>
                           <div class="col-xl-6 col-lg-6">
                               <h4 class="contact-three__input-title">Email Address *</h4>
                               <div class="contact-three__input-box">
                                   <input type="email" name="email" placeholder="john@domain.com" required="">
                               </div>
                           </div>
                           <div class="col-xl-12">
                               <h4 class="contact-three__input-title">Số điện thoại</h4>
                               <div class="contact-three__input-box">
                                   <input type="text" name="phone" placeholder="0990931433" required="">
                               </div>
                           </div>
                           <div class="col-xl-12">
                               <h4 class="contact-three__input-title">Lời nhắn</h4>
                               <div class="contact-three__input-box text-message-box">
                                   <textarea name="messs" placeholder="Write Your Message"></textarea>
                               </div>
                               <div class="contact-three__btn-box">
                                   <button type="submit" class="thm-btn-two contact-three__btn">
                                       <span>Gửi</span><i class="icon-angles-right"></i>
                                   </button>
                               </div>
                           </div>
                       </div>
                   </form>
               </div>
           </div>
       </div>
   </div>
</section>

@endsection