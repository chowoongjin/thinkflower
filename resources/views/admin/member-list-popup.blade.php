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
</head>
<body>

<div id="popup">
    <div class="popup__head">
        <h1 class="popup-title">{{ $popupTitle }}</h1>
    </div>
    <div class="popup__body">
        <div id="popup-member-result">
            @include('admin.partials.member-list-popup-content')
        </div>
    </div>
</div>

<script>
    $(function () {
        function loadMemberPopup(url, data = {}) {
            $.ajax({
                url: url,
                method: 'GET',
                data: data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (html) {
                    $('#popup-member-result').html(html);
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    alert('회원 리스트를 불러오지 못했습니다.');
                }
            });
        }

        $(document).on('submit', '#member-search-form', function (e) {
            e.preventDefault();
            loadMemberPopup($(this).attr('action'), $(this).serialize());
        });

        $(document).on('click', '#popup-member-result .pagination a', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url) {
                loadMemberPopup(url);
            }
        });

        $(document).on('click', '.btn-product-filter', function (e) {
            e.preventDefault();

            const value = $(this).data('value');
            $('#product_filter').val(value);
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
            const target = $(this).data("target");
            const shopId = $(this).data("shop-id");
            const shopDisplayName = $(this).data("shop-display-name");

            if (window.opener && !window.opener.closed) {
                if (target === "orderer") {
                    window.opener.document.getElementById("orderer_shop_id").value = shopId;
                    window.opener.document.getElementById("orderer_shop_name").value = shopDisplayName;
                    window.opener.document.getElementById("orderer_is_hq").value = '0';
                } else {
                    window.opener.document.getElementById("receiver_shop_id").value = shopId;
                    window.opener.document.getElementById("receiver_shop_name").value = shopDisplayName;
                    window.opener.document.getElementById("receiver_is_hq").value = '0';
                }
            }

            window.close();
        });
    });
</script>

</body>
</html>
