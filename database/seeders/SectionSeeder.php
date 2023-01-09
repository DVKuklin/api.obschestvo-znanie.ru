<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sections')->insert([
            [
                'name' => 'Человек и общество',
                'url' => 'section_1',
                'sort' => 1,
                'image' => '/myfiles/01_chel_and_obsch_red.webp',
                'imagehover' => '/myfiles/01_chel_and_obsch_blue.webp'
            ],
            [
                'name' => 'Экономика',
                'url' => 'section_2',
                'sort' => 2,
                'image' => '/myfiles/02_econ_red.webp',
                'imagehover' => '/myfiles/02_econ_blue.webp'                
            ]
        ]);
    }
}
