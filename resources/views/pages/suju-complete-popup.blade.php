@extends('layouts.popup')

@section('content')
    @if ($errors->any())
        <script>
            alert(@json($errors->first()));
        </script>
    @endif

    @php
        $completedBase = $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at) : now();

        $defaultCompletedDate = old('completed_date', $completedBase->format('Y-m-d'));
        $defaultCompletedHour = old('completed_hour', $completedBase->format('H'));

        $minuteUnits = ['00','10','20','30','40','50'];
        $baseMinute = $completedBase->format('i');
        $defaultCompletedMinute = old('completed_minute');

        if (!$defaultCompletedMinute) {
            $defaultCompletedMinute = '00';

            foreach ($minuteUnits as $minute) {
                if ((int) $minute >= (int) $baseMinute) {
                    $defaultCompletedMinute = $minute;
                    break;
                }
            }
        }

        $defaultReceiverName = old('receiver_name', $order->receiver_name);
        $defaultReceiverRelation = old('receiver_relation', $order->receiver_relation);
    @endphp

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

            <form id="complete-store-form"
                  method="POST"
                  action="{{ $formAction ?? route('suju-list.complete-store', $order->order_no) }}"
                  enctype="multipart/form-data">
                @csrf

                <h3 class="mt20 fs16 mb10">
                    인수자 정보등록
                    <span class="color-gray300 fs14 pl8">사진은 업로드가 완료되면, 잠시 뒤 반영됩니다.</span>
                </h3>

                <div class="photoBoxWrap">
                    <div class="photoBox" data-photo-field="photo_shop">
                        <h3>매장사진</h3>
                        <div class="photoBox__content">
                            <input type="file" name="photo_shop" id="photo_shop" accept="image/*,application/pdf">

                            @if(!empty($photoShop?->file_path))
                                <label for="photo_shop" data-role="photo-preview">
                                    <img src="{{ $photoShop->file_path }}" alt="매장사진">
                                </label>
                            @else
                                <label for="photo_shop" data-role="photo-preview">
                                    <i class="bi bi-image"></i> 이미지 업로드
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="photoBox" data-photo-field="photo_site">
                        <h3>현장사진</h3>
                        <div class="photoBox__content">
                            <input type="file" name="photo_site" id="photo_site" accept="image/*,application/pdf">

                            @if(!empty($photoSite?->file_path))
                                <label for="photo_site" data-role="photo-preview">
                                    <img src="{{ $photoSite->file_path }}" alt="현장사진">
                                </label>
                            @else
                                <label for="photo_site" data-role="photo-preview">
                                    <i class="bi bi-image"></i> 이미지 업로드
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="photoBox" data-photo-field="photo_extra">
                        <h3>추가사진</h3>
                        <div class="photoBox__content">
                            <input type="file" name="photo_extra" id="photo_extra" accept="image/*,application/pdf">

                            @if(!empty($photoExtra?->file_path))
                                <label for="photo_extra" data-role="photo-preview">
                                    <img src="{{ $photoExtra->file_path }}" alt="추가사진">
                                </label>
                            @else
                                <label for="photo_extra" data-role="photo-preview">
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
                                                   value="{{ $defaultCompletedDate }}"
                                                   style="width:120px;">
                                        </div>
                                        <div class="col">
                                            <select name="completed_hour" id="completed_hour" style="width:110px;">
                                                <option value="">시간선택</option>
                                                @for ($i = 0; $i <= 23; $i++)
                                                    @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                    <option value="{{ $hour }}" @selected($defaultCompletedHour === $hour)>
                                                        {{ (int) $hour }}시
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select name="completed_minute" id="completed_minute" style="width:65px;">
                                                @foreach (['00','10','20','30','40','50'] as $minute)
                                                    <option value="{{ $minute }}" @selected($defaultCompletedMinute === $minute)>
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
                                               value="{{ $defaultReceiverName }}"
                                               placeholder="인수자 입력"
                                               style="min-width:295px">
                                        <input type="text"
                                               name="receiver_relation"
                                               value="{{ $defaultReceiverRelation }}"
                                               placeholder="관계 입력"
                                               class="ml5">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                    <div class="flex__col">
                        <button type="submit" id="btn-complete-store" class="btn btn-primary">배송완료</button>
                    </div>
                </section>
            </form>
        </div>
    </div>
@include('partials.loading-modal')
@endsection

@push('styles')
    <style>
        .photoBox__content img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .photoBox.is-uploading .photoBox__content {
            position: relative;
        }

        .photoBox.is-uploading .photoBox__content::after {
            content: '업로드 처리중...';
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.82);
            font-size: 13px;
            color: #333;
            z-index: 5;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function () {
            const uploadUrl = @json($photoUploadUrl ?? route('suju-list.upload-photo', $order->order_no));
            const statusUrl = @json($photoUploadStatusUrl ?? route('suju-list.photo-upload-status', $order->order_no));

            let uploadStatusPolling = null;
            let completeSubmitting = false;

            function buildPreviewHtml(fieldName, imageUrl) {
                const labelFor = fieldName;
                const altMap = {
                    photo_shop: '매장사진',
                    photo_site: '현장사진',
                    photo_extra: '추가사진'
                };

                if (imageUrl) {
                    return `<label for="${labelFor}" data-role="photo-preview"><img src="${imageUrl}" alt="${altMap[fieldName] || '사진'}"></label>`;
                }

                return `<label for="${labelFor}" data-role="photo-preview"><i class="bi bi-image"></i> 이미지 업로드</label>`;
            }

            function setUploadingState(fieldName, isUploading) {
                const $box = $(`.photoBox[data-photo-field="${fieldName}"]`);
                $box.toggleClass('is-uploading', isUploading);
            }

            function refreshPhotoPreview(fieldName, imageUrl) {
                const $box = $(`.photoBox[data-photo-field="${fieldName}"]`);
                $box.find('[data-role="photo-preview"]').replaceWith(buildPreviewHtml(fieldName, imageUrl));
            }

            function stopPollingIfIdle(pendingFields) {
                if (!pendingFields || pendingFields.length === 0) {
                    if (uploadStatusPolling) {
                        clearInterval(uploadStatusPolling);
                        uploadStatusPolling = null;
                    }

                    if (window.opener && !window.opener.closed) {
                        window.opener.location.reload();
                    }
                }
            }

            function pollUploadStatus() {
                $.ajax({
                    url: statusUrl,
                    type: 'GET',
                    success: function (res) {
                        if (!res || !res.success) {
                            return;
                        }

                        refreshPhotoPreview('photo_shop', res.photos?.photo_shop || null);
                        refreshPhotoPreview('photo_site', res.photos?.photo_site || null);
                        refreshPhotoPreview('photo_extra', res.photos?.photo_extra || null);

                        const pending = Array.isArray(res.pending_fields) ? res.pending_fields : [];

                        ['photo_shop', 'photo_site', 'photo_extra'].forEach(function (field) {
                            setUploadingState(field, pending.includes(field));
                        });

                        stopPollingIfIdle(pending);
                    }
                });
            }

            function startPolling() {
                if (uploadStatusPolling) {
                    return;
                }

                pollUploadStatus();

                uploadStatusPolling = setInterval(function () {
                    pollUploadStatus();
                }, 2000);
            }

            function uploadCompletePhoto(fieldName, file) {
                const formData = new FormData();
                formData.append('_token', @json(csrf_token()));
                formData.append('photo_field', fieldName);
                formData.append('photo_file', file);

                setUploadingState(fieldName, true);

                $.ajax({
                    url: uploadUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        closeLoadingModal();
                        if (res.message) {
                            alert(res.message);
                        }

                        if (res.queued) {
                            startPolling();
                        } else {
                            setUploadingState(fieldName, false);
                        }
                    },
                    error: function (xhr) {
                        closeLoadingModal();
                        setUploadingState(fieldName, false);

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
                const mmList = ['00', '10', '20', '30', '40', '50'];
                let mm = '00';

                for (let i = 0; i < mmList.length; i++) {
                    if (parseInt(mmList[i], 10) >= now.getMinutes()) {
                        mm = mmList[i];
                        break;
                    }

                    if (i === mmList.length - 1) {
                        mm = '00';
                    }
                }

                if (mm === '00' && now.getMinutes() > 50) {
                    now.setHours(now.getHours() + 1);
                }

                const yyyy = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const hh = String(now.getHours()).padStart(2, '0');

                $('#completed_date').val(`${yyyy}-${month}-${dd}`);
                $('#completed_hour').val(hh);
                $('#completed_minute').val(mm);
            });

            $(document).on('submit', '#complete-store-form', function (e) {
                if (completeSubmitting) {
                    e.preventDefault();
                    return;
                }

                const receiverName = $.trim($('input[name="receiver_name"]').val());
                const completedDate = $.trim($('#completed_date').val());
                const completedHour = $.trim($('#completed_hour').val());
                const completedMinute = $.trim($('#completed_minute').val());

                if (!completedDate || !completedHour || !completedMinute || !receiverName) {
                    return;
                }

                completeSubmitting = true;
                $('#btn-complete-store').prop('disabled', true).text('처리중...');
                openLoadingModal();
            });
        });
    </script>
@endpush
