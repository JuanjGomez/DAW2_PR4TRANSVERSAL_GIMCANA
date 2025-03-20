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
        ]);

        Gimcana::create([
            'name' => 'Gimcana 2',
            'description' => 'Gimcana 2',
        ]);

    }
}
