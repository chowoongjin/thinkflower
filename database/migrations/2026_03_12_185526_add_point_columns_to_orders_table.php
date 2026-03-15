<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'point_used_amount')) {
                $table->unsignedInteger('point_used_amount')
                    ->default(0)
                    ->after('payment_amount')
                    ->comment('발주 시 사용 포인트');
            }

            if (!Schema::hasColumn('orders', 'point_earned_amount')) {
                $table->unsignedInteger('point_earned_amount')
                    ->default(0)
                    ->after('point_used_amount')
                    ->comment('수주 시 적립 포인트');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('orders', 'point_used_amount')) {
                $dropColumns[] = 'point_used_amount';
            }

            if (Schema::hasColumn('orders', 'point_earned_amount')) {
                $dropColumns[] = 'point_earned_amount';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
