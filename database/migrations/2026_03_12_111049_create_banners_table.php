<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->enum('banner_type', ['main', 'notice', 'warning'])->default('main')->comment('배너유형');
            $table->string('title', 150)->nullable()->comment('제목');
            $table->string('content', 255)->nullable()->comment('내용');
            $table->string('image_path', 255)->nullable()->comment('이미지경로');
            $table->string('link_url', 255)->nullable()->comment('링크URL');
            $table->integer('sort_order')->default(0)->comment('정렬순서');
            $table->boolean('is_active')->default(true)->comment('사용여부');
            $table->dateTime('start_at')->nullable()->comment('시작일시');
            $table->dateTime('end_at')->nullable()->comment('종료일시');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
