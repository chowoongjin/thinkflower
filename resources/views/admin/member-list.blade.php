@extends('layouts.admin')

@section('content')
    <div id="content__body">

        <div id="adminCalculate">

            <section>
                <h2 class="tt">✔️ 정직한플라워 전체 회원리스트</h2>
            </section>

            <section class="mt30">
                <form id="member-list-filter-form" method="GET" action="{{ route('admin.member-list') }}">
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
                            <th class="fs13">회원가입 일시</th>
                            <td colspan="5">
                                <div class="inline-flex">
                                    <div class="input-date-group">
                                        <i class="bi bi-calendar2-fill"></i>
                                        <input type="text" name="date_from" class="datepicker" value="{{ $dateFrom }}">
                                        <span>~</span>
                                        <i class="bi bi-calendar2-fill"></i>
                                        <input type="text" name="date_to" class="datepicker" value="{{ $dateTo }}">
                                    </div>
                                    <div class="input-group-radio pl15">
                                        <input type="radio" name="range" value="전체기간" id="test1-1" {{ $range === '전체기간' ? 'checked' : '' }}><label for="test1-1">전체기간</label>
                                        <input type="radio" name="range" value="최근 1개월" id="test1-2" {{ $range === '최근 1개월' ? 'checked' : '' }}><label for="test1-2">최근 1개월</label>
                                        <input type="radio" name="range" value="최근 6개월" id="test1-3" {{ $range === '최근 6개월' ? 'checked' : '' }}><label for="test1-3">최근 6개월</label>
                                        <input type="radio" name="range" value="최근 1년" id="test1-4" {{ $range === '최근 1년' ? 'checked' : '' }}><label for="test1-4">최근 1년</label>
                                        <input type="radio" name="range" value="최근 2년" id="test1-5" {{ $range === '최근 2년' ? 'checked' : '' }}><label for="test1-5">최근 2년</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="fs13">계정상태별 조회</th>
                            <td colspan="5">
                                <div class="input-group-radio">
                                    <input type="radio" name="status" value="전체상태" id="test2-1" {{ $status === '전체상태' ? 'checked' : '' }}><label for="test2-1">전체상태</label>
                                    <input type="radio" name="status" value="활성화" id="test2-2" {{ $status === '활성화' ? 'checked' : '' }}><label for="test2-2">활성화</label>
                                    <input type="radio" name="status" value="비활성화" id="test2-3" {{ $status === '비활성화' ? 'checked' : '' }}><label for="test2-3">비활성화</label>
                                    <input type="radio" name="status" value="무료체험" id="test2-4" {{ $status === '무료체험' ? 'checked' : '' }}><label for="test2-4">무료체험</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="fs13">빠른 지역별 조회</th>
                            <td colspan="5">
                                <div class="input-group-radio">
                                    <input type="radio" name="region" value="전체지역" id="test3-1" {{ $region === '전체지역' ? 'checked' : '' }}><label for="test3-1">전체지역</label>
                                    <input type="radio" name="region" value="서울" id="test3-2" {{ $region === '서울' ? 'checked' : '' }}><label for="test3-2">서울</label>
                                    <input type="radio" name="region" value="경기" id="test3-3" {{ $region === '경기' ? 'checked' : '' }}><label for="test3-3">경기</label>
                                    <input type="radio" name="region" value="인천" id="test3-4" {{ $region === '인천' ? 'checked' : '' }}><label for="test3-4">인천</label>
                                    <input type="radio" name="region" value="대구" id="test3-5" {{ $region === '대구' ? 'checked' : '' }}><label for="test3-5">대구</label>
                                    <input type="radio" name="region" value="광주" id="test3-6" {{ $region === '광주' ? 'checked' : '' }}><label for="test3-6">광주</label>
                                    <input type="radio" name="region" value="부산" id="test3-7" {{ $region === '부산' ? 'checked' : '' }}><label for="test3-7">부산</label>
                                    <input type="radio" name="region" value="경상도" id="test3-8" {{ $region === '경상도' ? 'checked' : '' }}><label for="test3-8">경상도</label>
                                    <input type="radio" name="region" value="전라도" id="test3-9" {{ $region === '전라도' ? 'checked' : '' }}><label for="test3-9">전라도</label>
                                    <input type="radio" name="region" value="충청도" id="test3-10" {{ $region === '충청도' ? 'checked' : '' }}><label for="test3-10">충청도</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>회원사명 조회</th>
                            <td>
                                <div class="input-group-search">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="shop_name" value="{{ $shopName }}" placeholder="회원사명을 입력해주세요">
                                </div>
                            </td>
                            <th>소재지 조회</th>
                            <td>
                                <div class="input-group-search">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="location_name" value="{{ $locationName }}" placeholder="지역명을 입력해주세요">
                                </div>
                            </td>
                            <th>주소지 검색</th>
                            <td>
                                <div class="input-group-search">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="address_keyword" value="{{ $addressKeyword }}" placeholder="주소지를 입력해주세요">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </section>

            <div id="member-list-table-area">
                @include('admin.partials.member-list-table')
            </div>

        </div>

    </div>

    <script>
        $(function () {
            function loadMemberList(url, data = null) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (res) {
                        if (typeof res === 'object' && res.table_html !== undefined) {
                            $('#member-list-table-area').html(res.table_html);
                        } else {
                            $('#member-list-table-area').html(res);
                        }

                        let nextUrl = url;

                        if (data) {
                            const queryString = typeof data === 'string' ? data : $.param(data);
                            nextUrl = queryString ? (url + '?' + queryString) : url;
                        }

                        window.history.replaceState({}, '', nextUrl);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('회원 리스트를 불러오지 못했습니다.');
                    }
                });
            }

            function pad2(num) {
                return String(num).padStart(2, '0');
            }

            function formatDate(date) {
                return date.getFullYear() + '-' + pad2(date.getMonth() + 1) + '-' + pad2(date.getDate());
            }

            function setMemberDateRangeByPreset(type) {
                const now = new Date();
                let startDate = null;
                let endDate = null;

                if (type === '전체기간') {
                    startDate = new Date(now);
                    startDate.setDate(startDate.getDate() - 15);

                    endDate = new Date(now);
                    endDate.setDate(endDate.getDate() + 15);
                } else if (type === '최근 1개월') {
                    startDate = new Date(now);
                    startDate.setMonth(startDate.getMonth() - 1);
                    endDate = new Date(now);
                } else if (type === '최근 6개월') {
                    startDate = new Date(now);
                    startDate.setMonth(startDate.getMonth() - 6);
                    endDate = new Date(now);
                } else if (type === '최근 1년') {
                    startDate = new Date(now);
                    startDate.setFullYear(startDate.getFullYear() - 1);
                    endDate = new Date(now);
                } else if (type === '최근 2년') {
                    startDate = new Date(now);
                    startDate.setFullYear(startDate.getFullYear() - 2);
                    endDate = new Date(now);
                }

                if (startDate && endDate) {
                    $('#member-list-filter-form input[name="date_from"]').val(formatDate(startDate));
                    $('#member-list-filter-form input[name="date_to"]').val(formatDate(endDate));
                }
            }

            $(document).on('submit', '#member-list-filter-form', function (e) {
                e.preventDefault();
                loadMemberList($(this).attr('action'), $(this).serialize());
            });

            $(document).on('click', '#member-list-table-area .pagination a', function (e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (!url) return;
                loadMemberList(url);
            });

            $(document).on('change', '#member-list-filter-form input[name="range"]', function () {
                setMemberDateRangeByPreset($(this).val());
                $('#member-list-filter-form').trigger('submit');
            });

            $(document).on('change', '#member-list-filter-form input[name="status"], #member-list-filter-form input[name="region"]', function () {
                $('#member-list-filter-form').trigger('submit');
            });

            $(document).on('change', '#member-list-filter-form input[name="date_from"], #member-list-filter-form input[name="date_to"]', function () {
                $('#member-list-filter-form').trigger('submit');
            });

            let keywordTimer = null;
            $(document).on('input', '#member-list-filter-form input[name="shop_name"], #member-list-filter-form input[name="location_name"], #member-list-filter-form input[name="address_keyword"]', function () {
                clearTimeout(keywordTimer);
                keywordTimer = setTimeout(function () {
                    $('#member-list-filter-form').trigger('submit');
                }, 500);
            });
        });
    </script>
@endsection
