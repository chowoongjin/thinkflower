<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();

                $table->foreignId('shop_id')
                    ->constrained('shops')
                    ->cascadeOnDelete()
                    ->comment('화원사');

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->comment('결제 요청 사용자');

                $table->string('provider', 30)
                    ->default('tosspayments')
                    ->comment('결제사');

                $table->string('payment_key', 200)
                    ->unique()
                    ->comment('토스 paymentKey');

                $table->string('order_id', 64)
                    ->unique()
                    ->comment('토스 orderId / 상점 주문번호');

                $table->string('order_name', 100)
                    ->nullable()
                    ->comment('주문명');

                $table->string('method', 30)
                    ->nullable()
                    ->comment('결제수단');

                $table->unsignedInteger('total_amount')
                    ->default(0)
                    ->comment('총 결제금액');

                $table->unsignedInteger('balance_amount')
                    ->default(0)
                    ->comment('취소 가능 잔액');

                $table->unsignedInteger('supplied_amount')
                    ->default(0)
                    ->comment('공급가액');

                $table->unsignedInteger('vat')
                    ->default(0)
                    ->comment('부가세');

                $table->unsignedInteger('charged_point_amount')
                    ->default(0)
                    ->comment('충전 포인트');

                $table->unsignedInteger('bonus_point_amount')
                    ->default(0)
                    ->comment('보너스 포인트');

                $table->string('status', 30)
                    ->comment('토스 결제 상태');

                $table->timestamp('requested_at')
                    ->nullable()
                    ->comment('결제 요청 시각');

                $table->timestamp('approved_at')
                    ->nullable()
                    ->comment('결제 승인 시각');

                $table->timestamp('cancelled_at')
                    ->nullable()
                    ->comment('최종 취소 시각');

                $table->json('raw_response')
                    ->nullable()
                    ->comment('토스 응답 원본 JSON');

                $table->string('note', 255)
                    ->nullable()
                    ->comment('비고');

                $table->timestamps();

                $table->index(['shop_id', 'status']);
                $table->index(['user_id']);
                $table->index(['approved_at']);
                $table->index(['requested_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payment_transactions')) {
            Schema::dropIfExists('payment_transactions');
        }
    }
};
