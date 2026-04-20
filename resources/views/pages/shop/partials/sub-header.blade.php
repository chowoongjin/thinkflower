<div id="shoppingmall" class="sub">
    <header id="shoppingmall__header">
        <div id="header-bar">
            <button type="button" id="shop-back" onclick="history.back();"><img src="{{ asset('assets/img/shop_arrow_left.png') }}" alt=""></button>

            <div id="shop-logo">
                <a href="{{ route('shop-site.home') }}"><i class="bi bi-house"></i> 이화플라워</a>
            </div>

            <nav id="shop-nav" style="display:none;">
                <ul>
                    <li><a href="{{ route('shop-site.home') }}">이화플라워</a></li>
                </ul>
            </nav>
        </div>
    </header>
    @include('pages.shop.partials.main-nav', ['active' => $active ?? 'category'])
    <div id="body">
