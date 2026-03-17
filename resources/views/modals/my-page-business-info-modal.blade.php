<div class="modal">
    <div class="modal__head">
        <div class="flex">
            <div class="flex__col">
                <h2 class="modal-title">사업자 정보수정</h2>
            </div>
            <div class="flex__col">
                <button type="button" class="modal-close">모달닫기</button>
            </div>
        </div>
    </div>
    <div class="modal__body">
        <form>
            <table class="table-data style3-1">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>사업자정보</th>
                    <td><input type="text" name="business_no" value="{{ $shop->business_no }}"></td>
                    <th>대표자명</th>
                    <td><input type="text" name="owner_name" value="{{ $shop->owner_name }}"></td>
                </tr>
                <tr>
                    <th>사업자소재지</th>
                    <td colspan="3">
                        <input type="text" name="business_address" value="{{ $businessAddress }}">
                    </td>
                </tr>
                <tr>
                    <th>계산서수령</th>
                    <td colspan="3">
                        <input type="text" name="tax_email" value="{{ $shop->tax_email }}">
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="mt20">
                <button type="button" class="btn btn-primary btn-fluid">정보수정</button>
            </div>
        </form>
    </div>
</div>
