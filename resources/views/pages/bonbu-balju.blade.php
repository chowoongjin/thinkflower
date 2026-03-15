@extends('layouts.app')

@section('content')
    @php
        $bonbuBaljuStoreRoute = route('bonbu-balju.order.store');
    @endphp
    <div id="content__body" style="width:900px;position:relative">

        <div id="bonbu-balju">

            <aside id="sidePanel">
                <section class="panel">
                    <div class="panel__head">
                        <h2 class="panel-title">본부 운영정보</h2>
                    </div>
                    <div class="panel__body">
                        <ul class="list-qa">
                            <li>
                                <div class="q">본부 연락처</div>
                                <div class="a">1688-1840</div>
                            </li>
                            <li>
                                <div class="q">본부 카카오톡</div>
                                <div class="a">H16681840</div>
                            </li>
                            <li>
                                <div class="q">본부 영업시간</div>
                                <div class="a">09:00 ~ 19:00</div>
                            </li>
                        </ul>
                    </div>
                </section>

                <section class="panel panel-preview">
                    <div class="panel__head">
                        <h2 class="panel-title">첨부이미지 확인</h2>
                    </div>
                    <div class="panel__body">
                        <img src="{{ asset('assets/img/noimg.jpg') }}" alt="noimg" id="product_image_preview">
                    </div>
                </section>

                <section class="panel">
                    <div class="panel__head">
                        <h2 class="panel-title">주문정보 확인</h2>
                    </div>
                    <div class="panel__body">
                        <ul class="list-qa">
                            <li>
                                <div class="q">상품상세</div>
                                <div class="a" id="order_product_preview">미 입력 상태</div>
                            </li>
                            <li>
                                <div class="q">발주금액</div>
                                <div class="a color-gray400 fw400" id="order_price2_preview">미 입력 상태</div>
                            </li>
                            <li>
                                <div class="q">배송장소</div>
                                <div class="a" id="order_delivery_address_preview">미 입력 상태</div>
                            </li>
                            <li>
                                <div class="q">배송일시</div>
                                <div class="a" id="order_delivery_datetime_preview">미 입력 상태</div>
                            </li>
                            <li>
                                <div class="q">받는사람</div>
                                <div class="a" id="order_receiver_preview">미 입력 상태</div>
                            </li>
                        </ul>
                    </div>
                </section>
            </aside>

            <section>
                <h2 class="tt">✔️ 본부발주<small>본사 수발주 사업부로 발주되어 중개됩니다.</small></h2>
            </section>

            <section class="mt30">
                <table class="table-data">
                    <colgroup>
                        <col style="width:140px">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>본부공지사항</th>
                        <td>
                            @forelse ($generalNotices as $notice)
                                <p class="color-primary">
                                    {{ $notice->title }}
                                </p>
                            @empty
                                <p class="color-gray400">등록된 본부공지사항이 없습니다.</p>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <th><span class="color-orange">특별</span>공지사항</th>
                        <td>
                            @forelse ($specialNotices as $notice)
                                <p class="color-orange">
                                    {{ $notice->title }}
                                </p>
                            @empty
                                <p class="color-gray400">등록된 특별공지사항이 없습니다.</p>
                            @endforelse
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>
            @if ($errors->any())
                <script>
                    alert(@json($errors->first()));
                </script>
            @endif

            @if (session('success_redirect_order_list'))
                <script>
                    alert(@json(session('success_redirect_order_list')));
                    location.href = @json(route('order-list'));
                </script>
            @endif
            <form id="bonbu-balju-form" method="POST" action="{{ $bonbuBaljuStoreRoute }}" enctype="multipart/form-data">
                @csrf

            <section class="mt30">
                <table class="table-data collapse">
                    <colgroup>
                        <col style="width:140px">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>상품간편선택 <em>*</em></th>
                        <td>
                            <div class="checkbox">
                                <ul>
                                    <li><input type="checkbox" name="quick_item" value="근조3단(기본)" id="item1" {{ old('product_name') === '근조3단(기본)' ? 'checked' : '' }}><label for="item1">근조3단(기본)</label></li>
                                    <li><input type="checkbox" name="quick_item" value="근조3단(고급)" id="item2" {{ old('product_name') === '근조3단(고급)' ? 'checked' : '' }}><label for="item2">근조3단(고급)</label></li>
                                    <li><input type="checkbox" name="quick_item" value="근조3단(특대)" id="item3" {{ old('product_name') === '근조3단(특대)' ? 'checked' : '' }}><label for="item3">근조3단(특대)</label></li>
                                    <li><input type="checkbox" name="quick_item" value="관엽식물" id="item4" {{ old('product_name') === '관엽식물' ? 'checked' : '' }}><label for="item4">관엽식물</label></li>
                                    <li><input type="checkbox" name="quick_item" value="꽃바구니" id="item5" {{ old('product_name') === '꽃바구니' ? 'checked' : '' }}><label for="item5">꽃바구니</label></li>
                                    <li><input type="checkbox" name="quick_item" value="서양란" id="item6" {{ old('product_name') === '서양란' ? 'checked' : '' }}><label for="item6">서양란</label></li>
                                    <li><input type="checkbox" name="quick_item" value="동양란" id="item7" {{ old('product_name') === '동양란' ? 'checked' : '' }}><label for="item7">동양란</label></li>
                                    <li><input type="checkbox" name="quick_item" value="축하3단(기본)" id="item8" {{ old('product_name') === '축하3단(기본)' ? 'checked' : '' }}><label for="item8">축하3단(기본)</label></li>
                                    <li><input type="checkbox" name="quick_item" value="축하3단(고급)" id="item9" {{ old('product_name') === '축하3단(고급)' ? 'checked' : '' }}><label for="item9">축하3단(고급)</label></li>
                                    <li><input type="checkbox" name="quick_item" value="축하3단(특대)" id="item10" {{ old('product_name') === '축하3단(특대)' ? 'checked' : '' }}><label for="item10">축하3단(특대)</label></li>
                                    <li><input type="checkbox" name="quick_item" value="근조바구니" id="item11" {{ old('product_name') === '근조바구니' ? 'checked' : '' }}><label for="item11">근조바구니</label></li>
                                    <li><input type="checkbox" name="quick_item" value="근조쌀화환" id="item12" {{ old('product_name') === '근조쌀화환' ? 'checked' : '' }}><label for="item12">근조쌀화환</label></li>
                                    <li><input type="checkbox" name="quick_item" value="축하쌀화환" id="item13" {{ old('product_name') === '축하쌀화환' ? 'checked' : '' }}><label for="item13">축하쌀화환</label></li>
                                    <li><input type="checkbox" name="quick_item" value="근조오브제" id="item14" {{ old('product_name') === '근조오브제' ? 'checked' : '' }}><label for="item14">근조오브제</label></li>
                                </ul>
                                <input type="hidden" name="product_name" id="product_name_hidden" value="{{ old('product_name') }}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>상품상세입력<em>*</em></th>
                        <td>
                            <input type="text" id="product_detail_input" name="product_detail" value="{{ old('product_detail') }}" placeholder="상품의 상세정보를 입력해 주세요">
                        </td>
                    </tr>
                    <tr>
                        <th>상품이미지</th>
                        <td>
                            <div class="input-group-file2">
                                <input type="file" id="product_image_file" name="product_image_file" accept="image/*">
                                <label for="product_image_file">이미지 첨부</label>
                                <input type="text" id="product_image_url" name="product_image_input_url" value="{{ old('product_image_input_url') }}" placeholder="상품이미지 주소가 있다면 붙여 넣어 주세요">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>원청금액<em>*</em></th>
                        <td>
                            <div class="moneyBox">
                                <input type="text" value="{{ old('original_amount', '50,000') }}" id="price1-input">
                                <input type="hidden" name="original_amount" id="original_amount_hidden" value="{{ old('original_amount', 50000) }}">
                                <ul class="money-list">
                                    <li><input type="radio" name="price1" value="10000" id="price1-1"><label for="price1-1">+ 1만원</label></li>
                                    <li><input type="radio" name="price1" value="50000" id="price1-2"><label for="price1-2">+ 5만원</label></li>
                                    <li><input type="radio" name="price1" value="60000" id="price1-3"><label for="price1-3">+ 6만원</label></li>
                                    <li><input type="radio" name="price1" value="70000" id="price1-4"><label for="price1-4">+ 7만원</label></li>
                                    <li><input type="radio" name="price1" value="80000" id="price1-5"><label for="price1-5">+ 8만원</label></li>
                                    <li><input type="radio" name="price1" value="100000" id="price1-6"><label for="price1-6">+ 10만원</label></li>
                                    <li><input type="radio" name="price1" value="150000" id="price1-7"><label for="price1-7">+ 15만원</label></li>
                                    <li><input type="radio" name="price1" value="0" id="price1-8"><label for="price1-8">초기화</label></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>발주금액<em>*</em></th>
                        <td>
                            <div class="moneyBox">
                                <input type="text" value="{{ old('order_amount', '40,000') }}" id="price2-input">
                                <input type="hidden" name="order_amount" id="order_amount_hidden" value="{{ old('order_amount', 40000) }}">
                                <ul class="money-list">
                                    <li><input type="radio" name="price2" value="10000" id="price2-1"><label for="price2-1">+ 1만원</label></li>
                                    <li><input type="radio" name="price2" value="50000" id="price2-2"><label for="price2-2">+ 5만원</label></li>
                                    <li><input type="radio" name="price2" value="60000" id="price2-3"><label for="price2-3">+ 6만원</label></li>
                                    <li><input type="radio" name="price2" value="70000" id="price2-4"><label for="price2-4">+ 7만원</label></li>
                                    <li><input type="radio" name="price2" value="80000" id="price2-5"><label for="price2-5">+ 8만원</label></li>
                                    <li><input type="radio" name="price2" value="100000" id="price2-6"><label for="price2-6">+ 10만원</label></li>
                                    <li><input type="radio" name="price2" value="150000" id="price2-7"><label for="price2-7">+ 15만원</label></li>
                                    <li><input type="radio" name="price2" value="0" id="price2-8"><label for="price2-8">초기화</label></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="table-data collapse mt10">
                    <colgroup>
                        <col style="width:140px">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>배달요청장소<em>*</em></th>
                        <td>
                            <div class="input-group-addr">
                                <button type="button" id="btn_delivery_address_search">주소검색</button>
                                <input type="text" id="delivery_address_input" name="delivery_addr1" value="{{ old('delivery_addr1') }}" placeholder="정확한 배송지 주소를 입력해 주세요">
                            </div>
                            <input type="hidden" name="delivery_addr2" value="{{ old('delivery_addr2') }}">
                        </td>
                    </tr>
                    <tr>
                        <th>배달요청일시<em>*</em></th>
                        <td>
                            <div class="input-group-column">
                                <div class="col">
                                    <div class="input-group-checkbox">
                                        <input type="checkbox" name="delivery_now" value="1" id="delivery_now" {{ old('is_urgent') ? 'checked' : '' }}>
                                        <label for="delivery_now">지금즉시</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <input type="text" class="datepicker" id="delivery_date" name="delivery_date" value="{{ old('delivery_date') }}" style="width:120px;">
                                </div>
                                <div class="col">
                                    <select id="delivery_hour" name="delivery_hour" style="width:110px;"></select>
                                </div>
                                <div class="col">
                                    <select id="delivery_minute" name="delivery_minute" style="width:110px;"></select>
                                </div>
                                <div class="col">
                                    <div class="input-group-checkbox">
                                        <input type="checkbox" name="delivery_time_type_ui" value="도착" id="delivery_type_arrival" {{ old('delivery_time_type') === 'arrival' ? 'checked' : '' }}>
                                        <label for="delivery_type_arrival">도착</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group-checkbox">
                                        <input type="checkbox" name="delivery_time_type_ui" value="예식" id="delivery_type_event" {{ old('delivery_time_type') === 'ceremony' ? 'checked' : '' }}>
                                        <label for="delivery_type_event">예식</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group-checkbox">
                                        <input type="checkbox" name="delivery_time_type_ui" value="행사" id="delivery_type_ceremony" {{ old('delivery_time_type') === 'event' ? 'checked' : '' }}>
                                        <label for="delivery_type_ceremony">행사</label>
                                    </div>
                                </div>
                                <input type="hidden" name="delivery_time_type" id="delivery_time_type_hidden" value="{{ old('delivery_time_type', '') }}">
                                <input type="hidden" name="is_urgent" id="is_urgent_hidden" value="{{ old('is_urgent', 0) }}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>받는고객정보<em>*</em></th>
                        <td>
                            <div class="input-group-two">
                                <input type="text" id="receiver_name_input" name="recipient_name" value="{{ old('recipient_name') }}" style="width:220px" placeholder="받는 분 성함을 입력해 주세요">
                                <input type="text" id="recipient_phone_input" name="recipient_phone" value="{{ old('recipient_phone') }}" style="width:220px" placeholder="받는 분 연락처를 입력해 주세요">
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="table-data collapse mt10">
                    <colgroup>
                        <col style="width:140px">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>간편문구 <em>*</em></th>
                        <td>
                            <div class="checkbox --expand">
                                <ul>
                                    <li><input type="checkbox" name="quick_ment" value="祝結婚" id="ment1" {{ old('quick_ment') === '祝結婚' ? 'checked' : '' }}><label for="ment1">祝結婚</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="祝華婚" id="ment2" {{ old('quick_ment') === '祝華婚' ? 'checked' : '' }}><label for="ment2">祝華婚</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="祝發展" id="ment3" {{ old('quick_ment') === '祝發展' ? 'checked' : '' }}><label for="ment3">祝發展</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="祝開業" id="ment4" {{ old('quick_ment') === '祝開業' ? 'checked' : '' }}><label for="ment4">祝開業</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="祝榮轉" id="ment5" {{ old('quick_ment') === '祝榮轉' ? 'checked' : '' }}><label for="ment5">祝榮轉</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="祝昇進" id="ment6" {{ old('quick_ment') === '祝昇進' ? 'checked' : '' }}><label for="ment6">祝昇進</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="謹弔" id="ment7" {{ old('quick_ment') === '謹弔' ? 'checked' : '' }}><label for="ment7">謹弔</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="삼가 故人의 冥福을 빕니다" id="ment8" {{ old('quick_ment') === '삼가 故人의 冥福을 빕니다' ? 'checked' : '' }}><label for="ment8">삼가 故人의 冥福을 빕니다</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="축결혼" id="ment9" {{ old('quick_ment') === '축결혼' ? 'checked' : '' }}><label for="ment9">축결혼</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="축화혼" id="ment10" {{ old('quick_ment') === '축화혼' ? 'checked' : '' }}><label for="ment10">축화혼</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="축발전" id="ment11" {{ old('quick_ment') === '축발전' ? 'checked' : '' }}><label for="ment11">축발전</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="축개업" id="ment12" {{ old('quick_ment') === '축개업' ? 'checked' : '' }}><label for="ment12">축개업</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="축영전" id="ment13" {{ old('quick_ment') === '축영전' ? 'checked' : '' }}><label for="ment13">축영전</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="축승진" id="ment14" {{ old('quick_ment') === '축승진' ? 'checked' : '' }}><label for="ment14">축승진</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="근조" id="ment15" {{ old('quick_ment') === '근조' ? 'checked' : '' }}><label for="ment15">근조</label></li>
                                    <li><input type="checkbox" name="quick_ment" value="삼가 고인의 명복을 빕니다" id="ment16" {{ old('quick_ment') === '삼가 고인의 명복을 빕니다' ? 'checked' : '' }}><label for="ment16">삼가 고인의 명복을 빕니다</label></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>경조사어<em>*</em></th>
                        <td>
                            <input type="text" id="congratulatory_message_input" name="ribbon_phrase" value="{{ old('ribbon_phrase') }}" placeholder="경조사어를 입력해 주세요 (예: 삼가 고인의 명복을 빕니다)">
                        </td>
                    </tr>
                    <tr>
                        <th>보내는분<em>*</em></th>
                        <td>
                            <input type="text" name="sender_name" value="{{ old('sender_name') }}" placeholder="보내는분을 입력해 주세요 (예: 정직한플라워 임직원 일동)">
                        </td>
                    </tr>
                    <tr>
                        <th>카드메세지</th>
                        <td>
                            <input type="text" name="card_message" value="{{ old('card_message') }}" placeholder="카드메세지가 있다면 작성해 주세요">
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="table-data collapse mt10">
                    <colgroup>
                        <col style="width:140px">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>고객요청사항<em>*</em></th>
                        <td>
                            <div class="input-group-addr">
                                <div class="input-group-checkbox mr5">
                                    <input type="checkbox" id="request_photo" {{ old('request_photo') ? 'checked' : '' }}>
                                    <label for="request_photo">현장사진요청</label>
                                </div>
                                <input type="text" id="customer_request_input" name="request_note" value="{{ old('request_note') }}" placeholder="고객 및 발주사의 요청사항이 있다면 작성해주세요">
                            </div>
                            <input type="hidden" name="request_photo" id="request_photo_hidden" value="{{ old('request_photo', 0) }}">
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="flex mt30">
                    <div class="flex__col">
                        <button type="submit" class="btn btn-primary btn-fluid" style="width:700px;margin-right:20px;border-radius:0">발주완료</button>
                    </div>
                    <div class="flex__col">
                        <button type="button" class="btn btn-gray btn-fluid" style="border-radius:0;">발주 전 미리보기</button>
                    </div>
                </div>
            </section>
            </form>

        </div>

    </div>

    <script>
        $(function () {
            const specialItems = ['관엽식물', '꽃바구니', '서양란', '동양란'];
            const defaultPreviewImage = "{{ asset('assets/img/noimg.jpg') }}";

            function parseNumber(value) {
                return parseInt(String(value).replace(/[^0-9]/g, ''), 10) || 0;
            }

            function formatNumber(value) {
                return Number(value || 0).toLocaleString('ko-KR');
            }

            function pad2(num) {
                return String(num).padStart(2, '0');
            }

            function formatDate(date) {
                const y = date.getFullYear();
                const m = pad2(date.getMonth() + 1);
                const d = pad2(date.getDate());
                return y + '-' + m + '-' + d;
            }

            function formatHourLabel(hour) {
                return pad2(hour) + '시';
            }

            function formatMinuteLabel(minute) {
                return pad2(minute) + '분';
            }

            function getPreviewAddress(address) {
                const parts = String(address).trim().split(/\s+/).filter(Boolean);
                return parts.length >= 2 ? parts.slice(0, 2).join(' ') : (parts[0] || '');
            }

            function setProductImagePreview(src) {
                $('#product_image_preview').attr('src', src);
            }

            function resetProductImagePreview() {
                setProductImagePreview(defaultPreviewImage);
            }

            function updatePrice2Preview() {
                const currentPrice2 = parseNumber($('#price2-input').val());

                $('#order_price2_preview').text(
                    currentPrice2 > 0 ? formatNumber(currentPrice2) + '원' : '미 입력 상태'
                );
            }

            function fillHourOptions() {
                const $hour = $('#delivery_hour');
                const currentValue = $hour.data('old') || $hour.val() || '';

                $hour.empty();
                $hour.append('<option value="">시간선택</option>');

                for (let i = 0; i <= 23; i++) {
                    $hour.append('<option value="' + pad2(i) + '">' + formatHourLabel(i) + '</option>');
                }

                if (currentValue !== '') {
                    $hour.val(String(currentValue).padStart(2, '0'));
                }
            }

            function fillMinuteOptions() {
                const $minute = $('#delivery_minute');
                const currentValue = $minute.data('old') || $minute.val() || '';

                $minute.empty();

                for (let i = 0; i <= 50; i += 10) {
                    $minute.append('<option value="' + pad2(i) + '">' + formatMinuteLabel(i) + '</option>');
                }

                if (currentValue !== '') {
                    $minute.val(String(currentValue).padStart(2, '0'));
                } else {
                    $minute.val('00');
                }
            }

            function roundMinutesTo10(date) {
                const d = new Date(date.getTime());
                let minutes = d.getMinutes();
                let rounded = Math.round(minutes / 10) * 10;

                if (rounded === 60) {
                    d.setHours(d.getHours() + 1);
                    rounded = 0;
                }

                d.setMinutes(rounded);
                d.setSeconds(0);
                d.setMilliseconds(0);

                return d;
            }

            function setDeliveryDateTime(dateObj) {
                $('#delivery_date').val(formatDate(dateObj)).trigger('change');
                $('#delivery_hour').val(pad2(dateObj.getHours())).trigger('change');
                $('#delivery_minute').val(pad2(dateObj.getMinutes())).trigger('change');
                updateDeliveryPreview();
            }

            function updateDeliveryPreview() {
                const date = ($('#delivery_date').val() || '').trim();
                const hour = ($('#delivery_hour').val() || '').trim();
                const minute = ($('#delivery_minute').val() || '').trim();
                const checkedType = $('input[name="delivery_time_type_ui"]:checked').val() || '';

                if (!date || hour === '' || minute === '') {
                    $('#order_delivery_datetime_preview').text('미 입력 상태');
                    return;
                }

                let text = date + ' ' + hour + ':' + minute;
                if (checkedType) {
                    text += ' ' + checkedType;
                }

                $('#order_delivery_datetime_preview').text(text);
            }

            function setDefaultToday() {
                const now = new Date();
                if (!$('#delivery_date').val()) {
                    $('#delivery_date').val(formatDate(now));
                }
            }

            // -----------------------------
            // 상품 선택
            // -----------------------------
            $(document).on('change', 'input[name="quick_item"]', function () {
                if ($(this).is(':checked')) {
                    $('input[name="quick_item"]').not(this).prop('checked', false);

                    const selectedValue = $(this).val();
                    const detailValue = specialItems.includes(selectedValue) ? '금액대맞게' : selectedValue;

                    $('#product_name_hidden').val(selectedValue);
                    $('#product_detail_input').val(detailValue).trigger('input').trigger('change');
                    $('#order_product_preview').text(selectedValue);
                } else {
                    $('#product_name_hidden').val('');
                    $('#product_detail_input').val('').trigger('input').trigger('change');
                    $('#order_product_preview').text('미 입력 상태');
                }
            });

            $(document).on('input', '#product_detail_input', function () {
                const value = ($(this).val() || '').trim();

                if (value !== '') {
                    $('#order_product_preview').text(value);
                } else {
                    const checkedItem = $('input[name="quick_item"]:checked').val() || '미 입력 상태';
                    $('#order_product_preview').text(checkedItem);
                }
            });

            // -----------------------------
            // 상품 이미지 미리보기
            // -----------------------------
            $(document).on('change', '#product_image_file', function () {
                const file = this.files && this.files[0] ? this.files[0] : null;

                if (!file) {
                    if (!$('#product_image_url').val().trim()) {
                        resetProductImagePreview();
                    }
                    return;
                }

                if (!file.type.match(/^image\//)) {
                    alert('이미지 파일만 업로드할 수 있습니다.');
                    $(this).val('');

                    if ($('#product_image_url').val().trim()) {
                        setProductImagePreview($('#product_image_url').val().trim());
                    } else {
                        resetProductImagePreview();
                    }
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    setProductImagePreview(e.target.result);
                };
                reader.readAsDataURL(file);
            });

            $(document).on('input change blur', '#product_image_url', function () {
                const url = ($(this).val() || '').trim();

                if (url !== '') {
                    setProductImagePreview(url);
                } else if ($('#product_image_file')[0].files && $('#product_image_file')[0].files[0]) {
                    const file = $('#product_image_file')[0].files[0];

                    if (file && file.type.match(/^image\//)) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            setProductImagePreview(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        resetProductImagePreview();
                    }
                } else {
                    resetProductImagePreview();
                }
            });

            $(document).on('error', '#product_image_preview', function () {
                resetProductImagePreview();
            });

            // -----------------------------
            // 금액 처리
            // -----------------------------
            $(document).on('click', 'input[name="price1"]', function () {
                const addAmount = parseNumber($(this).val());
                let currentPrice1 = parseNumber($('#price1-input').val());

                if (addAmount === 0) {
                    currentPrice1 = 0;
                } else {
                    currentPrice1 += addAmount;
                }

                $('#price1-input').val(formatNumber(currentPrice1));

                const currentPrice2 = Math.floor(currentPrice1 * 0.8);
                $('#price2-input').val(formatNumber(currentPrice2));
                updatePrice2Preview();

                const $this = $(this);
                setTimeout(function () {
                    $this.prop('checked', false);
                }, 80);
            });

            $(document).on('click', 'input[name="price2"]', function () {
                const addAmount = parseNumber($(this).val());
                let currentPrice2 = parseNumber($('#price2-input').val());

                if (addAmount === 0) {
                    currentPrice2 = 0;
                } else {
                    currentPrice2 += addAmount;
                }

                $('#price2-input').val(formatNumber(currentPrice2));
                updatePrice2Preview();

                const $this = $(this);
                setTimeout(function () {
                    $this.prop('checked', false);
                }, 80);
            });

            $(document).on('input', '#price1-input', function () {
                const currentPrice1 = parseNumber($(this).val());
                $(this).val(formatNumber(currentPrice1));

                const currentPrice2 = Math.floor(currentPrice1 * 0.8);
                $('#price2-input').val(formatNumber(currentPrice2));
                updatePrice2Preview();
            });

            $(document).on('input', '#price2-input', function () {
                const currentPrice2 = parseNumber($(this).val());
                $(this).val(formatNumber(currentPrice2));
                updatePrice2Preview();
            });

            // -----------------------------
            // 배송지 검색
            // -----------------------------
            $(document).on('click', '#btn_delivery_address_search', function () {
                new kakao.Postcode({
                    oncomplete: function(data) {
                        let addr = '';
                        let extraAddr = '';

                        if (data.userSelectedType === 'R') {
                            addr = data.roadAddress;
                        } else {
                            addr = data.jibunAddress;
                        }

                        if (data.userSelectedType === 'R') {
                            if (data.bname !== '' && /[동로가]$/.test(data.bname)) {
                                extraAddr += data.bname;
                            }

                            if (data.buildingName !== '' && data.apartment === 'Y') {
                                extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                            }

                            if (extraAddr !== '') {
                                extraAddr = ' (' + extraAddr + ')';
                            }
                        }

                        const fullAddress = (addr + extraAddr).trim();

                        $('#delivery_address_input').val(fullAddress + ' ').trigger('input').trigger('change').focus();
                        $('#order_delivery_address_preview').text(getPreviewAddress(fullAddress));
                    }
                }).open();
            });

            $(document).on('input change', '#delivery_address_input', function () {
                const value = ($(this).val() || '').trim();
                $('#order_delivery_address_preview').text(value !== '' ? getPreviewAddress(value) : '미 입력 상태');
            });

            // -----------------------------
            // 배송일시
            // -----------------------------
            fillHourOptions();
            fillMinuteOptions();
            setDefaultToday();

            $(document).on('change', '#delivery_now', function () {
                $('#is_urgent_hidden').val($(this).is(':checked') ? '1' : '0');

                if (!$(this).is(':checked')) {
                    return;
                }

                const now = new Date();
                const currentHour = now.getHours();
                let targetDate;

                if (currentHour < 9) {
                    alert('현재 시간에는 당일 12:00로 자동 설정됩니다.');
                    targetDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 12, 0, 0, 0);
                } else if (currentHour >= 18) {
                    alert('현재 시간에는 즉시배송이 불가능하여 다음날 12:00로 자동 설정됩니다.');
                    targetDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 12, 0, 0, 0);
                } else {
                    targetDate = new Date(now.getTime() + (3 * 60 * 60 * 1000));
                    targetDate = roundMinutesTo10(targetDate);
                }

                setDeliveryDateTime(targetDate);

                const $this = $(this);
                setTimeout(function () {
                    $this.prop('checked', false);
                    $('#is_urgent_hidden').val('0');
                }, 80);
            });

            $(document).on('change', 'input[name="delivery_time_type_ui"]', function () {
                if ($(this).is(':checked')) {
                    $('input[name="delivery_time_type_ui"]').not(this).prop('checked', false);
                    $('#delivery_time_type_hidden').val($(this).val());
                } else {
                    $('#delivery_time_type_hidden').val('');
                }

                updateDeliveryPreview();
            });

            $(document).on('change input', '#delivery_date, #delivery_hour, #delivery_minute', function () {
                updateDeliveryPreview();
            });

            if ($.fn.datepicker) {
                $('#delivery_date').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }

            // -----------------------------
            // 간편문구 / 받는분 / 요청사항
            // -----------------------------
            $(document).on('change', 'input[name="quick_ment"]', function () {
                if ($(this).is(':checked')) {
                    $('input[name="quick_ment"]').not(this).prop('checked', false);
                    $('#congratulatory_message_input').val($(this).val()).trigger('input').trigger('change');
                } else {
                    $('#congratulatory_message_input').val('').trigger('input').trigger('change');
                }
            });

            $(document).on('input change', '#receiver_name_input', function () {
                const value = ($(this).val() || '').trim();
                $('#order_receiver_preview').text(value !== '' ? value : '미 입력 상태');
            });

            $(document).on('change', '#request_photo', function () {
                if ($(this).is(':checked')) {
                    $('#customer_request_input')
                        .val('★현장사진 꼭 부탁드립니다')
                        .trigger('input')
                        .trigger('change');
                    $('#request_photo_hidden').val('1');
                } else {
                    $('#customer_request_input')
                        .val('')
                        .trigger('input')
                        .trigger('change');
                    $('#request_photo_hidden').val('0');
                }
            });

            // -----------------------------
            // submit 직전 hidden 값 최종 동기화
            // -----------------------------
            $('#bonbu-balju-form').on('submit', function () {
                $('#product_name_hidden').val($('input[name="quick_item"]:checked').val() || '');
                $('#original_amount_hidden').val(parseNumber($('#price1-input').val()));
                $('#order_amount_hidden').val(parseNumber($('#price2-input').val()));
                $('#delivery_time_type_hidden').val($('input[name="delivery_time_type_ui"]:checked').val() || '');
                $('#is_urgent_hidden').val($('#delivery_now').is(':checked') ? '1' : '0');
                $('#request_photo_hidden').val($('#request_photo').is(':checked') ? '1' : '0');
            });

            // -----------------------------
            // old() 값 복원
            // -----------------------------
            const oldProduct = $('#product_name_hidden').val();
            if (oldProduct) {
                $('input[name="quick_item"][value="' + oldProduct + '"]').prop('checked', true).trigger('change');
            }

            const oldMent = @json(old('quick_ment'));
            if (oldMent) {
                $('input[name="quick_ment"][value="' + oldMent + '"]').prop('checked', true);
            }

            const oldDeliveryType = $('#delivery_time_type_hidden').val();
            if (oldDeliveryType) {
                $('input[name="delivery_time_type_ui"][value="' + oldDeliveryType + '"]').prop('checked', true);
            }

            const oldHour = @json(old('delivery_hour'));
            if (oldHour !== null && oldHour !== '') {
                $('#delivery_hour').val(String(oldHour).padStart(2, '0'));
            }

            const oldMinute = @json(old('delivery_minute'));
            if (oldMinute !== null && oldMinute !== '') {
                $('#delivery_minute').val(String(oldMinute).padStart(2, '0'));
            }

            if ($('#request_photo_hidden').val() === '1') {
                $('#request_photo').prop('checked', true);
            }

            if (@json(old('is_urgent', 0)) == 1) {
                $('#delivery_now').prop('checked', true);
                $('#is_urgent_hidden').val('1');
            }

            if ($('#product_image_url').val().trim()) {
                setProductImagePreview($('#product_image_url').val().trim());
            }

            $('#original_amount_hidden').val(parseNumber($('#price1-input').val()));
            $('#order_amount_hidden').val(parseNumber($('#price2-input').val()));
            updatePrice2Preview();
            updateDeliveryPreview();
        });
    </script>
@endsection


