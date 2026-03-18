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
                                <strong>{{ number_format($mediationPendingAmount) }}원</strong>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="box --green">
                        <div class="flex">
                            <div class="flex__col">
                                {{ $dashboardMonthLabel }} 수주금액
                            </div>
                            <div class="flex__col">
                                <strong>{{ number_format($monthlyReceivedAmount) }}원</strong>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="box --green">
                        <div class="flex">
                            <div class="flex__col">
                                {{ $dashboardMonthLabel }} 이용료
                            </div>
                            <div class="flex__col">
                                <strong>{{ number_format($monthlyUsageFeeAmount) }}원</strong>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </section>

        <section class="row mt40">
            <div class="flex">
                <div class="flex__col">
                    <h2 class="tt">✔️ 중개가 필요한 발주건 <span class="color-orange pl10">{{ number_format($mediationDashboardCount) }}건</span></h2>
                </div>
                <div class="flex__col">
                    <a href="{{ route('admin.mediation-list') }}" class="fs15 color-8c8c8c">전체보기</a>
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
                    @php($orders = $mediationDashboardOrders)
                    @include('admin.partials.mediation-list-table-rows')
                    </tbody>
                </table>
            </div>
        </section>

        <section class="row mt40">
            <div class="flex">
                <div class="flex__col">
                    <h2 class="tt">✔️ 배송체크 필요한 당일 수주건 <span class="color-orange pl10">{{ number_format($todayCheckCount) }}건</span></h2>
                </div>
                <div class="flex__col">
                    <a href="{{ route('admin.all-order-list')}}" class="fs15 color-8c8c8c">전체보기</a>
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
                    <?php if ($todayCheckOrders->isEmpty()): ?>
                    <tr>
                        <td colspan="11" class="align-center">주문 내역이 없습니다.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($todayCheckOrders as $order): ?>
                        <?php
                        $today = now()->format('Y-m-d');
                        $deliveryDate = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : null;

                        $trClass = '';
                        if ($order->current_status === 'delivered') {
                            $trClass = '';
                        } elseif ($deliveryDate) {
                            if ($deliveryDate > $today) {
                                $trClass = 'tr-primary';
                            } elseif ($deliveryDate === $today) {
                                $trClass = 'tr-warning';
                            }
                        }

                        $statusSelectClass = '';
                        if ($order->current_status === 'delivered') {
                            $statusSelectClass = 'success';
                        } elseif ($order->current_status !== 'accepted') {
                            $statusSelectClass = 'active';
                        }

                        if ($order->current_status === 'delivered') {
                            $statusLabel = '배송완료';
                        } elseif ($order->current_status === 'accepted' && $order->accepted_by_type === 'admin') {
                            $statusLabel = '본부접수';
                        } elseif ($order->current_status === 'accepted' && $order->accepted_by_type === 'shop') {
                            $statusLabel = '주문접수';
                        } else {
                            $statusLabel = '중개필요';
                        }

                        $receiverName = $order->receiverShop->shop_name ?? '본부수발주사업부';
                        $ordererName = $order->ordererShop->shop_name ?? '-';
                        $hasPhoto = ($order->photos_count ?? 0) > 0;

                        $deliveryDateText = '-';
                        $deliveryTimeText = '-';

                        if ($order->delivery_date) {
                            $deliveryDateCarbon = \Carbon\Carbon::parse($order->delivery_date);
                            $deliveryDateText = $deliveryDateCarbon->format('Y/m/d');

                            if ($order->delivery_hour !== null && $order->delivery_minute !== null) {
                                $deliveryAt = \Carbon\Carbon::create(
                                    $deliveryDateCarbon->year,
                                    $deliveryDateCarbon->month,
                                    $deliveryDateCarbon->day,
                                    (int) $order->delivery_hour,
                                    (int) $order->delivery_minute,
                                    0
                                );

                                $nowTime = now();
                                $threeHoursLater = $nowTime->copy()->addHours(3);

                                if ($deliveryAt->lte($threeHoursLater)) {
                                    $deliveryTimeText = '지금즉시';
                                } else {
                                    $deliveryTimeText = $deliveryAt->format('H:i');
                                }
                            }
                        }
                        ?>

                    <tr class="{{ $trClass }}">
                        <td class="no-ellipsis">
                            <button
                                type="button"
                                class="btn-order-popup"
                                data-popup-url="{{ route('admin.all-order-list.popup', $order) }}"
                            >
                                <span class="color-blue">{{ $order->order_no }}</span>
                            </button>
                        </td>
                        <td>
                            <span class="color-gray300">{{ optional($order->created_at)->format('Y/m/d H:i') }}</span><br>
                            {{ $deliveryDateText }}
                            <span class="color-orange">{{ $deliveryTimeText }}</span>
                        </td>
                        <td>
                            <span class="color-gray300">{{ $receiverName }}</span><br>
                            {{ $ordererName }}
                        </td>
                        <td>
                            <span class="color-gray300">{{ \Illuminate\Support\Str::limit($order->sender_name ?? '-', 20) }}</span><br>
                            {{ \Illuminate\Support\Str::limit($order->delivery_addr1 ?? '-', 25) }}
                        </td>
                        <td>{{ $order->recipient_name }}</td>
                        <td>
                            <span class="color-gray300">{{ $order->product_name }}</span><br>
                            <b class="color-green">{{ $order->product_detail }}</b>
                        </td>
                        <td>
                            {{ number_format((int) $order->original_amount) }}원<br>
                            <b class="color-green">{{ number_format((int) $order->order_amount) }}원</b>
                        </td>
                        <td>
                            <select name="" class="select {{ $statusSelectClass }}">
                                <option {{ $statusLabel === '주문접수' ? 'selected' : '' }}>주문접수</option>
                                <option {{ $statusLabel === '본부접수' ? 'selected' : '' }}>본부접수</option>
                                <option {{ $statusLabel === '중개필요' ? 'selected' : '' }}>중개필요</option>
                                <option {{ $statusLabel === '배송완료' ? 'selected' : '' }}>배송완료</option>
                            </select>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn-order-history-modal"
                                data-history-url="{{ route('admin.all-order-list.history-modal', $order) }}"
                            >
                                <img src="{{ asset('adm/assets/img/ico_doc.png') }}" height="18">
                            </button>
                        </td>
                        <td>
                                <?php if ($hasPhoto): ?>
                            <button
                                type="button"
                                class="btn-photo-popup"
                                data-photo-url="{{ route('admin.all-order-list.photo-popup', $order) }}"
                            >
                                <img src="{{ asset('adm/assets/img/ico_photo_on.png') }}" height="18">
                            </button>
                            <?php else: ?>
                            <button type="button" disabled>
                                <img src="{{ asset('adm/assets/img/ico_photo_off.png') }}" height="18">
                            </button>
                            <?php endif; ?>
                        </td>
                        <td class="fs13">
                                <?php if ($order->current_status === 'delivered'): ?>
                            현장배치
                            <?php else: ?>
                            <button type="button" class="btn btn-orange">등록</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="row mt40">
            <div class="flex">
                <div class="flex__col">
                    <h2 class="tt">✔️ 전체 수발주 리스트</h2>
                </div>
                <div class="flex__col">
                    <a href="{{ route('admin.all-order-list') }}" class="fs15 color-8c8c8c">전체보기</a>
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
                    <?php if ($allOrderDashboardOrders->isEmpty()): ?>
                    <tr>
                        <td colspan="11" class="align-center">주문 내역이 없습니다.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($allOrderDashboardOrders as $order): ?>
                        <?php
                        $today = now()->format('Y-m-d');
                        $deliveryDate = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : null;

                        $trClass = '';
                        if ($order->current_status === 'delivered') {
                            $trClass = '';
                        } elseif ($deliveryDate) {
                            if ($deliveryDate > $today) {
                                $trClass = 'tr-primary';
                            } elseif ($deliveryDate === $today) {
                                $trClass = 'tr-warning';
                            } else {
                                $trClass = 'tr-warning';
                            }
                        }

                        $statusSelectClass = '';
                        if ($order->current_status === 'delivered') {
                            $statusSelectClass = 'success';
                        } elseif ($order->current_status !== 'accepted') {
                            $statusSelectClass = 'active';
                        }

                        if ($order->current_status === 'delivered') {
                            $statusLabel = '배송완료';
                        } elseif ($order->current_status === 'accepted' && $order->accepted_by_type === 'admin') {
                            $statusLabel = '본부접수';
                        } elseif ($order->current_status === 'accepted' && $order->accepted_by_type === 'shop') {
                            $statusLabel = '주문접수';
                        } else {
                            $statusLabel = '중개필요';
                        }

                        $receiverName = $order->receiverShop->shop_name ?? '본부수발주사업부';
                        $ordererName = $order->ordererShop->shop_name ?? '-';
                        $hasPhoto = ($order->photos_count ?? 0) > 0;

                        $deliveryDateText = '-';
                        $deliveryTimeText = '-';

                        if ($order->delivery_date) {
                            $deliveryDateCarbon = \Carbon\Carbon::parse($order->delivery_date);
                            $deliveryDateText = $deliveryDateCarbon->format('Y/m/d');

                            if ($order->delivery_hour !== null && $order->delivery_minute !== null) {
                                $deliveryAt = \Carbon\Carbon::create(
                                    $deliveryDateCarbon->year,
                                    $deliveryDateCarbon->month,
                                    $deliveryDateCarbon->day,
                                    (int) $order->delivery_hour,
                                    (int) $order->delivery_minute,
                                    0
                                );

                                $nowTime = now();
                                $threeHoursLater = $nowTime->copy()->addHours(3);

                                if ($deliveryAt->lte($threeHoursLater)) {
                                    $deliveryTimeText = '지금즉시';
                                } else {
                                    $deliveryTimeText = $deliveryAt->format('H:i');
                                }
                            }
                        }
                        ?>

                    <tr class="{{ $trClass }}">
                        <td class="no-ellipsis">
                            <button
                                type="button"
                                class="btn-order-popup"
                                data-popup-url="{{ route('admin.all-order-list.popup', $order) }}"
                            >
                                <span class="color-blue">{{ $order->order_no }}</span>
                            </button>
                        </td>
                        <td>
                            <span class="color-gray300">{{ optional($order->created_at)->format('Y/m/d H:i') }}</span><br>
                            {{ $deliveryDateText }}
                            <span class="color-orange">{{ $deliveryTimeText }}</span>
                        </td>
                        <td>
                            <span class="color-gray300">{{ $receiverName }}</span><br>
                            {{ $ordererName }}
                        </td>
                        <td>
                            <span class="color-gray300">{{ \Illuminate\Support\Str::limit($order->sender_name ?? '-', 20) }}</span><br>
                            {{ \Illuminate\Support\Str::limit($order->delivery_addr1 ?? '-', 25) }}
                        </td>
                        <td>{{ $order->recipient_name }}</td>
                        <td>
                            <span class="color-gray300">{{ $order->product_name }}</span><br>
                            <b class="color-green">{{ $order->product_detail }}</b>
                        </td>
                        <td>
                            {{ number_format((int) $order->original_amount) }}원<br>
                            <b class="color-green">{{ number_format((int) $order->order_amount) }}원</b>
                        </td>
                        <td>
                            <select name="" class="select {{ $statusSelectClass }}">
                                <option {{ $statusLabel === '주문접수' ? 'selected' : '' }}>주문접수</option>
                                <option {{ $statusLabel === '본부접수' ? 'selected' : '' }}>본부접수</option>
                                <option {{ $statusLabel === '중개필요' ? 'selected' : '' }}>중개필요</option>
                                <option {{ $statusLabel === '배송완료' ? 'selected' : '' }}>배송완료</option>
                            </select>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn-order-history-modal"
                                data-history-url="{{ route('admin.all-order-list.history-modal', $order) }}"
                            >
                                <img src="{{ asset('adm/assets/img/ico_doc.png') }}" height="18">
                            </button>
                        </td>
                        <td>
                                <?php if ($hasPhoto): ?>
                            <button
                                type="button"
                                class="btn-photo-popup"
                                data-photo-url="{{ route('admin.all-order-list.photo-popup', $order) }}"
                            >
                                <img src="{{ asset('adm/assets/img/ico_photo_on.png') }}" height="18">
                            </button>
                            <?php else: ?>
                            <button type="button" disabled>
                                <img src="{{ asset('adm/assets/img/ico_photo_off.png') }}" height="18">
                            </button>
                            <?php endif; ?>
                        </td>
                        <td class="fs13">
                                <?php if ($order->current_status === 'delivered'): ?>
                            현장배치
                            <?php else: ?>
                            <button type="button" class="btn btn-orange">등록</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    <script>
        $(function () {
            window.refreshMediationListPreserveQuery = function () {
                window.location.href = window.location.href;
            };

            $(document).on('click', '.btn-order-popup', function () {
                const url = $(this).data('popup-url');
                if (!url) return;

                window.open(
                    url,
                    'orderPopup',
                    'width=1000,height=890,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no'
                );
            });

            $(document).on('click', '.btn-select-receiver', function () {
                const url = $(this).data('popup-url');
                if (!url) return;

                window.open(
                    url,
                    'receiverPopup',
                    'width=1000,height=890,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no'
                );
            });

            $(document).on('click', '.btn-order-history-modal', function (e) {
                e.preventDefault();

                const url = $(this).data('history-url');
                if (!url) return;

                modal(url);
            });

            $(document).on('click', '.btn-photo-popup', function (e) {
                e.preventDefault();

                const url = $(this).data('photo-url');
                if (!url) return;

                window.open(
                    url,
                    'photoPopup',
                    'width=715,height=820,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no'
                );
            });

            $(document).on('click', '#modal', function (e) {
                if (e.target.id === 'modal') {
                    $('#modal, body').removeClass('active');
                    $('#ajax-modal').empty();
                }
            });
        });
    </script>
@endsection
