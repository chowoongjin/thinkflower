@extends('layouts.AdminPopup')

@section('content')
    @php
        $createdAtText = optional($order->created_at)->format('Y-m-d H:i') ?: '';
        $acceptedAtText = optional($order->accepted_at)->format('Y-m-d H:i') ?: '';
        $ordererShopName = optional($order->ordererShop)->shop_name ?: '';
        $receiverShopName = optional($order->receiverShop)->shop_name ?: '';
        $ordererPhone = optional($order->ordererShop)->phone ?: optional($order->ordererShop)->tel ?: '';
        $recipientPhone = $order->recipient_phone ?: '';

        $deliveryDueText = '';
        if ($order->delivery_date) {
            $deliveryDueText = \Carbon\Carbon::parse($order->delivery_date)->format('Y년 m월 d일');

            if ($order->delivery_hour !== null && $order->delivery_minute !== null) {
                $deliveryDueText .= ' ' . str_pad($order->delivery_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($order->delivery_minute, 2, '0', STR_PAD_LEFT) . ' 까지';
            }
        }

        $statusLabel = '중개대기';
        if ($order->current_status === 'delivered') {
            $statusLabel = '배송완료';
        } elseif ($order->current_status === 'accepted') {
            $statusLabel = '주문접수';
        } elseif ((int) ($order->receiver_shop_id ?? 0) === 0) {
            $statusLabel = '중개필요';
        } else {
            $statusLabel = '본부접수';
        }

        $isTodayDelivery = $order->delivery_date
            ? \Carbon\Carbon::parse($order->delivery_date)->isSameDay(now())
            : false;
    @endphp

    <div id="popup">
        <div class="popup__head">
            <h1 class="popup-title">주문정보</h1>
        </div>
        <div class="popup__body">
            <div class="headBox">
                <div class="flex">
                    <div class="flex__col">
                        <strong>✔️ 주문번호: <span class="color-purple">{{ $order->order_no }}</span></strong>
                    </div>
                    <div class="flex__col">
                        <button type="button" class="btn {{ $statusLabel === '중개대기' || $statusLabel === '중개필요' ? 'btn-purple' : '' }}">
                            {{ $statusLabel === '중개필요' ? '중개대기' : $statusLabel }}
                        </button>
                        <button type="button" class="btn {{ $statusLabel === '주문접수' ? 'btn-purple' : '' }}">주문접수</button>
                        <button type="button" class="btn {{ $statusLabel === '배송완료' ? 'btn-purple' : '' }}">배송완료처리</button>
                        <button type="button" class="btn">주문취소</button>
                        <button type="button" class="btn">삭제</button>
                    </div>
                </div>
            </div>

            <ul class="list-column-4" id="adminBtns">
                <li><button type="button" class="btn btn-print"><img src="{{ asset('adm/assets/img/ico_print2.png') }}" alt="print"> 주문인수증 인쇄</button></li>
                <li><button type="button" class="btn btn-cancel"><img src="{{ asset('adm/assets/img/ico_doc2.png') }}" alt="doc"> 주문취소처리</button></li>
                <li><button type="button" class="btn btn-del"><img src="{{ asset('adm/assets/img/ico_doc2.png') }}" alt="doc"> 주문서 삭제하기</button></li>
                <li><button type="button" class="btn btn-suju"><img src="{{ asset('adm/assets/img/ico_link2.png') }}" alt="link"> 수주사 지정</button></li>
            </ul>

            <table class="table-data style2-1">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>발주일시</th>
                    <td><span class="color-primary">{{ $createdAtText }}</span></td>
                    <th>접수일시</th>
                    <td>{{ $acceptedAtText }}</td>
                </tr>
                <tr>
                    <th>발주업체</th>
                    <td>{{ $ordererShopName }}</td>
                    <th>발주연락처</th>
                    <td>{{ $ordererPhone }}</td>
                </tr>
                <tr>
                    <th>수주업체</th>
                    <td colspan="3">{{ $receiverShopName }}</td>
                </tr>
                </tbody>
            </table>

            <table class="table-data style2-1 mt20">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>주문상품</th>
                    <td>{{ $order->product_name }}</td>
                    <th>발주금액</th>
                    <td><span class="color-primary">{{ number_format((int) $order->order_amount) }}원</span></td>
                </tr>
                <tr>
                    <th>상품이미지</th>
                    <td colspan="3">{{ $order->product_image_url ?: $order->product_image_path }}</td>
                </tr>
                <tr>
                    <th>배송요구일</th>
                    <td colspan="3">
                        <div class="inline-flex">
                            <span class="color-primary">{{ $deliveryDueText }}</span>
                            @if($isTodayDelivery)
                                <span class="img-inline">
                                    <img src="{{ asset('adm/assets/img/ico_siren.png') }}" height="20"><span class="color-red2">당일배송건</span>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>배송지주소</th>
                    <td colspan="3">{{ trim(($order->delivery_addr1 ?? '') . ' ' . ($order->delivery_addr2 ?? '')) }}</td>
                </tr>
                <tr>
                    <th>받는 분</th>
                    <td>{{ $order->recipient_name }}</td>
                    <th>연락처</th>
                    <td>{{ $recipientPhone }}</td>
                </tr>
                </tbody>
            </table>

            <table class="table-data style2-1 mt20">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>경조사어</th>
                    <td><span class="dotbox">{{ $order->ribbon_phrase ?: '' }}</span></td>
                </tr>
                <tr>
                    <th>보내는분</th>
                    <td><span class="dotbox">{{ $order->sender_name ?: '' }}</span></td>
                </tr>
                </tbody>
            </table>

            <h3 class="mt20 fs16 mb10">요청사항</h3>
            <table class="table-data style2-1">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>요청사항</th>
                    <td>{{ $order->request_note ?: ($order->request_photo ? '★현장사진 요청' : '') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
