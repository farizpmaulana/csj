<?php

namespace App\Console\Commands;

use App\Models\SiteSetting;
use Illuminate\Console\Command;

class ExportSiteSettingsCommand extends Command
{
    protected $signature = 'app:export-site-settings';

    protected $description = 'Export current site settings from the database into database/seed-data/site-settings.php';

    public function handle(): int
    {
        $settings = SiteSetting::current();

        if (! $settings) {
            $this->components->error('No SiteSetting record found in database.');

            return self::FAILURE;
        }

        $data = [
            'company_name' => $settings->company_name,
            'logo_path' => $settings->logo_path,
            'favicon_path' => $settings->favicon_path,
            'whatsapp_number' => $settings->whatsapp_number,
            'contact_phone' => $settings->contact_phone,
            'contact_email' => $settings->contact_email,
            'address_primary' => $settings->address_primary,
            'address_secondary' => $settings->address_secondary,
            'instagram_url' => $settings->instagram_url,
            'facebook_url' => $settings->facebook_url,
            'linkedin_url' => $settings->linkedin_url,
            'default_social_image' => $settings->default_social_image,
            'about_tagline' => $settings->about_tagline,
            'about_description' => $settings->about_description,
            'vision_text' => $settings->vision_text,
            'mission_items' => $settings->mission_items ?? [],
        ];

        $export = "<?php\n\ndeclare(strict_types=1);\n\nreturn ".var_export($data, true).";\n";

        file_put_contents(database_path('seed-data/site-settings.php'), $export);

        $this->components->info('Site settings successfully exported to database/seed-data/site-settings.php.');

        return self::SUCCESS;
    }
}
