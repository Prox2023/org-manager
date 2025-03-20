<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,  // First create roles and permissions
            OrganizationSeeder::class,       // Then create organizations
            UserSeeder::class,               // Then create users with roles
            TeamSeeder::class,               // Then create teams
            RankHistorySeeder::class,        // Finally create rank histories
        ]);
    }
}
