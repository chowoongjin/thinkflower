<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_hidden')
                ->default(false)
                ->after('cancel_reason')
                ->comment('리스트 숨김 여부');

            $table->dateTime('hidden_at')
                ->nullable()
                ->after('is_hidden')
                ->comment('숨김 처리 일시');

            $table->unsignedBigInteger('hidden_by_admin_user_id')
                ->nullable()
                ->after('hidden_at')
                ->comment('숨김 처리한 관리자 user id');

            $table->index('is_hidden');
            $table->index('hidden_at');

            $table->foreign('hidden_by_admin_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['hidden_by_admin_user_id']);
            $table->dropIndex(['is_hidden']);
            $table->dropIndex(['hidden_at']);
            $table->dropColumn([
                'is_hidden',
                'hidden_at',
                'hidden_by_admin_user_id',
            ]);
        });
    }
};
