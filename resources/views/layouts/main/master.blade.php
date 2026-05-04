<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta name="theme-color" content="#d70018">
      <!-- Responsive -->
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      @php
          $seoTitle = trim($__env->yieldContent('title')) ?: ($setting->webname ?? $setting->company ?? config('app.name'));
          $seoDescriptionRaw = trim($__env->yieldContent('description')) ?: ($setting->company ?? $setting->webname ?? config('app.name'));
          $seoDescription = \Illuminate\Support\Str::limit(trim(strip_tags($seoDescriptionRaw)), 160, '');
          $seoImage = trim($__env->yieldContent('image')) ?: url($setting->logo ?? $setting->favicon ?? 'img/logo.png');
          $seoCanonical = request()->url();
          $seoUrl = request()->fullUrl();
          $seoSiteName = $setting->webname ?? $setting->company ?? config('app.name');
          $seoLocale = 'vi_VN';
          $seoType = trim($__env->yieldContent('og_type')) ?: 'website';
          $seoRobots = trim($__env->yieldContent('robots')) ?: 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';
          $seoTwitterCard = trim($__env->yieldContent('twitter_card')) ?: 'summary_large_image';
          $seoKeywords = trim($__env->yieldContent('keywords'));
          $organizationSchema = [
              '@context' => 'https://schema.org',
              '@type' => 'Organization',
              'name' => $seoSiteName,
              'url' => url('/'),
              'logo' => $seoImage,
              'sameAs' => array_values(array_filter([
                  $setting->facebook ?? null,
                  $setting->youtube ?? null,
              ])),
              'contactPoint' => [
                  [
                      '@type' => 'ContactPoint',
                      'telephone' => $setting->phone1 ?? '',
                      'contactType' => 'customer support',
                      'availableLanguage' => ['vi', 'en'],
                  ],
              ],
          ];
          $websiteSchema = [
              '@context' => 'https://schema.org',
              '@type' => 'WebSite',
              'name' => $seoSiteName,
              'url' => url('/'),
              'inLanguage' => 'vi-VN',
              'potentialAction' => [
                  '@type' => 'SearchAction',
                  'target' => url('/search') . '?q={search_term_string}',
                  'query-input' => 'required name=search_term_string',
              ],
          ];
          $webpageSchema = [
              '@context' => 'https://schema.org',
              '@type' => 'WebPage',
              'name' => $seoTitle,
              'description' => $seoDescription,
              'url' => $seoUrl,
              'inLanguage' => 'vi-VN',
              'isPartOf' => [
                  '@type' => 'WebSite',
                  'name' => $seoSiteName,
                  'url' => url('/'),
              ],
              'primaryImageOfPage' => [
                  '@type' => 'ImageObject',
                  'url' => $seoImage,
              ],
          ];
          $segments = request()->segments();
          $itemListElement = [
              [
                  '@type' => 'ListItem',
                  'position' => 1,
                  'name' => 'Trang chủ',
                  'item' => url('/'),
              ],
          ];
          foreach ($segments as $index => $segment) {
              $itemListElement[] = [
                  '@type' => 'ListItem',
                  'position' => $index + 2,
                  'name' => ucwords(str_replace('-', ' ', $segment)),
                  'item' => url(implode('/', array_slice($segments, 0, $index + 1))),
              ];
          }
          $breadcrumbSchema = [
              '@context' => 'https://schema.org',
              '@type' => 'BreadcrumbList',
              'itemListElement' => $itemListElement,
          ];
      @endphp
      <title>{{ $seoTitle }}</title>
      <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
      <meta http-equiv="Content-Language" content="vi" />
      <link rel="alternate" href="{{ $seoCanonical }}" hreflang="vi-vn" />
      <link rel="alternate" href="{{ $seoCanonical }}" hreflang="x-default" />
      <meta name="description" content="{{ $seoDescription }}">
      @if ($seoKeywords !== '')
      <meta name="keywords" content="{{ $seoKeywords }}">
      @endif
      <meta name="robots" content="{{ $seoRobots }}" />
      <meta name="googlebot" content="{{ $seoRobots }}">
      <meta name="revisit-after" content="1 days" />
      <meta name="generator" content="{{ $seoSiteName }}" />
      <meta name="rating" content="General">
      <meta name="application-name" content="{{ $seoSiteName }}" />
      <meta name="theme-color" content="#ed3235" />
      <meta name="msapplication-TileColor" content="#ed3235" />
      <meta name="mobile-web-app-capable" content="yes">
      <meta name="apple-mobile-web-app-title" content="{{ $seoSiteName }}" />
      <link rel="apple-touch-icon-precomposed" href="{{ $seoImage }}" sizes="700x700">
      <meta property="og:url" content="{{ $seoUrl }}">
      <meta property="og:title" content="{{ $seoTitle }}">
      <meta property="og:description" content="{{ $seoDescription }}">
      <meta property="og:image" content="{{ $seoImage }}">
      <meta property="og:site_name" content="{{ $seoSiteName }}">
      <meta property="og:image:alt" content="{{ $seoTitle }}">
      <meta property="og:type" content="{{ $seoType }}" />
      <meta property="og:locale" content="{{ $seoLocale }}" />
      <meta name="twitter:card" content="{{ $seoTwitterCard }}" />
      <meta name="twitter:site" content="{{ parse_url(url('/'), PHP_URL_HOST) }}" />
      <meta name="twitter:title" content="{{ $seoTitle }}" />
      <meta name="twitter:description" content="{{ $seoDescription }}" />
      <meta name="twitter:image" content="{{ $seoImage }}" />
      <meta name="twitter:url" content="{{ $seoUrl }}" />
      <meta itemprop="name" content="{{ $seoTitle }}">
      <meta itemprop="description" content="{{ $seoDescription }}">
      <meta itemprop="image" content="{{ $seoImage }}">
      <meta itemprop="url" content="{{ $seoUrl }}">
      <link rel="canonical" href="{{ $seoCanonical }}">
      <!-- <link rel="amphtml" href="amp/" /> -->
      <link rel="image_src" href="{{ $seoImage }}" />
      <link rel="image_src" href="{{ $seoImage }}" />
      <script type="application/ld+json">@json($organizationSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
      <script type="application/ld+json">@json($websiteSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
      <script type="application/ld+json">@json($webpageSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
      <script type="application/ld+json">@json($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
      @yield('schema')
      <link rel="shortcut icon" href="{{url(''.$setting->favicon)}}" type="image/x-icon">
      <link rel="icon" href="{{url(''.$setting->favicon)}}" type="image/x-icon">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <!-- fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto+Serif:ital,opsz,wght@0,8..144,100..900;1,8..144,100..900&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/bootstrap.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/animate.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/custom-animate.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/swiper.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/font-awesome-all.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/jarallax.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/jquery.magnific-popup.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/odometer.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/flaticon.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/owl.carousel.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/owl.theme.default.min.css">
      <link rel="stylesheet" href="/frontend/css/nice-select.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/jquery-ui.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/aos.css">

      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/slider.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/footer.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/category.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/about.css">
      <link rel="stylesheet" href="/frontend/css/courses.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/why-choose.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/live-class.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/video-one.css">
      <link rel="stylesheet" href="/frontend/css/blog.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/counter.css">
      <link rel="stylesheet" href="/frontend/css/team.css">
      <link rel="stylesheet" href="/frontend/css/newsletter.css">
      <link rel="stylesheet" href="/frontend/css/testimonial.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/contact.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/courses-search.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/pricing.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/process.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/event.css">
      <link rel="stylesheet" href="/frontend/css/page-header.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/skill.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/become-a-teacher.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/gallery.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/notify.css">
      @yield('css')
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/shop.css">

      <!-- <link rel="stylesheet" href="assets/css/module-css/error.css" /> -->
      <!-- template styles -->
      <link rel="stylesheet" href="/frontend/css/style.css">
      <link rel="stylesheet" href="/frontend/css/responsive.css">
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/jquery-3.6.0.min.js"></script>
      <script src="https://rawgit.com/leafo/sticky-kit/master/dist/sticky-kit.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
      {{-- <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=es6,Array.prototype.includes,CustomEvent,Object.entries,Object.values,URL"></script> --}}
      {{-- <script src="https://cdn.plyr.io/3.6.7/plyr.js"></script> --}}
   </head>
   <body >
      <div class="page-wrapper">
        @include('layouts.header.index')
         <!-- /.stricky-header -->
         @yield('content')
         <!--Site Footer Two Start-->
         @include('layouts.footer.index')
         <!--Site Footer Two End-->
      </div>
      <!-- /.page-wrapper -->
      <div class="mobile-nav__wrapper">
         <div class="mobile-nav__overlay mobile-nav__toggler"></div>
         <!-- /.mobile-nav__overlay -->
         <div class="mobile-nav__content">
            <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>
            <div class="logo-box">
               <a href="{{route('home')}}" aria-label="logo image"><img src="{{$setting->logo}}" width="105" alt="" /></a>
            </div>
            <!-- /.logo-box -->
            <div class="mobile-nav__container"></div>
            <!-- /.mobile-nav__container -->
            <ul class="mobile-nav__contact list-unstyled">
               <li>
                  <i class="fa fa-envelope"></i>
                  <a href="mailto:{{$setting->email}}">{{$setting->email}}</a>
               </li>
               <li>
                  <i class="fas fa-phone"></i>
                  <a href="tel:{{$setting->phone1}}">{{$setting->phone1}}</a>
               </li>
            </ul>
            <!-- /.mobile-nav__contact -->
            <div class="mobile-nav__top">
               <div class="mobile-nav__social">
                  <a href="#" class="fab fa-twitter"></a>
                  <a href="#" class="fab fa-facebook-square"></a>
                  <a href="#" class="fab fa-pinterest-p"></a>
                  <a href="#" class="fab fa-instagram"></a>
               </div>
               <!-- /.mobile-nav__social -->
            </div>
            <!-- /.mobile-nav__top -->
         </div>
         <!-- /.mobile-nav__content -->
      </div>
      <!-- /.mobile-nav__wrapper -->
      <!-- Search Popup -->
      <div class="search-popup">
         <div class="color-layer"></div>
         <button class="close-search"><span class="far fa-times fa-fw"></span></button>
         <form method="post" action="blog.html">
            <div class="form-group">
               <input type="search" name="search-field" value="" placeholder="Search Here" required="">
               <button type="submit"><i class="fas fa-search"></i></button>
            </div>
         </form>
      </div>
      <!-- End Search Popup --><a href="#" data-target="html" class="scroll-to-target scroll-to-top">
      <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
      <span class="scroll-to-top__text"> Go Back Top</span>
      </a>
      @if(Session::has('success'))
      <div class="lobibox-notify-wrapper top right">
         <div class="lobibox-notify lobibox-notify-success animated-fast fadeInDown without-icon notify-mini" style="width: 368px;">
               <div class="lobibox-notify-icon-wrapper">
                  <div class="lobibox-notify-icon">
                     <div></div>
                  </div>
               </div>
               <div class="lobibox-notify-body">
                  <div class="lobibox-notify-msg" style="max-height: 32px;">{{ Session::get('success') }}</div>
               </div>
               <span class="lobibox-close" onclick="$('.lobibox-notify-wrapper').remove()">×</span>
         </div>
      </div>
      @endif
      @if(Session::has('error'))
      <div class="lobibox-notify-wrapper top right">
         <div class="lobibox-notify lobibox-notify-error animated-fast fadeInDown without-icon notify-mini" style="width: 368px;">
               <div class="lobibox-notify-icon-wrapper">
                  <div class="lobibox-notify-icon">
                     <div></div>
                  </div>
               </div>
               <div class="lobibox-notify-body">
                  <div class="lobibox-notify-msg" style="max-height: 32px;">{{ Session::get('error') }}</div>
               </div>
               <span class="lobibox-close" onclick="$('.lobibox-notify-wrapper').remove()">×</span>
         </div>
      </div>
      @endif
      <div class="notify bar-top do-show" >
      </div>
      <div class="InboxDolBtn__Container-sc-1iysa2f-0 djmCAT">
        <a href="{{$setting->facebook}}"  rel="noopener noreferrer">
            <div class="InboxDolBtn__InboxWrapper-sc-1iysa2f-1 fVJeBh shaking">
               <svg class="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1em" height="1em">
                  <path fill="currentColor" fill-rule="evenodd" d="M12 1C6.037 1 1 5.424 1 11.126c0 3.175 1.583 5.962 3.999 7.798v1.423L5 22a1 1 0 001.445.896l3.628-1.802a11.95 11.95 0 001.927.157c5.963 0 11-4.424 11-10.125C23 5.424 17.963 1 12 1zM3 11.126C3 6.702 6.959 3 12 3s9 3.702 9 8.126c0 4.423-3.959 8.125-9 8.125a9.95 9.95 0 01-1.887-.181 1 1 0 00-.634.086l-2.48 1.231v-1.973a1 1 0 00-.434-.826C4.38 16.09 3 13.748 3 11.127zm14.345-.798a1 1 0 10-1.2-1.6l-3.12 2.339-1.056-1.574a1 1 0 00-1.43-.242l-3.884 2.913a1 1 0 101.2 1.6l3.041-2.28 1.057 1.572a1 1 0 001.43.243l3.962-2.971z" clip-rule="evenodd"></path>
               </svg>
            </div>
         </a>
      </div>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/bootstrap.bundle.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/jarallax.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/jquery.appear.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/swiper.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/jquery.magnific-popup.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/jquery.validate.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/odometer.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/wNumb.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/wow.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/isotope.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/owl.carousel.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/jquery-ui.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/jquery.nice-select.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/marquee.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/aos.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/gsap.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/ScrollTrigger.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/SplitText.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/notify.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/notify.min.js"></script>
      <!-- template js -->
      <script src="/frontend/js/script.js"></script>
      <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
      @yield('js')
      <script>
         $(document).ready(function() {
             $('.themvaogiohangTailieu').click(function() {
                 var id = $(this).data('id');
                 var slug = $(this).data('slug');
                 $.ajax({
                     url: "/tai-lieu/them-vao-gio-hang",
                     type: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': "{{ csrf_token() }}"
                     },
                     data: {
                         document_id: id,
                         slug: slug
                     },
                     success: function(response) {
                         if(response.success){
                             $('.cart-count').html(response.count);
                             toastr.success(response.message);
                         }else{
                             toastr.error(response.message);
                         }
                     },
                     error: function(response) {
                         toastr.error(response.message);
                     }
                 });
             });
         });
     </script>
     <script>
        $(document).ready(function() {
            $('.themvaogiohangDethi').click(function() {
                var id = $(this).data('id'); 
                $.ajax({
                    url: "/de-thi/them-vao-gio-hang",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        dethi_id: id
                    },
                    success: function(response) {
                        if(response.success){
                            $('.cart-count').html(response.count);
                            toastr.success(response.message);
                        }else{
                            toastr.error(response.message);
                        }
                    },
                    error: function(response) {
                        toastr.error(response.message);
                    }
                });
            });
        });
    </script>
       <script> function jump(h) {
            var top = document.getElementById(h).offsetTop;
            window.scrollTo(0, top);
        }
        </script>
   </body>
</html>
