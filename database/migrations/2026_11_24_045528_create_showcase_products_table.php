<?php

use App\Models\Brand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('showcase_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete()->unique();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        // Default showcase: 2 products from each of our 4 current partners (maximum 8).
        $targetBrandSlugs = ['bosch', 'buccheri', 'sata', 'sumato'];
        $brands = Brand::query()
            ->whereIn('slug', $targetBrandSlugs)
            ->with(['products' => fn ($q) => $q->where('is_visible', true)->orderBy('position')])
            ->get()
            ->keyBy('slug');

        $position = 1;
        $now = now();

        foreach ([0, 1] as $index) {
            foreach ($targetBrandSlugs as $slug) {
                if ($position > 8) {
                    break;
                }

                $product = $brands->get($slug)?->products->get($index);

                if ($product) {
                    DB::table('showcase_products')->insert([
                        'product_id' => $product->id,
                        'position' => $position,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('products')->where('id', $product->id)->update([
                        'is_showcase' => true,
                        'showcase_position' => $position,
                    ]);

                    $position++;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_products');
    }
};
