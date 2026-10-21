<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'product_id',
    'path',
    'alt',
    'position',
])]
class ProductImage extends Model
{
    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function url(): ?string
    {
        return media($this->path);
    }

    public function fileExists(): bool
    {
        return filled($this->path) && Storage::disk(Media::DISK)->exists($this->path);
    }
}
