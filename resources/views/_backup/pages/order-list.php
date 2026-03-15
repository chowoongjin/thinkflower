<?php include_once '_header.php';?>
<div id="content__body" style="width:900px;position:relative">
	
	<div id="all-balju">
		
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
			
			<section class="panel">
				<div class="panel__head">
					<h2 class="panel-title">간편발주 요약</h2>
				</div>
				<div class="panel__body">
					<ul class="list-qa">
						<li>
							<div class="q">금일 발주건수</div>
							<div class="a">12건</div>
						</li>
						<li>
							<div class="q">금일 미완료건</div>
							<div class="a color-orange">3건</div>
						</li>
					</ul>
				</div>
			</section>
			
			<section class="panel">
				<div class="flex">
					<div class="flex__col">간편하게 보기</div>
					<div class="flex__col">
						<label class="toggle">
						  <input type="checkbox">
						  <span class="track"></span>
						</label>
					</div>
				</div>
			</section>
		</aside>
	
	<section>
		<h2 class="tt">✔️ 본부발주<small>본사 수발주 사업부로 발주되어 중개됩니다.</small></h2>
	</section>
	
	<section class="mt30">
		<table class="table-data style2">
			<colgroup>
				<col style="width:100px">
				<col style="">
				<col style="width:100px">
				<col style="">
				<col style="width:100px">
				<col style="">
			</colgroup>
			<tbody>
				<tr>
					<th>조회기간 현황</th>
					<td colspan="5">
						38건을 <span class="color-orange">발주</span>하여 1,830,000원이 지불 되었습니다.
					</td>
				</tr>
				<tr>
					<th>부가설명</th>
					<td colspan="5">
						<ul class="list-inline" id="infoList">
							<li><div class="input-group-check"><input type="checkbox" class="blue"><label>예약배송건</label></div></li>
							<li><div class="input-group-check"><input type="checkbox" class="red"><label>금일배송건</label></div></li>
							<li><div class="input-group-check"><input type="checkbox" class="white"><label>배송완료건</label></div></li>
							<li><img src="./assets/img/ico_photo_on.png"> 상품이미지</li>
							<li><img src="./assets/img/ico_photo_off.png"> 이미지없음</li>
							<li><img src="./assets/img/ico_doc.png"> 처리내역 조회</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th>배송요구일</th>
					<td colspan="5">
						<div class="inline-flex">
							<div class="input-date-group">
								<i class="bi bi-calendar2-fill"></i>
								<input type="text" name="" class="datepicker" value="2025-05-01">
								<span>~</span>
								<input type="text" name="" class="datepicker" value="2025-05-02">
							</div>
							<div class="input-group-radio pl15">
								<input type="radio" name="test" value="이번달" id="test1"><label for="test1">이번 달</label>
								<input type="radio" name="test" value="지난달" id="test2"><label for="test2">지난 달</label>
								<input type="radio" name="test" value="오늘" id="test3"><label for="test3">오늘</label>
								<input type="radio" name="test" value="내일" id="test4"><label for="test4">내일</label>
								<input type="radio" name="test" value="어제" id="test5"><label for="test5">어제</label>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>상품별 조회</th>
					<td colspan="5">
						<div class="input-group-radio">
							<input type="radio" name="test2" value="전체상품" id="test2-1" checked="checked"><label for="test2-1">전체상품</label>
							<input type="radio" name="test2" value="근조화환" id="test2-2"><label for="test2-2">근조화환</label>
							<input type="radio" name="test2" value="축하화환" id="test2-3"><label for="test2-3">축하화환</label>
							<input type="radio" name="test2" value="꽃바구니" id="test2-4"><label for="test2-4">꽃바구니</label>
							<input type="radio" name="test2" value="관엽식물" id="test2-5"><label for="test2-5">관엽식물</label>
							<input type="radio" name="test2" value="동양란" id="test2-6"><label for="test2-6">동양란</label>
							<input type="radio" name="test2" value="서양란" id="test2-7"><label for="test2-7">서양란</label>
						</div>
					</td>
				</tr>
				<tr>
					<th>주문번호 검색</th>
					<td>
						<div class="input-group-search">
							<i class="bi bi-search"></i>
							<input type="text" name="" placeholder="주문번호를 입력해주세요">
						</div>
					</td>
					<th>주소지 검색</th>
					<td>
						<div class="input-group-search">
							<i class="bi bi-search"></i>
							<input type="text" name="" placeholder="주문번호를 입력해주세요">
						</div>
					</td>
					<th>받는분 검색</th>
					<td>
						<div class="input-group-search">
							<i class="bi bi-search"></i>
							<input type="text" name="" placeholder="주문번호를 입력해주세요">
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</section>
	
	<!-- 최근 발주리스트 -->
	<section class="row mt20">
		
		<table class="table mt20">
			<caption>최근 발주리스트</caption>
			<cogroup>
				<col style="width:60px">
				<col style="width:160px">
				<col style="width:130px">
				<col style="width:180px">
				<col style="width:60px">
				<col style="width:120px">
				<col style="width:90px">
				<col style="width:40px">
				<col style="width:40px">
				<col style="width:66px">
			</cogroup>
			<thead>
				<tr>
					<th>주문번호</th>
					<th>주문접수일<br>배송요구일</th>
					<th>발주화원사<br>수주화원사</th>
					<th>보내는 문구<br>배송지</th>
					<th>담당자<br>받는분</th>
					<th>주문상품<br>상세정보</th>
					<th>원청금액<br>결제금액</th>
					<th>처리<br>내역</th>
					<th>배송<br>사진</th>
					<th>배송현황<br>인수정보</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr-primary">
					<td class="no-ellipsis"><span class="color-blue">23456</span></td>
					<td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">18:00</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_off.png" height="18"></button></td>
					<td class="fs13">
						<span class="color-red">중개대기</span>
					</td>
				</tr>
				<tr class="tr-primary">
					<td class="no-ellipsis"><span class="color-blue">57810</span></td>
					<td><span class="color-gray300">2025/07/04 14:45</span><br>2025/07/05 <span class="color-orange">12:00</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>(주)그린에너지 연구소장 서테스트123</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_off.png" height="18"></button></td>
					<td class="fs13">
						<span class="color-red">중개대기</span>
					</td>
				</tr>
				<tr class="tr-warning">
					<td class="no-ellipsis"><span class="color-blue">57810</span></td>
					<td><span class="color-gray300">2025/07/04 14:45</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>(주)그린에너지 연구소장 서테스트123</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_on.png" height="18"></button></td>
					<td class="fs13">
						<span class="color-red">중개대기</span>
					</td>
				</tr>
				<tr class="tr-warning">
					<td class="no-ellipsis"><span class="color-blue">57810</span></td>
					<td><span class="color-gray300">2025/07/04 14:45</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>(주)그린에너지 연구소장 서테스트123</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_off.png" height="18"></button></td>
					<td class="fs13">
						<span>본부확인</span>
					</td>
				</tr>
				<tr class="tr-warning">
					<td class="no-ellipsis"><span class="color-blue">57810</span></td>
					<td><span class="color-gray300">2025/07/04 14:45</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>(주)그린에너지 연구소장 서테스트123</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_on.png" height="18"></button></td>
					<td class="fs13">
						<span>주문접수</span>
					</td>
				</tr>
				<tr>
					<td class="no-ellipsis"><span class="color-blue">57810</span></td>
					<td><span class="color-gray300">2025/07/04 14:45</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>(주)그린에너지 연구소장 서테스트123</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_on.png" height="18"></button></td>
					<td class="fs13">
						<span>김현수</span>
					</td>
				</tr>
				<tr>
					<td class="no-ellipsis"><span class="color-blue">57810</span></td>
					<td><span class="color-gray300">2025/07/04 14:45</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>(주)그린에너지 연구소장 서테스트123</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_on.png" height="18"></button></td>
					<td class="fs13">
						<span>현장배치</span>
					</td>
				</tr>
				<tr>
					<td class="no-ellipsis"><span class="color-blue">57810</span></td>
					<td><span class="color-gray300">2025/07/04 14:45</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>(주)그린에너지 연구소장 서테스트123</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_on.png" height="18"></button></td>
					<td class="fs13">
						<span>본인</span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<nav class="pagination-wrap">
		  <ul class="pagination">
		    <li class="page-item disabled">
		      <a href="#" class="page-link">‹</a>
		    </li>
		
		    <li class="page-item active">
		      <a href="#" class="page-link">1</a>
		    </li>
		    <li class="page-item">
		      <a href="#" class="page-link">2</a>
		    </li>
		    <li class="page-item">
		      <a href="#" class="page-link">3</a>
		    </li>
		
		    <li class="page-item">
		      <a href="#" class="page-link">›</a>
		    </li>
		  </ul>
		</nav>

	</section>
	
	
	
	</div>
	
</div>
<?php include_once '_footer.php';?>

	
