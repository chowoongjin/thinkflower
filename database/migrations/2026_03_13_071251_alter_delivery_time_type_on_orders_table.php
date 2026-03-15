<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE orders
            MODIFY delivery_time_type ENUM('도착','예식','행사') NULL COMMENT '배송 시간 구분'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE orders
            MODIFY delivery_time_type ENUM('arrival','ceremony','event','normal') NULL COMMENT '배송 시간 구분'
        ");
    }
};
