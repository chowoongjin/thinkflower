<div class="modal modal-small" id="modal-suju-item">
	<div class="modal__head">
		<div class="flex">
			<div class="flex__col">
				<h2 class="modal-title">배송가능지역 (1/2)</h2>
			</div>
			<div class="flex__col">
				<button type="button" class="modal-close">모달닫기</button>
			</div>
		</div>
	</div>
	<div class="modal__body" id="ajax-local-modal">
		
		<div class="grid-2">
			<div class="grid__col">
				<table class="table-data style3">
					<colgroup>
						<col style="width:140px;min-width:140px">
						<col style="width:50px;min-width:50px;max-width:50px">
					</colgroup>
					<thead>
						<tr>
							<th>대표 지역구</th>
							<th>선택</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><label for="local1-1">서울특별시</label></td>
							<td><input type="checkbox" name="local[]" value="서울특별시" id="local1-1"></td>
						</tr>
						<tr>
							<td><label for="local1-2">부산광역시</label></td>
							<td><input type="checkbox" name="local[]" value="부산광역시" id="local1-2"></td>
						</tr>
						<tr>
							<td><label for="local1-3">대구광역시</label></td>
							<td><input type="checkbox" name="local[]" value="대구광역시" id="local1-3"></td>
						</tr>
						<tr>
							<td><label for="local1-4">인천광역시</label></td>
							<td><input type="checkbox" name="local[]" value="인천광역시" id="local1-4"></td>
						</tr>
						<tr>
							<td><label for="local1-5">광주광역시</label></td>
							<td><input type="checkbox" name="local[]" value="광주광역시" id="local1-5"></td>
						</tr>
						<tr>
							<td><label for="local1-6">대전광역시</label></td>
							<td><input type="checkbox" name="local[]" value="대전광역시" id="local1-6"></td>
						</tr>
						<tr>
							<td><label for="local1-7">울산광역시</label></td>
							<td><input type="checkbox" name="local[]" value="울산광역시" id="local1-7"></td>
						</tr>
						<tr>
							<td><label for="local1-8">세종특별자치시</label></td>
							<td><input type="checkbox" name="local[]" value="세종특별자치시" id="local1-8"></td>
						</tr>
						<tr>
							<td><label for="local1-9">제주특별자치도</label></td>
							<td><input type="checkbox" name="local[]" value="제주특별자치도" id="local1-9"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="grid__col">
				<table class="table-data style3">
					<colgroup>
						<col style="width:140px;min-width:140px">
						<col style="width:50px;min-width:50px;max-width:50px">
					</colgroup>
					<thead>
						<tr>
							<th>대표 지역구</th>
							<th>선택</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><label for="local2-1">강원도</label></td>
							<td><input type="checkbox" name="local[]" value="강원도" id="local2-1"></td>
						</tr>
						<tr>
							<td><label for="local2-2">경기도</label></td>
							<td><input type="checkbox" name="local[]" value="경기도" id="local2-2"></td>
						</tr>
						<tr>
							<td><label for="local2-3">충청북도</label></td>
							<td><input type="checkbox" name="local[]" value="충청북도" id="local2-3"></td>
						</tr>
						<tr>
							<td><label for="local2-4">충청남도</label></td>
							<td><input type="checkbox" name="local[]" value="충청남도" id="local2-4"></td>
						</tr>
						<tr>
							<td><label for="local2-5">경상남도</label></td>
							<td><input type="checkbox" name="local[]" value="경상남도" id="local2-5"></td>
						</tr>
						<tr>
							<td><label for="local2-6">경상북도</label></td>
							<td><input type="checkbox" name="local[]" value="경상북도" id="local2-6"></td>
						</tr>
						<tr>
							<td><label for="local2-7">전라남도</label></td>
							<td><input type="checkbox" name="local[]" value="전라남도" id="local2-7"></td>
						</tr>
						<tr>
							<td><label for="local2-8">전라북도</label></td>
							<td><input type="checkbox" name="local[]" value="전라북도" id="local2-8"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="mt20">
			<button type="button" class="btn btn-fluid btn-green fs18" id="next-local" >상세 지역구 선택하기 <i class="bi bi-play-fill pl5"></i></button>
		</div>
	</div>
</div>

<script>

$(document).on('click', '#next-local', function () {

	const checked = $('input[name="local[]"]:checked');

	if (!checked.length) {
		alert('지역을 하나 이상 선택해 주세요.');
		return;
	}

	modal('modal3.php');

});
</script>