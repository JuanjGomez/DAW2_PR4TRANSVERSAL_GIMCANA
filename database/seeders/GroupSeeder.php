<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        Group::create([
            'name' => 'Exploradores',
            'current_checkpoint' => 1,
            'gimcana_id' => 1,
        ]);

        Group::create([
            'name' => 'Aventureros',
            'current_checkpoint' => 2,
            'gimcana_id' => 1,
        ]);
    }
} 