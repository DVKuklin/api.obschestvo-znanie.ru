<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\SectionSeeder;
use Database\Seeders\ThemeSeeder;
use Database\Seeders\ParagraphSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(SectionSeeder::class);
        $this->call(ThemeSeeder::class);
        $this->call(ParagraphSeeder::class);
    }
}
