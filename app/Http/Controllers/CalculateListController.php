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
        $settledAtExpression = 'COALESCE(pt.transacted_at, pt.created_at)';

        $baseQuery = DB::table('point_transactions as pt')
            ->leftJoin('orders as o', 'pt.order_id', '=', 'o.id')
            ->where('pt.shop_id', $shop->id)
            ->whereDate(DB::raw($settledAtExpression), '>=', $dateFrom)
            ->whereDate(DB::raw($settledAtExpression), '<=', $dateTo)
            ->whereIn('pt.transaction_type', [
                'order_debit',
                'order_credit',
                'order_cancel_refund',
                'order_credit_cancel',
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
                'pt.created_at',
                'o.order_no',
                'o.delivery_addr1',
                'o.delivery_addr2',
                'o.recipient_name',
                'o.product_name',
            ])
            ->selectRaw($settledAtExpression . ' as settled_at')
            ->orderByRaw($settledAtExpression . ' desc')
            ->orderByDesc('pt.id')
            ->paginate(10)
            ->withQueryString();

        $rows->getCollection()->transform(function ($row) {
            $settledAt = $row->settled_at
                ? Carbon::parse($row->settled_at)
                : null;
            $isIncoming = $row->direction === 'in';
            $addressText = trim(($row->delivery_addr1 ?? '') . ' ' . ($row->delivery_addr2 ?? ''));
            $amount = (int) $row->amount;

            $row->display_type_label = $this->resolveSettlementTypeLabel($row->transaction_type);
            $row->display_type_class = $this->resolveSettlementTypeClass($row->transaction_type, $row->direction);
            $row->display_amount_label = $amount === 0
                ? '0원'
                : (($isIncoming ? '+' : '-') . number_format($amount) . '원');
            $row->display_amount_class = $isIncoming ? 'color-green' : 'color-orange';
            $row->display_settled_at = $settledAt ? $settledAt->format('Y/m/d H:i') : '-';
            $row->display_delivery_text = $addressText !== ''
                ? $addressText
                : ($row->description ?: $row->summary ?: '-');
            $row->display_recipient_text = $row->recipient_name
                ?: ($row->order_no ? '주문번호 ' . $row->order_no : '-');
            $row->display_product_text = $row->product_name
                ?: $this->resolveSettlementProductLabel($row->transaction_type, $row->summary);

            return $row;
        });

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

    protected function resolveSettlementTypeLabel(string $transactionType): string
    {
        return match ($transactionType) {
            'order_debit' => '발주',
            'order_cancel_refund' => '발주취소',
            'order_credit' => '수주',
            'order_credit_cancel' => '수주취소',
            'refund' => '환불',
            'adjust_plus', 'adjust_minus' => '정산',
            default => '포인트',
        };
    }

    protected function resolveSettlementTypeClass(string $transactionType, string $direction): string
    {
        return match ($transactionType) {
            'order_debit', 'order_cancel_refund' => 'color-orange',
            'order_credit', 'order_credit_cancel' => 'color-blue',
            default => $direction === 'in' ? 'color-green' : 'color-orange',
        };
    }

    protected function resolveSettlementProductLabel(string $transactionType, ?string $summary): string
    {
        if ($summary) {
            return $summary;
        }

        return match ($transactionType) {
            'order_debit' => '발주 포인트 차감',
            'order_cancel_refund' => '주문취소 포인트 환불',
            'order_credit' => '배송완료 포인트 적립',
            'order_credit_cancel' => '주문취소 포인트 회수',
            'refund' => '환불',
            'adjust_plus' => '장려금',
            'adjust_minus' => '차감',
            default => '포인트',
        };
    }
}
