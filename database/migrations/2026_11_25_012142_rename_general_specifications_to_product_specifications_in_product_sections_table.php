<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('product_sections')
            ->where('title', 'General Specifications')
            ->update(['title' => 'Product Specifications']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('product_sections')
            ->where('title', 'Product Specifications')
            ->update(['title' => 'General Specifications']);
    }
};
