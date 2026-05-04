
@php
$img = json_decode($pro->images);
@endphp


<div class="vc_column-inner">
   <div class="wpb_wrapper">
      <style scoped='scoped'></style>
      <div class="mgt-promo-block animated white-text cover-image text-size-normal darken mgt-promo-block-47329438164 wpb_content_element wpb_animate_when_almost_visible wpb_appear" data-style="background-image: url({{$img[0]}});background-repeat: no-repeat;width: 100%; height: 350px;">
         <div class="mgt-promo-block-content va-bottom">
            <div class="mgt-promo-block-content-inside vc_custom_1548148028971">
               <h2 >{{languageName($pro->name)}}</h2>
               <div class="mgt-button-wrapper mgt-button-wrapper-align-left mgt-button-wrapper-display-newline mgt-button-top-margin-disable">
                  <a class="btn hvr-icon-wobble-horizontal mgt-button mgt-style-textwhite mgt-size-normal mgt-align-left mgt-display-newline mgt-text-size-small mgt-button-icon-position-right mgt-text-transform-uppercase " href="{{route('detailProduct',['cate'=>$pro->cate_slug,'type'=>$pro->type_slug ? $pro->type_slug : 'loai','id'=>$pro->slug])}}">Xem<i class="fa fa-arrow-right"></i>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>