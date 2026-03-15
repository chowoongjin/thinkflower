<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 20)->unique()->comment('주문번호');

            $table->enum('order_type', ['hq', 'direct'])->default('hq')->comment('주문유형');
            $table->enum('brokerage_type', ['waiting', 'assigned', 'not_needed'])->default('waiting')->comment('중개상태');

            $table->foreignId('orderer_shop_id')->constrained('shops')->restrictOnDelete()->comment('발주화원');
            $table->foreignId('receiver_shop_id')->nullable()->constrained('shops')->nullOnDelete()->comment('수주화원');
            $table->foreignId('created_user_id')->constrained('users')->restrictOnDelete()->comment('작성자');
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete()->comment('상품카테고리');

            $table->string('product_name', 100)->comment('상품명');
            $table->string('product_detail', 255)->comment('상품상세');
            $table->string('product_image_path', 255)->nullable()->comment('상품이미지 경로');
            $table->string('product_image_url', 255)->nullable()->comment('상품이미지 URL');

            $table->unsignedInteger('original_amount')->default(0)->comment('원청금액');
            $table->unsignedInteger('order_amount')->default(0)->comment('발주금액');
            $table->unsignedInteger('supply_amount')->default(0)->comment('수주금액');
            $table->unsignedInteger('brokerage_fee')->default(0)->comment('중개수수료');
            $table->unsignedInteger('payment_amount')->default(0)->comment('실결제금액');

            $table->string('delivery_zipcode', 10)->nullable()->comment('배송지 우편번호');
            $table->string('delivery_addr1', 255)->comment('배송지 기본주소');
            $table->string('delivery_addr2', 255)->nullable()->comment('배송지 상세주소');
            $table->string('delivery_place', 255)->nullable()->comment('식장/빈소/행사장명');

            $table->date('delivery_date')->comment('배송일');
            $table->unsignedTinyInteger('delivery_hour')->nullable()->comment('배송시');
            $table->unsignedTinyInteger('delivery_minute')->nullable()->comment('배송분');
            $table->enum('delivery_time_type', ['arrival', 'ceremony', 'event', 'normal'])->default('normal')->comment('배송시간구분');
            $table->boolean('is_urgent')->default(false)->comment('긴급주문');

            $table->string('recipient_name', 50)->comment('수령인명');
            $table->string('recipient_phone', 30)->comment('수령인연락처');

            $table->string('ribbon_phrase', 100)->comment('리본문구');
            $table->string('sender_name', 100)->comment('보내는분');
            $table->string('card_message', 255)->nullable()->comment('카드메시지');

            $table->string('request_note', 255)->nullable()->comment('요청사항');
            $table->boolean('request_photo')->default(false)->comment('배송사진 요청');

            $table->enum('current_status', [
                'draft',
                'submitted',
                'hq_received',
                'brokerage_waiting',
                'assigned',
                'accepted',
                'working',
                'delivered',
                'cancelled',
            ])->default('submitted')->comment('현재상태');

            $table->dateTime('accepted_at')->nullable()->comment('접수일시');
            $table->dateTime('delivered_at')->nullable()->comment('배송완료일시');
            $table->dateTime('cancelled_at')->nullable()->comment('취소일시');
            $table->string('cancel_reason', 255)->nullable()->comment('취소사유');

            $table->string('receiver_name', 100)->nullable()->comment('인수자명');
            $table->string('receiver_relation', 50)->nullable()->comment('인수관계');

            $table->timestamps();
            $table->softDeletes();

            $table->index('order_no');
            $table->index('orderer_shop_id');
            $table->index('receiver_shop_id');
            $table->index('delivery_date');
            $table->index('current_status');
            $table->index('brokerage_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
