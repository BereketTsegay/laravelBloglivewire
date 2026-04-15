<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RollPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissinos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //create permissions
        $permissions = [
            'create posts',
            'edit own posts',
            'edit all posts',
            'delete all posts',
            'delete own posts',
            'publish posts',
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            # code...
            Permission::create(['name'=> $permission]);
        }

        //create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $editRole = Role::create(['name' => 'editor']);
        $authorRole = Role::create(['name' => 'author']);
        $subscriberRole = Role::create(['name' => 'subscriber']); // have no permission just reading

        $adminRole->givePermissionTo(Permission::all());
        $editRole->givePermissionTo([
            'create posts',
            'edit all posts',
            'delete all posts',
            'publish posts',
        ]);
        $authorRole->givePermissionTo([
            'create posts',
            'edit own posts',
            'delete own posts',
        ]);

    }
}
