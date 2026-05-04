@extends('layouts.main.master')
@section('title')
    Tạo bài test
@endsection
@section('description')
@endsection
@section('image')
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .ck-editor__editable {
            min-height: 150px;
            /* thay đổi chiều cao tại đây */
        }
    </style>
    <style>
        .wrap {
            width: 120px;
            margin: 0 auto;
            text-align: center;
            overflow: hidden;
        }

        label[for=upload] {
            width: 100%;
            display: inline-block;
            border: 1px solid #0a0a0a;
            color: #0c0c0c;
            font-weight: bold;
            /* background: #eee; */
            padding: 8px 4px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        label[for=upload]:hover {
            background: #ddd
        }

        label[for=upload] input {
            display: none
        }

        .thumb {
            position: relative;
            height: 230px;
            width: 100%;
            overflow: hidden;
            margin: 10px 0;
            cursor: pointer;
        }

        .thumb:before {
            content: "";
            display: block;
            position: absolute;
            width: 96%;
            height: 96%;
            border: 3px dashed #eee;
            z-index: 9;
            top: 1%;
            left: 1%;
            opacity: 0;
            transition: all 0.2s;
            pointer-events: none
        }

        .thumb:hover::before {
            opacity: 0.6
        }

        .thumb img {
            height: 100%;
            width: 100%;
            transition: all 0.4s;
            object-fit: cover;
            border: 1px solid black;
            border-radius: 10px;
        }

        .task {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
        }

        .detail-task {
            margin-left: 20px;
            padding-top: 10px;
            border-left: 3px solid #eee;
            padding-left: 10px;
            position: relative;
        }

        input {
            margin: 5px 0;
            display: block;
            width: 100%;
            padding: 5px;
        }

        button {
            margin-top: 8px;
            padding: 6px 12px;
            cursor: pointer;
        }

        .add-detail {
            background-color: #dff0d8;
        }

        .add-task {
            background-color: #d9edf7;
        }

        .submit-btn {
            background-color: #f0ad4e;
            color: white;
        }

        .delete-task,
        .delete-detail {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #f2dede;
            color: red;
            border: none;
            padding: 5px 10px;
        }
        .sub_name{
            font-size: 13px;
            color: forestgreen;
        }
    </style>
@endsection
@section('js')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/child-component.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
  
    <script>
        function preview(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    console.log(reader.result)
                    var img = new Image;
                    img.onload = function() {
                        $('#img').attr({
                            'src': e.target.result,
                            'width': img.width
                        });
                    };
                    img.src = reader.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#upload").change(function() {
            $("#img").css({
                top: 0,
                left: 0
            });
            preview(this);
            $("#img").draggable({
                containment: 'parent',
                scroll: false
            });
        });
    </script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();


            const validations = [{
                    selector: 'input[name="name"]',
                    tabId: '#become-an-intructor',
                    message: 'Vui lòng nhập tên bài test'
                }
            ];
            for (const v of validations) {
                const input = this.querySelector(v.selector);
                if (!input) continue;

                let value = input.value.trim();

                if ((input.tagName.toLowerCase() === 'select' && value === '') || (input.type === 'hidden' &&
                        value === '') || value === '') {

                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: v.message,
                        confirmButtonText: 'Đồng ý'
                    }).then(() => {
                        input.focus();
                    });

                    return false;
                }
            }
            this.submit();
        });
    </script>
@endsection

@section('content')
    <section class="become-a-teacher">
        <div class="container">
            <div class="become-a-teacher__top">
                <div class="section-title-two text-center sec-title-animation animation-style1">
                    <h2 class="section-title-two__title title-animation">Khởi tạo bài test của bạn
                    </h2>
                </div>
            </div>

            <form action="{{ route('submitTestOnline') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="number" value="{{$test->id}}" name="id_test" hidden>
                <div class="become-a-teacher__tab-box tabs-box">
                     <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <h4 class="contact-three__input-title">Tên bài test* <i class="sub_name">(Gợi ý: bạn nên đặt tên kèm với tên khóa học để dễ dàng lựa chọn khi đăng khóa học vd:Test bài 1 - Khóa học toán)</i></h4>
                                    <div class="contact-three__input-box">
                                        <input type="text" name="name" value="{{$test->name}}" placeholder="Nhập tên bài test...">
                                    </div>
                                </div>
                                {{-- <div class="col-xl-6 col-lg-6">
                                    <h4 class="contact-three__input-title">Danh mục khóa học</h4>
                                    <div class="contact-three__input-box">
                                        <select name="category" id="">
                                            <option value="">--Chọn--</option>
                                            @foreach ($category as $item)
                                                    <option value="{{$item->id}}">{{languageName($item->name)}}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="col-xl-12 col-lg-12">
                                    <h4 class="contact-three__input-title">Nội dung bài test</h4>
                                     <input type="hidden" name="tasks_json" id="tasks_json" />
                                     @php
                                        $noidungtest = json_decode($test->test_json);
                                    @endphp
                                        <div id="bai-test">
                                            <bai-test :initial-tasks='@json($noidungtest)'></bai-test>
                                        </div>
                                </div>
                            </div>
                            <div class="contact-three__btn-box">
                                <button type="submit" class="thm-btn-two contact-three__btn"><span>Cập nhật</span></button>
                            </div>
                </div>
            </form>
        </div>
    </section>
@endsection
