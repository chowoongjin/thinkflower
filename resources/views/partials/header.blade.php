@include('partials.header-sub')
@php
    $authUser = auth()->user();
    $shop = $authUser?->shop;

    $shopName = $shop?->shop_name ?? '화원명 없음';

    $regionLabel = \Illuminate\Support\Facades\DB::table('shop_delivery_areas')
        ->join('regions', 'shop_delivery_areas.region_id', '=', 'regions.id')
        ->where('shop_delivery_areas.shop_id', $shop?->id)
        ->orderBy('shop_delivery_areas.id')
        ->value('regions.sido');

    $shopDisplayName = $shopName . ($regionLabel ? '(' . $regionLabel . ')' : '');
@endphp

<header id="header">
    <div id="header-notice">
        ‘진짜’ 화원사를 위한 수발주 중개수수료 0% 인트라넷　본부 영업시간: 365일 연중무휴 09:00 ~ 19:00　현재 정상적으로 영업중입니다　<span class="color-danger">금일 포항/울산 근조화환 마감되었습니다　2025년 07월 01일 ~ 03일 까지 당일중 발주만 가능합니다</span>
    </div>

    <div id="header-main">
        <div class="container">
            <div id="header__logo" class="header__col">
                <a href="{{ route('home') }}"><img src="{{ asset('assets/img/logo.png') }}" alt="logo"></a>
            </div>

            <nav id="header__nav" class="header__col">
                <ul>
                    <li class="{{ request()->routeIs('bonbu-balju') ? 'active' : '' }}">
                        <a href="{{ route('bonbu-balju') }}">본부발주</a>
                    </li>
                    <li class="{{ request()->routeIs('order-list') ? 'active' : '' }}">
                        <a href="{{ route('order-list') }}">발주리스트</a>
                    </li>
                    <li class="{{ request()->routeIs('suju-list*') ? 'active' : '' }}">
                        <a href="{{ route('suju-list') }}">수주리스트</a>
                    </li>
                    <li class="{{ request()->routeIs('calculate-list') ? 'active' : '' }}">
                        <a href="{{ route('calculate-list') }}">정산내역</a>
                    </li>
                    <li>
                        <a href="#none">이용료안내</a>
                    </li>
                    <li class="{{ request()->routeIs('photo-list') ? 'active' : '' }}">
                        <a href="{{ route('photo-list') }}">사진공유방</a>
                    </li>
                    <li>
                        <a href="#none">계산서 내역</a>
                    </li>
                </ul>
            </nav>

            <div id="header__select" class="header__col">
                <div class="dropdown">
                    <button type="button">{{ $shopDisplayName }}<em>arrow</em></button>
                    <div class="dropdown-content">
                        <a href="#none">마이페이지</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button id="btn_logout" type="submit">로그아웃</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div id="body">
    <aside id="sidebar">
        <div class="row">
            <small>
                <em class="status-on"></em>
                계정상태 : 활성화
            </small>
            <h2 class="row-title mt20 mb10">{{ $shopDisplayName }}</h2>
            <p class="row-desc">사장님, 안녕하세요</p>
        </div>

        <div class="row">
            <small>{{ now()->format('Y년 m월 d일') }}</small>
            <div class="flex mt15">
                <div class="flex__col">
                    <h2 class="row-title">매장 운영현황</h2>
                </div>
                <div class="flex__col">
                    <label class="toggle">
                        <input type="checkbox">
                        <span class="track"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <small>보유금액</small>
            <strong class="color-primary fs20 pl10">{{ number_format(optional(optional(auth()->user())->shop)->current_point_balance ?? 0) }}원</strong>
            <a href="#none" class="btn btn-point btn-fluid mb10">포인트 간편충전</a>
        </div>

        <div class="row" id="sidebar__tel">
            <h3 class="sidebar-title">수발주사업부 대표번호</h3>
            <strong>1877-5681</strong>
        </div>

        <div class="row">
            <h3 class="sidebar-title">간편 메뉴 바로가기</h3>
            <ul>
                <li><a href="#none">공지사항 전체보기</a></li>
                <li><a href="#none">인트라넷 사용가이드</a></li>
                <li><a href="#none">개선희망사항 접수</a></li>
            </ul>
        </div>

        <div class="row">
            <h3 class="sidebar-title">정직한플라워 회원복지</h3>
            <ul>
                <li><a href="#none">세무·노무 무료상담</a></li>
                <li><a href="#none">소상공인 마케팅지원</a></li>
                <li><a href="#none">상조서비스 최대할인</a></li>
                <li><a href="#none">영업쇼핑몰 제작</a></li>
            </ul>
        </div>
    </aside>

    <div id="content">
