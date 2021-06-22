<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RolesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('role', function ($role) {
            return "<?php if (auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function ($role) {
            return "<?php endif; ?>";
        });

        Blade::directive('permission', function ($permission) {
            return "<?php if (auth()->check() && (auth()->user()->hasPermission({$permission}) || auth()->user()->isRoot())): ?>";
        });

        Blade::directive('else_permission', function ($permission) {
            return "<?php else: ?>";
        });

        Blade::directive('endpermission', function ($permission) {
            return "<?php endif; ?>";
        });

        Blade::if('anyPermission', function ($permissions) {
            $permissions = explode('|', $permissions);
            $hasPermission = false;

            foreach ($permissions as $permission) {
                if (auth()->user()->hasPermission($permission) || auth()->user()->isRoot()) {
                    $hasPermission = true;
                    break;
                }
            }

            return $hasPermission;
        });
    }
}
