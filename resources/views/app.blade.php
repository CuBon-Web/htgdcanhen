<!DOCTYPE html>
<html>
  <head>
    @php
      $appName = config('app.name', 'HTGD Cánh Én');
      $appUrl = url('/');
      $appImage = url('img/logo.png');
      $appDescription = 'Hệ thống học tập và quản lý khóa học trực tuyến.';
    @endphp
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="description" content="{{ $appDescription }}">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <link rel="canonical" href="{{ request()->url() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:title" content="{{ $appName }}">
    <meta property="og:description" content="{{ $appDescription }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:image" content="{{ $appImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $appName }}">
    <meta name="twitter:description" content="{{ $appDescription }}">
    <meta name="twitter:image" content="{{ $appImage }}">
    <link rel="icon" href="{{url('img/logo.png')}}">
    <title>{{ $appName }}</title>
    <script type="application/ld+json">
      @json([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $appName,
        'url' => $appUrl,
        'inLanguage' => 'vi-VN',
      ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
    </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
<link rel="stylesheet" href="{{ env('AWS_R2_URL') }}/frontend/css/bootstrap.min.css">

    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      // Shared ID
      gtag('config', 'UA-118965717-3');
      // Vue.js ID
      gtag('config', 'UA-118965717-7');
    </script>
    <style>
      .con-select{
        width: 100%!important;
      }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
      window.__ENV__ = window.__ENV || {}; __ENV__.link ='https://htgdcanhen.com/';
      window.Laravel = {!!
          json_encode([
              'csrf_token' => csrf_token(),
          ])
       !!};
      </script>

  </head>
  <body>

    <div id="app"></div>
    <!-- built files will be auto injected -->
 <script src="/frontend/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

  </body>
</html>
