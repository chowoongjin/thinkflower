@extends('layouts.shop', ['title' => '영업용 쇼핑몰 목록'])

@push('styles')
    <style>
        #body { padding: 5px; }
    </style>
@endpush

@section('content')
    @include('pages.shop.partials.sub-header', ['active' => 'category'])

    <div id="shopList">
        <ul>
            @for ($i = 0; $i < 12; $i++)
                <li>
                    <a href="{{ route('shop-site.product') }}"><img src="{{ asset('assets/img/shop_sample1.png') }}" alt=""></a>
                </li>
            @endfor
        </ul>
    </div>

    @include('pages.shop.partials.footer')
@endsection
