<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointChargeController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $validated = $request->validate([
            'charge_amount' => ['required', 'string'],
            'charge_method' => ['required', 'in:bank_transfer,card'],
        ], [
            'charge_amount.required' => '충전 금액을 입력해 주세요.',
            'charge_method.required' => '충전 방식을 선택해 주세요.',
        ]);

        $amount = (int) preg_replace('/[^0-9]/', '', $validated['charge_amount']);

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => '충전 금액을 확인해 주세요.',
            ], 422);
        }

        DB::transaction(function () use ($shop, $user, $amount, $validated) {
            $lockedShop = DB::table('shops')
                ->where('id', $shop->id)
                ->lockForUpdate()
                ->first();

            $beforeBalance = (int) $lockedShop->current_point_balance;
            $afterBalance = $beforeBalance + $amount;

            $paymentNo = $this->generatePaymentNo();
            $paymentMethod = $validated['charge_method']; // card | bank_transfer
            $title = $paymentMethod === 'card' ? '포인트 간편충전(카드)' : '포인트 간편충전(무통장입금)';

            $paymentTransactionId = DB::table('payment_transactions')->insertGetId([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'payment_no' => $paymentNo,
                'pg_provider' => 'tosspayments',
                'pg_transaction_id' => null,
                'payment_method' => $paymentMethod,
                'payment_amount' => $amount,
                'charged_point_amount' => $amount,
                'bonus_point_amount' => 0,
                'status' => 'paid',
                'paid_at' => now(),
                'cancelled_at' => null,
                'title' => $title,
                'note' => '포인트 간편충전 테스트',
                'created_at' => now(),
                'updated_at' => now(),
                'provider' => 'tosspayments',
                'payment_key' => null,
                'order_id' => 'TEST_' . $paymentNo,
                'order_name' => $title,
                'method' => $paymentMethod,
                'total_amount' => $amount,
                'balance_amount' => $amount,
                'supplied_amount' => $amount,
                'vat' => 0,
                'requested_at' => now(),
                'approved_at' => now(),
                'raw_response' => json_encode([
                    'test' => true,
                    'message' => '포인트 간편충전 테스트 결제',
                    'charge_method' => $paymentMethod,
                    'amount' => $amount,
                ], JSON_UNESCAPED_UNICODE),
            ]);

            DB::table('shops')
                ->where('id', $shop->id)
                ->update([
                    'current_point_balance' => $afterBalance,
                    'updated_at' => now(),
                ]);

            DB::table('point_transactions')->insert([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'order_id' => null,
                'payment_transaction_id' => $paymentTransactionId,
                'transaction_no' => $this->generatePointTransactionNo(),
                'transaction_type' => 'charge',
                'direction' => 'in',
                'amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $afterBalance,
                'summary' => $title,
                'description' => '포인트 간편충전 테스트',
                'transacted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => '포인트가 충전되었습니다.',
        ]);
    }

    protected function generatePaymentNo(): string
    {
        do {
            $paymentNo = 'PM' . now()->format('YmdHis') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (DB::table('payment_transactions')->where('payment_no', $paymentNo)->exists());

        return $paymentNo;
    }

    protected function generatePointTransactionNo(): string
    {
        do {
            $transactionNo = 'PT' . now()->format('YmdHis') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (DB::table('point_transactions')->where('transaction_no', $transactionNo)->exists());

        return $transactionNo;
    }
}
