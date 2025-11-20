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
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================
        // EXISTING PERMISSIONS (Petty Cash & System)
        // ============================================
        $existingPermissions = [
            'request pettycash',
            'view requested pettycash',
            'first pettycash approval',
            'last pettycash approval',
            'approve petycash payments',
            'view cashflow movements',
            'request item purchase',
            'approve item purchase',
            'view reports',
            'view settings',
            'users management settings',
            'update other settings',
            'approve final item purchase',
        ];

        // ============================================
        // DOCUMENT MANAGEMENT PERMISSIONS
        // ============================================
        $documentPermissions = [
            // Document Creation Permissions
            'create weekly plan',
            'create weekly report',
            'create monthly report',
            'create minutes',

            // Document Viewing Permissions
            'view own documents',
            'view all documents',

            // Document Management Permissions
            'edit own documents',
            'delete own documents',
            'submit documents',

            // Verification & Approval Permissions
            'verify documents',
            'approve documents',

            // Action Logs Permissions
            'view action logs',
            'create action logs',
            'manage action logs',

            // Dashboard Permissions
            'view dashboard',
            'view reviewer dashboard',
        ];

        // Combine all permissions
        $allPermissions = array_merge($existingPermissions, $documentPermissions);

        // Create all permissions
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ============================================
        // ROLES CREATION
        // ============================================

        // Legacy roles (keeping for backward compatibility if needed)
        $superuser = Role::firstOrCreate(['name' => 'superuser']);
        $basic_user = Role::firstOrCreate(['name' => 'basic_user']);

        // ============================================
        // WORKHUB DOCUMENT MANAGEMENT ROLES
        // ============================================

        // 1. ADMIN - Can do everything
        $admin = Role::firstOrCreate(['name' => 'admin']);

        // 2. STAFF - Write weekly plan, weekly report, monthly report
        $staff = Role::firstOrCreate(['name' => 'staff']);

        // 3. MINUTES_PREPARER - Prepare minutes and action logs
        $minutes_preparer = Role::firstOrCreate(['name' => 'minutes_preparer']);

        // 4. VERIFIER - Same as staff + minutes_preparer + can verify all documents
        $verifier = Role::firstOrCreate(['name' => 'verifier']);

        // 5. APPROVER - Boss role, can see all work and approve them (can do anything/all)
        $approver = Role::firstOrCreate(['name' => 'approver']);

        // ============================================
        // PERMISSIONS ASSIGNMENT
        // ============================================

        // Basic user permissions
        $basic_user->givePermissionTo(['request pettycash']);

        // Superuser gets all permissions
        $superuser->givePermissionTo(Permission::all());

        // ============================================
        // STAFF PERMISSIONS
        // ============================================
        $staff->givePermissionTo([
            // Document creation
            'create weekly plan',
            'create weekly report',
            'create monthly report',

            // Document management
            'view own documents',
            'edit own documents',
            'delete own documents',
            'submit documents',

            // Dashboard
            'view dashboard',

            // Basic pettycash
            'request pettycash',
        ]);

        // ============================================
        // MINUTES_PREPARER PERMISSIONS
        // ============================================
        $minutes_preparer->givePermissionTo([
            // Document creation
            'create minutes',

            // Document management
            'view own documents',
            'edit own documents',
            'delete own documents',
            'submit documents',

            // Action logs
            'view action logs',
            'create action logs',
            'manage action logs',

            // Dashboard
            'view dashboard',
        ]);

        // ============================================
        // VERIFIER PERMISSIONS
        // ============================================
        // Verifier has staff + minutes_preparer permissions + verify
        $verifier->givePermissionTo([
            // Staff permissions
            'create weekly plan',
            'create weekly report',
            'create monthly report',
            'view own documents',
            'edit own documents',
            'delete own documents',
            'submit documents',

            // Minutes preparer permissions
            'create minutes',
            'view action logs',
            'create action logs',
            'manage action logs',

            // Verifier specific permissions
            'view all documents',
            'verify documents',
            'view reviewer dashboard',
            'view dashboard',
        ]);

        // ============================================
        // APPROVER PERMISSIONS
        // ============================================
        // Approver can do everything - boss role
        $approver->givePermissionTo(Permission::all());

        // ============================================
        // ADMIN PERMISSIONS
        // ============================================
        // Admin can do everything
        $admin->givePermissionTo(Permission::all());
    }
}
