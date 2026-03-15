<div id="suju-list-content">
    <aside id="sidePanel">
        <section class="panel">
            <div class="panel__head">
                <h2 class="panel-title">본부 운영정보</h2>
            </div>
            <div class="panel__body">
                <ul class="list-qa">
                    <li>
                        <div class="q">본부 연락처</div>
                        <div class="a">1688-1840</div>
                    </li>
                    <li>
                        <div class="q">본부 카카오톡</div>
                        <div class="a">H16681840</div>
                    </li>
                    <li>
                        <div class="q">본부 영업시간</div>
                        <div class="a">09:00 ~ 19:00</div>
                    </li>
                </ul>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <h2 class="panel-title">간편수주 요약</h2>
            </div>
            <div class="panel__body">
                <ul class="list-qa">
                    <li>
                        <div class="q">조회 수주건수</div>
                        <div class="a">{{ number_format($summaryCount) }}건</div>
                    </li>
                    <li>
                        <div class="q">조회 접수금액</div>
                        <div class="a color-orange">{{ number_format($summaryAmount) }}원</div>
                    </li>
                </ul>
            </div>
        </section>
    </aside>

    <section class="mt30">
        <form method="GET" action="{{ route('suju-list') }}" id="suju-filter-form">
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
                    <th>조회기간 현황</th>
                    <td colspan="5">
                        {{ number_format($summaryCount) }}건을
                        <span class="color-blue">수주</span>하여
                        {{ number_format($summaryAmount) }}원이 접수 되었습니다.
                    </td>
                </tr>
                <tr>
                    <th>부가설명</th>
                    <td colspan="5">
                        <ul class="list-inline" id="infoList">
                            <li><div class="input-group-check"><input type="checkbox" class="blue" disabled><label>예약배송건</label></div></li>
                            <li><div class="input-group-check"><input type="checkbox" class="red" disabled><label>금일배송건</label></div></li>
                            <li><div class="input-group-check"><input type="checkbox" class="white" disabled><label>배송완료건</label></div></li>
                            <li><img src="{{ asset('assets/img/ico_photo_on.png') }}"> 상품이미지</li>
                            <li><img src="{{ asset('assets/img/ico_photo_off.png') }}"> 이미지없음</li>
                            <li><img src="{{ asset('assets/img/ico_doc.png') }}"> 처리내역 조회</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>배송요구일</th>
                    <td colspan="5">
                        <div class="inline-flex" style="gap:8px;flex-wrap:wrap;align-items:center;">
                            <div class="input-date-group">
                                <i class="bi bi-calendar2-fill"></i>
                                <input type="text" name="date_from" id="date_from" class="datepicker filter-change" value="{{ $dateFrom }}">
                                <span>~</span>
                                <input type="text" name="date_to" id="date_to" class="datepicker filter-change" value="{{ $dateTo }}">
                            </div>

                            <div class="input-group-radio" id="quick-date-range">
                                <input type="radio" name="quick_range" value="this_month" id="range-this-month">
                                <label for="range-this-month">이번 달</label>

                                <input type="radio" name="quick_range" value="last_month" id="range-last-month">
                                <label for="range-last-month">지난 달</label>

                                <input type="radio" name="quick_range" value="today" id="range-today">
                                <label for="range-today">오늘</label>

                                <input type="radio" name="quick_range" value="tomorrow" id="range-tomorrow">
                                <label for="range-tomorrow">내일</label>

                                <input type="radio" name="quick_range" value="yesterday" id="range-yesterday">
                                <label for="range-yesterday">어제</label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>상품별 조회</th>
                    <td colspan="5">
                        <div class="input-group-radio">
                            <input type="radio" name="product_name" value="" id="test2-1" class="filter-change" {{ request('product_name', '') === '' ? 'checked' : '' }}><label for="test2-1">전체상품</label>
                            <input type="radio" name="product_name" value="근조" id="test2-2" class="filter-change" {{ request('product_name') === '근조화환' ? 'checked' : '' }}><label for="test2-2">근조화환</label>
                            <input type="radio" name="product_name" value="축하" id="test2-3" class="filter-change" {{ request('product_name') === '축하화환' ? 'checked' : '' }}><label for="test2-3">축하화환</label>
                            <input type="radio" name="product_name" value="꽃바구니" id="test2-4" class="filter-change" {{ request('product_name') === '꽃바구니' ? 'checked' : '' }}><label for="test2-4">꽃바구니</label>
                            <input type="radio" name="product_name" value="관엽" id="test2-5" class="filter-change" {{ request('product_name') === '관엽식물' ? 'checked' : '' }}><label for="test2-5">관엽식물</label>
                            <input type="radio" name="product_name" value="동양" id="test2-6" class="filter-change" {{ request('product_name') === '동양란' ? 'checked' : '' }}><label for="test2-6">동양란</label>
                            <input type="radio" name="product_name" value="서양" id="test2-7" class="filter-change" {{ request('product_name') === '서양란' ? 'checked' : '' }}><label for="test2-7">서양란</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>주문번호 검색</th>
                    <td>
                        <div class="input-group-search">
                            <i class="bi bi-search"></i>
                            <input type="text" name="order_no" class="filter-change" value="{{ request('order_no') }}" placeholder="주문번호를 입력해주세요">
                        </div>
                    </td>
                    <th>주소지 검색</th>
                    <td>
                        <div class="input-group-search">
                            <i class="bi bi-search"></i>
                            <input type="text" name="delivery_addr1" class="filter-change" value="{{ request('delivery_addr1') }}" placeholder="주소지를 입력해주세요">
                        </div>
                    </td>
                    <th>받는분 검색</th>
                    <td>
                        <div class="input-group-search">
                            <i class="bi bi-search"></i>
                            <input type="text" name="recipient_name" class="filter-change" value="{{ request('recipient_name') }}" placeholder="받는분을 입력해주세요">
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>

        <div id="suju-list-result">
            @include('pages.partials.suju-list-table')
        </div>
    </section>
</div>
