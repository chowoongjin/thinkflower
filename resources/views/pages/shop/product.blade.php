@extends('layouts.shop', ['title' => '영업용 쇼핑몰 상품'])

@push('styles')
    <style>
        body { background-color: #F6F6F6; }
        #body { padding: 0; }
        #main-nav { display: none; }
    </style>
@endpush

@push('scripts')
    <script>
        function checkForm() {
            var pickup = $("input[name='pickup']:checked").length;
            var size = $("input[name='size']:checked").length;
            var date = $("input[name='pickup_date']").val().trim();
            var name = $("input[name='reservation_name']").val().trim();
            var phone = $("input[name='reservation_phone']").val().trim();

            if (pickup && size && date && name && phone) {
                $(".btn")
                    .prop("disabled", false)
                    .addClass("btn-green")
                    .text("예약하기");
            } else {
                $(".btn")
                    .prop("disabled", true)
                    .removeClass("btn-green")
                    .text("모든 내용을 입력해 주십시오");
            }
        }

        $(document).on("change keyup", "input", function () {
            checkForm();
        });

        $(document).on("change keyup", "input[name='pickup_date']", function () {
            var val = $(this).val().trim();
            var span = $(this).siblings("span");

            if (val) {
                span.show();
            } else {
                span.hide();
            }
        });

        $(document).on("click", "input[name='pickup_date'] + span", function () {
            $(this).siblings("input[name='pickup_date']").focus().trigger("click");
        });

        $(document).on("click", "#btn-shop-reserve", function () {
            if ($(this).prop("disabled")) {
                return;
            }

            modal($(this).data("modal-url"));
        });
    </script>
@endpush

@section('content')
    @include('pages.shop.partials.sub-header', ['active' => 'category'])

    <div id="shopSub">
        <div id="shopSub__header">
            <img src="{{ asset('assets/img/shop_sample2.png') }}" alt="">
            <div class="itemName">
                <h2>핑크코랄 거베라 꽃다발</h2>
                <p>핑크색과 코랄색의 조화로 20대 여성에게 선물하기 좋아요 :)</p>
            </div>
        </div>
        <div id="shopSub__body">
            <section class="section">
                <h3 class="section-title">상품 수령방법</h3>
                <div class="flex">
                    <div class="flex__col">
                        <input type="radio" name="pickup" value="방문픽업" id="pickup1"><label for="pickup1">방문픽업</label>
                    </div>
                    <div class="flex__col">
                        <i class="bi bi-geo-alt-fill color-green pr5"></i> 달서구 월성동
                    </div>
                </div>
                <div class="flex">
                    <div class="flex__col">
                        <input type="radio" name="pickup" value="퀵서비스" id="pickup2"><label for="pickup2">퀵서비스</label>
                    </div>
                    <div class="flex__col">
                        <i class="bi bi-box-fill color-green pr5"></i> 거리에 따라 차등부과
                    </div>
                </div>
            </section>

            <section class="section">
                <h3 class="section-title">사이즈 선택</h3>
                <div class="flex">
                    <div class="flex__col">
                        <input type="radio" name="size" value="미니사이즈 (2~4송이)" id="size1">
                        <label for="size1">미니사이즈 (2~4송이)</label>
                    </div>
                    <div class="flex__col">
                        <strong>29,000원</strong>
                    </div>
                </div>
                <div class="flex">
                    <div class="flex__col">
                        <input type="radio" name="size" value="소형사이즈 (4~8송이)" id="size2">
                        <label for="size2">소형사이즈 (4~8송이)</label>
                    </div>
                    <div class="flex__col">
                        <strong>49,000원</strong>
                    </div>
                </div>
                <div class="flex">
                    <div class="flex__col">
                        <input type="radio" name="size" value="중형사이즈 (10~15송이)" id="size3">
                        <label for="size3">중형사이즈 (10~15송이)</label>
                    </div>
                    <div class="flex__col">
                        <strong>79,000원</strong>
                    </div>
                </div>
                <div class="flex">
                    <div class="flex__col">
                        <input type="radio" name="size" value="대형사이즈 (20송이 이상)" id="size4">
                        <label for="size4">대형사이즈 (20송이 이상)</label>
                    </div>
                    <div class="flex__col">
                        <strong>109,000원</strong>
                    </div>
                </div>
            </section>

            <section class="section">
                <h3 class="section-title">상품 픽업일시<em>*</em></h3>

                <div style="position:relative;">
                    <input type="text" name="pickup_date" class="datepicker" placeholder="원하시는 일시를 선택해 주세요">

                    <span style="position:absolute;top:50%;transform:translateY(-50%);right:1rem;z-index:1;display:none;">변경</span>
                </div>
            </section>

            <section class="section">
                <h3 class="section-title">예약자 정보<em>*</em></h3>
                <input type="text" name="reservation_name" placeholder="예약자의 성함을 입력해 주세요">
                <input type="text" name="reservation_phone" placeholder="예약자의 연락처를 입력해 주세요">
            </section>

            <section class="section">
                <button
                    type="button"
                    id="btn-shop-reserve"
                    class="btn btn-fluid"
                    data-modal-url="{{ route('shop-site.modals.actions') }}"
                    disabled
                >
                    모든 내용을 입력해 주십시오
                </button>
            </section>
        </div>
    </div>

    @include('pages.shop.partials.footer')
@endsection
