@forelse ($orders as $order)
    @php
        $today = now()->format('Y-m-d');
        $deliveryDate = optional($order->delivery_date)->format('Y-m-d');
        $rowClass = '';

        if ($order->current_status !== 'delivered' && $deliveryDate) {
            if ($deliveryDate === $today) {
                $rowClass = 'tr-warning';
            } elseif ($deliveryDate > $today) {
                $rowClass = 'tr-primary';
            } else {
                $rowClass = 'tr-warning';
            }
        }

        $receiverShopName = optional($order->receiverShop)->shop_name ?: '본부수발주사업부';
        $hasPhoto = (int) ($order->photos_count ?? 0) > 0;
    @endphp

    <tr class="{{ $rowClass }}">
        <td class="no-ellipsis">
            <a href="{{ route('order-list.popup', $order->order_no) }}"
               class="color-blue order-popup-link"
               data-popup-url="{{ route('order-list.popup', $order->order_no) }}">
                {{ $order->order_no }}
            </a>
        </td>
        <td>
            <span class="color-gray300">{{ optional($order->created_at)->format('Y/m/d H:i') }}</span><br>
            {{ optional($order->delivery_date)->format('Y/m/d') }}
            @if($order->delivery_hour !== null && $order->delivery_minute !== null)
                <span class="color-orange">{{ str_pad($order->delivery_hour, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($order->delivery_minute, 2, '0', STR_PAD_LEFT) }}</span>
            @endif
        </td>
        <td>
            <span class="color-gray300">{{ optional($order->ordererShop)->shop_name ?? '-' }}</span><br>
            {{ $receiverShopName }}
        </td>
        <td>
            <span class="color-gray300">{{ $order->ribbon_phrase }}</span><br>
            {{ $order->delivery_addr1 }}
        </td>
        <td>
            <span class="color-gray300">미입력</span><br>
            {{ $order->recipient_name }}
        </td>
        <td>
            <span class="color-gray300">{{ $order->product_name }}</span><br>
            <b class="color-green">{{ $order->product_detail }}</b>
        </td>
        <td>
            {{ number_format($order->original_amount) }}원<br>
            <b class="color-green">{{ number_format($order->order_amount) }}원</b>
        </td>
        <td>
            <button type="button"
                    class="btn-order-history-modal"
                    data-history-url="{{ route('order-list.history-modal', $order) }}">
                <img src="{{ asset('assets/img/ico_doc.png') }}" height="18">
            </button>
        </td>
        <td>
            @if($hasPhoto)
                <a href="{{ route('order-list.photo-popup', $order) }}"
                   class="order-photo-popup-link"
                   data-popup-url="{{ route('order-list.photo-popup', $order) }}">
                    <img src="{{ asset('assets/img/ico_photo_on.png') }}" height="18">
                </a>
            @else
                <button type="button">
                    <img src="{{ asset('assets/img/ico_photo_off.png') }}" height="18">
                </button>
            @endif
        </td>
        <td class="fs13">
            @if($order->brokerage_type === 'waiting')
                <span class="color-red">중개대기</span>
            @else
                <span>{{ $order->brokerage_type }}</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="align-center">등록된 발주내역이 없습니다.</td>
    </tr>
@endforelse
