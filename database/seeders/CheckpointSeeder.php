<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Checkpoint;
class CheckpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Checkpoint::create([
            'place_id' => 1,
            'gimcana_id' => 1,
            'challenge' => 'Challenge 1',
            'clue' => 'Clue 1',
            'order' => 1,
        ]);

        Checkpoint::create([
            'place_id' => 2,
            'gimcana_id' => 1,
            'challenge' => 'Challenge 2',
            'clue' => 'Clue 2',
            'order' => 2,
        ]);

        Checkpoint::create([
            'place_id' => 3,
            'gimcana_id' => 1,
            'challenge' => 'Challenge 3',
            'clue' => 'Clue 3',
            'order' => 3,
        ]);
    }
}
