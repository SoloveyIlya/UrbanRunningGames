<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Партнёры/спонсоры по ТЗ: логотип, ссылка, описание. Форма обратной связи.
     */
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('logo_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('website_url', 512)->nullable();
            $table->text('description')->nullable();
            $table->string('level', 64)->nullable(); // партнёр / спонсор
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('event_partners', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->primary(['event_id', 'partner_id']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('topic', 64)->default('other'); // participation, merch, partnership, other
            $table->string('phone', 32)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('message');
            $table->string('status', 32)->default('new'); // new, in_progress, closed, spam
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('event_partners');
        Schema::dropIfExists('partners');
    }
};
