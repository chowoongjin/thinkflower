<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillListController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $range = (string) $request->input('range', 'this_year');
        $targetMonthInput = (string) $request->input('target_month', '');

        if ($targetMonthInput !== '') {
            try {
                $targetMonth = Carbon::createFromFormat('Y-m', $targetMonthInput)->startOfMonth();
            } catch (\Throwable $e) {
                $targetMonth = now()->startOfMonth();
            }
        } else {
            $targetMonth = now()->startOfMonth();
        }

        if ($range === 'last_year') {
            $yearStart = $targetMonth->copy()->subYear()->startOfYear();
            $yearEnd = $targetMonth->copy()->subYear()->endOfYear();
            $selectedYear = $targetMonth->copy()->subYear()->year;
        } else {
            $yearStart = $targetMonth->copy()->startOfYear();
            $yearEnd = $targetMonth->copy()->endOfYear();
            $selectedYear = $targetMonth->year;
        }

        $statements = DB::table('billing_statements')
            ->where('shop_id', $shop->id)
            ->whereBetween('period_start', [$yearStart->toDateString(), $yearEnd->toDateString()])
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $statements->getCollection()->transform(function ($row) {
            $issuedAt = $row->issued_at ? Carbon::parse($row->issued_at) : null;

            $row->type_label = '매입계산서';
            $row->type_class = 'color-active';
            $row->issued_date_label = $issuedAt ? $issuedAt->format('Y/m/d') : '-';

            $periodStart = $row->period_start ? Carbon::parse($row->period_start) : null;
            $row->item_label = $periodStart
                ? $periodStart->format('Y년 m월') . ' 꽃배달 거래대금'
                : '꽃배달 거래대금';

            $row->vendor_name = '주식회사 싱크플로';
            $row->vendor_business_no = '680-87-02988';

            return $row;
        });

        return view('pages.bill-list', [
            'statements' => $statements,
            'range' => $range,
            'targetMonth' => $targetMonth->format('Y-m'),
            'selectedYear' => $selectedYear,
        ]);
    }
}
