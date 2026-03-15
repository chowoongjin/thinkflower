@extends('layouts.admin')

@section('content')
    <div id="content__body">
        <div id="all-balju">
            <section>
                <h2 class="tt">✔️ 전체 수발주리스트조회</h2>
            </section>

            @include('admin.partials.all-order-list-content')

            <div id="all-order-list-table-area">
                @include('admin.partials.all-order-list-table')
            </div>
        </div>
    </div>

    <script>
        $(function () {
            function loadAllOrderTable(url, data = {}) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (html) {
                        $('#all-order-list-table-area').html(html);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('목록을 불러오지 못했습니다.');
                    }
                });
            }

            $(document).on('submit', '#all-order-list-filter-form', function (e) {
                e.preventDefault();
                loadAllOrderTable($(this).attr('action'), $(this).serialize());
            });

            $(document).on('click', '#all-order-list-table-area .pagination a', function (e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    const separator = url.includes('?') ? '&' : '?';
                    loadAllOrderTable(url + separator + $('#all-order-list-filter-form').serialize());
                }
            });

            $(document).on('change', 'input[name="product_type"], input[name="status_type"], input[name="range_preset"]', function () {
                $('#all-order-list-filter-form').trigger('submit');
            });

            $(document).on('change', 'input[name="date_from"], input[name="date_to"]', function () {
                $('#all-order-list-filter-form').trigger('submit');
            });

            let keywordTimer = null;
            $(document).on('input', '#all-order-list-filter-form input[name="order_no"], #all-order-list-filter-form input[name="recipient_name"], #all-order-list-filter-form input[name="delivery_addr"]', function () {
                clearTimeout(keywordTimer);
                keywordTimer = setTimeout(function () {
                    $('#all-order-list-filter-form').trigger('submit');
                }, 500);
            });
        });
    </script>
@endsection
