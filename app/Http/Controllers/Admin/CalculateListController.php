<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalculateListController extends Controller
{
    public function index(Request $request)
    {
        $range = (string) $request->input('range', '이번 달');
        $itemType = (string) $request->input('item_type', '전체항목');
        $targetMonthInput = (string) $request->input('target_month', '');

        if ($targetMonthInput !== '') {
            try {
                $targetMonth = Carbon::parse($targetMonthInput)->startOfMonth();
            } catch (\Throwable $e) {
                $targetMonth = now()->startOfMonth();
            }
        } else {
            $targetMonth = $range === '지난 달'
                ? now()->subMonthNoOverflow()->startOfMonth()
                : now()->startOfMonth();
        }

        $monthStart = $targetMonth->copy()->startOfMonth();
        $monthEnd = $targetMonth->copy()->endOfMonth();

        $baseQuery = DB::table('point_transactions')
            ->join('shops', 'shops.id', '=', 'point_transactions.shop_id')
            ->whereBetween('point_transactions.transacted_at', [$monthStart, $monthEnd])
            ->whereIn('point_transactions.transaction_type', ['order_debit', 'order_credit'])
            ->selectRaw('
                shops.id,
                shops.shop_name,
                shops.business_addr1,
                shops.business_addr2,
                SUM(CASE WHEN point_transactions.direction = "out" THEN point_transactions.amount ELSE 0 END) as order_amount_sum,
                SUM(CASE WHEN point_transactions.direction = "in" THEN point_transactions.amount ELSE 0 END) as receive_amount_sum,
                (
                    SUM(CASE WHEN point_transactions.direction = "in" THEN point_transactions.amount ELSE 0 END)
                    - SUM(CASE WHEN point_transactions.direction = "out" THEN point_transactions.amount ELSE 0 END)
                ) as net_amount
            ')
            ->groupBy('shops.id', 'shops.shop_name', 'shops.business_addr1', 'shops.business_addr2');

        if ($itemType === '입금 받을 금액') {
            $baseQuery->havingRaw('net_amount < 0');
        } elseif ($itemType === '입금 하는 금액') {
            $baseQuery->havingRaw('net_amount > 0');
        }

        $summaryQuery = clone $baseQuery;

        $settlements = (clone $baseQuery)
            ->orderByRaw('ABS(
                SUM(CASE WHEN point_transactions.direction = "in" THEN point_transactions.amount ELSE 0 END)
                - SUM(CASE WHEN point_transactions.direction = "out" THEN point_transactions.amount ELSE 0 END)
            ) DESC')
            ->orderBy('shops.shop_name')
            ->paginate(10)
            ->appends($request->query());

        $settlements->getCollection()->transform(function ($row) use ($targetMonth) {
            $net = (int) $row->net_amount;

            $row->month_label = $targetMonth->format('Y년 m월');
            $row->display_amount = abs($net);

            if ($net >= 0) {
                $row->settlement_type = '입금하기';
                $row->settlement_type_class = 'color-orange';
                $row->amount_class = 'color-orange';
            } else {
                $row->settlement_type = '입금받기';
                $row->settlement_type_class = 'color-green';
                $row->amount_class = 'color-green';
            }

            $row->memo_text = trim(($row->business_addr1 ?? '') . ' ' . ($row->business_addr2 ?? '')) ?: '-';
            $row->process_status_text = '처리완료';

            return $row;
        });

        $totalPayAmount = DB::query()
            ->fromSub($summaryQuery, 'settlement_rows')
            ->selectRaw('COALESCE(SUM(CASE WHEN net_amount > 0 THEN net_amount ELSE 0 END), 0) as total_amount')
            ->value('total_amount');

        $totalReceiveAmount = DB::query()
            ->fromSub($summaryQuery, 'settlement_rows')
            ->selectRaw('COALESCE(SUM(CASE WHEN net_amount < 0 THEN ABS(net_amount) ELSE 0 END), 0) as total_amount')
            ->value('total_amount');

        $totalPayAmount = (int) $totalPayAmount;
        $totalReceiveAmount = (int) $totalReceiveAmount;
        $totalGapAmount = $totalReceiveAmount - $totalPayAmount;

        return view('admin.calculate-list', [
            'settlements' => $settlements,
            'range' => $range,
            'itemType' => $itemType,
            'targetMonth' => $targetMonth->format('Y-m-d'),
            'totalPayAmount' => $totalPayAmount,
            'totalReceiveAmount' => $totalReceiveAmount,
            'totalGapAmount' => $totalGapAmount,
        ]);
    }
}
