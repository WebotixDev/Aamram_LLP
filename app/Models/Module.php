<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'actions'];

    protected $casts = [
        'actions' => 'array',
    ];

    protected static function booted()
    {
        // 1️⃣ Auto-create/update permissions when module is saved
        static::saved(function ($module) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            if (!$module->actions) return;

            $adminRole = Role::firstOrCreate([
                'name' => 'Admin',
                'guard_name' => config('auth.defaults.guard'),
            ]);

            foreach ($module->actions as $permissionName) {
                $permission = Permission::updateOrCreate([
                    'name' => $permissionName,
                    'guard_name' => config('auth.defaults.guard'),
                ]);

                // Assign permission automatically to Admin role
                $adminRole->givePermissionTo($permission);
            }
        });

        // 2️⃣ Auto-delete permissions when module is deleted
        static::deleted(function ($module) {
            if (!$module->actions) return;

            foreach ($module->actions as $permissionName) {
                Permission::where('name', $permissionName)
                    ->where('guard_name', config('auth.defaults.guard'))
                    ->delete();
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        });
    }
}
