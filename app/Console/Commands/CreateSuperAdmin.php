<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super-admin {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user with the admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create or get the admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create the super admin user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
        ]);

        // Assign the admin role to the user
        $user->assignRole($adminRole);

        $this->info('Super admin user created successfully!');
        $this->info('Email: ' . $user->email);
        $this->info('Role: ' . $adminRole->name);
    }
} 