<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Place;
use App\Models\Tag;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = [
            [
                'name' => 'Parque Central',
                'latitude' => 41.390205,
                'longitude' => 2.154007,
                'description' => 'Un hermoso parque en el centro de la ciudad',
                'icon' => 'park'
            ],
            [
                'name' => 'Museo de Historia',
                'latitude' => 41.385058,
                'longitude' => 2.173035,
                'description' => 'Museo histórico principal de la ciudad',
                'icon' => 'museum'
            ],
            [
                'name' => 'Plaza Mayor',
                'latitude' => 41.387147,
                'longitude' => 2.170047,
                'description' => 'Plaza histórica principal',
                'icon' => 'plaza'
            ]
        ];

        foreach ($places as $place) {
            $newPlace = Place::create($place);

            // Asignar tags aleatorios a cada lugar
            $tags = Tag::inRandomOrder()->take(rand(1, 3))->get();
            $newPlace->tags()->attach($tags);
        }
    }
}
