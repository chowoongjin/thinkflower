<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();

            $table->string('statement_no', 50)->unique()->comment('청구서 번호');

            $table->date('period_start')->comment('정산 시작일');
            $table->date('period_end')->comment('정산 종료일');

            $table->bigInteger('debit_total')->default(0)->comment('차감 합계');
            $table->bigInteger('credit_total')->default(0)->comment('적립 합계');
            $table->bigInteger('adjust_total')->default(0)->comment('조정 합계');
            $table->bigInteger('invoice_amount')->default(0)->comment('청구 금액');
            $table->bigInteger('paid_amount')->default(0)->comment('입금 금액');

            $table->enum('status', ['draft', 'issued', 'partially_paid', 'paid', 'cancelled'])
                ->default('draft')
                ->comment('청구 상태');

            $table->dateTime('issued_at')->nullable()->comment('청구서 발행일시');
            $table->date('due_date')->nullable()->comment('입금 마감일');
            $table->dateTime('paid_at')->nullable()->comment('최종 입금일시');

            $table->string('tax_invoice_no', 100)->nullable()->comment('세금계산서 번호');
            $table->text('memo')->nullable()->comment('메모');

            $table->timestamps();

            $table->index(['shop_id', 'status']);
            $table->index(['period_start', 'period_end']);
            $table->index('issued_at');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_statements');
    }
};
