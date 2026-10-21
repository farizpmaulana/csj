<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'path',
    'alt',
    'variants',
    'position',
    'is_visible',
])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;

    /**
     * @param  Builder<Client>  $query
     * @return Builder<Client>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /**
     * @param  Builder<Client>  $query
     * @return Builder<Client>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'variants' => 'array',
        ];
    }

    public function url(): ?string
    {
        return media($this->path);
    }

    /**
     * The srcset for the logo image.
     */
    public function variantSrcset(): ?string
    {
        return Media::srcset($this->variants);
    }
}
