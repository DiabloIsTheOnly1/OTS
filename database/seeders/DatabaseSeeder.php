<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create departments
        $department = Department::create([
            'department_name' => 'Human Resource',
        ]);

        // Create branches
        $branch1 = Branch::create(['branch_name' => 'HQKK']);
        $branch2 = Branch::create(['branch_name' => 'UMKK1']);

        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'password' => 'password', // auto-hashed
            'department_id' => $department->id,
        ]);

        // Assign branches to admin
        $admin->branches()->attach([
            $branch1->id,
            $branch2->id,
        ]);
    }
}
