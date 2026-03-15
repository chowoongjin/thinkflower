<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();

            $table->string('category', 20)->comment('공지구분: general=본부공지사항, special=특별공지사항');
            $table->string('title', 255)->comment('공지내용');

            $table->boolean('is_pinned')->default(false)->comment('상단고정 여부');
            $table->boolean('is_active')->default(true)->comment('노출 여부');

            $table->unsignedInteger('sort_order')->default(0)->comment('정렬순서');

            $table->timestamp('starts_at')->nullable()->comment('노출 시작일시');
            $table->timestamp('ends_at')->nullable()->comment('노출 종료일시');

            $table->unsignedBigInteger('created_by')->nullable()->comment('작성자 users.id');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('수정자 users.id');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'is_active']);
            $table->index(['category', 'is_pinned']);
            $table->index(['sort_order']);
            $table->index(['starts_at', 'ends_at']);
            $table->index(['created_by']);
            $table->index(['updated_by']);
        });

        DB::statement("
            ALTER TABLE notices
            ADD CONSTRAINT chk_notices_category
            CHECK (category IN ('general', 'special'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
