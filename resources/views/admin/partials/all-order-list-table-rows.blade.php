@forelse ($orders as $order)
    @php
        $today = now()->format('Y-m-d');
        $deliveryDate = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : null;

        $trClass = '';
        if ($order->current_status === 'delivered') {
            $trClass = '';
        } elseif ($deliveryDate) {
            if ($deliveryDate > $today) {
                $trClass = 'tr-primary';
            } elseif ($deliveryDate === $today) {
                $trClass = 'tr-warning';
            }
        }

        $statusSelectClass = '';
        if ($order->current_status === 'delivered') {
            $statusSelectClass = 'success';
        } elseif ((int) ($order->receiver_shop_id ?? 0) === 0) {
            $statusSelectClass = 'active';
        }

        $statusLabel = '본부접수';

        if ($order->current_status === 'delivered') {
            $statusLabel = '배송완료';
        } elseif ((int) ($order->receiver_shop_id ?? 0) === 0) {
            $statusLabel = '중개필요';
        } elseif ($order->current_status === 'accepted') {
            $statusLabel = '주문접수';
        } elseif ($order->order_type === 'direct') {
            $statusLabel = '주문접수';
        }

        $ordererName = $order->ordererShop->shop_name ?? '-';
        $receiverName = $order->receiverShop->shop_name ?? '본부수발주사업부';
        $hasPhoto = ($order->photos_count ?? 0) > 0;

        $deliveryDateText = '-';
        $deliveryTimeText = '-';

        if ($order->delivery_date) {
            $deliveryDateCarbon = \Carbon\Carbon::parse($order->delivery_date);
            $deliveryDateText = $deliveryDateCarbon->format('Y/m/d');

            if ($order->delivery_hour !== null && $order->delivery_minute !== null) {
                $deliveryAt = \Carbon\Carbon::create(
                    $deliveryDateCarbon->year,
                    $deliveryDateCarbon->month,
                    $deliveryDateCarbon->day,
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
        }
    @endphp

    <tr class="{{ $trClass }}">
        <td class="no-ellipsis">
            <button
                type="button"
                class="btn-order-popup"
                data-popup-url="{{ route('admin.all-order-list.popup', $order) }}"
            >
                <span class="color-blue">{{ $order->order_no }}</span>
            </button>
        </td>
        <td>
            <span class="color-gray300">{{ optional($order->created_at)->format('Y/m/d H:i') }}</span><br>
            {{ $deliveryDateText }}
            <span class="color-orange">{{ $deliveryTimeText }}</span>
        </td>
        <td>
            <span class="color-gray300">{{ $ordererName }}</span><br>
            {{ $receiverName }}
        </td>
        <td>
            <span class="color-gray300">{{ \Illuminate\Support\Str::limit($order->sender_name ?? '-', 20) }}</span><br>
            {{ \Illuminate\Support\Str::limit($order->delivery_addr1 ?? '-', 25) }}
        </td>
        <td>{{ $order->recipient_name }}</td>
        <td>
            <span class="color-gray300">{{ $order->product_name }}</span><br>
            <b class="color-green">{{ $order->product_detail }}</b>
        </td>
        <td>
            {{ number_format((int) $order->original_amount) }}원<br>
            <b class="color-green">{{ number_format((int) $order->order_amount) }}원</b>
        </td>
        <td class="align-center">
            <select name="" class="select {{ $statusSelectClass }}">
                <option {{ $statusLabel === '주문접수' ? 'selected' : '' }}>주문접수</option>
                <option {{ $statusLabel === '본부접수' ? 'selected' : '' }}>본부접수</option>
                <option {{ $statusLabel === '중개필요' ? 'selected' : '' }}>중개필요</option>
                <option {{ $statusLabel === '배송완료' ? 'selected' : '' }}>배송완료</option>
            </select>
        </td>
        <td>
            <button type="button"
                    class="btn-order-history-modal"
                    data-history-url="{{ route('admin.all-order-list.history-modal', $order) }}">
                <img src="{{ asset('adm/assets/img/ico_doc.png') }}" height="18">
            </button>
        </td>
        <td>
            @if ($hasPhoto)
                <button
                    type="button"
                    class="btn-photo-popup"
                    data-photo-url="{{ route('admin.all-order-list.photo-popup', $order) }}"
                >
                    <img src="{{ asset('adm/assets/img/ico_photo_on.png') }}" height="18">
                </button>
            @else
                <button type="button" disabled>
                    <img src="{{ asset('adm/assets/img/ico_photo_off.png') }}" height="18">
                </button>
            @endif
        </td>
        <td class="fs13">
            @if ($order->current_status === 'delivered')
                현장배치
            @else
                <button type="button" class="btn btn-orange">등록</button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="align-center">주문 내역이 없습니다.</td>
    </tr>
@endforelse
