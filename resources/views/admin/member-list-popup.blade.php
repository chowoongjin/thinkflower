<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $popupTitle }}</title>
    <link rel="stylesheet" href="{{ asset('adm/assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('adm/assets/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('adm/assets/css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('adm/assets/css/pretendard.css') }}">
    <script src="{{ asset('adm/assets/js/jquery-3.6.0.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

<div id="popup">
    <div class="popup__head">
        <h1 class="popup-title">{{ $popupTitle }}</h1>
    </div>
    <div class="popup__body">

        <div id="popup-member-result">
            <form id="member-search-form" method="GET" action="{{ route('admin.member-list-popup') }}">
                <input type="hidden" name="target" value="{{ $target }}">
                <input type="hidden" name="product_filter" id="product_filter" value="{{ $productFilter ?? '전체' }}">
                <input type="hidden" name="source" value="{{ $popupSource ?? '' }}">
                <input type="hidden" name="order_id" value="{{ $orderId ?? '' }}">
                <input type="hidden" name="order_no" value="{{ request('order_no') }}">
                <input type="hidden" name="return_url" value="{{ request('return_url') }}">

                <div class="row">
                    <h2 class="fw600">상품필터</h2>
                    <nav class="nav-tab style2 mt10">
                        <ul>
                            <li class="{{ ($productFilter ?? '전체') === '전체' ? 'active' : '' }}"><a href="#none" class="btn-product-filter" data-value="전체">전체</a></li>
                            <li class="{{ ($productFilter ?? '') === '근조' ? 'active' : '' }}"><a href="#none" class="btn-product-filter" data-value="근조">근조</a></li>
                            <li class="{{ ($productFilter ?? '') === '축하' ? 'active' : '' }}"><a href="#none" class="btn-product-filter" data-value="축하">축하</a></li>
                            <li class="{{ ($productFilter ?? '') === '오브제' ? 'active' : '' }}"><a href="#none" class="btn-product-filter" data-value="오브제">오브제</a></li>
                            <li class="{{ ($productFilter ?? '') === '쌀화환' ? 'active' : '' }}"><a href="#none" class="btn-product-filter" data-value="쌀화환">쌀화환</a></li>
                            <li class="{{ ($productFilter ?? '') === '관엽' ? 'active' : '' }}"><a href="#none" class="btn-product-filter" data-value="관엽">관엽</a></li>
                            <li class="{{ ($productFilter ?? '') === '동양란' ? 'active' : '' }}"><a href="#none" class="btn-product-filter" data-value="동양란">동양란</a></li>
                            <li class="{{ ($productFilter ?? '') === '서양란' ? 'active' : '' }}"><a href="#none" class="btn-product-filter" data-value="서양란">서양란</a></li>
                        </ul>
                    </nav>
                </div>

                <div class="row mt20">
                    <div class="grid">
                        <div class="grid__7">
                            <h2 class="fw600">지역필터</h2>
                            <div class="select-group mt10">
                                <select name="sido" id="filter_sido">
                                    <option value="">시/도 선택</option>
                                    @foreach ($sidoOptions as $item)
                                        <option value="{{ $item }}" {{ ($sido ?? '') === $item ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                                <select name="sigungu" id="filter_sigungu">
                                    <option value="">구/군 선택</option>
                                    @foreach ($sigunguOptions as $item)
                                        <option value="{{ $item }}" {{ ($sigungu ?? '') === $item ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid__5">
                            <h2 class="fw600">화원명 직접 검색</h2>
                            <div class="mt10">
                                <div class="input-group">
                                    <span><i class="bi bi-search"></i></span>
                                    <input type="text" name="keyword" value="{{ $keyword ?? '' }}" placeholder="화원사명을 입력하세요">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="popup-member-list-area">
                    @include('admin.partials.member-list-popup-list')
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    window.memberPopupMeta = {
        target: @json($target ?? 'receiver'),
        popupSource: @json($popupSource ?? ''),
        orderId: @json($orderId ?? null),
        orderNo: @json(request('order_no')),
        returnUrl: @json(request('return_url')),
        assignUrlTemplate: @json(route('admin.mediation-list.assign-receiver', ['order' => '__ORDER_ID__'])),
        allOrderAssignUrlTemplate: @json(route('admin.all-order-list.assign-receiver', ['order' => '__ORDER_NO__'])),
        csrf: @json(csrf_token())
    };
</script>
<script>
    $(function () {
        function loadMemberResult(url, data = {}) {
            $.ajax({
                url: url,
                method: 'GET',
                data: data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (html) {
                    $('#popup-member-list-area').html(html);
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    alert('회원 리스트를 불러오지 못했습니다.');
                }
            });
        }

        $(document).on('submit', '#member-search-form', function (e) {
            e.preventDefault();
            loadMemberResult($(this).attr('action'), $(this).serialize());
        });

        $(document).on('click', '#popup-member-list-area .pagination a', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url) {
                const separator = url.includes('?') ? '&' : '?';
                loadMemberResult(url + separator + $('#member-search-form').serialize());
            }
        });

        $(document).on('click', '.btn-product-filter', function (e) {
            e.preventDefault();

            const value = $(this).data('value');

            $('#product_filter').val(value);

            $(this).closest('ul').find('li').removeClass('active');
            $(this).closest('li').addClass('active');

            $('#member-search-form').trigger('submit');
        });

        $(document).on('change', '#filter_sido', function () {
            $('#filter_sigungu').val('');
            $('#member-search-form').trigger('submit');
        });

        $(document).on('change', '#filter_sigungu', function () {
            $('#member-search-form').trigger('submit');
        });

        let keywordSearchTimer = null;
        $(document).on('input', '#member-search-form input[name="keyword"]', function () {
            clearTimeout(keywordSearchTimer);
            keywordSearchTimer = setTimeout(function () {
                $('#member-search-form').trigger('submit');
            }, 500);
        });

        $(document).on("click", "#memberSearchResult .member-info", function () {
            var $radio = $(this).find("input[type='radio']");
            if ($radio.prop("disabled")) {
                return false;
            }
            $radio.prop("checked", true).trigger("change");
        });

        $(document).on("change", ".member-radio", function () {
            const $radio = $(this);
            const target = $radio.data("target");
            const shopId = $radio.data("shop-id");
            const shopDisplayName = $radio.data("shop-display-name");
            const meta = window.memberPopupMeta || {};

            // 중개리스트에서 연 수주사 선택 팝업 전용 처리
            // 전체수발주리스트에서 연 수주사 선택 팝업 전용 처리
            if (meta.popupSource === "all-order-list" && meta.orderNo) {
                if (!confirm('해당 수주사로 선택하시겠습니까?\n\n' + shopDisplayName)) {
                    $radio.prop('checked', false);
                    return;
                }

                const url = meta.allOrderAssignUrlTemplate.replace('__ORDER_NO__', meta.orderNo);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: meta.csrf,
                        receiver_shop_id: shopId,
                        return_url: meta.returnUrl || '',
                        admin_accept: 1
                    },
                    success: function (res) {
                        alert(res.message || '수주사가 선택되었습니다.');

                        if (window.opener && !window.opener.closed) {
                            if (window.opener.$ && window.opener.$('#all-order-list-filter-form').length) {
                                window.opener.$('#all-order-list-filter-form').trigger('submit');
                            } else {
                                window.opener.location.reload();
                            }
                        }

                        window.close();
                    },
                    error: function (xhr) {
                        $radio.prop('checked', false);

                        let message = '수주사 선택 처리 중 오류가 발생했습니다.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        alert(message);
                    }
                });

                return;
            }

            // 중개리스트에서 연 수주사 선택 팝업 전용 처리
            if (meta.popupSource === "mediation-list" && meta.orderId) {
                if (!confirm('해당 수주사로 선택하시겠습니까?\n\n' + shopDisplayName)) {
                    $radio.prop('checked', false);
                    return;
                }

                const url = meta.assignUrlTemplate.replace('__ORDER_ID__', meta.orderId);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: meta.csrf,
                        receiver_shop_id: shopId
                    },
                    success: function (res) {
                        alert(res.message || '수주사가 선택되었습니다.');

                        if (window.opener && !window.opener.closed) {
                            if (window.opener.$ && window.opener.$('#mediation-list-filter-form').length) {
                                window.opener.$('#mediation-list-filter-form').trigger('submit');
                            } else if (typeof window.opener.refreshMediationListPreserveQuery === 'function') {
                                window.opener.refreshMediationListPreserveQuery();
                            } else {
                                window.opener.location.reload();
                            }
                        }

                        window.close();
                    },
                    error: function (xhr) {
                        $radio.prop('checked', false);

                        let message = '수주사 선택 처리 중 오류가 발생했습니다.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        alert(message);
                    }
                });

                return;
            }

            // 기존 공용 팝업 처리
            if (target === "receiver2") {
                if (!confirm('선택한 수주사로 지정하시겠습니까?\n확인을 누르시면 바로 수주사가 지정됩니다.')) {
                    $radio.prop('checked', false);
                    return;
                }
            }

            if (window.opener && !window.opener.closed) {
                if (target === "receiver2" && typeof window.opener.setSelectedReceiverShop === 'function') {
                    window.opener.setSelectedReceiverShop(shopId, shopDisplayName);
                    window.close();
                    return;
                }

                if (target === "orderer") {
                    const ordererShopId = window.opener.document.getElementById("orderer_shop_id");
                    const ordererShopName = window.opener.document.getElementById("orderer_shop_name");
                    const ordererIsHq = window.opener.document.getElementById("orderer_is_hq");

                    if (ordererShopId) ordererShopId.value = shopId;
                    if (ordererShopName) ordererShopName.value = shopDisplayName;
                    if (ordererIsHq) ordererIsHq.value = '0';
                } else {
                    const receiverShopId = window.opener.document.getElementById("receiver_shop_id");
                    const receiverShopName = window.opener.document.getElementById("receiver_shop_name");
                    const receiverIsHq = window.opener.document.getElementById("receiver_is_hq");

                    if (receiverShopId) receiverShopId.value = shopId;
                    if (receiverShopName) receiverShopName.value = shopDisplayName;
                    if (receiverIsHq) receiverIsHq.value = '0';
                }
            }

            window.close();
        });

        function assignReceiverFromMediation(shopId, shopName) {
            const meta = window.memberPopupMeta || {};

            if (meta.popupSource !== 'mediation-list' || !meta.orderId) {
                return false;
            }

            if (!confirm('해당 수주사로 선택하겠습니까?\n\n[' + shopName + ']')) {
                return false;
            }

            const url = meta.assignUrlTemplate.replace('__ORDER_ID__', meta.orderId);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: meta.csrf,
                    receiver_shop_id: shopId
                },
                success: function (res) {
                    alert(res.message || '수주사가 선택되었습니다.');

                    if (window.opener && !window.opener.closed) {
                        if (typeof window.opener.refreshMediationListPreserveQuery === 'function') {
                            window.opener.refreshMediationListPreserveQuery();
                        } else {
                            window.opener.location.href = window.opener.location.href;
                        }
                    }

                    window.close();
                },
                error: function (xhr) {
                    let message = '수주사 선택 처리 중 오류가 발생했습니다.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    alert(message);
                }
            });

            return true;
        }
    });
</script>

</body>
</html>
