<?php

namespace App\Console\Commands;

use App\Enums\ProductSectionType;
use App\Models\Article;
use App\Models\Brand;
use App\Models\Client;
use App\Models\GalleryImage;
use App\Models\LegacyRedirect;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCatalogCommand extends Command
{
    protected $signature = 'app:import-catalog
                            {--fresh : Truncate the content tables before importing}';

    protected $description = 'Import the seeded catalog, articles and homepage media into the database';

    public function handle(): int
    {
        $catalog = $this->seedData('catalog');
        $articles = $this->seedData('articles');
        $homeMedia = $this->seedData('home-media');

        if ($this->option('fresh')) {
            $this->truncateContent();
        }

        DB::transaction(function () use ($catalog, $articles, $homeMedia): void {
            $this->importSiteSettings();
            $this->importBrands($catalog);
            $this->importProducts($catalog);
            $this->importArticles($articles);
            $this->importClients($homeMedia);
            $this->importGallery($homeMedia);
            $this->importLegacyRedirects($catalog);
        });

        $this->newLine();
        $this->components->info('Import complete.');

        $this->table(
            ['Table', 'Rows'],
            [
                ['brands', (string) Brand::count()],
                ['product_categories', (string) ProductCategory::count()],
                ['products', (string) Product::count()],
                ['articles', (string) Article::count()],
                ['clients', (string) Client::count()],
                ['gallery_images', (string) GalleryImage::count()],
                ['legacy_redirects', (string) LegacyRedirect::count()],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    protected function seedData(string $name): array
    {
        $path = database_path('seed-data/'.$name.'.php');

        if (! is_file($path)) {
            $this->components->error("Missing seed data file: {$path}");

            exit(self::FAILURE);
        }

        return require $path;
    }

    protected function truncateContent(): void
    {
        foreach ([
            'product_section_rows',
            'product_sections',
            'product_images',
            'products',
            'product_categories',
            'articles',
            'clients',
            'gallery_images',
            'legacy_redirects',
            'brands',
        ] as $table) {
            DB::table($table)->delete();
        }

        $this->components->info('Existing content cleared.');
    }

    protected function importSiteSettings(): void
    {
        SiteSetting::query()->updateOrCreate([], [
            'company_name' => 'PT Cahaya Sarange Jaya',
            'logo_path' => 'images/logo_own/logo_csj02.png',
            'favicon_path' => 'images/favicon.svg',
            'whatsapp_number' => '6281140000223',
            'contact_phone' => '+62 542 8860262',
            'contact_email' => 'info@cahayasarangejaya.co.id',
            'address_primary' => 'Jl. MT Haryono Ruko Haryono Palace No.25, Balikpapan, Kalimantan Timur 76125',
            'address_secondary' => 'Jl. Cilandak Raya KKO / Komplek Vico Kav. 12, Jakarta Selatan, DKI Jakarta 12560',
            'instagram_url' => 'https://www.instagram.com/cahayasarangejaya',
            'facebook_url' => 'https://www.facebook.com/ptcahayasarangejaya',
            'linkedin_url' => 'https://www.linkedin.com/company/pt-cahayasarangejaya/',
            'default_social_image' => 'images/logo_own/logo_csj02.png',
        ]);
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    protected function importBrands(array $catalog): void
    {
        $position = 0;

        foreach ($catalog['brands'] as $key => $brand) {
            Brand::query()->updateOrCreate(['slug' => $key], [
                'name' => $brand['name'],
                'headline' => $brand['headline'] ?? null,
                'intro' => $brand['intro'] ?? null,
                'seo_title' => $brand['seo_title'] ?? null,
                'card_image' => $brand['card_image'] ?? null,
                'card_alt' => $brand['card_alt'] ?? null,
                'position' => $position++,
                'is_visible' => true,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    protected function importProducts(array $catalog): void
    {
        $categoryIds = [];

        foreach ($catalog['brands'] as $brandKey => $brand) {
            $brandModel = Brand::query()->where('slug', $brandKey)->firstOrFail();
            $position = 0;

            foreach ($brand['categories'] as $category) {
                $categoryModel = ProductCategory::query()->updateOrCreate([
                    'brand_id' => $brandModel->id,
                    'name' => $category['name'],
                ], [
                    'position' => $position++,
                ]);

                $categoryIds[$brandKey][$category['name']] = $categoryModel->id;
            }
        }

        $position = 0;

        foreach ($catalog['products'] as $slug => $product) {
            $brandModel = Brand::query()->where('slug', $product['brand'])->firstOrFail();

            $productModel = Product::query()->updateOrCreate(['slug' => $slug], [
                'brand_id' => $brandModel->id,
                'product_category_id' => $categoryIds[$product['brand']][$product['category']] ?? null,
                'name' => $product['name'],
                'subtitle' => $product['subtitle'] ?? null,
                'seo_title' => $product['seo_title'] ?? null,
                'excerpt' => $product['excerpt'] ?? null,
                'description' => $product['description'] ?? null,
                'card_image' => $product['card_image'] ?? null,
                'card_alt' => $product['card_alt'] ?? null,
                'card_label' => null,
                'whatsapp_message' => $product['whatsapp'] ?? null,
                'position' => $position++,
                'is_visible' => true,
            ]);

            $this->importProductDetail($productModel, $product);
        }

        $this->applyBrandCardCopy($catalog, $categoryIds);
    }

    /**
     * The brand list pages carried their own card copy; fold it onto the product.
     *
     * @param  array<string, mixed>  $catalog
     * @param  array<string, array<string, int>>  $categoryIds
     */
    protected function applyBrandCardCopy(array $catalog, array $categoryIds): void
    {
        $position = 0;

        foreach ($catalog['brands'] as $brandKey => $brand) {
            foreach ($brand['categories'] as $category) {
                foreach ($category['products'] as $card) {
                    Product::query()
                        ->where('slug', $card['slug'])
                        ->update([
                            'card_label' => $card['label'] ?? null,
                            'card_image' => $card['image'] ?? null,
                            'card_alt' => $card['alt'] ?? null,
                            'excerpt' => $card['excerpt'] ?? null,
                            'product_category_id' => $categoryIds[$brandKey][$category['name']] ?? null,
                            'position' => $position++,
                        ]);
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $product
     */
    protected function importProductDetail(Product $productModel, array $product): void
    {
        $productModel->images()->delete();
        $productModel->sections()->delete();

        foreach ($product['gallery'] ?? [] as $position => $image) {
            $productModel->images()->create([
                'path' => $image['src'],
                'alt' => $image['alt'] ?? null,
                'position' => $position,
            ]);
        }

        foreach ($product['sections'] ?? [] as $position => $section) {
            $sectionModel = $productModel->sections()->create([
                'title' => $section['title'],
                'type' => ProductSectionType::tryFrom($section['type'] ?? 'list') ?? ProductSectionType::List,
                'position' => $position,
            ]);

            $rows = $section['rows'] ?? array_map(
                fn (string $item): array => ['label' => $item, 'value' => null],
                $section['items'] ?? []
            );

            foreach ($rows as $rowPosition => $row) {
                $sectionModel->rows()->create([
                    'label' => $row['label'] ?? null,
                    'value' => $row['value'] ?? null,
                    'position' => $rowPosition,
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $articles
     */
    protected function importArticles(array $articles): void
    {
        foreach ($articles as $slug => $article) {
            Article::query()->updateOrCreate(['slug' => $slug], [
                'category' => $article['category'],
                'title' => $article['title'],
                'seo_title' => $article['seo_title'] ?? null,
                'lead' => $article['lead'] ?? null,
                'excerpt' => $article['excerpt'] ?? null,
                'published_at' => $article['published_at'] ?? null,
                'reading_time' => $article['reading_time'] ?? null,
                'featured' => (bool) ($article['featured'] ?? false),
                'card_image' => $article['card_image'] ?? null,
                'card_alt' => $article['card_alt'] ?? null,
                'hero_image' => $article['hero_image'] ?? null,
                'hero_alt' => $article['hero_alt'] ?? null,
                'cta_text' => $article['cta_text'] ?? null,
                'body' => $article['body'] ?? null,
                'is_published' => true,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $homeMedia
     */
    protected function importClients(array $homeMedia): void
    {
        foreach ($homeMedia['clients'] as $position => $client) {
            Client::query()->updateOrCreate(
                ['name' => $client['name'], 'path' => $client['path']],
                [
                    'alt' => $client['alt'] ?? $client['name'],
                    'variants' => $client['variants'] ?? null,
                    'position' => $position,
                    'is_visible' => true,
                ]
            );
        }
    }

    /**
     * @param  array<string, mixed>  $homeMedia
     */
    protected function importGallery(array $homeMedia): void
    {
        foreach ($homeMedia['gallery'] as $position => $image) {
            GalleryImage::query()->updateOrCreate(
                ['path' => $image['path']],
                [
                    'full_path' => $image['full'] ?? null,
                    'alt' => $image['alt'] ?? null,
                    'caption' => $image['caption'] ?? null,
                    'width' => $image['width'] ?: null,
                    'height' => $image['height'] ?: null,
                    'variants' => $image['variants'] ?? null,
                    'position' => $position,
                    'is_visible' => true,
                ]
            );
        }
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    protected function importLegacyRedirects(array $catalog): void
    {
        foreach ($catalog['legacy'] as $fromPath => $target) {
            LegacyRedirect::query()->updateOrCreate(['from_path' => $fromPath], [
                'route_name' => $target['route'],
                'parameters' => $target['parameters'] ?? [],
            ]);
        }
    }
}
