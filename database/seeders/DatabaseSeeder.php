<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);

        $this->command?->info('Importing catalog, media, articles, and site settings...');
        Artisan::call('app:import-catalog', ['--fresh' => true], $this->command?->getOutput());
    }
}
