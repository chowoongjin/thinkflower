@include('partials.header-sub')

@yield('content')
@stack('styles')
@stack('scripts')
@include('partials.footer-sub')
