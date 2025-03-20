<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Place;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Place::create([
            'name' => 'Plaza de la Constitución',
            'address' => 'Plaza de la Constitución, 1',
            'latitude' => 37.389089,
            'longitude' => -5.984641,
            'icon' => 'https://example.com/icon.png',
        ]);
    }
}
