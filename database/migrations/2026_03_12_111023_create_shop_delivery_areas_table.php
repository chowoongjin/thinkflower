<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_delivery_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['shop_id', 'region_id'], 'uq_shop_delivery_areas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_delivery_areas');
    }
};
