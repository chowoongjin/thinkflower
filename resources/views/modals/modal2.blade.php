<div class="modal modal-small" id="modal-suju-item">
    <div class="modal__head">
        <div class="flex">
            <div class="flex__col">
                <h2 class="modal-title">배송가능지역 (1/2)</h2>
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
                                    <label for="local-{{ $tableIndex }}-{{ $i }}">{{ $row }}</label>
                                </td>
                                <td>
                                    <input type="checkbox" name="local[]" value="{{ $row }}" id="local-{{ $tableIndex }}-{{ $i }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="mt20">
            <button type="button" class="btn btn-fluid btn-green fs18" id="next-local">
                상세 지역구 선택하기 <i class="bi bi-play-fill pl5"></i>
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        $(document).off('change.modal2_single', '#ajax-modal input[name="local[]"]');
        $(document).off('click.modal2_next', '#ajax-modal #next-local');

        // 하나만 선택되도록
        $(document).on('change.modal2_single', '#ajax-modal input[name="local[]"]', function () {
            if ($(this).is(':checked')) {
                $('#ajax-modal input[name="local[]"]').not(this).prop('checked', false);
            }
        });

        // 다음 버튼
        $(document).on('click.modal2_next', '#ajax-modal #next-local', function (e) {
            e.preventDefault();

            const $selected = $('#ajax-modal input[name="local[]"]:checked').first();
            const region = $.trim($selected.val() || '');

            if (!region) {
                alert('지역을 선택해 주세요.');
                return;
            }

            // 세종은 바로 완료 처리
            if (region === '세종특별자치시') {
                $('#delivery_areas').val('세종특별자치시 세종 전체').trigger('input').trigger('change');
                $('#btn_regions').trigger('input').trigger('change');

                $('#btn_regions').attr('type', 'hidden');

                if ($('#region_complete').length === 0) {
                    $('#btn_regions').after(`
                        <div class="input-complete" id="region_complete">
                            <i class="bi bi-check-circle-fill color-green pl5"></i>
                            배송지역이 정상적으로 선택되어 있습니다
                        </div>
                    `);
                }

                $("#modal,body").removeClass("active");
                $("#ajax-modal").html('');
                return;
            }

            // 기존 다음 단계 이동
            const url = '{{ url('/modal/regions/detail') }}' + '?region=' + encodeURIComponent(region);
            modal(url);
        });
    })();
</script>
