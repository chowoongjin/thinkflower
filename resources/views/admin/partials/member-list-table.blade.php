<section class="row mt20">

    <table class="table --default mt20">
        <caption>리스트</caption>
        <cogroup>
            <col style="width:140px">
            <col style="width:150px">
            <col>
            <col style="width:130px">
            <col style="width:130px">
            <col style="width:130px">
            <col style="width:80px">
            <col style="width:85px">
        </cogroup>
        <thead>
        <tr>
            <th>회원가입 일시</th>
            <th>회원명</th>
            <th>사업장 소재지</th>
            <th>1순위 연락처</th>
            <th>2순위 연락처</th>
            <th>팩스번호</th>
            <th class="align-center">게정상태</th>
            <th class="align-center">수정관리</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($shops as $shop)
            @php
                $formatKoreanPhone = function ($value) {
                    $raw = preg_replace('/\D+/', '', (string) $value);

                    if ($raw === '') {
                        return '-';
                    }

                    if (preg_match('/^02(\d{3,4})(\d{4})$/', $raw, $m)) {
                        return '02-' . $m[1] . '-' . $m[2];
                    }

                    if (preg_match('/^(050\d)(\d{3,4})(\d{4})$/', $raw, $m)) {
                        return $m[1] . '-' . $m[2] . '-' . $m[3];
                    }

                    if (preg_match('/^(1\d{3})(\d{4})$/', $raw, $m)) {
                        return $m[1] . '-' . $m[2];
                    }

                    if (preg_match('/^(\d{3})(\d{3,4})(\d{4})$/', $raw, $m)) {
                        return $m[1] . '-' . $m[2] . '-' . $m[3];
                    }

                    return $value ?: '-';
                };

                $mainPhone = $formatKoreanPhone($shop->main_phone ?? '');
                $subPhone = $formatKoreanPhone($shop->sub_phone ?? '');
                $fax = $formatKoreanPhone($shop->fax ?? '');
            @endphp
            <tr>
                <td>{{ optional($shop->created_at)->format('Y/m/d H:i') }}</td>
                <td><a href="#noe" class="color-gray800 fw600 underline">{{ $shop->shop_name }}</a></td>
                <td>{{ trim(($shop->business_addr1 ?? '') . ' ' . ($shop->business_addr2 ?? '')) ?: '-' }}</td>
                <td>{{ $mainPhone }}</td>
                <td>{{ $subPhone }}</td>
                <td>{{ $fax }}</td>
                <td class="align-center">
                    @if ($shop->status === 'pending')
                        무료체험
                    @elseif ((int) ($shop->is_active ?? 1) === 1 && $shop->status === 'approved')
                        활성화
                    @else
                        비활성화
                    @endif
                </td>
                <td>
                    <button type="button" class="btn color-gray700">정보수정</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="align-center">회원 내역이 없습니다.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <nav class="pagination-wrap">
        {{ $shops->links('vendor.pagination.custom') }}
    </nav>

</section>
