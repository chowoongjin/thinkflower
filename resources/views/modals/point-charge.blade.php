@extends('layouts.sub')

@section('content')
    <div class="modal modal-small" id="modal-point">
        <div class="modal__head">
            <div class="flex">
                <div class="flex__col">
                    <h1 class="modal-title">포인트 간편충전</h1>
                </div>
                <div class="flex__col">
                    <button type="button" class="modal-close" onclick="window.close();">팝업닫기</button>
                </div>
            </div>
        </div>

        <div class="modal__body">
            <form id="point-charge-form" method="POST" action="{{ route('point-charge.test.store') }}">
                @csrf

                <input type="hidden" name="charge_method" id="charge_method">

                <div class="field">
                    <label>충전 금액<em class="color-orange pl5">*</em></label>
                    <input type="text" name="charge_amount" id="charge_amount" placeholder="충전 금액을 입력해주세요">
                </div>

                <ul class="list-column-3">
                    <li>
                        <input type="radio" name="amount" id="amount1">
                        <label for="amount1">+ 10,000원</label>
                    </li>
                    <li>
                        <input type="radio" name="amount" id="amount2">
                        <label for="amount2">+ 30,000원</label>
                    </li>
                    <li>
                        <input type="radio" name="amount" id="amount3">
                        <label for="amount3">+ 50,000원</label>
                    </li>
                    <li>
                        <input type="radio" name="amount" id="amount4">
                        <label for="amount4">+ 100,000원</label>
                    </li>
                    <li>
                        <input type="radio" name="amount" id="amount5">
                        <label for="amount5">+ 150,000원</label>
                    </li>
                    <li>
                        <input type="radio" name="amount" id="amount6">
                        <label for="amount6">+ 200,000원</label>
                    </li>
                </ul>

                <div class="mt30">
                    <div class="flex">
                        <div class="flex__col">
                            <button type="button" class="btn btn-blue btn-fluid fw600 btn-charge-submit" data-method="bank_transfer">
                                무통장입금
                            </button>
                        </div>
                        <div class="flex__col">
                            <button type="button" class="btn btn-blue btn-fluid fw600 btn-charge-submit" data-method="card">
                                간편결제(카드)
                            </button>
                        </div>
                    </div>
                </div>

                <p class="mt14 color-blue align-center">간편결제 시 결제사 수수료 3% 제외한 금액이 충전됩니다</p>
            </form>
        </div>
    </div>

    <script>
        $(function () {
            $(document).on('change', 'input[name="amount"]', function () {
                const labelText = $(this).next('label').text() || '';
                const amount = labelText.replace(/[^0-9]/g, '');
                $('#charge_amount').val(amount ? Number(amount).toLocaleString('ko-KR') : '');
            });

            $(document).on('input', '#charge_amount', function () {
                const onlyNum = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(onlyNum ? Number(onlyNum).toLocaleString('ko-KR') : '');
                $('input[name="amount"]').prop('checked', false);
            });

            $(document).on('click', '.btn-charge-submit', function (e) {
                e.preventDefault();

                const method = $(this).data('method') || '';
                const amount = ($('#charge_amount').val() || '').replace(/[^0-9]/g, '');

                if (!amount || parseInt(amount, 10) <= 0) {
                    alert('충전 금액을 입력해 주세요.');
                    return;
                }

                $.ajax({
                    url: $('#point-charge-form').attr('action'),
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        charge_amount: amount,
                        charge_method: method
                    },
                    success: function (res) {
                        alert(res.message || '포인트가 충전되었습니다.');

                        if (window.opener && !window.opener.closed) {
                            window.opener.location.reload();
                        }
                    },
                    error: function (xhr) {
                        const msg =
                            xhr.responseJSON?.message ||
                            xhr.responseJSON?.errors?.charge_amount?.[0] ||
                            xhr.responseJSON?.errors?.charge_method?.[0] ||
                            '포인트 충전에 실패했습니다.';

                        alert(msg);
                    }
                });
            });
        });
    </script>
@endsection
