<section class="row mt20" id="calculate-list-result">
    <table class="table style2 mt20">
        <caption>최근 정산내역</caption>
        <colgroup>
            <col style="width:70px">
            <col style="width:140px">
            <col>
            <col style="width:74px">
            <col style="width:78px">
            <col style="width:92px">
        </colgroup>
        <thead>
        <tr>
            <th class="align-center">구분</th>
            <th>결제일시</th>
            <th>배송지</th>
            <th class="align-center">받는분</th>
            <th class="align-center">주문상품</th>
            <th>결제금액</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($rows as $row)
            @php
                $isOrder = $row->transaction_type === 'order_debit';
                $isReceive = $row->transaction_type === 'order_credit';
            @endphp
            <tr>
                <td class="align-center">
                    @if($isOrder)
                        <span class="color-orange">발주</span>
                    @else
                        <span class="color-blue">수주</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($row->transacted_at)->format('Y/m/d H:i') }}</td>
                <td>{{ trim(($row->delivery_addr1 ?? '') . ' ' . ($row->delivery_addr2 ?? '')) }}</td>
                <td class="align-center">{{ $row->recipient_name ?? '-' }}</td>
                <td class="align-center">{{ $row->product_name ?? '-' }}</td>
                <td>
                    @if($isOrder)
                        <span class="color-orange">-{{ number_format($row->amount) }}원</span>
                    @else
                        <span class="color-green">+{{ number_format($row->amount) }}원</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="align-center">정산 내역이 없습니다.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if($rows->hasPages())
        {{ $rows->links('vendor.pagination.custom') }}
    @endif
</section>
