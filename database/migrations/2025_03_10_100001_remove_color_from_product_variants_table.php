<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('product_variants', 'color')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('product_variants', 'color')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->string('color', 32)->nullable()->after('size');
            });
        }
    }
};
