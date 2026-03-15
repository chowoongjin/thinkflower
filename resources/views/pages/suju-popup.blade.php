@extends('layouts.popup')

@section('content')
    @php
        $formatPhone = function ($phone) {
            $digits = preg_replace('/\D+/', '', (string) $phone);

            if ($digits === '') {
                return '-';
            }

            if (str_starts_with($digits, '02')) {
                if (strlen($digits) === 9) {
                    return preg_replace('/(\d{2})(\d{3})(\d{4})/', '$1-$2-$3', $digits);
                }
                if (strlen($digits) === 10) {
                    return preg_replace('/(\d{2})(\d{4})(\d{4})/', '$1-$2-$3', $digits);
                }
            }

            if (strlen($digits) === 8) {
                return preg_replace('/(\d{4})(\d{4})/', '$1-$2', $digits);
            }

            if (strlen($digits) === 10) {
                return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $digits);
            }

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{3})(\d{4})(\d{4})/', '$1-$2-$3', $digits);
            }

            return $phone;
        };

        $ordererPhone = $formatPhone(optional($order->ordererShop)->main_phone);
        $receiverPhoneRaw = optional($order->receiverShop)->main_phone;
        $receiverPhone = $receiverPhoneRaw ? $formatPhone($receiverPhoneRaw) : '1688-1840';

        $deliveryDateText = optional($order->delivery_date)->format('Y년 m월 d일');
        $deliveryTimeText = '';

        if ($order->delivery_hour !== null && $order->delivery_minute !== null) {
            $deliveryTimeText = str_pad($order->delivery_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($order->delivery_minute, 2, '0', STR_PAD_LEFT);
        }

        $isSameDay = optional($order->delivery_date)->format('Y-m-d') === now()->format('Y-m-d');
        $fullAddress = trim(($order->delivery_addr1 ?? '') . ' ' . ($order->delivery_addr2 ?? ''));
    @endphp
    @if (session('success') || session('error'))
        <script>
            alert(@json(session('success') ?? session('error')));
        </script>
    @endif

    <div id="popup">
        <div class="popup__head">
            <h1 class="popup-title">주문정보</h1>
        </div>

        <div class="popup__body">
            <div class="headBox">
                <div class="flex">
                    <div class="flex__col">
                        <strong>
                            ✔️ 주문번호:
                            <span class="color-purple">{{ $order->order_no }}</span>
                            | 본부문의 :
                            <span class="color-purple">1688-1840</span>
                        </strong>
                    </div>
                    @php
                        $actionLocked = $order->current_status === 'accepted' || $order->receiver_shop_id === null;
                    @endphp

                    <div class="flex__col">
                        @if (!$actionLocked)
                            <form method="POST" action="{{ route('suju-list.accept', $order) }}" style="display:inline-block;">
                                @csrf
                                <button
                                    type="submit"
                                    class="btn"
                                    onclick="return confirm('주문을 접수하시겠습니까?');"
                                >
                                    주문을 접수합니다
                                </button>
                            </form>

                            <form method="POST" action="{{ route('suju-list.reject', $order) }}" style="display:inline-block;">
                                @csrf
                                <button
                                    type="submit"
                                    class="btn"
                                    onclick="return confirm('주문을 거절하시겠습니까?');"
                                >
                                    주문을 거절합니다
                                </button>
                            </form>
                        @else
                            <span class="color-gray900 fs15">처리 완료된 주문입니다.</span>
                        @endif
                    </div>
                </div>
            </div>

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
                    <td><span class="color-primary">{{ optional($order->created_at)->format('Y-m-d H:i') }}</span></td>
                    <th>접수일시</th>
                    <td>{{ optional($order->accepted_at)->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <th>발주업체</th>
                    <td>{{ optional($order->ordererShop)->shop_name ?? '-' }}</td>
                    <th>발주연락처</th>
                    <td>{{ $ordererPhone }}</td>
                </tr>
                <tr>
                    <th>수주업체</th>
                    <td>{{ optional($order->receiverShop)->shop_name ?: '본부수발주사업부' }}</td>
                    <th>수주연락처</th>
                    <td>{{ $receiverPhone }}</td>
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
                    <td><span class="color-primary">{{ number_format($order->order_amount) }}원</span></td>
                </tr>
                <tr>
                    <th>상품이미지</th>
                    <td colspan="3">
                        @if($order->product_image_url)
                            <a href="javascript:void(0);"
                               class="color-purple js-open-product-image"
                               data-image-url="{{ $order->product_image_url }}">
                                상품이미지 보기
                            </a>
                        @elseif($order->product_image_path)
                            <a href="javascript:void(0);"
                               class="color-purple js-open-product-image"
                               data-image-url="{{ $order->product_image_path }}">
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
                            <span class="color-primary">
                                {{ $deliveryDateText }}
                                @if($deliveryTimeText) {{ $deliveryTimeText }} @endif
                                @if($order->delivery_time_type) {{ $order->delivery_time_type }} @endif
                                까지
                            </span>

                            @if($isSameDay)
                                <span class="img-inline">
                                    <img src="{{ asset('assets/img/ico_siren.png') }}" height="20">
                                    <span class="color-red2">당일배송건</span>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>배송지주소</th>
                    <td colspan="3">{{ $fullAddress }}</td>
                </tr>
                <tr>
                    <th>받는 분</th>
                    <td>{{ $order->recipient_name }}</td>
                    <th>연락처</th>
                    <td>{{ $formatPhone($order->recipient_phone) }}</td>
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
                    <td>
                        @if($order->ribbon_phrase)
                            <span class="dotbox">{{ $order->ribbon_phrase }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>보내는분</th>
                    <td>
                        @if($order->sender_name)
                            <span class="dotbox">{{ $order->sender_name }}</span>
                        @endif
                    </td>
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
                    <td>{{ $order->request_note ?: '-' }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="productImageModal" class="product-image-modal" style="display:none;">
        <div class="product-image-modal__backdrop"></div>
        <div class="product-image-modal__dialog">
            <img src="" alt="상품이미지" id="productImageModalImg">
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-image-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
        }

        .product-image-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            cursor: pointer;
        }

        .product-image-modal__dialog {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70vw;
            height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .product-image-modal__dialog img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            background: #fff;
            cursor: pointer;
            pointer-events: auto;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function () {
            function closeProductImageModal() {
                $('#productImageModal').hide();
                $('#productImageModalImg').attr('src', '');
            }

            $(document).on('click', '.js-open-product-image', function (e) {
                e.preventDefault();

                const imageUrl = $(this).data('image-url');
                if (!imageUrl) return;

                $('#productImageModalImg').attr('src', imageUrl);
                $('#productImageModal').show();
            });

            $(document).on('click', '#productImageModal .product-image-modal__backdrop', function () {
                closeProductImageModal();
            });

            $(document).on('click', '#productImageModalImg', function () {
                closeProductImageModal();
            });
        });
    </script>
@endpush
