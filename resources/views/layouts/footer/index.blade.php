<!--Newsletter Two Start -->
<section class="newsletter-two" id="section-contact-sales">
    <div class="container">
        <div class="newsletter-two__inner">
            <div class="newsletter-two__img">
                <img src="/frontend/images/footer.png" alt="">
            </div>
            <div class="newsletter-two__inner-content">
                <div class="newsletter-two__shape-bg"
                    style="background-image: url('{{ env('AWS_R2_URL') }}/frontend/images/newsletter-two-shape-bg.png');">
                </div>
                <div class="newsletter-two__like">
                    <img src="{{ env('AWS_R2_URL') }}/frontend/images/newsletter-two-like.png" alt="">
                </div>
                <div class="newsletter-two__title-box">
                    <h3 class="newsletter-two__title">ĐẶT LỊCH TƯ VẤN <span>MIỄN PHÍ
                            <p class="newsletter-two__text">Liên hệ ngay với chúng tôi để được tư vấn lộ trình học tập
                                hiệu quả
                            </p>
                </div>
                <div class="newsletter-two__form-box">
                    <form class="newsletter-two__form" id="getdatacus">

                        <div class="newsletter-two__input mb-1">
                            <input type="name" name="name" placeholder="Họ Tên">

                        </div>
                        <div class="newsletter-two__input mb-1">
                            <input type="phone" name="phone" placeholder="Số điện thoại">
                        </div>
                        <button type="submit" class="newsletter-two__btn_a">
                            <span class="loaders ml-15 spin-icon"></span> Gửi
                        </button>
                    </form>
                    <script>
                        $('#getdatacus').validate({
                            rules: {
                                "name": {
                                    required: true,
                                },
                                "phone": {
                                    required: true,
                                    minlength: 10,
                                    digits: true,
                                }
                            },
                            messages: {
                                "name": {
                                    required: "Tên bạn là gì?",
                                },
                                "phone": {
                                    required: "Nhập sdt liên hệ",
                                    digits: "Nhập đúng định dạng số điện thoại",
                                    minlength: "Nhập tối thiểu 10 số"
                                }
                            },
                            submitHandler: function(form) {
                                $(".spin-icon").css("display", "block");
                                $.ajax({
                                    url: "https://script.google.com/macros/s/AKfycbyzVnC9pnnBRgBxGkLCpFVIT4bf73Gp__7kNONNhXGFOJidpO0MlkhmZPtTLcPpd8OJMA/exec",
                                    type: "post",
                                    data: $("#commentform").serializeArray(),
                                    success: function() {
                                        $(".spin-icon").css("display", "none");
                                        alert("Thành công! Chúng tôi sẽ sớm liên hệ", "success");
                                    },
                                    error: function() {
                                        alert("Gửi thông tin thất bại", "error");
                                    }
                                });
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Newsletter Two End -->
<footer class="site-footer-two">
    <div class="site-footer-two__shape-1 img-bounce"></div>
    <div class="site-footer-two__shape-2 float-bob-y"></div>
    <div class="site-footer-two__star text-rotate-box">
        <img src="{{ env('AWS_R2_URL') }}/frontend/images/site-footer-star.png" alt="">
    </div>
    <div class="site-footer-two__top">
        <div class="site-footer-two__main-content">
            <div class="container">
                <div class="site-footer-two__main-content-inner">
                    <div class="row">
                        <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                            <div class="footer-widget-two__about">
                                <div class="footer-widget-two__about-logo">
                                    <a href="{{ route('home') }}">
                                        <img width="200" src="{{ $setting->logo }}" alt="">
                                    </a>
                                </div>
                                <p class="footer-widget-two__about-text">{{ $setting->webname }}
                                </p>
                                <div class="site-footer-two__social">
                                    <a href="#"><span class="fab fa-linkedin-in"></span></a>
                                    <a href="#"><span class="fab fa-pinterest-p"></span></a>
                                    <a href="#"><span class="fab fa-facebook-f"></span></a>
                                    <a href="#"><span class="fab fa-instagram"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                            <div class="footer-widget-two__quick-links">
                                <h4 class="footer-widget-two__title">Chính sách & điều khoản</h4>
                                <ul class="footer-widget-two__quick-links-list list-unstyled">
                                    @foreach ($pageContent as $item)
                                        @if ($item->type == 'ho-tro-khanh-hang')
                                            <li><a href="{{ route('pagecontent', ['slug' => $item->slug]) }}"> <span
                                                        class="icon-plus"></span> {{ $item->title }}</a></li>
                                        @endif
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                            <div class="footer-widget-two__support">
                                <h4 class="footer-widget-two__title">Khóa học</h4>
                                <ul class="footer-widget-two__quick-links-list list-unstyled">
                                    @foreach ($categoryhome as $item)
                                        <li><a href="{{ route('couseListCate', ['cate_slug' => $item->slug]) }}"> <span
                                                    class="icon-plus"></span> {{ languageName($item->name) }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-footer__bottom-inner">
                        <div class="site-footer__copyright">
                            <p class="site-footer__copyright-text">Copyright © 2025 <a href="#">Edu Alpha</a>.
                                All
                                Rights Reserved
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
