@extends('layouts.main.master')
@section('title')
{{languageName($blog_detail->title)}}
@endsection
@section('description')
{{languageName($blog_detail->description)}}
@endsection
@section('image')
{{url(''.$blog_detail->image)}}
@endsection
@section('og_type', 'article')
@section('schema')
@php
    $blogTitle = trim(languageName($blog_detail->title));
    $blogDescription = \Illuminate\Support\Str::limit(trim(strip_tags(languageName($blog_detail->description))), 160, '');
    $blogImage = url('' . $blog_detail->image);
    $blogPublished = optional($blog_detail->created_at)->toIso8601String();
    $blogModified = optional($blog_detail->updated_at)->toIso8601String();
    $blogAuthor = $blog_detail->author ?? ($setting->company ?? $setting->webname ?? config('app.name'));
    $blogCategoryName = optional($blog_detail->cate)->name ? languageName($blog_detail->cate->name) : 'Blog';

    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => request()->fullUrl(),
        ],
        'headline' => $blogTitle,
        'description' => $blogDescription,
        'image' => [$blogImage],
        'author' => [
            '@type' => 'Person',
            'name' => $blogAuthor,
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $setting->webname ?? $setting->company ?? config('app.name'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => url($setting->logo ?? $setting->favicon ?? 'img/logo.png'),
            ],
        ],
        'datePublished' => $blogPublished,
        'dateModified' => $blogModified ?: $blogPublished,
        'articleSection' => $blogCategoryName,
        'inLanguage' => 'vi-VN',
    ];
@endphp
<script type="application/ld+json">@json($articleSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
@endsection
@section('css')
@endsection
@section('js')
@endsection
@section('content')
<section class="page-header">
    <div class="page-header__bg" style="background-image: url({{ env('AWS_R2_URL') }}/frontend/images/page-header-bg-shape.png);">
    </div>
    <div class="page-header__shape-4">
        <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-4.png" alt="">
    </div>
    <div class="page-header__shape-5">
        <img src="{{ env('AWS_R2_URL') }}/frontend/images/page-header-shape-5.png" alt="">
    </div>
    <div class="container">
        <div class="page-header__inner">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1>{{languageName($blog_detail->title)}}</h1>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="{{route('home')}}">Trang chủ</a></li>
                            <li><span>//</span></li>
                            <li><a href="">{{languageName($blog_detail->cate->name)}}</a></li>
                            <li><span>//</span></li>
                            <li>{{languageName($blog_detail->title)}}</li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
 </section>
 <section class="blog-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="blog-details__left">
                    <div class="blog-details__content">
                        {!!languageName($blog_detail->content)!!}
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="sidebar">
                    <div class="sidebar__single sidebar__category">
                        <div class="sidebar__title-box">
                            <div class="sidebar__title-icon">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/sidebar-title-icon.png" alt="">
                            </div>
                            <h3 class="sidebar__title">Danh mục khác </h3>
                        </div>
                        <ul class="sidebar__category-list list-unstyled">
                            @foreach ($blogCate as $item)
                            <li>
                                <a href="{{route('listCateBlog',['slug'=>$item->slug])}}">{{languageName($item->name)}}<span
                                        class="fas fa-arrow-right"></span></a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="sidebar__single sidebar__post">
                        <div class="sidebar__title-box">
                            <div class="sidebar__title-icon">
                                <img src="{{ env('AWS_R2_URL') }}/frontend/images/sidebar-title-icon.png" alt="">
                            </div>
                            <h3 class="sidebar__title">Bài viết mới</h3>
                        </div>
                        <ul class="sidebar__post-list list-unstyled">
                            @foreach ($bloglq as $item)
                            <li>
                                <div class="sidebar__post-image">
                                    <img src="{{$item->image}}" alt="">
                                </div>
                                <div class="sidebar__post-content">
                                    <ul class="sidebar__post-meta list-unstyled">
                                        <li>
                                            <p><span class="icon-clock"></span>{{date_format($item->created_at,'d/m/Y')}}</p>
                                        </li>
                                    </ul>
                                    <h3 class="sidebar__post-title"><a href="{{route('detailBlog',['slug'=>$item->slug])}}">{{languageName($item->title)}}</a></h3>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection