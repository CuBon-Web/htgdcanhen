@extends('layouts.main.master')
@section('title')
{{$title}}
@endsection
@section('description')
Danh sách {{$title}}
@endsection
@section('image')
{{url(''.$setting->logo)}}
@endsection
@section('js')
@endsection
@section('css')
@endsection
@section('content')
<div class="content-block ">
    <div class="container-bg with-bgcolor" data-style="background-color: #F4F4F4">
       <div class="container-bg-overlay">
          <div class="container">
             <div class="row">
                <div class="col-md-12">
                   <div class="page-item-title">
                      <h1>{{$title}}</h1>
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
                         <a property="item" typeof="WebPage" title="Go to TheBuilt." href="{{route('home')}}" class="home">
                            <span property="name">Trang chủ</span>
                        </a>
                         <meta property="position" content="1">
                      </span>
                      &gt; 
                      <span property="itemListElement" typeof="ListItem">
                         <span property="name">{{$title}}</span>
                         <meta property="position" content="3">
                      </span>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
    <div class="page-container container">
       <div class="row">
          <div class="col-md-12 entry-content">
             <article>
                
                <div class="vc_row wpb_row vc_row-fluid">
                    @if (count($list) > 0)
                    @foreach ($list as $item)
                        @php
                        $imgpro = json_decode($item->images);
                        @endphp
                    <div class="wpb_column vc_column_container vc_col-sm-3">
                        <div class="vc_column-inner">
                            <div class="portfolio-item-block portfolio-item-animation-6 slide-item building" data-item="1" data-name="Modern House">
                                <div class="portfolio-item-block-inside">
                                   <a href="{{route('detailProduct',['cate'=>$item->cate_slug,'type'=>$item->type_slug ? $item->type_slug : 'loai','id'=>$item->slug])}}" target="_self" rel="" title="Modern House">
                                      <div class="portfolio-item-image" data-style="background-image: url({{$imgpro[0]}});"></div>
                                      <div class="portfolio-item-bg"></div>
                                      <div class="info">
                                         <span class="sub-title">Mẫu thiết kế</span>
                                         <h4 class="title" style="color: white">{{languageName($item->name)}}</h4>
                                         <div class="view-more btn mgt-button">Chi tiết</div>
                                      </div>
                                   </a>
                                </div>
                             </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <h4>Nội dung đang cập nhật</h4>
                    @endif
                    {{$list->links()}}
                </div>
                <div class="vc_row wpb_row vc_row-fluid">
                    <div class="wpb_column vc_column_container vc_col-sm-12">
                       <div class="vc_column-inner">
                          <div class="wpb_wrapper">
                             <div class="wpb_text_column wpb_content_element " >
                                <div class="wpb_wrapper">
                                   {!!$content!!}
                                </div>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
             </article>
          </div>
       </div>
    </div>
 </div>
@endsection

