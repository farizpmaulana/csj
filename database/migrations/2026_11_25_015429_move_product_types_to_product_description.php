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
        $sections = DB::table('product_sections')
            ->where('title', 'Product Types')
            ->get();

        foreach ($sections as $section) {
            $items = DB::table('product_section_rows')
                ->where('product_section_id', $section->id)
                ->orderBy('position')
                ->orderBy('id')
                ->pluck('label')
                ->filter(fn (?string $label): bool => filled($label))
                ->values();

            if ($items->isNotEmpty()) {
                $product = DB::table('products')->where('id', $section->product_id)->first();

                if ($product) {
                    $existing = trim((string) ($product->description ?? ''));
                    if ($existing !== '' && ! str_starts_with($existing, '<')) {
                        $existing = '<p>'.e($existing).'</p>';
                    }

                    $listHtml = '<ul>'.$items->map(fn (string $item): string => '<li>'.e($item).'</li>')->implode('').'</ul>';
                    $updated = $existing !== '' ? $existing."\n".$listHtml : $listHtml;

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['description' => $updated]);
                }
            }

            DB::table('product_section_rows')
                ->where('product_section_id', $section->id)
                ->delete();

            DB::table('product_sections')
                ->where('id', $section->id)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data migration cannot be losslessly reversed.
    }
};
