@extends('layouts.app')

@section('content')
    <div id="content__body" style="width:900px;position:relative">
        <div id="all-balju">
            <section>
                <h2 class="tt">✔️ 전체 발주리스트 조회</h2>
            </section>

            @include('pages.partials.order-list-content')

            <div id="order-list-result">
                @include('pages.partials.order-list-table')
            </div>
        </div>
    </div>

    <div id="order-history-modal-area"></div>

    <script>
        $(function () {
            let filterTimer = null;
            let currentAjax = null;

            function pad2(num) {
                return String(num).padStart(2, '0');
            }

            function formatDate(date) {
                return [
                    date.getFullYear(),
                    pad2(date.getMonth() + 1),
                    pad2(date.getDate())
                ].join('-');
            }

            function setRange(from, to) {
                $('#date_from').val(from);
                $('#date_to').val(to);
            }

            function syncQuickRangeRadio() {
                const from = ($('#date_from').val() || '').trim();
                const to = ($('#date_to').val() || '').trim();

                $('input[name="quick_range"]').prop('checked', false);

                if (!from || !to) return;

                const now = new Date();
                const today = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate()));
                const tomorrow = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1));
                const yesterday = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1));
                const thisMonthFrom = formatDate(new Date(now.getFullYear(), now.getMonth(), 1));
                const thisMonthTo = formatDate(new Date(now.getFullYear(), now.getMonth() + 1, 0));
                const lastMonthFrom = formatDate(new Date(now.getFullYear(), now.getMonth() - 1, 1));
                const lastMonthTo = formatDate(new Date(now.getFullYear(), now.getMonth(), 0));

                if (from === thisMonthFrom && to === thisMonthTo) {
                    $('#range-this-month').prop('checked', true);
                } else if (from === lastMonthFrom && to === lastMonthTo) {
                    $('#range-last-month').prop('checked', true);
                } else if (from === today && to === today) {
                    $('#range-today').prop('checked', true);
                } else if (from === tomorrow && to === tomorrow) {
                    $('#range-tomorrow').prop('checked', true);
                } else if (from === yesterday && to === yesterday) {
                    $('#range-yesterday').prop('checked', true);
                }
            }

            function bindDatepicker() {
                if ($.fn.datepicker) {
                    $('.datepicker').datepicker('destroy').datepicker({
                        dateFormat: 'yy-mm-dd',
                        onSelect: function () {
                            syncQuickRangeRadio();
                            loadOrderList();
                        }
                    });
                }
            }

            function loadOrderList(urlOverride = null) {
                const $form = $('#order-filter-form');
                const url = urlOverride || $form.attr('action');
                const data = urlOverride ? null : $form.serialize();

                if (currentAjax) {
                    currentAjax.abort();
                }

                $('#order-list-result').css('opacity', '0.5');

                currentAjax = $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (html) {
                        $('#order-list-result').html(html);
                        bindDatepicker();
                        syncQuickRangeRadio();

                        if (!urlOverride) {
                            window.history.replaceState({}, '', url + '?' + data);
                        } else {
                            window.history.replaceState({}, '', urlOverride);
                        }
                    },
                    error: function (xhr, status) {
                        if (status !== 'abort') {
                            location.href = urlOverride || (url + '?' + data);
                        }
                    },
                    complete: function () {
                        currentAjax = null;
                        $('#order-list-result').css('opacity', '1');
                    }
                });
            }

            function scheduleLoad(delay = 400) {
                clearTimeout(filterTimer);
                filterTimer = setTimeout(function () {
                    loadOrderList();
                }, delay);
            }

            $(document).on('change', '.filter-change', function () {
                scheduleLoad(200);
            });

            $(document).on('input', 'input.filter-change[type="text"]', function () {
                scheduleLoad(700);
            });

            $(document).on('change', 'input[name="quick_range"]', function () {
                const type = $(this).val();
                const now = new Date();

                let fromDate = null;
                let toDate = null;

                if (type === 'this_month') {
                    fromDate = new Date(now.getFullYear(), now.getMonth(), 1);
                    toDate = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                } else if (type === 'last_month') {
                    fromDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    toDate = new Date(now.getFullYear(), now.getMonth(), 0);
                } else if (type === 'today') {
                    fromDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    toDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                } else if (type === 'tomorrow') {
                    fromDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
                    toDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
                } else if (type === 'yesterday') {
                    fromDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
                    toDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
                }

                if (fromDate && toDate) {
                    setRange(formatDate(fromDate), formatDate(toDate));
                    $(this).prop('checked', true);
                    loadOrderList();
                }
            });

            $(document).on('click', '#order-list-result .pagination a', function (e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (href) {
                    loadOrderList(href);
                }
            });

            bindDatepicker();
            syncQuickRangeRadio();

            $(document).on('click', '.order-popup-link', function (e) {
                e.preventDefault();

                const url = $(this).data('popup-url') || $(this).attr('href');
                const name = 'orderPopup';
                const specs = 'width=1000,height=820,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no';

                window.open(url, name, specs);
            });

            $(document).on('click', '.btn-order-history-modal', function (e) {
                e.preventDefault();

                const url = $(this).data('history-url');
                if (!url) return;

                $.ajax({
                    url: url,
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
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

            $(document).on('click', '.order-photo-popup-link', function (e) {
                e.preventDefault();

                const url = $(this).data('popup-url') || $(this).attr('href');
                const name = 'orderPhotoPopup';
                const specs = 'width=800,height=820,scrollbars=no,resizable=no,toolbar=no,menubar=no,location=no,status=no';

                window.open(url, name, specs);
            });
        });
    </script>
@endsection
