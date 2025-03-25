<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gimcana;

class GimcanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gimcana::create([
            'name' => 'Gimcana Histórica',
            'description' => 'Descubre los monumentos más importantes de la ciudad',
            'max_groups' => 5,
            'max_users_per_group' => 4,
            'status' => 'waiting',
        ]);

        Gimcana::create([
            'name' => 'Gimcana Gastronómica',
            'description' => 'Prueba los platos más típicos de la región',
            'max_groups' => 3,
            'max_users_per_group' => 6,
            'status' => 'active',
        ]);
    }
}
