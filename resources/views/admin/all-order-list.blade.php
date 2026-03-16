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
            function pad2(num) {
                return String(num).padStart(2, '0');
            }

            function formatDate(date) {
                return date.getFullYear() + '-' + pad2(date.getMonth() + 1) + '-' + pad2(date.getDate());
            }

            function setDateRangeByPreset(type) {
                const now = new Date();
                let startDate = new Date(now);
                let endDate = new Date(now);

                switch (type) {
                    case 'thisMonth':
                        startDate = new Date(now.getFullYear(), now.getMonth(), 1);
                        endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        break;

                    case 'lastMonth':
                        startDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                        endDate = new Date(now.getFullYear(), now.getMonth(), 0);
                        break;

                    case 'today':
                        startDate = new Date(now);
                        endDate = new Date(now);
                        break;

                    case 'tomorrow':
                        startDate = new Date(now);
                        startDate.setDate(startDate.getDate() + 1);
                        endDate = new Date(startDate);
                        break;

                    case 'yesterday':
                        startDate = new Date(now);
                        startDate.setDate(startDate.getDate() - 1);
                        endDate = new Date(startDate);
                        break;

                    default:
                        return;
                }

                $('input[name="date_from"]').val(formatDate(startDate));
                $('input[name="date_to"]').val(formatDate(endDate));
            }

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

            $(document).on('change', 'input[name="range_preset"]', function () {
                const type = $(this).val();
                setDateRangeByPreset(type);
                $('#all-order-list-filter-form').trigger('submit');
            });

            $(document).on('change', 'input[name="product_type"], input[name="status_type"]', function () {
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
                }, 700);
            });
        });
    </script>
@endsection
