<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'path',
    'full_path',
    'alt',
    'caption',
    'width',
    'height',
    'variants',
    'position',
    'is_visible',
])]
class GalleryImage extends Model
{
    /** @use HasFactory<GalleryImageFactory> */
    use HasFactory;

    /**
     * @param  Builder<GalleryImage>  $query
     * @return Builder<GalleryImage>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /**
     * @param  Builder<GalleryImage>  $query
     * @return Builder<GalleryImage>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /**
     * The seeded images ship with hand made AVIF and WebP variants. Replacing an
     * image invalidates them, so they are dropped rather than left pointing at
     * the previous file. Explicitly provided variants, such as the ones the
     * catalog importer writes, are always kept.
     */
    protected static function booted(): void
    {
        static::saving(function (GalleryImage $image): void {
            if ($image->isDirty('variants')) {
                return;
            }

            if ($image->isDirty('path') || $image->isDirty('full_path')) {
                $image->variants = null;
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'variants' => 'array',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function url(): ?string
    {
        return media($this->path);
    }

    /**
     * The large image opened by the zoom button, falling back to the card image.
     */
    public function fullUrl(): ?string
    {
        return media($this->full_path) ?? $this->url();
    }

    /**
     * The srcset for the <img> fallback.
     */
    public function variantSrcset(): ?string
    {
        return Media::srcset($this->variants['fallback'] ?? null);
    }

    /**
     * The <source> elements for the picture, keyed by MIME type.
     *
     * @return array<string, string>
     */
    public function sourceSrcsets(): array
    {
        $srcsets = [];

        foreach ($this->variants['sources'] ?? [] as $type => $candidates) {
            $srcset = Media::srcset($candidates);

            if ($srcset !== null) {
                $srcsets[$type] = $srcset;
            }
        }

        return $srcsets;
    }
}
