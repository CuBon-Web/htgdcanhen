@extends('layouts.main.master')
@section('title')
    Tài tài liệu
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
            margin-bottom: 10px;
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

        .sub_name {
            font-size: 13px;
            color: forestgreen;
        }
    </style>
@endsection
@section('js')
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/child-component.js') }}"></script>

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
                message: 'Vui lòng nhập tên tài liệu'
            },
            {
                selector: 'select[name="category"]',
                tabId: '#become-an-intructor',
                message: 'Chọn danh mục cho tài liệu'
            }];
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
                    <h2 class="section-title-two__title title-animation">Khởi tạo tài liệu của bạn
                    </h2>
                </div>
            </div>

            <form action="{{ route('posttaoTaiLieu') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="number" value="0" name="id_test" hidden>
                <div class="become-a-teacher__tab-box tabs-box">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6">
                            <h4 class="contact-three__input-title">Tên tài liệu*</h4>
                            <div class="contact-three__input-box">
                                <input type="text" name="name" placeholder="Nhập tên tài liệu...">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6">
                            <h4 class="contact-three__input-title">Danh mục tài liệu</h4>
                            <div class="contact-three__input-box">
                                <select name="category" id="">
                                    <option value="">--Chọn--</option>
                                    @foreach ($cate as $item)
                                            <option value="{{$item->id}}">{{($item->name)}}</option>
                                    @endforeach
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6">
                            <h4 class="contact-three__input-title">Ảnh đại diện</h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="thumb">
                                        <img id="img"
                                            src="{{ $profile->avatar ? url('uploads/images/' . $profile->avatar) : 'https://s.udemycdn.com/course/750x422/placeholder.jpg' }}" />
                                            
                                    </div>
                                    <label for="upload">Click Upload Ảnh
                                        <input type='file' id="upload" name="image">
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6">
                            <h4 class="contact-three__input-title">Mô tả ngắn về tài liệu *</h4>
                            <div class="contact-three__input-box">
                                 <textarea name="description" placeholder="Nhập nội dung..." class="ckeditor"></textarea>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12">
                            <input type="hidden" name="tasks_json" id="tasks_json" />
                            <div id="tai-lieu">
                                <tai-lieu></tai-lieu>
                            </div>
                        </div>
                    </div>
                    <div class="contact-three__btn-box">
                        <button type="submit" class="thm-btn-two contact-three__btn"><span>Tạo Test</span></button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
