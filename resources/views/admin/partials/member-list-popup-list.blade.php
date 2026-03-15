<div class="row" id="memberSearchResult">
    <ul>
        @forelse ($shops as $shop)
            @php
                $openTime = '09:00';
                $closeTime = '18:30';
                $nowTime = now()->format('H:i');
                $isAvailable = ($nowTime >= $openTime && $nowTime <= $closeTime);

                $statusText = $isAvailable ? '영업중' : '배송불가';
                $statusClass = $isAvailable ? 'color-green' : 'color-orange';

                $regionText = $shop->delivery_region ?? '-';

                $rawPhone = preg_replace('/\D+/', '', (string) ($shop->main_phone ?? ''));
                if (preg_match('/^02(\d{3,4})(\d{4})$/', $rawPhone, $m)) {
                    $phone = '02-' . $m[1] . '-' . $m[2];
                } elseif (preg_match('/^(\d{3})(\d{3,4})(\d{4})$/', $rawPhone, $m)) {
                    $phone = $m[1] . '-' . $m[2] . '-' . $m[3];
                } else {
                    $phone = $shop->main_phone ?? '-';
                }

                $memo = $shop->memo ?? '여기에 화원사 메모가 들어갑니다';
                $shopDisplayName = $shop->shop_name . ($regionText !== '-' ? ' (' . $regionText . ')' : '');

                $pointBalance = number_format((int) ($shop->current_point_balance ?? 0)) . 'P';
                $pointPolicyLabel = ($shop->point_policy_type ?? 'prepaid') === 'postpaid' ? '후불' : '선불';
            @endphp

            <li>
                <div class="member-info">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <h3 class="shopName" style="margin:0;">
                            <input
                                type="radio"
                                name="member"
                                value="{{ $shop->id }}"
                                class="member-radio"
                                data-target="{{ $target }}"
                                data-shop-id="{{ $shop->id }}"
                                data-shop-name="{{ $shop->shop_name }}"
                                data-shop-display-name="{{ $shopDisplayName }}"
                            >
                            {{ $shop->shop_name }}
                        </h3>
                        <span>
                            <em>|</em>
                            보유포인트 {{ $pointBalance }}
                            <em>|</em>
                            {{ $pointPolicyLabel }}
                        </span>
                        <span class="time {{ $statusClass }}">{{ $statusText }}</span>
                    </div>
                    <div>
                        <i class="bi bi-alarm-fill"></i> {{ $openTime }} ~ {{ $closeTime }}
                        <em>|</em>
                        <i class="bi bi-geo-alt-fill"></i> {{ $regionText }}
                        <em>|</em>
                        <i class="bi bi-telephone-fill"></i> {{ $phone }}
                    </div>

                    <div class="memo">{{ $memo }}</div>
                </div>
            </li>
        @empty
            <li>
                <div class="member-info disabled">
                    <div>
                        <h3 class="shopName">검색 결과가 없습니다</h3>
                    </div>
                    <div><i class="bi bi-geo-alt-fill"></i> 조건에 맞는 화원사가 없습니다</div>
                    <div class="memo">검색어를 다시 확인해 주세요</div>
                </div>
            </li>
        @endforelse
    </ul>
</div>

<div class="mt20">
    {{ $shops->links('vendor.pagination.custom') }}
</div>
