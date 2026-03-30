<div id="content__body" style="position:relative">
    <div id="all-balju">

        <section>
            <h2 class="tt2">✔️ 전체 정산내역 조회</h2>
        </section>

        <section class="mt30">
            <form method="GET" action="{{ route('calculate-list') }}" id="calculate-filter-form">
                <table class="table-data style2" style="table-layout:fixed;">
                    <colgroup>
                        <col style="width:120px">
                        <col style="width:354px;">
                        <col style="width:120px">
                        <col style="width:354px;">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="fs13">조회기간 설정</th>
                        <td colspan="3">
                            <div class="inline-flex">
                                <div class="input-date-group">
                                    <i class="bi bi-calendar2-fill"></i>
                                    <input type="text" name="date_from" id="date_from" class="datepicker filter-change" value="{{ $dateFrom }}">
                                </div>
                                <div class="input-group-radio pl15">
                                    <input type="radio" name="quick_range" value="this_month" id="range-this-month" {{ request('quick_range', 'this_month') === 'this_month' ? 'checked' : '' }}>
                                    <label for="range-this-month">이번 달</label>

                                    <input type="radio" name="quick_range" value="last_month" id="range-last-month" {{ request('quick_range') === 'last_month' ? 'checked' : '' }}>
                                    <label for="range-last-month">지난 달</label>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th class="fs13">조회기간 현황</th>
                        <td colspan="3">
                            <span class="color-orange">차감건</span>
                            -{{ number_format($orderAmount) }}원({{ number_format($orderCount) }}건)
                            <span class="color-green pl10">적립건</span>
                            +{{ number_format($receiveAmount) }}원({{ number_format($receiveCount) }}건)
                        </td>
                    </tr>

                    <tr>
                        <th class="fs13">종합 정산금액</th>
                        <td colspan="3">
                            <strong class="color-primary fw500">
                                {{ $shop->shop_name }}는 본부로부터 {{ number_format(abs($netAmount)) }}원을
                                {{ $netAmount >= 0 ? '입금 받아야 합니다.' : '입금 해야 합니다.' }}
                            </strong>
                        </td>
                    </tr>

                    <tr>
                        <th class="fs13">본부계좌</th>
                        <td>KB국민은행 410701-04-244109 주식회사 싱크플로</td>
                        <th class="fs13">회원계좌</th>
                        <td>{{ $shop->bank_name }} {{ $shop->bank_account }} {{ $shop->bank_holder }}</td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </section>

        @include('pages.partials.calculate-list-table')
    </div>
</div>
