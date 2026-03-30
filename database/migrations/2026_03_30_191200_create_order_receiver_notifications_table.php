<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_receiver_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('receiver_shop_id')->constrained('shops')->restrictOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status', 20)->default('pending')->comment('pending|processing|sent|failed|superseded');
            $table->string('fax_status', 20)->default('pending')->comment('pending|processing|sent|failed');
            $table->string('kakaotalk_status', 20)->default('pending')->comment('pending|sent|failed|skipped');

            $table->string('fax_file_name')->nullable();
            $table->string('fax_send_key', 100)->nullable();
            $table->text('fax_response')->nullable();

            $table->string('kakaotalk_send_key', 100)->nullable();
            $table->text('kakaotalk_response')->nullable();

            $table->text('error_message')->nullable();
            $table->timestamp('fax_sent_at')->nullable();
            $table->timestamp('kakaotalk_sent_at')->nullable();
            $table->timestamp('history_recorded_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['receiver_shop_id', 'status']);
            $table->index('fax_send_key');
            $table->index('kakaotalk_send_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_receiver_notifications');
    }
};
