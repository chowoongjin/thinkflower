<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_type', 50)->comment('업로드 업무 유형');
            $table->string('upload_type', 50)->comment('cafe24 업로드 타입');
            $table->string('status', 20)->default('pending')->comment('pending|processing|done|failed');
            $table->string('local_path', 255)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->string('original_mime_type', 100)->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('photo_field', 50)->nullable();
            $table->string('photo_type', 30)->nullable();
            $table->unsignedInteger('sort_order')->nullable();

            $table->string('disk', 50)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('relative_path', 255)->nullable();
            $table->string('url', 255)->nullable();

            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'task_type']);
            $table->index('order_id');
            $table->index('shop_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_tasks');
    }
};
