<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Competition;
use App\Models\Challenge;
use App\Models\Group;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $challenges = Challenge::all();

        foreach ($challenges as $challenge) {
            Competition::create([
                'name' => "Competición de " . $challenge->name,
                'challenge_id' => $challenge->id,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'status' => 'pending'
            ]);
        }
    }
}
