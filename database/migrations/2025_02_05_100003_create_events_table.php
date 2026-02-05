<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * События по ТЗ: дата, город, место, описание, правила; без продажи слотов.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location_text', 255)->nullable();
            $table->dateTime('starts_at');
            $table->text('description')->nullable();
            $table->text('rules')->nullable();
            $table->string('status', 32)->default('draft'); // draft, published, closed, archived
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
