@extends('layouts.app')

@section('content')
    <div id="content__body" style="position:relative">

        <section class="mb20">
            <h2 class="tt2">✔️ 정직한플라워 마케팅 지원센터 <small>네이버 플레이스 부스터</small></h2>
        </section>

        <div id="marketingCenter">

            <section class="headBox --green">
                <div class="flex">
                    <div class="flex__col">
                        <span class="fw600">네이버 플레이스 부스터,</span> <span class="fw500">어떤 것이 궁금하신가요?</span>
                    </div>
                    <div class="flex__col">
                        <a href="#none" class="btn btn-green">설명서 읽어보기</a>
                        <a href="#none" class="btn btn-brown">마케팅 진행하기</a>
                    </div>
                </div>
            </section>

            <section class="row mt30">

                <h3 class="tt3">플레이스 부스터 구매내역</h3>
                <table class="table style2 mt20">
                    <colgroup>
                        <col style="width:90px">
                        <col style="width:130px">
                        <col style="">
                        <col style="width:90px">
                        <col>
                        <col style="width:90px">
                        <col style="width:90px">
                        <col style="width:90px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>주문번호</th>
                            <th>주문일시</th>
                            <th>업체명</th>
                            <th>지역카페</th>
                            <th>이용상품</th>
                            <th class="align-center">결제금액</th>
                            <th class="align-center">진행현황</th>
                            <th class="align-center">게시물</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw600">N123456</td>
                            <td>2025-11-02</td>
                            <td>이엘플라워</td>
                            <td>인천광역시</td>
                            <td>프리미엄 (게시물 3개 + 각 댓글 7개)</td>
                            <td class="align-center">75,000원</td>
                            <td class="align-center color-primary">진행중</td>
                            <td class="align-center"><a href="#none" class="color-green">바로가기</a></td>
                        </tr>
                        <tr>
                            <td class="fw600">N123456</td>
                            <td>2025-11-02</td>
                            <td>이엘플라워</td>
                            <td>인천광역시</td>
                            <td>스탠다드 (게시물 1개 + 댓글 3개)</td>
                            <td class="align-center">350,000원</td>
                            <td class="align-center color-gray800">진행완료</td>
                            <td class="align-center"><a href="#none" class="color-green">바로가기</a></td>
                        </tr>
                    </tbody>
                </table>

                <nav class="pagination-wrap">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <a href="#" class="page-link">‹</a>
                        </li>

                        <li class="page-item active">
                            <a href="#" class="page-link">1</a>
                        </li>
                        <li class="page-item">
                            <a href="#" class="page-link">2</a>
                        </li>
                        <li class="page-item">
                            <a href="#" class="page-link">3</a>
                        </li>

                        <li class="page-item">
                            <a href="#" class="page-link">›</a>
                        </li>
                    </ul>
                </nav>

            </section>

        </div>

    </div>
@endsection
