<div class="modal">
    <div class="modal__head">
        <div class="flex">
            <div class="flex__col">
                <h2 class="modal-title">정산정보 정보수정</h2>
            </div>
            <div class="flex__col">
                <button type="button" class="modal-close">모달닫기</button>
            </div>
        </div>
    </div>
    <div class="modal__body">
        <form id="settlement-info-form" method="POST" action="{{ route('my-page.settlement-info.update') }}">
            @csrf
            <table class="table-data style3-1">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>입금은행</th>
                    <td><input type="text" name="bank_name" value="{{ $shop->bank_name }}"></td>
                    <th>예금주명</th>
                    <td><input type="text" name="bank_holder" value="{{ $shop->bank_holder }}"></td>
                </tr>
                <tr>
                    <th>계좌번호</th>
                    <td colspan="3"><input type="text" name="bank_account" value="{{ $shop->bank_account }}"></td>
                </tr>
                </tbody>
            </table>
            <div class="mt20">
                <button type="submit" class="btn btn-primary btn-fluid">정보수정</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $(document).off('submit', '#settlement-info-form').on('submit', '#settlement-info-form', function (e) {
            e.preventDefault();

            const $form = $(this);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (res) {
                    alert(res.message || '정산 정보가 수정되었습니다.');
                    $('#modal, body').removeClass('active');
                    $('#ajax-modal').empty();

                    if (res.redirect) {
                        window.location.href = res.redirect;
                    } else {
                        window.location.reload();
                    }
                },
                error: function (xhr) {
                    let message = '정보수정 중 오류가 발생했습니다.';

                    if (xhr.responseJSON?.errors) {
                        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        if (firstKey && xhr.responseJSON.errors[firstKey][0]) {
                            message = xhr.responseJSON.errors[firstKey][0];
                        }
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }

                    alert(message);
                }
            });
        });
    });
</script>
