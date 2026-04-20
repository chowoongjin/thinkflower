<div id="shoppingmall" class="main">
    <header id="shoppingmall__header">
        <div id="header-bar" class="main">
            <div id="shop-logo">
                <a href="{{ route('shop-site.home') }}">이화플라워</a>
            </div>
            <button type="button" id="shop-toggle"><img src="{{ asset('assets/img/shopToggle.png') }}" alt=""></button>
            <nav id="shop-nav" style="display:none;">
                <ul>
                    <li><a href="{{ route('shop-site.home') }}">이화플라워</a></li>
                </ul>
            </nav>
        </div>
        <img src="{{ asset('assets/img/shopHeader.png') }}" alt="">
    </header>
    @include('pages.shop.partials.main-nav', ['active' => $active ?? 'home'])
    <div id="body">
