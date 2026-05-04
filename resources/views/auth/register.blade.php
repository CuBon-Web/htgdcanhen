@extends('layouts.main.master')
@section('title')
    Đăng ký thành viên
@endsection
@section('description')
    Đăng ký thành viên
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
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
                    <div class="col-lg-8">
                        <h1>Đăng ký tài khoản</h1>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Đăng ký</li>
                            </ul>
                        </div>
                        <div class="desc mb-3">
                            Đăng ký ngay để bắt đầu trải nghiệm học và luyện thi hiệu quả cùng hàng trăm
                            ngàn học viên mỗi ngày
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="page-header__img">
                            <img src="/uploads/images/danh-muc-797808265.png" alt="">
                            <div class="page-header__shape-1">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-1.png" alt="">
                            </div>
                            <div class="page-header__shape-2">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/banner-two-book-icon.png" alt="">
                            </div>
                            <div class="page-header__shape-3">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-3.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="sign-up-one">
        <div class="container">
            <div class="course-details__main-tab-box tabs-box">
                <ul class="tab-buttons list-unstyled">
                    <li data-tab="#hocsinh" class="tab-btn tab-btn-one active-btn">
                        <p><span class="fas fa-graduation-cap"></span> Học Sinh</p>
                    </li>
                    <li data-tab="#giaovien" class="tab-btn tab-btn-two">
                        <p><span class="fas fa-chalkboard-teacher"></span> Giáo Viên</p>
                    </li>
                </ul>
                <div class="tabs-content">
                    <div class="tab active-tab" id="hocsinh">
                        <div class="sign-up-one__form">
                            <form id="sign-up-one__form" name="sign-up-one_form" action="{{ route('postRegister') }}"
                                method="post">
                                @csrf
                                <input type="text" hidden name="type" value="0">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="name" class="form-label">Họ Tên:</label>
                                                <input placeholder="Nhập Họ Tên" type="text"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('name') ? old('name') : '' }}" name="name"
                                                    id="lastName" required="">
                                                    @error('name')
                                                        <i class="noti_error">{{ $message }}</i>
                                                    @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="phone" class="form-label">Số điện thoại:</label>
                                                <input placeholder="Nhập Số điện thoại" type="phone"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('phone') ? old('phone') : '' }}" name="phone"
                                                    required="">
                                                @error('phone')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="email" class="form-label">Địa chỉ Email:</label>
                                                <input placeholder="Nhập Địa chỉ Email" type="email"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('email') ? old('email') : '' }}" name="email"
                                                    id="email" placeholder="Email" >
                                                @error('email')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                                <small style="color: #6b7280; margin-top: 4px; display: block;">
                                                    <i class="fas fa-info-circle"></i> Có thể bỏ trống email
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="class_code" class="form-label">Mã lớp:</label>
                                                <input placeholder="Nhập mã lớp (VD: ABC123, XYZ456)" type="text"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('class_code') }}" name="class_code"
                                                    style="text-transform: uppercase;">
                                                @error('class_code')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                                <small style="color: #6b7280; margin-top: 4px; display: block;">
                                                    <i class="fas fa-info-circle"></i> Nhập mã lớp do giáo viên cung cấp để tham gia lớp (tùy chọn). Có thể nhập nhiều mã lớp, phân cách bằng dấu phẩy hoặc khoảng trắng.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="password" class="form-label">Mật khẩu:</label>
                                                <input placeholder="Nhập Mật khẩu" type="password"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('password') ? old('password') : '' }}" name="password"
                                                    id="password" required="">
                                                @error('password')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="password_confirm" class="form-label">Xác nhận mật khẩu:</label>
                                                <input placeholder="Nhập Xác nhận mật khẩu" type="password"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('password_confirm') ? old('password_confirm') : '' }}" name="password_confirm"
                                                    id="password_confirm" required="">
                                                @error('password_confirm')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <button class="thm-btn" type="submit">Đăng ký</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="create-account text-center">
                                    <p>Bạn đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a></p>

                                </div>
                            </form>
                        </div>
                    </div>
                    <!--Tab-->
                    <div class="tab" id="giaovien">
                        <div class="sign-up-one__form">
                            <form id="sign-up-one__form" name="sign-up-one_form" action="{{ route('postRegisterGiaovien') }}"
                                method="post">
                                @csrf
                                <input type="text" hidden name="type" value="1">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="name" class="form-label">Họ Tên:</label>
                                                <input placeholder="Tên Thầy/Cô" type="text"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('name') ? old('name') : '' }}" name="name"
                                                    id="lastName" required="">
                                                @error('name')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="phone" class="form-label">Số điện thoại:</label>
                                                <input placeholder="Nhập Số điện thoại" type="phone"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('phone') ? old('phone') : '' }}" name="phone"
                                                    required="">
                                                @error('phone')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="email" class="form-label">Địa chỉ Email:</label>
                                                <input placeholder="Nhập Địa chỉ Email" type="email"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('email') ? old('email') : '' }}" name="email"
                                                    id="email" placeholder="Email">
                                                @error('email')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                                <small style="color: #6b7280; margin-top: 4px; display: block;">
                                                    <i class="fas fa-info-circle"></i> Có thể bỏ trống email
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="bomon" class="form-label">Bộ môn giảng dạy:</label>
                                                <select name="bomon" id="bomon" class="form-control form-control-lg">
                                                    <option value="">Chọn bộ môn</option>
                                                    <option value="Toán" {{ old('bomon') == 'Toán' ? 'selected' : '' }}>Toán</option>
                                                    <option value="Vật lý" {{ old('bomon') == 'Vật lý' ? 'selected' : '' }} >Vật lý</option>
                                                    <option value="Hóa học" {{ old('bomon') == 'Hóa học' ? 'selected' : '' }} >Hóa học</option>
                                                    <option value="Sinh học" {{ old('bomon') == 'Sinh học' ? 'selected' : '' }} >Sinh học</option>
                                                    <option value="Ngữ văn" {{ old('bomon') == 'Ngữ văn' ? 'selected' : '' }} >Ngữ văn</option>
                                                    <option value="Tiếng Anh" {{ old('bomon') == 'Tiếng Anh' ? 'selected' : '' }} >Tiếng Anh</option>
                                                    <option value="Lịch sử" {{ old('bomon') == 'Lịch sử' ? 'selected' : '' }} >Lịch sử</option>
                                                    <option value="Địa lý" {{ old('bomon') == 'Địa lý' ? 'selected' : '' }} >Địa lý</option>
                                                    <option value="GDCD" {{ old('bomon') == 'GDCD' ? 'selected' : '' }} >GDCD</option>
                                                    <option value="Khoa học" {{ old('bomon') == 'Khoa học' ? 'selected' : '' }} >Khoa học</option>
                                                    <option value="Công nghệ" {{ old('bomon') == 'Công nghệ' ? 'selected' : '' }}   >Công nghệ</option>
                                                    <option value="Thể dục" {{ old('bomon') == 'Thể dục' ? 'selected' : '' }} >Thể dục</option>
                                                </select>
                                                
                                                @error('bomon')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="caphoc" class="form-label">Cấp học giảng dạy:</label>
                                                <select name="caphoc" id="caphoc" class="form-control form-control-lg">
                                                    <option value="">Chọn cấp học</option>
                                                    <option value="Cấp 1" {{ old('caphoc') == 'Cấp 1' ? 'selected' : '' }} >Cấp 1</option>
                                                    <option value="Cấp 2" {{ old('caphoc') == 'Cấp 2' ? 'selected' : '' }} >Cấp 2</option>
                                                    <option value="Cấp 3" {{ old('caphoc') == 'Cấp 3' ? 'selected' : '' }} >Cấp 3</option>
                                                </select>
                                                @error('caphoc')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="password" class="form-label">Mật khẩu:</label>
                                                <input placeholder="Nhập Mật khẩu" type="password"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('password') ? old('password') : '' }}" name="password"
                                                    id="password" required="">
                                                @error('password')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <label for="password_confirm" class="form-label">Xác nhận mật khẩu:</label>
                                                <input placeholder="Nhập Xác nhận mật khẩu" type="password"
                                                    class="form-control form-control-lg"
                                                    value="{{ old('password_confirm') ? old('password_confirm') : '' }}" name="password_confirm"
                                                    id="password_confirm" required="">
                                                @error('password_confirm')
                                                    <i class="noti_error">{{ $message }}</i>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <button class="thm-btn" type="submit">Đăng ký</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="create-account text-center">
                                    <p>Bạn đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a></p>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
