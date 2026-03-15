<?php include_once '_header.php';?>
<div id="content__head">
	<img src="./assets/img/content_head1.png" alt="">
</div>
<div id="content__body">
	
	<!-- 3개 박스 -->
	<ul class="list-column-3-small mt20">
		<li>
			<div class="box">
				<div class="flex">
					<div class="flex__col">
						중개대기 발주건
					</div>
					<div class="flex__col">
						<strong class="fs18 color-primary">2건</strong>
					</div>
				</div>
			</div>
		</li>
		<li>
			<div class="box">
				<div class="flex">
					<div class="flex__col">
						배송준비 잘주건
					</div>
					<div class="flex__col">
						<strong class="fs18 color-primary">2건</strong>
					</div>
				</div>
			</div>
		</li>
		<li>
			<div class="box active">
				<div class="flex">
					<div class="flex__col">
						미확인 수주건
					</div>
					<div class="flex__col">
						<strong class="fs18 color-primary">2건</strong>
					</div>
				</div>
			</div>
		</li>
	</ul>
	
	<!-- 최근 발주리스트 -->
	<section class="row mt40">
		<div class="flex">
			<div class="flex__col">
				<h2 class="tt">✔️ 최근 발주리스트 간편조회</h2>
			</div>
			<div class="flex__col">
				<a href="#none" class="fs15 color-8c8c8c">전체 발주리스트</a>
			</div>
		</div>
		
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
			</tbody>
		</table>
	</section>
	
	<!-- 최근 수주리스트 -->
	<section class="row mt40">
		<div class="flex">
			<div class="flex__col">
				<h2 class="tt">✔️ 최근 수주리스트 간편조회</h2>
			</div>
			<div class="flex__col">
				<a href="#none" class="fs15 color-8c8c8c">전체 발주리스트</a>
			</div>
		</div>
		
		<table class="table mt20">
			<caption>최근 수주리스트</caption>
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
					<th>수주화원사<br>발주화원사</th>
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
				<tr class="tr-warning">
					<td class="no-ellipsis"><span class="color-blue">23456</span></td>
					<td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_off.png" height="18"></button></td>
					<td class="fs13">
						<button type="button" class="btn btn-orange">등록</button>
					</td>
				</tr>
				<tr class="tr-warning">
					<td class="no-ellipsis"><span class="color-blue">23456</span></td>
					<td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_off.png" height="18"></button></td>
					<td class="fs13">
						<button type="button" class="btn btn-orange">등록</button>
					</td>
				</tr>
				<tr class="tr-warning">
					<td class="no-ellipsis"><span class="color-blue">23456</span></td>
					<td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_on.png" height="18"></button></td>
					<td class="fs13">
						<button type="button" class="btn btn-orange">등록</button>
					</td>
				</tr>
				<tr class="tr-warning">
					<td class="no-ellipsis"><span class="color-blue">23456</span></td>
					<td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_on.png" height="18"></button></td>
					<td class="fs13">
						<button type="button" class="btn btn-orange">등록</button>
					</td>
				</tr>
				<tr class="tr-warning">
					<td class="no-ellipsis"><span class="color-blue">23456</span></td>
					<td><span class="color-gray300">2025/07/04 11:30</span><br>2025/07/05 <span class="color-orange">지금즉시</span></td>
					<td><span class="color-gray300">메이플라워(대구)</span><br>드라이플라워(울산)</td>
					<td><span class="color-gray300">회계법인 더함 공인회계사...</span><br>울산광역시 동구 방어진순환...</td>
					<td><span class="color-gray300">미입력</span><br>한도현</td>
					<td><span class="color-gray300">관엽식물</span><br><b class="color-green">해피트리 바닥용</b></td>
					<td>100,000원<br><b class="color-green">80,000원</b></td>
					<td><button type="button"><img src="./assets/img/ico_doc.png" height="18"></button></td>
					<td><button type="button"><img src="./assets/img/ico_photo_on.png" height="18"></button></td>
					<td class="fs13">
						<button type="button" class="btn btn-orange">등록</button>
					</td>
				</tr>
			</tbody>
		</table>
	</section>
	
</div>
<?php include_once '_footer.php';?>

	
