<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalculateListController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $dateFrom = $request->filled('date_from')
            ? $request->input('date_from')
            : Carbon::today()->startOfMonth()->format('Y-m-d');

        $quickRange = $request->input('quick_range', 'this_month');

        if ($quickRange === 'last_month') {
            $start = Carbon::today()->subMonth()->startOfMonth();
        } else {
            $start = Carbon::parse($dateFrom)->startOfMonth();
        }

        $dateFrom = $start->copy()->format('Y-m-d');
        $dateTo = $start->copy()->endOfMonth()->format('Y-m-d');

        $baseQuery = DB::table('point_transactions as pt')
            ->leftJoin('orders as o', 'pt.order_id', '=', 'o.id')
            ->where('pt.shop_id', $shop->id)
            ->whereDate('pt.transacted_at', '>=', $dateFrom)
            ->whereDate('pt.transacted_at', '<=', $dateTo)
            ->whereIn('pt.transaction_type', [
                'order_debit',
                'order_credit',
                'refund',
                'adjust_plus',
                'adjust_minus',
            ]);

        $rows = (clone $baseQuery)
            ->select([
                'pt.id',
                'pt.order_id',
                'pt.transaction_no',
                'pt.transaction_type',
                'pt.direction',
                'pt.amount',
                'pt.summary',
                'pt.description',
                'pt.transacted_at',
                'o.order_no',
                'o.delivery_addr1',
                'o.delivery_addr2',
                'o.recipient_name',
                'o.product_name',
            ])
            ->orderByDesc('pt.transacted_at')
            ->orderByDesc('pt.id')
            ->paginate(10)
            ->withQueryString();

        $orderCount = (clone $baseQuery)
            ->where('pt.direction', 'out')
            ->count();

        $orderAmount = (clone $baseQuery)
            ->where('pt.direction', 'out')
            ->sum('pt.amount');

        $receiveCount = (clone $baseQuery)
            ->where('pt.direction', 'in')
            ->count();

        $receiveAmount = (clone $baseQuery)
            ->where('pt.direction', 'in')
            ->sum('pt.amount');

        $netAmount = $receiveAmount - $orderAmount;

        $data = compact(
            'rows',
            'dateFrom',
            'dateTo',
            'orderCount',
            'orderAmount',
            'receiveCount',
            'receiveAmount',
            'netAmount',
            'shop'
        );

        if ($request->ajax()) {
            return view('pages.partials.calculate-list-content', $data);
        }

        return view('pages.calculate-list', $data);
    }
}
