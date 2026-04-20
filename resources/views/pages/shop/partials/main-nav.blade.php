@php
    $active = $active ?? 'home';
    $currentCategory = request('category');
@endphp
<nav id="main-nav">
    <ul>
        <li class="{{ $active === 'home' ? 'active' : '' }}"><a href="{{ route('shop-site.home') }}">홈</a></li>
        <li class="{{ $active === 'category' && $currentCategory === '졸업꽃다발' ? 'active' : '' }}">
            <a href="{{ route('shop-site.list', ['category' => '졸업꽃다발']) }}">졸업꽃다발</a>
        </li>
        <li class="{{ $active === 'category' && $currentCategory === '꽃바구니' ? 'active' : '' }}">
            <a href="{{ route('shop-site.list', ['category' => '꽃바구니']) }}">꽃바구니</a>
        </li>
        <li class="{{ $active === 'category' && $currentCategory === '근조화환' ? 'active' : '' }}">
            <a href="{{ route('shop-site.list', ['category' => '근조화환']) }}">근조화환</a>
        </li>
        <li class="{{ $active === 'category' && $currentCategory === '축하화환' ? 'active' : '' }}">
            <a href="{{ route('shop-site.list', ['category' => '축하화환']) }}">축화화한</a>
        </li>
    </ul>
</nav>
