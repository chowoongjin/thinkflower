<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'view_count')) {
                $table->unsignedInteger('view_count')->default(0)->after('end_at')->comment('조회수');
            }

            if (!Schema::hasColumn('announcements', 'recommend_count')) {
                $table->unsignedInteger('recommend_count')->default(0)->after('view_count')->comment('추천수');
            }

            if (!Schema::hasColumn('announcements', 'updated_by_user_id')) {
                $table->unsignedBigInteger('updated_by_user_id')->nullable()->after('created_by_user_id')->comment('수정자 users.id');
            }

            if (!Schema::hasColumn('announcements', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('updated_at');
            }
        });

        Schema::table('announcements', function (Blueprint $table) {
            try {
                $table->foreign('updated_by_user_id', 'announcements_updated_by_user_id_foreign')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            } catch (\Throwable $e) {
            }

            try {
                $table->index(['is_active', 'created_at'], 'announcements_is_active_created_at_index');
            } catch (\Throwable $e) {
            }

            try {
                $table->index(['start_at', 'end_at'], 'announcements_start_at_end_at_index');
            } catch (\Throwable $e) {
            }

            try {
                $table->index('view_count', 'announcements_view_count_index');
            } catch (\Throwable $e) {
            }
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            try {
                $table->dropForeign('announcements_updated_by_user_id_foreign');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('announcements_is_active_created_at_index');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('announcements_start_at_end_at_index');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('announcements_view_count_index');
            } catch (\Throwable $e) {
            }

            if (Schema::hasColumn('announcements', 'updated_by_user_id')) {
                $table->dropColumn('updated_by_user_id');
            }

            if (Schema::hasColumn('announcements', 'recommend_count')) {
                $table->dropColumn('recommend_count');
            }

            if (Schema::hasColumn('announcements', 'view_count')) {
                $table->dropColumn('view_count');
            }

            if (Schema::hasColumn('announcements', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
