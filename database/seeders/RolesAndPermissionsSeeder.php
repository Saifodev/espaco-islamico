<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            'access admin panel',
            'access dev panel',
            'manage users',
            'manage roles',
            'view articles',
            'create articles',
            'edit any articles',
            'edit articles',
            'publish articles',
            'delete any articles',
            'delete articles',
            'archive articles',
            'restore articles',
            'force delete articles',
            'impersonate users',
            'manage newsletters',
            'receive newsletter'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $author = Role::firstOrCreate(['name' => 'author']);
        $developer = Role::firstOrCreate(['name' => 'developer']);

        $admin->givePermissionTo(Permission::all());
        $editor->givePermissionTo([
            'create articles',
            'edit articles',
            'publish articles',
        ]);

        $author->givePermissionTo([
            'create articles',
            'edit articles',
        ]);

        $developer->givePermissionTo([
            'access dev panel'
        ]);
    }
}
