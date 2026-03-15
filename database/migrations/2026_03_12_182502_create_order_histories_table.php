<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->comment('orders.id');
            $table->string('order_no', 20)->comment('주문번호 스냅샷');

            $table->string('history_type', 30)->default('etc')->comment('처리유형: created, updated, received, cancelled, etc');
            $table->string('message', 255)->comment('처리내용');

            $table->timestamp('processed_at')->useCurrent()->comment('처리시간');
            $table->unsignedBigInteger('actor_user_id')->nullable()->comment('처리자 users.id');

            $table->timestamps();

            $table->index('order_id');
            $table->index('order_no');
            $table->index('history_type');
            $table->index('processed_at');
            $table->index('actor_user_id');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();
        });

        DB::statement("
            ALTER TABLE order_histories
            ADD CONSTRAINT chk_order_histories_history_type
            CHECK (history_type IN ('created', 'updated', 'received', 'accepted', 'delivered', 'cancelled', 'etc'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('order_histories');
    }
};
