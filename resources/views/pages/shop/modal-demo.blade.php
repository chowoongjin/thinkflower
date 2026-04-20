@extends('layouts.shop', ['title' => '영업용 쇼핑몰 모달'])

@section('content')
    @include('pages.shop.partials.sub-header', ['active' => 'category'])

    <button type="button" class="btn" onclick="modal('{{ route('shop-site.modals.reservation') }}');">예약정보 확인</button>
    <button type="button" class="btn" onclick="modal('{{ route('shop-site.modals.stock-request') }}');">재고확인 요청</button>

    @include('pages.shop.partials.footer')
@endsection
