<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Place;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        Place::create([
            'name' => 'Plaza Mayor',
            'address' => 'Plaza Mayor, Madrid',
            'latitude' => 40.415363,
            'longitude' => -3.707398,
            'icon' => 'icon-plaza',
        ]);

        Place::create([
            'name' => 'Catedral de la Almudena',
            'address' => 'Calle de Bailén, 10, Madrid',
            'latitude' => 40.415028,
            'longitude' => -3.714167,
            'icon' => 'icon-cathedral',
        ]);
    }
} 