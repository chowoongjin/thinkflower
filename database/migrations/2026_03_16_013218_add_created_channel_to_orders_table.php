<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('created_channel', 30)
                ->default('member')
                ->after('created_user_id')
                ->comment('주문 생성 경로: member, admin_realtime, admin_manual');

            $table->unsignedBigInteger('created_by_admin_user_id')
                ->nullable()
                ->after('created_channel')
                ->comment('관리자 등록 시 관리자 user id');

            $table->index('created_channel');
            $table->index('created_by_admin_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_channel']);
            $table->dropIndex(['created_by_admin_user_id']);
            $table->dropColumn(['created_channel', 'created_by_admin_user_id']);
        });
    }
};
