<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `point_transactions`
            MODIFY COLUMN `transaction_type` ENUM(
                'charge',
                'bonus',
                'order_debit',
                'order_credit',
                'refund',
                'adjust_plus',
                'adjust_minus',
                'expire',
                'order_cancel_refund',
                'order_credit_cancel'
            ) NOT NULL COMMENT '포인트 거래유형'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE `point_transactions`
            MODIFY COLUMN `transaction_type` ENUM(
                'charge',
                'bonus',
                'order_debit',
                'order_credit',
                'refund',
                'adjust_plus',
                'adjust_minus',
                'expire'
            ) NOT NULL COMMENT '포인트 거래유형'
        ");
    }
};
