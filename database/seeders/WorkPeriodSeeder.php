<?php

namespace Database\Seeders;

use App\Models\WorkPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WorkPeriodSeeder extends Seeder
{
    /**
     * Seed an initial current work period so staff can start submitting documents.
     */
    public function run(): void
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $year = $startOfWeek->isoWeekYear();
        $weekNumber = $startOfWeek->isoWeek();

        $exists = WorkPeriod::where('year', $year)
            ->where('week_number', $weekNumber)
            ->exists();

        if ($exists) {
            return;
        }

        $planDeadline = $startOfWeek->copy()->addDays(4); // Friday
        if ($planDeadline->dayOfWeek !== Carbon::FRIDAY) {
            $planDeadline = $startOfWeek->copy()->next(Carbon::FRIDAY);
        }

        $reportDeadline = $startOfWeek->copy()->addDays(5); // Saturday
        if ($reportDeadline->dayOfWeek !== Carbon::SATURDAY) {
            $reportDeadline = $startOfWeek->copy()->next(Carbon::SATURDAY);
        }

        WorkPeriod::create([
            'year' => $year,
            'week_number' => $weekNumber,
            'week_start_date' => $startOfWeek->format('Y-m-d'),
            'week_end_date' => $endOfWeek->format('Y-m-d'),
            'plan_deadline' => $planDeadline->format('Y-m-d'),
            'report_deadline' => $reportDeadline->format('Y-m-d'),
            'status' => WorkPeriod::STATUS_OPEN,
        ]);
    }
}
