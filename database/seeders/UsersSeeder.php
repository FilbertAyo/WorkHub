<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample departments if they don't exist
        $departments = [
            ['name' => 'Administration', 'status' => 'active'],
            ['name' => 'Operations', 'status' => 'active'],
            ['name' => 'Finance', 'status' => 'active'],
            ['name' => 'Human Resources', 'status' => 'active'],
            ['name' => 'IT', 'status' => 'active'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept['name']],
                ['status' => $dept['status']]
            );
        }

        // Get departments
        $adminDept = Department::where('name', 'Administration')->first();
        $opsDept = Department::where('name', 'Operations')->first();
        $financeDept = Department::where('name', 'Finance')->first();
        $hrDept = Department::where('name', 'Human Resources')->first();
        $itDept = Department::where('name', 'IT')->first();

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $minutesPreparerRole = Role::where('name', 'minutes_preparer')->first();
        $verifierRole = Role::where('name', 'verifier')->first();
        $approverRole = Role::where('name', 'approver')->first();

        // ============================================
        // ADMIN USERS
        // ============================================
        $adminUsers = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '0712345678',
                'department_id' => $adminDept->id,
                'status' => 'active',
                'role' => $adminRole,
            ],
        ];

        foreach ($adminUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (!$user->hasRole($role->name)) {
                $user->assignRole($role);
            }
        }

        // ============================================
        // STAFF USERS (Write weekly plan, weekly report, monthly report)
        // ============================================
        $staffUsers = [
            [
                'name' => 'John Doe',
                'email' => 'staff1@example.com',
                'password' => Hash::make('password'),
                'phone' => '0711111111',
                'department_id' => $opsDept->id,
                'status' => 'active',
                'role' => $staffRole,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'staff2@example.com',
                'password' => Hash::make('password'),
                'phone' => '0711111112',
                'department_id' => $opsDept->id,
                'status' => 'active',
                'role' => $staffRole,
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'staff3@example.com',
                'password' => Hash::make('password'),
                'phone' => '0711111113',
                'department_id' => $financeDept->id,
                'status' => 'active',
                'role' => $staffRole,
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'staff4@example.com',
                'password' => Hash::make('password'),
                'phone' => '0711111114',
                'department_id' => $hrDept->id,
                'status' => 'active',
                'role' => $staffRole,
            ],
        ];

        foreach ($staffUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (!$user->hasRole($role->name)) {
                $user->assignRole($role);
            }
        }

        // ============================================
        // MINUTES_PREPARER USERS (Prepare minutes and action logs)
        // ============================================
        $minutesPreparerUsers = [
            [
                'name' => 'David Brown',
                'email' => 'minutes1@example.com',
                'password' => Hash::make('password'),
                'phone' => '0722222221',
                'department_id' => $adminDept->id,
                'status' => 'active',
                'role' => $minutesPreparerRole,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'minutes2@example.com',
                'password' => Hash::make('password'),
                'phone' => '0722222222',
                'department_id' => $opsDept->id,
                'status' => 'active',
                'role' => $minutesPreparerRole,
            ],
        ];

        foreach ($minutesPreparerUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (!$user->hasRole($role->name)) {
                $user->assignRole($role);
            }
        }

        // ============================================
        // VERIFIER USERS (Staff + Minutes Preparer + Verify permissions)
        // ============================================
        $verifierUsers = [
            [
                'name' => 'Robert Wilson',
                'email' => 'verifier1@example.com',
                'password' => Hash::make('password'),
                'phone' => '0733333331',
                'department_id' => $opsDept->id,
                'status' => 'active',
                'role' => $verifierRole,
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'verifier2@example.com',
                'password' => Hash::make('password'),
                'phone' => '0733333332',
                'department_id' => $financeDept->id,
                'status' => 'active',
                'role' => $verifierRole,
            ],
            [
                'name' => 'James Taylor',
                'email' => 'verifier3@example.com',
                'password' => Hash::make('password'),
                'phone' => '0733333333',
                'department_id' => $itDept->id,
                'status' => 'active',
                'role' => $verifierRole,
            ],
        ];

        foreach ($verifierUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (!$user->hasRole($role->name)) {
                $user->assignRole($role);
            }
        }

        // ============================================
        // APPROVER USERS (Boss role - can do everything)
        // ============================================
        $approverUsers = [
            [
                'name' => 'Chief Executive Officer',
                'email' => 'approver1@example.com',
                'password' => Hash::make('password'),
                'phone' => '0744444441',
                'department_id' => $adminDept->id,
                'status' => 'active',
                'role' => $approverRole,
            ],
            [
                'name' => 'Operations Manager',
                'email' => 'approver2@example.com',
                'password' => Hash::make('password'),
                'phone' => '0744444442',
                'department_id' => $opsDept->id,
                'status' => 'active',
                'role' => $approverRole,
            ],
            [
                'name' => 'Finance Director',
                'email' => 'approver3@example.com',
                'password' => Hash::make('password'),
                'phone' => '0744444443',
                'department_id' => $financeDept->id,
                'status' => 'active',
                'role' => $approverRole,
            ],
        ];

        foreach ($approverUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (!$user->hasRole($role->name)) {
                $user->assignRole($role);
            }
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Default password for all users: password');
    }
}
