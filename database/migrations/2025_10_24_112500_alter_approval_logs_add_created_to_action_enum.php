<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, we need to drop the existing constraint and add a new one
        if (DB::getDriverName() === 'pgsql') {
            // Find and drop the existing check constraint
            $constraints = DB::select("
                SELECT conname as constraint_name
                FROM pg_constraint
                WHERE conrelid = 'approval_logs'::regclass
                AND contype = 'c'
            ");

            // Drop all check constraints (there should only be one for the action column)
            foreach ($constraints as $constraint) {
                // Check if this constraint is for the action column by checking the constraint definition
                $checkDef = DB::selectOne("
                    SELECT pg_get_constraintdef(oid) as definition
                    FROM pg_constraint
                    WHERE conname = ?
                ", [$constraint->constraint_name]);

                if ($checkDef && str_contains($checkDef->definition, 'action')) {
                    DB::statement("ALTER TABLE approval_logs DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                    break;
                }
            }

            // Add new constraint with 'created' value
            DB::statement("ALTER TABLE approval_logs ADD CONSTRAINT approval_logs_action_check CHECK (action IN ('approved', 'rejected', 'resubmission', 'resubmitted', 'paid', 'created'))");
        } else {
            // MySQL syntax
            DB::statement("ALTER TABLE approval_logs MODIFY COLUMN action ENUM('approved', 'rejected', 'resubmission', 'resubmitted', 'paid', 'created') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous enum definition (without 'created')
        // Note: This will fail if rows with action='created' still exist.
        if (DB::getDriverName() === 'pgsql') {
            // Find and drop the existing check constraint
            $constraints = DB::select("
                SELECT conname as constraint_name
                FROM pg_constraint
                WHERE conrelid = 'approval_logs'::regclass
                AND contype = 'c'
            ");

            // Drop all check constraints (there should only be one for the action column)
            foreach ($constraints as $constraint) {
                // Check if this constraint is for the action column by checking the constraint definition
                $checkDef = DB::selectOne("
                    SELECT pg_get_constraintdef(oid) as definition
                    FROM pg_constraint
                    WHERE conname = ?
                ", [$constraint->constraint_name]);

                if ($checkDef && str_contains($checkDef->definition, 'action')) {
                    DB::statement("ALTER TABLE approval_logs DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                    break;
                }
            }

            // Add constraint without 'created' value
            DB::statement("ALTER TABLE approval_logs ADD CONSTRAINT approval_logs_action_check CHECK (action IN ('approved', 'rejected', 'resubmission', 'resubmitted', 'paid'))");
        } else {
            // MySQL syntax
            DB::statement("ALTER TABLE approval_logs MODIFY COLUMN action ENUM('approved', 'rejected', 'resubmission', 'resubmitted', 'paid') NOT NULL");
        }
    }
};
