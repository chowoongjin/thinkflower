<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete()->comment('화원사');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('처리 사용자');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete()->comment('관련 주문');
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions')->nullOnDelete()->comment('관련 결제');

            $table->string('transaction_no', 30)->unique()->comment('포인트 거래번호');

            $table->enum('transaction_type', [
                'charge',
                'bonus',
                'order_debit',
                'order_credit',
                'refund',
                'adjust_plus',
                'adjust_minus',
                'expire'
            ])->comment('포인트 거래유형');

            $table->enum('direction', ['in', 'out'])->comment('증가/차감');
            $table->unsignedInteger('amount')->comment('변동 포인트');

            $table->unsignedBigInteger('balance_before')->default(0)->comment('변경 전 잔액');
            $table->unsignedBigInteger('balance_after')->default(0)->comment('변경 후 잔액');

            $table->string('summary', 150)->comment('화면 표시용 제목');
            $table->string('description', 255)->nullable()->comment('상세 설명');

            $table->timestamp('transacted_at')->useCurrent()->comment('처리일시');
            $table->timestamps();

            $table->index(['shop_id', 'transacted_at']);
            $table->index(['order_id']);
            $table->index(['payment_transaction_id']);
            $table->index(['transaction_type']);
        });

        DB::statement("
            ALTER TABLE point_transactions
            ADD CONSTRAINT chk_point_transactions_direction
            CHECK (direction IN ('in', 'out'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
