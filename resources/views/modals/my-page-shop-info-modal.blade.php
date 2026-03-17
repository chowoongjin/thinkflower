<div class="modal">
    <div class="modal__head">
        <div class="flex">
            <div class="flex__col">
                <h2 class="modal-title">매장운영 정보수정</h2>
            </div>
            <div class="flex__col">
                <button type="button" class="modal-close">모달닫기</button>
            </div>
        </div>
    </div>
    <div class="modal__body">
        <form id="shop-info-form" method="POST" action="{{ route('my-page.shop-info.update') }}">
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
                    <th>대표 연락망</th>
                    <td><input type="text" name="main_phone" value="{{ $shop->main_phone }}"></td>
                    <th>비상 연락망</th>
                    <td><input type="text" name="sub_phone" value="{{ $shop->sub_phone }}"></td>
                </tr>
                <tr>
                    <th>팩스번호</th>
                    <td><input type="text" name="fax" value="{{ $shop->fax }}"></td>
                    <th>화원사명</th>
                    <td><input type="text" name="shop_name" value="{{ $shop->shop_name }}"></td>
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
        $(document).off('submit', '#shop-info-form').on('submit', '#shop-info-form', function (e) {
            e.preventDefault();

            const $form = $(this);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (res) {
                    alert(res.message || '매장운영 정보가 수정되었습니다.');
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
