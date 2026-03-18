@forelse ($orders as $order)
    @php
        $today = now()->format('Y-m-d');
        $deliveryDate = $order->delivery_date
            ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d')
            : null;

        $trClass = '';
        if ($deliveryDate) {
            if ($deliveryDate > $today) {
                $trClass = 'tr-primary';
            } elseif ($deliveryDate === $today) {
                $trClass = 'tr-warning';
            } else {
                $trClass = 'tr-warning';
            }
        }

        $ordererName = $order->ordererShop->shop_name ?? '-';
        $receiverName = $order->receiverShop->shop_name ?? '본부 수발주사업부';

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
                data-popup-url="{{ route('admin.mediation-list.popup', $order) }}"
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
            {{ \Illuminate\Support\Str::limit($order->delivery_addr1 ?? '-', 50) }}
        </td>
        <td class="align-center">{{ $order->recipient_name ?? '-' }}</td>
        <td>
            <span class="color-gray300">{{ $order->product_name ?? '-' }}</span><br>
            <b class="color-green">{{ $order->product_detail ?? '-' }}</b>
        </td>
        <td>
            {{ number_format((int) ($order->original_amount ?? 0)) }}원<br>
            <b class="color-green">{{ number_format((int) ($order->payment_amount ?? $order->order_amount ?? 0)) }}원</b>
        </td>
        <td class="align-center">
            <button
                type="button"
                class="btn btn-orange --outline btn-select-receiver"
                data-popup-url="{{ route('admin.member-list-popup', [
                    'target' => 'receiver',
                    'source' => 'mediation-list',
                    'order_id' => $order->order_no,
                ]) }}"
                >
                수주사선택
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="align-center">주문 내역이 없습니다.</td>
    </tr>
@endforelse
