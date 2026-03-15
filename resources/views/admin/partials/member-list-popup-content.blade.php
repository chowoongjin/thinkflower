<form id="member-search-form" method="GET" action="{{ route('admin.member-list-popup') }}">
    <input type="hidden" name="target" value="{{ $target }}">
    <input type="hidden" name="product_filter" id="product_filter" value="{{ $productFilter ?? '전체' }}">

    <div class="row">
        <h2 class="fw600">상품필터</h2>
        <nav class="nav-tab style2 mt10">
            <ul>
                <li class="{{ ($productFilter ?? '전체') === '전체' ? 'active' : '' }}">
                    <a href="#none" class="btn-product-filter" data-value="전체">전체</a>
                </li>
                <li class="{{ ($productFilter ?? '') === '근조' ? 'active' : '' }}">
                    <a href="#none" class="btn-product-filter" data-value="근조">근조</a>
                </li>
                <li class="{{ ($productFilter ?? '') === '축하' ? 'active' : '' }}">
                    <a href="#none" class="btn-product-filter" data-value="축하">축하</a>
                </li>
                <li class="{{ ($productFilter ?? '') === '오브제' ? 'active' : '' }}">
                    <a href="#none" class="btn-product-filter" data-value="오브제">오브제</a>
                </li>
                <li class="{{ ($productFilter ?? '') === '쌀화환' ? 'active' : '' }}">
                    <a href="#none" class="btn-product-filter" data-value="쌀화환">쌀화환</a>
                </li>
                <li class="{{ ($productFilter ?? '') === '관엽' ? 'active' : '' }}">
                    <a href="#none" class="btn-product-filter" data-value="관엽">관엽</a>
                </li>
                <li class="{{ ($productFilter ?? '') === '동양란' ? 'active' : '' }}">
                    <a href="#none" class="btn-product-filter" data-value="동양란">동양란</a>
                </li>
                <li class="{{ ($productFilter ?? '') === '서양란' ? 'active' : '' }}">
                    <a href="#none" class="btn-product-filter" data-value="서양란">서양란</a>
                </li>
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
                        @foreach ($sidoOptions as $item)
                            <option value="{{ $item }}" {{ ($sido ?? '') === $item ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                    </select>

                    <select name="sigungu" id="filter_sigungu">
                        <option value="">구/군 선택</option>
                        @foreach ($sigunguOptions as $item)
                            <option value="{{ $item }}" {{ ($sigungu ?? '') === $item ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
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
