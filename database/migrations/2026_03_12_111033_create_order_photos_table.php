<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('photo_type', ['product', 'delivery_site', 'receipt', 'other'])->default('delivery_site')->comment('사진유형');
            $table->string('file_path', 255)->comment('파일경로');
            $table->integer('sort_order')->default(0)->comment('정렬순서');
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('업로드 사용자');
            $table->timestamp('created_at')->nullable();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_photos');
    }
};
