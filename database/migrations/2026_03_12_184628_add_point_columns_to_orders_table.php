<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('point_used_amount')
                ->default(0)
                ->after('payment_amount')
                ->comment('발주 시 사용 포인트');

            $table->unsignedInteger('point_earned_amount')
                ->default(0)
                ->after('point_used_amount')
                ->comment('수주 시 적립 포인트');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['point_used_amount', 'point_earned_amount']);
        });
    }
};
