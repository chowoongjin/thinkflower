@extends('layouts.popup')

@section('content')
    @php
        $photoShop = $photos->first(function ($photo) {
            return $photo->photo_type === 'other' && (int) $photo->sort_order === 1;
        });

        $photoSite = $photos->first(function ($photo) {
            return $photo->photo_type === 'delivery_site';
        });

        $photoExtra = $photos->first(function ($photo) {
            return $photo->photo_type === 'other' && (int) $photo->sort_order === 3;
        });

        $formatMoney = fn ($amount) => number_format((int) $amount) . '원';
        $fullAddress = trim(($order->delivery_addr1 ?? '') . ' ' . ($order->delivery_addr2 ?? ''));
        $completedAt = optional($order->delivered_at)->format('Y-m-d H:i');
        $receiverInfo = trim(($order->receiver_name ?? '') . (($order->receiver_relation ?? '') ? ' / ' . $order->receiver_relation : ''));
    @endphp

    <div id="popup">
        <div class="popup__head">
            <h1 class="popup-title">주문정보</h1>
        </div>
        <div class="popup__body">
            <div class="headBox">
                <div class="flex">
                    <div class="flex__col">
                        <strong>✔️ 주문번호: <span class="color-purple">{{ $order->order_no }}</span> | 본부문의 : <span class="color-purple">1688-1840</span></strong>
                    </div>
                    <div class="flex__col">
                        <span class="color-gray900 fs15">본부문의 시 주문번호를 불러주세요</span>
                    </div>
                </div>
            </div>

            <h3 class="mt20 fs16 mb10">주문정보조회</h3>
            <table class="table-data style2-1">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>주문상품</th>
                    <td>
                        <span class="color-primary">{{ $order->product_name }}</span>
                        {{ $formatMoney($order->order_amount) }}
                    </td>
                </tr>
                <tr>
                    <th>배송지주소</th>
                    <td>{{ $fullAddress ?: '-' }}</td>
                </tr>
                <tr>
                    <th>경조사어</th>
                    <td>{{ $order->ribbon_phrase ?: '-' }}</td>
                </tr>
                <tr>
                    <th>보내는분</th>
                    <td>{{ $order->sender_name ?: '-' }}</td>
                </tr>
                </tbody>
            </table>

            <h3 class="mt20 fs16 mb10">배송 사진 현황</h3>

            <div class="photoBoxWrap">
                <div class="photoBox">
                    <h3>매장사진</h3>
                    <div class="photoBox__content">
                        @if(!empty($photoShop?->file_path))
                            <img src="{{ $photoShop->file_path }}" alt="매장사진">
                        @endif
                    </div>
                </div>
                <div class="photoBox">
                    <h3>현장사진</h3>
                    <div class="photoBox__content">
                        @if(!empty($photoSite?->file_path))
                            <img src="{{ $photoSite->file_path }}" alt="현장사진">
                        @endif
                    </div>
                </div>
                <div class="photoBox">
                    <h3>추가사진</h3>
                    <div class="photoBox__content">
                        @if(!empty($photoExtra?->file_path))
                            <img src="{{ $photoExtra->file_path }}" alt="추가사진">
                        @endif
                    </div>
                </div>
            </div>

            <table class="table-data style2-1 mt20">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>완료시간</th>
                    <td>{{ $completedAt ?: '' }}</td>
                </tr>
                <tr>
                    <th>인수자/관계</th>
                    <td>{{ $receiverInfo ?: '' }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .photoBox__content img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
    </style>
@endpush
