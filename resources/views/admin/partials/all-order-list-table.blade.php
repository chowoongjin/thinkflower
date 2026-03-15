<section class="row mt20">
    <table class="table mt20">
        <caption>최근 발주리스트</caption>
        <cogroup>
            <col style="width:60px">
            <col style="width:160px">
            <col style="width:160px">
            <col>
            <col style="width:70px">
            <col style="width:150px">
            <col style="width:110px">
            <col style="width:110px">
            <col style="width:40px">
            <col style="width:40px">
            <col style="width:70px">
        </cogroup>
        <thead>
        <tr>
            <th>주문번호</th>
            <th>주문접수일<br>배송요구일</th>
            <th>발주화원사<br>수주화원사</th>
            <th>보내는 문구<br>배송지</th>
            <th>받는분</th>
            <th>주문상품<br>상세정보</th>
            <th>원청금액<br>결제금액</th>
            <th class="align-center">배송현황</th>
            <th>처리<br>내역</th>
            <th>배송<br>사진</th>
            <th>배송현황<br>인수정보</th>
        </tr>
        </thead>
        <tbody>
        @include('admin.partials.all-order-list-table-rows')
        </tbody>
    </table>

    <nav class="pagination-wrap">
        {{ $orders->links('vendor.pagination.custom') }}
    </nav>
</section>
