@extends('layouts.main.master')
@section('title')
Kết quả tìm kiếm
@endsection
@section('description')
Đã tìm thấy {{$countproduct}} kết quả phù hợp cho từ khóa "{{$keyword}}"
@endsection
@section('image')
{{url(''.$setting->logo)}}
@endsection
@section('css')
<link href="{{asset('frontend/css/collection_style.scss.css?1663035745304')}}" rel="stylesheet" type="text/css" />
<link rel="preload" as='style' type="text/css" href="{{asset('frontend/css/collection_style.scss.css?1663035745304')}}">
@endsection
@section('content')
<div class="main-index">
	<section class="bread-crumb">
	   <div class="container">
		  <div class="row">
			 <div class="col-12 a-left">
				<ul class="breadcrumb">
				   <li class="home">
					  <a href="/"><span>Trang chủ</span></a>						
					  <span class="mr_lr">&nbsp;/&nbsp;</span>
				   </li>
				   <li><strong><span>Tìm kiếm</span></strong></li>
				</ul>
			 </div>
		  </div>
	   </div>
	</section>
	<section class="signup search-main wrap_background background_white clearfix">
	   <div class="container">
		  <div class="row bg_page clearfix margin-bottom-50">
			 <div class="col-12 col-xl-12 col-lg-12 margin-bottom-15">
				<div class="title-block-page">
				   <h1 style="font-size:20px;">Trang tìm kiếm</h1>
				</div>
				@if (count($resultPro)>0)
				<p style="display:block;margin-bottom:20px;">
					Đã tìm thấy {{$countproduct}} kết quả phù hợp
				</p>
				@else
				<p style="display:block;margin-bottom:20px;">
					Không tìm thấy kết quả phù hợp cho từ khóa {{$countproduct}}
				</p>
				@endif
				
			 </div>
			 <div class="search-page col-12">
				<div class="products-view products-view-grid ">
				   <div class="row">
					@foreach ($resultPro as $pro)
						@php
						$img = json_decode($pro['images']);
						@endphp
					  <div class="col-6 col-md-4 col-lg-3 col-xl-2 product-col">
						 <div class="item_product_main item_option margin-bottom-15">
							<form  action="" method="post" class="variants product-action wishItem" enctype="multipart/form-data">
							   <div class="product-thumbnail  ">
								  <a class="image_thumb" href="{{route('detailProduct',['cate'=>$pro['cate_slug'],'type'=>$pro['type_slug'] ? $pro['type_slug'] : 'loai','id'=>$pro['slug']])}}" title="{{languageName($pro['name'])}}" style="height: 183px;">
									 <span class="opacity"></span>
									 <picture>
										<source media="(min-width: 1200px)" srcset="{{$img[0]}}">
										<source media="(min-width: 992px)" srcset="{{$img[0]}}">
										<source media="(min-width: 569px)" srcset="{{$img[0]}}">
										<source media="(max-width: 480px)" srcset="{{$img[0]}}">
										<source media="(max-width: 375px)" srcset="{{$img[0]}}">
										<img width="199" height="199" src="{{$img[0]}}" data-src="{{$img[0]}}" alt="{{languageName($pro['name'])}}" class="lazyload img-responsive center-block loaded" data-was-processed="true">
									 </picture>
								  </a>
								  <div class="product-action">
									<input type="hidden"  id="inputqty" value="1">
									<button class="btn-buy btn-left btn-views " title="Cho vào giỏ"  onclick="addToCart({{$pro['id']}},1)">
									   <svg class="icon">
										  <use xlink:href="#addcarticon"></use>
									   </svg>
									</button>
								  </div>
							   </div>
							   <div class="product-info">
								  <h3 class="product-name"><a href="{{route('detailProduct',['cate'=>$pro['cate_slug'],'type'=>$pro['type_slug'] ? $pro['type_slug'] : 'loai','id'=>$pro['slug']])}}" title="{{languageName($pro['name'])}}">{{languageName($pro['name'])}}</a></h3>
								  <div class="price-box">
									@if ($pro['price'] > 0)
									@if ($pro['discount'] > 0)
									   <span class="price">{{number_format($pro['price'])}}₫</span>
									   <span class="compare-price">Giá cũ: <span>{{number_format($pro['price']-($pro['price']*($pro['discount']/100)))}}₫</span></span>
									@else
									<span class="price">{{number_format($pro['price'])}}₫</span>
									@endif
									@else 
									<span class="price">Liên Hệ</span>
									@endif
								  </div>
								  <div class="lazyload vendoritem" onclick="addToCart({{$pro['id']}},1)" data-src="{{url('frontend/images/cart.png')}}" data-was-processed="true" style="background-image: url(&quot;{{url('frontend/images/cart.png')}}&quot;);"></div>
							   </div>
							</form>
						 </div>
					  </div>
					@endforeach
				   </div>
				</div>
			 </div>
		  </div>
	   </div>
	</section>
 </div>
@endsection