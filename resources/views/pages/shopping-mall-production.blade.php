@extends('layouts.app')

@section('content')
    <div id="content__body" style="position:relative">

        <section class="mb20">
            <h2 class="tt2">✔️ 정직한플라워 마케팅 지원센터 <small>영업용 쇼핑몰 제작</small></h2>
        </section>

        <div id="content__head">
            <img src="{{ asset('assets/img/content_head4.png') }}" alt="">
        </div>

        <div id="marketingCenter">

            <nav class="nav-tab mt20">
                <ul>
                    <li><a href="{{ route('shopping-mall-production') }}">설명서 읽어보기</a></li>
                    <li class="active"><a href="{{ route('shop-site.home') }}">쇼핑몰 제작하기</a></li>
                </ul>
            </nav>

        </div>

    </div>
@endsection
