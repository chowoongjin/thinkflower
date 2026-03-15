<div class="modal modal-small" id="modal-suju-item">
    <div class="modal__head">
        <div class="flex">
            <div class="flex__col">
                <h2 class="modal-title">배송가능지역 (2/2)</h2>
            </div>
            <div class="flex__col">
                <button type="button" class="modal-close">모달닫기</button>
            </div>
        </div>
    </div>

    <div class="modal__body">
        <div class="grid-2">
            @foreach ([$leftRows, $rightRows] as $tableIndex => $rows)
                <div class="grid__col">
                    <table class="table-data style3">
                        <colgroup>
                            <col style="width:140px;min-width:140px">
                            <col style="width:50px;min-width:50px;max-width:50px">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>대표 지역구</th>
                            <th>선택</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($rows as $i => $row)
                            <tr>
                                <td>
                                    <label for="local2-{{ $tableIndex }}-{{ $i }}">{{ $row['label'] }}</label>
                                </td>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="local2[]"
                                        value="{{ $row['value'] }}"
                                        data-sido="{{ $row['sido'] }}"
                                        data-is-all="{{ $row['is_all'] ? '1' : '0' }}"
                                        id="local2-{{ $tableIndex }}-{{ $i }}"
                                    >
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="mt20">
            <button type="button" class="btn btn-fluid btn-green fs18" id="complete-region-select">
                배송가능지역 선택완료 <i class="bi bi-check-circle-fill pl5"></i>
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const hiddenInput = document.getElementById('delivery_areas');
        const currentValues = hiddenInput && hiddenInput.value
            ? hiddenInput.value.split(',').map(v => v.trim()).filter(Boolean)
            : [];

        $('input[name="local2[]"]').each(function () {
            if (currentValues.includes($(this).val())) {
                $(this).prop('checked', true);
            }
        });

        $(document).off('change.modal3', 'input[name="local2[]"]').on('change.modal3', 'input[name="local2[]"]', function () {
            const $this = $(this);
            const sido = $this.data('sido');
            const isAll = String($this.data('is-all')) === '1';

            if (isAll) {
                $('input[name="local2[]"][data-sido="' + sido + '"]').prop('checked', $this.is(':checked'));
                return;
            }

            if (!$this.is(':checked')) {
                $('input[name="local2[]"][data-sido="' + sido + '"][data-is-all="1"]').prop('checked', false);
                return;
            }

            const $children = $('input[name="local2[]"][data-sido="' + sido + '"][data-is-all="0"]');
            const $checkedChildren = $children.filter(':checked');
            const $allBox = $('input[name="local2[]"][data-sido="' + sido + '"][data-is-all="1"]');

            if ($children.length && $children.length === $checkedChildren.length) {
                $allBox.prop('checked', true);
            } else {
                $allBox.prop('checked', false);
            }
        });

        $(document).off('click.modal3', '#complete-region-select').on('click.modal3', '#complete-region-select', function () {
            let selected = $('input[name="local2[]"]:checked').filter(function () {
                return String($(this).data('is-all')) !== '1';
            }).map(function () {
                return $(this).val();
            }).get();

            // 세종처럼 하위 지역이 없는 경우 처리
            if (selected.length === 0) {
                const sido = ($('#selected_sido').val() || '').trim();
                const sidoLabel = ($('#selected_sido_label').val() || '').trim();

                if (sido === '세종특별자치시' || sidoLabel === '세종특별자치시') {
                    selected = ['세종특별자치시 세종 전체'];
                }
            }

            $('#delivery_areas').val(selected.join(',')).trigger('input').trigger('change');
            $('#btn_regions').trigger('input').trigger('change');

            if (selected.length > 0) {
                $('#btn_regions').attr('type', 'hidden');

                if ($('#region_complete').length === 0) {
                    $('#btn_regions').after(`
                <div class="input-complete" id="region_complete">
                    <i class="bi bi-check-circle-fill color-green pl5"></i>
                    배송지역이 정상적으로 선택되어 있습니다
                </div>
            `);
                }
            } else {
                $('#btn_regions').attr('type', 'text');
                $('#region_complete').remove();
            }

            $('#delivery_areas').trigger('input').trigger('change');

            $("#modal,body").removeClass("active");
            $("#ajax-modal").html('');
        });
    })();
</script>
