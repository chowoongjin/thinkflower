<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login_id', 50)->unique()->comment('로그인 아이디');
            $table->string('name', 100)->comment('이름');
            $table->string('password')->comment('비밀번호');
            $table->enum('role', ['admin', 'hq', 'member'])->default('member')->comment('권한');
            $table->dateTime('last_login_at')->nullable()->comment('마지막 로그인');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
