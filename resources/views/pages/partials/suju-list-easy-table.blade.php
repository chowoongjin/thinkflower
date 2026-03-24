@php
    $today = now()->format('Y-m-d');
@endphp

<table class="table mt20">
    <caption>최근 수주리스트</caption>
    <colgroup>
        <col style="width:60px">
        <col style="width:160px">
        <col style="width:130px">
        <col style="width:180px">
        <col style="width:80px">
        <col style="width:120px">
        <col style="width:90px">
        <col style="width:40px">
        <col style="width:85px">
    </colgroup>
    <thead>
    <tr>
        <th>주문번호</th>
        <th>배송요구일</th>
        <th>배송지(간편)</th>
        <th>보내는 문구</th>
        <th>받는분</th>
        <th>상세정보</th>
        <th>결제금액</th>
        <th>사진</th>
        <th>인수정보</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @include('pages.partials.suju-list-table-easy-rows', ['orders' => $orders])
    </tbody>
</table>

@if ($orders->hasPages())
    <div class="mt20">
        {{ $orders->links('vendor.pagination.custom') }}
    </div>
@endif
