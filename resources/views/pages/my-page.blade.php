@extends('layouts.app')

@section('content')
    @php
        $formatPhone = function ($phone) {
            $digits = preg_replace('/\D+/', '', (string) $phone);

            if ($digits === '') {
                return '-';
            }

            if (str_starts_with($digits, '02')) {
                if (strlen($digits) === 9) {
                    return preg_replace('/(\d{2})(\d{3})(\d{4})/', '$1-$2-$3', $digits);
                }
                if (strlen($digits) === 10) {
                    return preg_replace('/(\d{2})(\d{4})(\d{4})/', '$1-$2-$3', $digits);
                }
            }

            if (strlen($digits) === 8) {
                return preg_replace('/(\d{4})(\d{4})/', '$1-$2', $digits);
            }

            if (strlen($digits) === 10) {
                return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $digits);
            }

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{3})(\d{4})(\d{4})/', '$1-$2-$3', $digits);
            }

            return $phone;
        };

        $businessLicenseUploaded = !empty($shop->business_license_file_path) || !empty($shop->business_license_file_url);
    @endphp

    <div id="content__body" style="position:relative">
        <div id="photoGallery">
            <section>
                <h2 class="tt2">✔️ 마이페이지 조회/수정</h2>
            </section>

            <section class="mt30">
                <div class="flex">
                    <div class="flex__col">
                        <h3 class="tt3">사업자 정보</h3>
                    </div>
                    <div class="flex__col">
                        <button type="button"
                                class="btn btn-gray2 btn-small"
                                onclick="modal('{{ route('my-page.business-info-modal') }}');">
                            사업자정보 수정
                        </button>
                    </div>
                </div>
                <table class="table-data style2-1 mt10">
                    <colgroup>
                        <col style="width:90px;min-width:90px">
                        <col>
                        <col style="width:90px;min-width:90px">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>사업자번호</th>
                        <td>{{ $shop->business_no ?: '-' }}</td>
                        <th>대표자명</th>
                        <td>{{ $shop->owner_name ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>사업자소재지</th>
                        <td colspan="3">{{ $businessAddress }}</td>
                    </tr>
                    <tr>
                        <th>계산서수령</th>
                        <td colspan="3">{{ $shop->tax_email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>사업자등록증</th>
                        <td colspan="3"><i class="bi bi-check-circle-fill color-green"></i> 사업자등록증이 정상적으로 등록되어 있습니다.</td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <section class="mt30">
                <div class="flex">
                    <div class="flex__col">
                        <h3 class="tt3">매장운영 정보</h3>
                    </div>
                    <div class="flex__col">
                        <button type="button" class="btn btn-gray2 btn-small" onclick="modal('');">매장운영정보 수정</button>
                    </div>
                </div>
                <table class="table-data style2-1 mt10">
                    <colgroup>
                        <col style="width:90px;min-width:90px">
                        <col>
                        <col style="width:90px;min-width:90px">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>대표 연락망</th>
                        <td>{{ $formatPhone($shop->main_phone) }}</td>
                        <th>비상 연락망</th>
                        <td>{{ $formatPhone($shop->sub_phone) }}</td>
                    </tr>
                    <tr>
                        <th>팩스번호</th>
                        <td>{{ $formatPhone($shop->fax) }}</td>
                        <th>화원사명</th>
                        <td>{{ $shop->shop_name ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>수주취급상품</th>
                        <td colspan="3">
                            @if($handledProductsText)
                                <i class="bi bi-check-circle-fill color-green"></i> 수주상품이 정상적으로 선택되어 있습니다.
                            @else
                                수주상품이 선택되어 있지 않습니다.
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>배송가능지역</th>
                        <td colspan="3">
                            @if($deliveryRegionsText)
                                <i class="bi bi-check-circle-fill color-green"></i> 배송지역이 정상적으로 선택되어 있습니다.
                            @else
                                배송지역이 선택되어 있지 않습니다.
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <section class="mt30">
                <div class="flex">
                    <div class="flex__col">
                        <h3 class="tt3">정산 정보</h3>
                    </div>
                    <div class="flex__col">
                        <button type="button" class="btn btn-gray2 btn-small" onclick="modal('');">정산 정보 수정</button>
                    </div>
                </div>
                <table class="table-data style2-1 mt10">
                    <colgroup>
                        <col style="width:90px;min-width:90px">
                        <col>
                        <col style="width:90px;min-width:90px">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>입금은행</th>
                        <td>{{ $shop->bank_name ?: '-' }}</td>
                        <th>예금주명</th>
                        <td>{{ $shop->bank_holder ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>계좌번호</th>
                        <td colspan="3">{{ $shop->bank_account ?: '-' }}</td>
                    </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
    <div id="modal">
        <div id="ajax-modal"></div>
    </div>
@endsection
