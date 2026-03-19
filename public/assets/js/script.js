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

// -------------------------------
//	photobox attachment
// -------------------------------
$(document).on('change', '.photoBox__content input[type="file"]', function () {

    const file = this.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('이미지 파일만 업로드 가능합니다.');
        $(this).val('');
        return;
    }

    const reader = new FileReader();
    const $content = $(this).closest('.photoBox__content');

    reader.onload = function (e) {

        // 기존 이미지 제거
        $content.find('img').remove();

        // 이미지 생성
        const $img = $('<img>').attr('src', e.target.result);

        $content.append($img);

        // 라벨 숨김
        $content.find('label').hide();
    };

    reader.readAsDataURL(file);
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

$(document).on("click",".modal-close",function(){
    $("#modal,body").removeClass("active");
});

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

// ----------------------------------------------
// Custom Selectbox
// ----------------------------------------------
$(function () {

    $('.custom-select').each(function () {
        const $select = $(this);
        const options = [];

        $select.find('option').each(function () {
            options.push({
                value: $(this).val(),
                text: $(this).text()
            });
        });

        const $wrap = $('<div class="select-wrap"></div>');
        const $btn  = $('<button type="button" class="select-btn"></button>');
        const $list = $('<ul class="select-list"></ul>');

        $btn.text(options[0].text);

        options.forEach((opt, i) => {
            if (i === 0) return; // placeholder 제외
            $list.append(`<li data-value="${opt.value}">${opt.text}</li>`);
        });

        $wrap.append($btn).append($list);
        $select.after($wrap).hide();
    });

    // 열기 / 닫기
    $(document).on('click', '.select-btn', function (e) {
        e.stopPropagation();
        $('.select-wrap').not($(this).parent()).removeClass('open');
        $(this).parent().toggleClass('open');
    });

    // 선택
    $(document).on('click', '.select-list li', function () {
        const $wrap   = $(this).closest('.select-wrap');
        const value   = $(this).data('value');
        const text    = $(this).text();
        const $select = $wrap.prev('select');

        $wrap.find('.select-btn').text(text);
        $select.val(value).trigger('change');
        $wrap.removeClass('open');
    });

    // 바깥 클릭 시 닫기
    $(document).on('click', function () {
        $('.select-wrap').removeClass('open');
    });

    // 26.03.19 패널 토글 추가
    $(".panel .panel-toggle").click(function(){

        let panel = $(this).closest(".panel");
        panel.toggleClass("active");

        let img = $(this).find("img");

        if(panel.hasClass("active")){
            img.attr("src","./assets/img/arrow_up_new.png");
        }else{
            img.attr("src","./assets/img/arrow_down_new.png");
        }

    });

});
