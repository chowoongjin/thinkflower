<section class="mt30">
    <form id="mediation-list-filter-form" method="GET" action="{{ route('admin.mediation-list') }}">
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
					<th>부가설명</th>
					<td colspan="5">
						<ul class="list-inline" id="infoList">
							<li><div class="input-group-check"><input type="checkbox" class="blue"><label>예약배송건</label></div></li>
							<li><div class="input-group-check"><input type="checkbox" class="red"><label>금일배송건</label></div></li>
							<li><div class="input-group-check"><input type="checkbox" class="white"><label>배송완료건</label></div></li>
							<li><img src="{{ asset('adm/assets/img/ico_photo_on.png') }}"> 상품이미지</li>
							<li><img src="{{ asset('adm/assets/img/ico_photo_off.png') }}"> 이미지없음</li>
							<li><img src="{{ asset('adm/assets/img/ico_doc.png') }}"> 처리내역 조회</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th>조회기간 설정</th>
					<td colspan="5">
						<div class="inline-flex">
							<div class="input-date-group">
								<i class="bi bi-calendar2-fill"></i>
								<input type="text" name="date_from" class="datepicker" value="{{ $dateFrom }}">
								<span>~</span>
								<input type="text" name="date_to" class="datepicker" value="{{ $dateTo }}">
							</div>
							<div class="input-group-radio pl15">
								<input type="radio" name="range_preset" value="thisMonth" id="test1" {{ $rangePreset === 'thisMonth' ? 'checked' : '' }}><label for="test1">이번 달</label>
								<input type="radio" name="range_preset" value="lastMonth" id="test2" {{ $rangePreset === 'lastMonth' ? 'checked' : '' }}><label for="test2">지난 달</label>
								<input type="radio" name="range_preset" value="today" id="test3" {{ $rangePreset === 'today' ? 'checked' : '' }}><label for="test3">오늘</label>
								<input type="radio" name="range_preset" value="tomorrow" id="test4" {{ $rangePreset === 'tomorrow' ? 'checked' : '' }}><label for="test4">내일</label>
								<input type="radio" name="range_preset" value="yesterday" id="test5" {{ $rangePreset === 'yesterday' ? 'checked' : '' }}><label for="test5">어제</label>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>상품별 조회</th>
					<td colspan="5">
						<div class="input-group-radio">
							<input type="radio" name="product_type" value="전체상품" id="test2-1" {{ $productType === '전체상품' ? 'checked' : '' }}><label for="test2-1">전체상품</label>
							<input type="radio" name="product_type" value="근조화환" id="test2-2" {{ $productType === '근조화환' ? 'checked' : '' }}><label for="test2-2">근조화환</label>
							<input type="radio" name="product_type" value="축하화환" id="test2-3" {{ $productType === '축하화환' ? 'checked' : '' }}><label for="test2-3">축하화환</label>
							<input type="radio" name="product_type" value="꽃바구니" id="test2-4" {{ $productType === '꽃바구니' ? 'checked' : '' }}><label for="test2-4">꽃바구니</label>
							<input type="radio" name="product_type" value="관엽식물" id="test2-5" {{ $productType === '관엽식물' ? 'checked' : '' }}><label for="test2-5">관엽식물</label>
							<input type="radio" name="product_type" value="동양란" id="test2-6" {{ $productType === '동양란' ? 'checked' : '' }}><label for="test2-6">동양란</label>
							<input type="radio" name="product_type" value="서양란" id="test2-7" {{ $productType === '서양란' ? 'checked' : '' }}><label for="test2-7">서양란</label>
						</div>
					</td>
				</tr>
				<tr>
					<th>주문번호 검색</th>
					<td>
						<div class="input-group-search">
							<i class="bi bi-search"></i>
							<input type="text" name="order_no" value="{{ $orderNo }}" placeholder="주문번호를 입력해주세요">
						</div>
					</td>
					<th>받는분 검색</th>
					<td>
						<div class="input-group-search">
							<i class="bi bi-search"></i>
							<input type="text" name="recipient_name" value="{{ $recipientName }}" placeholder="받는 분을 입력해주세요">
						</div>
					</td>
					<th>주소지 검색</th>
					<td>
						<div class="input-group-search">
							<i class="bi bi-search"></i>
							<input type="text" name="delivery_addr" value="{{ $deliveryAddr }}" placeholder="주소지를 입력해주세요">
						</div>
					</td>
				</tr>
			</tbody>
		</table>
    </form>
</section>
