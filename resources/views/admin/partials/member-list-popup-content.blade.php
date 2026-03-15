<form id="member-search-form" method="GET" action="{{ route('admin.member-list-popup') }}">
    <input type="hidden" name="target" value="{{ $target }}">
    <input type="hidden" name="product_filter" id="product_filter" value="{{ $productFilter ?? '전체' }}">

    <div class="row">
        <h2 class="fw600">상품필터</h2>
        <nav class="nav-tab style2 mt10">
            <ul>
                @php
                    $productTabs = ['전체', '근조', '축하', '오브제', '쌀화환', '관엽', '동양란', '서양란'];
                    $activeProduct = $productFilter ?: '전체';
                @endphp

                @foreach ($productTabs as $tab)
                    <li class="{{ $activeProduct === $tab ? 'active' : '' }}">
                        <a href="#none" class="btn-product-filter" data-value="{{ $tab }}">{{ $tab }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>

    <div class="row mt20">
        <div class="grid">
            <div class="grid__7">
                <h2 class="fw600">지역필터</h2>
                <div class="select-group mt10">
                    <select name="sido" id="filter_sido">
                        <option value="">시/도 선택</option>
                        @foreach ($sidoOptions as $option)
                            <option value="{{ $option }}" {{ ($sido ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>

                    <select name="sigungu" id="filter_sigungu">
                        <option value="">구/군 선택</option>
                        @foreach ($sigunguOptions as $option)
                            <option value="{{ $option }}" {{ ($sigungu ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid__5">
                <h2 class="fw600">화원명 직접 검색</h2>
                <div class="mt10">
                    <div class="input-group">
                        <span><i class="bi bi-search"></i></span>
                        <input type="text" name="keyword" value="{{ $keyword ?? '' }}" placeholder="화원사명을 입력하세요">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.member-list-popup-list')
</form>
