<?php

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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('about_tagline', 500)->nullable()->after('default_social_image');
            $table->text('about_description')->nullable()->after('about_tagline');
            $table->text('vision_text')->nullable()->after('about_description');
            $table->json('mission_items')->nullable()->after('vision_text');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_showcase')->default(false)->after('is_visible');
            $table->unsignedSmallInteger('showcase_position')->default(0)->after('is_showcase');
        });

        // Seed current hardcoded values so the site stays identical.
        DB::table('site_settings')->update([
            'about_tagline' => 'Official Distributor of Sumato, Bosch, and Buccheri Industrial',
            'about_description' => 'PT Cahaya Sarange Jaya connects businesses with dependable products from Sumato, Bosch, and Buccheri. We pair quality products with practical guidance and responsive service, so your teams can move with confidence.',
            'vision_text' => 'To be a fast, trustworthy one-stop supplier of industrial goods for companies around the world.',
            'mission_items' => json_encode([
                ['text' => 'To deliver top-quality products to our customers.'],
                ['text' => 'To become the first choice of global partners for top-quality brands.'],
                ['text' => 'To connect brands and customers through a smooth business process.'],
                ['text' => 'To do business with trust and integrity.'],
            ]),
        ]);

        // Default showcase: first visible product from each brand.
        $brands = DB::table('brands')->where('is_visible', true)->orderBy('position')->get();
        $position = 0;

        foreach ($brands as $brand) {
            $product = DB::table('products')
                ->where('brand_id', $brand->id)
                ->where('is_visible', true)
                ->orderBy('position')
                ->first();

            if ($product) {
                DB::table('products')->where('id', $product->id)->update([
                    'is_showcase' => true,
                    'showcase_position' => $position++,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_showcase', 'showcase_position']);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['about_tagline', 'about_description', 'vision_text', 'mission_items']);
        });
    }
};
