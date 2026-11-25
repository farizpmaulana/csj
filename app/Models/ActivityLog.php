<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'subject_type',
        'subject_id',
        'subject_label',
        'changes',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function formatSubjectType(string $subjectType): string
    {
        return match ($subjectType) {
            Brand::class => 'Partner',
            Product::class => 'Product',
            ProductCategory::class => 'Product Category',
            Article::class => 'Article',
            Client::class => 'Client',
            GalleryImage::class => 'Gallery Image',
            ShowcaseProduct::class => 'Product Showcase',
            SiteSetting::class => 'Site Setting',
            LegacyRedirect::class => 'Legacy Redirect',
            User::class => 'User',
            default => class_basename($subjectType),
        };
    }
}
