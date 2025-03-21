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
            'name' => 'Gimcana 1',
            'description' => 'Gimcana 1',
            'max_groups' => 10,
            'max_users_per_group' => 10,
            'status' => 'waiting',
        ]);

        Gimcana::create([
            'name' => 'Gimcana 2',
            'description' => 'Gimcana 2',
            'max_groups' => 10,
            'max_users_per_group' => 10,
            'status' => 'waiting',
        ]);

    }
}
