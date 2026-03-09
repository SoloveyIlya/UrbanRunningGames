<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 32)->default('tbank');
            $table->string('external_payment_id', 128)->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('RUB');
            $table->string('status', 32)->default('new'); // new, pending, paid, failed, canceled, refunded
            $table->text('pay_url')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unique(['provider', 'external_payment_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->after('discount_amount')->constrained()->nullOnDelete();
            $table->timestamp('paid_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn(['payment_id', 'paid_at']);
        });
        Schema::dropIfExists('payments');
    }
};
