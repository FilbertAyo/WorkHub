<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_periods', function (Blueprint $table) {
            $table->id();
            $table->year('year'); // Year (2024, 2025, etc.)
            $table->unsignedTinyInteger('week_number'); // Week number within the year (1-53)
            $table->date('week_start_date'); // Start date of the week
            $table->date('week_end_date'); // End date of the week
            $table->date('plan_deadline'); // Deadline for weekly plan (Friday)
            $table->date('report_deadline'); // Deadline for weekly report (Saturday)
            $table->enum('status', ['open', 'closed', 'archived'])->default('open'); // Period status
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('year');
            $table->index('week_number');
            $table->index('status');
            $table->index('week_start_date');
            $table->index('week_end_date');
            $table->unique(['year', 'week_number']); // Ensure unique week per year
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_periods');
    }
};
