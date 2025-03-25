<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            ['name' => 'Histórico'],
            ['name' => 'Cultural'],
            ['name' => 'Naturaleza'],
            ['name' => 'Deportivo'],
            ['name' => 'Educativo'],
            ['name' => 'Entretenimiento'],
            ['name' => 'Gastronomía'],
            ['name' => 'Arte'],
            ['name' => 'Música'],
            ['name' => 'Ciencia']
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
} 