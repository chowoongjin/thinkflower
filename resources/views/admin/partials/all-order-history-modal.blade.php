<div class="modal">
    <div class="modal__head">
        <div class="flex">
            <div class="flex__col">
                <h2 class="modal-title">주문서의 처리내역을 안내드려요</h2>
                <p class="modal-desc">
                    주문번호 <strong class="color-primary">{{ $order->order_no }}</strong>
                    <span class="color-gray300">문의 시 주문번호를 불러주세요</span>
                <p>
            </div>
            <div class="flex__col">
                <button type="button" class="modal-close">모달닫기</button>
            </div>
        </div>
    </div>
    <div class="modal__body">
        <table class="table-data style3">
            <colgroup>
                <col style="width:150px;min-width:150px">
            </colgroup>
            <thead>
            <tr>
                <th><button type="button">처리시간 ↓</button></th>
                <th>처리내용</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($histories as $history)
            <tr>
                <td>{{ optional($history->processed_at)->format('Y-m-d H:i') }}</td>
                <td>{!! $history->message !!}</td>
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
