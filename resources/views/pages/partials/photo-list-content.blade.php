<div id="content__body" style="position:relative">

    <div id="photoGallery">

        <section>
            <h2 class="tt2">✔️ 정직한플라워 사진공유방</h2>
        </section>

        <section class="mt30">
            <form method="GET" action="{{ route('photo-list') }}" id="photo-filter-form">
                <table class="table-data style5">
                    <colgroup>
                        <col style="width:100px;min-width:100px">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="fs13 color-gray800">상품별 조회</th>
                        <td>
                            <div class="input-group-radio">
                                <input type="radio" name="product_name" value="" id="test2-1" {{ $productName === '' ? 'checked' : '' }}>
                                <label for="test2-1">전체상품</label>

                                <input type="radio" name="product_name" value="근조화환" id="test2-2" {{ $productName === '근조화환' ? 'checked' : '' }}>
                                <label for="test2-2">근조화환</label>

                                <input type="radio" name="product_name" value="축하화환" id="test2-3" {{ $productName === '축하화환' ? 'checked' : '' }}>
                                <label for="test2-3">축하화환</label>

                                <input type="radio" name="product_name" value="꽃바구니" id="test2-4" {{ $productName === '꽃바구니' ? 'checked' : '' }}>
                                <label for="test2-4">꽃바구니</label>

                                <input type="radio" name="product_name" value="꽃다발" id="test2-5" {{ $productName === '꽃다발' ? 'checked' : '' }}>
                                <label for="test2-5">꽃다발</label>

                                <input type="radio" name="product_name" value="관엽식물" id="test2-6" {{ $productName === '관엽식물' ? 'checked' : '' }}>
                                <label for="test2-6">관엽식물</label>

                                <input type="radio" name="product_name" value="동양란" id="test2-7" {{ $productName === '동양란' ? 'checked' : '' }}>
                                <label for="test2-7">동양란</label>

                                <input type="radio" name="product_name" value="서양란" id="test2-8" {{ $productName === '서양란' ? 'checked' : '' }}>
                                <label for="test2-8">서양란</label>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </section>

        <section class="mt20">
            @if ($photos->count())
                <ul class="list-column-6">
                    @foreach ($photos as $photo)
                        <li>
                            <div class="item">
                                <div class="item__head">
                                    <a href="#none">
                                        <img src="{{ $photo->file_path }}">
                                    </a>
                                </div>
                                <div class="item__body">
                                    <div class="item-info">
                                        <p class="item-info__type" title="{{ $photo->product_name ?? '-' }}">
                                            상품유형: {{ $photo->product_name ?? '-' }}
                                        </p>
                                        <p class="item-info__date">
                                            작성날짜: {{ $photo->created_at ? \Carbon\Carbon::parse($photo->created_at)->format('Y-m-d') : '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="mt20">
                    <table class="table style2">
                        <tbody>
                        <tr>
                            <td class="align-center">등록된 사진이 없습니다.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($photos->hasPages())
                {{ $photos->links('vendor.pagination.custom') }}
            @endif
        </section>

    </div>

</div>
