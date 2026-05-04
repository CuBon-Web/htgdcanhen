@extends('layouts.main.master')
@section('title')
Dự án tiêu biểu
@endsection
@section('description')
Dự án tiêu biểu
@endsection
@section('image')
{{url(''.$setting->logo)}}
@endsection
@section('js')
@endsection
@section('css')
@endsection
@section('content')
<div class="content-block  stick-to-footer">
    <div class="container-bg with-bgcolor" data-style="background-color: #F4F4F4">
       <div class="container-bg-overlay">
          <div class="container">
             <div class="row">
                <div class="col-md-12">
                   <div class="page-item-title">
                      <h1>Dự án tiêu biểu</h1>
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
                         <span property="name">Dự án tiêu biểu</span>
                         <meta property="position" content="2">
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
                  @foreach ($duan as $item)
                               @php
                                   $img = json_decode($item->images);
                               @endphp
                      <div class="wpb_column vc_column_container vc_col-sm-4">
                        <div class="vc_column-inner">
                           <div class="wpb_wrapper">
                              <style scoped='scoped'>.mgt-promo-block-47315338164.mgt-promo-block.darken .mgt-promo-block-content {background-color: rgba(10,10,10,0.3)!important;}.mgt-promo-block-47315338164.mgt-promo-block.animated:hover .mgt-promo-block-content {background-color: rgba(0,0,0,0.5)!important;}</style>
                              <div class="mgt-promo-block animated white-text cover-image text-size-normal darken mgt-promo-block-47315338164 wpb_content_element wpb_animate_when_almost_visible wpb_appear" data-style="background-image: url({{$img[0]}});background-repeat: no-repeat;width: 100%; height: 220px;">
                                 <div class="mgt-promo-block-content va-bottom">
                                    <div class="mgt-promo-block-content-inside vc_custom_1464356916127">
                                       <i class="fa fa-2 fa-building">
                                          <!-- Icon -->
                                       </i>
                                       <h3>{{$item->name}}</h3>
                                       <div class="mgt-button-wrapper mgt-button-wrapper-align-left mgt-button-wrapper-display-newline mgt-button-top-margin-disable">
                                          <a class="btn hvr-icon-wobble-horizontal mgt-button mgt-style-textwhite mgt-size-normal mgt-align-left mgt-display-newline mgt-text-size-small mgt-button-icon-position-right mgt-text-transform-uppercase " href="{{route('duanTieuBieuDetail',['slug'=>$item->slug])}}">Chi tiết<i class="fa fa-arrow-right"></i>
                                          </a></div>
                                    </div>
                                 </div>
                              </div>
                              <div class="wpb_text_column wpb_content_element  wpb_animate_when_almost_visible wpb_left-to-right left-to-right vc_custom_1459415178192" >
                                 <div class="wpb_wrapper line_3">
                                    {!!languageName($item->description)!!}
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  @endforeach
                </div>
                <div class="vc_row wpb_row vc_row-fluid">
                   <div class="wpb_column vc_column_container vc_col-sm-12">
                      <div class="vc_column-inner">
                         <div class="wpb_wrapper">
                            <div class="vc_separator wpb_content_element vc_separator_align_center vc_sep_width_100 vc_sep_pos_align_center vc_separator_no_text vc_sep_color_grey" ><span class="vc_sep_holder vc_sep_holder_l"><span  class="vc_sep_line"></span></span><span class="vc_sep_holder vc_sep_holder_r"><span  class="vc_sep_line"></span></span></div>
                         </div>
                      </div>
                   </div>
                </div>
                {{$duan->links()}}
             </article>
          </div>
       </div>
    </div>
 </div>

@endsection
