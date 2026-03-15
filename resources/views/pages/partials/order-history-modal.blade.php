<div id="orderHistoryModal" class="order-history-modal" style="display:none;">
    <div class="order-history-modal__backdrop btn-close-order-history-modal"></div>

    <div class="order-history-modal__dialog">
        <div class="order-history-modal__content">
            <button type="button" class="order-history-modal__close btn-close-order-history-modal" aria-label="닫기">
                ×
            </button>

            <div class="order-history-modal__header">
                <div class="order-history-modal__title">주문서의 처리내역을 안내드려요</div>
                <div class="order-history-modal__sub">
                    <strong>주문번호 <span class="color-blue">{{ $order->order_no }}</span></strong>
                    <span class="color-gray300">문의 시 주문번호를 불러주세요</span>
                </div>
            </div>

            <table class="table-data style3">
                <colgroup>
                    <col style="width:150px;min-width:150px">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>처리시간 ↓</th>
                    <th>처리내용</th>
                </tr>
                </thead>
                <tbody>
                @forelse($histories as $history)
                    <tr>
                        <td>
                            {{ optional($history->processed_at)->format('Y-m-d H:i') }}
                        </td>
                        <td >
                            {!! $history->message !!}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="align-center">처리내역이 없습니다.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
