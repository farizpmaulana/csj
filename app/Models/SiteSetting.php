<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_name',
    'logo_path',
    'favicon_path',
    'whatsapp_number',
    'contact_phone',
    'contact_email',
    'address_primary',
    'address_secondary',
    'instagram_url',
    'facebook_url',
    'linkedin_url',
    'default_social_image',
])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    /**
     * The single settings row, created on first use.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'company_name' => 'PT Cahaya Sarange Jaya',
        ]);
    }

    public function whatsappLink(?string $message = null): ?string
    {
        return wa_link($message);
    }
}
