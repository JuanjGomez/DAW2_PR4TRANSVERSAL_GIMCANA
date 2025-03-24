<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un usuario administrador
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // Cambia esto por una contraseña segura
            'role_id' => 1, // Asume que el rol de administrador tiene ID 1
        ]);

        // Crear usuarios normales
        User::create([
            'name' => 'Usuario 1',
            'email' => 'usuario1@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2, // Asume que el rol de usuario tiene ID 2
        ]);

        User::create([
            'name' => 'Usuario 2',
            'email' => 'usuario2@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2,
        ]);

        User::create([
            'name' => 'Usuario 3',
            'email' => 'usuario3@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2,
        ]);
    }
} 