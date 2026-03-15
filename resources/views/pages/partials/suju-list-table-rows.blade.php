@php
    $today = now()->format('Y-m-d');
@endphp

@forelse ($orders as $order)
    @php
        $deliveryDate = optional($order->delivery_date)->format('Y-m-d');
        $trClass = '';

        if ($order->current_status !== 'delivered' && $deliveryDate) {
            if ($deliveryDate === $today) {
                $trClass = 'tr-warning';
            } elseif ($deliveryDate > $today) {
                $trClass = 'tr-primary';
            } else {
                $trClass = 'tr-warning';
            }
        }

        $createdText = optional($order->created_at)->format('Y/m/d H:i') ?: '-';
        $deliveryText = optional($order->delivery_date)->format('Y/m/d') ?: '-';

        $deliveryTypeText = $order->is_urgent
            ? '지금즉시'
            : ($order->delivery_time_type ?? '');

        $deliveryStatus = $order->delivered_at
            ? ($order->receiver_relation ?: ($order->receiver_name ?: '완료'))
            : null;

        $hasPhoto = (int) ($order->photos_count ?? 0) > 0;
    @endphp

    <tr class="{{ $trClass }}">
        <td class="no-ellipsis">
            <a href="{{ route('suju-list.popup', $order->order_no) }}"
               class="color-blue order-popup-link"
               data-popup-url="{{ route('suju-list.popup', $order->order_no) }}">
                {{ $order->order_no }}
            </a>
        </td>

        <td>
            <span class="color-gray300">{{ $createdText }}</span><br>
            {{ $deliveryText }}
            @if($deliveryTypeText)
                <span class="color-orange">{{ $deliveryTypeText }}</span>
            @endif
        </td>

        <td>
            <span class="color-gray300">{{ optional($order->receiverShop)->shop_name ?: '미입력' }}</span><br>
            {{ optional($order->ordererShop)->shop_name ?: '본부수발주사업부' }}
        </td>

        <td>
            <span class="color-gray300">{{ \Illuminate\Support\Str::limit($order->ribbon_phrase ?: '미입력', 20) }}</span><br>
            {{ \Illuminate\Support\Str::limit($order->delivery_addr1, 24) }}
        </td>

        <td>
            <span class="color-gray300">{{ $order->receiver_name ?: '미입력' }}</span><br>
            {{ $order->recipient_name }}
        </td>

        <td>
            <span class="color-gray300">{{ $order->product_name }}</span><br>
            <b class="color-green">{{ $order->product_detail }}</b>
        </td>

        <td>
            {{ number_format((int) $order->original_amount) }}원<br>
            <b class="color-green">{{ number_format((int) $order->order_amount) }}원</b>
        </td>

        <td>
            <button
                type="button"
                class="btn-order-history-modal"
                data-history-url="{{ route('suju-list.history-modal', $order->order_no) }}"
            >
                <img src="{{ asset('assets/img/ico_doc.png') }}" height="18">
            </button>
        </td>

        <td>
            @if($hasPhoto)
                <a href="{{ route('suju-list.photo-popup', $order) }}"
                   class="order-photo-popup-link"
                   data-popup-url="{{ route('suju-list.photo-popup', $order) }}">
                    <img src="{{ asset('assets/img/ico_photo_on.png') }}" height="18">
                </a>
            @else
                <button type="button">
                    <img src="{{ asset('assets/img/ico_photo_off.png') }}" height="18">
                </button>
            @endif
        </td>

        <td class="fs13">
            @if($deliveryStatus)
                <span>{{ $deliveryStatus }}</span>
            @else
                <a href="{{ route('suju-list.complete-popup', $order->order_no) }}"
                   class="btn btn-orange order-complete-popup-link"
                   data-popup-url="{{ route('suju-list.complete-popup', $order->order_no) }}">
                    등록
                </a>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="align-center">수주 내역이 없습니다.</td>
    </tr>
@endforelse
