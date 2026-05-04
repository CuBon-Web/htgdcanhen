@extends('layouts.main.master')
@section('title')
Bảng giá dịch vụ taxi nội bài
@endsection
@section('description')
Bảng giá dịch vụ taxi nội bài
@endsection
@section('image')
{{url(''.$setting->logo)}}
@endsection
@section('css')
@endsection
@section('js')
@endsection
@section('content')
<div class="breadcumb-wrapper background-image" data-overlay="title" data-opacity="4" style="background-image: url(&quot;{{url('frontend/img/breadcumb-bg.jpg')}}&quot;);">
	<div class="container z-index-common">
	   <h1 class="breadcumb-title">Bảng giá dịch vụ</h1>
	   <ul class="breadcumb-menu">
		  <li><a href="{{route('home')}}">Trang chủ</a></li>
		  <li>Bảng giá dịch vụ</li>
	   </ul>
	</div>
 </div>
<div class="position-relative space">
	<div class="container">
	   <form class="booking-form-area ajax-booking">
		  <div class="row">
			<div class="col-md-6 col-lg-6 col-12">
				<table style="width: 100%; border-collapse: collapse; background-color: #ffffff; border-style: solid; border-color: #efefef;" border="1" cellpadding="5">
					<tbody>
					   <tr style="height: 24px;background: #23bc2c;">
						  <td style="width: 100%; height: 24px; text-align: center;    color: white;" colspan="2"><b>4 chỗ</b></td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Hà Nội =&gt; Nội Bài</td>
						  <td style="width: 53.7489%; height: 24px;">từ 180.000đ</td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Nội Bài =&gt; Hà Nội</td>
						  <td style="width: 53.7489%; height: 24px;">từ 230.000đ</td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Hai chiều&nbsp;(trong ngày)</td>
						  <td style="width: 53.7489%; height: 24px;">từ 400.000đ</td>
					   </tr>
					</tbody>
				 </table>
			 </div>
			 <div class="col-md-6 col-lg-6 col-12">
				<table style="width: 100%; border-collapse: collapse; background-color: #ffffff; border-color: #efefef; border-style: solid;" border="1" cellpadding="5">
					<tbody>
					   <tr style="height: 24px;background: #23bc2c;">
						  <td style="width: 100%; height: 24px; text-align: center;    color: white;" colspan="2"><b>5&nbsp; chỗ</b></td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Hà Nội =&gt; Nội Bài</td>
						  <td style="width: 53.7489%; height: 24px;">từ 200.000đ</td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Nội Bài =&gt; Hà Nội</td>
						  <td style="width: 53.7489%; height: 24px;">từ 250.000đ</td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Hai chiều&nbsp;(trong ngày)</td>
						  <td style="width: 53.7489%; height: 24px;">từ 450.000đ</td>
					   </tr>
					</tbody>
				 </table>
			 </div>
			 <div class="col-md-6 col-lg-6 col-12">
				<table style="width: 100%; border-collapse: collapse; background-color: #ffffff; border-color: #efefef; border-style: solid;" border="1" cellpadding="5">
					<tbody>
					   <tr style="height: 24px;background: #23bc2c;">
						  <td style="width: 100%; height: 24px; text-align: center;    color: white;" colspan="2"><b>7 chỗ</b></td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Hà Nội =&gt; Nội Bài</td>
						  <td style="width: 53.7489%; height: 24px;">từ 230.000đ</td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Nội Bài =&gt; Hà Nội</td>
						  <td style="width: 53.7489%; height: 24px;">từ 280.000đ</td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Hai chiều&nbsp;(trong ngày)</td>
						  <td style="width: 53.7489%; height: 24px;">từ 500.000đ</td>
					   </tr>
					</tbody>
				 </table>
			 </div>
			 <div class="col-md-6 col-lg-6 col-12">
				<table style="width: 100%; border-collapse: collapse; background-color: #ffffff; border-color: #efefef; border-style: solid;" border="1" cellpadding="5">
					<tbody>
					   <tr style="height: 24px;background: #23bc2c;">
						  <td style="width: 100%; height: 24px; text-align: center;    color: white;" colspan="2"><b>16 chỗ</b></td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Hà Nội =&gt; Nội Bài</td>
						  <td style="width: 53.7489%; height: 24px;">từ 450.000đ</td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Nội Bài =&gt; Hà Nội</td>
						  <td style="width: 53.7489%; height: 24px;">từ 500.000đ</td>
					   </tr>
					   <tr style="height: 24px;">
						  <td style="width: 46.2511%; height: 24px;">Hai chiều&nbsp;(trong ngày)</td>
						  <td style="width: 53.7489%; height: 24px;">từ 900.000đ</td>
					   </tr>
					</tbody>
				 </table>
			 </div>
			</div>
		  <p class="form-messages mb-0 mt-3"></p>
	   </form>
	</div>
 </div>
@endsection