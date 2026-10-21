<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'from_path',
    'route_name',
    'parameters',
])]
class LegacyRedirect extends Model
{
    /** @use HasFactory<LegacyRedirectFactory> */
    use HasFactory;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'parameters' => 'array',
        ];
    }

    /**
     * The absolute address this legacy path should be redirected to.
     *
     * Named to avoid colliding with the model, because a plain `destination()`
     * method would be mistaken for a relationship by Eloquent.
     */
    public function destinationUrl(): string
    {
        return route($this->route_name, $this->parameters ?? []);
    }
}
