@extends('layouts.app')

@section('content')
    @include('pages.partials.calculate-list-content')

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

            function bindDatepicker() {
                if ($.fn.datepicker) {
                    $('.datepicker').datepicker('destroy').datepicker({
                        dateFormat: 'yy-mm-dd',
                        onSelect: function () {
                            loadCalculateList();
                        }
                    });
                }
            }

            function loadCalculateList(urlOverride = null) {
                const $form = $('#calculate-filter-form');
                const url = urlOverride || $form.attr('action');
                const data = urlOverride ? null : $form.serialize();

                if (currentAjax) {
                    currentAjax.abort();
                }

                $('#content__body').css('opacity', '0.5');

                currentAjax = $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (html) {
                        $('#content__body').replaceWith(html);
                        bindDatepicker();

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
                    }
                });
            }

            function setThisMonth() {
                const now = new Date();
                const from = formatDate(new Date(now.getFullYear(), now.getMonth(), 1));
                const to = formatDate(new Date(now.getFullYear(), now.getMonth() + 1, 0));

                $('#date_from').val(from);
                $('#range-this-month').prop('checked', true);
            }

            function setLastMonth() {
                const now = new Date();
                const from = formatDate(new Date(now.getFullYear(), now.getMonth() - 1, 1));
                const to = formatDate(new Date(now.getFullYear(), now.getMonth(), 0));

                $('#date_from').val(from);
                $('#range-last-month').prop('checked', true);
            }

            function scheduleLoad(delay = 200) {
                clearTimeout(filterTimer);
                filterTimer = setTimeout(function () {
                    loadCalculateList();
                }, delay);
            }

            $(document).on('change', '#date_from', function () {
                scheduleLoad(100);
            });

            $(document).on('change', 'input[name="quick_range"]', function () {
                const type = $(this).val();

                if (type === 'this_month') {
                    setThisMonth();
                } else if (type === 'last_month') {
                    setLastMonth();
                }

                loadCalculateList();
            });

            $(document).on('click', '#calculate-list-result .pagination a', function (e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (href) {
                    loadCalculateList(href);
                }
            });

            bindDatepicker();
        });
    </script>
@endsection
