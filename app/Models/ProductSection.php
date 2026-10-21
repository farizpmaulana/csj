<?php

namespace App\Models;

use App\Enums\ProductSectionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'product_id',
    'title',
    'type',
    'position',
])]
class ProductSection extends Model
{
    /** @use HasFactory<ProductSectionFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return HasMany<ProductSectionRow, $this>
     */
    public function rows(): HasMany
    {
        return $this->hasMany(ProductSectionRow::class)->orderBy('position')->orderBy('id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ProductSectionType::class,
        ];
    }
}
