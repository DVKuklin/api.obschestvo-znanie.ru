<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Theme::insert([
            [
                'name'=>'Человек. Природное и общественное в человеке',
                'url'=>'them_01',
                'section'=>1,
                'sort'=>1,
                'image'=>'assets/myfiles/images/i.webp'
            ],
            [
                'name'=>'Духовный мир человека и Мировоззрение',
                'url'=>'them_02',
                'section'=>1,
                'sort'=>2,
                'image'=>'assets/myfiles/images/i.webp'
            ],
            [
                'name'=>'Мышление и деятельность',
                'url'=>'them_03',
                'section'=>1,
                'sort'=>3,
                'image'=>'assets/myfiles/images/i.webp'
            ],


            [
                'name'=>'Экономика и экономическая наука',
                'url'=>'them_01',
                'section'=>2,
                'sort'=>1,
                'image'=>'assets/myfiles/images/i.webp'
            ],
            [
                'name'=>'Производство и производительность труда. Разделение труда и специализация',
                'url'=>'them_02',
                'section'=>2,
                'sort'=>2,
                'image'=>'assets/myfiles/images/i.webp'
            ],

        ]);
    }
}
