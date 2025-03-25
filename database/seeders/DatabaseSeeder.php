<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            GimcanaSeeder::class,
            PlaceSeeder::class,
            GroupSeeder::class,
            CheckpointSeeder::class,
            UserSeeder::class,
        ]);
    }
} 