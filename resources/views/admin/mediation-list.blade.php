@extends('layouts.admin')

@section('content')
    <div id="content__body">

        <div id="all-balju">

            <section>
                <h2 class="tt">✔️ 중개가 필요한 주문목록</h2>
            </section>

            @include('admin.partials.mediation-list-content')

            <div id="mediation-list-table-area">
                @include('admin.partials.mediation-list-table')
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

            function loadMediationTable(url, data = null) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (res) {
                        if (typeof res === 'object' && res.table_html !== undefined) {
                            $('#mediation-list-table-area').html(res.table_html);
                        } else {
                            $('#mediation-list-table-area').html(res);
                        }

                        let nextUrl = url;

                        if (data) {
                            const queryString = typeof data === 'string' ? data : $.param(data);
                            nextUrl = queryString ? (url + '?' + queryString) : url;
                        }

                        window.history.replaceState({}, '', nextUrl);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('목록을 불러오지 못했습니다.');
                    }
                });
            }

            $(document).on('submit', '#mediation-list-filter-form', function (e) {
                e.preventDefault();
                loadMediationTable($(this).attr('action'), $(this).serialize());
            });

            $(document).on('click', '#mediation-list-table-area .pagination a', function (e) {
                e.preventDefault();

                const href = $(this).attr('href');
                if (!href) return;

                loadMediationTable(href);
            });

            $(document).on('change', 'input[name="range_preset"]', function () {
                setDateRangeByPreset($(this).val());
                $('#mediation-list-filter-form').trigger('submit');
            });

            $(document).on('change', 'input[name="product_type"]', function () {
                $('#mediation-list-filter-form').trigger('submit');
            });

            $(document).on('change', 'input[name="date_from"], input[name="date_to"]', function () {
                $('#mediation-list-filter-form').trigger('submit');
            });

            let keywordTimer = null;
            $(document).on(
                'input',
                '#mediation-list-filter-form input[name="order_no"], #mediation-list-filter-form input[name="recipient_name"], #mediation-list-filter-form input[name="delivery_addr"]',
                function () {
                    clearTimeout(keywordTimer);
                    keywordTimer = setTimeout(function () {
                        $('#mediation-list-filter-form').trigger('submit');
                    }, 700);
                }
            );

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

            function refreshMediationListPreserveQuery() {
                const currentUrl = window.location.href;
                loadMediationTable(currentUrl);
            }

            window.refreshMediationListPreserveQuery = refreshMediationListPreserveQuery;
        });
    </script>
@endsection
