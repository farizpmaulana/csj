<?php

namespace App\Models;

use App\Enums\ArticleCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'slug',
    'category',
    'title',
    'seo_title',
    'lead',
    'excerpt',
    'published_at',
    'reading_time',
    'featured',
    'card_image',
    'card_alt',
    'hero_image',
    'hero_alt',
    'cta_text',
    'body',
    'is_published',
])]
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeNewestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ArticleCategory::class,
            'published_at' => 'date',
            'featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected function publishedLabel(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->published_at?->format('F j, Y'));
    }

    protected function categoryLabel(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->category?->label());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
