{{-- https://templates.envytheme.com/joxi/default/data-table.html --}}
<!DOCTYPE html>
<html lang="zxx">
   <head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
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
      <script type="application/ld+json">@json($webpageSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
      @yield('schema')
      <link rel="shortcut icon" href="{{url(''.$setting->favicon)}}" type="image/x-icon">
      <link rel="icon" href="{{url(''.$setting->favicon)}}" type="image/x-icon">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <!-- Link Of CSS --> 
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/bootstrap.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/owl.theme.default.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/owl.carousel.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/remixicon.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/boxicons.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/iconsax.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/metismenu.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/simplebar.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/calendar.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/sweetalert2.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/jbox.all.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/editor.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/fontawesome.min.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/loaders.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/header.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/sidebar-menu.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/footer.css">
      <link rel="stylesheet" href="/frontend/crm-course/css/style.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/dark-mode.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/crm-course/css/responsive.css">
      <link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/notify.css">
      @yield('css_crm_course')
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/jquery-3.6.0.min.js"></script>
      <script src="https://rawgit.com/leafo/sticky-kit/master/dist/sticky-kit.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
      <script>
         window.MathJax = {
             tex: {
                 inlineMath: [
                     ['$', '$'],
                     ['\\(', '\\)']
                 ],
                 displayMath: [
                     ['$$', '$$'],
                     ['\\[', '\\]']
                 ]
             }
         };
     </script>
     <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
      <!-- Favicon -->
      <link rel="icon" type="image/png" href="/frontend/crm-course/images/favicon.svg">

   </head>
   <body class="body-bg-f8faff">
      <!-- Start Preloader Area -->
      <div class="preloader">
         <img src="{{$setting->logo}}" alt="main-logo">
      </div>
      <!-- End Preloader Area -->
      <!-- Start All Section Area -->
      <div class="all-section-area">
         @include('crm_course.main.header')
        
        @yield('content_crm_course')
         <!-- End Main Content Area -->
      </div>
      <!-- End All Section Area -->
      <!-- Start Template Sidebar Area -->
      <div class="template-sidebar-option">
         <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasRight">
            <div class="offcanvas-header">
               <a href="index.html">
               <img src="{{$setting->logo}}" alt="main-logo">
               </a>
               <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
               <ul>
                  <li>
                     <!-- Start Dark Mode All Area -->
                     <h4>Nèn Tối Giao Diện</h4>
                     <div class="dark-mode-btn">
                        <div class="dark-version">
                           <label class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="DarkModeSW">
                           </label>
                        </div>
                     </div>
                     <!-- End Dark Mode All Area -->
                  </li>
                  <li>
                     <!-- Start Dark Mode All Area -->
                     <h4>Nèn Tối Header</h4>
                     <div class="dark-mode-btn">
                        <div class="dark-version">
                           <label class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="DarkModeSW2">
                           </label>
                        </div>
                     </div>
                     <!-- End Dark Mode All Area -->
                  </li>
                  <li>
                     <!-- Start Dark Mode All Area -->
                     <h4>Nèn Tối Sidebar</h4>
                     <div class="dark-mode-btn">
                        <div class="dark-version">
                           <label class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="DarkModeSW3">
                           </label>
                        </div>
                     </div>
                     <!-- End Dark Mode All Area -->
                  </li>
               </ul>
            </div>
         </div>
      </div>
      <!-- End Template Sidebar Area -->
      <!-- Start Go Top Area -->
      <div class="go-top">
         <i class="ri-arrow-up-s-fill"></i>
         <i class="ri-arrow-up-s-fill"></i>
      </div>
      <!-- End Go Top Area -->
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
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/bootstrap.bundle.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/owl.carousel.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/metismenu.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/simplebar.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/geticons.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/sweetalert2.all.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/jbox.all.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/editor.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/calendar.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/form-validator.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/contact-form-script.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/ajaxchimp.min.js"></script>
      <script src="{{ env('AWS_R2_URL') }}/frontend/js/notify.js"></script>
      <!-- ApexCharts JS -->
      {{-- <script src="/frontend/crm-course/js/apexcharts.min.js"></script>
      <script src="/frontend/crm-course/js/apexcharts-stock-prices.js"></script>
      <script src="/frontend/crm-course/js/apexcharts-irregular-data-series.js"></script>
      <script src="/frontend/crm-course/js/apex-custom-line-chart.js"></script>
      <script src="/frontend/crm-course/js/apex-custom-pie-donut-chart.js"></script>
      <script src="/frontend/crm-course/js/apex-custom-area-charts.js"></script>
      <script src="/frontend/crm-course/js/apex-custom-column-chart.js"></script>
      <script src="/frontend/crm-course/js/apex-custom-bar-charts.js"></script>
      <script src="/frontend/crm-course/js/apex-custom-mixed-charts.js"></script>
      <script src="/frontend/crm-course/js/apex-custom-radialbar-charts.js"></script>
      <script src="/frontend/crm-course/js/apex-custom-radar-chart.js"></script>
      <script src="/frontend/crm-course/js/website-analytics.js"></script> --}}
      <script src="{{ env('AWS_R2_URL') }}/frontend/crm-course/js/custom.js"></script>
      @yield('js_crm_course')
   </body>
</html>