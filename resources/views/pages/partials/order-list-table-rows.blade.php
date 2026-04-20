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
        $photoCount = (int) ($order->photos_count ?? 0);

        if ($order->current_status === 'accepted' && $order->accepted_by_type === 'admin') {
            $statusLabel = '본부접수';
            $statusClass = '';
        } elseif ($order->current_status === 'accepted' && $order->accepted_by_type === 'shop') {
            $statusLabel = '주문접수';
            $statusClass = '';
        } else {
            $statusLabel = '중개대기';
            $statusClass = 'color-red';
        }
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

            @php
                $deliveryDateText = optional($order->delivery_date)->format('Y/m/d');
                $deliveryTimeText = '';

                if ($order->delivery_date && $order->delivery_hour !== null && $order->delivery_minute !== null) {
                    $deliveryAt = \Carbon\Carbon::create(
                        $order->delivery_date->format('Y'),
                        $order->delivery_date->format('m'),
                        $order->delivery_date->format('d'),
                        (int) $order->delivery_hour,
                        (int) $order->delivery_minute,
                        0
                    );

                    $now = now();
                    $threeHoursLater = $now->copy()->addHours(3);

                    if ($deliveryAt->lte($threeHoursLater)) {
                        $deliveryTimeText = '지금즉시';
                    } else {
                        $deliveryTimeText = $deliveryAt->format('H:i');
                    }
                }
            @endphp

            {{ $deliveryDateText }}
            @if($deliveryTimeText !== '')
                <span class="color-orange">{{ $deliveryTimeText }}</span>
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
            @if($photoCount >= 3)
                <a href="{{ route('order-list.photo-popup', $order) }}"
                   class="order-photo-popup-link"
                   data-popup-url="{{ route('order-list.photo-popup', $order) }}">
                    <img src="{{ asset('assets/img/ico_photo_on.png') }}" height="18">
                </a>
            @elseif($order->current_status === 'delivered' || $photoCount >= 1)
                <a href="{{ route('order-list.complete-popup', $order) }}"
                   class="order-photo-popup-link"
                   data-popup-url="{{ route('order-list.complete-popup', $order) }}">
                    <img src="{{ asset('assets/img/ico_photo_on.png') }}" height="18">
                </a>
            @else
                <button type="button">
                    <img src="{{ asset('assets/img/ico_photo_off.png') }}" height="18">
                </button>
            @endif
        </td>
        <td class="fs13">
            @if ($order->current_status === 'delivered')
                {{ $order->receiver_name ?: '미입력' }}
                @if (!empty($order->receiver_relation))
                    <br>
                    <span class="color-gray300">{{ $order->receiver_relation }}</span>
                @endif
            @elseif($statusClass)
                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
            @else
                <span>{{ $statusLabel }}</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="align-center">등록된 발주내역이 없습니다.</td>
    </tr>
@endforelse
