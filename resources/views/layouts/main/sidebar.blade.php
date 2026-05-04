<aside class="edu-blog-sidebar">
   <!-- Start Single Widget  -->
   <div class="edu-blog-widget widget-search">
      <div class="inner">
         <h5 class="widget-title" style="color: white;">Đăng Ký Tư Vấn</h5>
         <div class="content">
           <form class="rwt-dynamic-form row sal-animate" id="contact-form" data-sal-delay="150" data-sal="slide-up" data-sal-duration="800">
               <div class="col-lg-12">
                  @php
                      $date = date("d/m/Y");
                  @endphp
                  <input type="text" name="date" id="" hidden value="{{$date}}">
                   <div class="form-group">
                       <input type="text" class="form-control form-control-lg" name="name" placeholder="Họ Tên">
                   </div>
                   <div class="form-group">
                       <input type="text" class="form-control form-control-lg" name="phone" placeholder="Số Điện Thoại">
                   </div>
                   <div class="form-group">
                       <input type="text" class="form-control form-control-lg" name="email" placeholder="Email">
                   </div>
                   <div class="form-group">
                     <select name="course" id="">
                        <option value="0">Chọn khóa học</option>
                        @foreach ($servicehome as $item)
                        <option value="{{$item->name}}">{{$item->name}}</option>
                        @endforeach
                     </select>
                 </div>
               </div>
               <div class="col-lg-12">
                   <button class="rn-btn edu-btn" type="submit">
                     <i class="icon-send-plane-fill icn-spinner spin-icon" aria-hidden="true"></i>
                       <span>Gửi Thông Tin</span><i class="icon-arrow-right-line-right "></i></button>
               </div>
           </form>
           <script>
            $('#contact-form').validate({
                        rules: {
                           "name": {
                              required: true,
                           },
                           "phone": {
                              required: true,
                              minlength: 8
                           }
                        },
                        messages: {
                           "name": {
                              required: "Tên bạn là gì?",
                           },
                           "phone": {
                              required: "Nhập sdt liên hệ",
                           }
                        },
                     submitHandler: function(form) {
                        $(".icn-spinner").css("display", "inline-block");
                        $.ajax({
                         url: "https://script.google.com/macros/s/AKfycbwjwJHUPCKsuLItNRIUPTXr4oZvQ7zhtnqU2km54r0UCombeIJJOMII7JoLCZgEIpy_/exec",
                         type: "post",
                         data: $("#contact-form").serializeArray(),
                         success: function () {
                            $(".spin-icon").css("display", "none");
                           jQuery.notify("Thành công! Chúng tôi sẽ sớm liên hệ", "success");
                         },
                         error: function () {
                           jQuery.notify("Gửi thông tin thất bại", "error");
                         }
                      });
                     }
                     });
         </script>
         </div>
      </div>
   </div>
   <!-- End Single Widget  -->
   <!-- Start Single Widget  -->
</aside>