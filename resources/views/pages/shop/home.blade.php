@extends('layouts.shop', ['title' => '영업용 쇼핑몰'])

@section('content')
    @include('pages.shop.partials.main-header', ['active' => 'home'])

    <section id="introduce">
        <div class="flex" style="position:relative;">
            <div class="flex__col">
                <h3 class="location">대구광역시 | 달서구</h3>
                <h2 class="shopName">이화플라워</h2>
            </div>
            <div class="flex__col">
                <button type="button" class="btn_share"><i class="bi bi-upload"></i> 공유하기</button>
            </div>
        </div>
        <div class="description">
            저희 이화플라워는 고객님들의 마음을 전하는 꽃을 정성들여 만들어드립니다.저희 이화플라워는 고객님들의 마음을 전하는 꽃을 정성들여 만들어드립니다.
            저희 이화플라워는 고객님들의 마음을 전하는 꽃을 정성들여 만들어드립니다.
        </div>

        <div id="mapArea">
            <div id="map" style="height:300px;background:#f9f9f9;display:flex;align-items:center;justify-content:center;">다음지도 API</div>

            <div id="mapContent">
                <span><img src="{{ asset('assets/img/ico1.png') }}" height="20" alt=""> 대구 달서구 조암남로 24 1층 컬러위드플라워</span>
                <span><img src="{{ asset('assets/img/ico2.png') }}" height="20" alt=""> 10:00 ~ 20:00 <em class="color-green pl5">현재 영업 중인 매장</em></span>
                <span><img src="{{ asset('assets/img/ico3.png') }}" height="20" alt=""> 이미지를 둘러보고 간편하게 예약 해보세요!</span>
            </div>

            <div id="tags">
                <span class="tag">#꽃다발</span>
                <span class="tag">#꽃바구니</span>
                <span class="tag">#3단화환</span>
                <span class="tag">#개업화분</span>
                <span class="tag">#꽃다발추가</span>
            </div>
        </div>
    </section>

    <section id="quickMenu">
        <h2>빠른 메뉴</h2>
        <ul>
            <li><a href="#none">네이버 예약 바로가기 <i class="bi bi-box-arrow-up-right"></i></a></li>
            <li><a href="#none">네이버 예약 바로가기 <i class="bi bi-box-arrow-up-right"></i></a></li>
            <li><a href="#none">카카오톡 채널 바로가기 <i class="bi bi-box-arrow-up-right"></i></a></li>
        </ul>
    </section>

    <section id="faq">
        <h2>이화플라워에 자주하는 질문</h2>

        <ul>
            <li>
                <div class="q">이화플라워에 자주하는 질문</div>
                <div class="a">이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문</div>
            </li>
            <li>
                <div class="q">이화플라워에 자주하는 질문</div>
                <div class="a">이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문</div>
            </li>
            <li>
                <div class="q">이화플라워에 자주하는 질문</div>
                <div class="a">이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문</div>
            </li>
            <li>
                <div class="q">이화플라워에 자주하는 질문</div>
                <div class="a">이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문이화플라워에 자주하는 질문</div>
            </li>
        </ul>
    </section>

    @include('pages.shop.partials.footer')
@endsection
