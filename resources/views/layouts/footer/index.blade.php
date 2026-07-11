<style>
    .newsletter-two.newsletter-two--modern {
        padding: 0 0 90px;
    }

    .newsletter-two--modern .newsletter-two__inner {
        background: #f5ddaa;
        border-radius: 16px;
        border: 1px solid #f0d294;
        display: flex;
        align-items: stretch;
        overflow: hidden;
    }

    .newsletter-two--modern .newsletter-two__left {
        width: 48%;
        padding: 14px 16px 0px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.22) 0%, rgba(255, 255, 255, 0) 80%);
    }

    .newsletter-two--modern .newsletter-two__left img {
        width: 40%;
        max-width: 170px;
        object-fit: contain;
    }

    .newsletter-two--modern .newsletter-two__left-content {
        flex: 1;
        min-width: 0;
    }

    .newsletter-two--modern .newsletter-two__right {
        width: 52%;
        padding: 18px 20px 14px 0;
    }

    .newsletter-two--modern .newsletter-two__title-mini {
        margin: 0 0 2px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: #f38a00;
    }

    .newsletter-two--modern .newsletter-two__title {
        margin: 0 0 10px;
        font-size: 24px;
        line-height: 1.28;
        font-weight: 800;
        color: #232323;
    }

    .newsletter-two--modern .newsletter-two__points {
        margin: 0 0 12px;
        padding: 0;
        list-style: none;
    }

    .newsletter-two--modern .newsletter-two__points li {
        margin-bottom: 5px;
        color: #5b4f3b;
        font-size: 14px;
        line-height: 1.35;
    }

    .newsletter-two--modern .newsletter-two__points li i {
        color: #e2a33f;
        margin-right: 7px;
    }

    .newsletter-two--modern .newsletter-two__form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 29px;
        margin-bottom: 15px;
    }

    .newsletter-two--modern .newsletter-two__field {
        width: 100%;
        height: 46px;
        border-radius: 10px;
        border: 1px solid #eadfcd;
        padding: 0 14px;
        background: #fff;
        color: #5f6470;
        font-size: 14px;
        outline: none;
    }

    .newsletter-two--modern .newsletter-two__field:focus {
        border-color: #f3a53c;
    }

    .newsletter-two--modern .newsletter-two__submit {
        width: 100%;
        border: none;
        height: 42px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        background: #f57f12;
        margin-bottom: 7px;
        transition: 0.2s ease-in-out;
    }

    .newsletter-two--modern .newsletter-two__submit:hover {
        background: #e67108;
    }

    .newsletter-two--modern .newsletter-two__privacy {
        margin: 0;
        text-align: center;
        color: #7f6e56;
        font-size: 12px;
    }

    .newsletter-two--modern .newsletter-two__privacy i {
        margin-right: 5px;
    }

    #getdatacus label.error {
        display: block;
        color: #b92828;
        font-size: 12px;
        margin: 4px 2px 0;
    }

    .spin-icon {
        display: none;
    }

    @media  only screen and (max-width: 1199px) {
        .newsletter-two--modern .newsletter-two__left {
            width: 45%;
        }

        .newsletter-two--modern .newsletter-two__right {
            width: 55%;
            padding-right: 16px;
        }
    }

    @media  only screen and (max-width: 991px) {
        .newsletter-two--modern .newsletter-two__inner {
            flex-direction: column;
        }

        .newsletter-two--modern .newsletter-two__left,
        .newsletter-two--modern .newsletter-two__right {
            width: 100%;
        }

        .newsletter-two--modern .newsletter-two__left {
            justify-content: flex-start;
            padding: 14px 16px 4px;
        }

        .newsletter-two--modern .newsletter-two__left img {
            width: 100%;
            max-width: 140px;
        }

        .newsletter-two--modern .newsletter-two__right {
            padding: 8px 16px 16px;
        }
    }

    @media  only screen and (max-width: 767px) {
        .newsletter-two.newsletter-two--modern {
            padding-bottom: 60px;
        }

        .newsletter-two--modern .newsletter-two__title {
            font-size: 25px;
        }

        .newsletter-two--modern .newsletter-two__form-grid {
            grid-template-columns: 1fr;
        }

        .newsletter-two--modern .newsletter-two__left {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .newsletter-two--modern .newsletter-two__left img {
            max-width: 100%;
        }
    }
</style>
<section class="newsletter-two newsletter-two--modern" id="section-contact-sales">
    <div class="container">
        <div class="newsletter-two__inner">
            <div class="newsletter-two__left">
                <img src="/frontend/images/footer.png" alt="Tư vấn miễn phí Cánh Én">
                <div class="newsletter-two__left-content">
                    <p class="newsletter-two__title-mini">Đặt lịch tư vấn miễn phí</p>
                    <h3 class="newsletter-two__title">Để Cánh Én đồng hành cùng con trên hành trình tri thức</h3>
                    <ul class="newsletter-two__points">
                        <li><i class="fas fa-check"></i>Tư vấn lộ trình học tập phù hợp</li>
                        <li><i class="fas fa-check"></i>Kiểm tra năng lực miễn phí</li>
                        <li><i class="fas fa-check"></i>Giải đáp mọi thắc mắc của phụ huynh</li>
                    </ul>
                </div>
            </div>
            <div class="newsletter-two__right">
                <form id="getdatacus">
                    <div class="newsletter-two__form-grid">
                        <div>
                            <input class="newsletter-two__field" type="text" name="name" placeholder="Họ và tên">
                        </div>
                        <div>
                            <input class="newsletter-two__field" type="text" name="phone" placeholder="Số điện thoại">
                        </div>
                        <div>
                            <select class="newsletter-two__field" name="class_name">
                                <option value="">Lớp của con</option>
                                <option value="Lớp 6">Lớp 6</option>
                                <option value="Lớp 7">Lớp 7</option>
                                <option value="Lớp 8">Lớp 8</option>
                                <option value="Lớp 9">Lớp 9</option>
                                <option value="Lớp 10">Lớp 10</option>
                                <option value="Lớp 11">Lớp 11</option>
                                <option value="Lớp 12">Lớp 12</option>
                            </select>
                        </div>
                        <div>
                            <select class="newsletter-two__field" name="area">
                                <option value="">Khu vực</option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="TP.HCM">TP.HCM</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="newsletter-two__submit">
                        <span class="loaders ml-15 spin-icon"></span>
                        Đăng ký tư vấn ngay
                    </button>
                    <p class="newsletter-two__privacy">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        Thông tin của bạn được bảo mật tuyệt đối
                    </p>
                </form>
                <script>
                    $('#getdatacus').validate({
                        rules: {
                            name: {
                                required: true,
                            },
                            phone: {
                                required: true,
                                minlength: 10,
                                digits: true,
                            }
                        },
                        messages: {
                            name: {
                                required: "Tên bạn là gì?",
                            },
                            phone: {
                                required: "Nhập sdt liên hệ",
                                digits: "Nhập đúng định dạng số điện thoại",
                                minlength: "Nhập tối thiểu 10 số"
                            }
                        },
                        submitHandler: function(form) {
                            $(".spin-icon").css("display", "inline-block");
                            $.ajax({
                                url: "https://script.google.com/macros/s/AKfycbyzVnC9pnnBRgBxGkLCpFVIT4bf73Gp__7kNONNhXGFOJidpO0MlkhmZPtTLcPpd8OJMA/exec",
                                type: "post",
                                data: $("#getdatacus").serializeArray(),
                                success: function() {
                                    $(".spin-icon").css("display", "none");
                                    alert("Thành công! Chúng tôi sẽ sớm liên hệ", "success");
                                    form.reset();
                                },
                                error: function() {
                                    $(".spin-icon").css("display", "none");
                                    alert("Gửi thông tin thất bại", "error");
                                }
                            });
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</section>
<!--Newsletter Two End -->

<!-- Consult Register Modal -->
<style>
    .consult-modal .modal-dialog {
        max-width: 520px;
        margin: 1.25rem auto;
    }

    .consult-modal .modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 48px rgba(61, 38, 32, 0.18);
    }

    .consult-modal .modal-header {
        border: none;
        background: #fef9f2;
        padding: 18px 20px 8px;
        align-items: flex-start;
    }

    .consult-modal .modal-title {
        font-size: 20px;
        font-weight: 700;
        color: #3d2620;
        line-height: 1.35;
    }

    .consult-modal .modal-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: #7a7a7a;
        font-weight: 400;
    }

    .consult-modal .btn-close,
    .consult-modal .close {
        background: #fff;
        border: 1px solid #f1dfca;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        opacity: 1;
        font-size: 18px;
        line-height: 28px;
        padding: 0;
        margin: 0;
    }

    .consult-modal .modal-body {
        padding: 12px 20px 20px;
        background: #fff;
    }

    .consult-modal .consult-modal__points {
        list-style: none;
        margin: 0 0 14px;
        padding: 0;
    }

    .consult-modal .consult-modal__points li {
        margin-bottom: 4px;
        font-size: 13px;
        color: #5b4f3b;
    }

    .consult-modal .consult-modal__points li i {
        color: var(--fistudy-primary, #f46f01);
        margin-right: 6px;
    }

    .consult-modal .newsletter-two__form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 8px;
    }

    .consult-modal .newsletter-two__field {
        width: 100%;
        height: 46px;
        border-radius: 10px;
        border: 1px solid #eadfcd;
        padding: 0 14px;
        background: #fff;
        color: #5f6470;
        font-size: 14px;
        outline: none;
    }

    .consult-modal .newsletter-two__field:focus {
        border-color: #f3a53c;
    }

    .consult-modal .newsletter-two__submit {
        width: 100%;
        border: none;
        height: 44px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        background: #f57f12;
        margin-bottom: 8px;
        transition: background 0.2s ease;
    }

    .consult-modal .newsletter-two__submit:hover {
        background: #e67108;
    }

    .consult-modal .newsletter-two__privacy {
        margin: 0;
        text-align: center;
        color: #7f6e56;
        font-size: 12px;
    }

    .consult-modal .newsletter-two__privacy i {
        margin-right: 5px;
    }

    #getdatacus-popup label.error {
        display: block;
        color: #b92828;
        font-size: 12px;
        margin: 4px 2px 0;
    }

    .consult-modal .spin-icon {
        display: none;
    }

    @media only screen and (max-width: 575px) {
        .consult-modal .newsletter-two__form-grid {
            grid-template-columns: 1fr;
        }

        .consult-modal .modal-title {
            font-size: 18px;
        }
    }
</style>

<div class="modal fade consult-modal" id="consultRegisterModal" tabindex="-1" aria-labelledby="consultRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="consultRegisterModalLabel">Đặt lịch tư vấn miễn phí</h5>
                    <p class="modal-subtitle">Để Cánh Én đồng hành cùng con trên hành trình tri thức</p>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="consult-modal__points">
                    <li><i class="fas fa-check"></i>Tư vấn lộ trình học tập phù hợp</li>
                    <li><i class="fas fa-check"></i>Kiểm tra năng lực miễn phí</li>
                    <li><i class="fas fa-check"></i>Giải đáp mọi thắc mắc của phụ huynh</li>
                </ul>
                <form id="getdatacus-popup">
                    <div class="newsletter-two__form-grid">
                        <div>
                            <input class="newsletter-two__field" type="text" name="name" placeholder="Họ và tên">
                        </div>
                        <div>
                            <input class="newsletter-two__field" type="text" name="phone" placeholder="Số điện thoại">
                        </div>
                        <div>
                            <select class="newsletter-two__field" name="class_name">
                                <option value="">Lớp của con</option>
                                <option value="Lớp 6">Lớp 6</option>
                                <option value="Lớp 7">Lớp 7</option>
                                <option value="Lớp 8">Lớp 8</option>
                                <option value="Lớp 9">Lớp 9</option>
                                <option value="Lớp 10">Lớp 10</option>
                                <option value="Lớp 11">Lớp 11</option>
                                <option value="Lớp 12">Lớp 12</option>
                            </select>
                        </div>
                        <div>
                            <select class="newsletter-two__field" name="area">
                                <option value="">Khu vực</option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="TP.HCM">TP.HCM</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="newsletter-two__submit">
                        <span class="loaders ml-15 spin-icon"></span>
                        Đăng ký tư vấn ngay
                    </button>
                    <p class="newsletter-two__privacy">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        Thông tin của bạn được bảo mật tuyệt đối
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    (function ($) {
        if (!$ || !$.fn.validate) {
            return;
        }

        $('#getdatacus-popup').validate({
            rules: {
                name: { required: true },
                phone: { required: true, minlength: 10, digits: true }
            },
            messages: {
                name: { required: "Tên bạn là gì?" },
                phone: {
                    required: "Nhập sdt liên hệ",
                    digits: "Nhập đúng định dạng số điện thoại",
                    minlength: "Nhập tối thiểu 10 số"
                }
            },
            submitHandler: function (form) {
                var $form = $(form);
                var $spin = $form.find('.spin-icon');
                $spin.css('display', 'inline-block');

                $.ajax({
                    url: "https://script.google.com/macros/s/AKfycbyzVnC9pnnBRgBxGkLCpFVIT4bf73Gp__7kNONNhXGFOJidpO0MlkhmZPtTLcPpd8OJMA/exec",
                    type: "post",
                    data: $form.serializeArray(),
                    success: function () {
                        $spin.css('display', 'none');
                        alert("Thành công! Chúng tôi sẽ sớm liên hệ", "success");
                        form.reset();
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var modalEl = document.getElementById('consultRegisterModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) {
                                modal.hide();
                            }
                        } else if ($.fn.modal) {
                            $('#consultRegisterModal').modal('hide');
                        }
                    },
                    error: function () {
                        $spin.css('display', 'none');
                        alert("Gửi thông tin thất bại", "error");
                    }
                });
            }
        });
    })(window.jQuery);
</script>

<style>
    .site-footer-two.site-footer-two--modern {
        position: relative;
        background: #fef9f2;
        margin-top: 0;
        padding: 0;
        overflow: hidden;
    }

    .site-footer-two--modern .site-footer-two__shape-1,
    .site-footer-two--modern .site-footer-two__shape-2,
    .site-footer-two--modern .site-footer-two__star {
        display: none !important;
    }

    .site-footer-two--modern .site-footer-two__top,
    .site-footer-two--modern .site-footer-two__main-content,
    .site-footer-two--modern .site-footer-two__main-content-inner {
        background: transparent;
        padding: 0;
        margin: 0;
        border: none;
        box-shadow: none;
    }

    .site-footer-two--modern .site-footer-two__main-content {
        padding: 56px 0 40px;
    }

    .site-footer-two--modern .footer-modern__grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
        gap: 36px 28px;
    }

    .site-footer-two--modern .footer-modern__logo {
        display: inline-block;
        margin-bottom: 14px;
        line-height: 0;
    }

    .site-footer-two--modern .footer-modern__logo img {
        max-width: 220px;
        height: auto;
    }

    .site-footer-two--modern .footer-modern__desc {
        margin: 0 0 18px;
        max-width: 320px;
        font-size: 14px;
        line-height: 1.65;
        color: #5f5a54;
    }

    .site-footer-two--modern .footer-modern__social {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .site-footer-two--modern .footer-modern__social a {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--fistudy-primary, #f46f01);
        color: #fff;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .site-footer-two--modern .footer-modern__social a:hover {
        background: #d96200;
        color: #fff;
        transform: translateY(-2px);
    }

    .site-footer-two--modern .footer-modern__title {
        margin: 0 0 16px;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.3;
        color: #8a3b12;
    }

    .site-footer-two--modern .footer-modern__links {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .site-footer-two--modern .footer-modern__links li + li {
        margin-top: 10px;
    }

    .site-footer-two--modern .footer-modern__links a {
        color: #4f4a45;
        font-size: 14px;
        line-height: 1.4;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .site-footer-two--modern .footer-modern__links a:hover {
        color: var(--fistudy-primary, #f46f01);
    }

    .site-footer-two--modern .footer-modern__contact {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .site-footer-two--modern .footer-modern__contact li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: #4f4a45;
        font-size: 14px;
        line-height: 1.5;
    }

    .site-footer-two--modern .footer-modern__contact li + li {
        margin-top: 12px;
    }

    .site-footer-two--modern .footer-modern__contact i {
        flex-shrink: 0;
        width: 18px;
        margin-top: 2px;
        color: var(--fistudy-primary, #f46f01);
        font-size: 14px;
        text-align: center;
    }

    .site-footer-two--modern .footer-modern__contact a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .site-footer-two--modern .footer-modern__contact a:hover {
        color: var(--fistudy-primary, #f46f01);
    }

    .site-footer-two--modern .site-footer__bottom {
        background: var(--fistudy-primary, #f46f01);
        padding: 14px 0;
        border: none;
    }

    .site-footer-two--modern .site-footer__bottom-inner {
        display: block;
        text-align: center;
        border: none;
        padding: 0;
        margin: 0;
    }

    .site-footer-two--modern .site-footer__copyright,
    .site-footer-two--modern .site-footer__copyright-text {
        margin: 0;
        color: #fff;
        font-size: 13px;
        line-height: 1.5;
        text-align: center;
    }

    .site-footer-two--modern .site-footer__copyright-text a {
        color: #fff;
        font-weight: 600;
        text-decoration: none;
    }

    @media only screen and (max-width: 1199px) {
        .site-footer-two--modern .footer-modern__grid {
            grid-template-columns: 1.3fr 1fr 1fr;
            gap: 28px 20px;
        }

        .site-footer-two--modern .footer-modern__col--contact {
            grid-column: 1 / -1;
        }
    }

    @media only screen and (max-width: 991px) {
        .site-footer-two--modern .site-footer-two__main-content {
            padding: 44px 0 32px;
        }

        .site-footer-two--modern .footer-modern__grid {
            grid-template-columns: 1fr 1fr;
        }

        .site-footer-two--modern .footer-modern__col--brand,
        .site-footer-two--modern .footer-modern__col--contact {
            grid-column: 1 / -1;
        }
    }

    @media only screen and (max-width: 575px) {
        .site-footer-two--modern .footer-modern__grid {
            grid-template-columns: 1fr;
            gap: 28px;
        }

        .site-footer-two--modern .footer-modern__col--brand,
        .site-footer-two--modern .footer-modern__col--contact {
            grid-column: auto;
        }

        .site-footer-two--modern .footer-modern__desc {
            max-width: none;
        }
    }
</style>
<footer class="site-footer-two site-footer-two--modern">
    <div class="site-footer-two__top">
        <div class="site-footer-two__main-content">
            <div class="container">
                <div class="footer-modern__grid">
                    <div class="footer-modern__col footer-modern__col--brand">
                        <a href="{{ route('home') }}" class="footer-modern__logo">
                            <img src="{{ $setting->logo }}" alt="{{ $setting->company ?? 'Cánh Én' }}">
                        </a>
                        <p class="footer-modern__desc">
                            {{ $setting->webname ?: 'Cánh Én là hệ thống giáo dục uy tín, đồng hành cùng học sinh phát triển toàn diện về tri thức, kỹ năng và nhân cách.' }}
                        </p>
                        <div class="footer-modern__social">
                            @if (!empty($setting->facebook))
                                <a href="{{ $setting->facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if (!empty($setting->youtube))
                                <a href="{{ $setting->youtube }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                            @if (!empty($setting->email))
                                <a href="mailto:{{ $setting->email }}" aria-label="Email">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            @endif
                            @if (empty($setting->facebook) && empty($setting->youtube) && empty($setting->email))
                                <a href="{{ route('lienHe') }}" aria-label="Liên hệ">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="footer-modern__col">
                        <h4 class="footer-modern__title">Liên kết nhanh</h4>
                        <ul class="footer-modern__links">
                            <li><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li><a href="{{ route('aboutUs') }}">Giới thiệu</a></li>
                            <li><a href="{{ route('couseList') }}">Khóa học</a></li>
                            <li><a href="{{ route('listTeacher') }}">Đội ngũ giáo viên</a></li>
                            <li>
                                <a href="{{ isset($blogCate[0]) ? route('listCateBlog', ['slug' => $blogCate[0]->slug]) : route('home') }}">
                                    Tin tức
                                </a>
                            </li>
                            <li><a href="{{ route('lienHe') }}">Liên hệ</a></li>
                        </ul>
                    </div>

                    <div class="footer-modern__col">
                        <h4 class="footer-modern__title">Chính sách</h4>
                        <ul class="footer-modern__links">
                            @php
                                $policyPages = collect($pageContent)->where('type', 'ho-tro-khanh-hang');
                            @endphp
                            @forelse ($policyPages as $item)
                                <li>
                                    <a href="{{ route('pagecontent', ['slug' => $item->slug]) }}">{{ $item->title }}</a>
                                </li>
                            @empty
                                <li><a href="{{ route('lienHe') }}">Chính sách bảo mật</a></li>
                                <li><a href="{{ route('lienHe') }}">Điều khoản sử dụng</a></li>
                                <li><a href="{{ route('lienHe') }}">Chính sách hoàn học phí</a></li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="footer-modern__col footer-modern__col--contact">
                        <h4 class="footer-modern__title">Liên hệ</h4>
                        <ul class="footer-modern__contact">
                            @if (!empty($setting->phone1))
                                <li>
                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone1) }}">{{ $setting->phone1 }}</a>
                                </li>
                            @endif
                            @if (!empty($setting->email))
                                <li>
                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                    <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a>
                                </li>
                            @endif
                            @if (!empty($setting->address1))
                                <li>
                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                    <span>{{ $setting->address1 }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            <div class="site-footer__bottom-inner">
                <div class="site-footer__copyright">
                    <p class="site-footer__copyright-text">
                        © {{ date('Y') }} {{ $setting->company ?? 'Hệ thống giáo dục Cánh Én' }}. All rights reserved
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
