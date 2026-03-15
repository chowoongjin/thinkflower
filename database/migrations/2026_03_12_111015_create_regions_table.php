<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('sido', 50)->comment('시도');
            $table->string('sigungu', 50)->comment('시군구');
            $table->string('eup_myeon_dong', 80)->nullable()->comment('읍면동');
            $table->boolean('is_active')->default(true)->comment('사용여부');
            $table->timestamps();

            $table->index(['sido', 'sigungu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
