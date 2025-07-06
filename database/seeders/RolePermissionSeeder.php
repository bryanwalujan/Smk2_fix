<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Buat izin
        $permissions = [
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'view_attendance',
            'view_classroom',
            'view_subject',
            'export_excel',
            'export_pdf',
            'manage_lms',
            'view_lms',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Buat role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Berikan semua izin ke admin
        $adminRole->syncPermissions($permissions);
        $teacherRole->syncPermissions($permissions);

        // Berikan izin terbatas ke teacher
        $teacherRole->syncPermissions([
            'view_attendance',
            'view_classroom',
            'view_subject',
            'export_excel',
            'export_pdf',
            'manage_lms',
            'view_lms',
        ]);

        // Berikan izin terbatas ke student
        $studentRole->syncPermissions([
            'view_lms',
        ]);

        // Berikan role admin ke user pertama
        $adminUser = User::first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}