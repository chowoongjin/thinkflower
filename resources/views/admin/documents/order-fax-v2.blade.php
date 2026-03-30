@php
    $formatPhone = function (?string $value): string {
        $digits = preg_replace('/[^0-9]/', '', (string) $value);

        if ($digits === '') {
            return '-';
        }

        if (strlen($digits) === 11) {
            return preg_replace('/(\d{3})(\d{4})(\d{4})/', '$1-$2-$3', $digits);
        }

        if (strlen($digits) === 10) {
            if (str_starts_with($digits, '02')) {
                return preg_replace('/(\d{2})(\d{4})(\d{4})/', '$1-$2-$3', $digits);
            }

            return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $digits);
        }

        if (strlen($digits) === 9 && str_starts_with($digits, '02')) {
            return preg_replace('/(\d{2})(\d{3})(\d{4})/', '$1-$2-$3', $digits);
        }

        return $digits;
    };

    $weekdayMap = ['Sun' => '일', 'Mon' => '월', 'Tue' => '화', 'Wed' => '수', 'Thu' => '목', 'Fri' => '금', 'Sat' => '토'];
    $deliveryDate = $order->delivery_date;
    $weekdayKey = $deliveryDate?->format('D');
    $weekdayLabel = $weekdayMap[$weekdayKey] ?? '';

    $hour = $order->delivery_hour;
    $minute = $order->delivery_minute;
    $deliveryTime = ($hour !== null && $minute !== null)
        ? sprintf('%02d:%02d', (int) $hour, (int) $minute)
        : '미정';

    $deliveryAddress = trim(implode(' ', array_filter([
        $order->delivery_addr1,
        $order->delivery_addr2,
    ])));
    $resolvedFontFamily = $fontFamily ?? 'NotoSansKR';
@endphp
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        @if($fontRegular && $fontBold)
        @font-face {
            font-family: '{{ $resolvedFontFamily }}';
            src: url('{{ $fontRegular }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: '{{ $resolvedFontFamily }}';
            src: url('{{ $fontBold }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        @endif

        * {
            box-sizing: border-box;
        }

        body {
            font-family: {{ $fontRegular && $fontBold ? "'{$resolvedFontFamily}', sans-serif" : "'DejaVu Sans', sans-serif" }};
            color: #111827;
            font-size: 12px;
            line-height: 1.45;
            margin: 0;
        }

        .page {
            width: 194mm;
            margin: 0 auto;
            padding: 6mm 0 5mm;
        }

        .topbar,
        .head-row {
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .topbar > div,
        .head-row > div {
            display: table-cell;
            vertical-align: middle;
        }

        .topbar .right,
        .head-row .right {
            text-align: right;
        }

        .order-no {
            font-size: 20px;
            font-weight: 700;
        }

        .primary {
            color: #0f766e;
        }

        .notice {
            margin-top: 12px;
            padding: 8px 10px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }

        .fax-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .fax-table th,
        .fax-table td {
            border: 1px solid #111827;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .fax-table th {
            width: 20%;
            background: #f3f4f6;
            text-align: center;
            font-weight: 700;
        }

        .sub-th {
            width: 12%;
        }

        .center {
            text-align: center;
        }

        .strong {
            font-weight: 700;
        }

        .cut-line {
            margin: 0 0 8px;
            border-top: 1px dashed #111827;
            text-align: center;
            position: relative;
        }

        .cut-line span {
            display: inline-block;
            margin-top: -10px;
            background: #ffffff;
            padding: 0 12px;
            font-weight: 700;
            letter-spacing: 4px;
        }

        .placeholder {
            color: #9ca3af;
        }

        .slip-section .fax-table:first-of-type {
            margin-top: 0;
        }

        .slip-spacer {
            height: 38mm;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="top-section">
            <div class="topbar">
                <div><span class="strong">주문번호: {{ $order->order_no }}</span> 본부문의: <span class="primary strong">1668-1840</span></div>
                <div class="right">주문시간: {{ $order->created_at?->format('Y년 m월 d일 H:i') }}</div>
            </div>

            <div class="head-row" style="margin-top: 18px;">
                <div>
                    @if($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="정직한플라워" style="height: 40px;">
                    @endif
                </div>
                <div class="right">
                    <span class="order-no">주문번호: <span class="primary">{{ $order->order_no }}</span></span>
                </div>
            </div>

            <div class="notice">
                주문서 수신 후 발주처와 통화 및 주문접수, 빠른 배송 처리 부탁드립니다.
            </div>

            <table class="fax-table">
                <tbody>
                    <tr>
                        <th rowspan="2">발주화원</th>
                        <td rowspan="2">{{ $ordererDisplayName }}</td>
                        <th class="sub-th">연락처</th>
                        <td class="center">{{ $formatPhone(optional($ordererShop)->main_phone) }}</td>
                    </tr>
                    <tr>
                        <th class="sub-th">팩스번호</th>
                        <td class="center">{{ $formatPhone(optional($ordererShop)->fax) }}</td>
                    </tr>
                    <tr>
                        <th rowspan="2">수주화원</th>
                        <td rowspan="2">{{ $receiverDisplayName }}</td>
                        <th class="sub-th">연락처</th>
                        <td class="center">{{ $formatPhone($receiverShop->main_phone) }}</td>
                    </tr>
                    <tr>
                        <th class="sub-th">팩스번호</th>
                        <td class="center">{{ $formatPhone($receiverShop->fax) }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="fax-table">
                <tbody>
                    <tr>
                        <th>배달일시</th>
                        <td colspan="3" class="strong">
                            {{ $deliveryDate?->format('Y년 m월 d일') }}
                            @if($weekdayLabel)
                                ({{ $weekdayLabel }})
                            @endif
                            {{ $deliveryTime }}
                            {{ $order->delivery_time_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>배달장소</th>
                        <td colspan="3">{{ $deliveryAddress }}</td>
                    </tr>
                    <tr>
                        <th>상품정보</th>
                        <td>{{ $order->product_name }} / {{ $order->product_detail }}</td>
                        <th class="sub-th">금액</th>
                        <td class="center">{{ number_format((int) $order->order_amount) }}원</td>
                    </tr>
                </tbody>
            </table>

            <table class="fax-table">
                <tbody>
                    <tr>
                        <th>경조사어</th>
                        <td class="strong">{{ $order->ribbon_phrase }}</td>
                    </tr>
                    <tr>
                        <th>보내는 분</th>
                        <td class="strong">{{ $order->sender_name }}</td>
                    </tr>
                    <tr>
                        <th>요청사항</th>
                        <td class="strong">{{ $order->request_note }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="slip-spacer"></div>

        <div class="slip-section">
            <div class="cut-line"><span>절 취 선</span></div>

            <table class="fax-table">
                <tbody>
                    <tr>
                        <th>배송화원</th>
                        <td>{{ $receiverDisplayName }}</td>
                        <th class="sub-th">연락처</th>
                        <td class="center">{{ $formatPhone($receiverShop->main_phone) }}</td>
                    </tr>
                    <tr>
                        <th>배달일시</th>
                        <td colspan="3">
                            {{ $deliveryDate?->format('Y년 m월 d일') }}
                            @if($weekdayLabel)
                                ({{ $weekdayLabel }})
                            @endif
                            {{ $deliveryTime }}
                            {{ $order->delivery_time_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>배달장소</th>
                        <td colspan="3">{{ $deliveryAddress }}</td>
                    </tr>
                    <tr>
                        <th>상품정보</th>
                        <td colspan="3">{{ $order->product_name }} / {{ $order->product_detail }}</td>
                    </tr>
                    <tr>
                        <th>경조사어</th>
                        <td colspan="3" class="strong">{{ $order->ribbon_phrase }}</td>
                    </tr>
                    <tr>
                        <th>보내는 분</th>
                        <td colspan="3" class="strong">{{ $order->sender_name }}</td>
                    </tr>
                    <tr>
                        <th>요청사항</th>
                        <td colspan="3" class="strong">{{ $order->request_note }}</td>
                    </tr>
                    <tr>
                        <th>인수자</th>
                        <td><span class="placeholder">이곳에 이름을 입력하세요</span></td>
                        <th class="sub-th">관계</th>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
