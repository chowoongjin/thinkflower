@extends('layouts.popup')

@section('content')
    @if (session('success'))
        <script>
            alert(@json(session('success')));

            if (window.opener && !window.opener.closed) {
                window.opener.location.reload();
            }
        </script>
    @endif
    @if ($errors->any())
        <script>
            alert(@json($errors->first()));
        </script>
    @endif

    <div id="popup">
        <div class="popup__head">
            <h1 class="popup-title">주문정보</h1>
        </div>

        <div class="popup__body">
            <div class="headBox">
                <div class="flex">
                    <div class="flex__col">
                        <strong>
                            ✔️ 주문번호:
                            <span class="color-purple">{{ $order->order_no }}</span>
                            | 본부문의 :
                            <span class="color-purple">1688-1840</span>
                        </strong>
                    </div>
                    <div class="flex__col">
                        본부문의 시 주문번호를 불러주세요
                    </div>
                </div>
            </div>

            <table class="table-data style2-1 mt20">
                <colgroup>
                    <col style="width:90px;min-width:90px">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>주문상품</th>
                    <td>
                        <span class="color-primary">{{ $order->product_name }}</span>
                        {{ number_format($order->order_amount) }}원
                    </td>
                </tr>
                <tr>
                    <th>배송지주소</th>
                    <td>{{ trim(($order->delivery_addr1 ?? '') . ' ' . ($order->delivery_addr2 ?? '')) }}</td>
                </tr>
                <tr>
                    <th>경조사어</th>
                    <td>{{ $order->ribbon_phrase ?: '-' }}</td>
                </tr>
                <tr>
                    <th>보내는분</th>
                    <td>{{ $order->sender_name ?: '-' }}</td>
                </tr>
                </tbody>
            </table>

            <form method="POST"
                  action="{{ route('suju-list.complete-store', $order->order_no) }}"
                  enctype="multipart/form-data">
                @csrf

                <h3 class="mt20 fs16 mb10">
                    인수자 정보등록
                    <span class="color-gray300 fs14 pl8">사진은 업로드 시 즉시 저장됩니다.</span>
                </h3>

                <div class="photoBoxWrap">
                    <div class="photoBox">
                        <h3>매장사진</h3>
                        <div class="photoBox__content">
                            <input type="file" name="photo_shop" id="photo_shop" accept="image/*">

                            @if(!empty($photoShop?->file_path))
                                <label for="photo_shop">
                                    <img src="{{ $photoShop->file_path }}" alt="매장사진">
                                </label>
                            @else
                                <label for="photo_shop">
                                    <i class="bi bi-image"></i> 이미지 업로드
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="photoBox">
                        <h3>현장사진</h3>
                        <div class="photoBox__content">
                            <input type="file" name="photo_site" id="photo_site" accept="image/*">

                            @if(!empty($photoSite?->file_path))
                                <label for="photo_site">
                                    <img src="{{ $photoSite->file_path }}" alt="현장사진">
                                </label>
                            @else
                                <label for="photo_site">
                                    <i class="bi bi-image"></i> 이미지 업로드
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="photoBox">
                        <h3>추가사진</h3>
                        <div class="photoBox__content">
                            <input type="file" name="photo_extra" id="photo_extra" accept="image/*">

                            @if(!empty($photoExtra?->file_path))
                                <label for="photo_extra">
                                    <img src="{{ $photoExtra->file_path }}" alt="추가사진">
                                </label>
                            @else
                                <label for="photo_extra">
                                    <i class="bi bi-image"></i> 이미지 업로드
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <section class="flex mt20" id="flex-7-3">
                    <div class="flex__col">
                        <table class="table-data style4">
                            <colgroup>
                                <col style="width:100px;min-width:100px">
                                <col>
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>배송완료시간</th>
                                <td>
                                    <div class="input-group-column">
                                        <div class="col">
                                            <div class="input-group-checkbox">
                                                <input type="checkbox" id="completed_now">
                                                <label for="completed_now">현재시간</label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <input type="text"
                                                   name="completed_date"
                                                   id="completed_date"
                                                   class="datepicker"
                                                   value="{{ old('completed_date', now()->format('Y-m-d')) }}"
                                                   style="width:120px;">
                                        </div>
                                        <div class="col">
                                            <select name="completed_hour" id="completed_hour" style="width:110px;">
                                                <option value="">시간선택</option>
                                                @for ($i = 0; $i <= 23; $i++)
                                                    @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                    <option value="{{ $hour }}" @selected(old('completed_hour', now()->format('H')) === $hour)>
                                                        {{ (int) $hour }}시
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select name="completed_minute" id="completed_minute" style="width:65px;">
                                                @foreach (['00','10','20','30','40','50'] as $minute)
                                                    <option value="{{ $minute }}" @selected(old('completed_minute', '00') === $minute)>
                                                        {{ $minute }}분
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>인수자</th>
                                <td>
                                    <div class="inline-flex">
                                        <input type="text"
                                               name="receiver_name"
                                               value="{{ old('receiver_name') }}"
                                               placeholder="인수자 입력"
                                               style="min-width:295px">
                                        <input type="text"
                                               name="receiver_relation"
                                               value="{{ old('receiver_relation') }}"
                                               placeholder="관계 입력"
                                               class="ml5">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex__col">
                        <button type="submit" class="btn btn-primary">배송완료</button>
                    </div>
                </section>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .photoBox__content img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
    </style>
@endpush

@push('scripts')
    @push('scripts')
        <script>
            $(function () {
                function uploadCompletePhoto(fieldName, file) {
                    const formData = new FormData();
                    formData.append('_token', @json(csrf_token()));
                    formData.append('photo_field', fieldName);
                    formData.append('photo_file', file);

                    $.ajax({
                        url: @json(route('suju-list.upload-photo', $order->order_no)),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (res) {
                            if (res.message) {
                                alert(res.message);
                            }

                            if (res.reload_parent && window.opener && !window.opener.closed) {
                                window.opener.location.reload();
                            }

                            if (res.reload) {
                                window.location.reload();
                            }
                        },
                        error: function (xhr) {
                            const msg =
                                xhr.responseJSON?.message ||
                                xhr.responseJSON?.errors?.photo_file?.[0] ||
                                xhr.responseJSON?.errors?.photo_field?.[0] ||
                                '사진 업로드에 실패했습니다.';

                            alert(msg);
                        }
                    });
                }

                $(document).on('change', '#photo_shop, #photo_site, #photo_extra', function () {
                    const file = this.files && this.files[0] ? this.files[0] : null;
                    if (!file) return;

                    uploadCompletePhoto(this.name, file);
                });

                $(document).on('change', '#completed_now', function () {
                    if (!$(this).is(':checked')) {
                        return;
                    }

                    const now = new Date();
                    const minuteOptions = [0, 10, 20, 30, 40, 50];

                    let hour = now.getHours();
                    let minute = now.getMinutes();

                    let roundedMinute = minuteOptions.find(m => m >= minute);

                    if (roundedMinute === undefined) {
                        roundedMinute = 0;
                        hour += 1;
                        if (hour >= 24) {
                            now.setDate(now.getDate() + 1);
                            hour = 0;
                        }
                    }

                    const yyyy = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const dd = String(now.getDate()).padStart(2, '0');

                    $('#completed_date').val(`${yyyy}-${month}-${dd}`);
                    $('#completed_hour').val(String(hour).padStart(2, '0'));
                    $('#completed_minute').val(String(roundedMinute).padStart(2, '0'));
                });
            });
        </script>
    @endpush
@endpush
