<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use App\Models\Challenge;
use App\Models\Competition;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $challenges = Challenge::all();
        $competitions = Competition::all();

        foreach ($challenges as $challenge) {
            // Crear 2 grupos por desafío
            for ($i = 1; $i <= 2; $i++) {
                $group = Group::create([
                    'name' => "Grupo " . $i . " - " . $challenge->name,
                    'user_id' => $users->random()->id,
                    'challenge_id' => $challenge->id
                ]);

                // Agregar miembros al grupo (máximo 2 miembros ya que solo tenemos 2 usuarios)
                $memberCount = min(2, $users->count() - 1); // Restamos 1 para excluir al creador
                $members = $users->random($memberCount);
                foreach ($members as $member) {
                    $group->members()->attach($member, ['joined_at' => now()]);
                }

                // Asociar grupo a la competición correspondiente
                $competition = $competitions->where('challenge_id', $challenge->id)->first();
                if ($competition) {
                    $group->competitions()->attach($competition->id);
                }
            }
        }
    }
}
