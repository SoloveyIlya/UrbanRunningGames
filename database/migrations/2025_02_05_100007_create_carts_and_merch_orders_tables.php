<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Корзина и заявки на мерч по ТЗ: имя, телефон, email, комментарий; статусы заявок.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->char('session_token', 36)->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('qty')->default(1);
            $table->primary(['cart_id', 'product_variant_id']);
        });

        Schema::create('merch_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone', 32);
            $table->string('customer_email', 255);
            $table->text('comment')->nullable();
            $table->string('status', 32)->default('created'); // created, in_progress, confirmed, canceled, completed
            $table->decimal('items_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->char('currency', 3)->default('RUB');
            $table->timestamps();
        });

        Schema::create('merch_order_items', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained('merch_orders')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('qty');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->primary(['order_id', 'product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merch_order_items');
        Schema::dropIfExists('merch_orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
