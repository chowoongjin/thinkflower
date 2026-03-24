@extends('layouts.app')

@section('content')
    <div id="content__head">
        @if (!empty($mainBanners) && $mainBanners->count() > 1)
            <div class="main-banner-slider" id="mainBannerSlider">
                <div class="main-banner-track">
                    @foreach ($mainBanners as $banner)
                        <div class="main-banner-slide">
                            @if (!empty($banner->link_url))
                                <a href="{{ $banner->link_url }}">
                                    <img src="{{ $banner->image_path }}" alt="{{ $banner->title ?? '' }}">
                                </a>
                            @else
                                <img src="{{ $banner->image_path }}" alt="{{ $banner->title ?? '' }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif (!empty($mainBanner?->image_path))
            @if (!empty($mainBanner->link_url))
                <a href="{{ $mainBanner->link_url }}">
                    <img src="{{ $mainBanner->image_path }}" alt="{{ $mainBanner->title ?? '' }}">
                </a>
            @else
                <img src="{{ $mainBanner->image_path }}" alt="{{ $mainBanner->title ?? '' }}">
            @endif
        @else
            <img src="{{ asset('assets/img/content_head1.png') }}" alt="">
        @endif
    </div>

    <div id="content__body">
        <ul class="list-column-3-small mt20">
            <li>
                <div class="box">
                    <div class="flex">
                        <div class="flex__col">중개대기 발주건</div>
                        <div class="flex__col">
                            <strong class="fs18 color-primary">{{ number_format($waitingOrderCount ?? 0) }}건</strong>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="box">
                    <div class="flex">
                        <div class="flex__col">배송준비 수주건</div>
                        <div class="flex__col">
                            <strong class="fs18 color-primary">{{ number_format($acceptedReceiveCount ?? 0) }}건</strong>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="box {{ ($uncheckedReceiveCount ?? 0) > 0 ? 'active box-blink' : '' }}">
                    <div class="flex">
                        <div class="flex__col">미확인 수주건</div>
                        <div class="flex__col">
                            <strong class="fs18 color-primary">{{ number_format($uncheckedReceiveCount ?? 0) }}건</strong>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <section class="row mt40">
            <div class="flex">
                <div class="flex__col">
                    <h2 class="tt">✔️ 최근 발주리스트 간편조회</h2>
                </div>
                <div class="flex__col">
                    <a href="{{ route('order-list') }}" class="fs15 color-8c8c8c">전체 발주리스트</a>
                </div>
            </div>

            <table class="table mt20">
                <caption>최근 발주리스트</caption>
                <colgroup>
                    <col style="width:60px">
                    <col style="width:160px">
                    <col style="width:130px">
                    <col style="width:180px">
                    <col style="width:60px">
                    <col style="width:120px">
                    <col style="width:90px">
                    <col style="width:40px">
                    <col style="width:40px">
                    <col style="width:66px">
                </colgroup>
                <thead>
                <tr>
                    <th>주문번호</th>
                    <th>주문접수일<br>배송요구일</th>
                    <th>발주화원사<br>수주화원사</th>
                    <th>보내는 문구<br>배송지</th>
                    <th>담당자<br>받는분</th>
                    <th>주문상품<br>상세정보</th>
                    <th>원청금액<br>결제금액</th>
                    <th>처리<br>내역</th>
                    <th>배송<br>사진</th>
                    <th>배송현황<br>인수정보</th>
                </tr>
                </thead>
                <tbody>
                @include('pages.partials.order-list-table-rows', ['orders' => $recentOrders])
                </tbody>
            </table>
        </section>

        <section class="row mt40">
            <div class="flex">
                <div class="flex__col">
                    <h2 class="tt">✔️ 최근 수주리스트 간편조회</h2>
                </div>
                <div class="flex__col">
                    <a href="{{ route('suju-list') }}" class="fs15 color-8c8c8c">전체 수주리스트</a>
                </div>
            </div>

            <table class="table mt20">
                <caption>최근 수주리스트</caption>
                <colgroup>
                    <col style="width:60px">
                    <col style="width:160px">
                    <col style="width:110px">
                    <col style="width:180px">
                    <col style="width:60px">
                    <col style="width:120px">
                    <col style="width:90px">
                    <col style="width:40px">
                    <col style="width:40px">
                    <col style="width:86px">
                </colgroup>
                <thead>
                <tr>
                    <th>주문번호</th>
                    <th>주문접수일<br>배송요구일</th>
                    <th>수주화원사<br>발주화원사</th>
                    <th>보내는 문구<br>배송지</th>
                    <th>담당자<br>받는분</th>
                    <th>주문상품<br>상세정보</th>
                    <th>원청금액<br>결제금액</th>
                    <th>처리<br>내역</th>
                    <th>배송<br>사진</th>
                    <th>배송현황<br>인수정보</th>
                </tr>
                </thead>
                <tbody>
                @include('pages.partials.suju-list-table-rows', ['orders' => $recentReceives])
                </tbody>
            </table>
        </section>

        <div id="order-history-modal-area"></div>
    </div>

    <script>
        $(function () {
            const $slider = $('#mainBannerSlider');
            if (!$slider.length) return;

            const $track = $slider.find('.main-banner-track');
            const $slides = $track.find('.main-banner-slide');
            const slideCount = $slides.length;

            if (slideCount <= 1) return;

            let currentIndex = 0;

            setInterval(function () {
                currentIndex++;

                if (currentIndex >= slideCount) {
                    currentIndex = 0;
                }

                $track.css('transform', 'translateX(-' + (currentIndex * 100) + '%)');
            }, 3000);

            $(document).on('click', '.order-popup-link', function (e) {
                e.preventDefault();
                const url = $(this).data('popup-url') || $(this).attr('href');
                window.open(url, 'orderPopup', 'width=1000,height=820,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no');
            });

            $(document).on('click', '.order-photo-popup-link', function (e) {
                e.preventDefault();
                const url = $(this).data('popup-url') || $(this).attr('href');
                window.open(url, 'orderPhotoPopup', 'width=1000,height=800,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no');
            });

            $(document).on('click', '.order-complete-popup-link', function (e) {
                e.preventDefault();
                const url = $(this).data('popup-url') || $(this).attr('href');
                window.open(url, 'sujuCompletePopup', 'width=1000,height=800,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no');
            });

            $(document).on('click', '.btn-order-history-modal', function (e) {
                e.preventDefault();

                const url = $(this).data('history-url');
                if (!url) return;

                $.ajax({
                    url: url,
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (html) {
                        $('#order-history-modal-area').html(html);
                        $('#orderHistoryModal').show();
                        $('body').addClass('overflow-hidden');
                    },
                    error: function () {
                        alert('처리내역을 불러오지 못했습니다.');
                    }
                });
            });

            $(document).on('click', '.btn-close-order-history-modal', function () {
                $('#orderHistoryModal').hide();
                $('#order-history-modal-area').empty();
                $('body').removeClass('overflow-hidden');
            });

            $(document).on('click', '.btn-point-charge-popup', function (e) {
                e.preventDefault();

                const url = $(this).data('popup-url');
                if (!url) return;

                window.open(
                    url,
                    'pointChargePopup',
                    'width=500,height=500,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no'
                );
            });
        });
    </script>
@endsection
