@extends('layouts.main.master')
@section('title')
    Trang cá nhân
@endsection
@section('description')
    Trang cá nhân
@endsection
@section('image')
    {{ url('' . $banner[0]->image) }}
@endsection
@section('css')
    <style>
        .instructor-profile-left {
            background: #f5f5f5;
            border-radius: 5px;
            padding: 22px 9px;
        }

        .instructor-profile-left .inner .thumbnail {
            max-width: 230px;
            max-height: 230px;
            margin: 0 auto 30px;
        }

        .instructor-profile-left .inner .thumbnail img {
            border-radius: 100%;
            width: 100px;
            height: 100px;
        }

        .instructor-profile-left .inner .content .title {
            font-weight: 700;
            font-size: 24px;
            line-height: 36px;
            margin-bottom: 2px;
        }

        .instructor-profile-left .inner .content .subtitle {
            font-weight: 600;
            line-height: 26px;
            color: var(--color-primary);
            display: block;
            margin-bottom: 25px;
        }

        .courses-two__status {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 2;
        }

        .courses-two__status p.pending {
            margin: 0;

        }

        .courses-two__status p {
            margin: 0;

        }

        .courses-two__status.pending {
            background: #970a17;
        }

        .courses-two__status.success {
            background: rgb(5, 133, 62);
        }

        .courses-two__status {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 2;
            padding: 5px 15px;
            color: white;
            border-radius: 5px;
        }
        button.btn.need_active {
            background: #b90808;
            color: white;
                font-size: 13px;
            padding: 5px 5px;
            line-height: normal;
        }
        button.btn.no_active {
            background: #04ac2e;
            color: white;
                font-size: 13px;
            padding: 5px 5px;
        line-height: normal;
        }
    </style>
@endsection
@section('content')
    {{-- <section class="page-header">
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
                    <div class="col-lg-12 text-center">
                        <h1>Trang cá nhân</h1>
                        <div class="thm-breadcrumb__box">
                            <ul class="thm-breadcrumb list-unstyled">
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li><span>//</span></li>
                                <li>Trang cá nhân</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <section class="blog-details" style="padding: 120px 0 100px;">
        <div class="container">
            <div class="row">

                <div class="col-xl-12 col-lg-12">
                    <div class="become-a-teacher__top">
                        <div class="section-title-two text-center sec-title-animation animation-style1">
                            <div class="section-title-two__tagline-box">
                                <span class="section-title-two__tagline">Profile</span>
                            </div>
                            <h2 class="section-title-two__title title-animation">{{ $profile->name }}
                            </h2>
                        </div>
                        <div class="become-a-teacher__tab-box tabs-box">
                            <ul class="tab-buttons clearfix list-unstyled d-flex">
                                @if ($profile->type == 1 || $profile->type == 3)
                                    <li data-tab="#khoahoc" class="tab-btn active-btn"><span>Khóa học</span></li>
                                     <li data-tab="#tailieu" class="tab-btn"><span>Tài liệu</span></li>
                                    <li data-tab="#baithi" class="tab-btn"><span>Bài tập trong khóa học</span></li>
                                    <li data-tab="#chamdiem" class="tab-btn"><span>Chấm điểm</span></li>
                                @else
                                    <li data-tab="#khoahoc" class="tab-btn active-btn"><span>Khóa học đã mua</span></li>
                                    <li data-tab="#ketqua" class="tab-btn"><span>Kết quả làm bài</span></li>
                                @endif
                                <li data-tab="#canhan" class="tab-btn"><span>Cá Nhân</span></li>
                            </ul>
                            <div class="tabs-content">
                                <!--tab-->
                                <div class="tab active-tab" id="khoahoc">
                                    <div class="become-a-teacher__content">
                                        <div class="course-grid__right">
                                            <div class="course-grid__right-content-box">
                                                <div class="row">
                                                    <!--Courses Two Single Start-->
                                                    @if ($profile->type == 1 || $profile->type == 3)
                                                        @foreach ($product as $item)
                                                            <div class="col-xl-4">
                                                                <div class="courses-two__single">
                                                                    <div class="courses-two__img-box">
                                                                        <div class="courses-two__img">
                                                                            <a
                                                                                href="{{ route('editCouse', ['id' => $item->id]) }}">
                                                                                <img src="{{ $item->images }}" alt="">
                                                                            </a>

                                                                        </div>

                                                                    </div>
                                                                    <div class="courses-two__content">
                                                                        <div class="courses-two__doller-and-review">
                                                                            <div class="courses-two__doller">
                                                                                @if ($item->price == 0)
                                                                                    <p>Miễn phí</p>
                                                                                @else
                                                                                    <p>{{ number_format($item->price) }}đ
                                                                                        <del>{{ number_format($item->discount) }}đ</del>
                                                                                    </p>
                                                                                @endif
                                                                            </div>
                                                                            <div class="courses-two__review">
                                                                                <p
                                                                                    class="{{ $item->status == 0 ? 'pending' : 'success' }}">
                                                                                    {{ $item->status == 0 ? 'Đang xét duyệt' : 'Đang hoạt động' }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <h3 class="courses-two__title"><a
                                                                                href="{{ route('editCouse', ['id' => $item->id]) }}">{{ $item->name }}</a>
                                                                        </h3>
                                                                        <ul class="courses-two__meta list-unstyled">
                                                                            <li>
                                                                                <div class="icon">
                                                                                    <span class="icon-book"></span>
                                                                                </div>
                                                                                <p>{{ $item->ingredient }} Bài học</p>
                                                                            </li>
                                                                            <li>
                                                                                <div class="icon">
                                                                                    <span class="icon-clock"></span>
                                                                                </div>
                                                                                <p>{{ $item->thickness }} Buổi học</p>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else 
                                                           @foreach ($courser as $item)
                                                                <div class="col-xl-4">
                                                                <div class="courses-two__single">
                                                                    <div class="courses-two__img-box">
                                                                        <div class="courses-two__img">
                                                                            <a
                                                                                href="{{ route('startStudyCourse',['id' => $item->product->id]) }}">
                                                                                <img src="{{  $item->product->images }}" alt="">
                                                                            </a>

                                                                        </div>

                                                                    </div>
                                                                    <div class="courses-two__content">
                                                                        <div class="courses-two__doller-and-review">
                                                                            <div class="courses-two__doller">
                                                                                @if ( $item->product->price == 0)
                                                                                    <p>Miễn phí</p>
                                                                                @else
                                                                                    <p>{{ number_format( $item->product->price) }}đ
                                                                                        <del>{{ number_format( $item->product->discount) }}đ</del>
                                                                                    </p>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <h3 class="courses-two__title"><a
                                                                                href="{{ route('startStudyCourse',['id' => $item->product->id]) }}">{{  $item->product->name }}</a>
                                                                        </h3>
                                                                        <ul class="courses-two__meta list-unstyled">
                                                                            <li>
                                                                                <div class="icon">
                                                                                    <span class="icon-book"></span>
                                                                                </div>
                                                                                <p>{{  $item->product->ingredient }} Bài học</p>
                                                                            </li>
                                                                            <li>
                                                                                <div class="icon">
                                                                                    <span class="icon-clock"></span>
                                                                                </div>
                                                                                <p>{{  $item->product->thickness }} Buổi học</p>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                           @endforeach 
                                                    @endif
                                                   

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--tab-->
                                @if ($profile->type == 1 || $profile->type == 3 )
                                <!--tab-->
                                <div class="tab" id="tailieu">
                                    <div class="become-a-teacher__content">
                                       <div class="become-a-teacher__content">
                                        <div class="course-details__doller-and-btn-box">
                                            <h3 class="course-details__doller">Danh sách tài liệu của bạn</h3>
                                            <div class="course-details__doller-btn-box">
                                                <a href="{{ route('taoTaiLieu') }}" class="thm-btn-two">
                                                    <span>Tạo tài liệu</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="course-grid__right">
                                            <div class="course-grid__right-content-box">
                                                <div class="table-responsive">
                                                    <table class="table cart-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Tên tài liệu</th>
                                                                <th>Định dạng</th>

                                                                <th>Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($document as $item)
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-box">
                                                                            <h3><a
                                                                                    href="{{ route('editTailieu', ['id' => $item->id]) }}">{{ $item->name }}</a>
                                                                            </h3>
                                                                        </div>
                                                                    </td>
                                                                    <td>{{$item->type == 'test' ? 'Bài Test' : 'Tự Luận'}}</td>
                                                                    <td class="d-flex">
                                                                        <div class="cross-icon">
                                                                            <a
                                                                                href="{{ route('editTailieu', ['id' => $item->id]) }}">
                                                                                <i style="color: green"
                                                                                    class="fas fa-pen"></i></a>
                                                                        </div>
                                                                        <div class="cross-icon">
                                                                            <i style="color: #970a17"
                                                                                class="fas fa-trash"></i>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                 <!--tab-->
                                <div class="tab" id="baithi">
                                    <div class="become-a-teacher__content">
                                        <div class="course-details__doller-and-btn-box">
                                            <h3 class="course-details__doller">Danh sách bài test online</h3>
                                            <div class="course-details__doller-btn-box">
                                                <a href="{{ route('postTestOnline') }}" class="thm-btn-two">
                                                    <span>Tạo test</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="course-grid__right">
                                            <div class="course-grid__right-content-box">
                                                <div class="table-responsive">
                                                    <table class="table cart-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Tên bài test</th>
                                                                <th>Số lượng câu hỏi</th>

                                                                <th>Remove</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($listtest as $item)
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-box">
                                                                            <h3><a
                                                                                    href="{{ route('editTestOnline', ['id' => $item->id]) }}">{{ $item->name }}</a>
                                                                            </h3>
                                                                        </div>
                                                                    </td>
                                                                    <td>10</td>


                                                                    <td class="d-flex">
                                                                        <div class="cross-icon">
                                                                            <a
                                                                                href="{{ route('editTestOnline', ['id' => $item->id]) }}">
                                                                                <i style="color: green"
                                                                                    class="fas fa-pen"></i></a>
                                                                        </div>
                                                                        <div class="cross-icon">
                                                                            <i style="color: #970a17"
                                                                                class="fas fa-trash"></i>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--tab-->
                                <div class="tab" id="chamdiem">
                                    <div class="become-a-teacher__content">
                                        <div class="course-details__doller-and-btn-box">
                                            <h3 class="course-details__doller">Danh sách bài tập cần chấm</h3>
                                        </div>
                                        <div class="course-grid__right">
                                            <div class="course-grid__right-content-box">
                                                @if (count($baitap) > 0)
                                                <div class="table-responsive">
                                                    <table class="table cart-table table-striped ">
                                                        <thead>
                                                            <tr>
                                                                <th>Học sinh</th>
                                                                <th>Loại</th>
                                                                <th>Tên tài liệu hoặc khóa học</th>
                                                                <th>Bài tập</th>
                                                                <th>Trạng thái</th>
                                                                <th>Ngày làm</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if (count($baitap) > 0)
                                                            @foreach ($baitap as $item)
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-box">
                                                                            <h3><a
                                                                                    href="{{ route('teacherChamdiem', ['id' => $item->id]) }}">{{$item->student->name}}</a>
                                                                            </h3>
                                                                        </div>
                                                                    </td>
                                                                     @if ($item->type == 'khoahoc')
                                                                        <td>Khóa học</td>
                                                                     @else 
                                                                        <td>Tài Liệu</td>
                                                                     @endif
                                                                    @if ($item->type == 'khoahoc')
                                                                        <td>
                                                                            <div class="product-box">
                                                                                <h3><a
                                                                                        href="{{ route('teacherChamdiem', ['id' => $item->id]) }}">{{$item->course->name}}</a>
                                                                                </h3>
                                                                            </div>
                                                                        </td>
                                                                    @else 
                                                                        <td>
                                                                            <div class="product-box">
                                                                                <h3><a
                                                                                        href="{{ route('teacherChamdiem', ['id' => $item->id]) }}">{{$item->tailieu->name}}</a>
                                                                                </h3>
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                    @if ($item->type == 'khoahoc')
                                                                    <td>{{$item->test->name}}</td>
                                                                    @else
                                                                    <td><i>Nếu tài liệu sẽ không có</i></td>
                                                                    @endif
                                                                    <td>
                                                                        @if ($item->status == 0)
                                                                            <button class="btn need_active">Cần chấm</button>
                                                                        @else 
                                                                            <button class="btn no_active">Đã chấm</button>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{date_format($item->created_at,'d/m/Y')}}</td>
                                                                </tr>
                                                            @endforeach
                                                            @else 
                                                            <h4>Bạn chưa có bài tập nào của học sinh</h4>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @else 
                                                <h4>Bạn chưa có bài tập nào của học sinh</h4>
                                                @endif
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if ($profile->type == 1 || $profile->type == 3 )
                                <div class="tab" id="ketqua">
                                    <div class="become-a-teacher__content">
                                        <div class="course-details__doller-and-btn-box">
                                            <h3 class="course-details__doller">Kết quả làm bài</h3>
                                        </div>
                                        <div class="course-grid__right">
                                            <div class="course-grid__right-content-box">
                                                @if (count($ketqualambaicuahocsinh) > 0)
                                                <div class="table-responsive">
                                                    <table class="table cart-table table-striped ">
                                                        <thead>
                                                            <tr>
                                                                <th>Loại</th>
                                                                <th>Tên tài liệu hoặc khóa học</th>
                                                                <th>Bài tập</th>
                                                                <th>Trạng thái</th>
                                                                <th>Ngày làm</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($ketqualambaicuahocsinh as $item)
                                                                <tr>
                                                                     @if ($item->type == 'khoahoc')
                                                                        <td>Khóa học</td>
                                                                     @else 
                                                                        <td>Tài Liệu</td>
                                                                     @endif
                                                                    @if ($item->type == 'khoahoc')
                                                                        <td>
                                                                            <div class="product-box">
                                                                                <h3><a
                                                                                        href="">{{$item->course->name}}</a>
                                                                                </h3>
                                                                            </div>
                                                                        </td>
                                                                    @else 
                                                                        <td>
                                                                            <div class="product-box">
                                                                                <h3><a
                                                                                        href="{{route('ketquatailieu',['id'=>$item->id])}}">{{$item->tailieu->name}}</a>
                                                                                </h3>
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                    @if ($item->type == 'khoahoc')
                                                                    <td>{{$item->test->name}}</td>
                                                                    @else
                                                                    <td><i>Nếu tài liệu sẽ không có</i></td>
                                                                    @endif
                                                                    <td>
                                                                        @if ($item->status == 0)
                                                                            <button class="btn need_active">Cần chấm</button>
                                                                        @else 
                                                                            <button class="btn no_active">Đã chấm</button>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{date_format($item->created_at,'d/m/Y')}}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @else 
                                                <h4>Bạn chưa có đánh giá nào từ giáo viên</h4>
                                                @endif
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                               
                                <div class="tab" id="canhan">
                                    <div class="become-a-teacher__content">
                                        <div class="instructor-profile-left">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <img src="{{ $profile->avatar ? url('uploads/images/' . $profile->avatar) : url('frontend/images/user_icon.png') }}"
                                                        alt="none-avatar">
                                                </div>
                                                <div class="content">
                                                    <h5 class="title">{{ $profile->name }}</h5>
                                                    <span
                                                        class="subtitle">{{ $profile->type == 1 ? 'Giáo Viên' : 'Học Sinh' }}</span>
                                                    <div class="contact-with-info">
                                                        <p><span class="icon-contact"></span> <a
                                                                href="#">{{ $profile->email }}</a></p>
                                                        <p><span class="icon-phone"></span> <a
                                                                href="">{{ $profile->phone }}</a></p>
                                                    </div>
                                                    <a href="{{ route('showProfile') }}" class="thm-btn-two">
                                                        <span>Chỉnh sửa thông tin</span>
                                                        <i class="icon-angles-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--tab-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </section>
@endsection
