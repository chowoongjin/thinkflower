<!DOCTYPE html>
<html lang="ko">
<head>
    <title>정직한플라워</title>
    <meta name="title" lang="ko" content="Flord">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="robots" content="noindex,nofollow">

    <link rel="stylesheet" href="{{ asset('adm/assets/css/common.css') }}?v={{ now()->format('YmdHis') }}">
    <link rel="stylesheet" href="{{ asset('adm/assets/css/layout.css') }}?v={{ now()->format('YmdHis') }}">
    <link rel="stylesheet" href="{{ asset('adm/assets/css/pages.css') }}?v={{ now()->format('YmdHis') }}">
    <link rel="stylesheet" href="{{ asset('adm/assets/css/pretendard.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="{{ asset('adm/assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('adm/assets/js/script.js') }}?v={{ now()->format('YmdHis') }}"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>

<div id="modal">
    <div id="ajax-modal"></div>
</div>

<header id="header">
    <div id="header-notice">
        ‘진짜’ 화원사를 위한 수발주 중개수수료 0% 인트라넷　본부 영업시간: 365일 연중무휴 09:00 ~ 19:00　현재 정상적으로 영업중입니다　<span class="color-danger">금일 포항/울산 근조화환 마감되었습니다　2025년 07월 01일 ~ 03일 까지 당일중 발주만 가능합니다</span>　
    </div>
    <div id="header-main">
        <div class="container">
            <div id="header__logo" class="header__col">
                <a href="{{ route('admin.index') }}"><img src="{{ asset('adm/assets/img/logo.png') }}" alt="logo"></a>
            </div>
            <nav id="header__nav" class="header__col">
                <ul>
                    <li class="{{ request()->routeIs('admin.real-time.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.real-time.index') }}">실시간발주</a>
                    </li>
                    <li class="{{ request()->routeIs('admin.all-order-list') ? 'active' : '' }}">
                        <a href="{{ route('admin.all-order-list') }}">전체수발주</a>
                    </li>
                    <li class="{{ request()->routeIs('admin.mediation-list') ? 'active' : '' }}">
                        <a href="{{ route('admin.mediation-list') }}">중개리스트</a>
                    </li>
                    <li class="{{ request()->routeIs('admin.notice.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.notice.index') }}">공지사항관리</a>
                    </li>
                    <li class="{{ request()->routeIs('admin.calculate-list') ? 'active' : '' }}">
                        <a href="{{ route('admin.calculate-list') }}">정산관리</a>
                    </li>
                    <li class="{{ request()->routeIs('admin.member-list') ? 'active' : '' }}">
                        <a href="{{ route('admin.member-list') }}">회원리스트</a>
                    </li>
                    <li class="{{ request()->routeIs('admin.banner-set.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.banner-set.index') }}">배너/팝업 관리</a>
                    </li>
                    <li><a href="#none">마케팅 관리</a></li>
                </ul>
            </nav>
            <div id="header__select" class="header__col">
                <div class="dropdown">
                    <button type="button">본부 수발주 사업부<em>arrow</em></button>
                    <div class="dropdown-content">
                        <a href="#none">마이페이지</a>
                        <a href="#none">로그아웃</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div id="body">
    <aside id="sidebar">
        <div class="row">
            <h2 class="row-title mt20 mb10">본부 수발주사업부</h2>
            <p class="row-desc">운영자님, 안녕하세요</p>
        </div>

        <div class="row">
            <ul>
                <li><a href="#none">실시간발주</a></li>
                <li><a href="#none">전체수발주</a></li>
                <li><a href="#none">중개리스트</a></li>
                <li><a href="#none">공지사항 관리</a></li>
                <li><a href="#none">배너/팝업 관리</a></li>
                <li><a href="#none">마케팅 관리</a></li>
                <li><a href="#none">정산관리</a></li>
                <li><a href="#none">회원리스트</a></li>
            </ul>
        </div>
    </aside>

    <div id="content">
        @yield('content')
    </div>
</div>

<footer id="footer">
    <div class="container">
        <div class="flex">
            <div class="flex__col">
                <img src="{{ asset('adm/assets/img/logo.png') }}" alt="로고" id="footer-logo">
                <address class="mt20">
                    주식회사 싱크플로
                    대표: 김도훈
                    사업자번호: 680-87-02988<br>
                    Copyright ⓒ 2026 주식회사 싱크플로, All rights reserved.<br>
                    본 사이트내의 모든 자료와 내용은 저작권법으로 보호되며 무단 사용을 금합니다.
                </address>
            </div>
            <div class="flex__col">
                <ul class="list-inline">
                    <li><a href="#none"><img src="{{ asset('adm/assets/img/sample.png') }}" height="140"></a></li>
                    <li><a href="#none"><img src="{{ asset('adm/assets/img/sample.png') }}" height="140"></a></li>
                    <li><a href="#none"><img src="{{ asset('adm/assets/img/sample.png') }}" height="140"></a></li>
                    <li><a href="#none"><img src="{{ asset('adm/assets/img/sample.png') }}" height="140"></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
@stack('styles')
@stack('scripts')
@include('partials.loading-modal')
