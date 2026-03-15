@extends('layouts.admin')

@section('content')

    <div id="content__body">

        <div id="bonbu-balju">

            <section>
                <h2 class="tt">✔️ 실시간발주<small>회원 간 거래를 지정하여 발주할 수 있습니다.</small></h2>
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
                            <p class="color-primary">
                                @forelse ($generalNotices as $notice)
                                    {!! nl2br(e($notice->content ?: $notice->title)) !!}@if (!$loop->last)<br>@endif
                                @empty
                                    등록된 본부공지사항이 없습니다.
                                @endforelse
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="color-orange">특별</span>공지사항</th>
                        <td>
                            <p class="color-orange">
                                @forelse ($specialNotices as $notice)
                                    {!! nl2br(e($notice->content ?: $notice->title)) !!}@if (!$loop->last)<br>@endif
                                @empty
                                    등록된 특별공지사항이 없습니다.
                                @endforelse
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <form id="admin-real-time-form" method="POST" action="{{ route('admin.real-time.store') }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" id="selected_product_name" name="product_name" value="{{ old('product_name') }}">
                <input type="hidden" id="orderer_is_hq" name="orderer_is_hq" value="{{ old('orderer_is_hq', '0') }}">
                <input type="hidden" id="receiver_is_hq" name="receiver_is_hq" value="{{ old('receiver_is_hq', '0') }}">
                <input type="hidden" id="delivery_time_type" name="delivery_time_type" value="{{ old('delivery_time_type', '도착') }}">
                <input type="hidden" id="is_urgent" name="is_urgent" value="{{ old('is_urgent', '0') }}">

                <section class="mt30">
                    <table class="table-data collapse">
                        <colgroup>
                            <col style="width:140px">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>발주화원<em>*</em></th>
                            <td>
                                <div class="input-group-balju">
                                    <input type="text" id="orderer_shop_name" name="orderer_shop_name" value="{{ old('orderer_shop_name') }}" placeholder="" readonly>
                                    <input type="hidden" id="orderer_shop_id" name="orderer_shop_id" value="{{ old('orderer_shop_id') }}">
                                    <span>
                                        <button type="button" class="btn btn-baljusa btn-member-popup" data-target="orderer">발주사 선택</button>
                                        <button type="button" class="btn btn-bonbu btn-bonbu-select" data-target="orderer">본부 선택</button>
                                    </span>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>수주화원<em>*</em></th>
                            <td>
                                <div class="input-group-balju">
                                    <input type="text" id="receiver_shop_name" name="receiver_shop_name" value="{{ old('receiver_shop_name') }}" placeholder="" readonly>
                                    <input type="hidden" id="receiver_shop_id" name="receiver_shop_id" value="{{ old('receiver_shop_id') }}">
                                    <span>
                                        <button type="button" class="btn btn-baljusa btn-member-popup" data-target="receiver">수주사 선택</button>
                                        <button type="button" class="btn btn-bonbu btn-bonbu-select" data-target="receiver">본부 선택</button>
                                    </span>
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
                            <th>상품간편선택 <em>*</em></th>
                            <td>
                                <div class="checkbox">
                                    <ul>
                                        <li style="width:220.33px;"><input type="checkbox" name="item" value="근조3단(기본)" id="item1" {{ old('product_name') === '근조3단(기본)' ? 'checked' : '' }}><label for="item1">근조3단(기본)</label></li>
                                        <li style="width:220.33px;"><input type="checkbox" name="item" value="근조3단(고급)" id="item2" {{ old('product_name') === '근조3단(고급)' ? 'checked' : '' }}><label for="item2">근조3단(고급)</label></li>
                                        <li style="width:220.33px;"><input type="checkbox" name="item" value="근조3단(특대)" id="item3" {{ old('product_name') === '근조3단(특대)' ? 'checked' : '' }}><label for="item3">근조3단(특대)</label></li>
                                        <li style="width:100px;"><input type="checkbox" name="item" value="관엽식물" id="item4" {{ old('product_name') === '관엽식물' ? 'checked' : '' }}><label for="item4">관엽식물</label></li>
                                        <li style="width:100px;"><input type="checkbox" name="item" value="꽃바구니" id="item5" {{ old('product_name') === '꽃바구니' ? 'checked' : '' }}><label for="item5">꽃바구니</label></li>
                                        <li style="width:100px;"><input type="checkbox" name="item" value="서양란" id="item6" {{ old('product_name') === '서양란' ? 'checked' : '' }}><label for="item6">서양란</label></li>
                                        <li style="width:100px;"><input type="checkbox" name="item" value="동양란" id="item7" {{ old('product_name') === '동양란' ? 'checked' : '' }}><label for="item7">동양란</label></li>

                                        <li style="width:220.33px;"><input type="checkbox" name="item" value="축하3단(기본)" id="item8" {{ old('product_name') === '축하3단(기본)' ? 'checked' : '' }}><label for="item8">축하3단(기본)</label></li>
                                        <li style="width:220.33px;"><input type="checkbox" name="item" value="축하3단(고급)" id="item9" {{ old('product_name') === '축하3단(고급)' ? 'checked' : '' }}><label for="item9">축하3단(고급)</label></li>
                                        <li style="width:220.33px;"><input type="checkbox" name="item" value="축하3단(특대)" id="item10" {{ old('product_name') === '축하3단(특대)' ? 'checked' : '' }}><label for="item10">축하3단(특대)</label></li>
                                        <li style="width:100px;"><input type="checkbox" name="item" value="근조바구니" id="item11" {{ old('product_name') === '근조바구니' ? 'checked' : '' }}><label for="item11">근조바구니</label></li>
                                        <li style="width:100px;"><input type="checkbox" name="item" value="근조쌀화환" id="item12" {{ old('product_name') === '근조쌀화환' ? 'checked' : '' }}><label for="item12">근조쌀화환</label></li>
                                        <li style="width:100px;"><input type="checkbox" name="item" value="축하쌀화환" id="item13" {{ old('product_name') === '축하쌀화환' ? 'checked' : '' }}><label for="item13">축하쌀화환</label></li>
                                        <li style="width:100px;"><input type="checkbox" name="item" value="근조오브제" id="item14" {{ old('product_name') === '근조오브제' ? 'checked' : '' }}><label for="item14">근조오브제</label></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>상품상세입력<em>*</em></th>
                            <td>
                                <input type="text" id="product_detail" name="product_detail" placeholder="상품의 상세정보를 입력해 주세요" value="{{ old('product_detail') }}">
                            </td>
                        </tr>
                        <tr>
                            <th>상품이미지</th>
                            <td>
                                <div class="input-group-file2">
                                    <input type="file" name="product_image_file" id="file"><label for="file">이미지 첨부</label>
                                    <input type="text" name="product_image_url" placeholder="상품이미지 주소가 잇다면 붙여 넣어 주세요" value="{{ old('product_image_url') }}">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>원청금액<em>*</em></th>
                            <td>
                                <div class="moneyBox">
                                    <input type="text" value="{{ old('original_amount', '50,000') }}" id="price1-input" name="original_amount">
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
                                    <input type="text" value="{{ old('order_amount', '35,000') }}" id="price2-input" name="order_amount">
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
                                    <button type="button" id="btn-search-address">주소검색</button>
                                    <input type="text" id="delivery_addr1" name="delivery_addr1" placeholder="정확한 배송지 주소를 입력해 주세요" value="{{ old('delivery_addr1') }}">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>배달요청일시<em>*</em></th>
                            <td>
                                <div class="input-group-column">
                                    <div class="col">
                                        <div class="input-group-checkbox">
                                            <input type="checkbox" id="delivery_now" {{ old('is_urgent') == '1' ? 'checked' : '' }}><label for="delivery_now">지금즉시</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <input type="text" id="delivery_date" name="delivery_date" class="datepicker" value="{{ old('delivery_date', now()->format('Y-m-d')) }}" style="width:120px;">
                                    </div>
                                    <div class="col">
                                        <select id="delivery_hour" name="delivery_hour" style="width:110px;">
                                            <option value="">시간선택</option>
                                            @for ($h = 0; $h <= 23; $h++)
                                                <option value="{{ $h }}" {{ (string) old('delivery_hour') === (string) $h ? 'selected' : '' }}>{{ $h }}시</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select id="delivery_minute" name="delivery_minute" style="width:110px;">
                                            @foreach ([0,10,20,30,40,50] as $m)
                                                <option value="{{ $m }}" {{ (string) old('delivery_minute', 0) === (string) $m ? 'selected' : '' }}>{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}분</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <div class="input-group-checkbox">
                                            <input type="checkbox" name="delivery_time_type_ui" value="도착" id="time2" {{ old('delivery_time_type', '도착') === '도착' ? 'checked' : '' }}><label for="time2">도착</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group-checkbox">
                                            <input type="checkbox" name="delivery_time_type_ui" value="예식" id="time3" {{ old('delivery_time_type') === '예식' ? 'checked' : '' }}><label for="time3">예식</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group-checkbox">
                                            <input type="checkbox" name="delivery_time_type_ui" value="행사" id="time4" {{ old('delivery_time_type') === '행사' ? 'checked' : '' }}><label for="time4">행사</label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>받는고객정보<em>*</em></th>
                            <td>
                                <div class="input-group-two">
                                    <input type="text" name="recipient_name" style="width:220px" placeholder="받는 분 성함을 입력해 주세요" value="{{ old('recipient_name') }}">
                                    <input type="text" name="recipient_phone" style="width:220px" placeholder="받는 분 연락처를 입력해 주세요" value="{{ old('recipient_phone') }}">
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
                                        <li><input type="radio" name="ment" value="祝結婚" id="ment1" {{ old('ribbon_phrase') === '祝結婚' ? 'checked' : '' }}><label for="ment1">祝結婚</label></li>
                                        <li><input type="radio" name="ment" value="祝華婚" id="ment2" {{ old('ribbon_phrase') === '祝華婚' ? 'checked' : '' }}><label for="ment2">祝華婚</label></li>
                                        <li><input type="radio" name="ment" value="祝發展" id="ment3" {{ old('ribbon_phrase') === '祝發展' ? 'checked' : '' }}><label for="ment3">祝發展</label></li>
                                        <li><input type="radio" name="ment" value="祝開業" id="ment4" {{ old('ribbon_phrase') === '祝開業' ? 'checked' : '' }}><label for="ment4">祝開業</label></li>
                                        <li><input type="radio" name="ment" value="祝榮轉" id="ment5" {{ old('ribbon_phrase') === '祝榮轉' ? 'checked' : '' }}><label for="ment5">祝榮轉</label></li>
                                        <li><input type="radio" name="ment" value="祝昇進" id="ment6" {{ old('ribbon_phrase') === '祝昇進' ? 'checked' : '' }}><label for="ment6">祝昇進</label></li>
                                        <li><input type="radio" name="ment" value="謹弔" id="ment7" {{ old('ribbon_phrase') === '謹弔' ? 'checked' : '' }}><label for="ment7">謹弔</label></li>
                                        <li><input type="radio" name="ment" value="삼가 故人의 冥福을 빕니다" id="ment8" {{ old('ribbon_phrase') === '삼가 故人의 冥福을 빕니다' ? 'checked' : '' }}><label for="ment8">삼가 故人의 冥福을 빕니다</label></li>

                                        <li><input type="radio" name="ment" value="축결혼" id="ment9" {{ old('ribbon_phrase') === '축결혼' ? 'checked' : '' }}><label for="ment9">축결혼</label></li>
                                        <li><input type="radio" name="ment" value="축화혼" id="ment10" {{ old('ribbon_phrase') === '축화혼' ? 'checked' : '' }}><label for="ment10">축화혼</label></li>
                                        <li><input type="radio" name="ment" value="축발전" id="ment11" {{ old('ribbon_phrase') === '축발전' ? 'checked' : '' }}><label for="ment11">축발전</label></li>
                                        <li><input type="radio" name="ment" value="축개업" id="ment12" {{ old('ribbon_phrase') === '축개업' ? 'checked' : '' }}><label for="ment12">축개업</label></li>
                                        <li><input type="radio" name="ment" value="축영전" id="ment13" {{ old('ribbon_phrase') === '축영전' ? 'checked' : '' }}><label for="ment13">축영전</label></li>
                                        <li><input type="radio" name="ment" value="축승진" id="ment14" {{ old('ribbon_phrase') === '축승진' ? 'checked' : '' }}><label for="ment14">축승진</label></li>
                                        <li><input type="radio" name="ment" value="근조" id="ment15" {{ old('ribbon_phrase') === '근조' ? 'checked' : '' }}><label for="ment15">근조</label></li>
                                        <li><input type="radio" name="ment" value="삼가 고인의 명복을 빕니다" id="ment16" {{ old('ribbon_phrase') === '삼가 고인의 명복을 빕니다' ? 'checked' : '' }}><label for="ment16">삼가 고인의 명복을 빕니다</label></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>경조사어<em>*</em></th>
                            <td>
                                <input type="text" id="ribbon_phrase" name="ribbon_phrase" placeholder="경조사어를 입력해 주세요 (예: 삼가 고인의 명복을 빕니다)" value="{{ old('ribbon_phrase') }}">
                            </td>
                        </tr>
                        <tr>
                            <th>보내는분<em>*</em></th>
                            <td>
                                <input type="text" name="sender_name" placeholder="보내는분을 입력해 주세요 (예: 정직한플라워 임직원 일동)" value="{{ old('sender_name') }}">
                            </td>
                        </tr>
                        <tr>
                            <th>카드메세지</th>
                            <td>
                                <input type="text" name="card_message" placeholder="카드메세지가 있다면 작성해 주세요" value="{{ old('card_message') }}">
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
                                        <input type="checkbox" name="request_photo" id="request_photo" value="1" {{ old('request_photo') ? 'checked' : '' }}><label for="request_photo">현장사진요청</label>
                                    </div>
                                    <input type="text" id="request_note" name="request_note" placeholder="고객 및 발주사의 요청사항이 있다면 작성해주세요" value="{{ old('request_note') }}">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="mt30">
                        <button type="submit" class="btn btn-primary btn-fluid" style="margin-right:20px;border-radius:0">발주완료</button>
                    </div>
                </section>

            </form>

        </div>

    </div>

    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

    <script>
        $(function () {
            function parseNumber(value) {
                return parseInt(String(value).replace(/[^0-9]/g, ''), 10) || 0;
            }

            function formatNumber(value) {
                return parseNumber(value).toLocaleString('ko-KR');
            }

            function setMoneyInput($input, value) {
                $input.val(formatNumber(value));
            }

            function updateOrderAmountByOriginal() {
                const originalAmount = parseNumber($('#price1-input').val());
                const orderAmount = Math.floor(originalAmount * 0.8);
                setMoneyInput($('#price2-input'), orderAmount);
            }

            function getAutoRibbonPhraseByProduct(productName) {
                if (productName.includes('근조')) {
                    return '삼가 고인의 명복을 빕니다';
                }
                if (productName.includes('축하')) {
                    return '祝結婚';
                }
                return '';
            }

            function pad2(value) {
                return String(value).padStart(2, '0');
            }

            function syncMentRadioByRibbonPhrase(ribbonPhrase) {
                if (!ribbonPhrase) return;

                const $targetRadio = $('input[name="ment"]').filter(function () {
                    return $(this).val() === ribbonPhrase;
                }).first();

                if ($targetRadio.length) {
                    $('input[name="ment"]').prop('checked', false);
                    $targetRadio.prop('checked', true);
                }
            }

            function applyImmediateDeliveryTime(showAlert = true) {
                const now = new Date();
                const currentMinutes = now.getHours() * 60 + now.getMinutes();

                let target = new Date(now);
                let alertMessage = '';

                if (currentMinutes < 9 * 60) {
                    target.setHours(12, 0, 0, 0);
                    alertMessage = '즉시배송은 오전 09:00부터 오후 06:30까지만 가능합니다.\n당일 오후 12:00로 자동 설정됩니다.';
                } else if (currentMinutes < (18 * 60 + 30)) {
                    target = new Date(now.getTime() + 3 * 60 * 60 * 1000);

                    const minute = target.getMinutes();
                    const minuteOptions = [0, 10, 20, 30, 40, 50];
                    let roundedMinute = minuteOptions.find(m => minute <= m);

                    if (roundedMinute === undefined) {
                        target.setHours(target.getHours() + 1);
                        roundedMinute = 0;
                    }

                    target.setMinutes(roundedMinute, 0, 0);
                    alertMessage = '즉시배송 시간으로 자동 설정되었습니다.';
                } else {
                    target.setDate(target.getDate() + 1);
                    target.setHours(12, 0, 0, 0);
                    alertMessage = '즉시배송은 오전 09:00부터 오후 06:30까지만 가능합니다.\n다음날 오후 12:00로 자동 설정됩니다.';
                }

                const year = target.getFullYear();
                const month = pad2(target.getMonth() + 1);
                const day = pad2(target.getDate());

                $('#delivery_date').val(`${year}-${month}-${day}`);
                $('#delivery_hour').val(target.getHours());
                $('#delivery_minute').val(target.getMinutes());

                $('#is_urgent').val($('#delivery_now').is(':checked') ? '1' : '0');

                if (showAlert && alertMessage) {
                    alert(alertMessage);
                }
            }

            function applyProductAutoFill(productName) {
                if (!productName) return;

                $('#selected_product_name').val(productName);
                $('#product_detail').val(productName);

                setMoneyInput($('#price1-input'), 50000);
                setMoneyInput($('#price2-input'), 35000);

                $('#delivery_now').prop('checked', true);
                applyImmediateDeliveryTime(false);

                $('#request_photo').prop('checked', true);
                $('#request_note').val('★현장사진 꼭 부탁드립니다');

                const autoRibbon = getAutoRibbonPhraseByProduct(productName);
                if (autoRibbon !== '') {
                    $('#ribbon_phrase').val(autoRibbon);
                    syncMentRadioByRibbonPhrase(autoRibbon);
                }
            }

            $(document).on('click', '.btn-member-popup', function (e) {
                e.preventDefault();

                const target = $(this).data('target');
                const popupUrl = '{{ route('admin.member-list-popup') }}' + '?target=' + encodeURIComponent(target);

                window.open(
                    popupUrl,
                    'memberListPopup',
                    'width=1100,height=800,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
                );
            });

            $(document).on('click', '.btn-bonbu-select', function (e) {
                e.preventDefault();

                const target = $(this).data('target');
                const displayName = '본부 수발주사업부';

                if (target === 'orderer') {
                    $('#orderer_shop_id').val('1');
                    $('#orderer_shop_name').val(displayName);
                    $('#orderer_is_hq').val('1');
                } else {
                    $('#receiver_shop_id').val('1');
                    $('#receiver_shop_name').val(displayName);
                    $('#receiver_is_hq').val('1');
                }
            });

            $(document).on('change', 'input[name="item"]', function () {
                if ($(this).is(':checked')) {
                    $('input[name="item"]').not(this).prop('checked', false);
                    applyProductAutoFill($(this).val());
                } else {
                    $('#selected_product_name').val('');
                }
            });

            $(document).on('change', 'input[name="price1"]', function () {
                const addValue = parseNumber($(this).val());

                if (addValue === 0) {
                    setMoneyInput($('#price1-input'), 0);
                } else {
                    setMoneyInput($('#price1-input'), parseNumber($('#price1-input').val()) + addValue);
                }

                $(this).prop('checked', false);
                updateOrderAmountByOriginal();
            });

            $(document).on('input', '#price1-input', function () {
                const value = parseNumber($(this).val());
                $(this).val(formatNumber(value));
                updateOrderAmountByOriginal();
            });

            $(document).on('input', '#price2-input', function () {
                const value = parseNumber($(this).val());
                $(this).val(formatNumber(value));
            });

            $(document).on('change', 'input[name="price2"]', function () {
                const addValue = parseNumber($(this).val());

                if (addValue === 0) {
                    setMoneyInput($('#price2-input'), 0);
                } else {
                    setMoneyInput($('#price2-input'), parseNumber($('#price2-input').val()) + addValue);
                }

                $(this).prop('checked', false);
            });

            $(document).on('click', '#btn-search-address', function (e) {
                e.preventDefault();

                new daum.Postcode({
                    oncomplete: function (data) {
                        const addr = data.roadAddress || data.jibunAddress || '';
                        $('#delivery_addr1').val(addr + ' ');
                        $('#delivery_addr1').focus();
                    }
                }).open();
            });

            $(document).on('change', '#delivery_now', function () {
                $('#is_urgent').val($(this).is(':checked') ? '1' : '0');

                if ($(this).is(':checked')) {
                    applyImmediateDeliveryTime(true);
                }
            });

            $(document).on('change', 'input[name="ment"]', function () {
                $('#ribbon_phrase').val($(this).val());
            });

            $(document).on('change', '#request_photo', function () {
                const text = '★현장사진 꼭 부탁드립니다';

                if ($(this).is(':checked')) {
                    if ($('#request_note').val().trim() === '') {
                        $('#request_note').val(text);
                    } else if (!$('#request_note').val().includes(text)) {
                        $('#request_note').val(text + '\n' + $('#request_note').val());
                    }
                } else {
                    $('#request_note').val(
                        $('#request_note').val().replace(text, '').replace(/^\n+|\n+$/g, '')
                    );
                }
            });

            $(document).on('change', 'input[name="delivery_time_type_ui"]', function () {
                if ($(this).is(':checked')) {
                    $('input[name="delivery_time_type_ui"]').not(this).prop('checked', false);
                    $('#delivery_time_type').val($(this).val());
                }
            });

            if (!$('input[name="delivery_time_type_ui"]:checked').length) {
                $('#time2').prop('checked', true);
                $('#delivery_time_type').val('도착');
            }
        });
    </script>
    @if (session('success'))
        <script>
            alert(@json(session('success')));
        </script>
    @endif

    @if ($errors->any())
        @php
            $firstErrorField = array_key_first($errors->toArray());
        @endphp

        <script>
            alert(@json($errors->first()));

            window.addEventListener('load', function () {
                const fieldMap = {
                    orderer_shop_id: 'orderer_shop_name',
                    receiver_shop_id: 'receiver_shop_name',
                    product_name: 'selected_product_name',
                    product_detail: 'product_detail',
                    product_image_url: 'product_image_url',
                    original_amount: 'price1-input',
                    order_amount: 'price2-input',
                    delivery_addr1: 'delivery_addr1',
                    delivery_date: 'delivery_date',
                    delivery_hour: 'delivery_hour',
                    delivery_minute: 'delivery_minute',
                    delivery_time_type: 'time2',
                    recipient_name: 'recipient_name',
                    recipient_phone: 'recipient_phone',
                    ribbon_phrase: 'ribbon_phrase',
                    sender_name: 'sender_name',
                    card_message: 'card_message',
                    request_note: 'request_note',
                    request_photo: 'request_photo',
                };

                const errorField = @json($firstErrorField);
                const targetId = fieldMap[errorField] || errorField;
                const el = document.getElementById(targetId) || document.querySelector(`[name="${targetId}"]`);

                if (el) {
                    el.focus();

                    if (typeof el.select === 'function' && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA')) {
                        el.select();
                    }
                }
            });
        </script>
    @endif
@endsection
