<header class="main-header-two">
    <nav class="main-menu main-menu-two">
       <div class="main-menu-two__wrapper">
          <div class="container">
             <div class="main-menu-two__wrapper-inner">
                <div class="main-menu-two__left">
                   <div class="main-menu-two__logo">
                      <a href="{{route('home')}}"><img src="{{$setting->logo}}" alt=""></a>
                   </div>
                </div>
                <div class="main-menu-two__main-menu-box">
                   <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                   <ul class="main-menu__list">
                      <li class="dropdown">
                         <a href=#>Về chúng tôi</a>
                         <ul class="shadow-box">
                             <li><a href="{{route('aboutUs')}}">Giới thiệu</a></li>
                             <li><a href="{{route('listTeacher')}}">Đội ngũ giảng viên</a></li>
                            @foreach ($servicehome as $item)
                            <li><a href="{{route('serviceDetail',['slug'=>$item->slug])}}">{{$item->name}}</a></li>
                            @endforeach
                            <li>
                               <a href="{{route('diemReview')}}">Đánh giá học viên</a>
                            </li>
                         </ul>
                      </li>
                      
                      <li class="dropdown">
                         <a href="{{route('couseList')}}">Khóa học</a>
                         <ul class="shadow-box">
                            @foreach ($categoryhome as $item)
                            <li><a href="{{route('couseListCate',['cate_slug'=>$item->slug])}}">{{languageName($item->name)}}</a></li>
                            @endforeach
                            
                         </ul>
                      </li>
 
                       <li class="dropdown">
                         <a href="">Tài Liệu</a>
                         <ul class="shadow-box">
                            {{-- @foreach ($cateDocument as $item)
                            <li><a href="{{route('listTailieu',['slug'=>$item->slug])}}">{{$item->name}}</a></li>
                            @endforeach --}}
                            
                         </ul>
                      </li>
                      
                      <li class="dropdown">
                         <a href="">Kiến thức và Sự Kiện</a>
                         <ul class="shadow-box">
                            @foreach ($blogCate as $item)
                            <li><a href="{{route('listCateBlog',['slug'=>$item->slug])}}">{{languageName($item->name)}}</a></li>
                            @endforeach
                         </ul>
                      </li>
                      <li>
                         <a href="{{route('lienHe')}}">Liên Hệ</a>
                      </li>
                   </ul>
                </div>
                <div class="main-menu-two__right">
                   {{-- <div class="main-menu-two__search-box">
                      <a href=# class="main-menu-two__search searcher-toggler-box icon-search"></a>
                   </div> --}}
                   <div class="main-menu-two__signin-reg">
                      @if ($profile)
                      <div class="main-menu-two__signin-reg-icon">
                        <img src="{{$profile->avatar ? url('uploads/images/'.$profile->avatar) : url('frontend/images/user_icon.png')}}" alt="">
                      </div>
                      <div class="main-menu-two__signin-reg-content">
                         <a href="{{route('profile')}}" class="main-menu-two__signin">{{$profile->name}}</a>
                         <a href="{{route('logout')}}" class="main-menu-two__reg">Đăng xuất</a>
                      </div>
                      @else 
                      <div class="main-menu-two__signin-reg-icon">
                         <span class="icon-user-plus"></span>
                      </div>
                      <div class="main-menu-two__signin-reg-content">
                         <a href="{{route('login')}}" class="main-menu-two__signin">Đăng nhập</a>
                         <a href="{{route('register')}}" class="main-menu-two__reg">Đăng ký</a>
                      </div>
                      @endif
                      
                   </div>
                   {{-- <div class="main-menu__search-cart-box">
                         <div class="main-menu__cart">
                             <a href="#"><span class="fas fa-heart"></span></a>
                         </div>
                     </div> --}}
                </div>
             </div>
          </div>
       </div>
    </nav>
 </header>
 <div class="stricky-header stricked-menu main-menu">
    <div class="sticky-header__content"></div>
    <!-- /.sticky-header__content -->
 </div>