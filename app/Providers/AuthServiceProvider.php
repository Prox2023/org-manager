<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\App;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register a dynamic gate for each permission.
        if (!App::runningInConsole()) {
            foreach (Permission::all() as $permission) {
                Gate::define($permission->name, static function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission);
                });
            }
        }
    }
}
