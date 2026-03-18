@extends('layouts.admin')

@section('content')
    <div id="content__body">

        <div id="adminCalculate">

            <section>
                <h2 class="tt">✔️ 전체 정산내역 조회</h2>
            </section>

            <section class="mt30">
                <form id="calculate-list-filter-form" method="GET" action="{{ route('admin.calculate-list') }}">
                    <table class="table-data style5 --small">
                        <colgroup>
                            <col style="width:120px">
                            <col style="">
                            <col style="width:100px">
                            <col style="">
                            <col style="width:100px">
                            <col style="">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th class="fs13">조회기간 설정</th>
                            <td colspan="5">
                                <div class="inline-flex">
                                    <div class="input-date-group">
                                        <i class="bi bi-calendar2-fill"></i>
                                        <input type="text" name="target_month" class="datepicker" value="{{ $targetMonth }}">
                                    </div>
                                    <div class="input-group-radio pl15">
                                        <input type="radio" name="range" value="이번 달" id="test1" {{ $range === '이번 달' ? 'checked' : '' }}><label for="test1">이번 달</label>
                                        <input type="radio" name="range" value="지난 달" id="test2" {{ $range === '지난 달' ? 'checked' : '' }}><label for="test2">지난 달</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>조회기간 현황</th>
                            <td>
                                <strong class="fw500">입금 하는 금액</strong> {{ number_format($totalPayAmount) }}원 <span class="pl5 pr5">|</span>
                                <strong class="fw500">입금 받을 금액</strong> {{ number_format($totalReceiveAmount) }}원 <span class="pl5 pr5">|</span>
                                <strong class="fw500 color-green">전체차액</strong>
                                @if ($totalGapAmount > 0)
                                    + {{ number_format($totalGapAmount) }}원
                                @elseif ($totalGapAmount < 0)
                                    - {{ number_format(abs($totalGapAmount)) }}원
                                @else
                                    0원
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="fs13">항목별 조회</th>
                            <td colspan="5">
                                <div class="input-group-radio">
                                    <input type="radio" name="item_type" value="전체항목" id="test2-1" {{ $itemType === '전체항목' ? 'checked' : '' }}><label for="test2-1">전체항목</label>
                                    <input type="radio" name="item_type" value="입금 받을 금액" id="test2-2" {{ $itemType === '입금 받을 금액' ? 'checked' : '' }}><label for="test2-2">입금 받을 금액</label>
                                    <input type="radio" name="item_type" value="입금 하는 금액" id="test2-3" {{ $itemType === '입금 하는 금액' ? 'checked' : '' }}><label for="test2-3">입금 하는 금액</label>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </section>

            <section class="row mt20">

                <table class="table --default mt20">
                    <caption>리스트</caption>
                    <cogroup>
                        <col style="width:80px">
                        <col style="width:100px">
                        <col>
                        <col style="width:130px">
                        <col style="width:130px">
                        <col style="width:130px">
                        <col>
                        <col style="width:90px">
                    </cogroup>
                    <thead>
                    <tr>
                        <th class="align-center">구분</th>
                        <th class="align-center">해당월</th>
                        <th>거래업체</th>
                        <th>발주금액</th>
                        <th>수주금액</th>
                        <th>합계금액</th>
                        <th>화원사 입금정보</th>
                        <th class="align-center">처리현황</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($settlements as $row)
                        <tr>
                            <td class="align-center {{ $row->settlement_type_class }}">{{ $row->settlement_type }}</td>
                            <td>{{ $row->month_label }}</td>
                            <td>{{ $row->shop_name }}</td>
                            <td>{{ number_format((int) $row->order_amount_sum) }}원</td>
                            <td>{{ number_format((int) $row->receive_amount_sum) }}원</td>
                            <td class="{{ $row->amount_class }}">{{ number_format((int) $row->display_amount) }}원</td>
                            <td>{{ $row->memo_text }}</td>
                            <td>
                                <button type="button" class="btn btn-orange --outline" style="color:#fff;">처리완료</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="align-center">정산 내역이 없습니다.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <nav class="pagination-wrap">
                    {{ $settlements->links('vendor.pagination.custom') }}
                </nav>

            </section>



        </div>

    </div>

    <script>
        $(function () {
            function pad2(num) {
                return String(num).padStart(2, '0');
            }

            function formatDate(date) {
                return date.getFullYear() + '-' + pad2(date.getMonth() + 1) + '-' + pad2(date.getDate());
            }

            function setMonthByPreset(type) {
                const now = new Date();
                let target = new Date(now);

                if (type === '지난 달') {
                    target = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                } else {
                    target = new Date(now.getFullYear(), now.getMonth(), 1);
                }

                $('#calculate-list-filter-form input[name="target_month"]').val(formatDate(target));
            }

            $(document).on('change', '#calculate-list-filter-form input[name="range"]', function () {
                setMonthByPreset($(this).val());
                $('#calculate-list-filter-form').trigger('submit');
            });

            $(document).on('change', '#calculate-list-filter-form input[name="item_type"]', function () {
                $('#calculate-list-filter-form').trigger('submit');
            });

            $(document).on('change', '#calculate-list-filter-form input[name="target_month"]', function () {
                $('#calculate-list-filter-form').trigger('submit');
            });
        });
    </script>
@endsection
