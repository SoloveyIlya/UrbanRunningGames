<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        \DB::table('product_types')->insert([
            ['slug' => 'running', 'label' => 'Беговые футболки', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'urban', 'label' => 'Городские футболки', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'hoodies', 'label' => 'Толстовки', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('product_types');
    }
};
