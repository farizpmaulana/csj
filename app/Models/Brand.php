<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'name',
    'headline',
    'intro',
    'seo_title',
    'card_image',
    'card_alt',
    'position',
    'is_visible',
])]
class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use HasFactory;

    /**
     * @return HasMany<ProductCategory, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class)->orderBy('position')->orderBy('name');
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('position')->orderBy('name');
    }

    /**
     * Products that are not filed under any category, so the partner page can
     * still list them instead of dropping them on the floor.
     *
     * @return HasMany<Product, $this>
     */
    public function uncategorisedProducts(): HasMany
    {
        return $this->products()->whereNull('product_category_id');
    }

    /**
     * @param  Builder<Brand>  $query
     * @return Builder<Brand>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /**
     * @param  Builder<Brand>  $query
     * @return Builder<Brand>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
