<?php include_once '_header.sub.php';?>

<!--
	💬 설명
	<div class="field">에 --error 추가시 에러모드
	<div class="field">에 --success 추가시 성공모드
-->

<div id="page-registration">
	<div class="visual">
		<img src="./assets/img/bg_login.jpg" class="fluid">
	</div>
	<div class="container-small">
		<div class="text-center titleArea">
			<img src="./assets/img/symbol.png" height="46">
			<h1 class="title">정직한플라워에 회원가입하고<br>꽃집 운영을 활발하게!</h1>
			<p class="color-primary">이미 계정이 있으신가요? <a href="#none" class="color-primary underline">로그인하기</a></p>
		</div>
		
		<form action="" method="post" class="mt24">
			
			<section class="row">
				<div class="titleWrap">
					<h2 class="title2">로그인정보</h2>
					<span class="color-guide"><i class="bi bi-check-lg ico_check"></i> 필수입력항목</span>
				</div>

		
				<div class="field large mt24">
					<label>아이디 <i class="bi bi-check-lg ico_check ml5"></i></label>	
					<input type="text" name="" placeholder="이용하실 아이디를 입력해주세요">
					<p class="field-message">비밀번호는 5자 이상으로 작성 해 주세요</p>
				</div>
				<div class="field large">
					<label>비밀번호 <i class="bi bi-check-lg ico_check ml5"></i></label>
					<div class="input-group-side">
						<input type="password" name="" placeholder="비밀번호를 입력해주세요">
						<span><button type="button" data-toggle="pw"><i class="bi bi-eye"></i></button>
					</div>
					<div class="input-group-side">
						<input type="password" name="" placeholder="비밀번호를 다시 입력해주세요">
						<span><button type="button" data-toggle="pw"><i class="bi bi-eye"></i></button>
					</div>
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
			
			<!-- ======== 사업자 정보 ======== -->
			<section class="row mt60">
				<div class="titleWrap">
					<h2 class="title2">사업자 정보</h2>
					<span class="color-guide"><i class="bi bi-check-lg ico_check"></i> 필수입력항목</span>
				</div>
				
				<div class="field large grid-2 mt24">
					<div class="grid__col">
						<label>사업자록번호 <i class="bi bi-check-lg ico_check ml5"></i></label>
						<input type="text" name="" placeholder="사업자번호를 입력해 주세요">
					</div>
					<div class="grid__col">
						<label>대표자명 <i class="bi bi-check-lg ico_check ml5"></i></label>
						<input type="text" name="" placeholder="대표자 성함을 입력해 주세요">
					</div>
				</div>
				<div class="field large">
					<div class="input-group-side">
						<input type="text" name="" placeholder="실제로 사업을 영위하는 사업장 주소지를 입력해 주세요">
						<span><button type="button" class="btn btn-green">주소검색</button>
					</div>
					<div class="input-group">
						<input type="text" name="" placeholder="상세주소를 입력해 주세요 (층, 호수등)">
					</div>
				</div>
				
				<!-- // 파일첨부
					 // input id와 label id가 일치해야됨 
				-->
				<div class="field large">
				    <div class="input-group-file">
				        <input type="file" name="" id="file1"> <!-- 👈 감춤 -->
				        <input type="text" name="fake_file1"
				               placeholder="파일을 업로드 해주세요"
				               class="fake_file_input"
				               readonly> <!-- 👈 파일명 들어갈 가짜 input -->
				        <span>
				            <label for="file1"><i class="bi bi-upload"></i></label>
				        </span>
				    </div>
				</div>
				
				<div class="field large">
					<label>계산서 수령 이메일 <i class="bi bi-check-lg ico_check ml5"></i></label>
					<div class="input-group-email">
						<input type="text" name="">
						<span>@</span>
						<input type="text" name="" placeholder="naver.com">
					</div>
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
			
			<!-- ======== 매장 운영정보 ======== -->
			<section class="row mt60">
				<div class="titleWrap">
					<h2 class="title2">매장운영 정보</h2>
					<span class="color-guide"><i class="bi bi-check-lg ico_check"></i> 필수입력항목</span>
				</div>
				
				<div class="field large grid-2 mt24">
					<div class="grid__col">
						<label>화원사명 <i class="bi bi-check-lg ico_check ml5"></i></label>
						<input type="text" name="" placeholder="화원사 이름을 입력해 주세요">
					</div>
					<div class="grid__col">
						<label>화원 운영경력 <i class="bi bi-check-lg ico_check ml5"></i></label>
						<select name="" class="custom-select">
							<option value="">경력을 선택해 주세요</option>
							<option value="0년 이상 3년 이하">0년 이상 3년 이하</option>
							<option value="3년 이상 7년 이하">3년 이상 7년 이하</option>
							<option value="7년 이상 10년 이하">7년 이상 10년 이하</option>
							<option value="10년 이상">10년 이상</option>
						</select>
					</div>
				</div>
				<div class="field large grid-2 mt24">
					<div class="grid__col">
						<label>대표 연락망 <i class="bi bi-check-lg ico_check ml5"></i></label>
						<input type="text" name="" placeholder="사업자번호를 입력해 주세요">
					</div>
					<div class="grid__col">
						<label>팩스 수신번호 <i class="bi bi-check-lg ico_check ml5"></i></label>
						<input type="text" name="" placeholder="대표자 성함을 입력해 주세요">
					</div>
				</div>
				<div class="field large">
					<label>수주 취급상품 <i class="bi bi-check-lg ico_check ml5"></i></label>
					<div class="input-group-side">
						
						<!-- OFF :: 상품 선택 전 -->
						<input type="text" name="" placeholder="제작 및 배송 가능한 상품을 선택해 주세요">
						
						<!-- ON :: 상품선택 정상 입력시 
						<div class="input-complete"><i class="bi bi-check-circle-fill color-green pl5"></i> 상품이 정상적으로 선택되어 있습니다</div>
						-->
						
						
						<span><button type="button" class="btn btn-green" onclick="modal('modal1.php');">상품선택</button>
					</div>
				</div>
				<div class="field large">
					<label>배송 가능지역 <i class="bi bi-check-lg ico_check ml5"></i></label>
					<div class="input-group-side">
						
						<!-- OFF :: 배송가능 지역 정상 입력시 -->
						<input type="text" name="" placeholder="배송 가능한 지역을 선택해 주세요">
						
						<!-- ON :: 배송가능 지역 정상 입력시 
						<div class="input-complete"><i class="bi bi-check-circle-fill color-green pl5"></i> 배송지역이 정상적으로 선택되어 있습니다</div>
						-->
						<span><button type="button" class="btn btn-green" onclick="modal('modal2.php');">지역선택</button>
					</div>
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
			
			<!-- ======== 정산 정보 ======== -->
			<section class="row mt60">
				<div class="titleWrap">
					<h2 class="title2">정산 정보</h2>
					<span class="color-guide"><i class="bi bi-check-lg ico_check"></i> 필수입력항목</span>
				</div>
				
				<div class="field large grid-2 mt24">
					<div class="grid__col">
						<label>입금은행 <i class="bi bi-check-lg ico_check ml5"></i></label>
						<select name="" class="custom-select">
							<option value="">은행을 선택해 주세요</option>
							<option value="KB국민은행">KB국민은행</option>
							<option value="신한은행">신한은행</option>
							<option value="우리은행">우리은행</option>
							<option value="하나은행">하나은행</option>
							<option value="NH농협은행">NH농협은행</option>
							<option value="IBK기업은행">IBK기업은행</option>
							<option value="부산은행">부산은행</option>
							<option value="대구은행">대구은행</option>
							<option value="광주은행">광주은행</option>
							<option value="제주은행">제주은행</option>
							<option value="전북은행">전북은행</option>
							<option value="경남은행">경남은행</option>
							<option value="수협은행">수협은행</option>
							<option value="한국산업은행">한국산업은행</option>
							<option value="토스뱅크">토스뱅크</option>
							<option value="케이뱅크">케이뱅크</option>
						</select>
					</div>
					<div class="grid__col">
						<label>예금주명 <i class="bi bi-check-lg ico_check ml5"></i></label>
						<input type="text" name="" placeholder="예금주 명을 입력해 주세요">
					</div>
				</div>
				
				<div class="field large">
					<label>입금 계좌번호 <i class="bi bi-check-lg ico_check ml5"></i></label>
					<input type="text" name="" placeholder="입금 계좌번호를 입력해 주세요">
				</div>
				
				<div class="mt60">
					<div class="flex">
						<div class="flex__col">
							<span class="color-green"><i class="bi bi-check-circle-fill"></i> 모든 내용을 작성 완료했어요!</span>
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
			
			<!-- ======== 전체 동의 ======== -->
			<section id="section-agree" class="mt50">
				<div class="titleWrap">
					<h2 class="title2">전체 동의<input type="checkbox" id="checkALl"></h2>
				</div>
				<div class="field">
					<div class="flex">
						<div class="flex__col">
							<div class="input-group-check">
								<input type="checkbox" name="" value="1" id="agree1"><label for="agree1">서비스 이용약관(필수)</label>
							</div>
						</div>
						<div class="flex__col">
							<a href="#none" class="color-gray300">자세히보기</a>
						</div>
					</div>
					<div class="flex">
						<div class="flex__col">
							<div class="input-group-check">
								<input type="checkbox" name="" value="1" id="agree2"><label for="agree2">개인정보 수집 및 이용동의(필수)</label>
							</div>
						</div>
						<div class="flex__col">
							<a href="#none" class="color-gray300">자세히보기</a>
						</div>
					</div>
					<div class="flex">
						<div class="flex__col">
							<div class="input-group-check">
								<input type="checkbox" name="" value="1" id="agree3"><label for="agree3">개인정보 제3자 제공동의(필수)</label>
							</div>
						</div>
						<div class="flex__col">
							<a href="#none" class="color-gray300">자세히보기</a>
						</div>
					</div>
				</div>
			</section>
			
			<!-- ======== 회원가입 완료 버튼 ======== -->
			<section id="section-submit">
				<div class="field-error color-danger">아직 모든 사항이 입력되지 않았어요</div>
				<button type="button" class="btn btn-primary" disabled onclick="modal('modal-registration-complete.php');">회원가입 완료 <i class="bi bi-arrow-right-circle"></i></button>
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
	
	// ------------------
	// Validates
	// -------------------
	const $form        = $('#page-registration form');
    const $submitBtn   = $('#section-submit button:not(.test)');
    const $errorText   = $('#section-submit .field-error');

    // 전체 입력 검사 대상
    const $allInputs = $form.find('input, select, textarea')
        .not('[type="file"]')
        .not('.fake_file_input')
        .not('[type="checkbox"]');

    const $agreeChecks = $('#agree1, #agree2, #agree3');

    function isAllFormFilled() {
        let ok = true;

        // input / select / textarea 검사
        $allInputs.each(function () {
            const $el = $(this);

            if ($el.is('select')) {
                if (!$el.val()) {
                    ok = false;
                    return false;
                }
            } else {
                if ($el.val().trim() === '') {
                    ok = false;
                    return false;
                }
            }
        });

        if (!ok) return false;

        // 필수 동의 체크박스 검사
        $agreeChecks.each(function () {
            if (!$(this).is(':checked')) {
                ok = false;
                return false;
            }
        });

        return ok;
    }

    function updateSubmitState() {
        if (isAllFormFilled()) {
            // ✅ 모두 입력 완료
            $submitBtn.prop('disabled', false);
            $errorText.hide();
        } else {
            // ❌ 하나라도 미입력
            $submitBtn.prop('disabled', true);
            $errorText.show();
        }
    }

    // 이벤트 바인딩
    $form.on('input change', 'input, select, textarea', updateSubmitState);
    $agreeChecks.on('change', updateSubmitState);

    // 최초 1회 상태 체크
    updateSubmitState();

    $('.progress-wrap').each(function () {

        const $wrap = $(this);
        const $bar  = $wrap.find('.progressbar');

        // 목표 퍼센트
        const style = $bar.attr('style') || '';
        const match = style.match(/width\s*:\s*(\d+)%/);
        const targetPercent = match ? match[1] : '0';

        // 초기화
        $bar.css('width', '0%');

        // ⭐ progress 위의 입력들만 수집
        const $inputs = $wrap
            .closest('.mt60')
            .prevAll()
            .find('input, select, textarea')
            .not('[type="file"]')        // file 제외
            .not('.fake_file_input');    // ⭐ 핵심: 가짜 파일 input 제외

        function isAllFilled() {
            let ok = true;

            $inputs.each(function () {
                const $el = $(this);

                // select: value 있는 옵션만 인정
                if ($el.is('select')) {
                    if (!$el.val()) {
                        ok = false;
                        return false;
                    }
                }
                // 일반 input / textarea
                else {
                    if ($el.val().trim() === '') {
                        ok = false;
                        return false;
                    }
                }
            });

            return ok;
        }

        function updateProgress() {
            if (isAllFilled()) {
                $bar.css('width', targetPercent + '%');
            } else {
                $bar.css('width', '0%');
            }
        }

        $inputs.on('input change', updateProgress);

    });

});
</script>
<?php include_once '_footer.sub.php';?>