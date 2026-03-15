<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE shops
            MODIFY current_point_balance BIGINT NOT NULL DEFAULT 0 COMMENT '현재 보유포인트'
        ");

        Schema::table('shops', function (Blueprint $table) {
            $table->enum('point_policy_type', ['prepaid', 'postpaid'])
                ->default('prepaid')
                ->after('current_point_balance')
                ->comment('포인트정책: 선불/후불');

            $table->bigInteger('credit_limit')
                ->default(0)
                ->after('point_policy_type')
                ->comment('후불 한도');

            $table->unsignedTinyInteger('billing_day')
                ->nullable()
                ->after('credit_limit')
                ->comment('매월 청구 기준일');

            $table->unsignedTinyInteger('payment_due_day')
                ->nullable()
                ->after('billing_day')
                ->comment('입금 마감일');

            $table->boolean('is_tax_invoice_enabled')
                ->default(true)
                ->after('payment_due_day')
                ->comment('세금계산서 발행 여부');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'point_policy_type',
                'credit_limit',
                'billing_day',
                'payment_due_day',
                'is_tax_invoice_enabled',
            ]);
        });

        DB::statement("
            ALTER TABLE shops
            MODIFY current_point_balance BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '현재 보유포인트'
        ");
    }
};
