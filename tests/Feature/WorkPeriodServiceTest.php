<?php

namespace Tests\Feature;

use App\Models\WorkPeriod;
use App\Services\WorkPeriodService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkPeriodServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_week_one_for_new_year(): void
    {
        $service = new WorkPeriodService();

        WorkPeriod::create([
            'year' => 2024,
            'week_number' => 52,
            'week_start_date' => '2024-12-23',
            'week_end_date' => '2024-12-29',
            'plan_deadline' => '2024-12-27',
            'report_deadline' => '2024-12-28',
            'status' => WorkPeriod::STATUS_OPEN,
        ]);

        $newPeriod = $service->createNextWeek();

        $this->assertNotNull($newPeriod);
        $this->assertEquals(2025, $newPeriod->year);
        $this->assertEquals(1, $newPeriod->week_number);
    }

    public function test_automation_closes_current_and_creates_next(): void
    {
        $service = new WorkPeriodService();

        $currentWeek = WorkPeriod::create([
            'year' => 2024,
            'week_number' => 40,
            'week_start_date' => now()->startOfWeek()->format('Y-m-d'),
            'week_end_date' => now()->endOfWeek()->format('Y-m-d'),
            'plan_deadline' => now()->startOfWeek()->addDays(4)->format('Y-m-d'),
            'report_deadline' => now()->startOfWeek()->addDays(5)->format('Y-m-d'),
            'status' => WorkPeriod::STATUS_OPEN,
        ]);

        now()->setTestNow(now()->startOfWeek()->endOfWeek()->addDay()); // Move to Sunday

        $results = $service->processWeeklyAutomation();

        $this->assertTrue($results['closed']);
        $this->assertTrue($results['created']);

        $currentWeek->refresh();
        $this->assertEquals(WorkPeriod::STATUS_CLOSED, $currentWeek->status);

        $this->assertNotNull($results['created_period']);
        $expectedNextWeek = Carbon::parse($currentWeek->week_start_date)->addWeek();

        $this->assertEquals($expectedNextWeek->isoWeek(), $results['created_period']->week_number);
        $this->assertEquals($expectedNextWeek->isoWeekYear(), $results['created_period']->year);
        $this->assertEquals(WorkPeriod::STATUS_OPEN, $results['created_period']->status);
    }
}

