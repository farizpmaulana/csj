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
        $products = DB::table('products')->get(['id', 'name', 'description']);

        foreach ($products as $product) {
            $desc = trim((string) ($product->description ?? ''));
            $title = trim((string) ($product->name ?? ''));

            if ($desc === '' || $title === '') {
                continue;
            }

            $heading = '<h3>'.e($title).'</h3>';

            if (str_starts_with($desc, $heading)) {
                continue;
            }

            if (! str_starts_with($desc, '<')) {
                $desc = '<p>'.e($desc).'</p>';
            }

            DB::table('products')
                ->where('id', $product->id)
                ->update(['description' => $heading."\n".$desc]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $products = DB::table('products')->get(['id', 'name', 'description']);

        foreach ($products as $product) {
            $desc = trim((string) ($product->description ?? ''));
            $heading = '<h3>'.e(trim((string) ($product->name ?? ''))).'</h3>';

            if (str_starts_with($desc, $heading)) {
                $rest = ltrim(substr($desc, strlen($heading)));
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['description' => $rest]);
            }
        }
    }
};
