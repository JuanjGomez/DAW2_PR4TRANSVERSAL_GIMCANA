<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Challenge;
use App\Models\User;
use App\Models\Place;
use App\Models\Checkpoint;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $places = Place::all();

        // Crear algunos desafíos de ejemplo
        for ($i = 1; $i <= 3; $i++) {
            $challenge = Challenge::create([
                'name' => "Desafío " . $i,
                'description' => "Descripción del desafío " . $i,
                'user_id' => $users->random()->id
            ]);

            // Crear checkpoints para cada desafío
            foreach ($places as $index => $place) {
                Checkpoint::create([
                    'challenge_id' => $challenge->id,
                    'place_id' => $place->id,
                    'clue' => "Pista para encontrar el lugar " . ($index + 1),
                    'test' => "Prueba a superar en el checkpoint " . ($index + 1),
                    'order' => $index + 1
                ]);
            }
        }
    }
}
