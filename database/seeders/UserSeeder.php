<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super-admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole('super-admin');

        // Create admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Create org leader
        $orgLeader = User::create([
            'name' => 'Marcel Santing',
            'email' => 'marcel@prox-web.nl',
            'password' => Hash::make('password'),
        ]);
        $orgLeader->assignRole('org-leader');

        // Create team leader
        $teamLeader = User::create([
            'name' => 'Team Leader',
            'email' => 'team-leader@example.com',
            'password' => Hash::make('password'),
        ]);
        $teamLeader->assignRole('team-leader');

        // Create regular member
        $member = User::create([
            'name' => 'Regular Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
        ]);
        $member->assignRole('member');
    }
}
