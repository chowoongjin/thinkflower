<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('from_status', 30)->nullable()->comment('이전상태');
            $table->string('to_status', 30)->comment('변경상태');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('변경자');
            $table->string('memo', 255)->nullable()->comment('메모');
            $table->timestamp('created_at')->nullable();

            $table->index('order_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
