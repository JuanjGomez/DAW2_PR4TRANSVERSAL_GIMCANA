<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Checkpoint;

class CheckpointSeeder extends Seeder
{
    public function run(): void
    {
        Checkpoint::create([
            'gimcana_id' => 1,
            'place_id' => 1,
            'challenge' => 'Resuelve el acertijo sobre la historia de la plaza',
            'clue' => 'Busca la estatua del rey',
            'order' => 1,
        ]);

        Checkpoint::create([
            'gimcana_id' => 1,
            'place_id' => 2,
            'challenge' => 'Encuentra el año de construcción',
            'clue' => 'Mira en la fachada principal',
            'order' => 2,
        ]);
    }
} 