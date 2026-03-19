@extends('layouts.app')

@section('content')
    <div id="content__body" style="position:relative">

        <div id="usage_fee">

            <aside id="sidePanel" class="w380">
                <section class="panel">
                    <div class="panel__head">
                        <h2 class="panel-title">월 이용료 안내문</h2>
                    </div>
                    <div class="panel__body">
                        <p class="color-gray600 lh18">정직한플라워 인트라넷은 월 이용료와 유료 마케팅서비스를 제외한 모든 유용한 서비스가 무료입니다. 타사의 경우 월 이용료 · 수주 수수료 · 경조사비로 월 최대 100만원을 추가 사용료로 부과하고 있지만, 본부의 경우 이 비용들이 부당하다 생각하기에 모든 부대비용을 제거하게 되었습니다. <br><br>

                            월 이용료는 인트라넷 접속환경 개선과 꽃집의 실질적인 매출 향상, 꽃집 운영에 필요한 요소들을 제공해드리기 위해 부과하고 있으며, 인트라넷 활용성에 따라 일정 금액을 페이백 해드리고 있습니다.</p>

                        <div class="flex mt40">
                            <div class="flex__col">
                                <strong class="fw500">정직한플라워 대표 김도훈</strong>
                            </div>
                            <div class="flex__col">
                                <img src="{{ asset('assets/img/sign.png') }}" height="65">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="flex">
                        <div class="flex__col">
                            <h3 class="tt3 fs20 fw600">내 주변의 장례, 미리 준비</h3>
                            <p class="color-gray600 fs15">회원사 한정 장례지원금 30만원 지급</p>
                        </div>
                        <div class="flex__col">
                            <img src="{{ asset('assets/img/ico_ribbon.png') }}" height="50">
                        </div>
                    </div>
                </section>

                <section class="panel mt14">
                    <div class="flex">
                        <div class="flex__col">
                            <h3 class="tt3 fs20 fw600">세무상담 · 노무상담</h3>
                            <p class="color-gray600 fs15">화원사 한정 무료상담 및 기장대리 할인</p>
                        </div>
                        <div class="flex__col">
                            <img src="{{ asset('assets/img/ico_tax.png') }}" height="50">
                        </div>
                    </div>
                </section>

                <section class="panel mt14">
                    <div class="flex">
                        <div class="flex__col">
                            <h3 class="tt3 fs20 fw600">영업용 쇼핑몰 제작</h3>
                            <p class="color-gray600 fs15">화원사 한정 쇼핑몰 및 명함디자인 무료</p>
                        </div>
                        <div class="flex__col">
                            <img src="{{ asset('assets/img/ico_shoppingmall.png') }}" height="50">
                        </div>
                    </div>
                </section>
            </aside>

            <section>
                <h2 class="tt2">✔️ 전체 정산내역 조회</h2>
            </section>

            <section class="mt30">
                <table class="table-data style2" style="table-layout:fixed;">
                    <colgroup>
                        <col style="width:120px">
                        <col style="width:354px;">
                        <col style="width:120px">
                        <col style="width:354px;">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="fs13">결제수단</th>
                        <td>
                            <div class="img-inline ml0">
                                <img src="{{ asset('assets/img/ico_toss.png') }}" alt="toss" height="24">
                                <span class="color-active fw500 pl5">{{ $paymentMethodLabel }}</span>
                            </div>
                        </td>
                        <th class="fs13">계정 상태</th>
                        <td>
                            {{ $accountStatusLabel }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <section class="mt10">
                <table class="table-data style2" style="table-layout:fixed;">
                    <colgroup>
                        <col style="width:120px">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="fs13">이용료 안내</th>
                        <td>
                            매 월 정기결제 카드로 40,000원(VAT 10%별도)이 결제되며, 중도해지 및 환불이 불가능합니다.
                        </td>
                    </tr>
                    <tr>
                        <th class="fs13">이용료 페이백</th>
                        <td>
                            인트라넷에서 수주를 1건 이하로 받으신 경우 화원사 위로를 위해 30,000포인트를 페이백 해드립니다.
                        </td>
                    </tr>
                    <tr>
                        <th class="fs13">발주 장려금</th>
                        <td>
                            인트라넷으로 발주를 10건 이상 하신 경우 감사의 마음을 담아 장려금 30,000포인트를 지급 해드립니다.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <section class="mt10">
                <table class="table-data style2" style="table-layout:fixed;">
                    <colgroup>
                        <col style="width:120px">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="fs13" rowspan="4">
                            월 이용료는<br>
                            이렇게 쓰여지고<br>
                            있어요
                        </th>
                        <td>
                            인트라넷 접속 환경 개선 (AWS 대규모 트래픽서버, 대용량 이미지 단독서버, 개인정보 암호화 등)
                        </td>
                    </tr>
                    <tr>
                        <td>인트라넷 간편 이용을 위한 디자인 및 시스템 개선 · 유지보수 (PC접속, 모바일접속, 어플리케이션)</td>
                    </tr>
                    <tr>
                        <td>화원사 매출 향상을 위한 R&D 연구개발팀 운영 (수주건 확보, 영업용 쇼핑몰, 최저가 마케팅 등)</td>
                    </tr>
                    <tr>
                        <td>화원사 특별혜택 제공을 위한 B2B 업무협약팀 운영 (세무, 노무, 상조 등) → 2026년 대폭 확대 예정</td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <section class="row mt20">

                <table class="table style2 mt20">
                    <caption>최근 발주리스트</caption>
                    <cogroup>
                        <col style="width:70px">
                        <col style="width:140px">
                        <col style="">
                        <col style="width:74px">
                        <col style="width:78px">
                        <col style="width:92px">
                    </cogroup>
                    <thead>
                    <tr>
                        <th class="align-center">구분</th>
                        <th>결제일시</th>
                        <th>결제내용</th>
                        <th class="align-center">결제수단</th>
                        <th class="align-center">상품</th>
                        <th>변동사항</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($historyRows as $row)
                        <tr>
                            <td class="align-center"><span class="color-gray900">{{ $row->type_label }}</span></td>
                            <td class="fs14 color-gray700">{{ $row->paid_at_label }}</td>
                            <td class="fs14 color-gray700">{{ $row->content_label }}</td>
                            <td class="align-center fs14 color-gray700">{{ $row->payment_method_label }}</td>
                            <td class="align-center fs14 color-gray700">{{ $row->product_label }}</td>
                            <td><span class="{{ $row->amount_class }}">{{ $row->amount_label }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="align-center">이용료 내역이 없습니다.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </section>

        </div>

    </div>
@endsection
