@include('partials.header')

@yield('content')

@stack('styles')
@stack('scripts')

@include('partials.footer')
