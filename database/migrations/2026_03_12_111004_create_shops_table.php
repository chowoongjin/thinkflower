<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name', 100)->comment('화원사명');
            $table->string('owner_name', 50)->comment('대표자명');
            $table->string('business_no', 20)->unique()->comment('사업자번호');
            $table->string('business_license_path', 255)->nullable()->comment('사업자등록증 파일');
            $table->string('business_zipcode', 10)->nullable()->comment('우편번호');
            $table->string('business_addr1', 255)->comment('기본주소');
            $table->string('business_addr2', 255)->nullable()->comment('상세주소');
            $table->string('tax_email', 100)->nullable()->comment('세금계산서 이메일');
            $table->string('main_phone', 30)->comment('대표 연락처');
            $table->string('sub_phone', 30)->nullable()->comment('비상 연락처');
            $table->string('fax', 30)->nullable()->comment('팩스');
            $table->string('career_years_label', 30)->nullable()->comment('경력');
            $table->string('bank_name', 50)->comment('은행명');
            $table->string('bank_account', 50)->comment('계좌번호');
            $table->string('bank_holder', 100)->comment('예금주');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending')->comment('승인상태');
            $table->boolean('is_active')->default(true)->comment('사용여부');
            $table->dateTime('approved_at')->nullable()->comment('승인일시');
            $table->dateTime('rejected_at')->nullable()->comment('반려일시');
            $table->string('rejection_reason', 255)->nullable()->comment('반려사유');
            $table->timestamps();
            $table->softDeletes();

            $table->index('shop_name');
            $table->index('owner_name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
