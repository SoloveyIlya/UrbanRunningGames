<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('type', 16);
            $table->decimal('value', 10, 2);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('times_used')->default(0);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promo_code_product', function (Blueprint $table) {
            $table->foreignId('promo_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['promo_code_id', 'product_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('promo_code_id')->nullable()->after('comment')->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->nullable()->after('promo_code_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['promo_code_id']);
            $table->dropColumn(['promo_code_id', 'discount_amount']);
        });
        Schema::dropIfExists('promo_code_product');
        Schema::dropIfExists('promo_codes');
    }
};
