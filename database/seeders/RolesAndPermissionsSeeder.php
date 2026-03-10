<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // system
            'access admin panel',
            'access dev panel',
            'impersonate users',

            // users & roles
            'manage users',
            'manage roles',

            // articles
            'view articles',
            'create articles',
            'edit articles',
            'edit any articles',
            'publish articles',
            'delete articles',
            'delete any articles',
            'archive articles',
            'restore articles',
            'force delete articles',

            // newsletter
            'manage newsletters',
            'receive newsletter',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $author = Role::firstOrCreate(['name' => 'author']);
        $developer = Role::firstOrCreate(['name' => 'developer']);

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin->syncPermissions([
            'access admin panel',

            'manage users',
            'manage roles',

            'view articles',
            'create articles',
            'edit any articles',
            'publish articles',
            'delete any articles',
            'archive articles',
            'restore articles',
            'force delete articles',

            'manage newsletters',

            'impersonate users',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Editor
        |--------------------------------------------------------------------------
        */

        $editor->syncPermissions([
            'access admin panel',

            'view articles',
            'create articles',
            'edit any articles',
            'publish articles',
            'archive articles',
            'restore articles',

            'receive newsletter',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Author
        |--------------------------------------------------------------------------
        */

        $author->syncPermissions([
            'access admin panel',

            'view articles',
            'create articles',
            'edit articles',
            'delete articles',

            'receive newsletter',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Developer
        |--------------------------------------------------------------------------
        */

        $developer->syncPermissions(Permission::all());
    }
}