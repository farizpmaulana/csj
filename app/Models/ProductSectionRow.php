<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_section_id',
    'label',
    'value',
    'position',
])]
class ProductSectionRow extends Model
{
    /** @use HasFactory<ProductSectionRowFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<ProductSection, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(ProductSection::class, 'product_section_id');
    }
}
