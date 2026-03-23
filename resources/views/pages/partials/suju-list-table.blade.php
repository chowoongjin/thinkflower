@php
    $today = now()->format('Y-m-d');
@endphp

<table class="table mt20">
    <caption>최근 수주리스트</caption>
    <colgroup>
        <col style="width:60px">
        <col style="width:160px">
        <col style="width:130px">
        <col style="width:165px">
        <col style="width:60px">
        <col style="width:120px">
        <col style="width:86px">
        <col style="width:40px">
        <col style="width:40px">
        <col style="width:87px">
    </colgroup>
    <thead>
    <tr>
        <th>주문번호</th>
        <th>주문접수일<br>배송요구일</th>
        <th>수주화원사<br>발주화원사</th>
        <th>보내는 문구<br>배송지</th>
        <th>담당자<br>받는분</th>
        <th>주문상품<br>상세정보</th>
        <th>원청금액<br>결제금액</th>
        <th>처리<br>내역</th>
        <th>배송<br>사진</th>
        <th>배송현황<br>인수정보</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @include('pages.partials.suju-list-table-rows', ['orders' => $orders])
    </tbody>
</table>

@if ($orders->hasPages())
    <div class="mt20">
        {{ $orders->links('vendor.pagination.custom') }}
    </div>
@endif
