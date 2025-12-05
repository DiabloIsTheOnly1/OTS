<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Staff;
use App\Models\AccessLevel;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create departments
        $department1 = Department::create(['department_name' => 'Human Resource',]);
        $department2 = Department::create(['department_name' => 'Account',]);

        // Create branches
        $branch1 = Branch::create(['name' => 'HQKK']);
        $branch2 = Branch::create(['name' => 'UMKK1']);

        // Create access levels
        $accessLevel1 = AccessLevel::create([
            'name' => 'Admin',
            'access_level' => 1,
            'user' => 1,
            'branch_settings' => 1,
            'department_settings' => 1,
            'staff_settings' => 1,
            'manage_request' => 1,
            'hod_approval' => 1,
        ]);

        //create staff
        $staff1 = Staff::create([
            'staff_name'=> 'ALI BIN ABU', 
            'position'=> 'Manager', 
            'branch_id'=> $branch1->id, 
            'department_id'=> $department1->id,
        ]);

        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'password' => 'password', // auto-hashed
            'department_id' => $department1->id,
            'access_level_id' => $accessLevel1->id,
        ]);

        // Assign branches to admin
        $admin->branches()->attach([
            $branch1->id,
            $branch2->id,
        ]);
    }
}
