<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $operator = Role::create(['name' => 'operator']);

        Permission::create(['name' => 'admin.dashboard.index',
            'description' => 'Ver dashboard'])->syncRoles([$admin, $operator]);

        Permission::create(['name' => 'admin.users.index',
            'description' => 'Ver usuarios'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.users.create',
            'description' => 'Crear usuarios'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.users.edit',
            'description' => 'Editar usuarios'])->syncRoles([$admin]);

        Permission::create(['name' => 'admin.roles.index',
            'description' => 'Ver roles'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.roles.create',
            'description' => 'Crear roles'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.roles.edit',
            'description' => 'Editar roles'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.roles.destroy',
            'description' => 'Eliminar roles'])->syncRoles([$admin]);

        Permission::create(['name' => 'admin.clients.index',
            'description' => 'Ver clientes'])->syncRoles([$admin, $operator]);
        Permission::create(['name' => 'admin.clients.create',
            'description' => 'Crear clientes'])->syncRoles([$admin, $operator]);
        Permission::create(['name' => 'admin.clients.edit',
            'description' => 'Editar clientes'])->syncRoles([$admin, $operator]);
        Permission::create(['name' => 'admin.clients.destroy',
            'description' => 'Eliminar clientes'])->syncRoles([$admin, $operator]);

        Permission::create(['name' => 'admin.invoices.index',
            'description' => 'Ver facturas'])->syncRoles([$admin, $operator]);
        Permission::create(['name' => 'admin.invoices.create_607',
            'description' => 'Crear facturas 607'])->syncRoles([$admin, $operator]);
        Permission::create(['name' => 'admin.invoices.create_606',
            'description' => 'Crear facturas 606'])->syncRoles([$admin, $operator]);
        Permission::create(['name' => 'admin.invoices.edit',
            'description' => 'Editar facturas'])->syncRoles([$admin, $operator]);
        Permission::create(['name' => 'admin.invoices.compare',
            'description' => 'Comparar facturas'])->syncRoles([$admin, $operator]);
        Permission::create(['name' => 'admin.invoices.destroy',
            'description' => 'Eliminar facturas'])->syncRoles([$admin, $operator]);
    }
}
