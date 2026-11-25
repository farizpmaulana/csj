<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShowcaseProduct extends Model
{
    use Concerns\LogsActivity, HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'position',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (ShowcaseProduct $item): void {
            if (! isset($item->position) || $item->position === 0) {
                $item->position = ((int) static::max('position')) + 1;
            }
        });

        static::saved(function (ShowcaseProduct $item): void {
            $item->product?->updateQuietly([
                'is_showcase' => true,
                'showcase_position' => $item->position,
            ]);
        });

        static::deleted(function (ShowcaseProduct $item): void {
            $item->product?->updateQuietly([
                'is_showcase' => false,
                'showcase_position' => 0,
            ]);
        });
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
