@extends('layouts.admin')

@section('content')
    <div id="content__body">

        <section>
            <h2 class="tt2">본사 인트라넷 운영현황</h2>
            <ul class="list-column-3-small mt20">
                <li>
                    <div class="box --primary">
                        <div class="flex">
                            <div class="flex__col">
                                중개대기 발주건
                            </div>
                            <div class="flex__col">
                                <strong>3,655,600원</strong>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="box --green">
                        <div class="flex">
                            <div class="flex__col">
                                2026년 02월 수주금액
                            </div>
                            <div class="flex__col">
                                <strong>6,142,600원</strong>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="box --green">
                        <div class="flex">
                            <div class="flex__col">
                                2026년 02월 이용료
                            </div>
                            <div class="flex__col">
                                <strong>2,420,000원</strong>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </section>



        <!-- 중개가 필요한 발주건 -->
        <section class="row mt40">
            <div class="flex">
                <div class="flex__col">
                    <h2 class="tt">✔️ 중개가 필요한 발주건 <span class="color-orange pl10">3건</span></h2>
                </div>
                <div class="flex__col">
                    <a href="#none" class="fs15 color-8c8c8c">전체보기</a>
                </div>
            </div>

            <div class="outline-orange mt20">
                <table class="table">
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
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="tr-primary">
                        <td class="no-ellipsis"><span class="color-blue">23456</span></td>
                        <td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">18:00</span></td>
                        <td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
                        <td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
                        <td>한도현</td>
                        <td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
                        <td>100,000원<br><b class="color-green">80,000원</b></td>
                        <td class="align-center">
                            <button type="button" class="btn btn-orange --outline h32">수주사 선택</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 최근 수주리스트 -->
        <section class="row mt40">
            <div class="flex">
                <div class="flex__col">
                    <h2 class="tt">✔️ 배송체크 필요한 당일 수주건 <span class="color-orange pl10">3건</span></h2>
                </div>
                <div class="flex__col">
                    <a href="#none" class="fs15 color-8c8c8c">전체보기</a>
                </div>
            </div>

            <div class="outline-orange mt20">
                <table class="table">
                    <caption></caption>
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
                        <th>수주화원사<br>발주화원사</th>
                        <th>보내는 문구<br>배송지</th>
                        <th>받는분</th>
                        <th>주문상품<br>상세정보</th>
                        <th>원청금액<br>결제금액</th>
                        <th>배송현황</th>
                        <th>처리<br>내역</th>
                        <th>배송<br>사진</th>
                        <th>배송현황<br>인수정보</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="tr-warning">
                        <td class="no-ellipsis"><span class="color-blue">23456</span></td>
                        <td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
                        <td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
                        <td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
                        <td>한도현</td>
                        <td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
                        <td>100,000원<br><b class="color-green">80,000원</b></td>
                        <td>
                            <select name="" class="select">
                                <option>본부접수</option>
                            </select>
                        </td>
                        <td><button type="button"><img src="{{ asset('adm/assets/img/ico_doc.png') }}" height="18"></button></td>
                        <td><button type="button"><img src="{{ asset('adm/assets/img/ico_photo_off.png') }}" height="18"></button></td>
                        <td class="fs13">
                            <button type="button" class="btn btn-orange">등록</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 전체 발주 리스트 -->
        <section class="row mt40">
            <div class="flex">
                <div class="flex__col">
                    <h2 class="tt">✔️ 전체 수발주 리스트</h2>
                </div>
                <div class="flex__col">
                    <a href="#none" class="fs15 color-8c8c8c">전체보기</a>
                </div>
            </div>

            <div class="outline-black mt20">
                <table class="table">
                    <caption></caption>
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
                        <th>수주화원사<br>발주화원사</th>
                        <th>보내는 문구<br>배송지</th>
                        <th>받는분</th>
                        <th>주문상품<br>상세정보</th>
                        <th>원청금액<br>결제금액</th>
                        <th>배송현황</th>
                        <th>처리<br>내역</th>
                        <th>배송<br>사진</th>
                        <th>배송현황<br>인수정보</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="tr-warning">
                        <td class="no-ellipsis"><span class="color-blue">23456</span></td>
                        <td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
                        <td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
                        <td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
                        <td>한도현</td>
                        <td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
                        <td>100,000원<br><b class="color-green">80,000원</b></td>
                        <td>
                            <select name="" class="select">
                                <option>본부접수</option>
                            </select>
                        </td>
                        <td><button type="button"><img src="{{ asset('adm/assets/img/ico_doc.png') }}" height="18"></button></td>
                        <td><button type="button"><img src="{{ asset('adm/assets/img/ico_photo_off.png') }}" height="18"></button></td>
                        <td class="fs13">
                            <button type="button" class="btn btn-orange">등록</button>
                        </td>
                    </tr>
                    <tr class="tr-primary">
                        <td class="no-ellipsis"><span class="color-blue">23456</span></td>
                        <td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
                        <td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
                        <td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
                        <td>한도현</td>
                        <td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
                        <td>100,000원<br><b class="color-green">80,000원</b></td>
                        <td>
                            <select name="" class="select active">
                                <option>본부접수</option>
                                <option selected="selected">중개필요</option>
                            </select>
                        </td>
                        <td><button type="button"><img src="{{ asset('adm/assets/img/ico_doc.png') }}" height="18"></button></td>
                        <td><button type="button"><img src="{{ asset('adm/assets/img/ico_photo_off.png') }}" height="18"></button></td>
                        <td class="fs13">
                            <button type="button" class="btn btn-orange">등록</button>
                        </td>
                    </tr>
                    <tr class="tr-primary">
                        <td class="no-ellipsis"><span class="color-blue">23456</span></td>
                        <td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
                        <td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
                        <td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
                        <td>한도현</td>
                        <td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
                        <td>100,000원<br><b class="color-green">80,000원</b></td>
                        <td>
                            <select name="" class="select active">
                                <option>본부접수</option>
                                <option selected="selected">중개필요</option>
                            </select>
                        </td>
                        <td><button type="button"><img src="{{ asset('adm/assets/img/ico_doc.png') }}" height="18"></button></td>
                        <td><button type="button"><img src="{{ asset('adm/assets/img/ico_photo_off.png') }}" height="18"></button></td>
                        <td class="fs13">
                            <button type="button" class="btn btn-orange">등록</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
@endsection
