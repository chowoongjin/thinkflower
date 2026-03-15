<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete()->comment('화원사');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('결제 요청 사용자');

            $table->string('payment_no', 30)->unique()->comment('결제번호');
            $table->string('pg_provider', 30)->nullable()->comment('PG사');
            $table->string('pg_transaction_id', 100)->nullable()->comment('PG 거래번호');

            $table->enum('payment_method', ['card', 'bank_transfer', 'virtual_account', 'manual'])
                ->default('card')
                ->comment('결제수단');

            $table->unsignedInteger('payment_amount')->default(0)->comment('결제금액');
            $table->unsignedInteger('charged_point_amount')->default(0)->comment('충전 포인트');
            $table->unsignedInteger('bonus_point_amount')->default(0)->comment('보너스 포인트');

            $table->enum('status', ['ready', 'paid', 'cancelled', 'failed'])
                ->default('ready')
                ->comment('결제상태');

            $table->timestamp('paid_at')->nullable()->comment('결제완료일시');
            $table->timestamp('cancelled_at')->nullable()->comment('결제취소일시');

            $table->string('title', 100)->nullable()->comment('결제명');
            $table->string('note', 255)->nullable()->comment('비고');

            $table->timestamps();

            $table->index(['shop_id', 'status']);
            $table->index(['user_id']);
            $table->index(['paid_at']);
        });

        DB::statement("
            ALTER TABLE payment_transactions
            ADD CONSTRAINT chk_payment_transactions_status
            CHECK (status IN ('ready', 'paid', 'cancelled', 'failed'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
