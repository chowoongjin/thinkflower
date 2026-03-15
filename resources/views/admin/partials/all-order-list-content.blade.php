<section class="mt30">
    <form id="all-order-list-filter-form" method="GET" action="{{ route('admin.all-order-list') }}">
        <table class="table-data style2">
            <colgroup>
                <col style="width:100px">
                <col>
                <col style="width:100px">
                <col>
                <col style="width:100px">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th>부가설명</th>
                <td colspan="5">
                    <ul class="list-inline" id="infoList">
                        <li><div class="input-group-check"><input type="checkbox" class="blue"><label>예약배송건</label></div></li>
                        <li><div class="input-group-check"><input type="checkbox" class="red"><label>금일배송건</label></div></li>
                        <li><div class="input-group-check"><input type="checkbox" class="white"><label>배송완료건</label></div></li>
                        <li><img src="{{ asset('adm/assets/img/ico_photo_on.png') }}"> 상품이미지</li>
                        <li><img src="{{ asset('adm/assets/img/ico_photo_off.png') }}"> 이미지없음</li>
                        <li><img src="{{ asset('adm/assets/img/ico_doc.png') }}"> 처리내역 조회</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <th>조회기간 설정</th>
                <td colspan="5">
                    <div class="inline-flex">
                        <div class="input-date-group">
                            <i class="bi bi-calendar2-fill"></i>
                            <input type="text" name="date_from" class="datepicker" value="{{ $dateFrom }}">
                            <span>~</span>
                            <input type="text" name="date_to" class="datepicker" value="{{ $dateTo }}">
                        </div>
                        <div class="input-group-radio pl15">
                            <input type="radio" name="range_preset" value="thisMonth" id="range1" {{ $rangePreset === 'thisMonth' ? 'checked' : '' }}><label for="range1">이번 달</label>
                            <input type="radio" name="range_preset" value="lastMonth" id="range2" {{ $rangePreset === 'lastMonth' ? 'checked' : '' }}><label for="range2">지난 달</label>
                            <input type="radio" name="range_preset" value="today" id="range3" {{ $rangePreset === 'today' ? 'checked' : '' }}><label for="range3">오늘</label>
                            <input type="radio" name="range_preset" value="tomorrow" id="range4" {{ $rangePreset === 'tomorrow' ? 'checked' : '' }}><label for="range4">내일</label>
                            <input type="radio" name="range_preset" value="yesterday" id="range5" {{ $rangePreset === 'yesterday' ? 'checked' : '' }}><label for="range5">어제</label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>조회기간 현황</th>
                <td colspan="5">
                    <span class="color-active fw500">전체회원 발주금액(본부발주)</span> {{ number_format($totalOrderAmount) }}원
                    <span class="pl2 pr2">|</span>
                    <span class="color-orange fw500">전체회원 수주금액(본부 및 발주사 → 수주사)</span> {{ number_format($totalPaymentAmount) }}원
                </td>
            </tr>
            <tr>
                <th>상품별 조회</th>
                <td colspan="5">
                    <div class="input-group-radio">
                        <input type="radio" name="product_type" value="전체상품" id="product1" {{ $productType === '전체상품' ? 'checked' : '' }}><label for="product1">전체상품</label>
                        <input type="radio" name="product_type" value="근조화환" id="product2" {{ $productType === '근조화환' ? 'checked' : '' }}><label for="product2">근조화환</label>
                        <input type="radio" name="product_type" value="축하화환" id="product3" {{ $productType === '축하화환' ? 'checked' : '' }}><label for="product3">축하화환</label>
                        <input type="radio" name="product_type" value="꽃바구니" id="product4" {{ $productType === '꽃바구니' ? 'checked' : '' }}><label for="product4">꽃바구니</label>
                        <input type="radio" name="product_type" value="관엽식물" id="product5" {{ $productType === '관엽식물' ? 'checked' : '' }}><label for="product5">관엽식물</label>
                        <input type="radio" name="product_type" value="동양란" id="product6" {{ $productType === '동양란' ? 'checked' : '' }}><label for="product6">동양란</label>
                        <input type="radio" name="product_type" value="서양란" id="product7" {{ $productType === '서양란' ? 'checked' : '' }}><label for="product7">서양란</label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>현황별 조회</th>
                <td colspan="5">
                    <div class="input-group-radio">
                        <input type="radio" name="status_type" value="전체상태" id="status1" {{ $statusType === '전체상태' ? 'checked' : '' }}><label for="status1">전체상태</label>
                        <input type="radio" name="status_type" value="중개필요" id="status2" {{ $statusType === '중개필요' ? 'checked' : '' }}><label for="status2">중개필요</label>
                        <input type="radio" name="status_type" value="주문접수" id="status3" {{ $statusType === '주문접수' ? 'checked' : '' }}><label for="status3">주문접수</label>
                        <input type="radio" name="status_type" value="배송완료" id="status4" {{ $statusType === '배송완료' ? 'checked' : '' }}><label for="status4">배송완료</label>
                        <input type="radio" name="status_type" value="주문취소" id="status5" {{ $statusType === '주문취소' ? 'checked' : '' }}><label for="status5">주문취소</label>
                        <input type="radio" name="status_type" value="삭제처리" id="status6" {{ $statusType === '삭제처리' ? 'checked' : '' }}><label for="status6">삭제처리</label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>주문번호 검색</th>
                <td>
                    <div class="input-group-search">
                        <i class="bi bi-search"></i>
                        <input type="text" name="order_no" value="{{ $orderNo }}" placeholder="주문번호를 입력해주세요">
                    </div>
                </td>
                <th>받는분 검색</th>
                <td>
                    <div class="input-group-search">
                        <i class="bi bi-search"></i>
                        <input type="text" name="recipient_name" value="{{ $recipientName }}" placeholder="받는 분을 입력해주세요">
                    </div>
                </td>
                <th>주소지 검색</th>
                <td>
                    <div class="input-group-search">
                        <i class="bi bi-search"></i>
                        <input type="text" name="delivery_addr" value="{{ $deliveryAddr }}" placeholder="주소지를 입력해주세요">
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</section>

