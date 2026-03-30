@extends('layouts.app')

@section('content')
    <div id="content__body" style="width:900px;position:relative">
        <div id="bonbu-balju" class="{{ $errors->any() ? 'has-alert-error' : '' }}">
            <aside id="sidePanel" class="new-design">
                <section class="panel active" data-panel="ops">
                    <div class="panel__head">
                        <h2 class="panel-title">본부 운영정보</h2>
                        <button type="button" class="panel-toggle">
                            <img src="{{ asset('assets/img/arrow_down_new.png') }}">
                        </button>
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
                                <div class="a">09:00~19:00</div>
                            </li>
                        </ul>
                    </div>
                </section>

                <section class="panel active" data-panel="summary">
                    <div class="panel__head">
                        <h2 class="panel-title">주문정보 확인</h2>
                        <button type="button" class="panel-toggle">
                            <img src="{{ asset('assets/img/arrow_down_new.png') }}">
                        </button>
                    </div>
                    <div class="panel__body">
                        <ul class="list-qa">
                            <li>
                                <div class="q">상품상세</div>
                                <div class="a" id="summary-product-detail">미 입력 상태</div>
                            </li>
                            <li>
                                <div class="q">발주금액</div>
                                <div class="a color-gray400 fw400" id="summary-order-amount">미 입력 상태</div>
                            </li>
                            <li>
                                <div class="q">배달장소</div>
                                <div class="a" id="summary-delivery-place">미 입력 상태</div>
                            </li>
                            <li>
                                <div class="q">배송일시</div>
                                <div class="a" id="summary-delivery-datetime">미 입력 상태</div>
                            </li>
                            <li>
                                <div class="q">받는사람</div>
                                <div class="a" id="summary-recipient-name">미 입력 상태</div>
                            </li>
                        </ul>
                    </div>
                </section>

                <section class="panel" data-panel="image">
                    <div class="panel__head">
                        <h2 class="panel-title">첨부이미지 조회</h2>
                        <button type="button" class="panel-toggle">
                            <img src="{{ asset('assets/img/arrow_down_new.png') }}">
                        </button>
                    </div>
                    <div class="panel__body" id="summary-image-panel-body" style="display:none;">
                        <div class="mt20" id="summary-image-preview-wrap">
                            <img id="summary-image-preview" src="" alt="첨부이미지" style="display:none;width:100%;">
                        </div>
                        <div class="mt20">
                            <button type="submit" form="bonbu-balju-form" class="btn btn-primary btn-fluid">상품 발주하기</button>
                        </div>
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
                            <p class="color-primary">
                                @forelse ($generalNotices as $notice)
                                    {{ $notice->title }}@if (!$loop->last)<br>@endif
                                @empty
                                    등록된 공지사항이 없습니다.
                                @endforelse
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="color-orange">특별</span>공지사항</th>
                        <td>
                            <p class="color-orange">
                                @forelse ($specialNotices as $notice)
                                    {{ $notice->title }}@if (!$loop->last)<br>@endif
                                @empty
                                    등록된 특별 공지사항이 없습니다.
                                @endforelse
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <form id="bonbu-balju-form" action="{{ route('bonbu-balju.order.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="delivery_now" id="delivery_now_hidden" value="{{ old('delivery_now') ? '1' : '0' }}">
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
                                        @php
                                            $productOptions = [
                                                '근조3단(기본)','근조3단(고급)','근조3단(특대)','관엽식물','꽃바구니','서양란','동양란',
                                                '축하3단(기본)','축하3단(고급)','축하3단(특대)','근조바구니','근조쌀화환','축하쌀화환','근조오브제'
                                            ];
                                            $selectedProduct = old('product_name');
                                        @endphp
                                        @foreach ($productOptions as $idx => $productOption)
                                            <li>
                                                <input type="checkbox"
                                                       name="product_name"
                                                       value="{{ $productOption }}"
                                                       id="item{{ $idx + 1 }}"
                                                    {{ $selectedProduct === $productOption ? 'checked' : '' }}>
                                                <label for="item{{ $idx + 1 }}">{{ $productOption }}</label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @error('product_name')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>상품상세입력<em>*</em></th>
                            <td>
                                <input type="text" name="product_detail" value="{{ old('product_detail') }}" placeholder="상품의 상세정보를 입력해 주세요">
                                @error('product_detail')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>상품이미지</th>
                            <td>
                                <div class="input-group-file2">
                                    <input type="file" name="product_image_file" id="file" accept="image/*">
                                    <label for="file">이미지 첨부</label>
                                    <input type="text" name="product_image_input_url" id="product_image_url" value="{{ old('product_image_input_url') }}" placeholder="상품이미지 주소가 잇다면 붙여 넣어 주세요">
                                </div>
                                @error('product_image_file')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                                @error('product_image_url')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>원청금액<em>*</em></th>
                            <td>
                                <div class="moneyBox">
                                    <input type="text" name="original_amount" value="{{ old('original_amount', '50000') }}" id="price1-input">
                                    <ul class="money-list" data-target="#price1-input">
                                        <li><input type="radio" name="price1_quick" value="10000" id="price1-1"><label for="price1-1">+ 1만원</label></li>
                                        <li><input type="radio" name="price1_quick" value="50000" id="price1-2"><label for="price1-2">+ 5만원</label></li>
                                        <li><input type="radio" name="price1_quick" value="60000" id="price1-3"><label for="price1-3">+ 6만원</label></li>
                                        <li><input type="radio" name="price1_quick" value="70000" id="price1-4"><label for="price1-4">+ 7만원</label></li>
                                        <li><input type="radio" name="price1_quick" value="80000" id="price1-5"><label for="price1-5">+ 8만원</label></li>
                                        <li><input type="radio" name="price1_quick" value="100000" id="price1-6"><label for="price1-6">+ 10만원</label></li>
                                        <li><input type="radio" name="price1_quick" value="150000" id="price1-7"><label for="price1-7">+ 15만원</label></li>
                                        <li><input type="radio" name="price1_quick" value="0" id="price1-8"><label for="price1-8">초기화</label></li>
                                    </ul>
                                </div>
                                @error('original_amount')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>발주금액<em>*</em></th>
                            <td>
                                <div class="moneyBox">
                                    <input type="text" name="order_amount" value="{{ old('order_amount', '40000') }}" id="price2-input">
                                    <ul class="money-list" data-target="#price2-input">
                                        <li><input type="radio" name="price2_quick" value="10000" id="price2-1"><label for="price2-1">+ 1만원</label></li>
                                        <li><input type="radio" name="price2_quick" value="50000" id="price2-2"><label for="price2-2">+ 5만원</label></li>
                                        <li><input type="radio" name="price2_quick" value="60000" id="price2-3"><label for="price2-3">+ 6만원</label></li>
                                        <li><input type="radio" name="price2_quick" value="70000" id="price2-4"><label for="price2-4">+ 7만원</label></li>
                                        <li><input type="radio" name="price2_quick" value="80000" id="price2-5"><label for="price2-5">+ 8만원</label></li>
                                        <li><input type="radio" name="price2_quick" value="100000" id="price2-6"><label for="price2-6">+ 10만원</label></li>
                                        <li><input type="radio" name="price2_quick" value="150000" id="price2-7"><label for="price2-7">+ 15만원</label></li>
                                        <li><input type="radio" name="price2_quick" value="0" id="price2-8"><label for="price2-8">초기화</label></li>
                                    </ul>
                                </div>
                                @error('order_amount')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
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
                                    <button type="button" id="btn-address-search">주소검색</button>
                                    <input type="text" name="delivery_addr1" id="delivery_addr1" value="{{ old('delivery_addr1') }}" placeholder="정확한 배송지 주소를 입력해 주세요">
                                </div>
                                @error('delivery_addr1')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>배달요청일시<em>*</em></th>
                            <td>
                                <div class="input-group-column">
                                    <div class="col">
                                        <div class="input-group-checkbox">
                                            <input type="checkbox" id="delivery_now" {{ old('delivery_now') ? 'checked' : '' }}>
                                            <label for="delivery_now">지금즉시</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <input type="text" name="delivery_date" id="delivery_date" class="datepicker" value="{{ old('delivery_date', now()->format('Y-m-d')) }}" style="width:120px;">
                                    </div>
                                    <div class="col">
                                        <select name="delivery_hour" id="delivery_hour" style="width:110px;">
                                            <option value="">시간선택</option>
                                            @for ($i = 0; $i <= 23; $i++)
                                                <option value="{{ $i }}" {{ (string) old('delivery_hour') == (string) $i ? 'selected' : '' }}>{{ $i }}시</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select name="delivery_minute" id="delivery_minute" style="width:110px;">
                                            @foreach (['0','10','20','30','40','50'] as $minute)
                                                <option value="{{ $minute }}" {{ (string) old('delivery_minute', '0') == (string) $minute ? 'selected' : '' }}>{{ str_pad($minute, 2, '0', STR_PAD_LEFT) }}분</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <div class="input-group-checkbox">
                                            <input type="radio" name="delivery_time_type" value="도착" id="time2" {{ old('delivery_time_type', '도착') === '도착' ? 'checked' : '' }}>
                                            <label for="time2">도착</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group-checkbox">
                                            <input type="radio" name="delivery_time_type" value="예식" id="time3" {{ old('delivery_time_type') === '예식' ? 'checked' : '' }}>
                                            <label for="time3">예식</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group-checkbox">
                                            <input type="radio" name="delivery_time_type" value="행사" id="time4" {{ old('delivery_time_type') === '행사' ? 'checked' : '' }}>
                                            <label for="time4">행사</label>
                                        </div>
                                    </div>
                                </div>
                                @error('delivery_date')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                                @error('delivery_hour')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                                @error('delivery_minute')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                                @error('delivery_time_type')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>받는고객정보<em>*</em></th>
                            <td>
                                <div class="input-group-two">
                                    <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name') }}" style="width:220px" placeholder="받는 분 성함을 입력해 주세요">
                                    <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" style="width:220px" placeholder="받는 분 연락처를 입력해 주세요">
                                </div>
                                @error('recipient_name')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                                @error('recipient_phone')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
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
                                        @php
                                            $ribbonOptions = [
                                                '祝結婚','祝華婚','祝發展','祝開業','祝榮轉','祝昇進','謹弔','삼가 故人의 冥福을 빕니다',
                                                '축결혼','축화혼','축발전','축개업','축영전','축승진','근조','삼가 고인의 명복을 빕니다',
                                            ];
                                        @endphp
                                        @foreach ($ribbonOptions as $idx => $ribbonOption)
                                            <li>
                                                <input type="radio"
                                                       name="ribbon_quick"
                                                       value="{{ $ribbonOption }}"
                                                       id="ment{{ $idx + 1 }}"
                                                    {{ (string) old('ribbon_quick', old('ribbon_phrase')) == (string) $ribbonOption ? 'checked' : '' }}>
                                                <label for="ment{{ $idx + 1 }}">{{ $ribbonOption }}</label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>경조사어<em>*</em></th>
                            <td>
                                <input type="text" name="ribbon_phrase" id="ribbon_phrase" value="{{ old('ribbon_phrase') }}" placeholder="경조사어를 입력해 주세요 (예: 삼가 고인의 명복을 빕니다)">
                                @error('ribbon_phrase')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>보내는분<em>*</em></th>
                            <td>
                                <input type="text" name="sender_name" value="{{ old('sender_name') }}" placeholder="보내는분을 입력해 주세요 (예: 정직한플라워 임직원 일동)">
                                @error('sender_name')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>카드메세지</th>
                            <td>
                                <input type="text" name="card_message" value="{{ old('card_message') }}" placeholder="카드메세지가 있다면 작성해 주세요">
                                @error('card_message')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
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
                                        <input type="checkbox" name="request_photo" id="check1" value="1" {{ old('request_photo') ? 'checked' : '' }}>
                                        <label for="check1">현장사진요청</label>
                                    </div>
                                    <input type="text" name="request_note" id="request_note" value="{{ old('request_note') }}" placeholder="고객 및 발주사의 요청사항이 있다면 작성해주세요">
                                </div>
                                @error('request_note')
                                <p class="mt5 color-red">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="flex mt30">
                        <div class="flex__col">
                            <button type="submit" class="btn btn-primary btn-fluid" style="width:700px;margin-right:20px;border-radius:0">발주완료</button>
                        </div>
                        <div class="flex__col">
                            <button type="button" id="btn-preview-order" class="btn btn-gray btn-fluid" style="border-radius:0;">발주 전 미리보기</button>
                        </div>
                    </div>
                </section>
            </form>

        </div>

    </div>
    @include('partials.loading-modal')
@endsection
@push('styles')
    <style>
        #bonbu-balju.has-alert-error .mt5.color-red {
            display: none;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(function () {
            const PHOTO_REQUEST_TEXT = '★현장사진 꼭 부탁드립니다';
            const hasOldInput = @json(session()->hasOldInput());
            let isSubmitting = false;

            closeLoadingModal();

            @if ($errors->any())
            alert(@json($errors->all()[0]));
            @endif

            @if (session('success_redirect_order_list'))
            alert(@json(session('success_redirect_order_list')));
            @endif

            @if (session('error'))
            alert(@json(session('error')));
            @endif

            @if (session('success'))
            alert(@json(session('success')));
            @endif

            const productPresetMap = {
                '근조3단(기본)': {
                    product_detail: '근조3단(기본)',
                    original_amount: 50000,
                    immediate: true,
                    request_photo: true,
                    ribbon_phrase: '삼가 고인의 명복을 빕니다'
                },
                '근조3단(고급)': {
                    product_detail: '근조3단(고급)',
                    original_amount: 70000,
                    immediate: true,
                    request_photo: true,
                    ribbon_phrase: '삼가 고인의 명복을 빕니다'
                },
                '근조3단(특대)': {
                    product_detail: '근조3단(특대)',
                    original_amount: 100000,
                    immediate: true,
                    request_photo: true,
                    ribbon_phrase: '삼가 고인의 명복을 빕니다'
                },
                '관엽식물': {
                    product_detail: '금액에 맞게',
                    original_amount: 80000,
                    immediate: false,
                    request_photo: false,
                    ribbon_phrase: '축발전'
                },
                '꽃바구니': {
                    product_detail: '금액에 맞게',
                    original_amount: 70000,
                    immediate: false,
                    request_photo: false,
                    ribbon_phrase: '축발전'
                },
                '서양란': {
                    product_detail: '금액에 맞게',
                    original_amount: 100000,
                    immediate: false,
                    request_photo: false,
                    ribbon_phrase: '축발전'
                },
                '동양란': {
                    product_detail: '금액에 맞게',
                    original_amount: 80000,
                    immediate: false,
                    request_photo: false,
                    ribbon_phrase: '축발전'
                },
                '축하3단(기본)': {
                    product_detail: '축하3단(기본)',
                    original_amount: 50000,
                    immediate: false,
                    request_photo: false,
                    ribbon_phrase: '축발전'
                },
                '축하3단(고급)': {
                    product_detail: '축하3단(고급)',
                    original_amount: 70000,
                    immediate: false,
                    request_photo: false,
                    ribbon_phrase: '축발전'
                },
                '축하3단(특대)': {
                    product_detail: '축하3단(특대)',
                    original_amount: 100000,
                    immediate: false,
                    request_photo: false,
                    ribbon_phrase: '축발전'
                },
                '근조바구니': {
                    product_detail: '근조바구니',
                    original_amount: 70000,
                    immediate: true,
                    request_photo: true,
                    ribbon_phrase: '삼가 고인의 명복을 빕니다'
                },
                '근조쌀화환': {
                    product_detail: '근조쌀화환',
                    original_amount: 100000,
                    immediate: true,
                    request_photo: true,
                    ribbon_phrase: '삼가 고인의 명복을 빕니다'
                },
                '축하쌀화환': {
                    product_detail: '축하쌀화환',
                    original_amount: 100000,
                    immediate: false,
                    request_photo: false,
                    ribbon_phrase: '축발전'
                },
                '근조오브제': {
                    product_detail: '근조오브제',
                    original_amount: 120000,
                    immediate: true,
                    request_photo: true,
                    ribbon_phrase: '삼가 고인의 명복을 빕니다'
                }
            };

            function ensurePanelState() {
                $('.panel[data-panel="ops"]').addClass('active').find('.panel__body').show();
                $('.panel[data-panel="summary"]').addClass('active').find('.panel__body').show();
            }

            function togglePanel($targetPanel) {
                $targetPanel.toggleClass('active');
                $targetPanel.find('.panel__body').stop(true, true).slideToggle(200);
            }

            $('.panel').each(function () {
                const $panel = $(this);
                if ($panel.data('panel') === 'ops' || $panel.data('panel') === 'summary') {
                    $panel.addClass('active');
                    $panel.find('.panel__body').show();
                } else if (!$panel.hasClass('active')) {
                    $panel.find('.panel__body').hide();
                }
            });

            $(document).on('click', '.panel-toggle', function () {
                togglePanel($(this).closest('.panel'));
            });

            function parseMoney(value) {
                const onlyNum = String(value || '').replace(/[^0-9]/g, '');
                return onlyNum === '' ? 0 : parseInt(onlyNum, 10);
            }

            function formatMoney(value) {
                const amount = parseMoney(value);
                return amount > 0 ? amount.toLocaleString('ko-KR') : '';
            }

            function setMoneyValue(selector, amount) {
                $(selector).val(amount > 0 ? Number(amount).toLocaleString('ko-KR') : '');
            }

            function normalizeRequestNote() {
                let note = $('#request_note').val() || '';
                note = note.replace(/\s*★현장사진 꼭 부탁드립니다\s*/g, ' ').replace(/\s{2,}/g, ' ').trim();
                return note;
            }

            function syncRequestPhotoNote() {
                let note = normalizeRequestNote();

                if ($('#check1').is(':checked')) {
                    note = note !== '' ? (note + ' ' + PHOTO_REQUEST_TEXT) : PHOTO_REQUEST_TEXT;
                }

                $('#request_note').val(note.trim());
            }

            function roundUpImmediateMinute(date) {
                const minuteOptions = [0, 10, 20, 30, 40, 50];
                let hour = date.getHours();
                let minute = null;

                for (let i = 0; i < minuteOptions.length; i++) {
                    if (minuteOptions[i] >= date.getMinutes()) {
                        minute = minuteOptions[i];
                        break;
                    }
                }

                if (minute === null) {
                    minute = 0;
                    hour += 1;
                }

                return { hour, minute };
            }

            function setImmediateDeliveryTime(showAlert = true) {
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();

                let targetDate;

                if (currentHour >= 0 && currentHour < 9) {
                    if (showAlert) {
                        alert('지금즉시는 오전 시간대 요청으로 당일 12:00로 자동 설정됩니다.');
                    }
                    targetDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 12, 0, 0);
                } else if (currentHour > 18 || (currentHour === 18 && currentMinute >= 30)) {
                    if (showAlert) {
                        alert('지금즉시는 본부 운영시간 외 요청입니다. 다음날 12:00로 자동 설정됩니다.');
                    }
                    targetDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 12, 0, 0);
                } else {
                    targetDate = new Date(now.getTime());
                    targetDate.setHours(targetDate.getHours() + 3);

                    const rounded = roundUpImmediateMinute(targetDate);
                    targetDate = new Date(
                        targetDate.getFullYear(),
                        targetDate.getMonth(),
                        targetDate.getDate(),
                        rounded.hour,
                        rounded.minute,
                        0
                    );
                }

                const yyyy = targetDate.getFullYear();
                const mm = String(targetDate.getMonth() + 1).padStart(2, '0');
                const dd = String(targetDate.getDate()).padStart(2, '0');

                $('#delivery_date').val(`${yyyy}-${mm}-${dd}`);
                $('#delivery_hour').val(String(targetDate.getHours()));
                $('#delivery_minute').val(String(targetDate.getMinutes()));
            }

            function setRibbonQuickChecked(ribbonPhrase) {
                $('input[name="ribbon_quick"]').prop('checked', false);

                if (!ribbonPhrase) {
                    return;
                }

                $('input[name="ribbon_quick"]').each(function () {
                    const quickValue = $.trim($(this).val());
                    const targetValue = $.trim(ribbonPhrase);

                    if (quickValue === targetValue) {
                        $(this).prop('checked', true);
                        return false;
                    }
                });
            }

            function syncOrderAmountFromOriginal() {
                const originalAmount = parseMoney($('#price1-input').val());
                const orderAmount = Math.floor(originalAmount * 0.8);
                setMoneyValue('#price2-input', orderAmount);
            }

            function applyProductPreset(productName) {
                const preset = productPresetMap[productName];
                if (!preset) {
                    updateSummary();
                    return;
                }

                $('input[name="product_detail"]').val(preset.product_detail || productName);

                if (preset.original_amount) {
                    setMoneyValue('#price1-input', preset.original_amount);
                    syncOrderAmountFromOriginal();
                }

                setRibbonQuickChecked(preset.ribbon_phrase || '');
                $('#ribbon_phrase').val(preset.ribbon_phrase || '');

                $('#check1').prop('checked', !!preset.request_photo);
                syncRequestPhotoNote();

                if (preset.immediate) {
                    $('#delivery_now').prop('checked', true);
                    $('#delivery_now_hidden').val('1');
                    setImmediateDeliveryTime(false);
                } else {
                    $('#delivery_now').prop('checked', false);
                    $('#delivery_now_hidden').val('0');
                }

                updateSummary();
            }

            function updateSummary() {
                const productName = $('input[name="product_name"]:checked').val() || '';
                const productDetail = $('input[name="product_detail"]').val() || '';
                const orderAmount = $('#price2-input').val() || '';
                const deliveryPlace = $('#delivery_addr1').val() || '';
                const recipientName = $('#recipient_name').val() || '';
                const deliveryDate = $('#delivery_date').val() || '';
                const deliveryHour = $('#delivery_hour').val() || '';
                const deliveryMinute = $('#delivery_minute').val() || '';
                const deliveryType = $('input[name="delivery_time_type"]:checked').val() || '';

                $('#summary-product-detail').text(
                    productDetail !== '' ? productDetail : (productName !== '' ? productName : '미 입력 상태')
                );

                const formattedOrderAmount = formatMoney(orderAmount);

                $('#summary-order-amount')
                    .text(formattedOrderAmount !== '' ? formattedOrderAmount + '원' : '미 입력 상태')
                    .toggleClass('color-gray400', formattedOrderAmount === '');

                $('#summary-delivery-place').text(deliveryPlace !== '' ? deliveryPlace : '미 입력 상태');
                $('#summary-recipient-name').text(recipientName !== '' ? recipientName : '미 입력 상태');

                if (deliveryDate !== '' && deliveryHour !== '' && deliveryMinute !== '') {
                    const hour = String(deliveryHour).padStart(2, '0');
                    const minute = String(deliveryMinute).padStart(2, '0');

                    let deliveryText = deliveryDate.replaceAll('-', '.') + ' ' + hour + ':' + minute;

                    if (deliveryType !== '') {
                        deliveryText += ' ' + deliveryType;
                    }

                    $('#summary-delivery-datetime').text(deliveryText);
                } else {
                    $('#summary-delivery-datetime').text('미 입력 상태');
                }
            }

            function openImagePanelIfNeeded() {
                const $imagePanel = $('.panel[data-panel="image"]');
                const hasFile = $('#file').get(0)?.files?.length > 0;
                const imageUrl = ($('#product_image_url').val() || '').trim();
                const previewSrc = ($('#summary-image-preview').attr('src') || '').trim();
                const hasImage = hasFile || imageUrl !== '' || previewSrc !== '';

                if (hasImage) {
                    if (!$imagePanel.hasClass('active')) {
                        $imagePanel.addClass('active');
                        $imagePanel.find('.panel__body').stop(true, true).slideDown(200);
                    }
                } else {
                    $imagePanel.removeClass('active');
                    $imagePanel.find('.panel__body').stop(true, true).slideUp(200);
                }
            }

            function showPreviewFromUrl(url) {
                $('#summary-image-preview').attr('src', url).show();
                openImagePanelIfNeeded();
            }

            function loadDaumPostcodeScript(callback) {
                if (window.daum && window.daum.Postcode) {
                    callback();
                    return;
                }

                const existed = document.querySelector('script[data-daum-postcode="1"]');
                if (existed) {
                    existed.addEventListener('load', callback, { once: true });
                    return;
                }

                const script = document.createElement('script');
                script.src = '//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js';
                script.async = true;
                script.dataset.daumPostcode = '1';
                script.onload = callback;
                document.head.appendChild(script);
            }

            function openAddressSearch() {
                loadDaumPostcodeScript(function () {
                    new daum.Postcode({
                        oncomplete: function (data) {
                            let addr = data.roadAddress || data.jibunAddress || '';
                            $('#delivery_addr1').val(addr + ' ').focus();

                            const input = $('#delivery_addr1').get(0);
                            if (input) {
                                input.setSelectionRange(input.value.length, input.value.length);
                            }

                            updateSummary();
                        }
                    }).open();
                });
            }

            $(document).on('change', '.money-list input[type="radio"]', function () {
                const target = $(this).closest('.money-list').data('target');
                const amount = parseInt($(this).val(), 10) || 0;
                const currentAmount = parseMoney($(target).val());

                if (amount === 0) {
                    $(target).val('');
                } else {
                    const nextAmount = currentAmount + amount;
                    $(target).val(nextAmount.toLocaleString('ko-KR'));
                }

                if (target === '#price1-input') {
                    syncOrderAmountFromOriginal();
                }

                updateSummary();
                $(this).prop('checked', false);
            });

            $(document).on('input blur', '#price1-input', function () {
                const amount = parseMoney($(this).val());
                $(this).val(amount > 0 ? amount.toLocaleString('ko-KR') : '');
                syncOrderAmountFromOriginal();
                updateSummary();
            });

            $(document).on('input blur', '#price2-input', function () {
                const amount = parseMoney($(this).val());
                $(this).val(amount > 0 ? amount.toLocaleString('ko-KR') : '');
                updateSummary();
            });

            $(document).on('change', 'input[name="product_name"]', function () {
                if ($(this).is(':checked')) {
                    $('input[name="product_name"]').not(this).prop('checked', false);
                    applyProductPreset($(this).val());
                } else {
                    updateSummary();
                }
            });

            $(document).on('change', '#delivery_now', function () {
                $('#delivery_now_hidden').val($(this).is(':checked') ? '1' : '0');

                if ($(this).is(':checked')) {
                    setImmediateDeliveryTime(true);
                }

                updateSummary();
            });

            $(document).on('change', '#delivery_date, #delivery_hour, #delivery_minute, input[name="delivery_time_type"]', function () {
                updateSummary();
            });

            $(document).on('input', '#delivery_addr1, #recipient_name, input[name="product_detail"]', function () {
                updateSummary();
            });

            $(document).on('change', 'input[name="ribbon_quick"]', function () {
                $('#ribbon_phrase').val($(this).val());
            });

            $(document).on('change', '#check1', function () {
                syncRequestPhotoNote();
            });

            $(document).on('blur', '#request_note', function () {
                syncRequestPhotoNote();
            });

            $(document).on('change', '#file', function () {
                const file = this.files && this.files[0] ? this.files[0] : null;

                if (!file) {
                    $('#summary-image-preview').attr('src', '').hide();
                    openImagePanelIfNeeded();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#summary-image-preview').attr('src', e.target.result).show();
                    openImagePanelIfNeeded();
                };
                reader.readAsDataURL(file);
            });

            $(document).on('input blur', '#product_image_url', function () {
                const imageUrl = ($(this).val() || '').trim();

                if (imageUrl === '') {
                    if (!($('#file').get(0)?.files?.length > 0)) {
                        $('#summary-image-preview').attr('src', '').hide();
                    }
                    openImagePanelIfNeeded();
                    return;
                }

                showPreviewFromUrl(imageUrl);
            });

            $(document).on('click', '#btn-preview-order', function () {
                updateSummary();
                alert('우측 주문정보 확인 패널에서 내용을 확인해 주세요.');
            });

            $(document).on('click', '#btn-address-search', function () {
                openAddressSearch();
            });

            $(document).on('submit', '#bonbu-balju-form', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }

                isSubmitting = true;
                openLoadingModal();
            });

            const $checkedProducts = $('input[name="product_name"]:checked');
            if ($checkedProducts.length > 1) {
                $checkedProducts.not(':first').prop('checked', false);
            }

            ensurePanelState();

            if (!hasOldInput) {
                syncRequestPhotoNote();
            }

            updateSummary();
            openImagePanelIfNeeded();

            const initialImageUrl = ($('#product_image_url').val() || '').trim();
            if (initialImageUrl !== '') {
                showPreviewFromUrl(initialImageUrl);
            }

            const initProduct = $('input[name="product_name"]:checked').val();
            if (initProduct && !hasOldInput) {
                applyProductPreset(initProduct);
            }

            $('#delivery_now_hidden').val($('#delivery_now').is(':checked') ? '1' : '0');
        });
    </script>
@endpush
