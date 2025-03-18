<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        User::create([
            'nombre' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('asdASD123'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'nombre' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('asdASD123'),
            'role_id' => $userRole->id,
        ]);

        for ($i = 1; $i <= 8; $i++) {
            User::create([
                'nombre' => 'User ' . $i,
                'email' => 'user' . $i . '@gmail.com',
                'password' => Hash::make('asdASD123'),
                'role_id' => $userRole->id,
            ]);
        }
    }
}
