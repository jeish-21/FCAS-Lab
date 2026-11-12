<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Sample Student Account
        User::create([
            'name'        => 'Juan Dela Cruz',
            'student_id'  => '2024-00123',
            'course'      => 'BSIT',
            'department'  => null,
            'designation' => null,
            'email'       => 'juan.delacruz@ssct.edu.ph',
            'password'    => Hash::make('password123'),
            'role'        => 'student',
        ]);

        // 2. Sample Faculty Account
        User::create([
            'name'        => 'Prof. Maria Santos',
            'student_id'  => null,
            'course'      => null,
            'department'  => 'CEIT',
            'designation' => 'Assistant Professor',
            'email'       => 'maria.santos@ssct.edu.ph',
            'password'    => Hash::make('password123'),
            'role'        => 'faculty',
        ]);

        // 3. Sample Admin Account
        User::create([
            'name'        => 'System Administrator',
            'student_id'  => null,
            'course'      => null,
            'department'  => 'CEIT',
            'designation' => 'Department Admin',
            'email'       => 'admin@ssct.edu.ph',
            'password'    => Hash::make('password123'),
            'role'        => 'admin',
        ]);

        // 4. Sample SuperAdmin Account
        User::create([
            'name'        => 'Super Administrator',
            'student_id'  => null,
            'course'      => null,
            'department'  => null,
            'designation' => 'System Director',
            'email'       => 'superadmin@ssct.edu.ph',
            'password'    => Hash::make('password123'),
            'role'        => 'superadmin',
        ]);
    }
}