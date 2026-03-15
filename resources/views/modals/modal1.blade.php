<div class="modal modal-small" id="modal-suju-item">
    <div class="modal__head">
        <div class="flex">
            <div class="flex__col">
                <h2 class="modal-title">수주 취급상품</h2>
            </div>
            <div class="flex__col">
                <button type="button" class="modal-close">모달닫기</button>
            </div>
        </div>
    </div>

    <div class="modal__body">
        <div class="grid-2">
            @foreach ([$leftItems, $rightItems] as $tableIndex => $items)
                <div class="grid__col">
                    <table class="table-data style3">
                        <thead>
                        <tr>
                            <th>취급상품</th>
                            <th>선택</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $i => $item)
                            <tr>
                                <td>
                                    <label for="item-{{ $tableIndex }}-{{ $i }}">{{ $item }}</label>
                                </td>
                                <td>
                                    <input type="checkbox" name="item[]" value="{{ $item }}" id="item-{{ $tableIndex }}-{{ $i }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="mt20">
            <button type="button" class="btn btn-fluid btn-green fs18" id="complete-product-select">
                수주상품 선택완료 <i class="bi bi-check-circle-fill pl5"></i>
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const currentValues = $('#products').val()
            ? $('#products').val().split(',').map(v => v.trim()).filter(Boolean)
            : [];

        $('input[name="item[]"]').each(function () {
            if (currentValues.includes($(this).val())) {
                $(this).prop('checked', true);
            }
        });

        $(document).off('click', '#complete-product-select').on('click', '#complete-product-select', function () {
            const selected = $('input[name="item[]"]:checked').map(function () {
                return $(this).val();
            }).get();

            $('#products').val(selected.join(',')).trigger('input').trigger('change');
            $('#btn_products').trigger('input').trigger('change');

            if (selected.length > 0) {
                $('#btn_products').attr('type', 'hidden');

                if ($('#product_complete').length === 0) {
                    $('#btn_products').after(`
                <div class="input-complete" id="product_complete">
                    <i class="bi bi-check-circle-fill color-green pl5"></i>
                    상품이 정상적으로 선택되어 있습니다
                </div>
            `);
                }
            } else {
                $('#btn_products').attr('type', 'text');
                $('#product_complete').remove();
            }

            $('#products').trigger('input').trigger('change');

            $("#modal,body").removeClass("active");
            $("#ajax-modal").html('');
        });
    })();
</script>
