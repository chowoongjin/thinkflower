$(document).ready(function () {

    $('[data-toggle="pw"]').on('click', function () {

        const $btn   = $(this);
        const $icon  = $btn.find('i');
        const $input = $btn.closest('.input-group-side').find('input');

        const isPw = $input.attr('type') === 'password';

        // input 타입 토글
        $input.attr('type', isPw ? 'text' : 'password');

        // 아이콘 토글
        if (isPw) {
            $icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            $icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    // ----------------------------------
    // FAKE 첨부파일
    // ----------------------------------
    // fake input 클릭 → 같은 그룹의 file input 클릭
    $('.fake_file_input').on('click', function () {
        $(this)
            .closest('.input-group-file')
            .find('input[type="file"]')
            .trigger('click');
    });

    // 파일 선택 시 → 파일명 fake input에 표시
    $('input[type="file"]').on('change', function () {

        const fileName = this.files.length
            ? this.files[0].name
            : '';

        $(this)
            .closest('.input-group-file')
            .find('.fake_file_input')
            .val(fileName);
    });

});

// 토글 스위치 :: checkbox
document.addEventListener('change', function (e) {
    if (!e.target.classList.contains('toggle-input')) return;

    const isOn = e.target.checked;

    console.log('토글 상태:', isOn ? 'ON' : 'OFF');

    // 예시: AJAX / 상태 저장
    // fetch('/save', { method:'POST', body: JSON.stringify({ enabled:isOn }) })
});

// --------------------------------------------
// Modal
// --------------------------------------------
function modal(url){
	$.ajax({
		url:url,
		success: function(res){
			$("#modal,body").addClass("active");
			$("#ajax-modal").html(res);
		},
		error: function(){
			alert('모달 에러');
		}
	});
}

// ---------------------------------------------
// Dropdown
// ---------------------------------------------
$(function () {
	$('.dropdown > button').on('click', function (e) {
		e.preventDefault();

		var $dropdown = $(this).closest('.dropdown');

		$dropdown.toggleClass('on');
		$dropdown.find('.dropdown-content').toggle();
	});
});

// ----------------------------------------------
// Attachment
// ----------------------------------------------
$(document).on('click', '.attachment .file-name', function () {
	$(this).siblings('.file-input').trigger('click');
});

$(document).on('change', '.attachment .file-input', function () {
	const fileName = this.files.length ? this.files[0].name : '';
	$(this).siblings('.file-name').val(fileName);
});
$(document).on('click', '.btn-del.--attachment', function () {
	const $attachment = $(this).closest('tr').find('.attachment');
	
	// text input 비우기
	$attachment.find('.file-name').val('');

	// file input 완전 초기화 (DOM 교체)
	const $oldFile = $attachment.find('.file-input');
	const $newFile = $oldFile.clone().val('');
	$oldFile.replaceWith($newFile);
});

// ----------------------------------------------
// Datepicker
// ----------------------------------------------
$(function () {

	// 한국어 설정
	$.datepicker.setDefaults({
		dateFormat: 'yy-mm-dd',   // ⭐ 2025-01-01
		prevText: '이전 달',
		nextText: '다음 달',
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		showMonthAfterYear: true,
		yearSuffix: '년'
	});

	// datepicker 적용
	$('.datepicker').datepicker();

});