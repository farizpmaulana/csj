<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'brand_id',
    'product_category_id',
    'slug',
    'name',
    'subtitle',
    'seo_title',
    'excerpt',
    'description',
    'card_image',
    'card_alt',
    'card_label',
    'whatsapp_message',
    'position',
    'is_visible',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return BelongsTo<ProductCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position')->orderBy('id');
    }

    /**
     * @return HasMany<ProductSection, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(ProductSection::class)->orderBy('position')->orderBy('id');
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('name');
    }

    /**
     * A ready to use wa.me link for this product's enquiry.
     */
    protected function whatsappLink(): Attribute
    {
        return Attribute::get(fn (): ?string => wa_link($this->whatsapp_message));
    }

    /**
     * The public address of this product, which is scoped by its brand.
     */
    protected function url(): Attribute
    {
        return Attribute::get(fn (): string => route('products.show', [
            'brand' => $this->brand->slug,
            'product' => $this->slug,
        ]));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
