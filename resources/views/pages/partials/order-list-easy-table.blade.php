<section class="row mt20" id="order-list-result">
    <table class="table mt20">
        <caption>최근 발주리스트</caption>
        <colgroup>
            <col style="width:60px">
            <col style="width:160px">
            <col style="width:130px">
            <col style="width:180px">
            <col style="width:80px">
            <col style="width:120px">
            <col style="width:90px">
            <col style="width:40px">
            <col style="width:70px">
        </colgroup>
        <thead>
        <tr>
            <th>주문번호</th>
            <th>배송요구일</th>
            <th>배송지(간략)</th>
            <th>보내는 문구</th>
            <th>받는사람</th>
            <th>상세정보</th>
            <th>결제금액</th>
            <th>사진</th>
            <th>배송현황</th>
        </tr>
        </thead>
        <tbody>
        @include('pages.partials.order-list-table-easy-rows')
        </tbody>
    </table>

    <div class="mt20">
        {{ $orders->links('vendor.pagination.custom') }}
    </div>
</section>
