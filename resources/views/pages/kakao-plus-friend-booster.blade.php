@extends('layouts.app')

@section('content')
    <div id="content__body" style="position:relative">

        <section class="mb20">
            <h2 class="tt2">✔️ 정직한플라워 마케팅 지원센터 <small>카카오 플러스친구 부스터</small></h2>
        </section>

        <div id="marketingCenter">

            <section class="headBox --green">
                <div class="flex">
                    <div class="flex__col">
                        <span class="fw600">카카오 플러스친구 부스터,</span> <span class="fw500">어떤 것이 궁금하신가요?</span>
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
                        <col style="width:100px">
                        <col>
                        <col style="width:90px">
                        <col style="width:90px">
                        <col style="width:90px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>주문번호</th>
                            <th>주문일시</th>
                            <th>고유번호</th>
                            <th>이용상품</th>
                            <th class="align-center">결제금액</th>
                            <th class="align-center">진행현황</th>
                            <th class="align-center">플러스친구</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw600">N123456</td>
                            <td>2025-11-02</td>
                            <td>_xmcrwn</td>
                            <td>스탠다드 (친구 수 100명 증가)</td>
                            <td class="align-center">60,000원</td>
                            <td class="align-center color-primary">발행대기</td>
                            <td class="align-center"><a href="#none" class="color-green">바로가기</a></td>
                        </tr>
                        <tr>
                            <td class="fw600">N123456</td>
                            <td>2025-11-02</td>
                            <td>_xmcrwn</td>
                            <td>스탠다드 (친구 수 100명 증가)</td>
                            <td class="align-center">60,000원</td>
                            <td class="align-center">발행완료</td>
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
