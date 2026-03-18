<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberListController extends Controller
{
    public function index(Request $request)
    {
        $range = (string) $request->input('range', '전체기간');
        $status = (string) $request->input('status', '전체상태');
        $region = (string) $request->input('region', '전체지역');
        $shopName = trim((string) $request->input('shop_name', ''));
        $locationName = trim((string) $request->input('location_name', ''));
        $addressKeyword = trim((string) $request->input('address_keyword', ''));

        $inputDateFrom = (string) $request->input('date_from', '');
        $inputDateTo = (string) $request->input('date_to', '');

        if ($inputDateFrom === '' && $inputDateTo === '') {
            $dateFrom = now()->subDays(15)->format('Y-m-d');
            $dateTo = now()->addDays(15)->format('Y-m-d');
        } else {
            [$dateFrom, $dateTo] = $this->resolveDateRange(
                $range,
                $inputDateFrom,
                $inputDateTo
            );
        }

        $query = Shop::query();

        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($status === '활성화') {
            $query->where('is_active', 1)
                ->where('status', 'approved');
        } elseif ($status === '비활성화') {
            $query->where(function ($q) {
                $q->where('is_active', 0)
                    ->orWhere('status', 'suspended')
                    ->orWhere('status', 'rejected');
            });
        } elseif ($status === '무료체험') {
            $query->where('status', 'pending');
        }

        if ($shopName !== '') {
            $query->where('shop_name', 'like', '%' . $shopName . '%');
        }

        if ($locationName !== '') {
            $query->where(function ($q) use ($locationName) {
                $q->where('business_addr1', 'like', '%' . $locationName . '%')
                    ->orWhere('business_addr2', 'like', '%' . $locationName . '%');
            });
        }

        if ($addressKeyword !== '') {
            $query->where(function ($q) use ($addressKeyword) {
                $q->where('business_addr1', 'like', '%' . $addressKeyword . '%')
                    ->orWhere('business_addr2', 'like', '%' . $addressKeyword . '%');
            });
        }

        if ($region !== '' && $region !== '전체지역') {
            $regionPrefixes = [
                '서울' => ['서울 ', '서울특별시 '],
                '경기' => ['경기 ', '경기도 '],
                '인천' => ['인천 ', '인천광역시 '],
                '대구' => ['대구 ', '대구광역시 '],
                '광주' => ['광주 ', '광주광역시 '],
                '부산' => ['부산 ', '부산광역시 '],
                '경상도' => ['경북 ', '경상북도 ', '경남 ', '경상남도 ', '울산 ', '울산광역시 ', '부산 ', '부산광역시 ', '대구 ', '대구광역시 '],
                '전라도' => ['전북 ', '전라북도 ', '전남 ', '전라남도 ', '광주 ', '광주광역시 '],
                '충청도' => ['충북 ', '충청북도 ', '충남 ', '충청남도 ', '대전 ', '대전광역시 ', '세종 ', '세종특별자치시 '],
            ];

            $prefixes = $regionPrefixes[$region] ?? [];

            if (!empty($prefixes)) {
                $query->where(function ($q) use ($prefixes) {
                    foreach ($prefixes as $prefix) {
                        $q->orWhere('business_addr1', 'like', $prefix . '%');
                    }
                });
            }
        }

        $shops = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        $data = [
            'shops' => $shops,
            'range' => $range,
            'status' => $status,
            'region' => $region,
            'shopName' => $shopName,
            'locationName' => $locationName,
            'addressKeyword' => $addressKeyword,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];

        if ($request->ajax()) {
            return response()->json([
                'table_html' => view('admin.partials.member-list-table', $data)->render(),
            ]);
        }

        return view('admin.member-list', $data);
    }

    protected function resolveDateRange(string $range, string $dateFrom, string $dateTo): array
    {
        if ($range === '전체기간') {
            return [$dateFrom, $dateTo];
        }

        $today = Carbon::today();

        return match ($range) {
            '최근 1개월' => [$today->copy()->subMonth()->format('Y-m-d'), $today->format('Y-m-d')],
            '최근 6개월' => [$today->copy()->subMonths(6)->format('Y-m-d'), $today->format('Y-m-d')],
            '최근 1년' => [$today->copy()->subYear()->format('Y-m-d'), $today->format('Y-m-d')],
            '최근 2년' => [$today->copy()->subYears(2)->format('Y-m-d'), $today->format('Y-m-d')],
            default => [$dateFrom, $dateTo],
        };
    }
}
