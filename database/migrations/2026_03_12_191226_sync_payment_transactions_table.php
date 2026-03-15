<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_transactions', 'provider')) {
                $table->string('provider', 30)
                    ->default('tosspayments')
                    ->comment('결제사');
            }

            if (!Schema::hasColumn('payment_transactions', 'payment_key')) {
                $table->string('payment_key', 200)
                    ->nullable()
                    ->comment('토스 paymentKey');
            }

            if (!Schema::hasColumn('payment_transactions', 'order_id')) {
                $table->string('order_id', 64)
                    ->nullable()
                    ->comment('토스 orderId / 상점 주문번호');
            }

            if (!Schema::hasColumn('payment_transactions', 'order_name')) {
                $table->string('order_name', 100)
                    ->nullable()
                    ->comment('주문명');
            }

            if (!Schema::hasColumn('payment_transactions', 'method')) {
                $table->string('method', 30)
                    ->nullable()
                    ->comment('결제수단');
            }

            if (!Schema::hasColumn('payment_transactions', 'total_amount')) {
                $table->unsignedInteger('total_amount')
                    ->default(0)
                    ->comment('총 결제금액');
            }

            if (!Schema::hasColumn('payment_transactions', 'balance_amount')) {
                $table->unsignedInteger('balance_amount')
                    ->default(0)
                    ->comment('취소 가능 잔액');
            }

            if (!Schema::hasColumn('payment_transactions', 'supplied_amount')) {
                $table->unsignedInteger('supplied_amount')
                    ->default(0)
                    ->comment('공급가액');
            }

            if (!Schema::hasColumn('payment_transactions', 'vat')) {
                $table->unsignedInteger('vat')
                    ->default(0)
                    ->comment('부가세');
            }

            if (!Schema::hasColumn('payment_transactions', 'charged_point_amount')) {
                $table->unsignedInteger('charged_point_amount')
                    ->default(0)
                    ->comment('충전 포인트');
            }

            if (!Schema::hasColumn('payment_transactions', 'bonus_point_amount')) {
                $table->unsignedInteger('bonus_point_amount')
                    ->default(0)
                    ->comment('보너스 포인트');
            }

            if (!Schema::hasColumn('payment_transactions', 'status')) {
                $table->string('status', 30)
                    ->default('DONE')
                    ->comment('토스 결제 상태');
            }

            if (!Schema::hasColumn('payment_transactions', 'requested_at')) {
                $table->timestamp('requested_at')
                    ->nullable()
                    ->comment('결제 요청 시각');
            }

            if (!Schema::hasColumn('payment_transactions', 'approved_at')) {
                $table->timestamp('approved_at')
                    ->nullable()
                    ->comment('결제 승인 시각');
            }

            if (!Schema::hasColumn('payment_transactions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')
                    ->nullable()
                    ->comment('최종 취소 시각');
            }

            if (!Schema::hasColumn('payment_transactions', 'raw_response')) {
                $table->json('raw_response')
                    ->nullable()
                    ->comment('토스 응답 원본 JSON');
            }

            if (!Schema::hasColumn('payment_transactions', 'note')) {
                $table->string('note', 255)
                    ->nullable()
                    ->comment('비고');
            }
        });

        try {
            DB::statement('ALTER TABLE payment_transactions ADD UNIQUE uk_payment_transactions_payment_key (payment_key)');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('ALTER TABLE payment_transactions ADD UNIQUE uk_payment_transactions_order_id (order_id)');
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        // 운영 데이터 보호
    }
};
