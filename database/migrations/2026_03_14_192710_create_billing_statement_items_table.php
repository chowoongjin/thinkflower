<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_statement_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('billing_statement_id')
                ->constrained('billing_statements')
                ->cascadeOnDelete();

            $table->foreignId('point_transaction_id')
                ->nullable()
                ->constrained('point_transactions')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->bigInteger('amount')->default(0)->comment('청구 반영 금액');
            $table->string('summary', 255)->nullable()->comment('요약');
            $table->text('description')->nullable()->comment('상세 설명');

            $table->timestamps();

            $table->unique(['billing_statement_id', 'point_transaction_id'], 'billing_statement_items_unique_stmt_tx');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_statement_items');
    }
};
