<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_by_admin_user_id')
                ->nullable()
                ->after('receiver_shop_id')
                ->comment('본부에서 수주사 지정/처리한 관리자 user id');

            $table->foreign('assigned_by_admin_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['assigned_by_admin_user_id']);
            $table->dropColumn('assigned_by_admin_user_id');
        });
    }
};
