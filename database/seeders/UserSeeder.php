<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kollej.uz',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Davomat oluvchi users
        User::create([
            'name' => 'Aliyev Vali',
            'email' => 'davomat1@kollej.uz',
            'password' => Hash::make('davomat123'),
            'role' => 'davomat_oluvchi',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Karimova Madina',
            'email' => 'davomat2@kollej.uz',
            'password' => Hash::make('davomat123'),
            'role' => 'davomat_oluvchi',
            'is_active' => true,
        ]);

        // Koruvchi user
        User::create([
            'name' => 'Rahimov Tohir',
            'email' => 'koruvchi@kollej.uz',
            'password' => Hash::make('koruvchi123'),
            'role' => 'koruvchi',
            'is_active' => true,
        ]);
    }
}
