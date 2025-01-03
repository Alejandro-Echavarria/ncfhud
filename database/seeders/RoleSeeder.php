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

        Permission::create([
            'name' => 'admin.dashboard.index',
            'description' => 'Ver dashboard',
            'type' => 'dashboard',
            'order' => 1
        ])->syncRoles([$admin, $operator]);

        // Gestión de Usuarios
        Permission::create([
            'name' => 'admin.users.index',
            'description' => 'Ver usuarios',
            'type' => 'user_management',
            'order' => 1
        ])->syncRoles([$admin]);

        Permission::create([
            'name' => 'admin.users.create',
            'description' => 'Crear usuarios',
            'type' => 'user_management',
            'order' => 2
        ])->syncRoles([$admin]);

        Permission::create([
            'name' => 'admin.users.edit',
            'description' => 'Editar usuarios',
            'type' => 'user_management',
            'order' => 3
        ])->syncRoles([$admin]);

        // Gestión de Roles
        Permission::create([
            'name' => 'admin.roles.index',
            'description' => 'Ver roles',
            'type' => 'role_management',
            'order' => 1
        ])->syncRoles([$admin]);

        Permission::create([
            'name' => 'admin.roles.create',
            'description' => 'Crear roles',
            'type' => 'role_management',
            'order' => 2
        ])->syncRoles([$admin]);

        Permission::create([
            'name' => 'admin.roles.edit',
            'description' => 'Editar roles',
            'type' => 'role_management',
            'order' => 3
        ])->syncRoles([$admin]);

        Permission::create([
            'name' => 'admin.roles.destroy',
            'description' => 'Eliminar roles',
            'type' => 'role_management',
            'order' => 4
        ])->syncRoles([$admin]);

        // Gestión de Clientes
        Permission::create([
            'name' => 'admin.clients.index',
            'description' => 'Ver clientes',
            'type' => 'client_management',
            'order' => 1
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.clients.create',
            'description' => 'Crear clientes',
            'type' => 'client_management',
            'order' => 2
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.clients.edit',
            'description' => 'Editar clientes',
            'type' => 'client_management',
            'order' => 3
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.clients.destroy',
            'description' => 'Eliminar clientes',
            'type' => 'client_management',
            'order' => 4
        ])->syncRoles([$admin, $operator]);

        // Gestión de Facturas
        Permission::create([
            'name' => 'admin.invoices.index',
            'description' => 'Ver facturas',
            'type' => 'invoice_management',
            'order' => 1
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.invoices.create_607',
            'description' => 'Crear facturas 607',
            'type' => 'invoice_management',
            'order' => 2
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.invoices.create_606',
            'description' => 'Crear facturas 606',
            'type' => 'invoice_management',
            'order' => 3
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.invoices.edit',
            'description' => 'Editar facturas',
            'type' => 'invoice_management',
            'order' => 4
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.invoices.compare',
            'description' => 'Comparar facturas',
            'type' => 'invoice_management',
            'order' => 5
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.invoices.destroy_606',
            'description' => 'Eliminar facturas 606',
            'type' => 'invoice_management',
            'order' => 6
        ])->syncRoles([$admin, $operator]);

        Permission::create([
            'name' => 'admin.invoices.destroy_607',
            'description' => 'Eliminar facturas 607',
            'type' => 'invoice_management',
            'order' => 7
        ])->syncRoles([$admin, $operator]);
    }
}
