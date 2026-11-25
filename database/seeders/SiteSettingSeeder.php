<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Seed the site settings.
     */
    public function run(): void
    {
        $settingsFile = database_path('seed-data/site-settings.php');

        $data = is_file($settingsFile)
            ? require $settingsFile
            : [];

        if (! empty($data)) {
            SiteSetting::query()->updateOrCreate([], $data);
        }
    }
}
