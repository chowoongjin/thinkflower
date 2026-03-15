@extends('layouts.app')

@section('content')
    @include('pages.partials.photo-list-content')

    <script>
        $(function () {
            let currentAjax = null;

            function loadPhotoList(urlOverride = null) {
                const $form = $('#photo-filter-form');
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

            $(document).on('change', '#photo-filter-form input[name="product_name"]', function () {
                loadPhotoList();
            });

            $(document).on('click', '#photoGallery .pagination a', function (e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (href) {
                    loadPhotoList(href);
                }
            });
        });
    </script>
@endsection
