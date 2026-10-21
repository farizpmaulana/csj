<?php

use App\Filament\Resources\Articles\Pages\ManageArticles;
use App\Filament\Resources\Brands\Pages\ManageBrands;
use App\Filament\Resources\Clients\Pages\ManageClients;
use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Filament\Resources\LegacyRedirects\Pages\ManageLegacyRedirects;
use App\Filament\Resources\ProductCategories\Pages\ManageProductCategories;
use App\Filament\Resources\Products\Pages\ManageProducts;
use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Models\Article;
use App\Models\Brand;
use App\Models\Client;
use App\Models\GalleryImage;
use App\Models\LegacyRedirect;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    Artisan::call('app:import-catalog');

    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->member = User::factory()->create(['is_admin' => false]);
});

it('redirects guests to the login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('refuses non administrator accounts', function () {
    $this->actingAs($this->member)->get('/admin')->assertForbidden();
});

it('lets an administrator reach every resource page', function (string $page) {
    $this->actingAs($this->admin)->get($page)->assertOk();
})->with([
    '/admin',
    '/admin/brands',
    '/admin/product-categories',
    '/admin/products',
    '/admin/articles',
    '/admin/clients',
    '/admin/gallery-images',
    '/admin/legacy-redirects',
    '/admin/site-settings',
]);

it('creates a client through the panel', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageClients::class)
        ->callAction(CreateAction::class, [
            'name' => 'New Client Ltd',
            'alt' => 'New Client logo',
            'path' => [UploadedFile::fake()->image('new-client.png')],
        ])
        ->assertHasNoFormErrors();

    $client = Client::where('name', 'New Client Ltd')->firstOrFail();

    expect($client->path)->toStartWith('images/clients/')
        ->and(is_file(storage_path('app/public/'.$client->path)))->toBeTrue();
});

it('renames a brand through the panel', function () {
    $this->actingAs($this->admin);
    $brand = Brand::first();

    Livewire::test(ManageBrands::class)
        ->callTableAction(EditAction::class, $brand, ['name' => 'Renamed Partner'])
        ->assertHasNoTableActionErrors();

    expect($brand->fresh()->name)->toBe('Renamed Partner');
});

it('deletes a client through the panel', function () {
    $this->actingAs($this->admin);
    $client = Client::first();

    Livewire::test(ManageClients::class)
        ->callTableAction(DeleteAction::class, $client)
        ->assertHasNoTableActionErrors();

    expect(Client::whereKey($client->getKey())->exists())->toBeFalse();
});

it('unpublishes an article through the panel', function () {
    $this->actingAs($this->admin);
    $article = Article::first();

    Livewire::test(ManageArticles::class)
        ->callTableAction(EditAction::class, $article, [
            'title' => $article->title,
            'slug' => $article->slug,
            'category' => $article->category->value,
            'is_published' => false,
        ])
        ->assertHasNoTableActionErrors();

    expect($article->fresh()->is_published)->toBeFalse();
});

it('saves the site settings through the panel', function () {
    $this->actingAs($this->admin);

    Livewire::test(EditSiteSetting::class)
        ->fillForm([
            'company_name' => 'PT Cahaya Sarange Jaya',
            'contact_email' => 'hello@example.test',
            'whatsapp_number' => '628000000000',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(SiteSetting::current()->contact_email)->toBe('hello@example.test');
});

it('drops responsive variants when a gallery image is replaced', function () {
    $this->actingAs($this->admin);
    $image = GalleryImage::whereNotNull('variants')->firstOrFail();

    expect($image->variants)->not->toBeEmpty();

    Livewire::test(ManageGalleryImages::class)
        ->callTableAction(EditAction::class, $image, [
            'path' => [UploadedFile::fake()->image('replacement.jpg')],
            'full_path' => [$image->full_path],
            'alt' => $image->alt,
            'caption' => $image->caption,
            'position' => $image->position,
            'is_visible' => true,
        ])
        ->assertHasNoTableActionErrors();

    expect($image->fresh()->variants)->toBeNull()
        ->and($image->fresh()->path)->not->toBe($image->path);
});

it('keeps variants when a gallery image is only re-captioned', function () {
    $this->actingAs($this->admin);
    $image = GalleryImage::whereNotNull('variants')->firstOrFail();
    $variants = $image->variants;

    Livewire::test(ManageGalleryImages::class)
        ->callTableAction(EditAction::class, $image, [
            'path' => [$image->path],
            'full_path' => [$image->full_path],
            'alt' => $image->alt,
            'caption' => 'A new caption',
            'position' => $image->position,
            'is_visible' => true,
        ])
        ->assertHasNoTableActionErrors();

    expect($image->fresh()->variants)->toBe($variants)
        ->and($image->fresh()->caption)->toBe('A new caption');
});

it('shows products, articles, clients and redirects in the panel tables', function (string $resource, int $expected) {
    $this->actingAs($this->admin);

    $page = match ($resource) {
        'products' => ManageProducts::class,
        'articles' => ManageArticles::class,
        'clients' => ManageClients::class,
        'legacy-redirects' => ManageLegacyRedirects::class,
    };

    expect(Livewire::test($page)->instance()->getTableRecords()->total())->toBe($expected);
})->with([
    ['products', 23],
    ['articles', 7],
    ['clients', 17],
    ['legacy-redirects', 37],
]);

it('resolves a legacy redirect destination from the panel', function () {
    $redirect = LegacyRedirect::firstOrFail();

    expect($redirect->destinationUrl())->toBe(route($redirect->route_name, $redirect->parameters));
});

it('creates a partner through the panel and links it to its own catalog page', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageBrands::class)
        ->callAction(CreateAction::class, [
            'name' => 'Makita Indonesia',
            'slug' => 'makita',
            'intro' => 'Power tools partner.',
            'card_image' => [UploadedFile::fake()->image('makita.png')],
            'position' => 0,
            'is_visible' => true,
        ])
        ->assertHasNoFormErrors();

    $brand = Brand::where('slug', 'makita')->firstOrFail();

    expect($brand->exists)->toBeTrue()
        ->and(is_file(storage_path('app/public/'.$brand->card_image)))->toBeTrue();

    $this->get(route('products.brand', ['brand' => 'makita']))
        ->assertOk()
        ->assertSee('Makita Indonesia');
});

/**
 * A partner created in the panel starts with no categories, and the partner page
 * only knows how to list products through categories. This is the whole content
 * workflow: partner, then category, then product.
 */
it('takes a partner created in the panel all the way to a published product', function () {
    $this->actingAs($this->admin);

    Livewire::test(ManageBrands::class)
        ->callAction(CreateAction::class, [
            'name' => 'Makita Indonesia',
            'slug' => 'makita',
            'position' => 0,
            'is_visible' => true,
        ])
        ->assertHasNoFormErrors();

    $brand = Brand::where('slug', 'makita')->firstOrFail();

    Livewire::test(ManageProductCategories::class)
        ->callAction(CreateAction::class, [
            'brand_id' => $brand->getKey(),
            'name' => 'Power Tools',
            'position' => 0,
        ])
        ->assertHasNoFormErrors();

    $category = ProductCategory::where('brand_id', $brand->getKey())->firstOrFail();

    Livewire::test(ManageProducts::class)
        ->callAction(CreateAction::class, [
            'brand_id' => $brand->getKey(),
            'product_category_id' => $category->getKey(),
            'name' => 'Makita Hammer',
            'slug' => 'makita-hammer',
            'position' => 0,
            'is_visible' => true,
        ])
        ->assertHasNoFormErrors();

    expect(Product::where('slug', 'makita-hammer')->firstOrFail()->product_category_id)
        ->toBe($category->getKey());

    $this->get(route('products.brand', ['brand' => 'makita']))
        ->assertOk()
        ->assertSee('Power Tools')
        ->assertSee('Makita Hammer');
});

it('rejects a duplicate category name within one partner but allows it across partners', function () {
    $this->actingAs($this->admin);
    $brand = Brand::where('slug', 'bosch')->firstOrFail();
    $other = Brand::where('slug', 'sumato')->firstOrFail();

    $category = ProductCategory::where('brand_id', $brand->getKey())->firstOrFail();

    Livewire::test(ManageProductCategories::class)
        ->callAction(CreateAction::class, [
            'brand_id' => $brand->getKey(),
            'name' => $category->name,
            'position' => 0,
        ])
        ->assertHasFormErrors(['name' => 'unique']);

    Livewire::test(ManageProductCategories::class)
        ->callAction(CreateAction::class, [
            'brand_id' => $other->getKey(),
            'name' => $category->name,
            'position' => 0,
        ])
        ->assertHasNoFormErrors();
});

/**
 * Nothing stops a stale category id from another partner being written onto a
 * product, which would file it under the wrong partner on the public site.
 */
it('clears the category when the partner is changed on a product', function () {
    $this->actingAs($this->admin);
    $brand = Brand::where('slug', 'bosch')->firstOrFail();
    $other = Brand::where('slug', 'sumato')->firstOrFail();
    $category = ProductCategory::where('brand_id', $brand->getKey())->firstOrFail();

    $page = Livewire::test(ManageProducts::class)
        ->mountAction('create')
        ->set('mountedActions.0.data.brand_id', $brand->getKey())
        ->set('mountedActions.0.data.product_category_id', $category->getKey());

    expect($page->get('mountedActions.0.data.product_category_id'))->toBe($category->getKey());

    $page->set('mountedActions.0.data.brand_id', $other->getKey());

    expect($page->get('mountedActions.0.data.product_category_id'))->toBeNull();
});

it('creates a product under a partner with a multiple image gallery', function () {
    $this->actingAs($this->admin);
    $brand = Brand::where('slug', 'bosch')->firstOrFail();

    Livewire::test(ManageProducts::class)
        ->callAction(CreateAction::class, [
            'brand_id' => $brand->getKey(),
            'name' => 'Bosch Corded Drill X',
            'slug' => 'bosch-corded-drill-x',
            'images' => [
                ['path' => [UploadedFile::fake()->image('drill-front.jpg')], 'alt' => 'Front view'],
                ['path' => [UploadedFile::fake()->image('drill-side.jpg')], 'alt' => 'Side view'],
                ['path' => [UploadedFile::fake()->image('drill-case.jpg')], 'alt' => 'In its case'],
            ],
            'position' => 0,
            'is_visible' => true,
        ])
        ->assertHasNoFormErrors();

    $product = Product::where('slug', 'bosch-corded-drill-x')->firstOrFail();

    expect($product->brand_id)->toBe($brand->getKey())
        ->and($product->images)->toHaveCount(3)
        ->and($product->images->pluck('alt')->all())->toBe(['Front view', 'Side view', 'In its case'])
        ->and($product->images->pluck('position')->all())->toBe([1, 2, 3]);

    foreach ($product->images as $image) {
        expect(is_file(storage_path('app/public/'.$image->path)))->toBeTrue();
    }

    $this->get(route('products.show', [
        'brand' => 'bosch',
        'product' => 'bosch-corded-drill-x',
    ]))->assertOk()->assertSee('Bosch Corded Drill X');
});

/**
 * Repeats the create flow through the repeater "add" buttons the way the panel
 * does in the browser, so the gallery, the specification sections and the card
 * image are all built by the same interaction chain an editor performs.
 */
it('builds a product gallery and specifications through the repeater add buttons', function () {
    $this->actingAs($this->admin);
    $brand = Brand::where('slug', 'sumato')->firstOrFail();

    $page = Livewire::test(ManageProducts::class)->mountAction('create');

    $schema = $page->instance()->getMountedActionSchemaName();

    $page->set('mountedActions.0.data.brand_id', $brand->getKey())
        ->set('mountedActions.0.data.name', 'Sumato Widget')
        ->set('mountedActions.0.data.slug', 'sumato-widget')
        ->set('mountedActions.0.data.position', 0)
        ->set('mountedActions.0.data.is_visible', true);

    $page->callFormComponentAction('images', 'add', formName: $schema)->callMountedAction();
    $imageKey = array_key_first(data_get($page->get('mountedActions.0.data'), 'images'));
    $page->set('mountedActions.0.data.images.'.$imageKey.'.alt', 'Slider');

    $page->callFormComponentAction('sections', 'add', formName: $schema)->callMountedAction();
    $sectionKey = array_key_first(data_get($page->get('mountedActions.0.data'), 'sections'));
    $page->set('mountedActions.0.data.sections.'.$sectionKey.'.title', 'Dimensions');

    $page->callFormComponentAction("sections.{$sectionKey}.rows", 'add', formName: $schema)->callMountedAction();
    $rowKey = array_key_first(data_get($page->get('mountedActions.0.data'), "sections.{$sectionKey}.rows"));
    $page->set("mountedActions.0.data.sections.{$sectionKey}.rows.{$rowKey}.label", 'Height')
        ->set("mountedActions.0.data.sections.{$sectionKey}.rows.{$rowKey}.value", '30 cm');

    $data = $page->get('mountedActions.0.data');
    $data['card_image'] = [UploadedFile::fake()->image('widget-card.png')];
    $data['images'][$imageKey]['path'] = [UploadedFile::fake()->image('widget-slider.png')];

    $page->call('disableSchemaStateUpdateHooksForTesting');

    foreach (Arr::dot($data) as $path => $value) {
        if ($value instanceof UploadedFile
            || (is_array($value) && isset($value[0]) && $value[0] instanceof UploadedFile)
        ) {
            $page->set('mountedActions.0.data.'.$path, $value);

            Arr::set($data, $path, $page->get('mountedActions.0.data.'.$path));
        }
    }

    $page->call('enableSchemaStateUpdateHooksForTesting');

    $page->set('mountedActions.0.data', $data)
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $product = Product::where('slug', 'sumato-widget')->firstOrFail();

    expect($product->brand_id)->toBe($brand->getKey())
        ->and($product->card_image)->not->toBeNull()
        ->and($product->images)->toHaveCount(1)
        ->and($product->images->first()->alt)->toBe('Slider')
        ->and($product->sections)->toHaveCount(1)
        ->and($product->sections->first()->title)->toBe('Dimensions')
        ->and($product->sections->first()->rows)->toHaveCount(1)
        ->and($product->sections->first()->rows->first()->value)->toBe('30 cm');
});

/**
 * The product image repeater is replaced as a whole when the panel saves, so its
 * state is set directly instead of merged into the mounted action form data.
 * Uploads are registered first, the same way `fillForm()` does it internally.
 *
 * @param  array<string, array<string, mixed>>  $images
 */
function editProductImages(Product $product, array $images): Testable
{
    $component = Livewire::test(ManageProducts::class)
        ->mountTableAction(EditAction::class, $product);

    $state = ['images' => $images];

    $component->call('disableSchemaStateUpdateHooksForTesting');

    foreach (Arr::dot($state) as $path => $value) {
        if ($value instanceof UploadedFile
            || (is_array($value) && isset($value[0]) && $value[0] instanceof UploadedFile)
        ) {
            $component->set('mountedActions.0.data.'.$path, $value);

            Arr::set($state, $path, $component->get('mountedActions.0.data.'.$path));
        }
    }

    $component->call('enableSchemaStateUpdateHooksForTesting');

    return $component
        ->set('mountedActions.0.data.images', $state['images'])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();
}

it('removes a single product image and renumbers the rest', function () {
    $this->actingAs($this->admin);
    $product = Product::whereHas('images')->firstOrFail();
    $original = $product->images->all();

    expect($original)->toHaveCount(2);

    $kept = $original[1];

    editProductImages($product, [
        'record-'.$kept->getKey() => ['path' => [$kept->path], 'alt' => $kept->alt],
    ]);

    $images = $product->fresh()->images;

    expect($images)->toHaveCount(1)
        ->and($images->first()->getKey())->toBe($kept->getKey())
        ->and($images->first()->position)->toBe(1);
});

it('appends a new image to an existing product slider', function () {
    $this->actingAs($this->admin);
    $product = Product::whereHas('images')->firstOrFail();
    $original = $product->images->all();

    editProductImages($product, [
        'record-'.$original[0]->getKey() => ['path' => [$original[0]->path], 'alt' => $original[0]->alt],
        'record-'.$original[1]->getKey() => ['path' => [$original[1]->path], 'alt' => $original[1]->alt],
        'new-image' => ['path' => [UploadedFile::fake()->image('added.jpg')], 'alt' => 'Added later'],
    ]);

    $images = $product->fresh()->images;

    expect($images)->toHaveCount(3)
        ->and($images->pluck('position')->all())->toBe([1, 2, 3])
        ->and($images->last()->alt)->toBe('Added later')
        ->and(is_file(storage_path('app/public/'.$images->last()->path)))->toBeTrue();
});

it('deletes a product through the panel', function () {
    $this->actingAs($this->admin);
    $product = Product::whereHas('images')->firstOrFail();

    Livewire::test(ManageProducts::class)
        ->callTableAction(DeleteAction::class, $product)
        ->assertHasNoTableActionErrors();

    expect(Product::whereKey($product->getKey())->exists())->toBeFalse()
        ->and($product->images()->count())->toBe(0);
});

it('deletes a partner and its products together', function () {
    $this->actingAs($this->admin);
    $brand = Brand::whereHas('products')->firstOrFail();
    $productIds = $brand->products()->pluck('id');

    Livewire::test(ManageBrands::class)
        ->callTableAction(DeleteAction::class, $brand)
        ->assertHasNoTableActionErrors();

    expect(Brand::whereKey($brand->getKey())->exists())->toBeFalse()
        ->and(Product::whereIn('id', $productIds)->count())->toBe(0);
});

it('lists every product of a partner in the panel', function () {
    $this->actingAs($this->admin);

    $brand = Brand::where('slug', 'sumato')->firstOrFail();

    expect($brand->products()->count())
        ->toBe(Product::where('brand_id', $brand->getKey())->count());
});
