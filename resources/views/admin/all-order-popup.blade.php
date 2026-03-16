@extends('layouts.AdminPopup')

@section('content')
    @php
        $createdAtText = optional($order->created_at)->format('Y-m-d H:i') ?: '';
        $acceptedAtText = optional($order->accepted_at)->format('Y-m-d H:i') ?: '';
        $ordererShopName = optional($order->ordererShop)->shop_name ?: '';
        $receiverShopName = optional($order->receiverShop)->shop_name ?: '본부수발주사업부';
        $ordererPhoneRaw = optional($order->ordererShop)->main_phone
            ?: '';

        $ordererPhone = preg_replace('/\D+/', '', $ordererPhoneRaw);

        if (strlen($ordererPhone) === 11) {
            $ordererPhone = preg_replace('/^(\d{3})(\d{4})(\d{4})$/', '$1-$2-$3', $ordererPhone);
        } elseif (strlen($ordererPhone) === 10) {
            if (str_starts_with($ordererPhone, '02')) {
                $ordererPhone = preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '$1-$2-$3', $ordererPhone);
            } else {
                $ordererPhone = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1-$2-$3', $ordererPhone);
            }
        }
        $recipientPhone = $order->recipient_phone ?: '';

        $deliveryDueText = '';
if ($order->delivery_date) {
    $deliveryDueText = \Carbon\Carbon::parse($order->delivery_date)->format('Y년 m월 d일');

    if ($order->delivery_hour !== null && $order->delivery_minute !== null) {
        $deliveryDueText .= ' ' . str_pad($order->delivery_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($order->delivery_minute, 2, '0', STR_PAD_LEFT);
    }

    if (!empty($order->delivery_time_type)) {
        $deliveryDueText .= ' ' . $order->delivery_time_type;
    }

    $deliveryDueText .= ' 까지';
}

        $statusLabel = '중개대기';
        if ($order->current_status === 'delivered') {
            $statusLabel = '배송완료';
        } elseif ($order->current_status === 'accepted' && $order->accepted_by_type === 'admin') {
            $statusLabel = '본부접수';
        } elseif ($order->current_status === 'accepted' && $order->accepted_by_type === 'shop') {
            $statusLabel = '주문접수';
        } else {
            $statusLabel = '중개대기';
        }

        $isTodayDelivery = $order->delivery_date
            ? \Carbon\Carbon::parse($order->delivery_date)->isSameDay(now())
            : false;
        $productImageUrl = $order->product_image_url ?: $order->product_image_path ?: null;
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
                        <button type="button"
                                class="btn btn-order-status-action {{ in_array($statusLabel, ['중개대기', '중개필요'], true) ? 'btn-purple' : '' }}"
                                data-action="brokerage">
                            중개대기
                        </button>
                        <button type="button"
                                class="btn btn-order-status-action {{ in_array($statusLabel, ['주문접수', '본부접수'], true) ? 'btn-purple' : '' }}"
                                data-action="accept">
                            주문접수
                        </button>
                        <button type="button"
                                class="btn btn-order-status-action {{ $statusLabel === '배송완료' ? 'btn-purple' : '' }}"
                                data-action="delivered">
                            배송완료처리
                        </button>
                        <button type="button"
                                class="btn btn-order-status-action"
                                data-action="cancel">
                            주문취소
                        </button>
                        <button type="button"
                                class="btn btn-order-status-action"
                                data-action="delete">
                            삭제
                        </button>
                    </div>
                </div>
            </div>

            <ul class="list-column-4" id="adminBtns">
                <li><button type="button" class="btn btn-print"><img src="{{ asset('adm/assets/img/ico_print2.png') }}" alt="print"> 주문인수증 인쇄</button></li>
                <li><button type="button" class="btn btn-cancel"><img src="{{ asset('adm/assets/img/ico_doc2.png') }}" alt="doc"> 주문취소처리</button></li>
                <li><button type="button" class="btn btn-del"><img src="{{ asset('adm/assets/img/ico_doc2.png') }}" alt="doc"> 주문서 삭제하기</button></li>
                <li>
                    <button type="button"
                            class="btn btn-suju"
                            id="btn-select-receiver-shop"
                            data-popup-url="{{ route('admin.member-list-popup', ['target' => 'receiver2']) }}"
                            data-order-status="{{ $order->current_status }}">
                        <img src="{{ asset('adm/assets/img/ico_link2.png') }}" alt="link"> 수주사 지정
                    </button>
                </li>
                <input type="hidden" id="popup-order-current-status" value="{{ $order->current_status }}">
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
                    <td colspan="3">
                        @if($productImageUrl)
                            <a href="#"
                               class="color-blue btn-product-image-modal"
                               data-image-url="{{ $productImageUrl }}">
                                상품이미지 보기
                            </a>
                        @else
                            상품이미지 없음
                        @endif
                    </td>
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
                <tr>
                    <th>카드메시지</th>
                    <td>
                        @if($order->card_message)
                            <span class="dotbox">{{ $order->card_message }}</span>
                        @endif
                    </td>
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
    <input type="hidden" id="popup-order-current-status" value="{{ $order->current_status }}">
    <input type="hidden" id="popup-order-receiver-shop-id" value="{{ $order->receiver_shop_id }}">

    <div id="productImageModal" style="display:none;">
        <div class="product-image-modal__dim"></div>
        <div class="product-image-modal__content">
            <img src="" alt="상품이미지" id="productImageModalImg">
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #productImageModal {
            position: fixed;
            inset: 0;
            z-index: 9999;
        }

        #productImageModal .product-image-modal__dim {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
        }

        #productImageModal .product-image-modal__content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90vw;
            max-height: 90vh;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-sizing: border-box;
        }

        #productImageModal .product-image-modal__content img {
            display: block;
            max-width: 100%;
            max-height: calc(90vh - 20px);
            height: auto;
            margin: 0 auto;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(function () {
            $(document).on('click', '.btn-product-image-modal', function (e) {
                e.preventDefault();

                const imageUrl = $(this).data('image-url');
                if (!imageUrl) return;

                $('#productImageModalImg').attr('src', imageUrl);
                $('#productImageModal').show();
            });

            $(document).on('click', '#productImageModal .product-image-modal__dim, #productImageModal img', function () {
                $('#productImageModal').hide();
                $('#productImageModalImg').attr('src', '');
            });

            $(document).on('click', '#btn-select-receiver-shop', function (e) {
                e.preventDefault();

                const currentStatus = $('#popup-order-current-status').val();
                const url = $(this).data('popup-url');

                if (currentStatus === 'accepted') {
                    alert('주문접수된 주문건은 수주사를 선택할 수 없습니다.');
                    return;
                }

                if (currentStatus === 'delivered') {
                    alert('완료된 주문건은 수주사를 선택할 수 없습니다.');
                    return;
                }

                if (!url) return;

                if (!confirm('수주사를 지정하시겠습니까?')) {
                    return;
                }

                window.open(
                    url,
                    'memberListPopup',
                    'width=1100,height=850,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
                );
            });

            $(document).on('click', '.btn-order-status-action', function (e) {
                e.preventDefault();

                if ($(this).hasClass('btn-purple')) {
                    return;
                }

                const action = $(this).data('action');
                const currentStatus = $('#popup-order-current-status').val();
                const receiverShopId = $('#popup-order-receiver-shop-id').val();

                if (action === 'accept') {
                    if (!receiverShopId || receiverShopId === '0') {
                        alert('수주사가 지정되지 않은 주문입니다.');
                        return;
                    }

                    if (currentStatus === 'delivered') {
                        alert('배송완료된 주문건 입니다.');
                        return;
                    }

                    if (!confirm('주문접수 하겠습니까?')) {
                        return;
                    }

                    $.ajax({
                        url: @json(route('admin.all-order-list.accept', $order)),
                        method: 'POST',
                        data: {
                            _token: @json(csrf_token())
                        },
                        success: function (res) {
                            alert(res.message || '주문접수 처리되었습니다.');
                            location.reload();

                            if (window.opener && !window.opener.closed) {
                                window.opener.location.reload();
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.message || '주문접수 처리에 실패했습니다.';
                            alert(msg);
                        }
                    });
                    return;
                }

                if (action === 'brokerage') {
                    if (currentStatus === 'delivered') {
                        alert('배송완료된 주문건 입니다.');
                        return;
                    }

                    if (currentStatus === 'accepted') {
                        if (!confirm('수주사를 재선정 하시겠습니까?')) {
                            return;
                        }

                        $.ajax({
                            url: @json(route('admin.all-order-list.reset-brokerage', $order)),
                            method: 'POST',
                            data: {
                                _token: @json(csrf_token())
                            },
                            success: function (res) {
                                alert(res.message || '중개대기로 변경되었습니다.');
                                location.reload();

                                if (window.opener && !window.opener.closed) {
                                    window.opener.location.reload();
                                }
                            },
                            error: function (xhr) {
                                const msg = xhr.responseJSON?.message || '중개대기 변경에 실패했습니다.';
                                alert(msg);
                            }
                        });
                        return;
                    }

                    alert('이미 중개대기 상태입니다.');
                    return;
                }

                if (action === 'delivered') {
                    alert('배송완료처리는 준비중입니다.');
                    return;
                }

                if (action === 'cancel') {
                    alert('주문취소 처리는 준비중입니다.');
                    return;
                }

                if (action === 'delete') {
                    alert('주문서 삭제는 준비중입니다.');
                    return;
                }
            });
        });

        window.setSelectedReceiverShop = function (shopId, shopName) {
            $.ajax({
                url: @json(route('admin.all-order-list.assign-receiver', $order)),
                method: 'POST',
                data: {
                    _token: @json(csrf_token()),
                    receiver_shop_id: shopId
                },
                success: function () {
                    alert('수주사가 지정되었습니다.');

                    location.reload();

                    if (window.opener && !window.opener.closed) {
                        window.opener.location.reload();
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    alert('수주사 지정에 실패했습니다.');
                }
            });
        };
    </script>
@endpush
