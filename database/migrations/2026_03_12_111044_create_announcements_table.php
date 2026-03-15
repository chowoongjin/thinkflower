<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150)->comment('제목');
            $table->text('content')->comment('내용');
            $table->boolean('is_popup')->default(false)->comment('팝업여부');
            $table->boolean('is_active')->default(true)->comment('사용여부');
            $table->dateTime('start_at')->nullable()->comment('시작일시');
            $table->dateTime('end_at')->nullable()->comment('종료일시');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('작성자');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
