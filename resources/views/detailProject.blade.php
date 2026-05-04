@extends('layouts.main.master')
@section('title')
{{$detail->name}}
@endsection
@section('description')
{{$detail->description}}
@endsection
@section('image')
@php
        $img = json_decode($detail->images);
    @endphp
{{$img[0]}}
@endsection
@section('css')
<link rel="stylesheet" href="{{asset('frontend/css/vc_carousel.min.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/jquery.tosrus.min.all.css')}}">
@endsection
@section('js')
      <script src="{{asset('frontend/js/vc_carousel.min.js')}}"></script>
@endsection
@section('content')
<div class="content-block">
    <div class="container-bg with-bgcolor" data-style="background-color: #F4F4F4">
       <div class="container-bg-overlay">
          <div class="container">
             <div class="row">
                <div class="col-md-12">
                   <div class="page-item-title">
                      <h1>{{$detail->name}}</h1>
                   </div>
                </div>
             </div>
          </div>
       </div>
       <div class="breadcrumbs-container-wrapper">
          <div class="container">
             <div class="row">
                <div class="col-md-12">
                   <div class="breadcrumbs" typeof="BreadcrumbList" vocab="http://schema.org/">
                      <!-- Breadcrumb NavXT 6.2.1 -->
                      <span property="itemListElement" typeof="ListItem">
                         <a property="item" typeof="WebPage" title="Go to TheBuilt." href="{{route('home')}}" class="home"><span property="name">Trang chủ</span></a>
                         <meta property="position" content="1">
                      </span>
                      &gt; 
                      <span property="itemListElement" typeof="ListItem">
                         <a property="item" typeof="WebPage" title="Go to Portfolio." href="{{route('duanTieuBieu')}}" class="post post-mgt_portfolio-archive"><span property="name">Dự án tiêu biểu</span></a>
                         <meta property="position" content="2">
                      </span>
                      &gt; 
                      <span property="itemListElement" typeof="ListItem">
                         <span property="name">{{$detail->name}}</span>
                         <meta property="position" content="4">
                      </span>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>
 <div class="project-container container portfolio-item-details portfolio-layout-1 portfolio-title-position-default">
    <div class="row">
       <div class="col-md-12">
          <div class=" portfolio-item-image-container">
             <div class="row">
                <div class="col-md-12">
                   <div class="portfolio-item-image">
                      <div class="porftolio-slider">
                         <ul class="slides">
                            <li><a href="{{$img[0]}}" rel="lightbox"><img src="{{$img[0]}}" alt="{{$detail->name}}" /></a></li>
                         </ul>
                      </div>
                   </div>
                </div>
             </div>
          </div>
          <div class="portfolio-item-data clearfix">
             <div class="project-content">
                <div class="vc_row wpb_row vc_row-fluid vc_custom_1462805554108">
                   <div class="wpb_column vc_column_container vc_col-sm-12">
                      <div class="vc_column-inner">
                         <div class="wpb_wrapper">
                            <div class="wpb_images_carousel wpb_content_element vc_clearfix">
                               <div class="wpb_wrapper">
                                  <div id="vc_images-carousel-1-1692666687" data-ride="vc_carousel" data-wrap="false" style="width: 100%;" data-interval="0" data-auto-height="yes" data-mode="horizontal" data-partial="false" data-per-view="3" data-hide-on-end="true" class="vc_slide vc_images_carousel">
                                     <!-- Wrapper for slides -->
                                     <div class="vc_carousel-inner">
                                        <div class="vc_carousel-slideline">
                                           <div class="vc_carousel-slideline-inner">
                                            @foreach ($img as $item)
                                            <div class="vc_item">
                                                <div class="vc_inner">
                                                   <a href="{{$item}}" rel="lightbox" data-rel="lightbox-image-0" data-rl_title="" data-rl_caption="" title="">
                                                   <img width="1170" height="780" src="{{$item}}" class="attachment-full" alt="" srcset="{{$item}} 1170w, {{$item}} 300w, {{$item}} 768w, {{$item}} 1024w" sizes="(max-width: 1170px) 100vw, 1170px" />										</a>
                                                </div>
                                             </div>
                                            @endforeach
                                           </div>
                                        </div>
                                     </div>
                                     <!-- Controls -->
                                     <a class="vc_left vc_carousel-control" href="#vc_images-carousel-1-1692666687" data-slide="prev">
                                     <span class="icon-prev"></span>
                                     </a>
                                     <a class="vc_right vc_carousel-control" href="#vc_images-carousel-1-1692666687" data-slide="next">
                                     <span class="icon-next"></span>
                                     </a>
                                  </div>
                               </div>
                            </div>
                         </div>
                      </div>
                   </div>
                </div>
                <div class="vc_row wpb_row vc_row-fluid vc_custom_1462525906878">
                   <div class="wpb_column vc_column_container vc_col-sm-12">
                      <div class="vc_column-inner">
                         <div class="wpb_wrapper">
                            <div class="mgt-header-block clearfix text-left text-black wpb_animate_when_almost_visible wpb_top-to-bottom wpb_content_element  mgt-header-block-style-2 mgt-header-texttransform-header ">
                               <h2 class="mgt-header-block-title">{{$detail->name}}</h2>
                               <div class="mgt-header-line"></div>
                            </div>
                            <div class="vc_row wpb_row vc_inner vc_row-fluid">
                               <div class="wpb_column vc_column_container vc_col-sm-12">
                                  <div class="vc_column-inner">
                                    {!!languageName($detail->content)!!}
                                  </div>
                               </div>
                            </div>
                           
                         </div>
                      </div>
                   </div>
                </div>
                <div class="clear"></div>
             </div>
             <div class="row">
                <div class="col-md-12">
                </div>
             </div>
          </div>
          <div class="clear"></div>
       </div>
    </div>
 </div>
@endsection