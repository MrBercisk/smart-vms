<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage departments',
            'manage employees',
            'view reports',
            'view dashboard',
            'checkin visitor',
            'checkout visitor',
            'print visitor pass',
            'create appointment',
            'approve appointment',
            'view own appointments',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Buat roles
        $superAdmin  = Role::create(['name' => 'super-admin']);
        $receptionist = Role::create(['name' => 'receptionist']);
        $employee    = Role::create(['name' => 'employee']);

        // Assign permissions
        $superAdmin->givePermissionTo(Permission::all());

        $receptionist->givePermissionTo([
            'checkin visitor',
            'checkout visitor',
            'print visitor pass',
            'view dashboard',
        ]);

        $employee->givePermissionTo([
            'create appointment',
            'approve appointment',
            'view own appointments',
        ]);

        // Buat user Super Admin
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@vms.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('super-admin');

        // Buat user Receptionist
        $rec = User::create([
            'name'     => 'Receptionist',
            'email'    => 'receptionist@vms.com',
            'password' => bcrypt('password'),
        ]);
        $rec->assignRole('receptionist');

        // Buat user Employee
        $emp = User::create([
            'name'     => 'Employee',
            'email'    => 'employee@vms.com',
            'password' => bcrypt('password'),
        ]);
        $emp->assignRole('employee');
    }
}
?>