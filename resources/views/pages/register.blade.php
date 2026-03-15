@extends('layouts.sub')

@section('content')
    @if ($errors->any())
        <script>
            alert(@json($errors->first()));
        </script>
    @endif

    @if (session('success'))
        <script>
            alert(@json(session('success')));
        </script>
    @endif

    <div id="page-registration">
        <div class="visual">
            <img src="{{ asset('assets/img/bg_login.jpg') }}" class="fluid">
        </div>

        <div class="container-small">
            <div class="text-center titleArea">
                <img src="{{ asset('assets/img/symbol.png') }}" height="46">
                <h1 class="title">정직한플라워에 회원가입하고<br>꽃집 운영을 활발하게!</h1>
                <p class="color-primary">
                    이미 계정이 있으신가요?
                    <a href="{{ route('login') }}" class="color-primary underline">로그인하기</a>
                </p>
            </div>

            <form action="{{ route('register.store') }}" method="post" class="mt24" enctype="multipart/form-data">
                @csrf

                @php
                    $taxEmail = old('tax_email');
                    $taxEmailId = '';
                    $taxEmailDomain = '';

                    if ($taxEmail && str_contains($taxEmail, '@')) {
                        [$taxEmailId, $taxEmailDomain] = explode('@', $taxEmail, 2);
                    }
                @endphp

                <section class="row">
                    <div class="titleWrap">
                        <h2 class="title2">로그인정보</h2>
                        <span class="color-guide"><i class="bi bi-check-lg ico_check"></i> 필수입력항목</span>
                    </div>

                    <div class="field large mt24{{ $errors->has('login_id') ? ' --error' : '' }}">
                        <label>아이디 <i class="bi bi-check-lg ico_check ml5"></i></label>
                        <input type="text" name="login_id" value="{{ old('login_id') }}" placeholder="이용하실 아이디를 입력해주세요">
                        <p class="field-message">
                            @error('login_id')
                            {{ $message }}
                            @else
                                비밀번호는 5자 이상으로 작성 해 주세요
                                @enderror
                        </p>
                    </div>

                    <div class="field large{{ $errors->has('password') || $errors->has('password_confirmation') ? ' --error' : '' }}">
                        <label>비밀번호 <i class="bi bi-check-lg ico_check ml5"></i></label>
                        <div class="input-group-side">
                            <input type="password" name="password" placeholder="비밀번호를 입력해주세요">
                            <span><button type="button" data-toggle="pw"><i class="bi bi-eye"></i></button></span>
                        </div>
                        <div class="input-group-side">
                            <input type="password" name="password_confirmation" placeholder="비밀번호를 다시 입력해주세요">
                            <span><button type="button" data-toggle="pw"><i class="bi bi-eye"></i></button></span>
                        </div>
                        @error('password')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                        @error('password_confirmation')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt60">
                        <div class="flex">
                            <div class="flex__col">
                                <span class="color-green"><i class="bi bi-check-circle-fill"></i> 회원가입까지 30% 작성완료</span>
                            </div>
                            <div class="flex__col">
                                <div class="progress-wrap">
                                    <div class="progressbar" style="width:30%;"></div>
                                    <span class="progress-text">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="row mt60">
                    <div class="titleWrap">
                        <h2 class="title2">사업자 정보</h2>
                        <span class="color-guide"><i class="bi bi-check-lg ico_check"></i> 필수입력항목</span>
                    </div>

                    <div class="field large grid-2 mt24">
                        <div class="grid__col{{ $errors->has('business_no') ? ' --error' : '' }}">
                            <label>사업자록번호 <i class="bi bi-check-lg ico_check ml5"></i></label>
                            <input type="text" id="business_no" name="business_no" value="{{ old('business_no') }}" placeholder="사업자번호를 입력해 주세요">
                            @error('business_no')
                            <p class="field-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid__col{{ $errors->has('owner_name') ? ' --error' : '' }}">
                            <label>대표자명 <i class="bi bi-check-lg ico_check ml5"></i></label>
                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" placeholder="대표자 성함을 입력해 주세요">
                            @error('owner_name')
                            <p class="field-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="field large{{ $errors->has('business_addr1') || $errors->has('business_addr2') ? ' --error' : '' }}">
                        <div class="input-group-side">
                            <input type="text" name="business_addr1" id="business_addr1" value="{{ old('business_addr1') }}" placeholder="실제로 사업을 영위하는 사업장 주소지를 입력해 주세요" readonly>
                            <span><button type="button" class="btn btn-green" onclick="openBusinessPostcode()">주소검색</button></span>
                        </div>
                        <div class="input-group">
                            <input type="text" name="business_addr2" id="business_addr2" value="{{ old('business_addr2') }}" placeholder="상세주소를 입력해 주세요 (층, 호수등)">
                        </div>
                        @error('business_addr1')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                        @error('business_addr2')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field large{{ $errors->has('business_license') ? ' --error' : '' }}">
                        <div class="input-group-file">
                            <input type="file" id="file1" name="business_license" accept="image/*,.pdf,application/pdf">
                            <input type="text" name="fake_file1" placeholder="파일을 업로드 해주세요" class="fake_file_input" readonly>
                            <span>
                                <label for="file1"><i class="bi bi-upload"></i></label>
                            </span>
                        </div>
                        @error('business_license')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field large{{ $errors->has('tax_email') ? ' --error' : '' }}">
                        <label>계산서 수령 이메일 <i class="bi bi-check-lg ico_check ml5"></i></label>
                        <div class="input-group-email">
                            <input type="text" name="tax_email_id" value="{{ $taxEmailId }}">
                            <span>@</span>
                            <input type="text" name="tax_email_domain" value="{{ $taxEmailDomain }}" placeholder="naver.com">
                        </div>
                        @error('tax_email')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt60">
                        <div class="flex">
                            <div class="flex__col">
                                <span class="color-green"><i class="bi bi-check-circle-fill"></i> 회원가입까지 50% 작성완료</span>
                            </div>
                            <div class="flex__col">
                                <div class="progress-wrap">
                                    <div class="progressbar" style="width:50%;"></div>
                                    <span class="progress-text">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="row mt60">
                    <div class="titleWrap">
                        <h2 class="title2">매장운영 정보</h2>
                        <span class="color-guide"><i class="bi bi-check-lg ico_check"></i> 필수입력항목</span>
                    </div>

                    <div class="field large grid-2 mt24">
                        <div class="grid__col{{ $errors->has('shop_name') ? ' --error' : '' }}">
                            <label>화원사명 <i class="bi bi-check-lg ico_check ml5"></i></label>
                            <input type="text" name="shop_name" value="{{ old('shop_name') }}" placeholder="화원사 이름을 입력해 주세요">
                            @error('shop_name')
                            <p class="field-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid__col{{ $errors->has('career_years_label') ? ' --error' : '' }}">
                            <label>화원 운영경력 <i class="bi bi-check-lg ico_check ml5"></i></label>
                            <select name="career_years_label" class="custom-select">
                                <option value="">경력을 선택해 주세요</option>
                                <option value="0년 이상 3년 이하" @selected(old('career_years_label') === '0년 이상 3년 이하')>0년 이상 3년 이하</option>
                                <option value="3년 이상 7년 이하" @selected(old('career_years_label') === '3년 이상 7년 이하')>3년 이상 7년 이하</option>
                                <option value="7년 이상 10년 이하" @selected(old('career_years_label') === '7년 이상 10년 이하')>7년 이상 10년 이하</option>
                                <option value="10년 이상" @selected(old('career_years_label') === '10년 이상')>10년 이상</option>
                            </select>
                            @error('career_years_label')
                            <p class="field-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="field large grid-2 mt24">
                        <div class="grid__col{{ $errors->has('main_phone') ? ' --error' : '' }}">
                            <label>대표 연락망 <i class="bi bi-check-lg ico_check ml5"></i></label>
                            <input type="text" id="main_phone" name="main_phone" value="{{ old('main_phone') }}" placeholder="대표 연락망을 입력해 주세요">
                            @error('main_phone')
                            <p class="field-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid__col{{ $errors->has('fax') ? ' --error' : '' }}">
                            <label>팩스 수신번호 <i class="bi bi-check-lg ico_check ml5"></i></label>
                            <input type="text" id="fax_num" name="fax" value="{{ old('fax') }}" placeholder="팩스 수신번호를 입력해 주세요">
                            @error('fax')
                            <p class="field-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="field large{{ $errors->has('products') ? ' --error' : '' }}">
                        <label>수주 취급상품 <i class="bi bi-check-lg ico_check ml5"></i></label>

                        <div class="input-group-side" id="product-box">
                            <input type="text" id="btn_products" placeholder="제작 및 배송 가능한 상품을 선택해 주세요" {{ old('products') ? 'type=hidden' : '' }}>

                            @if(old('products'))
                                <div class="input-complete" id="product_complete">
                                    <i class="bi bi-check-circle-fill color-green pl5"></i>
                                    상품이 정상적으로 선택되어 있습니다
                                </div>
                            @endif

                            <input type="hidden" id="products" name="products" value="{{ old('products') }}">

                            <span>
                                <button type="button" class="btn btn-green" onclick="modal('{{ route('modal.products') }}');">상품선택</button>
                            </span>
                        </div>
                        @error('products')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field large{{ $errors->has('delivery_areas') ? ' --error' : '' }}">
                        <label>배송 가능지역 <i class="bi bi-check-lg ico_check ml5"></i></label>

                        <div class="input-group-side" id="region-box">
                            <input type="text" id="btn_regions" placeholder="배송 가능한 지역을 선택해 주세요" {{ old('delivery_areas') ? 'type=hidden' : '' }}>

                            @if(old('delivery_areas'))
                                <div class="input-complete" id="region_complete">
                                    <i class="bi bi-check-circle-fill color-green pl5"></i>
                                    배송지역이 정상적으로 선택되어 있습니다
                                </div>
                            @endif

                            <input type="hidden" id="delivery_areas" name="delivery_areas" value="{{ old('delivery_areas') }}">

                            <span>
                                <button type="button" class="btn btn-green" onclick="modal('{{ route('modal.regions.step1') }}');">지역선택</button>
                            </span>
                        </div>
                        @error('delivery_areas')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt60">
                        <div class="flex">
                            <div class="flex__col">
                                <span class="color-green"><i class="bi bi-check-circle-fill"></i> 회원가입까지 90% 작성완료</span>
                            </div>
                            <div class="flex__col">
                                <div class="progress-wrap">
                                    <div class="progressbar" style="width:90%;"></div>
                                    <span class="progress-text">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="row mt60">
                    <div class="titleWrap">
                        <h2 class="title2">정산 정보</h2>
                        <span class="color-guide"><i class="bi bi-check-lg ico_check"></i> 필수입력항목</span>
                    </div>

                    <div class="field large grid-2 mt24">
                        <div class="grid__col{{ $errors->has('bank_name') ? ' --error' : '' }}">
                            <label>입금은행 <i class="bi bi-check-lg ico_check ml5"></i></label>
                            <select name="bank_name" class="custom-select">
                                <option value="">은행을 선택해 주세요</option>
                                <option value="KB국민은행" @selected(old('bank_name') === 'KB국민은행')>KB국민은행</option>
                                <option value="신한은행" @selected(old('bank_name') === '신한은행')>신한은행</option>
                                <option value="우리은행" @selected(old('bank_name') === '우리은행')>우리은행</option>
                                <option value="하나은행" @selected(old('bank_name') === '하나은행')>하나은행</option>
                                <option value="NH농협은행" @selected(old('bank_name') === 'NH농협은행')>NH농협은행</option>
                                <option value="IBK기업은행" @selected(old('bank_name') === 'IBK기업은행')>IBK기업은행</option>
                                <option value="부산은행" @selected(old('bank_name') === '부산은행')>부산은행</option>
                                <option value="대구은행" @selected(old('bank_name') === '대구은행')>대구은행</option>
                                <option value="광주은행" @selected(old('bank_name') === '광주은행')>광주은행</option>
                                <option value="제주은행" @selected(old('bank_name') === '제주은행')>제주은행</option>
                                <option value="전북은행" @selected(old('bank_name') === '전북은행')>전북은행</option>
                                <option value="경남은행" @selected(old('bank_name') === '경남은행')>경남은행</option>
                                <option value="수협은행" @selected(old('bank_name') === '수협은행')>수협은행</option>
                                <option value="한국산업은행" @selected(old('bank_name') === '한국산업은행')>한국산업은행</option>
                                <option value="토스뱅크" @selected(old('bank_name') === '토스뱅크')>토스뱅크</option>
                                <option value="케이뱅크" @selected(old('bank_name') === '케이뱅크')>케이뱅크</option>
                            </select>
                            @error('bank_name')
                            <p class="field-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid__col{{ $errors->has('bank_holder') ? ' --error' : '' }}">
                            <label>예금주명 <i class="bi bi-check-lg ico_check ml5"></i></label>
                            <input type="text" name="bank_holder" value="{{ old('bank_holder') }}" placeholder="예금주 명을 입력해 주세요">
                            @error('bank_holder')
                            <p class="field-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="field large{{ $errors->has('bank_account') ? ' --error' : '' }}">
                        <label>입금 계좌번호 <i class="bi bi-check-lg ico_check ml5"></i></label>
                        <input type="text" id="bank_account" name="bank_account" value="{{ old('bank_account') }}" placeholder="입금 계좌번호를 입력해 주세요">
                        @error('bank_account')
                        <p class="field-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt60">
                        <div class="flex">
                            <div class="flex__col">
                                <span class="color-green register-complete-text"><i class="bi bi-check-circle-fill"></i> 모든 내용을 작성 완료했어요!</span>
                            </div>
                            <div class="flex__col">
                                <div class="progress-wrap">
                                    <div class="progressbar" style="width:100%;"></div>
                                    <span class="progress-text">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="section-agree" class="mt50">
                    <div class="titleWrap">
                        <h2 class="title2">전체 동의<input type="checkbox" id="checkALl"></h2>
                    </div>

                    <div class="field">
                        <div class="flex">
                            <div class="flex__col">
                                <div class="input-group-check">
                                    <input type="checkbox" name="agree_service" value="1" id="agree1" {{ old('agree_service') ? 'checked' : '' }}>
                                    <label for="agree1">서비스 이용약관(필수)</label>
                                </div>
                            </div>
                            <div class="flex__col">
                                <a href="#none" class="color-gray300">자세히보기</a>
                            </div>
                        </div>

                        <div class="flex">
                            <div class="flex__col">
                                <div class="input-group-check">
                                    <input type="checkbox" name="agree_privacy" value="1" id="agree2" {{ old('agree_privacy') ? 'checked' : '' }}>
                                    <label for="agree2">개인정보 수집 및 이용동의(필수)</label>
                                </div>
                            </div>
                            <div class="flex__col">
                                <a href="#none" class="color-gray300">자세히보기</a>
                            </div>
                        </div>

                        <div class="flex">
                            <div class="flex__col">
                                <div class="input-group-check">
                                    <input type="checkbox" name="agree_third_party" value="1" id="agree3" {{ old('agree_third_party') ? 'checked' : '' }}>
                                    <label for="agree3">개인정보 제3자 제공동의(필수)</label>
                                </div>
                            </div>
                            <div class="flex__col">
                                <a href="#none" class="color-gray300">자세히보기</a>
                            </div>
                        </div>

                        @if($errors->has('agree_service') || $errors->has('agree_privacy') || $errors->has('agree_third_party'))
                            <p class="field-message color-danger">필수 약관에 모두 동의해 주세요.</p>
                        @endif
                    </div>
                </section>

                <section id="section-submit">
                    <div class="field-error color-danger">아직 모든 사항이 입력되지 않았어요</div>
                    <button type="submit" class="btn btn-primary register-submit-button" disabled>
                        회원가입 완료 <i class="bi bi-arrow-right-circle"></i>
                    </button>
                </section>
            </form>
        </div>

        <footer>
            <div class="container-small">
                <div class="flex">
                    <div class="flex__col">
                        <strong>정직한플라워</strong><span>Thinkflow .Inc</span>
                    </div>
                    <div class="flex__col">
                        <span>고객센터</span>
                        <strong>1668-1840</strong>
                    </div>
                </div>

                <hr>

                <address>
                    대구광역시 달서구 구마로 40, E.Q TECH 3F 본부 수발주사업부<br>
                    사업자번호: 680-87-02988
                    통신판매업신고: 제 2024-대구달서-0884 호<br>
                    대표번호: 1668-1840
                    팩스: 053-715-2699
                    이메일: admin@thinkflow.info
                </address>
            </div>
        </footer>
    </div>

    <script>
        $(document).ready(function () {
            const $form = $('#page-registration form');
            const $submitBtn = $('#section-submit button:not(.test)');
            const $errorText = $('#section-submit .field-error');
            const $agreeChecks = $('#agree1, #agree2, #agree3');

            function hasValue(selector) {
                return $.trim($(selector).val() || '') !== '';
            }

            function isValidBusinessFile(file) {
                if (!file) return false;

                const fileName = (file.name || '').toLowerCase();
                const fileType = (file.type || '').toLowerCase();

                const allowedMimeTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'application/pdf'
                ];

                const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
                const ext = fileName.includes('.') ? fileName.split('.').pop() : '';

                return allowedMimeTypes.includes(fileType) || allowedExtensions.includes(ext);
            }

            function updateProductCompleteState() {
                if ($('#products').val()) {
                    $('#btn_products').attr('type', 'hidden');

                    if ($('#product_complete').length === 0) {
                        $('#btn_products').after(`
                        <div class="input-complete" id="product_complete">
                            <i class="bi bi-check-circle-fill color-green pl5"></i>
                            상품이 정상적으로 선택되어 있습니다
                        </div>
                    `);
                    }
                } else {
                    $('#btn_products').attr('type', 'text');
                    $('#product_complete').remove();
                }
            }

            function updateRegionCompleteState() {
                if ($('#delivery_areas').val()) {
                    $('#btn_regions').attr('type', 'hidden');

                    if ($('#region_complete').length === 0) {
                        $('#btn_regions').after(`
                        <div class="input-complete" id="region_complete">
                            <i class="bi bi-check-circle-fill color-green pl5"></i>
                            배송지역이 정상적으로 선택되어 있습니다
                        </div>
                    `);
                    }
                } else {
                    $('#btn_regions').attr('type', 'text');
                    $('#region_complete').remove();
                }
            }

            function isAllFormFilled() {
                const basicTextOk =
                    hasValue('input[name="login_id"]') &&
                    hasValue('input[name="password"]') &&
                    hasValue('input[name="password_confirmation"]') &&
                    hasValue('input[name="business_no"]') &&
                    hasValue('input[name="owner_name"]') &&
                    hasValue('#business_addr1') &&
                    hasValue('#business_addr2') &&
                    hasValue('input[name="tax_email_id"]') &&
                    hasValue('input[name="tax_email_domain"]') &&
                    hasValue('input[name="shop_name"]') &&
                    hasValue('select[name="career_years_label"]') &&
                    hasValue('input[name="main_phone"]') &&
                    hasValue('input[name="fax"]') &&
                    hasValue('#products') &&
                    hasValue('#delivery_areas') &&
                    hasValue('select[name="bank_name"]') &&
                    hasValue('input[name="bank_holder"]') &&
                    hasValue('input[name="bank_account"]');

                if (!basicTextOk) {
                    return false;
                }

                const hasExistingFileName = hasValue('input[name="fake_file1"]');
                const fileInput = $('#file1')[0];
                const hasSelectedFile = fileInput && fileInput.files && fileInput.files.length > 0;

                if (!hasExistingFileName && !hasSelectedFile) {
                    return false;
                }

                let agreesOk = true;
                $agreeChecks.each(function () {
                    if (!$(this).is(':checked')) {
                        agreesOk = false;
                        return false;
                    }
                });

                return agreesOk;
            }

            function updateSubmitState() {
                if (isAllFormFilled()) {
                    $submitBtn.prop('disabled', false);
                    $errorText.hide();
                } else {
                    $submitBtn.prop('disabled', true);
                    $errorText.show();
                }
            }

            function initProgressTargets() {
                $('.progress-wrap').each(function () {
                    const $wrap = $(this);
                    const $bar = $wrap.find('.progressbar');

                    if (!$bar.data('target')) {
                        const style = $bar.attr('style') || '';
                        const match = style.match(/width\s*:\s*(\d+)%/);
                        const targetPercent = match ? parseInt(match[1], 10) : 0;

                        $bar.data('target', targetPercent);
                        $bar.css('width', '0%');
                    }
                });
            }

            function updateAllProgress() {
                $('.progress-wrap').each(function () {
                    const $wrap = $(this);
                    const $bar = $wrap.find('.progressbar');
                    const $text = $wrap.find('.progress-text');
                    const targetPercent = parseInt($bar.data('target'), 10) || 0;

                    let filled = false;

                    if (targetPercent === 30) {
                        filled =
                            hasValue('input[name="login_id"]') &&
                            hasValue('input[name="password"]') &&
                            hasValue('input[name="password_confirmation"]');
                    } else if (targetPercent === 50) {
                        filled =
                            hasValue('input[name="business_no"]') &&
                            hasValue('input[name="owner_name"]') &&
                            hasValue('#business_addr1') &&
                            hasValue('#business_addr2') &&
                            hasValue('input[name="tax_email_id"]') &&
                            hasValue('input[name="tax_email_domain"]');

                        const fileInput = $('#file1')[0];
                        const hasFile = (fileInput && fileInput.files && fileInput.files.length > 0) || hasValue('input[name="fake_file1"]');
                        filled = filled && hasFile;
                    } else if (targetPercent === 90) {
                        filled =
                            hasValue('input[name="shop_name"]') &&
                            hasValue('select[name="career_years_label"]') &&
                            hasValue('input[name="main_phone"]') &&
                            hasValue('input[name="fax"]') &&
                            hasValue('#products') &&
                            hasValue('#delivery_areas');
                    } else if (targetPercent === 100) {
                        let agreesOk = true;
                        $agreeChecks.each(function () {
                            if (!$(this).is(':checked')) {
                                agreesOk = false;
                                return false;
                            }
                        });

                        filled =
                            hasValue('select[name="bank_name"]') &&
                            hasValue('input[name="bank_holder"]') &&
                            hasValue('input[name="bank_account"]') &&
                            agreesOk;
                    }

                    $bar.css('width', filled ? (targetPercent + '%') : '0%');
                    $text.text(filled ? (targetPercent + '%') : '0%');
                });
            }

            function refreshRegistrationUI() {
                updateProductCompleteState();
                updateRegionCompleteState();
                updateSubmitState();
                updateAllProgress();
            }

            $('#file1').on('change', function () {
                const file = this.files && this.files.length ? this.files[0] : null;

                if (!file) {
                    $('input[name="fake_file1"]').val('');
                    refreshRegistrationUI();
                    return;
                }

                if (!isValidBusinessFile(file)) {
                    alert('사업자등록증은 이미지 또는 PDF 파일만 업로드할 수 있습니다.');
                    $(this).val('');
                    $('input[name="fake_file1"]').val('');
                    refreshRegistrationUI();
                    return;
                }

                $('input[name="fake_file1"]').val(file.name);
                refreshRegistrationUI();
            });

            $('#checkALl').on('change', function () {
                const checked = $(this).is(':checked');
                $('#agree1, #agree2, #agree3').prop('checked', checked).trigger('change');
            });

            $form.on('input change', 'input, select, textarea', function () {
                refreshRegistrationUI();
            });

            $agreeChecks.on('change', function () {
                refreshRegistrationUI();
            });

            $(document).on('input change', '#products, #delivery_areas', function () {
                refreshRegistrationUI();
            });

            bindNumberDashOnly('#business_no');
            bindNumberDashOnly('#main_phone');
            bindNumberDashOnly('#fax_num');
            bindNumberDashOnly('#bank_account');

            initProgressTargets();
            refreshRegistrationUI();

            function bindNumberDashOnly(selector) {
                $(document).on('input', selector, function () {
                    let value = $(this).val() || '';
                    value = value.replace(/[^0-9-]/g, '');
                    $(this).val(value);
                });
            }
        });

        function openBusinessPostcode() {
            new kakao.Postcode({
                oncomplete: function (data) {
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

                    document.getElementById('business_addr1').value = addr + extraAddr;
                    document.getElementById('business_addr2').focus();

                    $('#business_addr1').trigger('input').trigger('change');
                    $('#business_addr2').trigger('input').trigger('change');
                }
            }).open();

        }
    </script>
@endsection
