<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\PointTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UsageFeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $latestPaidCardPayment = PaymentTransaction::query()
            ->where('shop_id', $shop->id)
            ->where('status', 'paid')
            ->where('payment_method', 'card')
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->first();

        $paymentMethodLabel = '정기결제 카드등록';
        $accountStatusLabel = ((int) ($shop->is_active ?? 1) === 1)
            ? '결제가 확인되어 활성화 상태입니다'
            : '결제 상태를 확인해 주세요';

        $paymentRows = PaymentTransaction::query()
            ->where('shop_id', $shop->id)
            ->where('status', 'paid')
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function ($row) {
                $paidAt = $row->paid_at ? Carbon::parse($row->paid_at) : null;

                return (object) [
                    'type_label' => '시스템',
                    'paid_at_label' => $paidAt ? $paidAt->format('Y/m/d H:i') : '-',
                    'content_label' => $row->title ?: ($row->order_name ?: '결제내역'),
                    'payment_method_label' => $this->resolvePaymentMethodLabel($row->payment_method),
                    'product_label' => $this->resolvePaymentProductLabel($row),
                    'amount_label' => '-' . number_format((int) $row->payment_amount) . '원',
                    'amount_class' => 'color-orange',
                    'sort_at' => $paidAt ? $paidAt->timestamp : 0,
                ];
            });

        $pointRows = PointTransaction::query()
            ->where('shop_id', $shop->id)
            ->whereIn('transaction_type', ['bonus', 'refund', 'adjust_plus', 'adjust_minus'])
            ->orderByDesc('transacted_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function ($row) {
                $transactedAt = $row->transacted_at ? Carbon::parse($row->transacted_at) : null;
                $isIn = $row->direction === 'in';

                return (object) [
                    'type_label' => '시스템',
                    'paid_at_label' => $transactedAt ? $transactedAt->format('Y/m/d H:i') : '-',
                    'content_label' => $row->summary ?: '포인트 변동',
                    'payment_method_label' => '포인트',
                    'product_label' => $this->resolvePointProductLabel($row->transaction_type),
                    'amount_label' => ($isIn ? '+' : '-') . number_format((int) $row->amount) . '원',
                    'amount_class' => $isIn ? 'color-green' : 'color-orange',
                    'sort_at' => $transactedAt ? $transactedAt->timestamp : 0,
                ];
            });

        $historyRows = $paymentRows
            ->concat($pointRows)
            ->sortByDesc('sort_at')
            ->take(20)
            ->values();

        return view('pages.usage-fee', [
            'latestPaidCardPayment' => $latestPaidCardPayment,
            'paymentMethodLabel' => $paymentMethodLabel,
            'accountStatusLabel' => $accountStatusLabel,
            'historyRows' => $historyRows,
        ]);
    }

    protected function resolvePaymentMethodLabel(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'card' => '신용카드',
            'bank_transfer' => '계좌이체',
            'virtual_account' => '가상계좌',
            'manual' => '수기등록',
            default => '기타',
        };
    }

    protected function resolvePaymentProductLabel($row): string
    {
        $title = (string) ($row->title ?? '');
        $orderName = (string) ($row->order_name ?? '');
        $text = trim($title . ' ' . $orderName);

        if (mb_strpos($text, '이용료') !== false) {
            return '이용료';
        }

        return '결제';
    }

    protected function resolvePointProductLabel(string $transactionType): string
    {
        return match ($transactionType) {
            'bonus' => '페이백',
            'refund' => '환불',
            'adjust_plus' => '장려금',
            'adjust_minus' => '차감',
            default => '포인트',
        };
    }
}
