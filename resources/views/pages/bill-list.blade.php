@extends('layouts.app')

@section('content')
    <div id="content__body" style="position:relative">

        <div id="photoGallery">

            <section>
                <h2 class="tt2">✔️ 계산서 내역</h2>
            </section>

            <section class="mt30">
                <form method="GET" action="{{ route('bill-list') }}">
                    <table class="table-data style5">
                        <colgroup>
                            <col style="width:100px;min-width:100px">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th class="fs13 color-gray800">조회 기간설정</th>
                            <td>
                                <div class="inline-flex">
                                    <div class="input-date-group">
                                        <i class="bi bi-calendar2-fill"></i>
                                        <input type="text" name="target_month" class="datepicker" value="{{ $targetMonth }}">
                                    </div>
                                    <div class="input-group-radio">
                                        <input type="radio" name="range" value="this_year" id="test2-1" {{ $range === 'this_year' ? 'checked' : '' }}>
                                        <label for="test2-1">이번년도</label>
                                        <input type="radio" name="range" value="last_year" id="test2-2" {{ $range === 'last_year' ? 'checked' : '' }}>
                                        <label for="test2-2">작년년도</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="fs13 color-gray800">부가안내</th>
                            <td>매출계산서의 경우 직접 홈택스를 통한 신고가 필요합니다</td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </section>

            <section class="row mt20">

                <table class="table style6 mt20">
                    <caption>최근 발주리스트</caption>
                    <colgroup>
                        <col style="width:100px">
                        <col style="width:110px">
                        <col style="width:238px">
                        <col style="width:130px">
                        <col style="width:140px">
                        <col style="width:120px">
                        <col style="width:110px">
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="align-center">구분</th>
                        <th class="align-center">발행년월일</th>
                        <th>품목</th>
                        <th class="align-center">거래처명</th>
                        <th class="align-center">사업자번호</th>
                        <th class="align-center">공급가액</th>
                        <th class="align-center">계산서출력</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($statements as $statement)
                        <tr>
                            <td class="align-center"><span class="{{ $statement->type_class }}">{{ mb_substr($statement->type_label, 0, 2) }}</span>{{ mb_substr($statement->type_label, 2) }}</td>
                            <td class="align-center">{{ $statement->issued_date_label }}</td>
                            <td>{{ $statement->item_label }}</td>
                            <td class="align-center">{{ $statement->vendor_name }}</td>
                            <td class="align-center">{{ $statement->vendor_business_no }}</td>
                            <td class="align-center">{{ number_format((int) $statement->invoice_amount) }} 원</td>
                            <td class="align-center">
                                <button type="button" class="btn">출력하기</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="align-center">계산서 내역이 없습니다.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                @if ($statements->hasPages())
                    <nav class="pagination-wrap">
                        {{ $statements->links('vendor.pagination.custom') }}
                    </nav>
                @endif

            </section>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            function submitBillFilter() {
                $('form[action="{{ route('bill-list') }}"]').trigger('submit');
            }

            $(document).on('change', 'input[name="range"]', function () {
                submitBillFilter();
            });

            $(document).on('change', 'input[name="target_month"]', function () {
                submitBillFilter();
            });
        });
    </script>
@endpush
