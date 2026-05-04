<div class="header-area">
    <div class="container-fluid">
       <div class="header-content-wrapper">
          <div class="header-content d-flex justify-content-between align-items-center">
             <div class="header-left-content d-flex">
                <div class="responsive-burger-menu d-block d-lg-none">
                   <span class="top-bar"></span>
                   <span class="middle-bar"></span>
                   <span class="bottom-bar"></span>
                </div>
                <div class="main-logo">
                   <a href="{{route('profile')}}">
                   <img src="{{$setting->logo}}" alt="main-logo">
                   </a>
                </div>
                {{-- <form class="search-bar d-flex">
                   <img src="/frontend/crm-course/images/search-normal.svg" alt="search-normal">
                   <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                </form>
                <div class="option-item for-mobile-devices d-block d-lg-none">
                   <i class="search-btn ri-search-line"></i>
                   <i class="close-btn ri-close-line"></i>
                   <div class="search-overlay search-popup">
                      <div class='search-box'>
                         <form class="search-form">
                            <input class="search-input" name="search" placeholder="Search" type="text">
                            <button class="search-button" type="submit">
                            <i class="ri-search-line"></i>
                            </button>
                         </form>
                      </div>
                   </div>
                </div> --}}
             </div>
             <div class="header-right-content d-flex align-items-center">
                <div class="header-right-option">
                   <a href="#" class="dropdown-item fullscreen-btn" id="fullscreen-button">
                   <img src="/frontend/crm-course/images/maximize.svg" alt="maximize">
                   </a>
                </div>
                @if ($profile->type == 1)
                <div class="header-right-option notification-option messenger-option dropdown d-lg-block d-none">
                   <div class="dropdown-item dropdown-toggle">
                    <a href="{{route('postCouse')}}" class="default-btn active me-1"><i class="ri-add-line"></i>  Tạo Khóa Học</a>
                   </div>
                </div>
                @endif
                <div class="header-right-option dropdown profile-nav-item pt-0 pb-0">
                   <a class="dropdown-item dropdown-toggle avatar d-flex align-items-center" href="#" id="navbarDropdown-4" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <img src="{{$profile->avatar ? url('uploads/images/'.$profile->avatar) : url('frontend/crm-course/images/user_icon.png')}}" alt="avatar">
                      <div class="d-none d-lg-block d-md-block">
                         <h3>{{$profile->name}}</h3>
                         <span>{{$profile->type == 3 ? 'Super Admin' : ($profile->type == 1 ? 'Giáo Viên' : 'Học Sinh')}}</span>
                      </div>
                   </a>
                   <div class="dropdown-menu">
                      <div class="dropdown-wrap">
                         <ul class="profile-nav p-0 pt-3">
                            <li class="nav-item">
                               <a href="{{route('chinhSuaTrangCaNhan')}}" class="nav-link">
                               <i class="ri-user-line"></i> 
                               <span>Trang cá nhân</span>
                               </a>
                            </li>
                         </ul>
                      </div>
                      <div class="dropdown-footer">
                         <ul class="profile-nav">
                            <li class="nav-item">
                               <a href="{{route('logout')}}" class="nav-link">
                               <i class="ri-login-circle-line"></i> 
                               <span>Đăng xuất</span>
                               </a>
                            </li>
                         </ul>
                      </div>
                   </div>
                </div>
                <div class="header-right-option template-option">
                   <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                   <img src="/frontend/crm-course/images/setting.svg" alt="setting">
                   </a>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>
<nav class="sidebar-menu">
    <ul class="list-group flex-column d-inline-block first-menu" data-simplebar>
       <li class="list-group-item main-grid active" >
          <a href="{{route('profile')}}" class="icon" data-toggle="tooltip" data-placement="right" title="Trang chủ">
          <img src="/frontend/crm-course/images/element.svg" alt="element">
          </a>
       </li>
       {{-- <li class="list-group-item main-grid" data-toggle="tooltip" data-placement="bottom" title="Trò chơi">
         <a href="{{route('gamelistAll')}}" class="icon">
         <img src="/frontend/crm-course/images/11-game-s-svgrepo-com.svg" alt="messages">
         </a>
      </li> --}}
       <li class="list-group-item main-grid" data-toggle="tooltip" data-placement="bottom" title="Khóa học">
          <a href="{{route('myCouseGiaoVien')}}" class="icon">
          <img src="/frontend/crm-course/images/couse.svg" alt="calendar">
          </a>
       </li>
       <li class="list-group-item main-grid" data-toggle="tooltip" data-placement="bottom" title="Đề Thi">
         <a href="{{route('khoiTaoDeThi')}}" class="icon">
         <img src="/frontend/crm-course/images/dethi.svg" alt="document-copy">
         </a>
      </li>
       <li class="list-group-item main-grid" data-toggle="tooltip" data-placement="bottom" title="Bài Giảng">
          <a href="{{route('danhSachBaiTap')}}" class="icon">
          <img src="/frontend/crm-course/images/baitap.svg" alt="messages">
          </a>
       </li>
       @if ($profile->type == 1 || $profile->type == 3)
      
       <li class="list-group-item main-grid" data-toggle="tooltip" data-placement="bottom" title="Quản Lý Lớp Học">
         <a href="{{route('classes.index')}}" class="icon">
         <img src="/frontend/crm-course/images/user-octagon.svg" alt="messages">
         </a>
      </li>
      {{-- <li class="list-group-item main-grid" data-toggle="tooltip" data-placement="bottom" title="Quản Lý quà tặng">
         <a href="{{route('game.reward.index')}}" class="icon">
         <img src="/frontend/crm-course/images/gift-svgrepo-com.svg" alt="messages">
         </a>
      </li> --}}
      
      @endif
      {{-- @if ($profile->type == 3)
      <li class="list-group-item main-grid" data-toggle="tooltip" data-placement="bottom" title="Quản Lý Trang HTML">
         <a href="{{route('html-pages.index')}}" class="icon">
         <img src="/frontend/crm-course/images/html5-svgrepo-com.svg" alt="messages">
         </a>
       </li>
      @endif --}}
    </ul>
 </nav>