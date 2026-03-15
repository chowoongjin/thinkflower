<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PointService
{
    public function debitForOrder(
        Shop $shop,
        User $user,
        Order $order,
        int $amount,
        string $summary = '발주 포인트 차감'
    ): PointTransaction {
        $beforePoint = (int) $shop->current_point_balance;
        $pointPolicyType = $shop->point_policy_type ?? 'prepaid';
        $creditLimit = (int) ($shop->credit_limit ?? 0);
        $afterPoint = $beforePoint - $amount;

        if ($pointPolicyType === 'prepaid') {
            if ($beforePoint < $amount) {
                throw ValidationException::withMessages([
                    'point' => '보유 포인트가 부족하여 발주할 수 없습니다.',
                ]);
            }
        } else {
            if ($creditLimit > 0 && $afterPoint < (-1 * $creditLimit)) {
                throw ValidationException::withMessages([
                    'point' => '후불 한도를 초과하여 발주할 수 없습니다.',
                ]);
            }
        }

        $transaction = PointTransaction::create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'payment_transaction_id' => null,
            'transaction_no' => $this->generatePointTransactionNo(),
            'transaction_type' => 'order_debit',
            'direction' => 'out',
            'amount' => $amount,
            'balance_before' => $beforePoint,
            'balance_after' => $afterPoint,
            'summary' => $summary,
            'description' => '주문번호 ' . $order->order_no . ' 발주 포인트 차감',
            'transacted_at' => now(),
        ]);

        $shop->update([
            'current_point_balance' => $afterPoint,
        ]);

        return $transaction;
    }

    public function creditForDelivery(
        Shop $shop,
        User $user,
        Order $order,
        int $amount,
        string $summary = '배송완료 포인트 적립'
    ): PointTransaction {
        $beforePoint = (int) $shop->current_point_balance;
        $afterPoint = $beforePoint + $amount;

        $transaction = PointTransaction::create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'payment_transaction_id' => null,
            'transaction_no' => $this->generatePointTransactionNo(),
            'transaction_type' => 'order_credit',
            'direction' => 'in',
            'amount' => $amount,
            'balance_before' => $beforePoint,
            'balance_after' => $afterPoint,
            'summary' => $summary,
            'description' => '주문번호 ' . $order->order_no . ' 배송완료 포인트 적립',
            'transacted_at' => now(),
        ]);

        $shop->update([
            'current_point_balance' => $afterPoint,
        ]);

        return $transaction;
    }

    protected function generatePointTransactionNo(): string
    {
        do {
            $transactionNo = 'PT' . now()->format('YmdHis') . strtoupper(Str::random(6));
        } while (PointTransaction::where('transaction_no', $transactionNo)->exists());

        return $transactionNo;
    }
}
