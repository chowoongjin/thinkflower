<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE point_transactions
            MODIFY balance_before BIGINT NOT NULL DEFAULT 0 COMMENT '거래 전 잔액',
            MODIFY balance_after BIGINT NOT NULL DEFAULT 0 COMMENT '거래 후 잔액'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE point_transactions
            MODIFY balance_before BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '거래 전 잔액',
            MODIFY balance_after BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '거래 후 잔액'
        ");
    }
};
